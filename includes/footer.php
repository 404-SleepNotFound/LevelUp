<?php
/**
 * LevelUp - Next-Gen E-Learning Platform
 * @page includes/footer.php (Combined Footer & Rocket Custom Cursor)
 */

// Mock Admin Status 
$isAdmin = false;
$appVersion = "1.0.0 (Build 2026.07)";
?>

    </main> <!-- ปิดแท็ก <main> ที่เปิดไว้ใน header.php -->

    <!-- =========================================
         Global Floating Elements
         ========================================= -->
    <div class="global-floating-actions d-flex flex-column align-items-end gap-3 position-fixed bottom-0 end-0 p-4" style="z-index: 1040;">
        <!-- Back to Top Button -->
        <button class="btn ft-card-theme rounded-circle shadow-lg d-none align-items-center justify-content-center btn-back-to-top transition-all" id="backToTopBtn" style="width: 45px; height: 45px;">
            <i class="bi bi-arrow-up fs-5 ft-text-main"></i>
        </button>

        <!-- Ask AI Shortcut -->
        <a class="btn text-white rounded-pill shadow-lg px-4 py-2 d-flex align-items-center gap-2 fw-bold ajax-link text-decoration-none" style="background: linear-gradient(135deg, #4F46E5, #6366F1); border: none;" href="ai-chat.php">
            <i class="bi bi-robot fs-5"></i> Ask AI
        </a>
    </div>

    <!-- =========================================
         Footer Section
         ========================================= -->
    <footer class="footer-wrapper pt-5 pb-4 mt-5">
        <div class="container-fluid px-4 px-md-5">
            
            <!-- 1. AI Tip & Statistics Section -->
            <div class="row mb-5 g-4 pb-5 ft-border-bottom">
                <div class="col-lg-4">
                    <div class="card h-100 p-4 shadow-sm ft-card-theme" style="border-radius: 20px;">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-2 d-flex align-items-center gap-2" style="color: #6366F1; font-family: 'Outfit', sans-serif;"><i class="bi bi-stars"></i> Tip of the Day</h6>
                                <p class="ft-text-muted fs-sm mb-0 lh-base" id="dailyAiTip">ระบบเรียนรู้ที่มี AI ช่วยโค้ช จะช่วยเพิ่มความเร็วในการทำเควสต์เขียนโค้ดได้ถึง 2 เท่า!</p>
                            </div>
                            <div class="flex-shrink-0 text-warning opacity-50 pt-1">
                                <i class="bi bi-lightbulb-fill fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="row g-3 h-100 align-items-center text-center text-md-start">
                        <div class="col-6 col-md-3">
                            <h3 class="fw-bold ft-text-main mb-1" style="font-family: 'Outfit', sans-serif;">250K+</h3>
                            <p class="ft-text-muted fs-xs text-uppercase tracking-wide mb-0">Learners</p>
                        </div>
                        <div class="col-6 col-md-3">
                            <h3 class="fw-bold ft-text-main mb-1" style="font-family: 'Outfit', sans-serif;">1,200+</h3>
                            <p class="ft-text-muted fs-xs text-uppercase tracking-wide mb-0">Quests</p>
                        </div>
                        <div class="col-6 col-md-3">
                            <h3 class="fw-bold ft-text-main mb-1" style="font-family: 'Outfit', sans-serif;">850+</h3>
                            <p class="ft-text-muted fs-xs text-uppercase tracking-wide mb-0">Coding Labs</p>
                        </div>
                        <div class="col-6 col-md-3">
                            <h3 class="fw-bold mb-1" style="color: #6366F1; font-family: 'Outfit', sans-serif;">12M+</h3>
                            <p class="ft-text-muted fs-xs text-uppercase tracking-wide mb-0">AI Answers</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Main Footer Grid -->
            <div class="row g-4 mb-5">
                <!-- Brand & Newsletter -->
                <div class="col-lg-3 pe-lg-4">
                    <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none mb-3 ajax-link" href="index.php">
                        <div class="lu-logo-icon fs-5 text-info"><i class="bi bi-rocket-takeoff-fill"></i></div>
                        <span class="fw-bold fs-4 ft-text-main" style="font-family: 'Outfit', sans-serif;">LevelUp</span>
                    </a>
                    <p class="ft-text-muted fs-sm mb-4">Learn Smarter with AI. Build Real Skills. The next-generation learning platform for tech professionals.</p>
                    
                    <h6 class="ft-text-main fs-sm fw-bold mb-3" style="font-family: 'Outfit', sans-serif;">Subscribe to Newsletter</h6>
                    
                    <!-- 🎯 ฟอร์ม Newsletter -->
                    <form id="newsletterForm" class="d-flex gap-2 mb-4 align-items-stretch">
                        <input type="email" name="email" class="form-control ft-card-theme ft-text-main shadow-none placeholder-glow-custom" style="border-radius: 12px; padding: 10px 16px; font-size: 0.9rem;" placeholder="Enter your email" required>
                        <button type="submit" class="btn text-white rounded-3 px-4 fw-bold" style="background: linear-gradient(135deg, #4F46E5, #6366F1); border: none; border-radius: 12px !important; font-size: 0.9rem; white-space: nowrap;" id="btnNewsletter">Subscribe</button>
                    </form>
                    
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-sm rounded-3 d-flex align-items-center gap-2 px-3 ft-card-theme ft-text-main text-decoration-none"><i class="bi bi-apple fs-5"></i> App Store</a>
                        <a href="#" class="btn btn-sm rounded-3 d-flex align-items-center gap-2 px-3 ft-card-theme ft-text-main text-decoration-none"><i class="bi bi-google-play fs-6"></i> Google Play</a>
                    </div>
                </div>

                <!-- Link Columns -->
                <div class="col-6 col-md-3 col-lg-2">
                    <h6 class="ft-text-main fs-sm fw-bold text-uppercase tracking-wide mb-4" style="font-family: 'Outfit', sans-serif;">Platform</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 fs-sm">
                        <li><a href="index.php" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">Home</a></li>
                        <li><a href="courses.php" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">Quests</a></li>
                        <li><a href="learning-path.php" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">Roadmaps</a></li>
                        <li><a href="coding-lab.php" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">Coding Lab</a></li>
                        <li><a href="virtual-lab.php" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">Virtual Lab</a></li>
                        <li><a href="ai-chat.php" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">AI Tutor</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <h6 class="ft-text-main fs-sm fw-bold text-uppercase tracking-wide mb-4" style="font-family: 'Outfit', sans-serif;">Categories</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 fs-sm">
                        <li><a href="courses.php?category=Programming" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">Programming</a></li>
                        <li><a href="courses.php?category=Web+Dev" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">Web Development</a></li>
                        <li><a href="courses.php?category=AI" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">AI & Machine Learning</a></li>
                        <li><a href="#" class="ft-text-muted text-decoration-none hover-text-primary">Data Science</a></li>
                        <li><a href="#" class="ft-text-muted text-decoration-none hover-text-primary">Cloud Computing</a></li>
                        <li><a href="#" class="ft-text-muted text-decoration-none hover-text-primary">Cybersecurity</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <h6 class="ft-text-main fs-sm fw-bold text-uppercase tracking-wide mb-4" style="font-family: 'Outfit', sans-serif;">Career Paths</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 fs-sm">
                        <li><a href="learning-path.php?path=frontend" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">Front-end Developer</a></li>
                        <li><a href="learning-path.php?path=backend" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">Back-end Developer</a></li>
                        <li><a href="#" class="ft-text-muted text-decoration-none hover-text-primary">Data Scientist</a></li>
                        <li><a href="#" class="ft-text-muted text-decoration-none hover-text-primary">AI Engineer</a></li>
                        <li><a href="#" class="ft-text-muted text-decoration-none hover-text-primary">DevOps Engineer</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-3 col-lg-3">
                    <h6 class="ft-text-main fs-sm fw-bold text-uppercase tracking-wide mb-4" style="font-family: 'Outfit', sans-serif;">Resources & Support</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 fs-sm mb-4">
                        <li><a href="#" class="ft-text-muted text-decoration-none hover-text-primary">Documentation</a></li>
                        <li><a href="#" class="ft-text-muted text-decoration-none hover-text-primary">Community Forum</a></li>
                        <li><a href="#" class="ft-text-muted text-decoration-none hover-text-primary">Help Center & FAQ</a></li>
                        <li><a href="#" class="text-decoration-none" style="color: #10B981;" data-bs-toggle="modal" data-bs-target="#feedbackModal">Give Feedback / Report Bug</a></li>
                    </ul>
                    
                    <h6 class="ft-text-main fs-sm fw-bold text-uppercase tracking-wide mb-3" style="font-family: 'Outfit', sans-serif;">Connect</h6>
                    <div class="d-flex gap-3">
                        <a href="#" class="ft-text-muted fs-5 hover-text-primary"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="ft-text-muted fs-5 hover-text-primary"><i class="bi bi-github"></i></a>
                        <a href="#" class="ft-text-muted fs-5 hover-text-primary"><i class="bi bi-linkedin"></i></a>
                        <a href="#" class="ft-text-muted fs-5 hover-text-primary"><i class="bi bi-discord"></i></a>
                        <a href="#" class="ft-text-muted fs-5 hover-text-primary"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
            </div>

            <hr class="ft-hr mb-4">

            <!-- 3. Bottom Bar -->
            <div class="row align-items-center g-3 fs-xs ft-text-muted">
                <div class="col-md-4 text-center text-md-start">
                    <span class="me-3">© 2026 LevelUp. All Rights Reserved.</span>
                    <br class="d-md-none">
                    <a href="#" class="ft-text-muted text-decoration-none me-3">Privacy</a>
                    <a href="#" class="ft-text-muted text-decoration-none me-3">Terms</a>
                    <a href="#" class="ft-text-muted text-decoration-none">Security</a>
                </div>
                
                <div class="col-md-4 text-center">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill ft-card-theme" title="View Status Page" style="cursor: pointer;">
                        <span class="p-1 rounded-circle" style="background-color: #10B981;"></span>
                        <span class="fw-medium ft-text-main">All Systems Operational</span>
                    </div>
                </div>

                <div class="col-md-4 d-flex justify-content-center justify-content-md-end gap-3 align-items-center">
                    <div class="d-flex gap-2">
                        <span class="badge ft-card-theme ft-text-main px-2 py-1"><i class="bi bi-globe me-1"></i> EN</span>
                        <span class="badge ft-card-theme ft-text-main px-2 py-1"><i class="bi bi-moon-stars me-1"></i> Dark/Light</span>
                    </div>
                    <span class="ms-2">v<?php echo $appVersion; ?></span>
                </div>
            </div>

            <!-- 4. Admin Server Info -->
            <?php if($isAdmin): ?>
            <div class="mt-4 p-2 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-3 text-danger fs-xs font-monospace text-center">
                [ADMIN DEBUG] Server: Node-BKK-01 | DB: Connected (4ms) | AI API: Alive | Mem: 42%
            </div>
            <?php endif; ?>

        </div>
    </footer>

    <!-- =========================================
         Feedback Modal
         ========================================= -->
    <div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content ft-modal-theme rounded-4 shadow-lg">
                <div class="modal-header ft-border-bottom">
                    <h5 class="modal-title fw-bold ft-text-main" style="font-family: 'Outfit', sans-serif;"><i class="bi bi-chat-right-text me-2" style="color: #6366F1;"></i> Send Feedback</h5>
                    <button type="button" class="btn-close ft-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="feedbackForm">
                        <div class="mb-3">
                            <label class="form-label fs-sm ft-text-muted">Feedback Type</label>
                            <select name="type" class="form-select ft-modal-input ft-text-main shadow-none">
                                <option value="suggestion">💡 Feature Suggestion</option>
                                <option value="bug">🐛 Report a Bug</option>
                                <option value="other">💬 General Feedback</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fs-sm ft-text-muted">Message</label>
                            <textarea name="message" class="form-control ft-modal-input ft-text-main shadow-none" rows="4" placeholder="Tell us what you think..." required></textarea>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-outline-secondary rounded-pill me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background: linear-gradient(135deg, #4F46E5, #6366F1); border: none;" id="btnSubmitFeedback">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts หลักของระบบ -->
    <script src="assets/js/footer.js"></script>
    <script src="assets/js/script.js"></script>

    <!-- ===================================================
         AJAX NAVIGATION ENGINE WITH FAILSAFE FALLBACK
         =================================================== -->
    <script>
    $(document).ready(function() {
        $(document).on('click', '.ajax-link', function(e) {
            var url = $(this).attr('href');
            if (url && url !== '#' && !url.startsWith('javascript:') && !url.startsWith('#')) {
                if ($('#content-area').length > 0) {
                    e.preventDefault();
                    loadPage(url);
                }
            }
        });

        function loadPage(url) {
            $('#content-area').animate({ opacity: 0.4 }, 120, function() {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        var $parsedResponse = $(response);
                        var newContent = $parsedResponse.filter('#content-area').html() || $parsedResponse.find('#content-area').html();
                        if (newContent) {
                            $('#content-area').html(newContent).animate({ opacity: 1 }, 120);
                            history.pushState({ url: url }, '', url);
                            window.scrollTo({ top: 0, behavior: 'instant' });
                        } else {
                            window.location.href = url;
                        }
                    },
                    error: function() {
                        window.location.href = url;
                    }
                });
            });
        }

        window.onpopstate = function(event) {
            if (event.state && event.state.url && $('#content-area').length > 0) {
                loadPage(event.state.url);
            } else {
                location.reload();
            }
        };

        var btn = $('#backToTopBtn');
        $(window).scroll(function() {
            if ($(window).scrollTop() > 300) {
                btn.removeClass('d-none').addClass('d-flex');
            } else {
                btn.addClass('d-none').removeClass('d-flex');
            }
        });
        btn.on('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
    </script>

    <!-- ===================================================
         🚀 ROCKET CUSTOM CURSOR ENGINE (SINGLETON & AJAX-SAFE)
         =================================================== -->
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
            if (el && mouseX >= 0 && mouseY >= 0) {
                el.style.transform = `translate3d(${mouseX - 12}px, ${mouseY - 10}px, 0) scaleX(-1)`;
                el.classList.add('is-visible');
            }
            isScheduled = false;
        }

        window.addEventListener('mousemove', function(e) {
            mouseX = e.clientX;
            mouseY = e.clientY;
            
            sessionStorage.setItem('rocket_x', mouseX);
            sessionStorage.setItem('rocket_y', mouseY);

            if (!isScheduled) {
                isScheduled = true;
                requestAnimationFrame(render);
            }
        }, { passive: true });

        document.addEventListener('mouseleave', function() {
            const el = document.getElementById('custom-rocket-cursor');
            if (el) el.classList.remove('is-visible');
        });

        if (mouseX >= 0 && mouseY >= 0) {
            requestAnimationFrame(render);
        }
    })();
    </script>

    <!-- ===================================================
         🎨 STYLES: CSS Variables & Custom Rocket Cursor
         =================================================== -->
    <style>
        /* 🚀 1. ซ่อนเมาส์ระบบแบบเด็ดขาดทุกสถานะ ป้องกันเมาส์จริงซ้อน */
        html, body, *, *::before, *::after,
        a, a:hover, a:active, a:focus,
        button, button:hover, button:active,
        input, select, textarea, [role="button"], 
        .btn, .nav-link, .dropdown-item, [onclick] {
            cursor: none !important;
        }

        /* 🚀 2. สไตล์ตัวจรวด 🚀 ใช้ GPU (translate3d) ในการประมวลผล */
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
            opacity: 0; /* ซ่อนไว้ก่อนตั้งแต่แรกเกิด ป้องกันการวาร์ปไปมุมซ้ายบน */
        }

        #custom-rocket-cursor.is-visible {
            opacity: 1;
        }

        /* 🎨 3. CSS Variables สำหรับ Light/Dark Mode ใน Footer */
        :root, html[data-theme="dark"] {
            --ft-bg: #0b0f19;
            --ft-card-bg: #0f172a;
            --ft-border: #1e293b;
            --ft-text-main: #ffffff;
            --ft-text-muted: #94a3b8;
            --ft-placeholder: rgba(255, 255, 255, 0.6);
            --ft-modal-bg: #0f172a;
            --ft-modal-input: #0b0f19;
            --ft-close-filter: invert(1) grayscale(100%) brightness(200%);
        }

        html[data-theme="light"] {
            --ft-bg: #f8fafc;
            --ft-card-bg: #ffffff;
            --ft-border: #e2e8f0;
            --ft-text-main: #0f172a;
            --ft-text-muted: #64748b;
            --ft-placeholder: #94a3b8;
            --ft-modal-bg: #ffffff;
            --ft-modal-input: #f1f5f9;
            --ft-close-filter: none;
        }

        .footer-wrapper {
            background-color: var(--ft-bg) !important;
            border-top: 1px solid var(--ft-border) !important;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .ft-card-theme {
            background-color: var(--ft-card-bg) !important;
            border: 1px solid var(--ft-border) !important;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .ft-modal-theme {
            background-color: var(--ft-modal-bg) !important;
            border: 1px solid var(--ft-border) !important;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .ft-modal-input {
            background-color: var(--ft-modal-input) !important;
            border: 1px solid var(--ft-border) !important;
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        .ft-text-main {
            color: var(--ft-text-main) !important;
            transition: color 0.3s ease;
        }

        .ft-text-muted {
            color: var(--ft-text-muted) !important;
            transition: color 0.3s ease;
        }

        .ft-border-bottom {
            border-bottom: 1px solid var(--ft-border) !important;
            transition: border-color 0.3s ease;
        }

        .ft-hr {
            border-color: var(--ft-border) !important;
            opacity: 1 !important;
            transition: border-color 0.3s ease;
        }

        .ft-btn-close {
            filter: var(--ft-close-filter);
        }

        .hover-text-primary:hover {
            color: #6366F1 !important;
            opacity: 1 !important;
            transition: all 0.2s ease-in-out;
        }

        .placeholder-glow-custom::placeholder {
            color: var(--ft-placeholder) !important;
            opacity: 1 !important;
        }
    </style>
</body>
</html>