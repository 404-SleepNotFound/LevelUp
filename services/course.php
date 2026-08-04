<?php
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');

// ดึงไฟล์เชื่อมต่อ Database (ถ้ามี)
if (file_exists(__DIR__ . '/../config/db.php')) {
    require_once __DIR__ . '/../config/db.php';
}

// จัดการความปลอดภัย ป้องกันช่องโหว่การโจมตีประเภท XSS
function safeEscape($str) {
    if (is_array($str)) {
        return array_map('safeEscape', $str);
    }
    return htmlspecialchars(trim((string)$str), ENT_QUOTES, 'UTF-8');
}

function normalizeToArray($data) {
    if (is_array($data)) return $data;
    if (is_string($data) && trim($data) !== '') return explode(',', $data);
    return [];
}

function nullableInt($val) {
    return (isset($val) && trim((string)$val) !== '') ? (int)$val : null;
}

function uploadMediaFile($fileKey, $uploadDir = '../uploads/') {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }
    $ext = strtolower(pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION));
    $newFileName = uniqid('media_', true) . '.' . $ext;
    $targetPath = $uploadDir . $newFileName;

    if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetPath)) {
        return str_replace('../', '', $uploadDir) . $newFileName;
    }
    return null;
}

// ✅ ตรวจสอบการรับ action ทั้งจาก $_POST, $_GET และ $_REQUEST
$action = safeEscape($_REQUEST['action'] ?? $_POST['action'] ?? $_GET['action'] ?? '');

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

// 5. บันทึกข้อมูลคอร์สเรียนฉบับเต็มลง Database (บันทึกร่าง / สร้างคอร์ส)
if ($action === 'save') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $user_id = $_SESSION['user_id'] ?? ($_POST['user_id'] ?? null);

    if (!$user_id) {
        echo json_encode(['status' => 'error', 'message' => 'กรุณาล็อกอินใหม่อีกครั้ง']);
        exit;
    }

    if (!isset($conn)) {
        echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถเชื่อมต่อฐานข้อมูลได้']);
        exit;
    }

    $data = $_POST;

    try {
        $conn->beginTransaction();

        $cover_path = uploadMediaFile('cover_file', '../uploads/covers/');
        $video_path = uploadMediaFile('video_file', '../uploads/videos/');

        $course_title        = safeEscape($data['course_title'] ?? '');
        $course_subtitle     = safeEscape($data['course_subtitle'] ?? '');
        $course_language     = safeEscape($data['course_language'] ?? '');
        $course_short_desc   = safeEscape($data['course_short_desc'] ?? '');
        $course_full_desc    = safeEscape($data['course_full_desc'] ?? '');
        
        $course_category     = nullableInt($data['course_category'] ?? null);
        $duration_hours      = nullableInt($data['duration_hours'] ?? null);
        $duration_chapters   = nullableInt($data['duration_chapters'] ?? null);
        $duration_week       = nullableInt($data['duration_week'] ?? null);

        $course_difficulty   = safeEscape($data['course_difficulty'] ?? '');
        $course_target       = safeEscape($data['course_target'] ?? '');
        $course_type         = safeEscape($data['course_type'] ?? '');
        $course_visibility   = safeEscape($data['course_visibility'] ?? 'private');
        $cert_condition_quiz = safeEscape($data['cert_condition_quiz'] ?? '0');
        $cert_condition_lab  = safeEscape($data['cert_condition_lab'] ?? '0');
        $seo_slug            = safeEscape($data['seo_slug'] ?? '');
        $seo_desc            = safeEscape($data['seo_desc'] ?? '');

        $certificate_type = 'None';
        if ($cert_condition_quiz == 1 && $cert_condition_lab == 1) $certificate_type = 'quiz/lab';
        else if ($cert_condition_quiz == 0 && $cert_condition_lab == 1) $certificate_type = 'lab';
        else if ($cert_condition_quiz == 1 && $cert_condition_lab == 0) $certificate_type = 'quiz';

        $sql = "INSERT INTO `course`(`user_id`, `title`, `subtitle`, `language`, `short_description`, `full_description`, `category_id`, 
                `difficulty`, `time_total`, `chapter_total`, `time_week`, `target`, `type`, `visibility`, `certificate_type`, `seo_slug`, `seo_description`, `img`, `video`) 
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $user_id,
            $course_title,
            $course_subtitle,
            $course_language,
            $course_short_desc,
            $course_full_desc,
            $course_category,
            $course_difficulty,
            $duration_hours,
            $duration_chapters,
            $duration_week,
            $course_target,
            $course_type,
            $course_visibility,
            $certificate_type,
            $seo_slug,
            $seo_desc,
            $cover_path,
            $video_path
        ]);

        $course_id = $conn->lastInsertId();

        $insertRelatedData = function ($tableName, $items) use ($conn, $course_id) {
            $cleanItems = normalizeToArray($items);
            if (!empty($cleanItems)) {
                $stmt = $conn->prepare("INSERT INTO `{$tableName}` (`course_id`, `name`) VALUES (?, ?)");
                foreach ($cleanItems as $item) {
                    $cleanValue = safeEscape($item);
                    if ($cleanValue !== '') {
                        $stmt->execute([$course_id, $cleanValue]);
                    }
                }
            }
        };

        $insertRelatedData('tags', $data['course_tag'] ?? []);
        $insertRelatedData('outcomes', $data['outcomes'] ?? []);
        $insertRelatedData('prereq', $data['prereq'] ?? []);
        $insertRelatedData('career', $data['course_career'] ?? []);

        $conn->commit();

        echo json_encode([
            'status'  => 'success',
            'message' => 'บันทึกข้อมูลและอัปโหลดไฟล์เรียบร้อยแล้ว: ' . $course_title
        ]);
        exit;

    } catch (PDOException $e) {
        if (isset($conn) && $conn->inTransaction()) {
            $conn->rollBack();
        }
        echo json_encode([
            'status'  => 'error',
            'message' => 'เกิดข้อผิดพลาดจากฐานข้อมูล: ' . $e->getMessage()
        ]);
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'ไม่พบคำสั่งการทำงานที่ถูกต้อง']);