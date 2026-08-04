<?php
// ajax_lesson.php
session_start();
header('Content-Type: application/json');

// ตรวจสอบ Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'chat') {
        $msg = htmlspecialchars($_POST['message'] ?? '');
        $context = htmlspecialchars($_POST['context'] ?? '');
        
        // AI Logic เบื้องต้น (เชื่อม OpenAI API หรือ logic ภายใน)
        echo json_encode([
            'status' => 'success',
            'reply' => "จากที่คุณถามว่า '$msg' (ในนาทีที่ $context) ขออธิบายดังนี้ครับ..."
        ]);
        exit;
    }
    
    if ($action === 'save_highlight') {
        // Save to Database logic here
        echo json_encode(['status' => 'success']);
        exit;
    }
}
echo json_encode(['status' => 'error', 'message' => 'Invalid Request']);
?>