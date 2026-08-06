<?php
/**
 * LevelUp Rocket Cursor Component v3.6 (Pro Sci-Fi Orbital Edition)
 * - Complete Redesign: Aerodynamic vector model with balanced proportions & sleek lighting.
 * - Calibrated Hotspot: Mouse click origin aligned perfectly to rocket nose tip.
 * - Dynamic Plasma Flame, Radar Scan, Gravitational Orbit & Particle Trail effect.
 * Usage in PHP: <?php include 'rocket-cursor.php'; ?>
 */
?>

<div id="rocket-cursor-wrapper" aria-hidden="true">
    <!-- Center Planet Core & Orbit Matrix at Mouse Point (0,0) -->
    <div id="rocket-center-core">
        <div class="rocket-mini-planet">
            <svg viewBox="0 0 40 40" class="planet-svg" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <radialGradient id="mini-planet-grad" cx="35%" cy="30%" r="65%">
                        <stop offset="0%" style="stop-color: rgb(var(--rocket-cyan-rgb))"/>
                        <stop offset="50%" style="stop-color: rgb(var(--rocket-indigo-rgb))"/>
                        <stop offset="100%" stop-color="#0F172A"/>
                    </radialGradient>
                    <linearGradient id="ring-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color: rgb(var(--rocket-cyan-rgb)); stop-opacity: 0.9"/>
                        <stop offset="50%" style="stop-color: rgb(var(--rocket-purple-rgb)); stop-opacity: 0.8"/>
                        <stop offset="100%" style="stop-color: rgb(var(--rocket-pink-rgb)); stop-opacity: 0.3"/>
                    </linearGradient>
                </defs>
                <!-- Planet Back Orbit Ring -->
                <ellipse cx="20" cy="20" rx="17" ry="5" fill="none" stroke="url(#ring-grad)" stroke-width="2" transform="rotate(-22 20 20)" stroke-dasharray="25 10"/>
                <!-- Planet Sphere Body -->
                <circle cx="20" cy="20" r="8.5" fill="url(#mini-planet-grad)"/>
                <!-- Planet Highlight Arc -->
                <path d="M 14 17 A 6.5 6.5 0 0 1 23 14" fill="none" stroke="#FFFFFF" stroke-width="1" opacity="0.6"/>
                <!-- Planet Front Orbit Ring -->
                <path d="M 4 20 A 17 5 0 0 0 36 20" fill="none" stroke="url(#ring-grad)" stroke-width="2" transform="rotate(-22 20 20)"/>
            </svg>
        </div>
        <div class="rocket-shield-ring"></div>
        <div class="rocket-radar-sweep"></div>
        <div class="rocket-core-aura"></div>
        <div class="rocket-shockwave-ring"></div>
    </div>

    <div id="rocket-cursor-main">
        <!-- Pure CSS Plasma Engine Jet Cone Base -->
        <div class="rocket-flame-container">
            <div class="rocket-flame-core"></div>
            <div class="rocket-flame-glow"></div>
            <div class="rocket-flame-aura"></div>
        </div>

        <!-- Modern Proportional Vector Rocket SVG Model (Calibrated Hotspot Nose Tip at X:32, Y:2) -->
        <svg class="rocket-svg" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <!-- Sleek Fuselage Metallic Gradient -->
                <linearGradient id="fuselage-grad" x1="16" y1="2" x2="48" y2="44" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#FFFFFF"/>
                    <stop offset="40%" stop-color="#F8FAFC"/>
                    <stop offset="80%" stop-color="#E2E8F0"/>
                    <stop offset="100%" stop-color="#CBD5E1"/>
                </linearGradient>

                <!-- 3D Curved Body Shadow -->
                <linearGradient id="fuselage-shadow-grad" x1="32" y1="2" x2="48" y2="44" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#0F172A" stop-opacity="0"/>
                    <stop offset="50%" stop-color="#334155" stop-opacity="0.15"/>
                    <stop offset="100%" stop-color="#0F172A" stop-opacity="0.45"/>
                </linearGradient>

                <!-- Red Nose Cone Gradient -->
                <linearGradient id="nose-grad" x1="32" y1="2" x2="32" y2="18" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#FF3B5C"/>
                    <stop offset="60%" stop-color="#EF4444"/>
                    <stop offset="100%" stop-color="#B91C1C"/>
                </linearGradient>

                <!-- Left Fin Gradient -->
                <linearGradient id="fin-left-grad" x1="8" y1="26" x2="21" y2="44" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#38BDF8"/>
                    <stop offset="100%" stop-color="#1D4ED8"/>
                </linearGradient>

                <!-- Right Fin Gradient -->
                <linearGradient id="fin-right-grad" x1="56" y1="26" x2="43" y2="44" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#0284C7"/>
                    <stop offset="100%" stop-color="#1E3A8A"/>
                </linearGradient>

                <!-- Glass Porthole Lens Gradient -->
                <radialGradient id="glass-grad" cx="30" cy="22" r="6" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#38BDF8"/>
                    <stop offset="70%" stop-color="#0284C7"/>
                    <stop offset="100%" stop-color="#0369A1"/>
                </radialGradient>

                <!-- Nozzle Metallic Gradient -->
                <linearGradient id="nozzle-grad" x1="26" y1="38" x2="38" y2="44" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#64748B"/>
                    <stop offset="50%" stop-color="#334155"/>
                    <stop offset="100%" stop-color="#0F172A"/>
                </linearGradient>
            </defs>

            <g id="rocket-group">
                <!-- Swept Aerodynamic Left Fin -->
                <path d="M21 28 L13 34 L15 41 L21 38 Z" fill="url(#fin-left-grad)"/>
                <path d="M16 38.5 L13 34 L15 41 L18 40 Z" fill="#1E3A8A" opacity="0.35"/>

                <!-- Swept Aerodynamic Right Fin -->
                <path d="M43 28 L51 34 L49 41 L43 38 Z" fill="url(#fin-right-grad)"/>
                <path d="M48 38.5 L51 34 L49 41 L46 40 Z" fill="#0F172A" opacity="0.35"/>

                <!-- Heavy Engine Nozzle Bell -->
                <path d="M24 37 H40 L38 45 H26 Z" fill="url(#nozzle-grad)"/>
                <ellipse cx="32" cy="45" rx="6" ry="1.8" fill="#020617"/>
                <ellipse cx="32" cy="45" rx="4.5" ry="1.2" fill="#38BDF8" opacity="0.85"/>

                <!-- Fuselage Main Body Capsule -->
                <path d="M23 16.5 C21 24 21 38 21 38 C21 40.5 26 42 32 42 C38 42 43 40.5 43 38 C43 38 43 24 41 16.5 C36.5 17.8 27.5 17.8 23 16.5 Z" fill="url(#fuselage-grad)"/>

                <!-- Fuselage 3D Volumetric Shadow Overlay -->
                <path d="M32 16.5 C32 16.5 41 20 41 38 C41 40.5 38 42 32 42 C32 42 41 37 41 24 C41 20 32 16.5 32 16.5 Z" fill="url(#fuselage-shadow-grad)"/>

                <!-- High-Gloss Specular Reflection Curve -->
                <path d="M29 19 C26.8 24 26.2 31 27.2 38" stroke="#FFFFFF" stroke-width="1.2" stroke-linecap="round" opacity="0.75"/>

                <!-- Red Nose Cone Cap -->
                <path d="M32 2 C36.5 6.5 41 12 41 16.5 C36.5 17.8 27.5 17.8 23 16.5 C23 12 27.5 6.5 32 2 Z" fill="url(#nose-grad)"/>

                <!-- Porthole Window Bezel & Lens -->
                <circle cx="32" cy="25" r="5.8" fill="#475569"/>
                <circle cx="32" cy="25" r="4.5" fill="url(#glass-grad)"/>
                <path d="M29.8 23.2 C30.4 22.2 31.4 21.7 32.8 21.8" stroke="#FFFFFF" stroke-width="1.0" stroke-linecap="round" opacity="0.9"/>
                <circle cx="30.2" cy="26.2" r="0.7" fill="#FFFFFF" opacity="0.85"/>
            </g>
        </svg>

        <!-- Hover Target Indicator Ring -->
        <div class="rocket-target-ring"></div>
    </div>
</div>

<!-- Canvas Element for Particle Trail Effects -->
<canvas id="rocket-particle-canvas"></canvas>

<style>
    /* 0. THEME VARIABLES — auto (prefers-color-scheme) + manual override support */
    :root {
        --rocket-cyan-rgb: 56, 189, 248;
        --rocket-indigo-rgb: 99, 102, 241;
        --rocket-purple-rgb: 168, 85, 247;
        --rocket-pink-rgb: 236, 72, 153;
        --rocket-gold-rgb: 253, 224, 71;
        --rocket-halo-rgb: 15, 23, 42;
        --rocket-halo-alpha: 0;
    }

    @media (prefers-color-scheme: light) {
        :root {
            --rocket-cyan-rgb: 2, 132, 199;
            --rocket-indigo-rgb: 67, 56, 202;
            --rocket-purple-rgb: 147, 51, 234;
            --rocket-pink-rgb: 190, 24, 93;
            --rocket-gold-rgb: 180, 83, 9;
            --rocket-halo-alpha: 0.22;
        }
    }

    html.dark, html[data-theme="dark"] {
        --rocket-cyan-rgb: 56, 189, 248;
        --rocket-indigo-rgb: 99, 102, 241;
        --rocket-purple-rgb: 168, 85, 247;
        --rocket-pink-rgb: 236, 72, 153;
        --rocket-gold-rgb: 253, 224, 71;
        --rocket-halo-alpha: 0;
    }

    html.light, html[data-theme="light"] {
        --rocket-cyan-rgb: 2, 132, 199;
        --rocket-indigo-rgb: 67, 56, 202;
        --rocket-purple-rgb: 147, 51, 234;
        --rocket-pink-rgb: 190, 24, 93;
        --rocket-gold-rgb: 180, 83, 9;
        --rocket-halo-alpha: 0.22;
    }

    /* 1. GLOBAL CURSOR OVERRIDE & FIXED CONTAINERS */
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

    /* 2. CENTER PLANET & ORBIT CORE */
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
        width: 46px;
        height: 46px;
        margin-top: -23px;
        margin-left: -23px;
        opacity: 0;
        pointer-events: none;
        transform: scale(0.3);
        transition: opacity 0.8s cubic-bezier(0.25, 1, 0.5, 1), transform 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        filter: drop-shadow(0 2px 10px rgba(15, 23, 42, 0.4)) drop-shadow(0 0 10px rgba(var(--rocket-cyan-rgb), 0.8));
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
        border: 1.5px solid rgba(var(--rocket-indigo-rgb), 0.75);
        border-top: 2px solid rgba(var(--rocket-cyan-rgb), 0.95);
        border-bottom: 2px solid rgba(var(--rocket-purple-rgb), 0.95);
        border-radius: 50%;
        opacity: 0;
        pointer-events: none;
        transform: scale(0.5) rotate(0deg);
        transition: opacity 0.8s ease-in-out, transform 0.8s ease-in-out;
        box-shadow: 0 0 12px rgba(var(--rocket-indigo-rgb), 0.35), 0 0 9px rgba(var(--rocket-halo-rgb), var(--rocket-halo-alpha));
    }

    .rocket-radar-sweep {
        position: absolute;
        top: 0;
        left: 0;
        width: 70px;
        height: 70px;
        margin-top: -35px;
        margin-left: -35px;
        border-radius: 50%;
        opacity: 0;
        pointer-events: none;
        background: conic-gradient(from 0deg, rgba(var(--rocket-cyan-rgb), 0.55) 0deg, rgba(var(--rocket-cyan-rgb), 0) 55deg, transparent 360deg);
        -webkit-mask-image: radial-gradient(circle, transparent 54%, black 56%, black 100%);
        mask-image: radial-gradient(circle, transparent 54%, black 56%, black 100%);
        transition: opacity 0.5s ease;
    }

    .rocket-core-aura {
        position: absolute;
        top: 0;
        left: 0;
        width: 56px;
        height: 56px;
        margin-top: -28px;
        margin-left: -28px;
        background: radial-gradient(circle, rgba(var(--rocket-indigo-rgb), 0.45) 0%, rgba(var(--rocket-purple-rgb), 0.2) 50%, rgba(0, 0, 0, 0) 75%);
        border-radius: 50%;
        filter: blur(5px);
        opacity: 0;
        pointer-events: none;
        transform: scale(0.5);
        transition: opacity 0.8s ease-in-out, transform 0.8s ease-in-out;
    }

    .rocket-shockwave-ring {
        position: absolute;
        top: 0;
        left: 0;
        width: 20px;
        height: 20px;
        margin-top: -10px;
        margin-left: -10px;
        border: 2px solid rgba(var(--rocket-cyan-rgb), 0.9);
        border-radius: 50%;
        opacity: 0;
        pointer-events: none;
        transform: scale(0.2);
        box-shadow: 0 0 15px rgba(var(--rocket-purple-rgb), 0.8), 0 0 10px rgba(var(--rocket-halo-rgb), var(--rocket-halo-alpha));
    }

    #rocket-cursor-wrapper.trigger-shockwave .rocket-shockwave-ring {
        animation: shockwaveExpand 0.85s cubic-bezier(0.1, 0.8, 0.3, 1) forwards;
    }

    @keyframes shockwaveExpand {
        0% { transform: scale(0.2); opacity: 1; border-width: 4px; }
        50% { opacity: 0.85; }
        100% { transform: scale(9); opacity: 0; border-width: 1px; }
    }

    /* Idle Animation States — Stage 1: Standby (0.8s), Stage 2: Scanning (3s), Stage 3: Deep Orbit (7s) */
    #rocket-cursor-wrapper.idle-stage-1 .rocket-mini-planet { opacity: 0.4; transform: scale(0.62); }
    #rocket-cursor-wrapper.idle-stage-1 .rocket-shield-ring { opacity: 0.35; transform: scale(0.62) rotate(0deg); animation: shieldRotate 9s infinite linear; }
    #rocket-cursor-wrapper.idle-stage-1 .rocket-core-aura { opacity: 0.4; transform: scale(0.6); animation: auraPulse 3.6s infinite alternate ease-in-out; }

    #rocket-cursor-wrapper.idle-stage-2 .rocket-mini-planet { opacity: 0.85; transform: scale(0.95); }
    #rocket-cursor-wrapper.idle-stage-2 .rocket-shield-ring { opacity: 0.75; transform: scale(0.85); animation: shieldRotate 4.5s infinite linear; }
    #rocket-cursor-wrapper.idle-stage-2 .rocket-radar-sweep { opacity: 0.85; animation: radarSpin 2.2s linear infinite; }
    #rocket-cursor-wrapper.idle-stage-2 .rocket-core-aura { opacity: 0.75; animation: auraPulse 2.5s infinite alternate ease-in-out; }

    #rocket-cursor-wrapper.idle-stage-3 .rocket-mini-planet { opacity: 1; transform: scale(1.15); animation: idleShimmer 3s infinite ease-in-out; }
    #rocket-cursor-wrapper.idle-stage-3 .rocket-shield-ring { opacity: 0.95; transform: scale(1); animation: shieldRotate 3.6s infinite linear; }
    #rocket-cursor-wrapper.idle-stage-3 .rocket-radar-sweep { opacity: 1; animation: radarSpin 1.6s linear infinite; }
    #rocket-cursor-wrapper.idle-stage-3 .rocket-core-aura { opacity: 1; animation: auraPulse 2.1s infinite alternate ease-in-out; }

    @keyframes planetFloat {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-3px) rotate(6deg); }
    }

    @keyframes shieldRotate {
        0% { transform: scale(1) rotate(0deg); }
        100% { transform: scale(1) rotate(360deg); }
    }

    @keyframes radarSpin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    @keyframes auraPulse {
        0% { transform: scale(0.8); opacity: 0.5; }
        100% { transform: scale(1.35); opacity: 0.95; }
    }

    @keyframes idleShimmer {
        0%, 100% { filter: drop-shadow(0 2px 10px rgba(15, 23, 42, 0.4)) drop-shadow(0 0 10px rgba(var(--rocket-cyan-rgb), 0.8)); }
        50% { filter: drop-shadow(0 2px 10px rgba(15, 23, 42, 0.4)) drop-shadow(0 0 15px rgba(var(--rocket-purple-rgb), 0.95)); }
    }

    /* 3. ROCKET MAIN BODY & HOTSPOT CALIBRATION */
    #rocket-cursor-main {
        position: absolute;
        top: 0;
        left: 0;
        width: 42px;
        height: 42px;
        margin-left: -21px;
        margin-top: -1.31px;
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

    /* Plasma Engine Flame Cone Base */
    .rocket-flame-container {
        position: absolute;
        top: 29.5px;
        left: 50%;
        transform: translateX(-50%);
        width: 14px;
        height: 15px;
        pointer-events: none;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        z-index: -1;
    }

    .rocket-flame-core {
        width: 7px;
        height: 100%;
        background: linear-gradient(180deg, #FFFFFF 0%, #FDE047 25%, #F97316 65%, rgba(239, 68, 68, 0) 100%);
        border-radius: 50% 50% 40% 40% / 80% 80% 20% 20%;
        animation: rocketPlasmaBurn 0.08s infinite alternate ease-in-out;
        filter: drop-shadow(0 0 6px rgba(250, 204, 21, 0.9));
        transition: transform 0.15s ease, background 0.15s ease, filter 0.15s ease;
    }

    .rocket-flame-glow {
        position: absolute;
        top: 0px;
        width: 11px;
        height: 12px;
        background: linear-gradient(180deg, rgba(253, 224, 71, 0.95) 0%, rgba(249, 115, 22, 0.7) 65%, rgba(239, 68, 68, 0) 100%);
        border-radius: 50% 50% 45% 45%;
        filter: blur(2px);
        animation: rocketPlasmaGlow 0.12s infinite alternate ease-in-out;
        transition: transform 0.15s ease;
    }

    .rocket-flame-aura {
        position: absolute;
        top: -2px;
        width: 16px;
        height: 16px;
        background: radial-gradient(circle, rgba(249, 115, 22, 0.5) 0%, rgba(168, 85, 247, 0.2) 65%, transparent 100%);
        border-radius: 50%;
        filter: blur(3px);
        transition: transform 0.15s ease;
    }

    @keyframes rocketPlasmaBurn {
        0% { transform: scaleY(0.8) scaleX(0.85); opacity: 0.9; }
        100% { transform: scaleY(1.05) scaleX(1.05); opacity: 1; }
    }

    @keyframes rocketPlasmaGlow {
        0% { transform: scale(0.8); opacity: 0.65; }
        100% { transform: scale(1.05); opacity: 0.95; }
    }

    /* Target Hover Ring */
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
        box-shadow: 0 0 10px rgba(56, 189, 248, 0.25);
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

    /* Movement Thrust State (bigger, brighter flame while dragging) */
    #rocket-cursor-main.is-moving .rocket-flame-core {
        transform: scaleY(1.7) scaleX(1.3) !important;
        filter: drop-shadow(0 0 12px rgba(250, 204, 21, 1)) drop-shadow(0 0 20px rgba(249, 115, 22, 0.95)) !important;
    }

    #rocket-cursor-main.is-moving .rocket-flame-glow {
        transform: scale(1.6) !important;
    }

    #rocket-cursor-main.is-moving .rocket-flame-aura {
        transform: scale(1.4);
    }

    /* Click Active State (body recoil + plasma flare combo) */
    #rocket-cursor-main.is-clicking {
        transform: scale(0.9) rotate(-10deg);
    }

    #rocket-cursor-main.is-clicking .rocket-flame-core {
        background: linear-gradient(180deg, #FFFFFF 0%, #38BDF8 30%, #A855F7 70%, rgba(168, 85, 247, 0) 100%) !important;
        transform: scaleY(2.4) scaleX(1.6) !important;
        filter: drop-shadow(0 0 20px rgba(56, 189, 248, 1)) drop-shadow(0 0 32px rgba(168, 85, 247, 1)) !important;
    }

    #rocket-cursor-main.is-clicking .rocket-flame-glow {
        transform: scale(2) !important;
    }

    /* Touch Device Fallback */
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

    // Disable execution on touch/coarse pointer devices
    if (window.matchMedia && window.matchMedia('(hover: none) and (pointer: coarse)').matches) {
        return;
    }

    function initCursor() {
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

        // Calibrated Tip Offset
        var hotspot = { x: 21.0, y: 1.31 };

        function updateHotspotOffsets(hx, hy) {
            hotspot.x = hx;
            hotspot.y = hy;
            rocket.style.marginLeft = (-hotspot.x) + 'px';
            rocket.style.marginTop = (-hotspot.y) + 'px';
            rocket.style.transformOrigin = hotspot.x + 'px ' + hotspot.y + 'px';
        }

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
        var stage2Time = 0;
        var idleStage = 0;
        var idleTimerStage1 = null;
        var idleTimerStage2 = null;
        var idleTimerStage3 = null;

        var currentIdleOffsetX = 0;
        var currentIdleOffsetY = 0;

        function getThemeMode() {
            var root = document.documentElement;
            if (root.classList.contains('dark') || root.getAttribute('data-theme') === 'dark') return 'dark';
            if (root.classList.contains('light') || root.getAttribute('data-theme') === 'light') return 'light';
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) return 'light';
            return 'dark';
        }

        var themeMode = getThemeMode();

        if (window.matchMedia) {
            var schemeQuery = window.matchMedia('(prefers-color-scheme: light)');
            var onSchemeChange = function() { themeMode = getThemeMode(); };
            if (schemeQuery.addEventListener) {
                schemeQuery.addEventListener('change', onSchemeChange);
            } else if (schemeQuery.addListener) {
                schemeQuery.addListener(onSchemeChange);
            }
        }

        var idlePalette = {
            dark: {
                radar: ['#00F0FF', '#A855F7'],
                pulse: ['#00F0FF', '#FF007F'],
                stardust: ['#00F0FF', '#E0E7FF', '#FFD700', '#F472B6', '#A78BFA', '#34D399', '#38BDF8']
            },
            light: {
                radar: ['#0369A1', '#4338CA'],
                pulse: ['#0369A1', '#BE185D'],
                stardust: ['#0891B2', '#4338CA', '#B45309', '#BE185D', '#6D28D9', '#047857', '#0369A1']
            }
        };

        function resetIdleTimer() {
            idleStage = 0;
            idleTime = 0;
            stage2Time = 0;

            rocket.classList.remove('idle-stage-1', 'idle-stage-2', 'idle-stage-3', 'trigger-shockwave');
            wrapper.classList.remove('idle-stage-1', 'idle-stage-2', 'idle-stage-3', 'trigger-shockwave');

            if (idleTimerStage1) clearTimeout(idleTimerStage1);
            if (idleTimerStage2) clearTimeout(idleTimerStage2);
            if (idleTimerStage3) clearTimeout(idleTimerStage3);

            idleTimerStage1 = setTimeout(function() {
                idleStage = 1;
                rocket.classList.add('idle-stage-1');
                wrapper.classList.add('idle-stage-1');
            }, 800);

            idleTimerStage2 = setTimeout(function() {
                idleStage = 2;
                stage2Time = 0;
                rocket.classList.add('idle-stage-2');
                wrapper.classList.add('idle-stage-2');

                wrapper.classList.remove('trigger-shockwave');
                void wrapper.offsetWidth;
                wrapper.classList.add('trigger-shockwave');

                createGravitationalPulse(pos.x, pos.y);
            }, 3000);

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

            wrapper.classList.remove('trigger-shockwave');
            void wrapper.offsetWidth;
            wrapper.classList.add('trigger-shockwave');

            if (isInitialized) {
                createBurstSparks(pos.x + currentIdleOffsetX, pos.y + currentIdleOffsetY, targetAngle, 32);
                createGravitationalPulse(pos.x + currentIdleOffsetX, pos.y + currentIdleOffsetY);
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

        function getEngineNozzlePos(centerX, centerY, currentAngleDeg) {
            var rad = (currentAngleDeg - 90) * (Math.PI / 180);
            var nozzleDist = 28;
            return {
                x: centerX - Math.cos(rad) * nozzleDist,
                y: centerY - Math.sin(rad) * nozzleDist
            };
        }

        function createThrusterParticle(x, y, vx, vy, isHoveredState) {
            particles.push({
                x: x,
                y: y,
                vx: vx + (Math.random() - 0.5) * 1.5,
                vy: vy + (Math.random() - 0.5) * 1.5,
                size: isHoveredState ? Math.random() * 6.0 + 4.0 : Math.random() * 5.0 + 3.5,
                color: isHoveredState ?
                    (Math.random() > 0.4 ? '#00F0FF' : '#A855F7') :
                    (Math.random() > 0.5 ? '#FFE600' : '#FF0055'),
                alpha: 1.0,
                decay: Math.random() * 0.03 + 0.018
            });
        }

        function createBurstSparks(centerX, centerY, currentAngleDeg, count) {
            var nozzle = getEngineNozzlePos(centerX, centerY, currentAngleDeg);
            for (var i = 0; i < count; i++) {
                var pAngle = Math.random() * Math.PI * 2;
                var speed = Math.random() * 7.5 + 2.5;
                particles.push({
                    x: nozzle.x,
                    y: nozzle.y,
                    vx: Math.cos(pAngle) * speed,
                    vy: Math.sin(pAngle) * speed,
                    size: Math.random() * 7.0 + 4.5,
                    color: i % 3 === 0 ? '#00F0FF' : (i % 3 === 1 ? '#FFE600' : '#FF0055'),
                    alpha: 1.0,
                    decay: Math.random() * 0.03 + 0.015
                });
            }
        }

        function createRadarScanPulse(px, py, angleDeg) {
            var nozzle = getEngineNozzlePos(px, py, angleDeg);
            var rad = (angleDeg + 90 + (Math.random() - 0.5) * 60) * (Math.PI / 180);
            var speed = Math.random() * 1.2 + 0.4;

            particles.push({
                x: nozzle.x + Math.cos(rad) * 4,
                y: nozzle.y + Math.sin(rad) * 4,
                vx: Math.cos(rad) * speed,
                vy: Math.sin(rad) * speed,
                size: Math.random() * 4.0 + 2.5,
                color: Math.random() > 0.4 ? idlePalette[themeMode].radar[0] : idlePalette[themeMode].radar[1],
                alpha: 0.85,
                decay: 0.03
            });
        }

        function createGravitationalPulse(x, y) {
            for (var i = 0; i < 24; i++) {
                var pAngle = (i / 24) * Math.PI * 2;
                var speed = Math.random() * 2.5 + 1.2;
                particles.push({
                    x: x,
                    y: y,
                    vx: Math.cos(pAngle) * speed,
                    vy: Math.sin(pAngle) * speed,
                    size: Math.random() * 5.0 + 2.8,
                    color: i % 2 === 0 ? idlePalette[themeMode].pulse[0] : idlePalette[themeMode].pulse[1],
                    alpha: 1.0,
                    decay: 0.024
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

                var radDiff = (targetAngle - angle) * (Math.PI / 180);
                var shortestDiff = Math.atan2(Math.sin(radDiff), Math.cos(radDiff)) * (180 / Math.PI);
                angle += shortestDiff * 0.2;

                var targetIdleX = 0;
                var targetIdleY = 0;
                var idleSway = 0;

                if (idleStage > 0) {
                    idleTime += 0.035;

                    if (idleStage === 1) {
                        targetIdleX = Math.cos(idleTime * 0.7) * 3;
                        targetIdleY = Math.sin(idleTime) * 5;
                        idleSway = Math.sin(idleTime * 0.5) * 4;
                    } else {
                        stage2Time += 0.035;
                        var T = stage2Time;

                        var growDuration = 3.0;
                        var growProgress = Math.min(T / growDuration, 1);
                        var growEase = growProgress * growProgress * (3 - 2 * growProgress);

                        // Balanced orbit radius: min 12px, max 80px (expanded orbit)
                        var minR = 12;
                        var maxR = 80;
                        var baseR = minR + (maxR - minR) * growEase;
                        var breathe = growProgress >= 1 ? Math.sin(T * 0.6) * 4 : 0;
                        var currentRadius = baseR + breathe;

                        var dRdt = growProgress < 1 ? (maxR - minR) * ((6 * growProgress * (1 - growProgress)) / growDuration) : 0;

                        var omega = 0.75;
                        var orbTheta = T * omega;

                        targetIdleX = Math.cos(orbTheta) * currentRadius;
                        targetIdleY = Math.sin(orbTheta) * currentRadius;

                        var vx = dRdt * Math.cos(orbTheta) - currentRadius * omega * Math.sin(orbTheta);
                        var vy = dRdt * Math.sin(orbTheta) + currentRadius * omega * Math.cos(orbTheta);
                        var orbitalAngle = Math.atan2(vy, vx) * (180 / Math.PI) + 90;

                        var angleRadDiff = (orbitalAngle - targetAngle) * (Math.PI / 180);
                        var shortestAngleDiff = Math.atan2(Math.sin(angleRadDiff), Math.cos(angleRadDiff)) * (180 / Math.PI);
                        targetAngle += shortestAngleDiff * 0.12;

                        var stardustRamp = Math.min(T / 6, 1);
                        var radarChance = 0.15 + 0.35 * Math.min(T / 6, 1);

                        if (Math.random() < radarChance) {
                            createRadarScanPulse(pos.x + targetIdleX, pos.y + targetIdleY, targetAngle);
                        }

                        // Prominent particle size with smooth organic drift velocity
                        if (stardustRamp > 0 && Math.random() < 0.22 * stardustRamp) {
                            var orbRad = Math.random() * Math.PI * 2;
                            var orbDist = currentRadius + (Math.random() - 0.5) * 6;
                            var vibrantColors = idlePalette[themeMode].stardust;
                            var floatSpeed = Math.random() * 0.5 + 0.2;

                            particles.push({
                                x: pos.x + Math.cos(orbRad) * orbDist,
                                y: pos.y + Math.sin(orbRad) * orbDist,
                                vx: -Math.sin(orbRad) * floatSpeed,
                                vy: Math.cos(orbRad) * floatSpeed,
                                damping: 0.985, // Gentle momentum glide without abrupt freezing
                                size: Math.random() * 4.0 + 4.5, // Refined particle size for 80px radius
                                color: vibrantColors[Math.floor(Math.random() * vibrantColors.length)],
                                alpha: 0,
                                targetAlpha: Math.random() * 0.45 + 0.3,
                                fadeIn: Math.random() * 0.04 + 0.02,
                                decay: Math.random() * 0.016 + 0.01
                            });
                        }
                    }

                    if (Math.random() < 0.12) {
                        var renderAngleNow = angle + idleSway;
                        var idleNozzle = getEngineNozzlePos(pos.x + currentIdleOffsetX, pos.y + currentIdleOffsetY, renderAngleNow);
                        var idleRad = (renderAngleNow - 90) * (Math.PI / 180);
                        createThrusterParticle(idleNozzle.x, idleNozzle.y, -Math.cos(idleRad) * 0.8, -Math.sin(idleRad) * 0.8, false);
                    }
                }

                var idleLerp = 0.15;
                currentIdleOffsetX += (targetIdleX - currentIdleOffsetX) * idleLerp;
                currentIdleOffsetY += (targetIdleY - currentIdleOffsetY) * idleLerp;

                var renderAngle = angle + idleSway;

                wrapper.style.transform = 'translate3d(' + pos.x.toFixed(2) + 'px, ' + pos.y.toFixed(2) + 'px, 0)';
                rocket.style.transform = 'translate3d(' + currentIdleOffsetX.toFixed(2) + 'px, ' + currentIdleOffsetY.toFixed(2) + 'px, 0) rotate(' + renderAngle.toFixed(2) + 'deg)';

                if (speed > 0.8 || isHovered) {
                    rocket.classList.add('is-moving');
                    var mainNozzle = getEngineNozzlePos(pos.x + currentIdleOffsetX, pos.y + currentIdleOffsetY, renderAngle);
                    var rad = (renderAngle - 90) * (Math.PI / 180);
                    var backVx = -Math.cos(rad) * (speed * 0.5 + 1.8);
                    var backVy = -Math.sin(rad) * (speed * 0.5 + 1.8);

                    createThrusterParticle(mainNozzle.x, mainNozzle.y, backVx, backVy, isHovered);
                } else {
                    rocket.classList.remove('is-moving');
                }

                // Enforce active particle cap
                while (particles.length > 120) {
                    particles.shift();
                }

                ctx.clearRect(0, 0, width, height);

                for (var i = particles.length - 1; i >= 0; i--) {
                    var p = particles[i];
                    p.x += p.vx;
                    p.y += p.vy;

                    // Apply smooth dynamic momentum reduction
                    var dampFactor = p.damping || 0.93;
                    p.vx *= dampFactor;
                    p.vy *= dampFactor;

                    // Controlled micro-drift so particles keep moving softly instead of freezing completely
                    if (p.damping) {
                        p.vx += (Math.random() - 0.5) * 0.04;
                        p.vy += (Math.random() - 0.5) * 0.04;
                    }

                    if (p.fadeIn && p.alpha < p.targetAlpha) {
                        p.alpha += p.fadeIn;
                    } else {
                        p.alpha -= p.decay;
                    }

                    if (p.alpha <= 0) {
                        particles.splice(i, 1);
                        continue;
                    }

                    ctx.save();
                    ctx.globalCompositeOperation = 'lighter';
                    ctx.globalAlpha = Math.max(0, p.alpha);

                    var radGrad = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.size);
                    radGrad.addColorStop(0, p.color);
                    radGrad.addColorStop(0.5, p.color);
                    radGrad.addColorStop(1, 'transparent');

                    ctx.fillStyle = radGrad;
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.size * 1.5, 0, Math.PI * 2);
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

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        initCursor();
    } else {
        document.addEventListener('DOMContentLoaded', initCursor);
    }
})();
</script>