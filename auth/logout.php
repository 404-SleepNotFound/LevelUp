<?php
// 1. เช็กและเริ่ม Session อย่างปลอดภัย
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. ล้างข้อมูลและทำลาย Session
session_unset();
session_destroy();

// 3. ย้ายหน้ากลับไปที่ index.php
// ใช้ '/' เพื่อวิ่งกลับไปที่ Root ของเว็บไซต์เสมอ (เช่น https://levelup.quizchainat.com/index.php)
header('Location: ../index.php');
exit;