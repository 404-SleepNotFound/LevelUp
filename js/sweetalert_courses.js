/**
 * js/sweetalert_courses.js
 * LevelUp Gamified E-Learning Platform
 * SweetAlert2 Event Handlers for Course Explorer & Pathfinder System (Light/Dark Mode Support)
 */

window.CourseAlerts = {
    /**
     * Escape HTML strings to prevent XSS injection
     */
    escapeHtml: function (str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    },

    /**
     * ดึงการตั้งค่าสีตาม Theme ปัจจุบัน (Light/Dark)
     */
    getThemeConfig: function() {
        // เช็กว่า html มี data-theme="light" หรือไม่
        const isLight = document.documentElement.getAttribute('data-theme') === 'light';
        
        return {
            background: isLight ? '#ffffff' : '#0f172a',
            color: isLight ? '#0f172a' : '#f8fafc',
            cancelBtn: isLight ? '#94a3b8' : '#475569',
            // สไตล์สำหรับกล่องด้านใน (Inline Box)
            boxStyle: isLight 
                ? 'background: rgba(0, 0, 0, 0.03); border: 1px solid rgba(0, 0, 0, 0.1); color: #0f172a;'
                : 'background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: #f8fafc;'
        };
    },

    /**
     * 1. AI Course Pathfinder Analyzer Modal
     */
    aiSearch: function (inputVal) {
        const theme = this.getThemeConfig();

        if (!inputVal || inputVal.trim() === '') {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณาระบุเป้าหมายการเรียนรู้ 🎯',
                text: 'พิมพ์เป้าหมาย ทักษะ หรือสายงานที่ต้องการ เช่น "อยากเป็น Fullstack Developer ใน 6 เดือน"',
                confirmButtonText: 'ตกลง',
                confirmButtonColor: '#6366F1',
                background: theme.background,
                color: theme.color,
                customClass: {
                    popup: 'levelup-swal-popup',
                    title: 'levelup-swal-title',
                    htmlContainer: 'levelup-swal-text'
                }
            });
            return;
        }

        const cleanInput = this.escapeHtml(inputVal.trim());

        // แสดง Loading State ขณะ AI กำลังประมวลผล
        Swal.fire({
            title: '🤖 AI กำลังวิเคราะห์เส้นทางเควส...',
            html: `ระบบกำลังประมวลผลแผนการเรียนรู้สำหรับ: <br><strong class="text-info" style="color: #06b6d4;">"${cleanInput}"</strong>`,
            allowOutsideClick: false,
            allowEscapeKey: false,
            background: theme.background,
            color: theme.color,
            didOpen: () => {
                Swal.showLoading();
            },
            customClass: {
                popup: 'levelup-swal-popup',
                title: 'levelup-swal-title',
                htmlContainer: 'levelup-swal-text'
            }
        });

        // จำลองเวลาประมวลผลของ AI (1.2 วินาที)
        setTimeout(() => {
            Swal.fire({
                icon: 'success',
                title: 'วิเคราะห์เส้นทางเควสสำเร็จ! 🎯',
                html: `ระบบได้จัดคอร์สเรียนและเส้นทางเควสที่เหมาะกับ <strong style="color: #06b6d4;">"${cleanInput}"</strong> เรียบร้อยแล้ว`,
                confirmButtonText: 'เริ่มออกลุยเควส',
                confirmButtonColor: '#06b6d4',
                background: theme.background,
                color: theme.color,
                customClass: {
                    popup: 'levelup-swal-popup',
                    title: 'levelup-swal-title',
                    htmlContainer: 'levelup-swal-text'
                }
            });
        }, 1200);
    },

    /**
     * 2. ยืนยันการรีเซ็ตตัวกรองค้นหาทั้งหมด
     */
    confirmResetFilters: function (onConfirmCallback) {
        const theme = this.getThemeConfig();

        Swal.fire({
            title: 'รีเซ็ตตัวกรองทั้งหมด? 🔄',
            text: 'เงื่อนไขการคัดกรองและการจัดเรียงทั้งหมดจะถูกคืนค่าเริ่มต้น',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: theme.cancelBtn,
            background: theme.background,
            color: theme.color,
            confirmButtonText: 'ใช่, รีเซ็ตเลย',
            cancelButtonText: 'ยกเลิก',
            customClass: {
                popup: 'levelup-swal-popup',
                title: 'levelup-swal-title',
                htmlContainer: 'levelup-swal-text'
            }
        }).then((result) => {
            if (result.isConfirmed && typeof onConfirmCallback === 'function') {
                onConfirmCallback();
                
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    background: theme.background,
                    color: theme.color
                });
                
                Toast.fire({
                    icon: 'success',
                    title: 'รีเซ็ตตัวกรองเรียบร้อยแล้ว'
                });
            }
        });
    },

    /**
     * 3. ยืนยันการเริ่มภารกิจ/คอร์สเรียน (Start Quest Modal)
     */
    confirmStartQuest: function (courseId, courseTitle, xp, price) {
        const theme = this.getThemeConfig();
        const cleanTitle = this.escapeHtml(courseTitle);
        const priceText = price === 0 ? 'ฟรี (FREE)' : `฿${Number(price).toLocaleString()}`;
        
        Swal.fire({
            title: 'พร้อมรับภารกิจนี้หรือไม่? ⚔️',
            background: theme.background,
            color: theme.color,
            html: `
                <div class="text-start mt-3 p-3 rounded-3" style="${theme.boxStyle}">
                    <div class="fw-bold fs-6 mb-2" style="color: #06b6d4;">${cleanTitle}</div>
                    <div class="d-flex justify-content-between align-items-center fs-sm mb-1" style="font-size: 0.9rem;">
                        <span>🏆 รางวัล XP:</span>
                        <span class="fw-bold" style="color: #10b981;">+${xp} XP</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center fs-sm" style="font-size: 0.9rem;">
                        <span>🏷️ ค่าธรรมเนียม:</span>
                        <span class="fw-bold">${priceText}</span>
                    </div>
                </div>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: theme.cancelBtn,
            confirmButtonText: '<i class="fas fa-play me-1"></i> เริ่มต้นเรียนรู้',
            cancelButtonText: 'ไว้ทีหลัง',
            customClass: {
                popup: 'levelup-swal-popup',
                title: 'levelup-swal-title',
                htmlContainer: 'levelup-swal-text'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `course-detail.php?course_id=${encodeURIComponent(courseId)}`;
            }
        });
    }
};