<?php
session_start();
header('Content-Type: application/json');

// ตรวจสอบ Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mission_id = $_POST['mission_id'] ?? 0;
    $status = $_POST['status'] ?? 0;
    
    // ในระบบจริง จะนำ $mission_id และ $status ไปอัปเดตใน Database 
    // และคำนวณ XP + Level ที่ได้
    
    $response = [
        'status' => 'success',
        'message' => 'Mission updated',
        'xp_gained' => 10,
        'is_all_completed' => false // เช็คว่าครบ 4 ภารกิจหรือยัง
    ];
    
    // สมมติว่าถ้าส่ง mission_id=4 มาคือทำครบ
    if ($mission_id == 4) {
        $response['is_all_completed'] = true;
        $response['xp_gained'] = 50; // โบนัส
    }
    
    echo json_encode($response);
    exit;
}
echo json_encode(['status' => 'error']);
exit;