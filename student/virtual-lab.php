<?php
$ROOT_PATH = '../';

// ==========================================
// 1. เตรียมข้อมูลจาก Database (Data Preparation)
// ==========================================

// ข้อมูลจำลองของ Lab
$labData = [
    'id' => 'lab_101',
    'title' => 'Docker & Network Setup',
    'type' => 'Docker',
    'difficulty' => 'Intermediate',
    'time_est' => '30 นาที',
    'xp_reward' => 50,
    'scenario' => 'แอปพลิเคชันล่มหลังจาก Deploy ระบบแจ้งว่าไม่สามารถเชื่อมต่อ Database ได้ ให้ตรวจสอบ Network และแก้ไข Docker Compose'
];

// ข้อมูลจำลองของภารกิจ (Quests)
$quests = [
    ['id' => 1, 'text' => 'ตรวจสอบสถานะ Container ที่รันอยู่', 'status' => 'completed'],
    ['id' => 2, 'text' => 'ค้นหาชื่อ Docker Network ที่ระบบสร้างไว้', 'status' => 'pending'],
    ['id' => 3, 'text' => 'แก้ไขไฟล์ docker-compose.yml ให้ใช้ Network เดียวกัน', 'status' => 'pending'],
    ['id' => 4, 'text' => 'Restart Container และทดสอบการเชื่อมต่อ', 'status' => 'pending']
];

// ข้อมูลจำลองแชทเริ่มต้นของ AI
$initialAiMessage = "สวัสดี Agent! ระบบตรวจพบความผิดปกติของการเชื่อมต่อ Database \n\nลองใช้คำสั่ง `docker network ls` เพื่อตรวจสอบเครือข่ายปัจจุบันดูไหมครับ?";

$pageTitle = $labData['title'] . " | LevelUp Virtual Lab";

// 📌 ดึง Header จาก includes/header.php
require_once $ROOT_PATH . 'includes/header.php';
?>

<!-- Google Font สำหรับ Code Editor & Terminal -->
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

<style>
    /* ===================================================
       DESIGN SYSTEM: GAMIFIED CYBER LAB
       =================================================== */
    :root {
        --lab-bg-dark: #0a0d14;
        --lab-surface: #121824;
        --lab-surface-alt: #1a2332;
        --lab-border: rgba(99, 102, 241, 0.3);
        --lab-primary: #6366f1;
        --lab-primary-glow: rgba(99, 102, 241, 0.5);
        --lab-success: #10b981;
        --lab-success-glow: rgba(16, 185, 129, 0.5);
        --lab-warning: #f59e0b;
        --lab-danger: #ef4444;
        
        --lab-text: #f8fafc;
        --lab-text-sub: #cbd5e1;
        --lab-text-muted: #94a3b8;
        
        --font-ui: 'Inter', sans-serif;
        --font-code: 'JetBrains Mono', monospace;
    }

    /* ปรับแต่ง Container หลัก ป้องกันซ้อนทับ Header หลัก */
    .virtual-lab-wrapper {
        min-height: calc(100vh - 65px);
        height: calc(100vh - 65px);
        background-color: var(--lab-bg-dark);
        color: var(--lab-text);
        font-family: var(--font-ui);
        position: relative;
        overflow: hidden;
    }

    .bg-surface { background-color: var(--lab-surface) !important; }
    .bg-surface-alt { background-color: var(--lab-surface-alt) !important; }
    
    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: var(--lab-bg-dark); }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: var(--lab-primary); }

    /* Lab Sub-Topbar (ปรับแต่งป้องกันการโดนบีบติด Header) */
    .lab-topbar {
        background: rgba(18, 24, 36, 0.98) !important;
        backdrop-filter: blur(10px);
        border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-bottom: 1px solid var(--lab-border) !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        z-index: 10;
        flex-shrink: 0; /* ห้ามบีบความสูงของ Topbar */
        padding-top: 10px !important;
        padding-bottom: 10px !important;
    }

    .progress {
        background-color: #0f172a;
        border-radius: 10px;
        overflow: visible;
    }
    .progress-bar {
        border-radius: 10px;
        box-shadow: 0 0 10px var(--lab-success-glow);
        transition: width 0.5s ease;
    }

    /* Fixed Custom Nav Tabs */
    .custom-lab-tabs {
        background-color: var(--lab-surface-alt) !important;
        border-bottom: 1px solid var(--lab-border) !important;
    }
    .custom-lab-tabs .nav-link {
        background-color: transparent !important;
        color: #cbd5e1 !important;
        border: none !important;
        border-bottom: 3px solid transparent !important;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
        border-radius: 0 !important;
    }
    .custom-lab-tabs .nav-link:hover {
        color: #ffffff !important;
        background-color: rgba(255, 255, 255, 0.05) !important;
    }
    .custom-lab-tabs .nav-link.active {
        background-color: var(--lab-surface) !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        border-bottom: 3px solid var(--lab-primary) !important;
    }

    /* Buttons */
    .btn-lab-primary {
        background: linear-gradient(135deg, var(--lab-primary), #4f46e5);
        color: #ffffff;
        font-weight: 600;
        border: none;
        box-shadow: 0 0 15px rgba(99, 102, 241, 0.4);
        transition: all 0.3s;
    }
    .btn-lab-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 25px rgba(99, 102, 241, 0.7);
        color: #ffffff;
    }

    /* Quest / Tasks */
    .quest-item {
        background: rgba(255,255,255,0.05);
        border: 1px solid var(--lab-border);
        border-radius: 8px;
        transition: all 0.3s;
    }
    .quest-item .quest-text {
        color: var(--lab-text-sub);
    }
    .quest-item.completed {
        background: rgba(16, 185, 129, 0.15);
        border-color: var(--lab-success);
    }
    .quest-item.completed .quest-text {
        text-decoration: line-through;
        color: #64748b;
    }
    .quest-checkbox {
        width: 20px; height: 20px;
        border: 2px solid var(--lab-text-muted);
        border-radius: 4px;
        display: flex; align-items: center; justify-content: center;
    }
    .quest-item.completed .quest-checkbox {
        border-color: var(--lab-success);
        background: var(--lab-success);
        box-shadow: 0 0 10px var(--lab-success-glow);
    }

    /* IDE Editor */
    .ide-section { background-color: #0f141d; }
    .ide-tabs { background-color: #1a2332; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .ide-tab {
        padding: 8px 16px; font-family: var(--font-ui); font-size: 13px;
        color: #94a3b8; cursor: pointer; border-top: 2px solid transparent;
    }
    .ide-tab.active {
        background-color: #0f141d; color: #ffffff; font-weight: 600;
        border-top-color: var(--lab-primary);
    }
    .ide-editor {
        font-family: var(--font-code); line-height: 1.6; font-size: 14px;
        color: #f1f5f9; outline: none;
        overflow-x: auto;
        white-space: pre;
    }
    /* Syntax Colors */
    .syntax-keyword { color: #38bdf8; font-weight: 600; }
    .syntax-string { color: #fde047; }
    .syntax-prop { color: #a78bfa; }
    .syntax-comment { color: #64748b; font-style: italic; }

    /* Terminal */
    .terminal-section { background-color: #090d16; }
    .terminal-header { background-color: #161e2e; border-top: 1px solid #2a364f; border-bottom: 1px solid #2a364f; }
    .terminal-body { color: #f8fafc !important; }
    .terminal-prompt { color: #4ade80; font-weight: bold; }
    
    /* AI Assistant */
    .ai-avatar {
        background: linear-gradient(135deg, #06b6d4, var(--lab-primary));
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.4);
    }
    .ai-bubble {
        background: rgba(30, 41, 59, 0.8);
        border: 1px solid var(--lab-border);
        color: #f8fafc;
        box-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }
    .typing-indicator span {
        display: inline-block; width: 6px; height: 6px;
        background-color: #38bdf8; border-radius: 50%;
        animation: typing 1.4s infinite both;
    }
    .typing-indicator span:nth-child(1) { animation-delay: 0s; }
    .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typing { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }

    /* Achievement Modal */
    .modal-content.gamified {
        background: radial-gradient(circle at top, #1e293b, #090d16);
        border: 1px solid var(--lab-primary);
        box-shadow: 0 0 50px rgba(99, 102, 241, 0.4);
    }
    .jump-anim { animation: jump 2s infinite; }
    @keyframes jump { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-15px) scale(1.1); text-shadow: 0 10px 20px var(--lab-warning); } }

    /* Mobile Overlay Backdrop */
    .mobile-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 1040;
        display: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .mobile-overlay.active {
        display: block;
        opacity: 1;
    }

    @media (max-width: 991.98px) {
        .lab-topbar { padding: 8px 12px !important; gap: 8px !important; }
        .lab-title-text { max-width: 130px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 0.85rem !important; }
        .progress-tracker { display: none !important; }
        .timer-box { padding: 2px 8px !important; }
        .timer-box div:first-child { display: none; }
        .timer-box #labTimer { font-size: 0.85rem !important; }

        .lab-sidebar-left {
            position: fixed !important; top: 0; left: -100%; width: 88% !important; max-width: 360px; min-width: unset !important;
            height: 100vh; z-index: 1050; transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 10px 0 30px rgba(0, 0, 0, 0.8);
        }
        .lab-sidebar-left.show-mobile { left: 0 !important; }

        .lab-sidebar-right {
            position: fixed !important; top: 0; right: -100%; width: 88% !important; max-width: 360px; min-width: unset !important;
            height: 100vh; z-index: 1050; transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: -10px 0 30px rgba(0, 0, 0, 0.8);
        }
        .lab-sidebar-right.show-mobile { right: 0 !important; }

        .lab-main-area { width: 100% !important; }
    }
    
    /* ===================================================
       ☀️ LIGHT MODE THEME OVERRIDES
       =================================================== */
    html[data-theme="light"] {
        --lab-bg-dark: #f8fafc;
        --lab-surface: #ffffff;
        --lab-surface-alt: #f1f5f9;
        --lab-border: rgba(99, 102, 241, 0.2);
        --lab-text: #0f172a;
        --lab-text-sub: #334155;
        --lab-text-muted: #64748b;
    }

    html[data-theme="light"] body,
    html[data-theme="light"] .virtual-lab-wrapper {
        background-color: var(--lab-bg-dark) !important;
        color: var(--lab-text) !important;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    html[data-theme="light"] .bg-dark { background-color: var(--lab-surface) !important; }
    html[data-theme="light"] .text-white, 
    html[data-theme="light"] .text-light { color: var(--lab-text) !important; }
    html[data-theme="light"] .border-secondary { border-color: #cbd5e1 !important; }

    html[data-theme="light"] .lab-topbar {
        background: rgba(255, 255, 255, 0.95) !important;
        border-bottom: 1px solid var(--lab-border) !important;
    }
    html[data-theme="light"] .custom-lab-tabs .nav-link { color: var(--lab-text-muted) !important; }
    html[data-theme="light"] .custom-lab-tabs .nav-link:hover { color: var(--lab-text) !important; background-color: #f1f5f9 !important; }
    html[data-theme="light"] .custom-lab-tabs .nav-link.active { background-color: #ffffff !important; color: var(--lab-text) !important; }
    html[data-theme="light"] .quest-item { background: #f8fafc; border-color: #e2e8f0; }
    
    html[data-theme="light"] .ide-section { background-color: #ffffff; }
    html[data-theme="light"] .ide-tabs { background-color: #f1f5f9; border-bottom: 1px solid #e2e8f0; }
    html[data-theme="light"] .ide-tab { color: var(--lab-text-muted); }
    html[data-theme="light"] .ide-tab.active { background-color: #ffffff; color: var(--lab-text); }
    html[data-theme="light"] .ide-editor { color: var(--lab-text); }
    html[data-theme="light"] .syntax-keyword { color: #0284c7; }
    html[data-theme="light"] .syntax-string { color: #d97706; }
    html[data-theme="light"] .syntax-prop { color: #7c3aed; }
    html[data-theme="light"] .syntax-comment { color: #94a3b8; }

    html[data-theme="light"] .terminal-section { background-color: #f8fafc; }
    html[data-theme="light"] .terminal-header { background-color: #e2e8f0; border-color: #cbd5e1; }
    html[data-theme="light"] .terminal-header span { color: #0f172a !important; }
    html[data-theme="light"] .terminal-body { color: #0f172a !important; }
    html[data-theme="light"] .terminal-prompt { color: #059669; }
    html[data-theme="light"] #terminalInput { color: #0f172a !important; }

    html[data-theme="light"] .ai-bubble {
        background: #ffffff;
        color: #0f172a;
        border: 1px solid var(--lab-border);
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    html[data-theme="light"] #aiChatInput {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
    }
</style>

<main>
    <!-- Mobile Overlay Backdrop -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <div class="virtual-lab-wrapper container-fluid p-0 d-flex flex-column" id="app">
        
        <!-- Lab Topbar -->
        <header class="lab-topbar d-flex justify-content-between align-items-center px-3 px-lg-4">
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!-- Mobile Left Sidebar Trigger -->
                <button class="btn btn-sm btn-outline-light d-lg-none me-1" id="btnToggleLeft" title="ดูโจทย์ภารกิจ">
                    <i class="bi bi-layout-sidebar"></i>
                </button>

                <a href="courses.php" class="btn btn-sm btn-outline-light border-secondary rounded-pill px-2 px-lg-3">
                    <i class="bi bi-box-arrow-left"></i> <span class="d-none d-sm-inline ms-1">Exit</span>
                </a>
                <div class="vr bg-secondary opacity-50 d-none d-sm-block"></div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-0 mb-lg-1">
                        <span class="badge bg-primary text-uppercase" style="font-size: 9px; letter-spacing: 1px;"><i class="bi bi-box-seam me-1"></i> <?= htmlspecialchars($labData['type']) ?></span>
                        <span class="badge bg-dark border border-warning text-warning fw-bold d-none d-sm-inline-block" style="font-size: 9px;"><i class="bi bi-star-fill me-1"></i> <?= $labData['xp_reward'] ?> XP</span>
                    </div>
                    <h5 class="mb-0 fw-bold text-white fs-6 lab-title-text"><?= htmlspecialchars($labData['title']) ?></h5>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-2 gap-lg-4">
                <div class="progress-tracker d-flex flex-column gap-1">
                    <div class="d-flex justify-content-between w-100 px-1">
                        <span class="fs-sm text-light" style="font-size: 11px; letter-spacing: 0.5px;">MISSION PROGRESS</span>
                        <span class="fs-sm fw-bold text-success" id="labProgressText" style="font-size: 11px;">25%</span>
                    </div>
                    <div class="progress" style="width: 180px; height: 6px;">
                        <div class="progress-bar bg-success" id="labProgressBar" role="progressbar" style="width: 25%;"></div>
                    </div>
                </div>
                
                <!-- Gamified Timer -->
                <div class="timer-box bg-dark border border-secondary rounded px-2 px-lg-3 py-1 text-center">
                    <div class="text-muted" style="font-size: 9px; letter-spacing: 1px;">TIME ELAPSED</div>
                    <div class="text-monospace text-warning fw-bold fs-6 fs-lg-5" id="labTimer" style="font-family: var(--font-code);">00:12:45</div>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-1 gap-lg-2">
                <!-- ปุ่ม Toggle Theme -->
                <button class="btn btn-sm btn-outline-light rounded-circle d-none d-sm-inline-flex align-items-center justify-content-center me-1 theme-toggle-btn" style="width:34px; height:34px;" title="Toggle Light/Dark Mode" onclick="labApp.toggleTheme()">
                    <i class="bi bi-sun-fill" id="themeIcon"></i>
                </button>
                <button class="btn btn-sm btn-outline-light rounded-circle d-none d-sm-inline-flex align-items-center justify-content-center" style="width:34px; height:34px;" onclick="labApp.createSnapshot()" title="Save Snapshot"><i class="bi bi-camera"></i></button>
                <button class="btn btn-sm btn-outline-danger rounded-circle d-none d-sm-inline-flex align-items-center justify-content-center" style="width:34px; height:34px;" onclick="labApp.resetEnvironment()" title="Reset VM"><i class="bi bi-arrow-clockwise"></i></button>
                <button class="btn btn-sm btn-lab-primary rounded-pill px-3 px-lg-4 fw-bold" onclick="labApp.submitLab()">
                    <i class="bi bi-rocket-takeoff me-1"></i> <span class="d-none d-sm-inline">Deploy & Submit</span>
                </button>

                <!-- Mobile Right Sidebar (AI) Trigger -->
                <button class="btn btn-sm btn-outline-info rounded-circle d-lg-none ms-1" id="btnToggleRight" style="width:34px; height:34px;" title="AI Mentor">
                    <i class="bi bi-robot"></i>
                </button>
            </div>
        </header>

        <!-- 3-Pane Workspace -->
        <div class="lab-workspace d-flex flex-grow-1 overflow-hidden position-relative">
            
            <!-- Left Pane: Instructions & Tasks -->
            <aside class="lab-sidebar-left bg-surface border-end border-secondary border-opacity-25 d-flex flex-column" style="width: 330px; min-width: 330px;" id="sidebarLeft">
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom border-secondary border-opacity-25 d-lg-none bg-surface-alt">
                    <span class="fw-bold text-white fs-sm"><i class="bi bi-journal-text me-2"></i>รายละเอียดภารกิจ</span>
                    <button class="btn-close btn-close-white" id="btnCloseLeft"></button>
                </div>
                
                <ul class="nav nav-tabs nav-justified custom-lab-tabs" id="labTabs">
                    <li class="nav-item">
                        <button class="nav-link active py-3" data-bs-toggle="tab" data-bs-target="#tab-instruction">
                            <i class="bi bi-journal-text me-1"></i> Briefing
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-3" data-bs-toggle="tab" data-bs-target="#tab-tasks">
                            <i class="bi bi-list-check me-1"></i> Quests <span class="badge bg-primary rounded-pill ms-1" id="taskCount">1/4</span>
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content flex-grow-1 overflow-auto p-3 p-lg-4 custom-scrollbar">
                    <!-- Instruction Tab -->
                    <div class="tab-pane fade show active" id="tab-instruction">
                        <div class="d-flex justify-content-between text-muted mb-4 pb-3 border-bottom border-secondary border-opacity-25" style="font-size: 12px;">
                            <span class="badge bg-dark border border-secondary text-light"><i class="bi bi-bar-chart"></i> <?= htmlspecialchars($labData['difficulty']) ?></span>
                            <span class="badge bg-dark border border-secondary text-light"><i class="bi bi-clock"></i> Est. <?= htmlspecialchars($labData['time_est']) ?></span>
                        </div>
                        
                        <h6 class="text-primary fw-bold mb-3"><i class="bi bi-shield-exclamation me-1"></i> Incident Scenario</h6>
                        <div class="bg-dark p-3 rounded-3 mb-4 border border-secondary border-opacity-25 text-white" style="font-size: 13.5px; line-height: 1.6;">
                            <?= htmlspecialchars($labData['scenario']) ?>
                        </div>
                        
                        <h6 class="text-white fw-bold mb-3"><i class="bi bi-bullseye me-1"></i> Objective</h6>
                        <p class="text-light mb-4" style="font-size: 13.5px; line-height: 1.6;">
                            เชื่อมต่อ Docker Container ฝั่ง Backend ให้สามารถคุยกับ Database Container ได้สำเร็จผ่าน Docker Network
                        </p>
                        
                        <div class="alert alert-dark border-info border-opacity-50 py-2 px-3 mb-0 d-flex gap-2 align-items-start" style="font-size: 12.5px;">
                            <i class="bi bi-robot text-info fs-5"></i>
                            <span class="text-light">หากติดปัญหา ไม่รู้จะเริ่มตรงไหน สามารถเรียกใช้ AI Mentor ทางขวามือเพื่อขอเบาะแส (Hint) ได้เลย Agent!</span>
                        </div>
                    </div>
                    
                    <!-- Tasks Tab (Gamified Quests) -->
                    <div class="tab-pane fade" id="tab-tasks">
                        <h6 class="text-white fw-bold mb-3">Active Missions</h6>
                        <div class="task-list d-flex flex-column gap-3" id="taskListContainer">
                            <?php foreach($quests as $quest): ?>
                                <?php $isCompleted = $quest['status'] === 'completed'; ?>
                                <div class="quest-item p-3 d-flex gap-3 align-items-start <?= $isCompleted ? 'completed' : '' ?>" data-id="<?= $quest['id'] ?>">
                                    <div class="quest-checkbox flex-shrink-0 mt-1">
                                        <?php if($isCompleted): ?><i class="bi bi-check-lg text-white"></i><?php endif; ?>
                                    </div>
                                    <div class="quest-text" style="font-size: 13.5px; line-height: 1.5;">
                                        <?= htmlspecialchars($quest['text']) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Center Pane: Editor & Terminal -->
            <main class="lab-main-area d-flex flex-column flex-grow-1 bg-dark">
                <!-- IDE Area -->
                <div class="ide-section flex-grow-1 d-flex flex-column border-bottom border-secondary border-opacity-25" style="height: 55%;">
                    <div class="ide-tabs d-flex gap-1">
                        <div class="ide-tab active"><i class="bi bi-filetype-yml text-danger me-1"></i> docker-compose.yml</div>
                        <div class="ide-tab"><i class="bi bi-filetype-sh text-success me-1"></i> setup.sh</div>
                    </div>
                    <div class="ide-editor flex-grow-1 position-relative p-3 p-lg-4 custom-scrollbar" contenteditable="true" spellcheck="false" id="codeEditor">
<span class="syntax-keyword">version:</span> <span class="syntax-string">'3.8'</span>

<span class="syntax-keyword">services:</span>
  <span class="syntax-prop">web:</span>
    <span class="syntax-keyword">image:</span> <span class="syntax-string">nginx:alpine</span>
    <span class="syntax-keyword">ports:</span>
      - <span class="syntax-string">"80:80"</span>
    <span class="syntax-comment"># TODO: Add networks configuration</span>
  
  <span class="syntax-prop">db:</span>
    <span class="syntax-keyword">image:</span> <span class="syntax-string">mysql:8.0</span>
    <span class="syntax-keyword">environment:</span>
      <span class="syntax-prop">MYSQL_ROOT_PASSWORD:</span> <span class="syntax-string">root</span>
    <span class="syntax-comment"># TODO: Add networks configuration</span>
                    </div>
                </div>
                
                <!-- Terminal Area -->
                <div class="terminal-section d-flex flex-column position-relative" style="height: 45%;">
                    <div class="terminal-header py-2 px-3 d-flex justify-content-between align-items-center">
                        <span class="text-light font-monospace" style="font-size: 12px;"><i class="bi bi-terminal-fill me-2 text-primary"></i> root@levelup-lab:~</span>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm text-light p-0"><i class="bi bi-plus-lg"></i></button>
                            <button class="btn btn-sm text-light p-0"><i class="bi bi-arrows-collapse"></i></button>
                        </div>
                    </div>
                    <div class="terminal-body flex-grow-1 p-3 overflow-auto custom-scrollbar font-monospace" id="terminalOutput" style="font-size: 13.5px; line-height: 1.6;">
                        <div class="mb-3">
                            <span style="color: #38bdf8; font-weight: bold;">LevelUp OS v2.4 (Virtual Environment)</span><br>
                            <span class="text-light">Type 'help' for a list of built-in commands.</span>
                        </div>
                        <!-- Mock History -->
                        <div><span class="terminal-prompt">root@levelup:~$</span> <span class="text-white">docker ps</span></div>
                        <div class="mb-2 text-light">
CONTAINER ID   IMAGE          COMMAND                  CREATED         STATUS         PORTS                NAMES<br>
a1b2c3d4e5f6   nginx:alpine   "/docker-entrypoint.…"   5 minutes ago   Up 5 minutes   0.0.0.0:80->80/tcp   web_1<br>
b2c3d4e5f6a1   mysql:8.0      "docker-entrypoint.s…"   5 minutes ago   Up 5 minutes   3306/tcp             db_1
                        </div>
                    </div>
                    <div class="terminal-input-wrapper d-flex p-3 pt-0 align-items-center bg-transparent">
                        <span class="terminal-prompt me-2 font-monospace" style="font-size: 13.5px;">root@levelup:~$</span>
                        <input type="text" class="flex-grow-1 bg-transparent border-0 text-white font-monospace shadow-none" id="terminalInput" autocomplete="off" autofocus style="font-size: 13.5px; outline: none;">
                    </div>
                    
                    <!-- Loading Overlay -->
                    <div class="terminal-loader position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 d-flex justify-content-center align-items-center d-none" id="terminalLoader" style="backdrop-filter: blur(2px);">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
            </main>

            <!-- Right Pane: AI Assistant -->
            <aside class="lab-sidebar-right bg-surface border-start border-secondary border-opacity-25 d-flex flex-column" style="width: 350px; min-width: 350px;" id="sidebarRight">
                <div class="p-3 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center bg-surface-alt">
                    <div class="d-flex align-items-center gap-3">
                        <div class="ai-avatar rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px;">
                            <i class="bi bi-robot fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-white fs-sm">A.I. Mentor</h6>
                            <div class="text-success fw-bold" style="font-size: 10px;"><i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i>Online</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-dark border-secondary text-light dropdown-toggle" style="font-size: 11px;" data-bs-toggle="dropdown">
                                <i class="bi bi-cpu me-1"></i> Guided
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm bg-dark border-secondary">
                                <li><a class="dropdown-item text-white active" href="#">Guided (แนะนำทีละสเต็ป)</a></li>
                                <li><a class="dropdown-item text-white" href="#">Expert (ให้แค่ Hint)</a></li>
                            </ul>
                        </div>
                        <button class="btn-close btn-close-white d-lg-none" id="btnCloseRight"></button>
                    </div>
                </div>
                
                <div class="ai-chat-body flex-grow-1 p-3 overflow-auto custom-scrollbar d-flex flex-column gap-3" id="aiChatContainer">
                    <!-- Initial AI Message -->
                    <div class="d-flex gap-2 ai-message">
                        <div class="ai-bubble p-3 rounded-4 rounded-top-0 w-100" style="font-size: 13.5px; line-height: 1.6;">
                            <?= nl2br(htmlspecialchars($initialAiMessage)) ?>
                        </div>
                    </div>
                </div>
                
                <div class="p-3 border-top border-secondary border-opacity-25 bg-surface-alt">
                    <button class="btn btn-outline-info w-100 mb-3 btn-sm rounded-pill fw-bold text-info" onclick="triggerAiAnalyze()">
                        <i class="bi bi-magic me-1"></i> Scan Code for Errors
                    </button>
                    <form id="aiLabChatForm" class="d-flex gap-2 position-relative">
                        <input type="text" class="form-control form-control-sm rounded-pill bg-dark border-secondary text-white shadow-none ps-3 pe-5" placeholder="Ask AI Mentor..." id="aiChatInput" autocomplete="off">
                        <button type="submit" class="btn btn-primary rounded-circle position-absolute end-0 top-50 translate-middle-y me-1 d-flex align-items-center justify-content-center" style="width: 26px; height: 26px; border: none;">
                            <i class="bi bi-send-fill text-white" style="font-size: 12px;"></i>
                        </button>
                    </form>
                </div>
            </aside>

        </div>
    </div>

    <!-- Modal: Achievement (Gamified) -->
    <div class="modal fade" id="achievementModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content gamified rounded-4 text-center p-4 border-0">
                <div class="modal-body py-4">
                    <div class="display-1 text-warning mb-4 jump-anim" style="text-shadow: 0 0 30px rgba(245, 158, 11, 0.5);">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <h2 class="fw-bold text-white mb-2" style="font-family: var(--font-code);">MISSION ACCOMPLISHED!</h2>
                    <p class="text-info fw-bold mb-4">+<?= $labData['xp_reward'] ?> XP <span class="text-light ms-2">| Docker Network Master</span></p>
                    
                    <div class="bg-dark p-3 rounded-3 text-start mb-4 border border-secondary border-opacity-50" style="font-size: 13.5px; line-height: 1.6;">
                        <h6 class="fw-bold text-primary mb-2"><i class="bi bi-stars"></i> A.I. Evaluation:</h6>
                        <span class="text-light">ยอดเยี่ยม Agent! คุณตั้งค่า Docker Network เชื่อมระหว่าง Web และ DB ได้อย่างสมบูรณ์แบบ โค้ดของคุณสะอาดและทำตาม Best Practice</span>
                    </div>
                    
                    <div class="d-flex gap-3 justify-content-center mt-4">
                        <button class="btn btn-outline-light px-4 rounded-pill" data-bs-dismiss="modal">Review Code</button>
                        <a href="courses.php" class="btn btn-lab-primary px-4 rounded-pill">Next Mission <i class="bi bi-chevron-double-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    const labApp = {
        createSnapshot: () => alert('Snapshot saved successfully.'),
        resetEnvironment: () => { if(confirm('Reset all changes?')) location.reload(); },
        submitLab: () => {
            $('.quest-item:not(.completed)').each(function() {
                completeQuest($(this).data('id'));
            });
        },
        toggleTheme: () => {
            let currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            let newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('lab_theme', newTheme);
            labApp.updateThemeIcon(newTheme);
        },
        updateThemeIcon: (theme) => {
            const btn = document.querySelector('.theme-toggle-btn');
            const icon = document.getElementById('themeIcon');
            
            if (theme === 'light') {
                icon.className = 'bi bi-moon-fill';
                if(btn) {
                    btn.classList.remove('btn-outline-light');
                    btn.classList.add('btn-outline-dark');
                }
            } else {
                icon.className = 'bi bi-sun-fill';
                if(btn) {
                    btn.classList.remove('btn-outline-dark');
                    btn.classList.add('btn-outline-light');
                }
            }
        },
        initTheme: () => {
            const savedTheme = localStorage.getItem('lab_theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
            labApp.updateThemeIcon(savedTheme);
        }
    };

    $(document).ready(function() {
        // เริ่มต้น Theme เมื่อหน้าเว็บโหลด
        labApp.initTheme();

        // --- Mobile Drawer Controls ---
        function closeMobileDrawers() {
            $('#sidebarLeft').removeClass('show-mobile');
            $('#sidebarRight').removeClass('show-mobile');
            $('#mobileOverlay').removeClass('active');
        }

        $('#btnToggleLeft').on('click', function() {
            $('#sidebarRight').removeClass('show-mobile');
            $('#sidebarLeft').toggleClass('show-mobile');
            $('#mobileOverlay').toggleClass('active', $('#sidebarLeft').hasClass('show-mobile'));
        });

        $('#btnToggleRight').on('click', function() {
            $('#sidebarLeft').removeClass('show-mobile');
            $('#sidebarRight').toggleClass('show-mobile');
            $('#mobileOverlay').toggleClass('active', $('#sidebarRight').hasClass('show-mobile'));
        });

        $('#btnCloseLeft, #btnCloseRight, #mobileOverlay').on('click', function() {
            closeMobileDrawers();
        });

        // --- Terminal Simulation ---
        $('#terminalInput').on('keypress', function(e) {
            if (e.which == 13) {
                let cmd = $(this).val().trim();
                if(cmd === '') return;
                
                $('#terminalOutput').append(`<div><span class="terminal-prompt">root@levelup:~$</span> <span class="text-white">${cmd}</span></div>`);
                $(this).val('');
                
                $('#terminalLoader').removeClass('d-none');
                
                setTimeout(() => {
                    $('#terminalLoader').addClass('d-none');
                    let response = '';
                    
                    if(cmd === 'docker network ls') {
                        response = `<div class="mb-2 text-light">NETWORK ID     NAME      DRIVER    SCOPE<br>1a2b3c4d5e6f   bridge    bridge    local<br>2b3c4d5e6f7a   host      host      local<br>3c4d5e6f7a8b   none      null      local</div>`;
                        completeQuest(2);
                    } else if (cmd === 'clear') {
                        $('#terminalOutput').html('');
                        return;
                    } else {
                        response = `<div class="mb-2 text-warning">bash: ${cmd}: command not found</div>`;
                    }
                    
                    $('#terminalOutput').append(response);
                    scrollToBottom('#terminalOutput');
                }, 600);
            }
        });

        // --- AI Chat Simulation ---
        $('#aiLabChatForm').on('submit', function(e) {
            e.preventDefault();
            let msg = $('#aiChatInput').val().trim();
            if(msg === '') return;

            $('#aiChatContainer').append(`
                <div class="d-flex gap-2 ai-message justify-content-end mb-2">
                    <div class="bg-primary text-white p-2 px-3 rounded-4 rounded-top-0 fs-sm" style="font-size: 13.5px;">${msg}</div>
                </div>
            `);
            $('#aiChatInput').val('');
            scrollToBottom('#aiChatContainer');

            let typingId = 'typing-' + Date.now();
            $('#aiChatContainer').append(`
                <div class="d-flex gap-2 ai-message mb-2" id="${typingId}">
                    <div class="ai-bubble p-3 rounded-4 rounded-top-0 d-flex align-items-center">
                        <div class="typing-indicator"><span></span><span></span><span></span></div>
                    </div>
                </div>
            `);
            scrollToBottom('#aiChatContainer');

            setTimeout(() => {
                $('#' + typingId).remove();
                $('#aiChatContainer').append(`
                    <div class="d-flex gap-2 ai-message mb-2">
                        <div class="ai-bubble p-3 rounded-4 rounded-top-0 fs-sm w-100" style="font-size: 13.5px;">
                            ระบบตรวจพบว่าคุณกำลังพยายามพิมพ์ <code>${msg}</code> <br>ลองตรวจสอบในแท็บ <b class="text-info">docker-compose.yml</b> ดูสิครับว่ามีการตั้งค่า networks หรือยัง?
                        </div>
                    </div>
                `);
                scrollToBottom('#aiChatContainer');
            }, 1500);
        });
    });

    function scrollToBottom(selector) {
        $(selector).scrollTop($(selector)[0].scrollHeight);
    }

    function completeQuest(id) {
        let $quest = $(`.quest-item[data-id="${id}"]`);
        if(!$quest.hasClass('completed')) {
            $quest.addClass('completed');
            $quest.find('.quest-checkbox').html('<i class="bi bi-check-lg text-white"></i>');
            
            let total = $('.quest-item').length;
            let completed = $('.quest-item.completed').length;
            let percent = (completed / total) * 100;
            
            $('#labProgressBar').css('width', percent + '%');
            $('#labProgressText').text(Math.round(percent) + '%');
            $('#taskCount').text(`${completed}/${total}`);
            
            if(completed === total) {
                setTimeout(() => {
                    var achievementModal = new bootstrap.Modal(document.getElementById('achievementModal'));
                    achievementModal.show();
                }, 1000);
            }
        }
    }

    function triggerAiAnalyze() {
        $('#aiChatInput').val('ช่วยตรวจสอบโค้ดให้หน่อย');
        $('#aiLabChatForm').submit();
    }
</script>

<?php
// 📌 ดึง Footer จาก includes/footer.php
require_once $ROOT_PATH . 'includes/footer.php';
?>