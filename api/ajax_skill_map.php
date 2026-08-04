<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$request = json_decode(file_get_contents('php://input'), true);
$action = $request['action'] ?? '';
$career = $request['career'] ?? 'backend';

// Mock Data: Skill Nodes
// status: 'mastered', 'learning', 'recommended', 'locked'
$nodes = [
    ["id" => "prog_basics", "label" => "Programming Basics", "category" => "Foundation", "status" => "mastered", "level" => 100, "demand" => 5, "x" => 400, "y" => 100, "depends" => []],
    ["id" => "python", "label" => "Python", "category" => "Language", "status" => "mastered", "level" => 80, "demand" => 5, "x" => 250, "y" => 250, "depends" => ["prog_basics"]],
    ["id" => "sql", "label" => "SQL", "category" => "Database", "status" => "mastered", "level" => 75, "demand" => 5, "x" => 550, "y" => 250, "depends" => ["prog_basics"]],
    ["id" => "oop", "label" => "OOP", "category" => "Concept", "status" => "learning", "level" => 40, "demand" => 4, "x" => 250, "y" => 400, "depends" => ["python"]],
    ["id" => "api", "label" => "RESTful API", "category" => "Architecture", "status" => "recommended", "level" => 0, "demand" => 5, "x" => 400, "y" => 400, "depends" => ["python", "oop"]],
    ["id" => "docker", "label" => "Docker", "category" => "DevOps", "status" => "recommended", "level" => 0, "demand" => 5, "x" => 700, "y" => 400, "depends" => ["api"]],
    ["id" => "redis", "label" => "Redis", "category" => "Database", "status" => "locked", "level" => 0, "demand" => 4, "x" => 550, "y" => 550, "depends" => ["sql"]],
    ["id" => "k8s", "label" => "Kubernetes", "category" => "DevOps", "status" => "locked", "level" => 0, "demand" => 4, "x" => 700, "y" => 550, "depends" => ["docker"]]
];

if ($action === 'get_map_data') {
    
    // จำลองผลลัพธ์ AI ตามสายอาชีพ
    $aiInsights = [];
    if ($career === 'backend') {
        $aiInsights = [
            "matchPercentage" => 72,
            "prediction" => "คุณมีความเข้าใจพื้นฐานที่ดีมาก! ขาดเพียงความรู้ด้าน DevOps เล็กน้อย หากเรียน <strong class='text-primary'>Docker</strong> จบ คุณจะพร้อมสำหรับงาน Junior Backend 100% (ใช้เวลาประมาณ 12 ชั่วโมง)",
            "missing" => [
                ["id" => "api", "label" => "RESTful API", "importance" => "High"],
                ["id" => "docker", "label" => "Docker", "importance" => "Critical"],
                ["id" => "redis", "label" => "Redis", "importance" => "Medium"]
            ]
        ];
    } elseif ($career === 'frontend') {
        $aiInsights = [
            "matchPercentage" => 35,
            "prediction" => "สายงานนี้ต้องการทักษะที่ต่างออกไป แนะนำให้เริ่มเรียน HTML/CSS และ JavaScript ก่อนเป็นอันดับแรก",
            "missing" => [
                ["id" => "js", "label" => "JavaScript", "importance" => "Critical"],
                ["id" => "react", "label" => "React.js", "importance" => "High"]
            ]
        ];
    } else {
        $aiInsights = [
            "matchPercentage" => 50,
            "prediction" => "คุณมีพื้นฐาน Python และ SQL ที่ดี เหมาะกับงาน Data แต่ต้องเพิ่มทักษะด้านสถิติ",
            "missing" => [
                ["id" => "pandas", "label" => "Pandas", "importance" => "Critical"]
            ]
        ];
    }

    echo json_encode([
        'status' => 'success',
        'nodes' => $nodes,
        'aiInsights' => $aiInsights
    ]);
    exit;
}

if ($action === 'get_skill_details') {
    $skillId = $request['skill_id'] ?? '';
    // หาข้อมูลใน Array $nodes
    $detail = null;
    foreach($nodes as $n) {
        if($n['id'] === $skillId) {
            $detail = $n;
            break;
        }
    }
    
    if ($detail) {
        // เพิ่ม Mock Data สำหรับหน้า Details
        $detail['description'] = "ทักษะนี้เป็นหัวใจสำคัญสำหรับการพัฒนาซอฟต์แวร์สมัยใหม่ ความต้องการในตลาดสูงมาก";
        $detail['levelString'] = ($detail['level'] >= 80) ? "Expert" : (($detail['level'] >= 40) ? "Intermediate" : "Beginner");
        
        // แปลง Array Depends เป็น Label เพื่อแสดงผล
        $preReqLabels = [];
        foreach($detail['depends'] as $depId) {
            foreach($nodes as $n) {
                if($n['id'] === $depId) {
                    $preReqLabels[] = ["label" => $n['label'], "status" => $n['status']];
                }
            }
        }
        $detail['preReqDetails'] = $preReqLabels;

        echo json_encode(['status' => 'success', 'data' => $detail]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Skill not found']);
    }
    exit;
}
?>