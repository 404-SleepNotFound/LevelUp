<?php
// ===================================================
// 0. PATH CONFIGURATION
// ===================================================
$ROOT_PATH = ''; // หน้าหลัก Root Directory

// ===================================================
// 1. PHP INITIALIZATION & DATABASE INTEGRATION
// ===================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_login']);
$sessionData = $_SESSION['user_login'] ?? null;

$navUser = [
    'username' => $sessionData['name'] ?? ($sessionData['username'] ?? 'User'),
    'avatar'   => $sessionData['img'] ?? ($sessionData['avatar'] ?? 'assets/images/default-avatar.png'),
    'level'    => $sessionData['level'] ?? 1,
    'exp'      => $sessionData['user_exp'] ?? ($sessionData['exp'] ?? 0),
    'streak'   => $sessionData['streak'] ?? 5
];

$currentQuest = [
    'title'    => 'กำลังดำเนินการเรียนรู้',
    'progress' => 32
];

// ---------------------------------------------------
// ข้อมูลหมวดหมู่หลัก (Skill Trees)
// ---------------------------------------------------
$category_data = [
    1  => ['id' => 1,  'name' => 'Dev', 'icon' => 'bi-code-slash', 'color' => '#10B981', 'count' => 0],
    2  => ['id' => 2,  'name' => 'การตลาดดิจิทัล', 'icon' => 'bi-megaphone', 'color' => '#F59E0B', 'count' => 0],
    3  => ['id' => 3,  'name' => 'ดีไซน์ & กราฟิก', 'icon' => 'bi-palette', 'color' => '#EC4899', 'count' => 0],
    4  => ['id' => 4,  'name' => 'ธุรกิจ & การจัดการ', 'icon' => 'bi-briefcase', 'color' => '#3B82F6', 'count' => 0],
    5  => ['id' => 5,  'name' => 'ภาษาต่างประเทศ', 'icon' => 'bi-translate', 'color' => '#06B6D4', 'count' => 0],
    6  => ['id' => 6,  'name' => 'ถ่ายภาพ & ตัดต่อวิดีโอ', 'icon' => 'bi-camera-video', 'color' => '#F97316', 'count' => 0],
    7  => ['id' => 7,  'name' => 'วิทยาศาสตร์ข้อมูล & AI', 'icon' => 'bi-cpu', 'color' => '#6366F1', 'count' => 0],
    8  => ['id' => 8,  'name' => 'การเงิน & การลงทุน', 'icon' => 'bi-graph-up-arrow', 'color' => '#10B981', 'count' => 0],
    9  => ['id' => 9,  'name' => 'พัฒนาตนเอง', 'icon' => 'bi-journal-bookmark', 'color' => '#8B5CF6', 'count' => 0],
    10 => ['id' => 10, 'name' => 'ดนตรี & เสียง', 'icon' => 'bi-music-note-beamed', 'color' => '#F43F5E', 'count' => 0],
    11 => ['id' => 11, 'name' => 'เขียนโปรแกรม & พัฒนาเว็บ', 'icon' => 'bi-code-square', 'color' => '#0284C7', 'count' => 0],
    12 => ['id' => 12, 'name' => 'เกม & 3D แอนิเมชัน', 'icon' => 'bi-controller', 'color' => '#A855F7', 'count' => 0]
];

// ---------------------------------------------------
// DATABASE CONNECT
// ---------------------------------------------------
if (file_exists($ROOT_PATH . 'config/db.php')) {
    require_once $ROOT_PATH . 'config/db.php';
} elseif (file_exists($ROOT_PATH . 'db.php')) {
    require_once $ROOT_PATH . 'db.php';
}

$db = $conn ?? $pdo ?? null;
$fallbackImg = 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=600&auto=format&fit=crop';
$courseTable = null;

// ---------------------------------------------------
// 2. ดึงการนับจำนวนคอร์สตามหมวดหมู่แบบ ROBUST
// ---------------------------------------------------
if ($db && $db instanceof PDO) {
    $possibleTables = ['course', 'courses', 'tb_course', 'tb_courses', 'tbl_course', 'tbl_courses'];
    foreach ($possibleTables as $tbl) {
        try {
            $db->query("SELECT 1 FROM `$tbl` LIMIT 1");
            $courseTable = $tbl;
            break;
        } catch (PDOException $e) {
            continue;
        }
    }

    $catColumn = null;
    $possibleCols = ['category_id', 'cat_id', 'category', 'category_name', 'course_category', 'cat_name', 'tag', 'type'];
    if ($courseTable) {
        foreach ($possibleCols as $col) {
            try {
                $db->query("SELECT `$col` FROM `$courseTable` LIMIT 1");
                $catColumn = $col;
                break;
            } catch (PDOException $e) {
                continue;
            }
        }
    }

    if ($courseTable && $catColumn) {
        try {
            $sql = "SELECT `$catColumn` AS cat_key, COUNT(*) AS total_count 
                    FROM `$courseTable` 
                    WHERE `$catColumn` IS NOT NULL AND `$catColumn` != '' 
                    GROUP BY `$catColumn`";
            
            $stmt = $db->query($sql);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $countsMap = [];
            foreach ($rows as $r) {
                $k = trim((string)$r['cat_key']);
                $countsMap[$k] = (int)$r['total_count'];
            }

            foreach ($category_data as $key => &$cat) {
                $cId   = (string)$cat['id'];
                $cName = mb_strtolower(trim($cat['name']));

                if (isset($countsMap[$cId])) {
                    $cat['count'] = $countsMap[$cId];
                } elseif (isset($countsMap[$cat['name']])) {
                    $cat['count'] = $countsMap[$cat['name']];
                } else {
                    $sum = 0;
                    foreach ($countsMap as $dbKey => $cnt) {
                        $dbKeyLower = mb_strtolower($dbKey);
                        if ($dbKey === $cId || $dbKeyLower === $cName || (strlen($dbKeyLower) > 0 && strpos($dbKeyLower, $cName) !== false)) {
                            $sum += $cnt;
                        }
                    }
                    $cat['count'] = $sum;
                }
            }
            unset($cat);
        } catch (PDOException $e) {
            error_log("Error counting courses: " . $e->getMessage());
        }
    }
}

// ---------------------------------------------------
// 3. ดึงข้อมูลคอร์สเรียนยอดนิยมจาก DATABASE
// ---------------------------------------------------
$popular_courses = [];

if ($db && $db instanceof PDO && !empty($courseTable)) {
    try {
        $sql = "SELECT * FROM `$courseTable` ORDER BY `id` DESC LIMIT 4";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $raw_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($raw_courses as $c) {
            $rawImg = $c['cover_file'] ?? $c['cover'] ?? $c['img'] ?? $c['image'] ?? $c['thumbnail'] ?? '';
            $imgPath = $fallbackImg;

            if (!empty($rawImg)) {
                if (filter_var($rawImg, FILTER_VALIDATE_URL)) {
                    $imgPath = $rawImg;
                } elseif (file_exists($ROOT_PATH . $rawImg)) {
                    $imgPath = $ROOT_PATH . $rawImg;
                } elseif (file_exists($ROOT_PATH . 'uploads/covers/' . basename($rawImg))) {
                    $imgPath = $ROOT_PATH . 'uploads/covers/' . basename($rawImg);
                } elseif (file_exists($ROOT_PATH . 'uploads/' . basename($rawImg))) {
                    $imgPath = $ROOT_PATH . 'uploads/' . basename($rawImg);
                }
            }

            $duration = 'N/A';
            if (!empty($c['duration_hours'])) {
                $duration = $c['duration_hours'] . ' ชม.';
            } elseif (!empty($c['time_total'])) {
                $duration = is_numeric($c['time_total']) ? $c['time_total'] . ' ชม.' : $c['time_total'];
            } elseif (!empty($c['duration'])) {
                $duration = is_numeric($c['duration']) ? $c['duration'] . ' ชม.' : $c['duration'];
            }

            $popular_courses[] = [
                'id'            => $c['id'] ?? $c['course_id'] ?? 1,
                'title'         => $c['course_title'] ?? $c['title'] ?? $c['name'] ?? $c['course_name'] ?? 'คอร์สเรียน',
                'level'         => $c['course_difficulty'] ?? $c['difficulty'] ?? $c['level'] ?? $c['course_level'] ?? 'All Levels',
                'duration'      => $duration,
                'image'         => $imgPath,
                'rating'        => $c['rating'] ?? '5.0',
                'reviews'       => $c['reviews'] ?? '120',
                'students'      => $c['students_count'] ?? $c['students'] ?? '1,500',
                'is_bestseller' => $c['is_bestseller'] ?? 1,
                'instructor'    => $c['instructor_name'] ?? $c['instructor'] ?? 'LevelUp Team'
            ];
        }
    } catch (PDOException $e) {
        error_log("Error fetching courses: " . $e->getMessage());
    }
}

if (empty($popular_courses)) {
    $popular_courses = [
        [
            'id' => 1,
            'title' => 'Full-Stack Modern Web Development 2026',
            'level' => 'Beginner - Intermediate',
            'duration' => '35 ชม.',
            'image' => 'https://images.unsplash.com/photo-1587620962725-abab7fe55159?q=80&w=600&auto=format&fit=crop',
            'rating' => '4.9',
            'reviews' => '128',
            'students' => '1420',
            'is_bestseller' => 1,
            'instructor' => 'LevelUp Team'
        ],
        [
            'id' => 2,
            'title' => 'AI & Machine Learning Essentials with Python',
            'level' => 'Intermediate',
            'duration' => '28 ชม.',
            'image' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=600&auto=format&fit=crop',
            'rating' => '5.0',
            'reviews' => '95',
            'students' => '890',
            'is_bestseller' => 1,
            'instructor' => 'LevelUp AI Specialist'
        ],
        [
            'id' => 3,
            'title' => 'Cybersecurity & Ethical Hacking Bootcamp',
            'level' => 'All Levels',
            'duration' => '40 ชม.',
            'image' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?q=80&w=600&auto=format&fit=crop',
            'rating' => '4.8',
            'reviews' => '64',
            'students' => '650',
            'is_bestseller' => 0,
            'instructor' => 'Cyber Security Team'
        ],
        [
            'id' => 4,
            'title' => 'UI/UX Design Masterclass & Figma Pro',
            'level' => 'Beginner',
            'duration' => '20 ชม.',
            'image' => 'https://images.unsplash.com/photo-1581291518857-4e27b48ff24e?q=80&w=600&auto=format&fit=crop',
            'rating' => '4.9',
            'reviews' => '210',
            'students' => '2100',
            'is_bestseller' => 1,
            'instructor' => 'LevelUp Design Lab'
        ]
    ];
}

$career_paths = [
    [
        'slug' => 'frontend',
        'title' => 'Frontend Developer Route',
        'quests' => 12,
        'duration' => 'แนะนำ 6 เดือน',
        'icon' => 'bi-browser-chrome',
        'bg_class' => 'bg-primary'
    ],
    [
        'slug' => 'backend',
        'title' => 'Backend Developer Route',
        'quests' => 10,
        'duration' => 'แนะนำ 5 เดือน',
        'icon' => 'bi-server',
        'bg_class' => 'bg-indigo'
    ]
];

$faq_list = [
    [
        'id' => 'faq1',
        'question' => 'ไม่เคยมีพื้นฐานการเขียนโค้ดเลย จะเรียนได้ไหม?',
        'answer' => 'เรียนได้แน่นอนครับ แพลตฟอร์มของเราถูกออกแบบเส้นทางเริ่มจาก Level 0 โดยมีระบบ AI โค้ดส่วนตัวคอยช่วยเหลือย่อยเนื้อหาที่ยากให้กลายเป็นเรื่องเข้าใจง่าย',
        'show' => true
    ],
    [
        'id' => 'faq2',
        'question' => 'ใบประกาศนียบัตร (Certificate) สามารถใช้สมัครงานได้จริงหรือไม่?',
        'answer' => 'ใบรับรองทักษะของ LevelUp มาพร้อมกับระบบตรวจสอบแบบสแกนข้อมูลผลงานแล็บ (Verifiable Credentials) ร่วมกับพาร์ทเนอร์บริษัทชั้นนำ ทำให้ใช้ยื่นแนบเป็นพอร์ตโฟลิโอสมัครงานได้อย่างเป็นทางการครับ',
        'show' => false
    ]
];

if (file_exists($ROOT_PATH . 'includes/header.php')) {
    include $ROOT_PATH . 'includes/header.php';
} else {
    die("Error: ไม่พบไฟล์ header.php");
}
?>

<link rel="stylesheet" href="<?php echo $ROOT_PATH; ?>css/custom-scrollbar.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    /* ===================================================
       THEME VARIABLES (DARK / LIGHT MODE)
       =================================================== */
    :root, html[data-theme="dark"] {
        --lv-bg-dark: #0b0f19;
        --lv-bg-alt: rgba(15, 23, 42, 0.5);
        --lv-card-bg: rgba(15, 23, 42, 0.75);
        --lv-card-hover: rgba(30, 41, 59, 0.9);
        --lv-border-glow: rgba(99, 102, 241, 0.2);
        --lv-card-border: rgba(255, 255, 255, 0.12);
        --lv-cyan: #06b6d4;
        --lv-indigo: #6366f1;
        --lv-text-main: #f8fafc;
        --lv-text-muted: #94a3b8;
        --lv-hero-bg: radial-gradient(circle at 85% 30%, #1e1b4b 0%, #0b0f19 70%);
        --lv-accordion-arrow: invert(1);
        --lv-box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    /* ☀️ FIX: CSS สำหรับโหมดสว่าง (Light Mode) ครอบคลุมทุก Section */
    html[data-theme="light"] {
        --lv-bg-dark: #f8fafc;
        --lv-bg-alt: #ffffff;
        --lv-card-bg: #ffffff;
        --lv-card-hover: #f1f5f9;
        --lv-border-glow: rgba(99, 102, 241, 0.15);
        --lv-card-border: rgba(0, 0, 0, 0.08);
        --lv-cyan: #0284c7;
        --lv-indigo: #4f46e5;
        --lv-text-main: #0f172a;
        --lv-text-muted: #64748b;
        --lv-hero-bg: radial-gradient(circle at 85% 30%, #e0e7ff 0%, #f8fafc 70%);
        --lv-accordion-arrow: none;
        --lv-box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    /* Global Body & Main Background Fix */
    body {
        background-color: var(--lv-bg-dark) !important;
        color: var(--lv-text-main) !important;
        font-family: 'Inter', 'Prompt', sans-serif;
        overflow-x: hidden;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* FIX: ลบแถบสีดำใต้ Navbar และระหว่าง Section */
    html[data-theme="light"] body,
    html[data-theme="light"] main,
    html[data-theme="light"] section,
    html[data-theme="light"] .bg-theme-main,
    html[data-theme="light"] .bg-theme-alt,
    html[data-theme="light"] .cta-section,
    html[data-theme="light"] .popular-courses-section,
    html[data-theme="light"] .categories-section,
    html[data-theme="light"] .lab-section {
        background-color: var(--lv-bg-dark) !important;
        color: var(--lv-text-main) !important;
    }

    html[data-theme="light"] header,
    html[data-theme="light"] nav,
    html[data-theme="light"] .navbar {
        background-color: #ffffff !important;
        border-bottom: 1px solid rgba(0, 0, 0, 0.06) !important;
    }

    /* Gradient Text Fixes */
    .ai-text-gradient {
        background: linear-gradient(135deg, var(--lv-indigo) 0%, #10B981 100%) !important;
        -webkit-background-clip: text !important;
        -webkit-text-fill-color: transparent !important;
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
    }

    /* Section Subtitle */
    .section-subtitle {
        color: var(--lv-indigo) !important;
        letter-spacing: 1px;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
    }

    /* Adaptive Glass Cards */
    .glass-card {
        background: var(--lv-card-bg) !important;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--lv-card-border) !important;
        border-radius: 20px;
        box-shadow: var(--lv-box-shadow);
        color: var(--lv-text-main) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .glass-card:hover {
        transform: translateY(-4px);
        border-color: rgba(99, 102, 241, 0.4) !important;
        box-shadow: 0 12px 35px rgba(79, 70, 229, 0.15);
    }

    /* Category Cards (Skill Trees) */
    .category-card-compact {
        background: var(--lv-card-bg) !important;
        border: 1px solid var(--lv-card-border) !important;
        border-radius: 16px;
        transition: all 0.25s ease-in-out;
    }

    .category-card-compact:hover {
        transform: translateY(-3px);
        background: var(--lv-card-hover) !important;
        border-color: rgba(99, 102, 241, 0.5) !important;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
    }

    .category-item-title {
        color: var(--lv-text-main) !important;
        font-weight: 600;
    }

    .category-item-count {
        color: var(--lv-text-muted) !important;
        font-size: 0.75rem;
    }

    /* Typography Utilities */
    .text-white-heading {
        color: var(--lv-text-main) !important;
    }

    .text-muted-desc {
        color: var(--lv-text-muted) !important;
    }

    /* Buttons */
    .btn-premium {
        background: linear-gradient(135deg, #4F46E5, #6366F1) !important;
        color: #ffffff !important;
        border: none !important;
        box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        transition: all 0.2s ease;
    }

    .btn-premium:hover {
        transform: scale(1.02);
        box-shadow: 0 0 25px rgba(79, 70, 229, 0.5);
        color: #ffffff !important;
    }

    .btn-theme-adaptive {
        background-color: var(--lv-card-bg) !important;
        color: var(--lv-text-main) !important;
        border: 1px solid var(--lv-card-border) !important;
    }

    .btn-theme-adaptive:hover {
        background-color: var(--lv-card-hover) !important;
        color: var(--lv-text-main) !important;
    }

    /* Progress & Streaks */
    .xp-bar-container {
        height: 10px;
        background-color: rgba(148, 163, 184, 0.2);
        border-radius: 99px;
        overflow: hidden;
    }

    .xp-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #10B981, #34D399);
        transition: width 0.5s ease-in-out;
    }

    .hero-float-anim {
        animation: floatAnimation 6s ease-in-out infinite;
    }

    @keyframes floatAnimation {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-12px); }
        100% { transform: translateY(0px); }
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .fs-xs { font-size: 0.75rem; }
    .fs-sm { font-size: 0.875rem; }

    /* Custom Search Input */
    .custom-search-input {
        color: var(--lv-text-main) !important;
    }

    .custom-search-input::placeholder {
        color: var(--lv-text-muted) !important;
    }

    /* Accordion Fixes */
    .custom-accordion .accordion-button {
        background-color: transparent !important;
        color: var(--lv-text-main) !important;
        box-shadow: none !important;
    }

    .custom-accordion .accordion-button:not(.collapsed) {
        color: var(--lv-indigo) !important;
    }

    .custom-accordion .accordion-button::after {
        filter: var(--lv-accordion-arrow);
    }

    /* Section Backgrounds */
    .bg-theme-main {
        background-color: var(--lv-bg-dark) !important;
    }

    .bg-theme-alt {
        background-color: var(--lv-bg-alt) !important;
    }

    /* Hero User Header Alignment Fix (แก้การพับของข้อความในรูปภาพที่ 2) */
    .user-hero-title {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
</style>

<!-- SECTION 1: HERO & GAMIFIED HERO DASHBOARD -->
<section class="hero-section position-relative overflow-hidden" style="padding: 40px 0 60px 0; background: var(--lv-hero-bg) !important;">
    <div class="position-absolute w-100 h-100 top-0 start-0 opacity-10" style="background-image: linear-gradient(rgba(148, 163, 184, 0.2) 1px, transparent 1px), linear-gradient(90deg, rgba(148, 163, 184, 0.2) 1px, transparent 1px); background-size: 32px 32px; pointer-events: none;"></div>

    <div class="container position-relative z-3">
        <div class="row align-items-center pt-2">
            <div class="col-lg-7 text-center text-lg-start mb-5 mb-lg-0">

                <?php if ($isLoggedIn): ?>
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill mb-4 glass-card">
                        <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px;">
                            <i class="bi bi-fire fs-6"></i>
                        </div>
                        <span class="fw-bold text-white-heading fs-sm"><?= htmlspecialchars($navUser['streak']) ?> Day Streak!</span>
                        <div class="vr mx-2 bg-secondary opacity-50"></div>
                        <span class="text-muted-desc fw-medium fs-sm"><i class="bi bi-star-fill text-warning me-1"></i>Lvl <?= htmlspecialchars($navUser['level']) ?></span>
                    </div>

                    <!-- FIX: ปรับ Layout ให้รูปโปรไฟล์ ข้อความต้อนรับ และจรวดเรียงสวยงาม ไม่หลุดบรรทัด -->
                    <div class="user-hero-title mb-3 justify-content-center justify-content-lg-start">
                        <img src="<?= htmlspecialchars($navUser['avatar']) ?>" alt="Avatar" class="rounded-circle border border-2 border-primary shadow-sm" style="width: 54px; height: 54px; object-fit: cover;">
                        <h1 class="display-5 fw-bold text-white-heading mb-0 lh-sm" style="font-family: 'Outfit', sans-serif;">
                            ยินดีต้อนรับกลับมา, <span class="ai-text-gradient"><?= htmlspecialchars($navUser['username']) ?>!</span> 🚀
                        </h1>
                    </div>

                    <div class="glass-card p-4 mb-4 text-start">
                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <h6 class="fw-bold text-white-heading mb-0 fs-sm"><i class="bi bi-journal-code text-info me-2"></i>เควสต์ปัจจุบัน: <?= htmlspecialchars($currentQuest['title']) ?></h6>
                            <span class="fw-bold text-success fs-sm"><?= htmlspecialchars($currentQuest['progress']) ?>%</span>
                        </div>
                        <div class="xp-bar-container mb-4">
                            <div class="xp-bar-fill" style="width: <?= htmlspecialchars($currentQuest['progress']) ?>%;"></div>
                        </div>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="<?= $ROOT_PATH; ?>student/lesson.php" class="btn btn-premium ajax-link px-4 py-2 rounded-pill fs-sm fw-bold"><i class="bi bi-play-fill me-1"></i> ลุยภารกิจต่อ</a>
                            <a href="<?= $ROOT_PATH; ?>student/dashboard.php" class="btn btn-theme-adaptive ajax-link px-4 py-2 rounded-pill fw-medium fs-sm"><i class="bi bi-grid-1x2-fill me-2"></i>ไปยังแดชบอร์ด</a>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="d-inline-block px-3 py-1 rounded-pill mb-3 glass-card">
                        <span class="ai-text-gradient fw-bold fs-xs"><i class="bi bi-stars me-1"></i> LevelUp Engine v2.5</span>
                    </div>
                    <h1 class="display-4 fw-bold text-white-heading mb-4 lh-sm" style="font-family: 'Outfit', sans-serif;">
                        เรียนรู้ทักษะสาย Tech<br>ด้วย <span class="ai-text-gradient">AI Companion</span>
                    </h1>
                    <p class="lead text-muted-desc mb-4 pe-lg-5 fs-sm" style="line-height: 1.7;">
                        ระบบอีเลิร์นนิงรูปแบบ Gamification ที่มาพร้อมกับเอไอโค้ชส่วนตัว ช่วยคุณวางแผนการเรียน แก้บั๊กโค้ดแบบ Real-time และจำลองแล็บเสมือนจริงเพื่อพัฒนาทักษะการทำงาน
                    </p>
                    <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start mb-4">
                        <a href="<?= $ROOT_PATH; ?>auth/login.php" class="btn btn-premium ajax-link btn-lg px-4 rounded-pill fs-sm fw-bold">เข้าสู่ระบบใช้งานฟรี</a>
                    </div>
                <?php endif; ?>

                <div class="search-box-wrapper glass-card p-2 mx-auto mx-lg-0" style="max-width: 550px;">
                    <form action="<?= $ROOT_PATH; ?>student/courses.php" method="GET" id="ajax-search-form" class="d-flex">
                        <div class="input-group align-items-center">
                            <span class="input-group-text bg-transparent border-0 ps-3 text-muted-desc"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control border-0 shadow-none py-2 bg-transparent fs-sm custom-search-input" name="q" placeholder="ค้นหาบทเรียน, คอร์ส หรือสกิลเทคโนโลยี..." autocomplete="off">
                            <button type="submit" class="btn btn-premium rounded-pill px-4 fw-bold fs-sm">ค้นหา</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-5 d-none d-lg-block">
                <div class="hero-image-wrapper position-relative text-center pe-4">
                    <img src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=600&auto=format&fit=crop" alt="AI Workspace" class="img-fluid rounded-4 shadow-lg hero-float-anim" style="border: 1px solid var(--lv-card-border); max-height: 400px; object-fit: cover; width: 100%;">

                    <div class="position-absolute glass-card p-3 d-flex align-items-center gap-3 shadow-lg" style="bottom: -20px; left: -10px; z-index: 10; min-width: 240px;">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fs-5 flex-shrink-0" style="width: 42px; height: 42px; box-shadow: 0 0 15px rgba(16,185,129,0.5);">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        <div class="text-start">
                            <h6 class="mb-0 fw-bold text-white-heading fs-sm">Quest Completed!</h6>
                            <small class="text-success fw-semibold fs-xs">+1,200 XP Earned Today</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 2: SKILL TREES -->
<section class="categories-section py-5 bg-theme-main">
    <div class="container py-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h6 class="section-subtitle mb-1">Skill Trees</h6>
                <h2 class="fw-bold text-white-heading mb-0 fs-3" style="font-family: 'Outfit', sans-serif;">เลือกคอร์สตามเป้าหมายสกิล</h2>
            </div>
            <a href="<?= $ROOT_PATH; ?>student/courses.php" class="btn btn-theme-adaptive ajax-link btn-sm rounded-pill px-3 fs-xs fw-bold">
                ดูหมวดหมู่ทั้งหมด <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 justify-content-center" id="category-grid-container">
            <?php foreach ($category_data as $cat): ?>
                <div class="col category-col-item">
                    <a href="<?= $ROOT_PATH; ?>student/courses.php?category=<?= urlencode($cat['id']) ?>" class="card category-card-compact ajax-link text-decoration-none p-3 h-100 d-flex flex-row align-items-center gap-3">
                        <div class="icon-box-sm rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px; background: rgba(99, 102, 241, 0.1); color: <?= htmlspecialchars($cat['color'] ?? '#818CF8') ?>; border: 1px solid var(--lv-card-border);">
                            <i class="bi <?= htmlspecialchars($cat['icon'] ?? 'bi-journal') ?> fs-5"></i>
                        </div>
                        <div class="overflow-hidden">
                            <h6 class="category-item-title mb-0 fs-sm text-truncate"><?= htmlspecialchars($cat['name']) ?></h6>
                            <small class="category-item-count"><?= (int)($cat['count'] ?? 0) ?> คอร์ส</small>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- SECTION 3: TRENDING QUESTS -->
<section class="popular-courses-section py-5 bg-theme-alt">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h6 class="section-subtitle mb-1">Trending Quests</h6>
                <h2 class="fw-bold text-white-heading mb-0 fs-3" style="font-family: 'Outfit', sans-serif;">คอร์สเรียนยอดนิยมสัปดาห์นี้</h2>
            </div>
            <a href="<?= $ROOT_PATH; ?>student/courses.php" class="btn btn-theme-adaptive ajax-link btn-sm rounded-pill px-3 fs-xs">ดูทั้งหมด</a>
        </div>

        <div class="row g-4">
            <?php if (!empty($popular_courses)): ?>
                <?php foreach ($popular_courses as $course): ?>
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card h-100 glass-card text-start overflow-hidden">
                            <div class="position-relative overflow-hidden rounded-top" style="height: 160px; background-color: rgba(0, 0, 0, 0.05);">
                                <img src="<?= htmlspecialchars($course['image']) ?>" 
                                     class="card-img-top w-100 h-100 d-block" 
                                     alt="<?= htmlspecialchars($course['title']) ?>" 
                                     style="object-fit: cover;"
                                     onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=600&auto=format&fit=crop';">
                                
                                <?php if (!empty($course['is_bestseller'])): ?>
                                    <span class="position-absolute top-0 start-0 m-2 badge bg-danger text-white fs-xs px-2 py-1 rounded fw-bold shadow-sm" style="z-index: 2;">BEST SELLER</span>
                                <?php endif; ?>
                                
                                <span class="position-absolute bottom-0 end-0 m-2 badge bg-dark text-light border border-secondary border-opacity-50 fs-xs shadow-sm" style="z-index: 2; backdrop-filter: blur(4px);">
                                    <?= htmlspecialchars($course['level'] ?? 'All Levels') ?>
                                </span>
                            </div>

                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="fw-bold text-white-heading mb-2 line-clamp-2 fs-sm" style="min-height: 40px; line-height: 1.4; font-family: 'Outfit', sans-serif;">
                                        <?= htmlspecialchars($course['title']) ?>
                                    </h6>
                                    <p class="text-muted-desc fs-xs mb-3">ผู้สอน: <?= htmlspecialchars($course['instructor'] ?? 'LevelUp Team') ?></p>

                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <span class="text-warning fw-bold fs-xs">
                                            <i class="bi bi-star-fill me-1"></i><?= number_format((float)($course['rating'] ?? 5.0), 1) ?>
                                        </span>
                                        <span class="text-muted-desc fs-xs">(<?= number_format((int)($course['reviews'] ?? 0)) ?>)</span>
                                        <span class="text-secondary opacity-50">|</span>
                                        <span class="text-muted-desc fs-xs">
                                            <i class="bi bi-people me-1"></i><?= number_format((int)($course['students'] ?? 0)) ?> คน
                                        </span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center border-top border-secondary border-opacity-25 pt-2 mt-2">
                                    <div>
                                        <span class="text-muted-desc fs-xs d-block">ระยะเวลา</span>
                                        <span class="fw-bold text-info fs-sm">
                                            <?= htmlspecialchars($course['duration'] ?? 'N/A') ?>
                                        </span>
                                    </div>
                                    <a href="<?= $isLoggedIn ? $ROOT_PATH . 'student/course-detail.php?id=' . $course['id'] : $ROOT_PATH . 'auth/login.php' ?>" class="btn btn-premium ajax-link btn-sm rounded-pill px-3 fs-xs fw-bold">ดูรายละเอียด</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted-desc">ยังไม่มีข้อมูลคอร์สเรียนยอดนิยมในขณะนี้</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- SECTION 4: CAREER MAPS & AI FEATURES -->
<section class="py-5 bg-theme-main">
    <div class="container py-3">
        <div class="row align-items-center gy-5">
            <div class="col-lg-5">
                <h6 class="section-subtitle mb-1">Career Maps</h6>
                <h2 class="fw-bold text-white-heading mb-4 fs-3" style="font-family: 'Outfit', sans-serif;">ก้าวตามโรดMAPสู่สายงานอาชีพ</h2>
                <p class="text-muted-desc fs-sm mb-4">เราคัดกรองเนื้อหาเป็นลำดับขั้นเพื่อปูพื้นฐานจากศูนย์จนสามารถพัฒนาโปรเจกต์ส่งสมัครงานจริงได้อย่างเป็นระบบ</p>

                <div class="d-flex flex-column gap-3 mb-4">
                    <?php foreach ($career_paths as $path): ?>
                        <a href="<?= $isLoggedIn ? $ROOT_PATH . 'student/learning-path.php?path=' . urlencode($path['slug']) : $ROOT_PATH . 'auth/login.php' ?>" class="glass-card ajax-link p-3 text-decoration-none d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="<?= htmlspecialchars($path['bg_class']) ?> text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi <?= htmlspecialchars($path['icon']) ?> fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-white-heading mb-0 fs-sm"><?= htmlspecialchars($path['title']) ?></h6>
                                    <small class="text-muted-desc fs-xs"><?= htmlspecialchars($path['quests']) ?> เควสต์ย่อย • <?= htmlspecialchars($path['duration']) ?></small>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right text-muted-desc"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
                <a href="<?= $isLoggedIn ? $ROOT_PATH . 'student/learning-path.php' : $ROOT_PATH . 'auth/login.php' ?>" class="btn btn-theme-adaptive ajax-link rounded-pill fw-bold px-4 fs-sm">ดูแผนผังทั้งหมด</a>
            </div>

            <div class="col-lg-6 offset-lg-1">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="glass-card text-white p-4" style="background: linear-gradient(135deg, #4F46E5, #3730A3) !important; border: none !important;">
                            <i class="bi bi-robot fs-1 mb-2 text-warning"></i>
                            <h4 class="fw-bold text-white" style="font-family: 'Outfit', sans-serif;">24/7 AI Companion</h4>
                            <p class="mb-0 fs-sm text-white opacity-85">ระบบที่คอยเป็นผู้ช่วยให้คำปรึกษาและตรวจโค้ดเสมือนมีรุ่นพี่โปรแกรมเมอร์คอยนั่งประกบอยู่เคียงข้างคุณตลอดการทำภารกิจ</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="glass-card p-4 h-100">
                            <i class="bi bi-bullseye text-primary fs-2 mb-3"></i>
                            <h5 class="fw-bold text-white-heading fs-sm" style="font-family: 'Outfit', sans-serif;">Skill Radar Graph</h5>
                            <p class="text-muted-desc fs-xs mb-0">ระบบประเมินค่าพลังวิเคราะห์ทักษะความรู้แต่ละด้าน เพื่อบอกจุดเด่นจุดด้อยที่คุณควรปรับปรุง</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="glass-card p-4 h-100">
                            <i class="bi bi-magic text-success fs-2 mb-3"></i>
                            <h5 class="fw-bold text-white-heading fs-sm" style="font-family: 'Outfit', sans-serif;">Dynamic Sandbox Labs</h5>
                            <p class="text-muted-desc fs-xs mb-0">โจทย์เขียนโค้ดปรับระดับความยากง่ายอัตโนมัติอิงตามทักษะและผลงานก่อนหน้าของคุณ</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 5: VIRTUAL CYBER LAB PREVIEW -->
<section class="lab-section py-5 text-center position-relative overflow-hidden bg-theme-alt">
    <div class="container position-relative z-3 py-3">
        <span class="badge rounded-pill bg-primary bg-opacity-25 text-primary border border-primary mb-3 px-3 py-2 fs-xs fw-bold">Cloud Terminal Environment</span>
        <h2 class="fw-bold mb-3 text-white-heading display-6" style="font-family: 'Outfit', sans-serif;">ลงมือปฏิบัติจริงบน Cyber Arena</h2>
        <p class="lead text-muted-desc mx-auto mb-5 fs-sm" style="max-width: 650px;">
            ไม่จำเป็นต้องติดตั้งโปรแกรมหรือตั้งค่าสภาพแวดล้อมให้ปวดหัว รันคำสั่งคอมมานด์ไลน์และโค้ดโปรแกรมผ่านเว็บบราวเซอร์ของคุณได้ทันที
        </p>

        <div class="browser-mockup glass-card shadow-lg text-start mx-auto border border-secondary border-opacity-50" style="max-width: 820px; background: #04060d !important;">
            <div class="browser-header p-3 border-bottom border-secondary border-opacity-25 d-flex align-items-center gap-2">
                <span class="dot bg-danger rounded-circle" style="width:10px; height:10px; display:inline-block;"></span>
                <span class="dot bg-warning rounded-circle" style="width:10px; height:10px; display:inline-block;"></span>
                <span class="dot bg-success rounded-circle" style="width:10px; height:10px; display:inline-block;"></span>
                <div class="bg-dark px-3 py-1 rounded fs-xs text-light opacity-50 ms-3 font-monospace">terminal - user@levelup-sandbox:~</div>
            </div>
            <div class="browser-body p-4 font-monospace fs-xs text-success" style="min-height: 200px; line-height: 1.6;">
                <span class="text-white">user@levelup-sandbox:~$</span> npm run dev<br>
                <span class="text-light opacity-50">&gt; next-gen-app@2.6.0 dev</span><br>
                <span class="text-light opacity-50">&gt; vite</span><br><br>
                <span class="text-info"> ➜ Local: http://localhost:5173/</span><br>
                <span style="color: #F59E0B;">[AI Analyzer]:</span> สแกนพบบั๊กคอมไพล์ในเส้นทาง Component บรรทัดที่ 42 แนะนำให้ใส่เครื่องหมายปิดปีกกาให้ถูกต้องครับ<br>
                <span class="text-white">user@levelup-sandbox:~$</span> <span class="text-white opacity-75 animate-blink">|</span>
            </div>
        </div>

        <div class="mt-5">
            <a href="<?= $isLoggedIn ? $ROOT_PATH . 'student/virtual-lab.php' : $ROOT_PATH . 'auth/login.php' ?>" class="btn btn-premium ajax-link px-5 py-3 rounded-pill fw-bold fs-sm">เปิดใช้งาน Sandbox Area <i class="bi bi-controller ms-2"></i></a>
        </div>
    </div>
</section>

<!-- SECTION 6: PLATFORM METRICS & FAQ -->
<section class="py-5 bg-theme-main">
    <div class="container py-3">
        <div class="row align-items-center justify-content-center gy-4 mb-5 pb-5 border-bottom border-secondary border-opacity-25">
            <div class="col-12 col-xl-4 col-lg-5 mb-3 mb-lg-0">
                <div class="glass-card p-3 p-md-4 d-flex align-items-center justify-content-between gap-3 h-100">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-stars text-primary fs-6"></i>
                            <span class="fw-bold fs-xs text-primary text-uppercase" style="letter-spacing: 0.5px;">Tip of the Day</span>
                        </div>
                        <p class="mb-0 fs-xs text-muted-desc" style="line-height: 1.5;">
                            ระบบเรียนรู้ที่มี AI ช่วยโค้ช จะช่วยเพิ่มความเร็วในการทำเควสต์เขียนโค้ดได้ถึง 2 เท่า!
                        </p>
                    </div>
                    <div class="flex-shrink-0 text-warning fs-2 opacity-75">
                        <i class="bi bi-lightbulb-fill"></i>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-8 col-lg-7">
                <div class="row text-center gy-3 justify-content-center align-items-center">
                    <div class="col-6 col-sm-3">
                        <h2 class="display-6 fw-bold mb-0 ai-text-gradient">250K+</h2>
                        <p class="text-muted-desc fw-bold text-uppercase fs-xs mt-1 mb-0">LEARNERS</p>
                    </div>
                    <div class="col-6 col-sm-3">
                        <h2 class="display-6 fw-bold mb-0 ai-text-gradient">1,200+</h2>
                        <p class="text-muted-desc fw-bold text-uppercase fs-xs mt-1 mb-0">QUESTS</p>
                    </div>
                    <div class="col-6 col-sm-3">
                        <h2 class="display-6 fw-bold mb-0 ai-text-gradient">850+</h2>
                        <p class="text-muted-desc fw-bold text-uppercase fs-xs mt-1 mb-0">CODING LABS</p>
                    </div>
                    <div class="col-6 col-sm-3">
                        <h2 class="display-6 fw-bold mb-0 ai-text-gradient" style="color: var(--lv-indigo) !important;">12M+</h2>
                        <p class="text-muted-desc fw-bold text-uppercase fs-xs mt-1 mb-0">AI ANSWERS</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mb-5">
            <h2 class="fw-bold text-white-heading" style="font-family: 'Outfit', sans-serif;">คำถามที่พบบ่อย (FAQ)</h2>
            <p class="text-muted-desc fs-sm">รวบรวมคำตอบเบื้องต้นสำหรับผู้เล่นใหม่ทุกท่าน</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion custom-accordion" id="faqAccordion">
                    <?php foreach ($faq_list as $faq): ?>
                        <div class="accordion-item glass-card mb-3 border-0">
                            <h2 class="accordion-header">
                                <button class="accordion-button <?= $faq['show'] ? '' : 'collapsed' ?> fw-bold bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#<?= htmlspecialchars($faq['id']) ?>">
                                    <?= htmlspecialchars($faq['question']) ?>
                                </button>
                            </h2>
                            <div id="<?= htmlspecialchars($faq['id']) ?>" class="accordion-collapse collapse <?= $faq['show'] ? 'show' : '' ?>" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted-desc pt-0 fs-sm">
                                    <?= htmlspecialchars($faq['answer']) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 7: FINAL CALL TO ACTION (CTA) -->
<section class="cta-section py-4 mb-5 bg-theme-main">
    <div class="container">
        <div class="glass-card p-4 p-md-5 text-center position-relative overflow-hidden" style="background: linear-gradient(135deg, #4F46E5, #3730A3) !important; border: none !important;">
            <div class="position-relative z-3 text-white">
                <h2 class="display-6 fw-bold mb-3 text-white" style="font-family: 'Outfit', sans-serif;">พร้อมที่จะอัปเกรดสถานะเลเวลของคุณหรือยัง?</h2>
                <p class="lead mb-4 mx-auto fs-sm text-white opacity-85" style="max-width: 600px;">เข้าร่วมสมรภูมิพัฒนาทักษะแบบไร้ขีดจำกัด เก็บสะสมแต้มผลงานเพื่อเพิ่มโอกาสสู่สายอาชีพที่มั่นคง</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <?php if (!$isLoggedIn): ?>
                        <a href="<?= $ROOT_PATH; ?>auth/login.php" class="btn btn-light ajax-link btn-lg px-4 px-md-5 fw-bold rounded-pill fs-sm text-dark bg-white">เข้าสู่ระบบผู้เล่น</a>
                        <a href="<?= $ROOT_PATH; ?>auth/login.php" class="btn btn-outline-light ajax-link btn-lg px-4 rounded-pill fs-sm"><i class="bi bi-robot me-2"></i>คุยกับ AI โค้ช</a>
                    <?php else: ?>
                        <a href="<?= $ROOT_PATH; ?>student/courses.php" class="btn btn-light ajax-link btn-lg px-4 px-md-5 fw-bold rounded-pill fs-sm text-dark bg-white"><i class="bi bi-rocket-takeoff-fill me-2"></i>เริ่มลุยเควสต์เรียนรู้</a>
                        <a href="<?= $ROOT_PATH; ?>ai/ai-chat.php" class="btn btn-outline-light ajax-link btn-lg px-4 rounded-pill fs-sm"><i class="bi bi-robot me-2"></i>คุยกับ AI โค้ช</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
if (file_exists($ROOT_PATH . 'includes/footer.php')) {
    include $ROOT_PATH . 'includes/footer.php';
}
?>

<script>
    $(document).ready(function() {
        // กรองการ์ดหมวดหมู่ซ้ำ
        var seenNames = {};
        $('#category-grid-container .category-col-item').each(function() {
            var catTitle = $(this).find('.category-item-title').text().trim();
            if (catTitle) {
                if (seenNames[catTitle]) {
                    $(this).remove();
                } else {
                    seenNames[catTitle] = true;
                }
            }
        });

        // ดักจับปุ่ม Logout เพื่อ Redirect ไปยัง auth/logout.php
        $(document).on('click', 'a', function(e) {
            var href = $(this).attr('href');
            var text = $(this).text().toLowerCase();

            if (href && (href.indexOf('logout.php') !== -1 || text.indexOf('logout') !== -1 || text.indexOf('ออกจากระบบ') !== -1)) {
                e.stopPropagation();
                window.location.href = '<?= $ROOT_PATH; ?>auth/logout.php';
            }
        });
    });
</script>