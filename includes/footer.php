<?php
/**
 * Rocket Custom Cursor Component (AJAX-Safe & Singleton Engine)
 * Location: includes/rocket_cursor.php (Fixed for Anti-Warp)
 */
?>
<style>
    /* 1. ซ่อนเมาส์ระบบแบบเด็ดขาดทุกสถานะ (รวมถึง Hover / Active / Input) ป้องกันเมาส์จริงซ้อน */
    html, body, *, *::before, *::after,
    a, a:hover, a:active, a:focus,
    button, button:hover, button:active,
    input, select, textarea, [role="button"], 
    .btn, .nav-link, .dropdown-item, [onclick] {
        cursor: none !important;
    }

    /* 2. สไตล์ตัวจรวด 🚀 ใช้ GPU (translate3d) ในการประมวลผล */
    #custom-rocket-cursor {
        position: fixed;
        top: 0;
        left: 0;
        font-size: 22px;
        line-height: 1;
        pointer-events: none;
        z-index: 9999999;
        user-select: none;
        will-change: transform;
        transition: transform 0.02s linear, opacity 0.1s ease-in-out;
        opacity: 0; /* 👈 ซ่อนไว้ก่อนตั้งแต่แรกเกิด ป้องกันการวาร์ปไปมุมซ้ายบน */
    }

    /* 3. แสดงผลเมื่อเมาส์อยู่ในตำแหน่งที่ถูกต้อง */
    #custom-rocket-cursor.is-visible {
        opacity: 1;
    }
</style>

<script>
(function() {
    // 📌 ป้องกันการเกิด Element ซ้อนกัน เมื่อโหลดหน้าผ่าน AJAX
    let cursor = document.getElementById('custom-rocket-cursor');
    if (!cursor) {
        cursor = document.createElement('div');
        cursor.id = 'custom-rocket-cursor';
        cursor.innerHTML = '🚀';
        document.body.appendChild(cursor);
    }

    // 📌 ป้องกันการลง Event Listener ซ้ำซ้อน
    if (window.__rocketCursorActive) return;
    window.__rocketCursorActive = true;

    // โหลดพิกัดเดิมจากหน้าเก่า (ป้องกันการกระตุกตอนเปลี่ยนหน้า)
    const savedX = sessionStorage.getItem('rocket_x');
    const savedY = sessionStorage.getItem('rocket_y');
    let mouseX = savedX !== null ? parseFloat(savedX) : -100;
    let mouseY = savedY !== null ? parseFloat(savedY) : -100;
    
    let isScheduled = false;

    // ถ้ามีพิกัดเดิม เซ็ตตำแหน่งเตรียมไว้ก่อนเลย
    if (mouseX >= 0 && mouseY >= 0 && cursor) {
        cursor.style.transform = `translate3d(${mouseX - 12}px, ${mouseY - 10}px, 0) scaleX(-1)`;
    }

    function render() {
        const el = document.getElementById('custom-rocket-cursor');
        // เงื่อนไขเหล็ก: ต้องมี Element และพิกัดต้องไม่ติดลบ
        if (el && mouseX >= 0 && mouseY >= 0) {
            // ปรับระยะ Offset ให้ปลายจรวดตรงกับพิกัดคลิกพอดี
            el.style.transform = `translate3d(${mouseX - 12}px, ${mouseY - 10}px, 0) scaleX(-1)`;
            el.classList.add('is-visible'); // โชว์จรวด
        }
        isScheduled = false;
    }

    window.addEventListener('mousemove', function(e) {
        mouseX = e.clientX;
        mouseY = e.clientY;
        
        // จำค่าพิกัดเอาไว้ใช้หน้าต่อไป
        sessionStorage.setItem('rocket_x', mouseX);
        sessionStorage.setItem('rocket_y', mouseY);

        if (!isScheduled) {
            isScheduled = true;
            requestAnimationFrame(render);
        }
    }, { passive: true });

    // ซ่อนจรวดเมื่อเอาเมาส์ออกนอกจอ
    document.addEventListener('mouseleave', function() {
        const el = document.getElementById('custom-rocket-cursor');
        if (el) el.classList.remove('is-visible');
    });

    // ปลุกการทำงานครั้งแรกถ้ามีพิกัดจากหน้าเก่า
    if (mouseX >= 0 && mouseY >= 0) {
        requestAnimationFrame(render);
    }
})();
</script>