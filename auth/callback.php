<?php
$ROOT_PATH = '../';

// ปรับให้เช็ก Session ก่อนเริ่ม เพื่อป้องกัน Error
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/google.php';
// เปิดการใช้งานไฟล์เชื่อมต่อฐานข้อมูล
require_once __DIR__ . '/../config/db.php'; 

// 1. ตรวจสอบว่ามี Code ส่งกลับมาจาก Google หรือไม่
if (isset($_GET['code'])) {
    try {
        // แลกเปลี่ยน Code เป็น Access Token
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        
        // ตรวจสอบว่าไม่มีข้อผิดพลาดเกี่ยวกับ Token
        if (!isset($token['error'])) {
            $client->setAccessToken($token['access_token']);
            
            // 2. ดึงข้อมูลโปรไฟล์จาก Google OAuth
            $google_oauth = new Google\Service\Oauth2($client);
            $google_account_info = $google_oauth->userinfo->get();
            
            // ตัวแปรข้อมูลโปรไฟล์ของผู้ใช้ที่ Google ส่งมาให้
            $google_id = $google_account_info->id;
            $email     = $google_account_info->email;
            $name      = $google_account_info->name;
            $img       = $google_account_info->picture;

            // ===================================================
            // [LOGIC หลังบ้านสำหรับระบบจัดการสมาชิก]
            // ===================================================
            
            // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
            $sql = "SELECT u.*, s.level, s.exp FROM users u
                    LEFT JOIN status_users s ON u.id = s.user_id
                    WHERE u.email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$email]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && $result['email'] == $email) {
                // กรณีมีอีเมลนี้ในฐานข้อมูลอยู่แล้ว
                $_SESSION['user_login'] = [
                    'user_id'   => $result['id'],
                    'email'     => $result['email'],
                    'name'      => $result['name'],
                    'img'       => $result['img'],
                    'level'     => $result['level'] ?? 1,
                    'user_exp'  => $result['exp'] ?? 0,
                    'user_role' => $result['user_role'] ?? 'user'
                ];
            } else {
                // กรณีเป็นสมาชิกใหม่ (บันทึกข้อมูลลงฐานข้อมูล)
                $sql = "INSERT INTO users (google_id, name, email, img) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$google_id, $name, $email, $img]);

                $lastId = $conn->lastInsertId();

                $sql = "INSERT INTO status_users (user_id, level, exp) VALUES (?, 1, 0)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$lastId]);

                $_SESSION['user_login'] = [
                    'user_id'   => $lastId,
                    'email'     => $email,
                    'name'      => $name,
                    'img'       => $img,
                    'level'     => 1,
                    'user_exp'  => 0,
                    'user_role' => 'user'
                ];
            }
            
            // ยืนยันสิทธิ์สำเร็จ ส่งผู้ใช้ไปที่หน้า index
            header("Location: ../index.php");
            exit;
            
        } else {
            // เกิดข้อผิดพลาดใน Token
            header("Location: login.php?error=invalid_token");
            exit;
        }
    } catch (Exception $e) {
        // เกิดข้อผิดพลาดในการเชื่อมต่อ API
        header("Location: login.php?error=api_failure");
        exit;
    }
} else {
    // หากเข้าหน้าไฟล์นี้โดยตรงโดยไม่มีการส่งค่ามาจาก Google
    header("Location: login.php");
    exit;
}