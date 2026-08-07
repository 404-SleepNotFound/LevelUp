<?php
$ROOT_PATH = '../';
/**
 * Component: Quizzes, Labs & Assignments History
 * File: quizzes-labs.php
 * Description: ส่วนแสดงประวัติการทำ Quiz, Coding Lab, Virtual Lab และ Assignment (Loaded via AJAX into learning-history.php)
 */

// Safety check for helper function
if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

// =========================================================================
// DATA PREPARATION (Mock Data for Quizzes & Labs)
// =========================================================================

// Quiz History Data
$quizHistory = [
    [
        'title' => 'SQL Advanced Queries Quiz', 
        'score' => 90, 
        'max_score' => 100, 
        'attempts' => 1, 
        'duration' => '12 min', 
        'status' => 'Passed', 
        'date' => '2026-07-23 09:45'
    ],
    [
        'title' => 'PHP Security & Vulnerabilities', 
        'score' => 100, 
        'max_score' => 100, 
        'attempts' => 2, 
        'duration' => '18 min', 
        'status' => 'Passed', 
        'date' => '2026-07-19 14:10'
    ],
    [
        'title' => 'CSS Grid & Flexbox Speedrun', 
        'score' => 65, 
        'max_score' => 100, 
        'attempts' => 3, 
        'duration' => '15 min', 
        'status' => 'Failed', 
        'date' => '2026-07-15 10:00'
    ]
];

// Coding Lab History Data
$codingLabHistory = [
    [
        'lab_name' => 'PHP REST API Authentication Lab', 
        'runs' => 14, 
        'compiles' => 8, 
        'test_cases_passed' => '10/10', 
        'score' => 100, 
        'time_spent' => '35 min'
    ],
    [
        'lab_name' => 'Algorithms: Binary Tree Search', 
        'runs' => 22, 
        'compiles' => 15, 
        'test_cases_passed' => '8/10', 
        'score' => 80, 
        'time_spent' => '50 min'
    ]
];

// Virtual Lab History Data
$virtualLabHistory = [
    [
        'category' => 'Docker Lab', 
        'lab' => 'Multi-Container Deployment', 
        'usage_time' => '1 hr 15 min', 
        'sessions' => 3, 
        'score' => 95, 
        'date' => '2026-07-23 10:20'
    ],
    [
        'category' => 'Linux Lab', 
        'lab' => 'Bash Scripting & File Permissions', 
        'usage_time' => '45 min', 
        'sessions' => 2, 
        'score' => 90, 
        'date' => '2026-07-18 16:00'
    ],
    [
        'category' => 'SQL Lab', 
        'lab' => 'Index Optimization Sandbox', 
        'usage_time' => '30 min', 
        'sessions' => 1, 
        'score' => 100, 
        'date' => '2026-07-12 13:40'
    ]
];

// Assignment History Data
$assignmentHistory = [
    [
        'assignment' => 'E-Commerce REST API v1 Project', 
        'submitted_date' => '2026-07-22 16:30', 
        'score' => 98, 
        'feedback' => 'Excellent code architecture and clean Security implementation!', 
        'file_name' => 'alex_rest_api_v1.zip'
    ],
    [
        'assignment' => 'Dockerized Microservices Setup', 
        'submitted_date' => '2026-06-10 18:00', 
        'score' => 92, 
        'feedback' => 'Good containerization structure. Add health checks next time.', 
        'file_name' => 'docker_project.tar.gz'
    ]
];
?>

<!-- 🎨 Dynamic Light & Dark Theme CSS Variables & Overrides -->
<style>
    /* =============================================================
       1. DARK MODE VARIABLES (DEFAULT / ROOT)
       ============================================================= */
    :root, html[data-theme="dark"], [data-bs-theme="dark"], body.dark-mode {
        --ql-card-bg: rgba(16, 24, 48, 0.65);
        --ql-card-border: rgba(255, 255, 255, 0.08);
        --ql-text-main: #f8fafc;
        --ql-text-muted: rgba(255, 255, 255, 0.55);
        --ql-table-border: rgba(255, 255, 255, 0.08);
        --ql-table-hover: rgba(255, 255, 255, 0.04);
        
        --ql-input-bg: #0f172a;
        --ql-input-border: rgba(255, 255, 255, 0.15);
        --ql-input-text: #f8fafc;
        
        /* Badges (Dark Mode) */
        --badge-warning-bg: rgba(255, 193, 7, 0.15);
        --badge-warning-text: #ffc107;
        --badge-warning-border: rgba(255, 193, 7, 0.3);

        --badge-success-bg: rgba(25, 135, 84, 0.15);
        --badge-success-text: #2ecc71;
        --badge-success-border: rgba(25, 135, 84, 0.3);

        --badge-info-bg: rgba(13, 202, 240, 0.15);
        --badge-info-text: #0dcaf0;
        --badge-info-border: rgba(13, 202, 240, 0.3);

        --badge-purple-bg: rgba(111, 66, 193, 0.18);
        --badge-purple-text: #c084fc;
        --badge-purple-border: rgba(168, 85, 247, 0.3);

        --badge-category-bg: rgba(255, 255, 255, 0.08);
        --badge-category-text: #cbd5e1;
        --badge-category-border: rgba(255, 255, 255, 0.12);

        --ql-btn-border: rgba(255, 255, 255, 0.2);
        --ql-btn-text: #cbd5e1;
        --ql-btn-hover-bg: rgba(255, 255, 255, 0.1);
        --text-purple-clr: #a855f7;
    }

    /* =============================================================
       2. LIGHT MODE OVERRIDES
       ============================================================= */
    html[data-theme="light"], 
    [data-bs-theme="light"], 
    body:not(.dark-mode) {
        --ql-card-bg: #ffffff;
        --ql-card-border: rgba(0, 0, 0, 0.08);
        --ql-text-main: #0f172a;
        --ql-text-muted: #64748b;
        --ql-table-border: rgba(0, 0, 0, 0.08);
        --ql-table-hover: rgba(0, 0, 0, 0.025);

        --ql-input-bg: #f8fafc;
        --ql-input-border: #cbd5e1;
        --ql-input-text: #0f172a;

        /* Badges (Light Mode) */
        --badge-warning-bg: #fffbeb;
        --badge-warning-text: #b45309;
        --badge-warning-border: #fde68a;

        --badge-success-bg: #f0fdf4;
        --badge-success-text: #15803d;
        --badge-success-border: #bbf7d0;

        --badge-info-bg: #f0f9ff;
        --badge-info-text: #0369a1;
        --badge-info-border: #bae6fd;

        --badge-purple-bg: #faf5ff;
        --badge-purple-text: #6b21a8;
        --badge-purple-border: #e9d5ff;

        --badge-category-bg: #f1f5f9;
        --badge-category-text: #475569;
        --badge-category-border: #e2e8f0;

        --ql-btn-border: #cbd5e1;
        --ql-btn-text: #475569;
        --ql-btn-hover-bg: #f1f5f9;
        --text-purple-clr: #7c3aed;
    }

    /* =============================================================
       3. GLOBAL HEADER & TYPOGRAPHY FIX (PREVENTS TEXT BLENDING)
       ============================================================= */
    html[data-theme="light"] h1,
    html[data-theme="light"] h2,
    html[data-theme="light"] h3,
    html[data-theme="light"] h4,
    html[data-theme="light"] h5,
    html[data-theme="light"] h6,
    html[data-theme="light"] .page-title,
    html[data-theme="light"] .page-header-title,
    [data-bs-theme="light"] h1,
    [data-bs-theme="light"] h2,
    [data-bs-theme="light"] h3,
    [data-bs-theme="light"] h4,
    [data-bs-theme="light"] h5,
    [data-bs-theme="light"] h6 {
        color: #0f172a !important;
    }

    html[data-theme="light"] .text-white,
    html[data-theme="light"] .text-white-50 {
        color: #334155 !important;
    }

    /* Card Box Styles */
    .dashboard-card,
    .card,
    .lab-builder-card {
        background: var(--ql-card-bg) !important;
        border: 1px solid var(--ql-card-border) !important;
        border-radius: 16px;
        color: var(--ql-text-main) !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        backdrop-filter: blur(10px);
        transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
    }

    /* Global Form Controls (Inputs, Selects, Textareas) Light/Dark Support */
    .form-control, 
    .form-select, 
    textarea,
    input[type="text"],
    input[type="number"],
    select {
        background-color: var(--ql-input-bg) !important;
        border: 1px solid var(--ql-input-border) !important;
        color: var(--ql-input-text) !important;
        transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
    }

    .form-select option {
        background-color: var(--ql-card-bg) !important;
        color: var(--ql-text-main) !important;
    }

    .form-label, 
    label {
        color: var(--ql-text-main) !important;
    }

    /* Table custom adjustments */
    .table-custom {
        --bs-table-bg: transparent !important;
        --bs-table-accent-bg: transparent !important;
        --bs-table-hover-bg: var(--ql-table-hover) !important;
        --bs-table-color: var(--ql-text-main) !important;
        color: var(--ql-text-main) !important;
    }
    .table-custom th, 
    .table-custom td {
        background-color: transparent !important;
        color: var(--ql-text-main) !important;
        border-bottom-color: var(--ql-table-border) !important;
    }
    .table-custom th {
        color: var(--ql-text-muted) !important;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .quizzes-labs-component .text-muted,
    .text-muted {
        color: var(--ql-text-muted) !important;
    }

    /* Standardized Soft Badges */
    .badge-soft-warning {
        background-color: var(--badge-warning-bg) !important;
        color: var(--badge-warning-text) !important;
        border: 1px solid var(--badge-warning-border) !important;
    }
    .badge-soft-success {
        background-color: var(--badge-success-bg) !important;
        color: var(--badge-success-text) !important;
        border: 1px solid var(--badge-success-border) !important;
    }
    .badge-soft-info {
        background-color: var(--badge-info-bg) !important;
        color: var(--badge-info-text) !important;
        border: 1px solid var(--badge-info-border) !important;
    }
    .badge-soft-purple {
        background-color: var(--badge-purple-bg) !important;
        color: var(--badge-purple-text) !important;
        border: 1px solid var(--badge-purple-border) !important;
    }
    .badge-soft-category {
        background-color: var(--badge-category-bg) !important;
        color: var(--badge-category-text) !important;
        border: 1px solid var(--badge-category-border) !important;
    }

    /* Buttons adaptation */
    .quizzes-labs-component .btn-outline-secondary {
        border-color: var(--ql-btn-border) !important;
        color: var(--ql-btn-text) !important;
    }
    .quizzes-labs-component .btn-outline-secondary:hover {
        background-color: var(--ql-btn-hover-bg) !important;
        color: var(--ql-text-main) !important;
    }

    .text-purple {
        color: var(--text-purple-clr) !important;
    }
</style>

<div class="quizzes-labs-component animate__animated animate__fadeIn">
    <div class="row g-4">
        
        <!-- 1. QUIZ HISTORY -->
        <div class="col-12 col-lg-6">
            <div class="dashboard-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-patch-question me-2 text-warning"></i>Quiz History (ประวัติการทำแบบทดสอบ)
                    </h5>
                    <span class="badge badge-soft-warning px-2.5 py-1">
                        <?= count($quizHistory) ?> Quizzes
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-custom align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">QUIZ</th>
                                <th scope="col">คะแนน</th>
                                <th scope="col">จำนวนครั้ง</th>
                                <th scope="col">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($quizHistory)): ?>
                                <?php foreach($quizHistory as $q): ?>
                                    <tr class="searchable-item" data-type="quiz" data-status="<?= strtolower(e($q['status'])) ?>">
                                        <td class="fw-semibold">
                                            <div><?= e($q['title']) ?></div>
                                            <small class="d-block text-muted fs-7"><?= e($q['date']) ?></small>
                                        </td>
                                        <td>
                                            <strong class="text-primary"><?= e($q['score']) ?></strong>
                                            <span class="text-muted">/ <?= e($q['max_score']) ?></span>
                                        </td>
                                        <td class="text-muted"><?= e($q['attempts']) ?> ครั้ง</td>
                                        <td>
                                            <span class="badge <?= $q['status'] === 'Passed' ? 'bg-success' : 'bg-danger' ?>">
                                                <?= e($q['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">ไม่พบข้อมูลแบบทดสอบ</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 2. CODING LAB HISTORY -->
        <div class="col-12 col-lg-6">
            <div class="dashboard-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-code-square me-2 text-success"></i>Coding Lab History
                    </h5>
                    <span class="badge badge-soft-success px-2.5 py-1">
                        <?= count($codingLabHistory) ?> Labs
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-custom align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">LAB NAME</th>
                                <th scope="col">RUN/COMPILE</th>
                                <th scope="col">TEST CASES</th>
                                <th scope="col">คะแนน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($codingLabHistory)): ?>
                                <?php foreach($codingLabHistory as $cl): ?>
                                    <tr class="searchable-item" data-type="lab">
                                        <td class="fw-semibold">
                                            <div><?= e($cl['lab_name']) ?></div>
                                            <small class="d-block text-muted fs-7">เวลาที่ใช้: <?= e($cl['time_spent']) ?></small>
                                        </td>
                                        <td class="text-muted"><?= e($cl['runs']) ?> / <?= e($cl['compiles']) ?></td>
                                        <td><span class="text-success fw-semibold"><?= e($cl['test_cases_passed']) ?></span></td>
                                        <td>
                                            <span class="badge badge-soft-success fw-bold px-2 py-1">
                                                <?= e($cl['score']) ?>/100
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">ไม่พบข้อมูล Coding Lab</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 3. VIRTUAL LAB HISTORY (SANDBOX) -->
        <div class="col-12 col-lg-6">
            <div class="dashboard-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-terminal me-2 text-info"></i>Virtual Lab History (Sandbox)
                    </h5>
                    <span class="badge badge-soft-info px-2.5 py-1">
                        <?= count($virtualLabHistory) ?> Environments
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-custom align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">CATEGORY / LAB</th>
                                <th scope="col">เวลาใช้งาน</th>
                                <th scope="col">SESSIONS</th>
                                <th scope="col">คะแนน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($virtualLabHistory)): ?>
                                <?php foreach($virtualLabHistory as $vl): ?>
                                    <tr class="searchable-item" data-type="lab">
                                        <td>
                                            <div class="fw-semibold mb-1"><?= e($vl['lab']) ?></div>
                                            <span class="badge badge-soft-category fs-7"><?= e($vl['category']) ?></span>
                                        </td>
                                        <td class="text-muted"><?= e($vl['usage_time']) ?></td>
                                        <td class="text-muted"><?= e($vl['sessions']) ?> ครั้ง</td>
                                        <td class="text-info fw-bold"><?= e($vl['score']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">ไม่พบข้อมูล Virtual Lab</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 4. ASSIGNMENT HISTORY -->
        <div class="col-12 col-lg-6">
            <div class="dashboard-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-file-earmark-arrow-up me-2 text-purple"></i>Assignment History (การส่งงาน)
                    </h5>
                    <span class="badge badge-soft-purple px-2.5 py-1">
                        <?= count($assignmentHistory) ?> Submissions
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-custom align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">ASSIGNMENT</th>
                                <th scope="col">คะแนน</th>
                                <th scope="col">FEEDBACK</th>
                                <th scope="col">ไฟล์</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($assignmentHistory)): ?>
                                <?php foreach($assignmentHistory as $as): ?>
                                    <tr class="searchable-item" data-type="assignment">
                                        <td class="fw-semibold">
                                            <div><?= e($as['assignment']) ?></div>
                                            <small class="d-block text-muted fs-7">ส่งเมื่อ: <?= e($as['submitted_date']) ?></small>
                                        </td>
                                        <td class="text-warning fw-bold"><?= e($as['score']) ?></td>
                                        <td class="small text-muted text-truncate" style="max-width: 180px;" title="<?= e($as['feedback']) ?>">
                                            <?= e($as['feedback']) ?>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-secondary p-1 px-2" title="Download <?= e($as['file_name']) ?>">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">ไม่พบข้อมูลประวัติการส่งงาน</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>