<?php
$ROOT_PATH = '../';
session_start();

require_once '../config/db.php';

// รับค่า id หรือ course_id จาก URL
$course_id = $_GET['id'] ?? $_GET['course_id'] ?? 0;

// 1. ดึงข้อมูล Course หลัก + Category + Instructor (รวม Query เดียวเพื่อลด Resource)[cite: 5]
$sql = "SELECT c.*, 
               ct.name AS category_name,
               u.id AS instructor_id,
               u.name AS instructor_name,
               u.img AS instructor_img,
               u.user_role AS instructor_role,
               u.bio AS instructor_bio
        FROM `course` c 
        LEFT JOIN `category` ct ON c.category_id = ct.id 
        LEFT JOIN `users` u ON c.user_id = u.id
        WHERE c.id = :course_id";

$stmt = $conn->prepare($sql);
$stmt->execute([':course_id' => $course_id]);
$course_data = $stmt->fetch(PDO::FETCH_ASSOC);

// ดักจับกรณีหา Course ไม่เจอ[cite: 5]
if (!$course_data) {
    die("ไม่พบข้อมูลคอร์สเรียนนี้");
}

// 2. ดึงข้อมูล Outcomes (สิ่งที่จะได้เรียนรู้)[cite: 5]
$sql_outcomes = "SELECT name FROM `outcomes` WHERE course_id = :course_id";
$stmt_outcomes = $conn->prepare($sql_outcomes);
$stmt_outcomes->execute([':course_id' => $course_id]);
$course_outcomes = $stmt_outcomes->fetchAll(PDO::FETCH_COLUMN);

// 3. ดึงข้อมูล Prereq (ความรู้พื้นฐานที่ต้องมีก่อน)[cite: 5]
$sql_prereq = "SELECT name FROM `prereq` WHERE course_id = :course_id";
$stmt_prereq = $conn->prepare($sql_prereq);
$stmt_prereq->execute([':course_id' => $course_id]);
$course_prereqs = $stmt_prereq->fetchAll(PDO::FETCH_COLUMN);

// ----------------------------------------------------------------------
// 4. [แก้ไขแล้ว] ดึงข้อมูล Chapters และ Lessons ทั้งหมด (รวมกรณี chapter_id เป็น NULL)[cite: 5]
// ----------------------------------------------------------------------
$sql_chapters = "SELECT * FROM `chapters` WHERE course_id = :course_id ORDER BY chapter_order ASC";
$stmt_chapters = $conn->prepare($sql_chapters);
$stmt_chapters->execute([':course_id' => $course_id]);
$chapters_data = $stmt_chapters->fetchAll(PDO::FETCH_ASSOC);

// ดึงบทเรียนทั้งหมดของคอร์สนี้
$sql_lessons = "SELECT * FROM `lessons` WHERE course_id = :course_id ORDER BY lesson_order ASC";
$stmt_lessons = $conn->prepare($sql_lessons);
$stmt_lessons->execute([':course_id' => $course_id]);
$all_lessons = $stmt_lessons->fetchAll(PDO::FETCH_ASSOC);

$curriculum = [];
$assigned_lesson_ids = [];

// 4.1 จัดกลุ่มบทเรียนใส่ตาม Chapter
if (!empty($chapters_data)) {
    foreach ($chapters_data as $chap) {
        $chap_lessons = array_filter($all_lessons, function($les) use ($chap, &$assigned_lesson_ids) {
            if (!empty($les['chapter_id']) && $les['chapter_id'] == $chap['id']) {
                $assigned_lesson_ids[] = $les['id'];
                return true;
            }
            return false;
        });

        $curriculum[] = [
            'id' => $chap['id'],
            'title' => $chap['title'],
            'lessons' => array_values($chap_lessons)
        ];
    }
}

// 4.2 กรองบทเรียนที่ chapter_id เป็น NULL หรือไม่ได้ผูกกับ Chapter ใดๆ
$unassigned_lessons = array_filter($all_lessons, function($les) use ($assigned_lesson_ids) {
    return !in_array($les['id'], $assigned_lesson_ids);
});

if (!empty($unassigned_lessons)) {
    $curriculum[] = [
        'id' => 0,
        'title' => !empty($chapters_data) ? 'บทเรียนเพิ่มเติม (ไม่มีหมวดหมู่)' : 'เนื้อหาหลักสูตรทั้งหมด',
        'lessons' => array_values($unassigned_lessons)
    ];
}

// 5. นับจำนวนผู้เรียนที่ลงทะเบียน (Enrollments Count)[cite: 5]
$sql_count_students = "SELECT COUNT(id) FROM `enrollments` WHERE course_id = :course_id";
$stmt_students = $conn->prepare($sql_count_students);
$stmt_students->execute([':course_id' => $course_id]);
$students_count = $stmt_students->fetchColumn() ?: 0;

// 6. เช็คว่าผู้ใช้ปัจจุบันเคย "บันทึกไว้ทีหลัง" คอร์สนี้แล้วหรือยัง[cite: 5]
$is_saved = false;
if (!empty($_SESSION['user_id'])) {
    $sql_saved = "SELECT id FROM `saved_courses` WHERE user_id = :user_id AND course_id = :course_id";
    $stmt_saved = $conn->prepare($sql_saved);
    $stmt_saved->execute([
        ':user_id'   => $_SESSION['user_id'],
        ':course_id' => $course_id
    ]);
    $is_saved = (bool) $stmt_saved->fetch(PDO::FETCH_ASSOC);
}

// 7. จัดรูปแบบราคา[cite: 5]
$price_val = (isset($course_data['price']) && is_numeric($course_data['price'])) ? (float)$course_data['price'] : 0;
$type_text = ($price_val > 0) ? number_format($price_val) . ' ฿' : 'เรียนฟรี';

// 8. จัดโครงสร้างข้อมูลคอร์ส[cite: 5]
$course = [
    'id' => $course_id,
    'title' => $course_data['title'] ?? 'Untitled Course',
    'subtitle' => $course_data['subtitle'] ?? '',
    'brief' => $course_data['short_description'] ?? '',
    'description' => $course_data['full_description'] ?? '',
    'category' => $course_data['category_name'] ?? 'ทั่วไป',
    'level' => $course_data['difficulty'] ?? 'Beginner',
    'language' => strtoupper($course_data['language'] ?? 'TH'),
    'image' => !empty($course_data['img']) ? $ROOT_PATH . $course_data['img'] : 'https://via.placeholder.com/600x400',
    'video_url' => $course_data['video'] ?? null,
    'time_total' => $course_data['time_total'] ?? 0,
    'chapter_total' => $course_data['chapter_total'] ?? count($all_lessons),
    'time_week' => $course_data['time_week'] ?? 0,
    'target' => $course_data['target'] ?? '',
    'certificate_type' => $course_data['certificate_type'] ?? 'มีใบรับรองเมื่อเรียนจบ',
    'updated_at' => $course_data['updated_at'] ?? null,
    'price' => $price_val,
    'type_text' => $type_text,
    'rating' => 5.0,
    'reviews_count' => 0,
    'students_count' => $students_count,
    'outcomes' => $course_outcomes,
    'prereqs' => $course_prereqs,
    
    'includes' => [
        ['icon' => 'bi-play-circle', 'text' => 'วิดีโอรวม ' . ($course_data['time_total'] ?? 0) . ' ชั่วโมง'],
        ['icon' => 'bi-journal-code', 'text' => 'เนื้อหาทั้งหมด ' . count($all_lessons) . ' บทเรียน'],
        ['icon' => 'bi-clock-history', 'text' => 'แนะนำเรียน ' . ($course_data['time_week'] ?? 0) . ' ชม./สัปดาห์'],
        ['icon' => 'bi-robot', 'text' => 'AI Tutor ช่วยตอบคำถาม 24/7'],
        ['icon' => 'bi-award', 'text' => $course_data['certificate_type'] ?? 'มีใบรับรองเมื่อเรียนจบ']
    ],
    'instructor' => [
        'id' => $course_data['instructor_id'] ?? $course_data['user_id'] ?? 0,
        'name' => $course_data['instructor_name'] ?? 'ผู้ดูแลระบบ',
        'title' => $course_data['instructor_role'] ?? 'Instructor',
        'company' => "LevelUp Academy",
        'image' => !empty($course_data['instructor_img']) ? $course_data['instructor_img'] : 'https://via.placeholder.com/150',
        'bio' => $course_data['instructor_bio'] ?? 'ผู้สอนและนักพัฒนาซอฟต์แวร์ผู้เชี่ยวชาญประจำหลักสูตร'
    ]
];

// คำนวณ AI Readiness Analysis[cite: 5]
$est_time_str = ($course['time_week'] > 0 && $course['time_total'] > 0) 
    ? ceil($course['time_total'] / $course['time_week']) . ' สัปดาห์' 
    : 'เรียนตามความสะดวก';

$ai_analysis = [
    'score' => 95,
    'match_text' => 'คอร์สนี้ตรงกับทักษะสายงานของคุณ',
    'reasons' => [
        'เน้นการลงมือปฏิบัติจริงกับโจทย์จำลอง',
        'สอดคล้องกับเส้นทางการเรียนรู้ของคุณ'
    ],
    'est_time' => $est_time_str
];

// 9. ดึงข้อมูลคอร์สที่เกี่ยวข้อง (Related Courses)[cite: 5]
$related_courses = [];
try {
    $category_id = $course_data['category_id'] ?? 0;
    $sql_related = "SELECT c.*, u.name AS instructor_name 
                    FROM `course` c 
                    LEFT JOIN `users` u ON c.user_id = u.id 
                    WHERE c.id != :course_id 
                    ORDER BY (c.category_id = :category_id) DESC, c.id DESC 
                    LIMIT 3";

    $stmt_related = $conn->prepare($sql_related);
    $stmt_related->execute([
        ':course_id'   => $course_id,
        ':category_id' => $category_id
    ]);
    $raw_related = $stmt_related->fetchAll(PDO::FETCH_ASSOC);

    $fallbackImg = 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=600&auto=format&fit=crop';

    foreach ($raw_related as $rel) {
        $rawImg = $rel['img'] ?? '';
        $imgPath = $fallbackImg;

        if (!empty($rawImg)) {
            if (filter_var($rawImg, FILTER_VALIDATE_URL)) {
                $imgPath = $rawImg;
            } elseif (file_exists($ROOT_PATH . $rawImg)) {
                $imgPath = $ROOT_PATH . $rawImg;
            }
        }

        $rel_price = (isset($rel['price']) && is_numeric($rel['price']) && (float)$rel['price'] > 0)
            ? number_format((float)$rel['price']) . ' ฿'
            : 'เรียนฟรี';

        $related_courses[] = [
            'id'         => $rel['id'],
            'title'      => $rel['title'] ?? 'คอร์สเรียน',
            'instructor' => $rel['instructor_name'] ?? 'Instructor',
            'price'      => $rel_price,
            'rating'     => 5.0,
            'image'      => $imgPath
        ];
    }
} catch (PDOException $e) {
    error_log("Related courses query error: " . $e->getMessage());
    $related_courses = [];
}

// ฟังก์ชันแปลง Video URL เป็น Embed Player[cite: 5]
function getEmbedUrl($url)
{
    if (empty($url)) return '';

    // YouTube[cite: 5]
    $youtube_pattern = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';
    if (preg_match($youtube_pattern, $url, $match)) {
        return "https://www.youtube.com/embed/" . $match[1];
    }

    // Google Drive[cite: 5]
    if (preg_match('%drive\.google\.com/file/d/([^/]+)%i', $url, $match)) {
        return "https://drive.google.com/file/d/" . $match[1] . "/preview";
    }

    // Vimeo[cite: 5]
    if (preg_match('%vimeo\.com/([0-9]+)%i', $url, $match)) {
        return "https://player.vimeo.com/video/" . $match[1];
    }

    return $url;
}

include_once $ROOT_PATH . 'includes/header.php';
?>

<!-- โหลด CSS และ JS สำหรับ Custom Dropdown Select -->
<link rel="stylesheet" href="css/dropdown-select.css">
<script src="js/lu-dropdown.js" defer></script>

    <!-- 1. Hero Section -->
    <section class="course-hero py-4 py-lg-5 position-relative overflow-hidden">
        <div class="container-xl position-relative z-1">
            <nav aria-label="breadcrumb" class="mb-3 mb-lg-4 d-none d-md-block">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="courses.php" class="text-muted text-decoration-none hover-text-primary">Courses</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-muted text-decoration-none hover-text-primary"><?= htmlspecialchars($course['category']) ?></a></li>
                    <li class="breadcrumb-item active text-main" aria-current="page"><?= htmlspecialchars($course['title']) ?></li>
                </ol>
            </nav>

            <div class="row align-items-center">
                <div class="col-lg-7 pb-3 pb-lg-0">
                    <h1 class="fw-bold mb-2 text-main lh-sm" style="font-size: clamp(1.8rem, 4vw, 2.4rem);">
                        <?= htmlspecialchars($course['title']) ?>
                    </h1>
                    <?php if (!empty($course['subtitle'])): ?>
                        <h6 class="text-info fw-normal mb-3"><?= htmlspecialchars($course['subtitle']) ?></h6>
                    <?php endif; ?>
                    
                    <p class="fs-6 text-muted mb-4 fw-light" style="max-width: 620px;">
                        <?= htmlspecialchars($course['brief']) ?>
                    </p>

                    <div class="d-flex flex-wrap align-items-center gap-2 gap-md-3 mb-4">
                        <?php if (!empty($course['video_url'])): ?>
                            <button class="btn btn-outline-info rounded-pill px-3 py-2 btn-sm d-flex align-items-center gap-2"
                                type="button"
                                onclick="if(typeof bootstrap !== 'undefined') { var m = new bootstrap.Modal(document.getElementById('videoPreviewModal')); m.show(); } else { alert('Error: Bootstrap JS not loaded'); }">
                                <i class="bi bi-play-circle-fill fs-5 text-info"></i> <span>ดูวิดีโอตัวอย่าง</span>
                            </button>
                        <?php else: ?>
                            <button class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-sm d-flex align-items-center gap-2"
                                type="button"
                                onclick="if(typeof Swal !== 'undefined') { Swal.fire({title: 'ไม่มีวิดีโอตัวอย่าง', text: 'ขออภัย คอร์สนี้ยังไม่มีการอัปโหลดวิดีโอแนะนำ', icon: 'info', confirmButtonColor: '#6366F1', customClass: { popup: 'levelup-swal-popup', title: 'levelup-swal-title', htmlContainer: 'levelup-swal-text' } }); } else { alert('ไม่มีวิดีโอตัวอย่าง: ขออภัย คอร์สนี้ยังไม่มีการอัปโหลดวิดีโอแนะนำ'); }">
                                <i class="bi bi-play-circle-fill fs-5 text-secondary"></i> <span>ไม่มีวิดีโอตัวอย่าง</span>
                            </button>
                        <?php endif; ?>
                        <span class="text-muted d-none d-sm-inline">|</span>
                        <div class="d-flex align-items-center gap-1 bg-surface px-3 py-1.5 rounded-pill border border-color">
                            <i class="bi bi-people-fill text-info me-1 small"></i>
                            <span class="fw-bold text-main small"><?= number_format($course['students_count']) ?></span>
                            <span class="text-muted small">ผู้ลงเรียน</span>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3 gap-md-4 mb-4 text-muted" style="font-size: 0.85rem;">
                        <div class="d-flex align-items-center gap-1"><i class="bi bi-clock me-1"></i> รวม <?= htmlspecialchars($course['time_total']) ?> ชั่วโมง</div>
                        <div class="d-flex align-items-center gap-1"><i class="bi bi-journal-check me-1"></i> <?= count($all_lessons) ?> บทเรียน</div>
                        <div class="d-flex align-items-center gap-1"><i class="bi bi-bar-chart me-1"></i> <?= htmlspecialchars($course['level']) ?></div>
                        <div class="d-flex align-items-center gap-1"><i class="bi bi-translate me-1"></i> <?= htmlspecialchars($course['language']) ?></div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <a href="instructor-profile.php?id=<?= urlencode($course['instructor']['id']) ?>">
                            <img src="<?= htmlspecialchars($course['instructor']['image']) ?>" class="rounded-circle border border-2 border-info shadow-sm" width="48" height="48" alt="Instructor" style="object-fit: cover;">
                        </a>
                        <div>
                            <p class="mb-0 text-muted" style="font-size: 0.75rem;">ผู้ฝึกสอน</p>
                            <a href="instructor-profile.php?id=<?= urlencode($course['instructor']['id']) ?>" class="text-main text-decoration-none fw-semibold hover-text-primary" style="font-size: 0.95rem;"><?= htmlspecialchars($course['instructor']['name']) ?></a>
                        </div>
                    </div>
                </div>

                <!-- Right Widget -->
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="ai-readiness-widget rounded-4 p-4 w-100 ms-auto" style="max-width: 410px;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="badge bg-gradient-primary rounded-pill mb-2"><i class="bi bi-cpu me-1"></i> AI SYNC ANALYSIS</span>
                                <h5 class="fw-bold mb-1 text-main">ความพร้อมสำหรับเควส</h5>
                            </div>
                            <div class="readiness-circle" style="--score: <?= $ai_analysis['score'] ?>%;">
                                <span><?= $ai_analysis['score'] ?>%</span>
                            </div>
                        </div>
                        <p class="fw-medium mb-3 text-muted small"><i class="bi bi-graph-up-arrow text-info me-2"></i><?= htmlspecialchars($ai_analysis['match_text']) ?></p>

                        <ul class="list-unstyled mb-4 small">
                            <?php foreach ($ai_analysis['reasons'] as $reason): ?>
                                <li class="mb-2 text-muted"><i class="bi bi-shield-check text-success me-2"></i><?= htmlspecialchars($reason) ?></li>
                            <?php endforeach; ?>
                            <li class="mb-0 text-muted"><i class="bi bi-clock-history text-info me-2"></i>คาดว่าจะเรียนจบใน <strong><?= $ai_analysis['est_time'] ?></strong></li>
                        </ul>

                        <div class="d-grid gap-2">
                            <a href="lesson.php?course_id=<?= $course['id'] ?>" class="btn btn-premium btn-lg rounded-3 fw-bold py-2 text-center text-decoration-none">
                                เข้าสู่บทเรียน (<?= htmlspecialchars($course['type_text']) ?>)
                            </a>
                            <button class="btn btn-outline-surface btn-lg rounded-3 d-flex justify-content-center align-items-center gap-2 py-2" id="btnToggleBookmark" type="button" data-id="<?= $course['id'] ?>" data-saved="<?= $is_saved ? '1' : '0' ?>" style="font-size: 0.9rem;">
                                <i class="bi <?= $is_saved ? 'bi-bookmark-star-fill' : 'bi-bookmark-star' ?>" id="bookmarkIcon"></i> <span id="bookmarkText"><?= $is_saved ? 'บันทึกไว้แล้ว' : 'บันทึกไว้ทีหลัง' ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Main Content & Curriculum Section -->
    <section class="py-4 py-lg-5">
        <div class="container-xl">
            <div class="row g-4 g-lg-5">

                <div class="col-lg-8">

                    <!-- Course Overview -->
                    <div class="dashboard-card rounded-4 p-4 mb-4">
                        <h5 class="fw-bold mb-3 text-main d-flex align-items-center fs-5"><i class="bi bi-file-text text-info me-2"></i> รายละเอียดคอร์สเรียน (Description)</h5>
                        <div class="text-muted fw-light lh-lg mb-0" style="font-size: 0.92rem;">
                            <?= nl2br(htmlspecialchars($course['description'])) ?>
                        </div>
                    </div>

                    <?php if (!empty($course['target'])): ?>
                        <!-- Target Audience -->
                        <div class="dashboard-card rounded-4 p-4 mb-4">
                            <h5 class="fw-bold mb-2 text-main d-flex align-items-center fs-6"><i class="bi bi-bullseye text-danger me-2"></i> คอร์สนี้เหมาะสำหรับใคร</h5>
                            <div class="text-muted mb-0 small">
                                <?= htmlspecialchars($course['target']) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- What You'll Learn & Prerequisites -->
                    <div class="dashboard-card rounded-4 p-4 mb-4">
                        <div class="row g-4">
                            <!-- สิ่งที่คุณจะได้เรียนรู้ -->
                            <div class="col-md-6 border-end-md border-color">
                                <h5 class="fw-bold mb-3 text-main d-flex align-items-center fs-6"><i class="bi bi-check-circle-fill text-success me-2"></i> สิ่งที่คุณจะได้เรียนรู้</h5>
                                <?php if (!empty($course['outcomes'])): ?>
                                    <ul class="list-unstyled mb-0 small">
                                        <?php foreach ($course['outcomes'] as $outcome): ?>
                                            <li class="d-flex align-items-start mb-2.5 text-muted">
                                                <i class="bi bi-check2 text-info me-2 mt-0.5"></i>
                                                <span><?= htmlspecialchars($outcome) ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted small mb-0">ไม่มีข้อมูลผลลัพธ์การเรียนรู้ที่ระบุไว้</p>
                                <?php endif; ?>
                            </div>
                            <!-- ความรู้พื้นฐานที่ต้องมี -->
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3 text-main d-flex align-items-center fs-6"><i class="bi bi-list-check text-warning me-2"></i> ความรู้พื้นฐานที่ต้องมี</h5>
                                <div class="d-flex flex-column gap-2">
                                    <?php if (!empty($course['prereqs'])): ?>
                                        <?php foreach ($course['prereqs'] as $pre): ?>
                                            <div class="d-flex align-items-center justify-content-between p-2 rounded prerequisite-item small">
                                                <span class="text-muted"><?= htmlspecialchars($pre == '0' ? 'ไม่จำเป็นต้องมีพื้นฐานมาก่อน (เรียนรู้ได้ทันที)' : $pre) ?></span>
                                                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25"><i class="bi bi-check-lg me-1"></i> ผ่าน</span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted small mb-0">ไม่จำเป็นต้องมีความรู้พื้นฐานมาก่อน</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Curriculum Roadmap Accordion (Dynamic Chapters & Lessons) -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-end mb-3">
                            <div>
                                <h5 class="fw-bold mb-1 text-main d-flex align-items-center fs-5"><i class="bi bi-map text-primary me-2"></i> แผนที่เส้นทางการเรียนรู้</h5>
                                <p class="text-muted mb-0 small"><?= count($curriculum) ?> ส่วน • <?= count($all_lessons) ?> บทเรียน • รวมเวลาเรียน <?= htmlspecialchars($course['time_total']) ?> ชั่วโมง</p>
                            </div>
                            <?php if (!empty($curriculum)): ?>
                                <button class="btn btn-link text-info text-decoration-none p-0 small" id="btnExpandAll" type="button" style="font-size: 0.85rem;">กางออกทั้งหมด</button>
                            <?php endif; ?>
                        </div>

                        <div class="accordion custom-accordion" id="curriculumAccordion">
                            <?php if (!empty($curriculum)): ?>
                                <?php foreach ($curriculum as $index => $chap): ?>
                                    <div class="accordion-item mb-2 overflow-hidden">
                                        <h2 class="accordion-header" id="heading<?= $index ?>">
                                            <button class="accordion-button <?= $index != 0 ? 'collapsed' : '' ?> bg-transparent fw-bold text-main shadow-none"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse<?= $index ?>"
                                                aria-expanded="<?= $index == 0 ? 'true' : 'false' ?>"
                                                aria-controls="collapse<?= $index ?>">
                                                <div class="flex-grow-1 d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center me-3 gap-1">
                                                    <span><?= htmlspecialchars(($chap['id'] > 0 ? "Chapter " . ($index + 1) . ": " : "") . $chap['title']) ?></span>
                                                    <span class="fw-normal text-muted small"><i class="bi bi-clock me-1"></i><?= count($chap['lessons']) ?> บทเรียน</span>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse<?= $index ?>" class="accordion-collapse collapse <?= $index == 0 ? 'show' : '' ?>">
                                            <div class="accordion-body p-0 border-top border-color bg-surface bg-opacity-25">
                                                <div class="list-group list-group-flush">
                                                    <?php if (!empty($chap['lessons'])): ?>
                                                        <?php foreach ($chap['lessons'] as $les): ?>
                                                            <a href="lesson.php?course_id=<?= $course['id'] ?>&lesson_id=<?= $les['id'] ?>"
                                                                class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-3 px-3 px-md-4 lesson-row-link">
                                                                <div class="d-flex align-items-center gap-3">
                                                                    <?php 
                                                                        $type = strtolower($les['lesson_type'] ?? 'video');
                                                                        
                                                                        switch ($type) {
                                                                            case 'lab':
                                                                            case 'coding':
                                                                                $icon = 'bi-terminal-fill';
                                                                                $color = 'text-warning';
                                                                                break;
                                                                            case 'doc':
                                                                            case 'pdf':
                                                                            case 'article':
                                                                            case 'document':
                                                                                $icon = 'bi-file-earmark-text-fill';
                                                                                $color = 'text-success';
                                                                                break;
                                                                            case 'quiz':
                                                                            case 'exam':
                                                                                $icon = 'bi-question-circle-fill';
                                                                                $color = 'text-danger';
                                                                                break;
                                                                            case 'video':
                                                                            default:
                                                                                $icon = 'bi-play-circle-fill';
                                                                                $color = 'text-info';
                                                                                break;
                                                                        }
                                                                    ?>
                                                                    <i class="bi <?= $icon ?> <?= $color ?> fs-5 flex-shrink-0"></i>
                                                                    <div>
                                                                        <span class="text-main fw-medium small d-block lh-sm mb-1 lesson-title">
                                                                            <?= htmlspecialchars($les['title']) ?>
                                                                        </span>
                                                                        <span class="d-block text-muted" style="font-size: 0.72rem;">
                                                                            <?= htmlspecialchars($les['subtitle'] ?? 'บทเรียนย่อย') ?> • <?= htmlspecialchars($les['duration_minutes'] ?? 15) ?> นาที
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <i class="bi bi-chevron-right text-muted small"></i>
                                                            </a>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <div class="p-3 text-muted small text-center">ยังไม่มีบทเรียนในส่วนนี้</div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="dashboard-card rounded-4 p-4 text-center text-muted small">
                                    <i class="bi bi-journal-x fs-3 d-block mb-2 text-secondary"></i>
                                    ยังไม่มีโครงสร้างบทเรียนสำหรับคอร์สนี้
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Instructor Profile -->
                    <div id="instructor" class="dashboard-card rounded-4 p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                            <h5 class="fw-bold text-main mb-0 fs-5"><i class="bi bi-person-badge text-info me-2"></i> ข้อมูลผู้สอน (Mentor)</h5>
                            <a href="instructor-profile.php?id=<?= urlencode($course['instructor']['id']) ?>" class="btn btn-sm btn-outline-info rounded-pill px-3">
                                <i class="bi bi-person-bounding-box me-1"></i> ดูโปรไฟล์
                            </a>
                        </div>
                        <div class="row align-items-center g-4">
                            <div class="col-sm-auto text-center">
                                <a href="instructor-profile.php?id=<?= urlencode($course['instructor']['id']) ?>">
                                    <img src="<?= htmlspecialchars($course['instructor']['image']) ?>" class="rounded-circle object-fit-cover border border-2 border-secondary shadow-sm" width="90" height="90" alt="Instructor">
                                </a>
                            </div>
                            <div class="col-sm text-center text-sm-start">
                                <h5 class="fw-bold mb-1">
                                    <a href="instructor-profile.php?id=<?= urlencode($course['instructor']['id']) ?>" class="text-main text-decoration-none hover-text-primary">
                                        <?= htmlspecialchars($course['instructor']['name']) ?>
                                    </a>
                                </h5>
                                <p class="text-info fw-medium mb-3 small"><?= htmlspecialchars($course['instructor']['title']) ?> @ <?= htmlspecialchars($course['instructor']['company']) ?></p>
                                <p class="text-muted mb-3 fw-light small" style="line-height: 1.5;"><?= htmlspecialchars($course['instructor']['bio']) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Related Courses Section -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-main mb-3 fs-5"><i class="bi bi-journal-bookmark text-info me-2"></i> คอร์สเรียนที่เกี่ยวข้อง</h5>
                        <div class="row g-3">
                            <?php if (!empty($related_courses)): ?>
                                <?php foreach ($related_courses as $rel): ?>
                                    <div class="col-md-4">
                                        <a href="course-detail.php?id=<?= $rel['id'] ?>" class="text-decoration-none">
                                            <div class="dashboard-card bg-surface rounded-4 overflow-hidden course-card-hover h-100 d-flex flex-column">
                                                <img src="<?= htmlspecialchars($rel['image']) ?>" class="w-100 object-fit-cover" height="120" alt="Course Thumbnail">
                                                <div class="p-3 d-flex flex-column flex-grow-1">
                                                    <h6 class="fw-bold text-main mb-2 line-clamp-2 small" style="line-height: 1.4;"><?= htmlspecialchars($rel['title']) ?></h6>
                                                    <span class="text-muted mb-3 d-block" style="font-size: 0.75rem;"><?= htmlspecialchars($rel['instructor']) ?></span>
                                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                                        <span class="fw-bold text-info small"><?= htmlspecialchars($rel['price']) ?></span>
                                                        <span class="text-warning small"><i class="bi bi-star-fill me-1"></i><?= $rel['rating'] ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12 text-center py-4 dashboard-card rounded-4">
                                    <p class="text-muted mb-0 small">ยังไม่มีคอร์สเรียนที่เกี่ยวข้องในขณะนี้</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

                <!-- Right Sidebar -->
                <div class="col-lg-4">
                    <div class="sticky-sidebar" style="position: sticky; top: 24px; z-index: 10;">

                        <!-- Benefits Card -->
                        <div class="dashboard-card bg-surface rounded-4 p-4 mb-4">
                            <h6 class="fw-bold mb-3 text-main"><i class="bi bi-box-seam text-primary me-2"></i> สิทธิ์ประโยชน์ที่จะได้รับ</h6>
                            <ul class="list-unstyled mb-0 small">
                                <?php foreach ($course['includes'] as $inc): ?>
                                    <li class="d-flex align-items-center mb-3 text-muted">
                                        <i class="bi <?= $inc['icon'] ?> fs-5 me-3 text-info"></i>
                                        <span><?= htmlspecialchars($inc['text']) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <!-- AI Chatbot Assistant -->
                        <div class="dashboard-card bg-surface rounded-4 p-0 mb-4 overflow-hidden d-flex flex-column ai-chat-card">
                            <div class="p-3 border-bottom border-color d-flex align-items-center gap-2" style="background: linear-gradient(90deg, rgba(6, 182, 212, 0.12) 0%, transparent 100%);">
                                <div class="text-info fs-3"><i class="bi bi-robot"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-main" style="font-size: 0.95rem; line-height: 1.4;">Holo AI Assistant</h6>
                                    <span class="subtext-bright d-block">วิเคราะห์หลักสูตรเรียลไทม์ 24/7</span>
                                </div>
                            </div>

                            <div class="p-3 flex-grow-1 overflow-auto bg-surface ai-chat-body" id="aiChatBody" style="height: 260px;">
                                <div class="ai-msg d-flex gap-2 mb-3">
                                    <div class="chat-bubble-ai rounded-3 p-3 shadow-sm w-100" style="font-size: 0.85rem;">
                                        <p class="mb-2 fw-medium text-main">สวัสดีครับ! 😊 ผมเป็น AI ประจำวิชานี้ สอบถามข้อมูลโมดูลการเรียนได้เลยครับ</p>
                                        <div class="d-flex flex-column gap-2 mt-3">
                                            <a href="#" class="qa-quick-btn">คอร์สนี้เหมาะกับมือใหม่ไหม?</a>
                                            <a href="#" class="qa-quick-btn">ได้ทำโปรเจกต์ส่งท้ายชิ้นไหนบ้าง?</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-2.5 border-top border-color bg-surface">
                                <form id="courseQaForm" class="position-relative m-0">
                                    <input type="text" id="qaInput" class="form-control form-control-sm rounded-3 py-2 pe-5 shadow-none" placeholder="พิมพ์คำถาม..." autocomplete="off">
                                    <button type="submit" class="btn btn-link text-info position-absolute end-0 top-50 translate-middle-y p-0 pe-3 shadow-none"><i class="bi bi-send-fill fs-5"></i></button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Script สำหรับควบคุมการ กางออกทั้งหมด / หุบทั้งหมด ของ Accordion -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const btnExpandAll = document.getElementById('btnExpandAll');
        const accordionItems = document.querySelectorAll('#curriculumAccordion .accordion-collapse');
        const accordionButtons = document.querySelectorAll('#curriculumAccordion .accordion-button');

        if (btnExpandAll) {
            let isExpanded = false;

            btnExpandAll.addEventListener('click', function () {
                isExpanded = !isExpanded;

                accordionItems.forEach((item, index) => {
                    const bsCollapse = bootstrap.Collapse.getOrCreateInstance(item, { toggle: false });
                    const btn = accordionButtons[index];

                    if (isExpanded) {
                        bsCollapse.show();
                        if (btn) btn.classList.remove('collapsed');
                    } else {
                        bsCollapse.hide();
                        if (btn) btn.classList.add('collapsed');
                    }
                });

                btnExpandAll.textContent = isExpanded ? 'หุบทั้งหมด' : 'กางออกทั้งหมด';
            });
        }
    });
    </script>

    <!-- Video Preview Modal -->
    <div class="modal fade" id="videoPreviewModal" tabindex="-1" aria-labelledby="videoPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-surface border-0 rounded-4 overflow-hidden">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-main fw-bold" id="videoPreviewModalLabel">
                        <i class="bi bi-play-circle-fill text-info me-2"></i> วิดีโอตัวอย่าง: <?= htmlspecialchars($course['title']) ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="ratio ratio-16x9 rounded-3 overflow-hidden bg-black">
                        <iframe id="videoPreviewFrame" src="" title="Video Preview" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script: วิดีโอตัวอย่าง -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var videoModalEl = document.getElementById('videoPreviewModal');
        var videoFrame = document.getElementById('videoPreviewFrame');
        var embedUrl = <?= json_encode(getEmbedUrl($course['video_url'])) ?>;

        if (videoModalEl && videoFrame && embedUrl) {
            videoModalEl.addEventListener('show.bs.modal', function () {
                var sep = embedUrl.indexOf('?') !== -1 ? '&' : '?';
                videoFrame.src = embedUrl + sep + 'autoplay=1';
            });
            videoModalEl.addEventListener('hidden.bs.modal', function () {
                videoFrame.src = '';
            });
        }
    });
    </script>

    <!-- Script: บันทึกคอร์สไว้ทีหลัง (Bookmark) -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var btnBookmark = document.getElementById('btnToggleBookmark');
        if (!btnBookmark) return;

        function showAlert(icon, title, text) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: text,
                    icon: icon,
                    confirmButtonColor: '#6366F1',
                    timer: icon === 'success' ? 1800 : undefined,
                    showConfirmButton: icon !== 'success',
                    customClass: {
                        popup: 'levelup-swal-popup',
                        title: 'levelup-swal-title',
                        htmlContainer: 'levelup-swal-text'
                    }
                });
            } else {
                alert(title + (text ? ': ' + text : ''));
            }
        }

        btnBookmark.addEventListener('click', function () {
            var courseId = btnBookmark.getAttribute('data-id');
            var icon = document.getElementById('bookmarkIcon');
            var text = document.getElementById('bookmarkText');

            btnBookmark.disabled = true;

            fetch('toggle-save-course.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'course_id=' + encodeURIComponent(courseId)
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                btnBookmark.disabled = false;

                if (!data.success) {
                    showAlert('warning', 'ไม่สำเร็จ', data.message || 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
                    return;
                }

                btnBookmark.setAttribute('data-saved', data.saved ? '1' : '0');

                if (data.saved) {
                    icon.classList.remove('bi-bookmark-star');
                    icon.classList.add('bi-bookmark-star-fill');
                    text.textContent = 'บันทึกไว้แล้ว';
                } else {
                    icon.classList.remove('bi-bookmark-star-fill');
                    icon.classList.add('bi-bookmark-star');
                    text.textContent = 'บันทึกไว้ทีหลัง';
                }

                showAlert('success', data.saved ? 'บันทึกสำเร็จ!' : 'ยกเลิกการบันทึกแล้ว', data.message);
            })
            .catch(function () {
                btnBookmark.disabled = false;
                showAlert('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อกับระบบได้ กรุณาลองใหม่อีกครั้ง');
            });
        });
    });
    </script>

    <!-- Include Footer -->
    <?php include_once $ROOT_PATH . 'includes/footer.php'; ?>