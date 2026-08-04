<?php
// 1. ตั้งค่า Cookie Path ให้ครอบคลุมทุกโฟลเดอร์ในเว็บ (ป้องกัน Session หลุดระหว่าง /auth/ กับหน้าแรก)
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ดึงค่าการตั้งค่าจาก config/google.php
require_once __DIR__ . '/../config/google.php';

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // แลกเปลี่ยน Authorization Code เป็น Access Token
    $tokenUrl = 'https://oauth2.googleapis.com/token';
    $postData = array(
        'code'          => $code,
        'client_id'     => $clientID,
        'client_secret' => $clientSecret,
        'redirect_uri'  => $redirectUri,
        'grant_type'    => 'authorization_code'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $tokenData = json_decode($response, true);

    // เช็กว่า Google คืนค่า access_token มาจริงไหม
    if (isset($tokenData['access_token'])) {
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
        
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, $userInfoUrl);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $tokenData['access_token']
        ));
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
        $userResponse = curl_exec($ch2);
        curl_close($ch2);

        $userInfo = json_decode($userResponse, true);

        if (isset($userInfo['email'])) {
            // บันทึก Session ทั้งสองรูปแบบ
            $_SESSION['user_data']  = $userInfo;
            $_SESSION['user_login'] = array(
                'id'      => $userInfo['id'] ?? '',
                'name'    => $userInfo['name'] ?? ($userInfo['email'] ?? 'Google User'),
                'email'   => $userInfo['email'],
                'picture' => $userInfo['picture'] ?? '',
                'level'   => 1
            );

            // บันทึก Session ลงดิสก์ทันทีก่อนเปลี่ยนหน้า
            session_write_close();
            
            // ส่งกลับหน้าแรก
            header('Location: ../index.php');
            exit;
        }
    } else {
        // หากแลก Token ไม่สำเร็จ แสดง Error ออกหน้าจอเพื่อตรวจสอบ
        echo "<div style='padding: 20px; font-family: sans-serif;'>";
        echo "<h3 style='color: red;'>❌ การล็อกอินด้วย Google ล้มเหลว!</h3>";
        echo "<p>รายละเอียด Error จาก Google Response:</p>";
        echo "<pre style='background: #f4f4f4; padding: 15px; border-radius: 5px;'>";
        print_r($tokenData);
        echo "</pre>";
        echo "<p><b>ข้อสังเกต:</b> โปรดตรวจสอบค่า <code>\$redirectUri</code> ใน <code>config/google.php</code> ว่าตรงกับที่ตั้งไว้ใน Google Cloud Console หรือไม่</p>";
        echo "<a href='../auth/login.php'>กลับหน้าล็อกอิน</a>";
        echo "</div>";
        exit;
    }
}

// ถ้าไม่มี code ส่งมา ให้เด้งกลับหน้าแรก
header('Location: ../index.php');
exit;