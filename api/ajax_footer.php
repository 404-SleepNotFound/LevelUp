<?php
// ==========================================
// 1. Backend Processing (สำหรับ AJAX POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    header('Content-Type: application/json; charset=utf-8');

    $action = $_POST['action'] ?? '';

    // ระบบสมัคร Newsletter
    if ($action === 'subscribe_newsletter') {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        
        if (!$email) {
            echo json_encode(["status" => "error", "message" => "Invalid email address."]);
            exit;
        }
        
        sleep(1); 
        echo json_encode(["status" => "success", "message" => "Subscribed successfully."]);
        exit;
    }

    // ระบบส่ง Feedback
    if ($action === 'submit_feedback') {
        $type = htmlspecialchars($_POST['type'] ?? '');
        $message = htmlspecialchars($_POST['message'] ?? '');
        
        if (empty($message)) {
            echo json_encode(["status" => "error", "message" => "Message is required."]);
            exit;
        }

        sleep(1);
        echo json_encode(["status" => "success", "message" => "Feedback received."]);
        exit;
    }

    echo json_encode(["status" => "error", "message" => "Unknown action."]);
    exit;
}
?>

<!-- ========================================== -->
<!-- 2. Frontend HTML (ส่วนแสดงผล Footer หน้าเว็บ) -->
<!-- ========================================== -->
<footer class="footer bg-white py-5 border-top">
    <div class="container">
        <div class="row gy-4">
            
            <!-- Column 1: Brand & Newsletter -->
            <div class="col-lg-4 col-md-12">
                <div class="brand-logo mb-2 d-flex align-items-center gap-2">
                    <span class="fs-3">🚀</span>
                    <h3 class="fw-bold m-0">LevelUp</h3>
                </div>
                <p class="text-secondary small mb-4">
                    Learn Smarter with AI. Build Real Skills. The next-generation learning platform for tech professionals.
                </p>
                
                <form id="newsletterForm" class="mb-3">
                    <label class="form-label fw-bold small text-uppercase mb-2">Subscribe to Newsletter</label>
                    <div class="input-group">
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                        <button type="submit" id="btnNewsletter" class="btn btn-primary px-4">Subscribe</button>
                    </div>
                </form>

                <!-- ❌ ปุ่ม App Store และ Google Play ถูกลบออกเรียบร้อยแล้ว -->
            </div>

            <!-- Column 2: Platform -->
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="fw-bold text-uppercase mb-3">PLATFORM</h6>
                <ul class="list-unstyled d-flex flex-column gap-2 small">
                    <li><a href="#" class="text-decoration-none text-secondary">Home</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary">Quests</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary">Roadmaps</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary">Coding Lab</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary">Virtual Lab</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary">AI Tutor</a></li>
                </ul>
            </div>

            <!-- Column 3: Categories -->
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="fw-bold text-uppercase mb-3">CATEGORIES</h6>
                <ul class="list-unstyled d-flex flex-column gap-2 small">
                    <li><a href="#" class="text-decoration-none text-secondary">Programming</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary">Web Development</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary">AI & Machine Learning</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary">Data Science</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary">Cloud Computing</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary">Cybersecurity</a></li>
                </ul>
            </div>

            <!-- Column 4: Career Paths -->
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="fw-bold text-uppercase mb-3">CAREER PATHS</h6>
                <ul class="list-unstyled d-flex flex-column gap-2 small">
                    <li><a href="#" class="text-decoration-none text-secondary">Front-end Developer</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary">Back-end Developer</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary">Data Scientist</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary">AI Engineer</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary">DevOps Engineer</a></li>
                </ul>
            </div>

            <!-- Column 5: Resources & Connect -->
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="fw-bold text-uppercase mb-3">RESOURCES & SUPPORT</h6>
                <ul class="list-unstyled d-flex flex-column gap-2 small mb-4">
                    <li><a href="#" class="text-decoration-none text-secondary">Documentation</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary">Community Forum</a></li>
                    <li><a href="#" class="text-decoration-none text-secondary">Help Center & FAQ</a></li>
                    <li><a href="#" class="text-decoration-none text-success fw-medium">Give Feedback / Report Bug</a></li>
                </ul>

                <h6 class="fw-bold text-uppercase mb-3">CONNECT</h6>
                <div class="d-flex gap-3 fs-5 text-secondary">
                    <a href="#" class="text-secondary"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="text-secondary"><i class="bi bi-github"></i></a>
                    <a href="#" class="text-secondary"><i class="bi bi-linkedin"></i></a>
                    <a href="#" class="text-secondary"><i class="bi bi-discord"></i></a>
                    <a href="#" class="text-secondary"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

        </div>

        <!-- Bottom Copyright Bar -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center pt-4 mt-4 border-top small text-secondary">
            <div class="d-flex gap-3 mb-2 mb-md-0">
                <span>© 2026 LevelUp. All Rights Reserved.</span>
                <a href="#" class="text-secondary text-decoration-none">Privacy</a>
                <a href="#" class="text-secondary text-decoration-none">Terms</a>
                <a href="#" class="text-secondary text-decoration-none">Security</a>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-success border border-success-subtle rounded-pill px-3 py-2">
                    🟢 All Systems Operational
                </span>
            </div>
        </div>
    </div>
</footer>