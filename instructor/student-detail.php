<?php
$ROOT_PATH = '../';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// รับค่า ID ผู้เรียนที่ส่งมาจาก URL
$student_id = isset($_GET['id']) ? trim($_GET['id']) : '';

if (empty($student_id)) {
    die("<div style='background:#090d1a; color:white; padding:50px; text-align:center; font-family:sans-serif;'><h2>❌ ไม่พบข้อมูลรหัสผู้เรียน</h2><a href='javascript:history.back()' style='color:#6366f1;'>กลับไปหน้ารายชื่อ</a></div>");
}

/* 
========================================================================
   💾 DATABASE CONNECTION MODULE
========================================================================
   แก้ไขข้อมูลด้านล่างให้ตรงกับ Database ของคุณ
*/
$db_host = 'localhost';
$db_name = 'your_database_name';
$db_user = 'root';
$db_pass = '';

$student_data = null;
$db_connected = false;

try {
    // ลองเชื่อมต่อ Database
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db_connected = true;

    // คำสั่ง SQL ดึงข้อมูลผู้เรียน (เปลี่ยนชื่อ Table และ Field ตามจริง)
    $stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = :id");
    $stmt->execute(['id' => $student_id]);
    $student_data = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // ถ้าต่อ DB ไม่ได้ หรือยังไม่ได้ตั้งค่า จะใช้ระบบ Mock Data ด้านล่างแทนเพื่อให้เว็บไม่พัง
    $db_connected = false;
}

// ---------------------------------------------------------
// 🎭 FALLBACK: DYNAMIC MOCK DATA (ข้อมูลจำลองแบบเปลี่ยนตาม ID)
// ---------------------------------------------------------
if (!$student_data) {
    // สร้างตารางข้อมูลจำลอง (เสมือนเป็น Database)
    $mock_database = [
        'STU001' => [
            'full_name' => 'สมชาย ใจดี',
            'email' => 'somchai.j@academy.io',
            'avatar' => 'https://images.unsplash.com/photo-1599566150163-29194dcaad36?q=80&w=200&auto=format&fit=crop',
            'career_goal' => 'Frontend Developer',
            'level' => 3, 'xp' => 3200, 'courses_completed' => 2,
            'study_hours' => 8.5, 'progress' => 25,
            'risk_status' => 'Low', 'last_active' => 'วันนี้'
        ],
        'STU002' => [
            'full_name' => 'สมหญิง รักเรียน',
            'email' => 'somying.r@academy.io',
            'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=200&auto=format&fit=crop',
            'career_goal' => 'Fullstack Developer',
            'level' => 8, 'xp' => 9500, 'courses_completed' => 6,
            'study_hours' => 24.0, 'progress' => 75,
            'risk_status' => 'Low', 'last_active' => '3 ชม. ที่แล้ว'
        ],
        'STU003' => [
            'full_name' => 'ณัฐนันท์ พรหมดี',
            'email' => 'natthanan.p@academy.io',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=200&auto=format&fit=crop',
            'career_goal' => 'Backend Developer',
            'level' => 5, 'xp' => 5800, 'courses_completed' => 4,
            'study_hours' => 12.5, 'progress' => 41,
            'risk_status' => 'High', 'last_active' => 'เมื่อวาน'
        ]
    ];

    // เช็คว่า ID ที่ส่งมา มีใน Mock Database ไหม
    if (array_key_exists($student_id, $mock_database)) {
        $student_data = $mock_database[$student_id];
        $student_data['student_id'] = $student_id;
    } else {
        // ถ้ากด ID อื่นๆ ที่ไม่มีในตารางด้านบน ให้สร้างข้อมูลสุ่ม (Auto Generate) ขึ้นมา
        $student_data = [
            'student_id' => $student_id,
            'full_name' => 'ผู้เรียน ' . $student_id,
            'email' => strtolower($student_id) . '@academy.io',
            'avatar' => 'https://ui-avatars.com/api/?name=' . $student_id . '&background=6366f1&color=fff&size=200',
            'career_goal' => 'ยังไม่ระบุเป้าหมาย',
            'level' => 1, 'xp' => 100, 'courses_completed' => 0,
            'study_hours' => 1.0, 'progress' => 5,
            'risk_status' => 'Low', 'last_active' => 'เพิ่งเข้าใช้งาน'
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="th" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ผู้เรียน - <?= htmlspecialchars($student_data['full_name']) ?></title>
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- 
    ========================================================================
         LEVELUP SYSTEM DESIGN ENGINE (Light & Dark Mode Supported)
    ========================================================================
    -->
    <style>
        :root, html[data-theme="dark"] {
            --lv-bg-dark: #090d1a;
            --lv-card-bg: rgba(20, 27, 45, 0.75);
            --lv-card-hover-bg: rgba(28, 38, 62, 0.85);
            --lv-border-glow: rgba(99, 102, 241, 0.18);
            --lv-card-shadow: 0 12px 40px -10px rgba(0, 0, 0, 0.35);
            
            --lv-primary: #6366F1;
            --lv-success: #10B981;
            --lv-warning: #F59E0B;
            --lv-info: #06B6D4;
            --lv-danger: #EF4444;
            
            --lv-text-main: #f1f5f9;
            --lv-text-muted: #94a3b8;
            --lv-inner-bg: rgba(255, 255, 255, 0.03);
            --lv-inner-border: rgba(255, 255, 255, 0.06);
            --lv-btn-bg: rgba(255, 255, 255, 0.03);
            --lv-btn-border: rgba(255, 255, 255, 0.08);
            --lv-btn-hover: rgba(255, 255, 255, 0.08);
        }

        /* ☀️ LIGHT MODE OVERRIDES - Grounded & High Contrast Architecture */
        html[data-theme="light"] {
            --lv-bg-dark: #f1f5f9;
            --lv-card-bg: #ffffff;
            --lv-card-hover-bg: #ffffff;
            --lv-border-glow: rgba(203, 213, 225, 0.8);
            --lv-card-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.05), 0 4px 12px -2px rgba(15, 23, 42, 0.03);
            
            --lv-primary: #4f46e5;
            --lv-success: #059669;
            --lv-warning: #d97706;
            --lv-info: #0891b2;
            --lv-danger: #dc2626;
            
            --lv-text-main: #0f172a;
            --lv-text-muted: #64748b;
            --lv-inner-bg: #f8fafc;
            --lv-inner-border: #e2e8f0;
            --lv-btn-bg: #ffffff;
            --lv-btn-border: #cbd5e1;
            --lv-btn-hover: #f1f5f9;
        }

        body {
            background-color: var(--lv-bg-dark) !important;
            font-family: 'Outfit', 'Kanit', sans-serif;
            color: var(--lv-text-main) !important;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Utility classes overrides for Light Mode */
        html[data-theme="light"] .text-white { color: var(--lv-text-main) !important; }
        html[data-theme="light"] .text-muted { color: var(--lv-text-muted) !important; }

        /* Cyber Grid Background Effect - Natural & Softened */
        .dashboard-section {
            background: radial-gradient(circle at 15% 15%, rgba(99, 102, 241, 0.08) 0%, transparent 45%),
                        radial-gradient(circle at 85% 75%, rgba(139, 92, 246, 0.05) 0%, transparent 50%),
                        linear-gradient(rgba(150, 150, 150, 0.04) 1px, transparent 1px),
                        linear-gradient(90deg, rgba(150, 150, 150, 0.04) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 36px 36px, 36px 36px;
            min-height: 100vh;
        }

        html[data-theme="light"] .dashboard-section {
            background: radial-gradient(circle at 20% 20%, rgba(99, 102, 241, 0.04) 0%, transparent 50%),
                        linear-gradient(rgba(203, 213, 225, 0.25) 1px, transparent 1px),
                        linear-gradient(90deg, rgba(203, 213, 225, 0.25) 1px, transparent 1px);
            background-size: 100% 100%, 32px 32px, 32px 32px;
        }

        /* Glassmorphism Premium Card Architecture with Grounded Shadow */
        .dashboard-card {
            background: var(--lv-card-bg);
            border: 1px solid var(--lv-border-glow) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: var(--lv-card-shadow);
            border-radius: 18px !important;
            transition: all 0.3s ease;
        }

        .avatar-glow {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid var(--lv-primary);
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.25);
            object-fit: cover;
        }

        /* Stats Hub Design */
        .stat-icon {
            width: 50px; height: 50px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.35rem; border-radius: 14px;
        }
        .bg-primary-subtle { background: rgba(99, 102, 241, 0.12) !important; color: var(--lv-primary) !important; border: 1px solid rgba(99, 102, 241, 0.2); }
        .bg-success-subtle { background: rgba(16, 185, 129, 0.12) !important; color: var(--lv-success) !important; border: 1px solid rgba(16, 185, 129, 0.2); }
        .bg-warning-subtle { background: rgba(245, 158, 11, 0.12) !important; color: var(--lv-warning) !important; border: 1px solid rgba(245, 158, 11, 0.2); }
        
        /* Custom Progress Track */
        .progress {
            background-color: var(--lv-inner-bg) !important;
            border-radius: 50px;
            border: 1px solid var(--lv-inner-border);
            overflow: hidden;
        }
        .progress-bar {
            background: linear-gradient(90deg, var(--lv-primary) 0%, #8B5CF6 100%) !important;
            box-shadow: 0 0 12px rgba(99, 102, 241, 0.3);
        }

        .btn-outline-surface {
            background: var(--lv-btn-bg);
            border: 1px solid var(--lv-btn-border);
            color: var(--lv-text-main);
            transition: all 0.25s ease;
            border-radius: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        }
        .btn-outline-surface:hover {
            background: var(--lv-btn-hover);
            border-color: var(--lv-primary);
            color: var(--lv-primary);
        }

        .info-card-box {
            background: var(--lv-inner-bg);
            border: 1px solid var(--lv-inner-border);
            border-radius: 14px;
            padding: 16px;
        }
    </style>
</head>
<body>

<?php 
// 📌 ดึงไฟล์ Header มาใช้งาน (รองรับทั้งแบบอยู่ใน $ROOT_PATH หรือโฟลเดอร์เดียวกัน)
if (file_exists($ROOT_PATH . 'header.php')) {
    include_once $ROOT_PATH . 'header.php';
} elseif (file_exists('header.php')) {
    include_once 'header.php';
}
?>

<section class="dashboard-section py-4 py-lg-5">
    <div class="container-xl">
        
        <!-- Header Bar: ปุ่มย้อนกลับ และ Status Badge (ลบปุ่ม Dark Mode ออกแล้ว) -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <a href="javascript:history.back()" class="btn-outline-surface px-4 py-2">
                <i class="bi bi-arrow-left me-1"></i> กลับหน้ารายชื่อ
            </a>

            <div class="d-flex align-items-center gap-3">
                <?php if(!$db_connected): ?>
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-2">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> แสดงผลด้วย Mock Data (รอเชื่อม Database)
                    </span>
                <?php else: ?>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2">
                        <i class="bi bi-database-check me-2"></i> Database Connected
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="row g-4">
            <!-- ซ้าย: Profile Overview -->
            <div class="col-12 col-xl-4">
                <div class="dashboard-card p-4 text-center">
                    <img src="<?= htmlspecialchars($student_data['avatar']) ?>" class="avatar-glow mb-4" alt="Profile">
                    <h4 class="fw-bold text-white mb-1"><?= htmlspecialchars($student_data['full_name']) ?></h4>
                    <p class="mb-3" style="color: var(--lv-text-muted); font-size: 0.9rem;"><?= htmlspecialchars($student_data['email']) ?></p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1.5 font-monospace">
                            ID: <?= htmlspecialchars($student_data['student_id']) ?>
                        </span>
                        <span class="badge bg-warning bg-opacity-15 border border-warning text-warning px-3 py-1.5 font-monospace">
                            LV. <?= htmlspecialchars($student_data['level']) ?>
                        </span>
                    </div>

                    <div class="text-start mt-4 pt-4 border-top border-secondary border-opacity-25">
                        <p class="fs-sm mb-2" style="color: var(--lv-text-muted);">เป้าหมายสายอาชีพ</p>
                        <h6 class="fw-bold text-white mb-4"><i class="bi bi-crosshair text-info me-2"></i><?= htmlspecialchars($student_data['career_goal']) ?></h6>
                        
                        <p class="fs-sm mb-2" style="color: var(--lv-text-muted);">สถานะความเสี่ยง (AI Analysis)</p>
                        <?php if($student_data['risk_status'] === 'High'): ?>
                            <div class="p-3 rounded-3 mb-3" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.25);">
                                <h6 class="text-danger fw-bold mb-1"><i class="bi bi-exclamation-octagon-fill me-2"></i>เสี่ยงสูง (High Risk)</h6>
                                <p class="fs-sm mb-0 text-danger opacity-75">ต้องได้รับการติดตามดูแลอย่างใกล้ชิด</p>
                            </div>
                        <?php else: ?>
                            <div class="p-3 rounded-3 mb-3" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.25);">
                                <h6 class="text-success fw-bold mb-1"><i class="bi bi-shield-check me-2"></i>ปกติ (Low Risk)</h6>
                                <p class="fs-sm mb-0 text-success opacity-75">เรียนได้ตามเป้าหมายที่วางไว้</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ขวา: Stats & Progress -->
            <div class="col-12 col-xl-8">
                <!-- 4 Stats Blocks -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="dashboard-card p-3 d-flex flex-column align-items-center text-center gap-2">
                            <div class="stat-icon bg-success-subtle mb-1"><i class="bi bi-journal-check"></i></div>
                            <h4 class="fw-bold mb-0 text-white"><?= htmlspecialchars($student_data['courses_completed']) ?></h4>
                            <p class="fs-sm mb-0" style="color: var(--lv-text-muted);">คอร์สที่จบ</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="dashboard-card p-3 d-flex flex-column align-items-center text-center gap-2">
                            <div class="stat-icon bg-primary-subtle mb-1"><i class="bi bi-clock-history"></i></div>
                            <h4 class="fw-bold mb-0 text-white"><?= htmlspecialchars($student_data['study_hours']) ?></h4>
                            <p class="fs-sm mb-0" style="color: var(--lv-text-muted);">ชั่วโมงเรียน</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="dashboard-card p-3 d-flex flex-column align-items-center text-center gap-2">
                            <div class="stat-icon bg-warning-subtle mb-1"><i class="bi bi-lightning-charge"></i></div>
                            <h4 class="fw-bold mb-0 text-white"><?= number_format($student_data['xp']) ?></h4>
                            <p class="fs-sm mb-0" style="color: var(--lv-text-muted);">Total XP</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="dashboard-card p-3 d-flex flex-column align-items-center text-center gap-2">
                            <div class="stat-icon bg-info bg-opacity-10 text-info border border-info border-opacity-25 mb-1"><i class="bi bi-wifi"></i></div>
                            <h5 class="fw-bold mb-0 text-white mt-1"><?= htmlspecialchars($student_data['last_active']) ?></h5>
                            <p class="fs-sm mb-0 mt-1" style="color: var(--lv-text-muted);">ใช้งานล่าสุด</p>
                        </div>
                    </div>
                </div>

                <!-- Course Progress -->
                <div class="dashboard-card p-4 mb-4">
                    <h5 class="fw-bold mb-4 text-white">ความคืบหน้าภาพรวม (Overall Progress)</h5>
                    <div class="d-flex justify-content-between align-items-end mb-2">
                        <span class="fs-sm text-info fw-medium">Learning Path Completion</span>
                        <h4 class="fw-bold text-white mb-0"><?= htmlspecialchars($student_data['progress']) ?>%</h4>
                    </div>
                    <div class="progress mb-4" style="height: 12px;">
                        <div class="progress-bar" style="width: <?= htmlspecialchars($student_data['progress']) ?>%;"></div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-card-box">
                                <h6 class="text-white fw-bold fs-sm">วิชาที่กำลังเรียนล่าสุด</h6>
                                <p class="text-info mb-0 fs-sm">Python Fundamentals - Lab 3.2</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card-box">
                                <h6 class="text-white fw-bold fs-sm">AI Recommendation</h6>
                                <p class="text-warning mb-0 fs-sm">แนะนำให้ทบทวนเรื่อง REST API</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Database Info Guide -->
                <div class="p-3 rounded-3 mt-4" style="background: rgba(99, 102, 241, 0.08); border: 1px dashed rgba(99, 102, 241, 0.3);">
                    <p class="fs-sm mb-0" style="color: var(--lv-primary);">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>ถึงผู้พัฒนา:</strong> คุณสามารถแก้ไขคำสั่ง SQL ในบรรทัดที่ 31 ของไฟล์นี้ เพื่อดึงคอลัมน์ชื่อ, คะแนน, หรือข้อมูลอื่นๆ จาก Database ของคุณมาแสดงผลแทน Mock Data ได้เลย
                    </p>
                </div>

            </div>
        </div>
    </div>
</section>

<script>
    // ซิงค์ธีมกับ LocalStorage ที่เปลี่ยนจาก Header
    document.addEventListener('DOMContentLoaded', () => {
        const savedTheme = localStorage.getItem('theme') || localStorage.getItem('student_detail_theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
    });
</script>

</body>
</html>