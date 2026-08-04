<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// ตรวจสอบว่ามีการส่ง POST มาหรือไม่
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit;
}

$action = $_POST['action'] ?? '';

// 1. ระบบสมัคร Newsletter
if ($action === 'subscribe_newsletter') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    
    if (!$email) {
        echo json_encode(["status" => "error", "message" => "Invalid email address."]);
        exit;
    }
    
    // จำลองการ delay ตอบกลับ (สามารถนำไปเขียนบันทึกลง Database ต่อได้)
    sleep(1); 
    
    echo json_encode(["status" => "success", "message" => "Subscribed successfully."]);
    exit;
}

// 2. ระบบส่ง Feedback
if ($action === 'submit_feedback') {
    $type = htmlspecialchars($_POST['type'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');
    
    if (empty($message)) {
        echo json_encode(["status" => "error", "message" => "Message is required."]);
        exit;
    }

    // จำลองการ delay ตอบกลับ (สามารถนำไปเขียนบันทึกลง Database ต่อได้)
    sleep(1);

    echo json_encode(["status" => "success", "message" => "Feedback received."]);
    exit;
}

echo json_encode(["status" => "error", "message" => "Unknown action."]);
?>