<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$request = json_decode(file_get_contents('php://input'), true);
$action = $request['action'] ?? '';

if ($action === 'get_profile') {
    // จำลองข้อมูลผู้ใช้จากฐานข้อมูล
    $response = [
        "status" => "success",
        "user" => [
            "name" => "Sompong Developer",
            "username" => "@sompong_dev",
            "location" => "Bangkok, Thailand",
            "website" => "sompong.dev",
            "join_date" => "March 2023",
            "level" => 42,
            "xp" => "18,250",
            "streak" => 14,
            "github" => "sompongdev"
        ],
        "ai_summary" => "คุณมีความโดดเด่นด้าน <strong>Backend Architecture</strong> และการเขียน <strong>RESTful API</strong> แนะนำให้เพิ่มทักษะด้าน Cloud (AWS/Docker) เพื่อให้พร้อมสมบูรณ์สำหรับตำแหน่ง Mid-Level Backend Developer",
        "career_readiness" => [
            "role" => "Backend Developer",
            "progress" => 86,
            "missing" => ["Kubernetes", "CI/CD Pipeline", "Redis"]
        ],
        "skills" => [
            ["name" => "PHP & Laravel", "score" => 92, "color" => "primary"],
            ["name" => "SQL (PostgreSQL)", "score" => 85, "color" => "info"],
            ["name" => "Docker", "score" => 60, "color" => "warning"]
        ],
        "stats" => [
            "courses" => 18,
            "labs" => 72,
            "time" => "486"
        ],
        "portfolio" => [
            [
                "title" => "E-Commerce API Service",
                "desc" => "Microservice architecture using Node.js & Docker",
                "image" => "https://images.unsplash.com/photo-1555099962-4199c345e5dd?w=400&q=80",
                "tags" => ["Node.js", "Docker", "MongoDB"]
            ],
            [
                "title" => "Realtime Chat App",
                "desc" => "WebSockets implementation for live messaging",
                "image" => "https://images.unsplash.com/photo-1618401471353-b98afee0b2eb?w=400&q=80",
                "tags" => ["Vue.js", "Socket.io", "Redis"]
            ]
        ],
        "timeline" => [
            ["date" => "Today", "event" => "Completed <strong>Docker Networking Lab</strong>", "icon" => "bi-hdd-network", "color" => "text-primary"],
            ["date" => "Yesterday", "event" => "Earned Badge <strong>SQL Master</strong>", "icon" => "bi-award", "color" => "text-warning"],
            ["date" => "3 days ago", "event" => "Finished Course <strong>Advanced REST APIs</strong>", "icon" => "bi-check-circle", "color" => "text-success"]
        ],
        "badges" => [
            ["name" => "Python Master", "icon" => "🐍"],
            ["name" => "SQL Explorer", "icon" => "🗄️"],
            ["name" => "100 Days", "icon" => "🔥"],
            ["name" => "Top Coder", "icon" => "💻"]
        ]
    ];
    echo json_encode($response);
    exit;
}

if ($action === 'update_profile') {
    // จำลองการรับข้อมูลอัปเดต
    $name = htmlspecialchars($request['data']['name'] ?? '');
    echo json_encode(["status" => "success", "message" => "Profile updated successfully.", "newName" => $name]);
    exit;
}

echo json_encode(["status" => "error", "message" => "Invalid action"]);
?>