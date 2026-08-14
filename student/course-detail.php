<?php
$ROOT_PATH = '../';
session_start();

require_once '../config/db.php';

// รับค่า id หรือ course_id จาก URL
$course_id = $_GET['id'] ?? $_GET['course_id'] ?? 0;

// 1. ดึงข้อมูล Course หลัก + Category + Instructor
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
    
    'price' => (isset($course_data['pice']) && is_numeric($course_data['pice'])) ? (float)$course_data['pice'] : 0,
    'type_text' => (!empty($course_data['pice']) && is_numeric($course_data['pice']) && (float)$course_data['pice'] > 0) 
                    ? number_format((float)$course_data['pice']) . ' ฿' 
                    : 'เรียนฟรี',
    
    'rating' => 4.8,
    'reviews_count' => 1245,
    'students_count' => 8500,
    'outcomes' => $course_outcomes,
    
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

// 4. ดึงข้อมูล Related Courses
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

include_once $ROOT_PATH . 'includes/header.php';
?>

<!-- Dynamic Theme CSS System -->
<style>
    /* Default (Light Mode) Variables */
    :root {
        --lv-card-bg: #ffffff;
        --lv-card-border: rgba(0, 0, 0, 0.08);
        --lv-text-primary: #1e293b;
        --lv-text-secondary: #64748b;
        --lv-accordion-header: #f8fafc;
        --lv-accordion-hover: #f1f5f9;
        --lv-btn-outline-border: #cbd5e1;
        --lv-btn-outline-text: #334155;
    }

    /* Dark Mode Auto-Adaptation */
    [data-bs-theme="dark"], body.dark-theme, body.dark-mode {
        --lv-card-bg: #111827;
        --lv-card-border: rgba(255, 255, 255, 0.12);
        --lv-text-primary: #f8fafc;
        --lv-text-secondary: #94a3b8;
        --lv-accordion-header: rgba(255, 255, 255, 0.04);
        --lv-accordion-hover: rgba(255, 255, 255, 0.08);
        --lv-btn-outline-border: rgba(255, 255, 255, 0.2);
        --lv-btn-outline-text: #f1f5f9;
    }

    /* Card & Text overrides */
    .dashboard-card, .ai-readiness-widget {
        background-color: var(--lv-card-bg) !important;
        border: 1px solid var(--lv-card-border) !important;
    }
    .theme-text-main { color: var(--lv-text-primary) !important; }
    .theme-text-sub { color: var(--lv-text-secondary) !important; }

    /* Accordion Fix for Light & Dark Mode */
    .custom-accordion .accordion-item {
        background-color: var(--lv-card-bg) !important;
        border: 1px solid var(--lv-card-border) !important;
        border-radius: 0.75rem !important;
    }
    .custom-accordion .accordion-button {
        background-color: var(--lv-accordion-header) !important;
        color: var(--lv-text-primary) !important;
        box-shadow: none !important;
    }
    .custom-accordion .accordion-button:not(.collapsed) {
        background-color: rgba(6, 182, 212, 0.1) !important;
        color: #06b6d4 !important;
    }
    [data-bs-theme="dark"] .custom-accordion .accordion-button::after,
    body.dark-mode .custom-accordion .accordion-button::after {
        filter: invert(1);
    }
    .lesson-row-link {
        color: var(--lv-text-primary) !important;
        border-bottom: 1px solid var(--lv-card-border) !important;
        transition: background-color 0.2s ease;
    }
    .lesson-row-link:hover {
        background-color: var(--lv-accordion-hover) !important;
    }

    /* Buttons Fix */
    .btn-premium {
        background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%) !important;
        color: #ffffff !important;
        border: none !important;
    }
    .btn-premium:hover {
        opacity: 0.9;
        color: #ffffff !important;
    }
    .btn-outline-surface {
        border: 1px solid var(--lv-btn-outline-border) !important;
        color: var(--lv-btn-outline-text) !important;
        background-color: transparent !important;
    }
    .btn-outline-surface:hover {
        background-color: var(--lv-accordion-hover) !important;
    }
</style>

    <!-- 1. Hero Section -->
    <section class="course-hero py-4 py-lg-5 position-relative overflow-hidden bg-dark text-white">
        <div class="container-xl position-relative z-1">
            <nav aria-label="breadcrumb" class="mb-3 mb-lg-4 d-none d-md-block">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="courses.php" class="text-white-50 text-decoration-none">Courses</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-white-50 text-decoration-none"><?= htmlspecialchars($course['category']) ?></a></li>
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
                                onclick="if(typeof Swal !== 'undefined') { Swal.fire({title: 'ไม่มีวิดีโอตัวอย่าง', text: 'ขออภัย คอร์สนี้ยังไม่มีการอัปโหลดวิดีโอแนะนำ', icon: 'info', confirmButtonColor: '#6366F1' }); } else { alert('ไม่มีวิดีโอตัวอย่าง'); }">
                                <i class="bi bi-play-circle-fill fs-5 text-secondary"></i> <span>ไม่มีวิดีโอตัวอย่าง</span>
                            </button>
                        <?php endif; ?>
                        <span class="text-white-50 d-none d-sm-inline">|</span>
                        <div class="d-flex align-items-center gap-1 bg-dark bg-opacity-50 px-3 py-1.5 rounded-pill border border-secondary">
                            <i class="bi bi-star-fill text-warning me-1 small"></i>
                            <span class="fw-bold text-white small"><?= $course['rating'] ?></span>
                            <a href="#reviews" class="text-white-50 text-decoration-none ms-1 small hover-text-white">(<?= number_format($course['reviews_count']) ?> รีวิว)</a>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3 gap-md-4 mb-4 text-white-50" style="font-size: 0.85rem;">
                        <div><i class="bi bi-clock me-1"></i> รวม <?= htmlspecialchars($course['time_total']) ?> ชั่วโมง</div>
                        <div><i class="bi bi-journal-check me-1"></i> <?= htmlspecialchars($course['chapter_total']) ?> บทเรียน</div>
                        <div><i class="bi bi-bar-chart me-1"></i> <?= htmlspecialchars($course['level']) ?></div>
                        <div><i class="bi bi-translate me-1"></i> <?= htmlspecialchars($course['language']) ?></div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <a href="instructor-profile.php?id=<?= urlencode($course['instructor']['id']) ?>">
                            <img src="<?= htmlspecialchars($course['instructor']['image']) ?>" class="rounded-circle border border-2 border-info shadow-sm" width="48" height="48" alt="Instructor" style="object-fit: cover;">
                        </a>
                        <div>
                            <p class="mb-0 text-white-50" style="font-size: 0.75rem;">ผู้ฝึกสอน</p>
                            <a href="instructor-profile.php?id=<?= urlencode($course['instructor']['id']) ?>" class="text-info text-decoration-none fw-semibold" style="font-size: 0.95rem;"><?= htmlspecialchars($course['instructor']['name']) ?></a>
                        </div>
                    </div>
                </div>

                <!-- Right Widget -->
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="ai-readiness-widget rounded-4 p-4 w-100 ms-auto shadow-sm" style="max-width: 410px;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="badge bg-primary rounded-pill mb-2"><i class="bi bi-cpu me-1"></i> AI SYNC ANALYSIS</span>
                                <h5 class="fw-bold mb-1 theme-text-main">ความพร้อมสำหรับเควส</h5>
                            </div>
                            <div class="readiness-circle fw-bold text-info fs-5">
                                <span><?= $ai_analysis['score'] ?>%</span>
                            </div>
                        </div>
                        <p class="fw-medium mb-3 theme-text-sub small"><i class="bi bi-graph-up-arrow text-info me-2"></i><?= htmlspecialchars($ai_analysis['match_text']) ?></p>

                        <ul class="list-unstyled mb-4 small">
                            <?php foreach ($ai_analysis['reasons'] as $reason): ?>
                                <li class="mb-2 theme-text-sub"><i class="bi bi-shield-check text-success me-2"></i><?= htmlspecialchars($reason) ?></li>
                            <?php endforeach; ?>
                            <li class="mb-0 theme-text-sub"><i class="bi bi-clock-history text-info me-2"></i>คาดว่าจะเรียนจบใน <strong class="theme-text-main"><?= $ai_analysis['est_time'] ?></strong></li>
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
                        <h5 class="fw-bold mb-3 theme-text-main d-flex align-items-center fs-5"><i class="bi bi-file-text text-info me-2"></i> รายละเอียดคอร์สเรียน (Description)</h5>
                        <div class="theme-text-sub fw-light lh-lg mb-0" style="font-size: 0.92rem;">
                            <?= $course['description'] ?>
                        </div>
                    </div>

                    <?php if (!empty($course['target'])): ?>
                        <!-- Target Audience -->
                        <div class="dashboard-card rounded-4 p-4 mb-4">
                            <h5 class="fw-bold mb-2 theme-text-main d-flex align-items-center fs-6"><i class="bi bi-bullseye text-danger me-2"></i> คอร์สนี้เหมาะสำหรับใคร</h5>
                            <div class="theme-text-sub mb-0 small">
                                <?= $course['target'] ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- What You'll Learn & Prerequisites -->
                    <div class="dashboard-card rounded-4 p-4 mb-4">
                        <div class="row g-4">
                            <div class="col-md-6 border-end-md">
                                <h5 class="fw-bold mb-3 theme-text-main d-flex align-items-center fs-6"><i class="bi bi-check-circle-fill text-success me-2"></i> สิ่งที่คุณจะได้เรียนรู้</h5>
                                <?php if (!empty($course['outcomes'])): ?>
                                    <ul class="list-unstyled mb-0 small">
                                        <?php foreach ($course['outcomes'] as $outcome): ?>
                                            <li class="d-flex align-items-start mb-2.5 theme-text-sub">
                                                <i class="bi bi-check2 text-info me-2 mt-0.5"></i>
                                                <span><?= htmlspecialchars($outcome) ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="theme-text-sub small mb-0">ไม่มีข้อมูลผลลัพธ์การเรียนรู้ที่ระบุไว้</p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3 theme-text-main d-flex align-items-center fs-6"><i class="bi bi-list-check text-warning me-2"></i> ตรวจสอบความพร้อมพื้นฐาน</h5>
                                <div class="d-flex flex-column gap-2">
                                    <?php foreach ($ai_analysis['prerequisites'] as $pre): ?>
                                        <div class="d-flex align-items-center justify-content-between p-2 rounded small border" style="border-color: var(--lv-card-border) !important;">
                                            <span class="theme-text-sub"><?= htmlspecialchars($pre['title']) ?></span>
                                            <?php if ($pre['status'] == 'passed'): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="bi bi-check-lg me-1"></i> ผ่านแล้ว</span>
                                            <?php elseif ($pre['status'] == 'learning'): ?>
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning"><i class="bi bi-hourglass-split me-1"></i> กำลังเรียน</span>
                                            <?php else: ?>
                                                <a href="course-detail.php?id=<?= $pre['suggest_id'] ?>" class="badge bg-danger bg-opacity-10 text-danger border border-danger text-decoration-none"><i class="bi bi-plus-lg me-1"></i> แนะนำเรียนเพิ่ม</a>
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
                                <h5 class="fw-bold mb-1 theme-text-main d-flex align-items-center fs-5"><i class="bi bi-map text-primary me-2"></i> แผนที่เส้นทางการเรียนรู้</h5>
                                <p class="theme-text-sub mb-0 small"><?= htmlspecialchars($course['chapter_total']) ?> บทเรียน • รวมเวลาเรียน <?= htmlspecialchars($course['time_total']) ?> ชั่วโมง</p>
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
                                        <button class="accordion-button <?= $index != 0 ? 'collapsed' : '' ?> fw-bold"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapse<?= $index ?>"
                                            aria-expanded="<?= $index == 0 ? 'true' : 'false' ?>">
                                            <div class="flex-grow-1 d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center me-3 gap-1">
                                                <span>Section <?= $index + 1 ?>: <?= $sec ?></span>
                                                <span class="fw-normal theme-text-sub small"><i class="bi bi-clock me-1"></i>5 บทเรียน</span>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse<?= $index ?>" class="accordion-collapse collapse <?= $index == 0 ? 'show' : '' ?>" data-bs-parent="#curriculumAccordion">
                                        <div class="accordion-body p-0">
                                            <div class="list-group list-group-flush">

                                                <a href="lesson.php?course_id=<?= $course['id'] ?>&lesson_id=<?= ($index * 5) + 1 ?>"
                                                    class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-3 px-3 px-md-4 lesson-row-link text-decoration-none">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <i class="bi bi-play-circle-fill text-info fs-5 flex-shrink-0"></i>
                                                        <div>
                                                            <span class="fw-medium small d-block lh-sm mb-1 theme-text-main">
                                                                Welcome to the Course
                                                            </span>
                                                            <span class="d-block theme-text-sub" style="font-size: 0.72rem;">Video Mission • 05:30</span>
                                                        </div>
                                                    </div>
                                                    <i class="bi bi-chevron-right theme-text-sub small"></i>
                                                </a>

                                                <a href="lesson.php?course_id=<?= $course['id'] ?>&lesson_id=<?= ($index * 5) + 2 ?>"
                                                    class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-3 px-3 px-md-4 lesson-row-link text-decoration-none">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <i class="bi bi-terminal-fill text-warning fs-5 flex-shrink-0"></i>
                                                        <div>
                                                            <span class="fw-medium small d-block lh-sm mb-1 theme-text-main">
                                                                Environment Setup & AI Tools Lab
                                                            </span>
                                                            <span class="d-block theme-text-sub" style="font-size: 0.72rem;">Coding Lab Challenge • 15:00</span>
                                                        </div>
                                                    </div>
                                                    <i class="bi bi-chevron-right theme-text-sub small"></i>
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
                            <h5 class="fw-bold theme-text-main mb-0 fs-5"><i class="bi bi-person-badge text-info me-2"></i> ข้อมูลผู้สอน (Mentor)</h5>
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
                                    <a href="instructor-profile.php?id=<?= urlencode($course['instructor']['id']) ?>" class="theme-text-main text-decoration-none">
                                        <?= htmlspecialchars($course['instructor']['name']) ?>
                                    </a>
                                </h5>
                                <p class="text-info fw-medium mb-3 small"><?= htmlspecialchars($course['instructor']['title']) ?> @ <?= htmlspecialchars($course['instructor']['company']) ?></p>
                                <p class="theme-text-sub mb-3 fw-light small" style="line-height: 1.5;"><?= htmlspecialchars($course['instructor']['bio']) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Student Reviews Section -->
                    <div id="reviews" class="dashboard-card rounded-4 p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold theme-text-main mb-0 fs-5"><i class="bi bi-chat-square-quote text-warning me-2"></i> รีวิวจากผู้เรียน</h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fs-4 fw-bold theme-text-main"><?= $course['rating'] ?></span>
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
                                <div class="p-3 rounded-3 border" style="border-color: var(--lv-card-border) !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center gap-2.5">
                                            <img src="<?= $rev['avatar'] ?>" class="rounded-circle" width="36" height="36" alt="User Avatar">
                                            <div>
                                                <h6 class="fw-semibold theme-text-main mb-0 small"><?= htmlspecialchars($rev['name']) ?></h6>
                                                <span class="theme-text-sub" style="font-size: 0.7rem;"><?= $rev['date'] ?></span>
                                            </div>
                                        </div>
                                        <div class="text-warning extra-small">
                                            <?php for ($i = 0; $i < $rev['rating']; $i++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
                                        </div>
                                    </div>
                                    <p class="theme-text-sub mb-0 small fw-light"><?= htmlspecialchars($rev['comment']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Related Courses Section -->
                    <div class="mb-4">
                        <h5 class="fw-bold theme-text-main mb-3 fs-5"><i class="bi bi-journal-bookmark text-info me-2"></i> คอร์สเรียนที่เกี่ยวข้อง</h5>
                        <div class="row g-3">
                            <?php if (!empty($related_courses)): ?>
                                <?php foreach ($related_courses as $rel): ?>
                                    <div class="col-md-4">
                                        <a href="course-detail.php?id=<?= $rel['id'] ?>" class="text-decoration-none">
                                            <div class="dashboard-card rounded-4 overflow-hidden h-100 d-flex flex-column">
                                                <img src="<?= htmlspecialchars($rel['image']) ?>" class="w-100 object-fit-cover" height="120" alt="Course Thumbnail">
                                                <div class="p-3 d-flex flex-column flex-grow-1">
                                                    <h6 class="fw-bold theme-text-main mb-2 line-clamp-2 small" style="line-height: 1.4;"><?= htmlspecialchars($rel['title']) ?></h6>
                                                    <span class="theme-text-sub mb-3 d-block" style="font-size: 0.75rem;"><?= htmlspecialchars($rel['instructor']) ?></span>
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
                                    <p class="theme-text-sub mb-0 small">ยังไม่มีคอร์สเรียนที่เกี่ยวข้องในขณะนี้</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

                <!-- Right Sidebar -->
                <div class="col-lg-4">
                    <div class="sticky-sidebar" style="position: sticky; top: 24px; z-index: 10;">

                        <!-- Benefits Card -->
                        <div class="dashboard-card rounded-4 p-4 mb-4">
                            <h6 class="fw-bold mb-3 theme-text-main"><i class="bi bi-box-seam text-primary me-2"></i> สิทธิ์ประโยชน์ที่จะได้รับ</h6>
                            <ul class="list-unstyled mb-0 small">
                                <?php foreach ($course['includes'] as $inc): ?>
                                    <li class="d-flex align-items-center mb-3 theme-text-main">
                                        <i class="bi <?= $inc['icon'] ?> fs-5 me-3 text-info"></i>
                                        <span><?= htmlspecialchars($inc['text']) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <!-- AI Chatbot Assistant -->
                        <div class="dashboard-card rounded-4 p-0 mb-4 overflow-hidden d-flex flex-column">
                            <div class="p-3 border-bottom d-flex align-items-center gap-2" style="border-color: var(--lv-card-border) !important; background: rgba(6, 182, 212, 0.08);">
                                <div class="text-info fs-3"><i class="bi bi-robot"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-0 theme-text-main" style="font-size: 0.95rem; line-height: 1.4;">Holo AI Assistant</h6>
                                    <span class="theme-text-sub d-block small">วิเคราะห์หลักสูตรเรียลไทม์ 24/7</span>
                                </div>
                            </div>

                            <div class="p-3 flex-grow-1 overflow-auto" id="aiChatBody" style="height: 260px;">
                                <div class="ai-msg d-flex gap-2 mb-3">
                                    <div class="rounded-3 p-3 shadow-sm w-100 border" style="font-size: 0.85rem; border-color: var(--lv-card-border) !important;">
                                        <p class="mb-2 fw-medium theme-text-main">สวัสดีครับ! 😊 ผมเป็น AI ประจำวิชานี้ สอบถามข้อมูลโมดูลการเรียนได้เลยครับ</p>
                                        <div class="d-flex flex-column gap-2 mt-3">
                                            <a href="#" class="btn btn-sm btn-outline-info text-start" style="font-size: 0.78rem;">คอร์สนี้เหมาะกับมือใหม่ไหม?</a>
                                            <a href="#" class="btn btn-sm btn-outline-info text-start" style="font-size: 0.78rem;">ได้ทำโปรเจกต์ส่งท้ายชิ้นไหนบ้าง?</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-2.5 border-top" style="border-color: var(--lv-card-border) !important;">
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

    <!-- Script สำหรับปุ่ม กางออกทั้งหมด -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnExpand = document.getElementById('btnExpandAll');
        if (btnExpand) {
            btnExpand.addEventListener('click', function() {
                const collapses = document.querySelectorAll('#curriculumAccordion .collapse');
                collapses.forEach(el => {
                    const bsCollapse = bootstrap.Collapse.getInstance(el) || new bootstrap.Collapse(el, {toggle: false});
                    bsCollapse.show();
                });
            });
        }
    });
    </script>

    <!-- Include Footer -->
    <?php include_once $ROOT_PATH . 'includes/footer.php'; ?>