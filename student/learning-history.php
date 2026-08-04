<?php
$ROOT_PATH = '../';
/**
 * LevelUp - E-Learning Platform
 * Page: learning-history.php (ประวัติการเรียน - Main Container Page)
 * Standard: Production-Ready, Gamified Futuristic UI, Responsive, Light & Dark Mode, AJAX Dynamic Loading
 */

// =========================================================================
// 0. HELPER FUNCTIONS
// =========================================================================
if (!function_exists('e')) {
    /**
     * Helper function for HTML escaping (XSS Protection)
     */
    function e($value) {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

// =========================================================================
// 1. DATA PREPARATION FOR MAIN CONTAINER
// =========================================================================

// User Profile Data
$user = [
    'id' => 101,
    'name' => 'Alex Mercer',
    'email' => 'alex.mercer@levelup.dev',
    'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80',
    'level' => 12,
    'xp' => 14850,
    'streak' => 14
];

// Learning Overview KPIs
$overview = [
    'total_courses' => 12,
    'completed_courses' => 5,
    'in_progress_courses' => 7,
    'total_lessons' => 128,
    'total_hours' => 84.5,
    'total_xp' => 14850,
    'level' => 12,
    'streak_days' => 14
];

// Continue Learning Bar Data
$continueLearning = [
    'last_lesson' => [
        'title' => 'Building REST APIs with PHP & PDO',
        'course' => 'Full-Stack PHP Web Developer Masterclass',
        'link' => 'lesson.php?id=302',
        'progress' => 75
    ]
];

// AI Learning Analysis Data
$aiAnalysis = [
    'learning_speed' => 'Fast (1.4x Fast-Learner Index)',
    'overall_progress' => '+28% improvement vs last month',
    'strengths' => ['PHP Security', 'Database Design (SQL)', 'Docker Basics'],
    'weaknesses' => ['Async JavaScript', 'CSS Flexbox Edge Cases', 'Linux Command Lines'],
    'comparison' => 'Top 5% active learner this month',
    'recommendations' => [
        'review_lesson' => 'Async/Await in JavaScript (Lesson 12)',
        'next_skill' => 'Advanced Docker Compose & Kubernetes',
        'recommended_course' => 'Cloud Native Engineering Path'
    ]
];

// Learning Statistics Data (For Report Modal)
$learningStats = [
    'avg_score' => '92.4%'
];

$page_title = "Learning History & Analytics - LevelUp";
include '../includes/header.php';
?>

<!-- Custom Cyberpunk / Futuristic & Dynamic Light-Dark Theme System -->
<style>
:root, html[data-theme="dark"] {
    --lv-bg-dark: #0b0f19;
    --lv-card-bg: rgba(17, 24, 39, 0.7);
    --lv-card-border: rgba(255, 255, 255, 0.08);
    --lv-border-glow: rgba(99, 102, 241, 0.2);
    --lv-cyan: #06b6d4;
    --lv-cyan-glow: rgba(6, 182, 212, 0.35);
    --lv-indigo: #6366f1;
    --lv-purple: #a855f7;
    --lv-amber: #f59e0b;
    --lv-emerald: #10b981;
    --lv-rose: #f43f5e;
    --lv-text-main: #f1f5f9;
    --lv-text-muted: #94a3b8;
    --lv-input-bg: rgba(15, 23, 42, 0.6);
    --lv-table-header-bg: rgba(15, 23, 42, 0.6);
    --lv-modal-bg: #0f172a;
}

/* ☀️ CSS Overrides สำหรับเปลี่ยนเป็นโหมดสว่าง (Light Mode) */
html[data-theme="light"] {
    --lv-bg-dark: #f8fafc;
    --lv-card-bg: rgba(255, 255, 255, 0.85);
    --lv-card-border: rgba(0, 0, 0, 0.08);
    --lv-border-glow: rgba(99, 102, 241, 0.15);
    --lv-cyan: #0284c7;
    --lv-cyan-glow: rgba(2, 132, 199, 0.2);
    --lv-indigo: #4f46e5;
    --lv-purple: #9333ea;
    --lv-amber: #d97706;
    --lv-emerald: #059669;
    --lv-rose: #e11d48;
    --lv-text-main: #0f172a;
    --lv-text-muted: #64748b;
    --lv-input-bg: #ffffff;
    --lv-table-header-bg: #f1f5f9;
    --lv-modal-bg: #ffffff;
}

body {
    background-color: var(--lv-bg-dark) !important;
    color: var(--lv-text-main) !important;
    transition: background-color 0.3s ease, color 0.3s ease;
}

/* Light Mode Overrides for Hardcoded Text & Background Classes */
html[data-theme="light"] .text-white {
    color: var(--lv-text-main) !important;
}
html[data-theme="light"] .text-white-50 {
    color: var(--lv-text-muted) !important;
}
html[data-theme="light"] .bg-dark {
    background-color: #f1f5f9 !important;
}
html[data-theme="light"] .bg-dark.bg-opacity-50,
html[data-theme="light"] .bg-dark.bg-opacity-40,
html[data-theme="light"] .bg-dark.bg-opacity-75 {
    background-color: rgba(241, 245, 249, 0.85) !important;
}

/* Glassmorphism Dashboard Cards */
.dashboard-card {
    background: var(--lv-card-bg);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--lv-card-border);
    border-radius: 18px;
    box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.2);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
html[data-theme="light"] .dashboard-card {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
}
.dashboard-card:hover {
    border-color: rgba(6, 182, 212, 0.3);
    box-shadow: 0 12px 35px -8px var(--lv-cyan-glow);
}

/* Custom 5-Column Grid Layout for KPI Cards */
@media (min-width: 1200px) {
    .col-xl-5-custom {
        flex: 0 0 auto;
        width: 20%;
    }
}

/* KPI Stat Cards Styling */
.stat-card-kpi {
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, rgba(30, 41, 59, 0.7), rgba(15, 23, 42, 0.8));
}
html[data-theme="light"] .stat-card-kpi {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(241, 245, 249, 0.9));
}
.stat-card-kpi::before {
    content: '';
    position: absolute;
    top: 0; left: 0; width: 100%; height: 3px;
    background: linear-gradient(90deg, transparent, var(--lv-cyan), transparent);
    opacity: 0.7;
}

/* Flame Pulse Effect for Streak */
.streak-flame-active {
    color: var(--lv-amber);
    filter: drop-shadow(0 0 8px rgba(245, 158, 11, 0.6));
    animation: pulseFlame 2s infinite alternate;
}
@keyframes pulseFlame {
    0% { transform: scale(1); filter: drop-shadow(0 0 6px rgba(245, 158, 11, 0.5)); }
    100% { transform: scale(1.1); filter: drop-shadow(0 0 14px rgba(245, 158, 11, 0.9)); }
}

/* Sub-Navigation Gamified Tabs */
.history-subnav {
    border-bottom: 1px solid var(--lv-card-border);
}
.history-subnav .nav-link {
    color: var(--lv-text-muted);
    border-radius: 12px;
    padding: 10px 22px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.25s ease;
    border: 1px solid transparent;
    white-space: nowrap;
    background: rgba(255, 255, 255, 0.03);
    cursor: pointer;
}
html[data-theme="light"] .history-subnav .nav-link {
    background: #ffffff;
    border-color: rgba(0, 0, 0, 0.08);
    color: var(--lv-text-muted);
}
.history-subnav .nav-link:hover {
    color: var(--lv-text-main);
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.15);
}
html[data-theme="light"] .history-subnav .nav-link:hover {
    background: #f1f5f9;
    border-color: rgba(0, 0, 0, 0.15);
    color: var(--lv-text-main);
}

/* FIX BUG: Active Subnav Tab High-Contrast Solid Gradient Style */
.history-subnav .nav-link.active {
    background: linear-gradient(135deg, var(--lv-indigo), var(--lv-cyan)) !important;
    border-color: transparent !important;
    color: #ffffff !important;
    box-shadow: 0 4px 18px rgba(6, 182, 212, 0.35);
}
html[data-theme="light"] .history-subnav .nav-link.active {
    background: linear-gradient(135deg, #4f46e5, #0284c7) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 14px rgba(2, 132, 199, 0.3);
}

/* Timeline Components */
.timeline-container {
    position: relative;
    padding-left: 28px;
    border-left: 2px dashed rgba(6, 182, 212, 0.25);
}
.timeline-item {
    position: relative;
    margin-bottom: 22px;
}
.timeline-marker {
    position: absolute;
    left: -41px;
    top: 0;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: var(--lv-modal-bg);
    border: 2px solid var(--lv-cyan);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    box-shadow: 0 0 10px var(--lv-cyan-glow);
}

/* Calendar Activity Heatmap Grid */
.calendar-heatmap-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
}
.heatmap-day {
    aspect-ratio: 1;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.heatmap-day:hover {
    transform: scale(1.12);
    z-index: 2;
}

/* 🌙 Dark Mode Heatmap Styles */
html[data-theme="dark"] .heatmap-day {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid var(--lv-card-border);
    color: var(--lv-text-muted);
}
html[data-theme="dark"] .heatmap-day.lvl-1 { background: rgba(6, 182, 212, 0.25); color: #38bdf8; border-color: rgba(6, 182, 212, 0.4); }
html[data-theme="dark"] .heatmap-day.lvl-2 { background: rgba(6, 182, 212, 0.55); color: #ffffff; border-color: rgba(6, 182, 212, 0.7); }
html[data-theme="dark"] .heatmap-day.lvl-3 { background: rgba(99, 102, 241, 0.75); color: #ffffff; border-color: rgba(99, 102, 241, 0.9); }
html[data-theme="dark"] .heatmap-day.lvl-4 { background: rgba(168, 85, 247, 0.95); color: #ffffff; border-color: #c084fc; box-shadow: 0 0 12px rgba(168, 85, 247, 0.6); }

/* ☀️ Light Mode Heatmap Styles */
html[data-theme="light"] .heatmap-day { background: #f1f5f9; border: 1px solid #cbd5e1; color: #475569; }
html[data-theme="light"] .heatmap-day.lvl-1 { background: #e0f2fe; color: #0369a1; border-color: #bae6fd; }
html[data-theme="light"] .heatmap-day.lvl-2 { background: #0284c7; color: #ffffff; border-color: #0369a1; }
html[data-theme="light"] .heatmap-day.lvl-3 { background: #6366f1; color: #ffffff; border-color: #4f46e5; }
html[data-theme="light"] .heatmap-day.lvl-4 { background: #9333ea; color: #ffffff; border-color: #7e22ce; box-shadow: 0 4px 12px rgba(147, 51, 234, 0.25); }

/* Table Styling */
.table-custom { color: var(--lv-text-main); }
.table-custom th {
    background: var(--lv-table-header-bg);
    color: var(--lv-text-muted);
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.75px;
    border-bottom: 1px solid var(--lv-card-border);
    padding: 14px 18px;
}
.table-custom td { padding: 16px 18px; border-bottom: 1px solid var(--lv-card-border); }
.table-custom tbody tr:hover { background: rgba(255, 255, 255, 0.03); }
html[data-theme="light"] .table-custom tbody tr:hover { background: rgba(0, 0, 0, 0.02); }

/* Custom Cyber Buttons */
.btn-premium {
    background: linear-gradient(135deg, var(--lv-indigo), var(--lv-cyan));
    color: #ffffff !important;
    border: none;
    font-weight: 600;
    box-shadow: 0 4px 18px rgba(6, 182, 212, 0.3);
    transition: all 0.25s ease;
}
.btn-premium:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 24px rgba(6, 182, 212, 0.5);
    color: #ffffff !important;
}
.btn-outline-surface {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid var(--lv-card-border);
    color: var(--lv-text-main);
    transition: all 0.2s ease;
}
html[data-theme="light"] .btn-outline-surface { background: rgba(0, 0, 0, 0.04); }
.btn-outline-surface:hover {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.25);
    color: var(--lv-text-main);
}
html[data-theme="light"] .btn-outline-surface:hover {
    background: rgba(0, 0, 0, 0.08);
    border-color: rgba(0, 0, 0, 0.2);
}

/* Modal Light Mode Overrides */
html[data-theme="light"] .modal-content {
    background-color: var(--lv-modal-bg) !important;
    color: var(--lv-text-main) !important;
    border-color: #e2e8f0 !important;
}
html[data-theme="light"] .modal-header,
html[data-theme="light"] .modal-footer { border-color: #e2e8f0 !important; }

/* Color Helper Utility Classes */
.text-cyan { color: var(--lv-cyan) !important; }
.text-purple { color: var(--lv-purple) !important; }
.bg-cyan { background-color: var(--lv-cyan) !important; }

/* Replay Timeline Modal Steps */
.replay-step {
    border-left: 2px dashed var(--lv-cyan);
    padding-left: 20px;
    position: relative;
    margin-bottom: 20px;
}
.replay-step::before {
    content: '';
    position: absolute;
    left: -7px; top: 4px;
    width: 12px; height: 12px;
    border-radius: 50%;
    background: var(--lv-cyan);
    box-shadow: 0 0 10px var(--lv-cyan);
}
</style>

<div class="container-fluid px-3 px-lg-4 py-4">

    <!-- TOP TITLE BAR & ACTION BUTTONS WITH THEME TOGGLE SWITCH -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-cyan bg-opacity-20 text-cyan border border-cyan border-opacity-25 px-2.5 py-1 rounded-pill small">
                    <i class="bi bi-cpu-fill me-1"></i> AI-Powered Analytics
                </span>
                <span class="badge bg-warning bg-opacity-20 text-warning border border-warning border-opacity-25 px-2.5 py-1 rounded-pill small">
                    <i class="bi bi-fire me-1"></i> <?= e($overview['streak_days']) ?> Days Streak
                </span>
            </div>
            <h2 class="fw-bold mb-1 d-flex align-items-center gap-2 text-white">
                <i class="bi bi-clock-history text-cyan"></i> Learning History & Analytics
            </h2>
            <p class="text-white-50 mb-0 small">ติดตามประวัติการเรียนรู้ พัฒนาการ สถิติการทำ Lab และรายงานการโต้ตอบกับ AI</p>
        </div>
        
        <!-- Export History, Modals & Theme Switch Button -->
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <!-- ☀️ สวิตช์ปุ่มกดสลับธีม (Toggle Theme) -->
            <button type="button" class="btn btn-outline-surface btn-sm rounded-3 px-3 d-flex align-items-center gap-1.5" id="themeToggleBtn" onclick="toggleTheme()" title="สลับโหมดสว่าง / โหมดมืด">
                <i class="bi bi-sun-fill text-warning" id="themeIcon"></i>
                <span id="themeText">Light Mode</span>
            </button>

            <button type="button" class="btn btn-outline-surface btn-sm rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#modalLearningReplay">
                <i class="bi bi-play-circle-fill text-cyan me-1.5"></i> Learning Replay
            </button>
            <button type="button" class="btn btn-outline-surface btn-sm rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#modalPersonalReport">
                <i class="bi bi-file-earmark-bar-graph-fill text-warning me-1.5"></i> Personal Report (PDF)
            </button>
            <div class="dropdown">
                <button class="btn btn-premium btn-sm rounded-3 px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download me-1.5"></i> Export Data
                </button>
                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow-lg border-secondary border-opacity-25">
                    <li><a class="dropdown-item py-2" href="javascript:void(0)" onclick="exportData('pdf')"><i class="bi bi-file-pdf text-danger me-2"></i> Export to PDF</a></li>
                    <li><a class="dropdown-item py-2" href="javascript:void(0)" onclick="exportData('excel')"><i class="bi bi-file-excel text-success me-2"></i> Export to Excel</a></li>
                    <li><a class="dropdown-item py-2" href="javascript:void(0)" onclick="exportData('csv')"><i class="bi bi-filetype-csv text-info me-2"></i> Export to CSV</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- SEARCH & FILTER TOOLBAR -->
    <div class="dashboard-card p-3 mb-4">
        <form id="historyFilterForm" class="row g-2 align-items-center" onsubmit="event.preventDefault(); applyFilters();">
            <div class="col-12 col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-transparent border-secondary border-opacity-25 text-white-50">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="filterKeyword" class="form-control bg-transparent border-secondary border-opacity-25 text-white shadow-none" placeholder="ค้นหา Course, Lesson, Quiz, Lab หรือ AI...">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <select id="filterType" class="form-select form-select-sm bg-dark text-white border-secondary border-opacity-25 shadow-none">
                    <option value="">ทุกหมวดหมู่ (All Types)</option>
                    <option value="course">Courses</option>
                    <option value="lesson">Lessons</option>
                    <option value="quiz">Quizzes</option>
                    <option value="lab">Labs</option>
                    <option value="ai">AI Chat</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select id="filterDate" class="form-select form-select-sm bg-dark text-white border-secondary border-opacity-25 shadow-none">
                    <option value="all">ทุกช่วงเวลา</option>
                    <option value="today">วันนี้</option>
                    <option value="7days">7 วันล่าสุด</option>
                    <option value="30days">30 วันล่าสุด</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select id="filterStatus" class="form-select form-select-sm bg-dark text-white border-secondary border-opacity-25 shadow-none">
                    <option value="">สถานะทั้งหมด</option>
                    <option value="completed">Completed / Passed</option>
                    <option value="in_progress">In Progress</option>
                    <option value="failed">Failed</option>
                </select>
            </div>
            <div class="col-6 col-md-2 d-flex gap-1">
                <button type="submit" id="btnFilterSubmit" class="btn btn-premium btn-sm w-100 d-flex align-items-center justify-content-center gap-1">
                    <i class="bi bi-filter"></i> <span>Filter</span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-surface" onclick="resetFilters()" title="Reset Filters">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- 1. LEARNING OVERVIEW (GAMIFIED KPI CARDS) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3 col-xl-5-custom">
            <div class="dashboard-card stat-card-kpi p-3 h-100">
                <span class="text-white-50 small d-block mb-1">Courses Enrolled</span>
                <div class="d-flex align-items-baseline justify-content-between">
                    <h3 class="fw-bold mb-0 text-white"><?= e($overview['total_courses']) ?></h3>
                    <span class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-25 small"><?= e($overview['completed_courses']) ?> Done</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl-5-custom">
            <div class="dashboard-card stat-card-kpi p-3 h-100">
                <span class="text-white-50 small d-block mb-1">Lessons Finished</span>
                <div class="d-flex align-items-baseline justify-content-between">
                    <h3 class="fw-bold mb-0 text-cyan"><?= e($overview['total_lessons']) ?></h3>
                    <span class="text-white-50 fs-7">Lessons</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl-5-custom">
            <div class="dashboard-card stat-card-kpi p-3 h-100">
                <span class="text-white-50 small d-block mb-1">Total Time Spent</span>
                <div class="d-flex align-items-baseline justify-content-between">
                    <h3 class="fw-bold mb-0 text-warning"><?= e($overview['total_hours']) ?></h3>
                    <span class="text-white-50 fs-7">Hours</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl-5-custom">
            <div class="dashboard-card stat-card-kpi p-3 h-100">
                <span class="text-white-50 small d-block mb-1">Total XP / Level</span>
                <div class="d-flex align-items-baseline justify-content-between">
                    <h3 class="fw-bold mb-0 text-primary"><?= e(number_format($overview['total_xp'])) ?></h3>
                    <span class="badge bg-primary bg-opacity-20 text-primary border border-primary border-opacity-25">Lv.<?= e($overview['level']) ?></span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3 col-xl-5-custom">
            <div class="dashboard-card stat-card-kpi p-3 h-100 border-warning border-opacity-25">
                <span class="text-white-50 small d-block mb-1">Active Streak</span>
                <div class="d-flex align-items-baseline justify-content-between">
                    <h3 class="fw-bold mb-0 text-warning d-flex align-items-center gap-1">
                        <i class="bi bi-fire streak-flame-active"></i> <?= e($overview['streak_days']) ?>
                    </h3>
                    <span class="text-white-50 fs-7">Days Streak</span>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTINUE LEARNING BAR -->
    <div class="dashboard-card p-3 mb-4" style="background: linear-gradient(135deg, rgba(6,182,212,0.12), rgba(99,102,241,0.08)); border-color: rgba(6,182,212,0.3);">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 rounded-4 bg-cyan bg-opacity-20 text-cyan fs-4 d-flex align-items-center justify-content-center shadow-sm">
                    <i class="bi bi-play-circle-fill"></i>
                </div>
                <div>
                    <span class="badge bg-cyan text-white fw-bold mb-1">▶ CONTINUE LEARNING</span>
                    <h6 class="fw-bold text-white mb-0"><?= e($continueLearning['last_lesson']['title']) ?></h6>
                    <small class="text-white-50"><?= e($continueLearning['last_lesson']['course']) ?></small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3 ms-auto ms-lg-0">
                <div class="d-flex flex-column align-items-end me-2">
                    <span class="text-white-50 fs-7">Progress: <strong class="text-cyan"><?= e($continueLearning['last_lesson']['progress']) ?>%</strong></span>
                    <div class="progress mt-1" style="width: 140px; height: 6px; background: rgba(0,0,0,0.1);">
                        <div class="progress-bar bg-cyan" style="width: <?= (int)$continueLearning['last_lesson']['progress'] ?>%;"></div>
                    </div>
                </div>
                <a href="<?= e($continueLearning['last_lesson']['link']) ?>" class="btn btn-premium btn-sm rounded-pill px-4 py-2">
                    เรียนต่อบทนี้ <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- AI LEARNING ANALYSIS BANNER -->
    <div class="dashboard-card p-4 mb-4">
        <div class="row g-4">
            <div class="col-12 col-lg-4 border-end-lg border-secondary border-opacity-25 pe-lg-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="p-2 rounded-3 bg-cyan bg-opacity-20 text-cyan">
                        <i class="bi bi-cpu-fill fs-5"></i>
                    </div>
                    <h5 class="fw-bold mb-0 text-white">AI Learning Analysis</h5>
                </div>
                <p class="text-white-50 small mb-2">ความเร็วในการเรียนรู้: <strong class="text-cyan"><?= e($aiAnalysis['learning_speed']) ?></strong></p>
                <div class="p-3 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-25">
                    <span class="text-success small fw-semibold d-block mb-1"><i class="bi bi-graph-up-arrow me-1"></i> <?= e($aiAnalysis['overall_progress']) ?></span>
                    <div class="text-white-50 fs-7"><?= e($aiAnalysis['comparison']) ?></div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <h6 class="fw-semibold text-success small mb-2"><i class="bi bi-shield-check me-1"></i> จุดแข็ง (Strengths)</h6>
                <ul class="list-unstyled small text-white-50 mb-3">
                    <?php
$ROOT_PATH = '../';
foreach($aiAnalysis['strengths'] as $st): ?>
                        <li class="mb-1 d-flex align-items-center"><i class="bi bi-check2-circle text-success me-2 fs-6"></i> <?= e($st) ?></li>
                    <?php
$ROOT_PATH = '../';
endforeach; ?>
                </ul>
                <h6 class="fw-semibold text-warning small mb-2"><i class="bi bi-exclamation-triangle me-1"></i> จุดที่ควรพัฒนา (Weaknesses)</h6>
                <ul class="list-unstyled small text-white-50 mb-0">
                    <?php
$ROOT_PATH = '../';
foreach($aiAnalysis['weaknesses'] as $wk): ?>
                        <li class="mb-1 d-flex align-items-center"><i class="bi bi-dash-circle text-warning me-2 fs-6"></i> <?= e($wk) ?></li>
                    <?php
$ROOT_PATH = '../';
endforeach; ?>
                </ul>
            </div>
            <div class="col-12 col-md-6 col-lg-4 bg-dark bg-opacity-40 rounded-3 p-3 border border-secondary border-opacity-25 d-flex flex-column justify-content-between">
                <div>
                    <h6 class="fw-semibold text-cyan small mb-2 d-flex align-items-center gap-1">
                        <i class="bi bi-lightbulb-fill text-warning"></i> AI Recommendation Action
                    </h6>
                    <div class="small text-white-50 mb-1">บทเรียนที่ควรทบทวน: <span class="text-white fw-semibold"><?= e($aiAnalysis['recommendations']['review_lesson']) ?></span></div>
                    <div class="small text-white-50 mb-1">Skill ที่ควรเรียนต่อ: <span class="text-white fw-semibold"><?= e($aiAnalysis['recommendations']['next_skill']) ?></span></div>
                    <div class="small text-white-50 mb-2">คอร์สแนะนำ: <span class="text-cyan fw-semibold"><?= e($aiAnalysis['recommendations']['recommended_course']) ?></span></div>
                </div>
                <a href="courses.php" class="btn btn-outline-surface btn-sm w-100 mt-2 rounded-3">ไปยังคอร์สแนะนำ <i class="bi bi-chevron-right ms-1"></i></a>
            </div>
        </div>
    </div>

    <!-- MAIN NAVIGATION TABS (AJAX DRIVEN) -->
    <ul class="nav nav-pills history-subnav mb-4 gap-2 flex-nowrap overflow-auto pb-2" id="historyTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link ajax-history-btn active" data-page="timeline-activity.php" type="button">
                <i class="bi bi-diagram-2 me-1.5"></i> Timeline & Activity
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link ajax-history-btn" data-page="courses-lessons.php" type="button">
                <i class="bi bi-journal-code me-1.5"></i> Courses & Lessons
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link ajax-history-btn" data-page="quizzes-labs.php" type="button">
                <i class="bi bi-terminal me-1.5"></i> Quizzes & Labs
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link ajax-history-btn" data-page="skills-achievements.php" type="button">
                <i class="bi bi-award me-1.5"></i> Skills & Achievements
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link ajax-history-btn" data-page="ai-system-logs.php" type="button">
                <i class="bi bi-robot me-1.5"></i> AI & System Logs
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link ajax-history-btn" data-page="certificates-stats.php" type="button">
                <i class="bi bi-patch-check me-1.5"></i> Certificates & Stats
            </button>
        </li>
    </ul>

    <!-- DYNAMIC AJAX CONTENT PLACEHOLDER -->
    <div id="history-content-placeholder">
        <!-- Sub-page HTML content will be loaded here via jQuery AJAX -->
    </div>

</div>

<!-- MODAL 1: LEARNING REPLAY -->
<div class="modal fade" id="modalLearningReplay" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold"><i class="bi bi-play-circle-fill text-cyan me-2"></i>Learning Replay (Playback)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-white-50 small">ย้อนดูเส้นทางพัฒนาการการเรียนรู้ตั้งแต่ Day 1</p>
                <div class="replay-step">
                    <strong class="text-cyan">Day 1:</strong> เริ่มเรียน Python & Web Fundamentals
                </div>
                <div class="replay-step">
                    <strong class="text-cyan">Day 5:</strong> ผ่าน Quiz 1 & ทำ Coding Lab สำเร็จ
                </div>
                <div class="replay-step">
                    <strong class="text-cyan">Day 10:</strong> สร้าง REST API Project & รับ Certificate ใบแรก
                </div>
                <div class="replay-step">
                    <strong class="text-cyan">Today:</strong> ทำ Docker Virtual Lab & รักษาสถิติ Streak 14 วัน
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-outline-surface btn-sm" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL 2: PERSONAL LEARNING REPORT -->
<div class="modal fade" id="modalPersonalReport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-pdf-fill text-warning me-2"></i>Personal Learning Report</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="p-4 rounded-3 border border-secondary border-opacity-25 bg-dark bg-opacity-75">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-secondary pb-3">
                        <div>
                            <h4 class="fw-bold mb-0 text-cyan">LevelUp Student Performance Report</h4>
                            <small class="text-white-50">ผู้เรียน: <?= e($user['name']) ?> | ประจำวันที่ 23 กรกฎาคม 2026</small>
                        </div>
                        <span class="badge bg-success">Verified Student</span>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-4"><small class="text-white-50">ชั่วโมงเรียนรวม:</small> <div class="fw-bold text-white"><?= e($overview['total_hours']) ?> hrs</div></div>
                        <div class="col-4"><small class="text-white-50">คอร์สที่จบแล้ว:</small> <div class="fw-bold text-success"><?= e($overview['completed_courses']) ?> Courses</div></div>
                        <div class="col-4"><small class="text-white-50">คะแนนเฉลี่ย:</small> <div class="fw-bold text-warning"><?= e($learningStats['avg_score']) ?></div></div>
                    </div>
                    <p class="small text-white-50 mb-0">รายงานนี้ถูกสร้างโดย AI สรุปผลเพื่อนำไปใช้ประกอบเป็น Portfolio หรือประเมินผลทักษะส่วนบุคคล</p>
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-outline-surface btn-sm" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-premium btn-sm" onclick="exportData('pdf')"><i class="bi bi-download me-1"></i> ดาวน์โหลด PDF Report</button>
            </div>
        </div>
    </div>
</div>

<!-- PAGE SCRIPTS: LIGHT/DARK MODE TOGGLE, JQUERY AJAX CONTENT LOADER & LIVE FILTER -->
<script>
// ☀️ LIGHT / DARK MODE TOGGLE & LOCALSTORAGE SYSTEM
function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);

    const themeIcon = document.getElementById('themeIcon');
    const themeText = document.getElementById('themeText');

    if (themeIcon && themeText) {
        if (theme === 'light') {
            themeIcon.className = 'bi bi-moon-stars-fill text-indigo';
            themeText.textContent = 'Dark Mode';
        } else {
            themeIcon.className = 'bi bi-sun-fill text-warning';
            themeText.textContent = 'Light Mode';
        }
    }
}

function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    applyTheme(newTheme);
}

// Restore theme from localStorage on page load
(function () {
    const savedTheme = localStorage.getItem('theme') || 'dark';
    applyTheme(savedTheme);
})();

// =========================================================================
// JQUERY AJAX CONTENT LOADING SYSTEM
// =========================================================================
function loadHistoryContent(page) {
    const container = $('#history-content-placeholder');
    
    // Display Futuristic Spinner while fetching content
    container.html(`
        <div class="dashboard-card p-5 text-center my-4">
            <div class="spinner-border text-cyan mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h6 class="text-white mb-1">กำลังโหลดข้อมูล...</h6>
            <p class="text-white-50 small mb-0">กรุณารอสักครู่ ระบบกำลังดึงเนื้อหาจากเซิร์ฟเวอร์</p>
        </div>
    `);

    $.ajax({
        url: page,
        type: 'GET',
        dataType: 'html',
        success: function(response) {
            container.hide().html(response).fadeIn(300);
            // Re-apply client-side filter if search input has value
            performClientFilter();
        },
        error: function(xhr, status, error) {
            container.html(`
                <div class="dashboard-card p-4 text-center my-4 border-danger border-opacity-50">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-1 mb-2 d-block"></i>
                    <h5 class="text-white mb-1">เกิดข้อผิดพลาดในการโหลดข้อมูล</h5>
                    <p class="text-white-50 small mb-3">ไม่สามารถโหลดไฟล์ <code>${page}</code> ได้ (HTTP Status: ${xhr.status})</p>
                    <button class="btn btn-outline-surface btn-sm" onclick="loadHistoryContent('${page}')">
                        <i class="bi bi-arrow-clockwise me-1"></i> ลองใหม่อีกครั้ง
                    </button>
                </div>
            `);
        }
    });
}

$(document).ready(function() {
    // 1. Initial Default Page Load
    loadHistoryContent('timeline-activity.php');

    // 2. Tab Navigation Click Handler via AJAX
    $(document).on('click', '.ajax-history-btn', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const targetPage = $btn.data('page');

        // Toggle Active Class
        $('.ajax-history-btn').removeClass('active');
        $btn.addClass('active');

        // Load Content
        if (targetPage) {
            loadHistoryContent(targetPage);
        }
    });

    // 3. Client-side Live Filtering Event Listener
    $(document).on('keyup', '#filterKeyword', performClientFilter);
    $(document).on('change', '#filterType, #filterStatus', performClientFilter);
});

// Client-side Filter Function across loaded elements
function performClientFilter() {
    const filterInput = document.getElementById('filterKeyword');
    const filterType = document.getElementById('filterType');
    const filterStatus = document.getElementById('filterStatus');

    if (!filterInput || !filterType || !filterStatus) return;

    const keyword = filterInput.value.toLowerCase().trim();
    const selectedType = filterType.value.toLowerCase();
    const selectedStatus = filterStatus.value.toLowerCase();
    
    const searchableItems = document.querySelectorAll('.searchable-item');

    searchableItems.forEach(item => {
        const textContent = item.textContent.toLowerCase();
        const itemType = (item.getAttribute('data-type') || '').toLowerCase();
        const itemStatus = (item.getAttribute('data-status') || '').toLowerCase();

        const matchesKeyword = !keyword || textContent.includes(keyword);
        const matchesType = !selectedType || itemType === selectedType;
        const matchesStatus = !selectedStatus || itemStatus === selectedStatus;

        if (matchesKeyword && matchesType && matchesStatus) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}

function applyFilters() {
    const btn = document.getElementById('btnFilterSubmit');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Filtering...';

    performClientFilter();

    setTimeout(function() {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }, 300);
}

function resetFilters() {
    const form = document.getElementById('historyFilterForm');
    if (form) form.reset();
    performClientFilter();
}

function verifyCertificate(hash) {
    alert('Certificate Verification Hash: ' + hash + '\nStatus: VALID & VERIFIED on LevelUp Blockchain Ledger');
}

function exportData(format) {
    alert('กำลังเตรียมดาวน์โหลดไฟล์ประวัติการเรียน รูปแบบ: ' + format.toUpperCase());
}
</script>

<?php
$ROOT_PATH = '../';
include '../includes/footer.php'; ?>
