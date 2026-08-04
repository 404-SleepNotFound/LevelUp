<?php
require_once __DIR__ . '/../vendor/autoload.php';

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