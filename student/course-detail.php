<?php
$ROOT_PATH = '../';
session_start();

require_once '../config/db.php';

// รับค่า id หรือ course_id จาก URL
$course_id = $_GET['id'] ?? $_GET['course_id'] ?? 0;

// 1. ดึงข้อมูล Course หลัก + Category + Instructor (รวม Query เดียวเพื่อลด Resource)
$sql = "SELECT c.*, 
               ct.name AS category_name,
               u.id AS instructor_id,
               u.name AS instructor_name,
               u.img AS instructor_img,
               u.user_role AS instructor_role
        FROM `course` c 
        LEFT JOIN `category` ct ON c.category_id = ct.id 
        LEFT JOIN `users` u ON c.user_id = u.id
        WHERE c.id = :course_id";

$stmt = $conn->prepare($sql);
$stmt->execute([':course_id' => $course_id]);
$course_data = $stmt->fetch(PDO::FETCH_ASSOC);

// ดักจับกรณีหา Course ไม่เจอ
if (!$course_data) {
    die("ไม่พบข้อมูลคอร์สเรียนนี้");
}

// 2. ดึงข้อมูล Outcomes
$course_outcomes = [];
$sql_outcomes = "SELECT name FROM `outcomes` WHERE course_id = :course_id";
$stmt_outcomes = $conn->prepare($sql_outcomes);
$stmt_outcomes->execute([':course_id' => $course_id]);
$course_outcomes = $stmt_outcomes->fetchAll(PDO::FETCH_COLUMN);

// 3. จัดโครงสร้างข้อมูลสำหรับนำไปใช้ในหน้า HTML
$course = [
    'id' => $course_id,
    'title' => $course_data['title'] ?? 'Untitled Course',
    'subtitle' => $course_data['subtitle'] ?? '',
    'brief' => $course_data['short_description'] ?? '',
    'description' => $course_data['full_description'] ?? '',
    'category' => $course_data['category_name'] ?? 'ทั่วไป',
    'level' => $course_data['difficulty'] ?? 'All Levels',
    'language' => $course_data['language'] ?? 'TH',
    'image' => $course_data['img'] ?? 'https://via.placeholder.com/600x400',
    'video_url' => $course_data['video'] ?? null,
    'time_total' => $course_data['time_total'] ?? 0,
    'chapter_total' => $course_data['chapter_total'] ?? 0,
    'time_week' => $course_data['time_week'] ?? 0,
    'target' => $course_data['target'] ?? '',
    'certificate_type' => $course_data['certificate_type'] ?? 'มีใบรับรองเมื่อเรียนจบ',
    'updated_at' => $course_data['updated_at'] ?? null,
    
    // ดึงราคาจากคอลัมน์ pice
    'price' => (isset($course_data['pice']) && is_numeric($course_data['pice'])) ? (float)$course_data['pice'] : 0,
    'type_text' => (!empty($course_data['pice']) && is_numeric($course_data['pice']) && (float)$course_data['pice'] > 0) 
                    ? number_format((float)$course_data['pice']) . ' ฿' 
                    : 'เรียนฟรี',
    
    // ข้อมูลจำลองสำหรับส่วนที่ยังไม่มีตาราง (สถิติ/รีวิว)
    'rating' => 4.8,
    'reviews_count' => 1245,
    'students_count' => 8500,
    'outcomes' => $course_outcomes,
    
    // ดึงข้อมูลมาใส่ประโยชน์ที่จะได้รับแบบ Dynamic
    'includes' => [
        ['icon' => 'bi-play-circle', 'text' => 'วิดีโอรวม ' . ($course_data['time_total'] ?? 0) . ' ชั่วโมง'],
        ['icon' => 'bi-journal-code', 'text' => 'เนื้อหาทั้งหมด ' . ($course_data['chapter_total'] ?? 0) . ' บทเรียน'],
        ['icon' => 'bi-clock-history', 'text' => 'แนะนำเรียน ' . ($course_data['time_week'] ?? 0) . ' ชม./สัปดาห์'],
        ['icon' => 'bi-robot', 'text' => 'AI Tutor ช่วยตอบคำถาม 24/7'],
        ['icon' => 'bi-award', 'text' => $course_data['certificate_type'] ?? 'มีใบรับรองเมื่อเรียนจบ']
    ],
    'instructor' => [
        'id' => $course_data['instructor_id'] ?? $course_data['user_id'] ?? 0,
        'name' => $course_data['instructor_name'] ?? 'Unknown Instructor',
        'title' => $course_data['instructor_role'] ?? 'Instructor',
        'company' => "BU",
        'image' => !empty($course_data['instructor_img']) ? $course_data['instructor_img'] : 'https://via.placeholder.com/150',
        'courses' => 5,
        'students' => 25000,
        'rating' => 4.9,
        'bio' => 'ผู้สอนและนักพัฒนาซอฟต์แวร์ผู้เชี่ยวชาญประจำหลักสูตร'
    ]
];

$ai_analysis = [
    'score' => 88,
    'match_text' => 'คอร์สนี้เหมาะกับคุณ 88%',
    'reasons' => [
        'คุณมีพื้นฐาน HTML/CSS แล้ว',
        'สอดคล้องกับเป้าหมาย Career Path ของคุณ'
    ],
    'prerequisites' => [
        ['title' => 'Basic HTML/CSS', 'status' => 'passed'],
        ['title' => 'Basic PHP', 'status' => 'learning'],
        ['title' => 'JavaScript DOM', 'status' => 'missing', 'suggest_id' => '50']
    ],
    'est_time' => ($course['time_week'] > 0 ? ceil($course['time_total'] / $course['time_week']) . ' สัปดาห์' : '18 วัน')
];

$reviews = [
    ['name' => 'สมชาย สายโค้ด', 'rating' => 5, 'date' => '2 วันที่แล้ว', 'comment' => 'เนื้อหาดีมากครับ สอนเข้าใจง่าย นำ AI มาประยุกต์ใช้ได้จริง ทำงานไวขึ้นเยอะเลย!', 'avatar' => 'https://i.pravatar.cc/150?img=11'],
    ['name' => 'กิตติพงษ์ Dev', 'rating' => 5, 'date' => '1 สัปดาห์ที่แล้ว', 'comment' => 'ชอบส่วน Coding Lab มากครับ ได้ลงมือทำจริง ระบบซัพพอร์ต AI ตอบคำถามตรงจุด', 'avatar' => 'https://i.pravatar.cc/150?img=12'],
    ['name' => 'อนันต์ สุขใจ', 'rating' => 4, 'date' => '2 สัปดาห์ที่แล้ว', 'comment' => 'เนื้อหาแน่นมาก เหมาะกับคนพอมีพื้นฐานมาบ้างแล้ว คุ้มค่าเงินครับ', 'avatar' => 'https://i.pravatar.cc/150?img=13']
];

// 4. ดึงข้อมูล คอร์สเรียนที่เกี่ยวข้อง (Related Courses) จาก Database
$related_courses = [];

try {
    $category_id = $course_data['category_id'] ?? 0;

    // ดึงคอร์สจาก DB (เรียงคอร์สหมวดหมู่เดียวกันก่อน และข้ามคอร์สปัจจุบัน)
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
        // ตรวจสอบรูปภาพประกอบ
        $rawImg = $rel['img'] ?? $rel['cover_file'] ?? $rel['cover'] ?? '';
        $imgPath = $fallbackImg;

        if (!empty($rawImg)) {
            if (filter_var($rawImg, FILTER_VALIDATE_URL)) {
                $imgPath = $rawImg;
            } elseif (file_exists($ROOT_PATH . $rawImg)) {
                $imgPath = $ROOT_PATH . $rawImg;
            } elseif (file_exists($ROOT_PATH . 'uploads/' . basename($rawImg))) {
                $imgPath = $ROOT_PATH . 'uploads/' . basename($rawImg);
            }
        }

        // จัดรูปแบบราคาจากคอลัมน์ pice ของคอร์สที่เกี่ยวข้อง
        $price_text = (!empty($rel['pice']) && is_numeric($rel['pice']) && (float)$rel['pice'] > 0) 
            ? number_format((float)$rel['pice']) . ' ฿' 
            : 'เรียนฟรี';

        $related_courses[] = [
            'id'         => $rel['id'],
            'title'      => $rel['title'] ?? $rel['name'] ?? 'คอร์สเรียน',
            'instructor' => $rel['instructor_name'] ?? 'Instructor',
            'price'      => $price_text,
            'rating'     => $rel['rating'] ?? 4.8,
            'reviews'    => $rel['reviews_count'] ?? 100,
            'image'      => $imgPath
        ];
    }
} catch (PDOException $e) {
    error_log("Related courses query error: " . $e->getMessage());
    $related_courses = [];
}

function getEmbedUrl($url)
{
    if (empty($url)) return '';

    // YouTube
    $youtube_pattern = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';
    if (preg_match($youtube_pattern, $url, $match)) {
        return "https://www.youtube.com/embed/" . $match[1];
    }

    // Google Drive
    if (preg_match('%drive\.google\.com/file/d/([^/]+)%i', $url, $match)) {
        return "https://drive.google.com/file/d/" . $match[1] . "/preview";
    }

    // Vimeo
    if (preg_match('%vimeo\.com/([0-9]+)%i', $url, $match)) {
        return "https://player.vimeo.com/video/" . $match[1];
    }

    return $url;
}

include_once $ROOT_PATH . 'includes/header.php';
?>

    <!-- 1. Hero Section -->
    <section class="course-hero py-4 py-lg-5 position-relative overflow-hidden">
        <div class="container-xl position-relative z-1">
            <nav aria-label="breadcrumb" class="mb-3 mb-lg-4 d-none d-md-block">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="courses.php" class="text-white-50 text-decoration-none hover-text-white">Courses</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-white-50 text-decoration-none hover-text-white"><?= htmlspecialchars($course['category']) ?></a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page"><?= htmlspecialchars($course['title']) ?></li>
                </ol>
            </nav>

            <div class="row align-items-center">
                <div class="col-lg-7 pb-3 pb-lg-0">
                    <h1 class="fw-bold mb-2 text-white lh-sm" style="font-size: clamp(1.8rem, 4vw, 2.4rem);">
                        <?= htmlspecialchars($course['title']) ?>
                    </h1>
                    <?php if (!empty($course['subtitle'])): ?>
                        <h6 class="text-info fw-normal mb-3"><?= htmlspecialchars($course['subtitle']) ?></h6>
                    <?php endif; ?>
                    
                    <p class="fs-6 text-white-50 mb-4 fw-light" style="max-width: 620px;">
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
                        <span class="text-white-50 d-none d-sm-inline">|</span>
                        <div class="d-flex align-items-center gap-1 bg-dark bg-opacity-10 px-3 py-1.5 rounded-pill border border-color">
                            <i class="bi bi-star-fill text-warning me-1 small"></i>
                            <span class="fw-bold text-white small"><?= $course['rating'] ?></span>
                            <a href="#reviews" class="text-white-50 text-decoration-none ms-1 small hover-text-white">(<?= number_format($course['reviews_count']) ?> รีวิว)</a>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3 gap-md-4 mb-4 text-white-50" style="font-size: 0.85rem;">
                        <div class="d-flex align-items-center gap-1"><i class="bi bi-clock me-1"></i> รวม <?= htmlspecialchars($course['time_total']) ?> ชั่วโมง</div>
                        <div class="d-flex align-items-center gap-1"><i class="bi bi-journal-check me-1"></i> <?= htmlspecialchars($course['chapter_total']) ?> บทเรียน</div>
                        <div class="d-flex align-items-center gap-1"><i class="bi bi-bar-chart me-1"></i> <?= htmlspecialchars($course['level']) ?></div>
                        <div class="d-flex align-items-center gap-1"><i class="bi bi-translate me-1"></i> <?= htmlspecialchars($course['language']) ?></div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <a href="instructor-profile.php?id=<?= urlencode($course['instructor']['id']) ?>">
                            <img src="<?= htmlspecialchars($course['instructor']['image']) ?>" class="rounded-circle border border-2 border-info shadow-sm" width="48" height="48" alt="Instructor" style="object-fit: cover;">
                        </a>
                        <div>
                            <p class="mb-0 text-white-50" style="font-size: 0.75rem;">ผู้ฝึกสอน</p>
                            <a href="instructor-profile.php?id=<?= urlencode($course['instructor']['id']) ?>" class="text-white text-decoration-none fw-semibold hover-text-white" style="color: var(--lv-cyan) !important; font-size: 0.95rem;"><?= htmlspecialchars($course['instructor']['name']) ?></a>
                        </div>
                    </div>
                </div>

                <!-- Right Widget -->
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="ai-readiness-widget rounded-4 p-4 w-100 ms-auto" style="max-width: 410px;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="badge bg-gradient-primary rounded-pill mb-2"><i class="bi bi-cpu me-1"></i> AI SYNC ANALYSIS</span>
                                <h5 class="fw-bold mb-1 text-white">ความพร้อมสำหรับเควส</h5>
                            </div>
                            <div class="readiness-circle" style="--score: <?= $ai_analysis['score'] ?>%;">
                                <span><?= $ai_analysis['score'] ?>%</span>
                            </div>
                        </div>
                        <p class="fw-medium mb-3 text-white-50 small"><i class="bi bi-graph-up-arrow text-info me-2"></i><?= htmlspecialchars($ai_analysis['match_text']) ?></p>

                        <ul class="list-unstyled mb-4 small">
                            <?php foreach ($ai_analysis['reasons'] as $reason): ?>
                                <li class="mb-2 text-white-50"><i class="bi bi-shield-check text-success me-2"></i><?= htmlspecialchars($reason) ?></li>
                            <?php endforeach; ?>
                            <li class="mb-0 text-white-50"><i class="bi bi-clock-history text-info me-2"></i>คาดว่าจะเรียนจบใน <strong><?= $ai_analysis['est_time'] ?></strong></li>
                        </ul>

                        <div class="d-grid gap-2">
                            <a href="lesson.php?course_id=<?= $course['id'] ?>&lesson_id=1" class="btn btn-premium btn-lg rounded-3 fw-bold py-2 text-center text-decoration-none">
                                เข้าสู่บทเรียน (<?= htmlspecialchars($course['type_text']) ?>)
                            </a>
                            <button class="btn btn-outline-surface btn-lg rounded-3 d-flex justify-content-center align-items-center gap-2 py-2" id="btnToggleBookmark" type="button" data-id="<?= $course['id'] ?>" style="font-size: 0.9rem;">
                                <i class="bi bi-bookmark-star" id="bookmarkIcon"></i> <span id="bookmarkText">บันทึกไว้ทีหลัง</span>
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
                        <h5 class="fw-bold mb-3 text-white d-flex align-items-center fs-5"><i class="bi bi-file-text text-info me-2"></i> รายละเอียดคอร์สเรียน (Description)</h5>
                        <div class="text-white-50 fw-light lh-lg mb-0" style="font-size: 0.92rem;">
                            <?= $course['description'] ?>
                        </div>
                    </div>

                    <?php if (!empty($course['target'])): ?>
                        <!-- Target Audience -->
                        <div class="dashboard-card rounded-4 p-4 mb-4">
                            <h5 class="fw-bold mb-2 text-white d-flex align-items-center fs-6"><i class="bi bi-bullseye text-danger me-2"></i> คอร์สนี้เหมาะสำหรับใคร</h5>
                            <div class="text-white-50 mb-0 small">
                                <?= $course['target'] ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- What You'll Learn & Prerequisites -->
                    <div class="dashboard-card rounded-4 p-4 mb-4">
                        <div class="row g-4">
                            <!-- สิ่งที่จะได้เรียนรู้ -->
                            <div class="col-md-6 border-end-md border-color">
                                <h5 class="fw-bold mb-3 text-white d-flex align-items-center fs-6"><i class="bi bi-check-circle-fill text-success me-2"></i> สิ่งที่คุณจะได้เรียนรู้</h5>
                                <?php if (!empty($course['outcomes'])): ?>
                                    <ul class="list-unstyled mb-0 small">
                                        <?php foreach ($course['outcomes'] as $outcome): ?>
                                            <li class="d-flex align-items-start mb-2.5 text-white-50">
                                                <i class="bi bi-check2 text-info me-2 mt-0.5"></i>
                                                <span><?= htmlspecialchars($outcome) ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-white-50 small mb-0">ไม่มีข้อมูลผลลัพธ์การเรียนรู้ที่ระบุไว้</p>
                                <?php endif; ?>
                            </div>
                            <!-- ความรู้พื้นฐานที่ต้องมี -->
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3 text-white d-flex align-items-center fs-6"><i class="bi bi-list-check text-warning me-2"></i> ตรวจสอบความพร้อมพื้นฐาน</h5>
                                <div class="d-flex flex-column gap-2">
                                    <?php foreach ($ai_analysis['prerequisites'] as $pre): ?>
                                        <div class="d-flex align-items-center justify-content-between p-2 rounded prerequisite-item small">
                                            <span class="text-white-50"><?= htmlspecialchars($pre['title']) ?></span>
                                            <?php if ($pre['status'] == 'passed'): ?>
                                                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25"><i class="bi bi-check-lg me-1"></i> ผ่านแล้ว</span>
                                            <?php elseif ($pre['status'] == 'learning'): ?>
                                                <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25"><i class="bi bi-hourglass-split me-1"></i> กำลังเรียน</span>
                                            <?php else: ?>
                                                <a href="course-detail.php?id=<?= $pre['suggest_id'] ?>" class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25 text-decoration-none"><i class="bi bi-plus-lg me-1"></i> แนะนำเรียนเพิ่ม</a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Curriculum Roadmap Accordion -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-end mb-3">
                            <div>
                                <h5 class="fw-bold mb-1 text-white d-flex align-items-center fs-5"><i class="bi bi-map text-primary me-2"></i> แผนที่เส้นทางการเรียนรู้</h5>
                                <p class="text-white-50 mb-0 small"><?= htmlspecialchars($course['chapter_total']) ?> บทเรียน • รวมเวลาเรียน <?= htmlspecialchars($course['time_total']) ?> ชั่วโมง</p>
                            </div>
                            <button class="btn btn-link text-info text-decoration-none p-0 small" id="btnExpandAll" type="button" style="font-size: 0.85rem;">กางออกทั้งหมด</button>
                        </div>

                        <div class="accordion custom-accordion" id="curriculumAccordion">
                            <?php
                            $sections = ['Introduction & Environment Setup', 'Frontend Development with React', 'Backend Architecture & Database', 'AI Agents Integration & Deployment'];
                            foreach ($sections as $index => $sec):
                            ?>
                                <div class="accordion-item mb-2 overflow-hidden">
                                    <h2 class="accordion-header" id="heading<?= $index ?>">
                                        <button class="accordion-button <?= $index != 0 ? 'collapsed' : '' ?> bg-transparent fw-bold text-white shadow-none"
                                            type="button"
                                            data-bs-target="#collapse<?= $index ?>"
                                            aria-expanded="<?= $index == 0 ? 'true' : 'false' ?>">
                                            <div class="flex-grow-1 d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center me-3 gap-1">
                                                <span>Section <?= $index + 1 ?>: <?= $sec ?></span>
                                                <span class="fw-normal text-white-50 small"><i class="bi bi-clock me-1"></i>5 บทเรียน</span>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse<?= $index ?>" class="accordion-collapse collapse <?= $index == 0 ? 'show' : '' ?>">
                                        <div class="accordion-body p-0 border-top border-color bg-surface bg-opacity-25">
                                            <div class="list-group list-group-flush">

                                                <a href="lesson.php?course_id=<?= $course['id'] ?>&lesson_id=<?= ($index * 5) + 1 ?>"
                                                    class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-3 px-3 px-md-4 lesson-row-link">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <i class="bi bi-play-circle-fill text-info fs-5 flex-shrink-0"></i>
                                                        <div>
                                                            <span class="text-white fw-medium small d-block lh-sm mb-1 lesson-title">
                                                                Welcome to the Course
                                                            </span>
                                                            <span class="d-block text-white-50" style="font-size: 0.72rem;">Video Mission • 05:30</span>
                                                        </div>
                                                    </div>
                                                    <i class="bi bi-chevron-right text-white-50 small"></i>
                                                </a>

                                                <a href="lesson.php?course_id=<?= $course['id'] ?>&lesson_id=<?= ($index * 5) + 2 ?>"
                                                    class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-3 px-3 px-md-4 lesson-row-link">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <i class="bi bi-terminal-fill text-warning fs-5 flex-shrink-0"></i>
                                                        <div>
                                                            <span class="text-white fw-medium small d-block lh-sm mb-1 lesson-title">
                                                                Environment Setup & AI Tools Lab
                                                            </span>
                                                            <span class="d-block text-white-50" style="font-size: 0.72rem;">Coding Lab Challenge • 15:00</span>
                                                        </div>
                                                    </div>
                                                    <i class="bi bi-chevron-right text-white-50 small"></i>
                                                </a>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Instructor Profile -->
                    <div id="instructor" class="dashboard-card rounded-4 p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                            <h5 class="fw-bold text-white mb-0 fs-5"><i class="bi bi-person-badge text-info me-2"></i> ข้อมูลผู้สอน (Mentor)</h5>
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
                                    <a href="instructor-profile.php?id=<?= urlencode($course['instructor']['id']) ?>" class="text-white text-decoration-none hover-text-white">
                                        <?= htmlspecialchars($course['instructor']['name']) ?>
                                    </a>
                                </h5>
                                <p class="text-info fw-medium mb-3 small"><?= htmlspecialchars($course['instructor']['title']) ?> @ <?= htmlspecialchars($course['instructor']['company']) ?></p>
                                <p class="text-white-50 mb-3 fw-light small" style="line-height: 1.5;"><?= htmlspecialchars($course['instructor']['bio']) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Student Reviews Section -->
                    <div id="reviews" class="dashboard-card rounded-4 p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-white mb-0 fs-5"><i class="bi bi-chat-square-quote text-warning me-2"></i> รีวิวจากผู้เรียน</h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fs-4 fw-bold text-white"><?= $course['rating'] ?></span>
                                <div class="text-warning small">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-half"></i>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($reviews as $rev): ?>
                                <div class="p-3 rounded-3 review-card-item">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center gap-2.5">
                                            <img src="<?= $rev['avatar'] ?>" class="rounded-circle" width="36" height="36" alt="User Avatar">
                                            <div>
                                                <h6 class="fw-semibold text-white mb-0 small"><?= htmlspecialchars($rev['name']) ?></h6>
                                                <span class="text-white-50" style="font-size: 0.7rem;"><?= $rev['date'] ?></span>
                                            </div>
                                        </div>
                                        <div class="text-warning extra-small">
                                            <?php for ($i = 0; $i < $rev['rating']; $i++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
                                        </div>
                                    </div>
                                    <p class="text-white-50 mb-0 small fw-light"><?= htmlspecialchars($rev['comment']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Related Courses Section -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-white mb-3 fs-5"><i class="bi bi-journal-bookmark text-info me-2"></i> คอร์สเรียนที่เกี่ยวข้อง</h5>
                        <div class="row g-3">
                            <?php if (!empty($related_courses)): ?>
                                <?php foreach ($related_courses as $rel): ?>
                                    <div class="col-md-4">
                                        <a href="course-detail.php?id=<?= $rel['id'] ?>" class="text-decoration-none">
                                            <div class="dashboard-card bg-surface rounded-4 overflow-hidden course-card-hover h-100 d-flex flex-column">
                                                <img src="<?= htmlspecialchars($rel['image']) ?>" class="w-100 object-fit-cover" height="120" alt="Course Thumbnail">
                                                <div class="p-3 d-flex flex-column flex-grow-1">
                                                    <h6 class="fw-bold text-white mb-2 line-clamp-2 small" style="line-height: 1.4;"><?= htmlspecialchars($rel['title']) ?></h6>
                                                    <span class="text-white-50 mb-3 d-block" style="font-size: 0.75rem;"><?= htmlspecialchars($rel['instructor']) ?></span>
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
                                    <p class="text-white-50 mb-0 small">ยังไม่มีคอร์สเรียนที่เกี่ยวข้องในขณะนี้</p>
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
                            <h6 class="fw-bold mb-3 text-white"><i class="bi bi-box-seam text-primary me-2"></i> สิทธิ์ประโยชน์ที่จะได้รับ</h6>
                            <ul class="list-unstyled mb-0 small">
                                <?php foreach ($course['includes'] as $inc): ?>
                                    <li class="d-flex align-items-center mb-3 text-white-50">
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
                                    <h6 class="fw-bold mb-0 text-white" style="font-size: 0.95rem; line-height: 1.4;">Holo AI Assistant</h6>
                                    <span class="subtext-bright d-block">วิเคราะห์หลักสูตรเรียลไทม์ 24/7</span>
                                </div>
                            </div>

                            <div class="p-3 flex-grow-1 overflow-auto bg-surface ai-chat-body" id="aiChatBody" style="height: 260px;">
                                <div class="ai-msg d-flex gap-2 mb-3">
                                    <div class="chat-bubble-ai rounded-3 p-3 shadow-sm w-100" style="font-size: 0.85rem;">
                                        <p class="mb-2 fw-medium text-white">สวัสดีครับ! 😊 ผมเป็น AI ประจำวิชานี้ สอบถามข้อมูลโมดูลการเรียนได้เลยครับ</p>
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

    <!-- Mobile Bottom Sticky Bar -->
    <div class="mobile-bottom-bar fixed-bottom d-lg-none py-2 px-3">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <div>
                <span class="d-block text-white-50" style="font-size: 0.7rem;">ราคาคอร์ส</span>
                <span class="fw-bold text-info fs-6"><?= htmlspecialchars($course['type_text']) ?></span>
            </div>
            <a href="lesson.php?course_id=<?= $course['id'] ?>&lesson_id=1" class="btn btn-premium rounded-3 fw-bold btn-sm px-4 py-2 text-decoration-none">
                เข้าสู่บทเรียน
            </a>
        </div>
    </div>

    <!-- Video Preview Modal -->
    <div class="modal fade" id="videoPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark border border-color rounded-4 overflow-hidden">
                <div class="modal-header border-bottom border-color p-3">
                    <h6 class="modal-title text-white fw-bold"><i class="bi bi-play-circle text-info me-2"></i> ตัวอย่างบทเรียน: <?= htmlspecialchars($course['title']) ?></h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 ratio ratio-16x9">
                    <?php if (!empty($course['video_url'])): ?>
                        <?php
                        $video_url = $course['video_url'];
                        $is_local_video = false;
                        $ext = strtolower(pathinfo($video_url, PATHINFO_EXTENSION));
                        if (in_array($ext, ['mp4', 'webm', 'ogg']) || strpos($video_url, 'uploads/') !== false) {
                            $is_local_video = true;
                        }
                        ?>
                        <?php if ($is_local_video): ?>
                            <video id="previewVideoPlayer" src="<?= htmlspecialchars($video_url) ?>" controls class="w-100 h-100" style="object-fit: contain; background: #000;"></video>
                        <?php else: ?>
                            <iframe id="previewVideoIframe" src="<?= htmlspecialchars(getEmbedUrl($video_url)) ?>" title="Course Preview Video" allowfullscreen></iframe>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include_once $ROOT_PATH . 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ควบคุมการหยุดเล่นวิดีโอเมื่อปิด Modal เพื่อไม่ให้เสียงยังคงเล่นอยู่เบื้องหลัง
            const videoModal = document.getElementById('videoPreviewModal');
            if (videoModal) {
                videoModal.addEventListener('hidden.bs.modal', function() {
                    const localPlayer = document.getElementById('previewVideoPlayer');
                    if (localPlayer) {
                        localPlayer.pause();
                        localPlayer.currentTime = 0;
                    }
                    const iframePlayer = document.getElementById('previewVideoIframe');
                    if (iframePlayer) {
                        const currentSrc = iframePlayer.getAttribute('src');
                        iframePlayer.setAttribute('src', '');
                        iframePlayer.setAttribute('src', currentSrc);
                    }
                });
            }

            // Accordion Toggle Controls
            const accordionButtons = document.querySelectorAll('#curriculumAccordion .accordion-button');
            accordionButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetSelector = this.getAttribute('data-bs-target') || this.getAttribute('data-target');
                    if (!targetSelector) return;
                    const targetContent = document.querySelector(targetSelector);
                    if (!targetContent) return;

                    const isCollapsed = this.classList.contains('collapsed');

                    if (isCollapsed) {
                        this.classList.remove('collapsed');
                        this.setAttribute('aria-expanded', 'true');
                        targetContent.classList.add('show');
                    } else {
                        this.classList.add('collapsed');
                        this.setAttribute('aria-expanded', 'false');
                        targetContent.classList.remove('show');
                    }
                });
            });

            // Expand All / Collapse All JS Logic
            let isAllExpanded = false;
            const btnExpandAll = document.getElementById('btnExpandAll');
            if (btnExpandAll) {
                btnExpandAll.addEventListener('click', function() {
                    const collapses = document.querySelectorAll('#curriculumAccordion .accordion-collapse');
                    const buttons = document.querySelectorAll('#curriculumAccordion .accordion-button');

                    isAllExpanded = !isAllExpanded;

                    collapses.forEach(c => {
                        if (isAllExpanded) c.classList.add('show');
                        else c.classList.remove('show');
                    });

                    buttons.forEach(b => {
                        if (isAllExpanded) {
                            b.classList.remove('collapsed');
                            b.setAttribute('aria-expanded', 'true');
                        } else {
                            b.classList.add('collapsed');
                            b.setAttribute('aria-expanded', 'false');
                        }
                    });

                    this.textContent = isAllExpanded ? 'หุบเข้าทั้งหมด' : 'กางออกทั้งหมด';
                });
            }

            // Bookmark Toggle JS Logic
            const btnBookmark = document.getElementById('btnToggleBookmark');
            if (btnBookmark) {
                btnBookmark.addEventListener('click', function() {
                    const icon = document.getElementById('bookmarkIcon');
                    const text = document.getElementById('bookmarkText');

                    if (icon.classList.contains('bi-bookmark-star')) {
                        icon.classList.replace('bi-bookmark-star', 'bi-bookmark-star-fill');
                        icon.classList.add('text-warning');
                        text.textContent = 'บันทึกแล้ว';
                    } else {
                        icon.classList.replace('bi-bookmark-star-fill', 'bi-bookmark-star');
                        icon.classList.remove('text-warning');
                        text.textContent = 'บันทึกไว้ทีหลัง';
                    }
                });
            }

            // Chatbot AI Interactive Logic
            $('#courseQaForm').on('submit', function(e) {
                e.preventDefault();
                const $input = $('#qaInput');
                const questionText = $input.val().trim();
                if (!questionText) return;
                executeAiChatResponse(questionText);
                $input.val('');
            });

            $(document).on('click', '.qa-quick-btn', function(e) {
                e.preventDefault();
                executeAiChatResponse($(this).text().trim());
            });

            function executeAiChatResponse(text) {
                const $chatBody = $('#aiChatBody');
                $chatBody.append(`
            <div class="user-msg d-flex gap-2 mb-3 justify-content-end">
                <div class="chat-bubble-user rounded-3 p-2.5 shadow-sm text-start" style="max-width: 85%; font-size: 0.85rem; color: #fff;">
                    ${text}
                </div>
            </div>
        `);
                scrollToChatBottom();

                const loadingId = 'ai-loading-' + Date.now();
                $chatBody.append(`
            <div class="ai-msg d-flex gap-2 mb-3" id="${loadingId}">
                <div class="chat-bubble-ai rounded-3 p-2.5 shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                    <div class="spinner-border spinner-border-sm text-info" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span>กำลังคิด...</span>
                </div>
            </div>
        `);
                scrollToChatBottom();

                // Mocking AI Response Delay
                setTimeout(() => {
                    $(`#${loadingId}`).remove();
                    $chatBody.append(`
                <div class="ai-msg d-flex gap-2 mb-3">
                    <div class="chat-bubble-ai rounded-3 p-3 shadow-sm w-100" style="font-size: 0.85rem;">
                        ขออภัยครับ ตอนนี้ผมเป็นเพียงระบบ Demo ยังไม่สามารถตอบคำถามของคุณได้จริงในขณะนี้ 😅
                    </div>
                </div>
            `);
                    scrollToChatBottom();
                }, 1500);
            }

            function scrollToChatBottom() {
                const $chatBody = $('#aiChatBody');
                $chatBody.scrollTop($chatBody[0].scrollHeight);
            }

        });
    </script>
</body>

</html>