<?php
// เปิดระบบแสดงผล Error เพื่อใช้ Debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$ROOT_PATH = '../';
$page_title = "Instructor Profile - LevelUp";

// 1. นำเข้า Header ตามแบบฟอร์มของไฟล์ตัวอย่าง
include_once $ROOT_PATH . 'includes/header.php';

// 2. จัดการเชื่อมต่อ Database (รองรับทั้ง PDO และ MySQLi แบบหน้า Courses)
if (!isset($conn) || !$conn) {
    if (file_exists($ROOT_PATH . 'config/db.php')) {
        require_once $ROOT_PATH . 'config/db.php';
    } elseif (file_exists($ROOT_PATH . 'db.php')) {
        require_once $ROOT_PATH . 'db.php';
    }
}

$db = $conn ?? $pdo ?? null;

// รับค่า id ของผู้สอน
$instructor_id = $_GET['id'] ?? 0;

if (!$instructor_id) {
    echo "<script>alert('ไม่พบ รหัสผู้ฝึกสอน'); window.location.href='courses.php';</script>";
    exit();
}

$instructor = null;
$courses = [];

if ($db) {
    try {
        if ($db instanceof PDO) {
            // ดึงข้อมูลผู้สอน
            $stmt_inst = $db->prepare("SELECT * FROM `users` WHERE id = :id");
            $stmt_inst->execute([':id' => $instructor_id]);
            $instructor = $stmt_inst->fetch(PDO::FETCH_ASSOC);

            // ดึงคอร์สเรียนของผู้สอนคนนี้
            $stmt_courses = $db->prepare("SELECT c.*, COALESCE(cat.name, 'ทั่วไป') AS category_name 
                                          FROM `course` c 
                                          LEFT JOIN `category` cat ON c.category_id = cat.id 
                                          WHERE c.user_id = :instructor_id 
                                          ORDER BY c.id DESC");
            $stmt_courses->execute([':instructor_id' => $instructor_id]);
            $courses = $stmt_courses->fetchAll(PDO::FETCH_ASSOC);

        } elseif ($db instanceof mysqli) {
            // กรณีฐานข้อมูลเป็น MySQLi
            $stmt_inst = $db->prepare("SELECT * FROM `users` WHERE id = ?");
            $stmt_inst->bind_param("i", $instructor_id);
            $stmt_inst->execute();
            $res_inst = $stmt_inst->get_result();
            $instructor = $res_inst->fetch_assoc();

            $stmt_courses = $db->prepare("SELECT c.*, COALESCE(cat.name, 'ทั่วไป') AS category_name 
                                          FROM `course` c 
                                          LEFT JOIN `category` cat ON c.category_id = cat.id 
                                          WHERE c.user_id = ? 
                                          ORDER BY c.id DESC");
            $stmt_courses->bind_param("i", $instructor_id);
            $stmt_courses->execute();
            $res_courses = $stmt_courses->get_result();
            while($row = $res_courses->fetch_assoc()) {
                $courses[] = $row;
            }
        }
    } catch (Exception $e) {
        die("<div class='container mt-5 text-center text-white'><h3>Database Error: " . $e->getMessage() . "</h3></div>");
    }
}

if (!$instructor) {
    echo "<div class='container mt-5 pt-5 text-center text-white'>
            <h2>ไม่พบข้อมูลผู้ฝึกสอนท่านนี้ในระบบ</h2>
            <a href='javascript:history.back()' class='btn btn-info mt-3'>กลับหน้าก่อนหน้า</a>
          </div>";
    include_once $ROOT_PATH . 'includes/footer.php';
    exit();
}

// เตรียมข้อมูลแสดงผล
$total_courses = count($courses);
$total_students = $total_courses * 750; 
$avg_rating = 4.8;
$total_reviews = $total_courses * 160;

$inst_name = $instructor['name'] ?? $instructor['fullname'] ?? $instructor['username'] ?? 'Certified Instructor';
$inst_email = $instructor['email'] ?? 'No email provided';
$inst_img = !empty($instructor['img']) ? $instructor['img'] : (!empty($instructor['avatar']) ? $instructor['avatar'] : '');
$inst_role = $instructor['user_role'] ?? $instructor['role'] ?? 'Certified Instructor';
$inst_bio = $instructor['bio'] ?? 'ผู้เชี่ยวชาญประจำสถาบัน LevelUp พร้อมถ่ายทอดความรู้และประสบการณ์ตรงจากอุตสาหกรรมซอฟต์แวร์';
?>

<!-- นำ CSS เฉพาะหน้านี้มาใส่ไว้ใน Content (ปรับสีให้เข้ากับธีม LevelUp) -->
<style>
    .dashboard-card {
        background: var(--lv-card-bg, rgba(20, 27, 45, 0.85));
        border: 1px solid var(--lv-border-glow, rgba(99, 102, 241, 0.25));
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    .stat-card {
        background: var(--lv-surface, rgba(20, 27, 45, 0.6));
        border: 1px solid var(--lv-border-glow, rgba(255, 255, 255, 0.08));
        transition: transform 0.2s ease, border-color 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        border-color: var(--lv-cyan, #06b6d4);
    }

    .avatar-circle {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--lv-cyan, #06b6d4), var(--lv-primary, #6366F1));
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

    .course-gamified-card {
        background: var(--lv-card-bg, rgba(20, 27, 45, 0.85));
        border: 1px solid var(--lv-border-glow, rgba(99, 102, 241, 0.25));
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .course-gamified-card:hover {
        transform: translateY(-5px);
        border-color: var(--lv-border-hover, rgba(6, 182, 212, 0.5));
        box-shadow: 0 15px 30px rgba(6, 182, 212, 0.15);
    }

    .course-image-wrapper {
        position: relative;
        height: 160px;
        background: linear-gradient(135deg, #1e1b4b 0%, #311042 100%);
    }

    .course-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .text-main { color: var(--lv-text-main, #f8fafc) !important; }
    .text-muted-custom { color: var(--lv-text-muted, #cbd5e1) !important; }
</style>

<div class="container py-4">
    <!-- Top Bar / Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="javascript:history.back()" class="btn btn-outline-info btn-sm rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> กลับหน้าก่อนหน้า
        </a>
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
                </div>

                <h4 class="fw-bold text-main mb-1"><?= htmlspecialchars($inst_name) ?></h4>
                <p class="text-info small mb-2"><?= htmlspecialchars($inst_role) ?></p>
                <p class="text-muted-custom small mb-3"><i class="fas fa-building me-1"></i> LevelUp Academy</p>

                <div class="d-flex justify-content-center align-items-center gap-2 mb-4">
                    <span class="badge bg-dark border border-secondary px-3 py-2">
                        ID: <?= htmlspecialchars($instructor['id']) ?>
                    </span>
                    <span class="badge bg-info bg-opacity-25 text-info border border-info px-3 py-2 rounded-pill">
                        <i class="fas fa-check-circle me-1"></i> Verified
                    </span>
                </div>

                <hr class="border-secondary my-4 opacity-25">

                <!-- Bio Section -->
                <div class="text-start mb-4">
                    <h6 class="fw-bold text-main mb-2 fs-6"><i class="fas fa-user-tie text-info me-2"></i>เกี่ยวกับผู้ฝึกสอน (Bio)</h6>
                    <p class="text-muted-custom small fw-light lh-lg mb-0">
                        <?= nl2br(htmlspecialchars($inst_bio)) ?>
                    </p>
                </div>

                <!-- Contact Info -->
                <div class="text-start">
                    <h6 class="fw-bold text-main mb-2 fs-6"><i class="fas fa-envelope text-info me-2"></i>อีเมลติดต่อ</h6>
                    <a href="mailto:<?= htmlspecialchars($inst_email) ?>" class="text-muted-custom text-decoration-none small">
                        <?= htmlspecialchars($inst_email) ?>
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
                        <div class="text-primary fs-3 mb-1"><i class="fas fa-users"></i></div>
                        <h4 class="fw-bold text-main mb-0"><?= number_format($total_students) ?></h4>
                        <span class="text-muted-custom" style="font-size: 0.75rem;">นักเรียนทั้งหมด</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card rounded-4 p-3 text-center h-100">
                        <div class="text-warning fs-3 mb-1"><i class="fas fa-star"></i></div>
                        <h4 class="fw-bold text-main mb-0"><?= number_format($avg_rating, 1) ?></h4>
                        <span class="text-muted-custom" style="font-size: 0.75rem;">คะแนนเฉลี่ย</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card rounded-4 p-3 text-center h-100">
                        <div class="text-info fs-3 mb-1"><i class="fas fa-book"></i></div>
                        <h4 class="fw-bold text-main mb-0"><?= number_format($total_courses) ?></h4>
                        <span class="text-muted-custom" style="font-size: 0.75rem;">คอร์สที่เปิดสอน</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card rounded-4 p-3 text-center h-100">
                        <div class="text-success fs-3 mb-1"><i class="fas fa-comments"></i></div>
                        <h4 class="fw-bold text-main mb-0"><?= number_format($total_reviews) ?></h4>
                        <span class="text-muted-custom" style="font-size: 0.75rem;">จำนวนรีวิว</span>
                    </div>
                </div>
            </div>

            <!-- Instructor's Courses Section -->
            <div class="dashboard-card rounded-4 p-4">
                <h5 class="fw-bold text-main mb-4 d-flex align-items-center">
                    <i class="fas fa-graduation-cap text-info me-2"></i> คอร์สเรียนที่เปิดสอนโดยท่านนี้
                </h5>

                <?php if (count($courses) > 0): ?>
                    <div class="row g-4">
                        <?php foreach ($courses as $c): ?>
                            <?php 
                                $c_img = !empty($c['img']) ? $c['img'] : 'https://via.placeholder.com/300x160';
                                
                                // จัดการเรื่องราคา
                                $c_price = 0;
                                if (isset($c['price']) && is_numeric($c['price'])) { $c_price = (float)$c['price']; } 
                                elseif (isset($c['type']) && is_numeric($c['type'])) { $c_price = (float)$c['type']; }

                                $c_category = !empty($c['category_name']) ? $c['category_name'] : 'ทั่วไป';
                                $c_level = !empty($c['difficulty']) ? ucfirst($c['difficulty']) : 'Beginner';
                            ?>
                            <div class="col-md-6">
                                <a href="course_detail.php?id=<?= htmlspecialchars($c['id'] ?? $c['course_id']) ?>" class="text-decoration-none">
                                    <div class="course-gamified-card">
                                        <!-- Course Image -->
                                        <div class="course-image-wrapper">
                                            <span class="position-absolute top-0 start-0 m-2 badge bg-dark border border-secondary text-white">
                                                <i class="fas fa-shield-alt text-warning"></i> <?= htmlspecialchars($c_level) ?>
                                            </span>
                                            <img src="<?= htmlspecialchars($c_img) ?>" alt="<?= htmlspecialchars($c['title'] ?? '') ?>">
                                        </div>

                                        <!-- Course Content -->
                                        <div class="p-3 d-flex flex-column flex-grow-1 justify-content-between">
                                            <div>
                                                <span class="text-info font-monospace small" style="font-size:0.75rem;"><?= htmlspecialchars($c_category) ?></span>
                                                <h6 class="fw-bold text-main mt-1 mb-2" style="font-size:0.95rem; line-height:1.4;">
                                                    <?= htmlspecialchars($c['title'] ?? $c['name'] ?? 'Untitled Course') ?>
                                                </h6>
                                            </div>

                                            <div class="mt-3 pt-2 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                                                <span class="text-warning small"><i class="fas fa-star me-1"></i>4.9</span>
                                                <span class="fw-bold text-success fs-6">
                                                    <?= $c_price == 0 ? 'FREE' : '฿' . number_format($c_price) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted-custom">
                        <i class="fas fa-book-open fs-1 d-block mb-3 opacity-50"></i>
                        <p class="mb-0">ยังไม่มีคอร์สเรียนที่เปิดสอน</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php 
// 3. นำเข้า Footer ตามแบบฟอร์มของไฟล์ตัวอย่าง
include_once $ROOT_PATH . 'includes/footer.php'; 
?>