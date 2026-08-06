<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0, 
        'path' => '/', 
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on', 
        'httponly' => true, 
        'samesite' => 'Lax'
    ]);
    session_start();
}

// ⚠️ เรียกใช้ไฟล์ตั้งค่า
require_once __DIR__ . '/../config/google.php';
require_once __DIR__ . '/../config/db.php'; 

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // 1. ขอ Access Token จาก Google
    $tokenUrl = 'https://oauth2.googleapis.com/token';
    $postData = [
        'code' => $code, 
        'client_id' => $clientID, 
        'client_secret' => $clientSecret, 
        'redirect_uri' => $redirectUri, 
        'grant_type' => 'authorization_code'
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $tokenUrl, 
        CURLOPT_POST => true, 
        CURLOPT_POSTFIELDS => http_build_query($postData), 
        CURLOPT_RETURNTRANSFER => true, 
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    $response = curl_exec($ch); 
    curl_close($ch);
    
    $tokenData = json_decode($response, true);

    if (isset($tokenData['access_token'])) {
        // 2. ดึงข้อมูล User จาก Google
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
        $ch2 = curl_init();
        curl_setopt_array($ch2, [
            CURLOPT_URL => $userInfoUrl, 
            CURLOPT_RETURNTRANSFER => true, 
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $tokenData['access_token']], 
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        $userResponse = curl_exec($ch2); 
        curl_close($ch2);
        
        $userInfo = json_decode($userResponse, true, 512, JSON_BIGINT_AS_STRING);

        if (isset($userInfo['email'])) {
            $googleId = (string)($userInfo['id'] ?? '');
            $email    = $userInfo['email'];
            $name     = $userInfo['name'] ?? 'Google User';
            $img      = $userInfo['picture'] ?? ''; // Google เรียก picture แต่เราเก็บลงคอลัมน์ img
            $dbUserId = null;

            // ✅ 3. จัดการ Database
            if (isset($conn)) {
                try {
                    $stmt = $conn->prepare("SELECT id FROM users WHERE google_id = :google_id OR email = :email LIMIT 1");
                    $stmt->execute([':google_id' => $googleId, ':email' => $email]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($user) {
                        // 🟢 มี User อยู่แล้ว -> อัปเดตข้อมูลให้ตรงกับตารางของคุณ (ตัด username, picture ทิ้ง)
                        $dbUserId = $user['id'];
                        $updateStmt = $conn->prepare("UPDATE users SET google_id = :google_id, name = :name, img = :img WHERE id = :id");
                        $updateStmt->execute([
                            ':google_id' => $googleId,
                            ':name' => $name, 
                            ':img' => $img, 
                            ':id' => $dbUserId
                        ]);
                    } else {
                        // 🔵 User ใหม่ -> Insert ให้ตรงกับคอลัมน์ในฐานข้อมูล (id, google_id, name, email, img)
                        $insertStmt = $conn->prepare("INSERT INTO users (google_id, name, email, img) VALUES (:google_id, :name, :email, :img)");
                        $insertStmt->execute([
                            ':google_id' => $googleId, 
                            ':name' => $name, 
                            ':email' => $email, 
                            ':img' => $img
                        ]);
                        $dbUserId = $conn->lastInsertId();
                    }
                } catch (PDOException $e) {
                    // หากยัง Error อีก สามารถดู Log ได้
                    error_log("DB Error in Callback: " . $e->getMessage());
                }
            }

            $finalId = $dbUserId ?? $googleId;

            // ✅ 4. เซ็ต Session เข้าสู่ระบบ
            $_SESSION['user_data']  = $userInfo;
            $_SESSION['user_id']    = $finalId;
            
            $_SESSION['user_login'] = [
                'id'       => $finalId,
                'name'     => $name,
                'email'    => $email,
                'img'      => $img,
                'level'    => 1, 
                'user_exp' => 0, 
                'streak'   => 1
            ];

            session_write_close();
            header('Location: ../index.php');
            exit;
        }
    }
}
header('Location: ../index.php');
exit;