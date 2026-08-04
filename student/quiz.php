﻿<?php
    $ROOT_PATH = '../';
    // เริ่มต้น Session และเตรียมข้อมูลจาก Database (Simulation)
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // ---------------------------------------------------------
    // [PHP] DATA PREPARATION (Database Placeholders)
    // ---------------------------------------------------------
    $quizData = [
        'title'           => 'Python Fundamentals Assessment',
        'total_questions' => 5,
        'time_limit'      => '15:00',
        'reward_xp'       => 500,
        'reward_coins'    => 50,
        'pass_score'      => 70
    ];

    $userStats = [
        'level'      => $_SESSION['user_level'] ?? 15,
        'xp'         => $_SESSION['user_xp'] ?? 12500,
        'max_xp'     => $_SESSION['max_xp'] ?? 15000,
        'rank_name'  => $_SESSION['rank_name'] ?? 'Data Padawan',
        'rank_color' => 'text-info'
    ];

    $pageTitle = $quizData['title'] . " | LevelUp Assessment";
    include '../includes/header.php';
    ?>

<!-- CSS Design System สำหรับ Quiz (รองรับทั้ง Dark & Light Mode) -->
<link rel="stylesheet" href="../assets/css/quiz.css">
<style>
    /* 🌙 Dark Mode Theme (Default) */

    :root,
    html[data-theme="dark"] {

        :root,
        html[data-theme="dark"] {

            --bg-space: #0B0F19;
            --surface-glass: rgba(15, 23, 42, 0.85);
            --surface-card: rgba(30, 41, 59, 0.7);
            --card-inner-bg: rgba(0, 0, 0, 0.4);
            --neon-cyan: #00E5FF;
            --neon-purple: #B026FF;
            --neon-green: #10B981;
            --neon-warning: #FFD600;
            --neon-danger: #FF0055;
            --border-glass: rgba(255, 255, 255, 0.15);
            --text-main: #FFFFFF;
            --text-muted: #CBD5E1;
            --timer-bg: rgba(0, 0, 0, 0.6);
            --btn-conf-bg: rgba(255, 255, 255, 0.08);
            --btn-conf-hover: rgba(0, 229, 255, 0.15);
            --shadow-color: rgba(0, 0, 0, 0.5);
        }

        /* ☀️ Light Mode Theme */
        html[data-theme="light"] {
            --bg-space: #F8FAFC;
            --surface-glass: rgba(255, 255, 255, 0.9);
            --surface-card: rgba(255, 255, 255, 0.95);
            --card-inner-bg: rgba(241, 245, 249, 0.9);
            --neon-cyan: #0284C7;
            --neon-purple: #7C3AED;
            --neon-green: #059669;
            --neon-warning: #D97706;
            --neon-danger: #DC2626;
            --border-glass: rgba(0, 0, 0, 0.12);
            --text-main: #0F172A;
            --text-muted: #475569;
            --timer-bg: #FFFFFF;
            --btn-conf-bg: rgba(0, 0, 0, 0.04);
            --btn-conf-hover: rgba(2, 132, 199, 0.1);
            --shadow-color: rgba(0, 0, 0, 0.08);
        }

        /* Force Base Text & Background Colors */

        body,
        .quiz-card,
        .glass-panel {

            body,
            .quiz-card,
            .glass-panel {

                background-color: var(--bg-space) !important;
                color: var(--text-main) !important;
                transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
            }

            /* Overrides for Light Mode Contrast */
            html[data-theme="light"] .bg-dark {
                background-color: var(--card-inner-bg) !important;
                color: var(--text-main) !important;
            }


            html[data-theme="light"] .text-white {
                color: var(--text-main) !important;
            }


            html[data-theme="light"] .text-white {
                color: var(--text-main) !important;
            }

            html[data-theme="light"] .btn-dark {
                background-color: #E2E8F0 !important;
                color: #0F172A !important;
                border-color: #CBD5E1 !important;
            }


            .text-muted {
                color: var(--text-muted) !important;
            }

            .text-main {
                color: var(--text-main) !important;
            }

            /* Background Glow FX */
            body {
                background-image: .text-muted {
                    color: var(--text-muted) !important;
                }

                .text-main {
                    color: var(--text-main) !important;
                }

                /* Background Glow FX */
                body {
                    background-image:

                        radial-gradient(circle at 15% 50%, rgba(0, 229, 255, 0.06), transparent 25%),
                        radial-gradient(circle at 85% 30%, rgba(176, 38, 255, 0.06), transparent 25%);
                }

                /* Glass Panels */
                .glass-panel {
                    background: var(--surface-glass);
                    backdrop-filter: blur(16px);
                    -webkit-backdrop-filter: blur(16px);
                    border: 1px solid var(--border-glass);
                    box-shadow: 0 8px 32px 0 var(--shadow-color);
                }





                .quiz-card {
                    background: var(--surface-card);
                    border: 1px solid var(--border-glass);
                    box-shadow: 0 4px 20px 0 var(--shadow-color);
                }

                /* Gamified HUD Components */
                .hud-timer {
                    background: var(--timer-bg);
                    border: 1px solid var(--neon-cyan);
                    color: var(--neon-cyan) !important;
                    box-shadow: 0 0 10px rgba(0, 229, 255, 0.2);
                    letter-spacing: 2px;
                }




                .hud-timer.danger {
                    border-color: var(--neon-danger);
                    color: var(--neon-danger) !important;
                    box-shadow: 0 0 15px rgba(255, 0, 85, 0.4);
                    animation: pulse-danger 1s infinite;
                }

                @keyframes pulse-danger {

                    0% {
                        box-shadow: 0 0 10px rgba(255, 0, 85, 0.2);
                    }

                    50% {
                        box-shadow: 0 0 20px rgba(255, 0, 85, 0.6);
                    }

                    100% {
                        box-shadow: 0 0 10px rgba(255, 0, 85, 0.2);
                    }

                    0% {
                        box-shadow: 0 0 10px rgba(255, 0, 85, 0.2);
                    }

                    50% {
                        box-shadow: 0 0 20px rgba(255, 0, 85, 0.6);
                    }

                    100% {
                        box-shadow: 0 0 10px rgba(255, 0, 85, 0.2);
                    }

                }

                /* Custom Progress Bar */
                .progress-hud {
                    background: rgba(150, 150, 150, 0.15);
                    border: 1px solid var(--border-glass);
                    height: 10px;
                    border-radius: 10px;
                    overflow: hidden;
                }




                .progress-hud .progress-bar {
                    background: linear-gradient(90deg, var(--neon-purple), var(--neon-cyan));
                    box-shadow: 0 0 10px var(--neon-cyan);
                }

                /* Confidence Buttons */
                .btn-confidence {
                    background: var(--btn-conf-bg);
                    border: 1px solid var(--border-glass);
                    color: var(--text-muted);
                    transition: all 0.3s ease;
                    border-radius: 12px;
                    padding: 15px 20px;
                    min-width: 120px;
                }




                .btn-confidence:hover {
                    background: var(--btn-conf-hover);
                    border-color: var(--neon-cyan);
                    color: var(--text-main);
                    transform: translateY(-3px);
                }




                .btn-confidence.active {
                    background: var(--btn-conf-hover);
                    border-color: var(--neon-cyan);
                    color: var(--neon-cyan) !important;
                    box-shadow: 0 0 15px rgba(0, 229, 255, 0.3);
                }


                /* Result SVG Chart */
                .score-circle {
                    display: block;
                    margin: 0 auto;
                    max-width: 200px;
                }

                .circle-bg {
                    fill: none;
                    stroke: rgba(150, 150, 150, 0.2);
                    stroke-width: 2.5;
                }

                .circle {
                    fill: none;
                    stroke-width: 2.5;
                    stroke-linecap: round;
                    stroke: var(--neon-cyan);
                    filter: drop-shadow(0 0 5px var(--neon-cyan));
                    transition: stroke-dasharray 1.5s ease-out;
                }

                .percentage {
                    fill: var(--neon-cyan);
                    font-family: monospace;
                    font-size: 8px;
                    font-weight: bold;
                    text-anchor: middle;
                    filter: drop-shadow(0 0 5px rgba(0, 229, 255, 0.3));
                }

                /* Utilities */
                .glow-text {
                    color: var(--neon-cyan);
                    text-shadow: 0 0 8px rgba(0, 229, 255, 0.3);
                }

                .fade-in-anim {
                    animation: fadeIn 0.5s forwards;
                }

                .slide-up-anim {
                    animation: slideUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
                }

                @keyframes fadeIn {
                    from {
                        opacity: 0;
                    }

                    to {
                        opacity: 1;
                    }
                }

                @keyframes slideUp {
                    from {
                        opacity: 0;
                        transform: translateY(20px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }


                /* Result SVG Chart */
                .score-circle {
                    display: block;
                    margin: 0 auto;
                    max-width: 200px;
                }

                .circle-bg {
                    fill: none;
                    stroke: rgba(150, 150, 150, 0.2);
                    stroke-width: 2.5;
                }

                .circle {
                    fill: none;
                    stroke-width: 2.5;
                    stroke-linecap: round;
                    stroke: var(--neon-cyan);
                    filter: drop-shadow(0 0 5px var(--neon-cyan));
                    transition: stroke-dasharray 1.5s ease-out;
                }

                .percentage {
                    fill: var(--neon-cyan);
                    font-family: monospace;
                    font-size: 8px;
                    font-weight: bold;
                    text-anchor: middle;
                    filter: drop-shadow(0 0 5px rgba(0, 229, 255, 0.3));
                }

                /* Utilities */
                .glow-text {
                    color: var(--neon-cyan);
                    text-shadow: 0 0 8px rgba(0, 229, 255, 0.3);
                }

                .fade-in-anim {
                    animation: fadeIn 0.5s forwards;
                }

                .slide-up-anim {
                    animation: slideUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
                }

                @keyframes fadeIn {
                    from {
                        opacity: 0;
                    }

                    to {
                        opacity: 1;
                    }
                }

                @keyframes slideUp {
                    from {
                        opacity: 0;
                        transform: translateY(20px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
</style>

<!--  -->
<!-- 1. QUIZ MISSION INTERFACE (โหมดทำข้อสอบ) -->
<!--  -->
<div class="container-fluid min-vh-100 d-flex flex-column transition-theme py-4" id="quizContainer">


    <div class="quiz-wrapper mx-auto w-100" style="max-width: 850px;">

        <!-- HUD Header: Timer, Progress & Rewards -->
        <div class="glass-panel rounded-4 p-3 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">



            <div class="quiz-wrapper mx-auto w-100" style="max-width: 850px;">

                <!-- HUD Header: Timer, Progress & Rewards -->
                <div class="glass-panel rounded-4 p-3 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">


                    <!-- Left: Exit & Mission Info -->
                    <div class="d-flex align-items-center gap-3 w-100 w-md-auto">
                        <button class="btn btn-dark border border-secondary rounded-circle d-flex justify-content-center align-items-center" id="btnExitQuiz" title="Abort Mission" style="width: 40px; height: 40px;">
                            <i class="bi bi-x-lg text-danger"></i>
                        </button>
                        <div>
                            <h5 class="mb-0 fw-bold fs-6 text-uppercase glow-text" style="letter-spacing: 1px;"><?= htmlspecialchars($quizData['title']) ?></h5>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge bg-success bg-opacity-25 text-success border border-success px-2 py-1 fs-xs fw-bold"><i class="bi bi-star-fill me-1"></i> +<?= $quizData['reward_xp'] ?> XP</span>
                                <span class="badge bg-warning bg-opacity-25 text-warning border border-warning px-2 py-1 fs-xs fw-bold"><i class="bi bi-coin me-1"></i> +<?= $quizData['reward_coins'] ?> Coins</span>
                            </div>
                        </div>
                    </div>





                    <!-- Right: Timer & Progress -->
                    <div class="d-flex align-items-center gap-4 w-100 w-md-auto justify-content-between justify-content-md-end">
                        <!-- Progress -->
                        <div class="d-flex flex-column align-items-end" style="width: 150px;">
                            <span class="text-uppercase fs-xs fw-bold text-main mb-1" id="progressText" style="letter-spacing: 1px;">Node 1 of <?= $quizData['total_questions'] ?></span>
                            <div class="progress-hud w-100">
                                <div class="progress-bar" id="quizProgressBar" role="progressbar" style="width: 0%;"></div>
                            </div>
                        </div>
                        <!-- Timer -->
                        <div class="hud-timer px-3 py-2 rounded-3 font-monospace fw-bold fs-5 d-flex align-items-center gap-2">
                            <i class="bi bi-stopwatch"></i> <span id="quizTimer"><?= $quizData['time_limit'] ?></span>
                        </div>
                    </div>
                </div>

                <!-- Question Area (Main Terminal) -->
                <div class="quiz-card rounded-4 p-4 p-md-5 mb-4 position-relative overflow-hidden slide-up-anim" id="questionArea">
                    <!-- Loading State Before JS Injection -->
                    <div class="text-center py-5 my-4">
                        <div class="spinner-border text-info" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h5 class="mt-4 text-info fw-bold text-uppercase" style="letter-spacing: 2px;">Decrypting Question...</h5>
                        <p class="text-muted fs-sm">Connecting to AI Assessment Core</p>
                    </div>
                </div>

                <!-- Confidence & Action Area (Sync Rating) -->
                <div class="glass-panel rounded-4 p-4 d-none slide-up-anim" id="actionArea">
                    <div class="row align-items-center">
                        <div class="col-md-9 mb-3 mb-md-0">
                            <h6 class="mb-3 fw-bold text-uppercase text-main fs-sm" style="letter-spacing: 1px;"><i class="bi bi-cpu-fill text-info me-2"></i>AI Confidence Sync</h6>
                            <div class="d-flex gap-2 flex-wrap">
                                <button class="btn btn-confidence outline-none flex-grow-1" data-level="4">
                                    <div class="fs-4 mb-1 fw-bold">100%</div>
                                    <span class="fs-xs text-uppercase fw-bold">Absolute</span>
                                </button>
                                <button class="btn btn-confidence outline-none flex-grow-1" data-level="3">
                                    <div class="fs-4 mb-1 fw-bold">75%</div>
                                    <span class="fs-xs text-uppercase fw-bold">High</span>
                                </button>
                                <button class="btn btn-confidence outline-none flex-grow-1" data-level="2">
                                    <div class="fs-4 mb-1 fw-bold">50%</div>
                                    <span class="fs-xs text-uppercase fw-bold">Partial</span>
                                </button>
                                <button class="btn btn-confidence outline-none flex-grow-1" data-level="1">
                                    <div class="fs-4 mb-1 fw-bold">25%</div>
                                    <span class="fs-xs text-uppercase fw-bold">Guess</span>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3 text-md-end text-center border-start border-secondary border-opacity-50 ps-md-4">
                            <button class="btn btn-info w-100 rounded-3 py-3 fw-bold text-dark text-uppercase d-flex flex-column align-items-center justify-content-center h-100" id="btnNext" disabled style="box-shadow: 0 0 15px rgba(0,229,255,0.4);">
                                <span>Execute</span>
                                <i class="bi bi-chevron-double-right mt-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!--  -->
        <!-- 2. MISSION ACCOMPLISHED (หน้าสรุปผล) -->
        <!--  -->
        <div class="container py-5 d-none fade-in-anim min-vh-100 d-flex align-items-center" id="resultContainer">
            <div class="row g-4 w-100 justify-content-center">





                <!-- Assessment Overview -->
                <div class="col-lg-7">
                    <div class="glass-panel rounded-4 h-100 position-relative overflow-hidden p-0">
                        <!-- Background Decor -->
                        <div class="position-absolute top-0 end-0 p-4 opacity-10 pointer-events-none">
                            <i class="bi bi-hexagon-fill" style="font-size: 15rem; color: var(--neon-cyan);"></i>
                        </div>


                        <div class="p-4 p-md-5 text-center position-relative z-index-1">
                            <h5 class="text-uppercase fw-bold text-main mb-4" style="letter-spacing: 2px;">Mission Accomplished</h5>



                            <div class="p-4 p-md-5 text-center position-relative z-index-1">
                                <h5 class="text-uppercase fw-bold text-main mb-4" style="letter-spacing: 2px;">Mission Accomplished</h5>


                                <div class="score-circle mx-auto mb-4" id="scoreCircle">
                                    <svg viewBox="0 0 36 36" class="circular-chart">
                                        <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                        <path class="circle" id="scorePath" stroke-dasharray="0, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                        <text x="18" y="21.5" class="percentage" id="scoreText">0%</text>
                                    </svg>
                                </div>





                                <div class="d-flex justify-content-center gap-4 mb-4">
                                    <div>
                                        <span class="fs-xs text-uppercase text-muted d-block mb-1 fw-bold">Time Elapsed</span>
                                        <span class="fw-bold fs-5 text-main font-monospace" id="timeUsedText">00:00</span>
                                    </div>
                                    <div class="vr bg-secondary opacity-50"></div>
                                    <div>
                                        <span class="fs-xs text-uppercase text-muted d-block mb-1 fw-bold">Required</span>
                                        <span class="fw-bold fs-5 text-warning font-monospace"><?= $quizData['pass_score'] ?>%</span>
                                    </div>
                                </div>

                                <!-- AI Feedback Box -->
                                <div class="bg-dark bg-opacity-80 rounded-4 p-4 text-start border border-purple border-opacity-50 position-relative" style="border-color: var(--neon-purple);">
                                    <div class="position-absolute top-0 start-0 translate-middle bg-dark border border-purple rounded-circle d-flex align-items-center justify-content-center shadow-lg" style="width: 45px; height: 45px; border-color: var(--neon-purple);">
                                        <i class="bi bi-robot text-purple" style="color: var(--neon-purple);"></i>
                                    </div>
                                    <h6 class="fw-bold text-uppercase mb-2 ms-2" style="color: var(--neon-purple); letter-spacing: 1px;">Oracle AI Analysis</h6>
                                    <div id="aiFeedbackContent" class="fs-sm text-main ms-2" style="line-height: 1.6;">
                                        <!-- AI Feedback Injected Here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rewards & Knowledge Gap -->
                    <div class="col-lg-5">
                        <div class="d-flex flex-column gap-4 h-100">





                            <!-- Rewards Panel -->
                            <div class="glass-panel rounded-4 p-4 border border-success border-opacity-50 position-relative overflow-hidden">
                                <div class="position-absolute top-0 start-0 w-100 h-100 bg-success opacity-10" style="filter: blur(30px);"></div>
                                <h6 class="text-uppercase fw-bold text-success mb-3" style="letter-spacing: 1px;"><i class="bi bi-gift-fill me-2"></i>Rewards Claimed</h6>





                                <div class="d-flex align-items-center gap-3 bg-dark bg-opacity-80 p-3 rounded-3 mb-3 border border-secondary border-opacity-50">
                                    <div class="fs-1 text-warning"><i class="bi bi-coin"></i></div>
                                    <div>
                                        <span class="d-block text-muted fs-xs text-uppercase fw-bold">Credit Bonus</span>
                                        <span class="fw-bold fs-4 text-main">+<?= $quizData['reward_coins'] ?></span>
                                    </div>
                                </div>





                                <div class="bg-dark bg-opacity-80 p-3 rounded-3 border border-secondary border-opacity-50">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fs-xs text-uppercase text-info fw-bold">Level <?= $userStats['level'] ?></span>
                                        <span class="fs-xs font-monospace text-main fw-bold"><?= number_format($userStats['xp'] + $quizData['reward_xp']) ?> / <?= number_format($userStats['max_xp']) ?> XP</span>
                                    </div>
                                    <div class="progress bg-secondary bg-opacity-50 mb-2" style="height: 6px;">
                                        <div class="progress-bar bg-info" style="width: <?= min(100, (($userStats['xp'] + $quizData['reward_xp']) / $userStats['max_xp']) * 100) ?>%; box-shadow: 0 0 10px var(--neon-cyan);"></div>
                                    </div>
                                    <span class="fs-xs text-success fw-bold"><i class="bi bi-arrow-up-right me-1"></i>+<?= $quizData['reward_xp'] ?> XP Acquired</span>
                                </div>
                            </div>

                            <!-- Knowledge Gap Map -->
                            <div class="glass-panel rounded-4 p-4 flex-grow-1">
                                <h6 class="text-uppercase fw-bold mb-1 text-main"><i class="bi bi-map text-info me-2"></i>Knowledge Gap</h6>
                                <p class="fs-xs text-muted mb-3">AI Detection based on Sync Rate & Accuracy</p>





                                <div class="gap-map-tree bg-dark bg-opacity-80 p-3 rounded-3 border border-secondary border-opacity-50 h-100 text-main" id="gapMapContent" style="min-height: 120px;">
                                    <!-- Tree Injected Here by JS -->
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 mt-auto">
                                <button class="btn btn-outline-secondary text-main w-50 py-2 rounded-3 text-uppercase fs-xs fw-bold border-secondary" onclick="location.reload()" style="letter-spacing: 1px;"><i class="bi bi-arrow-clockwise me-1"></i> Retry</button>
                                <a href="learning-path.php" class="btn btn-info w-50 py-2 rounded-3 text-uppercase fs-xs fw-bold text-dark" style="letter-spacing: 1px; box-shadow: 0 0 15px rgba(0,229,255,0.3);"><i class="bi bi-signpost-split-fill me-1"></i> Path</a>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
            <script src="../assets/js/quiz.js"></script>

            <?php
            $ROOT_PATH = '../';

            include '../includes/footer.php'; ?>