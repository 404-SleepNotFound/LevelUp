<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// สมมติระบบตรวจสอบสิทธิ์ Admin (Security Check)
if (!isset($_SESSION['user_login']['user_role']) || $_SESSION['user_login']['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enterprise Admin Command Center</title>
    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts & Chart.js -->
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300;400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/levelup-sweetalert.js?v=1.0"></script>

    <style>
        :root {
            --dark-core: #090d16;
            --dark-surface: #0f172a;
            --dark-card: rgba(30, 41, 59, 0.7);
            --border-glow: rgba(255, 255, 255, 0.08);
            --cyber-cyan: #06b6d4;
            --cyber-purple: #a855f7;
            --cyber-green: #22c55e;
            --cyber-warning: #f59e0b;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
        }

        body {
            font-family: 'Inter', 'Chakra Petch', sans-serif;
            background-color: var(--dark-core);
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* Layout Architecture */
        #admin-wrapper {
            display: flex;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
        }

        /* Dynamic Sidebar Style */
        #sidebar-container {
            width: 280px;
            background-color: var(--dark-surface);
            border-right: 1px solid var(--border-glow);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            transition: all 0.3s ease;
            z-index: 100;
        }

        .sidebar-brand {
            padding: 20px;
            border-bottom: 1px solid var(--border-glow);
            background: linear-gradient(90deg, var(--cyber-cyan), var(--cyber-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .sidebar-menu-wrapper {
            flex-grow: 1;
            overflow-y: auto;
            padding: 15px 10px;
        }

        .menu-category {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-secondary);
            margin: 15px 0 5px 10px;
            font-weight: 700;
        }

        .nav-menu-item {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 2px;
            font-size: 0.88rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .nav-menu-item i {
            margin-right: 12px;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .nav-menu-item:hover {
            color: var(--cyber-cyan);
            background: rgba(6, 182, 212, 0.05);
        }

        .nav-menu-item.active {
            color: var(--text-primary);
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.2), rgba(168, 85, 247, 0.1));
            border-left: 4px solid var(--cyber-cyan);
            font-weight: 600;
        }

        /* Content Context Platform */
        #main-content-viewport {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: radial-gradient(circle at 50% 0%, rgba(30, 41, 59, 0.3) 0%, var(--dark-core) 70%);
        }

        .main-navbar {
            height: 70px;
            background-color: var(--dark-surface);
            border-bottom: 1px solid var(--border-glow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            flex-shrink: 0;
        }

        .dynamic-stage-area {
            flex-grow: 1;
            overflow-y: auto;
            padding: 30px;
        }

        /* Universal Reusable Custom Components */
        .glass-card {
            background: var(--dark-card);
            border: 1px solid var(--border-glow);
            border-radius: 16px;
            backdrop-filter: blur(12px);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .glass-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }

        .stat-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--dark-core);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--cyber-cyan);
        }

        /* Responsive Mobile Drawer Toggle */
        @media (max-width: 992px) {
            #sidebar-container {
                margin-left: -280px;
                position: absolute;
                height: 100vh;
            }

            #sidebar-container.open {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

    <div id="admin-wrapper">

        <!-- =================================================================================
         SIDEBAR: NAVIGATION MATRIX CONTROL
         ================================================================================= -->
        <aside id="sidebar-container">
            <div class="sidebar-brand d-flex align-items-center justify-content-between matrix-glow">
                <span class="fs-4 fw-bold"><i class="bi bi-shield-lock-fill me-2"></i>NEXUS CORE</span>
                <button class="btn btn-sm btn-dark d-lg-none text-white" onclick="toggleSidebar()"><i class="bi bi-x-lg"></i></button>
            </div>

            <div class="sidebar-menu-wrapper">
                <!-- 1. คอนโทรลรวม -->
                <div class="menu-category">Core Ops Center</div>
                <a class="nav-menu-item active" data-target="overview.php"><i class="bi bi-grid-1x2-fill text-info"></i>Dashboard Overview</a>
                <a class="nav-menu-item" data-target="analytics.php"><i class="bi bi-bar-chart-line-fill text-success"></i>Advanced Analytics</a>

                <!-- 2. การจัดการสมาชิก -->
                <div class="menu-category">Human Resource Engine</div>
                <a class="nav-menu-item" data-target="users.php"><i class="bi bi-people-fill"></i>User Management</a>
                <a class="nav-menu-item" data-target="instructors.php"><i class="bi bi-person-workspace text-warning"></i>Instructors Ops</a>
                <a class="nav-menu-item" data-target="roles.php"><i class="bi bi-fingerprint text-danger"></i>Role & Permissions</a>

                <!-- 3. การจัดการการเรียนรู้ -->
                <div class="menu-category">Academic Framework</div>
                <a class="nav-menu-item" data-target="courses.php"><i class="bi bi-journal-bookmark-fill"></i>Course Master</a>
                <a class="nav-menu-item" data-target="course-review.php"><i class="bi bi-clipboard-check text-warning"></i>Content Review <span class="badge bg-danger ms-auto rounded-pill" style="font-size:0.65rem;">4</span></a>
                <a class="nav-menu-item" data-target="lessons.php"><i class="bi bi-play-circle-fill"></i>Lessons System</a>
                <a class="nav-menu-item" data-target="quizzes.php"><i class="bi bi-patch-question-fill"></i>Quiz Management</a>
                <a class="nav-menu-item" data-target="learning-paths.php"><i class="bi bi-signpost-2-fill"></i>Learning Paths</a>
                <a class="nav-menu-item" data-target="skill-maps.php"><i class="bi bi-diagram-3-fill"></i>Skill Tree Mapping</a>
                <a class="nav-menu-item" data-target="categories.php"><i class="bi bi-tags-fill"></i>Taxonomy & Tags</a>
                <a class="nav-menu-item" data-target="certificates.php"><i class="bi bi-award-fill"></i>Certificates Portal</a>

                <!-- 4. ระบบแล็บและนวัตกรรม -->
                <div class="menu-category">Lab Environments & Sandbox</div>
                <a class="nav-menu-item" data-target="coding-labs.php"><i class="bi bi-code-square text-success"></i>Coding Lab Runner</a>
                <a class="nav-menu-item" data-target="virtual-labs.php"><i class="bi bi-terminal-fill text-cyan"></i>Virtual Clouds Lab</a>
                <a class="nav-menu-item" data-target="ai-management.php"><i class="bi bi-cpu-fill text-purple"></i>AI Orchestration</a>

                <!-- 5. โครงสร้างพื้นฐานระบบ -->
                <div class="menu-category">Infrastructure & FinTech</div>
                <a class="nav-menu-item" data-target="payments.php"><i class="bi bi-credit-card-2-front-fill text-success"></i>Financial Ledger</a>
                <a class="nav-menu-item" data-target="notifications.php"><i class="bi bi-bell-fill"></i>Broadcast System</a>
                <a class="nav-menu-item" data-target="file-manager.php"><i class="bi bi-folder-fill"></i>Storage Explorer</a>
                <a class="nav-menu-item" data-target="logs.php"><i class="bi bi-terminal"></i>System Audit Logs</a>
                <a class="nav-menu-item" data-target="backup.php"><i class="bi bi-cloud-arrow-up-fill"></i>Disaster Backup</a>
                <a class="nav-menu-item" data-target="security.php"><i class="bi bi-shield-fill-check text-danger"></i>Security Firewalls</a>
                <a class="nav-menu-item" data-target="settings.php"><i class="bi bi-gear-fill"></i>Global Configuration</a>
            </div>

            <div class="p-3 border-top border-secondary border-opacity-20 bg-dark bg-opacity-30 text-center small text-secondary">
                v4.2.0-Build2026 • Security Level 5
            </div>
        </aside>

        <!-- =================================================================================
         VIEWPORT CONTAINER: NAVIGATION HEADER + DYNAMIC MAIN CONTAINER
         ================================================================================= -->
        <main id="main-content-viewport">

            <!-- Header Top Navigation Bar -->
            <header class="main-navbar">
                <div class="d-flex align-items-center gap-3 matrix-navbar-left">
                    <button class="btn btn-outline-secondary d-lg-none btn-sm text-white" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
                    <h5 class="mb-0 fw-bold text-white tracking-wide d-none d-sm-block" id="stage-title-indicator">Dashboard Overview</h5>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="position-relative cursor-pointer px-2" onclick="loadContentModule('notifications.php')">
                        <i class="bi bi-bell fs-5 text-secondary"></i>
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"><span class="visually-hidden">Alerts</span></span>
                    </div>
                    <div class="border-start border-secondary border-opacity-30 h-50 mx-2"></div>
                    <div class="dropdown">
                        <div class="d-flex align-items-center gap-2 cursor-pointer dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="rounded-circle bg-purple text-center fw-bold text-white shadow-sm" style="width: 36px; height: 36px; line-height: 36px; font-size: 0.85rem;">AD</div>
                            <span class="small text-white d-none d-md-inline-block fw-medium">Super Admin Console</span>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark border border-secondary border-opacity-20 rounded-3 mt-2 shadow">
                            <li><a class="dropdown-menu-item dropdown-item py-2 small" href="#" onclick="loadContentModule('settings.php')"><i class="bi bi-gear me-2"></i>ตั้งค่าระบบ</a></li>
                            <li><a class="dropdown-menu-item dropdown-item py-2 small" href="#" onclick="loadContentModule('security.php')"><i class="bi bi-shield-check me-2"></i>ความปลอดภัย</a></li>
                            <li>
                                <hr class="dropdown-divider border-secondary border-opacity-20">
                            </li>
                            <li><a class="dropdown-item py-2 small text-danger fw-bold" href="../logout.php"><i class="bi bi-power me-2"></i>ออกจากระบบ Control Panel</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Dynamic Ajax Inject Stage Viewport -->
            <div class="dynamic-stage-area" id="ajax-stage-engine">

                <!-- =================================================================================
                 1. DEFAULT ROOT MODULE: DASHBOARD OVERVIEW (โค้ดจำลองชุดดั้งเดิมเมื่อเปิดหน้าแรก)
                 ================================================================================= -->
                <div class="row g-3 mb-4 animate-fade-in">
                    <!-- Stat Card 1 -->
                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                        <div class="glass-card p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-secondary small fw-medium">ผู้ใช้งานทั้งหมด / ออนไลน์</div>
                                <h3 class="fw-bold text-white mt-1 mb-0">1,249,850 <span class="fs-6 text-success fw-normal">(<i class="bi bi-circle-fill text-success small-pulse"></i> 4,821)</span></h3>
                            </div>
                            <div class="stat-icon-wrapper bg-info bg-opacity-10 text-cyan"><i class="bi bi-people-fill"></i></div>
                        </div>
                    </div>
                    <!-- Stat Card 2 -->
                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                        <div class="glass-card p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-secondary small fw-medium">คอร์ส / ผู้สอนทั้งหมด</div>
                                <h3 class="fw-bold text-white mt-1 mb-0">3,450 <span class="fs-6 text-muted fw-normal">/ 482 คน</span></h3>
                            </div>
                            <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success"><i class="bi bi-journal-code"></i></div>
                        </div>
                    </div>
                    <!-- Stat Card 3 -->
                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                        <div class="glass-card p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-secondary small fw-medium">บทเรียน / ควิซ / แล็บ</div>
                                <h3 class="fw-bold text-white mt-1 mb-0">48,250 <span class="fs-6 text-warning fw-normal">วัตถุ</span></h3>
                            </div>
                            <div class="stat-icon-wrapper bg-warning bg-opacity-10 text-warning"><i class="bi bi-layers-half"></i></div>
                        </div>
                    </div>
                    <!-- Stat Card 4 -->
                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                        <div class="glass-card p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-secondary small fw-medium">AI Requests / Cost (MTD)</div>
                                <h3 class="fw-bold text-purple mt-1 mb-0">8.2M <span class="fs-6 text-secondary fw-normal">($1,420)</span></h3>
                            </div>
                            <div class="stat-icon-wrapper bg-purple bg-opacity-10 text-purple"><i class="bi bi-cpu-fill"></i></div>
                        </div>
                    </div>
                </div>

                <!-- Analytics Visual & Activity Logs Row -->
                <div class="row g-4">
                    <div class="col-12 col-xl-8">
                        <div class="glass-card p-4 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-white mb-0"><i class="bi bi-graph-up text-cyan me-2"></i>สถิติผู้ใช้งานและรายได้รวมระบบ (Platform Operations)</h6>
                                <span class="badge bg-dark border border-secondary border-opacity-30 text-white-50 px-3 font-monospace">LIVE DATA STREAM</span>
                            </div>
                            <div style="height: 320px; position: relative;">
                                <canvas id="overviewLiveChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-4">
                        <div class="glass-card p-4 h-100 d-flex flex-column">
                            <h6 class="fw-bold text-white mb-3"><i class="bi bi-activity text-danger me-2"></i>Real-time Critical Activity Logs</h6>
                            <div class="flex-grow-1 overflow-y-auto pe-1" style="max-height: 310px;">
                                <div class="p-2 mb-2 rounded bg-dark bg-opacity-40 border border-secondary border-opacity-10 small">
                                    <span class="text-danger font-monospace fw-bold">[SECURITY]</span> IP 182.23.45.10 ถูก Rate Limit บล็อกชั่วคราว <span class="text-muted float-end" style="font-size:0.7rem;">10:31</span>
                                </div>
                                <div class="p-2 mb-2 rounded bg-dark bg-opacity-40 border border-secondary border-opacity-10 small">
                                    <span class="text-purple font-monospace fw-bold">[AI ENGINE]</span> Prompt Template 'CodeReviewer v2' อัปเดตเสร็จสิ้น <span class="text-muted float-end" style="font-size:0.7rem;">10:28</span>
                                </div>
                                <div class="p-2 mb-2 rounded bg-dark bg-opacity-40 border border-secondary border-opacity-10 small">
                                    <span class="text-success font-monospace fw-bold">[LEDGER]</span> คอร์ส Premium Next.js ชำระเงินใหม่สำเร็จ +฿3,500 <span class="text-muted float-end" style="font-size:0.7rem;">10:24</span>
                                </div>
                                <div class="p-2 mb-2 rounded bg-dark bg-opacity-40 border border-secondary border-opacity-10 small">
                                    <span class="text-warning font-monospace fw-bold">[CONTENT]</span> ผู้สอนดร.สมชาย ส่งคอร์สใหม่ 'Advanced Docker' ให้รีวิว <span class="text-muted float-end" style="font-size:0.7rem;">10:15</span>
                                </div>
                                <div class="p-2 mb-2 rounded bg-dark bg-opacity-40 border border-secondary border-opacity-10 small">
                                    <span class="text-cyan font-monospace fw-bold">[SYS-BACKUP]</span> Automated Database Cron-Job Backup Successful <span class="text-muted float-end" style="font-size:0.7rem;">10:00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>

    </div>

    <!-- JAVASCRIPT ENGINE DRIVER -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Mobile Layout Controller Toggle Sidebar Drawer
        function toggleSidebar() {
            $('#sidebar-container').toggleClass('open');
        }

        // Dynamic Asynchronous Control Module (AJAX Routing System)
        function loadContentModule(moduleFile) {
            var $stage = $('#ajax-stage-engine');

            // เอฟเฟกต์ Loading ล้ำ ๆ รอระหว่างโหลดไฟล์จริง
            $stage.html(`
            <div class="d-flex flex-column align-items-center justify-content-center w-100 h-100 py-5 text-center">
                <div class="spinner-border text-info mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                <div class="text-secondary tracking-wide font-monospace">CONNECTING MATRIX MODULE: ${moduleFile.toUpperCase()}...</div>
            </div>
        `);

            // ถ้าเลือก Overview Dashboard ให้ใช้โค้ดจำลองชุดเดิม (หรือดึงผ่าน AJAX ได้ภายหลัง)
            if (moduleFile === 'overview.php') {
                location.reload(); // รีเฟรชเพื่อเรียกค่าเซ็ตติ้งหลักเบื้องต้น
                return;
            }

            // ส่ง AJAX Request ไปยังเซิร์ฟเวอร์เพื่อดึงหน้าไฟล์นั้น ๆ (เช่น users.php, courses.php)
            $.ajax({
                url: moduleFile,
                method: 'GET',
                dataType: 'html',
                success: function(responseHTML) {
                    $stage.hide().html(responseHTML).fadeIn(300);
                },
                error: function(xhr, status, error) {
                    // หากยังไม่ได้สร้างไฟล์แยกขึ้นมาจริงในระบบ จะแสดงกล่อง Sandbox Playground ให้คุณเทสได้ทันที
                    generateVirtualControlSandbox(moduleFile);
                }
            });
        }

        // กราฟจำลองความจุระบบ Chart.js ในหน้าหลัก
        $(document).ready(function() {
            var ctx = document.getElementById('overviewLiveChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        datasets: [{
                                label: 'Active Learners (DAU)',
                                data: [65000, 78000, 72000, 89000, 95000, 110000, 124000],
                                borderColor: '#06b6d4',
                                backgroundColor: 'rgba(6, 182, 212, 0.05)',
                                fill: true,
                                tension: 0.3
                            },
                            {
                                label: 'Platform Revenue (x100 ฿)',
                                data: [320, 450, 410, 600, 580, 890, 1050],
                                borderColor: '#22c55e',
                                backgroundColor: 'transparent',
                                fill: false,
                                tension: 0.1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    color: '#94a3b8'
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: 'rgba(255,255,255,0.05)'
                                },
                                ticks: {
                                    color: '#94a3b8'
                                }
                            },
                            y: {
                                grid: {
                                    color: 'rgba(255,255,255,0.05)'
                                },
                                ticks: {
                                    color: '#94a3b8'
                                }
                            }
                        }
                    }
                });
            }

            // เชื่อมโยง Event Listener สลับเมนูด้านซ้าย
            $('.nav-menu-item').on('click', function(e) {
                e.preventDefault();
                $('.nav-menu-item').removeClass('active');
                $(this).addClass('active');

                var targetModule = $(this).attr('data-target');
                var menuTitle = $(this).text().trim();

                $('#stage-title-indicator').text(menuTitle);
                loadContentModule(targetModule);

                // ปิด Sidebar อัตโนมัติบนหน้าจอมือถือหลังจากคลิก
                if ($(window).width() <= 992) {
                    $('#sidebar-container').removeClass('open');
                }
            });
        });

        // =================================================================================
        // BOILERPLATE VIRTUAL GENERATOR (สร้างหน้า Sandbox ให้กรณีไฟล์ย่อยยังไม่ถูกสร้างจริง)
        // =================================================================================
        function generateVirtualControlSandbox(filename) {
            var title = $('.nav-menu-item.active').text();
            var icon = $('.nav-menu-item.active i').attr('class');
            var mockContent = "";

            // ปรับเปลี่ยนคอนเทนต์ภายในตามชื่อไฟล์เพื่อความสมจริงขั้นสูงสุด
            if (filename === 'users.php') {
                mockContent = `
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div class="d-flex gap-2"><input type="text" class="form-control bg-dark text-white border-secondary" placeholder="ค้นหา ชื่อ, อีเมล, ID..."><button class="btn btn-cyan text-white">ค้นหา</button></div>
                <button class="btn btn-success text-white" onclick="alert('สร้างฟอร์มเพิ่มผู้ใช้ใหม่')">+ เพิ่มผู้ใช้งานระบบ</button>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover border-secondary small">
                    <thead><tr><th>UID</th><th>ชื่อผู้ใช้</th><th>อีเมล</th><th>Role</th><th>สถานะบัญชี</th><th>การจัดการเครื่องมือ</th></tr></thead>
                    <tbody>
                        <tr><td>#USR-9021</td><td>สมชาย สายโค้ด</td><td>somchai@nextjs.in.th</td><td><span class="badge bg-purple">Student</span></td><td><span class="badge bg-success">Active</span></td><td><button class="btn btn-xs btn-outline-info text-white py-0 px-1 me-1">แก้ไข</button><button class="btn btn-xs btn-outline-warning text-white py-0 px-1 me-1">เปลี่ยนสิทธิ์</button><button class="btn btn-xs btn-outline-danger text-white py-0 px-1">ระงับ</button></td></tr>
                        <tr><td>#USR-4412</td><td>ดร. วิชัย ใจดี</td><td>wichai@university.edu</td><td><span class="badge bg-warning text-dark">Instructor</span></td><td><span class="badge bg-success">Active</span></td><td><button class="btn btn-xs btn-outline-info text-white py-0 px-1 me-1">แก้ไข</button><button class="btn btn-xs btn-outline-warning text-white py-0 px-1 me-1">เปลี่ยนสิทธิ์</button><button class="btn btn-xs btn-outline-danger text-white py-0 px-1">ระงับ</button></td></tr>
                    </tbody>
                </table>
            </div>`;
            } else if (filename === 'ai-management.php') {
                mockContent = `
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 rounded bg-dark border border-secondary mb-3">
                        <label class="small text-white-50">Active Core AI Model</label>
                        <select class="form-select bg-dark text-white border-secondary mt-1"><option>GPT-4o (Default Production)</option><option>Claude 3.5 Sonnet (Coding Expert)</option><option>Gemini 1.5 Pro</option></select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded bg-dark border border-secondary mb-3">
                        <label class="small text-white-50">AI Token Rate Limit per Student / Day</label>
                        <input type="number" class="form-control bg-dark text-white border-secondary mt-1" value="50000">
                    </div>
                </div>
            </div>`;
            } else {
                mockContent = `<p class="text-secondary small">ระบบตรวจไม่พบไฟล์ <code>admin/${filename}</code> จริงบนโฮสต์ของคุณ โค้ดชุด Core Switcher จึงเปิดหน้าต่าง Sandbox Mockup ฟังก์ชันสำหรับพัฒนาระบบนี้ขึ้นมาทดแทน</p>
            <div class="p-4 rounded-4 bg-dark bg-opacity-40 border border-secondary border-opacity-20 text-center py-5">
                <i class="bi bi-code-slash text-cyan fs-1 mb-2 d-block"></i>
                <h6 class="text-white fw-bold">ระบบฟังก์ชันพร้อมเชื่อมต่อคิวงานหลังบ้าน (Ready to Inject Backend Link)</h6>
                <p class="small text-white-50 mb-0">คุณสามารถแยกเขียนตรรกะ PHP-MySQL ในไฟล์ <code>admin/${filename}</code> เพื่ออัปโหลดข้อมูลจริงมาแทนกล่องนี้ได้ทันที!</p>
            </div>`;
            }

            $('#ajax-stage-engine').html(`
            <div class="glass-card p-4 animate-fade-in">
                <div class="d-flex align-items-center justify-content-between border-bottom border-secondary border-opacity-20 pb-3 mb-3">
                    <h5 class="fw-bold text-white mb-0"><i class="${icon} text-cyan me-2"></i>ระบบย่อย: ${title}</h5>
                    <span class="badge bg-secondary px-2 font-monospace" style="font-size:0.7rem;">Target File: admin/${filename}</span>
                </div>
                ${mockContent}
            </div>
        `);
        }
    </script>

</body>

</html>