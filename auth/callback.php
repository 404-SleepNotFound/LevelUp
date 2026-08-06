<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on', 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

// ⚠️ เรียกใช้ไฟล์ตั้งค่า
require_once __DIR__ . '/../config/google.php';
require_once __DIR__ . '/../config/db.php'; // 👈 สำคัญมาก! ต้องเรียก db.php เพื่อใช้ $conn

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    $tokenUrl = 'https://oauth2.googleapis.com/token';
    $postData = ['code' => $code, 'client_id' => $clientID, 'client_secret' => $clientSecret, 'redirect_uri' => $redirectUri, 'grant_type' => 'authorization_code'];

    $ch = curl_init();
    curl_setopt_array($ch, [CURLOPT_URL => $tokenUrl, CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query($postData), CURLOPT_RETURNTRANSFER => true, CURLOPT_SSL_VERIFYPEER => false]);
    $response = curl_exec($ch); curl_close($ch);
    $tokenData = json_decode($response, true);

    if (isset($tokenData['access_token'])) {
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
        $ch2 = curl_init();
        curl_setopt_array($ch2, [CURLOPT_URL => $userInfoUrl, CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $tokenData['access_token']], CURLOPT_SSL_VERIFYPEER => false]);
        $userResponse = curl_exec($ch2); curl_close($ch2);
        
        $userInfo = json_decode($userResponse, true, 512, JSON_BIGINT_AS_STRING);

        if (isset($userInfo['email'])) {
            $googleId = (string)($userInfo['id'] ?? '');
            $email    = $userInfo['email'];
            $name     = $userInfo['name'] ?? 'Google User';
            $picture  = $userInfo['picture'] ?? '';
            $dbUserId = null;

            // ✅ ใช้ $conn ให้ตรงกับระบบของคุณ
            if (isset($conn)) {
                $stmt = $conn->prepare("SELECT id FROM users WHERE google_id = :google_id OR email = :email LIMIT 1");
                $stmt->execute([':google_id' => $googleId, ':email' => $email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    $dbUserId = $user['id']; // ได้ ID 18, 19, 20...
                } else {
                    $insertStmt = $conn->prepare("INSERT INTO users (google_id, email, username, name, picture) VALUES (:google_id, :email, :username, :name, :picture)");
                    $insertStmt->execute([':google_id' => $googleId, ':email' => $email, ':username' => $name, ':name' => $name, ':picture' => $picture]);
                    $dbUserId = $conn->lastInsertId();
                }
            }

            // ถ้า DB หา ID ไม่ได้จริงๆ ค่อยใช้ Google ID
            $finalId = $dbUserId ?? $googleId;

            // ✅ เซ็ต Session เป็น ID ตัวเลขสั้นๆ
            $_SESSION['user_data']  = $userInfo;
            $_SESSION['user_id']    = $finalId;
            $_SESSION['user_login'] = [
                'id'       => $finalId,
                'name'     => $name,
                'username' => $name,
                'email'    => $email,
                'picture'  => $picture,
                'img'      => $picture,
                'avatar'   => $picture,
                'level'    => 1, 'user_exp' => 0, 'streak' => 1
            ];

            session_write_close();
            header('Location: ../index.php');
            exit;
        }
    }
}
header('Location: ../index.php');
exit;