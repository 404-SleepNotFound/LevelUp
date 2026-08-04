<?php
// ดึงค่าการตั้งค่าจาก config/google.php
require_once __DIR__ . '/../config/google.php';

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // 1. แลกเปลี่ยน Authorization Code เป็น Access Token ด้วย cURL
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
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // ป้องกันปัญหา SSL บนเซิร์ฟเวอร์
    $response = curl_exec($ch);
    curl_close($ch);

    $tokenData = json_decode($response, true);

    if (isset($tokenData['access_token'])) {
        // 2. ดึงข้อมูลโปรไฟล์ผู้ใช้จาก Google API
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

        // 3. ถ้าดึงข้อมูลสำเร็จ บันทึกลง Session
        if (isset($userInfo['email'])) {
            $_SESSION['user_data'] = $userInfo;
        }
    }
}

// 4. ส่งผู้ใช้ออกจากโฟลเดอร์ auth กลับไปยังหน้าหลัก index.php
header('Location: /index.php');
exit;
67