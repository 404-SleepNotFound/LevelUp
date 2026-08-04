<?php
$ROOT_PATH = '../';
/**
 * LevelUp - E-Learning Platform
 * Component: ai-system-logs.php (ประวัติการใช้งาน AI และ Log ระบบความปลอดภัย)
 * Loaded via: jQuery + AJAX into #history-content-placeholder
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
// 1. DATA PREPARATION (Mock Data for Component)
// =========================================================================

// AI Learning Interactions History Data
$aiHistory = [
    ['type' => 'AI Tutor', 'query' => 'Explain CORS Policy in PHP APIs', 'date' => '2026-07-23 11:10', 'tokens_used' => 320],
    ['type' => 'AI Debug', 'query' => 'Fix Fatal Error: Uncaught PDOException in Database.php', 'date' => '2026-07-22 15:05', 'tokens_used' => 540],
    ['type' => 'AI Refactor', 'query' => 'Optimize SQL Join query with 50,000 rows', 'date' => '2026-07-20 09:30', 'tokens_used' => 410],
    ['type' => 'AI Tutor', 'query' => 'How to implement JWT Token in Modern PHP Applications?', 'date' => '2026-07-18 14:22', 'tokens_used' => 680],
    ['type' => 'AI Debug', 'query' => 'Solve Docker Compose Port Binding Conflict (8080:80)', 'date' => '2026-07-15 16:45', 'tokens_used' => 290]
];

// Login & Security History Data
$loginHistory = [
    ['action' => 'Login Success', 'device' => 'MacBook Pro 16"', 'browser' => 'Chrome 126.0', 'ip' => '182.52.102.45', 'date' => '2026-07-23 08:30', 'session_time' => '3 hrs 20 mins', 'status' => 'Active'],
    ['action' => 'Login Success', 'device' => 'iPad Air 5', 'browser' => 'Safari Mobile', 'ip' => '182.52.102.45', 'date' => '2026-07-22 14:00', 'session_time' => '1 hr 45 mins', 'status' => 'Ended'],
    ['action' => 'Login Success', 'device' => 'iPhone 15 Pro', 'browser' => 'Safari Mobile', 'ip' => '110.168.75.12', 'date' => '2026-07-20 19:15', 'session_time' => '45 mins', 'status' => 'Ended'],
    ['action' => 'Password Changed', 'device' => 'MacBook Pro 16"', 'browser' => 'Chrome 126.0', 'ip' => '182.52.102.45', 'date' => '2026-07-10 10:00', 'session_time' => '-', 'status' => 'Completed']
];
?>

<!-- FIX BUG: High-Contrast & Theme-Adaptive Styles -->
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

    /* List Group Item Background & Text Color Override */
    .list-group-item-custom {
        background-color: transparent !important;
        color: inherit !important;
    }

    /* Adaptive Muted Text for High Contrast in Dark Mode */
    .text-adaptive-muted {
        color: #64748b !important;
    }
    [data-bs-theme="dark"] .text-adaptive-muted, 
    body.dark-mode .text-adaptive-muted {
        color: #94a3b8 !important;
    }

    /* Theme-Adaptive Soft Badges */
    .badge-soft-cyan {
        background-color: rgba(13, 202, 240, 0.18) !important;
        color: #0891b2 !important;
        border: 1px solid rgba(13, 202, 240, 0.35) !important;
    }
    .badge-soft-success {
        background-color: rgba(25, 135, 84, 0.18) !important;
        color: #15803d !important;
        border: 1px solid rgba(25, 135, 84, 0.35) !important;
    }
    .badge-soft-secondary {
        background-color: rgba(108, 117, 125, 0.18) !important;
        color: #4b5563 !important;
        border: 1px solid rgba(108, 117, 125, 0.35) !important;
    }

    /* Dark Mode Badges Adjustments */
    [data-bs-theme="dark"] .badge-soft-cyan, body.dark-mode .badge-soft-cyan { color: #0dcaf0 !important; }
    [data-bs-theme="dark"] .badge-soft-success, body.dark-mode .badge-soft-success { color: #2ecc71 !important; }
    [data-bs-theme="dark"] .badge-soft-secondary, body.dark-mode .badge-soft-secondary { color: #cbd5e1 !important; }
</style>

<!-- AI & SYSTEM LOGS COMPONENT CONTENT -->
<div class="row g-4 animate__animated animate__fadeIn">
    <!-- AI LEARNING HISTORY -->
    <div class="col-12 col-lg-7">
        <div class="dashboard-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-robot me-2 text-info"></i>AI Learning Interactions History
                </h5>
                <span class="badge badge-soft-cyan px-2.5 py-1 rounded-pill small">
                    Total Tokens: <?= e(array_sum(array_column($aiHistory, 'tokens_used'))) ?>
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-custom align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">ประเภท AI</th>
                            <th scope="col">คำถาม / PROMPT</th>
                            <th scope="col">วันที่</th>
                            <th scope="col">TOKENS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
$ROOT_PATH = '../';
foreach($aiHistory as $ai): ?>
                            <tr class="searchable-item" data-type="ai">
                                <td>
                                    <span class="badge badge-soft-cyan">
                                        <?= e($ai['type']) ?>
                                    </span>
                                </td>
                                <td class="fw-semibold">
                                    <div><?= e($ai['query']) ?></div>
                                </td>
                                <td class="text-adaptive-muted fs-7"><?= e($ai['date']) ?></td>
                                <td class="text-adaptive-muted fs-7"><?= e($ai['tokens_used']) ?></td>
                            </tr>
                        <?php
$ROOT_PATH = '../';
endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- LOGIN & ACTIVITY HISTORY -->
    <div class="col-12 col-lg-5">
        <div class="dashboard-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-shield-lock me-2 text-danger"></i>Login & Device Security
                </h5>
                <span class="text-adaptive-muted fs-7"><i class="bi bi-shield-check text-success me-1"></i>2FA Active</span>
            </div>
            <div class="list-group list-group-flush bg-transparent">
                <?php
$ROOT_PATH = '../';
foreach($loginHistory as $lh): ?>
                    <div class="list-group-item list-group-item-custom border-secondary border-opacity-25 px-0 py-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="fs-6 fw-semibold">
                                <i class="bi bi-laptop me-1 text-info"></i> <?= e($lh['device']) ?>
                            </strong>
                            <span class="badge <?= $lh['status'] === 'Active' ? 'badge-soft-success' : 'badge-soft-secondary' ?> fs-7">
                                <?= e($lh['status']) ?>
                            </span>
                        </div>
                        <div class="small text-adaptive-muted">Browser: <?= e($lh['browser']) ?> | IP: <?= e($lh['ip']) ?></div>
                        <div class="small text-adaptive-muted mt-1 d-flex justify-content-between">
                            <span>เวลา: <?= e($lh['date']) ?></span>
                            <?php
$ROOT_PATH = '../';
if ($lh['session_time'] !== '-'): ?>
                                <span class="text-info font-monospace">(<?= e($lh['session_time']) ?>)</span>
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
</div>
