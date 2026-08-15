<?php
$ROOT_PATH = '../';
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}
include '../includes/header.php'; 

$user_role = 'Senior Instructor';
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

    /* 🌙 โหมดมืด (Default Theme Variables) */
    :root, html[data-theme="dark"] {
        --bg-dark-blend: #070a13;
        --card-bg-premium: rgba(16, 24, 48, 0.65);
        --card-border-premium: rgba(255, 255, 255, 0.06);
        --text-primary-lite: #f8fafc;
        --text-secondary-muted: #94a3b8;
        --color-neon-cyan: #06b6d4;

        --btn-tool-bg: rgba(15, 23, 42, 0.5);
        --btn-tool-border: rgba(6, 182, 212, 0.15);
        --btn-tool-text: #e2e8f0;
        --btn-tool-hover-bg: rgba(6, 182, 212, 0.08);

        --ai-card-bg: linear-gradient(135deg, rgba(6, 182, 212, 0.15) 0%, rgba(59, 130, 246, 0.05) 100%);
        --ai-card-border: rgba(6, 182, 212, 0.3);
    }

    /* ☀️ โหมดสว่าง (Light Theme Variables) */
    html[data-theme="light"] {
        --bg-dark-blend: #f8fafc;
        --card-bg-premium: #ffffff;
        --card-border-premium: rgba(226, 232, 240, 0.9);
        --text-primary-lite: #0f172a;
        --text-secondary-muted: #64748b;
        --color-neon-cyan: #0284c7;

        --btn-tool-bg: #ffffff;
        --btn-tool-border: #cbd5e1;
        --btn-tool-text: #1e293b;
        --btn-tool-hover-bg: #f1f5f9;

        --ai-card-bg: linear-gradient(135deg, rgba(6, 182, 212, 0.08) 0%, rgba(59, 130, 246, 0.03) 100%);
        --ai-card-border: rgba(6, 182, 212, 0.25);
    }

    body { 
        background-color: var(--bg-dark-blend) !important; 
        color: var(--text-primary-lite) !important;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .premium-instructor-wrapper {
        background: radial-gradient(circle at 80% 10%, rgba(6, 182, 212, 0.1) 0%, transparent 40%),
                    radial-gradient(circle at 20% 80%, rgba(168, 85, 247, 0.08) 0%, transparent 50%),
                    var(--bg-dark-blend);
        min-height: auto; 
        color: var(--text-primary-lite);
        font-family: 'Plus Jakarta Sans', 'Kanit', sans-serif;
        padding: 40px 20px 20px 20px;
        transition: background 0.3s ease, color 0.3s ease;
    }

    .inst-text-main {
        color: var(--text-primary-lite) !important;
        transition: color 0.3s ease;
    }

    .inst-text-muted {
        color: var(--text-secondary-muted) !important;
        transition: color 0.3s ease;
    }

    .welcome-outer-wrapper {
        display: flex;
        flex-direction: column;
        height: 100%;
        width: 100%;
    }
    .welcome-outer-wrapper > div,
    .welcome-outer-wrapper .card,
    .welcome-outer-wrapper .lv-custom-header-panel {
        height: 100% !important;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        margin-bottom: 0 !important;
    }

    .ai-insight-card {
        background: var(--ai-card-bg);
        border: 1px solid var(--ai-card-border); 
        border-radius: 16px; 
        padding: 25px; 
        height: 100%;
        transition: all 0.3s ease;
    }

    .studio-tools-grid-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 25px;
    }

    @media (max-width: 1200px) { .studio-tools-grid-container { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 992px) { .studio-tools-grid-container { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 576px) { .studio-tools-grid-container { grid-template-columns: 1fr; } }

    .btn-studio-tool {
        background: var(--btn-tool-bg);
        border: 1px solid var(--btn-tool-border);
        color: var(--btn-tool-text) !important;
        font-weight: 500;
        font-size: 0.9rem;
        padding: 10px 16px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        width: 100%;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }

    .btn-studio-tool:hover {
        background: var(--btn-tool-hover-bg);
        border-color: rgba(6, 182, 212, 0.4);
        transform: translateY(-2px);
    }

    .btn-studio-tool.active {
        background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
        border-color: #06b6d4;
        color: #ffffff !important;
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.35);
        font-weight: 600;
    }

    .btn-studio-tool.active .studio-mini-icon {
        background: rgba(255, 255, 255, 0.2) !important;
        color: #fff !important;
    }
    
    .studio-mini-icon { 
        width: 28px; height: 28px; border-radius: 8px; 
        display: flex; align-items: center; justify-content: center; font-size: 0.95rem;
        transition: all 0.2s ease;
    }
    .icon-green { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
    .icon-cyan { background: rgba(6, 182, 212, 0.15); color: #06b6d4; }
    .icon-purple { background: rgba(168, 85, 247, 0.15); color: #a855f7; }
    .icon-orange { background: rgba(249, 115, 22, 0.15); color: #f97316; }
    .icon-pink { background: rgba(236, 72, 153, 0.15); color: #ec4899; }
    .icon-blue { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .icon-yellow { background: rgba(234, 179, 8, 0.15); color: #eab308; }

    .stat-card-custom {
        background: var(--card-bg-premium);
        border: 1px solid var(--card-border-premium);
        border-radius: 20px;
        transition: all 0.3s ease;
    }

    #studio-content-placeholder {
        min-height: auto;
        margin-top: 10px;
        margin-bottom: 20px;
    }


    .ajax-loader {
        display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 160px; color: var(--text-secondary-muted);
    }

    /* 🎨 New Component Styles for Command Center */
    .course-draft-card {
        background: var(--card-bg-premium);
        border: 1px solid var(--card-border-premium);
        border-radius: 20px;
        padding: 25px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .course-draft-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        border-color: rgba(6, 182, 212, 0.3);
    }
    .card-subtitle-text {
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--text-secondary-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .card-title-text {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-primary-lite);
        margin-bottom: 0;
    }
    .status-badge {
        background-color: rgba(234, 179, 8, 0.15);
        color: #eab308;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
    }
    .progress-section {
        margin-top: 20px;
        margin-bottom: 20px;
    }
    .progress-text, .progress-percent {
        font-size: 0.8rem;
        color: var(--text-secondary-muted);
        font-weight: 500;
    }
    .progress-percent { color: var(--text-primary-lite); }
    .progress .progress-bar {
        background: linear-gradient(90deg, #06b6d4, #3b82f6);
        border-radius: 8px;
    }
    .btn-continue-building {
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.15) 0%, rgba(59, 130, 246, 0.1) 100%);
        border: 1px solid rgba(6, 182, 212, 0.3);
        color: var(--text-primary-lite) !important;
        border-radius: 12px;
        padding: 10px 15px;
        text-align: center;
        text-decoration: none;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.25s ease;
    }
    .btn-continue-building:hover {
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.25) 0%, rgba(59, 130, 246, 0.2) 100%);
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.2);
        color: #fff !important;
    }

    .create-new-course-card {
        background: linear-gradient(135deg, #0f172a 0%, var(--bg-dark-blend) 100%);
        border: 1px solid var(--card-border-premium);
        border-radius: 20px;
        padding: 25px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .create-new-course-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 0 25px rgba(6, 182, 212, 0.2);
        border-color: rgba(6, 182, 212, 0.4);
    }
    .create-new-course-card .studio-mini-icon {
        width: 48px; height: 48px; border-radius: 12px;
        font-size: 1.5rem;
        margin-bottom: 15px;
        box-shadow: 0 0 15px rgba(34, 197, 94, 0.3);
    }
    .go-corner {
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        width: 32px;
        height: 32px;
        overflow: hidden;
        top: 0;
        right: 0;
        background: linear-gradient(135deg, #06b6d4, #3b82f6);
        border-radius: 0 19px 0 10px;
    }
    .go-arrow {
        margin-top: -4px;
        margin-right: -4px;
        color: white;
        font-family: courier, sans-serif;
    }
    .create-new-course-card:hover .go-corner {
        background: linear-gradient(135deg, #0891b2, #2563eb);
    }

    .studio-tools-grid-container.secondary-tools {
        grid-template-columns: repeat(2, 1fr);
    }
    @media (max-width: 576px) { .studio-tools-grid-container.secondary-tools { grid-template-columns: 1fr; } }

</style>

<div class="premium-instructor-wrapper">
    <div class="container-fluid px-2 px-md-4 px-lg-5">
        
        <!-- 🛠️ ส่วน Profile Hero Header -->
        <div class="row g-4 align-items-stretch" style="margin-bottom: 35px;">
            <!-- ฝั่งซ้าย: กล่องข้อมูลต้อนรับผู้ใช้งาน -->
            <div class="col-12 col-xl-7 d-flex">
                <div class="welcome-outer-wrapper">
                    <?php
                    $ROOT_PATH = '../';
                    include '../includes/welcome-header.php'; 
                    ?>
                </div>
            </div>
            
            <!-- ฝั่งขวา: AI Instructor Analytics -->
            <div class="col-12 col-xl-5 d-flex">
                <div class="ai-insight-card w-100 d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="fw-bold text-info mb-2"><i class="bi bi-cpu-fill me-1"></i> AI Instructor Analytics</h5>
                        <p class="mb-3 inst-text-muted">ยินดีต้อนรับสู่แดชบอร์ดจัดการผู้สอน เลือกเครื่องมือที่ต้องการปรับแต่งจากแถบปุ่มกดด้านล่างนี้ได้ทันทีครับ</p>
                    </div>
                    <!-- 💡 ปุ่มทดสอบเรียกใช้ AI Copilot SweetAlert -->
                    <div>
                        <button class="btn btn-sm btn-outline-info rounded-pill px-3" onclick="InstructorAlerts.aiProcess('วิเคราะห์ประสิทธิภาพผู้เรียนแบบเรียลไทม์')">
                            <i class="bi bi-magic me-1"></i> ให้ AI ช่วยวิเคราะห์
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🎯 New: Command Center Header -->
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold inst-text-main mb-0"><i class="bi bi-rocket-takeoff-fill text-info me-2"></i> Instructor Command Center</h4>
            <div class="inst-text-muted d-none d-md-block">
                <i class="bi bi-grid-1x2-fill me-1"></i> Your learning creation hub
            </div>
        </div>
        
        <!-- 🚀 New: Primary Actions (Continue & Create) -->
        <div class="row g-4 mb-5">
            <!-- Left: Continue where you left off -->
            <div class="col-12 col-lg-7">
                <div class="course-draft-card h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="card-subtitle-text">คอร์สที่กำลังทำอยู่</div>
                            <h5 class="card-title-text">The Ultimate Guide to Modern PHP</h5>
                        </div>
                        <div class="status-badge">ร่าง</div>
                    </div>
                    <div class="progress-section">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="progress-text">ความคืบหน้า</span>
                            <span class="progress-percent">40%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" style="width: 40%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <a href="#" class="btn-continue-building ajax-menu-btn" data-page="lesson-editor.php?course_id=123">
                        <span><i class="bi bi-pencil-square me-2"></i> ทำต่อ: แก้ไขบทเรียน</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <!-- Right: Create New Course -->
            <div class="col-12 col-lg-5">
                <a class="create-new-course-card h-100 ajax-menu-btn" data-page="create-course.php">
                    <div class="icon-pulse-wrapper">
                         <div class="studio-mini-icon icon-green"><i class="bi bi-plus-lg"></i></div>
                    </div>
                    <h5 class="fw-bold card-title-text">สร้างคอร์สใหม่</h5>
                    <p class="inst-text-muted small mb-0">เริ่มต้นสร้างเส้นทางการเรียนรู้, บทเรียน, แบบทดสอบ และ Labs สำหรับผู้เรียนของคุณ</p>
                    <div class="go-corner" href="#">
                        <div class="go-arrow">→</div>
                    </div>
                </a>
            </div>
        </div>

        <!-- 🛠️ New: Tool Groups (Build & Insights) -->
        <div class="row g-4">
            <!-- Build Tools -->
            <div class="col-12 col-lg-7">
                <div class="mb-3">
                    <h5 class="fw-bold inst-text-main mb-0" style="font-size: 1.1rem;">เครื่องมือสร้างและจัดการ (Build)</h5>
                </div>
                <div class="studio-tools-grid-container secondary-tools">
                    <a class="btn-studio-tool ajax-menu-btn" data-page="course-builder.php">
                        <div class="studio-mini-icon icon-cyan"><i class="bi bi-diagram-3-fill"></i></div>
                        <span>จัดการโครงสร้างคอร์ส</span>
                    </a>
                    <a class="btn-studio-tool ajax-menu-btn" data-page="lesson-editor.php">
                        <div class="studio-mini-icon icon-purple"><i class="bi bi-pencil-square"></i></div>
                        <span>แก้ไขบทเรียน</span>
                    </a>
                    <a class="btn-studio-tool ajax-menu-btn" data-page="quiz-builder.php">
                        <div class="studio-mini-icon icon-orange"><i class="bi bi-patch-question-fill"></i></div>
                        <span>สร้างแบบทดสอบ</span>
                    </a>
                    <a class="btn-studio-tool ajax-menu-btn" data-page="lab-builder.php">
                        <div class="studio-mini-icon icon-pink"><i class="bi bi-terminal-fill"></i></div>
                        <span>สร้าง Coding / Virtual Lab</span>
                    </a>
                </div>
            </div>

            <!-- Insights & Analytics -->
            <div class="col-12 col-lg-5">
                <div class="mb-3">
                    <h5 class="fw-bold inst-text-main mb-0" style="font-size: 1.1rem;">สถิติและข้อมูลเชิงลึก (Insights)</h5>
                </div>
                <div class="studio-tools-grid-container secondary-tools">
                    <a class="btn-studio-tool ajax-menu-btn" data-page="course-analytics.php">
                        <div class="studio-mini-icon icon-blue"><i class="bi bi-bar-chart-line-fill"></i></div>
                        <span>สถิติวิเคราะห์</span>
                    </a>
                    <a class="btn-studio-tool ajax-menu-btn" data-page="student-management.php">
                        <div class="studio-mini-icon icon-yellow"><i class="bi bi-people-fill"></i></div>
                        <span>จัดการผู้เรียน</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- กล่องสำหรับเปลี่ยนเนื้อหาผ่าน AJAX -->
        <div id="studio-content-placeholder" class="mt-4">
            <!-- The default dashboard content can be loaded here if needed, or left empty -->
        </div>

    </div>
</div>

<!-- 🔔 ลบไฟล์ Script ที่ซ้ำซ้อนออกแล้ว (jQuery, Bootstrap, SweetAlert2) เนื่องจากถูกโหลดใน header.php แล้ว -->

<script>
$(document).ready(function() {
    // 🛠️ ปรับแก้ Path AJAX สำหรับยิงไปยังโฟลเดอร์ services/ ให้ถอยกลับ 1 ชั้นอัตโนมัติ (แก้ปัญหา 404 Not Found)
    $.ajaxPrefilter(function(options) {
        if (options.url && (options.url.startsWith('services/') || options.url.startsWith('api/'))) {
            options.url = '../' + options.url;
        }
    });

    const defaultDashboardContent = $('#studio-content-placeholder').html();

    // 🔄 ฟังก์ชันสำหรับโหลดเนื้อหา AJAX
    function loadStudioPage(targetPage) {
        // อัปเดตสถานะปุ่ม Active บน เมนู
        $('.ajax-menu-btn').removeClass('active');
        // Handle query strings in targetPage for the 'active' class
        const pageOnly = targetPage.split('?')[0];
        $(`.ajax-menu-btn[data-page^="${pageOnly}"]`).addClass('active');

        if(targetPage === 'DEFAULT_DASHBOARD') {
            $('#studio-content-placeholder').hide().html(defaultDashboardContent).fadeIn(200);
            return;
        }

        $('#studio-content-placeholder').html(`
            <div class="ajax-loader">
                <div class="spinner-border text-info mb-3" style="width: 2rem; height: 2rem;" role="status"></div>
                <div class="fw-medium inst-text-muted" style="font-size:0.9rem;">กำลังเรียกข้อมูลระบบห้องเครื่อง...</div>
            </div>
        `);

        $.ajax({
            url: targetPage,
            type: 'GET',
            dataType: 'html',
            success: function(response) {
                $('#studio-content-placeholder').hide().html(response).fadeIn(250);
            },
            error: function(xhr, status, error) {
                $('#studio-content-placeholder').html(`
                    <div class="alert alert-danger border-danger bg-dark text-center py-4 rounded-4 my-2">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-3"></i>
                        <h6 class="fw-bold text-white mt-2">ไม่สามารถดึงข้อมูลไฟล์เครื่องมือได้</h6>
                        <p class="text-secondary-muted small mb-0">กรุณาตรวจสอบว่ามีไฟล์ชื่อ <strong>${targetPage}</strong> ในเซิร์ฟเวอร์แล้วหรือไม่</p>
                    </div>
                `);
            }
        });
    }

    // ⚡ ใช้ Event Delegation ดักจับการคลิกปุ่ม AJAX ทั้งหมด
    $(document).on('click', '.ajax-menu-btn', function(e) {
        e.preventDefault();
        var targetPage = $(this).data('page');
        if (targetPage) {
            loadStudioPage(targetPage);
            // Optional: Update URL hash for better history, but can be complex.
            // window.location.hash = targetPage;
        }
    });

    // Optional: Load content based on URL hash on page load
    // if (window.location.hash) {
    //     var initialPage = window.location.hash.substring(1);
    //     loadStudioPage(initialPage);
    // }
});
</script>

<?php
$ROOT_PATH = '../';
include '../includes/footer.php'; 
?>