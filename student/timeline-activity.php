<?php
$ROOT_PATH = '../';
/**
 * LevelUp - E-Learning Platform
 * Sub-Component: timeline-activity.php (Timeline & Activity Heatmap)
 * Loaded via jQuery + AJAX into learning-history.php
 * Production-Ready, Gamified Futuristic UI, Responsive
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
// 1. DATA PREPARATION (PHP Mock Data)
// =========================================================================

// Learning Timeline Data
$learningTimeline = [
    '2026-07-23' => [
        ['time' => '11:10', 'type' => 'ai', 'icon' => 'bi-robot text-cyan', 'title' => 'AI Tutor Conversation', 'desc' => 'Asked AI to explain CORS policy in PHP'],
        ['time' => '11:05', 'type' => 'badge', 'icon' => 'bi-award-fill text-warning', 'title' => 'Unlocked Badge', 'desc' => 'Earned "Docker Container Master" Badge'],
        ['time' => '10:20', 'type' => 'lab', 'icon' => 'bi-code-slash text-success', 'title' => 'Passed Coding Lab', 'desc' => 'Completed Docker Networking Challenge (Score: 100/100)'],
        ['time' => '09:45', 'type' => 'quiz', 'icon' => 'bi-check-circle-fill text-info', 'title' => 'Passed Quiz', 'desc' => 'Scored 90% on SQL Advanced Queries'],
        ['time' => '09:10', 'type' => 'lesson', 'icon' => 'bi-play-circle-fill text-primary', 'title' => 'Completed Lesson 5', 'desc' => 'Watched "PHP OOP Design Patterns"']
    ],
    '2026-07-22' => [
        ['time' => '16:30', 'type' => 'assignment', 'icon' => 'bi-file-earmark-check-fill text-purple', 'title' => 'Submitted Assignment', 'desc' => 'Project: E-Commerce REST API v1'],
        ['time' => '14:00', 'type' => 'lesson', 'icon' => 'bi-play-circle-fill text-primary', 'title' => 'Learned Lesson 4', 'desc' => 'Watched "Intro to Docker Compose"']
    ]
];
?>

<!-- TIMELINE & CALENDAR HEATMAP CONTENT -->
<div class="row g-4 animate__animated animate__fadeIn">
    <!-- LEARNING TIMELINE (ฝั่งซ้าย) -->
    <div class="col-12 col-lg-7">
        <div class="dashboard-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-white mb-0">
                    <i class="bi bi-clock me-2 text-cyan"></i>Learning Timeline
                </h5>
                <span class="text-white-50 small">
                    <i class="bi bi-arrow-repeat me-1 text-cyan"></i>เรียลไทม์
                </span>
            </div>
            
            <?php
$ROOT_PATH = '../';
foreach($learningTimeline as $date => $events): ?>
                <div class="text-cyan small fw-bold mb-3 d-flex align-items-center gap-1">
                    <i class="bi bi-calendar-event"></i> <?= $date === date('Y-m-d') ? 'วันนี้ (Today)' : e($date) ?>
                </div>
                <div class="timeline-container mb-4">
                    <?php
$ROOT_PATH = '../';
foreach($events as $event): ?>
                        <div class="timeline-item searchable-item" data-type="<?= e($event['type']) ?>">
                            <div class="timeline-marker">
                                <i class="bi <?= e($event['icon']) ?>"></i>
                            </div>
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="fw-semibold text-white small d-block"><?= e($event['title']) ?></span>
                                    <p class="text-white-50 small mb-0"><?= e($event['desc']) ?></p>
                                </div>
                                <span class="badge bg-dark text-white-50 border border-secondary border-opacity-25 ms-2 fs-7">
                                    <?= e($event['time']) ?>
                                </span>
                            </div>
                        </div>
                    <?php
$ROOT_PATH = '../';
endforeach; ?>
                </div>
            <?php
$ROOT_PATH = '../';
endforeach; ?>
        </div>
    </div>

    <!-- CALENDAR HISTORY / HEATMAP (ฝั่งขวา) -->
    <div class="col-12 col-lg-5">
        <div class="dashboard-card p-4 mb-4">
            <h5 class="fw-bold text-white mb-2">
                <i class="bi bi-calendar3 me-2 text-warning"></i>Activity Heatmap
            </h5>
            <p class="text-white-50 small mb-3">ความถี่ในการเข้าเรียน การทำ Lab และส่งงานประจำเดือนนี้</p>
            
            <div class="calendar-heatmap-grid mb-3">
                <?php
$ROOT_PATH = '../';
for($i = 1; $i <= 28; $i++): 
                    $lvlClass = ($i % 5 === 0) ? 'lvl-4' : (($i % 3 === 0) ? 'lvl-3' : (($i % 2 === 0) ? 'lvl-1' : ''));
                ?>
                    <div class="heatmap-day <?= $lvlClass ?>" title="July <?= $i ?>, 2026: Activity Level <?= rand(1, 5) ?>">
                        <?= $i ?>
                    </div>
                <?php
$ROOT_PATH = '../';
endfor; ?>
            </div>

            <div class="d-flex align-items-center justify-content-between text-white-50 fs-7">
                <span>Less active</span>
                <div class="d-flex gap-1 align-items-center">
                    <span class="heatmap-day p-1" style="width:12px; height:12px;"></span>
                    <span class="heatmap-day lvl-1 p-1" style="width:12px; height:12px;"></span>
                    <span class="heatmap-day lvl-2 p-1" style="width:12px; height:12px;"></span>
                    <span class="heatmap-day lvl-4 p-1" style="width:12px; height:12px;"></span>
                </div>
                <span>More active</span>
            </div>
        </div>
    </div>
</div>
