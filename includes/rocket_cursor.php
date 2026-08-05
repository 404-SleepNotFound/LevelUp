<?php
/**
 * LevelUp Rocket Cursor Component v2.0
 * Ultra-high z-index & forced cursor-hide for all interactive states.
 * Usage in PHP: <?php include 'rocket-cursor.php'; ?>
 */
?>
<div id="rocket-cursor-wrapper" aria-hidden="true">
    <div id="rocket-cursor-main">
        <!-- Thruster Flame / Particle trail origin -->
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

<!-- Canvas for Engine Smoke & Spark Particles -->
<canvas id="rocket-particle-canvas"></canvas>

<style>
    /* ------------------------------------------------------------------
       1. GLOBAL CURSOR RESET (Fixes default mouse returning on hover)
       ------------------------------------------------------------------ */
    *, *::before, *::after,
    html, body, 
    a, button, input, select, textarea, label, [role="button"],
    .btn, .glass-card, .accordion-button, .card, iframe, svg, img {
        cursor: none !important;
    }

    /* ------------------------------------------------------------------
       2. CURSOR WRAPPER & HIGH Z-INDEX STACKING (Fixes rocket behind elements)
       ------------------------------------------------------------------ */
    #rocket-cursor-wrapper {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 0 !important;
        height: 0 !important;
        pointer-events: none !important;
        /* Maximum possible 32-bit signed integer z-index to stay above modal/headers */
        z-index: 2147483647 !important;
        isolation: isolate !important;
        transform: translate3d(0, 0, 0);
        will-change: transform;
        display: block;
    }

    #rocket-particle-canvas {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        pointer-events: none !important;
        z-index: 2147483646 !important; /* Just below main rocket */
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
        transition: transform 0.15s cubic-bezier(0.2, 0.8, 0.2, 1), opacity 0.2s ease;
        transform-origin: center center;
    }

    .rocket-svg {
        width: 100%;
        height: 100%;
        filter: drop-shadow(0px 4px 10px rgba(0, 0, 0, 0.35));
        transition: filter 0.2s ease;
    }

    /* Engine Flame Effect */
    .rocket-flame-container {
        position: absolute;
        bottom: -6px;
        left: 50%;
        transform: translateX(-50%);
        width: 14px;
        height: 18px;
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
        width: 16px;
        height: 16px;
        background: rgba(245, 158, 11, 0.6);
        border-radius: 50%;
        filter: blur(4px);
    }

    @keyframes rocketFlameBurn {
        0% { transform: scaleY(0.8) scaleX(0.9); opacity: 0.85; }
        100% { transform: scaleY(1.3) scaleX(1.1); opacity: 1; }
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
        border: 2px dashed rgba(99, 102, 241, 0.7);
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
        filter: drop-shadow(0px 0px 14px rgba(99, 102, 241, 0.8));
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

    /* Hide cursor on Touchscreens */
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
        /* ------------------------------------------------------------------
           1. DOM SETUP & DOM-TREE HOISTING (Ensures highest rendering z-index)
           ------------------------------------------------------------------ */
        var wrapper = document.getElementById('rocket-cursor-wrapper');
        var rocket = document.getElementById('rocket-cursor-main');
        var canvas = document.getElementById('rocket-particle-canvas');

        if (!wrapper || !rocket || !canvas) return;

        // Move cursor elements to absolute end of document body to break out of all parent stacking contexts
        document.body.appendChild(canvas);
        document.body.appendChild(wrapper);

        var ctx = canvas.getContext('2d');
        var width = canvas.width = window.innerWidth;
        var height = canvas.height = window.innerHeight;

        window.addEventListener('resize', function() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        });

        /* ------------------------------------------------------------------
           2. STATE & POSITION VARIABLES
           ------------------------------------------------------------------ */
        var mouse = { x: window.innerWidth / 2, y: window.innerHeight / 2 };
        var pos = { x: window.innerWidth / 2, y: window.innerHeight / 2 };
        var lastPos = { x: window.innerWidth / 2, y: window.innerHeight / 2 };
        var angle = 0;
        var targetAngle = 0;
        var particles = [];
        var isHovered = false;
        var isClicking = false;

        /* ------------------------------------------------------------------
           3. MOUSE EVENT LISTENERS
           ------------------------------------------------------------------ */
        window.addEventListener('mousemove', function(e) {
            mouse.x = e.clientX;
            mouse.y = e.clientY;

            // Make wrapper visible when mouse enters viewport
            if (wrapper.style.opacity === '0') {
                wrapper.style.opacity = '1';
                canvas.style.opacity = '1';
            }
        });

        document.addEventListener('mouseleave', function() {
            wrapper.style.opacity = '0';
            canvas.style.opacity = '0';
        });

        document.addEventListener('mouseenter', function() {
            wrapper.style.opacity = '1';
            canvas.style.opacity = '1';
        });

        window.addEventListener('mousedown', function() {
            isClicking = true;
            rocket.classList.add('is-clicking');
            createBurstSparks(pos.x, pos.y, 16);
        });

        window.addEventListener('mouseup', function() {
            isClicking = false;
            rocket.classList.remove('is-clicking');
        });

        /* ------------------------------------------------------------------
           4. HOVER DETECTION FOR INTERACTIVE ELEMENTS
           ------------------------------------------------------------------ */
        var interactiveSelector = 'a, button, input, select, textarea, [role="button"], .btn, .card, .glass-card, .category-card-compact, .accordion-button';
        
        document.addEventListener('mouseover', function(e) {
            if (e.target && e.target.closest(interactiveSelector)) {
                isHovered = true;
                rocket.classList.add('is-hovering');
            }
        });

        document.addEventListener('mouseout', function(e) {
            if (e.target && e.target.closest(interactiveSelector)) {
                isHovered = false;
                rocket.classList.remove('is-hovering');
            }
        });

        /* ------------------------------------------------------------------
           5. PARTICLE ENGINE (Smoke & Fire Thrusters)
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
           6. MAIN ANIMATION LOOP (LERP & Rotation)
           ------------------------------------------------------------------ */
        function render() {
            // Linear Interpolation (Smooth Rocket Movement)
            var lerpFactor = 0.22;
            pos.x += (mouse.x - pos.x) * lerpFactor;
            pos.y += (mouse.y - pos.y) * lerpFactor;

            // Movement delta calculation
            var dx = pos.x - lastPos.x;
            var dy = pos.y - lastPos.y;
            var speed = Math.sqrt(dx * dx + dy * dy);

            // Calculate rotation angle (Default Rocket pointing direction is 45 deg or up 90 deg)
            if (speed > 0.5) {
                // Rocket SVG base points UP (90 degrees offset)
                targetAngle = Math.atan2(dy, dx) * (180 / Math.PI) + 90;
            }

            // Smooth Angle Interpolation
            var dAngle = targetAngle - angle;
            // Normalize angle to prevent 360-degree spin flips
            while (dAngle < -180) dAngle += 360;
            while (dAngle > 180) dAngle -= 360;
            angle += dAngle * 0.2;

            // Update Rocket Element Position & Transform
            wrapper.style.transform = 'translate3d(' + pos.x + 'px, ' + pos.y + 'px, 0)';
            rocket.style.transform = 'rotate(' + angle + 'deg)';

            // Spawn Thruster Flame Particles when moving or hovering
            if (speed > 0.8 || isHovered) {
                // Calculate position behind the rocket nozzle
                var rad = (angle - 90) * (Math.PI / 180);
                var tailX = pos.x - Math.cos(rad) * 22;
                var tailY = pos.y - Math.sin(rad) * 22;
                var backVx = -Math.cos(rad) * (speed * 0.3 + 1);
                var backVy = -Math.sin(rad) * (speed * 0.3 + 1);

                createThrusterParticle(tailX, tailY, backVx, backVy, isHovered);
            }

            // Draw Particles on Canvas
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

            requestAnimationFrame(render);
        }

        // Start Animation Engine
        requestAnimationFrame(render);
    });
})();
</script>