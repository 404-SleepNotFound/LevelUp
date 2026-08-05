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

        <!-- Deep Idle Stage 2: Plasma Core Aura & Rotating Shield Matrix -->
        <div class="rocket-core-aura"></div>
        <div class="rocket-shield-ring"></div>

        <!-- Deep Idle Stage 3: Orbiting Mini Satellite Drone -->
        <div class="rocket-mini-drone"></div>
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

    /* Hover Target Indicator Ring - Centered around rocket body */
    .rocket-target-ring {
        position: absolute;
        top: 24px;
        left: 21px;
        width: 48px;
        height: 48px;
        margin-top: -24px;
        margin-left: -24px;
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

    /* ------------------------------------------------------------------
       DEEP IDLE STAGE 2: PLASMA CORE AURA & ROTATING SHIELD MATRIX
       ------------------------------------------------------------------ */
    .rocket-core-aura {
        position: absolute;
        top: 21px;
        left: 21px;
        width: 50px;
        height: 50px;
        margin-top: -25px;
        margin-left: -25px;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.45) 0%, rgba(99, 102, 241, 0.25) 50%, rgba(0,0,0,0) 75%);
        border-radius: 50%;
        filter: blur(4px);
        opacity: 0;
        pointer-events: none;
        transform: scale(0.5);
        transition: all 0.4s ease;
    }

    .rocket-shield-ring {
        position: absolute;
        top: 21px;
        left: 21px;
        width: 64px;
        height: 64px;
        margin-top: -32px;
        margin-left: -32px;
        border: 1.5px solid rgba(56, 189, 248, 0.6);
        border-top-color: rgba(168, 85, 247, 0.9);
        border-bottom-color: rgba(168, 85, 247, 0.9);
        border-radius: 50%;
        opacity: 0;
        pointer-events: none;
        transform: scale(0.6) rotate(0deg);
        transition: opacity 0.4s ease, transform 0.4s ease;
    }

    #rocket-cursor-main.idle-stage-2 .rocket-core-aura {
        opacity: 1;
        animation: auraPulse 2s infinite alternate ease-in-out;
    }

    #rocket-cursor-main.idle-stage-2 .rocket-shield-ring {
        opacity: 1;
        animation: shieldRotate 3s infinite linear;
    }

    #rocket-cursor-main.idle-stage-2 .rocket-flame-core {
        background: linear-gradient(180deg, #38BDF8 0%, #A855F7 60%, rgba(168, 85, 247, 0) 100%);
    }

    @keyframes auraPulse {
        0% { transform: scale(0.8); opacity: 0.5; }
        100% { transform: scale(1.35); opacity: 0.95; }
    }

    @keyframes shieldRotate {
        0% { transform: scale(1) rotate(0deg); }
        100% { transform: scale(1) rotate(360deg); }
    }

    /* ------------------------------------------------------------------
       DEEP IDLE STAGE 3: WIDE SPACE ORBIT & SATALITE DRONE
       ------------------------------------------------------------------ */
    .rocket-mini-drone {
        position: absolute;
        top: 2.625px;
        left: 21px;
        width: 12px;
        height: 12px;
        margin-top: -6px;
        margin-left: -6px;
        background: #EF4444;
        border: 2px solid #FFFFFF;
        border-radius: 50%;
        box-shadow: 0 0 12px #EF4444, 0 0 24px #F59E0B;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.5s ease;
    }

    #rocket-cursor-main.idle-stage-3 .rocket-mini-drone {
        opacity: 1;
        top: -58.7px;
        left: -7.6px;
        animation: droneRedPulse 0.35s infinite alternate ease-in-out;
    }

    /* RED ALERT BEACON: High-contrast flashing red beacon with evasive micro-jiggle */
    @keyframes droneRedPulse {
        0% {
            background: #EF4444;
            box-shadow: 0 0 10px #EF4444, 0 0 20px #EF4444;
            transform: scale(0.85) translate(0, 0);
        }
        100% {
            background: #FF0055;
            box-shadow: 0 0 22px #FF0055, 0 0 35px #F59E0B, 0 0 45px #EF4444;
            transform: scale(1.35) translate(2px, -2px);
        }
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
        // HOTSPOT COORDINATE ENGINE (DEFAULT: Tip at X: 21.0px, Y: 2.625px)
        // ------------------------------------------------------------------
        var hotspot = { x: 21.0, y: 2.625 };

        function updateHotspotOffsets(hx, hy) {
            hotspot.x = hx;
            hotspot.y = hy;

            // Apply offsets to cursor element
            rocket.style.marginLeft = (-hotspot.x) + 'px';
            rocket.style.marginTop = (-hotspot.y) + 'px';
            rocket.style.transformOrigin = hotspot.x + 'px ' + hotspot.y + 'px';

            // Update internal pin position
            var pin = rocket.querySelector('.rocket-tip-pin');
            if (pin) {
                pin.style.left = hotspot.x + 'px';
                pin.style.top = hotspot.y + 'px';
            }
        }

        // Initialize calibrated tip offset
        updateHotspotOffsets(21.0, 2.625);

        var mouse = { x: 0, y: 0 };
        var pos = { x: 0, y: 0 };
        var lastPos = { x: 0, y: 0 };
        var angle = 0;
        var targetAngle = 0;
        var particles = [];
        var isHovered = false;
        var isInitialized = false;

        // IDLE MULTI-STAGE SYSTEM STATE
        var idleTime = 0;
        var idleStage = 0; // 0: Active, 1: Drift (0.8s+), 2: Radar Scan (3s+), 3: Orbit Acrobatics (7s+)
        var idleTimerStage1 = null;
        var idleTimerStage2 = null;
        var idleTimerStage3 = null;

        function resetIdleTimer() {
            idleStage = 0;
            idleTime = 0;
            rocket.classList.remove('idle-stage-1', 'idle-stage-2', 'idle-stage-3');

            if (idleTimerStage1) clearTimeout(idleTimerStage1);
            if (idleTimerStage2) clearTimeout(idleTimerStage2);
            if (idleTimerStage3) clearTimeout(idleTimerStage3);

            // Stage 1: Zero-G Float (after 0.8s)
            idleTimerStage1 = setTimeout(function() {
                idleStage = 1;
                rocket.classList.add('idle-stage-1');
            }, 800);

            // Stage 2: Deep Radar Scan (after 3.0s)
            idleTimerStage2 = setTimeout(function() {
                idleStage = 2;
                rocket.classList.add('idle-stage-2');
                createBurstSparks(pos.x, pos.y, 8); // Minor scan energy pulse
            }, 3000);

            // Stage 3: Orbit Satellite & Stardust Field (after 7.0s)
            idleTimerStage3 = setTimeout(function() {
                idleStage = 3;
                rocket.classList.add('idle-stage-3');
                createOrbitStardustRing(pos.x, pos.y);
            }, 7000);
        }

        function onMouseMove(e) {
            mouse.x = e.clientX;
            mouse.y = e.clientY;

            resetIdleTimer();

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

        function createOrbitStardustRing(cx, cy) {
            for (var i = 0; i < 32; i++) {
                var rad = (i / 32) * Math.PI * 2;
                var dist = 75 + Math.random() * 20; // Expanded to match wide orbit
                particles.push({
                    x: cx + Math.cos(rad) * dist,
                    y: cy + Math.sin(rad) * dist,
                    vx: -Math.sin(rad) * 1.5,
                    vy: Math.cos(rad) * 1.5,
                    size: Math.random() * 3.5 + 1.5,
                    color: Math.random() > 0.5 ? '#06B6D4' : '#A855F7',
                    alpha: 0.95,
                    decay: 0.012
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

                // IDLE BOBBING, SWAY & ACROBATIC ROTATIONS
                var idleOffsetX = 0;
                var idleOffsetY = 0;
                var idleSway = 0;

                if (idleStage > 0) {
                    idleTime += 0.04;

                    // Stage 1 Motion (0.8s - 3s): Gentle zero-G float
                    idleOffsetY = Math.sin(idleTime) * 4;
                    idleOffsetX = Math.cos(idleTime * 0.7) * 2;
                    idleSway = Math.sin(idleTime * 0.5) * 4;

                    // Stage 2 Motion (3s - 7s): Plasma Charge & Infinity Figure-8 Float
                    if (idleStage >= 2) {
                        idleOffsetY += Math.sin(idleTime * 2) * 5;
                        idleOffsetX += Math.cos(idleTime * 1) * 6;
                        idleSway += Math.sin(idleTime * 1.5) * 8;
                        
                        // Plasma energy particles emission
                        if (Math.random() < 0.25) {
                            createThrusterParticle(pos.x + (Math.random() - 0.5) * 16, pos.y + (Math.random() - 0.5) * 16, (Math.random() - 0.5) * 0.8, (Math.random() - 0.5) * 0.8, true);
                        }
                    }

                    // Stage 3 Motion (7s+ Continuous Loop): Dogfight Chase Orbit along 80px Radius
                    if (idleStage === 3) {
                        var orbitSpeed = idleTime * 0.5; // Significantly slowed down orbital rotation speed
                        var rocketRadius = 80; // Rocket flies at 80px radius matching drone chase path

                        idleOffsetX = Math.cos(orbitSpeed) * rocketRadius;
                        idleOffsetY = Math.sin(orbitSpeed) * rocketRadius;

                        // Tangent flight vector so rocket nose points forward along circular path
                        var vx = -Math.sin(orbitSpeed);
                        var vy = Math.cos(orbitSpeed);

                        // Direct angle along orbital movement direction
                        targetAngle = Math.atan2(vy, vx) * (180 / Math.PI) + 90;

                        // Continuous thruster trail shooting backwards out of rocket engine along orbit arc
                        if (Math.random() < 0.6) {
                            var orbTailX = (pos.x + idleOffsetX) - vx * 24;
                            var orbTailY = (pos.y + idleOffsetY) - vy * 24;
                            createThrusterParticle(orbTailX, orbTailY, -vx * 1.5, -vy * 1.5, true);
                        }

                        // Spawn periodic stardust trailing particles along 80px orbit chase track
                        if (Math.random() < 0.4) {
                            var orbRad = Math.random() * Math.PI * 2;
                            var orbDist = 80 + (Math.random() - 0.5) * 16;
                            particles.push({
                                x: pos.x + Math.cos(orbRad) * orbDist,
                                y: pos.y + Math.sin(orbRad) * orbDist,
                                vx: -Math.sin(orbRad) * 0.5,
                                vy: Math.cos(orbRad) * 0.5,
                                size: Math.random() * 3.5 + 1.5,
                                color: Math.random() > 0.4 ? '#EF4444' : (Math.random() > 0.5 ? '#38BDF8' : '#F59E0B'),
                                alpha: 0.9,
                                decay: 0.015
                            });
                        }
                    }

                    // Periodic idle thruster pulse particles
                    if (Math.random() < 0.15) {
                        var idleRad = (angle + idleSway - 90) * (Math.PI / 180);
                        var idleTailX = (pos.x + idleOffsetX) - Math.cos(idleRad) * 30;
                        var idleTailY = (pos.y + idleOffsetY) - Math.sin(idleRad) * 30;
                        createThrusterParticle(idleTailX, idleTailY, -Math.cos(idleRad) * 0.8, -Math.sin(idleRad) * 0.8, false);
                    }
                }

                var renderX = pos.x + idleOffsetX;
                var renderY = pos.y + idleOffsetY;
                var renderAngle = angle + idleSway;

                wrapper.style.transform = 'translate3d(' + renderX.toFixed(2) + 'px, ' + renderY.toFixed(2) + 'px, 0)';
                rocket.style.transform = 'rotate(' + renderAngle.toFixed(2) + 'deg)';

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
    });
})();
</script>