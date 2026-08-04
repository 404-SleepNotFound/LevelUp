<?php
$ROOT_PATH = '../';
// 🐞 ปลดล็อกบรรทัดนี้หากต้องการเปิดดู Error แบบละเอียด
// error_reporting(E_ALL); ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==========================================
// 1. จัดโครงสร้างข้อมูลผู้ใช้ (User Session Data)
// ==========================================
$sessionData = $_SESSION['user_login'] ?? null;
$navUser = [
    'user_id'  => $sessionData['user_id'] ?? ($_SESSION['user_id'] ?? 'GUEST'),
    'username' => $sessionData['name'] ?? ($_SESSION['username'] ?? 'ผู้ใช้งานทั่วไป'),
    'avatar'   => $sessionData['img'] ?? ($_SESSION['user_picture'] ?? 'https://ui-avatars.com/api/?name=Guest'),
    'level'    => (int)($sessionData['level'] ?? ($_SESSION['user_level'] ?? ($_SESSION['level'] ?? 1))),
    'streak'   => is_numeric($sessionData['streak'] ?? null) ? (int)$sessionData['streak'] : (is_numeric($_SESSION['login_streak'] ?? null) ? (int)$_SESSION['login_streak'] : 0),
    'exp'      => (int)($sessionData['exp'] ?? ($_SESSION['user_exp'] ?? ($_SESSION['exp'] ?? 0)))
];

$user_role = 'Intermediate';
$career_goal = 'Backend Developer';

// ==========================================
// 2. ตัวแปรจำลอง (Placeholder Variables) สำหรับรองรับฐานข้อมูล
// ==========================================

// 📊 สถิติทั่วไป (Quick Stats)
$stats = [
    'completed_courses' => 8,
    'total_courses'     => 20,
    'study_hours'       => 15
];

// 🤖 ข้อความสรุปและแนะนำจาก AI (AI Study Summary)
$ai_summary = [
    'topic'       => 'REST API',
    'description' => 'เมื่อวานคุณทำคะแนน Quiz ต่ำในเรื่อง REST API วันนี้ AI แนะนำให้ทบทวนบทนี้ก่อนขึ้นเรื่องใหม่ (ใช้เวลา ~15 นาที)'
];

// 📚 คอร์สที่กำลังเรียนอยู่ (Continue Learning)
$current_course = [
    'title'          => 'Python Fundamentals',
    'path'           => 'Backend Path',
    'current_lesson' => 'บทที่ 4: Data Structures & Algorithms',
    'progress'       => 70,
    'link'           => 'lesson.php'
];

// 💡 คอร์สแนะนำจาก AI (AI Recommendation)
$recommended_course = [
    'title'       => 'Docker Essentials',
    'description' => 'AI แนะนำคอร์สนี้เพราะคุณกำลังศึกษา Backend Dev และทำ Lab เรื่อง API สำเร็จเมื่อวาน',
    'link'        => 'course-detail.php'
];

// 🎯 ความคืบหน้าเป้าหมายอาชีพ (Career Goal Progress)
$goal_progress = [
    'percentage'         => 65,
    'completed_courses'  => 12,
    'remaining_courses'  => 5,
    'remaining_projects' => 2
];

// 🗺️ แผนการเรียน (Learning Path / Roadmap)
$learning_path = [
    [
        'title'  => 'Python Basics',
        'desc'   => 'เรียนจบแล้วเมื่อ 3 วันก่อน',
        'status' => 'completed'
    ],
    [
        'title'  => 'Object-Oriented Programming (OOP)',
        'desc'   => 'เรียนจบแล้วเมื่อวาน',
        'status' => 'completed'
    ],
    [
        'title'  => 'Data Structures (ปัจจุบัน)',
        'desc'   => 'เหลือเวลาประมาณ 2 ชั่วโมง',
        'status' => 'current'
    ]
];

// 🎯 ภารกิจประจำวัน (Daily Missions)
$daily_missions = [
    [
        'id'        => 1,
        'title'     => 'เรียนจบ 1 บทเรียน',
        'completed' => true,
        'disabled'  => true
    ],
    [
        'id'        => 2,
        'title'     => 'ทำ Quiz ให้ผ่าน 80%',
        'completed' => false,
        'disabled'  => false
    ],
    [
        'id'        => 3,
        'title'     => 'ทำ Coding Lab 1 ครั้ง',
        'completed' => false,
        'disabled'  => false
    ]
];
$mission_reward_xp = 50;

// 🕒 กิจกรรมล่าสุด (Recent Activities)
$recent_activities = [
    [
        'title' => 'สอบผ่าน Quiz: OOP Basics',
        'time'  => '2 ชั่วโมงที่แล้ว',
        'icon'  => 'bi-patch-check',
        'color' => 'success'
    ],
    [
        'title' => 'AI วิเคราะห์ Code ใน Lab',
        'time'  => 'เมื่อวาน',
        'icon'  => 'bi-robot',
        'color' => 'primary'
    ]
];

include '../includes/header.php';
?>

<style>
    /* 🌙 Dark Mode Theme (โหมดมืด - ค่าเริ่มต้น) */
    :root, html[data-theme="dark"] {
        --lv-bg-dark: #090d1a;
        --lv-card-bg: rgba(20, 27, 45, 0.7);
        --lv-card-hover-bg: rgba(28, 38, 62, 0.85);
        --lv-border-glow: rgba(99, 102, 241, 0.15);
        --lv-border-hover: rgba(99, 102, 241, 0.4);
        
        --lv-primary: #6366F1;
        --lv-secondary: #8B5CF6;
        --lv-success: #10B981;
        --lv-warning: #F59E0B;
        --lv-info: #06B6D4;
        
        --lv-text-main: #f1f5f9;
        --lv-text-muted: #94a3b8;
        --lv-text-completed: rgba(241, 245, 249, 0.45);
        --lv-heading: #ffffff;
        
        --lv-grid-line: rgba(255, 255, 255, 0.01);
        --lv-btn-surface-bg: rgba(255, 255, 255, 0.02);
        --lv-btn-surface-border: rgba(255, 255, 255, 0.08);
        --lv-btn-surface-color: #cbd5e1;
        --lv-btn-surface-hover: rgba(255, 255, 255, 0.07);
        
        --lv-ai-card-bg: linear-gradient(135deg, rgba(23, 23, 53, 0.75) 0%, rgba(14, 16, 35, 0.9) 100%);
        --lv-ai-msg-bg: rgba(99, 102, 241, 0.08);
        --lv-ai-msg-border: rgba(99, 102, 241, 0.2);
        --lv-mission-bg: rgba(255, 255, 255, 0.02);
        --lv-mission-border: rgba(255, 255, 255, 0.04);
        --lv-progress-bg: rgba(255, 255, 255, 0.06);
    }

    /* ☀️ Light Mode Theme Overrides (โหมดสว่าง) */
    html[data-theme="light"] {
        --lv-bg-dark: #f8fafc;
        --lv-card-bg: #ffffff;
        --lv-card-hover-bg: #ffffff;
        --lv-border-glow: rgba(99, 102, 241, 0.12);
        --lv-border-hover: rgba(99, 102, 241, 0.35);
        
        --lv-text-main: #1e293b;
        --lv-text-muted: #64748b;
        --lv-text-completed: #94a3b8;
        --lv-heading: #0f172a;
        
        --lv-grid-line: rgba(0, 0, 0, 0.03);
        --lv-btn-surface-bg: #f1f5f9;
        --lv-btn-surface-border: rgba(0, 0, 0, 0.08);
        --lv-btn-surface-color: #334155;
        --lv-btn-surface-hover: #e2e8f0;
        
        --lv-ai-card-bg: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        --lv-ai-msg-bg: rgba(255, 255, 255, 0.85);
        --lv-ai-msg-border: rgba(99, 102, 241, 0.25);
        --lv-mission-bg: rgba(0, 0, 0, 0.02);
        --lv-mission-border: rgba(0, 0, 0, 0.06);
        --lv-progress-bg: rgba(0, 0, 0, 0.08);
    }

    body {
        background-color: var(--lv-bg-dark) !important;
        font-family: 'Outfit', 'Kanit', sans-serif;
        color: var(--lv-text-main);
        overflow-x: hidden;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .dashboard-section {
        background: radial-gradient(circle at 15% 15%, rgba(99, 102, 241, 0.08) 0%, transparent 45%),
                    radial-gradient(circle at 85% 75%, rgba(139, 92, 246, 0.05) 0%, transparent 50%),
                    linear-gradient(var(--lv-grid-line) 1px, transparent 1px),
                    linear-gradient(90deg, var(--lv-grid-line) 1px, transparent 1px);
        background-size: 100% 100%, 100% 100%, 40px 40px, 40px 40px;
        min-height: 100vh;
    }

    .dashboard-card {
        background: var(--lv-card-bg);
        border: 1px solid var(--lv-border-glow) !important;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.08);
        border-radius: 16px !important;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    .dashboard-card:hover {
        border-color: var(--lv-border-hover) !important;
        transform: translateY(-4px);
        box-shadow: 0 18px 35px -5px rgba(99, 102, 241, 0.15);
        background: var(--lv-card-hover-bg);
    }

    .dashboard-section .text-white {
        color: var(--lv-heading) !important;
    }

    .welcome-outer-wrapper {
        display: flex;
        flex-direction: column;
        height: 100%;
        width: 100%;
    }
    .welcome-outer-wrapper > div,
    .welcome-outer-wrapper .card,
    .welcome-outer-wrapper .dashboard-card {
        height: 100% !important;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        margin-bottom: 0 !important;
    }

    .ai-insight-card {
        background: var(--lv-ai-card-bg);
        border: 1px solid rgba(99, 102, 241, 0.3) !important;
    }
    
    .ai-message-box {
        background: var(--lv-ai-msg-bg);
        border: 1px solid var(--lv-ai-msg-border);
        backdrop-filter: blur(10px);
    }

    html[data-theme="light"] .ai-message-box p { color: #334155 !important; }
    html[data-theme="light"] .ai-message-box h6 { color: #1e1b4b !important; }
    
    .ai-glow-bg {
        position: absolute;
        top: -60%; right: -15%; width: 320px; height: 320px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.25) 0%, transparent 70%);
        filter: blur(30px);
        pointer-events: none;
        z-index: 0;
    }

    .stat-icon {
        width: 48px; height: 48px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; border-radius: 12px;
        flex-shrink: 0;
    }
    
    .bg-primary-subtle { background: rgba(99, 102, 241, 0.15) !important; color: #6366f1 !important; border: 1px solid rgba(99, 102, 241, 0.2); }
    .bg-info-subtle { background: rgba(6, 182, 212, 0.15) !important; color: #06b6d4 !important; border: 1px solid rgba(6, 182, 212, 0.2); }
    .bg-warning-subtle { background: rgba(245, 158, 11, 0.15) !important; color: #f59e0b !important; border: 1px solid rgba(245, 158, 11, 0.2); }
    .bg-success-subtle { background: rgba(16, 185, 129, 0.15) !important; color: #10b981 !important; border: 1px solid rgba(16, 185, 129, 0.2); }

    .progress {
        background-color: var(--lv-progress-bg) !important;
        border-radius: 50px;
        overflow: hidden;
    }
    .progress-bar {
        background: linear-gradient(90deg, var(--lv-primary) 0%, var(--lv-secondary) 100%) !important;
        box-shadow: 0 0 12px rgba(99, 102, 241, 0.4);
    }

    .roadmap-container {
        position: relative;
        padding-left: 28px;
    }
    .roadmap-container::before {
        content: ''; position: absolute; left: 8px; top: 12px; bottom: 12px;
        width: 2px; background: rgba(148, 163, 184, 0.2);
    }
    .roadmap-step { position: relative; padding-bottom: 1.8rem; }
    .roadmap-step:last-child { padding-bottom: 0; }
    .roadmap-step .step-icon {
        position: absolute; left: -28px; top: 2px;
        width: 18px; height: 18px; border-radius: 50%;
        background: #151b2e; border: 2px solid #475569;
        z-index: 2; display: flex; align-items: center; justify-content: center;
        font-size: 10px; color: #fff;
    }
    .roadmap-step.completed::before {
        content: ''; position: absolute; left: -27px; top: 20px; bottom: 0;
        width: 2px; background: var(--lv-success); z-index: 1;
    }
    .roadmap-step.completed .step-icon { background: var(--lv-success); border-color: var(--lv-success); }
    .roadmap-step.current .step-icon {
        background: var(--lv-primary); border-color: var(--lv-primary);
        animation: pulseGlow 2s infinite alternate;
    }

    .mission-item {
        background: var(--lv-mission-bg);
        border: 1px solid var(--lv-mission-border);
        padding: 12px 16px;
        border-radius: 12px;
        display: flex; align-items: center;
    }
    .mission-checkbox {
        width: 1.25rem; height: 1.25rem;
        background-color: var(--lv-btn-surface-bg); 
        border: 2px solid var(--lv-btn-surface-border);
        cursor: pointer;
    }
    .mission-checkbox:checked {
        background-color: var(--lv-success) !important;
        border-color: var(--lv-success) !important;
    }
    .mission-text-completed { text-decoration: line-through; color: var(--lv-text-completed) !important; }

    .btn-premium {
        background: linear-gradient(135deg, var(--lv-primary) 0%, #4f46e5 100%);
        border: none; color: white !important; font-weight: 600;
        border-radius: 12px;
    }
    .btn-premium:hover { color: white !important; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(99, 102, 241, 0.45); }
    
    .btn-outline-surface {
        background: var(--lv-btn-surface-bg);
        border: 1px solid var(--lv-btn-surface-border);
        color: var(--lv-btn-surface-color);
        border-radius: 12px;
    }
    .btn-outline-surface:hover { 
        background: var(--lv-btn-surface-hover); 
        color: var(--lv-heading); 
    }

    @keyframes pulseGlow {
        0% { box-shadow: 0 0 6px rgba(99, 102, 241, 0.4); }
        100% { box-shadow: 0 0 16px rgba(99, 102, 241, 0.9); }
    }

    .ajax-loading-overlay {
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(9, 13, 26, 0.5); display: flex; align-items: center; justify-content: center;
        border-radius: 12px; opacity: 0; pointer-events: none;
    }
    .mission-item.is-loading .ajax-loading-overlay { opacity: 1; pointer-events: auto; }

    /* 📱 Responsive Adjustments สำหรับมือถือ */
    @media (max-width: 576px) {
        .stat-icon {
            width: 38px !important;
            height: 38px !important;
            font-size: 1.05rem !important;
            border-radius: 10px !important;
        }
        .stat-card-title {
            font-size: 0.725rem !important;
            line-height: 1.2 !important;
        }
        .stat-card-value {
            font-size: 1rem !important;
        }
        .dashboard-card {
            border-radius: 14px !important;
        }
    }
</style>

<section class="dashboard-section py-3 py-lg-5">
    <div class="container-xl">
        
        <!-- 1. ROW ด้านบน: Welcome Card และ AI Summary -->
        <div class="row g-3 g-lg-4 mb-3 mb-lg-4 align-items-stretch">
            <div class="col-12 col-lg-7 d-flex">
                <div class="welcome-outer-wrapper">
                    <?php
$ROOT_PATH = '../';
$welcomeFile = __DIR__ . '/../includes/welcome-header.php';
                    if (file_exists($welcomeFile)) {
                        try {
                            include $welcomeFile;
                        } catch (Throwable $e) {
                            echo '<div class="dashboard-card p-4 d-flex flex-column justify-content-center">
                                    <h4 class="fw-bold text-white mb-2">ยินดีต้อนรับกลับมา, ' . htmlspecialchars($navUser['username']) . '! 👋</h4>
                                    <p class="mb-0" style="color: var(--lv-text-muted);">พร้อมลุยภารกิจพัฒนาทักษะประจำวันนี้แล้วหรือยัง?</p>
                                  </div>';
                        }
                    } else {
                        echo '<div class="dashboard-card p-4 d-flex flex-column justify-content-center">
                                <h4 class="fw-bold text-white mb-2">ยินดีต้อนรับกลับมา, ' . htmlspecialchars($navUser['username']) . '! 👋</h4>
                                <p class="mb-0" style="color: var(--lv-text-muted);">พร้อมลุยภารกิจพัฒนาทักษะประจำวันนี้แล้วหรือยัง?</p>
                              </div>';
                    }
                    ?>
                </div>
            </div>
            
            <div class="col-12 col-lg-5 d-flex">
                <div class="dashboard-card ai-insight-card p-3 p-md-4 h-100 w-100 position-relative overflow-hidden">
                    <div class="position-relative z-1 d-flex h-100 flex-column justify-content-between">
                        <div class="ai-message-box p-3 rounded-4 d-flex gap-3 align-items-start h-100">
                            <div class="icon-box text-info fs-4 flex-shrink-0" style="text-shadow: 0 0 10px rgba(34,211,238,0.4);"><i class="bi bi-stars"></i></div>
                            <div>
                                <h6 class="fw-bold mb-1">AI Study Summary</h6>
                                <p class="mb-0 fs-sm" style="line-height: 1.5;"><?= htmlspecialchars($ai_summary['description']) ?></p>
                            </div>
                        </div>
                        <div class="mt-3 px-1 d-flex justify-content-between align-items-center fs-sm">
                            <p class="mb-0 fs-xs" style="color: var(--lv-text-muted);">เป้าหมาย: <span class="text-white fw-medium"><?= htmlspecialchars($career_goal) ?></span></p>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 fs-xs">ระดับ <?= htmlspecialchars($user_role) ?></span>
                        </div>
                    </div>
                    <div class="ai-glow-bg"></div>
                </div>
            </div>
        </div>

        <!-- 2. Quick Stats Hub -->
        <div class="row g-2 g-md-3 mb-3 mb-lg-4">
            <div class="col-6 col-md-3">
                <div class="dashboard-card p-2.5 p-sm-3 d-flex align-items-center gap-2 gap-sm-3 h-100">
                    <div class="stat-icon bg-primary-subtle"><i class="bi bi-journal-check"></i></div>
                    <div class="overflow-hidden">
                        <p class="stat-card-title mb-0 text-truncate" style="color: var(--lv-text-muted);">คอร์สที่จบ</p>
                        <h5 class="stat-card-value fw-bold mb-0 text-white"><?= (int)$stats['completed_courses'] ?> <span class="fs-xs fw-normal" style="color: var(--lv-text-muted);">/ <?= (int)$stats['total_courses'] ?></span></h5>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="dashboard-card p-2.5 p-sm-3 d-flex align-items-center gap-2 gap-sm-3 h-100">
                    <div class="stat-icon bg-info-subtle"><i class="bi bi-clock-history"></i></div>
                    <div class="overflow-hidden">
                        <p class="stat-card-title mb-0 text-truncate" style="color: var(--lv-text-muted);">เวลาเรียน</p>
                        <h5 class="stat-card-value fw-bold mb-0 text-white"><?= (int)$stats['study_hours'] ?><span class="fs-xs fw-normal" style="color: var(--lv-text-muted);"> ชม.</span></h5>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="dashboard-card p-2.5 p-sm-3 d-flex align-items-center gap-2 gap-sm-3 h-100">
                    <div class="stat-icon bg-warning-subtle"><i class="bi bi-fire"></i></div>
                    <div class="overflow-hidden">
                        <p class="stat-card-title mb-0 text-truncate" style="color: var(--lv-text-muted);">Daily Streak</p>
                        <h5 class="stat-card-value fw-bold mb-0 text-white"><?= (int)$navUser['streak'] ?><span class="fs-xs fw-normal" style="color: var(--lv-text-muted);"> วัน</span></h5>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="dashboard-card p-2.5 p-sm-3 d-flex align-items-center gap-2 gap-sm-3 h-100">
                    <div class="stat-icon bg-success-subtle"><i class="bi bi-trophy"></i></div>
                    <div class="overflow-hidden">
                        <p class="stat-card-title mb-0 text-truncate" style="color: var(--lv-text-muted);">XP / LV.<?= (int)$navUser['level'] ?></p>
                        <h5 class="stat-card-value fw-bold mb-0 text-white"><?= number_format((int)$navUser['exp']) ?></h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. ROW กลาง: กำลังเรียน vs เมนูด่วน -->
        <div class="row g-3 g-lg-4 mb-3 mb-lg-4 align-items-stretch">
            <div class="col-12 col-xl-8 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-2" style="min-height: 27px;">
                    <h5 class="fw-bold mb-0 text-white fs-6 fs-md-5">กำลังเรียน (Continue Learning)</h5>
                    <a href="courses.php" class="fs-sm text-decoration-none fw-medium" style="color: var(--lv-primary);">ดูทั้งหมด</a>
                </div>
                
                <div class="dashboard-card p-3 p-md-4 flex-grow-1 w-100 d-flex flex-column justify-content-center course-continue-card" style="cursor: pointer;" onclick="location.href='<?= htmlspecialchars($current_course['link']) ?>';">
                    <div class="row align-items-center g-3">
                        <div class="col-3 col-md-3">
                            <div class="position-relative rounded-3 overflow-hidden d-flex align-items-center justify-content-center" style="height: 78px; background: linear-gradient(135deg, #3776AB 0%, #1e415e 100%);">
                                <i class="bi bi-filetype-py text-white" style="font-size: 2.2rem;"></i>
                            </div>
                        </div>
                        <div class="col-9 col-md-6 ps-2 ps-md-3">
                            <span class="badge mb-1.5" style="background: rgba(99,102,241,0.15); color: var(--lv-primary); border: 1px solid rgba(99,102,241,0.3); font-weight: 500; font-size: 0.7rem;"><?= htmlspecialchars($current_course['path']) ?></span>
                            <h6 class="fw-bold mb-1 text-white fs-6"><?= htmlspecialchars($current_course['title']) ?></h6>
                            <p class="fs-xs mb-2 text-truncate" style="color: var(--lv-text-muted);"><?= htmlspecialchars($current_course['current_lesson']) ?></p>
                            <div class="d-flex align-items-center gap-2 gap-md-3">
                                <div class="progress flex-grow-1" style="height: 6px;">
                                    <div class="progress-bar" style="width: <?= (int)$current_course['progress'] ?>%;"></div>
                                </div>
                                <span class="fs-xs fw-bold text-white"><?= (int)$current_course['progress'] ?>%</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 text-md-end mt-2 mt-md-0">
                            <button class="btn btn-premium w-100 py-2 fs-sm">เรียนต่อ <i class="bi bi-play-fill ms-1"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-2" style="min-height: 27px;">
                    <h5 class="fw-bold mb-0 text-white fs-6 fs-md-5">เมนูด่วน (Quick Access)</h5>
                </div>
                
                <div class="dashboard-card p-3 p-md-4 flex-grow-1 w-100 d-flex flex-column justify-content-center">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="../ai/ai-chat.php" class="btn btn-outline-surface w-100 py-2.5 py-md-3 text-start d-flex flex-column gap-1.5 text-decoration-none">
                                <i class="bi bi-robot text-primary fs-5"></i>
                                <span class="fs-xs fw-semibold">AI Tutor</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="coding-lab.php" class="btn btn-outline-surface w-100 py-2.5 py-md-3 text-start d-flex flex-column gap-1.5 text-decoration-none">
                                <i class="bi bi-code-square text-success fs-5"></i>
                                <span class="fs-xs fw-semibold">Coding Lab</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="quiz.php" class="btn btn-outline-surface w-100 py-2.5 py-md-3 text-start d-flex flex-column gap-1.5 text-decoration-none">
                                <i class="bi bi-ui-checks text-warning fs-5"></i>
                                <span class="fs-xs fw-semibold">ทำ Quiz</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="virtual-lab.php" class="btn btn-outline-surface w-100 py-2.5 py-md-3 text-start d-flex flex-column gap-1.5 text-decoration-none">
                                <i class="bi bi-pc-display text-info fs-5"></i>
                                <span class="fs-xs fw-semibold">Virtual Lab</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. ROW ล่างสุด -->
        <div class="row g-3 g-lg-4">
            <div class="col-12 col-xl-8">
                <div class="row g-3 g-lg-4 mb-3 mb-lg-4">
                    <div class="col-12 col-md-6">
                        <div class="dashboard-card p-4 h-100 position-relative overflow-hidden d-flex flex-column justify-content-between">
                            <div>
                                <div class="badge position-absolute top-0 end-0 m-3" style="background: rgba(6,182,212,0.15); color: var(--lv-info); border: 1px solid rgba(6,182,212,0.25); font-weight: 500;"><i class="bi bi-stars"></i> AI แนะนำ</div>
                                <h6 class="fw-bold mb-3" style="color: var(--lv-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">คอร์สแนะนำสำหรับคุณ</h6>
                                <h5 class="fw-bold text-white mb-2"><?= htmlspecialchars($recommended_course['title']) ?></h5>
                                <p class="fs-sm mb-4" style="color: var(--lv-text-muted); line-height: 1.6;"><?= htmlspecialchars($recommended_course['description']) ?></p>
                            </div>
                            <div>
                                <a href="<?= htmlspecialchars($recommended_course['link']) ?>" class="btn btn-outline-surface btn-sm px-3 py-2 fw-medium text-decoration-none">ดูรายละเอียด</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <div class="dashboard-card p-4 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="fw-bold mb-3" style="color: var(--lv-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">ความคืบหน้าเป้าหมาย</h6>
                                <div class="d-flex justify-content-between mb-1.5">
                                    <span class="fs-sm fw-medium text-white"><?= htmlspecialchars($career_goal) ?></span>
                                    <span class="fs-sm fw-bold" style="color: var(--lv-primary);"><?= (int)$goal_progress['percentage'] ?>%</span>
                                </div>
                                <div class="progress mb-3.5" style="height: 8px;">
                                    <div class="progress-bar" style="width: <?= (int)$goal_progress['percentage'] ?>%;"></div>
                                </div>
                            </div>
                            <ul class="list-unstyled fs-sm mb-0" style="color: var(--lv-text-muted); line-height: 2;">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> <span class="text-white">จบแล้ว <?= (int)$goal_progress['completed_courses'] ?> คอร์ส</span></li>
                                <li><i class="bi bi-circle me-2" style="color: var(--lv-text-muted);"></i> เหลืออีก <?= (int)$goal_progress['remaining_courses'] ?> คอร์สหลัก</li>
                                <li><i class="bi bi-circle me-2" style="color: var(--lv-text-muted);"></i> เหลืออีก <?= (int)$goal_progress['remaining_projects'] ?> Real-world Projects</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold mb-3 text-white fs-6 fs-md-5">แผนการเรียนของคุณ (Learning Path)</h5>
                <div class="dashboard-card p-3 p-md-4">
                    <div class="roadmap-container">
                        <?php
$ROOT_PATH = '../';
foreach ($learning_path as $step): ?>
                            <div class="roadmap-step <?= htmlspecialchars($step['status']) ?>">
                                <div class="step-icon">
                                    <i class="bi <?= $step['status'] === 'completed' ? 'bi-check' : ($step['status'] === 'current' ? 'bi-play-fill' : 'bi-circle') ?>"></i>
                                </div>
                                <div class="step-content ms-2">
                                    <h6 class="fw-bold mb-1 fs-6 <?= $step['status'] === 'current' ? '' : 'text-white' ?>" <?= $step['status'] === 'current' ? 'style="color: var(--lv-primary);"' : '' ?>>
                                        <?= htmlspecialchars($step['title']) ?>
                                    </h6>
                                    <p class="fs-sm mb-0" style="color: var(--lv-text-muted);"><?= htmlspecialchars($step['desc']) ?></p>
                                </div>
                            </div>
                        <?php
$ROOT_PATH = '../';
endforeach; ?>
                    </div>
                    
                    <a href="learning-path.php" class="btn btn-outline-surface w-100 mt-4 py-2.5 fw-medium fs-sm text-center d-block text-decoration-none">
                        เปิด Roadmap ฉบับเต็ม
                    </a>
                </div>
            </div>

            <div class="col-12 col-xl-4 d-flex flex-column gap-3 gap-lg-4">
                <div class="dashboard-card p-3 p-md-4 flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 text-white" style="text-transform: uppercase; letter-spacing: 0.5px;">ภารกิจวันนี้</h6>
                        <span class="badge" style="background: rgba(245,158,11,0.15); color: var(--lv-warning); border: 1px solid rgba(245,158,11,0.25); font-weight: 500;">รับ <?= (int)$mission_reward_xp ?> XP</span>
                    </div>
                    <div class="mission-list d-flex flex-column gap-2">
                        <?php
$ROOT_PATH = '../';
foreach ($daily_missions as $mission): ?>
                            <div class="mission-item position-relative">
                                <input type="checkbox" 
                                       class="form-check-input mission-checkbox rounded-circle me-3" 
                                       data-id="<?= (int)$mission['id'] ?>" 
                                       <?= $mission['completed'] ? 'checked' : '' ?> 
                                       <?= $mission['disabled'] ? 'disabled' : '' ?>>
                                <span class="fs-sm fw-medium <?= $mission['completed'] ? 'mission-text-completed' : 'text-white' ?>">
                                    <?= htmlspecialchars($mission['title']) ?>
                                </span>
                                <?php
$ROOT_PATH = '../';
if (!$mission['disabled']): ?>
                                    <div class="ajax-loading-overlay"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></div>
                                <?php
$ROOT_PATH = '../';
endif; ?>
                            </div>
                        <?php
$ROOT_PATH = '../';
endforeach; ?>
                    </div>
                </div>

                <div class="dashboard-card p-3 p-md-4 flex-grow-1">
                    <h6 class="fw-bold mb-3 text-white" style="text-transform: uppercase; letter-spacing: 0.5px;">กิจกรรมล่าสุด</h6>
                    <div class="activity-timeline">
                        <?php
$ROOT_PATH = '../';
foreach ($recent_activities as $activity): ?>
                            <div class="activity-item d-flex gap-3 mb-3.5">
                                <div class="activity-icon bg-<?= htmlspecialchars($activity['color']) ?>-subtle text-<?= htmlspecialchars($activity['color']) ?> rounded-circle d-flex align-items-center justify-content-center" style="width:34px; height:34px; flex-shrink: 0;">
                                    <i class="bi <?= htmlspecialchars($activity['icon']) ?>"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fs-sm fw-semibold text-white"><?= htmlspecialchars($activity['title']) ?></p>
                                    <span class="fs-xs" style="color: var(--lv-text-muted);"><?= htmlspecialchars($activity['time']) ?></span>
                                </div>
                            </div>
                        <?php
$ROOT_PATH = '../';
endforeach; ?>
                    </div>
                    <!-- 🔗 ปรับลิงก์เป้าหมายเป็น learning-history.php แล้ว -->
                    <a href="learning-history.php" class="btn btn-link fs-sm text-decoration-none w-100 text-center mt-3.5 fw-medium" style="color: var(--lv-primary);">ดูประวัติทั้งหมด</a>
                </div>
            </div>
        </div>
        
    </div>
</section>

<audio id="successSound" src="https://assets.mixkit.co/active_storage/sfx/2013/2013-preview.mp3" preload="auto"></audio>

<script>
$(document).ready(function() {
    $('.mission-checkbox').not(':disabled').on('change', function() {
        var $checkbox = $(this);
        var $parentContainer = $checkbox.closest('.mission-item');
        var missionId = $checkbox.data('id');
        var isChecked = $checkbox.is(':checked');
        
        $checkbox.prop('disabled', true);
        $parentContainer.addClass('is-loading');
        
        $.ajax({
            url: 'ajax/update_mission.php',
            method: 'POST',
            data: { id: missionId, status: isChecked ? 1 : 0 },
            dataType: 'json',
            success: function(response) {
                handleMissionFeedback($checkbox, $parentContainer, isChecked);
            },
            error: function() {
                handleMissionFeedback($checkbox, $parentContainer, isChecked);
            }
        });
    });

    function handleMissionFeedback($el, $parent, isChecked) {
        $el.prop('disabled', false);
        $parent.removeClass('is-loading');
        if(isChecked) {
            $el.siblings('span').addClass('mission-text-completed').removeClass('text-white');
            var audio = document.getElementById('successSound');
            if (audio) {
                audio.currentTime = 0;
                audio.play().catch(function(e){ console.log('Audio contextual play issue'); });
            }
        } else {
            $el.siblings('span').removeClass('mission-text-completed').addClass('text-white');
        }
    }
});
</script>

<?php
$ROOT_PATH = '../';
include '../includes/footer.php'; ?>

