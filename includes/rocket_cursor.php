<?php
/**
 * LevelUp - Centered Warp Rocket Cursor Engine
 * Architecture: Zero-Allocation Fixed Pool | Symmetrical Midpoint Starfield | Micro-Floating Animation
 * Developer: Senior UX / Performance Engineer
 * >>> DEPLOY-CHECK-MARKER-V10-CENTERED-WARP <<<
 */

if (defined('LEVELUP_ROCKET_CURSOR_LOADED')) { return; }
define('LEVELUP_ROCKET_CURSOR_LOADED', true);
?>
<style>
    /* 🛡️ ซ่อนเมาส์ระบบทั้งหมดเมื่อจรวดทำงาน */
    html.levelup-active-cursor,
    html.levelup-active-cursor * {
        cursor: none !important;
    }
    
    html.levelup-active-cursor * {
        -webkit-user-drag: none !important;
        -khtml-user-drag: none !important;
        -moz-user-drag: none !important;
        -o-user-drag: none !important;
        user-drag: none !important;
    }
    
    #levelup-rocket-cursor {
        transition: opacity 0.15s ease-out;
    }
    
    /* เรืองแสงเมื่อ Hover ปุ่ม */
    #levelup-rocket-cursor.is-hovering {
        filter: drop-shadow(0 0 8px rgba(0, 242, 254, 0.9)) !important;
    }
    
    #levelup-rocket-cursor.is-clicking {
        transition: transform 0.08s cubic-bezier(0.2, 1, 0.3, 1) !important;
    }
</style>

<!-- Canvas Overlay สำหรับเอฟเฟกต์ทั้งหมด -->
<canvas id="levelup-particle-canvas" style="position: fixed !important; top: 0 !important; left: 0 !important; pointer-events: none !important; z-index: 999999997 !important;"></canvas>

<!-- ตัวไอคอนจรวด -->
<div id="levelup-rocket-cursor" style="position: fixed; top: 0; left: 0; transform: translate3d(-100vw, -100vh, 0) scaleX(-1); opacity: 0; pointer-events: none; user-select: none; z-index: 999999998; width: 18px; height: 18px; font-size: 18px; line-height: 1; transform-origin: 21.2px 0.3px; will-change: transform, opacity; text-shadow: none;">🚀</div>

<script>
(function() {
    'use strict';

    // 🛡️ ระบบกำจัดจรวดส่วนเกินจาก Footer
    function setupRogueCursorAssassin() {
        const killRogue = () => {
            const rogue = document.getElementById('custom-rocket-cursor');
            if (rogue) rogue.remove();
        };
        killRogue();
        const observer = new MutationObserver(killRogue);
        observer.observe(document.documentElement, { childList: true, subtree: true });
    }
    setupRogueCursorAssassin();

    // เคลียร์ Canvas/Rocket ตัวเก่าถ้าระบบถูกโหลดซ้ำ
    const existingRockets = document.querySelectorAll('#levelup-rocket-cursor');
    for (let i = 1; i < existingRockets.length; i++) existingRockets[i].remove();
    const existingCanvases = document.querySelectorAll('#levelup-particle-canvas');
    for (let i = 1; i < existingCanvases.length; i++) existingCanvases[i].remove();

    if (window.__LevelUpRocketEngine) return;
    window.__LevelUpRocketEngine = true;

    if (window.matchMedia('(hover: none) and (pointer: coarse)').matches) return;

    const canvas = document.querySelector('#levelup-particle-canvas');
    const ctx = canvas.getContext('2d', { alpha: true });
    const rocket = document.querySelector('#levelup-rocket-cursor');

    // 🎯 พิกัดจุดหมุน และท่อไอพ่นจรวด
    const HOTSPOT_X = 21.2;
    const HOTSPOT_Y = 0.3;
    const BASE_NOZZLE_OFFSET_X = 12.3;
    const BASE_NOZZLE_OFFSET_Y = 16.5;
    const NOZZLE_ANGLE = Math.atan2(BASE_NOZZLE_OFFSET_Y, BASE_NOZZLE_OFFSET_X); // ทิศทางไอพ่นตามแนวท่อจริง
    
    const POOL_SIZE = 90; // โควต้าอนุภาครวมคงที่

    let hasValidPosition = false;
    let mouse = { x: -100, y: -100, lastX: -100, lastY: -100 };
    let state = { isHovering: false, isClicking: false, isNativeRequired: false, idleTime: 0 };
    
    let lastTarget = null;
    let lastTime = performance.now();
    let rafId = null;
    let dpr = window.devicePixelRatio || 1;

    function resizeCanvas() {
        dpr = window.devicePixelRatio || 1;
        canvas.width = window.innerWidth * dpr;
        canvas.height = window.innerHeight * dpr;
        canvas.style.width = window.innerWidth + 'px';
        canvas.style.height = window.innerHeight + 'px';
        ctx.scale(dpr, dpr);
    }

    // ⚡ Engine Particle Pool แบบ Zero-Memory Allocation
    class CosmicParticlePool {
        constructor(size) {
            this.pool = Array.from({ length: size }, () => ({
                active: false,
                isStar: false,
                x: 0, y: 0,
                vx: 0, vy: 0,
                life: 0, maxLife: 0,
                size: 0,
                color: ''
            }));
        }

        spawnThruster(x, y, vx, vy, isPlasma) {
            const p = this.pool.find(p => !p.active);
            if (!p) return;

            p.active = true;
            p.isStar = false;
            p.x = x;
            p.y = y;
            p.vx = vx;
            p.vy = vy;
            p.maxLife = p.life = 0.15 + Math.random() * 0.15;
            p.size = 2.5 + Math.random() * 3.0;

            if (isPlasma) {
                p.color = Math.random() > 0.5 ? '#00f2fe' : '#4facfe'; // เปลวพลาสม่าขณะนิ่ง
            } else {
                const rand = Math.random();
                if (rand > 0.6) p.color = '#fff375';
                else if (rand > 0.25) p.color = '#ff7f11';
                else p.color = '#ff3b00';
            }
        }

        spawnStar(centerX, centerY) {
            const p = this.pool.find(p => !p.active);
            if (!p) return;

            p.active = true;
            p.isStar = true;
            
            const speed = 2.4 + Math.random() * 2.6;
            const angle = Math.PI * 0.25; // มุมพุ่งเฉียงลงขวา 45 องศา

            p.vx = Math.cos(angle) * speed;
            p.vy = Math.sin(angle) * speed;

            p.maxLife = p.life = 0.4 + Math.random() * 0.3;

            // 🎯 คำนวณให้ตำแหน่งจรวดอยู่ "จุดศูนย์กลาง" ของเอฟเฟกต์พอดี (Symmetrical Centered Field)
            const travelDist = speed * (p.maxLife / 0.020);
            const travelProgress = (Math.random() - 0.5) * 1.2; // สุ่มให้เกิดทั้งฝั่งหน้าและฝั่งหลังจรวดอย่างสมมาตร
            const sideOffset = (Math.random() - 0.5) * 65; // รัศมีความกว้างโอบล้อมรอบตัวจรวด

            const dirX = Math.cos(angle);
            const dirY = Math.sin(angle);
            const perpX = -dirY;
            const perpY = dirX;

            // พิกัดดาวเริ่มต้นจะถูกกระจายตัวสมมาตรรอบศูนย์กลางจรวด
            p.x = centerX + (dirX * travelProgress * travelDist) + (perpX * sideOffset);
            p.y = centerY + (dirY * travelProgress * travelDist) + (perpY * sideOffset);

            p.size = 1.1 + Math.random() * 1.8;

            const colors = ['#ffffff', '#00f2fe', '#ffd700', '#a8ff78', '#e0c3fc'];
            p.color = colors[Math.floor(Math.random() * colors.length)];
        }

        updateAndDraw(ctx) {
            ctx.clearRect(0, 0, window.innerWidth, window.innerHeight);
            ctx.save();
            ctx.globalCompositeOperation = 'lighter';

            let hasActive = false;

            for (let i = 0; i < this.pool.length; i++) {
                const p = this.pool[i];
                if (!p.active) continue;

                hasActive = true;
                p.x += p.vx;
                p.y += p.vy;
                p.life -= p.isStar ? 0.020 : 0.025;

                if (p.life <= 0) {
                    p.active = false;
                    continue;
                }

                const progress = p.life / p.maxLife;

                if (p.isStar) {
                    ctx.globalAlpha = Math.min(1.0, progress * 1.2);
                    ctx.fillStyle = p.color;
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                    ctx.fill();
                } else {
                    // 🔥 เปลวไฟแบบมีแกนสว่าง + แสงฟุ้งรอบตัว ยืดตามทิศทางการเคลื่อนที่
                    const speed = Math.hypot(p.vx, p.vy);
                    const angle = Math.atan2(p.vy, p.vx);
                    const stretch = 1 + Math.min(speed * 0.6, 2.2);
                    const r = Math.max(0.4, p.size * progress);

                    ctx.save();
                    ctx.translate(p.x, p.y);
                    ctx.rotate(angle);

                    // ชั้นแสงฟุ้งด้านนอก
                    const glow = ctx.createRadialGradient(0, 0, 0, 0, 0, r * stretch * 1.3);
                    glow.addColorStop(0, p.color);
                    glow.addColorStop(1, 'rgba(0,0,0,0)');
                    ctx.globalAlpha = Math.min(1.0, progress * 0.9);
                    ctx.fillStyle = glow;
                    ctx.beginPath();
                    ctx.ellipse(0, 0, r * stretch * 1.3, r * 1.3, 0, 0, Math.PI * 2);
                    ctx.fill();

                    // แกนร้อนสว่างตรงกลาง
                    ctx.globalAlpha = Math.min(1.0, progress * 1.3);
                    ctx.fillStyle = '#fffbe6';
                    ctx.beginPath();
                    ctx.ellipse(0, 0, r * stretch * 0.55, r * 0.55, 0, 0, Math.PI * 2);
                    ctx.fill();

                    ctx.restore();
                }
            }

            ctx.restore();
            return hasActive;
        }
    }

    const particlePool = new CosmicParticlePool(POOL_SIZE);

    function scanTargetCursor(target) {
        if (!target || target === lastTarget) return;
        lastTarget = target;

        const isTextInput = target.closest('input:not([type="button"]):not([type="submit"]):not([type="checkbox"]):not([type="radio"]):not([type="reset"]), textarea, [contenteditable="true"]');
        const isInteractive = target.closest('a, button, [role="button"], input[type="button"], input[type="submit"], input[type="checkbox"], input[type="radio"], .btn, [onclick]');

        if (isTextInput) {
            state.isNativeRequired = true;
            state.isHovering = false;
        } else {
            state.isNativeRequired = false;
            state.isHovering = !!isInteractive;
        }
    }

    // 🎬 Animation Loop หลัก
    function render(now) {
        const dt = (now - lastTime) / 1000;
        lastTime = now;

        if (state.isNativeRequired || !hasValidPosition) {
            document.documentElement.classList.remove('levelup-active-cursor');
            rocket.style.opacity = '0';
        } else {
            document.documentElement.classList.add('levelup-active-cursor');

            const dx = mouse.x - mouse.lastX;
            const dy = mouse.y - mouse.lastY;
            const moveSpeed = Math.sqrt(dx * dx + dy * dy);

            if (moveSpeed > 0.2) {
                state.idleTime = 0;
            } else {
                state.idleTime += dt;
            }

            // 🌌 เอฟเฟกต์ลอยนิ่งแบบไร้น้ำหนัก (Zero-G Floating)
            let hoverX = 0;
            let hoverY = 0;
            if (state.idleTime > 1.0) {
                hoverX = Math.sin(now * 0.002) * 2.0;
                hoverY = Math.cos(now * 0.003) * 2.5;
            }

            let scale = state.isHovering ? 1.25 : 1.0;
            if (state.isClicking) scale *= 0.85;

            if (state.isHovering) rocket.classList.add('is-hovering');
            else rocket.classList.remove('is-hovering');

            if (state.isClicking) rocket.classList.add('is-clicking');
            else rocket.classList.remove('is-clicking');

            const currX = mouse.x + hoverX;
            const currY = mouse.y + hoverY;

            rocket.style.transform = `translate3d(${currX - HOTSPOT_X}px, ${currY - HOTSPOT_Y}px, 0) scaleX(-1) rotate(-8deg) scale(${scale})`;
            rocket.style.opacity = '1';

            const nozzleX = currX + (BASE_NOZZLE_OFFSET_X * scale);
            const nozzleY = currY + (BASE_NOZZLE_OFFSET_Y * scale);

            // 🚀 โหมดการทำงานสภาวะ Idle Warp กับ Active
            if (state.idleTime > 3.0) {
                // 🌟 [Idle Warp] เมาส์นิ่งเกิน 3 วินาที -> จรวดอยู่ตรงกลางหมู่ดาวที่พุ่งผ่าน
                if (Math.random() > 0.35) {
                    particlePool.spawnStar(currX, currY);
                }

                // 🔥 เปลวไฟยังคงลุกต่อเนื่องแบบกะพริบเบาๆ ขณะล่องลอย
                const flicker = 0.7 + Math.sin(now * 0.012) * 0.3;
                const idleOriginX = nozzleX + Math.cos(NOZZLE_ANGLE) * 4;
                const idleOriginY = nozzleY + Math.sin(NOZZLE_ANGLE) * 4;
                const speedIdle = (0.6 + Math.random() * 1.0) * flicker;
                const spreadIdle = (Math.random() - 0.5) * 0.5;
                particlePool.spawnThruster(
                    idleOriginX + (Math.random() - 0.5) * 1.5,
                    idleOriginY + (Math.random() - 0.5) * 1.5,
                    Math.cos(NOZZLE_ANGLE + spreadIdle) * speedIdle,
                    Math.sin(NOZZLE_ANGLE + spreadIdle) * speedIdle,
                    true
                );
            } else {
                // 🔥 [Active Thrust]
                const isMoving = moveSpeed > 0.5;

                if (isMoving) {
                    for (let n = 0; n < 2; n++) {
                        particlePool.spawnThruster(
                            nozzleX + (Math.random() - 0.5) * 2,
                            nozzleY + (Math.random() - 0.5) * 2,
                            (Math.random() - 0.5) * 0.3,
                            (Math.random() - 0.5) * 0.3,
                            false
                        );
                    }

                    const exhaustAngle = Math.atan2(dy, dx) + Math.PI;
                    for (let n = 0; n < 3; n++) {
                        const speed = 1.2 + Math.random() * 2.4;
                        const spread = (Math.random() - 0.5) * 0.55;
                        const vx = Math.cos(exhaustAngle + spread) * speed;
                        const vy = Math.sin(exhaustAngle + spread) * speed;
                        particlePool.spawnThruster(nozzleX, nozzleY, vx, vy, false);
                    }
                } else {
                    // นิ่งอยู่กับที่แต่ยังไม่เข้าโหมด Idle Warp -> เปลวไฟลุกไหลตามแนวท่อ ห่างออกจากตัวจรวดเล็กน้อย
                    const originX = nozzleX + Math.cos(NOZZLE_ANGLE) * 4;
                    const originY = nozzleY + Math.sin(NOZZLE_ANGLE) * 4;
                    for (let n = 0; n < 2; n++) {
                        const speed = 0.9 + Math.random() * 1.5;
                        const spread = (Math.random() - 0.5) * 0.5;
                        const vx = Math.cos(NOZZLE_ANGLE + spread) * speed;
                        const vy = Math.sin(NOZZLE_ANGLE + spread) * speed;
                        particlePool.spawnThruster(originX, originY, vx, vy, false);
                    }
                }
            }

            mouse.lastX = mouse.x;
            mouse.lastY = mouse.y;
        }

        particlePool.updateAndDraw(ctx);
        rafId = requestAnimationFrame(render);
    }

    function wakeUp() {
        if (!rafId) {
            lastTime = performance.now();
            rafId = requestAnimationFrame(render);
        }
    }

    window.addEventListener('resize', resizeCanvas, { passive: true });

    const engineStartTime = performance.now();

    ['pointermove', 'mousemove'].forEach(eventType => {
        window.addEventListener(eventType, (e) => {
            if (e.clientX === undefined) return;

            if (!hasValidPosition && e.clientX === 0 && e.clientY === 0 && (performance.now() - engineStartTime) < 500) {
                return;
            }

            hasValidPosition = true;
            mouse.x = Math.round(e.clientX);
            mouse.y = Math.round(e.clientY);

            scanTargetCursor(e.target);
            wakeUp();
        }, { passive: true, capture: true });
    });

    window.addEventListener('mousedown', (e) => {
        if (e.button !== 2) {
            state.isClicking = true;
            if (!state.isNativeRequired && hasValidPosition) {
                const scale = state.isHovering ? 1.25 : 1.0;
                const nX = mouse.x + (BASE_NOZZLE_OFFSET_X * scale);
                const nY = mouse.y + (BASE_NOZZLE_OFFSET_Y * scale);
                for (let i = 0; i < 6; i++) {
                    const angle = Math.random() * Math.PI * 2;
                    const spd = 1.5 + Math.random() * 2.5;
                    particlePool.spawnThruster(nX, nY, Math.cos(angle) * spd, Math.sin(angle) * spd, false);
                }
            }
            wakeUp();
        }
    }, { passive: true });

    window.addEventListener('mouseup', () => { state.isClicking = false; wakeUp(); }, { passive: true });

    // 😴 หยุด Loop ทันทีเมื่อสลับ Tab
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            if (rafId) {
                cancelAnimationFrame(rafId);
                rafId = null;
            }
        } else {
            wakeUp();
        }
    });

    document.addEventListener('mouseleave', () => {
        hasValidPosition = false;
        rocket.style.opacity = '0';
    });

    resizeCanvas();
    wakeUp();
})();
</script>