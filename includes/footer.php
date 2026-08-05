<?php
/**
 * LevelUp - Next-Gen E-Learning Platform
 * @page includes/footer.php (Combined Footer & Rocket Custom Cursor Integration)
 */

$isAdmin = $isAdmin ?? false;
$appVersion = $appVersion ?? "1.0.0 (Build 2026.08)";
$ROOT_PATH = $ROOT_PATH ?? '';
?>

    </main> <!-- Closing <main> tag opened in header.php -->

    <!-- =========================================
         Global Floating Elements
         ========================================= -->
    <div class="global-floating-actions d-flex flex-column align-items-end gap-3 position-fixed bottom-0 end-0 p-4" style="z-index: 1040;">
        <!-- Back to Top Button -->
        <button class="btn ft-card-theme rounded-circle shadow-lg d-none align-items-center justify-content-center btn-back-to-top transition-all" id="backToTopBtn" style="width: 45px; height: 45px;" aria-label="Back to Top">
            <i class="bi bi-arrow-up fs-5 ft-text-main"></i>
        </button>
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
                                <h6 class="fw-bold mb-2 d-flex align-items-center gap-2" style="color: #6366F1; font-family: 'Outfit', sans-serif;">
                                    <i class="bi bi-stars"></i> Tip of the Day
                                </h6>
                                <p class="ft-text-muted fs-sm mb-0 lh-base" id="dailyAiTip">
                                    ระบบเรียนรู้ที่มี AI ช่วยโค้ช จะช่วยเพิ่มความเร็วในการทำเควสต์เขียนโค้ดได้ถึง 2 เท่า!
                                </p>
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
                    <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none mb-3 ajax-link" href="<?= $ROOT_PATH ?>index.php">
                        <div class="lu-logo-icon fs-5 text-info"><i class="bi bi-rocket-takeoff-fill"></i></div>
                        <span class="fw-bold fs-4 ft-text-main" style="font-family: 'Outfit', sans-serif;">LevelUp</span>
                    </a>
                    <p class="ft-text-muted fs-sm mb-4">Learn Smarter with AI. Build Real Skills. The next-generation learning platform for tech professionals.</p>
                    
                    <h6 class="ft-text-main fs-sm fw-bold mb-3" style="font-family: 'Outfit', sans-serif;">Subscribe to Newsletter</h6>
                    
                    <!-- Newsletter Form -->
                    <form id="newsletterForm" class="d-flex gap-2 align-items-stretch">
                        <input type="email" name="email" class="form-control ft-card-theme ft-text-main shadow-none placeholder-glow-custom" style="border-radius: 12px; padding: 10px 16px; font-size: 0.9rem;" placeholder="Enter your email" required>
                        <button type="submit" class="btn text-white rounded-3 px-4 fw-bold" style="background: linear-gradient(135deg, #4F46E5, #6366F1); border: none; border-radius: 12px !important; font-size: 0.9rem; white-space: nowrap;" id="btnNewsletter">Subscribe</button>
                    </form>
                </div>

                <!-- Link Columns -->
                <div class="col-6 col-md-3 col-lg-2">
                    <h6 class="ft-text-main fs-sm fw-bold text-uppercase tracking-wide mb-4" style="font-family: 'Outfit', sans-serif;">Platform</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 fs-sm">
                        <li><a href="<?= $ROOT_PATH ?>index.php" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">Home</a></li>
                        <li><a href="<?= $ROOT_PATH ?>student/courses.php" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">Quests</a></li>
                        <li><a href="<?= $ROOT_PATH ?>student/learning-path.php" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">Roadmaps</a></li>
                        <li><a href="<?= $ROOT_PATH ?>student/coding-lab.php" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">Coding Lab</a></li>
                        <li><a href="<?= $ROOT_PATH ?>student/virtual-lab.php" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">Virtual Lab</a></li>
                        <li><a href="<?= $ROOT_PATH ?>ai/ai-chat.php" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">AI Tutor</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <h6 class="ft-text-main fs-sm fw-bold text-uppercase tracking-wide mb-4" style="font-family: 'Outfit', sans-serif;">Categories</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 fs-sm">
                        <li><a href="<?= $ROOT_PATH ?>student/courses.php?category=1" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">Programming</a></li>
                        <li><a href="<?= $ROOT_PATH ?>student/courses.php?category=11" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">Web Development</a></li>
                        <li><a href="<?= $ROOT_PATH ?>student/courses.php?category=7" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">AI & Data Science</a></li>
                        <li><a href="<?= $ROOT_PATH ?>student/courses.php?category=3" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">Design & Graphics</a></li>
                        <li><a href="<?= $ROOT_PATH ?>student/courses.php?category=2" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">Digital Marketing</a></li>
                        <li><a href="<?= $ROOT_PATH ?>student/courses.php?category=12" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">Game & 3D Dev</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <h6 class="ft-text-main fs-sm fw-bold text-uppercase tracking-wide mb-4" style="font-family: 'Outfit', sans-serif;">Career Paths</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 fs-sm">
                        <li><a href="<?= $ROOT_PATH ?>student/learning-path.php?path=frontend" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">Front-end Developer</a></li>
                        <li><a href="<?= $ROOT_PATH ?>student/learning-path.php?path=backend" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">Back-end Developer</a></li>
                        <li><a href="<?= $ROOT_PATH ?>student/learning-path.php?path=fullstack" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">Full-stack Engineer</a></li>
                        <li><a href="<?= $ROOT_PATH ?>student/learning-path.php?path=ai" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">AI Specialist</a></li>
                        <li><a href="<?= $ROOT_PATH ?>student/learning-path.php?path=devops" class="ft-text-muted text-decoration-none hover-text-primary ajax-link">DevOps Engineer</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-3 col-lg-3">
                    <h6 class="ft-text-main fs-sm fw-bold text-uppercase tracking-wide mb-4" style="font-family: 'Outfit', sans-serif;">Resources & Support</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 fs-sm mb-4">
                        <li><a href="<?= $ROOT_PATH ?>docs/index.php" class="ft-text-muted text-decoration-none hover-text-primary">Documentation</a></li>
                        <li><a href="<?= $ROOT_PATH ?>community/index.php" class="ft-text-muted text-decoration-none hover-text-primary">Community Forum</a></li>
                        <li><a href="<?= $ROOT_PATH ?>help/faq.php" class="ft-text-muted text-decoration-none hover-text-primary">Help Center & FAQ</a></li>
                        <li><a href="#" class="text-decoration-none" style="color: #10B981;" data-bs-toggle="modal" data-bs-target="#feedbackModal">Give Feedback / Report Bug</a></li>
                    </ul>
                    
                    <h6 class="ft-text-main fs-sm fw-bold text-uppercase tracking-wide mb-3" style="font-family: 'Outfit', sans-serif;">Connect</h6>
                    <div class="d-flex gap-3">
                        <a href="https://twitter.com" target="_blank" class="ft-text-muted fs-5 hover-text-primary" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                        <a href="https://github.com" target="_blank" class="ft-text-muted fs-5 hover-text-primary" aria-label="GitHub"><i class="bi bi-github"></i></a>
                        <a href="https://linkedin.com" target="_blank" class="ft-text-muted fs-5 hover-text-primary" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        <a href="https://discord.com" target="_blank" class="ft-text-muted fs-5 hover-text-primary" aria-label="Discord"><i class="bi bi-discord"></i></a>
                        <a href="https://youtube.com" target="_blank" class="ft-text-muted fs-5 hover-text-primary" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
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
                        <span class="badge ft-card-theme ft-text-main px-2 py-1"><i class="bi bi-globe me-1"></i> TH / EN</span>
                        <span class="badge ft-card-theme ft-text-main px-2 py-1 cursor-pointer" onclick="toggleMainTheme()"><i class="bi bi-moon-stars me-1"></i> Toggle Mode</span>
                    </div>
                    <span class="ms-2">v<?php echo htmlspecialchars($appVersion); ?></span>
                </div>
            </div>

            <!-- 4. Admin Server Info -->
            <?php if ($isAdmin): ?>
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
                <div class="modal-header ft-border-bottom border-0">
                    <h5 class="modal-title fw-bold ft-text-main" style="font-family: 'Outfit', sans-serif;">
                        <i class="bi bi-chat-right-text me-2" style="color: #6366F1;"></i> Send Feedback
                    </h5>
                    <button type="button" class="btn-close ft-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="feedbackForm">
                        <div class="mb-3">
                            <label class="form-label fs-sm ft-text-muted">Feedback Type</label>
                            <select name="type" class="form-select ft-modal-input ft-text-main shadow-none" required>
                                <option value="suggestion">💡 Feature Suggestion</option>
                                <option value="bug">🐛 Report a Bug</option>
                                <option value="course">📚 Course Content Feedback</option>
                                <option value="other">💬 General Feedback</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fs-sm ft-text-muted">Your Email (Optional)</label>
                            <input type="email" name="email" class="form-control ft-modal-input ft-text-main shadow-none" placeholder="name@example.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fs-sm ft-text-muted">Message / Bug Details</label>
                            <textarea name="message" class="form-control ft-modal-input ft-text-main shadow-none" rows="4" placeholder="Describe your experience or issues encountered..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-end gap-2 pt-2">
                            <button type="button" class="btn btn-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn text-white btn-sm px-4 rounded-pill fw-bold" style="background: linear-gradient(135deg, #4F46E5, #6366F1); border: none;" id="btnSubmitFeedback">
                                Send Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Include Rocket Custom Cursor component if exists
    $rocket_path = __DIR__ . '/rocket-cursor.php';
    if (file_exists($rocket_path)) {
        include $rocket_path;
    }
    ?>

    <!-- =========================================
         Footer Custom CSS Styles
         ========================================= -->
    <style>
        :root, html[data-theme="dark"], body[data-theme="dark"] {
            --ft-bg-main: #0b0f19;
            --ft-card-bg: rgba(15, 23, 42, 0.85);
            --ft-card-border: #1e293b;
            --ft-text-main: #f8fafc;
            --ft-text-muted: #94a3b8;
            --ft-hr-color: rgba(255, 255, 255, 0.08);
            --ft-modal-bg: #0f172a;
            --ft-modal-input-bg: rgba(15, 23, 42, 0.8);
            --ft-modal-input-border: #334155;
            --ft-btn-close-filter: invert(1);
        }

        html[data-theme="light"], body[data-theme="light"] {
            --ft-bg-main: #f8fafc;
            --ft-card-bg: #ffffff;
            --ft-card-border: #e2e8f0;
            --ft-text-main: #0f172a;
            --ft-text-muted: #64748b;
            --ft-hr-color: rgba(0, 0, 0, 0.08);
            --ft-modal-bg: #ffffff;
            --ft-modal-input-bg: #f1f5f9;
            --ft-modal-input-border: #cbd5e1;
            --ft-btn-close-filter: invert(0);
        }

        footer.footer-wrapper {
            background-color: var(--ft-bg-main);
            border-top: 1px solid var(--ft-card-border);
            color: var(--ft-text-main);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .ft-card-theme {
            background-color: var(--ft-card-bg) !important;
            border: 1px solid var(--ft-card-border) !important;
            color: var(--ft-text-main) !important;
        }

        .ft-text-main {
            color: var(--ft-text-main) !important;
        }

        .ft-text-muted {
            color: var(--ft-text-muted) !important;
        }

        .ft-border-bottom {
            border-bottom: 1px solid var(--ft-hr-color) !important;
        }

        .ft-hr {
            border-color: var(--ft-hr-color);
            opacity: 1;
        }

        .hover-text-primary:hover {
            color: #6366F1 !important;
            transition: color 0.2s ease;
        }

        .btn-back-to-top {
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .btn-back-to-top:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3) !important;
        }

        .ft-modal-theme {
            background-color: var(--ft-modal-bg) !important;
            border: 1px solid var(--ft-card-border) !important;
            color: var(--ft-text-main) !important;
        }

        .ft-modal-input {
            background-color: var(--ft-modal-input-bg) !important;
            border: 1px solid var(--ft-modal-input-border) !important;
            color: var(--ft-text-main) !important;
            border-radius: 10px;
        }

        .ft-btn-close {
            filter: var(--ft-btn-close-filter);
        }

        .cursor-pointer {
            cursor: pointer;
        }
    </style>

    <!-- =========================================
         Footer Core JavaScript
         ========================================= -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Back to Top Button Logic
            const backToTopBtn = document.getElementById('backToTopBtn');
            if (backToTopBtn) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 300) {
                        backToTopBtn.classList.remove('d-none');
                        backToTopBtn.classList.add('d-flex');
                    } else {
                        backToTopBtn.classList.add('d-none');
                        backToTopBtn.classList.remove('d-flex');
                    }
                });

                backToTopBtn.addEventListener('click', function() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }

            // 2. Newsletter Form Submission Handler
            const newsletterForm = document.getElementById('newsletterForm');
            if (newsletterForm) {
                newsletterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const btn = document.getElementById('btnNewsletter');
                    const originalText = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="bi bi-hourglass-split"></i>';

                    setTimeout(function() {
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                        newsletterForm.reset();
                        alert('ขอบคุณสำหรับการติดตามข่าวสาร LevelUp!');
                    }, 800);
                });
            }

            // 3. Feedback Form Submission Handler
            const feedbackForm = document.getElementById('feedbackForm');
            if (feedbackForm) {
                feedbackForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const btn = document.getElementById('btnSubmitFeedback');
                    const originalText = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = 'Sending...';

                    setTimeout(function() {
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                        feedbackForm.reset();
                        
                        // Close Bootstrap Modal safely
                        const modalEl = document.getElementById('feedbackModal');
                        if (modalEl && typeof bootstrap !== 'undefined') {
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                        }
                        alert('ขอบคุณสำหรับข้อเสนอแนะของคุณ! ทีมงานจะนำไปปรับปรุงระบบให้ดียิ่งขึ้น');
                    }, 1000);
                });
            }

            // 4. Daily AI Tip Cycler
            const tips = [
                "ระบบเรียนรู้ที่มี AI ช่วยโค้ช จะช่วยเพิ่มความเร็วในการทำเควสต์เขียนโค้ดได้ถึง 2 เท่า!",
                "ลองใช้งาน AI Chat เพื่อสอบถามตัวอย่างโค้ด หรือแนวทางการแก้บั๊กได้ตลอด 24 ชั่วโมง",
                "ทำภารกิจรายวันให้ครบเพื่อรักษาสถิติ Login Streak และรับ XP โบนัสเพิ่มขึ้น!",
                "ฝึกเขียนโค้ดและทดสอบรันผลใน Virtual Cyber Lab โดยไม่ต้องตั้งค่า Environment บนเครื่องตนเอง"
            ];
            const tipEl = document.getElementById('dailyAiTip');
            if (tipEl) {
                let randomTip = tips[Math.floor(Math.random() * tips.length)];
                tipEl.innerText = randomTip;
            }
        });
    </script>
</body>
</html>