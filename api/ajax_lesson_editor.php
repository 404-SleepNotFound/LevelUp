<?php
// ajax_lesson_editor.php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

header('Content-Type: application/json; charset=utf-8');

// ตรวจสอบสิทธิ์ผู้ใช้งาน (Authentication Guard)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    // Note: ปรับเงื่อนไขการเช็ค Session ให้ตรงกับระบบเดิมของคุณ
    // echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']); exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'save_lesson':
        try {
            // รับค่าพื้นฐานจาก Form
            $lesson_id   = intval($_POST['lesson_id'] ?? 0);
            $title       = trim($_POST['title'] ?? '');
            $chapter_id  = intval($_POST['chapter_id'] ?? 0);
            $order_num   = intval($_POST['order_num'] ?? 1);
            $description = trim($_POST['description'] ?? '');
            $lesson_type = $_POST['lesson_type'] ?? 'Theory Lecture';
            $status      = $_POST['status'] ?? 'draft';
            
            // ข้อมูล Meta & Dynamic Blocks (รับมาเป็น JSON String)
            $blocks_json = $_POST['blocks_data'] ?? '[]';
            $objectives  = $_POST['objectives'] ?? '';
            $time_est    = $_POST['learning_time'] ?? '';
            $resources   = $_POST['resources'] ?? '';
            $slug        = $_POST['slug'] ?? '';

            // [DATABASE OPERATION EXAMPLE]
            /*
            if ($lesson_id > 0) {
                $stmt = $db->prepare("UPDATE lessons SET title=?, chapter_id=?, order_num=?, description=?, lesson_type=?, status=?, blocks_data=?, objectives=?, learning_time=?, resources=?, slug=?, updated_at=NOW() WHERE id=?");
                $stmt->execute([$title, $chapter_id, $order_num, $description, $lesson_type, $status, $blocks_json, $objectives, $time_est, $resources, $slug, $lesson_id]);
            } else {
                $stmt = $db->prepare("INSERT INTO lessons (title, chapter_id, order_num, description, lesson_type, status, blocks_data, objectives, learning_time, resources, slug, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$title, $chapter_id, $order_num, $description, $lesson_type, $status, $blocks_json, $objectives, $time_est, $resources, $slug]);
                $lesson_id = $db->lastInsertId();
            }
            */

            echo json_encode([
                'status' => 'success',
                'message' => ($status === 'published') ? 'เผยแพร่บทเรียนเรียบร้อยแล้ว!' : 'บันทึกแบบร่างสำเร็จ',
                'lesson_id' => $lesson_id ?: 999
            ]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'delete_lesson':
        $lesson_id = intval($_POST['lesson_id'] ?? 0);
        // DB Delete Query logic
        echo json_encode(['status' => 'success', 'message' => 'ลบบทเรียนเรียบร้อยแล้ว']);
        break;

    case 'ai_assistant':
        $mode = $_POST['ai_mode'] ?? 'content';
        $context = $_POST['context'] ?? '';
        
        // Mockup AI Response Engine
        $ai_responses = [
            'content' => "โครงสร้างเนื้อหาแนะนำเพิ่มเติม:\n1. การประกาศตัวแปรด้วย let และ const\n2. Temporal Dead Zone (TDZ)\n3. ตัวอย่าง Scope ใน Block Statement",
            'summarize' => "สรุปบทเรียน: เรียนรู้ความแตกต่างระหว่าง Global, Function และ Block Scope รวมถึงแนวปฏิบัติตัวแปรสมัยใหม่",
            'language' => "ปรับปรุงภาษา: ทำความเข้าใจหลักการจัดสรรพื้นที่ตัวแปรระดับบล็อกคำสั่งอย่างมีประสิทธิภาพ",
            'spell' => "ตรวจทานแล้ว: ไม่พบคำผิดในเนื้อหาปัจจุบัน",
            'quiz' => "คำถามแนะนำ: 'ข้อใดคือความแตกต่างหลักระหว่าง var และ let ใน JavaScript?'",
            'flashcard' => "Flashcard 1: Q: Scope คืออะไร? A: ขอบเขตการเข้าถึงและใช้งานตัวแปรในโปรแกรม"
        ];

        echo json_encode([
            'status' => 'success',
            'result' => $ai_responses[$mode] ?? 'ประมวลผลเรียบร้อยแล้ว'
        ]);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid Action']);
        break;
}
exit;