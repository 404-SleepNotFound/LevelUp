<?php
$ROOT_PATH = '../';
// เริ่มต้น Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------------------
// [PHP] DATA PREPARATION (Database Placeholders)
// ---------------------------------------------------------
$coachData = [
    'user_name'       => $_SESSION['username'] ?? 'Developer',
    'greeting_time'   => 'สวัสดีตอนเย็น',
    'motivation_msg'  => '"คุณเรียนต่อเนื่องมา 7 วันแล้ว เหลืออีกเพียง 3 Skill ก็พร้อมสำหรับตำแหน่ง Junior Backend Developer"',
    'study_time'      => '2h 15m',
    'xp_gained'       => 150,
    'target_career'   => 'Backend Developer',
    'missing_skills'  => 'ขาดทักษะ Docker & CI/CD',
    'default_whatif'  => 2, // ชั่วโมง
];

$pageTitle = "AI Personal Coach | LevelUp";
include '../includes/header.php';
?>

<!-- ดึง CSS เดิม (ถ้ามี) และเขียน Override ทับเพื่อให้รองรับ Design System ใหม่ -->
<link rel="stylesheet" href="../assets/css/ai-coach.css">
<style>
    /* 🌙 Dark Mode / Sci-Fi Theme (Default) */
    :root, html[data-theme="dark"] {
        --bg-space: #0B0F19;
        --surface-glass: rgba(15, 23, 42, 0.7);
        --surface-glass-hover: rgba(30, 41, 59, 0.9);
        --card-inner-bg: rgba(0, 0, 0, 0.4);
        --neon-cyan: #00E5FF;
        --neon-purple: #B026FF;
        --neon-green: #10B981;
        --neon-warning: #FFD600;
        --neon-danger: #FF0055;
        --border-glass: rgba(255, 255, 255, 0.1);
        --text-main: #FFFFFF;
        --text-muted: #94A3B8;
        --shadow-color: rgba(0, 0, 0, 0.5);
    }

    /* ☀️ Light Mode Theme */
    html[data-theme="light"] {
        --bg-space: #F8FAFC;
        --surface-glass: rgba(255, 255, 255, 0.85);
        --surface-glass-hover: rgba(255, 255, 255, 1);
        --card-inner-bg: rgba(241, 245, 249, 0.8);
        --neon-cyan: #0284C7;
        --neon-purple: #7C3AED;
        --neon-green: #059669;
        --neon-warning: #D97706;
        --neon-danger: #DC2626;
        --border-glass: rgba(0, 0, 0, 0.1);
        --text-main: #0F172A;
        --text-muted: #475569;
        --shadow-color: rgba(0, 0, 0, 0.05);
    }

    /* ========================================== */
    /* Base Layout & Reset                        */
    /* ========================================== */
    html, body {
        margin: 0 !important;
        /* ลบ padding: 0 !important ออก เพื่อป้องกันการไปกวน Header ปกติของระบบ */
        border: none !important;
        min-height: 100vh;
        width: 100%;
        overflow-x: hidden;
        background-color: var(--bg-space) !important;
        color: var(--text-main) !important;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Wrapper Theme */
    .dark-theme-wrapper {
        background-color: transparent !important;
        background-image: 
            radial-gradient(circle at 10% 20%, rgba(0, 229, 255, 0.03), transparent 30%),
            radial-gradient(circle at 90% 80%, rgba(176, 38, 255, 0.03), transparent 30%);
        padding-top: 90px !important; /* ดันเนื้อหาลงมาไม่ให้ชน Header (ปรับตัวเลขได้ตามความสูง Header จริง) */
        padding-bottom: 2rem !important;
    }

    /* Typography */
    .text-main { color: var(--text-main) !important; }
    .text-muted { color: var(--text-muted) !important; }
    .glow-text { color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0, 229, 255, 0.3); }
    .glow-purple { color: var(--neon-purple); text-shadow: 0 0 10px rgba(176, 38, 255, 0.3); }

    /* Component: AI Cards */
    .ai-card {
        background: var(--surface-glass) !important;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--border-glass) !important;
        box-shadow: 0 8px 32px 0 var(--shadow-color) !important;
        transition: transform 0.3s ease, border-color 0.3s ease, background 0.3s ease;
    }
    .ai-card:hover {
        border-color: rgba(0, 229, 255, 0.3) !important;
        background: var(--surface-glass-hover) !important;
    }

    /* Special Card: Daily Summary */
    .bg-gradient-ai {
        background: linear-gradient(135deg, rgba(176, 38, 255, 0.15) 0%, rgba(0, 229, 255, 0.15) 100%) !important;
        border: 1px solid rgba(0, 229, 255, 0.2) !important;
    }
    html[data-theme="light"] .bg-gradient-ai {
        background: linear-gradient(135deg, rgba(124, 58, 237, 0.1) 0%, rgba(2, 132, 199, 0.1) 100%) !important;
    }

    /* Range Slider Styling */
    .custom-range {
        -webkit-appearance: none;
        width: 100%;
        height: 6px;
        background: var(--border-glass);
        border-radius: 5px;
        outline: none;
    }
    .custom-range::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--neon-cyan);
        cursor: pointer;
        box-shadow: 0 0 10px var(--neon-cyan);
    }

    /* Inner Dark Boxes */
    .inner-box {
        background: var(--card-inner-bg);
        border: 1px solid var(--border-glass);
        color: var(--text-main);
    }

    /* Chart / Circle */
    .circular-chart { display: block; margin: 0 auto; }
    .circle-bg { fill: none; stroke: var(--border-glass); stroke-width: 2.5; }
    .circle { fill: none; stroke-width: 2.5; stroke-linecap: round; stroke: var(--neon-purple); filter: drop-shadow(0 0 5px var(--neon-purple)); transition: stroke-dasharray 1.5s ease-out; }

    /* Button */
    .glow-btn {
        background-color: var(--neon-cyan);
        color: #000 !important;
        border: none;
        box-shadow: 0 0 15px rgba(0, 229, 255, 0.4);
        font-weight: bold;
    }
    .glow-btn:hover {
        background-color: #FFFFFF;
        box-shadow: 0 0 20px rgba(0, 229, 255, 0.6);
    }
    html[data-theme="light"] .glow-btn {
        color: #FFF !important;
    }
</style>

<div class="container-fluid dark-theme-wrapper" style="min-height: calc(100vh - 70px);">
    <div class="container">
        
        <!-- Header & Action -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-1 d-flex align-items-center gap-2 glow-text text-uppercase" style="letter-spacing: 1px;">
                    <i class="bi bi-robot"></i> Oracle System
                </h2>
                <p class="text-muted mb-0 fs-sm">AI Personal Coach: Real-time Analysis & Trajectory Planning</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary rounded-pill border-secondary text-main"><i class="bi bi-sliders"></i> Adjust Core</button>
                <button class="btn rounded-pill glow-btn px-4"><i class="bi bi-cloud-arrow-down-fill me-1"></i> Data Log</button>
            </div>
        </div>

        <!-- System Overload Warning (Hidden by default, shown via JS) -->
        <div id="burnoutAlert" class="alert ai-card border-warning border-opacity-50 text-main rounded-4 d-none align-items-center gap-3 mb-4 position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-warning opacity-10" style="filter: blur(20px);"></div>
            <i class="bi bi-exclamation-triangle-fill fs-2" style="color: var(--neon-warning); z-index: 1;"></i>
            <div class="z-index-1">
                <h6 class="fw-bold mb-1 text-uppercase" style="color: var(--neon-warning); letter-spacing: 1px;">System Overload Warning</h6>
                <p class="mb-0 fs-sm text-muted">ระบบพบอัตราความเหนื่อยล้าสูงและ Sync Rate (Quiz Score) ตกลงใน 2 วันที่ผ่านมา แนะนำให้เข้าโหมดพักผ่อนหรือเปลี่ยนเป็นภารกิจระดับต่ำ</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column: Planning & Actions -->
            <div class="col-12 col-xl-8">
                
                <!-- Daily Summary & Motivation -->
                <div class="card ai-card bg-gradient-ai rounded-4 mb-4">
                    <div class="card-body p-4 p-md-5 position-relative overflow-hidden">
                        <div class="z-index-1 position-relative">
                            <h4 class="fw-bold mb-2 text-main" id="greetingMsg"><?= htmlspecialchars($coachData['greeting_time'] . ', ' . $coachData['user_name']) ?>!</h4>
                            <p class="fs-sm mb-4 text-muted" id="motivationMsg" style="max-width: 80%;"><?= htmlspecialchars($coachData['motivation_msg']) ?></p>
                            
                            <div class="d-flex flex-wrap gap-4 gap-md-5 bg-dark bg-opacity-25 p-3 rounded-4 border border-secondary border-opacity-25 d-inline-flex">
                                <div>
                                    <div class="fs-xs text-muted text-uppercase fw-bold mb-1"><i class="bi bi-clock-history me-1"></i> Active Time</div>
                                    <div class="fw-bold fs-3 text-main font-monospace" id="dailyTime"><?= htmlspecialchars($coachData['study_time']) ?></div>
                                </div>
                                <div>
                                    <div class="fs-xs text-muted text-uppercase fw-bold mb-1"><i class="bi bi-lightning-charge-fill me-1 text-warning"></i> Energy / XP</div>
                                    <div class="fw-bold fs-3 font-monospace" style="color: var(--neon-warning);" id="dailyXp">+<?= htmlspecialchars($coachData['xp_gained']) ?></div>
                                </div>
                            </div>
                        </div>
                        <i class="bi bi-cpu position-absolute top-50 end-0 translate-middle-y opacity-10 pointer-events-none" style="font-size: 12rem; margin-right: -4rem;"></i>
                    </div>
                </div>

                <!-- AI What-if Planner -->
                <div class="card ai-card rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold d-flex align-items-center gap-2 mb-4 text-uppercase text-main" style="letter-spacing: 1px;">
                            <i class="bi bi-fast-forward-btn glow-text"></i> Time-Dilation Simulator
                        </h6>
                        <div class="row align-items-center g-4">
                            <div class="col-md-7">
                                <label class="fs-sm text-muted mb-3">จำลองสถานการณ์ หากป้อนข้อมูล: <strong class="text-main fs-5 ms-2 font-monospace" id="whatIfHoursDisplay"><?= $coachData['default_whatif'] ?> hr/day</strong></label>
                                <input type="range" class="form-range custom-range" min="0.5" max="6" step="0.5" id="whatIfSlider" value="<?= $coachData['default_whatif'] ?>">
                                <div class="d-flex justify-content-between fs-xs text-muted mt-2 fw-bold font-monospace">
                                    <span>0.5h</span>
                                    <span>6.0h</span>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="inner-box p-3 p-xl-4 rounded-4 text-center">
                                    <div class="fs-xs text-muted mb-2 text-uppercase fw-bold">Estimated Completion</div>
                                    <h3 class="fw-bold mb-0 glow-text font-monospace" id="whatIfResult">5m 12d</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's Learning Plan -->
                <div class="card ai-card rounded-4 mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-main text-uppercase"><i class="bi bi-list-task me-2 glow-text"></i> Adaptive Directives</h6>
                        <span class="badge rounded-pill border border-info" style="color: var(--neon-cyan); background: rgba(0, 229, 255, 0.1);">AI Adjusted</span>
                    </div>
                    <div class="card-body p-4">
                        <!-- List จะถูก Inject ผ่าน ai-coach.js ตาม ID นี้ -->
                        <div class="todo-list" id="dailyPlanList">
                            <div class="text-center py-4">
                                <div class="spinner-border spinner-border-sm glow-text" role="status"></div>
                                <span class="ms-2 text-muted fs-sm">Synchronizing Directives...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Learning Timeline -->
                <div class="card ai-card rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4 text-main text-uppercase"><i class="bi bi-signpost-split me-2 glow-text"></i> Progression Timeline</h6>
                        <!-- Timeline จะถูก Inject ผ่าน ai-coach.js ตาม ID นี้ -->
                        <div class="timeline text-main" id="learningTimeline">
                            <div class="text-center py-3 text-muted fs-sm">Loading Node Data...</div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Analytics & Recommendations -->
            <div class="col-12 col-xl-4">
                
                <!-- Career Readiness -->
                <div class="card ai-card rounded-4 mb-4 text-center">
                    <div class="card-body p-4">
                        <p class="text-muted fs-xs text-uppercase fw-bold mb-1">Class Advancement</p>
                        <h6 class="fw-bold text-main mb-4 glow-purple"><?= htmlspecialchars($coachData['target_career']) ?></h6>
                        
                        <div class="position-relative d-inline-block mb-4">
                            <svg viewBox="0 0 36 36" class="circular-chart" style="width: 140px; height: 140px;">
                                <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path class="circle" id="careerMatchRing" stroke-dasharray="0, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle text-center w-100">
                                <span class="fs-xs text-muted text-uppercase d-block mb-n1">Sync</span>
                                <h2 class="fw-bold mb-0 text-main font-monospace" id="careerMatchTxt">0%</h2>
                            </div>
                        </div>
                        <div class="inner-box rounded-3 p-2 d-inline-block border-danger border-opacity-50">
                            <p class="fs-xs text-muted mb-0"><i class="bi bi-shield-exclamation text-danger me-1"></i> <?= htmlspecialchars($coachData['missing_skills']) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Knowledge Assessment -->
                <div class="card ai-card rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4 text-main text-uppercase"><i class="bi bi-radar me-2 glow-text"></i> Core Knowledge</h6>
                        <div id="knowledgeList" class="text-main">
                            <div class="text-center py-2 text-muted fs-sm">Scanning Cores...</div>
                        </div>
                    </div>
                </div>

                <!-- Strengths & Weaknesses -->
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="card ai-card rounded-4 h-100">
                            <div class="card-body p-3">
                                <h6 class="fs-xs text-uppercase fw-bold mb-3" style="color: var(--neon-green);"><i class="bi bi-arrow-up-right-square me-1"></i> High Aptitude</h6>
                                <div id="strengthList" class="d-flex flex-column gap-2 text-main"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card ai-card rounded-4 h-100">
                            <div class="card-body p-3">
                                <h6 class="fs-xs text-uppercase fw-bold mb-3" style="color: var(--neon-danger);"><i class="bi bi-arrow-down-right-square me-1"></i> Critical Low</h6>
                                <div id="weaknessList" class="d-flex flex-column gap-2 text-main"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Review Reminder -->
                <div class="card ai-card rounded-4 mb-4 position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-3 opacity-10">
                        <i class="bi bi-arrow-repeat fs-1 text-warning"></i>
                    </div>
                    <div class="card-body p-4 position-relative z-index-1">
                        <h6 class="fw-bold mb-2 d-flex align-items-center gap-2 text-uppercase text-main">
                            <i class="bi bi-arrow-repeat" style="color: var(--neon-warning);"></i> Memory Sync Required
                        </h6>
                        <p class="fs-xs text-muted mb-4">ระบบตรวจพบอัตราการเลือนลางของข้อมูล (Memory Decay) แนะนำให้ทำการฝึกซ้ำ (Spaced Repetition)</p>
                        <div id="reviewList" class="d-flex flex-column gap-3 text-main">
                            <!-- Injected via JS -->
                            <div class="text-center py-2 text-muted fs-sm">Loading Sync Targets...</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- คง Script เดิมไว้เพื่อให้ฟังก์ชัน Inject List และ Animation ยังทำงานได้ -->
<script src="../assets/js/ai-coach.js"></script>

<?php
$ROOT_PATH = '../';
include '../includes/footer.php'; ?>
