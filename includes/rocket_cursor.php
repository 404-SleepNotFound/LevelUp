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
    </div>

    <div id="rocket-cursor-main">
        <!-- Pure CSS Engine Flame Effect Base -->
        <div class="rocket-flame-container">
            <div class="rocket-flame-core"></div>
            <div class="rocket-flame-glow"></div>
        </div>
        
        <!-- SVG Rocket Icon (Nose tip calibrated at X: 32, Y: 4 in 64x64 space) -->
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
        transition: opacity 1.2s cubic-bezier(0.25, 1, 0.5, 1), transform 1.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        filter: drop-shadow(0 0 10px rgba(56, 189, 248, 0.8));
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
        border: 1.5px solid rgba(56, 189, 248, 0.5);
        border-top-color: rgba(168, 85, 247, 0.9);
        border-bottom-color: rgba(168, 85, 247, 0.9);
        border-radius: 50%;
        opacity: 0;
        pointer-events: none;
        transform: scale(0.5) rotate(0deg);
        transition: opacity 1.2s ease-in-out, transform 1.2s ease-in-out;
    }

    .rocket-core-aura {
        position: absolute;
        top: 0;
        left: 0;
        width: 56px;
        height: 56px;
        margin-top: -28px;
        margin-left: -28px;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.5) 0%, rgba(99, 102, 241, 0.3) 50%, rgba(0,0,0,0) 75%);
        border-radius: 50%;
        filter: blur(5px);
        opacity: 0;
        pointer-events: none;
        transform: scale(0.5);
        transition: opacity 1.2s ease-in-out, transform 1.2s ease-in-out;
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
        margin-top: -2.625px;
        pointer-events: none !important;
        transition: transform 0.18s cubic-bezier(0.2, 0.8, 0.2, 1);
        transform-origin: 21px 2.625px !important;
    }

    .rocket-svg {
        width: 100%;
        height: 100%;
        filter: drop-shadow(0px 4px 10px rgba(0, 0, 0, 0.35));
        transition: filter 0.2s ease;
    }

    /* Engine Flame Effect Base */
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

        var hotspot = { x: 21.0, y: 2.625 };

        function updateHotspotOffsets(hx, hy) {
            hotspot.x = hx;
            hotspot.y = hy;

            // Apply offsets to cursor element
            rocket.style.marginLeft = (-hotspot.x) + 'px';
            rocket.style.marginTop = (-hotspot.y) + 'px';
            rocket.style.transformOrigin = hotspot.x + 'px ' + hotspot.y + 'px';
        }

        // Initialize calibrated tip offset
        updateHotspotOffsets(21.0, 2.625);

        var mouse = { x: -100, y: -100 };
        var pos = { x: -100, y: -100 };
        var lastPos = { x: -100, y: -100 };
        var angle = 0;
        var targetAngle = 0;
        var particles = [];
        var isHovered = false;
        var isInitialized = false;

        var idleTime = 0;
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

            rocket.classList.remove('idle-stage-1', 'idle-stage-2', 'idle-stage-3');
            wrapper.classList.remove('idle-stage-1', 'idle-stage-2', 'idle-stage-3');

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
                rocket.classList.add('idle-stage-2');
                wrapper.classList.add('idle-stage-2');
                createBurstSparks(pos.x, pos.y, 8); // Minor gravitational pulse
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

                    // Continuous orbital angular velocity
                    var omega = 0.8;
                    var orbTheta = idleTime * omega;

                    if (idleStage === 1) {
                        // Stage 1 Motion: Gentle zero-G float
                        targetIdleX = Math.cos(idleTime * 0.7) * 3;
                        targetIdleY = Math.sin(idleTime) * 5;
                        idleSway = Math.sin(idleTime * 0.5) * 4;
                    } else {
                        // Stage 2 & 3: Seamless Spiral into Stable Planetary Orbit
                        var spiralDuration = 3.5;
                        var spiralProgress = Math.min(Math.max((idleTime - 0.8) / spiralDuration, 0), 1);
                        
                        // Smoothstep curve for seamless expansion from R=15 to R=80
                        var rEase = spiralProgress * spiralProgress * (3 - 2 * spiralProgress);
                        var rEaseDerivative = (6 * spiralProgress * (1 - spiralProgress)) / spiralDuration;

                        var startR = 15;
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

                // Spawn Thruster Flame Particles from tail (offset behind tip)
                if (speed > 0.8 || isHovered) {
                    var rad = (angle - 90) * (Math.PI / 180);
                    var tailX = (pos.x + currentIdleOffsetX) - Math.cos(rad) * 30;
                    var tailY = (pos.y + currentIdleOffsetY) - Math.sin(rad) * 30;
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