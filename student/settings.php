<?php
$ROOT_PATH = '../';
$pageTitle = "Settings | LevelUp";
// เริ่มต้น Session เพื่อความปลอดภัย (หากยังไม่เริ่ม)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 💡 เทคนิค Output Buffering: ดักจับการเรนเดอร์ของ header.php ชั่วคราว
ob_start();
include '../includes/header.php';
$header_html = ob_get_clean();

// 💡 เตรียม Script และ Style ทั้งหมดเพื่อยัดเข้าไปใน <head>
$head_injection = <<<'EOT'
<!-- สคริปต์บังคับโหลดธีมสีดำ (Dark Mode) และป้องกัน White Flash 100% -->
<script>
    (function() {
        // 1. เซ็ต Theme ทันทีที่สคริปต์รัน
        const savedTheme = localStorage.getItem('theme');
        const currentTheme = savedTheme ? savedTheme : 'dark';
        document.documentElement.setAttribute('data-theme', currentTheme);
        
        // 2. รอให้โหลดหน้าเสร็จก่อน ค่อยเปิดการทำงานของ Transition 
        window.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.body.classList.add('theme-loaded');
            }, 150);
        });
    })();
</script>

<!-- เก็บ Link เดิมไว้เผื่อมีสไตล์พื้นฐาน -->
<link rel="stylesheet" href="../assets/css/settings.css">

<style>
    /* ===================================================
       DESIGN SYSTEM OVERRIDES (อ้างอิงจากระบบ Light Mode หลัก)
       =================================================== */
    
    /* 🌙 ตั้งค่าสีดำ (Dark Mode) เป็นค่าเริ่มต้นหลักของระบบ (:root) */
    :root, html[data-theme="dark"] {
        /* ชุดตัวแปรจาก Light Mode.txt */
        --lv-bg-dark: #0b0f19;
        --lv-card-bg: rgba(17, 24, 39, 0.7);
        --lv-border-glow: rgba(99, 102, 241, 0.2);
        --lv-cyan: #06b6d4;
        --lv-indigo: #6366f1;
        --lv-text-main: #f1f5f9;
        --lv-text-muted: #94a3b8;

        /* ตัวแปรสีเพิ่มเติมเฉพาะหน้า Settings */
        --neon-indigo: #4F46E5;
        --neon-purple: #9333EA;
        --cyber-green: #10B981;
        --plasma-orange: #F59E0B;
        --cyber-red: #EF4444;
        --input-bg: rgba(0, 0, 0, 0.2);
    }

    /* ☀️ เพิ่ม CSS สำหรับหน้า Settings ตอนเปลี่ยนเป็นโหมดสว่าง */
    html[data-theme="light"] {
        /* ชุดตัวแปรจาก Light Mode.txt */
        --lv-bg-dark: #f8fafc;
        --lv-card-bg: rgba(255, 255, 255, 0.85);
        --lv-border-glow: rgba(99, 102, 241, 0.15);
        --lv-text-main: #0f172a;
        --lv-text-muted: #64748b;

        /* ตัวแปรสีเพิ่มเติมเฉพาะหน้า Settings (ปรับให้เข้ากับโหมดสว่าง) */
        --neon-indigo: #4F46E5;
        --neon-purple: #9333EA;
        --cyber-green: #059669;
        --plasma-orange: #D97706;
        --cyber-red: #DC2626;
        --input-bg: #ffffff;
    }

    /* 🛠️ บังคับให้คลาส text-white และคลาสอื่นๆ กลับสีอัตโนมัติเมื่ออยู่ในโหมดสว่าง */
    html[data-theme="light"] .text-white {
        color: var(--lv-text-main) !important;
    }

    html[data-theme="light"] .bg-dark {
        background-color: rgba(226, 232, 240, 0.8) !important;
    }

    html[data-theme="light"] .btn-outline-light {
        color: var(--lv-text-main) !important;
        border-color: var(--lv-border-glow) !important;
    }

    html[data-theme="light"] .btn-outline-light:hover {
        background-color: rgba(0, 0, 0, 0.05) !important;
    }

    html[data-theme="light"] .btn-close-white {
        filter: invert(1) grayscale(100%) brightness(0);
    }

    /* อ้างอิงโครงสร้างจาก Light Mode.txt และ Settings เดิม */
    body {
        background-color: var(--lv-bg-dark) !important;
        color: var(--lv-text-main) !important;
        font-family: 'Inter', 'Prompt', sans-serif;
        line-height: 1.6;
        letter-spacing: 0.3px;
        overflow-x: hidden;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    /* คลาสนี้จะถูกเติมเข้ามาผ่าน JavaScript หลังจากโหลดหน้าเสร็จแล้วเท่านั้น */
    body.theme-loaded {
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Glassmorphism Card นำไปเชื่อมกับตัวแปรหลัก */
    .glass-card {
        background: var(--lv-card-bg);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--lv-border-glow);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .ai-text-gradient {
        background: linear-gradient(135deg, #6366F1, #10B981);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-family: 'Outfit', sans-serif;
    }

    /* Sidebar Navigation Settings */
    .settings-nav .nav-link {
        color: var(--lv-text-main) !important;
        transition: all 0.3s ease;
        border: 1px solid transparent;
        line-height: 1.5;
    }

    .settings-nav .nav-link:hover {
        background: rgba(128, 128, 128, 0.1);
        transform: translateX(5px);
    }

    .settings-nav .nav-link.active {
        background: linear-gradient(90deg, rgba(79, 70, 229, 0.15), transparent);
        border-left: 3px solid var(--neon-indigo);
        font-weight: 600;
        text-shadow: 0 0 10px rgba(79, 70, 229, 0.2);
    }

    /* Form Elements */
    .form-control,
    .form-select {
        background-color: var(--input-bg) !important;
        border: 1px solid var(--lv-border-glow) !important;
        color: var(--lv-text-main) !important;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        background-color: var(--input-bg) !important; /* บังคับให้ใช้สีเดิมตอนกำลังพิมพ์/Focus */
        color: var(--lv-text-main) !important;         /* บังคับสีข้อความให้คงเดิม */
        border-color: var(--neon-indigo) !important;
        box-shadow: 0 0 15px rgba(79, 70, 229, 0.3) !important;
    }

    .form-select option {
        background-color: var(--lv-bg-dark) !important;
        color: var(--lv-text-main) !important;
        padding: 10px;
    }

    /* ป้องกันเบราว์เซอร์เปลี่ยนสีพื้นหลังเป็นสีสว่างเมื่อ Autofill หรือพิมพ์ด่วน */
    input:-webkit-autofill,
    input:-webkit-autofill:hover, 
    input:-webkit-autofill:focus,
    textarea:-webkit-autofill,
    textarea:-webkit-autofill:hover,
    textarea:-webkit-autofill:focus {
        -webkit-text-fill-color: var(--lv-text-main) !important;
        -webkit-box-shadow: 0 0 0px 1000px var(--input-bg) inset !important;
        transition: background-color 5000s ease-in-out 0s;
    }

    /* Gamified AI Persona Cards */
    .persona-card .card-body {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .persona-card input:checked+.card-body {
        border-color: var(--neon-indigo) !important;
        background: rgba(79, 70, 229, 0.1);
        box-shadow: 0 0 20px rgba(79, 70, 229, 0.2);
        transform: translateY(-3px);
    }

    .persona-card input:checked+.card-body::after {
        content: '\F26A';
        font-family: 'bootstrap-icons';
        position: absolute;
        top: 15px;
        right: 15px;
        color: var(--neon-indigo);
        font-size: 1.2rem;
    }

    .persona-card:hover .card-body {
        border-color: rgba(128, 128, 128, 0.3);
    }

    /* Buttons */
    .btn-premium {
        background: linear-gradient(135deg, var(--neon-indigo), #6366F1);
        color: white !important;
        border: none;
        box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        transition: all 0.2s ease;
    }

    .btn-premium:hover {
        transform: scale(1.02);
        box-shadow: 0 0 25px rgba(79, 70, 229, 0.5);
    }

    /* Custom Switch */
    .custom-switch .form-check-input {
        background-color: var(--lv-border-glow);
        border-color: var(--lv-border-glow);
        cursor: pointer;
    }

    .custom-switch .form-check-input:checked {
        background-color: var(--cyber-green);
        border-color: var(--cyber-green);
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.4);
    }

    .fs-sm,
    .fs-xs,
    small,
    label.text-white {
        color: var(--lv-text-main) !important;
        opacity: 0.9 !important;
        line-height: 1.6;
    }
</style>
EOT;

// 💡 ทำการแทรก Script และ Style เข้าไปก่อนปิดแท็ก </head> แล้วค่อยวาดหน้าจอออกมา
if (strpos($header_html, '</head>') !== false) {
    echo str_replace('</head>', $head_injection . "\n</head>", $header_html);
} else {
    echo $header_html . $head_injection;
}

// ดึงข้อมูลผู้ใช้งานตามปกติ
$sql = "SELECT * FROM `users` WHERE `id` = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$_SESSION['user_login']['user_id']]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="container-fluid dark-theme-wrapper pb-5 pt-4" style="min-height: 100vh;">
    <div class="container">

        <!-- Header Section -->
        <div class="mb-5 d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-25 rounded-circle border border-primary border-opacity-50 text-white flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                <i class="bi bi-gear-fill fs-3"></i>
            </div>
            <div>
                <h2 class="fw-bold text-white mb-1" style="font-family: 'Outfit', sans-serif;">Command Center</h2>
                <p class="text-white fs-sm mb-0">ปรับแต่งตัวละคร, ระบบ AI ส่วนตัว และตั้งค่าความปลอดภัยของบัญชี</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Sidebar Navigation -->
            <div class="col-12 col-md-3">
                <div class="nav flex-column nav-pills settings-nav sticky-md-top glass-card p-3 rounded-4" id="v-pills-tab" role="tablist" aria-orientation="vertical" style="top: 80px; z-index: 10;">
                    <button class="nav-link active text-start rounded-3 mb-2 py-2" id="v-pills-account-tab" data-bs-toggle="pill" data-bs-target="#v-pills-account" type="button" role="tab"><i class="bi bi-person-badge me-2"></i> บัญชีผู้เล่น</button>
                    <button class="nav-link text-start rounded-3 mb-2 py-2" id="v-pills-ai-tab" data-bs-toggle="pill" data-bs-target="#v-pills-ai" type="button" role="tab"><i class="bi bi-robot me-2"></i> ระบบ AI & การเรียน</button>
                    <button class="nav-link text-start rounded-3 mb-2 py-2" id="v-pills-security-tab" data-bs-toggle="pill" data-bs-target="#v-pills-security" type="button" role="tab"><i class="bi bi-shield-lock me-2"></i> ความปลอดภัย</button>
                    <button class="nav-link text-start rounded-3 mb-2 py-2" id="v-pills-notifications-tab" data-bs-toggle="pill" data-bs-target="#v-pills-notifications" type="button" role="tab"><i class="bi bi-bell me-2"></i> การแจ้งเตือน</button>
                    <button class="nav-link text-start rounded-3 mb-2 py-2" id="v-pills-integrations-tab" data-bs-toggle="pill" data-bs-target="#v-pills-integrations" type="button" role="tab"><i class="bi bi-plug me-2"></i> การเชื่อมต่อ</button>
                    <button class="nav-link text-start rounded-3 py-2 text-white" id="v-pills-data-tab" data-bs-toggle="pill" data-bs-target="#v-pills-data" type="button" role="tab"><i class="bi bi-database-exclamation me-2 text-danger"></i> ข้อมูล & ขั้นสูง</button>
                </div>
            </div>

            <!-- Right Content Area -->
            <div class="col-12 col-md-9">
                <div class="tab-content" id="v-pills-tabContent">

                    <!-- 1. Account Settings -->
                    <div class="tab-pane fade show active" id="v-pills-account" role="tabpanel">
                        <div class="glass-card rounded-4 mb-4 p-4 p-md-5">
                            <h5 class="text-white fw-bold mb-4 ai-text-gradient"><i class="bi bi-person-bounding-box me-2"></i>Player Profile (โปรไฟล์สาธารณะ)</h5>

                            <form id="profileForm" data-user-id="<?php
$ROOT_PATH = '../';
echo $user_data['id'] ?? ''; ?>">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label text-white fs-sm fw-medium">ชื่อตัวละคร (Full Name)</label>
                                        <input type="text" class="form-control shadow-none rounded-3 py-2" name="name" value="<?php
$ROOT_PATH = '../';
echo htmlspecialchars($user_data['name'] ?? '', ENT_QUOTES); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-white fs-sm fw-medium">ไอดีประจำตัว (Google ID)</label>
                                        <input type="text" class="form-control shadow-none rounded-3 py-2" name="google_id" value="<?php
$ROOT_PATH = '../';
echo htmlspecialchars($user_data['google_id'] ?? '', ENT_QUOTES); ?>" disabled>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-white fs-sm fw-medium">ประวัติย่อ (Bio / Status)</label>
                                        <textarea class="form-control shadow-none rounded-3 py-2" name="bio" rows="3">Frontend Developer & AI Enthusiast.</textarea>
                                    </div>
                                    <div class="col-12 text-end mt-5 border-top border-secondary border-opacity-25 pt-4">
                                        <button type="submit" class="btn btn-premium rounded-pill px-5 fw-bold fs-sm w-100 w-md-auto" id="btnSaveProfile">
                                            <i class="bi bi-save2 me-2"></i> บันทึกข้อมูล
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- 2. AI & Learning Settings -->
                    <div class="tab-pane fade" id="v-pills-ai" role="tabpanel">
                        <div class="glass-card rounded-4 mb-4 p-4 p-md-5">
                            <h5 class="text-white fw-bold mb-2 ai-text-gradient"><i class="bi bi-cpu me-2"></i>AI Companion Setup 🤖</h5>
                            <p class="text-white fs-sm mb-4">เลือกคลาส (Class) ให้กับ AI ของคุณ เพื่อปรับสไตล์การสอนและการวิเคราะห์โค้ด</p>

                            <div class="row g-3 mb-5">
                                <div class="col-12 col-md-6">
                                    <label class="persona-card w-100 h-100">
                                        <input type="radio" name="ai_persona" value="teacher" class="d-none">
                                        <div class="card-body border border-secondary border-opacity-25 rounded-4 p-4 h-100 cursor-pointer text-white">
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                <div class="bg-primary bg-opacity-25 rounded-circle text-primary flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                    <i class="bi bi-book-half fs-4"></i>
                                                </div>
                                                <h6 class="mb-0 fw-bold">The Teacher (สายวิชาการ)</h6>
                                            </div>
                                            <p class="fs-sm text-white mb-0">อธิบายละเอียด เน้นทฤษฎีปูพื้นฐาน และแนะนำ Step-by-step อย่างใจเย็น</p>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="persona-card w-100 h-100">
                                        <input type="radio" name="ai_persona" value="senior_dev" class="d-none" checked>
                                        <div class="card-body border border-secondary border-opacity-25 rounded-4 p-4 h-100 cursor-pointer text-white">
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                <div class="bg-warning bg-opacity-25 rounded-circle text-warning flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                    <i class="bi bi-terminal fs-4"></i>
                                                </div>
                                                <h6 class="mb-0 fw-bold">Senior Dev (สายปฏิบัติจริง)</h6>
                                            </div>
                                            <p class="fs-sm text-white mb-0">พูดตรงไปตรงมา ใช้ศัพท์เทคนิค เน้น Best Practice และโค้ดที่พร้อมใช้งานบนโปรดักชัน</p>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="persona-card w-100 h-100">
                                        <input type="radio" name="ai_persona" value="mentor" class="d-none">
                                        <div class="card-body border border-secondary border-opacity-25 rounded-4 p-4 h-100 cursor-pointer text-white">
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                <div class="bg-success bg-opacity-25 rounded-circle text-success flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                    <i class="bi bi-compass fs-4"></i>
                                                </div>
                                                <h6 class="mb-0 fw-bold">The Mentor (สายโค้ชชิ่ง)</h6>
                                            </div>
                                            <p class="fs-sm text-white mb-0">ไม่บอกคำตอบตรงๆ แต่เน้นให้คำใบ้ (Hints) เพื่อให้คุณคิดและแก้ไขปัญหาด้วยตัวเอง</p>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="persona-card w-100 h-100">
                                        <input type="radio" name="ai_persona" value="interview" class="d-none">
                                        <div class="card-body border border-secondary border-opacity-25 rounded-4 p-4 h-100 cursor-pointer text-white">
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                <div class="bg-danger bg-opacity-25 rounded-circle text-danger flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                    <i class="bi bi-shield-shaded fs-4"></i>
                                                </div>
                                                <h6 class="mb-0 fw-bold">Interview Coach (สายฮาร์ดคอร์)</h6>
                                            </div>
                                            <p class="fs-sm text-white mb-0">จำลองการสัมภาษณ์งานสุดโหด ท้าทายโค้ดของคุณ และถามเจาะลึกถึง Big O Notation</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <hr class="border-secondary opacity-25 my-5">

                            <h5 class="text-white fw-bold mb-4">Learning Preferences (เป้าหมายการเรียน)</h5>

                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 p-3 border border-secondary border-opacity-25 rounded-3">
                                <div class="flex-grow-1">
                                    <h6 class="text-white mb-1"><i class="bi bi-crosshair text-info me-2"></i>Daily Quest Goal (เป้าหมายรายวัน)</h6>
                                    <p class="text-white fs-sm mb-0">กำหนดเวลาขั้นต่ำที่ต้องการอัปเวลในแต่ละวัน</p>
                                </div>
                                <select class="form-select w-auto shadow-none flex-shrink-0">
                                    <option>30 นาที (Casual)</option>
                                    <option selected>1 ชั่วโมง (Standard)</option>
                                    <option>2 ชั่วโมง (Hardcore)</option>
                                </select>
                            </div>

                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 p-3 border border-secondary border-opacity-25 rounded-3">
                                <div class="flex-grow-1">
                                    <h6 class="text-white mb-1">Auto Debugging Assistant</h6>
                                    <p class="text-white fs-sm mb-0">ให้ AI เสนอวิธีแก้บั๊กอัตโนมัติเมื่อโค้ดพังใน Labs</p>
                                </div>
                                <div class="form-check form-switch custom-switch fs-4 m-0 flex-shrink-0">
                                    <input class="form-check-input ajax-toggle" type="checkbox" role="switch" data-setting="auto_debug" checked>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 p-3 border border-secondary border-opacity-25 rounded-3">
                                <div class="flex-grow-1">
                                    <h6 class="text-white mb-1">Spaced Repetition System (SRS)</h6>
                                    <p class="text-white fs-sm mb-0">ระบบจัดตารางทบทวนเควสต์เก่าอัตโนมัติ เพื่อป้องกันการลืม</p>
                                </div>
                                <div class="form-check form-switch custom-switch fs-4 m-0 flex-shrink-0">
                                    <input class="form-check-input ajax-toggle" type="checkbox" role="switch" data-setting="srs_system" checked>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Security Settings -->
                    <div class="tab-pane fade" id="v-pills-security" role="tabpanel">
                        <div class="glass-card rounded-4 mb-4 p-4 p-md-5">
                            <h5 class="text-white fw-bold mb-4"><i class="bi bi-key-fill me-2 text-warning"></i>เปลี่ยนรหัสผ่านการเข้าถึง (Change Password)</h5>
                            <form id="passwordForm" onsubmit="return false;">
                                <div class="row g-4">
                                    <div class="col-12">
                                        <label class="form-label text-white fs-sm fw-medium">รหัสผ่านปัจจุบัน</label>
                                        <input type="password" class="form-control shadow-none rounded-3 py-2" name="current_pwd" required autocomplete="current-password">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-white fs-sm fw-medium">รหัสผ่านใหม่ (New Password)</label>
                                        <input type="password" class="form-control shadow-none rounded-3 py-2" name="new_pwd" id="new_pwd" required autocomplete="new-password">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-white fs-sm fw-medium">ยืนยันรหัสผ่านใหม่ (Confirm Password)</label>
                                        <input type="password" class="form-control shadow-none rounded-3 py-2" name="confirm_pwd" required autocomplete="new-password">
                                    </div>
                                    <div class="col-12 text-end mt-4">
                                        <button type="submit" class="btn btn-outline-light rounded-pill w-100 w-md-auto px-4 fw-bold fs-sm">อัปเดตรหัสผ่าน</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="glass-card rounded-4 mb-4 p-4 p-md-5">
                            <h5 class="text-white fw-bold mb-4"><i class="bi bi-shield-check me-2 text-success"></i>Security Center (ศูนย์ความปลอดภัย)</h5>

                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 p-3 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-3">
                                <div>
                                    <h6 class="text-white mb-1">Two-Factor Authentication (2FA)</h6>
                                    <p class="text-white fs-sm mb-0">เพิ่มเกราะป้องกันอีกชั้นให้บัญชีของคุณ</p>
                                </div>
                                <button class="btn btn-success btn-sm rounded-pill px-4 fw-bold text-white w-100 w-md-auto">เปิดใช้งาน</button>
                            </div>

                            <hr class="border-secondary opacity-25 my-4">

                            <h6 class="text-white fw-bold mb-3">เซสชันที่ออนไลน์อยู่ (Active Sessions)</h6>
                            <ul class="list-group list-group-flush bg-transparent gap-2">
                                <li class="list-group-item bg-transparent px-3 py-3 border border-secondary border-opacity-25 rounded-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bi bi-laptop fs-2 text-white flex-shrink-0"></i>
                                        <div>
                                            <h6 class="text-white mb-0 fs-sm fw-bold">Mac OS · Chrome</h6>
                                            <p class="text-white fs-xs mb-0"><i class="bi bi-circle-fill text-success me-1" style="font-size: 8px;"></i>Online now · Bangkok, TH</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item bg-transparent px-3 py-3 border border-secondary border-opacity-25 rounded-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bi bi-phone fs-2 text-white flex-shrink-0"></i>
                                        <div>
                                            <h6 class="text-white mb-0 fs-sm fw-bold">iOS 16 · Safari</h6>
                                            <p class="text-white fs-xs mb-0">Last active: 2 hrs ago</p>
                                        </div>
                                    </div>
                                    <button class="btn btn-outline-danger btn-sm rounded-pill fs-xs px-3 btn-logout-device text-white align-self-end align-self-md-center">Log out</button>
                                </li>
                            </ul>
                            <div class="mt-4 text-end">
                                <button class="btn btn-link text-white fs-sm p-0 text-decoration-none fw-medium"><i class="bi bi-box-arrow-right me-1 text-danger"></i>ออกจากระบบทุกอุปกรณ์</button>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Notifications -->
                    <div class="tab-pane fade" id="v-pills-notifications" role="tabpanel">
                        <div class="glass-card rounded-4 mb-4 p-4 p-md-5">
                            <h5 class="text-white fw-bold mb-5"><i class="bi bi-broadcast me-2 text-info"></i>ระบบสื่อสาร (Notification Preferences)</h5>

                            <h6 class="ai-text-gradient fs-xs fw-bold text-uppercase tracking-wide mb-3">Learning Activities (กิจกรรมการเรียน)</h6>
                            <div class="p-3 border border-secondary border-opacity-25 rounded-3 mb-4">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                                    <span class="text-white fs-sm flex-grow-1">แจ้งเตือนเควสต์และควิซใหม่ (New Assignments & Quizzes)</span>
                                    <div class="form-check form-switch custom-switch fs-5 m-0 flex-shrink-0"><input class="form-check-input ajax-toggle" type="checkbox" role="switch" checked></div>
                                </div>
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3 border-top border-secondary border-opacity-25 pt-3">
                                    <span class="text-white fs-sm flex-grow-1">ย้ำเตือนความคืบหน้า (Course Progress Reminders)</span>
                                    <div class="form-check form-switch custom-switch fs-5 m-0 flex-shrink-0"><input class="form-check-input ajax-toggle" type="checkbox" role="switch" checked></div>
                                </div>
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 border-top border-secondary border-opacity-25 pt-3">
                                    <span class="text-white fs-sm flex-grow-1">คำแนะนำจากโค้ช AI (AI Coach Recommendations)</span>
                                    <div class="form-check form-switch custom-switch fs-5 m-0 flex-shrink-0"><input class="form-check-input ajax-toggle" type="checkbox" role="switch" checked></div>
                                </div>
                            </div>

                            <h6 class="ai-text-gradient fs-xs fw-bold text-uppercase tracking-wide mb-3">Delivery Methods (ช่องทางการรับสาร)</h6>
                            <div class="p-3 border border-secondary border-opacity-25 rounded-3">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                                    <div class="d-flex gap-2 align-items-center text-white fs-sm flex-grow-1"><i class="bi bi-envelope text-primary fs-5"></i> Email Notifications</div>
                                    <div class="form-check form-switch custom-switch fs-5 m-0 flex-shrink-0"><input class="form-check-input ajax-toggle" type="checkbox" role="switch" checked></div>
                                </div>
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 border-top border-secondary border-opacity-25 pt-3">
                                    <div class="d-flex gap-2 align-items-center text-white fs-sm flex-grow-1"><i class="bi bi-browser-chrome text-warning fs-5"></i> Browser Push</div>
                                    <div class="form-check form-switch custom-switch fs-5 m-0 flex-shrink-0"><input class="form-check-input ajax-toggle" type="checkbox" role="switch"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Integrations -->
                    <div class="tab-pane fade" id="v-pills-integrations" role="tabpanel">
                        <div class="glass-card rounded-4 mb-4 p-4 p-md-5">
                            <h5 class="text-white fw-bold mb-4"><i class="bi bi-link-45deg me-2 text-primary"></i>เชื่อมโยงระบบ (Connected Accounts)</h5>

                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3 bg-dark bg-opacity-50 p-3 rounded-4 border border-secondary border-opacity-25 transition-all">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="bi bi-github fs-1 text-white flex-shrink-0"></i>
                                    <div>
                                        <h6 class="text-white mb-0 fw-bold">GitHub</h6>
                                        <p class="text-white fs-xs mb-0">Connected as <strong class="text-white">johndoe_dev</strong></p>
                                    </div>
                                </div>
                                <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 fs-sm text-white w-100 w-md-auto">Disconnect</button>
                            </div>

                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 bg-dark bg-opacity-50 p-3 rounded-4 border border-secondary border-opacity-25 transition-all">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="bi bi-google fs-1 text-white flex-shrink-0"></i>
                                    <div>
                                        <h6 class="text-white mb-0 fw-bold">Google Account</h6>
                                        <p class="text-white fs-xs mb-0">ซิงค์ปฏิทินและแบคอัพงานเข้า Drive</p>
                                    </div>
                                </div>
                                <button class="btn btn-premium btn-sm rounded-pill px-4 fw-bold fs-sm w-100 w-md-auto">Connect</button>
                            </div>

                            <hr class="border-secondary opacity-25 my-5">

                            <h5 class="text-white fw-bold mb-3"><i class="bi bi-terminal-dash me-2"></i>API & Developer Keys</h5>
                            <p class="text-white fs-sm mb-4">สร้าง API Key สำหรับเชื่อมต่อแพลตฟอร์ม LevelUp เข้ากับโปรเจกต์ส่วนตัวของคุณ</p>
                            <button class="btn btn-outline-light rounded-pill fs-sm px-4 w-100 w-md-auto"><i class="bi bi-plus-lg me-2"></i> Generate New Key</button>
                        </div>
                    </div>

                    <!-- 6. Data & Advanced -->
                    <div class="tab-pane fade" id="v-pills-data" role="tabpanel">
                        <div class="glass-card rounded-4 mb-4 p-4 p-md-5">
                            <h5 class="text-white fw-bold mb-4"><i class="bi bi-hdd-network me-2 text-info"></i>จัดการข้อมูล (Data Management)</h5>

                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 p-3 border border-secondary border-opacity-25 rounded-3">
                                <div>
                                    <h6 class="text-white mb-1">ดาวน์โหลดข้อมูล (Export Data)</h6>
                                    <p class="text-white fs-sm mb-0">ดึงประวัติการเรียน, โค้ดที่เคยเขียน, และเกียรติบัตรทั้งหมด</p>
                                </div>
                                <button class="btn btn-outline-info rounded-pill px-4 fs-sm fw-medium text-white w-100 w-md-auto"><i class="bi bi-download me-2"></i> Export JSON</button>
                            </div>

                            <hr class="border-secondary opacity-25 my-5">

                            <h5 class="text-white fw-bold mb-3"><i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>Danger Zone (พื้นที่อันตราย)</h5>
                            <div class="border border-danger border-opacity-50 bg-danger bg-opacity-10 rounded-4 p-4">
                                <h6 class="text-white mb-2 fw-bold">ลบบัญชีผู้ใช้ (Delete Account)</h6>
                                <p class="text-white fs-sm mb-4">การลบบัญชีจะเป็นการทำลายข้อมูลความคืบหน้า เควสต์ และประวัติแชท AI ทั้งหมดอย่างถาวร ไม่สามารถกู้คืนได้</p>
                                <button class="btn btn-danger rounded-pill px-4 fw-bold fs-sm w-100 w-md-auto" id="btnDeleteAccount">Initiate Deletion (ลบบัญชี)</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3 p-md-4">
    <div id="liveToast" class="toast align-items-center glass-card border border-success border-opacity-50 rounded-4 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex px-2 py-1">
            <div class="toast-body fw-bold text-success d-flex align-items-center gap-2" id="toastMessage">
                <i class="bi bi-check-circle-fill fs-5"></i> Settings updated successfully.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- Modal for Delete Account -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content glass-card border-danger border-opacity-50 text-white rounded-4 shadow-lg">
            <div class="modal-header border-bottom border-danger border-opacity-25 p-4">
                <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-octagon-fill me-2"></i> ยืนยันการทำลายข้อมูล?</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="fs-sm mb-4 text-white">นี่คือคำเตือนครั้งสุดท้าย การกระทำนี้ไม่สามารถย้อนกลับได้ ข้อมูลทั้งหมดจะถูกลบออกจากฐานข้อมูลทันที</p>
                <p class="fs-sm mb-2 fw-medium text-white">กรุณาพิมพ์คำว่า <strong class="text-danger">DELETE</strong> เพื่อยืนยัน</p>
                <input type="text" class="form-control bg-dark bg-opacity-50 border-danger border-opacity-50 text-white shadow-none mb-2 py-2" id="deleteConfirmInput" autocomplete="off" placeholder="DELETE">
            </div>
            <div class="modal-footer border-top border-danger border-opacity-25 p-3 flex-column flex-md-row">
                <button type="button" class="btn btn-outline-secondary rounded-pill w-100 w-md-auto px-4 fs-sm text-white mb-2 mb-md-0" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-danger rounded-pill w-100 w-md-auto px-4 fw-bold fs-sm" id="confirmDeleteBtn" disabled>ทำลายบัญชีถาวร</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
<script src="../assets/js/settings.js"></script>

<script>
    $(document).ready(function () {
        // จัดการการส่งฟอร์ม Profile
        $('#profileForm').on('submit', function (e) {
            e.preventDefault();

            var formData = {};
            $(this).serializeArray().forEach(function (item) {
                formData[item.name] = item.value;
            });

            // ดึงค่า user_id จาก Attribute data-user-id ของ Form
            var userId = $(this).data('user-id');

            var requestData = {
                action: 'update_profile',
                data: formData,
                user_id: userId
            };

            $.ajax({
                url: 'services/user.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
                dataType: 'JSON',
                success: function (response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'บันทึกข้อมูลสำเร็จ',
                            text: response.message || 'ข้อมูลของคุณถูกบันทึกเรียบร้อยแล้ว',
                            confirmButtonText: 'ตกลง',
                            confirmButtonColor: '#2e8b57'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: response.message || 'ไม่สามารถบันทึกข้อมูลได้',
                            confirmButtonText: 'ตกลง',
                            confirmButtonColor: '#d33'
                        });
                    }
                },
                error: function (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้',
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        });
    });
</script>

<?php
$ROOT_PATH = '../';
include '../includes/footer.php'; ?>
