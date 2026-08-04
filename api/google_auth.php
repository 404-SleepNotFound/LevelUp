<?php
// google_auth.php (ขาไป: ส่งผู้ใช้ไปหน้า Google)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/google.php';

// สร้าง URL แล้ว Redirect ผู้ใช้ไปที่หน้าล็อกอินของ Google ทันที
$login_url = $client->createAuthUrl();
header("Location: " . $login_url);
exit;