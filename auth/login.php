<?php
// ตั้งค่า Error Reporting (สามารถปิดใช้งานใน Production โดยเปลี่ยนเป็น 0)
ini_set('display_errors', 1);
error_reporting(E_ALL);

$ROOT_PATH = '../';

// ตรวจสอบและเริ่มต้น Session หากยังไม่ได้เริ่ม
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// นำเข้าไฟล์ Google Auth Config (หากมี)
if (file_exists(__DIR__ . '/../config/google.php')) {
    require_once __DIR__ . '/../config/google.php';
}

// ตรวจสอบสถานะการเข้าสู่ระบบ หาก Login แล้วให้ Redirect ไปยังหน้าหลัก
if (isset($_SESSION['user_login']) || isset($_SESSION['user_id'])) {
    header("Location: " . $ROOT_PATH . "index.php");
    exit;
}

// -------------------------------------------------------------------------
// ตัวแปรข้อมูลการตั้งค่าหน้า Login
// -------------------------------------------------------------------------
$site_settings = [
    'hero_title' => 'INITIALIZE QUEST',
    'hero_desc' => 'เข้าสู่ระบบเพื่อเริ่มสะสม XP และดำเนินภารกิจเรียนรู้ของคุณต่อ',
    'security_note' => 'ปลอดภัยด้วยระบบรับรองตัวตน Google & GitHub OAuth 2.0',
    'footer_links' => [
        ['url' => $ROOT_PATH . 'index.php', 'icon' => 'bi bi-arrow-left', 'text' => 'กลับหน้าแรก'],
        ['url' => '#', 'icon' => '', 'text' => 'นโยบายความเป็นส่วนตัว'],
        ['url' => '#', 'icon' => '', 'text' => 'ติดต่อเรา']
    ]
];

// สร้าง URL Login ผ่าน Google Client (ถ้ามีออบเจกต์ $client)
$login_url = (isset($client) && method_exists($client, 'createAuthUrl')) ? $client->createAuthUrl() : $ROOT_PATH . 'api/google_auth.php';

// นำเข้า Header
if (file_exists(__DIR__ . '/../includes/header.php')) {
    include __DIR__ . '/../includes/header.php';
}
?>

<!-- 
========================================================================
     STYLESHEET SECTION (LEVELUP CORE STYLE EMBED)
========================================================================
-->
<style>
    /* Custom Scrollbar Styles (ฝังตรงในหน้าลบปัญหาเรื่อง Selector เพื่อให้แสดงผลทันที) */
    * {
        scrollbar-width: thin;
    }
    html[data-theme="dark"], :root:not([data-theme="light"]) {
        scrollbar-color: #6366f1 #090d1a;
    }
    html[data-theme="light"] {
        scrollbar-color: #94a3b8 #f1f5f9;
    }
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    html[data-theme="dark"]::-webkit-scrollbar-track,
    :root:not([data-theme="light"])::-webkit-scrollbar-track,
    ::-webkit-scrollbar-track {
        background: #090d1a !important;
    }
    html[data-theme="dark"]::-webkit-scrollbar-thumb,
    :root:not([data-theme="light"])::-webkit-scrollbar-thumb,
    ::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #6366f1 0%, #06b6d4 100%) !important;
        border-radius: 10px !important;
        border: 2px solid #090d1a !important;
    }
    html[data-theme="dark"]::-webkit-scrollbar-thumb:hover,
    :root:not([data-theme="light"])::-webkit-scrollbar-thumb:hover,
    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #818cf8 0%, #22d3ee 100%) !important;
        box-shadow: 0 0 10px rgba(6, 182, 212, 0.6);
    }
    html[data-theme="light"]::-webkit-scrollbar-track {
        background: #f1f5f9 !important;
    }
    html[data-theme="light"]::-webkit-scrollbar-thumb {
        background: #cbd5e1 !important;
        border-radius: 10px !important;
        border: 2px solid #f1f5f9 !important;
    }
    html[data-theme="light"]::-webkit-scrollbar-thumb:hover {
        background: #94a3b8 !important;
    }

    :root, html[data-theme="dark"] {
        --lv-bg-dark: #090d16;
        --lv-card-bg: rgba(15, 23, 42, 0.88);
        --lv-card-border: rgba(99, 102, 241, 0.25);
        --lv-text-main: #f8fafc;
        --lv-text-muted: #94a3b8;
        --lv-grid-color: rgba(99, 102, 241, 0.07);
        --lv-gradient-1: rgba(99, 102, 241, 0.15);
        --lv-gradient-2: rgba(16, 185, 129, 0.08);
        --lv-footer-link: #94a3b8;
    }

    html[data-theme="light"] {
        --lv-bg-dark: #f1f5f9;
        --lv-card-bg: #ffffff;
        --lv-card-border: #e2e8f0;
        --lv-text-main: #0f172a;
        --lv-text-muted: #64748b;
        --lv-grid-color: rgba(99, 102, 241, 0.06);
        --lv-gradient-1: rgba(99, 102, 241, 0.08);
        --lv-gradient-2: rgba(16, 185, 129, 0.05);
        --lv-footer-link: #64748b;
    }

    body {
        background-color: var(--lv-bg-dark) !important;
        font-family: 'Outfit', 'Kanit', sans-serif;
        color: var(--lv-text-main) !important;
        overflow-x: hidden;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .auth-section {
        background: radial-gradient(circle at 80% 20%, var(--lv-gradient-1) 0%, transparent 50%),
                    radial-gradient(circle at 20% 80%, var(--lv-gradient-2) 0%, transparent 50%);
        transition: background 0.3s ease;
    }

    .bg-cyber-grid {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background-image: linear-gradient(var(--lv-grid-color) 1px, transparent 1px),
                          linear-gradient(90deg, var(--lv-grid-color) 1px, transparent 1px);
        background-size: 36px 36px;
        z-index: 0;
        pointer-events: none;
    }

    .auth-card-gamified {
        background-color: var(--lv-card-bg) !important;
        border: 1px solid var(--lv-card-border) !important;
        backdrop-filter: blur(16px);
        border-radius: 24px !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12), 0 0 30px rgba(99, 102, 241, 0.08);
        transition: all 0.3s ease;
    }

    .quest-icon {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
        transition: all 0.3s ease;
    }

    .btn-google-premium {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        color: #1e293b !important;
        font-weight: 600;
        letter-spacing: 0.3px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
        transition: all 0.25s ease;
    }

    .btn-google-premium:hover {
        transform: translateY(-2px);
        background: #f8fafc !important;
        border-color: #94a3b8 !important;
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.15);
        color: #0f172a !important;
    }

    .btn-github-premium {
        background: #1e293b !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        color: #ffffff !important;
        font-weight: 600;
        letter-spacing: 0.3px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
        transition: all 0.25s ease;
    }

    html[data-theme="light"] .btn-github-premium {
        background: #0f172a !important;
        border: 1px solid #0f172a !important;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.15);
    }

    .btn-github-premium:hover {
        transform: translateY(-2px);
        background: #334155 !important;
        border-color: rgba(255, 255, 255, 0.25) !important;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
        color: #ffffff !important;
    }

    html[data-theme="light"] .btn-github-premium:hover {
        background: #1e293b !important;
    }

    .pulse-glow {
        animation: pulseEffect 2s infinite alternate;
    }

    @keyframes pulseEffect {
        0% { box-shadow: 0 0 10px rgba(99, 102, 241, 0.3); }
        100% { box-shadow: 0 0 25px rgba(99, 102, 241, 0.6); }
    }

    .footer-link-item {
        color: var(--lv-footer-link) !important;
        transition: color 0.3s ease;
    }

    .footer-link-item:hover {
        color: var(--lv-text-main) !important;
    }
</style>

<!-- 
========================================================================
     HTML CONTENT SECTION
========================================================================
-->
<section class="auth-section d-flex align-items-center min-vh-100 position-relative py-5">
    <div class="bg-cyber-grid"></div>

    <div class="container position-relative z-1 mt-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-5 col-xl-4">

                <div class="card auth-card-gamified border-0 p-4 p-md-5 text-center">

                    <div class="mb-4">
                        <div class="quest-icon rounded-4 d-inline-flex align-items-center justify-content-center mb-3 pulse-glow" style="width: 58px; height: 58px; font-size: 28px;">
                            <i class="bi bi-rocket-takeoff-fill"></i>
                        </div>
                        <h3 class="fw-bold mb-2" style="font-family: 'Outfit', sans-serif; color: var(--lv-text-main);">
                            <?= htmlspecialchars($site_settings['hero_title']); ?>
                        </h3>
                        <p class="fs-sm mb-0" style="color: var(--lv-text-muted);">
                            <?= htmlspecialchars($site_settings['hero_desc']); ?>
                        </p>
                    </div>

                    <!-- Social Login Buttons -->
                    <div class="my-4 d-flex flex-column gap-3">
                        <!-- Google Login -->
                        <a href="<?= htmlspecialchars($login_url); ?>" class="btn btn-google-premium w-100 d-flex align-items-center justify-content-center gap-3 py-3 text-decoration-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 48 48" style="display: block;">
                                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.13-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                            </svg>
                            <span class="fs-5">Sign In with Google</span>
                        </a>

                        <!-- GitHub Login -->
                        <a href="<?= $ROOT_PATH; ?>auth/github_login.php" class="btn btn-github-premium w-100 d-flex align-items-center justify-content-center gap-3 py-3 text-decoration-none">
                            <i class="bi bi-github fs-4"></i>
                            <span class="fs-5">Sign In with GitHub</span>
                        </a>
                    </div>

                    <!-- Security Note -->
                    <div class="mt-4 pt-2">
                        <p class="fs-xs mb-0" style="color: var(--lv-text-muted);">
                            <i class="bi bi-shield-check text-success me-1"></i> <?= htmlspecialchars($site_settings['security_note']); ?>
                        </p>
                    </div>

                </div>

                <!-- Footer Links -->
                <div class="text-center mt-4 fs-sm">
                    <?php foreach ($site_settings['footer_links'] as $link): ?>
                        <a href="<?= htmlspecialchars($link['url']); ?>" class="text-decoration-none me-3 footer-link-item">
                            <?php if (!empty($link['icon'])): ?>
                                <i class="<?= htmlspecialchars($link['icon']); ?> me-1"></i> 
                            <?php endif; ?>
                            <?= htmlspecialchars($link['text']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </div>
</section>

<?php 
if (file_exists(__DIR__ . '/../includes/footer.php')) {
    include __DIR__ . '/../includes/footer.php'; 
}
?>