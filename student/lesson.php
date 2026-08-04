<?php
$ROOT_PATH = '../';
// lesson.php - หน้าจอการเรียนรู้หลัก (Main Learning Workspace)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =========================================================================
// 1. การจัดการข้อมูลจาก Database (PHP Setup Zone)
// =========================================================================

// ข้อมูลผู้ใช้ (User Profile Data)
$user = [
    'id' => 101,
    'username' => 'Alex Dev',
    'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=100&q=80',
    'level' => 12,
    'title' => 'Level 12 Developer',
    'xp' => 1450
];

// ข้อมูลคอร์สเรียน (Course Data)
$course = [
    'id' => 1,
    'title' => 'Mastering Python for AI',
    'slug' => 'mastering-python-for-ai',
    'category' => 'Artificial Intelligence'
];

// ข้อมูลบทเรียนปัจจุบัน (Current Lesson Data)
$current_lesson = [
    'id' => 4,
    'chapter_num' => 2,
    'chapter_title' => 'Chapter 2: Functions',
    'title' => '4. Understanding Recursion & Memoization',
    'video_url' => 'https://www.w3schools.com/html/mov_bbb.mp4',
    'poster_url' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=100&q=80',
    'description' => 'Recursion (การเรียกซ้ำ) คือเทคนิคการเขียนโปรแกรมที่ฟังก์ชันเรียกใช้งานตัวเองเพื่อแก้ปัญหาที่เล็กลงจนกว่าจะถึงจุดสิ้นสุด เสมือนการเปิดกล่องซ้อนกล่องไปเรื่อยๆ จนกว่าจะเจอกล่องใบเล็กสุดที่เปิดไม่ได้อีกแล้ว',
    'code_snippet' => "def factorial(n):\n    # Base case\n    if n == 1:\n        return 1\n    # Recursive case\n    else:\n        return n * factorial(n - 1)\n\nprint(factorial(5)) # Output: 120",
    'mini_lab' => [
        'title' => 'ภารกิจย่อย: Countdown Protocol',
        'instruction' => 'เขียนฟังก์ชัน `countdown(n)` แบบ Recursive พิมพ์เลขจนถึง 0',
        'reward_xp' => 10,
        'initial_code' => "def countdown(n):\n    # พิมพ์คำสั่งตรวจสอบ Base Case และ Recursive Case ที่นี่\n    pass"
    ]
];

// ข้อมูลความคืบหน้า (User Progress)
$user_progress = [
    'percent' => 78,
    'completed_lessons' => [1, 2, 3]
];

// รายการโครงสร้างบทเรียนทั้งหมด (Syllabus List)
$chapters = [
    [
        'title' => 'Chapter 1: Basics',
        'lessons' => [
            ['id' => 1, 'title' => '1. Intro to Python', 'status' => 'completed'],
            ['id' => 2, 'title' => '2. Variables & Types', 'status' => 'completed']
        ]
    ],
    [
        'title' => 'Chapter 2: Functions',
        'lessons' => [
            ['id' => 3, 'title' => '3. Defining Functions', 'status' => 'completed'],
            ['id' => 4, 'title' => '4. Understanding Recursion', 'status' => 'active'],
            ['id' => 5, 'title' => '5. Mini Lab: Fibonacci', 'status' => 'locked']
        ]
    ]
];

// ข้อมูลเอกสารประกอบการเรียน (Resources)
$resources = [
    [
        'title' => 'พิมพ์เขียว (Lecture Slides)',
        'type' => 'PDF',
        'size' => '2.4 MB',
        'icon' => 'bi-file-earmark-pdf-fill',
        'badge_bg' => 'bg-danger bg-opacity-25 text-danger'
    ],
    [
        'title' => 'โค้ดตัวอย่าง (Source Code)',
        'type' => 'ZIP',
        'size' => '15 KB',
        'icon' => 'bi-file-earmark-code-fill',
        'badge_bg' => 'bg-info bg-opacity-25 text-info'
    ]
];

// ข้อมูลกระดานพูดคุย (Discussions)
$discussions = [
    [
        'id' => 1,
        'username' => 'Alex Dev',
        'user_level' => 'Lvl 8',
        'time_ago' => '2 วันที่แล้ว',
        'upvotes' => 12,
        'question' => 'ถ้าไม่ใส่ Base case จะเกิดอะไรขึ้นครับ? โปรแกรมจะระเบิดไหม 😅',
        'reply' => [
            'author' => 'Holo AI (Instructor)',
            'content' => 'ถ้าไม่ใส่ Base Case ฟังก์ชันจะเรียกตัวเองไปเรื่อยๆ ไม่มีที่สิ้นสุด (Infinite Loop) จนกระทั่งหน่วยความจำ Stack เต็ม ซึ่งจะทำให้ระบบตัดการทำงานและแจ้งเตือน <code>RecursionError</code> ออกมาครับ!'
        ]
    ]
];

// =========================================================================
// 2. Include Header หลักของระบบ
// =========================================================================
require_once '../includes/header.php';
?>

<!-- 🎨 Dynamic CSS สำหรับ Workspace บทเรียน (ปรับแก้เรื่องสีข้อความให้รองรับทั้ง Light และ Dark Mode) -->
<style>
    /* Dark Mode Theme Variables */
    :root, html[data-theme="dark"] {
        --lv-bg-dark: #090d1a;
        --lv-surface: rgba(20, 27, 45, 0.95);
        --lv-surface-alt: #0d1326;
        --lv-border-glow: rgba(99, 102, 241, 0.25);
        --lv-border-hover: rgba(6, 182, 212, 0.5);
        --lv-primary: #6366F1;
        --lv-cyan: #06b6d4;
        --lv-success: #10B981;
        --lv-warning: #F59E0B;
        --lv-danger: #EF4444;
        --lv-text-main: #f8fafc;
        --lv-text-body: #cbd5e1;
        --lv-text-muted: #94a3b8;
        --lv-card-bg: rgba(20, 27, 45, 0.9);
        --lv-code-bg: #0d1117;
        --lv-footer-bg: rgba(5, 8, 17, 0.9);
    }

    /* Light Mode Theme Variables */
    html[data-theme="light"] {
        --lv-bg-dark: #f8fafc;
        --lv-surface: #ffffff;
        --lv-surface-alt: #f1f5f9;
        --lv-border-glow: #e2e8f0;
        --lv-border-hover: #06b6d4;
        --lv-primary: #4f46e5;
        --lv-cyan: #0891b2;
        --lv-success: #059669;
        --lv-warning: #d97706;
        --lv-danger: #dc2626;
        --lv-text-main: #0f172a;
        --lv-text-body: #334155;
        --lv-text-muted: #64748b;
        --lv-card-bg: #ffffff;
        --lv-code-bg: #1e293b;
        --lv-footer-bg: #f1f5f9;
    }

    /* 🛠️ Dynamic Text Color Controls */
    html, body {
        background-color: var(--lv-bg-dark) !important;
        color: var(--lv-text-main) !important;
    }

    .text-muted {
        color: var(--lv-text-muted) !important;
    }

    .text-main {
        color: var(--lv-text-main) !important;
    }

    .lesson-reading, 
    .lesson-reading p, 
    .lesson-reading li {
        color: var(--lv-text-body) !important;
        line-height: 1.8;
        font-size: 1.05rem;
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    ::-webkit-scrollbar-track {
        background: var(--lv-bg-dark);
    }
    ::-webkit-scrollbar-thumb {
        background: rgba(150, 150, 150, 0.25);
        border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: rgba(150, 150, 150, 0.4);
    }

    /* Mapping Classes */
    .bg-surface-alt { background-color: var(--lv-surface-alt) !important; }
    .text-cyan { color: var(--lv-cyan) !important; }
    .border-color { border-color: var(--lv-border-glow) !important; }

    /* Layout Workspace Structure */
    .lesson-workspace {
        display: flex;
        height: calc(100vh - 76px);
        overflow: hidden;
        position: relative;
        background-color: var(--lv-bg-dark);
    }

    .lesson-sidebar-left {
        width: 320px;
        background: var(--lv-surface);
        border-right: 1px solid var(--lv-border-glow);
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease;
        z-index: 1040;
    }

    .lesson-main-content {
        flex-grow: 1;
        background: var(--lv-bg-dark);
        overflow-y: auto;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background-image: radial-gradient(circle at 50% 0%, rgba(99, 102, 241, 0.05) 0%, transparent 70%);
        z-index: 10;
    }

    .lesson-sidebar-right {
        width: 360px;
        background: var(--lv-surface);
        border-left: 1px solid var(--lv-border-glow);
        display: flex;
        flex-direction: column;
        z-index: 1040;
    }

    /* Mission Log */
    .mission-group { margin-bottom: 1rem; }
    .mission-header {
        font-size: 0.75rem;
        letter-spacing: 1px;
        color: var(--lv-text-muted);
        padding: 0.5rem 1.25rem;
        text-transform: uppercase;
        font-weight: 700;
    }
    .mission-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0.8rem 1.25rem;
        color: var(--lv-text-main);
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s;
        border-left: 3px solid transparent;
        cursor: pointer;
    }
    .mission-item:hover:not(.locked) { 
        background: rgba(150, 150, 150, 0.08); 
    }
    .mission-item.active {
        background: rgba(99, 102, 241, 0.12);
        border-left-color: var(--lv-cyan);
        font-weight: 600;
    }
    .mission-item.locked { 
        opacity: 0.4; 
        pointer-events: none;
        cursor: not-allowed;
    }

    /* Video Player */
    .video-container {
        border: 1px solid var(--lv-border-glow);
        background: #000;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        position: relative;
    }
    .video-container video { width: 100%; height: auto; display: block; border-radius: 16px; }
    
    /* Mini Lab Challenge */
    .mini-lab-card {
        background: var(--lv-card-bg);
        border: 1px solid var(--lv-border-hover);
        box-shadow: 0 0 20px rgba(6, 182, 212, 0.08);
    }
    .code-editor {
        font-family: 'Fira Code', monospace;
        background: var(--lv-code-bg) !important;
        color: #c9d1d9 !important;
        border: 1px solid var(--lv-border-glow) !important;
        font-size: 0.9rem;
    }

    /* AI Tutor Panel */
    .ai-nav-tabs .nav-link {
        color: var(--lv-text-muted);
        border: none;
        background: transparent;
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .ai-nav-tabs .nav-link.active {
        color: var(--lv-text-main);
        background: rgba(99, 102, 241, 0.15);
        font-weight: 600;
    }
    .chat-bubble-ai {
        background: var(--lv-surface-alt);
        border: 1px solid var(--lv-border-glow);
        border-radius: 12px;
        border-top-left-radius: 2px;
        color: var(--lv-text-main);
    }
    .chat-bubble-user {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.2) 0%, rgba(139, 92, 246, 0.2) 100%);
        border: 1px solid var(--lv-border-glow);
        border-radius: 12px;
        border-top-right-radius: 2px;
        color: var(--lv-text-main);
    }

    /* Buttons & Utility */
    .btn-premium {
        background: linear-gradient(135deg, var(--lv-cyan) 0%, var(--lv-primary) 100%);
        color: #fff !important;
        border: none;
        box-shadow: 0 4px 15px rgba(6, 182, 212, 0.25);
    }
    .btn-premium:hover { box-shadow: 0 6px 20px rgba(6, 182, 212, 0.4); transform: translateY(-1px); }
    .btn-outline-surface {
        background: rgba(150, 150, 150, 0.05);
        border: 1px solid var(--lv-border-glow);
        color: var(--lv-text-main);
    }
    .btn-outline-surface:hover { border-color: var(--lv-cyan); color: var(--lv-text-main); }

    /* Responsive Support */
    @media (max-width: 991px) {
        .lesson-workspace { height: auto; min-height: calc(100vh - 76px); }
        .lesson-sidebar-left { position: fixed; top: 76px; bottom: 0; transform: translateX(-100%); }
        .lesson-sidebar-left.show { transform: translateX(0); box-shadow: 10px 0 30px rgba(0,0,0,0.3); }
        .lesson-sidebar-right { display: none; }
    }
</style>

<!-- 🧩 Main Workspace Area -->
<div class="lesson-workspace w-100">
    
    <!-- 📜 Left Sidebar: Mission Log (Syllabus) -->
    <aside class="lesson-sidebar-left" id="lesson-sidebar">
        <div class="p-3 border-bottom border-color d-flex justify-content-between align-items-center bg-surface-alt">
            <h6 class="mb-0 fw-bold text-main"><i class="bi bi-journal-bookmark-fill text-primary me-2"></i>Mission Log</h6>
        </div>
        <div class="flex-grow-1 overflow-auto py-2">
            
            <?php
$ROOT_PATH = '../';
foreach ($chapters as $chapter): ?>
                <div class="mission-group">
                    <div class="mission-header"><?= htmlspecialchars($chapter['title']) ?></div>
                    <?php
$ROOT_PATH = '../';
foreach ($chapter['lessons'] as $item): ?>
                        <?php
$ROOT_PATH = '../';
$is_active = ($item['id'] === $current_lesson['id']);
                            $is_completed = in_array($item['id'], $user_progress['completed_lessons']);
                            $is_locked = ($item['status'] === 'locked');
                        ?>
                        <a href="#" class="mission-item <?= $is_active ? 'active' : '' ?> <?= $is_locked ? 'locked' : '' ?>" <?= $is_locked ? 'title="ต้องผ่านภารกิจปัจจุบันก่อน"' : '' ?>>
                            <?php
$ROOT_PATH = '../';
if ($is_completed): ?>
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                            <?php
$ROOT_PATH = '../';
elseif ($is_active): ?>
                                <i class="bi bi-play-circle-fill text-cyan fs-5"></i>
                            <?php
$ROOT_PATH = '../';
else: ?>
                                <i class="bi bi-lock-fill text-muted fs-5"></i>
                            <?php
$ROOT_PATH = '../';
endif; ?>
                            
                            <span class="mission-title-text text-main"><?= htmlspecialchars($item['title']) ?></span>
                            
                            <?php
$ROOT_PATH = '../';
if ($is_active): ?>
                                <span class="badge bg-primary ms-auto rounded-pill badge-now" style="font-size: 0.65rem;">NOW</span>
                            <?php
$ROOT_PATH = '../';
endif; ?>
                        </a>
                    <?php
$ROOT_PATH = '../';
endforeach; ?>
                </div>
            <?php
$ROOT_PATH = '../';
endforeach; ?>

        </div>
    </aside>

    <!-- 🎯 Center: Main Content Area -->
    <main class="lesson-main-content">
        <div class="p-3 p-md-4 p-lg-5 mx-auto w-100 flex-grow-1" style="max-width: 950px;">
            
            <!-- Breadcrumb Title -->
            <div class="mb-3 d-flex align-items-center gap-2 text-muted small">
                <a href="course-detail.php" class="text-muted text-decoration-none hover-text-main"><i class="bi bi-arrow-left me-1"></i> ย้อนกลับ</a>
                <span>/</span>
                <span><?= htmlspecialchars($current_lesson['chapter_title']) ?></span> 
                <i class="bi bi-chevron-right" style="font-size: 0.7rem;"></i> 
                <span class="text-cyan fw-medium" id="breadcrumb-current-mission"><?= htmlspecialchars($current_lesson['title']) ?></span>
            </div>

            <!-- Video Holographic Player -->
            <div class="video-container mb-4">
                <video id="lesson-video" src="<?= htmlspecialchars($current_lesson['video_url']) ?>" controls class="w-100" poster="<?= htmlspecialchars($current_lesson['poster_url']) ?>"></video>
            </div>

            <!-- Content Navigation Tabs -->
            <ul class="nav nav-pills mb-4 gap-2 border-bottom border-color pb-3" id="lesson-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill px-4 btn-outline-surface border-0" data-bs-toggle="pill" data-bs-target="#tab-content" style="font-size: 0.9rem;">
                        <i class="bi bi-book me-1"></i> Data Log (เนื้อหา)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4 btn-outline-surface border-0" data-bs-toggle="pill" data-bs-target="#tab-resources" style="font-size: 0.9rem;">
                        <i class="bi bi-box-seam me-1"></i> Supply (ทรัพยากร)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4 btn-outline-surface border-0" data-bs-toggle="pill" data-bs-target="#tab-discussion" style="font-size: 0.9rem;">
                        <i class="bi bi-chat-square-text me-1"></i> Comms (ถาม-ตอบ)
                    </button>
                </li>
            </ul>

            <!-- Tab Content Area -->
            <div class="tab-content" id="lesson-tabs-content">
                
                <!-- 1. Data Log (Overview) -->
                <div class="tab-pane fade show active" id="tab-content">
                    
                    <!-- AI Smart Intervention Alert -->
                    <div class="alert border border-color rounded-4 shadow-sm mb-4 d-flex gap-3 align-items-start p-4" style="background: var(--lv-card-bg); border-left: 4px solid var(--lv-primary) !important;">
                        <div class="bg-primary bg-opacity-25 p-2 rounded-circle text-primary">
                            <i class="bi bi-robot fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-2 text-main">ระบบ AI ประเมินผลระหว่างเรียน</h6>
                            <p class="mb-3 text-muted" style="font-size: 0.9rem;">ตรวจพบว่าคุณหยุดวิดีโอบ่อยในช่วงนาทีที่ 02:15 (Base Case) ต้องการให้จำลองสถานการณ์ Base Condition ด้วยภาพประกอบ หรือตัวอย่างโค้ดเพิ่มเติมไหม?</p>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-premium rounded-pill px-3"><i class="bi bi-image me-1"></i> แสดงภาพจำลอง</button>
                                <button class="btn btn-sm btn-outline-surface rounded-pill px-3">ข้ามไปก่อน</button>
                            </div>
                        </div>
                    </div>

                    <!-- Reading Material -->
                    <article class="lesson-reading my-4">
                        <h2 class="fw-bold mb-4 text-main" id="main-lesson-title"><?= htmlspecialchars($current_lesson['title']) ?></h2>
                        <p><?= htmlspecialchars($current_lesson['description']) ?></p>
                        
                        <h4 class="mt-4 mb-3 text-main">โครงสร้างของ Recursive Function</h4>
                        <p>ทุกฟังก์ชันที่เรียกตัวเองจะต้องมี 2 ส่วนหลัก เพื่อป้องกันไม่ให้โปรแกรมทำงานวนลูปไม่รู้จบ:</p>
                        <ul class="mb-4">
                            <li><strong class="text-cyan">Base Case:</strong> เงื่อนไขการหยุด (กล่องใบเล็กสุด)</li>
                            <li><strong class="text-primary">Recursive Case:</strong> ส่วนที่ฟังก์ชันเรียกใช้งานตัวเองด้วยข้อมูลที่เล็กลง</li>
                        </ul>

                        <!-- Code Block -->
                        <div class="rounded-4 border border-secondary overflow-hidden my-4 shadow-sm" style="background: var(--lv-code-bg);">
                            <div class="d-flex justify-content-between align-items-center p-2 px-3 border-bottom border-secondary" style="background: rgba(0,0,0,0.2);">
                                <span class="small text-muted fw-bold font-monospace">python</span>
                                <button class="btn btn-sm btn-outline-surface border-0 text-muted"><i class="bi bi-clipboard"></i></button>
                            </div>
                            <div class="p-3 font-monospace text-light" style="font-size: 0.9rem; overflow-x: auto;">
                                <pre class="m-0"><?= htmlspecialchars($current_lesson['code_snippet']) ?></pre>
                            </div>
                        </div>
                    </article>

                    <hr class="my-5 border-secondary opacity-25">

                    <!-- ⚔️ Boss Challenge: Mini Lab -->
                    <div class="mini-lab-card rounded-4 p-4 p-md-5 mb-5 position-relative">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                            <div>
                                <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-50 rounded-pill mb-2 px-2 py-1" style="font-size: 0.7rem;"><i class="bi bi-fire me-1"></i> LIVE CHALLENGE</span>
                                <h4 class="fw-bold mb-1 text-main"><?= htmlspecialchars($current_lesson['mini_lab']['title']) ?></h4>
                                <p class="text-muted small mb-0"><?= htmlspecialchars($current_lesson['mini_lab']['instruction']) ?></p>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-warning fs-5">+<?= $current_lesson['mini_lab']['reward_xp'] ?> XP</div>
                                <div class="small text-muted">รางวัลเมื่อผ่านด่าน</div>
                            </div>
                        </div>

                        <textarea id="lab-editor" class="form-control code-editor rounded-3 p-3 mb-3" rows="6" spellcheck="false"><?= htmlspecialchars($current_lesson['mini_lab']['initial_code']) ?></textarea>
                        
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div class="d-flex gap-2">
                                <button class="btn btn-premium rounded-pill px-4" id="btn-run-lab">
                                    <i class="bi bi-play-fill me-1"></i> Deploy & Test
                                </button>
                                <button class="btn btn-outline-surface rounded-pill px-3" id="btn-ask-hint">
                                    <i class="bi bi-lightbulb"></i> ขอคำใบ้
                                </button>
                            </div>
                        </div>

                        <div id="lab-result" class="mt-4 p-3 rounded-3 border border-secondary font-monospace small text-muted d-none" style="min-height: 80px; background: var(--lv-code-bg);">
                            <div class="terminal-text"></div>
                        </div>
                    </div>

                    <!-- Prev / Next Navigation -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="#" class="btn btn-outline-surface rounded-pill px-4"><i class="bi bi-arrow-left me-2"></i> Mission ก่อนหน้า</a>
                        <a href="#" class="btn btn-primary rounded-pill px-4" style="background: var(--lv-primary); border: none;">Mission ถัดไป <i class="bi bi-arrow-right ms-2"></i></a>
                    </div>
                </div>

                <!-- 2. Supply (Resources) -->
                <div class="tab-pane fade" id="tab-resources">
                    <div class="row g-3">
                        <?php
$ROOT_PATH = '../';
foreach ($resources as $res): ?>
                            <div class="col-md-6">
                                <div class="border border-color p-3 rounded-4 d-flex align-items-center gap-3" style="background: var(--lv-card-bg);">
                                    <div class="<?= $res['badge_bg'] ?> p-3 rounded-3">
                                        <i class="bi <?= $res['icon'] ?> fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold text-main"><?= htmlspecialchars($res['title']) ?></h6>
                                        <div class="small text-muted"><?= htmlspecialchars($res['size']) ?> • <?= htmlspecialchars($res['type']) ?></div>
                                    </div>
                                    <button class="btn btn-outline-surface rounded-circle p-2"><i class="bi bi-download"></i></button>
                                </div>
                            </div>
                        <?php
$ROOT_PATH = '../';
endforeach; ?>
                    </div>
                </div>

                <!-- 3. Comms (Discussion) -->
                <div class="tab-pane fade" id="tab-discussion">
                    <div class="d-flex gap-3 mb-5 p-3 rounded-4 border border-color" style="background: var(--lv-card-bg);">
                        <img src="<?= htmlspecialchars($user['avatar']) ?>" class="rounded-circle border border-secondary" width="45" height="45" alt="You">
                        <div class="flex-grow-1 position-relative">
                            <input type="text" class="form-control border-secondary rounded-pill px-4 py-2 shadow-none" placeholder="ส่งข้อความสื่อสารไปยังฐาน (AI ช่วยตอบทันที)..." style="font-size: 0.9rem; background: var(--lv-surface-alt); color: var(--lv-text-main);">
                            <button class="btn btn-link text-cyan position-absolute end-0 top-50 translate-middle-y px-3"><i class="bi bi-send-fill"></i></button>
                        </div>
                    </div>

                    <?php
$ROOT_PATH = '../';
foreach ($discussions as $disc): ?>
                        <div class="discussion-item d-flex gap-3 mb-4 border-bottom border-secondary pb-4">
                            <div class="d-flex flex-column align-items-center text-muted">
                                <button class="btn btn-sm btn-link text-muted p-0"><i class="bi bi-caret-up-fill fs-5"></i></button>
                                <span class="fw-bold my-1 text-main"><?= $disc['upvotes'] ?></span>
                                <button class="btn btn-sm btn-link text-muted p-0"><i class="bi bi-caret-down-fill fs-5"></i></button>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="fw-bold small me-1 text-main"><?= htmlspecialchars($disc['username']) ?></span>
                                    <span class="badge bg-secondary bg-opacity-25 text-muted rounded-pill" style="font-size: 0.65rem;"><?= htmlspecialchars($disc['user_level']) ?></span>
                                    <span class="text-muted small ms-auto" style="font-size: 0.75rem;"><?= htmlspecialchars($disc['time_ago']) ?></span>
                                </div>
                                <p class="mb-2 text-muted" style="font-size: 0.95rem;"><?= htmlspecialchars($disc['question']) ?></p>
                                <div class="d-flex gap-3 text-muted small fw-semibold">
                                    <a href="#" class="text-decoration-none text-cyan"><i class="bi bi-reply-fill me-1"></i>ตอบกลับ</a>
                                </div>
                                
                                <?php
$ROOT_PATH = '../';
if (!empty($disc['reply'])): ?>
                                    <div class="mt-3 p-3 rounded-4 border border-color" style="background: var(--lv-card-bg); border-left: 4px solid var(--lv-primary) !important;">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="bi bi-robot text-primary fs-5"></i>
                                            <span class="fw-bold text-primary" style="font-size: 0.85rem;"><?= htmlspecialchars($disc['reply']['author']) ?></span>
                                        </div>
                                        <p class="mb-0 text-muted" style="font-size: 0.9rem;"><?= $disc['reply']['content'] ?></p>
                                    </div>
                                <?php
$ROOT_PATH = '../';
endif; ?>
                            </div>
                        </div>
                    <?php
$ROOT_PATH = '../';
endforeach; ?>
                </div>

            </div>
        </div>

        <main>
        <?php
$ROOT_PATH = '../';
require_once '../includes/footer.php'; ?>
    </main>

    <!-- 🤖 Right Sidebar: AI Tutor Panel -->
    <aside class="lesson-sidebar-right d-none d-lg-flex" id="ai-sidebar">
        <ul class="nav nav-pills ai-nav-tabs p-2 gap-1 border-bottom border-color bg-surface-alt" role="tablist">
            <li class="nav-item flex-grow-1 text-center" role="presentation">
                <button class="nav-link active w-100" data-bs-toggle="pill" data-bs-target="#ai-chat"><i class="bi bi-robot me-1"></i> AI Assistant</button>
            </li>
            <li class="nav-item flex-grow-1 text-center" role="presentation">
                <button class="nav-link w-100" data-bs-toggle="pill" data-bs-target="#my-notes"><i class="bi bi-journal-text me-1"></i> Note</button>
            </li>
        </ul>

        <div class="tab-content flex-grow-1 overflow-hidden d-flex flex-column">
            <div class="tab-pane fade show active h-100 d-flex flex-column" id="ai-chat">
                <div class="flex-grow-1 overflow-auto p-3 d-flex flex-column gap-3" id="chat-messages">
                    <div class="d-flex gap-2">
                        <div class="chat-bubble-ai p-3 shadow-sm" style="max-width: 90%; font-size: 0.85rem;">
                            <strong>สวัสดีผู้กล้า! 🛡️</strong><br>
                            ฉันคือ AI ประจำด่าน <strong id="ai-welcome-title">"<?= htmlspecialchars($current_lesson['title']) ?>"</strong><br><br>
                            คุณกำลังติดปัญหาตรงไหนไหม? สามารถส่งโค้ด หรือให้ฉันสรุปย่อคลิปวิดีโอส่วนที่ดูอยู่ให้ฟังได้เลย!
                        </div>
                    </div>
                </div>
                
                <div class="p-3 border-top border-color bg-surface-alt">
                    <div class="d-flex gap-2 mb-2 overflow-auto text-nowrap pb-1">
                        <button class="badge bg-secondary bg-opacity-25 text-muted border border-secondary rounded-pill px-3 py-2 btn-quick-prompt" style="cursor: pointer;">อธิบายโค้ด</button>
                        <button class="badge bg-secondary bg-opacity-25 text-muted border border-secondary rounded-pill px-3 py-2 btn-quick-prompt" style="cursor: pointer;">ขอตัวอย่าง</button>
                    </div>
                    <form id="aiChatForm" class="d-flex border border-secondary rounded-4 p-1" style="background: var(--lv-bg-dark);">
                        <input type="text" id="ai-input" class="form-control bg-transparent border-0 shadow-none" placeholder="ป้อนคำสั่ง AI..." autocomplete="off" style="font-size: 0.85rem; color: var(--lv-text-main);">
                        <button type="submit" class="btn btn-premium rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; min-width: 32px;"><i class="bi bi-send-fill" style="font-size: 0.8rem;"></i></button>
                    </form>
                </div>
            </div>

            <div class="tab-pane fade h-100 d-flex flex-column p-3" id="my-notes">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0 text-main">สมุดจดบันทึก</h6>
                    <button class="btn btn-sm btn-link text-cyan p-0 text-decoration-none" style="font-size: 0.8rem;"><i class="bi bi-stars"></i> สรุปด้วย AI</button>
                </div>
                <textarea class="form-control flex-grow-1 border-secondary p-3 rounded-4 shadow-none mb-3" placeholder="พิมพ์ข้อความ หรือคัดลอกโค้ดมาวางที่นี่..." style="resize: none; font-size: 0.9rem; background: var(--lv-bg-dark); color: var(--lv-text-main);"></textarea>
                <button class="btn btn-outline-surface w-100 rounded-pill py-2">บันทึกข้อมูล</button>
            </div>
        </div>
    </aside>
</div>

<!-- ⚡ JavaScript ประจำหน้า Lesson Workspace -->
<script>
$(document).ready(function() {
    
    // 1. Mission Log Selection
    $('.mission-item').on('click', function(e) {
        if ($(this).hasClass('locked')) {
            e.preventDefault();
            return false;
        }
        
        e.preventDefault();

        $('.mission-item').removeClass('active');
        $('.badge-now').remove();
        
        $('.mission-item:not(.locked)').each(function() {
            const icon = $(this).find('i');
            icon.removeClass('bi-play-circle-fill text-cyan').addClass('bi-check-circle-fill text-success');
        });

        $(this).addClass('active');
        $(this).append('<span class="badge bg-primary ms-auto rounded-pill badge-now" style="font-size: 0.65rem;">NOW</span>');
        
        const activeIcon = $(this).find('i');
        activeIcon.removeClass('bi-check-circle-fill text-success').addClass('bi-play-circle-fill text-cyan');

        const newTitle = $(this).find('.mission-title-text').text();
        $('#breadcrumb-current-mission').text(newTitle);
        $('#main-lesson-title').text(newTitle);
        $('#ai-welcome-title').text('"' + newTitle + '"');

        $('.lesson-main-content').animate({ scrollTop: 0 }, 300);

        if ($(window).width() < 992) {
            $('#lesson-sidebar').removeClass('show');
        }
    });

    // 2. Mini Lab Code Submission Simulation
    $('#btn-run-lab').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        const $resultBox = $('#lab-result');
        const $terminalText = $('.terminal-text');
        
        $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> กำลังตรวจสอบ...');
        $resultBox.removeClass('d-none').addClass('border-secondary text-muted');
        $terminalText.html('Compiling mission code...<br>> Executing test cases...');

        setTimeout(function() {
            $btn.prop('disabled', false).html(originalText);
            $resultBox.removeClass('border-secondary text-muted').addClass('border-success text-success bg-success bg-opacity-10');
            $terminalText.html('<i class="bi bi-check-circle-fill"></i> Test Case 1: Passed<br><i class="bi bi-check-circle-fill"></i> Test Case 2: Passed<br><br><strong>🎉 Mission Accomplished!</strong>');
            
            showXpPopup($btn);

            const lockedMission = $('.mission-item.locked').first();
            if (lockedMission.length) {
                lockedMission.removeClass('locked').attr('title', '');
                lockedMission.find('i').removeClass('bi-lock-fill text-muted').addClass('bi-check-circle-fill text-success');
            }

        }, 1200);
    });

    function showXpPopup($element) {
        const offset = $element.offset();
        const $popup = $('<div class="position-absolute text-warning fw-bold fs-4" style="z-index: 9999; animation: floatUp 1.5s forwards;">+10 XP!</div>');
        $('body').append($popup);
        $popup.css({ top: offset.top - 20, left: offset.left + ($element.outerWidth() / 2) - 30 });
        setTimeout(() => { $popup.remove(); }, 1500);
    }

    // 3. AI Chat Interaction
    $('#aiChatForm').on('submit', function(e) {
        e.preventDefault();
        const $input = $('#ai-input');
        const text = $input.val().trim();
        if(!text) return;

        appendUserMessage(text);
        $input.val('');

        $input.prop('disabled', true);
        const loaderId = appendAiLoader();

        setTimeout(function() {
            $('#' + loaderId).remove();
            appendAiMessage('รับทราบคำสั่ง! สำหรับบทเรียนนี้ หากสงสัยสามารถระบุหมายเลขบรรทัดในโค้ดมาให้ผมช่วยอธิบายเจาะลึกได้เลยครับ 🚀');
            $input.prop('disabled', false).focus();
        }, 1000);
    });

    $('.btn-quick-prompt').on('click', function() {
        const prompt = $(this).text();
        $('#ai-input').val(prompt);
        $('#aiChatForm').submit();
    });

    function appendUserMessage(text) {
        const msgHtml = `<div class="d-flex gap-2 justify-content-end mb-1"><div class="chat-bubble-user p-2 px-3 shadow-sm" style="font-size: 0.85rem; max-width: 85%;">${escapeHtml(text)}</div></div>`;
        $('#chat-messages').append(msgHtml);
        scrollToBottom();
    }

    function appendAiMessage(text) {
        const msgHtml = `<div class="d-flex gap-2 mt-2 mb-1"><div class="chat-bubble-ai p-3 shadow-sm" style="font-size: 0.85rem; max-width: 90%;">${text}</div></div>`;
        $('#chat-messages').append(msgHtml);
        scrollToBottom();
    }

    function appendAiLoader() {
        const id = 'loader-' + Date.now();
        const msgHtml = `<div class="d-flex gap-2 mt-2 mb-1" id="${id}"><div class="chat-bubble-ai p-2 px-3 text-muted shadow-sm" style="font-size: 0.8rem;"><i class="bi bi-cpu spin me-1"></i> กำลังวิเคราะห์...</div></div>`;
        $('#chat-messages').append(msgHtml);
        scrollToBottom();
        return id;
    }

    function scrollToBottom() {
        const container = $('#chat-messages')[0];
        if(container) container.scrollTop = container.scrollHeight;
    }

    function escapeHtml(text) {
        return $('<div>').text(text).html();
    }
    
    $('<style>.spin { display: inline-block; animation: spin 1s linear infinite; } @keyframes spin { 100% { transform: rotate(360deg); } } @keyframes floatUp { 0% { opacity: 0; transform: translateY(10px); } 50% { opacity: 1; } 100% { opacity: 0; transform: translateY(-30px); } }</style>').appendTo('head');
});
</script>
