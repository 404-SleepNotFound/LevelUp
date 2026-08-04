<?php
$ROOT_PATH = '../';
/**
 * LevelUp - E-Learning Platform
 * Component: certificates-stats.php (Certificates & Detailed Statistics)
 * Description: แสดงผลประวัติใบประกาศนียบัตร และสถิติการเรียนรู้เชิงลึก สำหรับโหลดผ่าน AJAX
 */

// =========================================================================
// HELPER FUNCTIONS
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
// DATA PREPARATION (PHP Mock Data for Component)
// =========================================================================

// Certificate History Data
$certificateHistory = [
    [
        'title' => 'Certified Full-Stack PHP Specialist',
        'cert_id' => 'LVL-PHP-9942',
        'issued_date' => '2026-06-15',
        'pdf_url' => '#',
        'verify_hash' => '0x8F92A1B3C4D5'
    ],
    [
        'title' => 'DevOps & Container Fundamentals',
        'cert_id' => 'LVL-DO-4421',
        'issued_date' => '2026-04-28',
        'pdf_url' => '#',
        'verify_hash' => '0x7E61B2C3D4E5'
    ]
];

// Learning Statistics Data
$learningStats = [
    'daily_avg' => '2.4 Hours',
    'weekly_avg' => '16.8 Hours',
    'monthly_total' => '68.5 Hours',
    'avg_score' => '92.4%',
    'lab_completion_rate' => '94%',
    'quiz_completion_rate' => '88%'
];
?>

<!-- CERTIFICATES & DETAILED STATS COMPONENT CONTENT -->
<div class="row g-4">
    <!-- CERTIFICATE HISTORY -->
    <div class="col-12 col-lg-6">
        <div class="dashboard-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-white mb-0">
                    <i class="bi bi-patch-check-fill me-2 text-warning"></i>Certificate History
                </h5>
                <span class="badge bg-warning bg-opacity-20 text-warning border border-warning border-opacity-25 px-2.5 py-1 rounded-pill small">
                    <?= e(count($certificateHistory)) ?> Certificates
                </span>
            </div>
            
            <?php
$ROOT_PATH = '../';
if (!empty($certificateHistory)): ?>
                <?php
$ROOT_PATH = '../';
foreach ($certificateHistory as $cert): ?>
                    <div class="p-3 mb-3 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-25 d-flex justify-content-between align-items-center searchable-item" data-type="certificate">
                        <div>
                            <h6 class="fw-bold text-white mb-1"><?= e($cert['title']) ?></h6>
                            <div class="small text-white-50">
                                Cert ID: <span class="text-cyan font-monospace"><?= e($cert['cert_id']) ?></span> | Issued: <?= e($cert['issued_date']) ?>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-info" onclick="verifyCertificate('<?= e($cert['verify_hash']) ?>')" title="Verify Certificate Hash">
                                <i class="bi bi-shield-check"></i>
                            </button>
                            <a href="<?= e($cert['pdf_url']) ?>" class="btn btn-sm btn-premium" title="Download Certificate PDF">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                <?php
$ROOT_PATH = '../';
endforeach; ?>
            <?php
$ROOT_PATH = '../';
else: ?>
                <div class="text-center py-4 text-white-50">
                    <i class="bi bi-award fs-1 d-block mb-2 text-muted"></i>
                    ยังไม่มีประวัติใบประกาศนียบัตร
                </div>
            <?php
$ROOT_PATH = '../';
endif; ?>
        </div>
    </div>

    <!-- LEARNING STATISTICS -->
    <div class="col-12 col-lg-6">
        <div class="dashboard-card p-4 h-100">
            <h5 class="fw-bold text-white mb-3">
                <i class="bi bi-bar-chart-line me-2 text-info"></i>Detailed Statistics
            </h5>
            <div class="row g-3">
                <div class="col-6">
                    <div class="p-3 rounded-3 bg-dark bg-opacity-40 border border-secondary border-opacity-25">
                        <small class="text-white-50 d-block mb-1">ชั่วโมงเรียน / วัน (เฉลี่ย)</small>
                        <h5 class="fw-bold text-white mb-0"><?= e($learningStats['daily_avg']) ?></h5>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 rounded-3 bg-dark bg-opacity-40 border border-secondary border-opacity-25">
                        <small class="text-white-50 d-block mb-1">ชั่วโมงเรียน / สัปดาห์</small>
                        <h5 class="fw-bold text-cyan mb-0"><?= e($learningStats['weekly_avg']) ?></h5>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 rounded-3 bg-dark bg-opacity-40 border border-secondary border-opacity-25">
                        <small class="text-white-50 d-block mb-1">เวลารวมประจำเดือน</small>
                        <h5 class="fw-bold text-purple mb-0"><?= e($learningStats['monthly_total']) ?></h5>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 rounded-3 bg-dark bg-opacity-40 border border-secondary border-opacity-25">
                        <small class="text-white-50 d-block mb-1">คะแนนสอบเฉลี่ย</small>
                        <h5 class="fw-bold text-warning mb-0"><?= e($learningStats['avg_score']) ?></h5>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 rounded-3 bg-dark bg-opacity-40 border border-secondary border-opacity-25">
                        <small class="text-white-50 d-block mb-1">Lab Completion Rate</small>
                        <h5 class="fw-bold text-success mb-0"><?= e($learningStats['lab_completion_rate']) ?></h5>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 rounded-3 bg-dark bg-opacity-40 border border-secondary border-opacity-25">
                        <small class="text-white-50 d-block mb-1">Quiz Pass Rate</small>
                        <h5 class="fw-bold text-warning mb-0"><?= e($learningStats['quiz_completion_rate']) ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
