<?php
// 1. ตั้งค่า Cookie Path
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

// ดึงไฟล์ Config และไฟล์เชื่อมต่อ Database
require_once __DIR__ . '/../config/google.php';
// require_once __DIR__ . '/../config/db.php'; // 👈 อย่าลืม include ไฟล์เชื่อมต่อ Database ของคุณที่นี่

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
            $name     = $userInfo['name'] ?? $email;
            $picture  = $userInfo['picture'] ?? '';

            // ------------------------------------------------------------------
            // 🔍 ส่วนที่เพิ่มเข้ามา: ดึง ID จริงจาก Database
            // ------------------------------------------------------------------
            $dbUserId = null;

            /* 📌 ตัวอย่างการเขียนคำสั่งดึง/บันทึก DB (เปิดใช้งานแล้วปรับชื่อตาราง/ตัวแปรตามจริง)
            
            // 1. ลองค้นหาผู้ใช้จาก Database ด้วย email หรือ google_id
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR google_id = ?");
            $stmt->execute([$email, $googleId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // ถ้ามีผู้ใช้อยู่แล้ว -> ดึง Primary Key ID ใน DB มาใช้ (เช่น 1, 2, 3...)
                $dbUserId = $user['id'];
            } else {
                // ถ้ายังไม่มี -> เพิ่มผู้ใช้ใหม่ลง DB แล้วดึง ID ล่าสุดมาใช้
                $stmtInsert = $pdo->prepare("INSERT INTO users (google_id, name, email, picture) VALUES (?, ?, ?, ?)");
                $stmtInsert->execute([$googleId, $name, $email, $picture]);
                $dbUserId = $pdo->lastInsertId();
            }
            */

            // กำหนด ID ให้เป็น DB ID (ถ้าไม่มีให้ fallback เป็น googleId ไปก่อน)
            $finalUserId = $dbUserId ?? $googleId;

            // บันทึก Session
            $_SESSION['user_data'] = $userInfo;
            $_SESSION['user_id']   = $finalUserId;
            
            $_SESSION['user_login'] = [
                'id'       => $finalUserId, // 👈 ตอนนี้จะเป็น ID จาก Database (เช่น 1, 2) แล้วครับ
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

header('Location: ../index.php');
exit;