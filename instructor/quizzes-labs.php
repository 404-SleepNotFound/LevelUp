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

<!-- FIX BUG: Custom Theme-Adaptive CSS for Tables and Badges -->
<style>
    .table-custom {
        --bs-table-bg: transparent !important;
        --bs-table-accent-bg: transparent !important;
        --bs-table-hover-bg: rgba(128, 128, 128, 0.08) !important;
        --bs-table-color: inherit !important;
        color: inherit !important;
    }
    .table-custom th, 
    .table-custom td {
        background-color: transparent !important;
        color: inherit !important;
        border-bottom-color: rgba(128, 128, 128, 0.15) !important;
    }
    .table-custom th {
        opacity: 0.65;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    /* Standardized Soft Badges (Light & Dark Compatible) */
    .badge-soft-warning {
        background-color: rgba(255, 193, 7, 0.18) !important;
        color: #b45309 !important;
        border: 1px solid rgba(255, 193, 7, 0.35) !important;
    }
    .badge-soft-success {
        background-color: rgba(25, 135, 84, 0.18) !important;
        color: #15803d !important;
        border: 1px solid rgba(25, 135, 84, 0.35) !important;
    }
    .badge-soft-info {
        background-color: rgba(13, 202, 240, 0.18) !important;
        color: #0369a1 !important;
        border: 1px solid rgba(13, 202, 240, 0.35) !important;
    }
    .badge-soft-purple {
        background-color: rgba(111, 66, 193, 0.18) !important;
        color: #6b21a8 !important;
        border: 1px solid rgba(111, 66, 193, 0.35) !important;
    }
    .badge-soft-category {
        background-color: rgba(108, 117, 125, 0.12) !important;
        color: #4b5563 !important;
        border: 1px solid rgba(108, 117, 125, 0.25) !important;
    }

    /* Dark Mode Adjustments for Soft Badges */
    [data-bs-theme="dark"] .badge-soft-warning, body.dark-mode .badge-soft-warning { color: #ffc107 !important; }
    [data-bs-theme="dark"] .badge-soft-success, body.dark-mode .badge-soft-success { color: #2ecc71 !important; }
    [data-bs-theme="dark"] .badge-soft-info, body.dark-mode .badge-soft-info { color: #0dcaf0 !important; }
    [data-bs-theme="dark"] .badge-soft-purple, body.dark-mode .badge-soft-purple { color: #a855f7 !important; }
    [data-bs-theme="dark"] .badge-soft-category, body.dark-mode .badge-soft-category { color: #cbd5e1 !important; }
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
                            <?php
$ROOT_PATH = '../';
if (!empty($quizHistory)): ?>
                                <?php
$ROOT_PATH = '../';
foreach($quizHistory as $q): ?>
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
                                <?php
$ROOT_PATH = '../';
endforeach; ?>
                            <?php
$ROOT_PATH = '../';
else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">ไม่พบข้อมูลแบบทดสอบ</td>
                                </tr>
                            <?php
$ROOT_PATH = '../';
endif; ?>
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
                            <?php
$ROOT_PATH = '../';
if (!empty($codingLabHistory)): ?>
                                <?php
$ROOT_PATH = '../';
foreach($codingLabHistory as $cl): ?>
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
                                <?php
$ROOT_PATH = '../';
endforeach; ?>
                            <?php
$ROOT_PATH = '../';
else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">ไม่พบข้อมูล Coding Lab</td>
                                </tr>
                            <?php
$ROOT_PATH = '../';
endif; ?>
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
                            <?php
$ROOT_PATH = '../';
if (!empty($virtualLabHistory)): ?>
                                <?php
$ROOT_PATH = '../';
foreach($virtualLabHistory as $vl): ?>
                                    <tr class="searchable-item" data-type="lab">
                                        <td>
                                            <div class="fw-semibold mb-1"><?= e($vl['lab']) ?></div>
                                            <span class="badge badge-soft-category fs-7"><?= e($vl['category']) ?></span>
                                        </td>
                                        <td class="text-muted"><?= e($vl['usage_time']) ?></td>
                                        <td class="text-muted"><?= e($vl['sessions']) ?> ครั้ง</td>
                                        <td class="text-info fw-bold"><?= e($vl['score']) ?></td>
                                    </tr>
                                <?php
$ROOT_PATH = '../';
endforeach; ?>
                            <?php
$ROOT_PATH = '../';
else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">ไม่พบข้อมูล Virtual Lab</td>
                                </tr>
                            <?php
$ROOT_PATH = '../';
endif; ?>
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
                            <?php
$ROOT_PATH = '../';
if (!empty($assignmentHistory)): ?>
                                <?php
$ROOT_PATH = '../';
foreach($assignmentHistory as $as): ?>
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
                                <?php
$ROOT_PATH = '../';
endforeach; ?>
                            <?php
$ROOT_PATH = '../';
else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">ไม่พบข้อมูลประวัติการส่งงาน</td>
                                </tr>
                            <?php
$ROOT_PATH = '../';
endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
