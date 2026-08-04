<?php
$ROOT_PATH = '';
/**
 * LevelUp - Next-Gen E-Learning Platform
 * @page index.php (Home Dashboard & Marketing Showcase - Light/Dark Mode Supported)
 * @style Modern Premium Gamified AI Theme
 */

// ===================================================
// 1. PHP INITIALIZATION & DATABASE PLACEHOLDER VARIABLES
// ===================================================

// เริ่มต้น Session สำหรับระบบสมาชิกอย่างปลอดภัย
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบสถานะการเข้าสู่ระบบ
$isLoggedIn = isset($_SESSION['user_login']);
$sessionData = $_SESSION['user_login'] ?? null;

// ข้อมูลสมาชิก (ปรับคีย์ให้ตรงกับ callback.php)
$navUser = [
    'username' => $sessionData['name'] ?? ($sessionData['username'] ?? 'User'),
    'avatar'   => $sessionData['img'] ?? ($sessionData['avatar'] ?? 'assets/images/default-avatar.png'),
    'level'    => $sessionData['level'] ?? 1,
    'exp'      => $sessionData['user_exp'] ?? ($sessionData['exp'] ?? 0),
    'streak'   => $sessionData['streak'] ?? 5
];

// ข้อมูลเควสต์และการเรียนปัจจุบันของผู้ใช้
$currentQuest = [
    'title'    => 'กำลังดำเนินการเรียนรู้',
    'progress' => 32
];

// ข้อมูลหมวดหมู่ / Skill Trees
$category_data = [
    ['name' => 'Web Development', 'icon' => 'bi-code-slash', 'color' => '#10B981', 'count' => 24],
    ['name' => 'AI & Machine Learning', 'icon' => 'bi-robot', 'color' => '#6366F1', 'count' => 18],
    ['name' => 'Cybersecurity', 'icon' => 'bi-shield-check', 'color' => '#EF4444', 'count' => 12],
    ['name' => 'UI/UX Design', 'icon' => 'bi-palette', 'color' => '#F59E0B', 'count' => 15],
    ['name' => 'Data Science', 'icon' => 'bi-bar-chart-line', 'color' => '#8B5CF6', 'count' => 20]
];

// ข้อมูลคอร์สเรียนยอดนิยม
$popular_courses = [
    [
        'id' => 1,
        'title' => 'Python for AI & Machine Learning Masterclass',
        'instructor' => 'Dr. Sarah Chen',
        'level' => 'Intermediate',
        'duration' => '24 ชม.',
        'rating' => '4.9',
        'reviews' => '4,200',
        'students' => '15,400',
        'price' => '฿1,290',
        'is_bestseller' => true,
        'image' => 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?q=80&w=400&auto=format&fit=crop'
    ],
    [
        'id' => 2,
        'title' => 'Complete Web Development Bootcamp 2026',
        'instructor' => 'John Doe',
        'level' => 'Beginner',
        'duration' => '48 ชม.',
        'rating' => '4.8',
        'reviews' => '8,900',
        'students' => '45,000',
        'price' => '฿1,590',
        'is_bestseller' => true,
        'image' => 'https://images.unsplash.com/photo-1547658719-da2b51169166?q=80&w=400&auto=format&fit=crop'
    ],
    [
        'id' => 3,
        'title' => 'Cybersecurity: Penetration Testing Lab',
        'instructor' => 'Alex Hacker',
        'level' => 'Advanced',
        'duration' => '18 ชม.',
        'rating' => '4.7',
        'reviews' => '1,200',
        'students' => '8,500',
        'price' => '฿1,990',
        'is_bestseller' => false,
        'image' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?q=80&w=400&auto=format&fit=crop'
    ],
    [
        'id' => 4,
        'title' => 'UI/UX Design Process & Figma Mastery',
        'instructor' => 'Jane Smith',
        'level' => 'All Levels',
        'duration' => '12 ชม.',
        'rating' => '4.9',
        'reviews' => '3,500',
        'students' => '12,000',
        'price' => '฿990',
        'is_bestseller' => true,
        'image' => 'https://images.unsplash.com/photo-1618788372246-79faff0c3742?q=80&w=400&auto=format&fit=crop'
    ]
];

// ข้อมูลเส้นทางอาชีพ
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
        'bg_class' => 'btn-theme-dark-adaptive'
    ]
];

// ข้อมูลสถิติแพลตฟอร์ม
$platform_metrics = [
    ['value' => '120+', 'label' => 'แผนการเรียนรู้'],
    ['value' => '15,000+', 'label' => 'โจทย์ความท้าทาย'],
    ['value' => '500,000+', 'label' => 'ผู้เล่นในระบบ'],
    ['value' => '94%', 'label' => 'ได้งานทำหลังเรียนจบ']
];

// ข้อมูลคำถามที่พบบ่อย
$faq_list = [
    [
        'id' => 'faq1',
        'question' => 'ไม่เคยมีพื้นฐานการเขียนโค้ดเลย จะเรียนได้ไหม?',
        'answer' => 'เรียนได้แน่นอนครับ แพลตฟอร์มของเราถูกออกแบบเส้นทางเริ่มจาก Level 0 โดยมีระบบ AI โค้ดส่วนตัวคอยช่วยเหลือย่อยเนื้อหายยากๆ ให้กลายเป็นเรื่องเข้าใจง่าย',
        'show' => true
    ],
    [
        'id' => 'faq2',
        'question' => 'ใบประกาศนียบัตร (Certificate) สามารถใช้สมัครงานได้จริงหรือไม่?',
        'answer' => 'ใบรับรองทักษะของ LevelUp มาพร้อมกับระบบตรวจสอบแบบสแกนข้อมูลผลงานแล็บ (Verifiable Credentials) ร่วมกับพาร์ทเนอร์บริษัทชั้นนำ ทำให้ใช้ยื่นแนบเป็นพอร์ตโฟลิโอสมัครงานได้อย่างเป็นทางการครับ',
        'show' => false
    ]
];

// ดึงไฟล์ Header ของระบบเข้ามาทำงาน
if (file_exists('includes/header.php')) {
    include 'includes/header.php';
} else {
    die("Error: ไม่พบไฟล์ 'includes/header.php' กรุณาตรวจสอบโครงสร้างโฟลเดอร์");
}
?>

<!-- Include jQuery จาก CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- ===================================================
     GLOBAL STYLES (Cyberpunk UI / Dual Theme Support)
     =================================================== -->
<style>
    :root,
    html[data-theme="dark"] {
        --neon-indigo: #4F46E5;
        --neon-purple: #9333EA;
        --cyber-green: #10B981;
        --plasma-orange: #F59E0B;
        --bg-deep-space: #0b0f19;
        --surface-card: rgba(15, 23, 42, 0.65);
        --border-color: rgba(30, 41, 59, 0.8);
        --text-main: #ffffff;
        --text-muted: rgba(255, 255, 255, 0.75);
        --hero-bg: radial-gradient(circle at 85% 30%, #1e1b4b 0%, #0b0f19 70%);
        --section-bg-alt: rgba(15, 23, 42, 0.3);
        --lab-bg: rgba(15, 23, 42, 0.4);
        --terminal-bg: #04060d;
        --input-placeholder: rgba(255, 255, 255, 0.65);
        --btn-dark-bg: #1e293b;
        --btn-dark-text: #ffffff;
        --btn-dark-border: rgba(255, 255, 255, 0.15);
    }

    html[data-theme="light"] {
        --neon-indigo: #4F46E5;
        --neon-purple: #8B5CF6;
        --cyber-green: #059669;
        --plasma-orange: #D97706;
        --bg-deep-space: #f8fafc;
        --surface-card: rgba(255, 255, 255, 0.9);
        --border-color: rgba(226, 232, 240, 0.9);
        --text-main: #0f172a;
        --text-muted: #64748b;
        --hero-bg: radial-gradient(circle at 85% 30%, #e0e7ff 0%, #f8fafc 70%);
        --section-bg-alt: #f1f5f9;
        --lab-bg: #f1f5f9;
        --terminal-bg: #0f172a;
        --input-placeholder: #64748b;
        --btn-dark-bg: #e2e8f0;
        --btn-dark-text: #0f172a;
        --btn-dark-border: #cbd5e1;
    }

    body {
        background-color: var(--bg-deep-space) !important;
        color: var(--text-main);
        font-family: 'Inter', 'Prompt', sans-serif;
        overflow-x: hidden;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .opacity-10 { opacity: 0.1 !important; }
    .opacity-05 { opacity: 0.05 !important; }

    .glass-card {
        background: var(--surface-card);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .glass-card:hover {
        transform: translateY(-5px);
        border-color: rgba(99, 102, 241, 0.4);
        box-shadow: 0 15px 35px rgba(79, 70, 229, 0.18);
    }

    .category-card-compact {
        border: 1px solid var(--border-color);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        background: var(--surface-card);
    }

    .category-card-compact:hover {
        transform: translateY(-3px);
        border-color: rgba(99, 102, 241, 0.5);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .category-card-compact:hover .icon-box-sm {
        transform: scale(1.08);
        transition: transform 0.2s ease;
    }

    .ai-text-gradient {
        background: linear-gradient(135deg, #6366F1, #10B981);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
    }

    .btn-premium {
        background: linear-gradient(135deg, var(--neon-indigo), #6366F1);
        color: white !important;
        border: none;
        box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        transition: all 0.2s ease;
    }

    .btn-premium:hover {
        transform: scale(1.02);
        box-shadow: 0 0 25px rgba(79, 70, 229, 0.5);
    }

    .xp-bar-container {
        height: 10px;
        background-color: var(--border-color);
        border-radius: 99px;
        overflow: hidden;
    }

    .xp-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--cyber-green), #34D399);
        transition: width 0.5s ease-in-out;
    }

    .hero-float-anim {
        animation: floatAnimation 6s ease-in-out infinite;
    }

    @keyframes floatAnimation {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
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

    .custom-accordion .accordion-button:not(.collapsed) {
        color: #6366F1 !important;
        background-color: transparent;
        box-shadow: none;
    }

    .custom-accordion .accordion-button::after {
        transition: transform 0.2s ease;
    }

    html[data-theme="dark"] .custom-accordion .accordion-button::after {
        filter: invert(1);
    }

    .animate-blink {
        animation: blink-caret 1s step-end infinite;
    }

    @keyframes blink-caret {
        from, to { color: transparent; }
        50% { color: #10B981; }
    }

    .avatar-glow {
        border: 2px solid var(--cyber-green);
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.4);
    }

    .custom-search-input {
        color: var(--text-main) !important;
    }

    .custom-search-input::placeholder {
        color: var(--input-placeholder) !important;
        opacity: 1 !important;
    }

    .btn-theme-dark-adaptive {
        background-color: var(--btn-dark-bg) !important;
        color: var(--btn-dark-text) !important;
        border: 1px solid var(--btn-dark-border) !important;
    }

    html[data-theme="light"] .text-white { color: var(--text-main) !important; }
    html[data-theme="light"] .text-light { color: var(--text-muted) !important; }
    html[data-theme="light"] .icon-box { background-color: rgba(0, 0, 0, 0.04) !important; }
</style>

<!-- ===================================================
     2. MAIN CONTENT AREA (HTML PRESENTATION)
     =================================================== -->

<!-- SECTION 1: HERO & GAMIFIED HERO DASHBOARD -->
<section class="hero-section position-relative overflow-hidden" style="padding: 120px 0 80px 0; background: var(--hero-bg);">
    <div class="position-absolute w-100 h-100 top-0 start-0 opacity-10" style="background-image: linear-gradient(var(--border-color) 1px, transparent 1px), linear-gradient(90deg, var(--border-color) 1px, transparent 1px); background-size: 32px 32px; pointer-events: none;"></div>

    <div class="container position-relative z-3">
        <div class="row align-items-center pt-4">
            <div class="col-lg-7 text-center text-lg-start mb-5 mb-lg-0">

                <?php if ($isLoggedIn): ?>
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill mb-4" style="background-color: var(--surface-card); border: 1px solid var(--border-color);">
                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                            <i class="bi bi-fire fs-6"></i>
                        </div>
                        <span class="fw-bold text-white fs-sm"><?= htmlspecialchars($navUser['streak']) ?> Day Streak!</span>
                        <div class="vr mx-2 bg-secondary opacity-50"></div>
                        <span class="text-light opacity-75 fw-medium fs-sm"><i class="bi bi-star-fill text-warning me-1"></i>Lvl <?= htmlspecialchars($navUser['level']) ?></span>
                    </div>

                    <h1 class="display-4 fw-bold text-white mb-3 lh-sm" style="font-family: 'Outfit', sans-serif;">
                        <img src="<?= htmlspecialchars($navUser['avatar']) ?>" alt="Avatar" class="rounded-circle avatar-glow me-2 align-middle" style="width: 50px; height: 50px; object-fit: cover;">
                        ยินดีต้อนรับกลับมา, <span class="ai-text-gradient"><?= htmlspecialchars($navUser['username']) ?>!</span> 🚀
                    </h1>

                    <div class="glass-card p-4 mb-4 text-start">
                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <h6 class="fw-bold text-light opacity-75 mb-0 fs-sm"><i class="bi bi-journal-code text-info me-2"></i>เควสต์ปัจจุบัน: <?= htmlspecialchars($currentQuest['title']) ?></h6>
                            <span class="fw-bold text-success fs-sm"><?= htmlspecialchars($currentQuest['progress']) ?>%</span>
                        </div>
                        <div class="xp-bar-container mb-4">
                            <div class="xp-bar-fill" style="width: <?= htmlspecialchars($currentQuest['progress']) ?>%;"></div>
                        </div>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="student/lesson.php" class="btn btn-premium ajax-link px-4 py-2 rounded-pill fs-sm fw-bold"><i class="bi bi-play-fill me-1"></i> ลุยภารกิจต่อ</a>
                            <a href="student/dashboard.php" class="btn btn-theme-dark-adaptive ajax-link px-4 py-2 rounded-pill fw-medium fs-sm"><i class="bi bi-grid-1x2-fill me-2"></i>ไปยังแดชบอร์ด</a>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="d-inline-block px-3 py-1 rounded-pill mb-3" style="background-color: var(--surface-card); border: 1px solid var(--border-color);">
                        <span class="ai-text-gradient fw-bold fs-xs"><i class="bi bi-stars me-1"></i> LevelUp Engine v2.5 สมบูรณ์แบบ</span>
                    </div>
                    <h1 class="display-4 fw-bold text-white mb-4 lh-sm" style="font-family: 'Outfit', sans-serif;">
                        เรียนรู้ทักษะสาย Tech<br>ด้วย <span class="ai-text-gradient">AI Companion</span>
                    </h1>
                    <p class="lead text-light opacity-75 mb-4 pe-lg-5 fs-sm" style="line-height: 1.7;">
                        ระบบอีเลิร์นนิงรูปแบบ Gamification ที่มาพร้อมกับเอไอโค้ชส่วนตัว ช่วยคุณวางแผนการเรียน แก้บั๊กโค้ดแบบ Real-time และจำลองแล็บเสมือนจริงเพื่อพัฒนาทักษะการทำงาน
                    </p>
                    <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start mb-4">
                        <a href="auth/login.php" class="btn btn-premium ajax-link btn-lg px-4 rounded-pill fs-sm fw-bold">เข้าสู่ระบบใช้งานฟรี</a>
                    </div>
                <?php endif; ?>

                <!-- กล่องค้นหาหลัก -->
                <div class="search-box-wrapper glass-card p-2 mx-auto mx-lg-0" style="max-width: 550px;">
                    <form action="<?= $isLoggedIn ? 'student/courses.php' : 'auth/login.php' ?>" method="GET" id="ajax-search-form" class="d-flex">
                        <div class="input-group align-items-center">
                            <span class="input-group-text bg-transparent border-0 ps-3 text-white opacity-75"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control border-0 shadow-none py-3 bg-transparent fs-sm custom-search-input" name="q" placeholder="ค้นหาบทเรียน, คอร์ส หรือสกิลเทคโนโลยี..." autocomplete="off">
                            <button type="submit" class="btn btn-premium rounded-pill px-4 fw-bold fs-sm">ค้นหา</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-5 d-none d-lg-block">
                <div class="hero-image-wrapper position-relative text-center pe-4">
                    <img src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=600&auto=format&fit=crop" alt="AI Workspace" class="img-fluid rounded-4 shadow-lg hero-float-anim" style="border: 1px solid var(--border-color); max-height: 420px; object-fit: cover; width: 100%;">

                    <div class="position-absolute glass-card p-2 px-3 d-flex align-items-center gap-3 shadow-lg" style="bottom: 20px; left: 15px; z-index: 10; min-width: 220px; background: rgba(15, 23, 42, 0.85);">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fs-5" style="width: 40px; height: 40px; box-shadow: 0 0 15px var(--cyber-green);">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        <div class="text-start">
                            <h6 class="mb-0 fw-bold text-white fs-sm">Quest Completed!</h6>
                            <small class="text-light opacity-75 fs-xs">+1,200 XP Earned Today</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 2: SKILL TREES -->
<section class="categories-section py-5">
    <div class="container py-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h6 class="ai-text-gradient text-uppercase fw-bold mb-1 fs-xs" style="letter-spacing: 1px;">Skill Trees</h6>
                <h2 class="fw-bold text-white mb-0 fs-3" style="font-family: 'Outfit', sans-serif;">เลือกคอร์สตามเป้าหมายสกิล</h2>
            </div>
            <a href="<?= $isLoggedIn ? 'student/courses.php' : 'auth/login.php' ?>" class="btn btn-outline-light ajax-link btn-sm rounded-pill px-3 fs-xs fw-bold">
                ดูหมวดหมู่ทั้งหมด <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
            <?php foreach ($category_data as $cat): ?>
                <div class="col">
                    <a href="<?= $isLoggedIn ? 'student/courses.php?category=' . urlencode($cat['name']) : 'auth/login.php' ?>" class="card glass-card category-card-compact ajax-link text-decoration-none p-3 h-100 d-flex flex-row align-items-center gap-3">
                        <div class="icon-box-sm rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px; background: rgba(255, 255, 255, 0.05); color: <?= $cat['color'] ?>; border: 1px solid rgba(255, 255, 255, 0.1);">
                            <i class="bi <?= htmlspecialchars($cat['icon']) ?> fs-5"></i>
                        </div>
                        <div class="overflow-hidden">
                            <h6 class="fw-bold text-white mb-0 fs-sm text-truncate"><?= htmlspecialchars($cat['name']) ?></h6>
                            <small class="text-light opacity-50 fs-xs"><?= htmlspecialchars($cat['count']) ?> บทเรียน</small>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- SECTION 3: TRENDING QUESTS -->
<section class="popular-courses-section py-5" style="background-color: var(--section-bg-alt);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h6 class="ai-text-gradient text-uppercase fw-bold mb-2 fs-xs" style="letter-spacing: 1px;">Trending Quests</h6>
                <h2 class="fw-bold text-white mb-0" style="font-family: 'Outfit', sans-serif;">คอร์สเรียนยอดนิยมสัปดาห์นี้</h2>
            </div>
            <a href="<?= $isLoggedIn ? 'student/courses.php' : 'auth/login.php' ?>" class="btn btn-outline-secondary ajax-link btn-sm rounded-pill px-3 fs-xs">ดูทั้งหมด</a>
        </div>

        <div class="row g-4">
            <?php foreach ($popular_courses as $course): ?>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card h-100 glass-card text-start overflow-hidden">
                        <div class="position-relative">
                            <img src="<?= htmlspecialchars($course['image']) ?>" class="card-img-top" alt="Thumbnail" style="height: 160px; object-fit: cover; opacity: 0.9;">
                            <?php if ($course['is_bestseller']): ?>
                                <span class="position-absolute top-0 start-0 m-3 badge bg-danger text-white fs-xs px-2 py-1 rounded fw-bold">BEST SELLER</span>
                            <?php endif; ?>
                            <span class="position-absolute bottom-0 end-0 m-2 badge bg-dark text-light border border-secondary border-opacity-50 fs-xs"><?= htmlspecialchars($course['level']) ?></span>
                        </div>

                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="fw-bold text-white mb-2 line-clamp-2 fs-sm" style="min-height: 40px; line-height: 1.4; font-family: 'Outfit', sans-serif;">
                                    <?= htmlspecialchars($course['title']) ?>
                                </h6>
                                <p class="text-light opacity-50 fs-xs mb-3">ผู้สอน: <?= htmlspecialchars($course['instructor']) ?></p>

                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="text-warning fw-bold fs-xs"><i class="bi bi-star-fill me-1"></i><?= htmlspecialchars($course['rating']) ?></span>
                                    <span class="text-light opacity-50 fs-xs">(<?= htmlspecialchars($course['reviews']) ?>)</span>
                                    <span class="text-light opacity-25">|</span>
                                    <span class="text-light opacity-50 fs-xs"><i class="bi bi-people me-1"></i><?= htmlspecialchars($course['students']) ?> คน</span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center border-top border-secondary border-opacity-25 pt-2 mt-2">
                                <div>
                                    <span class="text-light opacity-50 fs-xs d-block">ราคาแพ็กเกจ</span>
                                    <span class="fw-bold text-info fs-sm"><?= htmlspecialchars($course['price']) ?></span>
                                </div>
                                <a href="<?= $isLoggedIn ? 'student/course-detail.php?id=' . $course['id'] : 'auth/login.php' ?>" class="btn btn-premium ajax-link btn-sm rounded-pill px-3 fs-xs fw-bold">ดูรายละเอียด</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- SECTION 4: CAREER MAPS & AI FEATURES -->
<section class="py-5">
    <div class="container py-3">
        <div class="row align-items-center gy-5">
            <div class="col-lg-5">
                <h6 class="ai-text-gradient text-uppercase fw-bold mb-2 fs-xs" style="letter-spacing: 1px;">Career Maps</h6>
                <h2 class="fw-bold text-white mb-4" style="font-family: 'Outfit', sans-serif;">ก้าวตามโรดMAPสู่สายงานอาชีพ</h2>
                <p class="text-light opacity-75 fs-sm mb-4">เราคัดกรองเนื้อหาเป็นลำดับขั้นเพื่อปูพื้นฐานจากศูนย์จนสามารถพัฒนาโปรเจกต์ส่งสมัครงานจริงได้อย่างเป็นระบบ</p>

                <div class="d-flex flex-column gap-3 mb-4">
                    <?php foreach ($career_paths as $path): ?>
                        <a href="<?= $isLoggedIn ? 'student/learning-path.php?path=' . urlencode($path['slug']) : 'auth/login.php' ?>" class="glass-card ajax-link p-3 text-decoration-none d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="<?= htmlspecialchars($path['bg_class']) ?> text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi <?= htmlspecialchars($path['icon']) ?> fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-white mb-0 fs-sm"><?= htmlspecialchars($path['title']) ?></h6>
                                    <small class="text-light opacity-50 fs-xs"><?= htmlspecialchars($path['quests']) ?> เควสต์ย่อย • <?= htmlspecialchars($path['duration']) ?></small>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right text-light opacity-50"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
                <a href="<?= $isLoggedIn ? 'student/learning-path.php' : 'auth/login.php' ?>" class="btn btn-theme-dark-adaptive ajax-link rounded-pill fw-bold px-4 fs-sm">ดูแผนผังทั้งหมด</a>
            </div>

            <div class="col-lg-6 offset-lg-1">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="glass-card text-white p-4" style="background: linear-gradient(135deg, var(--neon-indigo), #3730A3); border: none;">
                            <i class="bi bi-robot fs-1 mb-2 text-warning"></i>
                            <h4 class="fw-bold text-white" style="font-family: 'Outfit', sans-serif;">24/7 AI Companion</h4>
                            <p class="mb-0 text-white-50 fs-sm">ระบบที่คอยเป็นผู้ช่วยให้คำปรึกษาและตรวจโค้ดเสมือนมีรุ่นพี่โปรแกรมเมอร์คอยนั่งประกบอยู่เคียงข้างคุณตลอดการทำภารกิจ</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="glass-card p-4 h-100">
                            <i class="bi bi-bullseye text-primary fs-2 mb-3"></i>
                            <h5 class="fw-bold text-white fs-sm" style="font-family: 'Outfit', sans-serif;">Skill Radar Graph</h5>
                            <p class="text-light opacity-75 fs-xs mb-0">ระบบประเมินค่าพลังวิเคราะห์ทักษะความรู้แต่ละด้าน เพื่อบอกจุดเด่นจุดด้อยที่คุณควรปรับปรุง</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="glass-card p-4 h-100">
                            <i class="bi bi-magic text-success fs-2 mb-3"></i>
                            <h5 class="fw-bold text-white fs-sm" style="font-family: 'Outfit', sans-serif;">Dynamic Sandbox Labs</h5>
                            <p class="text-light opacity-75 fs-xs mb-0">โจทย์เขียนโค้ดปรับระดับความยากง่ายอัตโนมัติอิงตามทักษะและผลงานก่อนหน้าของคุณ</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 5: VIRTUAL CYBER LAB PREVIEW -->
<section class="lab-section py-5 text-center position-relative overflow-hidden" style="background-color: var(--lab-bg) !important;">
    <div class="container position-relative z-3 py-3">
        <span class="badge rounded-pill bg-primary bg-opacity-25 text-info border border-primary mb-3 px-3 py-2 fs-xs fw-bold">Cloud Terminal Environment</span>
        <h2 class="fw-bold mb-3 text-white display-6" style="font-family: 'Outfit', sans-serif;">ลงมือปฏิบัติจริงบน Cyber Arena</h2>
        <p class="lead text-light opacity-75 mx-auto mb-5 fs-sm" style="max-width: 650px;">
            ไม่จำเป็นต้องติดตั้งโปรแกรมหรือตั้งค่าสภาพแวดล้อมให้ปวดหัว รันคำสั่งคอมมานด์ไลน์และโค้ดโปรแกรมผ่านเว็บบราวเซอร์ของคุณได้ทันที
        </p>

        <div class="browser-mockup glass-card shadow-lg text-start mx-auto border border-secondary border-opacity-50" style="max-width: 820px; background: var(--terminal-bg);">
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
                <span style="color: var(--plasma-orange);">[AI Analyzer]:</span> สแกนพบบั๊กคอมไพล์ในเส้นทาง Component บรรทัดที่ 42 แนะนำให้ใส่เครื่องหมายปิดปีกกาให้ถูกต้องครับ<br>
                <span class="text-white">user@levelup-sandbox:~$</span> <span class="text-white opacity-75 animate-blink">|</span>
            </div>
        </div>

        <div class="mt-5">
            <a href="<?= $isLoggedIn ? 'student/virtual-lab.php' : 'auth/login.php' ?>" class="btn btn-premium ajax-link px-5 py-3 rounded-pill fw-bold fs-sm">เปิดใช้งาน Sandbox Area <i class="bi bi-controller ms-2"></i></a>
        </div>
    </div>
</section>

<!-- SECTION 6: PLATFORM METRICS & FAQ -->
<section class="py-5">
    <div class="container py-3">
        <div class="row text-center gy-4 mb-5 pb-5 border-bottom border-secondary border-opacity-25">
            <?php foreach ($platform_metrics as $metric): ?>
                <div class="col-6 col-md-3">
                    <h2 class="display-5 fw-bold mb-0 ai-text-gradient"><?= htmlspecialchars($metric['value']) ?></h2>
                    <p class="text-light opacity-50 fw-bold text-uppercase fs-xs mt-1"><?= htmlspecialchars($metric['label']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mb-5">
            <h2 class="fw-bold text-white" style="font-family: 'Outfit', sans-serif;">คำถามที่พบบ่อย (FAQ)</h2>
            <p class="text-light opacity-50 fs-sm">รวบรวมคำตอบเบื้องต้นสำหรับผู้เล่นใหม่ทุกท่าน</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion custom-accordion" id="faqAccordion">
                    <?php foreach ($faq_list as $faq): ?>
                        <div class="accordion-item glass-card mb-3 border-0">
                            <h2 class="accordion-header">
                                <button class="accordion-button <?= $faq['show'] ? '' : 'collapsed' ?> fw-bold text-white bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#<?= htmlspecialchars($faq['id']) ?>">
                                    <?= htmlspecialchars($faq['question']) ?>
                                </button>
                            </h2>
                            <div id="<?= htmlspecialchars($faq['id']) ?>" class="accordion-collapse collapse <?= $faq['show'] ? 'show' : '' ?>" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-light opacity-75 pt-0 fs-sm">
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
<section class="cta-section py-4 mb-5">
    <div class="container">
        <div class="glass-card p-4 p-md-5 text-center position-relative overflow-hidden" style="background: linear-gradient(135deg, var(--neon-indigo), #3730A3); border: none;">
            <div class="position-relative z-3 text-white">
                <h2 class="display-6 fw-bold mb-3 text-white" style="font-family: 'Outfit', sans-serif;">พร้อมที่จะอัปเกรดสถานะเลเวลของคุณหรือยัง?</h2>
                <p class="lead mb-4 text-white-50 mx-auto fs-sm" style="max-width: 600px;">เข้าร่วมสมรภูมิพัฒนาทักษะแบบไร้ขีดจำกัด เก็บสะสมแต้มผลงานเพื่อเพิ่มโอกาสสู่สายอาชีพที่มั่นคง</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="auth/login.php" class="btn btn-light ajax-link btn-lg px-4 px-md-5 fw-bold text-primary rounded-pill fs-sm">เข้าสู่ระบบผู้เล่น</a>
                    <a href="<?= $isLoggedIn ? 'ai/ai-chat.php' : 'auth/login.php' ?>" class="btn btn-outline-light ajax-link btn-lg px-4 rounded-pill fs-sm"><i class="bi bi-robot me-2"></i>คุยกับ AI โค้ช</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// ดึงไฟล์ Footer เข้ามาทำงานปิดหน้าเว็บ
if (file_exists('includes/footer.php')) {
    include 'includes/footer.php';
} else {
    echo "Warning: ไม่พบไฟล์ 'includes/footer.php'";
}
?>

<!-- ===================================================
     3. JAVASCRIPT SPECIAL INTERCEPTOR
     =================================================== -->
<script>
    $(document).ready(function() {
        // ดักจับเหตุการณ์การคลิกบนหน้าเว็บเพื่อความปลอดภัยของลิงก์ Logout
        $(document).on('click', 'a', function(e) {
            var href = $(this).attr('href');
            var text = $(this).text().toLowerCase();

            if (href && (href.indexOf('logout.php') !== -1 || text.indexOf('logout') !== -1 || text.indexOf('ออกจากระบบ') !== -1)) {
                e.stopPropagation();
                window.location.href = 'auth/logout.php';
            }
        });
    });
</script>