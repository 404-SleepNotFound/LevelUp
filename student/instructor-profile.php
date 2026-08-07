<?php
// 1. เปิดระบบแสดงผล Error เพื่อใช้ Debug (แก้ปัญหา HTTP 500)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$ROOT_PATH = '../';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Check ไฟล์ db.php
if (!file_exists('../config/db.php')) {
    die("<div style='color:red; padding:20px; background:#fff;'>Error: ไม่พบไฟล์ ../config/db.php กรุณาตรวจสอบ Path ไฟล์เชื่อมต่อฐานข้อมูล</div>");
}

require_once '../config/db.php'; 

// รับค่า id
$instructor_id = $_GET['id'] ?? 0;

if (!$instructor_id) {
    echo "<script>alert('ไม่พบ รหัสผู้ฝึกสอน'); window.location.href='courses.php';</script>";
    exit();
}

try {
    // 2. ดึงข้อมูลผู้สอนจากตาราง `users`
    $sql_instructor = "SELECT * FROM `users` WHERE id = :id";
    $stmt_inst = $conn->prepare($sql_instructor);
    $stmt_inst->execute([':id' => $instructor_id]);
    $instructor = $stmt_inst->fetch(PDO::FETCH_ASSOC);

    if (!$instructor) {
        die("<div style='color:white; background:#090d1a; padding:50px; text-align:center; font-family:sans-serif;'>
                <h2>ไม่พบข้อมูลผู้ฝึกสอนท่านนี้ในระบบ (ID: ".htmlspecialchars($instructor_id).")</h2>
                <a href='javascript:history.back()' style='color:#06b6d4;'>กลับหน้าก่อนหน้า</a>
             </div>");
    }

    // 3. ดึงรายการคอร์สเรียนของผู้สอนคนนี้จากตาราง `course`
    $sql_courses = "SELECT c.*, ct.name AS category_name 
                    FROM `course` c 
                    LEFT JOIN `category` ct ON c.category_id = ct.id 
                    WHERE c.user_id = :instructor_id 
                    ORDER BY c.id DESC";
    $stmt_courses = $conn->prepare($sql_courses);
    $stmt_courses->execute([':instructor_id' => $instructor_id]);
    $courses = $stmt_courses->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("<div style='color:#ff5555; background:#111; padding:30px; font-family:monospace;'>
            <h3>⚠️ เกิดข้อผิดพลาดทางฐานข้อมูล (Database Error):</h3>
            <p>" . $e->getMessage() . "</p>
            <hr>
            <small>วิธีแก้: ตรวจสอบว่าชื่อคอลัมน์ในตาราง users หรือ course ตรงกับที่ใช้ Query หรือไม่</small>
         </div>");
}

// 4. เตรียมข้อมูลแสดงผล
$total_courses = count($courses);
$total_students = $total_courses * 750; 
$avg_rating = 4.8;
$total_reviews = $total_courses * 160;

// ป้องกันกรณีชื่อคอลัมน์ใน DB ไม่เหมือนกัน
$inst_name = $instructor['name'] ?? $instructor['fullname'] ?? $instructor['username'] ?? 'Certified Instructor';
$inst_email = $instructor['email'] ?? 'No email provided';
$inst_img = !empty($instructor['img']) ? $instructor['img'] : (!empty($instructor['avatar']) ? $instructor['avatar'] : '');
$inst_role = $instructor['user_role'] ?? $instructor['role'] ?? 'Certified Instructor';
$inst_bio = $instructor['bio'] ?? 'ผู้เชี่ยวชาญประจำสถาบัน LevelUp พร้อมถ่ายทอดความรู้และประสบการณ์ตรงจากอุตสาหกรรมซอฟต์แวร์';

// 📌 5. ดึง Header มาใช้งานตรงนี้ (ไม่ต้องสร้าง <!DOCTYPE html> ซ้ำแล้ว)
if (file_exists($ROOT_PATH . 'header.php')) {
    include_once $ROOT_PATH . 'header.php';
} elseif (file_exists('header.php')) {
    include_once 'header.php';
}
?>

<!-- นำ CSS เฉพาะหน้านี้มาใส่ไว้ใน Content -->
<style>
    /* 🌙 DARK MODE (DEFAULT) */
    :root, html[data-theme="dark"] {
        --lv-bg-dark: #090d1a;
        --lv-card-bg: rgba(20, 27, 45, 0.75);
        --lv-card-inner: rgba(20, 27, 45, 0.6);
        --lv-border-glow: rgba(99, 102, 241, 0.2);
        --lv-border-subtle: rgba(255, 255, 255, 0.08);
        --lv-cyan: #06b6d4;
        --lv-primary: #6366F1;
        --lv-text-main: #f8fafc;
        --lv-text-muted: #cbd5e1;
        --lv-badge-bg: rgba(0, 0, 0, 0.3);
    }

    /* ☀️ LIGHT MODE OVERRIDES */
    html[data-theme="light"] {
        --lv-bg-dark: #f1f5f9;
        --lv-card-bg: #ffffff;
        --lv-card-inner: #f8fafc;
        --lv-border-glow: rgba(203, 213, 225, 0.8);
        --lv-border-subtle: #e2e8f0;
        --lv-cyan: #0891b2;
        --lv-primary: #4f46e5;
        --lv-text-main: #0f172a;
        --lv-text-muted: #64748b;
        --lv-badge-bg: #e2e8f0;
    }

    /* Responsive Text Overrides for Light Mode */
    html[data-theme="light"] .text-white { color: var(--lv-text-main) !important; }
    html[data-theme="light"] .text-white-50 { color: var(--lv-text-muted) !important; }
    html[data-theme="light"] .bg-dark { background-color: var(--lv-badge-bg) !important; color: var(--lv-text-main) !important; }

    .dashboard-card {
        background: var(--lv-card-bg) !important;
        border: 1px solid var(--lv-border-glow) !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    .stat-card {
        background: var(--lv-card-inner) !important;
        border: 1px solid var(--lv-border-subtle) !important;
        transition: transform 0.2s ease, border-color 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        border-color: var(--lv-cyan) !important;
    }

    .avatar-circle {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--lv-cyan), var(--lv-primary));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 700;
        color: #fff;
        margin: 0 auto;
        box-shadow: 0 0 25px rgba(6, 182, 212, 0.3);
        object-fit: cover;
    }

    .course-card {
        background: var(--lv-card-inner) !important;
        border: 1px solid var(--lv-border-subtle) !important;
        transition: all 0.25s ease;
    }

    .course-card:hover {
        transform: translateY(-5px);
        border-color: var(--lv-cyan) !important;
        box-shadow: 0 10px 25px rgba(6, 182, 212, 0.15);
    }

    .badge-verified {
        background: rgba(6, 182, 212, 0.15);
        color: var(--lv-cyan);
        border: 1px solid rgba(6, 182, 212, 0.3);
        font-size: 0.75rem;
    }

    .badge-category {
        background: rgba(6, 182, 212, 0.2);
        color: var(--lv-cyan);
        border: 1px solid rgba(6, 182, 212, 0.3);
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .hover-text-cyan:hover {
        color: var(--lv-cyan) !important;
    }

    .border-color {
        border-color: var(--lv-border-subtle) !important;
    }

    .btn-surface {
        background: var(--lv-card-inner);
        border: 1px solid var(--lv-border-subtle);
        color: var(--lv-text-main);
        transition: all 0.2s ease;
    }
    .btn-surface:hover {
        background: var(--lv-border-subtle);
        color: var(--lv-text-main);
    }
</style>

<div class="container-xl mt-3 py-4">
    <!-- Top Bar / Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="javascript:history.back()" class="btn btn-surface btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> กลับหน้ารายชื่อ
        </a>
        
        <div class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill small">
            <i class="bi bi-database-check me-1"></i> ข้อมูลเชื่อมต่อระบบสำเร็จ
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Sidebar: Profile Details -->
        <div class="col-lg-4">
            <div class="dashboard-card rounded-4 p-4 text-center">
                <!-- Profile Image / Avatar -->
                <div class="position-relative d-inline-block mb-3">
                    <?php if (!empty($inst_img)): ?>
                        <img src="<?= htmlspecialchars($inst_img) ?>" class="avatar-circle border border-2 border-info" alt="Instructor Profile">
                    <?php else: ?>
                        <div class="avatar-circle">
                            <?= mb_substr(htmlspecialchars($inst_name), 0, 1, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>
                    <span class="position-absolute bottom-0 end-0 p-2 bg-info border border-light rounded-circle shadow" style="transform: translate(-5px, -5px);"></span>
                </div>

                <h4 class="fw-bold text-white mb-1"><?= htmlspecialchars($inst_name) ?></h4>
                <p class="text-info small mb-2"><?= htmlspecialchars($inst_role) ?></p>
                <p class="text-white-50 extra-small mb-3"><i class="bi bi-building me-1"></i> LevelUp Academy</p>

                <div class="d-flex justify-content-center align-items-center gap-2 mb-4">
                    <span class="badge bg-dark border border-secondary px-2.5 py-1.5" style="font-size: 0.75rem;">
                        ID: <?= htmlspecialchars($instructor['id']) ?>
                    </span>
                    <span class="badge badge-verified px-3 py-1.5 rounded-pill">
                        <i class="bi bi-patch-check-fill me-1"></i> Verified Instructor
                    </span>
                </div>

                <hr class="border-color my-4">

                <!-- Bio Section -->
                <div class="text-start mb-4">
                    <h6 class="fw-bold text-white mb-2 fs-6"><i class="bi bi-person-lines-fill text-info me-2"></i>เกี่ยวกับผู้ฝึกสอน (Bio)</h6>
                    <p class="text-white-50 small fw-light lh-lg mb-0">
                        <?= nl2br(htmlspecialchars($inst_bio)) ?>
                    </p>
                </div>

                <!-- Contact Info -->
                <div class="text-start">
                    <h6 class="fw-bold text-white mb-2 fs-6"><i class="bi bi-envelope text-info me-2"></i>อีเมลติดต่อ</h6>
                    <a href="mailto:<?= htmlspecialchars($inst_email) ?>" class="text-white-50 text-decoration-none small hover-text-cyan">
                        <i class="bi bi-envelope-at me-1"></i> <?= htmlspecialchars($inst_email) ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Main Area: Stats & Courses -->
        <div class="col-lg-8">
            <!-- 4 Stat Cards Grid -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="stat-card rounded-4 p-3 text-center h-100">
                        <div class="text-primary fs-3 mb-1"><i class="bi bi-people-fill"></i></div>
                        <h4 class="fw-bold text-white mb-0"><?= number_format($total_students) ?></h4>
                        <span class="text-white-50 extra-small">นักเรียนทั้งหมด</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card rounded-4 p-3 text-center h-100">
                        <div class="text-warning fs-3 mb-1"><i class="bi bi-star-fill"></i></div>
                        <h4 class="fw-bold text-white mb-0"><?= number_format($avg_rating, 1) ?></h4>
                        <span class="text-white-50 extra-small">คะแนนเฉลี่ย</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card rounded-4 p-3 text-center h-100">
                        <div class="text-info fs-3 mb-1"><i class="bi bi-journal-bookmark-fill"></i></div>
                        <h4 class="fw-bold text-white mb-0"><?= number_format($total_courses) ?></h4>
                        <span class="text-white-50 extra-small">คอร์สที่เปิดสอน</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card rounded-4 p-3 text-center h-100">
                        <div class="text-success fs-3 mb-1"><i class="bi bi-chat-left-text-fill"></i></div>
                        <h4 class="fw-bold text-white mb-0"><?= number_format($total_reviews) ?></h4>
                        <span class="text-white-50 extra-small">จำนวนรีวิว</span>
                    </div>
                </div>
            </div>

            <!-- Instructor's Courses Section -->
            <div class="dashboard-card rounded-4 p-4">
                <h5 class="fw-bold text-white mb-4 d-flex align-items-center">
                    <i class="bi bi-mortarboard text-info me-2"></i> คอร์สเรียนที่เปิดสอนโดยผู้ฝึกสอนท่านนี้
                </h5>

                <?php if (count($courses) > 0): ?>
                    <div class="row g-3">
                        <?php foreach ($courses as $c): ?>
                            <?php 
                                $c_img = !empty($c['img']) ? $c['img'] : 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=600&q=80';
                                
                                // จัดการเรื่องราคา
                                $c_price = 'เรียนฟรี';
                                if (isset($c['price']) && is_numeric($c['price'])) {
                                    $c_price = number_format((float)$c['price']) . ' ฿';
                                } elseif (isset($c['type']) && is_numeric($c['type'])) {
                                    $c_price = number_format((float)$c['type']) . ' ฿';
                                } elseif (!empty($c['type'])) {
                                    $c_price = $c['type'];
                                }

                                $c_category = !empty($c['category_name']) ? $c['category_name'] : 'ทั่วไป';
                            ?>
                            <div class="col-md-6">
                                <a href="course-detail.php?id=<?= $c['id'] ?>" class="text-decoration-none ajax-link">
                                    <div class="course-card rounded-4 overflow-hidden h-100 d-flex flex-column">
                                        <!-- Course Image -->
                                        <div class="position-relative">
                                            <img src="<?= htmlspecialchars($c_img) ?>" class="w-100 object-fit-cover" height="150" alt="<?= htmlspecialchars($c['title'] ?? '') ?>">
                                            <span class="badge badge-category position-absolute top-0 start-0 m-3 rounded-pill">
                                                <?= htmlspecialchars($c_category) ?>
                                            </span>
                                        </div>

                                        <!-- Course Content -->
                                        <div class="p-3 d-flex flex-column flex-grow-1">
                                            <h6 class="fw-bold text-white mb-2 line-clamp-2" style="line-height: 1.4;">
                                                <?= htmlspecialchars($c['title'] ?? 'Untitled Course') ?>
                                            </h6>
                                            <p class="text-white-50 extra-small line-clamp-2 mb-3 fw-light">
                                                <?= htmlspecialchars($c['short_description'] ?? $c['subtitle'] ?? '') ?>
                                            </p>

                                            <div class="mt-auto pt-2 border-top border-color d-flex justify-content-between align-items-center">
                                                <span class="fw-bold text-info fs-6"><?= $c_price ?></span>
                                                <span class="text-warning small"><i class="bi bi-star-fill me-1"></i>4.9</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <!-- Case: No Courses Found -->
                    <div class="text-center py-5 text-white-50">
                        <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary"></i>
                        <p class="mb-0">ยังไม่มีคอร์สเรียนที่เปิดสอนโดยผู้ฝึกสอนท่านนี้</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php 
// 📌 ถ้ามี footer ให้ include ตรงนี้
if (file_exists($ROOT_PATH . 'footer.php')) {
    include_once $ROOT_PATH . 'footer.php';
} elseif (file_exists('footer.php')) {
    include_once 'footer.php';
}
?>