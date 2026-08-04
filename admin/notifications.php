<?php
$ROOT_PATH = '../';
// เริ่มต้น Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------------------
// [PHP] DATA PREPARATION (Database Placeholders)
// ---------------------------------------------------------
$notifyStats = [
    'total'  => 12,
    'unread' => 5
];

$aiDigest = [
    'title'    => 'System Directives',
    'subtitle' => 'AI ประมวลผลเส้นทางการเรียนรู้ของคุณ นี่คือภารกิจเร่งด่วนสำหรับวันนี้:',
    'tasks'    => [
        ['icon' => 'bi-play-circle-fill text-info', 'text' => 'เข้าเรียน 2 บทเรียน (Data Structure)', 'reward' => '+50 XP'],
        ['icon' => 'bi-terminal-fill text-success', 'text' => 'ผ่านด่าน Docker Basics Lab', 'reward' => '+100 XP'],
        ['icon' => 'bi-exclamation-triangle-fill text-warning', 'text' => 'แบบทดสอบ SQL Query กำลังจะหมดเวลา!', 'reward' => 'URGENT']
    ]
];

$pageTitle = "Mission Logs | LevelUp";
include '../includes/header.php';
?>

<!-- ดึง CSS เดิม และเขียน Override เพื่อให้เข้ากับ Gamified Design System -->
<link rel="stylesheet" href="../assets/css/notifications.css">
<style>
    /* 🌙 Dark Mode / Sci-Fi Theme (Default) */
    :root, html[data-theme="dark"] {
        --lv-bg-dark: #0b0f19;
        --lv-card-bg: rgba(17, 24, 39, 0.7);
        --lv-border-glow: rgba(99, 102, 241, 0.2);
        --lv-text-main: #f1f5f9;
        --lv-text-muted: #94a3b8;
        
        /* Gamified Custom Variables */
        --neon-cyan: #00E5FF;
        --neon-purple: #B026FF;
        --neon-warning: #FFD600;
        --inner-box-bg: rgba(0, 0, 0, 0.3);
        --shadow-glow: rgba(0, 229, 255, 0.3);
    }

    /* ☀️ Light Mode Theme */
    html[data-theme="light"] {
        --lv-bg-dark: #f8fafc;
        --lv-card-bg: rgba(255, 255, 255, 0.85);
        --lv-border-glow: rgba(99, 102, 241, 0.15);
        --lv-text-main: #0f172a;
        --lv-text-muted: #64748b;

        /* Gamified Custom Variables */
        --neon-cyan: #0284C7;
        --neon-purple: #7C3AED;
        --neon-warning: #D97706;
        --inner-box-bg: rgba(255, 255, 255, 0.6);
        --shadow-glow: rgba(2, 132, 199, 0.2);
    }

    /* Base Layout Reset */
    html, body {
        margin: 0 !important;
        border: none !important;
        min-height: 100vh;
        background-color: var(--lv-bg-dark) !important;
        color: var(--lv-text-main) !important;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .dark-theme-wrapper {
        background-color: transparent !important;
        background-image: 
            radial-gradient(circle at 80% 20%, rgba(0, 229, 255, 0.04), transparent 40%),
            radial-gradient(circle at 20% 80%, rgba(176, 38, 255, 0.04), transparent 40%);
        padding-top: 90px !important; /* ป้องกัน Header ทับเนื้อหา */
    }

    /* Typography (บังคับสีไม่ให้กลืนกับพื้นหลัง) */
    .text-main { color: var(--lv-text-main) !important; transition: color 0.3s ease; }
    .text-muted { color: var(--lv-text-muted) !important; transition: color 0.3s ease; }
    .glow-text { color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0, 229, 255, 0.4); }

    /* Gamified Cards (Mission Control) */
    .gamified-card {
        background: var(--lv-card-bg) !important;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--lv-border-glow) !important;
        box-shadow: 0 8px 32px 0 rgba(0,0,0,0.1) !important;
        transition: all 0.3s ease;
    }
    
    /* กล่องย่อยด้านใน */
    .inner-box {
        background: var(--inner-box-bg) !important;
        border: 1px solid var(--lv-border-glow) !important;
        transition: all 0.3s ease;
    }

    /* Search Bar */
    .sci-fi-input {
        background: var(--inner-box-bg) !important;
        border: 1px solid var(--lv-border-glow) !important;
        color: var(--lv-text-main) !important;
        transition: all 0.3s ease;
    }
    .sci-fi-input:focus { border-color: var(--neon-cyan) !important; box-shadow: 0 0 10px var(--shadow-glow) !important; }
    html[data-theme="light"] .sci-fi-input { color: #0f172a !important; }

    /* Filters Link */
    .filter-link { transition: all 0.2s ease; border-radius: 8px; padding: 8px 12px; margin-left: -12px; margin-right: -12px; }
    .filter-link:hover { background: rgba(150, 150, 150, 0.1); transform: translateX(5px); }
    html[data-theme="light"] .filter-link:hover { background: rgba(0, 0, 0, 0.05); color: #0f172a; }
    .filter-link.active { background: rgba(0, 229, 255, 0.1); border-left: 3px solid var(--neon-cyan); }
    .filter-link.active span:first-child { color: var(--neon-cyan) !important; font-weight: bold; }

    /* AI Digest Banner */
    .ai-digest-banner {
        background: linear-gradient(135deg, rgba(176, 38, 255, 0.15) 0%, rgba(0, 229, 255, 0.15) 100%) !important;
        border: 1px solid var(--lv-border-glow) !important;
        box-shadow: 0 0 20px rgba(0, 229, 255, 0.05) !important;
    }
    
    .ai-pulse-icon {
        animation: pulse-glow 2s infinite;
    }
    @keyframes pulse-glow {
        0% { filter: drop-shadow(0 0 5px var(--neon-warning)); transform: scale(1); }
        50% { filter: drop-shadow(0 0 15px var(--neon-warning)); transform: scale(1.1); }
        100% { filter: drop-shadow(0 0 5px var(--neon-warning)); transform: scale(1); }
    }

    /* Skeleton Loading Gamified */
    .skeleton-item {
        background: var(--lv-card-bg) !important;
        position: relative;
        overflow: hidden;
        border: 1px solid var(--lv-border-glow);
        transition: all 0.3s ease;
    }
    .skeleton-item::after {
        content: "";
        position: absolute;
        top: 0; left: -100%;
        width: 50%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(0, 229, 255, 0.1), transparent);
        animation: skeleton-sweep 1.5s infinite linear;
    }
    .skeleton-avatar { background: rgba(150,150,150,0.2); }
    .skeleton-text { background: rgba(150,150,150,0.2); height: 12px; border-radius: 4px; }
    html[data-theme="light"] .skeleton-avatar, html[data-theme="light"] .skeleton-text { background: rgba(0,0,0,0.08); }

    /* Buttons */
    .btn-glow-cyan {
        background: var(--neon-cyan);
        color: #000 !important;
        font-weight: 700;
        border: none;
        box-shadow: 0 0 10px var(--shadow-glow);
    }
    .btn-glow-cyan:hover { background: #fff; box-shadow: 0 0 20px var(--shadow-glow); transform: scale(1.02); }
    html[data-theme="light"] .btn-glow-cyan { color: #FFF !important; }
</style>

<div class="container-fluid pb-5 dark-theme-wrapper">
    <div class="container">
        
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-end mb-4 border-bottom border-secondary border-opacity-25 pb-3">
            <div>
                <h2 class="fw-bold mb-1 glow-text text-uppercase" style="letter-spacing: 1px;">
                    <i class="bi bi-hdd-network"></i> Mission Logs
                </h2>
                <p class="text-muted mb-0 fs-sm">ศูนย์รวมการแจ้งเตือนและภารกิจที่ระบบประมวลผลให้คุณ</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 text-main hover-cyan" id="btnMarkAllRead">
                    <i class="bi bi-check-all"></i> Clear Logs
                </button>
                <button class="btn btn-outline-secondary btn-sm rounded-circle text-main" title="Notification Settings">
                    <i class="bi bi-sliders"></i>
                </button>
            </div>
        </div>

        <div class="row g-4">
            
            <!-- Left Sidebar: Filters & Search -->
            <div class="col-12 col-lg-3">
                <div class="card gamified-card rounded-4 sticky-top" style="top: 100px;">
                    <div class="card-body p-4">
                        <!-- Search -->
                        <div class="input-group sci-fi-input rounded-pill mb-4 px-2 py-1 overflow-hidden">
                            <span class="input-group-text bg-transparent border-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-transparent border-0 text-main shadow-none fs-sm" id="searchInput" placeholder="Search logs...">
                        </div>

                        <!-- Status Filters -->
                        <h6 class="text-muted fw-bold mb-3 fs-xs text-uppercase" style="letter-spacing: 1px;">Log Status</h6>
                        <ul class="list-unstyled mb-4 d-flex flex-column gap-1" id="statusFilters">
                            <li>
                                <a href="#" class="filter-link active text-decoration-none d-flex justify-content-between align-items-center" data-filter="all">
                                    <span class="text-muted fs-sm">All Signals</span> 
                                    <span class="badge bg-secondary rounded-pill text-white"><?= $notifyStats['total'] ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="filter-link text-decoration-none d-flex justify-content-between align-items-center" data-filter="unread">
                                    <span class="text-muted fs-sm">Unread Alerts</span> 
                                    <span class="badge rounded-pill text-white" style="background-color: var(--neon-purple);"><?= $notifyStats['unread'] ?></span>
                                </a>
                            </li>
                        </ul>

                        <!-- Category Filters -->
                        <h6 class="text-muted fw-bold mb-3 fs-xs text-uppercase" style="letter-spacing: 1px;">Categories</h6>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-1" id="categoryFilters">
                            <li><a href="#" class="filter-link text-decoration-none d-flex align-items-center gap-2" data-cat="all"><i class="bi bi-grid text-muted"></i> <span class="text-muted fs-sm">Everything</span></a></li>
                            <li><a href="#" class="filter-link text-decoration-none d-flex align-items-center gap-2" data-cat="course"><i class="bi bi-book text-info"></i> <span class="text-muted fs-sm">Courses</span></a></li>
                            <li><a href="#" class="filter-link text-decoration-none d-flex align-items-center gap-2" data-cat="assignment"><i class="bi bi-file-earmark-code text-danger"></i> <span class="text-muted fs-sm">Assignments</span></a></li>
                            <li><a href="#" class="filter-link text-decoration-none d-flex align-items-center gap-2" data-cat="lab"><i class="bi bi-pc-display-horizontal text-warning"></i> <span class="text-muted fs-sm">Labs & Quizzes</span></a></li>
                            <li><a href="#" class="filter-link text-decoration-none d-flex align-items-center gap-2" data-cat="ai"><i class="bi bi-robot" style="color: var(--neon-cyan);"></i> <span class="text-muted fs-sm">Oracle Systems</span></a></li>
                            <li><a href="#" class="filter-link text-decoration-none d-flex align-items-center gap-2" data-cat="achievement"><i class="bi bi-trophy text-success"></i> <span class="text-muted fs-sm">Achievements</span></a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Content: Feed -->
            <div class="col-12 col-lg-9">
                
                <!-- AI Daily Digest (Daily Quest) -->
                <div class="card ai-digest-banner rounded-4 mb-4">
                    <div class="card-body p-4 d-flex flex-column flex-md-row gap-4 align-items-md-center position-relative overflow-hidden">
                        <div class="ai-pulse-icon flex-shrink-0">
                            <i class="bi bi-stars fs-1" style="color: var(--neon-warning);"></i>
                        </div>
                        <div class="flex-grow-1 z-index-1">
                            <h5 class="fw-bold mb-1 text-main font-monospace"><i class="bi bi-lightning-charge-fill text-warning me-1"></i> <?= htmlspecialchars($aiDigest['title']) ?></h5>
                            <p class="fs-sm mb-3 text-muted"><?= htmlspecialchars($aiDigest['subtitle']) ?></p>
                            
                            <!-- Quest List -->
                            <div class="d-flex flex-column gap-2">
                                <?php
$ROOT_PATH = '../';
foreach($aiDigest['tasks'] as $task): ?>
                                    <div class="d-flex align-items-center gap-2 fs-sm fw-medium inner-box p-2 rounded-3 w-fit">
                                        <i class="bi <?= $task['icon'] ?>"></i>
                                        <span class="text-main"><?= htmlspecialchars($task['text']) ?></span>
                                        <span class="badge border ms-2 <?= $task['reward'] === 'URGENT' ? 'text-danger border-danger' : 'text-warning border-warning' ?> bg-transparent font-monospace">
                                            <?= htmlspecialchars($task['reward']) ?>
                                        </span>
                                    </div>
                                <?php
$ROOT_PATH = '../';
endforeach; ?>
                            </div>
                        </div>
                        <div class="z-index-1 mt-3 mt-md-0">
                            <button class="btn btn-glow-cyan rounded-pill px-4 py-2">
                                <i class="bi bi-crosshair me-1"></i> Engage Focus Mode
                            </button>
                        </div>
                        <!-- Decorative background icon -->
                        <i class="bi bi-robot position-absolute opacity-10" style="font-size: 15rem; right: -2rem; bottom: -4rem; color: var(--neon-cyan); pointer-events: none;"></i>
                    </div>
                </div>

                <!-- Notifications Feed (Injected via JS) -->
                <div id="notificationsContainer" class="d-flex flex-column gap-3">
                    <!-- Gamified Skeletons while loading -->
                    <div class="skeleton-item rounded-4 p-3">
                        <div class="d-flex gap-3">
                            <div class="skeleton-avatar rounded-circle flex-shrink-0" style="width: 45px; height: 45px;"></div>
                            <div class="flex-grow-1 py-1">
                                <div class="skeleton-text w-50 mb-2"></div>
                                <div class="skeleton-text w-75"></div>
                            </div>
                        </div>
                    </div>
                    <div class="skeleton-item rounded-4 p-3">
                        <div class="d-flex gap-3">
                            <div class="skeleton-avatar rounded-circle flex-shrink-0" style="width: 45px; height: 45px;"></div>
                            <div class="flex-grow-1 py-1">
                                <div class="skeleton-text w-25 mb-2"></div>
                                <div class="skeleton-text w-50"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button class="btn btn-outline-secondary rounded-pill px-5 fs-sm text-main hover-cyan" id="btnLoadMore" style="display: none; border-style: dashed;">
                        <i class="bi bi-arrow-down-circle me-1"></i> Load Older Archives
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../assets/js/notifications.js"></script>

<?php
$ROOT_PATH = '../';
include '../includes/footer.php'; ?>
