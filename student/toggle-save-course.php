<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../config/db.php';

// ต้อง Login ก่อนถึงจะบันทึกคอร์สได้
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'กรุณาเข้าสู่ระบบก่อนบันทึกคอร์สเรียน'
    ]);
    exit;
}

$user_id   = (int) $_SESSION['user_id'];
$course_id = (int) ($_POST['course_id'] ?? 0);

if ($course_id <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'ไม่พบรหัสคอร์สเรียนที่ต้องการบันทึก'
    ]);
    exit;
}

try {
    // ตรวจสอบว่าคอร์สนี้ยืนยันมีอยู่จริงหรือไม่
    $stmt_check = $conn->prepare("SELECT id FROM `course` WHERE id = :course_id");
    $stmt_check->execute([':course_id' => $course_id]);
    if (!$stmt_check->fetch(PDO::FETCH_ASSOC)) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'ไม่พบคอร์สเรียนนี้ในระบบ'
        ]);
        exit;
    }

    // เช็คว่าผู้ใช้บันทึกคอร์สนี้ไว้แล้วหรือยัง
    $stmt = $conn->prepare("SELECT id FROM `saved_courses` WHERE user_id = :user_id AND course_id = :course_id");
    $stmt->execute([':user_id' => $user_id, ':course_id' => $course_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // มีอยู่แล้ว -> ยกเลิกการบันทึก (ลบออกจากฐานข้อมูล)
        $del = $conn->prepare("DELETE FROM `saved_courses` WHERE id = :id");
        $del->execute([':id' => $existing['id']]);

        echo json_encode([
            'success' => true,
            'saved'   => false,
            'message' => 'ยกเลิกการบันทึกคอร์สเรียนแล้ว'
        ]);
    } else {
        // ยังไม่มี -> บันทึกใหม่ลงฐานข้อมูล
        $ins = $conn->prepare("INSERT INTO `saved_courses` (user_id, course_id) VALUES (:user_id, :course_id)");
        $ins->execute([':user_id' => $user_id, ':course_id' => $course_id]);

        echo json_encode([
            'success' => true,
            'saved'   => true,
            'message' => 'บันทึกคอร์สเรียนไว้ดูทีหลังเรียบร้อยแล้ว'
        ]);
    }
} catch (PDOException $e) {
    error_log('toggle-save-course.php error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง'
    ]);
}
