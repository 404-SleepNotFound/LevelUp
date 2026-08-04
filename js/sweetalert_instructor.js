/**
 * js/sweetalert_instructor.js
 * LevelUp Gamified E-Learning Platform
 * SweetAlert2 Event Handlers for Instructor Dashboard (Light/Dark Mode & Bootstrap Icons Support)
 */

window.InstructorAlerts = {
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
        const isLight = document.documentElement.getAttribute('data-theme') === 'light' || 
                        document.body.classList.contains('light-theme');
        
        return {
            background: isLight ? '#ffffff' : '#0f172a',
            color: isLight ? '#0f172a' : '#f8fafc',
            cancelBtn: isLight ? '#94a3b8' : '#475569',
            confirmDanger: '#ef4444',
            confirmPrimary: '#3b82f6',
            confirmSuccess: '#10b981',
            confirmPurple: '#a855f7',
            // สไตล์สำหรับกล่องด้านใน (Inline Box)
            boxStyle: isLight 
                ? 'background: rgba(0, 0, 0, 0.03); border: 1px solid rgba(0, 0, 0, 0.1); color: #0f172a;'
                : 'background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: #f8fafc;'
        };
    },

    /**
     * 1. ยืนยันการลบรายการ (Delete Item)
     */
    confirmDelete: function(itemName = 'รายการนี้', onConfirmCallback = null) {
        const theme = this.getThemeConfig();
        const cleanName = this.escapeHtml(itemName);

        return Swal.fire({
            title: 'ยืนยันการลบข้อมูล? 🗑️',
            text: `คุณต้องการลบ "${cleanName}" ใช่หรือไม่? การกระทำนี้ไม่สามารถย้อนกลับได้`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: theme.confirmDanger,
            cancelButtonColor: theme.cancelBtn,
            confirmButtonText: '<i class="bi bi-trash3-fill me-1"></i> ยืนยันลบ',
            cancelButtonText: 'ยกเลิก',
            background: theme.background,
            color: theme.color,
            customClass: {
                popup: 'levelup-swal-popup',
                title: 'levelup-swal-title',
                htmlContainer: 'levelup-swal-text'
            }
        }).then((result) => {
            if (result.isConfirmed && typeof onConfirmCallback === 'function') {
                onConfirmCallback();
            }
            return result;
        });
    },

    /**
     * 2. ยืนยันการเผยแพร่เนื้อหา (Publish Content)
     */
    confirmPublish: function(title = 'เผยแพร่เนื้อหาสู่ผู้เรียน? 🚀', text = 'เนื้อหานี้จะเปิดใช้งานและแสดงให้ผู้เรียนเห็นทันที', onConfirmCallback = null) {
        const theme = this.getThemeConfig();

        return Swal.fire({
            title: title,
            text: text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: theme.confirmSuccess,
            cancelButtonColor: theme.cancelBtn,
            confirmButtonText: '<i class="bi bi-send-fill me-1"></i> เผยแพร่ทันที',
            cancelButtonText: 'ยกเลิก',
            background: theme.background,
            color: theme.color,
            customClass: {
                popup: 'levelup-swal-popup',
                title: 'levelup-swal-title',
                htmlContainer: 'levelup-swal-text'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                if (typeof onConfirmCallback === 'function') {
                    onConfirmCallback();
                } else {
                    this.toast('success', 'เผยแพร่ข้อมูลสำเร็จแล้ว');
                }
            }
            return result;
        });
    },

    /**
     * 3. บันทึกร่าง (Save Draft)
     */
    saveDraft: function(onSuccessCallback = null) {
        const theme = this.getThemeConfig();

        return Swal.fire({
            title: 'บันทึกร่างเรียบร้อย 💾',
            text: 'ข้อมูลของคุณถูกบันทึกสำรองในระบบเรียบร้อยแล้ว',
            icon: 'success',
            timer: 1800,
            showConfirmButton: false,
            background: theme.background,
            color: theme.color,
            customClass: {
                popup: 'levelup-swal-popup',
                title: 'levelup-swal-title',
                htmlContainer: 'levelup-swal-text'
            }
        }).then(() => {
            if (typeof onSuccessCallback === 'function') onSuccessCallback();
        });
    },

    /**
     * 4. ยืนยันการคัดลอกรายการ (Duplicate Item)
     */
    confirmDuplicate: function(itemName = 'รายการ', onConfirmCallback = null) {
        const theme = this.getThemeConfig();
        const cleanName = this.escapeHtml(itemName);

        return Swal.fire({
            title: 'คัดลอกรายการนี้? 📋',
            text: `ระบบจะสร้างสำเนาใหม่ของ "${cleanName}" ขึ้นมาอีก 1 ชุด`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: theme.confirmPrimary,
            cancelButtonColor: theme.cancelBtn,
            confirmButtonText: '<i class="bi bi-copy me-1"></i> คัดลอก',
            cancelButtonText: 'ยกเลิก',
            background: theme.background,
            color: theme.color,
            customClass: {
                popup: 'levelup-swal-popup',
                title: 'levelup-swal-title',
                htmlContainer: 'levelup-swal-text'
            }
        }).then((result) => {
            if (result.isConfirmed && typeof onConfirmCallback === 'function') {
                onConfirmCallback();
            }
            return result;
        });
    },

    /**
     * 5. AI Copilot / Lab / Quiz Assistant Loader
     * รองรับทั้งการกำหนดเวลา (ms) หรือส่ง Promise / Async Task เข้ามา
     */
    aiProcess: function(actionName = 'ประมวลผลข้อมูล', durationOrPromise = 1800, onComplete = null) {
        const theme = this.getThemeConfig();
        const cleanAction = this.escapeHtml(actionName);

        Swal.fire({
            title: '🤖 AI Copilot กำลังทำงาน...',
            html: `ระบบกำลังประมวลผล: <strong style="color: #a855f7;">"${cleanAction}"</strong><br><div class="mt-3 p-2 rounded-3 text-center" style="${theme.boxStyle} font-size: 0.85rem;">⚡ Generative Intelligence Engine Running</div>`,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
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

        // รองรับกรณีส่ง Promise / Async Function
        if (typeof durationOrPromise === 'object' && typeof durationOrPromise.then === 'function') {
            return durationOrPromise
                .then((res) => {
                    Swal.close();
                    if (typeof onComplete === 'function') onComplete(res);
                    else this.toast('success', `ดำเนินการ ${cleanAction} สำเร็จแล้ว`);
                    return res;
                })
                .catch((err) => {
                    Swal.close();
                    this.toast('error', `เกิดข้อผิดพลาดในการ ${cleanAction}`);
                    throw err;
                });
        }

        // กรณีเป็นตัวเลขระบุเวลา (ms)
        const duration = typeof durationOrPromise === 'number' ? durationOrPromise : 1800;
        if (duration > 0) {
            setTimeout(() => {
                Swal.close();
                if (typeof onComplete === 'function') {
                    onComplete();
                } else {
                    this.toast('success', `ดำเนินการ ${cleanAction} สำเร็จแล้ว`);
                }
            }, duration);
        }
    },

    /**
     * 6. แสดง Modal แจ้งเตือนทั่วไป (ใช้แทน alert())
     */
    info: function(title = 'แจ้งเตือน', text = '', icon = 'info') {
        const theme = this.getThemeConfig();

        return Swal.fire({
            title: title,
            text: text,
            icon: icon,
            confirmButtonColor: theme.confirmPrimary,
            confirmButtonText: 'ตกลง',
            background: theme.background,
            color: theme.color,
            customClass: {
                popup: 'levelup-swal-popup',
                title: 'levelup-swal-title',
                htmlContainer: 'levelup-swal-text'
            }
        });
    },

    /**
     * 7. Toast Notification
     */
    toast: function(icon = 'success', title = 'ทำรายการสำเร็จ') {
        const theme = this.getThemeConfig();

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            background: theme.background,
            color: theme.color,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        return Toast.fire({
            icon: icon,
            title: title
        });
    },

    /**
     * 8. ปิด Alert
     */
    close: function() {
        Swal.close();
    }
};