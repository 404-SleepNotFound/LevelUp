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

// 5. บันทึกโครงสร้างคอร์สเรียน (Course Builder Save Draft)
if ($action === 'save_course_structure') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
        exit;
    }

    $current_user_id = $_SESSION['user_login']['user_id'] ?? $_SESSION['user_id'] ?? null;
    if (!$current_user_id) {
        echo json_encode(['status' => 'error', 'message' => 'Authentication required. Please log in.']);
        exit;
    }

    try {
        // --- Database Connection ---
        $db_path = __DIR__ . '/../config/db.php';
        if (!file_exists($db_path)) {
            throw new Exception('Database configuration file not found.');
        }
        require_once $db_path;
        $pdo = $conn ?? $pdo ?? null;
        if (!$pdo) {
            throw new Exception('Database connection failed.');
        }

        // --- Get and Decode Payload ---
        $json_payload = file_get_contents('php://input');
        $data = json_decode($json_payload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON payload: ' . json_last_error_msg());
        }

        $course_id = filter_var($data['course_id'] ?? null, FILTER_VALIDATE_INT);
        $chapters = $data['chapters'] ?? [];

        if (!$course_id) {
            throw new Exception('Course ID is missing or invalid.');
        }

        // --- Authorization Check ---
        $stmt_check = $pdo->prepare("SELECT user_id FROM course WHERE id = ?");
        $stmt_check->execute([$course_id]);
        $course_owner = $stmt_check->fetchColumn();

        if (!$course_owner || $course_owner != $current_user_id) {
            throw new Exception('You do not have permission to edit this course.');
        }

        // --- Database Transaction ---
        $pdo->beginTransaction();

        $all_lesson_ids_from_client = [];
        $all_chapter_ids_from_client = [];

        foreach ($chapters as $chap_data) {
            $chapter_id = filter_var($chap_data['id'] ?? null, FILTER_VALIDATE_INT);
            $chapter_title = trim($chap_data['title']);
            $chapter_order = filter_var($chap_data['order'], FILTER_VALIDATE_INT);
            
            // Keep track of IDs sent from the client
            if ($chapter_id) {
                $all_chapter_ids_from_client[] = $chapter_id;
            }

            if ($chapter_id) {
                // Update Existing Chapter
                $stmt_chap = $pdo->prepare("UPDATE chapters SET title = ?, chapter_order = ? WHERE id = ? AND course_id = ?");
                $stmt_chap->execute([$chapter_title, $chapter_order, $chapter_id, $course_id]);
            } else {
                // Insert New Chapter
                $stmt_chap = $pdo->prepare("INSERT INTO chapters (course_id, title, chapter_order) VALUES (?, ?, ?)");
                $stmt_chap->execute([$course_id, $chapter_title, $chapter_order]);
                $chapter_id = $pdo->lastInsertId(); // Get new ID for lessons
            }

            foreach ($chap_data['lessons'] ?? [] as $less_data) {
                $lesson_id = filter_var($less_data['id'] ?? null, FILTER_VALIDATE_INT);
                $lesson_title = trim($less_data['title']);
                $lesson_type = trim($less_data['type']);
                $lesson_order = filter_var($less_data['order'], FILTER_VALIDATE_INT);

                if ($lesson_id) {
                    $all_lesson_ids_from_client[] = $lesson_id;
                }

                if ($lesson_id) {
                    // Update Existing Lesson
                    $stmt_less = $pdo->prepare("UPDATE lessons SET title = ?, lesson_type = ?, lesson_order = ?, chapter_id = ? WHERE id = ? AND course_id = ?");
                    $stmt_less->execute([$lesson_title, $lesson_type, $lesson_order, $chapter_id, $lesson_id, $course_id]);
                } else {
                    // Insert New Lesson
                    $stmt_less = $pdo->prepare("INSERT INTO lessons (course_id, chapter_id, title, lesson_type, lesson_order, status) VALUES (?, ?, ?, ?, ?, 'draft')");
                    $stmt_less->execute([$course_id, $chapter_id, $lesson_title, $lesson_type, $lesson_order]);
                }
            }
        }
        
        // --- Handle Deletions ---
        // Get all chapter and lesson IDs for this course from the DB
        $stmt_get_all_chaps = $pdo->prepare("SELECT id FROM chapters WHERE course_id = ?");
        $stmt_get_all_chaps->execute([$course_id]);
        $db_chapter_ids = $stmt_get_all_chaps->fetchAll(PDO::FETCH_COLUMN);

        $stmt_get_all_less = $pdo->prepare("SELECT id FROM lessons WHERE course_id = ?");
        $stmt_get_all_less->execute([$course_id]);
        $db_lesson_ids = $stmt_get_all_less->fetchAll(PDO::FETCH_COLUMN);

        // Find which IDs to delete
        $chapters_to_delete = array_diff($db_chapter_ids, $all_chapter_ids_from_client);
        $lessons_to_delete = array_diff($db_lesson_ids, $all_lesson_ids_from_client);

        if (!empty($chapters_to_delete)) {
            $in_chap_del = str_repeat('?,', count($chapters_to_delete) - 1) . '?';
            $stmt_del_chap = $pdo->prepare("DELETE FROM chapters WHERE id IN ($in_chap_del) AND course_id = ?");
            $stmt_del_chap->execute(array_merge($chapters_to_delete, [$course_id]));
        }

        if (!empty($lessons_to_delete)) {
            $in_less_del = str_repeat('?,', count($lessons_to_delete) - 1) . '?';
            $stmt_del_less = $pdo->prepare("DELETE FROM lessons WHERE id IN ($in_less_del) AND course_id = ?");
            $stmt_del_less->execute(array_merge($lessons_to_delete, [$course_id]));
        }

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Course structure saved successfully.']);

    } catch (Exception $e) {
        if ($pdo && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'ไม่พบคำสั่งการทำงานที่ถูกต้อง']);