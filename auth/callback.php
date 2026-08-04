<?php
// 1. เริ่ม Session และเปิดการแสดง Error ทั้งหมด
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ดึงค่าการตั้งค่าจาก config/google.php
require_once __DIR__ . '/../config/google.php';

echo "<h2>🔍 เริ่มการตรวจสอบระบบ Google Login</h2>";

if (!isset($_GET['code'])) {
    die("<h3 style='color:red;'>❌ ขั้นตอนที่ 1 พัง: ไม่ได้รับค่า 'code' จาก Google</h3>");
}
echo "<p style='color:green;'>✅ <b>ขั้นตอนที่ 1 ผ่าน:</b> รับค่า Code จาก Google สำเร็จแล้ว</p>";

// 1. แลกเปลี่ยน Authorization Code เป็น Access Token
$tokenUrl = 'https://oauth2.googleapis.com/token';
$postData = array(
    'code'          => $_GET['code'],
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
$curlErr = curl_error($ch);
curl_close($ch);

if ($curlErr) {
    die("<h3 style='color:red;'>❌ cURL Error ( Token Request Failed ): {$curlErr}</h3>");
}

$tokenData = json_decode($response, true);

echo "<h4>📦 ข้อมูล Response จาก Google Token:</h4><pre>";
print_r($tokenData);
echo "</pre>";

if (!isset($tokenData['access_token'])) {
    die("<h3 style='color:red;'>❌ ขั้นตอนที่ 2 พัง: Google ไม่ยอมออก access_token ให้ (โปรดเช็ก config/google.php)</h3>");
}
echo "<p style='color:green;'>✅ <b>ขั้นตอนที่ 2 ผ่าน:</b> ได้รับ access_token เรียบร้อยแล้ว</p>";

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
$curlErr2 = curl_error($ch2);
curl_close($ch2);

if ($curlErr2) {
    die("<h3 style='color:red;'>❌ cURL Error ( UserInfo Request Failed ): {$curlErr2}</h3>");
}

$userInfo = json_decode($userResponse, true);

echo "<h4>👤 ข้อมูล Profile จาก Google:</h4><pre>";
print_r($userInfo);
echo "</pre>";

// 3. ทดสอบการบันทึกลง Session
if (isset($userInfo['email'])) {
    $_SESSION['user_data'] = $userInfo;
    $_SESSION['user_login'] = array(
        'id'      => $userInfo['id'] ?? '',
        'name'    => $userInfo['name'] ?? ($userInfo['email'] ?? 'Google User'),
        'email'   => $userInfo['email'],
        'picture' => $userInfo['picture'] ?? '',
        'level'   => 1
    );

    echo "<h3 style='color:green;'>🎉 สำเร็จ! บันทึก Session เรียบร้อยแล้ว</h3>";
    echo "<h4>ข้อมูล Session ปัจจุบัน:</h4><pre>";
    print_r($_SESSION);
    echo "</pre>";
    echo "<a href='../index.php' style='padding:10px 20px; background:blue; color:white; text-decoration:none; border-radius:5px;'>คลิกตรงนี้เพื่อกลับหน้าหลัก</a>";
} else {
    echo "<h3 style='color:red;'>❌ ขั้นตอนที่ 3 พัง: ดึงข้อมูล Email จาก Google ไม่สำเร็จ</h3>";
}