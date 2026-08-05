<?php
/**
 * LevelUp Rocket Cursor Component v3.0 (No-Canvas Lightweight Version)
 * Fixes F5 Refresh Warp Issue & Removes Canvas overhead.
 * Usage in PHP: <?php include 'rocket-cursor.php'; ?>
 */
?>
<div id="rocket-cursor-wrapper" aria-hidden="true">
    <div id="rocket-cursor-main">
        <!-- Pure CSS Engine Flame Effect -->
        <div class="rocket-flame-container">
            <div class="rocket-flame-core"></div>
            <div class="rocket-flame-glow"></div>
        </div>
        
        <!-- SVG Rocket Icon -->
        <svg class="rocket-svg" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g id="rocket-group">
                <!-- Main Rocket Body -->
                <path d="M32 4C32 4 48 16 48 38C48 44 44 48 44 48L32 42L20 48C20 48 16 44 16 38C16 16 32 4 32 4Z" fill="url(#rocket-body-grad)" stroke="#ffffff" stroke-width="1.5" stroke-linejoin="round"/>
                <!-- Rocket Nose Cone -->
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
        z-index: 2147483647 !important; /* Maximum 32-bit signed integer z-index */
        isolation: isolate !important;
        transform: translate3d(-100px, -100px, 0);
        will-change: transform;
        display: block;
        opacity: 0; /* Hidden initially until first mouse move snaps position */
        transition: opacity 0.25s ease;
    }

    /* ------------------------------------------------------------------
       3. ROCKET ANIMATION & VISUAL STYLING
       ------------------------------------------------------------------ */
    #rocket-cursor-main {
        position: absolute;
        top: 0;
        left: 0;
        width: 42px;
        height: 42px;
        margin-top: -21px;
        margin-left: -21px;
        pointer-events: none !important;
        transition: transform 0.18s cubic-bezier(0.2, 0.8, 0.2, 1);
        transform-origin: center center;
    }

    .rocket-svg {
        width: 100%;
        height: 100%;
        filter: drop-shadow(0px 4px 10px rgba(0, 0, 0, 0.35));
        transition: filter 0.2s ease;
    }

    /* Engine Flame Effect (Pure CSS Animations) */
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

    /* Interactive Hover Target Ring */
    .rocket-target-ring {
        position: absolute;
        top: 50%;
        left: 50%;
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

    /* Hover State (When pointing to links/buttons) */
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
        #rocket-cursor-wrapper {
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

        var wrapper = document.getElementById('rocket-cursor-wrapper');
        var rocket = document.getElementById('rocket-cursor-main');

        if (!wrapper || !rocket) return;

        // Move cursor elements to end of document body to guarantee stacking context
        document.body.appendChild(wrapper);

        var mouse = { x: 0, y: 0 };
        var pos = { x: 0, y: 0 };
        var lastPos = { x: 0, y: 0 };
        var angle = 0;
        var targetAngle = 0;
        var isHovered = false;
        var isInitialized = false; // Flag to fix F5 refresh warp jump

        function onMouseMove(e) {
            mouse.x = e.clientX;
            mouse.y = e.clientY;

            // FIRST MOUSE MOVE FIX: Snap directly to mouse coordinates without LERP delay on refresh
            if (!isInitialized) {
                pos.x = mouse.x;
                pos.y = mouse.y;
                lastPos.x = mouse.x;
                lastPos.y = mouse.y;
                
                // Set CSS transform instantly before revealing wrapper
                wrapper.style.transform = 'translate3d(' + pos.x + 'px, ' + pos.y + 'px, 0)';
                wrapper.style.opacity = '1';
                isInitialized = true;
            }
        }

        window.addEventListener('mousemove', onMouseMove, { passive: true });

        document.addEventListener('mouseleave', function() {
            wrapper.style.opacity = '0';
        });

        document.addEventListener('mouseenter', function() {
            if (isInitialized) {
                wrapper.style.opacity = '1';
            }
        });

        window.addEventListener('mousedown', function() {
            rocket.classList.add('is-clicking');
        });

        window.addEventListener('mouseup', function() {
            rocket.classList.remove('is-clicking');
        });

        var interactiveSelector = 'a, button, input, select, textarea, [role="button"], .btn, .card, .glass-card, .category-card-compact, .accordion-button';

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

        function render() {
            if (isInitialized) {
                // Linear Interpolation (Smooth Rocket Follow)
                var lerpFactor = 0.25;
                pos.x += (mouse.x - pos.x) * lerpFactor;
                pos.y += (mouse.y - pos.y) * lerpFactor;

                // Calculate movement speed and directional angle
                var dx = pos.x - lastPos.x;
                var dy = pos.y - lastPos.y;
                var speed = Math.sqrt(dx * dx + dy * dy);

                if (speed > 0.4) {
                    targetAngle = Math.atan2(dy, dx) * (180 / Math.PI) + 90;
                }

                // Smooth rotation angle interpolation & 360-degree flip prevention
                var dAngle = targetAngle - angle;
                while (dAngle < -180) dAngle += 360;
                while (dAngle > 180) dAngle -= 360;
                angle += dAngle * 0.2;

                // Update transforms
                wrapper.style.transform = 'translate3d(' + pos.x.toFixed(2) + 'px, ' + pos.y.toFixed(2) + 'px, 0)';
                rocket.style.transform = 'rotate(' + angle.toFixed(2) + 'deg)';

                lastPos.x = pos.x;
                lastPos.y = pos.y;
            }

            requestAnimationFrame(render);
        }

        requestAnimationFrame(render);
    });
})();
</script>