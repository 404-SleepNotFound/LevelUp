<?php
// 1. ตั้งค่า Cookie Path ให้ครอบคลุมทุกโฟลเดอร์ในเว็บ
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// ดึงไฟล์ Config ของ Google และ **อย่าลืมดึงไฟล์เชื่อมต่อ Database ของคุณมาด้วย**
require_once __DIR__ . '/../config/google.php';
// require_once __DIR__ . '/../config/db.php'; // ⚠️ เปลี่ยนพาธนี้ให้ตรงกับไฟล์เชื่อมต่อ DB ของคุณ

// ตรวจสอบ CSRF State Token
if (isset($_GET['state']) && isset($_SESSION['oauth2state'])) {
    if ($_GET['state'] !== $_SESSION['oauth2state']) {
        unset($_SESSION['oauth2state']);
        exit('Invalid OAuth State Parameter');
    }
    unset($_SESSION['oauth2state']);
}

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // แลกเปลี่ยน Authorization Code เป็น Access Token
    $tokenUrl = 'https://oauth2.googleapis.com/token';
    $postData = [
        'code'          => $code,
        'client_id'     => $clientID,
        'client_secret' => $clientSecret,
        'redirect_uri'  => $redirectUri,
        'grant_type'    => 'authorization_code'
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $tokenUrl,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($postData),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    
    $response  = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        exit('cURL Error: ' . htmlspecialchars($curlError));
    }

    $tokenData = json_decode($response, true);

    if (isset($tokenData['access_token'])) {
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
        
        $ch2 = curl_init();
        curl_setopt_array($ch2, [
            CURLOPT_URL            => $userInfoUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $tokenData['access_token']],
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $userResponse = curl_exec($ch2);
        curl_close($ch2);

        $userInfo = json_decode($userResponse, true, 512, JSON_BIGINT_AS_STRING);

        if (isset($userInfo['email'])) {
            $googleId = (string)($userInfo['id'] ?? '');
            $email    = $userInfo['email'];
            $name     = $userInfo['name'] ?? ($email ?? 'Google User');
            $picture  = $userInfo['picture'] ?? '';
            
            $dbUserId = null;

            // ==========================================================
            // 🛠️ ส่วนที่แก้ไข: ดึงข้อมูล ID ภายในจาก Database
            // หมายเหตุ: โค้ดด้านล่างใช้ PDO (หากใช้ MySQLi ให้ปรับคำสั่งให้ตรงกัน)
            // ==========================================================
            
            /* ⚠️ เอา Comment (/* และ * /) ด้านล่างนี้ออก และปรับชื่อตารางให้ตรงกับในฐานข้อมูลของคุณ ⚠️
            
            // 1. ค้นหาว่าใน Database มีบัญชีที่ผูกด้วย google_id หรือ email นี้หรือยัง
            $stmt = $pdo->prepare("SELECT id FROM users WHERE google_id = :google_id OR email = :email LIMIT 1");
            $stmt->execute([':google_id' => $googleId, ':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // มีข้อมูลแล้ว -> ใช้ ID จาก Database (เช่น 18, 19, 20...)
                $dbUserId = $user['id'];
            } else {
                // ยังไม่มีข้อมูล -> บันทึกผู้ใช้ใหม่ลง Database
                $insertStmt = $pdo->prepare("INSERT INTO users (google_id, email, username, name, picture) VALUES (:google_id, :email, :username, :name, :picture)");
                $insertStmt->execute([
                    ':google_id' => $googleId,
                    ':email'     => $email,
                    ':username'  => $name,
                    ':name'      => $name,
                    ':picture'   => $picture
                ]);
                
                // ดึง ID ล่าสุดที่เพิ่งถูกเพิ่มเข้าไป
                $dbUserId = $pdo->lastInsertId();
            }
            */

            // หากยังไม่ได้เอาคอมเมนต์ด้านบนออก หรือ DB ล่ม ให้ fallback เป็น Google ID กันพัง
            $finalId = $dbUserId ?? $googleId;

            // บันทึก Session เป็น ID จาก Database แน่นอนแล้ว
            $_SESSION['user_data'] = $userInfo;
            $_SESSION['user_id']   = $finalId; // ใช้ DB ID (เช่น 18)
            
            $_SESSION['user_login'] = [
                'id'       => $finalId,    // ใช้ DB ID ตรงนี้ครับ
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

            // บันทึก Session ลงดิสก์ทันทีก่อนเปลี่ยนหน้า
            session_write_close();
            
            // ส่งกลับหน้าแรก
            header('Location: ../index.php');
            exit;
        }
    } else {
        echo "<div style='padding: 20px; font-family: sans-serif;'>";
        echo "<h3 style='color: red;'>❌ การล็อกอินด้วย Google ล้มเหลว!</h3>";
        echo "<pre style='background: #f4f4f4; padding: 15px; border-radius: 5px;'>";
        echo htmlspecialchars(print_r($tokenData, true));
        echo "</pre>";
        echo "<a href='../auth/login.php'>กลับหน้าล็อกอิน</a>";
        echo "</div>";
        exit;
    }
}

// ถ้าไม่มี code ส่งมา ให้เด้งกลับหน้าแรก
header('Location: ../index.php');
exit;