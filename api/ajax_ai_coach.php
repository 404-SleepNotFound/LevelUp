<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$request = json_decode(file_get_contents('php://input'), true);
$action = $request['action'] ?? 'get_dashboard';

if ($action === 'get_dashboard') {
    // ข้อมูลจำลอง AI Coach
    $response = [
        "status" => "success",
        "user" => [
            "burnoutDetected" => true,
            "dailyTime" => "2h 15m",
            "dailyXp" => 150,
            "motivation" => "คุณเรียนต่อเนื่องมา 7 วันแล้ว! เหลืออีกเพียง 3 Skill ก็พร้อมสมัครตำแหน่ง Junior Backend Developer",
            "careerMatch" => 82
        ],
        "dailyPlan" => [
            ["title" => "ทบทวน SQL JOIN", "type" => "review", "completed" => true],
            ["title" => "ทำ Docker Lab (Basic)", "type" => "lab", "completed" => false, "note" => "ปรับแผนจากสัปดาห์หน้ามาเป็นวันนี้"],
            ["title" => "Quiz: Containerization", "type" => "quiz", "completed" => false]
        ],
        "knowledge" => [
            ["topic" => "Programming", "level" => "Intermediate", "score" => 78],
            ["topic" => "SQL", "level" => "Beginner", "score" => 42],
            ["topic" => "Git", "level" => "Advanced", "score" => 90]
        ],
        "strengths" => ["Python", "Git", "Linux"],
        "weaknesses" => ["SQL JOIN", "Docker Net", "Pointers"],
        "reviews" => [
            ["topic" => "Python Functions", "last_seen" => "14 วันก่อน"],
            ["topic" => "REST API Status Codes", "last_seen" => "20 วันก่อน"]
        ],
        "timeline" => [
            ["month" => "พฤษภาคม", "event" => "Skill Match Backend ถึง 82%"],
            ["month" => "เมษายน", "event" => "ทำ Docker Lab ครั้งแรก"],
            ["month" => "มีนาคม", "event" => "ผ่านคอร์ส SQL Basics"]
        ]
    ];

    echo json_encode($response);
    exit;
}

if ($action === 'calculate_whatif') {
    $hours = (float)$request['hours'];
    // สูตรคำนวณจำลอง (สมมติเวลาเรียนที่เหลือคือ 300 ชั่วโมง)
    $totalHoursNeeded = 300; 
    $days = ceil($totalHoursNeeded / $hours);
    $months = floor($days / 30);
    $remainingDays = $days % 30;

    $resultText = "";
    if ($months > 0) {
        $resultText .= "{$months} เดือน ";
    }
    $resultText .= "{$remainingDays} วัน";

    echo json_encode(["status" => "success", "prediction" => $resultText]);
    exit;
}
?>