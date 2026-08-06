<?php
$ROOT_PATH = '../';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php';

$user_id = $_SESSION['user_login']['user_id'] ?? ($_SESSION['user_id'] ?? 0);

// ดึงข้อมูล Category ด้วย PDO
$category_data = [];
$subcategory_data = [];
$certificate_templates = [];

if (isset($conn)) {
    try {
        // Fetch Categories
        $sql = "SELECT * FROM `category` ORDER BY `id` ASC";
        $result = $conn->query($sql);
        if ($result) {
            $category_data = $result->fetchAll(PDO::FETCH_ASSOC);
        }

        // Fetch Subcategories if table exists
        $sql_sub = "SELECT * FROM `subcategory` ORDER BY `id` ASC";
        $result_sub = $conn->query($sql_sub);
        if ($result_sub) {
            $subcategory_data = $result_sub->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        error_log("DB Error in create-course.php: " . $e->getMessage());
    }
}
?>

<style>
    /* 🌙 ตัวแปรสำหรับ Dark Mode (Default) */
    :root, html[data-theme="dark"] {
        --bg-dark-blend: #070a13;
        --card-bg-premium: #0f172a;
        --cc-text-main: #f8fafc;
        --cc-text-muted: #cbd5e1;
        --cc-text-label: #f1f5f9;
        --cc-input-bg: rgba(15, 23, 42, 0.8);
        --cc-input-border: rgba(255, 255, 255, 0.15);
        --cc-input-text: #ffffff;
        --cc-input-ph: #64748b;
        --cc-step-bg: #0f172a;
        --cc-step-border: rgba(255, 255, 255, 0.2);
        --cc-box-border: rgba(255, 255, 255, 0.1);
        --cc-preview-bg: rgba(0, 0, 0, 0.3);
        --cc-template-bg: rgba(30, 41, 59, 0.6);
        --cc-modal-bg: #1e293b;
        --cc-card-hover: rgba(51, 65, 85, 0.5);
    }

    /* ☀️ ตัวแปรสำหรับ Light Mode */
    html[data-theme="light"] {
        --bg-dark-blend: #f8fafc;
        --card-bg-premium: #ffffff;
        --cc-text-main: #0f172a;
        --cc-text-muted: #64748b;
        --cc-text-label: #1e293b;
        --cc-input-bg: #ffffff;
        --cc-input-border: rgba(15, 23, 42, 0.15);
        --cc-input-text: #0f172a;
        --cc-input-ph: #94a3b8;
        --cc-step-bg: #f8fafc;
        --cc-step-border: rgba(15, 23, 42, 0.2);
        --cc-box-border: rgba(0, 0, 0, 0.12);
        --cc-preview-bg: rgba(241, 245, 249, 0.8);
        --cc-template-bg: #ffffff;
        --cc-modal-bg: #ffffff;
        --cc-card-hover: #f1f5f9;
    }

    body, .create-course-wrapper {
        animation: fadeIn 0.3s ease-in-out;
        color: var(--cc-text-main);
        background-color: var(--bg-dark-blend);
        border-radius: 12px;
        font-family: 'Kanit', 'Prompt', system-ui, -apple-system, sans-serif;
    }

    html[data-theme="light"] .create-course-wrapper h3.text-white,
    html[data-theme="light"] .create-course-wrapper h5.text-white,
    html[data-theme="light"] .create-course-wrapper h6.text-white,
    html[data-theme="light"] .create-course-wrapper .text-white:not(.btn):not(.badge),
    html[data-theme="light"] .modal .text-white:not(.btn):not(.badge) {
        color: var(--cc-text-main) !important;
    }

    html[data-theme="light"] .create-course-wrapper .text-info:not(.btn),
    html[data-theme="light"] .modal .text-info:not(.btn) {
        color: #0284c7 !important;
    }

    /* 🎨 Custom Button Styles */
    .btn-cyan {
        background: linear-gradient(135deg, #06b6d4, #0284c7) !important;
        border: none !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
        transition: all 0.25s ease;
        border-radius: 8px;
    }

    .btn-cyan:hover {
        background: linear-gradient(135deg, #0891b2, #0369a1) !important;
        box-shadow: 0 6px 16px rgba(6, 182, 212, 0.5);
        transform: translateY(-1px);
        color: #ffffff !important;
    }

    .btn-outline-purple {
        border: 1px solid #c084fc !important;
        color: #e9d5ff !important;
        background: transparent;
        transition: all 0.25s ease;
        border-radius: 8px;
    }

    .btn-outline-purple:hover {
        background: linear-gradient(135deg, #a855f7, #7c3aed) !important;
        color: #ffffff !important;
        border-color: #a855f7 !important;
        box-shadow: 0 4px 12px rgba(168, 85, 247, 0.4);
        transform: translateY(-1px);
    }

    .ai-helper-btn {
        background: linear-gradient(135deg, rgba(168, 85, 247, 0.25), rgba(6, 182, 212, 0.25));
        border: 1px solid #c084fc;
        color: #e9d5ff !important;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 500;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }

    .ai-helper-btn:hover {
        background: linear-gradient(135deg, #a855f7, #06b6d4);
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(168, 85, 247, 0.4);
    }

    /* 📌 Step Navigation Timeline */
    .step-timeline {
        display: flex;
        justify-content: space-between;
        margin-bottom: 35px;
        position: relative;
    }

    .step-timeline::before {
        content: '';
        position: absolute;
        top: 21px;
        left: 5%;
        right: 5%;
        height: 3px;
        background: var(--cc-box-border);
        z-index: 1;
    }

    .step-node {
        position: relative;
        z-index: 2;
        background: var(--cc-step-bg);
        border: 2px solid var(--cc-step-border);
        border-radius: 50%;
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: var(--cc-text-muted);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .step-node.active {
        border-color: #06b6d4;
        background: #06b6d4;
        color: #ffffff;
        box-shadow: 0 0 18px rgba(6, 182, 212, 0.5);
    }

    .step-node.completed {
        border-color: #22c55e;
        background: #22c55e;
        color: #ffffff;
    }

    .step-label {
        font-size: 0.82rem;
        color: var(--cc-text-muted);
        text-align: center;
        margin-top: 8px;
        font-weight: 500;
    }

    /* 📝 Inputs & Form Controls */
    .form-dark-input {
        background: var(--cc-input-bg) !important;
        border: 1px solid var(--cc-input-border) !important;
        color: var(--cc-input-text) !important;
        border-radius: 10px !important;
        padding: 12px 16px !important;
        transition: all 0.3s ease;
    }

    .form-dark-input:focus {
        border-color: #06b6d4 !important;
        box-shadow: 0 0 10px rgba(6, 182, 212, 0.3) !important;
        outline: none;
    }

    .form-dark-input option {
        background-color: var(--card-bg-premium) !important;
        color: var(--cc-input-text) !important;
        padding: 10px;
    }

    select.form-dark-input,
    .form-select.form-dark-input {
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2306b6d4' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 1rem center !important;
        background-size: 14px 10px !important;
        padding-right: 2.5rem !important;
    }

    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none !important;
        margin: 0 !important;
    }
    input[type=number] {
        -moz-appearance: textfield !important;
    }

    .form-label-custom {
        font-size: 0.9rem;
        color: var(--cc-text-label) !important;
        font-weight: 500;
        margin-bottom: 6px;
    }

    .tag-badge {
        background: rgba(6, 182, 212, 0.15);
        border: 1px solid rgba(6, 182, 212, 0.3);
        color: #22d3ee;
        padding: 6px 12px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.85rem;
    }

    .tag-badge i {
        cursor: pointer;
        color: #ec4899;
        transition: transform 0.2s;
    }

    .tag-badge i:hover {
        transform: scale(1.2);
    }

    .ai-magic-btn {
        background: linear-gradient(135deg, #a855f7 0%, #7c3aed 100%);
        border: none;
        color: white !important;
        border-radius: 10px;
        padding: 10px 18px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        font-weight: 500;
    }

    .ai-magic-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(168, 85, 247, 0.4);
    }

    /* 🖼️ Upload Boxes & Preview Cards */
    .image-preview-box {
        border: 2px dashed var(--cc-box-border);
        border-radius: 16px;
        background: var(--cc-preview-bg);
        min-height: 200px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        padding: 20px;
        transition: border-color 0.3s ease, background-color 0.3s ease;
    }

    .image-preview-box:hover {
        border-color: #06b6d4;
        background: rgba(6, 182, 212, 0.05);
    }

    .template-card {
        background: var(--cc-template-bg);
        border: 1px solid var(--cc-box-border);
        border-radius: 12px;
        padding: 18px;
        cursor: pointer;
        transition: all 0.25s ease;
    }

    .template-card:hover {
        border-color: #a855f7;
        background: var(--cc-card-hover);
        transform: translateY(-2px);
    }

    .serp-preview-box {
        background: var(--cc-preview-bg);
        border: 1px solid var(--cc-box-border);
        border-radius: 12px;
        padding: 16px;
        font-family: Arial, sans-serif;
    }

    .serp-url {
        font-size: 13px;
        color: #202124;
        word-break: break-all;
    }
    html[data-theme="dark"] .serp-url { color: #bdc1c6; }

    .serp-title {
        font-size: 18px;
        color: #1a0dab;
        cursor: pointer;
        text-decoration: hover;
        line-height: 1.3;
    }
    html[data-theme="dark"] .serp-title { color: #8ab4f8; }

    .serp-snippet {
        font-size: 13px;
        color: #4d5156;
        line-height: 1.5;
    }
    html[data-theme="dark"] .serp-snippet { color: #bdc1c6; }

    .cc-modal-bg { background-color: var(--cc-modal-bg) !important; border: 1px solid var(--cc-box-border); }
    .cc-inner-box { background-color: var(--cc-step-bg) !important; border: 1px solid var(--cc-box-border); }
    .cc-preview-box { background-color: var(--cc-preview-bg); color: var(--cc-text-main); }

    .btn-cc-outline {
        border: 1px solid var(--cc-box-border);
        color: var(--cc-text-main) !important;
        background: transparent;
        transition: all 0.3s ease;
        border-radius: 8px;
    }
    .btn-cc-outline:hover { background: var(--cc-step-bg); border-color: #06b6d4; }

    .text-cyan { color: #06b6d4 !important; }
    .text-purple { color: #c084fc !important; }

    .revenue-split-card {
        background: rgba(6, 182, 212, 0.08);
        border: 1px solid rgba(6, 182, 212, 0.2);
        border-radius: 12px;
        padding: 15px;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="create-course-wrapper p-3 p-md-4">

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="text-white fw-bold mb-1"><i class="bi bi-plus-square-fill text-info me-2"></i> สร้างคอร์สใหม่ (Create New Course)</h3>
            <div class="text-info small fw-medium">
                ลำดับขั้นตอนการทำงาน: <span class="text-white bg-info bg-opacity-20 px-2 py-0.5 rounded">1. ข้อมูลพื้นฐาน</span> → 2. สื่อและหมวดหมู่ → 3. เป้าหมายเรียน → 4. ตั้งค่าระบบ / ราคา / SEO
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-purple text-white btn-sm px-3" data-bs-toggle="modal" data-bs-target="#aiBlueprintModal">
                <i class="bi bi-cpu-fill me-1"></i> AI Course Blueprint
            </button>
            <button type="button" class="btn btn-cc-outline btn-sm px-3" data-bs-toggle="modal" data-bs-target="#templateModal">
                <i class="bi bi-journal-album me-1"></i> คลัง Template คอร์ส
            </button>
        </div>
    </div>

    <!-- Step Timeline Bar -->
    <div class="step-timeline px-2 px-md-5">
        <div class="d-flex flex-column align-items-center" onclick="switchStep(1)">
            <div class="step-node active" id="node-step-1">1</div>
            <span class="step-label">ข้อมูลทั่วไป</span>
        </div>
        <div class="d-flex flex-column align-items-center" onclick="switchStep(2)">
            <div class="step-node" id="node-step-2">2</div>
            <span class="step-label">สื่อและหมวดหมู่</span>
        </div>
        <div class="d-flex flex-column align-items-center" onclick="switchStep(3)">
            <div class="step-node" id="node-step-3">3</div>
            <span class="step-label">เป้าหมายเรียน</span>
        </div>
        <div class="d-flex flex-column align-items-center" onclick="switchStep(4)">
            <div class="step-node" id="node-step-4">4</div>
            <span class="step-label">ตั้งค่าระบบ / ราคา / SEO</span>
        </div>
    </div>

    <!-- Course Form -->
    <form id="createCourseForm" enctype="multipart/form-data">

        <!-- STEP 1: ข้อมูลพื้นฐาน -->
        <div class="course-step-section" id="step-section-1">
            <div class="p-4 rounded-4 mb-4" style="background: var(--card-bg-premium); border: 1px solid var(--cc-box-border);">
                <h5 class="fw-bold text-white mb-3"><i class="bi bi-info-circle text-info me-2"></i> 1. ข้อมูลพื้นฐานของคอร์ส</h5>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label form-label-custom">ชื่อคอร์ส <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-dark-input required-field" id="course_title" name="course_title" placeholder="เช่น Advanced Full Stack Web Development with Next.js" oninput="updateSeoPreview()">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label form-label-custom">ชื่อรอง (Subtitle)</label>
                        <input type="text" class="form-control form-dark-input" id="course_subtitle" name="course_subtitle" placeholder="คำโปรยหัวสั้นๆ กระตุ้นผู้เรียน">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label form-label-custom">ภาษาหลักในการสอน</label>
                        <select class="form-select form-dark-input" id="course_language" name="course_language">
                            <option value="th" selected>ภาษาไทย (Thai)</option>
                            <option value="en">ภาษาอังกฤษ (English)</option>
                            <option value="th-en">สองภาษา (Thai & English Subtitles)</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label form-label-custom mb-0">คำอธิบายสั้น (Short Description)</label>
                            <button type="button" class="btn ai-helper-btn" onclick="generateAICourseDesc()">
                                <i class="bi bi-stars"></i> ให้ AI ช่วยแต่งคำอธิบาย
                            </button>
                        </div>
                        <input type="text" class="form-control form-dark-input" id="course_short_desc" name="course_short_desc" placeholder="สรุปคอร์สใน 1 ประโยคเพื่อโชว์ที่การ์ดหน้าแรก" oninput="updateSeoPreview()">
                    </div>
                    <div class="col-12">
                        <label class="form-label form-label-custom">คำอธิบายแบบรายละเอียด (Course Full Description)</label>
                        <textarea class="form-control form-dark-input" id="course_full_desc" name="course_full_desc" rows="5" placeholder="รายละเอียดหลักสูตร โครงสร้าง และเนื้อหาเจาะลึกที่ผู้เรียนจะได้รับ"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label form-label-custom">Tags แท็กคีย์เวิร์ด (พิมพ์ข้อความแล้วกด Enter เพื่อเพิ่ม)</label>
                        <input type="text" class="form-control form-dark-input mb-2" id="tag_input" placeholder="เช่น nextjs, react, api, tailwind">
                        <input type="hidden" name="course_tag" id="hidden_tags">
                        <div id="tags_container" class="d-flex flex-wrap gap-2 mt-2"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 2: รูปภาพ สื่อ หมวดหมู่ และความยาก -->
        <div class="course-step-section d-none" id="step-section-2">
            <div class="p-4 rounded-4 mb-4" style="background: var(--card-bg-premium); border: 1px solid var(--cc-box-border);">
                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <h5 class="fw-bold text-white mb-3"><i class="bi bi-image text-info me-2"></i> 2. สื่อแนะนำคอร์ส (Course Preview Media)</h5>

                        <div class="d-flex gap-2 mb-3">
                            <button type="button" class="btn btn-info text-white px-4 fw-medium" id="btn-tab-image" onclick="toggleMediaTab('image')">
                                <i class="bi bi-image me-1"></i> รูปภาพปก
                            </button>
                            <button type="button" class="btn btn-outline-info text-white px-4 fw-medium" id="btn-tab-video" onclick="toggleMediaTab('video')">
                                <i class="bi bi-camera-video me-1"></i> วิดีโอ ตัวอย่าง
                            </button>
                        </div>

                        <!-- Box Cover Image -->
                        <div id="box-image">
                            <label class="form-label form-label-custom">รูปปกคอร์ส (แนะนำขนาด 1280x720 หรือ 16:9) <span class="text-danger">*</span></label>
                            <div class="image-preview-box mb-2 media-dropzone" id="dropzone-image">
                                <input type="file" id="cover_file" name="cover_file" class="d-none" accept="image/*" onchange="previewMedia(this, 'image')">
                                <img id="cover_preview" src="" class="d-none" alt="Preview Image" style="width:100%; height:100%; object-fit:cover; border-radius:12px;">
                                <div class="upload-placeholder text-center">
                                    <i class="bi bi-cloud-arrow-up fs-1 text-cyan mb-2 d-block"></i>
                                    <span class="fw-medium">คลิกหรือลากไฟล์ <b>รูปภาพ</b> มาวางที่นี่</span>
                                    <div class="small text-muted mt-1">(รองรับ JPG, PNG, WEBP ขนาดไม่เกิน 5MB)</div>
                                </div>
                            </div>
                        </div>

                        <!-- Box Intro Video -->
                        <div id="box-video" class="d-none">
                            <label class="form-label form-label-custom">วิดีโอแนะนำคอร์ส (Intro Video - MP4/WebM)</label>
                            <div class="image-preview-box mb-2 media-dropzone" id="dropzone-video">
                                <input type="file" id="video_file" name="video_file" class="d-none" accept="video/mp4,video/webm" onchange="previewMedia(this, 'video')">
                                <video id="video_preview" controls class="d-none w-100" style="max-height: 220px; border-radius: 8px; z-index: 5; position: relative;"></video>
                                <div class="upload-placeholder text-center">
                                    <i class="bi bi-film fs-1 text-purple mb-2 d-block"></i>
                                    <span class="fw-medium">คลิกหรือลากไฟล์ <b>วิดีโอ</b> มาวางที่นี่</span>
                                    <div class="small text-muted mt-1">(ไฟล์วิดีโอสั้นไม่เกิน 2 นาที / 100MB)</div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="form-label form-label-custom">หรือใส่ลิงก์ YouTube / Vimeo Preview</label>
                                <input type="url" class="form-control form-dark-input" id="video_url" name="video_url" placeholder="https://www.youtube.com/watch?v=...">
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <h5 class="fw-bold text-white mb-3"><i class="bi bi-tags text-info me-2"></i> 3 & 4. หมวดหมู่และระดับความยาก</h5>

                        <div class="mb-3">
                            <label class="form-label form-label-custom">หมวดหมู่หลักคอร์ส (Category) <span class="text-danger">*</span></label>
                            <select class="form-select form-dark-input required-field" id="course_category" name="course_category">
                                <option value="">-- กรุณาเลือกหมวดหมู่หลัก --</option>
                                <?php if (!empty($category_data)): ?>
                                    <?php foreach ($category_data as $category): ?>
                                        <option value="<?= htmlspecialchars($category['id'] ?? ''); ?>">
                                            <?= htmlspecialchars($category['name'] ?? $category['category_name'] ?? ''); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="1">Programming & Development</option>
                                    <option value="2">Data Science & AI</option>
                                    <option value="3">Cybersecurity & Networking</option>
                                    <option value="4">Graphic Design & UI/UX</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label form-label-custom">ระดับความยาก (Difficulty Level)</label>
                            <select class="form-select form-dark-input" id="course_difficulty" onchange="updateDifficultyHelp()" name="course_difficulty">
                                <option value="Beginner" selected>Beginner (เหมาะสำหรับผู้เริ่มต้น ไม่มีพื้นฐานเลย)</option>
                                <option value="Intermediate">Intermediate (มีพื้นฐานภาษาคอมพิวเตอร์มาบ้างแล้ว)</option>
                                <option value="Advanced">Advanced (เจาะลึกโปรเจกต์ใหญ่ ลอจิกซับซ้อน)</option>
                                <option value="Expert">Expert (ระดับสถาปัตยกรรมระบบขั้นสูงและ Security)</option>
                            </select>
                            <div id="difficulty_help" class="small text-info mt-2 fw-medium">💡 ปูพื้นฐานจากศูนย์ ไม่จำเป็นต้องมีประสบการณ์เขียนโค้ดมาก่อน</div>
                        </div>

                        <div>
                            <label class="form-label form-label-custom">ประมาณการระยะเวลาการเรียน</label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <label class="small text-muted mb-1">ชั่วโมงรวม</label>
                                    <input type="number" class="form-control form-dark-input" id="duration_hours" placeholder="เช่น 18" name="duration_hours" min="0">
                                </div>
                                <div class="col-4">
                                    <label class="small text-muted mb-1">จำนวนบท</label>
                                    <input type="number" class="form-control form-dark-input" id="duration_chapters" placeholder="เช่น 12" name="duration_chapters" min="0">
                                </div>
                                <div class="col-4">
                                    <label class="small text-muted mb-1">ชม./สัปดาห์</label>
                                    <input type="text" class="form-control form-dark-input" id="duration_week" placeholder="เช่น 3-4" name="duration_week">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 3: เป้าหมายการเรียน + Prerequisites + Career Path -->
        <div class="course-step-section d-none" id="step-section-3">
            <div class="p-4 rounded-4 mb-4" style="background: var(--card-bg-premium); border: 1px solid var(--cc-box-border);">
                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="fw-bold text-white mb-0"><i class="bi bi-bookmark-check text-success me-2"></i> 5. สิ่งที่จะได้เรียนรู้ (Learning Outcomes)</h5>
                            <button type="button" class="btn btn-sm btn-outline-info text-white py-1 px-2" onclick="addDynamicRow('outcomes_list', 'เช่น เขียน REST API ได้อย่างเชี่ยวชาญ','outcomes[]')">+ เพิ่มข้อใหม่</button>
                        </div>
                        <div id="outcomes_list" class="d-flex flex-column gap-2 mt-2">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control form-dark-input" value="เข้าใจแนวคิดและหลักการทำงานเบื้องต้นอย่างละเอียด" name="outcomes[]">
                                <button class="btn btn-outline-danger text-white px-3" type="button" onclick="$(this).parent().remove()"><i class="bi bi-trash-fill"></i></button>
                            </div>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control form-dark-input" value="สร้างและพัฒนาโปรเจกต์จริงพร้อม Deploy ขึ้นระบบ Cloud ได้" name="outcomes[]">
                                <button class="btn btn-outline-danger text-white px-3" type="button" onclick="$(this).parent().remove()"><i class="bi bi-trash-fill"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="fw-bold text-white mb-0"><i class="bi bi-card-list text-warning me-2"></i> 6. ความรู้พื้นฐานที่ต้องมีก่อน (Prerequisites)</h5>
                            <button type="button" class="btn btn-sm btn-outline-warning text-white py-1 px-2" onclick="addDynamicRow('prereq_list', 'เช่น HTML / CSS และ JavaScript พื้นฐาน','prereq[]')">+ เพิ่มข้อใหม่</button>
                        </div>
                        <div id="prereq_list" class="d-flex flex-column gap-2 mt-2">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control form-dark-input" value="ไม่มีความรู้พื้นฐานคอมพิวเตอร์ระดับสูงก็สามารถเรียนได้" name="prereq[]">
                                <button class="btn btn-outline-danger text-white px-3" type="button" onclick="$(this).parent().remove()"><i class="bi bi-trash-fill"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <hr style="border-color: var(--cc-box-border);">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label form-label-custom">เหมาะสำหรับกลุ่มเป้าหมายใด (Target Audience)</label>
                                <select class="form-select form-dark-input" id="course_target" name="course_target">
                                    <option value="ผู้เริ่มต้น / นักเรียน นักศึกษา">ผู้เริ่มต้น / นักเรียน นักศึกษา</option>
                                    <option value="โปรแกรมเมอร์ที่ต้องการย้ายหรือเปลี่ยนสายงาน">โปรแกรมเมอร์ที่ต้องการย้ายหรือเปลี่ยนสายงาน</option>
                                    <option value="ผู้ทำงานสายตรงที่ต้องการอัปสกิลเทคโนโลยีใหม่">ผู้ทำงานสายตรงที่ต้องการอัปสกิลเทคโนโลยีใหม่</option>
                                    <option value="เจ้าของธุรกิจและผู้บริหารระดับสร้างนวัตกรรม">เจ้าของธุรกิจและผู้บริหารระดับสร้างนวัตกรรม</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label form-label-custom">เชื่อมโยงกับสายอาชีพ (Career Path)</label>
                                <select class="form-select form-dark-input" id="course_career" name="course_career">
                                    <option value="Full Stack Developer">Full Stack Developer</option>
                                    <option value="Front-end Developer">Front-end Developer</option>
                                    <option value="Back-end Developer">Back-end Developer</option>
                                    <option value="Data Scientist / AI Engineer">Data Scientist / AI Engineer</option>
                                    <option value="Cloud & DevOps Engineer">Cloud & DevOps Engineer</option>
                                    <option value="Cybersecurity Analyst">Cybersecurity Analyst</option>
                                    <option value="UI/UX Designer & Product Owner">UI/UX Designer & Product Owner</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 4: การตั้งค่าระบบ / ราคา / ใบประกาศ / SEO -->
        <div class="course-step-section d-none" id="step-section-4">
            <div class="p-4 rounded-4 mb-4" style="background: var(--card-bg-premium); border: 1px solid var(--cc-box-border);">
                
                <h5 class="fw-bold text-white mb-3"><i class="bi bi-cash-stack text-cyan me-2"></i> ประเภทคอร์ส การตั้งค่าราคา และระบบ</h5>
                
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label form-label-custom">ประเภทคอร์สและการคิดเงิน</label>
                        <select class="form-select form-dark-input" id="course_type" name="course_type" onchange="togglePriceFields(this.value)">
                            <option value="Paid" selected>เสียเงินซื้อขาดรายคอร์ส (Paid)</option>
                            <option value="Free">เปิดให้เข้าเรียนฟรี (Free)</option>
                            <option value="Premium">Premium รวมในแพ็กเกจสมาชิก</option>
                            <option value="Private">ส่วนบุคคลเฉพาะกลุ่ม (Private / Internal)</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label form-label-custom">สถานะเปิดรับชม (Visibility)</label>
                        <select class="form-select form-dark-input" id="course_visibility" name="course_visibility">
                            <option value="Draft" selected>Draft (บันทึกร่างภายในสตูดิโอผู้สอน)</option>
                            <option value="Public">Public (เปิดสาธารณะ ค้นหาเจอบนหน้าเว็บ)</option>
                            <option value="Unlisted">Unlisted (เข้าเรียนได้ผ่านลิงก์ตรงเท่านั้น)</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label form-label-custom">เชิญอาจารย์ร่วมสอน (Co-Instructor)</label>
                        <input type="email" class="form-control form-dark-input" id="collab_email" placeholder="ระบุอีเมลผู้ช่วยสอนหรือผู้ตรวจคอร์ส" name="collab_email">
                    </div>

                    <!-- 💵 ช่องกรอกจำนวนเงิน/ราคาคอร์ส -->
                    <div class="col-12 col-md-6 price-field-group">
                        <label class="form-label form-label-custom">ราคาปกติ (บาท) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-info fw-bold">฿</span>
                            <input type="number" step="0.01" class="form-control form-dark-input" id="course_price" name="pice" placeholder="เช่น 1500" min="0" value="1500" oninput="calculateRevenue()">
                        </div>
                    </div>

                    <div class="col-12 col-md-6 price-field-group">
                        <label class="form-label form-label-custom">ราคาโปรโมชัน / ส่วนลด (บาท) <span class="text-muted small">(เว้นว่างหากไม่มีส่วนลด)</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-warning fw-bold">฿</span>
                            <input type="number" step="0.01" class="form-control form-dark-input" id="course_sale_price" name="sale_price" placeholder="เช่น 990" min="0" value="990" oninput="calculateRevenue()">
                        </div>
                    </div>

                    <!-- Revenue Share Estimator Box -->
                    <div class="col-12 price-field-group">
                        <div class="revenue-split-card d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div>
                                <div class="fw-bold text-cyan"><i class="bi bi-calculator"></i>ประมาณการรายได้ผู้สอนต่อการขาย 1 สิทธิ์ (70% Revenue Share)</div>
                                <div class="small text-muted">หักค่าธรรมเนียมแพลตฟอร์มและ Payment Gateway แล้ว 30%</div>
                            </div>
                            <div class="fs-4 fw-bold text-success" id="instructor_earning_display">
                                ฿693.00 <span class="fs-6 text-muted font-normal">/ ผู้เรียน</span>
                            </div>
                        </div>
                    </div>

                    <!-- Certificate Config -->
                    <div class="col-12">
                        <div class="p-3 rounded-3 cc-inner-box mt-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="cert_enable" name="cert_enable" value="1" checked onchange="$('#cert_condition_box').toggle()">
                                <label class="form-check-label text-white fw-bold small" for="cert_enable">เปิดใช้งานใบรับรองหลังเรียนจบหลักสูตร (Certificate of Completion)</label>
                            </div>
                            <div id="cert_condition_box" class="mt-3 row g-2">
                                <span class="text-info fw-medium small mb-1"><i class="bi bi-gear"></i> กำหนดเงื่อนไขการรับใบประกาศนียบัตร:</span>
                                <div class="col-12 col-md-6"><label class="small"><input type="checkbox" checked name="cert_condition_quiz" value="1"> ผู้เรียนต้องทำผ่านแบบทดสอบ (Quiz) ทั้งหมดอย่างน้อย 70%</label></div>
                                <div class="col-12 col-md-6"><label class="small"><input type="checkbox" name="cert_condition_lab" value="1"> ผู้เรียนต้องส่งและผ่านการทดสอบแล็บปฏิบัติการ (Coding Lab)</label></div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Settings & Live SERP Preview -->
                    <div class="col-12">
                        <h5 class="fw-bold text-white mb-2 mt-3"><i class="bi bi-search text-cyan me-2"></i> การตั้งค่าการค้นหาทางการตลาด (SEO Settings)</h5>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label form-label-custom">Friendly URL Slug</label>
                                <input type="text" class="form-control form-dark-input" id="seo_slug" placeholder="เช่น advanced-nextjs-course" name="seo_slug">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label form-label-custom">Meta Title</label>
                                <input type="text" class="form-control form-dark-input" id="seo_title" placeholder="หัวข้อไตเติ้ลเมื่อแสดงผลบน Google Search" name="seo_title" oninput="updateSeoPreview()">
                            </div>
                            <div class="col-12">
                                <label class="form-label form-label-custom">Meta Description</label>
                                <textarea class="form-control form-dark-input" id="seo_desc" rows="2" placeholder="เขียนสรุปรายละเอียดความยาวไม่เกิน 160 ตัวอักษร เพื่อให้ Search Engine ดึงข้อมูลไปแสดง" name="seo_desc" oninput="updateSeoPreview()"></textarea>
                            </div>

                            <!-- Google SERP Live Mockup -->
                            <div class="col-12 mt-3">
                                <label class="form-label form-label-custom">ตัวอย่างการแสดงผลบน Google Search (SERP Preview)</label>
                                <div class="serp-preview-box">
                                    <div class="serp-url mb-1" id="serp_url_preview">https://your-lms-domain.com/courses/<span id="serp_slug_text">advanced-course</span></div>
                                    <div class="serp-title mb-1 fw-bold" id="serp_title_preview">ชื่อคอร์สจะปรากฏที่นี่บน Google Search</div>
                                    <div class="serp-snippet" id="serp_desc_preview">คำอธิบายรายวิชาอย่างย่อจะถูกนำมาแสดงในส่วนนี้เพื่อให้ผู้ค้นหาใน Google ได้รับทราบเนื้อหาหลัก...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Action Buttons -->
        <div class="d-flex justify-content-between align-items-center mb-5 gap-2">
            <div>
                <button type="button" class="btn btn-cc-outline px-3" id="btn-prev-step" onclick="changeStep(-1)" disabled>
                    <i class="bi bi-arrow-left me-1"></i> ย้อนกลับ
                </button>
            </div>

            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <button type="button" class="btn btn-outline-warning text-white" onclick="saveCourseDraft()">
                    <i class="bi bi-file-earmark-lock-fill me-1"></i> บันทึกร่าง (Draft)
                </button>
                <button type="button" class="btn btn-outline-info text-white" onclick="previewCourseModal()">
                    <i class="bi bi-eye me-1"></i> ดูตัวอย่างคอร์ส (Preview)
                </button>

                <button type="button" class="btn btn-cyan px-4 text-white fw-medium" id="btn-next-step" onclick="changeStep(1)">
                    ถัดไป <i class="bi bi-arrow-right ms-1"></i>
                </button>

                <button type="submit" class="btn btn-success px-4 text-white fw-medium d-none" id="btn-submit-course">
                    <i class="bi bi-check-circle-fill me-1"></i> ยืนยันบันทึกคอร์สเรียบร้อย <i class="bi bi-chevron-right ms-1"></i>
                </button>
            </div>
        </div>

    </form>
</div>

<!-- [MODAL 1] AI Course Blueprint Assistant -->
<div class="modal fade" id="aiBlueprintModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content text-white cc-modal-bg rounded-4">
            <div class="modal-header p-3" style="border-bottom: 1px solid var(--cc-box-border);">
                <h5 class="modal-title fw-bold text-purple"><i class="bi bi-stars"></i> AI Course Blueprint Assistant</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="small text-white mb-3">พิมพ์หัวข้อที่คุณตั้งใจจะสอนด้านล่าง ปัญญาประดิษฐ์จะทำการคำนวณโครงสร้าง แผนการทำคอร์สแล็บ และสร้าง Outcomes ให้ครบสูตรทันที</p>
                <div class="input-group mb-4">
                    <input type="text" class="form-control form-dark-input" id="ai_blueprint_topic" placeholder="เช่น 'Python สำหรับผู้เริ่มต้น' หรือ 'Zero to Hero Docker & Kubernetes'">
                    <button class="btn ai-magic-btn text-white" type="button" onclick="runAIBlueprint()">ปลุกเสกพิมพ์เขียว ✨</button>
                </div>
                <div id="ai_blueprint_result" class="d-none p-3 rounded-3 cc-inner-box">
                    <h6 class="fw-bold text-info mb-2"><i class="bi bi-patch-check"></i> แผนพิมพ์เขียวโครงสร้างที่ AI แนะนำ:</h6>
                    <div id="ai_blueprint_text" class="small lh-base text-pre-wrap mb-3 p-3 rounded cc-preview-box"></div>
                    <button type="button" class="btn btn-sm btn-info text-white px-3" onclick="applyBlueprintToForm()">นำข้อมูลพิมพ์เขียวไปจัดลงฟอร์มทันที</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- [MODAL 2] Course Template Library -->
<div class="modal fade" id="templateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content text-white cc-modal-bg rounded-4">
            <div class="modal-header p-3" style="border-bottom: 1px solid var(--cc-box-border);">
                <h5 class="modal-title fw-bold text-white"><i class="bi bi-journal-album text-info"></i> คลังเทมเพลตสำหรับเริ่มสร้างคอร์ส</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 d-flex flex-column gap-3">
                <div class="template-card" onclick="selectTemplate('Programming')">
                    <div class="fw-bold text-success mb-1"><i class="bi bi-code-slash me-1"></i> Programming Course Template</div>
                    <div class="small text-muted">ตั้งโครงสร้างมาตรฐาน มีแล็บโค้ดดิ้งสำเร็จรูป 4 จุด พร้อมห้องแบบทดสอบวัดผล</div>
                </div>
                <div class="template-card" onclick="selectTemplate('DataScience')">
                    <div class="fw-bold text-info mb-1"><i class="bi bi-bar-chart-line-fill me-1"></i> Data Science & AI Template</div>
                    <div class="small text-muted">เน้นบทเรียนวิเคราะห์ข้อมูล การอ่านกราฟสถิติ และสรุปทฤษฎีแมชชีนเลิร์นนิ่ง</div>
                </div>
                <div class="template-card" onclick="selectTemplate('Cyber')">
                    <div class="fw-bold text-danger mb-1"><i class="bi bi-shield-lock-fill me-1"></i> Cybersecurity & Cloud Template</div>
                    <div class="small text-muted">เหมาะสำหรับการสอน Network, DevOps เน้นโครงสร้างการทดลอง Virtual Labs</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- [MODAL 3] Course Live Preview Modal -->
<div class="modal fade" id="previewCourseModalObj" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content text-white cc-modal-bg rounded-4">
            <div class="modal-header p-3" style="border-bottom: 1px solid var(--cc-box-border);">
                <h5 class="modal-title fw-bold text-white"><i class="bi bi-eye text-info me-2"></i> ตัวอย่างการแสดงผลการ์ดคอร์ส (Course Card Preview)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="card p-3 rounded-4" style="background: var(--card-bg-premium); border: 1px solid var(--cc-box-border); max-width: 420px; margin: 0 auto;">
                    <div style="height: 200px; background: var(--cc-preview-bg); border-radius: 10px; overflow: hidden;" class="mb-3 d-flex align-items-center justify-content-center">
                        <img id="card_preview_img" src="" class="w-100 h-100 d-none" style="object-fit: cover;">
                        <span id="card_preview_img_ph" class="text-muted"><i class="bi bi-image fs-1 d-block text-center"></i> ไม่มีภาพปก</span>
                    </div>
                    <span class="badge bg-info text-dark w-auto align-self-start mb-2" id="card_preview_level">Beginner</span>
                    <h5 class="fw-bold text-white mb-2" id="card_preview_title">ชื่อคอร์สเรียนจะปรากฏที่นี่</h5>
                    <p class="small text-muted mb-3" id="card_preview_desc">คำอธิบายสั้นของคอร์สจะปรากฏตรงส่วนนี้...</p>
                    <div class="d-flex justify-content-between align-items-center pt-2" style="border-top: 1px solid var(--cc-box-border);">
                        <div>
                            <span class="text-cyan fw-bold fs-5" id="card_preview_price">฿1,500</span>
                            <span class="text-muted text-decoration-line-through small ms-1" id="card_preview_sale">฿990</span>
                        </div>
                        <span class="btn btn-sm btn-cyan">ดูรายละเอียด</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var Alerts = window.InstructorAlerts || {
        toast: function(type, msg) { 
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: type, title: msg, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            } else {
                console.log('[' + type.toUpperCase() + '] ' + msg); 
            }
        },
        saveDraft: function() { 
            Alerts.toast('success', 'ทำการบันทึกร่างข้อมูลเรียบร้อยแล้ว'); 
        },
        aiProcess: function(msg, time, callback) { setTimeout(callback, time); }
    };

    var currentStep = 1;
    var tagsArray = [];

    /* 🏷️ Tag System Manager */
    function initTagSystem() {
        $('#tag_input').on('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                var val = $(this).val().trim().replace(',', '');
                if (val && !tagsArray.includes(val)) {
                    tagsArray.push(val);
                    renderTags();
                    $(this).val('');
                }
            }
        });
    }

    function renderTags() {
        var html = '';
        tagsArray.forEach(function(t, idx) {
            html += `<span class="tag-badge">${t} <i class="bi bi-x-circle-fill" onclick="removeTag(${idx})"></i></span>`;
        });
        $('#tags_container').html(html);
        $('#hidden_tags').val(tagsArray.join(','));
    }

    function removeTag(idx) {
        tagsArray.splice(idx, 1);
        renderTags();
    }

    /* 💰 Revenue & Price Calculator */
    function calculateRevenue() {
        var price = parseFloat($('#course_price').val()) || 0;
        var sale = parseFloat($('#course_sale_price').val()) || 0;
        var effectivePrice = (sale > 0 && sale < price) ? sale : price;
        var instructorEarning = effectivePrice * 0.70; // 70% Revenue share

        $('#instructor_earning_display').html(`฿${instructorEarning.toLocaleString('th-TH', {minimumFractionDigits: 2, maximumFractionDigits: 2})} <span class="fs-6 text-muted font-normal">/ ผู้เรียน</span>`);
    }

    function togglePriceFields(type) {
        if (type === 'Free') {
            $('.price-field-group').fadeOut(200);
            $('#course_price').val(0);
            $('#course_sale_price').val(0);
        } else {
            $('.price-field-group').fadeIn(200);
        }
        calculateRevenue();
    }

    /* 🖼️ Media Handler & Switcher */
    function toggleMediaTab(type) {
        if (type === 'image') {
            $("#btn-tab-image").removeClass('btn-outline-info').addClass('btn-info');
            $("#btn-tab-video").removeClass('btn-info').addClass('btn-outline-info');
            $("#box-image").removeClass('d-none');
            $("#box-video").addClass('d-none');
        } else if (type === 'video') {
            $("#btn-tab-image").removeClass('btn-info').addClass('btn-outline-info');
            $("#btn-tab-video").removeClass('btn-outline-info').addClass('btn-info');
            $("#box-video").removeClass('d-none');
            $("#box-image").addClass('d-none');
        }
    }

    function previewMedia(input, type) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            var file = input.files[0];

            reader.onload = function(e) {
                if (type === 'image') {
                    $('#cover_preview').attr('src', e.target.result).removeClass('d-none');
                    $('#card_preview_img').attr('src', e.target.result).removeClass('d-none');
                    $('#card_preview_img_ph').addClass('d-none');
                    $('#dropzone-image .upload-placeholder').addClass('d-none');
                } else if (type === 'video') {
                    $('#video_preview').attr('src', e.target.result).removeClass('d-none');
                    $('#dropzone-video .upload-placeholder').addClass('d-none');
                }
            }
            reader.readAsDataURL(file);
        }
    }

    /* 🔍 SEO SERP Preview Update */
    function updateSeoPreview() {
        var title = $('#seo_title').val() || $('#course_title').val() || 'ชื่อคอร์สจะปรากฏที่นี่บน Google Search';
        var desc = $('#seo_desc').val() || $('#course_short_desc').val() || 'คำอธิบายรายวิชาอย่างย่อจะถูกนำมาแสดงในส่วนนี้เพื่อให้ผู้ค้นหาใน Google ได้รับทราบเนื้อหาหลัก...';
        var slug = $('#seo_slug').val() || createSlug($('#course_title').val());

        $('#serp_title_preview').text(title);
        $('#serp_desc_preview').text(desc);
        $('#serp_slug_text').text(slug ? slug : 'course-permalink');
    }

    function createSlug(str) {
        if (!str) return '';
        return str.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    /* 🔄 Step Navigation Control */
    function switchStep(stepNum) {
        if (stepNum < 1 || stepNum > 4) return;
        if (currentStep === stepNum) return;

        var prevStep = currentStep;
        currentStep = stepNum;

        $(`#step-section-${prevStep}`).fadeOut(150, function() {
            $(this).addClass('d-none').css('display', '');
            $(`#step-section-${currentStep}`).removeClass('d-none').hide().fadeIn(150);
        });

        $('.step-node').removeClass('active completed');
        $('.step-node').each(function(index) {
            var nodeIdx = index + 1;
            if (nodeIdx === currentStep) {
                $(this).addClass('active');
            } else if (nodeIdx < currentStep) {
                $(this).addClass('completed');
            }
        });

        $('#btn-prev-step').prop('disabled', currentStep === 1);

        if (currentStep === 4) {
            $('#btn-next-step').addClass('d-none');
            $('#btn-submit-course').removeClass('d-none');
        } else {
            $('#btn-next-step').removeClass('d-none');
            $('#btn-submit-course').addClass('d-none');
        }
    }

    function changeStep(direction) {
        switchStep(currentStep + direction);
    }

    /* ➕ Dynamic Outcomes and Prerequisites Manager */
    function addDynamicRow(containerId, placeholderText, inputName) {
        $(`#${containerId}`).append(`
            <div class="input-group input-group-sm mt-2">
                <input type="text" class="form-control form-dark-input" placeholder="${placeholderText}" name="${inputName}">
                <button class="btn btn-outline-danger text-white px-3" type="button" onclick="$(this).parent().remove()"><i class="bi bi-trash-fill"></i></button>
            </div>
        `);
    }

    function updateDifficultyHelp() {
        var val = $('#course_difficulty').val();
        var txt = "💡 ปูพื้นฐานจากศูนย์ ไม่จำเป็นต้องมีประสบการณ์เขียนโค้ดมาก่อน";
        if (val === 'Intermediate') txt = "💡 เหมาะสำหรับคนที่มีความคุ้นเคยคำสั่งลูป เงื่อนไข และเขียนฟังก์ชันมาบ้างแล้ว";
        if (val === 'Advanced') txt = "💡 เจาะลึกสถาปัตยกรรมขนาดใหญ่ ลอจิกยากซับซ้อน และลุยโครงงานจริง";
        if (val === 'Expert') txt = "💡 เน้นความปลอดภัยระดับสูง การจัดการสิทธิ์โครงสร้างระดับโปรดักชันขององค์กร";
        $('#difficulty_help').text(txt);
    }

    /* 💾 Actions & AI Features */
    function saveCourseDraft() { 
        Alerts.saveDraft(); 
    }

    function generateAICourseDesc() {
        var title = $('#course_title').val();
        if (!title) {
            Alerts.toast('warning', 'กรุณาระบุชื่อคอร์สตรงช่องด้านบนก่อน');
            return;
        }

        Alerts.aiProcess('กำลังสร้างคำอธิบายคอร์สสั้นอัตโนมัติ...', 1200, function() {
            $('#course_short_desc').val(`คอร์สเร่งรัดและเจาะลึกวิชา ${title} มุ่งเน้นการลงมือปฏิบัติจริงเพื่อเสริมทักษะระดับมืออาชีพ`);
            updateSeoPreview();
            Alerts.toast('success', 'AI สร้างคำอธิบายสั้นเรียบร้อยแล้ว');
        });
    }

    function runAIBlueprint() {
        var topic = $('#ai_blueprint_topic').val();
        if (!topic) {
            Alerts.toast('warning', 'กรุณากรอกชื่อเรื่องหรือคีย์เวิร์ด');
            return;
        }

        Alerts.aiProcess('คำนวณและประมวลผลพิมพ์เขียวโครงสร้างคอร์ส...', 1500, function() {
            $('#ai_blueprint_result').removeClass('d-none');
            $('#ai_blueprint_text').html(`✨ [วิเคราะห์และวางรากฐานสำเร็จสำหรับวิชา: <b>${topic}</b>]
• หมวดหมู่แนะนำ: Programming & Software Development
• ความยาวที่แนะนำ: 8 บทใหญ่ (18-20 ชั่วโมง)
• แล็บปฏิบัติการ: Interactive Sandbox 3 จุด
• ผลลัพธ์: สามารถเขียนและ Deploy ชิ้นงานจริงได้พร้อมต่อยอดสายอาชีพ`);
        });
    }

    function applyBlueprintToForm() {
        var topic = $('#ai_blueprint_topic').val();
        $('#course_title').val(topic);
        $('#course_short_desc').val(`คอร์สเรียนฉบับสมบูรณ์ปูพื้นฐานและลงลึกทุกมิติสำคัญสำหรับ ${topic}`);
        $('#aiBlueprintModal').modal('hide');
        switchStep(1);
        updateSeoPreview();
        Alerts.toast('success', 'ปรับใช้ข้อมูลพิมพ์เขียวลงในฟอร์มเรียบร้อยแล้ว');
    }

    function selectTemplate(type) {
        if (type === 'Programming') {
            $('#course_title').val('Core Programming Architecture Mastery');
            $('#course_short_desc').val('เรียนรู้สถาปัตยกรรมซอฟต์แวร์และการเขียนโค้ด clean code ระดับองค์กร');
        } else if (type === 'DataScience') {
            $('#course_title').val('Data Intelligence & Advanced Applied AI Insights');
            $('#course_short_desc').val('การวิเคราะห์ข้อมูลเชิงลึกและการสร้างโมเดล Machine Learning เพื่อการใช้งานจริง');
        } else {
            $('#course_title').val('Enterprise Cybersecurity Architect & DevOps Security');
            $('#course_short_desc').val('เจาะลึกระบบความปลอดภัยเครือข่ายและการทำ DevSecOps ในระบบ Cloud');
        }
        $('#templateModal').modal('hide');
        switchStep(1);
        updateSeoPreview();
        Alerts.toast('info', 'เลือกใช้งาน Template เรียบร้อย');
    }

    function previewCourseModal() {
        var title = $('#course_title').val() || 'ชื่อคอร์สเรียนจะปรากฏที่นี่';
        var desc = $('#course_short_desc').val() || 'คำอธิบายสั้นของคอร์สจะปรากฏตรงส่วนนี้...';
        var price = parseFloat($('#course_price').val()) || 0;
        var sale = parseFloat($('#course_sale_price').val()) || 0;
        var level = $('#course_difficulty').val() || 'Beginner';

        $('#card_preview_title').text(title);
        $('#card_preview_desc').text(desc);
        $('#card_preview_level').text(level);

        if (sale > 0 && sale < price) {
            $('#card_preview_price').text(`฿${sale.toLocaleString()}`);
            $('#card_preview_sale').text(`฿${price.toLocaleString()}`).removeClass('d-none');
        } else {
            $('#card_preview_price').text(price > 0 ? `฿${price.toLocaleString()}` : 'เรียนฟรี');
            $('#card_preview_sale').addClass('d-none');
        }

        var previewModal = new bootstrap.Modal(document.getElementById('previewCourseModalObj'));
        previewModal.show();
    }

    $(document).ready(function() {
        initTagSystem();
        togglePriceFields($('#course_type').val());
        calculateRevenue();
        updateSeoPreview();

        // Drag and drop prevent defaults
        $(document).on('dragenter dragover dragleave drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });

        // Dropzone click triggers
        $('#dropzone-image').on('click', function(e) {
            if (e.target.id !== 'cover_file') {
                $('#cover_file').click();
            }
        });

        $('#dropzone-video').on('click', function(e) {
            if (e.target.id !== 'video_file' && e.target.tagName !== 'VIDEO') {
                $('#video_file').click();
            }
        });

        // Form Submit Handler
        $('#createCourseForm').on('submit', function(e) {
            e.preventDefault();

            var isValid = true;
            $('.required-field').each(function() {
                if ($(this).val().trim() === "") {
                    isValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!isValid) {
                Alerts.toast('warning', 'กรุณากรอกข้อมูลสำคัญที่มีเครื่องหมาย (*) ให้ครบถ้วน');
                switchStep(1);
                return false;
            }

            var formData = new FormData(this);
            formData.append('action', 'save');
            formData.append('user_id', '<?= $user_id ?>');

            var rawPrice = $('#course_price').val();
            var rawSalePrice = $('#course_sale_price').val();

            var priceVal = (rawPrice !== '' && !isNaN(rawPrice)) ? parseFloat(rawPrice) : 0;
            var salePriceVal = (rawSalePrice !== '' && !isNaN(rawSalePrice)) ? parseFloat(rawSalePrice) : 0;

            formData.set('pice', priceVal);
            formData.set('price', priceVal);
            formData.set('course_price', priceVal);
            formData.set('sale_price', salePriceVal);
            formData.set('course_sale_price', salePriceVal);

            // Outcomes & Prereq Arrays
            formData.delete('outcomes');
            formData.delete('outcomes[]');
            formData.delete('prereq');
            formData.delete('prereq[]');

            $('input[name="outcomes[]"]').each(function() {
                if ($(this).val().trim() !== "") formData.append('outcomes[]', $(this).val().trim());
            });
            $('input[name="prereq[]"]').each(function() {
                if ($(this).val().trim() !== "") formData.append('prereq[]', $(this).val().trim());
            });

            $.ajax({
                url: 'services/course.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Alerts.toast('success', response.message || 'บันทึกข้อมูลคอร์สสำเร็จเรียบร้อย!');
                    } else {
                        Alerts.toast('error', response.message || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
                    }
                },
                error: function(xhr, status, error) {
                    Alerts.toast('error', 'เชื่อมต่อเซิร์ฟเวอร์เรียบร้อยแล้ว (Status: ' + xhr.status + ')');
                }
            });
        });
    });
</script>