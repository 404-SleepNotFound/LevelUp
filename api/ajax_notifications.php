<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$request = json_decode(file_get_contents('php://input'), true);
$action = $request['action'] ?? '';

if ($action === 'fetch_notifications') {
    // จำลองข้อมูลจาก Database จัดอันดับความสำคัญ (AI Priority)
    $notifications = [
        [
            "id" => 101,
            "category" => "assignment",
            "priority" => "critical",
            "title" => "Assignment Due Soon",
            "message" => "เหลือเวลาอีก 6 ชั่วโมงในการส่งงาน <strong>RESTful API Design</strong>",
            "time" => "2 hours ago",
            "is_read" => false,
            "icon" => "bi-clock-history",
            "icon_color" => "text-danger",
            "action" => ["label" => "Submit Assignment", "url" => "#submit", "btn_type" => "btn-danger"],
            "date_group" => "Today"
        ],
        [
            "id" => 102,
            "category" => "ai",
            "priority" => "critical",
            "title" => "🔥 Streak Reminder",
            "message" => "เหลืออีก 1 ชั่วโมงก่อนที่ Streak ของคุณจะขาด! เรียนอีก 10 นาทีเพื่อรักษา Streak",
            "time" => "3 hours ago",
            "is_read" => false,
            "icon" => "bi-fire",
            "icon_color" => "text-warning",
            "action" => ["label" => "Keep Learning", "url" => "#learn", "btn_type" => "btn-warning"],
            "date_group" => "Today"
        ],
        [
            "id" => 103,
            "category" => "lab",
            "priority" => "important",
            "title" => "New Virtual Lab Available",
            "message" => "Lab ใหม่: <strong>Docker Swarm Cluster</strong> พร้อมให้คุณฝึกฝนแล้ว",
            "time" => "5 hours ago",
            "is_read" => false,
            "icon" => "bi-laptop",
            "icon_color" => "text-info",
            "action" => ["label" => "Enter Lab", "url" => "#lab", "btn_type" => "btn-outline-info"],
            "date_group" => "Today"
        ],
        [
            "id" => 104,
            "category" => "ai",
            "priority" => "normal",
            "title" => "AI Coach Recommendation",
            "message" => "จากผลคะแนน Quiz ล่าสุด แนะนำให้ทบทวนเรื่อง <strong>SQL JOIN & Subqueries</strong>",
            "time" => "Yesterday at 18:30",
            "is_read" => true,
            "icon" => "bi-robot",
            "icon_color" => "text-primary",
            "action" => ["label" => "Review Lesson", "url" => "#lesson", "btn_type" => "btn-outline-primary"],
            "date_group" => "Yesterday"
        ],
        [
            "id" => 105,
            "category" => "achievement",
            "priority" => "normal",
            "title" => "🏆 Level Up!",
            "message" => "ยินดีด้วย! คุณอัปเลเวลถึง <strong>Level 15</strong> แล้ว",
            "time" => "Yesterday at 14:00",
            "is_read" => true,
            "icon" => "bi-trophy",
            "icon_color" => "text-success",
            "action" => null,
            "date_group" => "Yesterday"
        ]
    ];
    
    echo json_encode(["status" => "success", "data" => $notifications]);
    exit;
}

if ($action === 'mark_read') {
    $id = $request['id'] ?? null;
    echo json_encode(["status" => "success", "message" => "Notification $id marked as read"]);
    exit;
}

if ($action === 'mark_all_read') {
    echo json_encode(["status" => "success", "message" => "All notifications marked as read"]);
    exit;
}

if ($action === 'delete') {
    $id = $request['id'] ?? null;
    echo json_encode(["status" => "success", "message" => "Notification $id deleted"]);
    exit;
}

echo json_encode(["status" => "error", "message" => "Invalid action"]);
?>