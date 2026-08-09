<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

// =========================================================================
// 1. DATA PREPARATION (ข้อมูลจำลองรองรับการเชื่อมต่อฐานข้อมูล)
// =========================================================================

// ข้อมูล AI Learning Planner & Career Goals
$plannerData = [
    'selected_career' => 'backend',
    'hours_per_day' => 2,
    'estimated_months' => 5,
    'overall_progress' => 65,
    'career_options' => [
        'backend'   => ['title' => 'Backend Developer (Class)', 'icon' => 'bi-server', 'desc' => 'เน้น Architecture, API & Database'],
        'frontend'  => ['title' => 'Frontend Developer (Class)', 'icon' => 'bi-layout-text-window-reverse', 'desc' => 'เน้น UI/UX, Web Design & React'],
        'fullstack' => ['title' => 'Full Stack Developer (Class)', 'icon' => 'bi-layers-half', 'desc' => 'ครบวงจรทั้ง Frontend & Backend'],
        'data'      => ['title' => 'Data Scientist (Class)', 'icon' => 'bi-graph-up-arrow', 'desc' => 'เน้น Data Analysis & AI Model'],
        'cyber'     => ['title' => 'Cybersecurity Analyst (Class)', 'icon' => 'bi-shield-lock', 'desc' => 'เน้น Security & Defense System']
    ]
];

// ข้อมูล แนะนำบทเรียนถัดไป (AI Recommendation)
$recommendedNextStep = [
    'badge'       => 'AI Recommendation (Next Step)',
    'title'       => 'เรียน SQL JOIN & Subqueries',
    'description' => 'จากประวัติ คุณผ่าน Basic SQL แล้ว การทำความเข้าใจ JOIN จะช่วยให้คุณพร้อมทำ Project ต่อไป',
    'link'        => 'lesson.php?id=sql-join'
];

// ข้อมูล สถานะการเรียนรู้ของผู้ใช้
$userStats = [
    'projects_cleared'    => 4,
    'total_projects'      => 6,
    'coding_labs_cleared' => 18,
    'total_coding_labs'   => 25,
    'class_match_percent' => 72
];

// ข้อมูล Skill Gap Analysis
$skillGapList = [
    [
        'skill'   => 'Python',
        'status'  => 'cleared', // cleared, percent, not_started
        'percent' => 100,
        'target'  => true
    ],
    [
        'skill'   => 'SQL',
        'status'  => 'percent',
        'percent' => 60,
        'target'  => true
    ],
    [
        'skill'   => 'Docker',
        'status'  => 'not_started',
        'percent' => 0,
        'target'  => true
    ],
    [
        'skill'   => 'CI/CD',
        'status'  => 'not_started',
        'percent' => 0,
        'target'  => true
    ]
];

// ข้อมูล Milestones
$milestonesList = [
    [
        'title'       => 'Milestone 1',
        'description' => 'พื้นฐาน Programming แน่น',
        'status'      => 'completed', // completed, active, locked
        'icon'        => 'bi-check-circle-fill text-success'
    ],
    [
        'title'       => 'Milestone 2',
        'description' => 'ผ่านระบบ Database (SQL)',
        'status'      => 'active',
        'icon'        => 'bi-hourglass-split text-info'
    ],
    [
        'title'       => 'Milestone 3',
        'description' => 'สร้าง REST API Project',
        'status'      => 'locked',
        'icon'        => 'bi-lock-fill text-muted-custom'
    ]
];

// ข้อมูล Roadmap Skill Tree Nodes
$roadmapNodes = [
    [
        'id'          => 1,
        'status'      => 'completed', // completed, active, locked
        'progress'    => 100,
        'badge'       => 'QUEST CLEARED',
        'title'       => 'Python Basics',
        'description' => 'เรียนรู้พื้นฐานตัวแปร, Loop และ Function',
        'footer_text' => 'ไม่มี Prerequisite',
        'footer_icon' => 'bi-info-circle text-info',
        'link'        => '',
        'has_line'    => true
    ],
    [
        'id'          => 2,
        'status'      => 'completed',
        'progress'    => 100,
        'badge'       => 'QUEST CLEARED',
        'title'       => 'Python OOP & Data Structure',
        'description' => 'การเขียนโปรแกรมเชิงวัตถุและโครงสร้างข้อมูล',
        'footer_text' => 'ผ่าน: Python Basics',
        'footer_icon' => 'bi-check-circle-fill text-success',
        'link'        => '',
        'has_line'    => true
    ],
    [
        'id'          => 3,
        'status'      => 'active',
        'progress'    => 60,
        'badge'       => 'CURRENT ACTIVE QUEST',
        'title'       => 'SQL & Relational Database',
        'description' => 'การออกแบบฐานข้อมูลและการ Query ข้อมูลเชิงลึก',
        'footer_text' => 'ผ่าน: Data Structure',
        'footer_icon' => 'bi-check-circle-fill text-success',
        'link'        => 'lesson.php',
        'has_line'    => true
    ],
    [
        'id'             => 4,
        'status'         => 'locked',
        'progress'       => 0,
        'badge'          => 'LOCKED LEVEL',
        'title'          => 'REST API with Flask',
        'description'    => 'การสร้าง API สำหรับเชื่อมต่อระบบ Frontend และ Backend',
        'prereq_warning' => ['ต้องผ่าน: SQL & Database (ขาดอีก 40%)'],
        'has_line'       => true
    ],
    [
        'id'             => 5,
        'status'         => 'locked',
        'progress'       => 0,
        'badge'          => 'LOCKED LEVEL',
        'title'          => 'Docker & Deployment',
        'description'    => 'การบรรจุแอปพลิเคชันและนำขึ้น Server จริง',
        'prereq_warning' => [
            'ต้องผ่าน: REST API with Flask',
            'ต้องผ่าน: Linux Command Line (ย้ายมาจากหมวดอื่น)'
        ],
        'has_line'       => false
    ]
];

include '../includes/header.php';
?>

<!-- โหลดไฟล์ CSS สำหรับ Dropdown สำรอง -->
<link rel="stylesheet" href="css/dropdown-select.css">

<!-- Custom Embedded Styles for Learning Path (LevelUp Design System - Dual Theme) -->
<style>
    /* Dark Mode Theme Variables (Default) */
    :root, html[data-theme="dark"] {
        --lv-bg-dark: #090d1a;
        --lv-card-bg: rgba(20, 27, 45, 0.85);
        --lv-card-border: rgba(99, 102, 241, 0.25);
        --lv-cyan: #06b6d4;
        --lv-indigo: #6366f1;
        --lv-success: #10b981;
        --lv-warning: #f59e0b;
        --lv-danger: #ef4444;
        --lv-text-main: #f8fafc;
        --lv-text-muted: #94a3b8;
        --lv-hero-bg: radial-gradient(circle at 50% 0%, rgba(99, 102, 241, 0.18) 0%, rgba(9, 13, 26, 0.98) 75%);
        --lv-input-bg: rgba(15, 23, 42, 0.85);
        --lv-input-border: rgba(255, 255, 255, 0.15);
        --lv-input-text: #ffffff;
        --lv-progress-track: #1e293b;
    }

    /* Light Mode Theme Variables */
    html[data-theme="light"] {
        --lv-bg-dark: #f8fafc;
        --lv-card-bg: #ffffff;
        --lv-card-border: #e2e8f0;
        --lv-cyan: #0891b2;
        --lv-indigo: #4f46e5;
        --lv-success: #059669;
        --lv-warning: #d97706;
        --lv-danger: #dc2626;
        --lv-text-main: #0f172a;
        --lv-text-muted: #64748b;
        --lv-hero-bg: radial-gradient(circle at 50% 0%, rgba(99, 102, 241, 0.08) 0%, rgba(248, 250, 252, 0.98) 75%);
        --lv-input-bg: #ffffff;
        --lv-input-border: #cbd5e1;
        --lv-input-text: #0f172a;
        --lv-progress-track: #e2e8f0;
    }

    body {
        background-color: var(--lv-bg-dark) !important;
        color: var(--lv-text-main) !important;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Dynamic Text Color Classes */
    .text-main { color: var(--lv-text-main) !important; }
    .text-muted-custom { color: var(--lv-text-muted) !important; }

    /* AI Hero Section */
    .ai-hero-section {
        background: var(--lv-hero-bg);
        border-bottom: 1px solid var(--lv-card-border) !important;
    }

    .ai-glow-bg {
        background: radial-gradient(circle, rgba(6, 182, 212, 0.35) 0%, rgba(99, 102, 241, 0) 70%);
        filter: blur(70px);
        pointer-events: none;
    }

    .dashboard-card {
        background: var(--lv-card-bg) !important;
        border: 1px solid var(--lv-card-border) !important;
        backdrop-filter: blur(16px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .dashboard-card:hover {
        border-color: var(--lv-cyan) !important;
    }

    .border-color {
        border-color: var(--lv-card-border) !important;
    }

    /* Stat Cards Alignment */
    .stat-card-box {
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        align-items: center !important;
        padding: 1.25rem 1rem !important;
        min-height: 105px !important;
        box-sizing: border-box !important;
    }

    .stat-card-title {
        font-size: 0.85rem !important;
        color: var(--lv-text-muted) !important;
        margin-bottom: 0.5rem !important;
        white-space: nowrap !important;
        text-align: center !important;
        font-weight: 500 !important;
    }

    .stat-card-value {
        font-size: 1.85rem !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        display: flex !important;
        align-items: baseline !important;
        justify-content: center !important;
        gap: 0.35rem !important;
    }

    /* Custom Gamified Buttons */
    .btn-premium {
        background: linear-gradient(135deg, var(--lv-cyan) 0%, var(--lv-indigo) 100%) !important;
        color: #fff !important;
        border: none !important;
        box-shadow: 0 4px 18px rgba(6, 182, 212, 0.35);
        transition: all 0.3s ease !important;
    }

    .btn-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 24px rgba(6, 182, 212, 0.55) !important;
        color: #fff !important;
    }

    .btn-outline-surface {
        background: rgba(150, 150, 150, 0.05) !important;
        border: 1px solid var(--lv-card-border) !important;
        color: var(--lv-text-main) !important;
        transition: all 0.25s ease;
    }

    .btn-outline-surface:hover {
        background: rgba(150, 150, 150, 0.12) !important;
        border-color: var(--lv-cyan) !important;
        color: var(--lv-text-main) !important;
    }

    /* Skill Tree Visualizer */
    .roadmap-tree {
        position: relative;
        padding-left: 5px;
    }

    .roadmap-node {
        position: relative;
    }

    .roadmap-node.locked {
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .roadmap-node.locked:hover {
        transform: translateX(4px);
    }

    .node-indicator {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .node-icon {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.15rem;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
    }

    .node-line {
        width: 3px;
        flex-grow: 1;
        min-height: 45px;
        margin: 4px 0;
    }

    .pulse-ring-active {
        position: relative;
    }

    .pulse-ring-active::before {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: var(--lv-cyan);
        opacity: 0.7;
        animation: activePulse 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
        z-index: -1;
    }

    @keyframes activePulse {
        0% { transform: scale(0.95); opacity: 0.8; }
        70% { transform: scale(1.45); opacity: 0; }
        100% { transform: scale(0.95); opacity: 0; }
    }

    .text-gradient-cyan {
        background: linear-gradient(135deg, var(--lv-cyan) 0%, #3b82f6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .table-skill-gap th, .table-skill-gap td {
        background: transparent !important;
        color: var(--lv-text-main) !important;
    }

    .levelup-swal-popup {
        background: var(--lv-card-bg) !important;
        border: 1px solid var(--lv-card-border) !important;
        border-radius: 20px !important;
        backdrop-filter: blur(16px);
        color: var(--lv-text-main) !important;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3) !important;
        padding: 1.5rem !important;
    }

    .levelup-swal-title {
        color: var(--lv-text-main) !important;
        font-weight: 700 !important;
        font-size: 1.35rem !important;
    }

    .levelup-swal-text {
        color: var(--lv-text-muted) !important;
        font-size: 0.95rem !important;
    }

    @media (max-width: 768px) {
        .roadmap-tree { padding-left: 0; }
        .node-content { margin-left: 0.8rem !important; }
    }

    @media (max-width: 576px) {
        .levelup-swal-popup {
            width: 90% !important;
            padding: 1.25rem 1rem !important;
        }
    }
</style>

<!-- ========================================================================= -->
<!-- 2. HTML PRESENTATION LAYER -->
<!-- ========================================================================= -->

<!-- 1. AI Simulator & Control Panel -->
<section class="ai-hero-section py-4 py-lg-5 position-relative border-bottom border-color">
    <div class="ai-glow-bg position-absolute top-0 start-50 translate-middle-x" style="width: 800px; height: 400px; opacity: 0.2; pointer-events: none;"></div>
    <div class="container-xl position-relative z-1">
        <div class="row align-items-center g-4">
            <div class="col-12 col-lg-7">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-2 fs-sm fw-medium">
                        <i class="bi bi-cpu-fill me-1"></i> AI Learning Planner v2.5
                    </span>
                </div>
                <h1 class="fw-bold display-5 mb-3 text-main">เส้นทางการเรียนของคุณ</h1>
                <p class="text-muted-custom fs-5 mb-4 fw-light">ปรับเปลี่ยนเป้าหมายและเวลาว่างของคุณ AI จะคำนวณและจัดโครงสร้างหลักสูตรที่เหมาะสมที่สุดให้ทันที</p>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fs-sm fw-medium text-muted-custom">เป้าหมายอาชีพ (Career Goal)</label>
                        
                        <select name="career" id="careerSelector" class="lu-select">
                            <?php foreach ($plannerData['career_options'] as $key => $option): ?>
                                <option value="<?= htmlspecialchars($key) ?>" 
                                        data-icon="<?= htmlspecialchars($option['icon']) ?>"
                                        <?= ($key === $plannerData['selected_career']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($option['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fs-sm fw-medium text-muted-custom d-flex justify-content-between">
                            <span>เวลาเรียนต่อวัน</span>
                            <span class="text-info fw-bold"><span id="timeDisplay"><?= (int)$plannerData['hours_per_day'] ?></span> ชั่วโมง</span>
                        </label>
                        <div class="pt-2">
                            <input type="range" class="form-range custom-range-slider" min="1" max="8" step="1" id="timeSlider" value="<?= (int)$plannerData['hours_per_day'] ?>">
                        </div>
                    </div>
                </div>
                <div class="mt-4 d-flex flex-wrap gap-2">
                    <button class="btn btn-outline-surface shadow-sm rounded-pill px-4" id="btnShareRoadmap" type="button">
                        <i class="bi bi-share me-1 text-info"></i> แชร์ Roadmap
                    </button>
                    <button class="btn btn-outline-surface shadow-sm rounded-pill px-4" id="btnExportPdf" type="button">
                        <i class="bi bi-download me-1 text-info"></i> Export PDF
                    </button>
                </div>
            </div>

            <div class="col-12 col-lg-4 offset-lg-1">
                <div class="dashboard-card rounded-4 p-4 text-center position-relative overflow-hidden h-100 d-flex flex-column justify-content-center">
                    <div class="position-absolute top-0 end-0 p-3 text-info opacity-10"><i class="bi bi-cpu fs-1"></i></div>
                    <p class="fs-sm text-muted-custom fw-medium mb-1"><i class="bi bi-hourglass-split me-1 text-info"></i> คาดว่าจะเรียนจบใน</p>
                    <h2 class="display-4 fw-bold text-gradient-cyan mb-3" id="estimatedTime"><?= (int)$plannerData['estimated_months'] ?> เดือน</h2>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fs-sm fw-medium text-muted-custom">ภาพรวมความคืบหน้า</span>
                        <span class="fs-sm fw-bold text-info"><?= (int)$plannerData['overall_progress'] ?>%</span>
                    </div>
                    <div class="progress rounded-pill border border-color" style="height: 10px; background-color: var(--lv-progress-track);">
                        <div class="progress-bar rounded-pill" role="progressbar" style="width: <?= (int)$plannerData['overall_progress'] ?>%; background: linear-gradient(90deg, var(--lv-cyan), var(--lv-indigo));" aria-valuenow="<?= (int)$plannerData['overall_progress'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    
                    <button class="btn btn-premium w-100 rounded-pill mt-4 py-2.5 fw-bold" id="btnRebuildPath" type="button">
                        <i class="bi bi-arrow-repeat me-1"></i> Rebuild Learning Path
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container-xl">
        
        <!-- 2. Recommended Next Step Banner -->
        <div class="dashboard-card rounded-4 p-4 mb-5 shadow-sm d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(6, 182, 212, 0.12) 0%, rgba(99, 102, 241, 0.12) 100%) !important; border: 1px solid var(--lv-cyan) !important;">
            <div class="d-flex gap-3 align-items-start position-relative z-1">
                <div class="bg-info bg-opacity-25 text-info border border-info border-opacity-50 rounded-circle p-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-lg" style="width: 58px; height: 58px;">
                    <i class="bi bi-lightning-charge-fill fs-3 text-info"></i>
                </div>
                <div>
                    <span class="badge bg-info text-white fw-bold mb-1"><i class="bi bi-stars me-1"></i> <?= htmlspecialchars($recommendedNextStep['badge']) ?></span>
                    <h4 class="fw-bold text-main mb-1"><?= htmlspecialchars($recommendedNextStep['title']) ?></h4>
                    <p class="text-muted-custom mb-0 fs-sm"><?= htmlspecialchars($recommendedNextStep['description']) ?></p>
                </div>
            </div>
            <div class="position-relative z-1">
                <a href="<?= htmlspecialchars($recommendedNextStep['link']) ?>" class="btn btn-premium px-5 py-2.5 rounded-pill shadow-lg fw-bold text-nowrap">เริ่มเรียนเลย <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
        </div>

        <div class="row g-4 g-lg-5">
            <!-- Left Column: Skill Gap & Progress HUD -->
            <div class="col-12 col-lg-4 order-2 order-lg-1">
                
                <h5 class="fw-bold mb-4 text-main d-flex align-items-center fs-5"><i class="bi bi-trophy-fill text-warning me-2"></i> สถานะการเรียนรู้</h5>
                
                <div class="row g-3 mb-4 align-items-stretch">
                    <div class="col-6">
                        <div class="dashboard-card rounded-4 stat-card-box h-100">
                            <div class="stat-card-title">Projects Cleared</div>
                            <div class="stat-card-value text-info">
                                <?= (int)$userStats['projects_cleared'] ?> <span class="fs-6 text-muted-custom fw-normal">/ <?= (int)$userStats['total_projects'] ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="dashboard-card rounded-4 stat-card-box h-100">
                            <div class="stat-card-title">Coding Labs</div>
                            <div class="stat-card-value text-success">
                                <?= (int)$userStats['coding_labs_cleared'] ?> <span class="fs-6 text-muted-custom fw-normal">/ <?= (int)$userStats['total_coding_labs'] ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-main d-flex align-items-center"><i class="bi bi-cpu-fill text-info me-2"></i> Skill Gap Analysis</h5>
                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2.5 py-1">Class Match <?= (int)$userStats['class_match_percent'] ?>%</span>
                </div>
                
                <div class="dashboard-card rounded-4 p-0 border-color overflow-hidden mb-4">
                    <div class="table-responsive" id="skillGapContainer">
                        <table class="table table-borderless table-skill-gap mb-0 align-middle">
                            <thead class="bg-secondary bg-opacity-10 border-bottom border-color">
                                <tr>
                                    <th class="fs-sm fw-medium text-muted-custom py-3 ps-4">ทักษะ</th>
                                    <th class="fs-sm fw-medium text-muted-custom py-3 text-center">คุณมี</th>
                                    <th class="fs-sm fw-medium text-muted-custom py-3 text-center pe-4">เป้าหมาย</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($skillGapList as $index => $item): ?>
                                    <tr class="<?= ($index < count($skillGapList) - 1) ? 'border-bottom border-color border-opacity-25' : '' ?>">
                                        <td class="ps-4 fw-medium text-main"><?= htmlspecialchars($item['skill']) ?></td>
                                        <td class="text-center">
                                            <?php if ($item['status'] === 'cleared'): ?>
                                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                            <?php elseif ($item['status'] === 'percent'): ?>
                                                <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25"><?= (int)$item['percent'] ?>%</span>
                                            <?php else: ?>
                                                <i class="bi bi-x-circle text-muted-custom opacity-50 fs-5"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center pe-4">
                                            <?php if ($item['target']): ?>
                                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <h5 class="fw-bold mb-3 text-main d-flex align-items-center"><i class="bi bi-flag-fill text-primary me-2"></i> Milestones</h5>
                <div class="dashboard-card rounded-4 p-4 border-color">
                    <?php foreach ($milestonesList as $index => $milestone): ?>
                        <div class="d-flex align-items-center gap-3 <?= ($milestone['status'] === 'locked') ? 'opacity-50' : '' ?> <?= ($index < count($milestonesList) - 1) ? 'mb-4' : '' ?>">
                            <div class="fs-3 flex-shrink-0"><i class="bi <?= htmlspecialchars($milestone['icon']) ?>"></i></div>
                            <div>
                                <h6 class="fw-bold mb-0 text-main"><?= htmlspecialchars($milestone['title']) ?></h6>
                                <p class="fs-sm text-muted-custom mb-0"><?= htmlspecialchars($milestone['description']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>

            <!-- Right Column: Roadmap Visualizer (RPG Skill Tree) -->
            <div class="col-12 col-lg-8 order-1 order-lg-2">
                
                <h5 class="fw-bold mb-4 text-main d-flex align-items-center fs-5"><i class="bi bi-diagram-3-fill text-info me-2"></i> Roadmap (เส้นทางการเรียน)</h5>

                <div class="dashboard-card rounded-4 p-4 p-md-5 border-color overflow-hidden" id="roadmapContainer" style="min-height: 600px;">
                    
                    <div class="roadmap-tree position-relative">
                        
                        <?php foreach ($roadmapNodes as $node): ?>
                            <div class="roadmap-node <?= htmlspecialchars($node['status']) ?> d-flex">
                                <div class="node-indicator">
                                    <?php if ($node['status'] === 'completed'): ?>
                                        <div class="node-icon bg-success text-white"><i class="bi bi-check-lg"></i></div>
                                        <?php if ($node['has_line']): ?><div class="node-line bg-success"></div><?php endif; ?>
                                    <?php elseif ($node['status'] === 'active'): ?>
                                        <div class="node-icon bg-info text-white pulse-ring-active shadow-lg" style="transform: scale(1.15); z-index:3;"><i class="bi bi-play-fill fs-4"></i></div>
                                        <?php if ($node['has_line']): ?><div class="node-line bg-secondary bg-opacity-50"></div><?php endif; ?>
                                    <?php else: ?>
                                        <div class="node-icon bg-secondary bg-opacity-25 border border-color text-muted-custom"><i class="bi bi-lock-fill"></i></div>
                                        <?php if ($node['has_line']): ?><div class="node-line bg-secondary bg-opacity-25"></div><?php endif; ?>
                                    <?php endif; ?>
                                </div>

                                <?php if ($node['status'] === 'completed'): ?>
                                    <div class="node-content dashboard-card border-success border-opacity-50 p-3 p-md-4 rounded-4 w-100 ms-3 ms-md-4 mb-4">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 mb-2"><i class="bi bi-check-circle me-1"></i> <?= htmlspecialchars($node['badge']) ?></span>
                                                <h5 class="fw-bold text-main mb-1"><?= htmlspecialchars($node['title']) ?></h5>
                                                <p class="fs-sm text-muted-custom mb-2"><?= htmlspecialchars($node['description']) ?></p>
                                            </div>
                                            <div class="text-success fw-bold fs-5"><?= (int)$node['progress'] ?>%</div>
                                        </div>
                                        <div class="fs-xs text-muted-custom"><i class="bi <?= htmlspecialchars($node['footer_icon']) ?> me-1"></i> <?= htmlspecialchars($node['footer_text']) ?></div>
                                    </div>
                                
                                <?php elseif ($node['status'] === 'active'): ?>
                                    <div class="node-content dashboard-card border-info p-4 rounded-4 w-100 ms-3 ms-md-4 mb-4 position-relative overflow-hidden" style="background: rgba(6, 182, 212, 0.08) !important;">
                                        <div class="d-flex justify-content-between align-items-start position-relative z-1">
                                            <div>
                                                <span class="badge bg-info text-white fw-bold mb-2 shadow-sm"><i class="bi bi-fire me-1"></i> <?= htmlspecialchars($node['badge']) ?></span>
                                                <h4 class="fw-bold text-main mb-1"><?= htmlspecialchars($node['title']) ?></h4>
                                                <p class="fs-sm text-muted-custom mb-3"><?= htmlspecialchars($node['description']) ?></p>
                                            </div>
                                            <div class="text-info fw-bold fs-4"><?= (int)$node['progress'] ?>%</div>
                                        </div>
                                        <div class="progress mb-3 position-relative z-1 rounded-pill" style="height: 8px; background-color: var(--lv-progress-track);">
                                            <div class="progress-bar bg-info rounded-pill" role="progressbar" style="width: <?= (int)$node['progress'] ?>%;" aria-valuenow="<?= (int)$node['progress'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center position-relative z-1 mt-3 pt-3 border-top border-color">
                                            <div class="fs-xs text-muted-custom"><i class="bi <?= htmlspecialchars($node['footer_icon']) ?> me-1"></i> <?= htmlspecialchars($node['footer_text']) ?></div>
                                            <?php if (!empty($node['link'])): ?>
                                                <a href="<?= htmlspecialchars($node['link']) ?>" class="btn btn-sm btn-premium rounded-pill px-4 py-1.5 fw-bold">เรียนต่อ <i class="bi bi-arrow-right ms-1"></i></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                <?php else: ?>
                                    <div class="node-content dashboard-card border-color p-3 p-md-4 rounded-4 w-100 ms-3 ms-md-4 mb-4 opacity-75">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <span class="badge bg-secondary bg-opacity-25 text-muted-custom border border-color mb-2"><i class="bi bi-lock-fill me-1"></i> <?= htmlspecialchars($node['badge']) ?></span>
                                                <h5 class="fw-bold text-muted-custom mb-1"><?= htmlspecialchars($node['title']) ?></h5>
                                                <p class="fs-sm text-muted-custom mb-2"><?= htmlspecialchars($node['description']) ?></p>
                                            </div>
                                        </div>
                                        <?php if (!empty($node['prereq_warning'])): ?>
                                            <div class="fs-xs text-muted-custom mt-2 pt-2 border-top border-color">
                                                <?php foreach ($node['prereq_warning'] as $warning): ?>
                                                    <span class="text-danger d-block mb-1"><i class="bi bi-x-circle me-1"></i> <?= htmlspecialchars($warning) ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========================================================================= -->
<!-- 3. JAVASCRIPT LAYER & LEVELUP-SWEETALERT INTEGRATION -->
<!-- ========================================================================= -->
<script>
    // ฟังก์ชันจัดการ Slider เวลาเรียน
    function initSlider() {
        const timeSlider = document.getElementById('timeSlider');
        const timeDisplay = document.getElementById('timeDisplay');
        const estimatedTime = document.getElementById('estimatedTime');

        if (timeSlider && timeDisplay && estimatedTime) {
            timeSlider.addEventListener('input', function() {
                const hours = parseInt(this.value, 10) || 1;
                timeDisplay.textContent = hours;

                const months = Math.max(2, Math.round(10 / hours));
                estimatedTime.textContent = months + ' เดือน';
            });
        }
    }

    // ฟังก์ชันจัดการ LevelUpAlerts
    function initLevelUpAlerts() {
        // 1. ปุ่ม Rebuild Learning Path
        const btnRebuild = document.getElementById('btnRebuildPath');
        if (btnRebuild) {
            btnRebuild.addEventListener('click', function() {
                if (typeof LevelUpAlert !== 'undefined') {
                    LevelUpAlert.confirm(
                        'คำนวณเส้นทางการเรียนใหม่?',
                        'ระบบ AI จะทำการปรับโครงสร้างแผนการเรียนตามเป้าหมายและเวลาที่คุณระบุ'
                    ).then((isConfirmed) => {
                        if (isConfirmed) {
                            LevelUpAlert.loading('AI กำลังวิเคราะห์และปรับปรุง Roadmap...');
                            
                            setTimeout(() => {
                                LevelUpAlert.close();
                                LevelUpAlert.success(
                                    'ปรับปรุงเส้นทางสำเร็จ!',
                                    'ระบบ AI ได้ปรับเปลี่ยนแผนการเรียนของคุณเรียบร้อยแล้ว'
                                );
                            }, 1500);
                        }
                    });
                }
            });
        }

        // 2. ปุ่มแชร์ Roadmap
        const btnShare = document.getElementById('btnShareRoadmap');
        if (btnShare) {
            btnShare.addEventListener('click', function() {
                if (typeof LevelUpAlert !== 'undefined') {
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(window.location.href).then(() => {
                            LevelUpAlert.toast('คัดลอกลิงก์ Roadmap เรียบร้อยแล้ว', 'success');
                        }).catch(() => {
                            LevelUpAlert.toast('คัดลอกลิงก์เรียบร้อยแล้ว', 'success');
                        });
                    } else {
                        LevelUpAlert.toast('คัดลอกลิงก์ Roadmap เรียบร้อยแล้ว', 'success');
                    }
                }
            });
        }

        // 3. ปุ่ม Export PDF
        const btnExport = document.getElementById('btnExportPdf');
        if (btnExport) {
            btnExport.addEventListener('click', function() {
                if (typeof LevelUpAlert !== 'undefined') {
                    LevelUpAlert.loading('กำลังจัดเตรียมไฟล์ PDF...');
                    setTimeout(() => {
                        LevelUpAlert.close();
                        LevelUpAlert.toast('เริ่มดาวน์โหลด PDF เรียบร้อยแล้ว', 'success');
                    }, 1200);
                }
            });
        }

        // 4. การคลิกเลเวลที่ยังล็อกอยู่ (Locked Roadmap Nodes)
        const lockedNodes = document.querySelectorAll('.roadmap-node.locked');
        lockedNodes.forEach(node => {
            node.addEventListener('click', function() {
                const titleElement = this.querySelector('h5');
                const nodeTitle = titleElement ? titleElement.textContent.trim() : 'บทเรียนนี้';

                if (typeof LevelUpAlert !== 'undefined') {
                    LevelUpAlert.warning(
                        'เลเวลนี้ยังไม่เปิดใช้งาน',
                        `คุณต้องผ่านเงื่อนไข Prerequisite ก่อนจึงจะสามารถปลดล็อกบทเรียน "${nodeTitle}" ได้`
                    );
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initSlider();
            initLevelUpAlerts();
        });
    } else {
        initSlider();
        initLevelUpAlerts();
    }
</script>

<?php include '../includes/footer.php'; ?>