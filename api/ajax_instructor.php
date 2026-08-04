<?php
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');

// จัดการความปลอดภัย ป้องกันช่องโหว่การโจมตีประเภท XSS
function safeEscape($str) {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

$action = safeEscape($_GET['action'] ?? '');

// 1. ส่งค่ากลับข้อมูลตัวเลขสถิติภาพรวมผู้เรียน (Analytics Payload)
if ($action === 'fetch_stats') {
    // หน่วงความเร็วจำลองทราฟฟิกเครือข่าย เพื่อให้มองเห็นความสวยงามของ Skeleton Loader
    usleep(200000);

    $statistics_cards = [
        ['title' => 'รายได้รวมสะสม (เดือนนี้)', 'value' => '฿152,400', 'icon' => 'bi-wallet2', 'variant' => 'bg-success-subtle text-success'],
        ['title' => 'นักเรียนสมัครเรียนรวม', 'value' => '4,920 คน', 'icon' => 'bi-people', 'variant' => 'bg-primary-subtle text-primary'],
        ['title' => 'เรตติ้งความพึงพอใจเฉลี่ย', 'value' => '4.96 / 5.0', 'icon' => 'bi-star-fill', 'variant' => 'bg-warning-subtle text-warning'],
        ['title' => 'อัตราการทำแล็บผ่าน', 'value' => '91.4%', 'icon' => 'bi-terminal-dash', 'variant' => 'bg-info-subtle text-info']
    ];
    echo json_encode(['status' => 'success', 'payload' => $statistics_cards]);
    exit;
}

// 2. ดึงข้อมูลกิจกรรมเรียลไทม์ล่าสุดของผู้เรียนบนคอร์ส
if ($action === 'fetch_activities') {
    $timeline_feed = [
        ['icon' => 'bi-person-plus-fill', 'color' => 'bg-primary-subtle text-primary', 'message' => 'คุณ Kitisak ได้สมัครเข้าเรียนคอร์ส Docker Microservices', 'time' => '3 นาทีที่แล้ว'],
        ['icon' => 'bi-bug', 'color' => 'bg-danger-subtle text-danger', 'message' => 'ระบบ AI ช่วยเหลือแนะนำ Hint ในข้อคำถามสัญญากลุ่มให้กับคุณ Nutthawut', 'time' => '12 นาทีที่แล้ว'],
        ['icon' => 'bi-patch-check-fill', 'color' => 'bg-success-subtle text-success', 'message' => 'คุณ Pitchaya ทำการสอบผ่านและรับใบเซอร์คอร์ส Next.js สำเร็จ', 'time' => '45 นาทีที่แล้ว']
    ];
    echo json_encode(['status' => 'success', 'payload' => $timeline_feed]);
    exit;
}

// 3. เรียกดึงรายการหลักสูตรทั้งหมด
if ($action === 'fetch_courses') {
    $courses_database = [
        ['id' => 101, 'title' => 'Modern React & Next.js Core Professional', 'category' => 'Web Development', 'students' => 2850, 'earnings' => '฿85,500', 'rating' => '4.95', 'status' => 'Published'],
        ['id' => 102, 'title' => 'DevOps Automation Pipeline with Kubernetes', 'category' => 'Cloud & DevOps', 'students' => 2070, 'earnings' => '฿66,900', 'rating' => '4.98', 'status' => 'Published'],
        ['id' => 103, 'title' => 'Python Advanced concepts for Quant Trading AI', 'category' => 'AI & Deep Learning', 'students' => 0, 'earnings' => '฿0', 'rating' => '0.00', 'status' => 'Draft']
    ];
    echo json_encode(['status' => 'success', 'payload' => $courses_database]);
    exit;
}

// 4. บันทึกและดึงข้อมูลจากการส่งฟอร์มผ่านสเต็ปเปอร์ (Form Processor)
if ($action === 'execute_creation') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'การเชื่อมต่อไม่ได้รับสิทธิ์ทางกฎหมายโปรโตคอล']);
        exit;
    }

    $title = safeEscape($_POST['title'] ?? '');
    $category = safeEscape($_POST['category'] ?? '');
    $price_tier = safeEscape($_POST['price_tier'] ?? 'Free');
    $price = floatval($_POST['price'] ?? 0);
    $publish_status = safeEscape($_POST['publish_status'] ?? 'Draft');

    // ตรวจสอบความสมบูรณ์ฝั่ง Back-end (Validation Guard)
    if (empty($title) || empty($category)) {
        echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกข้อมูลฟิลด์ที่กำหนดความปลอดภัยหลักให้ครบถ้วน']);
        exit;
    }

    // ส่งโครงสร้างโมเดลกลับเพื่อไปแสดงผลบนตารางหลักทันทีโดยไม่ต้องโหลดรีเฟรชหน้าเว็บใหม่
    echo json_encode([
        'status' => 'success',
        'message' => 'บันทึกโครงสร้างระบบข้อมูลหลักสูตรคอร์สเรียนเรียบร้อยแล้ว!',
        'new_node' => [
            'id' => rand(200, 999),
            'title' => $title,
            'category' => $category,
            'students' => 0,
            'earnings' => '฿' . number_format($price),
            'rating' => '0.00',
            'status' => $publish_status
        ]
    ]);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'ไม่พบคำสั่งการทำงานที่ถูกต้อง']);