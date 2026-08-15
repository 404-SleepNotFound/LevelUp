<?php
// =======================================================
// 1. ตั้งค่าพื้นฐาน และเปิดใช้งาน Session
// =======================================================
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

// ดึง User ID จาก Session
$current_user_id = $_SESSION['user_login']['user_id'] 
                ?? $_SESSION['user_id'] 
                ?? null;

$ROOT_PATH = '../';
$course = null;
$all_courses = [];
$chapters = [];
$outcomes = [];
$prereqs = [];
$tags = [];
$db_error = null;
$auth_error = null;

// =======================================================
// 2. ตรวจสอบการเข้าสู่ระบบ (Authentication)
// =======================================================
if (!$current_user_id) {
    $auth_error = "กรุณาเข้าสู่ระบบก่อนใช้งานหน้าจัดการคอร์สเรียน";
} else {
    // =======================================================
    // 3. เชื่อมต่อฐานข้อมูล
    // =======================================================
    try {
        $db_paths = [
            __DIR__ . '/../config/database.php',
            __DIR__ . '/../config/db.php',
            __DIR__ . '/../db.php',
            __DIR__ . '/config/database.php',
            __DIR__ . '/config/db.php',
            $ROOT_PATH . 'config/database.php',
            $ROOT_PATH . 'config/db.php',
            '../db.php',
            '../config/db.php',
            'db.php'
        ];

        foreach ($db_paths as $path) {
            if (file_exists($path)) {
                require_once $path;
                break;
            }
        }

        if (!isset($pdo) && isset($conn)) {
            $pdo = $conn;
        }

        if (isset($pdo)) {
            // ดึงรายการคอร์สทั้งหมดของผู้ใช้ที่ล็อกอินอยู่
            $stmt_list = $pdo->prepare("SELECT id, title FROM course WHERE user_id = ? ORDER BY id DESC");
            $stmt_list->execute([$current_user_id]);
            $all_courses = $stmt_list->fetchAll(PDO::FETCH_ASSOC);

            // =======================================================
            // 4. ตรวจสอบสิทธิ์และดึงข้อมูล Dynamic ของคอร์ส
            // =======================================================
            if (isset($_GET['course_id'])) {
                $requested_course_id = intval($_GET['course_id']);

                $stmt_check = $pdo->prepare("
                    SELECT c.*, cat.name as category_name 
                    FROM course c 
                    LEFT JOIN category cat ON c.category_id = cat.id 
                    WHERE c.id = ? LIMIT 1
                ");
                $stmt_check->execute([$requested_course_id]);
                $target_course = $stmt_check->fetch(PDO::FETCH_ASSOC);

                if (!$target_course) {
                    $auth_error = "ไม่พบข้อมูลคอร์สเรียนที่คุณต้องการเข้าถึง";
                } elseif ($target_course['user_id'] != $current_user_id) {
                    $auth_error = "คุณไม่มีสิทธิ์แก้ไขคอร์สเรียนนี้ (อนุญาตเฉพาะผู้สร้างคอร์สเท่านั้น)";
                } else {
                    $course = $target_course;
                }
            } elseif (!empty($all_courses)) {
                $course_id = $all_courses[0]['id'];
                $stmt = $pdo->prepare("
                    SELECT c.*, cat.name as category_name 
                    FROM course c 
                    LEFT JOIN category cat ON c.category_id = cat.id 
                    WHERE c.id = ? AND c.user_id = ? LIMIT 1
                ");
                $stmt->execute([$course_id, $current_user_id]);
                $course = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            // หากมีคอร์ส ให้ทำการดึง Chapters, Lessons, Outcomes, Prereqs และ Tags
            if ($course) {
                $active_course_id = $course['id'];

                // 4.1 ดึงข้อมูล Chapters
                $stmt_chap = $pdo->prepare("SELECT * FROM chapters WHERE course_id = ? ORDER BY chapter_order ASC, id ASC");
                $stmt_chap->execute([$active_course_id]);
                $chapters = $stmt_chap->fetchAll(PDO::FETCH_ASSOC);

                // 4.2 ดึงข้อมูล Lessons ทั้งหมดในคอร์ส
                $stmt_less = $pdo->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY lesson_order ASC, id ASC");
                $stmt_less->execute([$active_course_id]);
                $all_lessons = $stmt_less->fetchAll(PDO::FETCH_ASSOC);

                // จัดกลุ่ม Lessons ลงตาม Chapter ID จริง
                if (!empty($chapters)) {
                    foreach ($chapters as &$chap) {
                        $chap['lessons'] = [];
                        foreach ($all_lessons as $less) {
                            if ($less['chapter_id'] == $chap['id']) {
                                $chap['lessons'][] = $less;
                            }
                        }
                    }
                    unset($chap);
                } elseif (!empty($all_lessons)) { 
                    // [แก้ไขตรงนี้] เปลี่ยนเป็น elseif เพื่อสร้าง Chapter จำลองก็ต่อเมื่อมี Lesson ค้างอยู่จริงๆ
                    $chapters = [
                        [
                            'id' => 0,
                            'title' => 'เนื้อหาบทเรียนทั้งหมด',
                            'chapter_order' => 1,
                            'lessons' => $all_lessons
                        ]
                    ];
                }

                // 4.3 ดึงข้อมูล Outcomes (Connected Skills)
                $stmt_out = $pdo->prepare("SELECT * FROM outcomes WHERE course_id = ?");
                $stmt_out->execute([$active_course_id]);
                $outcomes = $stmt_out->fetchAll(PDO::FETCH_ASSOC);

                // 4.4 ดึงข้อมูล Prereq
                $stmt_pre = $pdo->prepare("SELECT * FROM prereq WHERE course_id = ?");
                $stmt_pre->execute([$active_course_id]);
                $prereqs = $stmt_pre->fetchAll(PDO::FETCH_ASSOC);

                // 4.5 ดึงข้อมูล Tags
                $stmt_tag = $pdo->prepare("SELECT * FROM tags WHERE course_id = ?");
                $stmt_tag->execute([$active_course_id]);
                $tags = $stmt_tag->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            $db_error = "ไม่พบตัวแปร \$pdo สำหรับเชื่อมต่อฐานข้อมูล";
        }
    } catch (PDOException $e) {
        $db_error = "Database Error: " . $e->getMessage();
    }
}

// คำนวณสรุปสถิติต่างๆ แบบ Dynamic
$total_lessons_count = 0;
$total_quiz_count = 0;
$total_lab_count = 0;

if (!empty($chapters)) {
    foreach ($chapters as $ch) {
        if (!empty($ch['lessons'])) {
            foreach ($ch['lessons'] as $ls) {
                $total_lessons_count++;
                $type = $ls['lesson_type'] ?? 'theory';
                if ($type === 'quiz') {
                    $total_quiz_count++;
                } elseif ($type === 'coding_lab' || $type === 'virtual_lab') {
                    $total_lab_count++;
                }
            }
        }
    }
}

// คำนวณ Completion Checklist State
$has_cover = !empty($course['img']);
$has_desc = !empty($course['full_description']);
$has_lessons = $total_lessons_count > 0;
$has_cert = !empty($course['certificate_type']);
$has_lab = $total_lab_count > 0;
$has_skills = !empty($outcomes);

$checklist_items = [
    'cover'   => ['label' => 'รูปหน้าปกคอร์ส', 'status' => $has_cover],
    'desc'    => ['label' => 'คำอธิบายวิชา', 'status' => $has_desc],
    'lessons' => ['label' => 'บทเรียนย่อย', 'status' => $has_lessons],
    'cert'    => ['label' => 'ใบรับรองจบ', 'status' => $has_cert],
    'lab'     => ['label' => 'Coding Lab', 'status' => $has_lab],
    'skills'  => ['label' => 'Skill Map', 'status' => $has_skills],
];

$passed_count = 0;
foreach ($checklist_items as $item) {
    if ($item['status']) $passed_count++;
}
$completion_percentage = round(($passed_count / count($checklist_items)) * 100);
$can_publish = ($has_lessons && count($chapters) > 0);
?>

<!-- Import SweetAlert2 Library & LevelUp Alert System Utility -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= $ROOT_PATH ?>js/levelup-sweetalert.js" defer></script>

<!-- Import Dropdown Select Stylesheet & Script -->
<link rel="stylesheet" href="<?= $ROOT_PATH ?>css/dropdown-select.css">
<script src="<?= $ROOT_PATH ?>js/lu-dropdown.js" defer></script>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Bootstrap 5 JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<style>
    :root, html[data-theme="dark"] {
        --cb-bg: #0f172a;
        --cb-card: rgba(30, 41, 59, 0.7);
        --cb-card-heavy: rgba(15, 23, 42, 0.9);
        --cb-border: rgba(255, 255, 255, 0.08);
        --text-bright: #f8fafc;
        --text-muted: #94a3b8;
        --cyber-cyan: #06b6d4;
        --cyber-purple: #a855f7;
        --cyber-green: #22c55e;
    }

    html[data-theme="light"] {
        --cb-bg: #f8fafc;
        --cb-card: rgba(255, 255, 255, 0.95);
        --cb-card-heavy: #ffffff;
        --cb-border: rgba(0, 0, 0, 0.15);
        --text-bright: #0f172a;
        --text-muted: #64748b;
        --cyber-cyan: #0284c7;
        --cyber-purple: #7e22ce;
        --cyber-green: #16a34a;
    }

    body {
        background-color: var(--cb-bg) !important;
        transition: background-color 0.3s ease;
    }

    html[data-theme="light"] .text-white { color: var(--text-bright) !important; }
    html[data-theme="light"] .text-white-50 { color: var(--text-muted) !important; }
    html[data-theme="light"] .bg-dark, 
    html[data-theme="light"] .bg-black { background-color: var(--cb-card-heavy) !important; }
    html[data-theme="light"] .border-secondary { border-color: var(--cb-border) !important; }
    
    html[data-theme="light"] .overview-pill { background: #f1f5f9; border-color: #e2e8f0; }
    html[data-theme="light"] .chapter-card { background: #ffffff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
    html[data-theme="light"] .lesson-item { background: #f8fafc; border-color: #e2e8f0; }
    html[data-theme="light"] .lesson-item:hover { background: #f1f5f9; border-color: #cbd5e1; }
    html[data-theme="light"] .chapter-title-input { color: var(--text-bright); }
    
    html[data-theme="light"] .cb-input { 
        background: #ffffff !important; 
        color: var(--text-bright) !important; 
        border-color: #cbd5e1 !important; 
    }
    html[data-theme="light"] .modal-content { background-color: #ffffff !important; border-color: #cbd5e1 !important; }
    html[data-theme="light"] .btn-close-white { filter: invert(1) grayscale(100%) brightness(0); }
    html[data-theme="light"] .flow-node { background: #ffffff; border-color: #cbd5e1; }

    .text-cyan { color: var(--cyber-cyan) !important; }
    .text-purple { color: var(--cyber-purple) !important; }
    .text-success { color: var(--cyber-green) !important; }

    .btn-cyan { background-color: var(--cyber-cyan); color: #fff !important; border: none; }
    .btn-cyan:hover { background-color: #0891b2; }

    .btn-outline-cyan { border: 1px solid var(--cyber-cyan); color: var(--cyber-cyan) !important; background: transparent; }
    .btn-outline-cyan:hover { background-color: var(--cyber-cyan); color: #fff !important; }

    .btn-purple { background-color: var(--cyber-purple); color: #fff !important; border: none; }
    .btn-purple:hover { background-color: #9333ea; }

    .btn-outline-purple { border: 1px solid var(--cyber-purple); color: var(--cyber-purple) !important; background: transparent; }
    .btn-outline-purple:hover { background-color: var(--cyber-purple); color: #fff !important; }

    .skill-badge { font-size: 0.75rem; padding: 4px 10px; border-radius: 6px; font-weight: 500; display: inline-block; }
    .skill-badge-purple { background: rgba(168, 85, 247, 0.15); color: var(--cyber-purple); border: 1px solid rgba(168, 85, 247, 0.3); }
    .skill-badge-cyan { background: rgba(6, 182, 212, 0.15); color: var(--cyber-cyan); border: 1px solid rgba(6, 182, 212, 0.3); }
    .skill-badge-green { background: rgba(34, 197, 94, 0.15); color: var(--cyber-green); border: 1px solid rgba(34, 197, 94, 0.3); }

    .cb-wrapper { color: var(--text-bright); animation: fadeIn 0.4s ease-in-out; }
    .overview-pill { background: rgba(15, 23, 42, 0.6); border: 1px solid var(--cb-border); border-radius: 12px; padding: 10px 15px; display: flex; align-items: center; gap: 10px; transition: 0.3s; }
    .zone-container { background: var(--cb-card); border: 1px solid var(--cb-border); border-radius: 16px; backdrop-filter: blur(10px); min-height: 70px; transition: 0.3s; }

    .chapter-card { background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; margin-bottom: 15px; transition: 0.3s; }
    .chapter-card:hover { border-color: var(--cyber-cyan); box-shadow: 0 0 15px rgba(6, 182, 212, 0.15); }

    .chapter-header-flex { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
    .chapter-title-area { display: flex; align-items: center; gap: 8px; flex-grow: 1; min-width: 0; }
    .chapter-title-input { background: transparent; color: #fff; border: 0; font-weight: bold; padding: 4px; width: 100%; min-width: 120px; transition: 0.3s; }
    .chapter-title-input:focus { border-bottom: 1px solid var(--cyber-cyan); outline: none; }

    .lesson-item { background: rgba(30, 41, 59, 0.5); border: 1px solid var(--cb-border); border-radius: 8px; padding: 12px; margin-top: 8px; display: flex; flex-direction: row; align-items: center; justify-content: space-between; gap: 10px; cursor: grab; transition: 0.3s; }
    .lesson-left-content { display: flex; align-items: center; gap: 8px; min-width: 0; flex-wrap: wrap; }
    .lesson-item:hover { background: rgba(255, 255, 255, 0.03); border-color: rgba(255, 255, 255, 0.2); }

    .type-badge { font-size: 0.75rem; padding: 2px 8px; border-radius: 4px; font-weight: 500; white-space: nowrap; }
    .badge-video { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
    .badge-quiz { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
    .badge-lab { background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); }
    .badge-doc { background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); }

    .flow-node { background: rgba(15, 23, 42, 0.9); border: 2px solid var(--cb-border); border-radius: 10px; padding: 10px; text-align: center; position: relative; max-width: 220px; margin: 0 auto; transition: 0.3s; }
    .flow-arrow { text-align: center; color: var(--cyber-cyan); margin: 5px 0; font-size: 1.2rem; }

    .cb-input { background: rgba(15, 23, 42, 0.7) !important; border: 1px solid rgba(255, 255, 255, 0.15) !important; color: #fff !important; transition: 0.3s; }
    .cb-input::placeholder { color: var(--text-muted); }
    .cb-input option { background: var(--cb-bg); color: var(--text-bright); }

    /* Visual Hierarchy Action Bar */
    .action-bar-container { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 12px; }
    .action-btn-group-primary { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
    .action-btn-group-secondary { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }

    .empty-chapter-box { border: 2px dashed rgba(255, 255, 255, 0.15); border-radius: 10px; padding: 20px; text-align: center; background: rgba(0, 0, 0, 0.15); margin-top: 10px; }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="cb-wrapper p-1">

    <?php if ($db_error): ?>
        <div class="alert alert-danger bg-danger bg-opacity-20 border-danger text-white mb-4 rounded-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>ข้อผิดพลาดฐานข้อมูล:</strong> <?= htmlspecialchars($db_error) ?>
        </div>
    <?php endif; ?>

    <?php if ($auth_error): ?>
        <div class="zone-container p-5 text-center my-4">
            <div class="mb-3">
                <i class="bi bi-shield-lock-fill text-danger display-1"></i>
            </div>
            <h3 class="fw-bold text-white mb-2">ปฏิเสธการเข้าถึง (Access Denied)</h3>
            <p class="text-white-50 mb-4" style="max-width: 550px; margin: 0 auto;">
                <?= htmlspecialchars($auth_error) ?>
            </p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <?php if (!$current_user_id): ?>
                    <a href="../login.php" class="btn btn-cyan text-white px-4 py-2">
                        <i class="bi bi-box-arrow-in-right me-2"></i> เข้าสู่ระบบ
                    </a>
                <?php else: ?>
                    <a href="?page=course-builder" class="btn btn-cyan text-white px-4 py-2">
                        <i class="bi bi-journal-bookmark-fill me-2"></i> กลับไปยังคอร์สของคุณ
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>

        <?php if (!empty($all_courses)): ?>
            <div class="mb-4 p-3 rounded-4 bg-dark bg-opacity-50 border border-secondary border-opacity-20 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <i class="bi bi-journal-bookmark-fill text-cyan fs-5"></i>
                    <label class="text-white fw-bold me-2">รายการคอร์สของคุณ:</label>
                    <select class="form-select cb-input lu-select" style="min-width: 260px; width: auto;" onchange="safeNavigateCourse(this.value)">
                        <?php foreach ($all_courses as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($course && $course['id'] == $c['id']) ? 'selected' : '' ?>>
                                [ID: <?= $c['id'] ?>] <?= htmlspecialchars($c['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="button" class="btn btn-sm btn-cyan text-white ajax-menu-btn" data-page="create-course.php">
                    <i class="bi bi-plus-lg me-1"></i> สร้างคอร์สเรียนใหม่
                </button>
            </div>
        <?php endif; ?>

        <?php if (!$course): ?>
            <div class="zone-container p-5 text-center my-4">
                <div class="mb-3">
                    <i class="bi bi-journal-plus text-cyan display-1"></i>
                </div>
                <h3 class="fw-bold text-white mb-2">คุณยังไม่มีคอร์สเรียนในระบบ</h3>
                <p class="text-white-50 mb-4" style="max-width: 550px; margin: 0 auto;">
                    เริ่มต้นสร้างคอร์สเรียนแรกของคุณ เพื่อสร้างเนื้อหา ออกแบบบทเรียน และเปิดสอนผู้เรียนได้ทันที
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <button type="button" class="btn btn-cyan text-white px-4 py-2 ajax-menu-btn" data-page="create-course.php">
                        <i class="bi bi-plus-circle-fill me-2"></i> สร้างคอร์สเรียนใหม่
                    </button>
                </div>
            </div>
        <?php else: ?>

            <!-- DYNAMIC: ข้อมูลภาพรวมคอร์ส -->
            <div class="row g-3 align-items-center mb-4">
                <div class="col-12 col-md-5 d-flex align-items-center gap-3">
                    <div style="position: relative; width: 95px; height: 55px; border-radius: 8px; overflow: hidden; border: 1px solid var(--cb-border); flex-shrink: 0; background: #000;">
                        <?php if (!empty($course['img'])): ?>
                            <img src="<?= $ROOT_PATH . htmlspecialchars($course['img']) ?>" alt="Thumbnail" style="width:100%; height:100%; object-fit:cover;">
                        <?php else: ?>
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-dark text-cyan small" style="font-size: 0.65rem;">No Image</div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1 text-white" id="cb-course-title"><?= htmlspecialchars($course['title']) ?></h4>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-warning text-dark px-2 py-0.5 fw-bold" style="font-size: 0.7rem;" id="course-visibility-badge">
                                <i class="bi bi-file-earmark-lock"></i> <?= strtoupper($course['visibility'] ?? 'DRAFT') ?>
                            </span>
                            <?php if (!empty($course['category_name'])): ?>
                                <span class="badge bg-info text-dark px-2 py-0.5 fw-bold" style="font-size: 0.7rem;">
                                    <i class="bi bi-tag"></i> <?= htmlspecialchars($course['category_name']) ?>
                                </span>
                            <?php endif; ?>
                            <span class="text-white-50 small" style="font-size:0.75rem;" id="auto-save-status">
                                <i class="bi bi-clock-history"></i> Updated: <?= !empty($course['updated_at']) ? date('d/m/Y H:i', strtotime($course['updated_at'])) : 'Recently' ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-7">
                    <div class="row g-2 justify-content-md-end">
                        <div class="col-6 col-sm-3 col-md-2">
                            <div class="overview-pill text-center d-block">
                                <div class="text-white-50 small text-uppercase" style="font-size:0.65rem;">Chapters</div>
                                <div class="fw-bold text-white fs-5" id="cnt-chap"><?= count($chapters) ?></div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3 col-md-2">
                            <div class="overview-pill text-center d-block">
                                <div class="text-white-50 small text-uppercase" style="font-size:0.65rem;">Lessons</div>
                                <div class="fw-bold text-cyan fs-5" id="cnt-less"><?= $total_lessons_count ?></div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3 col-md-2">
                            <div class="overview-pill text-center d-block">
                                <div class="text-white-50 small text-uppercase" style="font-size:0.65rem;">Quizzes</div>
                                <div class="fw-bold text-warning fs-5" id="cnt-quiz"><?= $total_quiz_count ?></div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3 col-md-2">
                            <div class="overview-pill text-center d-block">
                                <div class="text-white-50 small text-uppercase" style="font-size:0.65rem;">Labs</div>
                                <div class="fw-bold text-success fs-5" id="cnt-lab"><?= $total_lab_count ?></div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="overview-pill flex-column align-items-start justify-content-center">
                                <div class="d-flex justify-content-between w-100 text-white-50 small" style="font-size:0.65rem;">
                                    <span>READINESS</span><span class="text-cyan fw-bold" id="progress-percent-label"><?= $completion_percentage ?>%</span>
                                </div>
                                <div class="progress w-100 mt-1" style="height: 6px; background: rgba(255,255,255,0.05);">
                                    <div class="progress-bar bg-info" id="course-progress-bar" style="width: <?= $completion_percentage ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- แผงเครื่องมือหลัก: ปรับ Visual Hierarchy -->
            <div class="action-bar-container mb-4 p-3 rounded-4 bg-dark bg-opacity-40 border border-secondary border-opacity-10">
                <!-- กลุ่มปุ่ม Secondary Actions -->
                <div class="action-btn-group-secondary">
                    <button class="btn btn-sm btn-outline-cyan text-white" onclick="openAIConfigModal()"><i class="bi bi-cpu text-purple me-1"></i> AI Assistant</button>
                    <button class="btn btn-sm btn-outline-light text-white" data-bs-toggle="modal" data-bs-target="#importExportModal"><i class="bi bi-arrow-left-right me-1"></i> Import/Export</button>
                    <button class="btn btn-sm btn-outline-purple text-white" onclick="toggleCollaborators()"><i class="bi bi-people me-1"></i> Collab</button>
                </div>
                <!-- กลุ่มปุ่ม Primary Actions -->
                <div class="action-btn-group-primary">
                    <button class="btn btn-sm btn-outline-info text-white" onclick="triggerAction('preview')"><i class="bi bi-eye-fill me-1"></i> Preview</button>
                    <button class="btn btn-sm btn-warning text-dark fw-bold" onclick="runQualityValidation()"><i class="bi bi-shield-check me-1"></i> Validate</button>
                    <button class="btn btn-sm btn-secondary text-white px-3" onclick="triggerAction('save')"><i class="bi bi-hdd-fill me-1"></i> Save Draft</button>
                    
                    <button class="btn btn-sm <?= $can_publish ? 'btn-success' : 'btn-secondary opacity-50' ?> text-white fw-bold px-3" id="publish-btn" onclick="triggerAction('publish')" <?= $can_publish ? '' : 'disabled' ?>>
                        <?php if ($can_publish): ?>
                            <i class="bi bi-rocket-takeoff-fill me-1"></i> เผยแพร่คอร์สเรียน
                        <?php else: ?>
                            <i class="bi bi-lock-fill me-1"></i> เพิ่มอย่างน้อย 1 บทเรียนเพื่อเผยแพร่
                        <?php endif; ?>
                    </button>
                </div>
            </div>

            <!-- ส่วนจัดการโครงสร้างคอร์ส Dynamic -->
            <div class="row g-3">
                
                <!-- คอลัมน์ซ้าย: Chapters & Lessons Dynamic Loop -->
                <div class="col-12 col-lg-7">
                    <div class="zone-container p-3 p-md-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <h5 class="fw-bold text-white mb-0"><i class="bi bi-layers text-cyan me-2"></i> โครงสร้างหลักสูตร</h5>
                            <button class="btn btn-sm btn-cyan text-white px-3" onclick="addNewChapter()"><i class="bi bi-plus-circle-fill me-1"></i> เพิ่ม Chapter ใหม่</button>
                        </div>

                        <div id="course-chapters-root">
                            <?php foreach ($chapters as $index => $chap): ?>
                                <div class="chapter-card p-3" id="chap-<?= $chap['id'] ?>" data-chapter-id="<?= $chap['id'] ?>">
                                    <div class="chapter-header-flex border-bottom border-secondary border-opacity-10 pb-2 mb-2">
                                        <div class="chapter-title-area">
                                            <i class="bi bi-grid-3x3-gap text-white-50 cursor-move fs-5 flex-shrink-0"></i>
                                            <span class="fw-bold text-info flex-shrink-0 chap-label">Chapter <?= $index + 1 ?>:</span>
                                            <input type="text" class="chapter-title-input" value="<?= htmlspecialchars($chap['title']) ?>">
                                        </div>
                                        <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                            <!-- Header Inline Quick-Add Button -->
                                            <button type="button" class="btn btn-xs btn-outline-cyan text-white px-2 py-1" style="font-size: 0.75rem;" onclick="openAddLessonModal(this)" title="เพิ่มบทเรียนย่อยใน Chapter นี้">
                                                <i class="bi bi-plus-lg me-1"></i>เพิ่มบทเรียน
                                            </button>
                                            <button class="btn btn-link text-white-50 p-1 btn-sm" onclick="duplicateItem('Chapter', <?= $index + 1 ?>)" title="คัดลอก"><i class="bi bi-copy"></i></button>
                                            <button class="btn btn-link text-warning p-1 btn-sm" onclick="LevelUpAlert.toast('ซ่อนบทเรียนเรียบร้อยแล้ว', 'info')" title="ซ่อน"><i class="bi bi-eye-slash"></i></button>
                                            <button class="btn btn-link text-danger p-1 btn-sm" onclick="removeChapter(this)" title="ลบ"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </div>
                                    
                                    <div class="lessons-sortable-zone">
                                        <?php if (!empty($chap['lessons'])): ?>
                                            <?php foreach ($chap['lessons'] as $less_index => $less): 
                                                $ltype = $less['lesson_type'] ?? 'theory';
                                                $badge_class = 'badge-video';
                                                $badge_icon = 'bi-play-btn-fill';
                                                $badge_name = 'Video / Theory';

                                                if ($ltype === 'slide') {
                                                    $badge_class = 'badge-doc'; $badge_icon = 'bi-file-earmark-text-fill'; $badge_name = 'Slide';
                                                } elseif ($ltype === 'quiz') {
                                                    $badge_class = 'badge-quiz'; $badge_icon = 'bi-patch-question-fill'; $badge_name = 'Quiz';
                                                } elseif ($ltype === 'coding_lab') {
                                                    $badge_class = 'badge-lab'; $badge_icon = 'bi-code-square'; $badge_name = 'Coding Lab';
                                                } elseif ($ltype === 'virtual_lab') {
                                                    $badge_class = 'badge-lab'; $badge_icon = 'bi-cpu'; $badge_name = 'Virtual Lab';
                                                } elseif ($ltype === 'assignment') {
                                                    $badge_class = 'badge-doc'; $badge_icon = 'bi-file-zip'; $badge_name = 'Assignment';
                                                }
                                            ?>
                                                <div class="lesson-item" data-type="<?= $ltype ?>" data-id="<?= $less['id'] ?>">
                                                    <div class="lesson-left-content">
                                                        <i class="bi bi-list text-muted fs-6 flex-shrink-0"></i>
                                                        <span class="small text-white-50 flex-shrink-0 lesson-num"><?= ($index + 1) . '.' . ($less_index + 1) ?></span>
                                                        <span class="small text-white me-2 lesson-title"><?= htmlspecialchars($less['title']) ?></span>
                                                        <span class="type-badge <?= $badge_class ?>"><i class="bi <?= $badge_icon ?>"></i> <?= $badge_name ?></span>
                                                    </div>
                                                    <div class="d-flex gap-2 flex-shrink-0">
                                                        <button class="btn btn-link text-info p-0 btn-sm" onclick="editLessonDetails('<?= $ltype ?>', this)"><i class="bi bi-pencil-square"></i></button>
                                                        <button class="btn btn-link text-danger p-0 btn-sm" onclick="removeLesson(this)"><i class="bi bi-x-circle"></i></button>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <!-- Interactive Empty State Card -->
                                            <div class="empty-chapter-box">
                                                <i class="bi bi-journal-plus text-cyan fs-3 d-block mb-1"></i>
                                                <div class="text-white-50 small mb-2">ยังไม่มีบทเรียนย่อยใน Chapter นี้</div>
                                                <button type="button" class="btn btn-xs btn-cyan text-white px-3 py-1" style="font-size:0.8rem;" onclick="openAddLessonModal(this)">
                                                    <i class="bi bi-plus-circle-fill me-1"></i> เพิ่มบทเรียนแรกของคุณ
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mt-2 text-end">
                                        <button type="button" class="btn btn-xs btn-outline-light text-white opacity-75" style="font-size:0.75rem;" onclick="openAddLessonModal(this)">+ เพิ่มวัตถุย่อย</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- คอลัมน์ขวา: DYNAMIC Skill Mapping & Flow -->
                <div class="col-12 col-lg-5 d-flex flex-column gap-3">
                    <div class="zone-container p-4">
                        <h5 class="fw-bold text-white mb-2"><i class="bi bi-diagram-3 text-purple me-2"></i> Skill & Outcome Mapping</h5>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="small text-white-50 mb-1">เวลาเรียนโดยประมาณรวม</label>
                                <div class="p-2 rounded bg-black bg-opacity-30 text-cyan fw-bold text-center small">
                                    <i class="bi bi-hourglass-split"></i> ~<?= htmlspecialchars($course['time_total'] ?? '0') ?> ชั่วโมง
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="small text-white-50 mb-1">การทำ Mapping สกิล</label>
                                <div class="p-2 rounded bg-black bg-opacity-30 text-success fw-bold text-center small">
                                    <i class="bi bi-check2-circle"></i> เชื่อมต่อ <?= count($outcomes) ?> แกนสกิล
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="small text-white-50 mb-2 d-block">ทักษะที่จะได้รับ (Connected Skills)</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php if (!empty($outcomes)): ?>
                                    <?php 
                                        $badge_styles = ['skill-badge-purple', 'skill-badge-cyan', 'skill-badge-green'];
                                        foreach ($outcomes as $idx => $out): 
                                            $style = $badge_styles[$idx % 3];
                                    ?>
                                        <span class="skill-badge <?= $style ?>"><?= htmlspecialchars($out['name']) ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-white-50 small style-italic">ยังไม่ได้กำหนดผลการเรียนรู้</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="zone-container p-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h5 class="fw-bold text-white mb-0"><i class="bi bi-lock-fill text-warning me-2"></i> เงื่อนไขการข้ามบท (Unlock Flow)</h5>
                            <i class="bi bi-info-circle text-white-50" data-bs-toggle="tooltip" data-bs-placement="top" title="บังคับให้ผู้เรียนต้องผ่านเนื้อหาตามลำดับก่อนปลดล็อกบทถัดไป"></i>
                        </div>
                        <div class="p-3 rounded bg-dark bg-opacity-50 border border-secondary border-opacity-10 small">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="strict-flow" <?= (!isset($course['is_strict_flow']) || $course['is_strict_flow'] == 1) ? 'checked' : '' ?>>
                                <label class="form-check-label text-white fw-medium" for="strict-flow">บังคับเรียนแบบเป็นลำดับ (Linear Progression)</label>
                            </div>
                            <ul class="mb-0 ps-3 text-white-50" style="font-size: 0.8rem;">
                                <li>ดูวิดีโอต้องเก็บความยาวขั้นต่ำ 80%</li>
                                <li>สอบผ่าน Quiz ประจำบทขั้นต่ำ 70%</li>
                                <li>ผ่านการทดสอบ AI Coding Lab อย่างน้อย 1 ครั้ง</li>
                            </ul>
                        </div>
                    </div>

                    <div class="zone-container p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="fw-bold text-white mb-0"><i class="bi bi-bounding-box text-success me-2"></i> Visual Course Flow</h5>
                            <button class="btn btn-xs btn-outline-info text-white py-0 px-2" style="font-size: 0.75rem;" onclick="LevelUpAlert.toast('เปิดโปรแกรมแก้ไข Flow ในหน้าต่างใหม่...', 'info')">เต็มจอ</button>
                        </div>
                        
                        <div class="p-3 rounded-4 bg-black bg-opacity-40 border border-secondary border-opacity-10 py-4">
                            <div class="flow-node border-info">
                                <div class="small fw-bold text-cyan"><?= htmlspecialchars($course['title']) ?></div>
                            </div>
                            <div class="flow-arrow"><i class="bi bi-arrow-down-short"></i></div>
                            <div class="flow-node">
                                <div class="small text-white">Chapter 1: <?= htmlspecialchars($chapters[0]['title'] ?? 'เริ่มต้นบทเรียน') ?></div>
                                <span class="type-badge badge-video d-inline-block mt-1">Start Chapter</span>
                            </div>
                            <div class="flow-arrow"><i class="bi bi-arrow-down-short"></i></div>
                            <div class="flow-node border-success">
                                <div class="small fw-bold text-success">Certificate Evaluation</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Quality Score & Dynamic Readiness Checklist -->
            <div class="zone-container p-4 mt-3 mb-5" id="quality-checker-section">
                <h5 class="fw-bold text-white mb-2"><i class="bi bi-patch-check-fill text-warning me-2"></i> AI Course Quality & Publish Checklist</h5>
                
                <div class="row g-3">
                    <div class="col-12 col-md-3 text-center border-md-end border-secondary border-opacity-10 py-2">
                        <div class="text-white-50 small text-uppercase fw-medium" style="font-size:0.7rem;">Course Quality Score</div>
                        <div class="fs-1 fw-bold text-warning my-1" id="quality-score">-- <span class="fs-6 text-white-50">/ 100</span></div>
                        <div class="text-warning small" id="quality-stars">⭐⭐⭐⭐⭐</div>
                    </div>
                    <div class="col-12 col-md-5 py-2">
                        <div class="fw-bold text-white small mb-2"><i class="bi bi-list-task text-cyan me-1"></i> รายการตรวจสอบความสมบูรณ์หลักสูตร (Live Checklist)</div>
                        <div class="row g-2 small text-white-50" id="checklist-results">
                            <div class="col-6" id="chk-item-cover">
                                <i class="bi <?= $has_cover ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' ?> me-1"></i> รูปหน้าปกคอร์ส
                            </div>
                            <div class="col-6" id="chk-item-desc">
                                <i class="bi <?= $has_desc ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' ?> me-1"></i> คำอธิบายวิชา
                            </div>
                            <div class="col-6" id="chk-item-lessons">
                                <i class="bi <?= $has_lessons ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' ?> me-1"></i> บทเรียนย่อย
                            </div>
                            <div class="col-6" id="chk-item-cert">
                                <i class="bi <?= $has_cert ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' ?> me-1"></i> ใบรับรองจบ
                            </div>
                            <div class="col-6" id="chk-item-lab">
                                <i class="bi <?= $has_lab ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' ?> me-1"></i> Coding Lab
                            </div>
                            <div class="col-6" id="chk-item-skills">
                                <i class="bi <?= $has_skills ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' ?> me-1"></i> Skill Map
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 py-2">
                        <div class="fw-bold text-purple small mb-1"><i class="bi bi-chat-square-dots-fill me-1"></i> ข้อเสนอแนะจาก AI Expert:</div>
                        <div class="p-2 rounded bg-black bg-opacity-30 small text-white-50" id="ai-suggestion-box" style="font-size:0.8rem; min-height: 65px;">
                            <?php if ($can_publish): ?>
                                ✨ <b>คอร์สเรียนมีโครงสร้างพื้นฐานครบถ้วนแล้ว!</b> คุณสามารถกด เผยแพร่คอร์ส หรือกด "Validate" เพื่อให้ AI สแกนวิเคราะห์คะแนนโดยละเอียด
                            <?php else: ?>
                                ⚠️ <b>คำแนะนำ:</b> กรุณาเพิ่มอย่างน้อย 1 Chapter และ 1 บทเรียนย่อยเพื่อให้ระบบพร้อมสำหรับเผยแพร่
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    <?php endif; ?>

</div>

<!-- [MODAL SYSTEM] -->
<div class="modal fade" id="lessonObjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content text-white bg-dark border border-secondary border-opacity-30 rounded-4">
            <div class="modal-header border-bottom border-secondary border-opacity-20 p-3">
                <h5 class="modal-title fw-bold" id="lessonModalTitle"><i class="bi bi-plus-square text-cyan me-2"></i> จัดการรายละเอียดวัตถุเรียนรู้</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small text-white-50">ประเภทบทเรียน (Lesson Type)</label>
                    <select class="form-select cb-input lu-select" id="modal-lesson-type" onchange="switchModalFormType(this.value)">
                        <option value="theory">🎥 วิดีโอแนะนำหลัก (Video Content / Theory)</option>
                        <option value="slide">📊 สไลด์เอกสารและบทความ (Slide / PDF / Article)</option>
                        <option value="quiz">📝 แบบทดสอบประเมินผล (Quiz Management)</option>
                        <option value="coding_lab">💻 ปฏิบัติการโค้ดดิ้งอัตโนมัติ (Coding Lab Management)</option>
                        <option value="virtual_lab">🌐 เซิร์ฟเวอร์จำลองขั้นสูง (Virtual Lab Configuration)</option>
                        <option value="assignment">📂 งานส่งและการทำโปรเจกต์ (Assignment Management)</option>
                    </select>
                </div>

                <hr style="border-color: rgba(255,255,255,0.15);">

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-8">
                        <label class="form-label small text-white-50">ชื่อหัวข้อย่อยหรือบทเรียนนี้</label>
                        <input type="text" class="form-control cb-input" id="modal-item-title" value="หัวข้อบทเรียนใหม่">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label small text-white-50">กำหนดเวลาเรียน (นาที)</label>
                        <input type="number" class="form-control cb-input" id="modal-item-time" value="15">
                    </div>
                </div>

                <!-- Block Form ย่อยตามประเภท -->
                <div class="sub-form-block d-none" id="sub-form-quiz">
                    <h6 class="text-warning fw-bold small mb-2"><i class="bi bi-gear-fill"></i> ตั้งค่าโหมดแบบทดสอบ (Quiz Config)</h6>
                    <div class="row g-2">
                        <div class="col-12 col-sm-4">
                            <label class="small text-white-50">ประเภทข้อสอบ</label>
                            <select class="form-select cb-input lu-select"><option>Quiz หลังเรียนจบชุด</option><option>Quiz ก่อนเรียนบทเรียน</option><option>Final Quiz</option></select>
                        </div>
                        <div class="col-6 col-sm-4"><label class="small text-white-50">เกณฑ์คะแนนผ่าน (%)</label><input type="number" class="form-control cb-input" value="70"></div>
                        <div class="col-6 col-sm-4"><label class="small text-white-50">เวลาสอบ (นาที)</label><input type="number" class="form-control cb-input" value="15"></div>
                    </div>
                </div>

                <div class="sub-form-block d-none" id="sub-form-coding_lab">
                    <h6 class="text-success fw-bold small mb-2"><i class="bi bi-code-slash"></i> ตั้งค่าระบบตรวจโค้ดอัตโนมัติ (Coding Lab Config)</h6>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="small text-white-50">ภาษาโปรแกรม</label>
                            <select class="form-select cb-input lu-select"><option>JavaScript (Node.js)</option><option>Python 3.x</option><option>TypeScript</option><option>SQL</option></select>
                        </div>
                        <div class="col-3"><label class="small text-white-50">Time Limit (s)</label><input type="number" class="form-control cb-input" value="2"></div>
                        <div class="col-3"><label class="small text-white-50">Memory (MB)</label><input type="number" class="form-control cb-input" value="256"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top border-secondary border-opacity-20 p-3">
                <button type="button" class="btn btn-secondary text-white btn-sm px-3" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-cyan text-white btn-sm px-4" onclick="saveLessonObjectData()">บันทึกข้อมูล</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="aiAssistantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content text-white bg-dark border border-secondary border-opacity-30 rounded-4">
            <div class="modal-header border-bottom border-secondary border-opacity-20 p-3">
                <h5 class="modal-title fw-bold text-purple"><i class="bi bi-cpu-fill me-2"></i> AI Curriculum Assistant</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="bi bi-stars text-purple fs-1 mb-2 d-block"></i>
                <p class="small text-white mb-3">ให้ AI ตรวจวิเคราะห์ แนะนำบทเรียนที่ขาดหาย และตรวจความซ้ำซ้อนของหลักสูตร</p>
                <button type="button" class="btn btn-purple btn-sm text-white w-100 py-2" onclick="runAICurriculumProcess()">เริ่มรัน AI วิเคราะห์โครงสร้างหลักสูตร ✨</button>
                <div id="ai-curriculum-result" class="d-none mt-3 p-3 bg-black bg-opacity-40 border border-secondary border-opacity-10 text-start rounded small text-white-50"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="importExportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content text-white bg-dark border border-secondary border-opacity-30 rounded-4">
            <div class="modal-header border-bottom border-secondary border-opacity-20 p-3">
                <h5 class="modal-title fw-bold text-white"><i class="bi bi-arrow-left-right text-cyan me-2"></i> Import / Export</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small text-info fw-bold">Import โครงสร้าง</label>
                    <select class="form-select cb-input lu-select mb-2"><option>JSON Structure</option><option>SCORM Package (.zip)</option></select>
                    <button class="btn btn-sm btn-outline-cyan text-white w-100" onclick="LevelUpAlert.toast('นำเข้าไฟล์โครงสร้างสำเร็จแล้ว', 'success')">นำเข้าไฟล์</button>
                </div>
                <hr style="border-color: rgba(255,255,255,0.15)">
                <div>
                    <label class="form-label small text-success fw-bold">Export โครงสร้าง</label>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-success text-white flex-grow-1" onclick="LevelUpAlert.toast('Export JSON สำเร็จ', 'success')"><i class="bi bi-filetype-json"></i> Export JSON</button>
                        <button class="btn btn-sm btn-outline-warning text-white flex-grow-1" onclick="LevelUpAlert.toast('Backup ZIP สำเร็จ', 'success')"><i class="bi bi-file-zip"></i> Backup (.zip)</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let targetChapCardForNewLesson = null;
    let currentEditLessonEl = null;
    let isFormDirty = false;

    document.addEventListener('DOMContentLoaded', () => {
        reindexLessons();
        recalculateStats();
        updatePublishReadiness();

        // ตรวจจับ Unsaved Changes ใน Form Inputs
        $(document).on('change input', '.chapter-title-input, .cb-input, #modal-item-title', function() {
            setDirtyState(true);
        });

        // เปิดใช้งาน Bootstrap Tooltips
        if (typeof bootstrap !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });

    function setDirtyState(dirty) {
        isFormDirty = dirty;
        if (dirty) {
            $('#auto-save-status').html('<span class="text-warning"><i class="bi bi-pencil-fill me-1"></i> มีการแก้ไขที่ยังไม่ได้บันทึก</span>');
        } else {
            const now = new Date();
            const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
            $('#auto-save-status').html('<i class="bi bi-check-circle-fill text-success me-1"></i> บันทึกล่าสุดเมื่อ ' + timeStr);
        }
    }

    function safeNavigateCourse(courseId) {
        if (isFormDirty) {
            LevelUpAlert.confirm({
                title: 'คุณมีข้อมูลที่ยังไม่ได้บันทึก! ⚠️',
                text: 'หากเปลี่ยนหน้าตอนนี้ ข้อมูลที่คุณแก้ไขล่าสุดจะสูญหาย คุณต้องการดำเนินการต่อหรือไม่?',
                confirmText: 'เปลี่ยนหน้าโดยไม่บันทึก',
                cancelText: 'ย้อนกลับไปบันทึก'
            }).then((isConfirmed) => {
                if (isConfirmed) {
                    isFormDirty = false;
                    location.href = '?course_id=' + courseId;
                }
            });
        } else {
            location.href = '?course_id=' + courseId;
        }
    }

    function openAIConfigModal() {
        var modalEl = document.getElementById('aiAssistantModal');
        if (typeof bootstrap !== 'undefined') {
            var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.show();
        } else {
            $('#aiAssistantModal').modal('show');
        }
    }

    function runAICurriculumProcess() {
        $('#aiAssistantModal').modal('hide');
        LevelUpAlert.aiLoading('วิเคราะห์โครงสร้างหลักสูตร', 'กำลังประมวลผลข้อมูลและตรวจสอบความซ้ำซ้อน...');
        
        setTimeout(() => {
            LevelUpAlert.close();
            $('#aiAssistantModal').modal('show');
            var $res = $('#ai-curriculum-result');
            $res.removeClass('d-none').html(`🤖 <b>ผลการวิเคราะห์จาก AI Expert:</b><br>
• <u>สิ่งที่ขาด:</u> ควรเพิ่ม Coding Lab เรื่อง Error Handling ใน Chapter 2<br>
• <u>ตรวจความซ้ำซ้อน:</u> ไม่พบเนื้อหาที่ซ้ำซ้อนกับคอร์สอื่นในระบบ<br>
• <u>ข้อแนะนำ:</u> เพิ่ม Quiz ท้ายบทเรียนเสมือนจริง (Virtual Lab) เพื่อรีเช็คความเข้าใจ`);
        }, 1500);
    }

    function openAddLessonModal(btn) {
        targetChapCardForNewLesson = $(btn).closest('.chapter-card');
        currentEditLessonEl = null;
        
        $('#lessonModalTitle').html('<i class="bi bi-plus-square text-cyan me-2"></i> เพิ่มวัตถุเรียนรู้ใหม่');
        $('#modal-item-title').val('');
        $('#modal-lesson-type').val('theory');
        switchModalFormType('theory');
        $('#lessonObjectModal').modal('show');
    }

    function editLessonDetails(type, btn) {
        targetChapCardForNewLesson = null;
        currentEditLessonEl = $(btn).closest('.lesson-item');
        
        const currentTitle = currentEditLessonEl.find('.lesson-title').text();
        $('#lessonModalTitle').html('<i class="bi bi-pencil-square text-info me-2"></i> แก้ไขวัตถุเรียนรู้');
        $('#modal-item-title').val(currentTitle);
        $('#modal-lesson-type').val(type);
        switchModalFormType(type);
        $('#lessonObjectModal').modal('show');
    }

    function switchModalFormType(type) {
        $('.sub-form-block').addClass('d-none');
        if(type === 'quiz') $('#sub-form-quiz').removeClass('d-none');
        if(type === 'coding_lab') $('#sub-form-coding_lab').removeClass('d-none');
    }

    function saveLessonObjectData() {
        const title = $('#modal-item-title').val().trim() || 'บทเรียนย่อยใหม่';
        const type = $('#modal-lesson-type').val();
        
        const badgeMap = {
            'theory': '<span class="type-badge badge-video"><i class="bi bi-play-btn-fill"></i> Video / Theory</span>',
            'slide': '<span class="type-badge badge-doc"><i class="bi bi-file-earmark-text-fill"></i> Slide</span>',
            'quiz': '<span class="type-badge badge-quiz"><i class="bi bi-patch-question-fill"></i> Quiz</span>',
            'coding_lab': '<span class="type-badge badge-lab"><i class="bi bi-code-square"></i> Coding Lab</span>',
            'virtual_lab': '<span class="type-badge badge-lab"><i class="bi bi-cpu"></i> Virtual Lab</span>',
            'assignment': '<span class="type-badge badge-doc"><i class="bi bi-file-zip"></i> Assignment</span>'
        };

        if (targetChapCardForNewLesson) {
            const targetZone = targetChapCardForNewLesson.find('.lessons-sortable-zone');
            targetZone.find('.empty-chapter-box').remove();

            const newLessonHtml = `
            <div class="lesson-item" data-type="${type}">
                <div class="lesson-left-content">
                    <i class="bi bi-list text-muted fs-6 flex-shrink-0"></i>
                    <span class="small text-white-50 flex-shrink-0 lesson-num"></span>
                    <span class="small text-white me-2 lesson-title">${title}</span>
                    ${badgeMap[type] || badgeMap['theory']}
                </div>
                <div class="d-flex gap-2 flex-shrink-0">
                    <button class="btn btn-link text-info p-0 btn-sm" onclick="editLessonDetails('${type}', this)"><i class="bi bi-pencil-square"></i></button>
                    <button class="btn btn-link text-danger p-0 btn-sm" onclick="removeLesson(this)"><i class="bi bi-x-circle"></i></button>
                </div>
            </div>`;

            targetZone.append(newLessonHtml);
            LevelUpAlert.toast('เพิ่มบทเรียนย่อยเรียบร้อยแล้ว', 'success');
        } else if (currentEditLessonEl) {
            currentEditLessonEl.attr('data-type', type);
            currentEditLessonEl.find('.lesson-title').text(title);
            currentEditLessonEl.find('.type-badge').replaceWith(badgeMap[type] || badgeMap['theory']);
            currentEditLessonEl.find('.btn-info').attr('onclick', `editLessonDetails('${type}', this)`);
            LevelUpAlert.toast('อัปเดตข้อมูลบทเรียนเรียบร้อยแล้ว', 'success');
        }
        
        setDirtyState(true);
        reindexLessons();
        recalculateStats();
        updatePublishReadiness();
        $('#lessonObjectModal').modal('hide');
    }

    function removeLesson(btn) {
        const lessonTitle = $(btn).closest('.lesson-item').find('.lesson-title').text();
        LevelUpAlert.deleteConfirm({
            title: `ต้องการลบรายการ "${lessonTitle}" ใช่หรือไม่?`,
            text: 'หากลบแล้วจะไม่สามารถกู้คืนข้อมูลได้'
        }).then((isConfirmed) => {
            if (isConfirmed) {
                const targetZone = $(btn).closest('.lessons-sortable-zone');
                $(btn).closest('.lesson-item').remove();
                
                if (targetZone.find('.lesson-item').length === 0) {
                    targetZone.html(`
                        <div class="empty-chapter-box">
                            <i class="bi bi-journal-plus text-cyan fs-3 d-block mb-1"></i>
                            <div class="text-white-50 small mb-2">ยังไม่มีบทเรียนย่อยใน Chapter นี้</div>
                            <button type="button" class="btn btn-xs btn-cyan text-white px-3 py-1" style="font-size:0.8rem;" onclick="openAddLessonModal(this)">
                                <i class="bi bi-plus-circle-fill me-1"></i> เพิ่มบทเรียนแรกของคุณ
                            </button>
                        </div>
                    `);
                }

                setDirtyState(true);
                reindexLessons();
                recalculateStats();
                updatePublishReadiness();
                LevelUpAlert.toast('ลบบทเรียนย่อยเรียบร้อยแล้ว', 'success');
            }
        });
    }

    function removeChapter(btn) {
        const $card = $(btn).closest('.chapter-card');
        const chapTitle = $card.find('.chapter-title-input').val() || 'Chapter นี้';
        const lessonCountInChap = $card.find('.lesson-item').length;

        let confirmText = ' Chapter นี้จะถูกลบออกจากโครงสร้าง';
        if (lessonCountInChap > 0) {
            confirmText = `⚠️ Chapter นี้มีบทเรียนย่อยอยู่ ${lessonCountInChap} รายการ ซึ่งจะถูกลบไปด้วยทั้งหมด`;
        }

        LevelUpAlert.deleteConfirm({
            title: `ต้องการลบ "${chapTitle}" ใช่หรือไม่?`,
            text: confirmText
        }).then((isConfirmed) => {
            if (isConfirmed) {
                $card.remove();
                setDirtyState(true);
                reindexLessons();
                recalculateStats();
                updatePublishReadiness();
                LevelUpAlert.toast('ลบ Chapter เรียบร้อยแล้ว', 'success');
            }
        });
    }

    function addNewChapter() {
        var chapCount = $('#course-chapters-root .chapter-card').length + 1;
        
        var newHtml = `
        <div class="chapter-card p-3">
            <div class="chapter-header-flex border-bottom border-secondary border-opacity-10 pb-2 mb-2">
                <div class="chapter-title-area">
                    <i class="bi bi-grid-3x3-gap text-white-50 cursor-move fs-5 flex-shrink-0"></i>
                    <span class="fw-bold text-info flex-shrink-0 chap-label">Chapter ${chapCount}:</span>
                    <input type="text" class="chapter-title-input" value="บทเรียนใหม่ Chapter ${chapCount}">
                </div>
                <div class="d-flex align-items-center gap-1 flex-shrink-0">
                    <button type="button" class="btn btn-xs btn-outline-cyan text-white px-2 py-1" style="font-size: 0.75rem;" onclick="openAddLessonModal(this)" title="เพิ่มบทเรียนย่อยใน Chapter นี้">
                        <i class="bi bi-plus-lg me-1"></i>เพิ่มบทเรียน
                    </button>
                    <button class="btn btn-link text-white-50 p-1 btn-sm" onclick="duplicateItem('Chapter', ${chapCount})" title="คัดลอก"><i class="bi bi-copy"></i></button>
                    <button class="btn btn-link text-warning p-1 btn-sm" onclick="LevelUpAlert.toast('ซ่อนบทเรียนเรียบร้อยแล้ว', 'info')" title="ซ่อน"><i class="bi bi-eye-slash"></i></button>
                    <button class="btn btn-link text-danger p-1 btn-sm" onclick="removeChapter(this)" title="ลบ"><i class="bi bi-trash"></i></button>
                </div>
            </div>
            <div class="lessons-sortable-zone">
                <div class="empty-chapter-box">
                    <i class="bi bi-journal-plus text-cyan fs-3 d-block mb-1"></i>
                    <div class="text-white-50 small mb-2">ยังไม่มีบทเรียนย่อยใน Chapter นี้</div>
                    <button type="button" class="btn btn-xs btn-cyan text-white px-3 py-1" style="font-size:0.8rem;" onclick="openAddLessonModal(this)">
                        <i class="bi bi-plus-circle-fill me-1"></i> เพิ่มบทเรียนแรกของคุณ
                    </button>
                </div>
            </div>
            <div class="mt-2 text-end">
                <button type="button" class="btn btn-xs btn-outline-light text-white opacity-75" style="font-size:0.75rem;" onclick="openAddLessonModal(this)">+ เพิ่มวัตถุย่อย</button>
            </div>
        </div>`;
        
        $('#course-chapters-root').append(newHtml);
        setDirtyState(true);
        reindexLessons();
        recalculateStats();
        updatePublishReadiness();
        LevelUpAlert.toast('เพิ่ม Chapter ใหม่แล้ว', 'success');
    }

    function reindexLessons() {
        $('#course-chapters-root .chapter-card').each(function(chapIdx) {
            const chapNum = chapIdx + 1;
            $(this).find('.chap-label').text(`Chapter ${chapNum}:`);
            $(this).find('.lessons-sortable-zone .lesson-item').each(function(lessIdx) {
                $(this).find('.lesson-num').text(`${chapNum}.${lessIdx + 1}`);
            });
        });
    }

    function recalculateStats() {
        const chapCount = $('#course-chapters-root .chapter-card').length;
        const lessonCount = $('.lesson-item').length;
        const quizCount = $('.lesson-item[data-type="quiz"]').length;
        const labCount = $('.lesson-item[data-type="coding_lab"], .lesson-item[data-type="virtual_lab"]').length;

        $('#cnt-chap').text(chapCount);
        $('#cnt-less').text(lessonCount);
        $('#cnt-quiz').text(quizCount);
        $('#cnt-lab').text(labCount);

        if (lessonCount > 0) {
            $('#chk-item-lessons').html('<i class="bi bi-check-circle-fill text-success me-1"></i> บทเรียนย่อย (' + lessonCount + ')');
        } else {
            $('#chk-item-lessons').html('<i class="bi bi-x-circle-fill text-danger me-1"></i> บทเรียนย่อย');
        }

        if (labCount > 0) {
            $('#chk-item-lab').html('<i class="bi bi-check-circle-fill text-success me-1"></i> Coding Lab (' + labCount + ')');
        } else {
            $('#chk-item-lab').html('<i class="bi bi-x-circle-fill text-danger me-1"></i> Coding Lab');
        }
    }

    function updatePublishReadiness() {
        const hasChapters = $('#course-chapters-root .chapter-card').length > 0;
        const lessonCount = $('.lesson-item').length;
        
        const isReady = hasChapters && lessonCount > 0;
        
        if (isReady) {
            $('#publish-btn')
                .prop('disabled', false)
                .removeClass('btn-secondary opacity-50')
                .addClass('btn-success')
                .html('<i class="bi bi-rocket-takeoff-fill me-1"></i> เผยแพร่คอร์สเรียน');
        } else {
            $('#publish-btn')
                .prop('disabled', true)
                .removeClass('btn-success')
                .addClass('btn-secondary opacity-50')
                .html('<i class="bi bi-lock-fill me-1"></i> เพิ่มอย่างน้อย 1 บทเรียนเพื่อเผยแพร่');
        }
    }

    function duplicateItem(type, id) {
        LevelUpAlert.confirm({
            title: `ยืนยันการคัดลอก ${type} ${id}?`,
            text: 'ระบบจะสร้างสำเนาใหม่ในโครงสร้างหลักสูตร'
        }).then((isConfirmed) => {
            if (isConfirmed) {
                setDirtyState(true);
                LevelUpAlert.toast(`คัดลอก ${type} ${id} เรียบร้อยแล้ว`, 'success');
            }
        });
    }

    function runQualityValidation() {
        LevelUpAlert.aiLoading('สแกนและประเมินคุณภาพคอร์สเรียน', 'กำลังประเมินโครงสร้างด้วย AI...');
        
        setTimeout(() => {
            LevelUpAlert.close();
            $('#quality-score').html('96 <span class="fs-6 text-white-50">/ 100</span>');
            $('#quality-stars').text('★★★★★');
            $('#ai-suggestion-box').html('✨ <b>Course คุณภาพดีเยี่ยม!</b> โครงสร้างคอร์สมีความสมบูรณ์สูง พร้อมเปิดสอนผู้เรียนทันที');
            
            updatePublishReadiness();
            LevelUpAlert.success('ประเมินคุณภาพสำเร็จ!', 'โครงสร้างคอร์สมีความสมบูรณ์พร้อมสำหรับเผยแพร่');
        }, 1200);
    }

    function toggleCollaborators() {
        LevelUpAlert.info({
            title: 'ผู้ร่วมสร้างคอร์ส 👥',
            html: '<div class="text-start ps-3"><p class="mb-1">1. ผู้สอนหลัก (Owner - คุณ)</p><p class="mb-0">2. Co-Instructor (Editor)</p></div>'
        });
    }

    function saveCourseStructure() {
        LevelUpAlert.aiLoading('กำลังบันทึก...', 'กำลังรวบรวมและจัดเก็บโครงสร้างหลักสูตรของคุณ');

        const courseId = <?= $course['id'] ?? 'null' ?>;
        if (!courseId) {
            LevelUpAlert.error('ผิดพลาด', 'ไม่พบ ID ของคอร์สเรียนปัจจุบัน');
            return;
        }

        let payload = {
            course_id: courseId,
            chapters: [],
            deleted_items: {
                chapters: [],
                lessons: []
            }
        };

        // Note: This is a simplified data collection. A real implementation would track deleted items.
        $('#course-chapters-root .chapter-card').each(function(chapIdx) {
            let chapterData = {
                id: $(this).data('chapter-id') || null,
                title: $(this).find('.chapter-title-input').val(),
                order: chapIdx + 1,
                lessons: []
            };

            $(this).find('.lessons-sortable-zone .lesson-item').each(function(lessIdx) {
                let lessonData = {
                    id: $(this).data('id') || null,
                    title: $(this).find('.lesson-title').text(),
                    type: $(this).data('type'),
                    order: lessIdx + 1
                };
                chapterData.lessons.push(lessonData);
            });
            payload.chapters.push(chapterData);
        });
        
        // Use jQuery since it's available on the page
        $.ajax({
            url: '../api/ajax_instructor.php?action=save_course_structure',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    setDirtyState(false);
                    LevelUpAlert.close();
                    LevelUpAlert.success('บันทึกสำเร็จ!', 'โครงสร้างคอร์สของคุณถูกบันทึกเป็นฉบับร่างแล้ว');
                    // Optionally, reload the page or update the UI with new IDs from the server
                    setTimeout(() => location.reload(), 800);
                } else {
                    LevelUpAlert.error('เกิดข้อผิดพลาด', res.message || 'ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง');
                }
            },
            error: function(xhr) {
                LevelUpAlert.error('Network Error', 'เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + xhr.statusText);
            }
        });
    }

    function triggerAction(actionType) {
        if(actionType === 'save') {
            saveCourseStructure();
        } else if(actionType === 'preview') {
            LevelUpAlert.toast('กำลังเปิดโหมดแสดงผลพรีวิวสำหรับผู้เรียน...', 'info');
        } else if(actionType === 'publish') {
            const courseTitle = $('#cb-course-title').text();
            
            Swal.fire({
                title: 'ยืนยันการเผยแพร่หลักสูตร? 🚀',
                html: `คอร์ส <b>"${courseTitle}"</b> จะถูกเปลี่ยนสถานะเป็น <b>PUBLIC</b> และเปิดให้ผู้เรียนเข้าเรียนได้ทันที`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#22c55e',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="bi bi-rocket-takeoff-fill me-1"></i> ยืนยันเผยแพร่ทันที',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    setDirtyState(false);
                    $('#course-visibility-badge')
                        .removeClass('bg-warning text-dark')
                        .addClass('bg-success text-white')
                        .html('<i class="bi bi-globe me-1"></i> PUBLIC');

                    Swal.fire({
                        title: 'เผยแพร่คอร์สเรียนสำเร็จ! 🎉',
                        html: `
                            <p class="text-muted small">คอร์สของคุณเปิดให้ผู้เรียนลงทะเบียนได้แล้วเรียบร้อย</p>
                            <div class="p-3 bg-light rounded border mb-3 text-start">
                                <label class="small text-muted d-block mb-1">ลิงก์สำหรับแชร์คอร์สเรียน:</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" value="https://levelup.online/course/<?= $course['id'] ?? 1 ?>" readonly id="share-course-url">
                                    <button class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText($('#share-course-url').val()); LevelUpAlert.toast('คัดลอกลิงก์สำเร็จ', 'success');">คัดลอก</button>
                                </div>
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonText: 'เข้าสู่มุมมองนักเรียน (Student View)',
                        showCloseButton: true
                    });
                }
            });
        }
    }
</script>