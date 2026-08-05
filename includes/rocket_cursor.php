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
    <!-- Mini Planet Core & Shield Matrix at Center Origin (0,0) -->
    <div id="rocket-center-core">
        <div class="rocket-mini-planet">
            <svg viewBox="0 0 32 32" class="planet-svg" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <radialGradient id="mini-planet-grad" cx="30%" cy="30%" r="70%">
                        <stop offset="0%" stop-color="#38BDF8"/>
                        <stop offset="60%" stop-color="#6366F1"/>
                        <stop offset="100%" stop-color="#1E1B4B"/>
                    </radialGradient>
                </defs>
                <!-- Planet Back Ring Orbit -->
                <path d="M 4 16 A 12 4 0 0 1 28 16" fill="none" stroke="rgba(56, 189, 248, 0.6)" stroke-width="1.8" transform="rotate(-20 16 16)" />
                <!-- Planet Sphere Body -->
                <circle cx="16" cy="16" r="6.5" fill="url(#mini-planet-grad)" />
                <!-- Planet Front Ring Orbit -->
                <path d="M 28 16 A 12 4 0 0 1 4 16" fill="none" stroke="rgba(168, 85, 247, 0.95)" stroke-width="1.8" transform="rotate(-20 16 16)" />
            </svg>
        </div>
        <div class="rocket-shield-ring"></div>
        <div class="rocket-core-aura"></div>
        <div class="rocket-shockwave-ring"></div>
    </div>

    <div id="rocket-cursor-main">
        <!-- Pure CSS Engine Flame Effect Base (Redesigned Tapered Plasma Cone) -->
        <div class="rocket-flame-container">
            <div class="rocket-flame-core"></div>
            <div class="rocket-flame-glow"></div>
            <div class="rocket-flame-aura"></div>
        </div>
        
        <!-- Modern Sleek Sci-Fi Rocket SVG (Redesigned Aerodynamic & Proportional Proportions) -->
        <svg class="rocket-svg" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g id="rocket-group">
                <!-- Swept-Back Left Wing Fin -->
                <path d="M22 28L10 42C9 43.5 12 46.5 16 45L22 39V28Z" fill="url(#fin-left-grad)"/>
                <path d="M22 28L13 41.5C14.5 43.5 16.5 44.5 18.5 43L22 39V28Z" fill="#1D4ED8" opacity="0.5"/>

                <!-- Swept-Back Right Wing Fin -->
                <path d="M42 28L54 42C55 43.5 52 46.5 48 45L42 39V28Z" fill="url(#fin-right-grad)"/>
                <path d="M42 28L51 41.5C49.5 43.5 47.5 44.5 45.5 43L42 39V28Z" fill="#1E3A8A" opacity="0.5"/>

                <!-- Sleek Exhaust Nozzle Bell -->
                <path d="M26 38H38L36 44C36 45 28 45 28 44L26 38Z" fill="url(#nozzle-grad)"/>
                <ellipse cx="32" cy="44" rx="4" ry="1" fill="#0F172A"/>

                <!-- Aerodynamic Fuselage Main Body -->
                <path d="M32 2C32 2 44 14 43 38C42 41 38 42.5 32 42.5C26 42.5 22 41 21 38C20 14 32 2 32 2Z" fill="url(#fuselage-grad)"/>

                <!-- Fuselage Volumetric 3D Shadow -->
                <path d="M32 2C32 2 44 14 43 38C42 41 38 42.5 32 42.5C32 42.5 40 37 40 24C40 12 32 2 32 2Z" fill="url(#fuselage-shadow-grad)" opacity="0.4"/>

                <!-- Specular Highlight Curve -->
                <path d="M31 4C31 4 25.5 16 25.5 28C25.5 34 27.5 38 29.5 40" stroke="url(#highlight-line-grad)" stroke-width="1.2" stroke-linecap="round" opacity="0.85"/>

                <!-- Sleek Red Nose Cap Tip (Calibrated Origin Hotspot at X:32, Y:2) -->
                <path d="M32 2C34 6 38.5 14 39 17.5C35.5 19 28.5 19 25 17.5C25.5 14 30 6 32 2Z" fill="url(#nose-grad)"/>
                <path d="M32 2C30.8 6.5 27 13.5 25.5 17" stroke="#FFFFFF" stroke-width="0.9" opacity="0.7" stroke-linecap="round"/>

                <!-- Metallic Porthole Window -->
                <circle cx="32" cy="24" r="5.5" fill="url(#bezel-grad)"/>
                <circle cx="32" cy="24" r="4.2" fill="url(#glass-grad)"/>
                <path d="M29.5 22.2C30 21.2 31.2 20.6 32.5 20.6" stroke="#FFFFFF" stroke-width="1" stroke-linecap="round" opacity="0.9"/>
                <circle cx="30.5" cy="25" r="0.7" fill="#FFFFFF" opacity="0.8"/>

                <!-- Hull Lower Accent Band -->
                <path d="M23 35.5C26.5 37 35.5 37 39 35.5C39.5 37.5 37.5 40 32 40C26.5 40 24.5 37.5 23 35.5Z" fill="url(#stripe-grad)"/>
            </g>

            <defs>
                <linearGradient id="fuselage-grad" x1="21" y1="2" x2="43" y2="42.5" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FFFFFF"/>
                    <stop offset="0.45" stop-color="#F1F5F9"/>
                    <stop offset="0.85" stop-color="#E2E8F0"/>
                    <stop offset="1" stop-color="#CBD5E1"/>
                </linearGradient>

                <linearGradient id="fuselage-shadow-grad" x1="32" y1="2" x2="43" y2="42.5" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#0F172A" stop-opacity="0"/>
                    <stop offset="0.6" stop-color="#334155" stop-opacity="0.25"/>
                    <stop offset="1" stop-color="#0F172A" stop-opacity="0.55"/>
                </linearGradient>

                <linearGradient id="highlight-line-grad" x1="31" y1="4" x2="29.5" y2="40" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FFFFFF" stop-opacity="0.95"/>
                    <stop offset="1" stop-color="#FFFFFF" stop-opacity="0.1"/>
                </linearGradient>

                <linearGradient id="nose-grad" x1="32" y1="2" x2="32" y2="19" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FF334B"/>
                    <stop offset="0.7" stop-color="#EF4444"/>
                    <stop offset="1" stop-color="#991B1B"/>
                </linearGradient>

                <linearGradient id="fin-left-grad" x1="10" y1="28" x2="22" y2="45" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#3B82F6"/>
                    <stop offset="1" stop-color="#1D4ED8"/>
                </linearGradient>

                <linearGradient id="fin-right-grad" x1="54" y1="28" x2="42" y2="45" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#2563EB"/>
                    <stop offset="1" stop-color="#1E3A8A"/>
                </linearGradient>

                <linearGradient id="nozzle-grad" x1="26" y1="38" x2="38" y2="45" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#64748B"/>
                    <stop offset="0.5" stop-color="#334155"/>
                    <stop offset="1" stop-color="#0F172A"/>
                </linearGradient>

                <linearGradient id="bezel-grad" x1="27" y1="19" x2="37" y2="29" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#94A3B8"/>
                    <stop offset="0.5" stop-color="#475569"/>
                    <stop offset="1" stop-color="#0F172A"/>
                </linearGradient>

                <radialGradient id="glass-grad" cx="30" cy="22" r="5" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#38BDF8"/>
                    <stop offset="0.65" stop-color="#0284C7"/>
                    <stop offset="1" stop-color="#0369A1"/>
                </radialGradient>

                <linearGradient id="stripe-grad" x1="23" y1="35" x2="39" y2="40" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#38BDF8"/>
                    <stop offset="1" stop-color="#2563EB"/>
                </linearGradient>
            </defs>
        </svg>

        <!-- Hover Target Indicator Ring -->
        <div class="rocket-target-ring"></div>
    </div>
</div>

<!-- Canvas for Engine Smoke & Spark Particles -->
<canvas id="rocket-particle-canvas"></canvas>

<style>
    /* ------------------------------------------------------------------
       1. GLOBAL CURSOR OVERRIDE & FIXED CONTAINER STYLES
       ------------------------------------------------------------------ */
    html, body, a, button, input, select, textarea, [role="button"] {
        cursor: none !important;
    }

    #rocket-cursor-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        width: 0;
        height: 0;
        pointer-events: none !important;
        z-index: 2147483646;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    #rocket-particle-canvas {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        pointer-events: none !important;
        z-index: 2147483645;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    /* ------------------------------------------------------------------
       2. CENTER PLANET & ORBIT CORE (FIXED AT RADIUS 0,0 ORIGIN)
       ------------------------------------------------------------------ */
    #rocket-center-core {
        position: absolute;
        top: 0;
        left: 0;
        width: 0;
        height: 0;
        pointer-events: none !important;
    }

    .rocket-mini-planet {
        position: absolute;
        top: 0;
        left: 0;
        width: 44px;
        height: 44px;
        margin-top: -22px;
        margin-left: -22px;
        opacity: 0;
        pointer-events: none;
        transform: scale(0.3);
        transition: opacity 0.8s cubic-bezier(0.25, 1, 0.5, 1), transform 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        filter: drop-shadow(0 2px 10px rgba(15, 23, 42, 0.4)) drop-shadow(0 0 8px rgba(56, 189, 248, 0.8));
    }

    .planet-svg {
        width: 100%;
        height: 100%;
        animation: planetFloat 4s infinite ease-in-out;
    }

    .rocket-shield-ring {
        position: absolute;
        top: 0;
        left: 0;
        width: 64px;
        height: 64px;
        margin-top: -32px;
        margin-left: -32px;
        border: 2px solid rgba(79, 70, 229, 0.75);
        border-top-color: rgba(147, 51, 234, 0.95);
        border-bottom-color: rgba(147, 51, 234, 0.95);
        border-radius: 50%;
        opacity: 0;
        pointer-events: none;
        transform: scale(0.5) rotate(0deg);
        transition: opacity 0.8s ease-in-out, transform 0.8s ease-in-out;
        box-shadow: 0 0 12px rgba(99, 102, 241, 0.35);
    }

    .rocket-core-aura {
        position: absolute;
        top: 0;
        left: 0;
        width: 56px;
        height: 56px;
        margin-top: -28px;
        margin-left: -28px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.45) 0%, rgba(139, 92, 246, 0.25) 50%, rgba(0,0,0,0) 75%);
        border-radius: 50%;
        filter: blur(5px);
        opacity: 0;
        pointer-events: none;
        transform: scale(0.5);
        transition: opacity 0.8s ease-in-out, transform 0.8s ease-in-out;
    }

    /* Shockwave Energy Pulse for Stage Transition */
    .rocket-shockwave-ring {
        position: absolute;
        top: 0;
        left: 0;
        width: 20px;
        height: 20px;
        margin-top: -10px;
        margin-left: -10px;
        border: 2px solid rgba(56, 189, 248, 0.9);
        border-radius: 50%;
        opacity: 0;
        pointer-events: none;
        transform: scale(0.2);
        box-shadow: 0 0 15px rgba(168, 85, 247, 0.8);
    }

    #rocket-cursor-wrapper.trigger-shockwave .rocket-shockwave-ring {
        animation: shockwaveExpand 0.85s cubic-bezier(0.1, 0.8, 0.3, 1) forwards;
    }

    @keyframes shockwaveExpand {
        0% { transform: scale(0.2); opacity: 1; border-width: 3px; }
        50% { opacity: 0.8; }
        100% { transform: scale(7.5); opacity: 0; border-width: 1px; }
    }

    /* Stage 1: Soft Charging Warm-up */
    #rocket-cursor-wrapper.idle-stage-1 .rocket-mini-planet {
        opacity: 0.3;
        transform: scale(0.55);
    }
    #rocket-cursor-wrapper.idle-stage-1 .rocket-shield-ring {
        opacity: 0.25;
        transform: scale(0.6) rotate(0deg);
        animation: shieldRotate 8s infinite linear;
    }
    #rocket-cursor-wrapper.idle-stage-1 .rocket-core-aura {
        opacity: 0.3;
        transform: scale(0.6);
    }

    /* Stage 2: Gravitational Orbital Insertion */
    #rocket-cursor-wrapper.idle-stage-2 .rocket-mini-planet {
        opacity: 0.85;
        transform: scale(0.95);
    }
    #rocket-cursor-wrapper.idle-stage-2 .rocket-shield-ring {
        opacity: 0.65;
        transform: scale(0.85);
        animation: shieldRotate 5s infinite linear;
    }
    #rocket-cursor-wrapper.idle-stage-2 .rocket-core-aura {
        opacity: 0.7;
        animation: auraPulse 2.5s infinite alternate ease-in-out;
    }

    /* Stage 3: Full Orbit Acrobatics */
    #rocket-cursor-wrapper.idle-stage-3 .rocket-mini-planet {
        opacity: 1;
        transform: scale(1.15);
    }
    #rocket-cursor-wrapper.idle-stage-3 .rocket-shield-ring {
        opacity: 0.85;
        transform: scale(1);
        animation: shieldRotate 3.5s infinite linear;
    }
    #rocket-cursor-wrapper.idle-stage-3 .rocket-core-aura {
        opacity: 1;
        animation: auraPulse 2s infinite alternate ease-in-out;
    }

    @keyframes planetFloat {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-3px) rotate(6deg); }
    }

    @keyframes shieldRotate {
        0% { transform: scale(1) rotate(0deg); }
        100% { transform: scale(1) rotate(360deg); }
    }

    @keyframes auraPulse {
        0% { transform: scale(0.8); opacity: 0.5; }
        100% { transform: scale(1.35); opacity: 0.95; }
    }

    /* ------------------------------------------------------------------
       3. ROCKET MAIN BODY & HOTSPOT CALIBRATION
       ------------------------------------------------------------------ */
    #rocket-cursor-main {
        position: absolute;
        top: 0;
        left: 0;
        width: 42px;
        height: 42px;
        margin-left: -21px;
        margin-top: -1.31px; /* Precision alignment for tip Y:2 in 64x64 grid */
        pointer-events: none !important;
        transition: transform 0.18s cubic-bezier(0.2, 0.8, 0.2, 1);
        transform-origin: 21px 1.31px !important;
    }

    .rocket-svg {
        width: 100%;
        height: 100%;
        filter: drop-shadow(0px 4px 10px rgba(15, 23, 42, 0.35)) drop-shadow(0px 1px 3px rgba(15, 23, 42, 0.25));
        transition: filter 0.2s ease;
    }

    /* Precision Engine Flame Positioning (Origins directly from inside Nozzle Bell at Y:44) */
    .rocket-flame-container {
        position: absolute;
        top: 28.5px; /* Perfectly aligned to the nozzle exit */
        left: 50%;
        transform: translateX(-50%);
        width: 10px;
        height: 22px;
        pointer-events: none;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        z-index: -1; /* Placed behind rocket nozzle */
    }

    .rocket-flame-core {
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #FFFFFF 0%, #FDE047 20%, #F97316 60%, rgba(239, 68, 68, 0) 100%);
        border-radius: 50% 50% 40% 40% / 80% 80% 20% 20%;
        animation: rocketPlasmaBurn 0.08s infinite alternate ease-in-out;
        filter: drop-shadow(0 0 5px rgba(250, 204, 21, 0.9));
    }

    .rocket-flame-glow {
        position: absolute;
        top: 0px;
        width: 8px;
        height: 16px;
        background: linear-gradient(180deg, rgba(253, 224, 71, 0.95) 0%, rgba(249, 115, 22, 0.7) 65%, rgba(239, 68, 68, 0) 100%);
        border-radius: 50% 50% 45% 45%;
        filter: blur(1.5px);
        animation: rocketPlasmaGlow 0.12s infinite alternate ease-in-out;
    }

    .rocket-flame-aura {
        position: absolute;
        top: -2px;
        width: 14px;
        height: 14px;
        background: radial-gradient(circle, rgba(249, 115, 22, 0.5) 0%, rgba(168, 85, 247, 0.2) 65%, transparent 100%);
        border-radius: 50%;
        filter: blur(3px);
    }

    @keyframes rocketPlasmaBurn {
        0% { transform: scaleY(0.85) scaleX(0.85); opacity: 0.9; }
        100% { transform: scaleY(1.25) scaleX(1.1); opacity: 1; }
    }

    @keyframes rocketPlasmaGlow {
        0% { transform: scale(0.85); opacity: 0.65; }
        100% { transform: scale(1.2); opacity: 0.95; }
    }

    /* Hover Target Indicator Ring */
    .rocket-target-ring {
        position: absolute;
        top: 21px;
        left: 21px;
        width: 52px;
        height: 52px;
        margin-top: -26px;
        margin-left: -26px;
        border: 1.5px dashed rgba(56, 189, 248, 0.85);
        border-radius: 50%;
        opacity: 0;
        transform: scale(0.6) rotate(0deg);
        transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        pointer-events: none;
        box-shadow: 0 0 10px rgba(56, 189, 248, 0.2);
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

<script>
(function() {
    'use strict';

    // Disable execution only on pure touch mobile devices (allows touchscreen laptops)
    if (window.matchMedia && window.matchMedia('(hover: none) and (pointer: coarse)').matches) {
        return;
    }

    function initCursor() {
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
        var dpr = window.devicePixelRatio || 1;
        var width = window.innerWidth;
        var height = window.innerHeight;

        function resizeCanvas() {
            width = window.innerWidth;
            height = window.innerHeight;
            canvas.width = width * dpr;
            canvas.height = height * dpr;
            ctx.scale(dpr, dpr);
        }
        resizeCanvas();

        window.addEventListener('resize', resizeCanvas);

        // Calibrated tip origin (X: 32, Y: 2 in SVG grid -> X: 21px, Y: 1.31px)
        var hotspot = { x: 21.0, y: 1.31 };

        function updateHotspotOffsets(hx, hy) {
            hotspot.x = hx;
            hotspot.y = hy;

            // Apply offsets to cursor element
            rocket.style.marginLeft = (-hotspot.x) + 'px';
            rocket.style.marginTop = (-hotspot.y) + 'px';
            rocket.style.transformOrigin = hotspot.x + 'px ' + hotspot.y + 'px';
        }

        // Initialize calibrated tip offset
        updateHotspotOffsets(21.0, 1.31);

        var mouse = { x: -100, y: -100 };
        var pos = { x: -100, y: -100 };
        var lastPos = { x: -100, y: -100 };
        var angle = 0;
        var targetAngle = 0;
        var particles = [];
        var isHovered = false;
        var isInitialized = false;

        var idleTime = 0;
        var stage2Time = 0; // Track Stage 2 elapsed time separately for smooth spiral expansion
        var idleStage = 0; // 0: Active, 1: Drift (0.8s+), 2: Orbital Insertion (3s+), 3: Orbit Acrobatics (7s+)
        var idleTimerStage1 = null;
        var idleTimerStage2 = null;
        var idleTimerStage3 = null;

        // Smoothing state variables for seamless Stage 2 -> Stage 3 transitions
        var currentIdleOffsetX = 0;
        var currentIdleOffsetY = 0;

        function resetIdleTimer() {
            idleStage = 0;
            idleTime = 0;
            stage2Time = 0;

            rocket.classList.remove('idle-stage-1', 'idle-stage-2', 'idle-stage-3', 'trigger-shockwave');
            wrapper.classList.remove('idle-stage-1', 'idle-stage-2', 'idle-stage-3', 'trigger-shockwave');

            if (idleTimerStage1) clearTimeout(idleTimerStage1);
            if (idleTimerStage2) clearTimeout(idleTimerStage2);
            if (idleTimerStage3) clearTimeout(idleTimerStage3);

            // Stage 1: Zero-G Float (after 0.8s)
            idleTimerStage1 = setTimeout(function() {
                idleStage = 1;
                rocket.classList.add('idle-stage-1');
                wrapper.classList.add('idle-stage-1');
            }, 800);

            // Stage 2: Gravitational Orbital Insertion (after 3.0s)
            idleTimerStage2 = setTimeout(function() {
                idleStage = 2;
                stage2Time = 0; // Reset stage 2 spiral progression clock
                rocket.classList.add('idle-stage-2');
                wrapper.classList.add('idle-stage-2');
                
                // Trigger visual shockwave pulse effect
                wrapper.classList.remove('trigger-shockwave');
                void wrapper.offsetWidth; // Force CSS reflow
                wrapper.classList.add('trigger-shockwave');

                createGravitationalPulse(pos.x, pos.y);
            }, 3000);

            // Stage 3: Full Satellite Orbit & Stardust Field (after 7.0s)
            idleTimerStage3 = setTimeout(function() {
                idleStage = 3;
                rocket.classList.add('idle-stage-3');
                wrapper.classList.add('idle-stage-3');
            }, 7000);
        }

        function updateMousePosition(clientX, clientY) {
            mouse.x = clientX;
            mouse.y = clientY;

            resetIdleTimer();

            // FIRST MOUSE MOVE FIX: Snap directly to mouse coordinates without LERP delay
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

        function onMouseMove(e) {
            updateMousePosition(e.clientX, e.clientY);
        }

        window.addEventListener('mousemove', onMouseMove, { passive: true });
        window.addEventListener('pointermove', onMouseMove, { passive: true });

        document.addEventListener('mouseleave', function() {
            wrapper.style.opacity = '0';
            canvas.style.opacity = '0';
        });

        document.addEventListener('mouseenter', function(e) {
            if (e.clientX && e.clientY) {
                updateMousePosition(e.clientX, e.clientY);
            }
        });

        window.addEventListener('mousedown', function() {
            rocket.classList.add('is-clicking');
            if (isInitialized) {
                createBurstSparks(pos.x, pos.y, 16);
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

        // Radar Scan Energy Wave Pulse for Stage 2 (Emitted smoothly from nose tip)
        function createRadarScanPulse(px, py, angleDeg) {
            var rad = (angleDeg - 90) * (Math.PI / 180);
            var speed = Math.random() * 1.8 + 0.8;
            
            // Cyan Sonar Beam Spark
            particles.push({
                x: px + Math.cos(rad) * 12,
                y: py + Math.sin(rad) * 12,
                vx: Math.cos(rad) * speed + (Math.random() - 0.5) * 0.6,
                vy: Math.sin(rad) * speed + (Math.random() - 0.5) * 0.6,
                size: Math.random() * 3.5 + 1.5,
                color: Math.random() > 0.3 ? '#00F0FF' : '#38BDF8',
                alpha: 0.95,
                decay: 0.025
            });
        }

        // Energy burst pulse emitted when Stage 2 initiates
        function createGravitationalPulse(x, y) {
            for (var i = 0; i < 18; i++) {
                var pAngle = (i / 18) * Math.PI * 2;
                var speed = Math.random() * 2.5 + 1.5;
                particles.push({
                    x: x,
                    y: y,
                    vx: Math.cos(pAngle) * speed,
                    vy: Math.sin(pAngle) * speed,
                    size: Math.random() * 3.5 + 2,
                    color: i % 2 === 0 ? '#38BDF8' : '#A855F7',
                    alpha: 1,
                    decay: 0.025
                });
            }
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

                // Shortest path angular interpolation
                var radDiff = (targetAngle - angle) * (Math.PI / 180);
                var shortestDiff = Math.atan2(Math.sin(radDiff), Math.cos(radDiff)) * (180 / Math.PI);
                angle += shortestDiff * 0.2;

                // Target offsets for smooth LERP positioning
                var targetIdleX = 0;
                var targetIdleY = 0;
                var idleSway = 0;

                if (idleStage > 0) {
                    idleTime += 0.035;

                    if (idleStage === 1) {
                        // Stage 1 Motion: Gentle zero-G float
                        targetIdleX = Math.cos(idleTime * 0.7) * 3;
                        targetIdleY = Math.sin(idleTime) * 5;
                        idleSway = Math.sin(idleTime * 0.5) * 4;
                    } else {
                        // Stage 2 & 3: Seamless Spiral into Stable Planetary Orbit
                        stage2Time += 0.035;
                        
                        var spiralDuration = 2.5; // Smooth 2.5s transition into full orbit
                        var spiralProgress = Math.min(stage2Time / spiralDuration, 1);
                        
                        // Smoothstep curve for seamless expansion from R=5 to R=80
                        var rEase = spiralProgress * spiralProgress * (3 - 2 * spiralProgress);
                        var rEaseDerivative = (6 * spiralProgress * (1 - spiralProgress)) / spiralDuration;

                        var omega = 0.8;
                        var orbTheta = stage2Time * omega;

                        var startR = 5;
                        var endR = 80;
                        var currentRadius = startR + (endR - startR) * rEase;
                        var dRdt = (endR - startR) * rEaseDerivative;

                        targetIdleX = Math.cos(orbTheta) * currentRadius;
                        targetIdleY = Math.sin(orbTheta) * currentRadius;

                        // Exact tangent velocity vector (differential calculus)
                        var vx = dRdt * Math.cos(orbTheta) - currentRadius * omega * Math.sin(orbTheta);
                        var vy = dRdt * Math.sin(orbTheta) + currentRadius * omega * Math.cos(orbTheta);
                        var orbitalAngle = Math.atan2(vy, vx) * (180 / Math.PI) + 90;

                        // Shortest-path angular interpolation to prevent sudden rotation snaps
                        var angleRadDiff = (orbitalAngle - targetAngle) * (Math.PI / 180);
                        var shortestAngleDiff = Math.atan2(Math.sin(angleRadDiff), Math.cos(angleRadDiff)) * (180 / Math.PI);
                        targetAngle += shortestAngleDiff * 0.12;

                        var stardustRamp = 0;
                        if (idleStage === 2) {
                            stardustRamp = spiralProgress * 0.35; // Faint subtle dust during spiral expansion
                        } else if (idleStage === 3) {
                            var stage3Time = Math.max(0, idleTime - 4.3);
                            stardustRamp = 0.35 + 0.65 * Math.min(stage3Time / 2.5, 1.0); // Smooth ramp to full density over 2.5s
                        }

                        // Stage 2: Sonar pulse emissions
                        if (idleStage === 2 && Math.random() < 0.3) {
                            createRadarScanPulse(pos.x + targetIdleX, pos.y + targetIdleY, targetAngle);
                        }

                        // Stage 3: Thruster trail particles
                        if (idleStage === 3) {
                            var vMag = Math.hypot(vx, vy) || 1;
                            if (Math.random() < 0.6) {
                                var orbTailX = (pos.x + targetIdleX) - (vx / vMag) * 24;
                                var orbTailY = (pos.y + targetIdleY) - (vy / vMag) * 24;
                                createThrusterParticle(orbTailX, orbTailY, -vx * 0.03, -vy * 0.03, true);
                            }
                        }

                        // Emit Stardust with Smooth Fade-In Lifecycle
                        if (stardustRamp > 0 && Math.random() < 0.7 * stardustRamp) {
                            var orbRad = Math.random() * Math.PI * 2;
                            var orbDist = currentRadius + (Math.random() - 0.5) * 16;
                            var vibrantColors = ['#00F0FF', '#FF007F', '#FFD700', '#FF5500', '#A020F0', '#00FF88', '#38BDF8', '#FF2A55'];
                            
                            particles.push({
                                x: pos.x + Math.cos(orbRad) * orbDist,
                                y: pos.y + Math.sin(orbRad) * orbDist,
                                vx: -Math.sin(orbRad) * (Math.random() * 1.0 + 0.2),
                                vy: Math.cos(orbRad) * (Math.random() * 1.0 + 0.2),
                                size: Math.random() * 4.0 + 1.8,
                                color: vibrantColors[Math.floor(Math.random() * vibrantColors.length)],
                                alpha: 0, // Starts fully transparent!
                                targetAlpha: Math.random() * 0.65 + 0.25,
                                fadeIn: Math.random() * 0.025 + 0.015, // Smooth gradual fade-in
                                decay: Math.random() * 0.008 + 0.004 // Smooth fade-out decay
                            });
                        }
                    }

                    // Periodic idle thruster pulse particles
                    if (Math.random() < 0.15) {
                        var idleRad = (angle + idleSway - 90) * (Math.PI / 180);
                        var idleTailX = (pos.x + currentIdleOffsetX) - Math.cos(idleRad) * 30;
                        var idleTailY = (pos.y + currentIdleOffsetY) - Math.sin(idleRad) * 30;
                        createThrusterParticle(idleTailX, idleTailY, -Math.cos(idleRad) * 0.8, -Math.sin(idleRad) * 0.8, false);
                    }
                }

                // Smooth LERP interpolation for idle offsets to eliminate any sudden movement jumps
                var idleLerp = 0.15;
                currentIdleOffsetX += (targetIdleX - currentIdleOffsetX) * idleLerp;
                currentIdleOffsetY += (targetIdleY - currentIdleOffsetY) * idleLerp;

                var renderAngle = angle + idleSway;

                // Wrapper stays positioned at origin (pos.x, pos.y) where the central planet sits at (0,0)
                wrapper.style.transform = 'translate3d(' + pos.x.toFixed(2) + 'px, ' + pos.y.toFixed(2) + 'px, 0)';
                
                // Rocket offsets by currentIdleOffsetX/currentIdleOffsetY and rotates around its calibrated nose tip
                rocket.style.transform = 'translate3d(' + currentIdleOffsetX.toFixed(2) + 'px, ' + currentIdleOffsetY.toFixed(2) + 'px, 0) rotate(' + renderAngle.toFixed(2) + 'deg)';
                // Spawn Thruster Flame Particles directly from nozzle base
                if (speed > 0.8 || isHovered) {
                    var rad = (angle - 90) * (Math.PI / 180);
                    // Precision offset from tip to nozzle outlet (approx 27px behind tip)
                    var tailX = (pos.x + currentIdleOffsetX) - Math.cos(rad) * 27;
                    var tailY = (pos.y + currentIdleOffsetY) - Math.sin(rad) * 27;
                    var backVx = -Math.cos(rad) * (speed * 0.3 + 1);
                    var backVy = -Math.sin(rad) * (speed * 0.3 + 1);

                    createThrusterParticle(tailX, tailY, backVx, backVy, isHovered);
                }

                // Render Canvas Particles with High-Contrast Shadows for Light Mode
                ctx.clearRect(0, 0, width, height);
                for (var i = particles.length - 1; i >= 0; i--) {
                    var p = particles[i];
                    p.x += p.vx;
                    p.y += p.vy;

                    // Smooth Particle Lifecycle: Fade-In then Fade-Out Decay
                    if (p.fadeIn && p.fadeIn > 0) {
                        p.alpha += p.fadeIn;
                        if (p.alpha >= p.targetAlpha) {
                            p.alpha = p.targetAlpha;
                            p.fadeIn = 0; // Fade-in phase complete, proceed to decay
                        }
                    } else {
                        p.alpha -= p.decay;
                    }

                    if (p.alpha <= 0) {
                        particles.splice(i, 1);
                        continue;
                    }

                    ctx.save();
                    ctx.globalAlpha = Math.max(0, p.alpha);
                    ctx.shadowColor = 'rgba(15, 23, 42, 0.4)';
                    ctx.shadowBlur = 4;
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
    }

    // Immediate or DOMReady activation fallback
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCursor);
    } else {
        initCursor();
    }
})();
</script>