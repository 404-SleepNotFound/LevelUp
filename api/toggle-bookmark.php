<?php
session_start();
$id = $_POST['id'] ?? '';
// ในระบบจริง ทำการ Update Database (Insert/Delete)
$is_bookmarked = rand(0,1) == 1; // จำลองผลลัพธ์

header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'is_bookmarked' => $is_bookmarked,
    'message' => $is_bookmarked ? 'บันทึกคอร์สเรียบร้อย' : 'นำคอร์สออกจากรายการที่บันทึก'
]);
exit;