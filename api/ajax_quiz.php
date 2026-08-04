<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$request = json_decode(file_get_contents('php://input'), true);
$action =$request['action'] ?? '';

// Mock Data: คลังข้อสอบ
$quizData = [
    [
        "id" => "q1",
        "type" => "multiple_choice",
        "category" => "Variables",
        "question" => "ข้อใดคือการประกาศตัวแปรที่ถูกต้องใน Python?",
        "options" => [
            ["id" => "o1", "text" => "var x = 10"],
            ["id" => "o2", "text" => "x = 10"],
            ["id" => "o3", "text" => "int x = 10"],
            ["id" => "o4", "text" => "$x = 10"]
        ],
        "correct" => "o2",
        "explanation" => "Python ไม่ต้องระบุประเภทตัวแปร (Dynamically typed) สามารถพิมพ์ `x = 10` ได้เลย"
    ],
    [
        "id" => "q2",
        "type" => "multiple_choice",
        "category" => "Loop",
        "question" => "ผลลัพธ์ของโค้ดนี้คืออะไร?\n```python\nfor i in range(2):\n    print(i)\n```",
        "options" => [
            ["id" => "o1", "text" => "0 1 2"],
            ["id" => "o2", "text" => "1 2"],
            ["id" => "o3", "text" => "0 1"],
            ["id" => "o4", "text" => "Error"]
        ],
        "correct" => "o3",
        "explanation" => "`range(2)` จะสร้าง sequence เริ่มจาก 0 ถึง n-1 (คือ 0 และ 1)"
    ],
    [
        "id" => "q3",
        "type" => "multiple_choice",
        "category" => "Function",
        "question" => "คำสั่งใดใช้สำหรับสร้างฟังก์ชันใน Python?",
        "options" => [
            ["id" => "o1", "text" => "function myFunc():"],
            ["id" => "o2", "text" => "def myFunc():"],
            ["id" => "o3", "text" => "create myFunc():"],
            ["id" => "o4", "text" => "fn myFunc():"]
        ],
        "correct" => "o2",
        "explanation" => "Python ใช้ Keyword `def` นำหน้าชื่อฟังก์ชันเสมอ"
    ]
];

if ($action === 'get_quiz') {
    // ในระบบจริงอาจจะ Query ตาม Course ID
    // ไม่ส่งเฉลยไปให้ Client ป้องกันการโกง
    $safeQuizData = array_map(function($q) {
        unset($q['correct']);
        unset($q['explanation']);
        return $q;
    }, $quizData);
    
    echo json_encode(['status' => 'success', 'questions' => $safeQuizData, 'timeLimit' => 15 * 60]);
    exit;
}

if ($action === 'submit_quiz') {
    $userAnswers = $request['answers'] ?? [];
    $score = 0;
    $total = count($quizData);
    $analysis = [];
    $gapMap = [];

    // ตรวจคำตอบและวิเคราะห์ Confidence Score
    foreach ($quizData as $q) {
        $userAns = null;
        foreach ($userAnswers as $ua) {
            if ($ua['questionId'] === $q['id']) {
                $userAns = $ua;
                break;
            }
        }

        $isCorrect = ($userAns && $userAns['selectedId'] === $q['correct']);
        $confidence = $userAns ? (int)$userAns['confidenceLevel'] : 0;
        
        if ($isCorrect) $score++;

        // Logic AI วิเคราะห์ Gap
        $status = "❌";
        $color = "text-danger";
        if ($isCorrect && $confidence >= 3) {
            $status = "✅"; // เข้าใจจริง
            $color = "text-success";
        } elseif ($isCorrect && $confidence <= 2) {
            $status = "🟡"; // เดาถูก / ยังไม่ชัวร์
            $color = "text-warning";
        } elseif (!$isCorrect && $confidence >= 3) {
            $status = "⚠️"; // Misconception (เข้าใจผิดแต่มั่นใจ)
            $color = "text-danger";
        }

        $gapMap[] = [
            "category" => $q['category'],
            "status" => $status,
            "color" => $color
        ];
    }

    $percent = round(($score / $total) * 100);
    
    // จำลอง AI Feedback
    $aiFeedback = "";
    if ($percent >= 80) {
        $aiFeedback = "คุณมีพื้นฐาน Python ที่ยอดเยี่ยมมาก! การประกาศตัวแปรและการใช้ Loop แม่นยำ แต่มีบางจุดที่คุณตอบถูกแต่ยัง <b>ไม่มั่นใจ (🟡)</b> แนะนำให้ทบทวนเพิ่มเติมเล็กน้อยเพื่อความมั่นใจ 100%";
    } elseif ($percent >= 50) {
        $aiFeedback = "คุณมีความเข้าใจพื้นฐานระดับกลาง พบว่ามี <b>ความเข้าใจที่คลาดเคลื่อน (⚠️)</b> ในเรื่อง Function แนะนำให้กลับไปดู Video Lesson 3 อีกครั้งครับ";
    } else {
        $aiFeedback = "ดูเหมือนคุณจะยังสับสนกับโครงสร้างพื้นฐานของ Python แนะนำให้ทำ Coding Lab พื้นฐานอีก 1-2 ครั้งก่อนเริ่มเรียนบทถัดไปครับ";
    }

    echo json_encode([
        'status' => 'success',
        'score' => $score,
        'total' => $total,
        'percentage' => $percent,
        'aiFeedback' => $aiFeedback,
        'gapMap' => $gapMap
    ]);
    exit;
}
?>