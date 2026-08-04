<?php
// ค้นหาและโหลด autoload.php จาก Composer
$autoloadPaths = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php',
    __DIR__ . '/vendor/autoload.php',
    $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php'
];

$loaded = false;
foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $loaded = true;
        break;
    }
}

// หากยังไม่พบไฟล์ autoload.php ให้แสดงข้อความแจ้งเตือนแทนการเกิด Fatal Error
if (!$loaded) {
    die('<b>Error:</b> ไม่พบไฟล์ <code>vendor/autoload.php</code> กรุณารันคำสั่ง <code>composer install</code> หรือ <code>composer require google/apiclient</code> บนเซิร์ฟเวอร์ของคุณก่อนใช้งาน');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// กำหนดค่าต่างๆ จาก Google Console
$clientID = '391621497018-cbigmvo3gcaiqjk89rij042m30cqtmks.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'https://levelup.quizchainat.com/auth/callback.php'; // ต้องตรงกับที่ตั้งค่าไว้ในหน้าเว็บ Google

// สร้าง Object ของ Google Client
$client = new Google\Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);

// บอก Google ว่าเราขอแค่ อีเมล และ โปรไฟล์ นะ
$client->addScope("email");
$client->addScope("profile");

// สมมติว่านี่คือตัวแปรเชื่อมต่อ Database ของคุณ (PDO)
// $db = new PDO('mysql:host=localhost;dbname=your_db', 'username', 'password');