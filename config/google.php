<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// กำหนดค่าต่างๆ จาก Google Console
$clientID     = '391621497018-cbigmvo3gcaiqjk89rij042m30cqtmks.apps.googleusercontent.com';
$clientSecret = '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';

// ตั้งค่าให้ Google ส่งข้อมูลกลับมาที่ index.php ตรงๆ
$redirectUri  = 'https://levelup.quizchainat.com/index.php';

// สร้าง URL สำหรับ Redirect ไปหน้า Login ของ Google
$googleParams = [
    'client_id'     => $clientID,
    'redirect_uri'  => $redirectUri,
    'response_type' => 'code',
    'scope'         => 'openid email profile',
    'access_type'   => 'online',
    'prompt'        => 'select_account'
];
$googleAuthUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($googleParams);

// หากมีการส่ง code กลับมาจาก Google ให้ทำการแลก Token และดึงข้อมูลผู้ใช้ทันที
if (isset($_GET['code'])) {
    $code = $_GET['code'];

    $tokenUrl = 'https://oauth2.googleapis.com/token';
    $postData = [
        'code'          => $code,
        'client_id'     => $clientID,
        'client_secret' => $clientSecret,
        'redirect_uri'  => $redirectUri,
        'grant_type'    => 'authorization_code'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $tokenData = json_decode($response, true);

    if (isset($tokenData['access_token'])) {
        // ดึงข้อมูลโปรไฟล์ผู้ใช้จาก Google
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $tokenData['access_token'];
        $userInfoJson = file_get_contents($userInfoUrl);
        $userInfo = json_decode($userInfoJson, true);

        // บันทึกข้อมูลลง Session
        $_SESSION['user_data'] = $userInfo;

        // รีเฟรช URL เพื่อลบ query string (?code=...) ออกจากช่องสแกน URL
        header('Location: /index.php');
        exit;
    }
}