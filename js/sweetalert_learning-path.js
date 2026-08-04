/**
 * LevelUp Design System - SweetAlert2 Controller
 * File: sweetalert_learning-path.js
 * Purpose: Interactive Gamified Modals & Toasts for learning-path.php
 * Compatibility: Standalone Vanilla JS (No jQuery dependency required)
 */

(function () {
    'use strict';

    function initSweetAlertLearningPath() {
        // ตรวจสอบว่ามี Library SweetAlert2 ในระบบหรือไม่
        if (typeof Swal === 'undefined') {
            console.error('[LevelUp Error] SweetAlert2 Library (Swal) is missing! Please check CDN import in learning-path.php');
            return;
        }

        // -------------------------------------------------------------------------
        // 1. Custom SweetAlert2 Base Configuration (LevelUp Design System)
        // -------------------------------------------------------------------------
        const LevelUpSwal = Swal.mixin({
            customClass: {
                popup: 'levelup-swal-popup shadow-lg',
                title: 'levelup-swal-title',
                htmlContainer: 'levelup-swal-text',
                confirmButton: 'btn btn-premium px-4 py-2 rounded-pill shadow-sm fw-bold',
                cancelButton: 'btn btn-outline-surface px-4 py-2 rounded-pill shadow-sm fw-bold me-2'
            },
            buttonsStyling: false
        });

        // Toast Notification Mixin (สำหรับการแจ้งเตือนสั้นๆ มุมขวาบน)
        const ToastSwal = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'levelup-swal-popup'
            }
        });

        // -------------------------------------------------------------------------
        // 2. Global Event Delegation (ดักจับ Event แม่นยำ 100%)
        // -------------------------------------------------------------------------
        document.addEventListener('click', function (e) {

            // A. ปุ่ม Rebuild Learning Path (#btnRebuildPath)
            const rebuildBtn = e.target.closest('#btnRebuildPath');
            if (rebuildBtn) {
                e.preventDefault();

                // ดึงค่า Element จาก Form บนหน้าเว็บ
                const careerSelect = document.getElementById('careerSelector');
                const timeSlider = document.getElementById('timeSlider');

                const selectedCareerText = (careerSelect && careerSelect.options[careerSelect.selectedIndex]) 
                    ? careerSelect.options[careerSelect.selectedIndex].text.trim() 
                    : 'Backend Developer (Class)';
                
                const selectedHours = timeSlider ? timeSlider.value : '2';

                // เก็บข้อความ/ HTML ดั้งเดิมบนปุ่ม
                const originalHtml = rebuildBtn.innerHTML;

                // เปลี่ยนสถานะปุ่มเป็นกำลังโหลด (Loading State)
                rebuildBtn.disabled = true;
                rebuildBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> AI Recalculating...';

                // จำลองการประมวลผล AJAX (1 วินาที)
                setTimeout(function () {
                    rebuildBtn.disabled = false;
                    rebuildBtn.innerHTML = originalHtml;

                    LevelUpSwal.fire({
                        title: '⚡ คำนวณเส้นทางใหม่สำเร็จ!',
                        html: `AI ได้ปรับโครงสร้าง Learning Path สำหรับสายอาชีพ <strong class="text-info">${selectedCareerText}</strong> (${selectedHours} ชม./วัน) ให้เหมาะสมกับคุณเรียบร้อยแล้ว`,
                        icon: 'success',
                        confirmButtonText: '<i class="bi bi-check-circle-fill me-1"></i> ตกลง'
                    });
                }, 1000);
                return;
            }

            // B. ปุ่มแชร์ Roadmap (#btnShareRoadmap)
            const shareBtn = e.target.closest('#btnShareRoadmap');
            if (shareBtn) {
                e.preventDefault();
                const currentUrl = window.location.href;

                LevelUpSwal.fire({
                    title: '🗺️ แชร์ Roadmap การเรียนรู้',
                    html: `
                        <p class="mb-3 fs-sm">อวดความคืบหน้าและเส้นทางการเรียนรู้ของคุณให้เพื่อนๆ บนโซเชียลมีเดียเห็น!</p>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control form-control-sm text-center form-select-custom" value="${currentUrl}" id="shareLinkInput" readonly>
                        </div>
                    `,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-clipboard-check me-1"></i> คัดลอกลิงก์',
                    cancelButtonText: 'ปิดหน้าต่าง'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const copyInput = document.getElementById("shareLinkInput");
                        if (copyInput) {
                            copyInput.select();
                            copyInput.setSelectionRange(0, 99999);
                        }

                        // คัดลอกลิงก์พร้อม Fallback
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(currentUrl).then(() => {
                                ToastSwal.fire({
                                    icon: 'success',
                                    title: 'คัดลอกลิงก์ Roadmap เรียบร้อยแล้ว!'
                                });
                            }).catch(() => {
                                document.execCommand('copy');
                                ToastSwal.fire({
                                    icon: 'success',
                                    title: 'คัดลอกลิงก์ Roadmap เรียบร้อยแล้ว!'
                                });
                            });
                        } else {
                            document.execCommand('copy');
                            ToastSwal.fire({
                                icon: 'success',
                                title: 'คัดลอกลิงก์ Roadmap เรียบร้อยแล้ว!'
                            });
                        }
                    }
                });
                return;
            }

            // C. ปุ่ม Export PDF (#btnExportPdf)
            const exportBtn = e.target.closest('#btnExportPdf');
            if (exportBtn) {
                e.preventDefault();

                LevelUpSwal.fire({
                    title: '📄 Export PDF Roadmap',
                    text: 'ระบบกำลังประมวลผลพิมพ์โครงสร้าง Skill Tree และแผนการเรียนของคุณเป็นไฟล์ PDF',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-download me-1"></i> ดาวน์โหลดทันที',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        ToastSwal.fire({
                            icon: 'success',
                            title: 'เริ่มดาวน์โหลดไฟล์ PDF เรียบร้อยแล้ว'
                        });
                    }
                });
                return;
            }

            // D. คลิกด่านที่ถูกล็อกอยู่ใน Roadmap (.roadmap-node.locked)
            const lockedNode = e.target.closest('.roadmap-node.locked');
            if (lockedNode) {
                const nodeTitle = lockedNode.querySelector('h5')?.innerText || 'ด่านนี้ยังไม่ปลดล็อก';
                const warnings = Array.from(lockedNode.querySelectorAll('.text-danger'))
                    .map(el => el.innerText)
                    .join('<br>');

                LevelUpSwal.fire({
                    title: '🔒 ด่านนี้ยังถูกล็อกอยู่',
                    html: `
                        <p class="mb-2 fw-bold text-main">${nodeTitle}</p>
                        <p class="fs-sm mb-3">คุณต้องผ่านเงื่อนไขวิชาก่อนหน้าเพื่อปลดล็อกบทเรียนนี้:</p>
                        <div class="alert alert-danger bg-danger bg-opacity-10 border-danger border-opacity-25 text-start fs-sm mb-0">
                            ${warnings || 'โปรดผ่านบทเรียนก่อนหน้าให้ครบถ้วน'}
                        </div>
                    `,
                    icon: 'warning',
                    confirmButtonText: '<i class="bi bi-shield-lock me-1"></i> รับทราบ'
                });
                return;
            }

        });
    }

    // เรียกใช้งานฟังก์ชันเมื่อ DOM โหลดพร้อม
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSweetAlertLearningPath);
    } else {
        initSweetAlertLearningPath();
    }
})();