<?php
$ROOT_PATH = '../';
/**
 * LevelUp - E-Learning Platform
 * Component: skills-achievements.php
 * Description: ส่วนแสดงประวัติการพัฒนาทักษะ (Skill Progress), เส้นทางการเรียนรู้ (Learning Path), และเหรียญรางวัล (Achievements & Badges)
 * Usage: Loaded via AJAX into #history-content-placeholder
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
// 1. DATA PREPARATION (Mock Data for Skills & Achievements)
// =========================================================================

// Skill Progress History Data
$skillHistory = [
    ['skill' => 'PHP Security & Sanitization', 'level' => 'Level 4 (Expert)', 'xp_earned' => 2400, 'unlocked_date' => '2026-07-22'],
    ['skill' => 'Docker & Containerization', 'level' => 'Level 3 (Advanced)', 'xp_earned' => 1800, 'unlocked_date' => '2026-07-18'],
    ['skill' => 'SQL Query Optimization', 'level' => 'Level 3 (Advanced)', 'xp_earned' => 1500, 'unlocked_date' => '2026-07-10']
];

// Learning Path History Data
$pathHistory = [
    ['path' => 'Full-Stack Web Developer Path', 'status' => 'Active', 'switched_on' => '2026-05-01', 'progress' => 78, 'completed_date' => 'Estimated Oct 2026'],
    ['path' => 'Frontend UI/UX Developer Path', 'status' => 'Completed', 'switched_on' => '2026-01-10', 'progress' => 100, 'completed_date' => '2026-04-28']
];

// Achievement History Data
$achievementHistory = [
    ['title' => '14-Day Study Streak', 'category' => 'Consistency', 'xp' => 500, 'unlocked' => '2026-07-23', 'icon' => 'bi-fire text-warning'],
    ['title' => 'Docker Container Master', 'category' => 'Lab Mastery', 'xp' => 800, 'unlocked' => '2026-07-23', 'icon' => 'bi-box-seam text-info'],
    ['title' => 'Quiz Whiz (100% Score)', 'category' => 'Excellence', 'xp' => 300, 'unlocked' => '2026-07-19', 'icon' => 'bi-trophy-fill text-warning']
];
?>

<!-- SUB-COMPONENT CONTAINER WITH FADE-IN ANIMATION -->
<div class="skills-achievements-wrapper fade-in-content">
    <div class="row g-4">

        <!-- SKILL PROGRESS HISTORY -->
        <div class="col-12 col-md-6">
            <div class="dashboard-card p-4 h-100">
                <h5 class="fw-bold text-white mb-3 d-flex align-items-center">
                    <i class="bi bi-cpu me-2 text-cyan"></i>Skill Progress History
                </h5>
                <div class="list-group list-group-flush bg-transparent">
                    <?php
$ROOT_PATH = '../';
foreach($skillHistory as $sk): ?>
                        <div class="list-group-item bg-transparent text-white border-secondary border-opacity-25 px-0 py-3 searchable-item">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 fw-semibold text-white"><?= e($sk['skill']) ?></h6>
                                <span class="badge bg-cyan bg-opacity-20 text-cyan border border-cyan border-opacity-25"><?= e($sk['level']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center small text-white-50">
                                <span>Unlocked: <?= e($sk['unlocked_date']) ?></span>
                                <span class="text-warning fw-bold">+<?= e($sk['xp_earned']) ?> XP</span>
                            </div>
                        </div>
                    <?php
$ROOT_PATH = '../';
endforeach; ?>
                </div>
            </div>
        </div>

        <!-- LEARNING PATH HISTORY -->
        <div class="col-12 col-md-6">
            <div class="dashboard-card p-4 h-100">
                <h5 class="fw-bold text-white mb-3 d-flex align-items-center">
                    <i class="bi bi-diagram-3 me-2 text-primary"></i>Learning Path History
                </h5>
                <?php
$ROOT_PATH = '../';
foreach($pathHistory as $ph): ?>
                    <div class="p-3 mb-3 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-25 searchable-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-semibold text-white mb-0"><?= e($ph['path']) ?></h6>
                            <span class="badge <?= $ph['status'] === 'Active' ? 'bg-primary' : 'bg-success' ?>"><?= e($ph['status']) ?></span>
                        </div>
                        <div class="progress mb-2" style="height: 6px; background: rgba(0,0,0,0.1);">
                            <div class="progress-bar bg-cyan" style="width: <?= (int)$ph['progress'] ?>%;"></div>
                        </div>
                        <div class="d-flex justify-content-between fs-7 text-white-50">
                            <span>เริ่มเมื่อ: <?= e($ph['switched_on']) ?></span>
                            <span><?= e($ph['completed_date']) ?></span>
                        </div>
                    </div>
                <?php
$ROOT_PATH = '../';
endforeach; ?>
            </div>
        </div>

        <!-- ACHIEVEMENT HISTORY & BADGES -->
        <div class="col-12">
            <div class="dashboard-card p-4">
                <h5 class="fw-bold text-white mb-3 d-flex align-items-center">
                    <i class="bi bi-trophy me-2 text-warning"></i>Achievement History & Badges
                </h5>
                <div class="row g-3">
                    <?php
$ROOT_PATH = '../';
foreach($achievementHistory as $ac): ?>
                        <div class="col-12 col-sm-6 col-md-4 searchable-item">
                            <div class="p-3 rounded-3 bg-dark bg-opacity-40 border border-secondary border-opacity-25 d-flex align-items-center gap-3 h-100">
                                <div class="p-3 rounded-circle bg-dark border border-warning border-opacity-25 fs-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 54px; height: 54px;">
                                    <i class="bi <?= e($ac['icon']) ?>"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <h6 class="fw-semibold text-white mb-0 text-truncate"><?= e($ac['title']) ?></h6>
                                    <small class="text-white-50 d-block text-truncate"><?= e($ac['category']) ?></small>
                                    <div class="text-warning small fw-bold mt-1">+<?= e($ac['xp']) ?> XP</div>
                                </div>
                            </div>
                        </div>
                    <?php
$ROOT_PATH = '../';
endforeach; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
/* Smooth Animation for AJAX Component Ingestion */
.fade-in-content {
    animation: fadeIn 0.35s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
