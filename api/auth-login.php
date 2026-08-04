<?php
session_start();
header('Content-Type: application/json');

// ตั้งเวลาจำลองการโหลด (Delay) เล็กน้อยเพื่อให้เห็น UI Loading (ใน Production จริงเอาออกได้)
usleep(800000); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;

    // 1. ตรวจสอบ CSRF Token (Security)
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrf_token)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Security Token. กรุณารีเฟรชหน้าแล้วลองใหม่']);
        exit;
    }

    // 2. ตรวจสอบค่าว่าง
    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกอีเมลและรหัสผ่านให้ครบถ้วน']);
        exit;
    }

    // 3. ตรวจสอบรูปแบบ Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'รูปแบบอีเมลไม่ถูกต้อง']);
        exit;
    }

    // 4. Mock Authentication (ในระบบจริงต้อง Query ข้อมูลจาก DB และใช้ password_verify)
    /* 
    ตัวอย่าง Code ในระบบจริง:
    $stmt = $pdo->prepare("SELECT id, password, role FROM users WHERE email = ? AND is_active = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        // Success logic
    }
    */
    
    // จำลอง User ที่มีในระบบ (Mocking)
    if ($email === 'student@levelup.com' && $password === 'password123') {
        
        // --- กรณี Login สำเร็จ ---
        
        // ป้องกัน Session Fixation
        session_regenerate_id(true); 
        
        // สร้าง Session 
        $_SESSION['user_id'] = 1;
        $_SESSION['role'] = 'student';
        $_SESSION['email'] = $email;
        
        // บันทึก Token กรณี Remember Me
        if ($remember) {
            $token = bin2hex(random_bytes(64));
            // บันทึก $token ลงตาราง remember_tokens ในฐานข้อมูล
            setcookie('remember_token', $token, time() + (86400 * 30), "/", "", true, true); // 30 วัน, HttpOnly, Secure
        }

        // บันทึก Login History (Mock)
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        // Insert into login_logs...

        echo json_encode([
            'status' => 'success', 
            'message' => 'เข้าสู่ระบบสำเร็จ กำลังพาท่านไปหน้า Dashboard...',
            'redirect' => 'dashboard.php'
        ]);
    } else {
        // --- กรณี Login ไม่สำเร็จ ---
        echo json_encode([
            'status' => 'error', 
            'message' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง หรือบัญชีอาจถูกระงับ'
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request Method']);
}
exit;