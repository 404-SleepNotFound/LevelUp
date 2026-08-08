/* =========================================
   LevelUp Frontend - script.js
   Combines: global site behaviour, auth/register wizard,
   dashboard, course detail, lesson page, AI chat widgets,
   course creation form handler, and auto select-to-custom dropdown converter.

   Every section below is guarded (it checks the elements it
   needs exist before doing anything), so this single file can
   safely be included on any page without throwing errors for
   markup that isn't present on that page.
   ========================================= */
'use strict';

$(document).ready(function () {
    initThemeToggleGlobal();
    initNavbarScrollEffect();
    initStatsCounterAnimation();
    initAiSearchAutocomplete();
    initRegisterWizard();
    initDashboard();
    initCourseDetail();
    initLessonPage();
    initLearningPathSimulator();
    initAiMentorChat();
    initCourseCreateForm(); // เพิ่มการทำงานของฟอร์มสร้างคอร์สเรียน
    initAutoCustomDropdowns(); // แปลง <select> ปกติเป็น Custom Dropdown เพื่อรองรับ Rocket Cursor
});

/* =========================================
   1. Global Theme Toggle (persisted via localStorage)
   ========================================= */
function initThemeToggleGlobal() {
    const $themeToggleBtn = $('#themeToggleBtn');
    if (!$themeToggleBtn.length) return;

    const $html = $('html');
    const $icon = $themeToggleBtn.find('i');

    function updateIcon(theme) {
        if (theme === 'dark') {
            $icon.removeClass('bi-moon-stars-fill').addClass('bi-sun-fill text-warning');
        } else {
            $icon.removeClass('bi-sun-fill text-warning').addClass('bi-moon-stars-fill');
        }
    }

    const savedTheme = localStorage.getItem('theme') || 'light';
    $html.attr('data-theme', savedTheme);
    updateIcon(savedTheme);

    $themeToggleBtn.on('click', function () {
        const currentTheme = $html.attr('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';

        $html.attr('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateIcon(newTheme);
    });
}

/* =========================================
   2. Navbar Scroll Effect
   ========================================= */
function initNavbarScrollEffect() {
    const $navbar = $('.custom-navbar');
    if (!$navbar.length) return;

    $(window).on('scroll', function () {
        $navbar.toggleClass('shadow-sm', $(window).scrollTop() > 50);
    });
}

/* =========================================
   3. Counter Animation (Statistics section)
   ========================================= */
function initStatsCounterAnimation() {
    const $statsSection = $('.stats-section');
    if (!$statsSection.length) return;

    const $counters = $('.counter');
    let hasAnimated = false;

    $(window).on('scroll', function () {
        const offsetTop = $statsSection.offset().top - window.innerHeight;
        if ($(window).scrollTop() > offsetTop && !hasAnimated) {
            $counters.each(function () {
                const $this = $(this);
                const target = parseInt($this.attr('data-target'), 10);

                $({ countNum: 0 }).animate({ countNum: target }, {
                    duration: 2000,
                    easing: 'swing',
                    step: function () {
                        $this.text(Math.floor(this.countNum).toLocaleString());
                    },
                    complete: function () {
                        $this.text(this.countNum.toLocaleString());
                    }
                });
            });
            hasAnimated = true;
        }
    });
}

/* =========================================
   4. AJAX AI Search Autocomplete
   ========================================= */
function initAiSearchAutocomplete() {
    const $searchInput = $('#aiSearchInput');
    if (!$searchInput.length) return;

    const $dropdown = $('#searchDropdown');
    let searchTimeout;

    $searchInput.on('input', function () {
        const query = $(this).val().trim();
        clearTimeout(searchTimeout);

        if (query.length < 2) {
            $dropdown.addClass('d-none');
            return;
        }

        searchTimeout = setTimeout(function () {
            $.ajax({
                url: 'api/search-autocomplete.php',
                method: 'GET',
                data: { q: query },
                dataType: 'json',
                success: function (response) {
                    $dropdown.empty();
                    if (response.length > 0) {
                        response.forEach(function (item) {
                            const badge = item.type === 'ai'
                                ? '<span class="badge bg-primary-subtle text-primary ms-2 fs-sm"><i class="bi bi-stars"></i> AI แนะนำ</span>'
                                : '';
                            $dropdown.append(`
                                <a href="${item.url}" class="search-item">
                                    <div class="fw-medium">${item.title} ${badge}</div>
                                    <small class="text-muted">${item.desc}</small>
                                </a>
                            `);
                        });
                    } else {
                        $dropdown.html('<div class="p-3 text-muted text-center">ไม่พบผลลัพธ์ ลองเปลี่ยนคำค้นหา</div>');
                    }
                    $dropdown.removeClass('d-none');
                },
                error: function () {
                    // Silent fail for UX
                }
            });
        }, 300); // Debounce 300ms
    });

    // Close dropdown when clicking elsewhere
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.search-box-wrapper').length) {
            $dropdown.addClass('d-none');
        }
    });
}

/* =========================================
   5. Register Wizard
   ========================================= */
function initRegisterWizard() {
    const $form = $('#registerForm');
    if (!$form.length) return;

    // 5.1 Step Navigation
    $('.btn-next').on('click', function () {
        const nextStep = $(this).data('next');
        const currentStep = nextStep - 1;

        if (validateStep(currentStep)) {
            $('#step' + currentStep).addClass('d-none');
            $('#step' + nextStep).removeClass('d-none');
            updateProgress(nextStep);
            if (nextStep === 3) $('#loginLinkContainer').addClass('d-none'); // hide login link on last step
        }
    });

    $('.btn-prev').on('click', function () {
        const prevStep = $(this).data('prev');
        const currentStep = prevStep + 1;

        $('#step' + currentStep).addClass('d-none');
        $('#step' + prevStep).removeClass('d-none');
        updateProgress(prevStep);
        if (prevStep < 3) $('#loginLinkContainer').removeClass('d-none');
    });

    function updateProgress(step) {
        const percentage = (step / 3) * 100;
        $('#registerProgress').css('width', percentage + '%');
        $('.step-indicator').removeClass('active');
        $(`.step-indicator[data-step="${step}"]`).addClass('active');
    }

    // 5.2 Password Strength Scoring (shared by validation + the strength meter UI)
    function scorePassword(pass) {
        let score = 0;
        const hasLen = pass.length >= 8;
        const hasUpLow = /[a-z]/.test(pass) && /[A-Z]/.test(pass);
        const hasNumSpec = /\d/.test(pass) || /[^a-zA-Z0-9]/.test(pass);

        if (hasLen) score++;
        if (hasUpLow) score++;
        if (hasNumSpec) score++;
        if (pass.length > 10 && hasUpLow && /\d/.test(pass) && /[^a-zA-Z0-9]/.test(pass)) score = 4; // Max score

        return score;
    }

    // 5.3 Step Validation
    function validateStep(step) {
        let isValid = true;

        if (step === 1) {
            const $email = $('#email');
            const $user = $('#username');
            const $pass = $('#password');
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailPattern.test($email.val())) { $email.addClass('is-invalid'); isValid = false; }
            if ($user.val().length < 4) { $user.addClass('is-invalid'); isValid = false; }
            if (scorePassword($pass.val()) < 2) { $pass.addClass('is-invalid'); isValid = false; } // require at least level 2
        } else if (step === 2) {
            $('#step2 input, #step2 select').each(function () {
                if (!$(this).val()) {
                    $(this).addClass('is-invalid');
                    isValid = false;
                }
            });
        }
        return isValid;
    }

    // Remove invalid state as soon as the user edits a field
    $('#registerForm input, #registerForm select').on('input change', function () {
        $(this).removeClass('is-invalid');
    });

    // 5.4 Password Strength Meter UI
    $('#password').on('input', function () {
        const pass = $(this).val();
        const score = scorePassword(pass);

        const hasLen = pass.length >= 8;
        const hasUpLow = /[a-z]/.test(pass) && /[A-Z]/.test(pass);
        const hasNumSpec = /\d/.test(pass) || /[^a-zA-Z0-9]/.test(pass);

        $('#rule-len').toggleClass('valid', hasLen);
        $('#rule-up').toggleClass('valid', hasUpLow);
        $('#rule-num').toggleClass('valid', hasNumSpec);

        $('.meter-bar').css('background-color', 'var(--border-color)');
        let color = '#EF4444'; // Red
        let text = 'อ่อนแอ';

        if (score === 2) { color = '#F59E0B'; text = 'ปานกลาง'; } // Yellow
        if (score >= 3) { color = '#10B981'; text = 'ปลอดภัย'; } // Green

        for (let i = 1; i <= score; i++) {
            $(`.meter-bar[data-index="${i}"]`).css('background-color', color);
        }

        $('#passwordText')
            .text(pass.length === 0 ? 'ความปลอดภัยรหัสผ่าน' : text)
            .css('color', pass.length > 0 ? color : 'var(--text-muted)');
    });

    // 5.5 Selectable Badges (Multiple)
    $('.selection-group .selection-badge').on('click', function () {
        $(this).toggleClass('active');
        const $parent = $(this).closest('.selection-group');
        const inputId = $parent.data('input');

        const selected = [];
        $parent.find('.selection-badge.active').each(function () {
            selected.push($(this).text());
        });

        $('#' + inputId).val(selected.join(', '));
        $('#error-' + inputId).addClass('d-none');
    });

    // 5.6 Selectable Cards (Single/Radio)
    $('.selection-group-radio .selection-card').on('click', function () {
        const $parent = $(this).closest('.selection-group-radio');
        $parent.find('.selection-card').removeClass('active');
        $(this).addClass('active');

        const inputId = $parent.data('input');
        $('#' + inputId).val($(this).data('val'));
        $('#error-' + inputId).addClass('d-none');
    });

    // 5.7 Final Form Submission
    $form.on('submit', function (e) {
        e.preventDefault();

        let isValid = true;
        if (!$('#goals').val()) { $('#error-goals').removeClass('d-none'); isValid = false; }
        if (!$('#study_time').val()) { $('#error-study').removeClass('d-none'); isValid = false; }
        if (!$('#terms').is(':checked')) { $('#terms').addClass('is-invalid'); isValid = false; }

        if (!isValid) return;

        const $btn = $('#btnSubmit');
        $btn.prop('disabled', true).find('.btn-text').text('กำลังสร้างบัญชี...');
        $btn.find('.spinner-border').removeClass('d-none');

        const formData = $(this).serialize() + '&action=register';

        $.ajax({
            url: 'api/auth-register.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    $('#registerAlert').text(response.message).removeClass('d-none alert-danger').addClass('alert-success');
                    $btn.removeClass('btn-primary').addClass('btn-success').find('.btn-text').text('สำเร็จ!');
                    setTimeout(() => { window.location.href = response.redirect; }, 1500);
                } else {
                    $('#registerAlert').text(response.message).removeClass('d-none alert-success').addClass('alert-danger');
                    $btn.prop('disabled', false).find('.btn-text').text('สร้างบัญชีผู้ใช้');
                    $btn.find('.spinner-border').addClass('d-none');
                }
            },
            error: function () {
                $('#registerAlert').text('เกิดข้อผิดพลาดจากเซิร์ฟเวอร์').removeClass('d-none alert-success').addClass('alert-danger');
                $btn.prop('disabled', false).find('.btn-text').text('สร้างบัญชีผู้ใช้');
                $btn.find('.spinner-border').addClass('d-none');
            }
        });
    });
}

/* =========================================
   6. Dashboard
   ========================================= */
function initDashboard() {
    const $missionCheckboxes = $('.mission-checkbox');
    if (!$missionCheckboxes.length && !$('.course-continue-card').length) return;

    // 6.1 Mission Checkbox Toggle & AJAX
    $missionCheckboxes.on('change', function () {
        const $checkbox = $(this);
        const missionId = $checkbox.data('id');
        const isChecked = $checkbox.is(':checked');
        const $textSpan = $checkbox.next('span');

        // Optimistic UI update
        $textSpan.toggleClass('text-decoration-line-through text-muted', isChecked);
        $checkbox.prop('disabled', true);

        $.ajax({
            url: 'api/update-mission.php',
            type: 'POST',
            data: { mission_id: missionId, status: isChecked ? 1 : 0 },
            dataType: 'json',
            success: function (response) {
                $checkbox.prop('disabled', false);

                if (response.status === 'success') {
                    if (isChecked) {
                        playSuccessSound();
                        showToast(`ได้รับ ${response.xp_gained} XP! 🎯`);
                    }
                    if (response.is_all_completed) {
                        setTimeout(() => showToast('ยินดีด้วย! คุณทำภารกิจวันนี้ครบแล้ว 🔥 (โบนัส +50 XP)'), 1000);
                    }
                }
            },
            error: function () {
                // Revert UI on error
                $checkbox.prop('checked', !isChecked).prop('disabled', false);
                $textSpan.toggleClass('text-decoration-line-through text-muted', !isChecked);
                showToast('เกิดข้อผิดพลาด กรุณาลองใหม่');
            }
        });
    });

    // 6.2 Play Success Sound
    function playSuccessSound() {
        const audio = document.getElementById('successSound');
        if (audio) {
            audio.currentTime = 0;
            audio.volume = 0.5;
            audio.play().catch(e => console.log('Audio play prevented by browser policy'));
        }
    }

    // 6.3 Simple Toast Notification
    function showToast(message) {
        const $toast = $(`
            <div class="toast-notification position-fixed bottom-0 end-0 m-4 p-3 bg-surface border border-color shadow-lg rounded-3 d-flex align-items-center gap-3" style="z-index: 1050; transform: translateY(100px); opacity: 0; transition: all 0.3s ease;">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;"><i class="bi bi-check"></i></div>
                <span class="fw-medium text-main fs-sm">${message}</span>
            </div>
        `);
        $('body').append($toast);

        setTimeout(() => $toast.css({ transform: 'translateY(0)', opacity: '1' }), 10);
        setTimeout(() => {
            $toast.css({ transform: 'translateY(20px)', opacity: '0' });
            setTimeout(() => $toast.remove(), 300);
        }, 3000);
    }

    // 6.4 Continue-Learning Card: click anywhere on the card (not just the button)
    $('.course-continue-card').on('click', function (e) {
        if (!$(e.target).closest('button').length) {
            window.location.href = 'lesson.php';
        }
    });
}

/* =========================================
   7. Course Detail
   ========================================= */
function initCourseDetail() {
    const $expandAllBtn = $('#btnExpandAll');
    const $bookmarkBtn = $('#btnToggleBookmark');
    const $qaForm = $('#courseQaForm');
    if (!$expandAllBtn.length && !$bookmarkBtn.length && !$qaForm.length) return;

    // 7.1 Expand/Collapse All Accordions
    $expandAllBtn.on('click', function () {
        const isExpanded = $(this).data('expanded');
        if (isExpanded) {
            $('.custom-accordion .collapse').collapse('hide');
            $(this).text('ขยายทั้งหมด').data('expanded', false);
        } else {
            $('.custom-accordion .collapse').collapse('show');
            $(this).text('ย่อทั้งหมด').data('expanded', true);
        }
    });

    // 7.2 Bookmark Toggle (AJAX)
    $bookmarkBtn.on('click', function () {
        const $btn = $(this);
        const courseId = $btn.data('id');

        $btn.prop('disabled', true);

        $.ajax({
            url: 'api/toggle-bookmark.php',
            type: 'POST',
            data: { id: courseId },
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    if (res.is_bookmarked) {
                        $btn.removeClass('btn-outline-surface text-main').addClass('btn-primary text-white');
                        $btn.html(`<i class="bi bi-bookmark-fill"></i> บันทึกแล้ว`);
                    } else {
                        $btn.removeClass('btn-primary text-white').addClass('btn-outline-surface text-main');
                        $btn.html(`<i class="bi bi-bookmark"></i> บันทึกไว้ทีหลัง`);
                    }
                }
            },
            complete: function () {
                $btn.prop('disabled', false);
            }
        });
    });

    // 7.3 AI Course Q&A Widget
    const $chatBody = $('#aiChatBody');
    const $qaInput = $('#qaInput');

    function appendUserMessage(text) {
        $chatBody.append(`
            <div class="user-msg d-flex justify-content-end mb-3">
                <div class="bg-primary text-white rounded-3 p-2 fs-sm shadow-sm" style="max-width: 85%;">
                    ${text}
                </div>
            </div>
        `);
        scrollToBottom();
    }

    function appendTypingIndicator() {
        const id = 'typing-' + Date.now();
        $chatBody.append(`
            <div id="${id}" class="ai-msg d-flex gap-2 mb-3">
                <div class="bg-white border border-color rounded-3 p-2 fs-sm shadow-sm d-flex align-items-center">
                    <div class="typing-indicator"><span></span><span></span><span></span></div>
                </div>
            </div>
        `);
        scrollToBottom();
        return id;
    }

    function replaceTypingWithAnswer(id, html) {
        $(`#${id}`).html(`
            <div class="bg-white border border-color rounded-3 p-2 fs-sm shadow-sm text-main" style="max-width: 85%;">
                ${html}
            </div>
        `);
        scrollToBottom();
    }

    function scrollToBottom() {
        $chatBody.scrollTop($chatBody[0].scrollHeight);
    }

    function askAI(questionText) {
        if (!questionText.trim()) return;

        $qaInput.val('');
        appendUserMessage(questionText);
        const typingId = appendTypingIndicator();

        $.ajax({
            url: 'api/ai-course-qa.php',
            type: 'POST',
            data: { question: questionText, course_id: '101' }, // sample course id
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    replaceTypingWithAnswer(typingId, res.answer);
                }
            },
            error: function () {
                replaceTypingWithAnswer(typingId, "<span class='text-danger'>ขออภัย เกิดข้อผิดพลาดในการเชื่อมต่อ AI</span>");
            }
        });
    }

    $qaForm.on('submit', function (e) {
        e.preventDefault();
        askAI($qaInput.val());
    });

    $(document).on('click', '.qa-quick-btn', function (e) {
        e.preventDefault();
        askAI($(this).text());
        $(this).hide(); // hide the quick-prompt button once it's been used
    });
}

/* =========================================
   8. Lesson Page (video player, AI context chat,
      text highlighting, mini lab)
   ========================================= */
function initLessonPage() {
    const video = document.getElementById('lesson-video');
    if (!video) return;

    // 8.1 Theme Toggle (lesson-page specific button)
    $('#theme-toggle').on('click', function () {
        const currentTheme = $('html').attr('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        $('html').attr('data-theme', newTheme);
        const icon = newTheme === 'dark' ? 'fa-moon' : 'fa-sun';
        $(this).html(`<i class="fa-solid ${icon}"></i>`);
    });

    // 8.2 Toggle Left Sidebar
    $('.toggle-sidebar-btn').on('click', function () {
        $('#lesson-sidebar').toggleClass('collapsed');
    });

    // 8.3 Custom Video Player
    const playBtn = document.getElementById('play-pause');
    const progressBar = document.getElementById('video-progress');
    const progressFilled = document.getElementById('video-progress-filled');
    const timeDisplay = document.getElementById('current-time');
    const durationDisplay = document.getElementById('duration');

    function formatTime(seconds) {
        const m = Math.floor(seconds / 60);
        const s = Math.floor(seconds % 60);
        return `${m < 10 ? '0' : ''}${m}:${s < 10 ? '0' : ''}${s}`;
    }

    video.addEventListener('loadedmetadata', () => {
        durationDisplay.textContent = formatTime(video.duration);
    });

    function togglePlay() {
        if (video.paused) {
            video.play();
            playBtn.innerHTML = '<i class="fa-solid fa-pause"></i>';
            $('.video-container').removeClass('paused');
        } else {
            video.pause();
            playBtn.innerHTML = '<i class="fa-solid fa-play"></i>';
            $('.video-container').addClass('paused');
        }
    }

    playBtn.addEventListener('click', togglePlay);
    video.addEventListener('click', togglePlay);

    video.addEventListener('timeupdate', () => {
        const percent = (video.currentTime / video.duration) * 100;
        progressFilled.style.width = `${percent}%`;
        timeDisplay.textContent = formatTime(video.currentTime);
    });

    progressBar.addEventListener('click', (e) => {
        const time = (e.offsetX / progressBar.offsetWidth) * video.duration;
        video.currentTime = time;
    });

    // 8.4 AI Context-Aware Chat (video timestamp aware)
    function scrollAiChatToBottom() {
        const chatbox = document.getElementById('chat-messages');
        chatbox.scrollTop = chatbox.scrollHeight;
    }

    function sendAIMessage() {
        const text = $('#ai-input').val().trim();
        if (!text) return;

        const contextTime = formatTime(video.currentTime);

        const userHtml = `
            <div class="chat-message user text-sm mb-3">
                <div class="message-bubble">${text}</div>
            </div>`;
        $('#chat-messages').append(userHtml);
        $('#ai-input').val('');
        scrollAiChatToBottom();

        // Simulated AJAX request. In production this posts to ajax_lesson.php
        // with { action: 'chat', message: text, context: contextTime }.
        setTimeout(() => {
            const aiHtml = `
            <div class="chat-message ai text-sm mb-3">
                <div class="message-bubble">
                    <span class="text-primary small fw-bold d-block mb-1"><i class="fa-solid fa-clock"></i> Context: วิดีโอนาทีที่ ${contextTime}</span>
                    ระบบได้รับคำถามของคุณเกี่ยวกับ: "${text}"<br>
                    นี่คือคำตอบจำลองจาก AI อธิบายว่า Recursion ทำงานโดยการวาง Stack ทับซ้อนกันจนถึง Base Case...
                </div>
            </div>`;
            $('#chat-messages').append(aiHtml);
            scrollAiChatToBottom();

            $('#ai-tabs button[data-bs-target="#ai-chat"]').tab('show');
        }, 1000);
    }

    $('#btn-send-ai').on('click', sendAIMessage);
    $('#ai-input').on('keypress', function (e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            sendAIMessage();
        }
    });
    $('.prompt-btn').on('click', function () {
        $('#ai-input').val($(this).text());
        sendAIMessage();
    });

    // 8.5 Kindle-like Text Highlighting
    const readingArea = document.getElementById('reading-area');
    const highlightMenu = document.getElementById('highlight-menu');
    let savedRange = null;

    if (readingArea && highlightMenu) {
        readingArea.addEventListener('mouseup', function () {
            const selection = window.getSelection();
            if (selection.toString().trim() !== '') {
                savedRange = selection.getRangeAt(0);
                const rect = savedRange.getBoundingClientRect();
                highlightMenu.style.display = 'flex';
                highlightMenu.style.top = (rect.top + window.scrollY - 40) + 'px';
                highlightMenu.style.left = (rect.left + window.scrollX) + 'px';
            } else {
                highlightMenu.style.display = 'none';
            }
        });

        document.addEventListener('mousedown', function (e) {
            if (!highlightMenu.contains(e.target) && e.target.id !== 'reading-area') {
                highlightMenu.style.display = 'none';
            }
        });

        $('.color-btn').on('click', function () {
            const color = $(this).data('color');
            if (savedRange) {
                const span = document.createElement('span');
                span.className = `highlight highlight-${color}`;
                savedRange.surroundContents(span);
                window.getSelection().removeAllRanges();
                highlightMenu.style.display = 'none';

                // AJAX: persist the highlight, e.g.
                // $.post('ajax_lesson.php', { action: 'save_highlight', text: span.innerText, color: color });
            }
        });
    }

    // 8.6 Mini Lab Execution
    $('#btn-run-lab').on('click', function () {
        const code = $('#lab-editor').val();
        const $btn = $(this);
        $btn.html('<i class="fa-solid fa-spinner fa-spin me-2"></i>Running...');

        // Simulated execution
        setTimeout(() => {
            $btn.html('<i class="fa-solid fa-play me-2"></i>Run & Submit');
            const $resBox = $('#lab-result');
            $resBox.removeClass('d-none');

            if (code.includes('print')) {
                $resBox.removeClass('bg-danger bg-opacity-10 text-danger border-danger').addClass('bg-success bg-opacity-10 text-success border border-success');
                $resBox.html('<i class="fa-solid fa-check-circle me-2"></i> <strong>Passed!</strong> คุณได้ 10 คะแนน<br><small>Output: 5 4 3 2 1 0</small>');
            } else {
                $resBox.removeClass('bg-success bg-opacity-10 text-success border-success').addClass('bg-danger bg-opacity-10 text-danger border border-danger');
                $resBox.html('<i class="fa-solid fa-xmark-circle me-2"></i> <strong>Failed!</strong> กรุณาใช้คำสั่งที่พิมพ์ผลลัพธ์ออกมา');
            }
        }, 800);
    });
}

/* =========================================
   9. Learning Path & AI Simulator
   ========================================= */
function initLearningPathSimulator() {
    const $timeSlider = $('#timeSlider');
    if (!$timeSlider.length) return;

    const $timeDisplay = $('#timeDisplay');
    const $estimatedTime = $('#estimatedTime');
    const $careerSelector = $('#careerSelector');
    const $btnRebuildPath = $('#btnRebuildPath');
    const $roadmapContainer = $('#roadmapContainer');

    // Client-side estimate, updated instantly while dragging the slider
    function updateSimulatedTime() {
        const hours = parseInt($timeSlider.val(), 10);
        $timeDisplay.text(hours);

        const baseMonths = 10;
        const calcMonths = Math.max(1, Math.round((baseMonths / hours) * 10) / 10);

        $estimatedTime.css('opacity', '0');
        setTimeout(() => {
            $estimatedTime.text(calcMonths + ' เดือน').css('opacity', '1');
        }, 200);
    }
    $timeSlider.on('input', updateSimulatedTime);

    // Ask the server to rebuild the roadmap (AJAX)
    function rebuildLearningPath() {
        const originalBtnText = $btnRebuildPath.html();
        $btnRebuildPath.html('<span class="spinner-border spinner-border-sm me-2"></span>AI กำลังคำนวณ...').prop('disabled', true);

        $roadmapContainer.html(`
            <div class="d-flex flex-column gap-4 py-5 align-items-center justify-content-center text-center">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                <div class="text-muted">AI กำลังวิเคราะห์ประวัติและจัด Roadmap ใหม่...</div>
            </div>
        `);

        const requestData = {
            career: $careerSelector.val(),
            hours: $timeSlider.val()
        };

        $.ajax({
            url: 'api/ajax_learning_path.php',
            type: 'POST',
            data: requestData,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    $roadmapContainer.html(response.roadmap_html);
                    $estimatedTime.text(response.estimated_time);
                }
            },
            error: function () {
                $roadmapContainer.html('<div class="text-center text-danger py-5"><i class="bi bi-x-circle fs-1"></i><p>เกิดข้อผิดพลาดในการดึงข้อมูล Roadmap</p></div>');
            },
            complete: function () {
                $btnRebuildPath.html(originalBtnText).prop('disabled', false);
            }
        });
    }

    $careerSelector.on('change', rebuildLearningPath);
    $btnRebuildPath.on('click', rebuildLearningPath);

    // Roadmap zoom controls
    let zoomLevel = 1;
    $('button[title="ซูมเข้า"]').on('click', function () {
        if (zoomLevel < 1.5) { zoomLevel += 0.1; }
        $('.roadmap-tree').css('transform', `scale(${zoomLevel})`).css('transform-origin', 'top left');
    });
    $('button[title="ซูมออก"]').on('click', function () {
        if (zoomLevel > 0.6) { zoomLevel -= 0.1; }
        $('.roadmap-tree').css('transform', `scale(${zoomLevel})`).css('transform-origin', 'top left');
    });
}

/* =========================================
   10. AI Mentor Chat Widget (Markdown + Mermaid)
   ========================================= */
function initAiMentorChat() {
    const $chatInput = $('#chatInput');
    if (!$chatInput.length) return;

    if (typeof marked !== 'undefined') {
        marked.setOptions({
            breaks: true, // support line breaks
            gfm: true,
            sanitize: false // handled separately / left to the rendering framework
        });
    }

    const $btnSend = $('#btnSend');
    const $chatTimeline = $('#chatTimeline');
    const $mentorSwitch = $('#mentorModeSwitch');

    // Auto-resize textarea
    $chatInput.on('input', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
        if (this.value.trim() === '') {
            this.style.height = '44px';
        }
    });

    // Enter to send, Shift+Enter for a new line
    $chatInput.on('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    $btnSend.on('click', sendMessage);

    // Quick prompt buttons send immediately on click
    $(document).on('click', '.btn-quick-prompt', function () {
        const promptText = $(this).text();
        $chatInput.val(promptText);
        sendMessage();
    });

    function sendMessage() {
        const message = $chatInput.val().trim();
        if (message === '') return;

        const mentorMode = $mentorSwitch.is(':checked');

        appendUserMessage(message);

        $chatInput.val('');
        $chatInput.css('height', '44px');
        scrollToBottom();

        const typingId = showTypingIndicator();

        $.ajax({
            url: 'api/ajax_ai_chat.php',
            type: 'POST',
            data: {
                message: message,
                mentor_mode: mentorMode
            },
            dataType: 'json',
            success: function (response) {
                $('#' + typingId).remove();

                if (response.status === 'success') {
                    appendAiMessage(response.reply);
                } else {
                    appendAiMessage('เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์ กรุณาลองใหม่ครับ');
                }
            },
            error: function () {
                $('#' + typingId).remove();
                appendAiMessage('ไม่สามารถเชื่อมต่อ AI ได้ โปรดตรวจสอบการเชื่อมต่ออินเทอร์เน็ต');
            }
        });
    }

    function appendUserMessage(text) {
        const safeText = $('<div>').text(text).html(); // basic escaping
        const html = `
            <div class="chat-message user-message max-w-chat w-100">
                <div class="message-content shadow-sm">
                    ${safeText.replace(/\n/g, '<br>')}
                </div>
            </div>
        `;
        $chatTimeline.append(html);
    }

    function showTypingIndicator() {
        const id = 'typing-' + Date.now();
        const html = `
            <div class="chat-message ai-message" id="${id}">
                <div class="d-flex gap-3 max-w-chat">
                    <div class="avatar-sm bg-surface border border-color text-primary rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center shadow-sm">
                        <i class="bi bi-robot"></i>
                    </div>
                    <div class="message-content bg-surface border border-color rounded-4 rounded-top-start-0 p-3 shadow-sm d-flex align-items-center">
                        <div class="typing-indicator">
                            <span></span><span></span><span></span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $chatTimeline.append(html);
        scrollToBottom();
        return id;
    }

    function appendAiMessage(markdownText) {
        const htmlContent = marked.parse(markdownText);

        const html = `
            <div class="chat-message ai-message">
                <div class="d-flex gap-3 max-w-chat">
                    <div class="avatar-sm bg-primary text-white rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center shadow-sm">
                        <i class="bi bi-stars"></i>
                    </div>
                    <div class="message-content bg-surface border border-color rounded-4 rounded-top-start-0 p-3 shadow-sm w-100 markdown-body">
                        ${htmlContent}
                    </div>
                </div>
            </div>
        `;

        $chatTimeline.append(html);

        // Render any Mermaid diagrams in the reply
        if (typeof mermaid !== 'undefined') {
            mermaid.init(undefined, $('.markdown-body .language-mermaid'));
            $('.language-mermaid').removeClass('language-mermaid').addClass('mermaid');
            mermaid.init(undefined, $('.mermaid'));
        }

        scrollToBottom();
    }

    function scrollToBottom() {
        $chatTimeline.stop().animate({
            scrollTop: $chatTimeline[0].scrollHeight
        }, 300);
    }
}

/* =========================================
   11. Instructor Course Creation Form Handler
   ========================================= */
function initCourseCreateForm() {
    const $form = $('#form-create-course');
    if (!$form.length) return;

    $form.on('submit', function (e) {
        e.preventDefault();

        // 1. สร้าง FormData จากฟอร์ม
        var formData = new FormData(this);
        
        // 2. ระบุ action ให้ตรงกับที่ Backend รอรับ
        formData.append('action', 'save');

        var $submitBtn = $(this).find('button[type="submit"]');
        $submitBtn.prop('disabled', true);

        $.ajax({
            // ✅ ถอยออกไป 1 โฟลเดอร์เพื่อเข้า services/course.php
            url: '../services/course.php',
            type: 'POST',
            data: formData,
            contentType: false, // จำเป็นสำหรับ FormData
            processData: false, // จำเป็นสำหรับ FormData
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    alert(response.message || 'บันทึกข้อมูลเรียบร้อยแล้ว');
                    window.location.reload();
                } else {
                    alert('เกิดข้อผิดพลาด: ' + (response.message || 'ไม่สามารถสร้างคอร์สได้'));
                }
            },
            error: function (xhr) {
                alert('ไม่สามารถติดต่อ Server ได้ (Status: ' + xhr.status + ')');
            },
            complete: function () {
                $submitBtn.prop('disabled', false);
            }
        });
    });
}

/* =========================================
   12. Auto Custom Dropdown Converter (For Custom Cursors)
   ========================================= */
function initAutoCustomDropdowns() {
    // 1. ค้นหาแท็ก <select> ปกติทุกตัวในหน้าเว็บที่ยังไม่ได้แปลง
    function convertSelects() {
        $('select:not([data-customized="true"])').each(function () {
            const $select = $(this);
            
            // ข้าม select ที่ซ่อนอยู่หรือถูกระบุว่าไม่ต้องแปลง
            if ($select.is(':hidden') || $select.hasClass('no-custom')) return;

            $select.attr('data-customized', 'true').hide(); // ซ่อน <select> ตัวจริงไว้หลังบ้าน

            // สร้าง Custom Wrapper
            const $wrapper = $('<div class="custom-dropdown-wrapper"></div>');
            if ($select.attr('class')) {
                // คัดลอก Class ดั้งเดิมมาใส่เพื่อคงเลย์เอาต์เดิมไว้
                $wrapper.addClass($select.attr('class')).removeClass('form-select lu-select form-select-custom');
            }

            const $selectedOpt = $select.find('option:selected').length ? $select.find('option:selected') : $select.find('option').first();
            const selectedText = $selectedOpt.text().trim() || 'เลือกรายการ';

            // สร้างรายการตัวเลือก HTML ย่อย
            let menuHtml = '';
            $select.find('option').each(function () {
                const $opt = $(this);
                const val = $opt.val();
                const text = $opt.text().trim();
                const isSelected = $opt.is(':selected') ? 'selected' : '';
                const isDisabled = $opt.is(':disabled') ? 'style="opacity:0.5; pointer-events:none;"' : '';

                menuHtml += `<div class="custom-dropdown-item ${isSelected}" data-value="${val}" ${isDisabled}>${text}</div>`;
            });

            $wrapper.html(`
                <div class="custom-dropdown-btn">
                    <span class="selected-text">${selectedText}</span>
                    <i class="bi bi-chevron-down"></i>
                </div>
                <div class="custom-dropdown-menu">
                    ${menuHtml}
                </div>
            `);

            $select.after($wrapper);

            // เมื่อคลิกเลือกไอเทมย่อย
            $wrapper.on('click', '.custom-dropdown-item', function (e) {
                e.stopPropagation();
                const $item = $(this);
                const val = $item.data('value');

                // ส่งค่ากลับไปหา <select> ดั้งเดิมและแจ้ง Event Change
                $select.val(val).trigger('change');

                // อัปเดตการแสดงผลฝั่ง Custom UI
                $wrapper.find('.selected-text').text($item.text().trim());
                $wrapper.find('.custom-dropdown-item').removeClass('selected');
                $item.addClass('selected');
                $wrapper.removeClass('active');
            });

            // เมื่อคลิกปุ่มเปิด-ปิด Dropdown
            $wrapper.on('click', '.custom-dropdown-btn', function (e) {
                e.stopPropagation();
                const isOpen = $wrapper.hasClass('active');
                $('.custom-dropdown-wrapper.active').not($wrapper).removeClass('active');
                $wrapper.toggleClass('active', !isOpen);
            });
        });
    }

    // เรียกฟังก์ชันแปลง Select ทันทีเมื่อสคริปต์ทำงาน
    convertSelects();

    // คลิกพื้นที่อื่นนอก Dropdown เพื่อปิดรายการ
    $(document).on('click', function () {
        $('.custom-dropdown-wrapper.active').removeClass('active');
    });
}

/* =========================================
   Global helper (called from inline HTML, e.g. a "New Chat" button)
   ========================================= */
function startNewChat() {
    $('#chatTimeline').html('<div class="text-center text-muted my-5"><i class="bi bi-chat-square-dots fs-1"></i><p class="mt-2">เริ่มการสนทนาใหม่...</p></div>');
}

// script.js - LevelUp E-Learning JS
const CourseExplorer = (function($) {
    
    let compareList = [];
    
    // ตั้งค่าเริ่มต้น
    const init = () => {
        // โหลดคอร์สครั้งแรก
        loadCourses();
        
        // ผูก Event Listener
        $('.filter-checkbox').on('change', debounce(loadCourses, 300));
        $('#sort-select').on('change', loadCourses);
        
        // Enter เพื่อค้นหา AI
        $('#ai-search-input').on('keypress', function(e) {
            if(e.which == 13) { aiSearch(); }
        });
    };

    // ฟังก์ชันหน่วงเวลาลดการยิง Request ซ้ำซ้อน
    const debounce = (func, delay) => {
        let timer;
        return function() {
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(this, arguments), delay);
        };
    };

    // ฟังก์ชันโหลดข้อมูลผ่าน AJAX (Filter & Sort)
    const loadCourses = () => {
        const grid = $('#course-grid');
        grid.html('<div class="col-12 text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');
        
        // เก็บค่าตัวกรอง
        let categories = [];
        $('input[data-filter="categories"]:checked').each(function() { categories.push($(this).val()); });
        
        let levels = [];
        $('input[data-filter="levels"]:checked').each(function() { levels.push($(this).val()); });
        
        let sort = $('#sort-select').val();

        $.ajax({
            url: 'ajax_courses.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'load_courses',
                categories: categories,
                levels: levels,
                sort: sort
            },
            success: function(res) {
                if(res.status === 'success') {
                    grid.html(res.html);
                    $('#course-count').text(res.count);
                    $('#ai-recommendation-alert').addClass('d-none');
                }
            },
            error: function() {
                grid.html('<div class="col-12 text-center text-danger py-5">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>');
            }
        });
    };

    // AI Natural Language Search
    const aiSearch = () => {
        const query = $('#ai-search-input').val().trim();
        if(!query) return;

        const grid = $('#course-grid');
        grid.html('<div class="col-12 text-center py-5"><i class="fas fa-robot fa-spin fa-3x text-primary mb-3"></i><p>AI กำลังวิเคราะห์และจัดหลักสูตรให้คุณ...</p></div>');
        
        // ล้างตัวกรองเดิมออกก่อน
        $('.filter-checkbox').prop('checked', false);

        $.ajax({
            url: 'ajax_courses.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'ai_search',
                query: query
            },
            success: function(res) {
                if(res.status === 'success') {
                    grid.html(res.html);
                    $('#course-count').text(res.count);
                    
                    // แสดง AI Alert
                    $('#ai-recommendation-msg').html(res.ai_message);
                    $('#ai-recommendation-alert').removeClass('d-none').addClass('d-flex');
                    
                    // เลื่อนหน้าจอลงมาที่ผลลัพธ์
                    $('html, body').animate({ scrollTop: $('#course-grid').offset().top - 150 }, 500);
                }
            }
        });
    };

    // ล้างตัวกรองทั้งหมด
    const resetFilters = () => {
        $('.filter-checkbox').prop('checked', false);
        $('#sort-select').val('popular');
        $('#ai-search-input').val('');
        loadCourses();
    };

    // Bookmark Toggle (จำลองการทำงาน)
    const toggleBookmark = (btn, id) => {
        const icon = $(btn).find('i');
        $(btn).toggleClass('active');
        
        if($(btn).hasClass('active')) {
            icon.removeClass('far').addClass('fas');
            // TODO: $.post('toggle-bookmark.php', {id: id, state: 1})
            showToast('บันทึกคอร์สใน Wishlist แล้ว');
        } else {
            icon.removeClass('fas').addClass('far');
            showToast('นำคอร์สออกจาก Wishlist แล้ว');
        }
    };

    // จัดการ Compare
    const toggleCompare = (btn, id, title) => {
        const index = compareList.findIndex(c => c.id === id);
        
        if (index > -1) {
            compareList.splice(index, 1);
            $(btn).removeClass('btn-primary text-white').addClass('btn-outline-secondary');
        } else {
            if (compareList.length >= 4) {
                showToast('คุณสามารถเปรียบเทียบได้สูงสุด 4 คอร์ส', 'warning');
                return;
            }
            compareList.push({ id: id, title: title });
            $(btn).removeClass('btn-outline-secondary').addClass('btn-primary text-white');
        }
        updateCompareTray();
    };

    const updateCompareTray = () => {
        const tray = $('#compare-tray');
        const list = $('#compare-list');
        
        $('#compare-count').text(compareList.length);
        list.empty();
        
        if(compareList.length > 0) {
            tray.addClass('show');
            compareList.forEach(course => {
                list.append(`
                    <li>
                        <span class="text-truncate" style="max-width: 230px;">${course.title}</span>
                        <button class="btn btn-link text-danger p-0 ms-2" onclick="CourseExplorer.toggleCompare($('.btn-compare[data-id=${course.id}]'), ${course.id}, '${course.title}')">
                            <i class="fas fa-times"></i>
                        </button>
                    </li>
                `);
            });
        } else {
            tray.removeClass('show');
        }
    };

    const closeCompareTray = () => {
        $('#compare-tray').removeClass('show');
    };

    const openCompareModal = () => {
        if(compareList.length < 2) {
            showToast('กรุณาเลือกอย่างน้อย 2 คอร์สเพื่อเปรียบเทียบ', 'warning');
            return;
        }
        
        // Mockup ตารางเปรียบเทียบ
        let th = '<th>คุณสมบัติ</th>';
        let tdLevel = '<td>ระดับ</td>';
        let tdPrice = '<td>ราคา</td>';
        
        compareList.forEach(c => {
            th += `<th>${c.title}</th>`;
            tdLevel += `<td><span class="badge bg-primary">ระดับของคุณ</span></td>`;
            tdPrice += `<td>฿XXX</td>`;
        });
        
        $('#compare-table').html(`
            <thead><tr class="table-light">${th}</tr></thead>
            <tbody>
                <tr>${tdLevel}</tr>
                <tr>${tdPrice}</tr>
            </tbody>
        `);
        
        const modal = new bootstrap.Modal(document.getElementById('compareModal'));
        modal.show();
    };

    // UI Helper: Toast Notification
    const showToast = (message, type = 'success') => {
        // ใช้ Toast ของ Bootstrap หรือสามารถใส่ Custom HTML ไว้ที่มุมขวาล่าง
        alert(message); // สำหรับ Demo ให้ใช้ Alert แทน แต่ใน Production ควรใช้ Bootstrap Toast
    };

    return {
        init,
        aiSearch,
        resetFilters,
        toggleBookmark,
        toggleCompare,
        closeCompareTray,
        openCompareModal
    };

})(jQuery);

// เริ่มการทำงานเมื่อ DOM พร้อม
$(document).ready(function() {
    CourseExplorer.init();
});

$(document).ready(function() {
    
    // ==========================================
    // 10. Coding Lab & Monaco Editor Logic
    // ==========================================
    if ($('#monaco-editor').length) {
        
        let editor; // ตัวแปรเก็บ Instance ของ Monaco
        
        // Initial Code Template
        const defaultCode = `def twoSum(nums, target):\n    # Write your code here\n    for i in range(len(nums)):\n        for j in range(i + 1, len(nums)):\n            if nums[i] + nums[j] == target:\n                return [i, j]\n\n    return []`;

        // 1. Initialize Monaco (ต้องใช้ require syntax ของ AMD)
        require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.38.0/min/vs' }});
        require(['vs/editor/editor.main'], function() {
            $('#editorLoading').remove();
            
            // เช็ค Theme ปัจจุบันของเว็บ
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            
            editor = monaco.editor.create(document.getElementById('monaco-editor'), {
                value: defaultCode,
                language: 'python',
                theme: isDark ? 'vs-dark' : 'vs-light',
                automaticLayout: true,
                minimap: { enabled: true },
                fontSize: 14,
                fontFamily: "'Fira Code', 'Consolas', monospace",
                lineHeight: 24,
                roundedSelection: false,
                scrollBeyondLastLine: false,
                cursorBlinking: "smooth"
            });

            // Listen Theme Change to update Editor Theme
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === "data-theme") {
                        const currentTheme = document.documentElement.getAttribute('data-theme');
                        monaco.editor.setTheme(currentTheme === 'dark' ? 'vs-dark' : 'vs-light');
                    }
                });
            });
            observer.observe(document.documentElement, { attributes: true });

            // Auto-Save Simulator
            editor.onDidChangeModelContent(() => {
                $('#saveStatus').removeClass('bi-cloud-check text-success').addClass('bi-cloud-arrow-up text-warning');
                clearTimeout(window.saveTimer);
                window.saveTimer = setTimeout(() => {
                    // จำลองว่าเซฟแล้ว
                    $('#saveStatus').removeClass('bi-cloud-arrow-up text-warning').addClass('bi-cloud-check text-success');
                }, 1500);
            });
        });

        // 2. Language Change
        $('#languageSelect').on('change', function() {
            const lang = $(this).val();
            if(editor) monaco.editor.setModelLanguage(editor.getModel(), lang);
        });

        // 3. Panel Toggles
        $('#toggleLeftPanel').on('click', () => $('#leftPanel').toggleClass('collapsed'));
        $('#closeAiPanel').on('click', () => $('#rightPanel').addClass('collapsed'));
        $('#toggleAiPanel').on('click', () => $('#rightPanel').toggleClass('collapsed'));

        // 4. Run Code Button
        $('#btnRun').on('click', function() {
            const code = editor.getValue();
            const btn = $(this);
            const originalHtml = btn.html();
            
            btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Running...').prop('disabled', true);
            $('[data-bs-target="#tab-terminal"]').tab('show'); // สลับไปหน้า Console
            $('#consoleOutput').html('Compiling & Running...\n<span class="spinner-grow spinner-grow-sm text-success" role="status"></span>');

            $.ajax({
                url: 'api/ajax_coding_lab.php', type: 'POST',
                data: { action: 'run', code: code, language: $('#languageSelect').val() },
                success: (res) => { $('#consoleOutput').html(res.console); },
                complete: () => { btn.html(originalHtml).prop('disabled', false); }
            });
        });

        // 5. Submit Button (Test Cases)
        $('#btnSubmit').on('click', function() {
            const code = editor.getValue();
            const btn = $(this);
            const originalHtml = btn.html();
            
            btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Testing...').prop('disabled', true);
            $('[data-bs-target="#tab-tests"]').tab('show');
            $('#testResults').html('<div class="text-center mt-4"><div class="spinner-border text-primary"></div><div class="mt-2 text-muted">Running Test Cases...</div></div>');

            $.ajax({
                url: 'api/ajax_coding_lab.php', type: 'POST',
                data: { action: 'submit', code: code },
                success: (res) => { $('#testResults').html(res.test_html); },
                complete: () => { btn.html(originalHtml).prop('disabled', false); }
            });
        });

        // 6. AI Actions (Review, Explain, Optimize)
        $('.ai-action-btn').on('click', function() {
            const action = $(this).data('action'); // review หรือ explain
            const code = editor.getValue();
            const $aiArea = $('#aiFeedbackArea');
            
            $('#rightPanel').removeClass('collapsed'); // เปิด Panel
            $aiArea.html('<div class="text-center mt-4"><div class="spinner-border text-primary mb-2"></div><div>AI is analyzing...</div></div>');

            $.ajax({
                url: 'api/ajax_coding_lab.php', type: 'POST',
                data: { action: 'ai_' + action, code: code },
                success: (res) => {
                    // ใช้ Marked.js Render Markdown
                    $aiArea.html(`<div class="markdown-body fs-sm">${marked.parse(res.markdown)}</div>`);
                }
            });
        });

        // 7. AI Hint Request
        let hintLevel = 1;
        $('#btnRequestHint').on('click', function() {
            const btn = $(this);
            btn.html('<span class="spinner-border spinner-border-sm"></span> Analyzing...');
            
            $.ajax({
                url: 'api/ajax_coding_lab.php', type: 'POST',
                data: { action: 'ai_hint', level: hintLevel },
                success: (res) => {
                    $('#hintContainer').removeClass('d-none').html(res.html);
                    hintLevel++;
                    if (hintLevel > 2) {
                        btn.hide(); // ซ่อนปุ่มถ้าให้ Hint ครบแล้ว
                    } else {
                        btn.html('ขอคำใบ้ระดับ ' + hintLevel + ' (โครงสร้าง Loop)');
                    }
                }
            });
        });

    }
});

/**
 * Global Scripts - LevelUp
 */
document.addEventListener('DOMContentLoaded', () => {
    // Theme Initialization
    const initTheme = () => {
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
        updateThemeUI(savedTheme);
    };

    const updateThemeUI = (theme) => {
        document.querySelectorAll('.theme-toggle').forEach(el => {
            el.classList.remove('active', 'text-primary');
            if (el.dataset.themeValue === theme) {
                el.classList.add('active', 'text-primary');
            }
        });
    };

    // Theme Switcher Event
    document.querySelectorAll('.theme-toggle').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const theme = e.currentTarget.dataset.themeValue;
            
            if (theme === 'auto') {
                const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                document.documentElement.setAttribute('data-theme', systemTheme);
                localStorage.removeItem('theme');
            } else {
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
            }
            updateThemeUI(theme);
        });
    });

    // Run on load
    initTheme();
});

// ==========================================
// Custom Dropdown Event Listener (Rocket Cursor Friendly)
// ==========================================
document.addEventListener('DOMContentLoaded', function () {
    
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.custom-dropdown-btn');
        const item = e.target.closest('.custom-dropdown-item');

        // กรณีที่ 1: คลิกเปิด-ปิด ปุ่ม Dropdown
        if (btn) {
            e.stopPropagation();
            const wrapper = btn.closest('.custom-dropdown-wrapper');
            const isOpen = wrapper.classList.contains('active');

            // ปิด Dropdown ตัวอื่นที่เปิดค้างอยู่
            document.querySelectorAll('.custom-dropdown-wrapper.active').forEach(w => {
                if (w !== wrapper) w.classList.remove('active');
            });

            wrapper.classList.toggle('active', !isOpen);
            return;
        }

        // กรณีที่ 2: คลิกเลือกตัวเลือกใน Dropdown
        if (item) {
            const wrapper = item.closest('.custom-dropdown-wrapper');
            if (wrapper) {
                const textSpan = wrapper.querySelector('.selected-text');
                const hiddenInput = wrapper.querySelector('input[type="hidden"]');
                const val = item.getAttribute('data-value') || item.textContent.trim();

                if (textSpan) textSpan.textContent = item.textContent.trim();
                if (hiddenInput) hiddenInput.value = val;

                wrapper.querySelectorAll('.custom-dropdown-item').forEach(i => i.classList.remove('selected'));
                item.classList.add('selected');

                wrapper.classList.remove('active');
            }
            return;
        }

        // กรณีที่ 3: คลิกพื้นที่ภายนอก ให้ปิด Dropdown ทั้งหมด
        document.querySelectorAll('.custom-dropdown-wrapper.active').forEach(w => {
            w.classList.remove('active');
        });
    });

});