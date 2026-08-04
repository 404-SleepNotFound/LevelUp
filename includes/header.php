<?php
// ระบบ Session และเช็คสถานะการเข้าสู่ระบบ
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($isLoggedIn)) {
    $isLoggedIn = isset($_SESSION['user_login']) ? true : false;
}

require_once __DIR__ . '/../config/db.php';

// 🔒 เพิ่มระบบป้องกันเปลี่ยน URL (Route Guard)
$currentPage = basename($_SERVER['SCRIPT_NAME']);
$allowedPages = ['index.php', 'login.php', 'register.php'];

if (!$isLoggedIn && !in_array($currentPage, $allowedPages)) {
    header("Location: index.php");
    exit();
}

// แตกข้อมูลโปรไฟล์ให้ตรงกับโครงสร้างหลัก
$sessionData = $_SESSION['user_login'] ?? null;
$navUser = [
    'user_id'  => $sessionData['user_id'] ?? ($_SESSION['user_id'] ?? 'N'),
    'username' => $sessionData['name'] ?? ($_SESSION['name'] ?? 'Player One'),
    'avatar'   => $sessionData['img'] ?? ($_SESSION['img'] ?? 'https://ui-avatars.com/api/?name=Player+One'),
    'level'    => $sessionData['level'] ?? ($_SESSION['level'] ?? ($_SESSION['level'] ?? 'N')),
    'streak'   => $sessionData['streak'] ?? ($_SESSION['login_streak'] ?? ($_SESSION['streak'] ?? 'N')),
    'exp'      => $sessionData['user_exp'] ?? ($_SESSION['user_exp'] ?? ($_SESSION['user_exp'] ?? '0'))
];

$category_data = [];
$sql = "SELECT * FROM `category`";
$result = $conn->query($sql);
if ($result) {
    $category_data = $result->fetchAll(PDO::FETCH_ASSOC);
}

$course_data = [];
$sql = "SELECT * FROM `course`";
$result = $conn->query($sql);
if ($result) {
    $course_data = $result->fetchAll(PDO::FETCH_ASSOC);
}

$sql = "SELECT *,
            c.id AS course_id,
            c.title AS course_name,
            c.img AS course_img,
            c.difficulty,
            cat.name AS category_name
        FROM `course` c 
        INNER JOIN `category` cat ON cat.id = c.category_id";

$result = $conn->query($sql);

$course_details = [];
if ($result) {
    $course_details = $result->fetchAll(PDO::FETCH_ASSOC);
}

// ตรวจสอบว่าเป็นการเรียกผ่าน AJAX (jQuery) หรือไม่
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if (!$isAjax):
?>
    <!DOCTYPE html>
    <html lang="th" data-theme="dark">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title>LevelUp - AI Gamified Learning Platform</title>

        <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/8074/8074788.png" type="image/png">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@500;700;800;900&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

        <!-- เพิ่ม jQuery & Bootstrap JS ในส่วน Header เพื่อให้ Dropdown และ Menu ทำงานได้ทันที -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- 🚀 เรียกใช้งาน Rocket Cursor -->
        <?php include_once __DIR__ . '/rocket_cursor.php'; ?>

        <!-- ⚡ โหลดธีมจาก localStorage ทันที ป้องกันหน้าจอวูบวาบ -->
        <script>
            (function() {
                const savedTheme = localStorage.getItem('theme') || 'dark';
                document.documentElement.setAttribute('data-theme', savedTheme);
            })();
        </script>

        <style>
            /* 🎨 CSS Variables สำหรับ Header สลับโหมด Dark/Light */
            :root,
            html[data-theme="dark"] {
                --body-bg: #0b0f19;
                --body-text: #f1f5f9;
                --nav-bg: #0f172a;
                --nav-border: #1e293b;
                --nav-text: #f8fafc;
                --nav-text-muted: #94a3b8;
                --nav-input-bg: rgba(15, 23, 42, 0.8);
                --nav-input-border: #334155;
                --nav-btn-theme-bg: rgba(255, 255, 255, 0.08);
                --nav-mobile-bg: #0f172a;
                --nav-dropdown-bg: #0f172a;
                --nav-dropdown-border: #1e293b;
                --nav-dropdown-text: #f1f5f9;
                --nav-dropdown-hover-bg: #1e293b;
                --nav-dropdown-hover-text: #ffffff;
            }

            html[data-theme="light"] {
                --body-bg: #f8fafc;
                --body-text: #0f172a;
                --nav-bg: #ffffff;
                --nav-border: #e2e8f0;
                --nav-text: #0f172a;
                --nav-text-muted: #64748b;
                --nav-input-bg: #f1f5f9;
                --nav-input-border: #cbd5e1;
                --nav-btn-theme-bg: rgba(0, 0, 0, 0.05);
                --nav-mobile-bg: #ffffff;
                --nav-dropdown-bg: #ffffff;
                --nav-dropdown-border: #e2e8f0;
                --nav-dropdown-text: #1e293b;
                --nav-dropdown-hover-bg: #f1f5f9;
                --nav-dropdown-hover-text: #0f172a;
            }

            /* 🛑 ป้องกันแสงขาววาบ (FOUC) โดยการตั้งค่าสีพื้นหลัง body ให้เปลี่ยนตาม Theme ทันที */
            html,
            body {
                background-color: var(--body-bg) !important;
                color: var(--body-text) !important;
                transition: background-color 0.3s ease, color 0.3s ease;
                min-height: 100vh;
            }

            /* การประยุกต์ใช้ตัวแปร CSS กับ Header */
            .lu-navbar {
                background-color: var(--nav-bg) !important;
                border-bottom: 1px solid var(--nav-border) !important;
                transition: background-color 0.3s ease, border-color 0.3s ease;
            }

            /* 🛠️ บังคับให้ไอคอนและข้อความอยู่บรรทัดเดียวกันเสมอ (ไม่ตกบรรทัด) */
            .lu-navbar .nav-link {
                color: var(--nav-text) !important;
                display: inline-flex !important;
                align-items: center !important;
                white-space: nowrap !important;
            }

            .lu-navbar .navbar-brand span,
            .lu-navbar .nav-icon-link {
                color: var(--nav-text) !important;
            }

            .lu-navbar .nav-link:hover {
                color: #6366f1 !important;
            }

            /* ช่องค้นหา */
            .lu-search-form {
                max-width: 320px;
                transition: all 0.2s ease;
            }

            .lu-search-input {
                background-color: var(--nav-input-bg) !important;
                border-color: var(--nav-input-border) !important;
                color: var(--nav-text) !important;
                transition: all 0.3s ease;
            }

            .lu-search-input::placeholder {
                color: var(--nav-text-muted) !important;
            }

            .lu-search-icon {
                color: var(--nav-text-muted) !important;
            }

            /* ปุ่ม Badge XP */
            .lu-badge-xp {
                background-color: var(--nav-input-bg) !important;
                border-color: var(--nav-input-border) !important;
                color: var(--nav-text) !important;
                white-space: nowrap !important;
            }

            /* 🛠️ สี Dropdown */
            .user-dropdown-menu {
                background-color: var(--nav-dropdown-bg) !important;
                border: 1px solid var(--nav-dropdown-border) !important;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4) !important;
                z-index: 1060 !important;
            }

            .user-dropdown-menu .dropdown-item {
                color: var(--nav-dropdown-text) !important;
                font-weight: 500;
                padding: 0.6rem 1rem;
                transition: all 0.15s ease;
            }

            .user-dropdown-menu .dropdown-item i {
                color: var(--nav-dropdown-text) !important;
                opacity: 0.85;
            }

            .user-dropdown-menu .dropdown-item:hover,
            .user-dropdown-menu .dropdown-item:focus {
                background-color: var(--nav-dropdown-hover-bg) !important;
                color: var(--nav-dropdown-hover-text) !important;
            }

            .user-dropdown-menu .dropdown-item.text-danger {
                color: #ef4444 !important;
            }

            .user-dropdown-menu .dropdown-item.text-danger i {
                color: #ef4444 !important;
            }

            .user-dropdown-menu .dropdown-item.text-danger:hover {
                background-color: rgba(239, 68, 68, 0.15) !important;
                color: #f87171 !important;
            }

            .profile-name-wrap {
                white-space: normal !important;
                word-wrap: break-word !important;
                word-break: break-all !important;
                line-height: 1.4;
                color: var(--nav-text-muted) !important;
                font-size: 0.85rem;
                font-weight: 700;
                padding: 0.5rem 1rem;
            }

            /* 📱/💻 แก้ไขปุ่ม AI Chat และองค์ประกอบข้างเคียงไม่ให้เบี้ยวบน iPad / Safari */
            .user-action-panel {
                flex-shrink: 0 !important;
            }

            .lu-ai-btn-group {
                flex-shrink: 0 !important;
                display: inline-flex !important;
                align-items: center !important;
                height: 38px !important;
            }

            .lu-ai-btn-group .btn {
                white-space: nowrap !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                height: 100% !important;
                padding-top: 0 !important;
                padding-bottom: 0 !important;
                line-height: 1 !important;
            }

            /* Toggle Icon ในโหมดสว่าง */
            html[data-theme="light"] .navbar-toggler-icon {
                filter: none !important;
            }

            /* 📱 แก้ไขการจัดระเบียบสำหรับหน้าจอ iPad และ แท็บเล็ต (768px - 1199px) */
            @media (min-width: 768px) and (max-width: 1199.98px) {
                .lu-search-form {
                    max-width: 180px !important;
                }

                .navbar-nav {
                    gap: 0.25rem !important;
                }

                .navbar-nav .nav-link {
                    font-size: 0.85rem !important;
                    padding-left: 0.3rem !important;
                    padding-right: 0.3rem !important;
                }

                .user-action-panel {
                    gap: 0.5rem !important;
                }
            }

            /* 📱 การตั้งค่าโหมดมือถือขนาดเล็ก */
            @media (max-width: 991.98px) {
                .navbar-collapse {
                    background-color: var(--nav-mobile-bg) !important;
                    padding: 1rem !important;
                    border-radius: 0.5rem !important;
                    margin-top: 0.5rem !important;
                    border: 1px solid var(--nav-border) !important;
                    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.15) !important;
                }

                /* บังคับเมนูรายการให้อยู่ชิดซ้ายบนมือถือ */
                .navbar-nav {
                    align-items: flex-start !important;
                    width: 100% !important;
                }

                .lu-search-form {
                    max-width: 100% !important;
                    margin-left: 0 !important;
                    margin-right: 0 !important;
                    margin-bottom: 1rem !important;
                    margin-top: 0 !important;
                }

                .navbar-nav .nav-link {
                    padding: 0.5rem 0 !important;
                    justify-content: flex-start !important;
                    width: 100% !important;
                }

                .user-action-panel {
                    flex-wrap: wrap !important;
                    justify-content: space-between !important;
                    padding-top: 1rem !important;
                    margin-top: 0.5rem !important;
                    width: 100% !important;
                    border-top: 1px solid var(--nav-border) !important;
                    gap: 0.5rem !important;
                }

                .user-action-panel .dropdown-menu {
                    position: absolute !important;
                    top: 100% !important;
                    right: 0 !important;
                    left: auto !important;
                    min-width: 220px !important;
                    z-index: 1060 !important;
                }
            }

            /* =========================================
               🔔 SweetAlert2 Dynamic Dark / Light Theme
               ========================================= */
            /* 🌙 Dark Mode */
            html[data-theme="dark"] .swal2-popup {
                background-color: #0f172a !important;
                color: #f8fafc !important;
                border: 1px solid #1e293b !important;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.5) !important;
            }

            html[data-theme="dark"] .swal2-title,
            html[data-theme="dark"] .swal2-html-container,
            html[data-theme="dark"] .swal2-content {
                color: #f8fafc !important;
            }

            /* ☀️ Light Mode */
            html[data-theme="light"] .swal2-popup {
                background-color: #ffffff !important;
                color: #0f172a !important;
                border: 1px solid #e2e8f0 !important;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            }

            html[data-theme="light"] .swal2-title,
            html[data-theme="light"] .swal2-html-container,
            html[data-theme="light"] .swal2-content {
                color: #0f172a !important;
            }
        </style>
    </head>

    <body style="padding-top: 76px;">

        <!-- ⏳ Cyber Loading Screen -->
        <div id="ajax-loading-screen" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:var(--body-bg); z-index:9999; opacity:0.95; align-items:center; justify-content:center; flex-direction:column;">
            <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem; border-width: 0.25em;"></div>
            <div class="mt-3 font-monospace text-muted style-xs" style="font-size: 0.75rem; letter-spacing: 1px;">LOADING DATA...</div>
        </div>

        <nav class="navbar navbar-expand-lg fixed-top lu-navbar shadow-sm" style="z-index: 1050 !important;">
            <div class="container">

                <!-- โลโก้ -->
                <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none ajax-link m-0 me-2" href="<?= $ROOT_PATH ?? '' ?>index.php">
                    <div class="lu-logo-icon fs-5 text-info"><i class="bi bi-rocket-takeoff-fill"></i></div>
                    <span class="fw-bold fs-4" style="font-family: 'Outfit', sans-serif;">LevelUp</span>
                </a>

                <!-- ปุ่ม Toggle มือถือ -->
                <button class="navbar-toggler border-0 shadow-none p-2"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#luMainNav"
                    aria-controls="luMainNav"
                    aria-expanded="false"
                    style="background: transparent !important;">
                    <span class="navbar-toggler-icon" style="filter: invert(1) brightness(2); width: 1.5em; height: 1.5em;"></span>
                </button>

                <!-- ส่วนของเมนูเมื่อหด/ขยาย -->
                <div class="collapse navbar-collapse" id="luMainNav">

                    <?php if ($isLoggedIn): ?>
                        <!-- ช่องค้นหา -->
                        <form class="mx-lg-2 d-flex flex-grow-1 lu-search-form" action="<?= $ROOT_PATH ?? '' ?>student/courses.php" method="GET" id="ajax-search-form">
                            <div class="position-relative w-100">
                                <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3 lu-search-icon" style="left: 0; z-index: 10;"></i>
                                <input type="text" name="search" class="form-control rounded-pill lu-search-input" placeholder="ค้นหา Quests, สกิล..." style="padding-left: 2.5rem; padding-right: 4.5rem;">
                                <span class="position-absolute top-50 translate-middle-y me-2 border rounded px-2 py-1 lu-search-icon d-none d-xl-block" style="right: 0; font-size: 0.75rem; z-index: 10; border-color: var(--nav-input-border) !important;">Ctrl K</span>
                            </div>
                        </form>

                        <!-- เมนูหลัก -->
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1 align-items-start align-items-lg-center">
                            <li class="nav-item"><a class="nav-link lu-nav-link ajax-link" href="<?= $ROOT_PATH ?? '' ?>student/courses.php"><i class="bi bi-journal-code me-1"></i> Quests</a></li>
                            <li class="nav-item"><a class="nav-link lu-nav-link ajax-link" href="<?= $ROOT_PATH ?? '' ?>student/learning-path.php"><i class="bi bi-map me-1"></i> Roadmaps</a></li>
                            <li class="nav-item dropdown">
                                <a class="nav-link lu-nav-link dropdown-toggle" href="#" id="labsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-flask me-1"></i> Labs</a>
                                <ul class="dropdown-menu user-dropdown-menu border-0 shadow mt-2" aria-labelledby="labsDropdown">
                                    <li><a class="dropdown-item lu-dropdown-item ajax-link" href="<?= $ROOT_PATH ?? '' ?>student/coding-lab.php"><i class="bi bi-terminal me-2"></i> Coding Lab</a></li>
                                    <li><a class="dropdown-item lu-dropdown-item ajax-link" href="<?= $ROOT_PATH ?? '' ?>student/virtual-lab.php"><i class="bi bi-cpu me-2"></i> Virtual Lab</a></li>
                                </ul>
                            </li>
                        </ul>
                    <?php endif; ?>

                    <!-- เมนูผู้ใช้ -->
                    <div class="user-action-panel d-flex align-items-center gap-2 gap-lg-3 ms-auto mt-3 mt-lg-0">

                        <!-- 🌙/☀️ ปุ่มสลับโหมด Dark/Light Theme -->
                        <div class="btn-group me-1">
                            <button type="button" id="theme-toggle-btn" class="theme-toggle btn border-0 p-0 shadow-none" style="border-radius: 50%; width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; background: var(--nav-btn-theme-bg);" title="สลับโหมดมืด/สว่าง">
                                <i class="bi bi-sun-fill text-warning d-none" id="theme-icon-light" style="font-size: 1.1rem;"></i>
                                <i class="bi bi-moon-stars-fill text-light" id="theme-icon-dark" style="font-size: 1.1rem;"></i>
                            </button>
                        </div>

                        <?php if ($isLoggedIn): ?>
                            <a href="<?= $ROOT_PATH ?? '' ?>admin/notifications.php" class="nav-icon-link fs-5 position-relative ajax-link d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-bell"></i></a>

                            <div class="d-none d-sm-flex align-items-center">
                                <a href="<?= $ROOT_PATH ?? '' ?>student/skill-map.php" class="text-decoration-none ajax-link">
                                    <span class="badge rounded-pill lu-badge-xp px-3 py-2 d-flex align-items-center gap-1 fs-6 border">
                                        <i class="bi bi-star-fill text-warning"></i> Lvl <?= htmlspecialchars($navUser['level']) ?>
                                    </span>
                                </a>
                            </div>

                            <!-- ปุ่ม AI Chat -->
                            <div class="btn-group lu-ai-btn-group">
                                <a href="<?= $ROOT_PATH ?? '' ?>ai/ai-chat.php" class="btn btn-primary rounded-start-pill px-3 ajax-link text-white border-0">
                                    <i class="bi bi-robot me-1"></i> <span class="d-none d-md-inline">AI Chat</span>
                                </a>
                                <a href="<?= $ROOT_PATH ?? '' ?>ai/ai-coach.php" class="btn btn-primary rounded-end-pill px-2 border-start-0 ajax-link text-white border-0" style="border-left: 1px solid rgba(255,255,255,0.2) !important;">
                                    <i class="bi bi-lightbulb"></i>
                                </a>
                            </div>

                            <div class="dropdown position-relative d-flex align-items-center">
                                <a href="#" class="dropdown-toggle border-0 text-decoration-none d-flex align-items-center" data-bs-toggle="dropdown">
                                    <img src="<?= htmlspecialchars($navUser['avatar']) ?>" class="rounded-circle border border-2 border-primary" width="38" height="38" style="object-fit: cover;">
                                </a>
                                <ul class="dropdown-menu user-dropdown-menu dropdown-menu-end shadow-lg py-2">
                                    <li>
                                        <div class="profile-name-wrap">
                                            <?= htmlspecialchars($navUser['username']) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider border-secondary opacity-25">
                                    </li>
                                    <li><a class="dropdown-item ajax-link" href="<?= $ROOT_PATH ?? '' ?>student/dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                                    <li><a class="dropdown-item ajax-link" href="<?= $ROOT_PATH ?? '' ?>student/profile.php"><i class="bi bi-person me-2"></i> Profile</a></li>
                                    <li><a class="dropdown-item ajax-link" href="<?= $ROOT_PATH ?? '' ?>student/settings.php"><i class="bi bi-gear me-2"></i> Settings</a></li>
                                    <li>
                                        <hr class="dropdown-divider border-secondary opacity-25">
                                    </li>
                                    <li><a class="dropdown-item text-danger" href="<?= $ROOT_PATH ?? '' ?>auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Log Out</a></li>
                                </ul>
                            </div>
                            
                        <?php else: ?>
                            <a href="<?= $ROOT_PATH ?? '' ?>auth/login.php" class="btn btn-outline-primary rounded-pill px-4 fw-medium ajax-link shadow-sm fs-sm">Log In</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>

        <!-- 🛠️ JavaScript ควบคุมการกดสลับโหมด Dark/Light -->
        <script>
            $(document).ready(function() {
                function applyThemeUI(theme) {
                    $('html').attr('data-theme', theme);
                    if (theme === 'dark') {
                        $('#theme-icon-dark').removeClass('d-none');
                        $('#theme-icon-light').addClass('d-none');
                    } else {
                        $('#theme-icon-light').removeClass('d-none');
                        $('#theme-icon-dark').addClass('d-none');
                    }
                }

                const currentTheme = localStorage.getItem('theme') || 'dark';
                applyThemeUI(currentTheme);

                $('#theme-toggle-btn').on('click', function(e) {
                    e.preventDefault();
                    const activeTheme = $('html').attr('data-theme');
                    const nextTheme = activeTheme === 'dark' ? 'light' : 'dark';

                    localStorage.setItem('theme', nextTheme);
                    applyThemeUI(nextTheme);
                });
            });
        </script>

        <!-- 📌 แก้ไขจุดนี้: เปลี่ยนจาก <div id="main-content-area"> เป็น <main id="content-area"> -->
        <main id="content-area">
        <?php
    endif;
        ?>