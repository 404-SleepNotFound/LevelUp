<?php
$ROOT_PATH = '../';
session_start();

// ==================================================================
// 1. จำลองข้อมูลจาก Database (Gamification & Challenge Data)
// ==================================================================
$challenge = [
    'title' => 'Two Sum (Optimized)',
    'difficulty' => 'Hard',
    'time_limit' => 'O(n)'
];

$user_stats = [
    'energy' => 85,
    'max_energy' => 100,
];

// ==================================================================
// 2. ดึงข้อมูลโปรไฟล์จากระบบหลัก (อ้างอิงจาก welcome-header)
// ==================================================================
global $navUser;

// ถ้าระบบหลักประกาศ $navUser ไว้แล้ว
if (isset($navUser) && is_array($navUser)) {
    $user_id       = $navUser['user_id'] ?? 'N';
    $user_username = $navUser['username'] ?? 'Player One';
    $user_avatar   = $navUser['avatar'] ?? 'https://ui-avatars.com/api/?name=Player+One';
    $user_level    = $navUser['level'] ?? '12';
    $user_exp      = $navUser['exp'] ?? '150';
} 
// [Smart Fallback] ดึงจาก Session
else {
    $sessionData   = $_SESSION['user_login'] ?? null;
    $user_id       = $sessionData['user_id'] ?? ($_SESSION['user_id'] ?? 'N');
    $user_username = $sessionData['name'] ?? ($_SESSION['username'] ?? 'Player One');
    $user_avatar   = $sessionData['img'] ?? ($_SESSION['user_picture'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=PlayerOne&backgroundColor=141b2d');
    $user_level    = $sessionData['level'] ?? ($_SESSION['user_level'] ?? ($_SESSION['level'] ?? '12'));
    $user_exp      = $sessionData['exp'] ?? ($_SESSION['user_exp'] ?? ($_SESSION['exp'] ?? '150'));
}
$user_coins = $_SESSION['coins'] ?? '20';

include '../includes/header.php';
?>

<!-- นำเข้า Font & Library -->
<link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Kanit:wght@300;400;500;600&family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.38.0/min/vs/loader.min.js"></script>

<style>
    /* ==================================================================
       CSS VARIABLES: DARK MODE (Default)
       ================================================================== */
    :root, html[data-theme="dark"] {
        --lab-bg-base: #030712;
        --lab-surface: rgba(15, 23, 42, 0.85);
        --lab-surface-solid: #0f172a;
        --lab-border: rgba(56, 189, 248, 0.25);
        --lab-primary: #38bdf8;
        --lab-accent: #8b5cf6;
        --lab-success: #10b981;
        --lab-danger: #f43f5e;
        --lab-text-main: #ffffff;
        --lab-text-muted: #cbd5e1;
        
        /* ตัวแปรสี Component ย่อยสำหรับสลับธีม */
        --lab-pill-bg: #0b1120;
        --lab-pill-border: rgba(255, 255, 255, 0.15);
        --lab-pill-divider: rgba(255, 255, 255, 0.2);
        --lab-console-bg: #000000;
        --lab-code-bg: rgba(0,0,0,0.6);
        --lab-code-border: rgba(255,255,255,0.15);
        --lab-hint-bg: linear-gradient(180deg, rgba(139, 92, 246, 0.1) 0%, rgba(0,0,0,0.3) 100%);
        --lab-select-bg: #1e293b;
        --lab-select-text: #ffffff;
        --lab-tab-bg: rgba(0, 0, 0, 0.3);
        --lab-ai-bubble-bg: rgba(139, 92, 246, 0.15);
        --lab-grid-line: rgba(255, 255, 255, 0.02);
    }

    /* ==================================================================
       CSS VARIABLES: LIGHT MODE
       ================================================================== */
    html[data-theme="light"] {
        --lab-bg-base: #f8fafc;
        --lab-surface: rgba(255, 255, 255, 0.95);
        --lab-surface-solid: #ffffff;
        --lab-border: rgba(99, 102, 241, 0.2);
        --lab-primary: #0284c7;
        --lab-accent: #6366f1;
        --lab-success: #059669;
        --lab-danger: #e11d48;
        --lab-text-main: #0f172a;
        --lab-text-muted: #64748b;

        /* ตัวแปรสี Component ย่อยสำหรับสลับธีม (โหมดสว่าง) */
        --lab-pill-bg: #ffffff;
        --lab-pill-border: rgba(99, 102, 241, 0.2);
        --lab-pill-divider: #e2e8f0;
        --lab-console-bg: #f1f5f9;
        --lab-code-bg: #f1f5f9;
        --lab-code-border: #e2e8f0;
        --lab-hint-bg: linear-gradient(180deg, rgba(99, 102, 241, 0.05) 0%, rgba(255,255,255,1) 100%);
        --lab-select-bg: #ffffff;
        --lab-select-text: #0f172a;
        --lab-tab-bg: #f8fafc;
        --lab-ai-bubble-bg: #ffffff;
        --lab-grid-line: rgba(0, 0, 0, 0.03);
    }

    body {
        background-color: var(--lab-bg-base) !important;
        background-image: 
            radial-gradient(circle at 50% 0%, rgba(139, 92, 246, 0.1) 0%, transparent 50%),
            linear-gradient(var(--lab-grid-line) 1px, transparent 1px),
            linear-gradient(90deg, var(--lab-grid-line) 1px, transparent 1px);
        background-size: 100% 100%, 30px 30px, 30px 30px;
        color: var(--lab-text-main) !important;
        font-family: 'Kanit', sans-serif;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .text-muted { color: var(--lab-text-muted) !important; }
    .text-adaptive { color: var(--lab-text-main) !important; }
    .font-outfit { font-family: 'Outfit', sans-serif; }
    .font-mono { font-family: 'Fira Code', monospace; }

    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: var(--lab-border); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: var(--lab-primary); }

    /* Layout & Glassmorphism */
    .coding-lab-layout { height: calc(100vh - 70px); overflow: hidden; }
    
    .ide-toolbar {
        background: var(--lab-surface-solid);
        border-bottom: 1px solid var(--lab-border);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        height: 64px;
        z-index: 10;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }
    
    .ide-panel-left, .ide-panel-right {
        background: var(--lab-surface);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-color: var(--lab-border) !important;
        transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.3s ease, left 0.3s ease, right 0.3s ease;
    }

    .ide-panel-bottom {
        background: var(--lab-surface-solid);
        border-top: 1px solid var(--lab-border);
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    /* Buttons */
    .btn-lab-primary {
        background: linear-gradient(135deg, var(--lab-primary), var(--lab-accent));
        color: white !important;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        letter-spacing: 0.5px;
        box-shadow: 0 0 15px rgba(56, 189, 248, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-transform: uppercase;
    }
    .btn-lab-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 0 25px rgba(139, 92, 246, 0.4);
    }
    
    .btn-lab-outline {
        background: transparent;
        border: 1px solid var(--lab-border);
        color: var(--lab-text-main);
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    .btn-lab-outline:hover {
        background: rgba(99, 102, 241, 0.1);
        border-color: var(--lab-primary);
        color: var(--lab-primary);
    }

    /* Top Right Profile Pill */
    .hud-profile-pill {
        background: var(--lab-pill-bg);
        border: 1px solid var(--lab-pill-border);
        border-radius: 50px;
        padding: 4px 16px 4px 4px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .hud-profile-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid var(--lab-border);
        object-fit: cover;
    }

    .hud-profile-divider {
        width: 1px;
        height: 16px;
        background: var(--lab-pill-divider);
        transition: background 0.3s ease;
    }

    /* Difficulty & Energy */
    .difficulty-badge {
        background: rgba(244, 63, 94, 0.15);
        color: var(--lab-danger);
        border: 1px solid rgba(244, 63, 94, 0.4);
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .energy-container {
        width: 100px;
        height: 6px;
        background: rgba(0,0,0,0.2);
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid rgba(251, 191, 36, 0.3);
    }
    .energy-fill {
        height: 100%;
        background: linear-gradient(90deg, #f59e0b, #fde047);
        box-shadow: 0 0 8px rgba(251, 191, 36, 0.8);
    }

    /* Tabs & Content */
    .lab-tabs { 
        border-bottom: 1px solid var(--lab-border); 
        background: var(--lab-tab-bg); 
        transition: background 0.3s ease;
    }
    .lab-tabs .nav-link { 
        color: var(--lab-text-muted); 
        border: none; 
        padding: 10px 20px; 
        font-size: 0.9rem; 
        border-top: 2px solid transparent; 
        border-radius: 0; 
    }
    .lab-tabs .nav-link:hover { color: var(--lab-text-main); }
    .lab-tabs .nav-link.active { 
        color: var(--lab-primary); 
        border-top-color: var(--lab-primary); 
        background: transparent; 
        font-weight: 500; 
    }
    .problem-desc code { 
        background: rgba(99, 102, 241, 0.1); 
        padding: 2px 6px; 
        border-radius: 4px; 
        font-family: 'Fira Code', monospace; 
        font-size: 0.85em; 
        color: var(--lab-accent); 
    }
    .ai-bubble { 
        background: var(--lab-ai-bubble-bg); 
        border: 1px solid rgba(139, 92, 246, 0.3); 
        border-radius: 0 12px 12px 12px; 
        padding: 12px; 
        font-size: 0.95rem; 
        line-height: 1.6; 
        color: var(--lab-text-main); 
        transition: all 0.3s ease;
    }
    .lab-select { 
        background-color: var(--lab-select-bg) !important; 
        border: 1px solid var(--lab-border); 
        color: var(--lab-select-text) !important; 
        transition: all 0.3s ease;
    }
    .lab-example-box {
        background: var(--lab-code-bg);
        border: 1px solid var(--lab-code-border);
        transition: all 0.3s ease;
    }
    .lab-hint-box {
        background: var(--lab-hint-bg);
        border: 1px solid rgba(139, 92, 246, 0.4);
        transition: all 0.3s ease;
    }

    /* ฉากหลังสไลด์เมนูมือถือ (Overlay Backdrop) */
    .lab-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 1040;
        display: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .lab-backdrop.show {
        display: block;
        opacity: 1;
    }

    /* ==================================================================
       📱 MOBILE RESPONSIBLENESS (SCREEN <= 991px)
       ================================================================== */
    @media (max-width: 991.98px) {
        .coding-lab-layout {
            height: auto;
            min-height: calc(100vh - 60px);
        }

        .ide-toolbar {
            padding-left: 12px !important;
            padding-right: 12px !important;
            height: auto;
            min-height: 56px;
            padding-top: 6px;
            padding-bottom: 6px;
        }

        .quest-title-text {
            max-width: 130px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ซ่อนข้อความปุ่มในมือถือเพื่อประหยัดพื้นที่ */
        .btn-text-hide { display: none; }

        /* ปรับตำแหน่ง Side Panel ซ้ายให้เลื่อนสไลด์มาจากซ้ายมือ */
        .ide-panel-left {
            position: fixed !important;
            top: 0;
            left: -100%;
            width: 88% !important;
            max-width: 380px;
            height: 100vh;
            z-index: 1050;
            box-shadow: 10px 0 30px rgba(0, 0, 0, 0.5);
            display: flex !important;
        }
        .ide-panel-left.mobile-open {
            left: 0 !important;
        }

        /* ปรับตำแหน่ง Side Panel ขวา (AI) ให้เลื่อนสไลด์มาจากขวามือ */
        .ide-panel-right {
            position: fixed !important;
            top: 0;
            right: -100%;
            width: 88% !important;
            max-width: 380px;
            height: 100vh;
            z-index: 1050;
            box-shadow: -10px 0 30px rgba(0, 0, 0, 0.5);
            display: flex !important;
        }
        .ide-panel-right.mobile-open {
            right: 0 !important;
        }

        /* Editor พื้นที่เขียนโค้ดเต็มจอในมือถือ */
        .ide-workspace {
            height: calc(100vh - 60px);
            position: relative;
        }

        .ide-panel-center {
            width: 100% !important;
            height: 100%;
        }

        #languageSelect {
            width: 100px !important;
            font-size: 0.8rem;
        }
    }
</style>

<!-- ฉากหลังเมื่อเปิดเมนูด้านข้างในมือถือ -->
<div class="lab-backdrop" id="labBackdrop"></div>

<div class="coding-lab-layout d-flex flex-column">
    
    <!-- IDE Toolbar / HUD -->
    <header class="ide-toolbar px-3 px-md-4 d-flex justify-content-between align-items-center flex-shrink-0">
        <div class="d-flex align-items-center gap-2 gap-md-3">
            <button class="btn btn-sm btn-lab-outline d-lg-none" id="toggleLeftPanel" title="ดูโจทย์">
                <i class="bi bi-layout-sidebar"></i>
            </button>
            <div class="d-flex flex-column">
                <div class="d-flex align-items-center gap-2 mb-0 mb-md-1">
                    <span class="difficulty-badge text-uppercase" style="font-size: 0.65rem;"><i class="bi bi-fire"></i> <?= htmlspecialchars($challenge['difficulty']) ?></span>
                    <span class="fs-xs text-muted font-outfit fw-bold tracking-wider d-none d-sm-inline">QUEST</span>
                </div>
                <h5 class="mb-0 font-outfit fw-bold text-adaptive fs-6 quest-title-text"><?= htmlspecialchars($challenge['title']) ?></h5>
            </div>
        </div>
        
        <div class="d-flex align-items-center gap-2 gap-md-3">
            
            <!-- Profile Pill (Adaptive Theme) -->
            <div class="d-none d-xl-flex hud-profile-pill font-outfit me-2">
                <div class="d-flex align-items-center gap-2" title="User Profile">
                    <img src="<?= htmlspecialchars($user_avatar) ?>" alt="avatar" class="hud-profile-avatar">
                    <span class="fs-6 fw-bold text-adaptive" style="line-height: 1;">Lv.<?= htmlspecialchars($user_level) ?></span>
                </div>
                
                <div class="hud-profile-divider"></div>
                
                <div class="d-flex align-items-center gap-1" title="Experience Points">
                    <i class="bi bi-star-fill" style="color: #facc15; font-size: 0.9rem;"></i>
                    <span class="fs-6 fw-bold" style="color: #22c55e; line-height: 1;"><?= htmlspecialchars($user_exp) ?></span>
                </div>
                
                <div class="d-flex align-items-center gap-1 ms-1" title="Coins">
                    <i class="bi bi-coin" style="color: #f59e0b; font-size: 0.95rem;"></i>
                    <span class="fs-6 fw-bold" style="color: #f59e0b; line-height: 1;"><?= htmlspecialchars($user_coins) ?></span>
                </div>
            </div>
            
            <div class="d-none d-md-flex align-items-center gap-2 me-2">
                <i class="bi bi-cloud-check text-success fs-5" id="saveStatus" title="Auto-saved"></i>
            </div>
            
            <select class="form-select form-select-sm font-outfit lab-select" id="languageSelect" style="width: 120px;">
                <option value="python">Python 3</option>
                <option value="javascript">JavaScript</option>
                <option value="java">Java</option>
                <option value="cpp">C++</option>
            </select>
            
            <button class="btn btn-sm btn-lab-outline px-2 px-md-3 font-outfit fw-bold" id="btnRun">
                <i class="bi bi-play-fill text-success"></i> <span class="btn-text-hide">RUN</span>
            </button>
            <button class="btn btn-sm btn-lab-primary px-3 px-md-4 font-outfit" id="btnSubmit">
                <i class="bi bi-rocket-takeoff me-1"></i> <span class="btn-text-hide">SUBMIT</span>
            </button>
            
            <button class="btn btn-sm btn-lab-outline position-relative ms-1 text-primary" style="border-color: var(--lab-primary);" id="toggleAiPanel" title="AI Copilot">
                <i class="bi bi-robot fs-6"></i>
                <span class="position-absolute top-0 start-100 translate-middle p-1 rounded-circle" style="background-color: var(--lab-primary); box-shadow: 0 0 10px var(--lab-primary);"></span>
            </button>
        </div>
    </header>

    <!-- Main Workspace -->
    <div class="ide-workspace d-flex flex-grow-1 overflow-hidden">
        
        <!-- Left Panel: Problem Description -->
        <aside class="ide-panel-left border-end d-flex flex-column transition-all" style="width: 400px;" id="leftPanel">
            <div class="d-flex justify-content-between align-items-center border-bottom d-lg-none p-2 px-3">
                <span class="fw-bold font-outfit text-adaptive"><i class="bi bi-journal-text me-2"></i>รายละเอียดโจทย์</span>
                <button class="btn btn-sm btn-icon text-muted" id="closeLeftPanelMobile"><i class="bi bi-x-lg fs-5"></i></button>
            </div>
            <ul class="nav nav-tabs lab-tabs flex-shrink-0" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active font-outfit" data-bs-toggle="tab" data-bs-target="#tab-desc" type="button"><i class="bi bi-file-earmark-text me-1"></i> Description</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link font-outfit" data-bs-toggle="tab" data-bs-target="#tab-files" type="button"><i class="bi bi-folder2-open me-1"></i> Files</button>
                </li>
            </ul>
            
            <div class="tab-content flex-grow-1 overflow-y-auto custom-scrollbar p-3 p-md-4">
                <div class="tab-pane fade show active problem-desc" id="tab-desc" role="tabpanel">
                    <h3 class="fw-bold mb-3 font-outfit text-adaptive fs-5 fs-md-3"><?= htmlspecialchars($challenge['title']) ?></h3>
                    
                    <p class="fs-sm lh-lg text-muted">กำหนดให้ Array ของตัวเลข <code>nums</code> และตัวเลขเป้าหมาย <code>target</code> จงหา index ของตัวเลข 2 ตัวใน Array ที่บวกกันแล้วได้เท่ากับเป้าหมาย</p>
                    
                    <div class="p-3 mb-4 rounded-3 d-flex gap-3 align-items-start" style="background: rgba(244, 63, 94, 0.05); border: 1px solid rgba(244, 63, 94, 0.2);">
                        <i class="bi bi-shield-exclamation fs-4 text-danger"></i>
                        <div class="fs-sm">
                            <strong class="text-danger font-outfit">Mission Constraints:</strong><br>
                            <span class="text-muted">ห้ามใช้ element เดิมซ้ำ และต้องมี Time Complexity ที่ <code><?= htmlspecialchars($challenge['time_limit']) ?></code></span>
                        </div>
                    </div>
                    
                    <div class="rounded-3 p-3 mb-4 fs-sm font-mono lab-example-box">
                        <div class="text-adaptive mb-2"><i class="bi bi-terminal me-2 text-muted"></i>Example 1:</div>
                        <div class="mb-1"><span class="text-muted">Input:</span> <span class="text-adaptive">nums = [2,7,11,15], target = 9</span></div>
                        <div><span class="text-muted">Output:</span> <span class="text-success">[0,1]</span></div>
                    </div>
                    
                    <!-- AI Hints -->
                    <div class="ai-hint-section p-3 rounded-3 position-relative overflow-hidden lab-hint-box">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fs-sm fw-bold m-0 font-outfit text-adaptive" style="color: var(--lab-accent) !important;"><i class="bi bi-magic me-1"></i> Hint Crystal</h6>
                            <!-- Visual Energy Bar -->
                            <div class="d-flex align-items-center gap-2" title="Energy: <?= $user_stats['energy'] ?>/<?= $user_stats['max_energy'] ?>">
                                <i class="bi bi-lightning-charge-fill text-warning" style="font-size: 0.8rem;"></i>
                                <div class="energy-container">
                                    <div class="energy-fill" style="width: <?= ($user_stats['energy'] / $user_stats['max_energy']) * 100 ?>%;"></div>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-lab-outline w-100 d-flex justify-content-between align-items-center bg-transparent border">
                            <span class="fs-xs font-outfit text-adaptive">Unlock Level 1 Hint</span>
                            <span class="fw-bold fs-xs text-warning">-5 Energy</span>
                        </button>
                    </div>
                </div>
                
                <div class="tab-pane fade" id="tab-files" role="tabpanel">
                    <ul class="list-unstyled fs-sm mt-2 font-mono">
                        <li class="p-2 rounded mb-1 cursor-pointer" style="background: rgba(56, 189, 248, 0.1); border-left: 3px solid var(--lab-primary);">
                            <i class="bi bi-filetype-py fs-5 me-2 text-primary"></i> <span class="text-adaptive">main.py</span>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Center Panel: Editor -->
        <main class="ide-panel-center flex-grow-1 d-flex flex-column position-relative" style="background-color: var(--lab-surface-solid); transition: background-color 0.3s ease;">
            <div class="editor-wrapper flex-grow-1 position-relative" id="monaco-editor" style="min-height: 45%;">
                <div id="editorLoading" class="position-absolute top-50 start-50 translate-middle text-muted text-center z-2">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 2.5rem; height: 2.5rem;"></div>
                    <div class="font-outfit fw-bold tracking-widest text-uppercase text-adaptive" style="letter-spacing: 2px;">Booting Engine...</div>
                </div>
            </div>

            <div class="ide-resizer-h" style="height: 3px; background: var(--lab-border); cursor: row-resize;"></div>

            <!-- Bottom Panel: Console -->
            <div class="ide-panel-bottom d-flex flex-column" style="height: 35%;">
                <ul class="nav nav-tabs lab-tabs flex-shrink-0 px-3" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active font-outfit" data-bs-toggle="tab" data-bs-target="#tab-terminal"><i class="bi bi-terminal-fill me-1"></i> Output</button>
                    </li>
                </ul>
                <div class="tab-content flex-grow-1 overflow-hidden custom-scrollbar" style="background: var(--lab-console-bg); transition: background 0.3s ease;">
                    <div class="tab-pane fade show active h-100 p-3" id="tab-terminal">
                        <div class="console-output h-100 font-mono fs-sm" id="consoleOutput" style="color: var(--lab-success); overflow-y: auto;">
                            <span class="text-muted">14:03:42</span> <span class="text-primary">[SYS]</span> LevelUp Engine v3.1<br>
                            <span class="text-muted">14:03:42</span> <span class="text-primary">[SYS]</span> Workspace isolated. Ready for input.<br>
                            <span class="text-muted">14:03:42</span> <span class="text-warning">> </span><span class="cursor-blink">_</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Right Panel: AI Copilot -->
        <aside class="ide-panel-right border-start d-flex flex-column transition-all" style="width: 380px; display: none;" id="rightPanel">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="border-color: var(--lab-border) !important;">
                <h6 class="mb-0 fw-bold font-outfit d-flex align-items-center gap-2" style="color: var(--lab-accent);">
                    <i class="bi bi-stars fs-5"></i> LevelUp Copilot
                </h6>
                <button class="btn btn-sm btn-icon text-muted" id="closeAiPanel" style="border: none; background: transparent;"><i class="bi bi-x-lg fs-5"></i></button>
            </div>
            
            <div class="flex-grow-1 overflow-y-auto p-3 p-md-4 custom-scrollbar" id="aiFeedbackArea">
                <div class="d-flex gap-3 mb-4">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(139, 92, 246, 0.15); border: 1px solid rgba(139, 92, 246, 0.4); color: var(--lab-accent);">
                            <i class="bi bi-robot"></i>
                        </div>
                    </div>
                    <div class="ai-bubble text-adaptive">
                        <strong class="d-block mb-1 font-outfit" style="color: var(--lab-accent);">Copilot</strong>
                        ระบบพร้อมแล้วครับ <?= htmlspecialchars($user_username) ?>!
                    </div>
                </div>
            </div>
            
            <!-- Chat Input -->
            <div class="p-3 border-top" style="border-color: var(--lab-border) !important; background: var(--lab-surface-solid);">
                <div class="input-group">
                    <input type="text" class="form-control form-control-sm bg-transparent text-adaptive fs-sm" style="border: 1px solid var(--lab-border);" placeholder="ถามอะไรเพิ่มเติม...">
                    <button class="btn btn-sm" style="background: var(--lab-accent); color: white;"><i class="bi bi-send-fill"></i></button>
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
    const leftPanel = document.getElementById('leftPanel');
    const rightPanel = document.getElementById('rightPanel');
    const backdrop = document.getElementById('labBackdrop');

    const toggleLeftBtn = document.getElementById('toggleLeftPanel');
    const closeLeftBtnMobile = document.getElementById('closeLeftPanelMobile');
    const toggleAiBtn = document.getElementById('toggleAiPanel');
    const closeAiBtn = document.getElementById('closeAiPanel');

    // ฟังก์ชันปิด Drawer ทั้งหมดในมือถือ
    function closeMobileDrawers() {
        leftPanel.classList.remove('mobile-open');
        rightPanel.classList.remove('mobile-open');
        backdrop.classList.remove('show');
    }

    // Toggle Left Panel
    toggleLeftBtn.addEventListener('click', () => {
        if (window.innerWidth <= 991.98) {
            leftPanel.classList.toggle('mobile-open');
            rightPanel.classList.remove('mobile-open');
            backdrop.classList.toggle('show', leftPanel.classList.contains('mobile-open'));
        } else {
            leftPanel.style.display = leftPanel.style.display === 'none' ? 'flex' : 'none';
        }
    });

    if(closeLeftBtnMobile) {
        closeLeftBtnMobile.addEventListener('click', closeMobileDrawers);
    }

    // Toggle AI Panel
    toggleAiBtn.addEventListener('click', () => {
        if (window.innerWidth <= 991.98) {
            rightPanel.classList.toggle('mobile-open');
            leftPanel.classList.remove('mobile-open');
            backdrop.classList.toggle('show', rightPanel.classList.contains('mobile-open'));
        } else {
            rightPanel.style.display = (rightPanel.style.display === 'none' || rightPanel.style.display === '') ? 'flex' : 'none';
        }
    });

    closeAiBtn.addEventListener('click', () => {
        if (window.innerWidth <= 991.98) {
            closeMobileDrawers();
        } else {
            rightPanel.style.display = 'none';
        }
    });

    // ปิดเมนูด้านข้างเมื่อกดที่ฉากหลัง
    backdrop.addEventListener('click', closeMobileDrawers);

    // Cursor Blink
    setInterval(() => { 
        const cursor = document.querySelector('.cursor-blink'); 
        if(cursor) cursor.style.opacity = cursor.style.opacity === '0' ? '1' : '0'; 
    }, 500);
</script>

<?php
$ROOT_PATH = '../';
include '../includes/footer.php';
?>
