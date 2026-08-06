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

// ⚠️ เรียกใช้ไฟล์ตั้งค่า (ตรวจสอบ Path ให้ตรงกับโฟลเดอร์ของคุณ)
require_once __DIR__ . '/../config/google.php';
require_once __DIR__ . '/../config/db.php'; // 👈 สำคัญมาก! ต้องเรียก db.php เพื่อใช้ $conn

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
            $picture  = $userInfo['picture'] ?? '';
            $dbUserId = null;

            // ✅ 3. จัดการ Database (ใช้ $conn)
            if (isset($conn)) {
                try {
                    // ค้นหาว่ามี User นี้ในระบบหรือยัง (จาก Google ID หรือ Email)
                    $stmt = $conn->prepare("SELECT id FROM users WHERE google_id = :google_id OR email = :email LIMIT 1");
                    $stmt->execute([':google_id' => $googleId, ':email' => $email]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($user) {
                        // 🟢 กรณีมี User อยู่แล้ว -> อัปเดตข้อมูลล่าสุดจาก Google (ป้องกันข้อมูลไม่อัปเดต)
                        $dbUserId = $user['id'];
                        
                        // อัปเดตชื่อ รูป และผูก google_id (เผื่อล็อกอินครั้งแรกด้วยอีเมลปกติ)
                        $updateStmt = $conn->prepare("UPDATE users SET google_id = :google_id, name = :name, picture = :picture, img = :img WHERE id = :id");
                        $updateStmt->execute([
                            ':google_id' => $googleId,
                            ':name' => $name, 
                            ':picture' => $picture, 
                            ':img' => $picture, // สมมติว่ามีคอลัมน์ img ด้วย
                            ':id' => $dbUserId
                        ]);
                    } else {
                        // 🔵 กรณีเป็น User ใหม่ -> ทำการ Insert ลงฐานข้อมูล
                        $insertStmt = $conn->prepare("INSERT INTO users (google_id, email, username, name, picture, img) VALUES (:google_id, :email, :username, :name, :picture, :img)");
                        $insertStmt->execute([
                            ':google_id' => $googleId, 
                            ':email' => $email, 
                            ':username' => $name, // ใช้ชื่อตั้งเป็น username ไปก่อน
                            ':name' => $name, 
                            ':picture' => $picture,
                            ':img' => $picture
                        ]);
                        $dbUserId = $conn->lastInsertId();
                    }
                } catch (PDOException $e) {
                    // ดักจับ Error เผื่อตารางไม่มีคอลัมน์บางตัว
                    // error_log("DB Error in Callback: " . $e->getMessage());
                }
            }

            // ถ้า DB ทำงานพลาด หา ID ไม่ได้จริงๆ ให้ Fallback กลับไปใช้ Google ID เพื่อให้เว็บไม่ล่ม
            $finalId = $dbUserId ?? $googleId;

            // ✅ 4. เซ็ต Session เข้าสู่ระบบ
            $_SESSION['user_data']  = $userInfo;
            $_SESSION['user_id']    = $finalId;
            
            // เลียนแบบโครงสร้าง Session ให้ตรงกับระบบเดิมของคุณ
            $_SESSION['user_login'] = [
                'id'       => $finalId,
                'name'     => $name,
                'username' => $name,
                'email'    => $email,
                'picture'  => $picture,
                'img'      => $picture,
                'avatar'   => $picture,
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
// ถ้าทำงานไม่สำเร็จ ให้เด้งกลับหน้าแรก
header('Location: ../index.php');
exit;