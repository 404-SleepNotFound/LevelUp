<?php
/**
 * LevelUp Rocket Cursor Component v3.5 (Ultimate Hotspot Calibrated Edition)
 * - Calibrated Hotspot: Mouse focus point perfectly aligned to the rocket nose tip.
 * - Integrated Calibration Studio: Click to align tip & preview live interaction.
 * - Full Particle Thrusters & Spark Burst on click.
 * - Smooth LERP movement + F5 refresh warp fix.
 * Usage in PHP: <?php include 'rocket-cursor.php'; ?>
 */
?>

<div id="rocket-cursor-wrapper" aria-hidden="true">
    <div id="rocket-cursor-main">
        <!-- Pure CSS Engine Flame Effect Base -->
        <div class="rocket-flame-container">
            <div class="rocket-flame-core"></div>
            <div class="rocket-flame-glow"></div>
        </div>
        
        <!-- SVG Rocket Icon -->
        <svg class="rocket-svg" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g id="rocket-group">
                <!-- Main Rocket Body -->
                <path d="M32 4C32 4 48 16 48 38C48 44 44 48 44 48L32 42L20 48C20 48 16 44 16 38C16 16 32 4 32 4Z" fill="url(#rocket-body-grad)" stroke="#ffffff" stroke-width="1.5" stroke-linejoin="round"/>
                <!-- Rocket Nose Cone (Tip is at X: 32, Y: 4) -->
                <path d="M32 4C37 10 42 20 43.5 26H20.5C22 20 27 10 32 4Z" fill="#EF4444"/>
                <!-- Wings / Fins -->
                <path d="M16 34L6 44C6 44 10 49 18 47L16 34Z" fill="#3B82F6" stroke="#ffffff" stroke-width="1"/>
                <path d="M48 34L58 44C58 44 54 49 46 47L48 34Z" fill="#3B82F6" stroke="#ffffff" stroke-width="1"/>
                <!-- Porthole Window -->
                <circle cx="32" cy="24" r="6" fill="#06B6D4" stroke="#ffffff" stroke-width="1.5"/>
                <circle cx="30" cy="22" r="2" fill="#ffffff" opacity="0.8"/>
                <!-- Center Thruster Nozzle -->
                <path d="M26 44H38L36 49H28L26 44Z" fill="#475569"/>
            </g>
            <defs>
                <linearGradient id="rocket-body-grad" x1="16" y1="4" x2="48" y2="48" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FFFFFF"/>
                    <stop offset="0.6" stop-color="#E2E8F0"/>
                    <stop offset="1" stop-color="#CBD5E1"/>
                </linearGradient>
            </defs>
        </svg>

        <!-- Hover Target Indicator Ring -->
        <div class="rocket-target-ring"></div>
        <!-- Visual Hotspot Marker Pin -->
        <div class="rocket-tip-pin" title="Nose Tip Hotspot"></div>
    </div>
</div>

<!-- Canvas for Engine Smoke & Spark Particles -->
<canvas id="rocket-particle-canvas"></canvas>

<style>
    /* ------------------------------------------------------------------
       1. GLOBAL CURSOR RESET (Ensures default cursor stays hidden)
       ------------------------------------------------------------------ */
    *, *::before, *::after,
    html, body, html *, body *,
    a, button, input, select, textarea, label, [role="button"],
    .btn, .glass-card, .accordion-button, .card, iframe, svg, img {
        cursor: none !important;
    }

    /* Force hide legacy or duplicate cursor elements */
    #custom-cursor, .custom-cursor, #cursor, .cursor, .custom-mouse,
    #mouse-pointer, [data-cursor], .cursor-follower, .mouse-cursor {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }

    /* ------------------------------------------------------------------
       2. CURSOR WRAPPER & HIGH Z-INDEX STACKING
       ------------------------------------------------------------------ */
    #rocket-cursor-wrapper {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 0 !important;
        height: 0 !important;
        pointer-events: none !important;
        z-index: 2147483647 !important;
        isolation: isolate !important;
        transform: translate3d(-100px, -100px, 0);
        will-change: transform;
        display: block;
        opacity: 0;
        transition: opacity 0.25s ease;
    }

    #rocket-particle-canvas {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        pointer-events: none !important;
        z-index: 2147483646 !important;
        opacity: 0;
        transition: opacity 0.25s ease;
    }

    /* ------------------------------------------------------------------
       3. ROCKET ANIMATION & VISUAL STYLING (CALIBRATED TIP HOTSPOT)
       ------------------------------------------------------------------ */
    #rocket-cursor-main {
        position: absolute;
        top: 0;
        left: 0;
        width: 42px;
        height: 42px;
        /* HOTSPOT CALIBRATION: Tip of rocket (X: 21px, Y: 2.6px) offset */
        margin-left: -21px;
        margin-top: -2.625px;
        pointer-events: none !important;
        transition: transform 0.18s cubic-bezier(0.2, 0.8, 0.2, 1);
        transform-origin: 21px 2.625px; /* Pivot exactly on the tip! */
    }

    .rocket-svg {
        width: 100%;
        height: 100%;
        filter: drop-shadow(0px 4px 10px rgba(0, 0, 0, 0.35));
        transition: filter 0.2s ease;
    }

    .rocket-tip-pin {
        position: absolute;
        top: 2.625px;
        left: 21px;
        width: 4px;
        height: 4px;
        margin-top: -2px;
        margin-left: -2px;
        background: #EF4444;
        border: 1px solid #FFFFFF;
        border-radius: 50%;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease;
        z-index: 10;
    }

    body.show-hotspot-pin .rocket-tip-pin {
        opacity: 1;
    }

    /* Engine Flame Effect */
    .rocket-flame-container {
        position: absolute;
        bottom: -8px;
        left: 50%;
        transform: translateX(-50%);
        width: 16px;
        height: 22px;
        pointer-events: none;
        display: flex;
        justify-content: center;
    }

    .rocket-flame-core {
        width: 8px;
        height: 100%;
        background: linear-gradient(180deg, #FBBF24 0%, #EF4444 60%, rgba(239, 68, 68, 0) 100%);
        border-radius: 50% 50% 20% 20%;
        animation: rocketFlameBurn 0.12s infinite alternate ease-in-out;
    }

    .rocket-flame-glow {
        position: absolute;
        top: -2px;
        width: 18px;
        height: 18px;
        background: rgba(245, 158, 11, 0.6);
        border-radius: 50%;
        filter: blur(5px);
        animation: rocketFlameGlow 0.2s infinite alternate ease-in-out;
    }

    @keyframes rocketFlameBurn {
        0% { transform: scaleY(0.8) scaleX(0.9); opacity: 0.85; }
        100% { transform: scaleY(1.4) scaleX(1.1); opacity: 1; }
    }

    @keyframes rocketFlameGlow {
        0% { transform: scale(0.9); opacity: 0.5; }
        100% { transform: scale(1.3); opacity: 0.8; }
    }

    /* Hover Target Indicator Ring */
    .rocket-target-ring {
        position: absolute;
        top: 2.625px;
        left: 21px;
        width: 40px;
        height: 40px;
        margin-top: -20px;
        margin-left: -20px;
        border: 2px dashed rgba(99, 102, 241, 0.8);
        border-radius: 50%;
        opacity: 0;
        transform: scale(0.5) rotate(0deg);
        transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        pointer-events: none;
    }

    /* Hover State */
    #rocket-cursor-main.is-hovering {
        transform: scale(1.25);
    }

    #rocket-cursor-main.is-hovering .rocket-svg {
        filter: drop-shadow(0px 0px 14px rgba(99, 102, 241, 0.85));
    }

    #rocket-cursor-main.is-hovering .rocket-target-ring {
        opacity: 1;
        transform: scale(1.1) rotate(90deg);
    }

    #rocket-cursor-main.is-hovering .rocket-flame-core {
        background: linear-gradient(180deg, #38BDF8 0%, #6366F1 60%, rgba(99, 102, 241, 0) 100%);
        animation-duration: 0.08s;
    }

    /* Click Active Down State */
    #rocket-cursor-main.is-clicking {
        transform: scale(0.9) rotate(-10deg);
    }

    /* Hide custom cursor on touchscreens */
    @media (hover: none) and (pointer: coarse) {
        #rocket-cursor-wrapper, #rocket-particle-canvas {
            display: none !important;
        }
        *, *::before, *::after, html, body, a, button, input {
            cursor: auto !important;
        }
    }
</style>

<!-- STANDALONE INTERACTIVE CALIBRATION & TEST BENCH STUDIO UI -->
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<div class="min-h-screen bg-slate-950 text-slate-100 font-['Inter'] p-4 md:p-8 selection:bg-indigo-500 selection:text-white">
    <div class="max-w-5xl mx-auto space-y-6">
        
        <!-- Header Section -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-800">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 text-xs font-semibold uppercase tracking-wider mb-2">
                    🚀 Cursor Calibration Workbench
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">
                    เครื่องมือปรับจุดโฟกัสของเคอร์เซอร์รูปจรวด
                </h1>
                <p class="text-sm text-slate-400 mt-1">
                    ปรับจุด Hotspot ให้ตรงกับปลายแหลมของหัวจรวด เพื่อให้คลิกเมนูและปุ่มบนเว็บได้อย่างแม่นยำ 100%
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <button id="toggle-pin-btn" class="px-4 py-2 text-xs font-semibold rounded-lg border border-slate-700 bg-slate-900 hover:bg-slate-800 transition text-slate-300">
                    📍 แสดงจุด Pin บนจรวด
                </button>
                <button id="reset-default-btn" class="px-4 py-2 text-xs font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white transition shadow-lg shadow-indigo-600/30">
                    🔄 รีเซ็ตค่าเริ่มต้น (Tip 21.0, 2.6)
                </button>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Left Panel: Interactive Stage Calibration Widget -->
            <div class="lg:col-span-7 bg-slate-900/80 backdrop-blur border border-slate-800 rounded-2xl p-5 shadow-xl flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-base font-semibold text-white flex items-center gap-2">
                            🎯 คลิกเลือกจุดปลายหัวจรวด
                        </h2>
                        <span class="text-xs text-slate-400">คลิกตรงตำแหน่งปลายจรวดเพื่อให้ตรงกับจุดสีแดง</span>
                    </div>

                    <!-- Interactive Calibration Stage -->
                    <div id="stage" class="relative w-full h-[320px] bg-slate-950 border border-slate-800 rounded-xl overflow-hidden cursor-crosshair select-none shadow-inner flex items-center justify-center">
                        <!-- Crosshair grid lines -->
                        <div class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b_1px,transparent_1px),linear-gradient(to_bottom,#1e293b_1px,transparent_1px)] bg-[size:20px_20px] opacity-40"></div>
                        
                        <!-- Fixed Red Target Hotspot Center -->
                        <div id="target" class="absolute w-3 h-3 rounded-full bg-red-500 border-2 border-white shadow-[0_0_12px_rgba(239,68,68,0.8)] -translate-x-1/2 -translate-y-1/2 z-20 pointer-events-none"></div>

                        <!-- Rocket Scaled Preview Box -->
                        <div id="box" class="absolute w-[210px] h-[210px] pointer-events-none transition-transform duration-75">
                            <svg class="w-full h-full drop-shadow-2xl" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M32 4C32 4 48 16 48 38C48 44 44 48 44 48L32 42L20 48C20 48 16 44 16 38C16 16 32 4 32 4Z" fill="url(#studio-rocket-body)" stroke="#ffffff" stroke-width="1.5" stroke-linejoin="round"/>
                                <path d="M32 4C37 10 42 20 43.5 26H20.5C22 20 27 10 32 4Z" fill="#EF4444"/>
                                <path d="M16 34L6 44C6 44 10 49 18 47L16 34Z" fill="#3B82F6" stroke="#ffffff" stroke-width="1"/>
                                <path d="M48 34L58 44C58 44 54 49 46 47L48 34Z" fill="#3B82F6" stroke="#ffffff" stroke-width="1"/>
                                <circle cx="32" cy="24" r="6" fill="#06B6D4" stroke="#ffffff" stroke-width="1.5"/>
                                <circle cx="30" cy="22" r="2" fill="#ffffff" opacity="0.8"/>
                                <path d="M26 44H38L36 49H28L26 44Z" fill="#475569"/>
                                <defs>
                                    <linearGradient id="studio-rocket-body" x1="16" y1="4" x2="48" y2="48" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#FFFFFF"/>
                                        <stop offset="0.6" stop-color="#E2E8F0"/>
                                        <stop offset="1" stop-color="#CBD5E1"/>
                                    </linearGradient>
                                </defs>
                            </svg>
                            <!-- Hotspot marker point -->
                            <div id="marker" class="absolute w-2.5 h-2.5 rounded-full bg-cyan-400 border border-white shadow-[0_0_8px_rgba(6,182,212,0.9)] -translate-x-1/2 -translate-y-1/2"></div>
                        </div>
                    </div>
                </div>

                <!-- Readout & Action Controls -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-5 pt-4 border-t border-slate-800">
                    <div class="text-sm text-slate-300 font-mono">
                        HOTSPOT_X, HOTSPOT_Y = <span id="readout" class="font-bold text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20">21.0, 2.6</span>
                    </div>
                    <button id="use-btn" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold text-xs rounded-xl shadow-lg shadow-indigo-500/25 transition active:scale-95 flex items-center justify-center gap-2">
                        <span>ใช้พิกัดนี้กับเคอร์เซอร์จริง</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </div>

            <!-- Right Panel: Fine-Tuning & Live Interaction Playground -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Manual Adjustment Controls -->
                <div class="bg-slate-900/80 backdrop-blur border border-slate-800 rounded-2xl p-5 shadow-xl space-y-4">
                    <h3 class="text-sm font-semibold text-slate-200 flex items-center gap-2">
                        ⚙️ ปรับความละเอียดพิกัด (Fine-Tune)
                    </h3>
                    
                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between text-xs text-slate-400 mb-1">
                                <span>HOTSPOT X (แนวนอน):</span>
                                <span id="slider-x-val" class="font-mono text-indigo-400 font-semibold">21.0px</span>
                            </div>
                            <input id="slider-x" type="range" min="0" max="42" step="0.1" value="21.0" class="w-full accent-indigo-500 bg-slate-800 rounded-lg h-2 cursor-pointer">
                        </div>

                        <div>
                            <div class="flex justify-between text-xs text-slate-400 mb-1">
                                <span>HOTSPOT Y (แนวตั้ง):</span>
                                <span id="slider-y-val" class="font-mono text-indigo-400 font-semibold">2.6px</span>
                            </div>
                            <input id="slider-y" type="range" min="0" max="42" step="0.1" value="2.6" class="w-full accent-indigo-500 bg-slate-800 rounded-lg h-2 cursor-pointer">
                        </div>
                    </div>
                </div>

                <!-- Live Test Sandbox -->
                <div class="bg-slate-900/80 backdrop-blur border border-slate-800 rounded-2xl p-5 shadow-xl space-y-4">
                    <h3 class="text-sm font-semibold text-slate-200 flex items-center gap-2">
                        🧪 ทดสอบการคลิกบนองค์ประกอบต่างๆ (Live Playground)
                    </h3>
                    <p class="text-xs text-slate-400">
                        ลองเลื่อนเมาส์ไปชี้และกดคลิกปุ่มด้านล่าง เพื่อสอบเช็คว่าปลายจรวดคลิกโดนเป้าหมายอย่างแม่นยำหรือไม่
                    </p>

                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <button class="px-3 py-2.5 bg-slate-800 hover:bg-slate-700 text-xs font-semibold rounded-xl border border-slate-700 transition text-slate-200 hover:text-white text-center active:scale-95">
                            🔘 ปุ่มทดสอบ A
                        </button>
                        <button class="px-3 py-2.5 bg-indigo-600/30 hover:bg-indigo-600/50 text-indigo-300 border border-indigo-500/30 text-xs font-semibold rounded-xl transition text-center active:scale-95">
                            ⚡ ปุ่มทดสอบ B
                        </button>
                    </div>

                    <div class="pt-1">
                        <input type="text" placeholder="พิมพ์ทดสอบในช่องนี้..." class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-200 focus:outline-none focus:border-indigo-500 transition">
                    </div>

                    <div class="p-3 bg-indigo-950/40 border border-indigo-800/40 rounded-xl flex items-center justify-between">
                        <span class="text-xs text-indigo-200">จำนวนการคลิกทดสอบ:</span>
                        <span id="click-counter" class="text-sm font-bold text-indigo-400 font-mono">0 ครั้ง</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';

    // Disable execution on touch devices
    if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
        return;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Auto-cleanup duplicate elements if PHP includes this file multiple times
        var wrappers = document.querySelectorAll('#rocket-cursor-wrapper');
        if (wrappers.length > 1) {
            for (var i = 1; i < wrappers.length; i++) {
                wrappers[i].parentNode.removeChild(wrappers[i]);
            }
        }
        var canvases = document.querySelectorAll('#rocket-particle-canvas');
        if (canvases.length > 1) {
            for (var j = 1; j < canvases.length; j++) {
                canvases[j].parentNode.removeChild(canvases[j]);
            }
        }

        var wrapper = document.getElementById('rocket-cursor-wrapper');
        var rocket = document.getElementById('rocket-cursor-main');
        var canvas = document.getElementById('rocket-particle-canvas');

        if (!wrapper || !rocket || !canvas) return;

        // Move cursor elements to end of document body to guarantee stacking context
        document.body.appendChild(canvas);
        document.body.appendChild(wrapper);

        var ctx = canvas.getContext('2d');
        var width = canvas.width = window.innerWidth;
        var height = canvas.height = window.innerHeight;

        window.addEventListener('resize', function() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        });

        // ------------------------------------------------------------------
        // HOTSPOT COORDINATE ENGINE (DEFAULT: Tip at X: 21.0px, Y: 2.6px)
        // ------------------------------------------------------------------
        var hotspot = { x: 21.0, y: 2.625 };

        function updateHotspotOffsets(hx, hy) {
            hotspot.x = hx;
            hotspot.y = hy;

            // Apply offsets to cursor element
            rocket.style.marginLeft = (-hotspot.x) + 'px';
            rocket.style.marginTop = (-hotspot.y) + 'px';
            rocket.style.transformOrigin = hotspot.x + 'px ' + hotspot.y + 'px';

            // Update internal pin & ring position
            var pin = rocket.querySelector('.rocket-tip-pin');
            var ring = rocket.querySelector('.rocket-target-ring');
            if (pin) {
                pin.style.left = hotspot.x + 'px';
                pin.style.top = hotspot.y + 'px';
            }
            if (ring) {
                ring.style.left = hotspot.x + 'px';
                ring.style.top = hotspot.y + 'px';
            }
        }

        // Initialize default offset
        updateHotspotOffsets(21.0, 2.625);

        var mouse = { x: 0, y: 0 };
        var pos = { x: 0, y: 0 };
        var lastPos = { x: 0, y: 0 };
        var angle = 0;
        var targetAngle = 0;
        var particles = [];
        var isHovered = false;
        var isInitialized = false;

        function onMouseMove(e) {
            mouse.x = e.clientX;
            mouse.y = e.clientY;

            // FIRST MOUSE MOVE FIX: Snap directly to mouse coordinates without LERP delay on refresh
            if (!isInitialized) {
                pos.x = mouse.x;
                pos.y = mouse.y;
                lastPos.x = mouse.x;
                lastPos.y = mouse.y;
                
                wrapper.style.transform = 'translate3d(' + pos.x + 'px, ' + pos.y + 'px, 0)';
                wrapper.style.opacity = '1';
                canvas.style.opacity = '1';
                isInitialized = true;
            } else {
                if (wrapper.style.opacity === '0') {
                    wrapper.style.opacity = '1';
                    canvas.style.opacity = '1';
                }
            }
        }

        window.addEventListener('mousemove', onMouseMove, { passive: true });

        document.addEventListener('mouseleave', function() {
            wrapper.style.opacity = '0';
            canvas.style.opacity = '0';
        });

        document.addEventListener('mouseenter', function() {
            if (isInitialized) {
                wrapper.style.opacity = '1';
                canvas.style.opacity = '1';
            }
        });

        var clickCounter = 0;
        var clickCounterEl = document.getElementById('click-counter');

        window.addEventListener('mousedown', function() {
            rocket.classList.add('is-clicking');
            if (isInitialized) {
                createBurstSparks(pos.x, pos.y, 16);
            }
            clickCounter++;
            if (clickCounterEl) {
                clickCounterEl.textContent = clickCounter + ' ครั้ง';
            }
        });

        window.addEventListener('mouseup', function() {
            rocket.classList.remove('is-clicking');
        });

        var interactiveSelector = 'a, button, input, select, textarea, [role="button"], .btn, .card, .glass-card, .accordion-button';

        document.addEventListener('mouseover', function(e) {
            if (e.target && e.target.closest(interactiveSelector)) {
                isHovered = true;
                rocket.classList.add('is-hovering');
            }
        }, { passive: true });

        document.addEventListener('mouseout', function(e) {
            if (e.target && e.target.closest(interactiveSelector)) {
                isHovered = false;
                rocket.classList.remove('is-hovering');
            }
        }, { passive: true });

        /* ------------------------------------------------------------------
           PARTICLE ENGINE (Smoke, Fire Thrusters & Click Sparks)
           ------------------------------------------------------------------ */
        function createThrusterParticle(x, y, vx, vy, isHoveredState) {
            particles.push({
                x: x,
                y: y,
                vx: vx + (Math.random() - 0.5) * 1.5,
                vy: vy + (Math.random() - 0.5) * 1.5,
                size: isHoveredState ? Math.random() * 5 + 3 : Math.random() * 4 + 2,
                color: isHoveredState ? 
                    (Math.random() > 0.4 ? '#38BDF8' : '#6366F1') : 
                    (Math.random() > 0.5 ? '#F59E0B' : '#EF4444'),
                alpha: 1,
                decay: Math.random() * 0.04 + 0.02
            });
        }

        function createBurstSparks(x, y, count) {
            for (var i = 0; i < count; i++) {
                var pAngle = Math.random() * Math.PI * 2;
                var speed = Math.random() * 5 + 2;
                particles.push({
                    x: x,
                    y: y,
                    vx: Math.cos(pAngle) * speed,
                    vy: Math.sin(pAngle) * speed,
                    size: Math.random() * 4 + 2,
                    color: '#10B981',
                    alpha: 1,
                    decay: 0.03
                });
            }
        }

        /* ------------------------------------------------------------------
           MAIN ANIMATION LOOP (LERP & Rotation around Tip Hotspot)
           ------------------------------------------------------------------ */
        function render() {
            if (isInitialized) {
                var lerpFactor = 0.25;
                pos.x += (mouse.x - pos.x) * lerpFactor;
                pos.y += (mouse.y - pos.y) * lerpFactor;

                var dx = pos.x - lastPos.x;
                var dy = pos.y - lastPos.y;
                var speed = Math.sqrt(dx * dx + dy * dy);

                if (speed > 0.4) {
                    targetAngle = Math.atan2(dy, dx) * (180 / Math.PI) + 90;
                }

                var dAngle = targetAngle - angle;
                while (dAngle < -180) dAngle += 360;
                while (dAngle > 180) dAngle -= 360;
                angle += dAngle * 0.2;

                wrapper.style.transform = 'translate3d(' + pos.x.toFixed(2) + 'px, ' + pos.y.toFixed(2) + 'px, 0)';
                rocket.style.transform = 'rotate(' + angle.toFixed(2) + 'deg)';

                // Spawn Thruster Flame Particles from tail (offset behind tip)
                if (speed > 0.8 || isHovered) {
                    var rad = (angle - 90) * (Math.PI / 180);
                    var tailX = pos.x - Math.cos(rad) * 30;
                    var tailY = pos.y - Math.sin(rad) * 30;
                    var backVx = -Math.cos(rad) * (speed * 0.3 + 1);
                    var backVy = -Math.sin(rad) * (speed * 0.3 + 1);

                    createThrusterParticle(tailX, tailY, backVx, backVy, isHovered);
                }

                // Render Canvas Particles
                ctx.clearRect(0, 0, width, height);
                for (var i = particles.length - 1; i >= 0; i--) {
                    var p = particles[i];
                    p.x += p.vx;
                    p.y += p.vy;
                    p.alpha -= p.decay;

                    if (p.alpha <= 0) {
                        particles.splice(i, 1);
                        continue;
                    }

                    ctx.save();
                    ctx.globalAlpha = p.alpha;
                    ctx.fillStyle = p.color;
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.restore();
                }

                lastPos.x = pos.x;
                lastPos.y = pos.y;
            }

            requestAnimationFrame(render);
        }

        requestAnimationFrame(render);

        // ------------------------------------------------------------------
        // CALIBRATION STUDIO WIDGET LOGIC
        // ------------------------------------------------------------------
        const SCALE = 5; // Studio preview scale factor
        const stage = document.getElementById('stage');
        const box = document.getElementById('box');
        const marker = document.getElementById('marker');
        const target = document.getElementById('target');
        const readout = document.getElementById('readout');
        const useBtn = document.getElementById('use-btn');
        const sliderX = document.getElementById('slider-x');
        const sliderY = document.getElementById('slider-y');
        const sliderXVal = document.getElementById('slider-x-val');
        const sliderYVal = document.getElementById('slider-y-val');
        const togglePinBtn = document.getElementById('toggle-pin-btn');
        const resetDefaultBtn = document.getElementById('reset-default-btn');

        let hx = 21.0, hy = 2.6;
        let tx = 0, ty = 0;

        function clamp(v, lo, hi){ return Math.max(lo, Math.min(hi, v)); }

        function placeStage(){
            if (!stage || !target) return;
            const rect = stage.getBoundingClientRect();
            tx = rect.width / 2;
            ty = rect.height / 2;
            target.style.left = tx + 'px';
            target.style.top = ty + 'px';
            renderStage();
        }

        function renderStage(){
            if (!box || !marker || !readout) return;
            box.style.left = (tx - hx * SCALE) + 'px';
            box.style.top = (ty - hy * SCALE) + 'px';
            box.style.transformOrigin = (hx * SCALE) + 'px ' + (hy * SCALE) + 'px';
            
            marker.style.left = (hx * SCALE) + 'px';
            marker.style.top = (hy * SCALE) + 'px';
            
            const xStr = hx.toFixed(1);
            const yStr = hy.toFixed(1);
            readout.textContent = xStr + ', ' + yStr;

            if (sliderX) { sliderX.value = hx; sliderXVal.textContent = xStr + 'px'; }
            if (sliderY) { sliderY.value = hy; sliderYVal.textContent = yStr + 'px'; }
        }

        if (stage) {
            stage.addEventListener('click', function(e){
                const rect = box.getBoundingClientRect();
                const localX = (e.clientX - rect.left) / SCALE;
                const localY = (e.clientY - rect.top) / SCALE;
                hx = clamp(Math.round(localX * 10) / 10, -10, 52);
                hy = clamp(Math.round(localY * 10) / 10, -10, 52);
                renderStage();
                updateHotspotOffsets(hx, hy);
            });
        }

        if (sliderX) {
            sliderX.addEventListener('input', function(e){
                hx = parseFloat(e.target.value);
                renderStage();
                updateHotspotOffsets(hx, hy);
            });
        }

        if (sliderY) {
            sliderY.addEventListener('input', function(e){
                hy = parseFloat(e.target.value);
                renderStage();
                updateHotspotOffsets(hx, hy);
            });
        }

        if (useBtn) {
            useBtn.addEventListener('click', function(){
                updateHotspotOffsets(hx, hy);
                alert('อัปเดตจุด Hotspot เป็น (X: ' + hx.toFixed(1) + ', Y: ' + hy.toFixed(1) + ') เรียบร้อยแล้ว!');
            });
        }

        if (resetDefaultBtn) {
            resetDefaultBtn.addEventListener('click', function(){
                hx = 21.0;
                hy = 2.625;
                renderStage();
                updateHotspotOffsets(hx, hy);
            });
        }

        if (togglePinBtn) {
            togglePinBtn.addEventListener('click', function(){
                document.body.classList.toggle('show-hotspot-pin');
            });
        }

        window.addEventListener('resize', placeStage, { passive: true });
        placeStage();
    });
})();
</script>