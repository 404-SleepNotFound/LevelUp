<?php
session_start();
header('Content-Type: application/json');

usleep(500000); // Simulate network delay

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ตรวจสอบ CSRF Token
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrf_token)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Security Token.']);
        exit;
    }

    $action = $_POST['action'] ?? 'register';

    // 1. จำลองการตรวจสอบแบบ Real-time (Email / Username ซ้ำ)
    if ($action === 'check') {
        $field = $_POST['field'] ?? '';
        $value = $_POST['value'] ?? '';
        
        // Mock: ถือว่า admin@levelup.com และ username 'admin' มีคนใช้แล้ว
        if ($field === 'email' && $value === 'admin@levelup.com') {
            echo json_encode(['status' => 'error', 'message' => 'อีเมลนี้ถูกใช้งานแล้ว']);
        } elseif ($field === 'username' && $value === 'admin') {
            echo json_encode(['status' => 'error', 'message' => 'Username นี้ถูกใช้งานแล้ว']);
        } else {
            echo json_encode(['status' => 'success']);
        }
        exit;
    }

    // 2. การสมัครสมาชิก (Final Submit)
    if ($action === 'register') {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $username = htmlspecialchars($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $terms = isset($_POST['terms']) ? true : false;
        
        // Validation ฝั่งเซิร์ฟเวอร์
        if (!$terms) {
            echo json_encode(['status' => 'error', 'message' => 'กรุณายอมรับเงื่อนไขการใช้งาน']);
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'รูปแบบอีเมลไม่ถูกต้อง']);
            exit;
        }
        if (strlen($password) < 8) {
            echo json_encode(['status' => 'error', 'message' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร']);
            exit;
        }

        /*
        Code ในระบบจริง:
        1. เช็ค DB อีกครั้งว่า Email/Username ซ้ำไหม
        2. Hash Password: $hashed_password = password_hash($password, PASSWORD_ARGON2ID);
        3. Insert ข้อมูลลงตาราง users
        4. Insert ข้อมูล goals, careers, study_time ลงตาราง user_profiles
        5. สร้าง Verification Token และส่ง Email (PHPMailer)
        */

        // สร้าง Session ชั่วคราวเพื่อนำไปใช้ในหน้า Email Verification
        $_SESSION['temp_register_email'] = $email;

        // ตอบกลับ Success พร้อม Redirect ไปหน้า ยืนยันอีเมล
        echo json_encode([
            'status' => 'success', 
            'message' => 'สร้างบัญชีสำเร็จ กำลังพาท่านไปหน้ายืนยันอีเมล...',
            'redirect' => 'email-verification.php'
        ]);
        exit;
    }
}
echo json_encode(['status' => 'error', 'message' => 'Invalid Request']);
exit;