<?php
$ROOT_PATH = '../';
// 🛠️ เปิดระบบแจ้งเตือน Error
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ==================================================================
// 1. เรียกใช้งาน Header
// ==================================================================
if (file_exists($ROOT_PATH . 'includes/header.php')) {
    include $ROOT_PATH . 'includes/header.php';
} else {
    die("<div style='padding:50px; text-align:center; color:red; font-family:sans-serif;'><h2>🚨 Error: ไม่พบไฟล์ 'includes/header.php'</h2></div>");
}

// ==================================================================
// 2. จัดการข้อมูล (ดึงจาก Session ที่เตรียมไว้ใน header.php)
// ==================================================================
$user_id       = $navUser['user_id'] ?? 'N';
$user_username = $navUser['username'] ?? 'Player One';
$user_avatar   = $navUser['avatar'] ?? 'https://ui-avatars.com/api/?name=Player+One';
$user_level    = $navUser['level'] ?? 'N';
$user_exp      = $navUser['exp'] ?? '0';

// คำนวณเปอร์เซ็นต์หลอด EXP
$numeric_exp    = is_numeric($user_exp) ? (int)$user_exp : 0;
$max_exp        = 1000; 
$exp_percentage = min(100, max(0, ($numeric_exp / $max_exp) * 100));

// ข้อมูลสถิติ (Mock Data)
$stats = [
    'enrolled_courses'  => 8,
    'completed_courses' => 5,
    'total_hours'       => 42,
    'current_streak'    => 7
];

$in_progress_courses = [
    [
        'id' => 'C001',
        'title' => 'Fullstack Web Development with Node.js & React',
        'image' => 'https://images.unsplash.com/photo-1633356122544-f134324a6cee?w=500&q=80',
        'progress' => 65,
        'last_lesson' => 'React Hooks & State Management'
    ],
    [
        'id' => 'C002',
        'title' => 'Python for Data Science & AI Essentials',
        'image' => 'https://images.unsplash.com/photo-1555949963-aa79dcee981c?w=500&q=80',
        'progress' => 20,
        'last_lesson' => 'Introduction to Pandas'
    ]
];

$badges = [
    ['name' => 'First Blood', 'icon' => '🩸', 'desc' => 'เรียนจบ 1 คอร์สแรก', 'unlocked' => true],
    ['name' => 'Streak Master', 'icon' => '🔥', 'desc' => 'ล็อกอินต่อเนื่อง 7 วัน', 'unlocked' => true],
    ['name' => 'Code Spy', 'icon' => '🕵️‍♀️', 'desc' => 'ส่งโปรเจกต์ผ่านในครั้งแรก', 'unlocked' => true],
    ['name' => 'AI Innovator', 'icon' => '🤖', 'desc' => 'เรียนจบคอร์ส AI ครบ 3 คอร์ส', 'unlocked' => false],
];
?>

<!-- ==================================================================
     🔥 MAIN CONTENT AREA
=================================================================== -->
<div id="content-area">

    <!-- นำเข้า Font เฉพาะหน้านี้ -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS เฉพาะหน้า Profile -->
    <style>
        /* 🌙 โหมดมืด (ค่าเริ่มต้น) */
        :root, html[data-theme="dark"] {
            --profile-bg: #090d1a;
            --profile-card-bg: #141b2d;
            --profile-card-border: rgba(99, 102, 241, 0.2);
            --profile-text-main: #ffffff;
            --profile-text-muted: #94a3b8;
            --profile-accent-cyan: #06b6d4;
            --profile-accent-purple: #6366F1;
            --profile-accent-purple-hover: #8B5CF6;
            --profile-card-shadow: 0 12px 40px -10px rgba(0, 0, 0, 0.6);
            --profile-box-bg: rgba(255, 255, 255, 0.03);
            --profile-box-border: rgba(255, 255, 255, 0.05);
            --profile-track-bg: rgba(255, 255, 255, 0.06);
            --profile-hover-bg: rgba(255, 255, 255, 0.05);
        }

        /* ☀️ โหมดสว่าง */
        html[data-theme="light"] {
            --profile-bg: #f8fafc;
            --profile-card-bg: rgba(255, 255, 255, 0.85);
            --profile-card-border: rgba(99, 102, 241, 0.15);
            --profile-text-main: #0f172a;
            --profile-text-muted: #64748b;
            --profile-accent-cyan: #0891b2; 
            --profile-accent-purple: #4f46e5;
            --profile-accent-purple-hover: #6366f1;
            --profile-card-shadow: 0 12px 40px -10px rgba(0, 0, 0, 0.08);
            --profile-box-bg: rgba(0, 0, 0, 0.02);
            --profile-box-border: rgba(0, 0, 0, 0.06);
            --profile-track-bg: rgba(0, 0, 0, 0.06);
            --profile-hover-bg: rgba(0, 0, 0, 0.04);
        }

        /* 🛠️ แก้ไขปัญหาขอบขาว/เส้นเด้ง บน-ล่าง เมื่อสลับโหมด */
        html, body {
            background-color: var(--profile-bg) !important;
            margin: 0 !important;
            padding: 0 !important;
            transition: background-color 0.3s ease, color 0.3s ease !important;
        }

        /* 🛠️ แก้ปัญหาเนื้อหาถูก Header บัง (เพิ่ม padding-top: 80px) */
        #content-area {
            background-color: var(--profile-bg) !important;
            color: var(--profile-text-main);
            transition: background-color 0.3s ease, color 0.3s ease !important;
            font-family: 'Kanit', sans-serif;
            min-height: calc(100vh - 100px);
            padding-top: 80px !important; /* เว้นระยะจาก Fixed Header ด้านบน */
            padding-bottom: 40px !important;
            margin: 0 !important;
            border: none !important;
            outline: none !important;
            box-sizing: border-box;
        }

        #content-area .h-font { font-family: 'Outfit', 'Kanit', sans-serif; }

        #content-area .lv-card {
            background: var(--profile-card-bg);
            border: 1px solid var(--profile-card-border);
            border-radius: 16px;
            padding: 24px;
            box-shadow: var(--profile-card-shadow);
            transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
            color: var(--profile-text-main);
        }
        #content-area .lv-card:hover { border-color: rgba(99, 102, 241, 0.45); }

        #content-area .lv-avatar-lg {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid var(--profile-accent-cyan);
            box-shadow: 0 0 20px rgba(6, 182, 212, 0.4);
            object-fit: cover;
            margin-bottom: 16px;
        }

        #content-area .lv-exp-box {
            background: var(--profile-box-bg);
            border: 1px solid var(--profile-box-border);
            transition: all 0.3s ease;
        }

        #content-area .lv-stat-box {
            background: var(--profile-box-bg);
            border: 1px solid var(--profile-box-border);
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            height: 100%;
            transition: all 0.3s ease;
        }
        #content-area .lv-stat-number {
            font-size: 24px;
            font-weight: 700;
            color: var(--profile-accent-cyan);
            font-family: 'Outfit', sans-serif;
        }
        #content-area .lv-stat-label {
            font-size: 13px;
            color: var(--profile-text-muted);
        }

        #content-area .lv-progress-track {
            background-color: var(--profile-track-bg);
            border-radius: 50px;
            height: 8px;
            overflow: hidden;
            width: 100%;
            margin-top: 8px;
            transition: all 0.3s ease;
        }
        #content-area .lv-progress-fill {
            background: linear-gradient(90deg, var(--profile-accent-purple) 0%, var(--profile-accent-purple-hover) 100%);
            height: 100%;
            border-radius: 50px;
            transition: width 1s ease-in-out;
            box-shadow: 0 0 10px rgba(99, 102, 241, 0.5);
            position: relative;
        }
        #content-area .lv-progress-fill::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0) 100%);
            animation: progressShine 2.5s infinite linear;
        }
        @keyframes progressShine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        #content-area .lv-course-item {
            display: flex;
            gap: 16px;
            padding: 16px;
            background: var(--profile-box-bg);
            border: 1px solid var(--profile-box-border);
            border-radius: 12px;
            margin-bottom: 16px;
            transition: background 0.2s ease, transform 0.2s ease;
        }
        #content-area .lv-course-item:last-child {
            margin-bottom: 0;
        }
        #content-area .lv-course-item:hover {
            background: var(--profile-hover-bg);
            transform: translateY(-2px);
        }
        #content-area .lv-course-img {
            width: 120px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
        }
        #content-area .lv-course-info {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        #content-area .lv-course-title {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
            line-height: 1.4;
            color: var(--profile-text-main);
            transition: color 0.3s ease;
        }

        #content-area .lv-badge-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 16px;
        }
        #content-area .lv-badge-item {
            text-align: center;
            opacity: 1;
            transition: transform 0.2s ease;
            cursor: pointer;
            position: relative;
        }
        #content-area .lv-badge-item.locked {
            opacity: 0.3;
            filter: grayscale(100%);
        }
        #content-area .lv-badge-item:hover { transform: scale(1.1); }
        #content-area .lv-badge-icon {
            font-size: 32px;
            background: var(--profile-box-bg);
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto 8px auto;
            border: 1px solid var(--profile-box-border);
            transition: all 0.3s ease;
        }
        #content-area .lv-badge-name {
            font-size: 12px;
            color: var(--profile-text-muted);
            line-height: 1.2;
            transition: color 0.3s ease;
        }

        #content-area .lv-btn-primary {
            background: linear-gradient(90deg, var(--profile-accent-purple), var(--profile-accent-purple-hover));
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        #content-area .lv-btn-primary:hover {
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.6);
            transform: translateY(-1px);
            color: white;
        }
        
        /* Utility classes overrides */
        #content-area .text-info { color: var(--profile-accent-cyan) !important; transition: color 0.3s ease; }
        #content-area .text-muted { color: var(--profile-text-muted) !important; transition: color 0.3s ease; }
    </style>

    <!-- HTML ของ Profile โครงสร้างหลัก -->
    <div class="container py-4">
        <div class="row g-4 align-items-stretch">
            
            <!-- LEFT COLUMN: Profile & Stats -->
            <div class="col-lg-4 d-flex flex-column">
                <div class="lv-card text-center d-flex flex-column justify-content-between h-100">
                    <div>
                        <img src="<?= htmlspecialchars($user_avatar) ?>" onerror="this.src='https://ui-avatars.com/api/?name=Player+One';" alt="User Avatar" class="lv-avatar-lg">
                        <h3 class="h-font fw-bold mb-1"><?= htmlspecialchars($user_username) ?></h3>
                        <p class="text-info mb-3 h-font">ID: <?= htmlspecialchars($user_id) ?></p>
                        
                        <div class="text-start p-3 rounded-3 mb-4 lv-exp-box">
                            <div class="d-flex justify-content-between align-items-center mb-1 h-font">
                                <span class="fw-bold" style="color: var(--profile-text-main);">LEVEL <?= htmlspecialchars($user_level) ?></span>
                                <span class="text-muted" style="font-size: 13px;"><?= number_format($numeric_exp) ?> / <?= number_format($max_exp) ?> XP</span>
                            </div>
                            <div class="lv-progress-track">
                                <div class="lv-progress-fill" style="width: <?= $exp_percentage ?>%;"></div>
                            </div>
                        </div>

                        <button class="lv-btn-primary w-100 mb-4" id="btnEditProfile" onclick="window.location.href = 'settings.php';">ตั้งค่าโปรไฟล์</button>
                    </div>

                    <!-- ส่วนข้อมูลสถิติ -->
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="lv-stat-box">
                                <div class="lv-stat-number"><?= $stats['enrolled_courses'] ?></div>
                                <div class="lv-stat-label">คอร์สทั้งหมด</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="lv-stat-box">
                                <div class="lv-stat-number text-success"><?= $stats['completed_courses'] ?></div>
                                <div class="lv-stat-label">เรียนจบแล้ว</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="lv-stat-box">
                                <div class="lv-stat-number text-warning"><?= $stats['current_streak'] ?></div>
                                <div class="lv-stat-label">Streak (วัน)</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="lv-stat-box">
                                <div class="lv-stat-number" style="color: #a855f7;"><?= $stats['total_hours'] ?></div>
                                <div class="lv-stat-label">ชั่วโมงเรียน</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Quests & Achievements -->
            <div class="col-lg-8 d-flex flex-column justify-content-between gap-4">
                
                <div class="lv-card flex-grow-1 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold m-0"><span class="me-2">🎯</span>คอร์สที่กำลังเรียน</h5>
                            <a href="#" class="text-decoration-none text-info ajax-link" style="font-size: 14px;">ดูทั้งหมด</a>
                        </div>

                        <div id="courseListContainer">
                            <?php
$ROOT_PATH = '../';
if (!empty($in_progress_courses)): ?>
                                <?php
$ROOT_PATH = '../';
foreach ($in_progress_courses as $course): ?>
                                    <div class="lv-course-item">
                                        <img src="<?= htmlspecialchars($course['image']) ?>" alt="Course Image" class="lv-course-img">
                                        <div class="lv-course-info">
                                            <div>
                                                <h6 class="lv-course-title"><?= htmlspecialchars($course['title']) ?></h6>
                                                <p class="m-0 mt-1" style="font-size: 13px; color: var(--profile-text-muted);">ล่าสุด: <?= htmlspecialchars($course['last_lesson']) ?></p>
                                            </div>
                                            <div class="mt-2">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span style="font-size: 12px; color: var(--profile-text-muted);">ความคืบหน้า</span>
                                                    <span class="h-font fw-bold" style="font-size: 12px; color: var(--profile-text-main);"><?= $course['progress'] ?>%</span>
                                                </div>
                                                <div class="lv-progress-track" style="height: 6px;">
                                                    <div class="lv-progress-fill" style="width: <?= $course['progress'] ?>%; background: linear-gradient(90deg, #10b981, #34d399); animation: none;"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <button class="lv-btn-primary btn-continue-course" data-id="<?= $course['id'] ?>">เรียนต่อ</button>
                                        </div>
                                    </div>
                                <?php
$ROOT_PATH = '../';
endforeach; ?>
                            <?php
$ROOT_PATH = '../';
else: ?>
                                <p class="text-center py-4" style="color: var(--profile-text-muted);">คุณยังไม่มีคอร์สที่กำลังเรียนอยู่ในขณะนี้</p>
                            <?php
$ROOT_PATH = '../';
endif; ?>
                        </div>
                    </div>
                </div>

                <div class="lv-card">
                    <h5 class="fw-bold mb-4"><span class="me-2">🏆</span>ความสำเร็จ (Achievements)</h5>
                    
                    <div class="lv-badge-grid">
                        <?php
$ROOT_PATH = '../';
foreach ($badges as $badge): ?>
                            <div class="lv-badge-item <?= $badge['unlocked'] ? '' : 'locked' ?>" title="<?= htmlspecialchars($badge['desc']) ?>">
                                <div class="lv-badge-icon"><?= $badge['icon'] ?></div>
                                <div class="lv-badge-name"><?= htmlspecialchars($badge['name']) ?></div>
                            </div>
                        <?php
$ROOT_PATH = '../';
endforeach; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Script สำหรับหน้า Profile -->
    <script>
    $(document).ready(function() {
        // Interactive Tooltips
        $('.lv-badge-item').on('mouseenter', function() {
            let title = $(this).attr('title');
            if(title) {
                $(this).append('<div class="badge-tooltip" style="position:absolute; background:rgba(0,0,0,0.8); color:#fff; padding:4px 8px; border-radius:4px; font-size:11px; top:-30px; left:50%; transform:translateX(-50%); white-space:nowrap; z-index:10; border: 1px solid rgba(255,255,255,0.1);">'+title+'</div>');
            }
        }).on('mouseleave', function() {
            $(this).find('.badge-tooltip').remove();
        });

        // AJAX Continue Course Handler
        $('.btn-continue-course').off('click').on('click', function(e) {
            e.preventDefault();
            let btn = $(this);
            let courseId = btn.data('id');
            
            let originalText = btn.text();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

            setTimeout(() => {
                alert('กำลังเข้าสู่คอร์ส: ' + courseId);
                btn.prop('disabled', false).text(originalText);
            }, 800);
        });
    });
    </script>
</div>
<!-- ================= END MAIN CONTENT ================= -->

<?php
$ROOT_PATH = '../';
// ==================================================================
// 3. เรียกใช้งาน Footer
// ==================================================================
if (file_exists($ROOT_PATH . 'includes/footer.php')) {
    require_once $ROOT_PATH . 'includes/footer.php';
} else {
    die("<div style='padding:20px; text-align:center; color:red; font-family:sans-serif;'><h2>🚨 Error: ไม่พบไฟล์ 'includes/footer.php'</h2></div>");
}
?>