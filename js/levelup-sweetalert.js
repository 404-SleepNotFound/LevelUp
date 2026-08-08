/**
 * js/levelup-sweetalert.js
 * LevelUp E-Learning Platform - Global SweetAlert2 System Utility
 * 
 * Target: /js/levelup-sweetalert.js
 * Concept: Modern, Premium, Gamified & AI E-Learning Theme
 * Features: Light/Dark Mode Dynamic Observer, Queued Alerts, Promise Support, XSS Safe
 * 
 * STRICT MANDATE:
 * - NO business logic, database calls, or page routes hardcoded.
 * - Centralized UI Utility / Event Handler Service.
 */

(function (window, document) {
  'use strict';

  // Prevent duplicate script initialization
  if (window.LevelUpAlert && window.LevelUpAlert.__initialized) {
    console.warn('[LevelUpAlert] System already initialized.');
    return;
  }

  // Check SweetAlert2 Dependency
  if (typeof Swal === 'undefined') {
    console.error('[LevelUpAlert] SweetAlert2 is required but not loaded.');
    return;
  }

  // ==========================================
  // Internal State & Queue Management
  // ==========================================
  const modalQueue = [];
  let isDisplayingModal = false;

  // ==========================================
  // Custom Styles Injection (Gamified Theme)
  // ==========================================
  function injectLevelUpStyles() {
    if (document.getElementById('levelup-swal-custom-styles')) return;

    const style = document.createElement('style');
    style.id = 'levelup-swal-custom-styles';
    style.textContent = `
      /* LevelUp Base Modal Design */
      .levelup-swal-popup {
        font-family: 'Kanit', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        border-radius: 20px !important;
        padding: 1.75rem !important;
        backdrop-filter: blur(16px) !important;
        -webkit-backdrop-filter: blur(16px) !important;
        box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.35) !important;
        transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease !important;
      }
      
      /* Light Mode Styles */
      .levelup-swal-popup.levelup-theme-light {
        background: rgba(255, 255, 255, 0.96) !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        color: #0f172a !important;
      }

      /* Dark Mode Styles */
      .levelup-swal-popup.levelup-theme-dark {
        background: rgba(15, 23, 42, 0.96) !important;
        border: 1px solid rgba(99, 102, 241, 0.3) !important;
        color: #f8fafc !important;
        box-shadow: 0 20px 50px -10px rgba(0, 0, 0, 0.7), 0 0 25px rgba(99, 102, 241, 0.2) !important;
      }

      /* Typography & Text Formatting */
      .levelup-swal-title {
        font-size: 1.45rem !important;
        font-weight: 700 !important;
        line-height: 1.35 !important;
        letter-spacing: -0.01em !important;
        margin-bottom: 0.5rem !important;
      }
      
      .levelup-swal-html {
        font-size: 0.98rem !important;
        line-height: 1.55 !important;
        margin-top: 0.5rem !important;
      }

      /* Buttons Design */
      .levelup-swal-confirm-btn, .levelup-swal-cancel-btn, .levelup-swal-deny-btn {
        border-radius: 12px !important;
        font-weight: 600 !important;
        font-size: 0.95rem !important;
        padding: 0.7rem 1.5rem !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12) !important;
        outline: none !important;
      }

      .levelup-swal-confirm-btn:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4) !important;
      }

      .levelup-swal-cancel-btn:hover {
        transform: translateY(-2px) !important;
        filter: brightness(1.1) !important;
      }

      .levelup-swal-confirm-btn:focus, .levelup-swal-cancel-btn:focus {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.4) !important;
      }

      /* Toast Customization */
      .levelup-swal-toast {
        border-radius: 14px !important;
        padding: 0.75rem 1.25rem !important;
        font-weight: 500 !important;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.25) !important;
      }

      /* Gamified & Custom Card Containers */
      .levelup-card-box {
        border-radius: 14px;
        padding: 1rem;
        margin-top: 0.85rem;
        text-align: left;
        transition: all 0.3s ease;
      }
      .levelup-theme-light .levelup-card-box {
        background: rgba(241, 245, 249, 0.8);
        border: 1px solid rgba(203, 213, 225, 0.8);
        color: #1e293b;
      }
      .levelup-theme-dark .levelup-card-box {
        background: rgba(30, 41, 59, 0.7);
        border: 1px solid rgba(51, 65, 85, 0.8);
        color: #f1f5f9;
      }

      /* Progress Bar Utility */
      .levelup-progress-container {
        width: 100%;
        height: 12px;
        background: rgba(148, 163, 184, 0.25);
        border-radius: 999px;
        overflow: hidden;
        margin-top: 1rem;
        position: relative;
      }
      .levelup-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #6366f1, #06b6d4);
        border-radius: 999px;
        transition: width 0.35s ease-out;
      }

      /* Gamified Animations */
      @keyframes levelup-pulse-glow {
        0% { transform: scale(1); filter: drop-shadow(0 0 4px rgba(6, 182, 212, 0.4)); }
        50% { transform: scale(1.08); filter: drop-shadow(0 0 18px rgba(6, 182, 212, 0.85)); }
        100% { transform: scale(1); filter: drop-shadow(0 0 4px rgba(6, 182, 212, 0.4)); }
      }
      .levelup-ai-icon-anim {
        font-size: 3.2rem;
        display: inline-block;
        animation: levelup-pulse-glow 2s infinite ease-in-out;
      }

      @keyframes levelup-trophy-bounce {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        25% { transform: translateY(-8px) rotate(-6deg); }
        75% { transform: translateY(-4px) rotate(6deg); }
      }
      .levelup-trophy-anim {
        font-size: 3.5rem;
        display: inline-block;
        animation: levelup-trophy-bounce 1.6s infinite ease-in-out;
      }

      /* Responsive Rules */
      @media (max-width: 576px) {
        .levelup-swal-popup {
          width: 92% !important;
          padding: 1.25rem !important;
        }
        .levelup-swal-title {
          font-size: 1.2rem !important;
        }
        .levelup-swal-html {
          font-size: 0.9rem !important;
        }
        .levelup-swal-confirm-btn, .levelup-swal-cancel-btn {
          width: 100% !important;
          margin-bottom: 0.5rem !important;
        }
      }
    `;
    document.head.appendChild(style);
  }

  // Safe HTML Escaping (XSS Prevention Utility)
  function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // Dynamic Dark/Light Theme Detection
  function isDarkMode() {
    const docTheme = document.documentElement.getAttribute('data-theme') || 
                     document.documentElement.getAttribute('data-bs-theme');
    if (docTheme) return docTheme === 'dark';
    if (document.body && (document.body.classList.contains('dark-mode') || document.body.classList.contains('dark'))) return true;
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
  }

  // Get Active Theme Palette
  function getThemeConfig() {
    const dark = isDarkMode();
    return {
      isDark: dark,
      themeClass: dark ? 'levelup-theme-dark' : 'levelup-theme-light',
      background: dark ? '#0f172a' : '#ffffff',
      color: dark ? '#f8fafc' : '#0f172a',
      confirmButtonColor: '#6366f1',
      cancelButtonColor: dark ? '#334155' : '#94a3b8',
      dangerButtonColor: '#ef4444',
      successButtonColor: '#10b981',
      warningButtonColor: '#f59e0b',
      aiAccent: '#06b6d4',
      subtext: dark ? '#94a3b8' : '#64748b'
    };
  }

  // Standardize Input Parameters
  function normalizeArgs(arg1, arg2, defaultTitle) {
    if (typeof arg1 === 'object' && arg1 !== null) {
      return arg1;
    }
    const options = { title: arg1 || defaultTitle };
    if (typeof arg2 === 'string') {
      options.text = arg2;
    } else if (typeof arg2 === 'object' && arg2 !== null) {
      Object.assign(options, arg2);
    }
    return options;
  }

  // ==========================================
  // Queue Executor System
  // ==========================================
  function fireSwal(config, resolvePromise) {
    const theme = getThemeConfig();
    
    // Inject custom class
    config.customClass = config.customClass || {};
    config.customClass.popup = `levelup-swal-popup ${theme.themeClass} ${config.customClass.popup || ''}`.trim();
    config.customClass.title = `levelup-swal-title ${config.customClass.title || ''}`.trim();
    config.customClass.htmlContainer = `levelup-swal-html ${config.customClass.htmlContainer || ''}`.trim();
    config.customClass.confirmButton = `levelup-swal-confirm-btn ${config.customClass.confirmButton || ''}`.trim();
    config.customClass.cancelButton = `levelup-swal-cancel-btn ${config.customClass.cancelButton || ''}`.trim();
    config.customClass.denyButton = `levelup-swal-deny-btn ${config.customClass.denyButton || ''}`.trim();

    if (!config.toast) {
      config.background = theme.background;
      config.color = theme.color;
    }

    const originalDidClose = config.didClose;
    config.didClose = () => {
      if (typeof originalDidClose === 'function') {
        originalDidClose();
      }
      if (!config.toast) {
        isDisplayingModal = false;
        processNextInQueue();
      }
    };

    const swalInstance = Swal.fire(config);
    if (resolvePromise) {
      swalInstance.then(resolvePromise);
    }
    return swalInstance;
  }

  function queueModal(config, resolvePromise) {
    injectLevelUpStyles();
    if (isDisplayingModal && Swal.isVisible() && !Swal.getPopup().classList.contains('swal2-toast')) {
      modalQueue.push({ config, resolvePromise });
    } else {
      isDisplayingModal = true;
      fireSwal(config, resolvePromise);
    }
  }

  function processNextInQueue() {
    if (modalQueue.length > 0) {
      const next = modalQueue.shift();
      isDisplayingModal = true;
      setTimeout(() => {
        fireSwal(next.config, next.resolvePromise);
      }, 120);
    }
  }

  // ==========================================
  // Core Public API Definition
  // ==========================================
  const LevelUpAlert = {
    __initialized: true,

    /**
     * 1. Success Alert
     */
    success: function (titleOrOpts, text) {
      const opts = normalizeArgs(titleOrOpts, text, 'ดำเนินการสำเร็จ');
      const theme = getThemeConfig();
      queueModal({
        icon: 'success',
        title: opts.title,
        text: opts.text,
        confirmButtonText: opts.confirmText || 'ตกลง',
        confirmButtonColor: opts.confirmButtonColor || theme.successButtonColor,
        timer: opts.timer || undefined,
        timerProgressBar: !!opts.timer,
        ...opts
      });
    },

    /**
     * 2. Error Alert
     */
    error: function (titleOrOpts, text) {
      const opts = normalizeArgs(titleOrOpts, text, 'เกิดข้อผิดพลาด');
      const theme = getThemeConfig();
      queueModal({
        icon: 'error',
        title: opts.title,
        text: opts.text,
        confirmButtonText: opts.confirmText || 'ตกลง',
        confirmButtonColor: opts.confirmButtonColor || theme.dangerButtonColor,
        ...opts
      });
    },

    /**
     * 3. Warning Alert
     */
    warning: function (titleOrOpts, text) {
      const opts = normalizeArgs(titleOrOpts, text, 'คำเตือน');
      const theme = getThemeConfig();
      queueModal({
        icon: 'warning',
        title: opts.title,
        text: opts.text,
        confirmButtonText: opts.confirmText || 'ตกลง',
        confirmButtonColor: opts.confirmButtonColor || theme.warningButtonColor,
        ...opts
      });
    },

    /**
     * 4. Info Alert
     */
    info: function (titleOrOpts, text) {
      const opts = normalizeArgs(titleOrOpts, text, 'แจ้งเพื่อทราบ');
      const theme = getThemeConfig();
      queueModal({
        icon: 'info',
        title: opts.title,
        text: opts.text,
        confirmButtonText: opts.confirmText || 'ตกลง',
        confirmButtonColor: opts.confirmButtonColor || theme.confirmButtonColor,
        ...opts
      });
    },

    /**
     * 5. Confirm Dialog (Returns Promise<boolean>)
     */
    confirm: function (titleOrOpts, text) {
      const opts = normalizeArgs(titleOrOpts, text, 'ยืนยันการทำรายการ');
      const theme = getThemeConfig();

      return new Promise((resolve) => {
        queueModal({
          icon: opts.icon || 'question',
          title: opts.title,
          text: opts.text,
          showCancelButton: true,
          confirmButtonText: opts.confirmText || 'ยืนยัน',
          cancelButtonText: opts.cancelText || 'ยกเลิก',
          confirmButtonColor: opts.confirmButtonColor || theme.confirmButtonColor,
          cancelButtonColor: opts.cancelButtonColor || theme.cancelButtonColor,
          reverseButtons: opts.reverseButtons !== undefined ? opts.reverseButtons : true,
          focusCancel: opts.focusCancel !== undefined ? opts.focusCancel : false,
          ...opts
        }, (result) => {
          resolve(!!result.isConfirmed);
        });
      });
    },

    /**
     * 6. Loading Modal
     */
    loading: function (titleOrOpts, text) {
      const opts = normalizeArgs(titleOrOpts, text, 'กำลังประมวลผล...');
      queueModal({
        title: opts.title,
        text: opts.text,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
          Swal.showLoading();
          if (typeof opts.didOpen === 'function') opts.didOpen();
        },
        ...opts
      });
    },

    /**
     * 7. Close Active Modal
     */
    close: function () {
      if (Swal.isVisible()) {
        Swal.close();
      }
    },

    /**
     * 8. AI Loading Alert (Gamified Futuristic Theme)
     */
    aiLoading: function (titleOrOpts, text) {
      const opts = normalizeArgs(titleOrOpts, text, '🤖 AI กำลังคิดวิเคราะห์...');
      const cleanTitle = escapeHtml(opts.title);
      const cleanSubtext = escapeHtml(opts.text || 'กำลังประมวลผลข้อมูลอัจฉริยะ กรุณารอสักครู่');

      queueModal({
        html: `
          <div class="py-3 text-center">
            <div class="levelup-ai-icon-anim mb-3">🤖</div>
            <div class="fw-bold fs-5 mb-2" style="color: #06b6d4;">${cleanTitle}</div>
            <div class="text-muted fs-sm">${cleanSubtext}</div>
          </div>
        `,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
          Swal.showLoading();
          if (typeof opts.didOpen === 'function') opts.didOpen();
        },
        ...opts
      });
    },

    /**
     * 9. Toast Notification
     */
    toast: function (titleOrOpts, icon) {
      injectLevelUpStyles();
      let opts = {};
      if (typeof titleOrOpts === 'string') {
        opts = { title: titleOrOpts, icon: icon || 'success' };
      } else {
        opts = titleOrOpts || {};
      }

      const theme = getThemeConfig();
      const Toast = Swal.mixin({
        toast: true,
        position: opts.position || 'top-end',
        showConfirmButton: false,
        timer: opts.timer || 3000,
        timerProgressBar: true,
        background: theme.background,
        color: theme.color,
        customClass: {
          popup: `levelup-swal-toast ${theme.themeClass}`
        },
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer);
          toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
      });

      return Toast.fire({
        icon: opts.icon || 'info',
        title: opts.title || '',
        text: opts.text || undefined,
        ...opts
      });
    },

    /**
     * 10. Progress Modal
     */
    progress: function (titleOrOpts, percent) {
      let opts = {};
      if (typeof titleOrOpts === 'string') {
        opts = { title: titleOrOpts, percent: typeof percent === 'number' ? percent : 0 };
      } else {
        opts = titleOrOpts || {};
      }

      const initialPercent = Math.min(100, Math.max(0, opts.percent || 0));
      const cleanTitle = escapeHtml(opts.title || 'กำลังดำเนินการ...');

      queueModal({
        title: cleanTitle,
        html: `
          <div class="levelup-progress-wrapper text-center">
            <div id="levelup-progress-text" class="fw-bold mb-2 fs-6" style="color: #6366f1;">${initialPercent}%</div>
            <div class="levelup-progress-container">
              <div id="levelup-progress-bar" class="levelup-progress-bar" style="width: ${initialPercent}%;"></div>
            </div>
            <div id="levelup-progress-status" class="text-muted fs-sm mt-2">${escapeHtml(opts.statusText || 'กรุณารอสักครู่...')}</div>
          </div>
        `,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        ...opts
      });
    },

    /**
     * 11. Update Active Progress Modal
     */
    updateProgress: function (percent, statusText) {
      const pText = document.getElementById('levelup-progress-text');
      const pBar = document.getElementById('levelup-progress-bar');
      const pStatus = document.getElementById('levelup-progress-status');

      const cleanPercent = Math.min(100, Math.max(0, parseInt(percent, 10) || 0));

      if (pText) pText.innerText = `${cleanPercent}%`;
      if (pBar) pBar.style.width = `${cleanPercent}%`;
      if (pStatus && statusText) pStatus.innerText = escapeHtml(statusText);
    },

    /**
     * 12. Network Error Alert (With Retry Option)
     */
    networkError: function (opts) {
      opts = opts || {};
      const theme = getThemeConfig();

      return new Promise((resolve) => {
        queueModal({
          icon: 'error',
          title: opts.title || 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้ 📡',
          text: opts.text || 'กรุณาตรวจสอบการเชื่อมต่ออินเทอร์เน็ต แล้วลองใหม่อีกครั้ง',
          showCancelButton: true,
          confirmButtonText: opts.retryText || 'ลองใหม่',
          cancelButtonText: opts.cancelText || 'ปิด',
          confirmButtonColor: theme.confirmButtonColor,
          cancelButtonColor: theme.cancelButtonColor,
          ...opts
        }, (result) => {
          if (result.isConfirmed && typeof opts.onRetry === 'function') {
            opts.onRetry();
          }
          resolve(!!result.isConfirmed);
        });
      });
    },

    /**
     * 13. AJAX Error Universal Parser
     */
    ajaxError: function (xhr, status, error) {
      let title = 'เกิดข้อผิดพลาดในการเชื่อมต่อ';
      let message = 'ไม่สามารถดึงข้อมูลจากเซิร์ฟเวอร์ได้';

      if (status === 'timeout') {
        title = 'หมดเวลาการเชื่อมต่อ ⏱️';
        message = 'การร้องขอใช้เวลานานเกินไป กรุณาลองใหม่อีกครั้ง';
      } else if (xhr && xhr.responseJSON) {
        if (xhr.responseJSON.message) {
          message = xhr.responseJSON.message;
        } else if (xhr.responseJSON.error) {
          message = xhr.responseJSON.error;
        }
      } else if (xhr && xhr.status) {
        switch (xhr.status) {
          case 400:
            title = 'ข้อมูลไม่ถูกต้อง (400)';
            message = 'คำขอไม่ถูกต้อง กรุณาตรวจสอบข้อมูล';
            break;
          case 401:
            title = 'ไม่ได้เข้าสู่ระบบ (401)';
            message = 'กรุณาเข้าสู่ระบบก่อนทำรายการ';
            break;
          case 403:
            title = 'ไม่มีสิทธิ์เข้าถึง (403)';
            message = 'คุณไม่มีสิทธิ์ในการดำเนินการนี้';
            break;
          case 404:
            title = 'ไม่พบข้อมูล (404)';
            message = 'ไม่พบทรัพยากรที่ต้องการ';
            break;
          case 422:
            title = 'ข้อมูลไม่ผ่านการตรวจสอบ (422)';
            message = 'กรุณาตรวจสอบข้อมูลที่กรอกอีกครั้ง';
            break;
          case 500:
            title = 'ข้อผิดพลาดภายในเซิร์ฟเวอร์ (500)';
            message = 'เกิดปัญหาที่ระบบเซิร์ฟเวอร์ กรุณาติดต่อผู้ดูแลระบบ';
            break;
          default:
            message = `HTTP Status Error Code: ${xhr.status}`;
        }
      } else if (error) {
        message = String(error);
      }

      this.error(title, message);
    },

    /**
     * 14. Session Timeout Alert
     */
    sessionTimeout: function (opts) {
      opts = opts || {};
      let secondsLeft = opts.seconds || 60;
      const theme = getThemeConfig();
      let timerInterval = null;

      return new Promise((resolve) => {
        queueModal({
          icon: 'warning',
          title: opts.title || 'Session กำลังจะหมดอายุ ⌛',
          html: `
            <div>คุณจะถูกออกจากระบบโดยอัตโนมัติภายใน <strong id="levelup-session-timer" style="color:#ef4444; font-size: 1.2rem;">${secondsLeft}</strong> วินาที</div>
            <div class="text-muted fs-sm mt-1">ต้องการต่ออายุการใช้งานหรือไม่?</div>
          `,
          showCancelButton: true,
          confirmButtonText: opts.continueText || 'อยู่ต่อในระบบ',
          cancelButtonText: opts.logoutText || 'ออกจากระบบ',
          confirmButtonColor: theme.successButtonColor,
          cancelButtonColor: theme.dangerButtonColor,
          allowOutsideClick: false,
          allowEscapeKey: false,
          didOpen: () => {
            const timerEl = document.getElementById('levelup-session-timer');
            timerInterval = setInterval(() => {
              secondsLeft--;
              if (timerEl) timerEl.innerText = secondsLeft;
              if (secondsLeft <= 0) {
                clearInterval(timerInterval);
                Swal.close();
                if (typeof opts.onLogout === 'function') opts.onLogout();
                resolve('timeout');
              }
            }, 1000);
          },
          willClose: () => {
            if (timerInterval) clearInterval(timerInterval);
          }
        }, (result) => {
          if (timerInterval) clearInterval(timerInterval);
          if (result.isConfirmed) {
            if (typeof opts.onContinue === 'function') opts.onContinue();
            resolve('continue');
          } else {
            if (typeof opts.onLogout === 'function') opts.onLogout();
            resolve('logout');
          }
        });
      });
    },

    /**
     * 15. Form Validation Alert
     */
    validation: function (titleOrOpts, text) {
      const opts = normalizeArgs(titleOrOpts, text, 'กรุณาตรวจสอบข้อมูล');
      const theme = getThemeConfig();
      queueModal({
        icon: 'warning',
        title: opts.title,
        text: opts.text,
        confirmButtonText: opts.confirmText || 'ตกลง',
        confirmButtonColor: opts.confirmButtonColor || theme.warningButtonColor,
        ...opts
      });
    },

    /**
     * 16. Logout Confirmation (Returns Promise<boolean>)
     */
    logout: function (opts) {
      opts = opts || {};
      const theme = getThemeConfig();
      return new Promise((resolve) => {
        queueModal({
          icon: 'question',
          title: opts.title || 'ออกจากระบบ? 🚪',
          text: opts.text || 'คุณต้องการออกจากระบบ LevelUp หรือไม่?',
          showCancelButton: true,
          confirmButtonText: opts.confirmText || 'ออกจากระบบ',
          cancelButtonText: opts.cancelText || 'ยกเลิก',
          confirmButtonColor: theme.dangerButtonColor,
          cancelButtonColor: theme.cancelButtonColor,
          reverseButtons: true,
          ...opts
        }, (result) => {
          resolve(!!result.isConfirmed);
        });
      });
    },

    /**
     * 17. Delete Confirmation (Returns Promise<boolean>)
     */
    deleteConfirm: function (opts) {
      opts = opts || {};
      const theme = getThemeConfig();
      return new Promise((resolve) => {
        queueModal({
          icon: 'warning',
          title: opts.title || 'คุณแน่ใจหรือไม่? 🗑️',
          text: opts.text || 'การดำเนินการนี้จะถูกลบอย่างถาวรและไม่สามารถย้อนกลับได้',
          showCancelButton: true,
          confirmButtonText: opts.confirmText || 'ใช่, ลบเลย',
          cancelButtonText: opts.cancelText || 'ยกเลิก',
          confirmButtonColor: theme.dangerButtonColor,
          cancelButtonColor: theme.cancelButtonColor,
          focusCancel: true,
          reverseButtons: true,
          ...opts
        }, (result) => {
          resolve(!!result.isConfirmed);
        });
      });
    },

    /**
     * 18. Gamification: Quiz Result Modal
     */
    quizResult: function (opts) {
      opts = opts || {};
      const theme = getThemeConfig();
      const score = Number(opts.score || 0);
      const total = Number(opts.total || 100);
      const passed = opts.passed !== undefined ? opts.passed : (score >= total * 0.7);
      const percent = Math.round((score / total) * 100);

      const statusBadge = passed
        ? `<span style="background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid #10b981; padding: 4px 12px; border-radius: 999px; font-weight: 700; font-size: 0.85rem;">ผ่าน ✓</span>`
        : `<span style="background: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid #ef4444; padding: 4px 12px; border-radius: 999px; font-weight: 700; font-size: 0.85rem;">ไม่ผ่าน ✕</span>`;

      queueModal({
        title: opts.title || (passed ? '🎉 Quiz Complete!' : '💪 พยายามอีกครั้ง!'),
        html: `
          <div class="levelup-card-box">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="fw-bold">สถานะ:</span>
              <div>${statusBadge}</div>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="fw-bold">คะแนนที่ได้:</span>
              <span class="fs-5 fw-bold" style="color: ${passed ? '#10b981' : '#ef4444'};">${score} / ${total} (${percent}%)</span>
            </div>
            ${opts.xp ? `
              <div class="d-flex justify-content-between align-items-center pt-2" style="border-top: 1px dashed rgba(148, 163, 184, 0.3);">
                <span class="fw-bold">รางวัล XP:</span>
                <span class="fw-bold" style="color: #f59e0b;">+${escapeHtml(opts.xp)} XP ⚡</span>
              </div>
            ` : ''}
          </div>
        `,
        icon: passed ? 'success' : 'error',
        confirmButtonText: opts.confirmText || 'ตกลง',
        confirmButtonColor: passed ? theme.successButtonColor : theme.confirmButtonColor,
        ...opts
      });
    },

    /**
     * 19. Gamification: Lab Result Modal
     */
    labResult: function (opts) {
      opts = opts || {};
      const theme = getThemeConfig();
      const testCasesPassed = opts.testCasesPassed !== undefined ? opts.testCasesPassed : (opts.passCount || 0);
      const totalTestCases = opts.totalTestCases !== undefined ? opts.totalTestCases : (opts.totalCount || 0);
      const passed = opts.passed !== undefined ? opts.passed : (testCasesPassed === totalTestCases);

      queueModal({
        title: opts.title || (passed ? '🧪 Lab Complete!' : '🧪 Lab Attempt Failed'),
        html: `
          <div class="levelup-card-box">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="fw-bold">Test Cases:</span>
              <span class="fw-bold" style="color: ${passed ? '#10b981' : '#f59e0b'};">
                ${passed ? '✓' : '⚡'} ${testCasesPassed} / ${totalTestCases}
              </span>
            </div>
            ${opts.score !== undefined ? `
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold">คะแนน:</span>
                <span class="fw-bold" style="color: #6366f1;">${opts.score} / ${opts.maxScore || 100}</span>
              </div>
            ` : ''}
          </div>
        `,
        icon: passed ? 'success' : 'warning',
        confirmButtonText: opts.confirmText || 'ตกลง',
        confirmButtonColor: theme.confirmButtonColor,
        ...opts
      });
    },

    /**
     * 20. Gamification: Achievement Unlocked Modal
     */
    achievement: function (opts) {
      opts = opts || {};
      const theme = getThemeConfig();
      const title = escapeHtml(opts.title || 'Achievement Unlocked!');
      const description = escapeHtml(opts.description || opts.text || 'คุณปลดล็อกความสำเร็จใหม่');
      const xp = opts.xp ? escapeHtml(opts.xp) : null;

      queueModal({
        html: `
          <div class="text-center py-2">
            <div class="levelup-trophy-anim mb-2">🏆</div>
            <div class="text-uppercase fw-bold fs-xs tracking-wider mb-1" style="color: #f59e0b;">Achievement Unlocked!</div>
            <div class="fw-bold fs-4 mb-2" style="color: ${theme.color};">${title}</div>
            <div class="text-muted fs-sm mb-3">${description}</div>
            ${xp ? `
              <div class="d-inline-block px-3 py-1 rounded-pill fw-bold" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3);">
                +${xp} XP ⚡
              </div>
            ` : ''}
          </div>
        `,
        confirmButtonText: opts.confirmText || 'รับรางวัล 🎉',
        confirmButtonColor: theme.warningButtonColor,
        ...opts
      });
    },

    /**
     * 21. Gamification: Level Up Modal
     */
    levelUp: function (opts) {
      opts = opts || {};
      const theme = getThemeConfig();
      const oldLevel = opts.oldLevel || 1;
      const newLevel = opts.newLevel || (oldLevel + 1);
      const xpGained = opts.xpGained || opts.xp || null;

      queueModal({
        html: `
          <div class="text-center py-2">
            <div class="fs-1 mb-1">🚀</div>
            <div class="fw-bold fs-3 mb-2" style="color: #6366f1; letter-spacing: 1px;">LEVEL UP!</div>
            <div class="d-flex align-items-center justify-content-center gap-3 my-3">
              <span class="fs-5 text-muted">Level ${oldLevel}</span>
              <span class="fs-4" style="color: #06b6d4;">➔</span>
              <span class="fs-3 fw-bold" style="color: #10b981;">Level ${newLevel}</span>
            </div>
            ${xpGained ? `
              <div class="text-muted fs-sm">ได้รับคะแนนประสบการณ์สะสม <strong style="color: #f59e0b;">+${escapeHtml(xpGained)} XP</strong></div>
            ` : ''}
          </div>
        `,
        confirmButtonText: opts.confirmText || 'ลุยต่อเลย!',
        confirmButtonColor: theme.confirmButtonColor,
        ...opts
      });
    },

    /**
     * 22. Gamification: Certificate Modal
     */
    certificate: function (opts) {
      opts = opts || {};
      const theme = getThemeConfig();
      const courseTitle = escapeHtml(opts.courseTitle || opts.title || 'คอร์สเรียน LevelUp');

      return new Promise((resolve) => {
        queueModal({
          html: `
            <div class="text-center py-2">
              <div class="fs-1 mb-2">🎓</div>
              <div class="fw-bold fs-4 mb-2" style="color: #10b981;">ยินดีด้วย! คุณเรียนจบแล้ว</div>
              <div class="text-muted fs-sm mb-3">คุณได้รับใบประกาศนียบัตรสำหรับคอร์ส</div>
              <div class="levelup-card-box text-center fw-bold" style="color: #6366f1;">
                ${courseTitle}
              </div>
            </div>
          `,
          showCancelButton: true,
          confirmButtonText: opts.confirmText || 'ดูใบประกาศนียบัตร 📜',
          cancelButtonText: opts.cancelText || 'ปิด',
          confirmButtonColor: theme.successButtonColor,
          cancelButtonColor: theme.cancelButtonColor,
          ...opts
        }, (result) => {
          if (result.isConfirmed && typeof opts.onViewCertificate === 'function') {
            opts.onViewCertificate();
          }
          resolve(!!result.isConfirmed);
        });
      });
    },

    /**
     * 23. Bookmark Toast Alert
     */
    bookmark: function (isBookmarked, title) {
      const active = isBookmarked !== undefined ? isBookmarked : true;
      const textTitle = title || (active ? 'เพิ่ม Bookmark แล้ว 🔖' : 'ลบออกจาก Bookmark แล้ว 🔖');
      return this.toast({
        title: textTitle,
        icon: active ? 'success' : 'info',
        timer: 2000
      });
    },

    /**
     * 24. Copied Toast Alert
     */
    copied: function (text) {
      const textTitle = text ? `คัดลอก "${escapeHtml(text)}" แล้ว` : 'คัดลอกไปยัง Clipboard แล้ว ✓';
      return this.toast({
        title: textTitle,
        icon: 'success',
        timer: 2000
      });
    }
  };

  // Auto Initializer for Dynamic Theme CSS Injection
  injectLevelUpStyles();

  // Expose to Global Window Namespace
  window.LevelUpAlert = LevelUpAlert;

})(window, document);