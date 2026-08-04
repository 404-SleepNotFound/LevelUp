<?php
$ROOT_PATH = '../';
/**
 * Component: Courses & Lessons History
 * File: courses-lessons.php
 * Description: ส่วนแสดงประวัติการเรียนคอร์สและบทเรียน (Loaded via AJAX into learning-history.php)
 */

// Safety check for helper function
if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

// =========================================================================
// DATA PREPARATION (Mock Data for Courses & Lessons)
// =========================================================================

// Course History Data
$courseHistory = [
    [
        'image' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=300&q=80',
        'title' => 'Full-Stack PHP & Modern Web Architecture',
        'instructor' => 'Dr. Jane Doe',
        'start_date' => '2026-05-10',
        'last_accessed' => '2026-07-23',
        'completion_date' => 'In Progress',
        'progress' => 82,
        'status' => 'Active'
    ],
    [
        'image' => 'https://images.unsplash.com/photo-1618401471353-b98afee0b2eb?auto=format&fit=crop&w=300&q=80',
        'title' => 'DevOps & Cloud Native Specialist',
        'instructor' => 'Alex Rivera',
        'start_date' => '2026-04-01',
        'last_accessed' => '2026-06-15',
        'completion_date' => '2026-06-15',
        'progress' => 100,
        'status' => 'Completed'
    ]
];

// Lesson History Data
$lessonHistory = [
    [
        'title' => 'PHP OOP Design Patterns', 
        'course' => 'Full-Stack PHP', 
        'study_date' => '2026-07-23 09:10', 
        'time_spent' => '25 min', 
        'repeat_count' => 1, 
        'completed' => true
    ],
    [
        'title' => 'Async/Await in JavaScript', 
        'course' => 'Modern Frontend Mastery', 
        'study_date' => '2026-07-21 15:20', 
        'time_spent' => '45 min', 
        'repeat_count' => 3, 
        'completed' => true
    ],
    [
        'title' => 'Docker Compose Fundamentals', 
        'course' => 'DevOps Specialist', 
        'study_date' => '2026-07-20 11:00', 
        'time_spent' => '30 min', 
        'repeat_count' => 1, 
        'completed' => true
    ]
];
?>

<!-- FIX BUG: Custom Table Styling for Dark Theme -->
<style>
    .table-custom {
        --bs-table-bg: transparent !important;
        --bs-table-color: rgba(255, 255, 255, 0.85) !important;
        --bs-table-border-color: rgba(255, 255, 255, 0.1) !important;
        --bs-table-hover-bg: rgba(255, 255, 255, 0.03) !important;
        --bs-table-hover-color: #ffffff !important;
        color: #fff !important;
    }
    .table-custom th, 
    .table-custom td {
        background-color: transparent !important;
        color: rgba(255, 255, 255, 0.85) !important;
        border-bottom-color: rgba(255, 255, 255, 0.08) !important;
    }
    .table-custom th {
        color: rgba(255, 255, 255, 0.6) !important;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
</style>

<div class="courses-lessons-component animate__animated animate__fadeIn">
    
    <!-- 1. COURSE HISTORY TABLE -->
    <div class="dashboard-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-white mb-0">
                <i class="bi bi-journal-bookmark me-2 text-primary"></i>Course History (ประวัติการลงเรียน)
            </h5>
            <span class="badge bg-primary bg-opacity-20 text-primary border border-primary border-opacity-25 px-2.5 py-1">
                <?= count($courseHistory) ?> Courses Total
            </span>
        </div>
        
        <div class="table-responsive">
            <table class="table table-dark table-hover table-custom align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">คอร์สเรียน</th>
                        <th scope="col">ผู้สอน</th>
                        <th scope="col">เริ่มเรียน</th>
                        <th scope="col">เรียนล่าสุด</th>
                        <th scope="col">PROGRESS</th>
                        <th scope="col">สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
$ROOT_PATH = '../';
if (!empty($courseHistory)): ?>
                        <?php
$ROOT_PATH = '../';
foreach($courseHistory as $c): ?>
                            <tr class="searchable-item" data-type="course" data-status="<?= strtolower(e($c['status'])) ?>">
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= e($c['image']) ?>" width="48" height="48" class="rounded-3 object-fit-cover shadow-sm" alt="Course Image">
                                        <div>
                                            <span class="fw-semibold text-white d-block"><?= e($c['title']) ?></span>
                                            <small class="text-white-50">เรียนจบเมื่อ: <?= e($c['completion_date']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-white-50"><?= e($c['instructor']) ?></td>
                                <td class="text-white-50"><?= e($c['start_date']) ?></td>
                                <td class="text-white-50"><?= e($c['last_accessed']) ?></td>
                                <td style="width: 160px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height:6px; background: rgba(255,255,255,0.1);">
                                            <div class="progress-bar bg-cyan" style="width: <?= (int)$c['progress'] ?>%;"></div>
                                        </div>
                                        <span class="small text-white-50"><?= e($c['progress']) ?>%</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge <?= $c['status'] === 'Completed' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                        <?= e($c['status']) ?>
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
                            <td colspan="6" class="text-center text-white-50 py-4">ไม่พบข้อมูลประวัติการเรียนคอร์ส</td>
                        </tr>
                    <?php
$ROOT_PATH = '../';
endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2. LESSON HISTORY TABLE -->
    <div class="dashboard-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-white mb-0">
                <i class="bi bi-play-btn me-2 text-cyan"></i>Lesson History (ประวัติการเข้าเรียนบทเรียน)
            </h5>
            <span class="badge bg-cyan bg-opacity-20 text-cyan border border-cyan border-opacity-25 px-2.5 py-1">
                <?= count($lessonHistory) ?> Lessons Logged
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-dark table-hover table-custom align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">บทเรียน</th>
                        <th scope="col">คอร์ส</th>
                        <th scope="col">เวลาเรียน</th>
                        <th scope="col">เวลาที่ใช้</th>
                        <th scope="col">เรียนซ้ำ</th>
                        <th scope="col">สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
$ROOT_PATH = '../';
if (!empty($lessonHistory)): ?>
                        <?php
$ROOT_PATH = '../';
foreach($lessonHistory as $lh): ?>
                            <tr class="searchable-item" data-type="lesson">
                                <td class="fw-semibold text-white">
                                    <i class="bi bi-file-earmark-play text-cyan me-2"></i><?= e($lh['title']) ?>
                                </td>
                                <td class="text-white-50"><?= e($lh['course']) ?></td>
                                <td class="text-white-50"><?= e($lh['study_date']) ?></td>
                                <td class="text-white-50"><?= e($lh['time_spent']) ?></td>
                                <td>
                                    <span class="badge bg-dark border border-secondary border-opacity-25 text-white-50">
                                        <?= e($lh['repeat_count']) ?> ครั้ง
                                    </span>
                                </td>
                                <td>
                                    <?php
$ROOT_PATH = '../';
if ($lh['completed']): ?>
                                        <i class="bi bi-check-circle-fill text-success fs-5" title="เรียนจบแล้ว"></i>
                                    <?php
$ROOT_PATH = '../';
else: ?>
                                        <i class="bi bi-clock-history text-warning fs-5" title="กำลังเรียน"></i>
                                    <?php
$ROOT_PATH = '../';
endif; ?>
                                </td>
                            </tr>
                        <?php
$ROOT_PATH = '../';
endforeach; ?>
                    <?php
$ROOT_PATH = '../';
else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-white-50 py-4">ไม่พบข้อมูลประวัติการเรียนบทเรียน</td>
                        </tr>
                    <?php
$ROOT_PATH = '../';
endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
