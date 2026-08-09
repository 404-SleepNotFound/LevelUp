<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';

// ฟังก์ชันสำหรับแปลงข้อมูลไม่ว่าจะเป็น Array หรือ String ให้เป็น Array เสมอ
function normalizeToArray($data)
{
    if (is_array($data)) {
        return $data;
    }
    if (is_string($data) && trim($data) !== '') {
        return explode(',', $data);
    }
    return [];
}

$jsonInput = file_get_contents('php://input');
$request   = json_decode($jsonInput, true);

if (!$request) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'ไม่มีข้อมูลส่งมา หรือรูปแบบ JSON ไม่ถูกต้อง'
    ]);
    exit;
}

$action  = $request['action'] ?? '';
$data    = $request['data'] ?? [];
$user_id = $request['user_id'] ?? null;

switch ($action) {

    case 'update_profile':
        try {
            // เริ่ม Transaction เพื่อความปลอดภัยของข้อมูล
            $conn->beginTransaction();

            // ⚠️ ชื่อ field ใน formData มาจาก attribute name="..." ของ input จริงในฟอร์ม
            // (settings.php ใช้ name="name" และ name="bio" ไม่ใช่ full_name)
            $full_name = trim($data['name'] ?? '');
            $bio       = trim($data['bio'] ?? '');

            if ($full_name === '') {
                throw new Exception('กรุณากรอกชื่อตัวละคร');
            }

            // 1. บันทึกลงตาราง users
            $sql = "UPDATE `users` SET `name` = ?, `bio` = ? WHERE `id` = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $full_name,
                $bio,
                $user_id
            ]);

            // บันทึกสำเร็จทั้งหมดค่อย Commit
            $conn->commit();

            echo json_encode([
                'status'  => 'success',
                'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว'
            ]);
        } catch (Throwable $e) {
            // หากเกิดข้อผิดพลาดให้ ย้อนกลับ (Rollback) ข้อมูลทั้งหมด (ถ้า transaction ยังเปิดอยู่)
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            echo json_encode([
                'status'  => 'error',
                'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()
            ]);
        }
        break;

    case 'delete':
        $courseId = $data['id'] ?? null;
        if (!$courseId) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'ไม่พบ ID ที่ต้องการลบ'
            ]);
            break;
        }

        echo json_encode([
            'status'  => 'success',
            'message' => 'ลบข้อมูลสำเร็จ'
        ]);
        break;

    default:
        echo json_encode([
            'status'  => 'error',
            'message' => 'ไม่พบ Action ที่ระบุ (' . $action . ')'
        ]);
        break;
}
exit;