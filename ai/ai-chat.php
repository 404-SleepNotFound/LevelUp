<?php
$ROOT_PATH = '../';
session_start();
$_SESSION['current_context'] = [
    'course' => 'Python for Backend',
    'lesson' => 'REST API with Flask',
    'status' => 'coding_lab',
    'last_error' => 'ImportError: No module named flask'
];
include '../includes/header.php';
?>

<!-- Include Markdown Parser, Diagram Renderer, SweetAlert2 & Custom LevelUp Alert -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../js/levelup-sweetalert.js"></script>

<!-- Custom Styles for AI Chat Companion -->
<style>
    :root, html[data-theme="dark"] {
        --lv-bg-dark: #0b0f19;
        --lv-card-bg: rgba(17, 24, 39, 0.7);
        --lv-border-glow: rgba(99, 102, 241, 0.2);
        --lv-cyan: #06b6d4;
        --lv-indigo: #6366f1;
        --lv-success: #10b981;
        --lv-warning: #f59e0b;
        --lv-danger: #ef4444;
        --lv-text-main: #f1f5f9;
        --lv-text-muted: #94a3b8;
    }

    /* ☀️ CSS สำหรับหน้าแชตตอนเปลี่ยนเป็นโหมดสว่าง */
    html[data-theme="light"] {
        --lv-bg-dark: #f8fafc;
        --lv-card-bg: rgba(255, 255, 255, 0.85);
        --lv-border-glow: rgba(99, 102, 241, 0.15);
        --lv-text-main: #0f172a;
        --lv-text-muted: #64748b;
    }

    body {
        background-color: var(--lv-bg-dark) !important;
        color: var(--lv-text-main) !important;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .ai-chat-layout {
        background-color: var(--lv-bg-dark);
        color: var(--lv-text-main);
    }

    /* Sidebars Styling */
    .ai-sidebar-left, .ai-sidebar-right {
        background: var(--lv-card-bg) !important;
        border-color: rgba(255, 255, 255, 0.06) !important;
        backdrop-filter: blur(16px);
    }

    .ai-sidebar-left {
        width: 280px;
    }

    /* Chat History Items */
    .chat-history-item {
        display: flex;
        align-items: center;
        padding: 0.7rem 1rem;
        color: var(--lv-text-muted);
        text-decoration: none;
        border-radius: 12px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        margin-bottom: 4px;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .chat-history-item:hover {
        color: #fff;
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.08);
    }

    .chat-history-item.active {
        color: #fff;
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.15) 0%, rgba(99, 102, 241, 0.15) 100%);
        border-color: rgba(6, 182, 212, 0.3);
        font-weight: 600;
    }

    /* ปรับแต่งความสว่างของข้อความแชตสำหรับโหมด Light */
    html[data-theme="light"] .chat-history-item {
        color: #475569;
    }
    html[data-theme="light"] .chat-history-item:hover {
        background: rgba(0, 0, 0, 0.05);
        color: #0f172a;
    }
    html[data-theme="light"] .chat-history-item.active {
        color: #0f172a;
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.15) 0%, rgba(99, 102, 241, 0.15) 100%);
    }
    html[data-theme="light"] .chat-header h5, 
    html[data-theme="light"] .chat-header .text-white {
        color: #0f172a !important;
    }
    html[data-theme="light"] #chatInput {
        color: #0f172a !important;
    }
    html[data-theme="light"] .text-white {
        color: var(--lv-text-main) !important;
    }
    html[data-theme="light"] .text-white-50 {
        color: var(--lv-text-muted) !important;
    }
    html[data-theme="light"] .btn-outline-light {
        color: #0f172a;
        border-color: #cbd5e1;
    }
    html[data-theme="light"] .btn-outline-light:hover {
        background-color: #cbd5e1;
    }
    html[data-theme="light"] .bg-dark {
        background-color: #f1f5f9 !important;
    }
    html[data-theme="light"] .input-group-custom {
        background: rgba(255, 255, 255, 0.7) !important;
    }

    /* Main Chat Header */
    .chat-header {
        background: var(--lv-card-bg) !important;
        border-color: rgba(255, 255, 255, 0.06) !important;
        backdrop-filter: blur(16px);
    }

    /* Message Styling */
    .chat-message .message-content {
        background: var(--lv-card-bg) !important;
        border: 1px solid var(--lv-border-glow) !important;
        color: var(--lv-text-main);
        backdrop-filter: blur(12px);
    }

    .chat-message.ai-message .avatar-sm {
        background: linear-gradient(135deg, var(--lv-cyan) 0%, var(--lv-indigo) 100%) !important;
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.3);
    }

    .chat-message.user-message {
        flex-direction: row-reverse;
    }

    .chat-message.user-message .message-content {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.25) 0%, rgba(6, 182, 212, 0.25) 100%) !important;
        border-color: rgba(6, 182, 212, 0.4) !important;
        border-top-right-radius: 0 !important;
        border-top-left-radius: 1rem !important;
    }

    /* Quick Prompt Buttons */
    .btn-quick-prompt {
        background: rgba(255, 255, 255, 0.04) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: var(--lv-text-main) !important;
        font-size: 0.825rem;
        padding: 0.45rem 0.9rem;
        transition: all 0.2s ease;
    }

    .btn-quick-prompt:hover {
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.2) 0%, rgba(99, 102, 241, 0.2) 100%) !important;
        border-color: var(--lv-cyan) !important;
        color: #fff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(6, 182, 212, 0.2);
    }

    /* Input Area Styling */
    .chat-input-area {
        background: var(--lv-card-bg) !important;
        border-color: rgba(255, 255, 255, 0.06) !important;
        backdrop-filter: blur(16px);
    }

    .input-group-custom {
        background: rgba(11, 15, 25, 0.8) !important;
        border: 1px solid var(--lv-border-glow) !important;
        border-radius: 16px !important;
        transition: all 0.3s ease;
    }

    .input-group-custom:focus-within {
        border-color: var(--lv-cyan) !important;
        box-shadow: 0 0 20px rgba(6, 182, 212, 0.2) !important;
    }

    #chatInput {
        color: #fff !important;
    }

    #chatInput::placeholder {
        color: var(--lv-text-muted);
    }

    .btn-premium-send {
        background: linear-gradient(135deg, var(--lv-cyan) 0%, var(--lv-indigo) 100%) !important;
        color: #fff !important;
        border: none !important;
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.3);
        transition: all 0.25s ease;
    }

    .btn-premium-send:hover {
        transform: scale(1.04);
        box-shadow: 0 0 20px rgba(6, 182, 212, 0.5);
    }

    /* Dashboard Cards Layout */
    .dashboard-card-sm {
        background: rgba(255, 255, 255, 0.025);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        padding: 1.1rem;
        transition: all 0.3s ease;
    }

    html[data-theme="light"] .dashboard-card-sm {
        background: rgba(0, 0, 0, 0.02);
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .resource-item {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        padding: 0.65rem 0.85rem;
        transition: all 0.25s ease;
    }

    .resource-item:hover {
        background: rgba(6, 182, 212, 0.08);
        border-color: rgba(6, 182, 212, 0.3);
        transform: translateX(4px);
    }

    .resource-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.12);
        border-radius: 10px;
    }
    html[data-theme="light"] .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.15);
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: var(--lv-cyan);
    }
</style>

<div class="ai-chat-layout overflow-hidden d-flex" style="height: calc(100vh - 70px);">
    
    <!-- Left Sidebar: Chat History -->
    <aside class="ai-sidebar-left border-end d-none d-lg-flex flex-column h-100 flex-shrink-0">
        <div class="p-3 border-bottom border-color d-flex justify-content-between align-items-center">
            <button class="btn btn-premium-send w-100 rounded-3 d-flex align-items-center justify-content-center gap-2 py-2.5 fw-bold text-nowrap shadow-sm" id="btnNewChat">
                <i class="bi bi-plus-lg"></i> แชตใหม่ (New Quest)
            </button>
        </div>
        <div class="flex-grow-1 overflow-y-auto p-2 custom-scrollbar" id="sidebarChatList">
            <div class="text-white-50 fs-xs fw-bold px-3 py-2 text-uppercase letter-spacing-1 section-today">วันนี้</div>
            <a class="chat-history-item active" data-chat-id="chat-1">
                <i class="bi bi-chat-left-text me-2 text-info"></i><span class="text-truncate">อธิบายการสร้าง API Route</span>
            </a>
            <a class="chat-history-item" data-chat-id="chat-2">
                <i class="bi bi-bug me-2 text-warning"></i><span class="text-truncate">Debug: Flask ImportError</span>
            </a>
            
            <div class="text-white-50 fs-xs fw-bold px-3 py-2 mt-3 text-uppercase letter-spacing-1">เมื่อวาน</div>
            <a class="chat-history-item" data-chat-id="chat-3">
                <i class="bi bi-file-code me-2 text-primary"></i><span class="text-truncate">สรุปเรื่อง Decorator</span>
            </a>
            <a class="chat-history-item" data-chat-id="chat-4">
                <i class="bi bi-diagram-3 me-2 text-success"></i><span class="text-truncate">เปรียบเทียบ Flask vs Django</span>
            </a>
        </div>
    </aside>

    <!-- Center: Main Chat Area -->
    <main class="ai-chat-main flex-grow-1 d-flex flex-column h-100 position-relative min-w-0">
        
        <!-- Header / Context Bar -->
        <header class="px-4 py-3 chat-header border-bottom d-flex justify-content-between align-items-center z-1 shadow-sm">
            <div class="d-flex align-items-center gap-3 min-w-0">
                <button class="btn btn-sm btn-outline-light d-lg-none rounded-circle" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasHistory" aria-controls="offcanvasHistory">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="text-truncate">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2 text-white">
                        <i class="bi bi-robot text-info"></i> AI Tutor Companion 
                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill fs-xs font-monospace fw-normal">v2.5 Gamified</span>
                    </h5>
                    <div class="d-flex align-items-center gap-2 mt-1 text-truncate">
                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill fw-medium fs-xs text-truncate">
                            <i class="bi bi-geo-alt-fill me-1"></i> บริบท: <span id="currentLessonTitle"><?= htmlspecialchars($_SESSION['current_context']['lesson']) ?></span>
                        </span>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill fs-xs d-none d-sm-inline-block">
                            <i class="bi bi-circle-fill me-1 fs-xs"></i> Ready to Assist
                        </span>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 flex-shrink-0">
                <!-- เพิ่มปุ่มสลับโหมด Light/Dark ที่นี่ -->
                <button class="btn btn-sm btn-outline-light rounded-circle" id="btnThemeToggle" title="สลับโหมด Light/Dark" style="width:36px; height:36px;"><i class="bi bi-sun"></i></button>
                <button class="btn btn-sm btn-outline-light rounded-circle" id="btnDownloadChat" title="ดาวน์โหลดประวัติแชต" style="width:36px; height:36px;"><i class="bi bi-download"></i></button>
                <button class="btn btn-sm btn-outline-light rounded-circle d-xxl-none" title="ข้อมูลวิเคราะห์" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAnalytics" style="width:36px; height:36px;"><i class="bi bi-bar-chart"></i></button>
            </div>
        </header>

        <!-- Chat Timeline -->
        <div class="chat-timeline flex-grow-1 overflow-y-auto p-3 p-md-4 custom-scrollbar" id="chatTimeline">
            <!-- Messages render dynamically via JavaScript -->
        </div>

        <!-- Input Area -->
        <div class="chat-input-area p-3 p-md-4 border-top">
            <div class="container-fluid max-w-chat px-0">
                
                <!-- Tools & Mentor Toggle -->
                <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-icon text-white-50 hover-primary rounded-circle" id="btnUploadFile" title="อัปโหลดไฟล์/รูปภาพ"><i class="bi bi-paperclip fs-5"></i></button>
                        <button class="btn btn-sm btn-icon text-white-50 hover-primary rounded-circle" id="btnUploadZip" title="วิเคราะห์โปรเจกต์ (ZIP)"><i class="bi bi-folder-zip fs-5"></i></button>
                    </div>
                    
                    <!-- Mentor Mode Toggle -->
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill fs-xs d-none d-md-inline-block" id="xpBonusBadge">
                            <i class="bi bi-lightning-charge-fill me-1"></i> +15% EXP Bonus
                        </span>
                        <div class="form-check form-switch d-flex align-items-center gap-2 mb-0 ps-0">
                            <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" id="mentorModeSwitch" checked style="cursor: pointer;">
                            <label class="form-check-label fs-sm fw-medium select-none text-white-50" for="mentorModeSwitch" title="เปิดเพื่อรับคำใบ้แทนการเฉลยตรงๆ">
                                Mentor Mode <span class="text-warning">(ให้คำใบ้)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Input Box -->
                <div class="input-group-custom d-flex align-items-end p-2 transition-all">
                    <textarea class="form-control border-0 bg-transparent resize-none fs-6 custom-scrollbar" id="chatInput" rows="1" placeholder="ถามเกี่ยวกับบทเรียน, วางโค้ดที่ Error หรือขอคำใบ้จาก AI..." style="max-height: 180px; min-height: 44px; padding-top: 10px;"></textarea>
                    <button class="btn btn-premium-send rounded-circle ms-2 flex-shrink-0 d-flex align-items-center justify-content-center" id="btnSend" style="width: 44px; height: 44px;">
                        <i class="bi bi-send-fill fs-6"></i>
                    </button>
                </div>
                <div class="text-center mt-2">
                    <span class="fs-xs text-white-50"><i class="bi bi-info-circle me-1"></i> AI Mentor ประมวลผลจากบริบทคอร์สเรียนปัจจุบันของคุณ</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Right Sidebar: Context & Analytics -->
    <aside class="ai-sidebar-right border-start d-none d-xxl-flex flex-column h-100 p-3.5 overflow-y-auto custom-scrollbar flex-shrink-0" style="width: 320px;">
        <h6 class="fw-bold mb-3 text-uppercase letter-spacing-1 text-white-50 fs-xs d-flex align-items-center">
            <i class="bi bi-cpu-fill text-info me-2 fs-6"></i> AI Personal Knowledge
        </h6>
        
        <!-- Brain Skill Map -->
        <div class="dashboard-card-sm mb-3">
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="resource-icon bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                    <i class="bi bi-brain fs-5"></i>
                </div>
                <h6 class="fw-bold mb-0 text-white fs-sm">จุดแข็ง & จุดอ่อน</h6>
            </div>
            <ul class="list-unstyled fs-sm mb-0">
                <li class="mb-2 d-flex align-items-center justify-content-between">
                    <span class="text-white fs-xs"><i class="bi bi-check-circle-fill text-success me-1.5"></i> Python Basic</span>
                    <span class="badge bg-success bg-opacity-25 text-success fs-xs fw-normal">Mastered</span>
                </li>
                <li class="mb-2 d-flex align-items-center justify-content-between">
                    <span class="text-white fs-xs"><i class="bi bi-check-circle-fill text-success me-1.5"></i> OOP Concept</span>
                    <span class="badge bg-success bg-opacity-25 text-success fs-xs fw-normal">Good</span>
                </li>
                <li class="d-flex align-items-center justify-content-between">
                    <span class="text-white-50 fs-xs"><i class="bi bi-exclamation-circle-fill text-warning me-1.5"></i> Decorators & Scope</span>
                    <span class="badge bg-warning bg-opacity-25 text-warning fs-xs fw-normal">Review</span>
                </li>
            </ul>
        </div>

        <!-- Analytics Card -->
        <div class="dashboard-card-sm mb-3">
            <h6 class="fw-bold mb-3 text-white fs-sm d-flex align-items-center">
                <i class="bi bi-graph-up-arrow text-primary me-2"></i> Learning Analytics
            </h6>
            
            <div class="d-flex justify-content-between mb-1 fs-xs">
                <span class="text-white-50">คะแนน Quiz ล่าสุด</span>
                <span class="fw-bold text-success">85%</span>
            </div>
            <div class="progress rounded-pill bg-dark mb-3 border border-secondary border-opacity-25" style="height: 5px;">
                <div class="progress-bar bg-success rounded-pill" style="width: 85%;"></div>
            </div>
            
            <div class="d-flex justify-content-between mb-1 fs-xs">
                <span class="text-white-50">อัตราทำ Coding Lab</span>
                <span class="fw-bold text-warning">60%</span>
            </div>
            <div class="progress rounded-pill bg-dark mb-0 border border-secondary border-opacity-25" style="height: 5px;">
                <div class="progress-bar bg-warning rounded-pill" style="width: 60%;"></div>
            </div>
        </div>

        <!-- Recommended Resources -->
        <div class="dashboard-card-sm">
            <h6 class="fw-bold mb-3 text-white fs-sm d-flex align-items-center">
                <i class="bi bi-journal-bookmark-fill text-info me-2"></i> AI แนะนำ (Resources)
            </h6>
            
            <div class="d-flex flex-column gap-2">
                <a href="#" class="resource-item d-flex align-items-center gap-3 text-decoration-none">
                    <div class="resource-icon bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                        <i class="bi bi-play-circle-fill fs-5"></i>
                    </div>
                    <div class="min-w-0 flex-grow-1">
                        <div class="fw-semibold text-white fs-xs text-truncate">วิดีโอ: Flask Router เบื้องต้น</div>
                        <div class="fs-xs text-white-50">ความยาว 5 นาที</div>
                    </div>
                </a>
                
                <a href="#" class="resource-item d-flex align-items-center gap-3 text-decoration-none">
                    <div class="resource-icon bg-secondary bg-opacity-20 text-white-50 border border-secondary border-opacity-25">
                        <i class="bi bi-file-earmark-text fs-5"></i>
                    </div>
                    <div class="min-w-0 flex-grow-1">
                        <div class="fw-semibold text-white fs-xs text-truncate">Docs: Module & Import</div>
                        <div class="fs-xs text-white-50">อ่าน 3 นาที</div>
                    </div>
                </a>
            </div>
        </div>
    </aside>

</div>

<!-- Mobile Offcanvas: Chat History -->
<div class="offcanvas offcanvas-start bg-dark text-white border-end border-color" tabindex="-1" id="offcanvasHistory" aria-labelledby="offcanvasHistoryLabel">
    <div class="offcanvas-header border-bottom border-color">
        <h5 class="offcanvas-title fw-bold text-white d-flex align-items-center gap-2" id="offcanvasHistoryLabel">
            <i class="bi bi-clock-history text-info"></i> ประวัติการสนทนา
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-2 custom-scrollbar" id="offcanvasChatList">
        <button class="btn btn-premium-send w-100 rounded-3 d-flex align-items-center justify-content-center gap-2 py-2.5 fw-bold mb-3" onclick="startNewChat()">
            <i class="bi bi-plus-lg"></i> แชตใหม่ (New Quest)
        </button>
        <div class="text-white-50 fs-xs fw-bold px-3 py-2 text-uppercase section-today">วันนี้</div>
        <a class="chat-history-item active" data-chat-id="chat-1"><i class="bi bi-chat-left-text me-2 text-info"></i><span class="text-truncate">อธิบายการสร้าง API Route</span></a>
        <a class="chat-history-item" data-chat-id="chat-2"><i class="bi bi-bug me-2 text-warning"></i><span class="text-truncate">Debug: Flask ImportError</span></a>
        
        <div class="text-white-50 fs-xs fw-bold px-3 py-2 mt-3 text-uppercase">เมื่อวาน</div>
        <a class="chat-history-item" data-chat-id="chat-3"><i class="bi bi-file-code me-2 text-primary"></i><span class="text-truncate">สรุปเรื่อง Decorator</span></a>
        <a class="chat-history-item" data-chat-id="chat-4"><i class="bi bi-diagram-3 me-2 text-success"></i><span class="text-truncate">เปรียบเทียบ Flask vs Django</span></a>
    </div>
</div>

<!-- Mobile Offcanvas: Analytics -->
<div class="offcanvas offcanvas-end bg-dark text-white border-start border-color" tabindex="-1" id="offcanvasAnalytics" aria-labelledby="offcanvasAnalyticsLabel">
    <div class="offcanvas-header border-bottom border-color">
        <h5 class="offcanvas-title fw-bold text-white d-flex align-items-center gap-2" id="offcanvasAnalyticsLabel">
            <i class="bi bi-bar-chart text-info"></i> AI Analytics
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3 custom-scrollbar">
        <div class="dashboard-card-sm mb-4">
            <h6 class="fw-bold mb-2 text-white"><i class="bi bi-brain text-info me-2"></i>จุดแข็ง & จุดอ่อน</h6>
            <ul class="list-unstyled fs-sm mb-0">
                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Python Basic (Mastered)</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>OOP Concept (Good)</li>
                <li><i class="bi bi-exclamation-circle-fill text-warning me-2"></i>Decorators (Review)</li>
            </ul>
        </div>
    </div>
</div>

<!-- Interactive Chat & Dynamic History Session Management -->
<script>
    let currentChatId = 'chat-1';

    // Mock Data สำหรับแต่ละเควสต์/บทสนทนา
    const mockChatData = {
        'chat-1': {
            lesson: 'REST API with Flask',
            isNew: false,
            messages: `
                <div class="chat-message ai-message mb-4">
                    <div class="d-flex gap-3">
                        <div class="avatar-sm text-white rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center shadow-sm" style="width:42px; height:42px;">
                            <i class="bi bi-stars fs-5"></i>
                        </div>
                        <div class="message-content rounded-4 rounded-top-start-0 p-3 p-md-4 shadow-sm w-100 max-w-chat">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-info fs-sm"><i class="bi bi-cpu me-1"></i> AI Tutor Companion</span>
                                <span class="fs-xs text-white-50 font-monospace">Just now</span>
                            </div>
                            <p class="mb-2">สวัสดีครับ! ผมเห็นว่าคุณกำลังอยู่ใน Quest บทเรียน <b>REST API with Flask</b> และเจอปัญหา Error จากระบบ Coding Lab:</p>
                            <div class="p-2.5 rounded-3 mb-3 font-monospace fs-sm border border-danger border-opacity-25" style="background: rgba(239, 68, 68, 0.1); color: #fca5a5;">
                                <i class="bi bi-exclamation-triangle-fill me-1 text-danger"></i> ImportError: No module named flask
                            </div>
                            <p class="mb-0">มีจุดไหนให้ผมช่วยอธิบาย ให้คำใบ้ หรือช่วยไล่โค้ดทีละบรรทัดไหมครับ?</p>
                            <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top border-color">
                                <button class="btn btn-sm btn-quick-prompt rounded-pill" onclick="sendQuickPrompt('ช่วยแก้บั๊ก ImportError นี้หน่อย')">💡 ช่วยแก้บั๊ก ImportError นี้หน่อย</button>
                                <button class="btn btn-sm btn-quick-prompt rounded-pill" onclick="sendQuickPrompt('อธิบายบทเรียนนี้แบบสรุปเข้าใจง่าย')">📖 อธิบายบทเรียนนี้แบบสรุป</button>
                                <button class="btn btn-sm btn-quick-prompt rounded-pill" onclick="sendQuickPrompt('สร้างโจทย์ฝึกเขียน API เพิ่มเติม')">⚔️ สร้างโจทย์ฝึกเขียน API</button>
                            </div>
                        </div>
                    </div>
                </div>
            `
        },
        'chat-2': {
            lesson: 'Debug: Flask ImportError',
            isNew: false,
            messages: `
                <div class="chat-message user-message mb-4 d-flex">
                    <div class="d-flex gap-3 justify-content-end w-100">
                        <div class="message-content rounded-4 p-3 p-md-4 shadow-sm" style="max-width: 80%;">
                            <p class="mb-0 text-white">ทำไมรันไฟล์แล้วขึ้น ImportError: No module named flask ครับ?</p>
                        </div>
                    </div>
                </div>
                <div class="chat-message ai-message mb-4">
                    <div class="d-flex gap-3">
                        <div class="avatar-sm text-white rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center shadow-sm" style="width:42px; height:42px; background: linear-gradient(135deg, #06b6d4, #6366f1);">
                            <i class="bi bi-stars fs-5"></i>
                        </div>
                        <div class="message-content rounded-4 rounded-top-start-0 p-3 p-md-4 shadow-sm w-100">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-info fs-sm"><i class="bi bi-cpu me-1"></i> AI Tutor Companion</span>
                                <span class="badge bg-warning bg-opacity-25 text-warning fs-xs"><i class="bi bi-mortarboard me-1"></i>Mentor Hint</span>
                            </div>
                            <p class="mb-2">สาเหตุหลักของ <b>ImportError</b> เกิดจาก Python ไม่พบแพ็กเกจ Flask ใน Environment ที่กำลังรันอยู่ครับ!</p>
                            <p class="mb-0"><b>คำใบ้ (Hint):</b> ลองรันคำสั่ง <code>pip install flask</code> ใน Terminal หรือตรวจสอบดูว่าได้เปิดใช้งาน Virtual Environment (venv) แล้วหรือยังนะครับ 👍</p>
                        </div>
                    </div>
                </div>
            `
        },
        'chat-3': {
            lesson: 'Python Decorators Masterclass',
            isNew: false,
            messages: `
                <div class="chat-message user-message mb-4 d-flex">
                    <div class="d-flex gap-3 justify-content-end w-100">
                        <div class="message-content rounded-4 p-3 p-md-4 shadow-sm" style="max-width: 80%;">
                            <p class="mb-0 text-white">ช่วยสรุปการทำงานของ Decorator ใน Python ให้สั้นที่สุดหน่อยครับ</p>
                        </div>
                    </div>
                </div>
                <div class="chat-message ai-message mb-4">
                    <div class="d-flex gap-3">
                        <div class="avatar-sm text-white rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center shadow-sm" style="width:42px; height:42px; background: linear-gradient(135deg, #06b6d4, #6366f1);">
                            <i class="bi bi-stars fs-5"></i>
                        </div>
                        <div class="message-content rounded-4 rounded-top-start-0 p-3 p-md-4 shadow-sm w-100">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-info fs-sm"><i class="bi bi-cpu me-1"></i> AI Tutor Companion</span>
                            </div>
                            <p class="mb-1"><b>Decorator</b> คือฟังก์ชันที่ใช้ครอบฟังก์ชันอื่น เพื่อครอบหรือเพิ่มความสามารถใหม่เข้าไป โดยไม่ต้องแก้ไขโค้ดเดิมในฟังก์ชันนั้นครับ!</p>
                            <p class="mb-0 font-monospace text-info fs-xs">ตัวอย่าง: @app.route('/') คือ Decorator ที่บอก Flask ให้เรียกใช้ฟังก์ชันเมื่อมี request มาที่ URL นั้น</p>
                        </div>
                    </div>
                </div>
            `
        },
        'chat-4': {
            lesson: 'Architecture: Flask vs Django',
            isNew: false,
            messages: `
                <div class="chat-message user-message mb-4 d-flex">
                    <div class="d-flex gap-3 justify-content-end w-100">
                        <div class="message-content rounded-4 p-3 p-md-4 shadow-sm" style="max-width: 80%;">
                            <p class="mb-0 text-white">ควรเลือกใช้ Flask หรือ Django สำหรับทำ REST API ดีครับ?</p>
                        </div>
                    </div>
                </div>
                <div class="chat-message ai-message mb-4">
                    <div class="d-flex gap-3">
                        <div class="avatar-sm text-white rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center shadow-sm" style="width:42px; height:42px; background: linear-gradient(135deg, #06b6d4, #6366f1);">
                            <i class="bi bi-stars fs-5"></i>
                        </div>
                        <div class="message-content rounded-4 rounded-top-start-0 p-3 p-md-4 shadow-sm w-100">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-info fs-sm"><i class="bi bi-cpu me-1"></i> AI Tutor Companion</span>
                            </div>
                            <p class="mb-2">ขึ้นอยู่กับสเกลของโปรเจกต์เลยครับ:</p>
                            <ul class="mb-0 ps-3">
                                <li><b>Flask:</b> น้ำหนักเบา ยืดหยุ่น เหมาะสำหรับ Microservices หรือ API ขนาดเล็กถึงกลาง</li>
                                <li><b>Django:</b> มาพร้อมถ่านในตัว (Batteries-Included) มี ORM และ Admin Panel ในตัว เหมาะกับแอปใหญ่</li>
                            </ul>
                        </div>
                    </div>
                </div>
            `
        }
    };

    // ฟังก์ชันสำหรับสลับแชต (Load Chat History)
    function loadChatSession(chatId) {
        const session = mockChatData[chatId];
        if (!session) return;

        currentChatId = chatId;

        // 1. อัปเดต Active Class
        $(`.chat-history-item`).removeClass('active');
        $(`.chat-history-item[data-chat-id="${chatId}"]`).addClass('active');

        // 2. อัปเดตชื่อบทเรียนบน Header
        $('#currentLessonTitle').text(session.lesson);

        // 3. Render ข้อความใน Chat Timeline
        $('#chatTimeline').html(session.messages);

        // 4. Scroll ไปด้านล่างสุด
        const timeline = document.getElementById('chatTimeline');
        timeline.scrollTop = timeline.scrollHeight;

        // 5. ปิด Offcanvas ในมือถือ
        const offcanvasElem = document.getElementById('offcanvasHistory');
        if (offcanvasElem && bootstrap.Offcanvas.getInstance(offcanvasElem)) {
            bootstrap.Offcanvas.getInstance(offcanvasElem).hide();
        }
    }

    // ฟังก์ชันสร้างแชตใหม่ (Dynamic Add to Sidebar)
    function startNewChat() {
        const newChatId = 'chat-' + Date.now();
        const defaultTitle = 'แชตใหม่ (New Quest)';

        // 1. เพิ่มข้อมูลเข้าไปใน Mock Data
        mockChatData[newChatId] = {
            lesson: defaultTitle,
            isNew: true, // กำหนด flag ว่าเป็นแชตใหม่ เพื่อรอรับข้อความแรกนำไปเปลี่ยนชื่อ
            messages: `
                <div class="chat-message ai-message mb-4">
                    <div class="d-flex gap-3">
                        <div class="avatar-sm text-white rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center shadow-sm" style="width:42px; height:42px; background: linear-gradient(135deg, #06b6d4, #6366f1);">
                            <i class="bi bi-stars fs-5"></i>
                        </div>
                        <div class="message-content rounded-4 rounded-top-start-0 p-3 p-md-4 shadow-sm w-100">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-info fs-sm"><i class="bi bi-cpu me-1"></i> AI Tutor Companion</span>
                                <span class="fs-xs text-white-50 font-monospace">Just now</span>
                            </div>
                            <p class="mb-0">เริ่มต้นการสนทนาใหม่เรียบร้อยแล้ว! พิมพ์ถามหรือวางโค้ดที่สงสัยลงในช่องด้านล่างได้เลยครับ</p>
                        </div>
                    </div>
                </div>
            `
        };

        // 2. สร้าง Element HTML ใหม่สำหรับรายการแชตด้านข้าง
        const newItemHtml = `
            <a class="chat-history-item active" data-chat-id="${newChatId}">
                <i class="bi bi-chat-left-text me-2 text-info"></i>
                <span class="text-truncate">${defaultTitle}</span>
            </a>
        `;

        // 3. เอา Active ออกจากอันเดิมทั้งหมด
        $('.chat-history-item').removeClass('active');

        // 4. แทรกรายการใหม่ลงไปล่างสุดของหัวข้อ "วันนี้" ในทั้ง 2 Sidebar
        $('#sidebarChatList .section-today').after(newItemHtml);
        $('#offcanvasChatList .section-today').after(newItemHtml);

        // 5. โหลดแชตใหม่ขึ้นหน้าจอ
        loadChatSession(newChatId);

        LevelUpAlert.toast('สร้างห้องแชตใหม่สำเร็จ!', 'success');
    }

    // ฟังก์ชันส่ง Quick Prompt
    function sendQuickPrompt(promptText) {
        $('#chatInput').val(promptText);
        sendMessage();
    }

    // ฟังก์ชันส่งข้อความ
    function sendMessage() {
        const text = $('#chatInput').val().trim();
        if (!text) return;

        // หากเป็นแชตใหม่ที่เพิ่งสร้าง ให้เปลี่ยนชื่อรายการแถบซ้ายตามข้อความแรกที่พิมพ์
        if (mockChatData[currentChatId] && mockChatData[currentChatId].isNew) {
            const displayTitle = text.length > 22 ? text.substring(0, 22) + '...' : text;
            mockChatData[currentChatId].lesson = displayTitle;
            mockChatData[currentChatId].isNew = false;
            
            // เปลี่ยนชื่อในเมนูซ้าย
            $(`.chat-history-item[data-chat-id="${currentChatId}"] span`).text(displayTitle);
            $('#currentLessonTitle').text(displayTitle);
        }

        const userHtml = `
            <div class="chat-message user-message mb-4 d-flex">
                <div class="d-flex gap-3 justify-content-end w-100">
                    <div class="message-content rounded-4 p-3 p-md-4 shadow-sm" style="max-width: 80%;">
                        <p class="mb-0 text-white">${text.replace(/</g, "&lt;").replace(/>/g, "&gt;")}</p>
                    </div>
                </div>
            </div>
        `;

        $('#chatTimeline').append(userHtml);
        
        // บันทึกลง Mock Data
        mockChatData[currentChatId].messages = $('#chatTimeline').html();

        $('#chatInput').val('').css('height', 'auto');

        const timeline = document.getElementById('chatTimeline');
        timeline.scrollTop = timeline.scrollHeight;

        const isMentor = $('#mentorModeSwitch').is(':checked');
        
        // จำลอง AI ตอบกลับ
        setTimeout(() => {
            const aiHtml = `
                <div class="chat-message ai-message mb-4">
                    <div class="d-flex gap-3">
                        <div class="avatar-sm text-white rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center shadow-sm" style="width:42px; height:42px; background: linear-gradient(135deg, #06b6d4, #6366f1);">
                            <i class="bi bi-stars fs-5"></i>
                        </div>
                        <div class="message-content rounded-4 rounded-top-start-0 p-3 p-md-4 shadow-sm w-100">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-info fs-sm"><i class="bi bi-cpu me-1"></i> AI Tutor Companion</span>
                                ${isMentor ? '<span class="badge bg-warning bg-opacity-25 text-warning fs-xs"><i class="bi bi-mortarboard me-1"></i>Mentor Hint (+15% EXP)</span>' : ''}
                            </div>
                            <p class="mb-0">รับทราบครับ! กำลังประมวลผลคำตอบจากบริบทบทเรียนที่คุณสอบถาม...</p>
                        </div>
                    </div>
                </div>
            `;
            $('#chatTimeline').append(aiHtml);
            mockChatData[currentChatId].messages = $('#chatTimeline').html();
            timeline.scrollTop = timeline.scrollHeight;
        }, 600);
    }

    // ฟังก์ชันดาวน์โหลดประวัติแชต
    function downloadChatHistory() {
        let chatText = "=== LevelUp AI Chat Log ===\n\n";

        $('#chatTimeline .chat-message').each(function() {
            const isUser = $(this).hasClass('user-message');
            const sender = isUser ? 'User' : 'AI Companion';
            const content = $(this).find('.message-content p').text();
            chatText += `[${sender}]: ${content}\n`;
        });

        const blob = new Blob([chatText], { type: 'text/plain;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `chat-history-${Date.now()}.txt`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        LevelUpAlert.toast('ดาวน์โหลดประวัติแชตสำเร็จ', 'success');
    }

    $(document).ready(function() {
        // โหลดห้องแชตแรก (chat-1) เมื่อเปิดหน้าเว็บ
        loadChatSession('chat-1');

        // Event Listener: เมื่อคลิกเลือกรายการประวัติแชต
        $(document).on('click', '.chat-history-item', function(e) {
            e.preventDefault();
            const chatId = $(this).data('chat-id');
            if (chatId) {
                loadChatSession(chatId);
            }
        });

        // ปุ่มแชตใหม่
        $('#btnNewChat').on('click', startNewChat);

        // ปุ่มดาวน์โหลด
        $('#btnDownloadChat').on('click', downloadChatHistory);

        // ระบบสลับโหมด Light / Dark
        $('#btnThemeToggle').on('click', function() {
            const currentTheme = $('html').attr('data-theme');
            if (currentTheme === 'light') {
                $('html').attr('data-theme', 'dark');
                $(this).html('<i class="bi bi-sun"></i>');
                LevelUpAlert.toast('เปลี่ยนเป็นโหมด Dark', 'info');
            } else {
                $('html').attr('data-theme', 'light');
                $(this).html('<i class="bi bi-moon"></i>');
                LevelUpAlert.toast('เปลี่ยนเป็นโหมด Light', 'info');
            }
        });

        // Auto-resize textarea
        $('#chatInput').on('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Enter key to send
        $('#chatInput').on('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        $('#btnSend').on('click', function(e) {
            e.preventDefault();
            sendMessage();
        });

        // Toggle Mentor Mode Badge
        $('#mentorModeSwitch').on('change', function() {
            const isChecked = $(this).is(':checked');
            if(isChecked) {
                $('#xpBonusBadge').removeClass('d-none');
                LevelUpAlert.toast('เปิด Mentor Mode (คำใบ้ +15% EXP)', 'info');
            } else {
                $('#xpBonusBadge').addClass('d-none');
                LevelUpAlert.toast('ปิด Mentor Mode (โหมดเฉลยตรง)', 'warning');
            }
        });

        // Tools
        $('#btnUploadFile').on('click', function() {
            LevelUpAlert.info('อัปโหลดไฟล์/รูปภาพ', 'รองรับไฟล์ภาพ .png, .jpg และโค้ดตัวอย่าง .py, .js เพื่อให้ AI วิเคราะห์');
        });

        $('#btnUploadZip').on('click', function() {
            LevelUpAlert.info('วิเคราะห์โปรเจกต์ (ZIP)', 'อัปโหลดโครงสร้างโปรเจกต์ .zip เพื่อให้ AI ตรวจสอบบั๊กทั้งโปรเจกต์');
        });
    });
</script>

<?php
$ROOT_PATH = '../';
include '../includes/footer.php'; ?>