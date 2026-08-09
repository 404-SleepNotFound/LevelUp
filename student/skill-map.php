<?php
$ROOT_PATH = '../';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = "Skill Map & Career Intelligence | LevelUp";

// ---------------------------------------------------------
// [PHP] DATA PREPARATION (SESSION INTEGRATION & FALLBACKS)
// ---------------------------------------------------------
$sessionData = (isset($_SESSION['user_login']) && is_array($_SESSION['user_login'])) ? $_SESSION['user_login'] : [];

$userStats = [
    'level'      => $sessionData['level'] ?? ($_SESSION['user_level'] ?? ($_SESSION['level'] ?? 1)),
    'xp'         => $sessionData['user_exp'] ?? ($sessionData['exp'] ?? ($_SESSION['user_exp'] ?? ($_SESSION['exp'] ?? 0))),
    'max_xp'     => $sessionData['max_xp'] ?? ($sessionData['user_max_xp'] ?? ($_SESSION['user_max_xp'] ?? ($_SESSION['max_xp'] ?? 1000))),
    'rank_badge' => $sessionData['rank_badge'] ?? ($_SESSION['rank_badge'] ?? 'bi-shield-shaded'),
    'rank_color' => $sessionData['rank_color'] ?? ($_SESSION['rank_color'] ?? 'text-info'),
    'rank_name'  => $sessionData['rank_name'] ?? ($_SESSION['rank_name'] ?? 'Elite Coder')
];

$careerPaths = [
    ['id' => 'backend', 'name' => 'Backend Architect'],
    ['id' => 'frontend', 'name' => 'Frontend Master'],
    ['id' => 'data', 'name' => 'Data Wizard'],
    ['id' => 'devops', 'name' => 'DevOps Commander']
];

$aiInsights = [
    'match_score' => 72,
    'target_role' => 'Backend Architect'
];

// ดึง Header เข้ามาแสดงผล
include '../includes/header.php';
?>

<!-- CSS Design System สำหรับ Skill Map Matrix (รองรับ Dark & Light Mode) -->
<link rel="stylesheet" href="../assets/css/skill-map.css">
<style>
    /* 🌙 Dark Mode Theme (Default) */
    :root, html[data-theme="dark"] {
        --bg-space: #0B0F19;
        --surface-glass: rgba(21, 27, 43, 0.75);
        --neon-cyan: #00E5FF;
        --neon-purple: #B026FF;
        --neon-green: #10B981;
        --neon-warning: #FFD600;
        --border-glass: rgba(255, 255, 255, 0.08);
        --map-text-main: #E2E8F0;
        --map-text-muted: #94A3B8;
        --xp-bg: #1E293B;
        --control-btn-bg: rgba(15, 23, 42, 0.8);
        --control-btn-text: #FFFFFF;
        --grid-line: rgba(0, 229, 255, 0.03);
        --badge-bg: linear-gradient(135deg, #1E293B, #0F172A);
        --panel-card-bg: rgba(15, 23, 42, 0.6);
        --panel-card-border: rgba(255, 255, 255, 0.1);
    }

    /* ☀️ Light Mode Theme Options */
    html[data-theme="light"] {
        --bg-space: #F8FAFC;
        --surface-glass: rgba(255, 255, 255, 0.88);
        --neon-cyan: #0284C7;
        --neon-purple: #9333EA;
        --neon-green: #059669;
        --neon-warning: #D97706;
        --border-glass: rgba(0, 0, 0, 0.12);
        --map-text-main: #0F172A;
        --map-text-muted: #64748B;
        --xp-bg: #E2E8F0;
        --control-btn-bg: rgba(255, 255, 255, 0.95);
        --control-btn-text: #0F172A;
        --grid-line: rgba(2, 132, 199, 0.08);
        --badge-bg: linear-gradient(135deg, #E2E8F0, #CBD5E1);
        --panel-card-bg: rgba(241, 245, 249, 0.85);
        --panel-card-border: rgba(0, 0, 0, 0.08);
    }

    body {
        background-color: var(--bg-space) !important;
        color: var(--map-text-main) !important;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Grid Hologram Background สำหรับ Map */
    .bg-map {
        background-color: var(--bg-space);
        background-image: 
            linear-gradient(var(--grid-line) 1px, transparent 1px),
            linear-gradient(90deg, var(--grid-line) 1px, transparent 1px);
        background-size: 40px 40px;
        background-position: center center;
        transition: background-color 0.3s ease;
    }

    /* Glassmorphism Panels */
    .glass-panel {
        background: var(--surface-glass);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--border-glass);
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.15);
        z-index: 10;
        color: var(--map-text-main);
        transition: background 0.3s ease, border-color 0.3s ease;
    }

    /* Gamified Topbar */
    .skill-topbar {
        background: var(--surface-glass);
        border-bottom: 1px solid var(--border-glass);
        backdrop-filter: blur(10px);
        transition: background 0.3s ease, border-color 0.3s ease;
    }

    .xp-bar-container {
        width: 150px;
        height: 8px;
        background: var(--xp-bg);
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid var(--border-glass);
    }
    
    .xp-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--neon-purple), var(--neon-cyan));
        box-shadow: 0 0 10px var(--neon-cyan);
    }

    .level-badge {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--badge-bg);
        border: 2px solid var(--neon-cyan);
        box-shadow: 0 0 8px rgba(0, 229, 255, 0.3);
        border-radius: 50%;
        font-weight: 800;
        color: var(--neon-cyan);
    }

    /* Glowing Elements */
    .glow-text-primary {
        color: var(--neon-cyan);
        text-shadow: 0 0 8px rgba(0, 229, 255, 0.3);
    }
    .glow-text-purple {
        color: var(--neon-purple);
        text-shadow: 0 0 8px rgba(176, 38, 255, 0.3);
    }

    /* Floating Panels Configuration */
    .skill-sidebar {
        width: 350px;
        height: calc(100vh - 75px);
        position: absolute;
        top: 0;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s, background 0.3s ease;
    }
    .left-panel { left: 0; border-right: 1px solid var(--border-glass); }
    .right-panel { right: 0; border-left: 1px solid var(--border-glass); }

    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(150, 150, 150, 0.3); border-radius: 10px; }

    /* SVG Circular Chart */
    .circular-chart { display: block; margin: 0 auto; max-width: 80%; max-height: 250px; }
    .circle-bg { fill: none; stroke: var(--xp-bg); stroke-width: 2.5; }
    .circle { fill: none; stroke-width: 2.5; stroke-linecap: round; filter: drop-shadow(0 0 4px var(--neon-green)); }

    /* Controls & Legend */
    .map-controls button {
        background: var(--control-btn-bg);
        border: 1px solid var(--border-glass);
        color: var(--control-btn-text);
        transition: all 0.2s;
    }
    .map-controls button:hover {
        background: var(--neon-cyan);
        color: #ffffff;
        box-shadow: 0 0 10px var(--neon-cyan);
    }
    
    .map-legend { background: var(--surface-glass); backdrop-filter: blur(8px); border: 1px solid var(--border-glass); }
    .legend-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }

    /* Adaptability Overrides for Light Mode */
    html[data-theme="light"] .text-light,
    html[data-theme="light"] .text-white {
        color: var(--map-text-main) !important;
    }
    html[data-theme="light"] .bg-dark {
        background-color: var(--panel-card-bg) !important;
        border-color: var(--panel-card-border) !important;
    }
    html[data-theme="light"] #careerSelector {
        background-color: var(--surface-glass) !important;
        color: var(--map-text-main) !important;
    }
</style>

<div class="skill-map-wrapper position-relative overflow-hidden" style="height: 100vh;">
    
    <!-- Player HUD / Topbar -->
    <header class="skill-topbar px-4 py-2 d-flex justify-content-between align-items-center" style="height: 75px;">
        <!-- Left: Branding & Role -->
        <div class="d-flex align-items-center gap-4">
            <h4 class="mb-0 fw-black glow-text-primary text-uppercase fs-5" style="letter-spacing: 1px;">
                <i class="bi bi-hexagon-fill me-2"></i>Skill Matrix
            </h4>
            
            <div class="vr bg-secondary opacity-25"></div>
            
            <!-- Career Selector -->
            <div class="d-flex align-items-center gap-2">
                <span class="fs-xs text-uppercase fw-bold opacity-75">Target Class:</span>
                <select class="form-select form-select-sm lu-select bg-dark text-info border-info border-opacity-25 rounded-3 fw-bold" id="careerSelector" style="width: 200px; box-shadow: 0 0 5px rgba(0,229,255,0.2);">
                    <?php
$ROOT_PATH = '../';
foreach($careerPaths as $path): ?>
                        <option value="<?= $path['id'] ?>"><?= $path['name'] ?></option>
                    <?php
$ROOT_PATH = '../';
endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Right: Player Stats -->
        <div class="d-flex align-items-center gap-4">
            <!-- Rank & Level System -->
            <div class="d-flex align-items-center gap-3 bg-dark bg-opacity-60 px-3 py-1.5 rounded-pill border border-secondary border-opacity-25">
                <div class="d-flex flex-column align-items-end">
                    <span class="fs-xs text-uppercase fw-bold">
                        <i class="bi <?= htmlspecialchars($userStats['rank_badge']) ?> <?= htmlspecialchars($userStats['rank_color']) ?> me-1"></i>
                        <?= htmlspecialchars($userStats['rank_name']) ?>
                    </span>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <div class="xp-bar-container">
                            <?php
$ROOT_PATH = '../';
$maxVal = max(1, (int)$userStats['max_xp']);
                                $currXp = (int)$userStats['xp'];
                                $xpPercent = min(100, max(0, ($currXp / $maxVal) * 100)); 
                            ?>
                            <div class="xp-bar-fill" style="width: <?= $xpPercent ?>%;"></div>
                        </div>
                        <span class="fs-xs fw-medium"><?= is_numeric($userStats['xp']) ? number_format((float)$userStats['xp']) : $userStats['xp'] ?> / <?= is_numeric($userStats['max_xp']) ? number_format((float)$userStats['max_xp']) : $userStats['max_xp'] ?> XP</span>
                    </div>
                </div>
                <!-- Dynamic Level Badge -->
                <div class="level-badge fs-6"><?= htmlspecialchars($userStats['level']) ?></div>
            </div>

            <!-- Actions -->
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-dark border-secondary rounded-3 px-3"><i class="bi bi-share"></i></button>
                <button class="btn btn-sm btn-outline-info rounded-3 px-3 fw-bold"><i class="bi bi-cloud-download me-1"></i> Export Data</button>
            </div>
        </div>
    </header>

    <!-- Map Area (เต็มจอใต้ Header) -->
    <div class="position-relative w-100 bg-map" style="height: calc(100vh - 75px);">
        
        <!-- Center: Interactive Map Canvas -->
        <main class="skill-map-container w-100 h-100 position-absolute top-0 start-0" id="mapWrapper">
            
            <!-- Map Legend -->
            <div class="map-legend position-absolute top-0 start-0 m-4 p-3 rounded-4 shadow-lg z-index-10 fs-xs">
                <h6 class="text-uppercase fw-bold opacity-75 mb-3 fs-xs letter-spacing-1">Node Status</h6>
                <div class="d-flex align-items-center gap-2 mb-2"><span class="legend-dot bg-success" style="box-shadow: 0 0 8px var(--neon-green);"></span> Mastered</div>
                <div class="d-flex align-items-center gap-2 mb-2"><span class="legend-dot bg-warning"></span> In Progress</div>
                <div class="d-flex align-items-center gap-2 mb-2"><span class="legend-dot bg-info" style="box-shadow: 0 0 8px var(--neon-cyan);"></span> AI Recommended</div>
                <div class="d-flex align-items-center gap-2"><span class="legend-dot bg-secondary opacity-50"></span> Locked</div>
            </div>

            <!-- Controls (Zoom/Pan) -->
            <div class="map-controls position-absolute bottom-0 start-0 m-4 d-flex flex-column gap-2 z-index-10">
                <button class="btn shadow rounded-circle d-flex justify-content-center align-items-center p-0" style="width: 45px; height: 45px;" id="btnZoomIn" title="Zoom In"><i class="bi bi-plus-lg fs-5"></i></button>
                <button class="btn shadow rounded-circle d-flex justify-content-center align-items-center p-0" style="width: 45px; height: 45px;" id="btnZoomOut" title="Zoom Out"><i class="bi bi-dash-lg fs-5"></i></button>
                <button class="btn shadow rounded-circle d-flex justify-content-center align-items-center p-0" style="width: 45px; height: 45px;" id="btnResetMap" title="Reset View"><i class="bi bi-crosshair fs-5"></i></button>
            </div>

            <!-- The Draggable/Zoomable Canvas (JS Render Area) -->
            <div class="map-canvas" id="mapCanvas" style="width: 100%; height: 100%; cursor: grab;">
                <!-- SVG Layer for Connection Lines -->
                <svg class="map-svg-layer w-100 h-100 position-absolute top-0 start-0" id="svgLayer"></svg>
                
                <!-- Nodes Layer (HTML Elements) -->
                <div class="map-nodes-layer position-absolute top-0 start-0 w-100 h-100" id="nodesLayer">
                    <!-- Nodes Injected via JS -->
                </div>
            </div>
        </main>

        <!-- Left Panel: Skill Details (Floating Quest Panel) -->
        <aside class="skill-sidebar left-panel glass-panel p-4 d-none flex-column custom-scrollbar overflow-auto" id="skillDetailPanel">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div class="badge bg-dark border border-info text-info text-uppercase mb-1 px-2 py-1 fs-xs" id="sdCategory" style="letter-spacing: 1px;">Category</div>
                <button class="btn btn-sm btn-dark text-light rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" id="btnCloseLeftPanel"><i class="bi bi-x-lg"></i></button>
            </div>
            
            <h2 class="fw-bold mb-2 glow-text-primary" id="sdTitle">Skill Name</h2>
            <p class="text-secondary fs-sm mb-4" id="sdDesc" style="line-height: 1.6;">Description goes here.</p>

            <!-- Mastery Level -->
            <div class="bg-dark bg-opacity-50 p-3 rounded-4 mb-4 border border-info border-opacity-25 relative overflow-hidden">
                <div class="position-absolute top-0 start-0 w-100 h-100 bg-info opacity-10" style="filter: blur(20px);"></div>
                <div class="d-flex justify-content-between mb-2 fs-sm position-relative">
                    <span class="text-uppercase fw-bold fs-xs">Mastery Level: <span class="ms-1" id="sdLevelStr">Intermediate</span></span>
                    <span class="text-info fw-bold font-monospace" id="sdProgressTxt">65%</span>
                </div>
                <div class="progress bg-secondary bg-opacity-50 position-relative" style="height: 6px;">
                    <div class="progress-bar bg-info" id="sdProgressBar" style="width: 65%; box-shadow: 0 0 10px var(--neon-cyan);"></div>
                </div>
            </div>

            <!-- Gamified Stats: Market Demand -> Power Rating -->
            <div class="mb-4">
                <h6 class="fw-bold fs-xs opacity-75 mb-2 text-uppercase" style="letter-spacing: 1px;">Market Power Rating</h6>
                <div class="text-warning fs-5 d-flex gap-1" id="sdMarketDemand">
                    <!-- Stars Injected via JS -->
                </div>
            </div>

            <!-- Prerequisites Quest -->
            <div class="mb-4 flex-grow-1">
                <h6 class="fw-bold fs-xs opacity-75 mb-2 text-uppercase" style="letter-spacing: 1px;"><i class="bi bi-key-fill text-secondary me-1"></i> Required Quests</h6>
                <div class="d-flex flex-wrap gap-2" id="sdPreReq">
                    <!-- Badges Injected via JS -->
                </div>
            </div>

            <!-- Completion Reward -->
            <div class="mb-4">
                 <h6 class="fw-bold fs-xs opacity-75 mb-2 text-uppercase" style="letter-spacing: 1px;"><i class="bi bi-gift-fill text-success me-1"></i> Completion Reward</h6>
                 <div class="d-flex align-items-center gap-2 bg-dark bg-opacity-50 p-2 rounded-3 border border-success border-opacity-25">
                     <span class="badge bg-success bg-opacity-25 text-success border border-success"><i class="bi bi-arrow-up-circle"></i> +500 XP</span>
                     <span class="badge bg-warning bg-opacity-25 text-warning border border-warning"><i class="bi bi-coin"></i> 50 Coins</span>
                 </div>
            </div>

            <div class="mt-auto pt-3 border-top border-secondary border-opacity-25">
                <button class="btn btn-info w-100 rounded-3 py-3 fw-bold text-dark text-uppercase" id="sdActionBtn" style="letter-spacing: 1px; box-shadow: 0 0 15px rgba(0,229,255,0.4);"><i class="bi bi-play-fill fs-5 align-middle me-1"></i> Initialize Training</button>
            </div>
        </aside>

        <!-- Right Panel: AI Career Insights (Oracle Panel) -->
        <aside class="skill-sidebar right-panel glass-panel p-4 d-flex flex-column custom-scrollbar overflow-auto">
            
            <div class="d-flex align-items-center gap-3 mb-4 border-bottom border-secondary border-opacity-25 pb-3">
                <div class="bg-dark border border-purple rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; border-color: var(--neon-purple); box-shadow: 0 0 10px rgba(176,38,255,0.3);">
                    <i class="bi bi-robot fs-4 glow-text-purple"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-uppercase" style="letter-spacing: 1px;">Oracle AI Guide</h6>
                    <span class="fs-xs opacity-75">Path Optimization System</span>
                </div>
            </div>

            <!-- Readiness Score Hologram -->
            <div class="text-center mb-4">
                <p class="opacity-75 fs-xs mb-1 text-uppercase fw-bold">Class Sync Rate</p>
                <h5 class="fw-bold glow-text-primary mb-3" id="aiCareerTitle"><?= htmlspecialchars($aiInsights['target_role']) ?></h5>
                
                <div class="position-relative d-inline-block">
                    <svg viewBox="0 0 36 36" class="circular-chart success" style="width: 140px; height: 140px;">
                        <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <path class="circle" id="careerMatchRing" stroke-dasharray="<?= $aiInsights['match_score'] ?>, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" stroke="#10b981" />
                    </svg>
                    <div class="position-absolute top-50 start-50 translate-middle text-center">
                        <h2 class="fw-bold mb-0" id="careerMatchTxt"><?= $aiInsights['match_score'] ?>%</h2>
                        <span class="fs-xs text-success text-uppercase fw-bold" style="letter-spacing: 1px;">Sync</span>
                    </div>
                </div>
            </div>

            <!-- AI Prediction / Quest Dialogue -->
            <div class="bg-dark bg-opacity-80 border border-purple border-opacity-50 rounded-4 p-3 mb-4 position-relative">
                <div class="position-absolute top-0 start-0 w-100 h-100 pointer-events-none rounded-4" style="box-shadow: inset 0 0 20px rgba(176,38,255,0.1);"></div>
                <div class="d-flex gap-2 mb-2">
                    <i class="bi bi-chat-square-quote text-purple" style="color: var(--neon-purple);"></i>
                    <span class="fs-xs text-uppercase opacity-75 fw-bold">Transmission:</span>
                </div>
                <p class="fs-sm mb-0" id="aiPredictionTxt" style="line-height: 1.6;">
                    "ระบบตรวจพบว่าคุณมีศักยภาพพร้อมก้าวสู่ระดับถัดไป เพื่อเพิ่ม Sync Rate ให้เต็ม 100% แนะนำให้ทำภารกิจ <strong class="text-info">Docker</strong> และ <strong class="text-info">Redis</strong> (ระยะเวลาประเมิน: 3 สัปดาห์)"
                </p>
            </div>

            <!-- Missing Skills (Target Quests) -->
            <h6 class="fw-bold fs-xs opacity-75 mb-3 text-uppercase" style="letter-spacing: 1px;"><i class="bi bi-crosshair text-danger me-1"></i> Priority Targets (Missing Skills)</h6>
            <div class="d-flex flex-column gap-2 mb-4" id="missingSkillsList">
                <!-- Injected via JS -->
            </div>

        </aside>

    </div>
</div>

<!-- Script ประจำหน้า Skill Map -->
<script src="../assets/js/skill-map.js"></script>

<?php
$ROOT_PATH = '../';
// ดึง Footer เข้ามาแสดงผล
include '../includes/footer.php';
?>