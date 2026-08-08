<?php
$ROOT_PATH = '../';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php';

$user_id = $_SESSION['user_login']['user_id'] ?? ($_SESSION['user_id'] ?? 0);

// ดึงข้อมูล Category ด้วย PDO
$category_data = [];
if (isset($conn)) {
    try {
        $sql = "SELECT * FROM `category`";
        $result = $conn->query($sql);
        if ($result) {
            $category_data = $result->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        // Handle exception silently to not break the UI, or log it
        error_log("DB Error: " . $e->getMessage());
    }
}
// ⚠️ โหลดผ่านระบบ AJAX ของหน้าหลัก ไม่ต้อง include header.php หรือ footer.php ซ้ำซ้อน
?>

<style>
    /* 🌙 ตัวแปรสำหรับ Dark Mode (Default & Fallback เมื่อเปิดหน้าตรง) */
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
        --cc-modal-bg: #212529;
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
        --cc-box-border: rgba(0, 0, 0, 0.15);
        --cc-preview-bg: rgba(241, 245, 249, 0.8);
        --cc-template-bg: #ffffff;
        --cc-modal-bg: #ffffff;
    }

    /* Safe Container Styles */
    body, .create-course-wrapper {
        animation: fadeIn 0.3s ease-in-out;
        color: var(--cc-text-main);
        background-color: var(--bg-dark-blend);
        border-radius: 12px;
    }

    html[data-theme="light"] .create-course-wrapper h3.text-white,
    html[data-theme="light"] .create-course-wrapper h5.text-white,
    html[data-theme="light"] .create-course-wrapper .text-white:not(.btn),
    html[data-theme="light"] .modal .text-white:not(.btn) {
        color: var(--cc-text-main) !important;
    }
    
    html[data-theme="light"] .create-course-wrapper .text-info:not(.btn),
    html[data-theme="light"] .modal .text-info:not(.btn) {
        color: #0284c7 !important;
    }
    
    html[data-theme="light"] .btn-close-white {
        filter: invert(1) grayscale(100%) brightness(200%);
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
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 600;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        text-shadow: 0 0 5px rgba(168, 85, 247, 0.5);
    }

    .ai-helper-btn:hover {
        background: linear-gradient(135deg, #a855f7, #06b6d4);
        color: #ffffff !important;
        border-color: #38bdf8;
        box-shadow: 0 0 12px rgba(168, 85, 247, 0.6);
        transform: translateY(-1px);
    }

    html[data-theme="light"] .ai-helper-btn {
        background: linear-gradient(135deg, #f3e8ff, #e0f2fe);
        border: 1px solid #a855f7;
        color: #7e22ce !important;
        text-shadow: none;
    }

    html[data-theme="light"] .ai-helper-btn:hover {
        background: linear-gradient(135deg, #a855f7, #0284c7);
        color: #ffffff !important;
    }

    .step-timeline {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
        position: relative;
    }

    .step-timeline::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--cc-box-border);
        z-index: 1;
    }

    .step-node {
        position: relative;
        z-index: 2;
        background: var(--cc-step-bg);
        border: 2px solid var(--cc-step-border);
        border-radius: 50%;
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: var(--cc-text-muted);
        cursor: pointer;
        transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
    }

    .step-node.active {
        border-color: #06b6d4;
        background: #06b6d4;
        color: #ffffff;
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.4);
    }

    .step-node.completed {
        border-color: #22c55e;
        background: #22c55e;
        color: #ffffff;
    }

    .step-label {
        font-size: 0.8rem;
        color: var(--cc-text-muted);
        text-align: center;
        margin-top: 6px;
        font-weight: 500;
    }

    .form-dark-input {
        background: var(--cc-input-bg) !important;
        border: 1px solid var(--cc-input-border) !important;
        color: var(--cc-input-text) !important;
        border-radius: 10px !important;
        padding: 12px 16px !important;
        transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-dark-input:focus {
        border-color: #06b6d4 !important;
        box-shadow: 0 0 10px rgba(6, 182, 212, 0.3) !important;
        outline: none;
    }

    .form-dark-input::placeholder {
        color: var(--cc-input-ph) !important;
        opacity: 1;
    }

    /* 🔻 Custom Dropdown Styles 🔻 */
    .custom-dropdown-wrapper {
        position: relative;
        width: 100%;
        user-select: none;
    }

    .custom-dropdown-btn {
        background: var(--cc-input-bg);
        border: 1px solid var(--cc-input-border);
        color: var(--cc-input-text);
        border-radius: 10px;
        padding: 12px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: all 0.25s ease;
    }

    .custom-dropdown-btn:hover,
    .custom-dropdown-wrapper.active .custom-dropdown-btn {
        border-color: #06b6d4;
        box-shadow: 0 0 10px rgba(6, 182, 212, 0.3);
    }

    .custom-dropdown-menu {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        background: var(--card-bg-premium);
        border: 1px solid var(--cc-box-border);
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        z-index: 1050;
        display: none;
        max-height: 250px;
        overflow-y: auto;
        padding: 6px;
    }

    .custom-dropdown-wrapper.active .custom-dropdown-menu {
        display: block;
        animation: fadeIn 0.2s ease-in-out;
    }

    .custom-dropdown-item {
        padding: 10px 14px;
        border-radius: 6px;
        color: var(--cc-text-main);
        cursor: pointer;
        font-size: 0.9rem;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .custom-dropdown-item:hover {
        background: rgba(6, 182, 212, 0.15);
        color: #06b6d4;
    }

    .custom-dropdown-item.selected {
        background: #06b6d4;
        color: #ffffff !important;
        font-weight: 600;
    }

    /* ซ่อนลูกศรในช่อง Number Input */
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
        transition: color 0.3s ease;
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
    
    html[data-theme="light"] .tag-badge {
        color: #0284c7;
    }

    .tag-badge i {
        cursor: pointer;
        color: #ec4899;
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
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(168, 85, 247, 0.4);
    }

    .image-preview-box {
        border: 2px dashed var(--cc-box-border);
        border-radius: 16px;
        background: var(--cc-preview-bg);
        min-height: 180px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        padding: 20px;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    .image-preview-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        position: absolute;
        top: 0;
        left: 0;
    }

    .image-preview-box span {
        color: var(--cc-text-main) !important;
        font-weight: 500;
    }

    .template-card {
        background: var(--cc-template-bg);
        border: 1px solid var(--cc-box-border);
        border-radius: 12px;
        padding: 18px;
        cursor: pointer;
        transition: border-color 0.2s ease, background-color 0.2s ease;
    }

    .template-card:hover {
        border-color: #a855f7;
        background: rgba(168, 85, 247, 0.1);
    }

    .template-card .text-desc {
        color: var(--cc-text-muted) !important;
    }

    .checkbox-text-container {
        color: var(--cc-text-label) !important;
        font-size: 0.9rem;
    }

    .checkbox-text-container input[type="checkbox"] {
        margin-right: 6px;
        accent-color: #06b6d4;
    }

    .cc-modal-bg {
        background-color: var(--cc-modal-bg) !important;
        border: 1px solid var(--cc-box-border);
    }
    
    .cc-inner-box {
        background-color: var(--cc-step-bg) !important;
        border: 1px solid var(--cc-box-border);
    }
    
    .cc-preview-box {
        background-color: var(--cc-preview-bg);
        color: var(--cc-text-main);
    }

    .btn-cc-outline {
        border: 1px solid var(--cc-box-border);
        color: var(--cc-text-main) !important;
        background: transparent;
        transition: background-color 0.3s ease, border-color 0.3s ease;
        border-radius: 8px;
    }
    
    .btn-cc-outline:hover {
        background: var(--cc-step-bg);
        border-color: #06b6d4;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .media-dropzone.dragover {
        border-color: #06b6d4 !important;
        background: rgba(6, 182, 212, 0.2) !important;
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.4);
    }
</style>

<div class="create-course-wrapper p-3">

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <div>
            <h3 class="text-white fw-bold mb-1"><i class="bi bi-plus-square-fill text-info me-2"></i> สร้างคอร์สใหม่ (Create Course)</h3>
            <div class="text-info small fw-medium">
                ลำดับขั้นตอนการทำงาน: <span class="text-white bg-info bg-opacity-20 px-2 py-0.5 rounded">1. ข้อมูลพื้นฐาน</span> → 2. สื่อและหมวดหมู่ → 3. เป้าหมายเรียน → 4. ตั้งค่าระบบ / ราคา / SEO
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-purple text-white btn-sm px-3" data-bs-toggle="modal" data-bs-target="#aiBlueprintModal">
                <i class="bi bi-cpu-fill me-1"></i> AI Course Blueprint
            </button>
            <button type="button" class="btn btn-cc-outline btn-sm px-3" data-bs-toggle="modal" data-bs-target="#templateModal">
                <i class="bi bi-journal-album me-1"></i> คลัง Template คอร์ส
            </button>
        </div>
    </div>

    <!-- Step Timeline Bar -->
    <div class="step-timeline px-4">
        <div class="d-flex flex-column align-items-center">
            <div class="step-node active" onclick="switchStep(1)">1</div>
            <span class="step-label">ข้อมูลทั่วไป</span>
        </div>
        <div class="d-flex flex-column align-items-center">
            <div class="step-node" id="node-step-2" onclick="switchStep(2)">2</div>
            <span class="step-label">สื่อและหมวดหมู่</span>
        </div>
        <div class="d-flex flex-column align-items-center">
            <div class="step-node" id="node-step-3" onclick="switchStep(3)">3</div>
            <span class="step-label">เป้าหมายเรียน</span>
        </div>
        <div class="d-flex flex-column align-items-center">
            <div class="step-node" id="node-step-4" onclick="switchStep(4)">4</div>
            <span class="step-label">ตั้งค่าระบบ / ราคา / SEO</span>
        </div>
    </div>

    <!-- Course Form -->
    <form id="createCourseForm">

        <!-- STEP 1: ข้อมูลพื้นฐาน -->
        <div class="course-step-section" id="step-section-1">
            <div class="p-4 rounded-4 mb-4" style="background: var(--card-bg-premium); border: 1px solid var(--cc-box-border);">
                <h5 class="fw-bold text-white mb-3"><i class="bi bi-info-circle text-info me-2"></i> 1. ข้อมูลพื้นฐานของคอร์ส</h5>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label form-label-custom">ชื่อคอร์ส <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-dark-input required-field" id="course_title" name="course_title" placeholder="เช่น Advanced Full Stack Web Development with Next.js">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label form-label-custom">ชื่อรอง (Subtitle)</label>
                        <input type="text" class="form-control form-dark-input" id="course_subtitle" name="course_subtitle" placeholder="คำโปรยหัวสั้นๆ กระตุ้นผู้เรียน">
                    </div>

                    <!-- 🔻 Dropdown 1: ภาษาในคอร์ส 🔻 -->
                    <div class="col-12 col-md-6">
                        <label class="form-label form-label-custom">ภาษาในคอร์ส</label>
                        <div class="custom-dropdown-wrapper">
                            <input type="hidden" name="course_language" id="course_language" value="th">
                            <div class="custom-dropdown-btn">
                                <span class="selected-text">ภาษาไทย (Thai)</span>
                                <i class="bi bi-chevron-down"></i>
                            </div>
                            <div class="custom-dropdown-menu">
                                <div class="custom-dropdown-item selected" data-value="th">ภาษาไทย (Thai)</div>
                                <div class="custom-dropdown-item" data-value="en">ภาษาอังกฤษ (English)</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label form-label-custom mb-0">คำอธิบายสั้น</label>
                            <button type="button" class="btn ai-helper-btn" onclick="generateAICourseDesc()">
                                <i class="bi bi-stars"></i> ให้ AI ช่วยแต่งคำอธิบาย
                            </button>
                        </div>
                        <input type="text" class="form-control form-dark-input" id="course_short_desc" name="course_short_desc" placeholder="สรุปคอร์สใน 1 ประโยคเพื่อโชว์ที่การ์ด">
                    </div>
                    <div class="col-12">
                        <label class="form-label form-label-custom">คำอธิบายแบบรายละเอียด</label>
                        <textarea class="form-control form-dark-input" id="course_full_desc" name="course_full_desc" rows="4" placeholder="รายละเอียดหลักสูตร โครงสร้าง และเนื้อหาเจาะลึกที่ผู้เรียนจะได้รับ"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label form-label-custom">Tags คอร์ส (ระบุข้อความแล้วกด Enter เพื่อเพิ่มคีย์เวิร์ด)</label>
                        <input type="text" class="form-control form-dark-input mb-2" id="tag_input" placeholder="เช่น nextjs, react, api">
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
                    <!-- สื่อแนะนำคอร์ส -->
                    <div class="col-12 col-md-6">
                        <h5 class="fw-bold text-white mb-3"><i class="bi bi-image text-info me-2"></i> 2. สื่อแนะนำคอร์ส</h5>

                        <div class="d-flex gap-2 mb-3">
                            <button type="button" class="btn btn-info text-white px-4 fw-medium" id="btn-tab-image" onclick="toggleMediaTab('image')">
                                <i class="bi bi-image me-1"></i> ใส่รูปภาพปก
                            </button>
                            <button type="button" class="btn btn-outline-info text-white px-4 fw-medium" id="btn-tab-video" onclick="toggleMediaTab('video')">
                                <i class="bi bi-camera-video me-1"></i> ใส่วิดีโอแนะนำ
                            </button>
                        </div>

                        <!-- Dropzone Image -->
                        <div id="box-image">
                            <label class="form-label form-label-custom">รูปปกคอร์ส (อัตราส่วนแนะนำ 16:9) <span class="text-danger">*</span></label>
                            <div class="image-preview-box mb-2 media-dropzone" id="dropzone-image">
                                <input type="file" id="cover_file" name="cover_file" class="d-none" accept="image/*" onchange="previewMedia(this, 'image')">
                                <img id="cover_preview" src="" class="d-none" alt="Preview Image">
                                <div class="upload-placeholder text-center">
                                    <i class="bi bi-cloud-arrow-up fs-2 text-cyan mb-2 d-block"></i>
                                    <span>คลิกหรือลากไฟล์ <b>รูปภาพ</b> มาวางที่นี่</span>
                                </div>
                            </div>
                        </div>

                        <!-- Dropzone Video -->
                        <div id="box-video" class="d-none">
                            <label class="form-label form-label-custom">วิดีโอแนะนำ (Intro Video - MP4)</label>
                            <div class="image-preview-box mb-2 media-dropzone" id="dropzone-video">
                                <input type="file" id="video_file" name="video_file" class="d-none" accept="video/mp4,video/webm" onchange="previewMedia(this, 'video')">
                                <video id="video_preview" controls class="d-none w-100" style="max-height: 200px; border-radius: 8px; z-index: 5; position: relative;"></video>
                                <div class="upload-placeholder text-center">
                                    <i class="bi bi-film fs-2 text-purple mb-2 d-block"></i>
                                    <span>คลิกหรือลากไฟล์ <b>วิดีโอ</b> มาวางที่นี่</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- หมวดหมู่และระดับความยาก -->
                    <div class="col-12 col-md-6">
                        <h5 class="fw-bold text-white mb-3"><i class="bi bi-tags text-info me-2"></i> 3 & 4. หมวดหมู่และระดับความยาก</h5>

                        <!-- 🔻 Dropdown 2: หมวดหมู่หลักคอร์ส 🔻 -->
                        <div class="mb-3">
                            <label class="form-label form-label-custom">หมวดหมู่หลักคอร์ส <span class="text-danger">*</span></label>
                            <div class="custom-dropdown-wrapper">
                                <input type="hidden" name="course_category" id="course_category" class="required-field" value="">
                                <div class="custom-dropdown-btn">
                                    <span class="selected-text">-- กรุณาเลือกหมวดหมู่หลัก --</span>
                                    <i class="bi bi-chevron-down"></i>
                                </div>
                                <div class="custom-dropdown-menu">
                                    <div class="custom-dropdown-item selected" data-value="">-- กรุณาเลือกหมวดหมู่หลัก --</div>
                                    <?php if (!empty($category_data)): ?>
                                        <?php foreach ($category_data as $category): ?>
                                            <div class="custom-dropdown-item" data-value="<?= htmlspecialchars($category['id'] ?? ''); ?>">
                                                <?= htmlspecialchars($category['name'] ?? ''); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="custom-dropdown-item" data-value="">ไม่มีข้อมูลหมวดหมู่</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- 🔻 Dropdown 3: ระดับความยากผู้เรียน 🔻 -->
                        <div class="mb-3">
                            <label class="form-label form-label-custom">ระดับความยากผู้เรียน (Difficulty Level)</label>
                            <div class="custom-dropdown-wrapper">
                                <input type="hidden" name="course_difficulty" id="course_difficulty" value="Beginner" onchange="updateDifficultyHelp()">
                                <div class="custom-dropdown-btn">
                                    <span class="selected-text">Beginner (เหมาะสำหรับผู้เริ่มต้น ไม่มีพื้นฐานเลย)</span>
                                    <i class="bi bi-chevron-down"></i>
                                </div>
                                <div class="custom-dropdown-menu">
                                    <div class="custom-dropdown-item selected" data-value="Beginner">Beginner (เหมาะสำหรับผู้เริ่มต้น ไม่มีพื้นฐานเลย)</div>
                                    <div class="custom-dropdown-item" data-value="Intermediate">Intermediate (มีพื้นฐานภาษาคอมพิวเตอร์มาบ้างแล้ว)</div>
                                    <div class="custom-dropdown-item" data-value="Advanced">Advanced (เจาะลึกโปรเจกต์ใหญ่ ลอจิกซับซ้อน)</div>
                                    <div class="custom-dropdown-item" data-value="Expert">Expert (ระดับสถาปัตยกรรมระบบขั้นสูงและ Security)</div>
                                </div>
                            </div>
                            <div id="difficulty_help" class="small text-info mt-2 fw-medium">💡 ปูพื้นฐานจากศูนย์ ไม่จำเป็นต้องมีประสบการณ์เขียนโค้ดมาก่อน</div>
                        </div>

                        <div>
                            <label class="form-label form-label-custom">ระยะเวลาคอร์สโดยประมาณ</label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <input type="number" class="form-control form-dark-input" id="duration_hours" placeholder="ชั่วโมงรวม" name="duration_hours" min="0">
                                </div>
                                <div class="col-4">
                                    <input type="number" class="form-control form-dark-input" id="duration_chapters" placeholder="จำนวนบท" name="duration_chapters" min="0">
                                </div>
                                <div class="col-4">
                                    <input type="text" class="form-control form-dark-input" id="duration_week" placeholder="ชม./สัปดาห์" name="duration_week">
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
                            <h5 class="fw-bold text-white mb-0"><i class="bi bi-bookmark-check text-success me-2"></i> 5. สิ่งที่จะได้เรียนรู้ (Outcomes)</h5>
                            <button type="button" class="btn btn-sm btn-outline-info text-white py-1 px-2" onclick="addDynamicRow('outcomes_list', 'เช่น เขียน REST API ได้อย่างเชี่ยวชาญ','outcomes[]')">+ เพิ่มข้อใหม่</button>
                        </div>
                        <div id="outcomes_list" class="d-flex flex-column gap-2 mt-2">
                            <input type="text" class="form-control form-dark-input" value="เข้าใจแนวคิดและหลักการทำงานเบื้องต้นอย่างรายละเอียด" name="outcomes[]">
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="fw-bold text-white mb-0"><i class="bi bi-card-list text-warning me-2"></i> 6. ความรู้พื้นฐานที่ต้องมีก่อน (Prerequisites)</h5>
                            <button type="button" class="btn btn-sm btn-outline-warning text-white py-1 px-2" onclick="addDynamicRow('prereq_list', 'เช่น HTML / CSS และ JavaScript พื้นฐาน','prereq[]')">+ เพิ่มข้อใหม่</button>
                        </div>
                        <div id="prereq_list" class="d-flex flex-column gap-2 mt-2">
                            <input type="text" class="form-control form-dark-input" value="ไม่มีความรู้พื้นฐานคอมพิวเตอร์ระดับสูงก็สามารถเรียนได้" name="prereq[]">
                        </div>
                    </div>

                    <div class="col-12">
                        <hr style="border-color: var(--cc-box-border);">
                        <div class="row g-3">
                            <!-- 🔻 Dropdown 4: กลุ่มเป้าหมาย 🔻 -->
                            <div class="col-12 col-md-6">
                                <label class="form-label form-label-custom">เหมาะสำหรับกลุ่มเป้าหมายใด (Target Audience)</label>
                                <div class="custom-dropdown-wrapper">
                                    <input type="hidden" name="course_target" id="course_target" value="ผู้เริ่มต้น / นักเรียน นักศึกษา">
                                    <div class="custom-dropdown-btn">
                                        <span class="selected-text">ผู้เริ่มต้น / นักเรียน นักศึกษา</span>
                                        <i class="bi bi-chevron-down"></i>
                                    </div>
                                    <div class="custom-dropdown-menu">
                                        <div class="custom-dropdown-item selected" data-value="ผู้เริ่มต้น / นักเรียน นักศึกษา">ผู้เริ่มต้น / นักเรียน นักศึกษา</div>
                                        <div class="custom-dropdown-item" data-value="โปรแกรมเมอร์ที่ต้องการย้ายหรือเปลี่ยนสายงาน">โปรแกรมเมอร์ที่ต้องการย้ายหรือเปลี่ยนสายงาน</div>
                                        <div class="custom-dropdown-item" data-value="ผู้ทำงานสายตรงที่ต้องการอัปสกิลเทคโนโลยีใหม่">ผู้ทำงานสายตรงที่ต้องการอัปสกิลเทคโนโลยีใหม่</div>
                                    </div>
                                </div>
                            </div>

                            <!-- 🔻 Dropdown 5: แผนอาชีพ 🔻 -->
                            <div class="col-12 col-md-6">
                                <label class="form-label form-label-custom">เชื่อมโยงกับแผนอาชีพ (Career Path)</label>
                                <div class="custom-dropdown-wrapper">
                                    <input type="hidden" name="course_career" id="course_career" value="Full Stack Developer">
                                    <div class="custom-dropdown-btn">
                                        <span class="selected-text">Full Stack Developer</span>
                                        <i class="bi bi-chevron-down"></i>
                                    </div>
                                    <div class="custom-dropdown-menu">
                                        <div class="custom-dropdown-item selected" data-value="Full Stack Developer">Full Stack Developer</div>
                                        <div class="custom-dropdown-item" data-value="Front-end Developer">Front-end Developer</div>
                                        <div class="custom-dropdown-item" data-value="Back-end Developer">Back-end Developer</div>
                                        <div class="custom-dropdown-item" data-value="Data Scientist / AI Engineer">Data Scientist / AI Engineer</div>
                                        <div class="custom-dropdown-item" data-value="Cloud & DevOps Engineer">Cloud & DevOps Engineer</div>
                                        <div class="custom-dropdown-item" data-value="Cybersecurity Analyst">Cybersecurity Analyst</div>
                                    </div>
                                </div>
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
                    <!-- 🔻 Dropdown 6: ประเภทคอร์สและการคิดเงิน 🔻 -->
                    <div class="col-12 col-md-4">
                        <label class="form-label form-label-custom">ประเภทคอร์สและการคิดเงิน</label>
                        <div class="custom-dropdown-wrapper">
                            <input type="hidden" name="course_type" id="course_type" value="Paid" onchange="togglePriceFields(this.value)">
                            <div class="custom-dropdown-btn">
                                <span class="selected-text">เสียเงินซื้อขาดรายคอร์ส (Paid)</span>
                                <i class="bi bi-chevron-down"></i>
                            </div>
                            <div class="custom-dropdown-menu">
                                <div class="custom-dropdown-item selected" data-value="Paid">เสียเงินซื้อขาดรายคอร์ส (Paid)</div>
                                <div class="custom-dropdown-item" data-value="Free">เปิดให้เข้าเรียนฟรี (Free)</div>
                                <div class="custom-dropdown-item" data-value="Premium">Premium รวมในแพ็กเกจสมาชิก</div>
                                <div class="custom-dropdown-item" data-value="Private">ส่วนบุคคลเฉพาะกลุ่ม (Private / Internal)</div>
                            </div>
                        </div>
                    </div>

                    <!-- 🔻 Dropdown 7: สถานะเปิดรับชม 🔻 -->
                    <div class="col-12 col-md-4">
                        <label class="form-label form-label-custom">สถานะเปิดรับชม (Visibility)</label>
                        <div class="custom-dropdown-wrapper">
                            <input type="hidden" name="course_visibility" id="course_visibility" value="Draft">
                            <div class="custom-dropdown-btn">
                                <span class="selected-text">Draft (บันทึกร่างภายในสตูดิโอผู้สอน)</span>
                                <i class="bi bi-chevron-down"></i>
                            </div>
                            <div class="custom-dropdown-menu">
                                <div class="custom-dropdown-item selected" data-value="Draft">Draft (บันทึกร่างภายในสตูดิโอผู้สอน)</div>
                                <div class="custom-dropdown-item" data-value="Public">Public (เปิดสาธารณะ ค้นหาเจอบนหน้าเว็บ)</div>
                                <div class="custom-dropdown-item" data-value="Unlisted">Unlisted (เข้าเรียนได้ผ่านลิงก์ตรงเท่านั้น)</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label form-label-custom">เชิญอาจารย์ร่วมสอน (Collaboration)</label>
                        <input type="email" class="form-control form-dark-input" id="collab_email" placeholder="ระบุอีเมลผู้ช่วยสอนหรือผู้ตรวจคอร์ส" name="collab_email">
                    </div>

                    <!-- 💵 ช่องกรอกจำนวนเงิน/ราคาคอร์ส -->
                    <div class="col-12 col-md-6 price-field-group">
                        <label class="form-label form-label-custom">ราคาปกติ (บาท) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary border-opacity-30 text-info fw-bold">฿</span>
                            <input type="number" class="form-control form-dark-input" id="course_price" name="course_price" placeholder="เช่น 1500" min="0" value="1500">
                        </div>
                    </div>

                    <div class="col-12 col-md-6 price-field-group">
                        <label class="form-label form-label-custom">ราคาโปรโมชัน / ส่วนลด (บาท) <span class="text-white-50 small">(เว้นว่างหากไม่มี)</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary border-opacity-30 text-warning fw-bold">฿</span>
                            <input type="number" class="form-control form-dark-input" id="course_sale_price" name="course_sale_price" placeholder="เช่น 990" min="0" value="990">
                        </div>
                    </div>

                    <!-- Certificate Config -->
                    <div class="col-12">
                        <div class="p-3 rounded-3 cc-inner-box mt-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="cert_enable" name="cert_enable" value="1" checked onchange="$('#cert_condition_box').toggle()">
                                <label class="form-check-label text-white fw-bold small" for="cert_enable">เปิดใช้งานใบรับรองหลังเรียนจบหลักสูตร (Certificate of Completion)</label>
                            </div>
                            <div id="cert_condition_box" class="mt-3 row g-2 checkbox-text-container">
                                <span class="text-info fw-medium small mb-1"><i class="bi bi-gear"></i> กำหนดเงื่อนไขการรับใบประกาศนียบัตร:</span>
                                <div class="col-auto"><label><input type="checkbox" checked name="cert_condition_quiz" value="1"> ผู้เรียนต้องทำผ่านแบบทดสอบ (Quiz) ทั้งหมด</label></div>
                                <div class="col-auto ms-md-4"><label><input type="checkbox" name="cert_condition_lab" value="1"> ผู้เรียนต้องผ่านการทดสอบแล็บปฏิบัติการ (Coding Lab)</label></div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Settings -->
                    <div class="col-12">
                        <h5 class="fw-bold text-white mb-2 mt-3"><i class="bi bi-search text-cyan me-2"></i> การตั้งค่าค้นหาทางการตลาด (SEO Settings)</h5>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label form-label-custom">Friendly URL Slug</label>
                                <input type="text" class="form-control form-dark-input" id="seo_slug" placeholder="เช่น advanced-nextjs-course" name="seo_slug">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label form-label-custom">Meta Title</label>
                                <input type="text" class="form-control form-dark-input" id="seo_title" placeholder="หัวข้อไตเติ้ลเมื่อแสดงผลบน Google Search" name="seo_title">
                            </div>
                            <div class="col-12">
                                <label class="form-label form-label-custom">Meta Description</label>
                                <textarea class="form-control form-dark-input" id="seo_desc" rows="2" placeholder="เขียนสรุปรายละเอียดความยาวไม่เกิน 160 ตัวอักษร เพื่อให้ Search Engine ดึงข้อมูลไปแสดง" name="seo_desc"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <button type="button" class="btn btn-cc-outline px-3" id="btn-prev-step" onclick="changeStep(-1)" disabled>
                    <i class="bi bi-arrow-left me-1"></i> ย้อนกลับ
                </button>
            </div>

            <div class="d-flex gap-2">
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
                    <i class="bi bi-check-circle-fill me-1"></i> บันทึกคอร์สเรียบร้อย <i class="bi bi-chevron-right ms-1"></i>
                </button>
            </div>
        </div>

    </form>
</div>

<!-- ==========================================
     [MODAL 1] AI Course Blueprint Assistant
     ========================================== -->
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
                    <div id="ai_blueprint_text" class="small lh-base text-pre-wrap mb-3 p-2 rounded cc-preview-box"></div>
                    <button type="button" class="btn btn-sm btn-info text-white px-3" onclick="applyBlueprintToForm()">นำข้อมูลพิมพ์เขียวไปจัดลงฟอร์มทันที</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================
     [MODAL 2] Course Template Library
     ========================================== -->
<div class="modal fade" id="templateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content text-white cc-modal-bg rounded-4">
            <div class="modal-header p-3" style="border-bottom: 1px solid var(--cc-box-border);">
                <h5 class="modal-title fw-bold text-white"><i class="bi bi-journal-album text-info"></i> คลังเทมเพลตสำหรับเริ่มสร้างคอร์ส</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 d-flex flex-column gap-3">
                <div class="template-card" onclick="selectTemplate('Programming')">
                    <div class="fw-bold text-success mb-1"><i class="bi bi-code-slash"></i> Programming Course Template</div>
                    <div class="small text-desc">ตั้งโครงสร้างมาตรฐาน มีแล็บโค้ดดิ้งสำเร็จรูป 4 จุด พร้อมห้องแบบทดสอบวัดผล</div>
                </div>
                <div class="template-card" onclick="selectTemplate('DataScience')">
                    <div class="fw-bold text-info mb-1"><i class="bi bi-bar-chart-line-fill"></i> Data Science & AI Template</div>
                    <div class="small text-desc">เน้นบทเรียนวิเคราะห์ข้อมูล การอ่านกราฟสถิติ และสรุปทฤษฎีแมชชีนเลิร์นนิ่ง</div>
                </div>
                <div class="template-card" onclick="selectTemplate('Cyber')">
                    <div class="fw-bold text-danger mb-1"><i class="bi bi-shield-lock-fill"></i> Cybersecurity & Cloud Template</div>
                    <div class="small text-desc">เหมาะสำหรับการสอน Network, DevOps เน้นโครงสร้างการทดลอง Virtual Labs</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // 🛡️ Fallback สำหรับ InstructorAlerts
    var Alerts = window.InstructorAlerts || {
        toast: function(type, msg) { alert('[' + type.toUpperCase() + '] ' + msg); },
        saveDraft: function() { alert('ทำการบันทึกร่างเรียบร้อยแล้ว'); },
        aiProcess: function(msg, time, callback) { setTimeout(callback, time); }
    };

    var currentStep = 1;
    var tagsArray = [];

    // ⚡ ระบบการทำงานของ Custom Dropdown ⚡
    $(document).on('click', '.custom-dropdown-btn', function(e) {
        e.stopPropagation();
        var $wrapper = $(this).closest('.custom-dropdown-wrapper');
        $('.custom-dropdown-wrapper').not($wrapper).removeClass('active');
        $wrapper.toggleClass('active');
    });

    $(document).on('click', '.custom-dropdown-item', function(e) {
        e.stopPropagation();
        var $item = $(this);
        var $wrapper = $item.closest('.custom-dropdown-wrapper');
        var val = $item.data('value');
        var text = $item.text();

        $wrapper.find('.selected-text').text(text);
        var $hiddenInput = $wrapper.find('input[type="hidden"]');
        $hiddenInput.val(val).trigger('change');

        $wrapper.find('.custom-dropdown-item').removeClass('selected');
        $item.addClass('selected');

        $wrapper.removeClass('active');
    });

    $(document).on('click', function() {
        $('.custom-dropdown-wrapper').removeClass('active');
    });

    // 💡 ซ่อน/แสดงช่องใส่ราคา ตามประเภทคอร์ส
    function togglePriceFields(type) {
        if (type === 'Free') {
            $('.price-field-group').fadeOut(200);
            $('#course_price').val(0);
            $('#course_sale_price').val(0);
        } else {
            $('.price-field-group').fadeIn(200);
        }
    }

    // 1. สลับแท็บรูป/วิดีโอ
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

    // 2. Preview ไฟล์
    function previewMedia(input, type) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            var file = input.files[0];

            reader.onload = function(e) {
                if (type === 'image') {
                    $('#cover_preview').attr('src', e.target.result).removeClass('d-none');
                    $('#dropzone-image .upload-placeholder').addClass('d-none');
                } else if (type === 'video') {
                    $('#video_preview').attr('src', e.target.result).removeClass('d-none');
                    $('#dropzone-video .upload-placeholder').addClass('d-none');
                }
            }
            reader.readAsDataURL(file);
        }
    }

    $(document).ready(function() {
        togglePriceFields($('#course_type').val());

        $(document).on('dragenter dragover dragleave drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });

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

        $('.media-dropzone').on('dragover', function() {
            $(this).addClass('border-primary').css('background', 'rgba(6, 182, 212, 0.2)');
        });

        $('.media-dropzone').on('dragleave drop', function() {
            $(this).removeClass('border-primary').css('background', '');
        });

        $('#dropzone-image').on('drop', function(e) {
            var files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                $('#cover_file')[0].files = files;
                previewMedia($('#cover_file')[0], 'image');
            }
        });

        $('#dropzone-video').on('drop', function(e) {
            var files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                $('#video_file')[0].files = files;
                previewMedia($('#video_file')[0], 'video');
            }
        });

        // Tags
        $('#tag_input').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                var tag = $(this).val().trim().toLowerCase();
                if (tag && !tagsArray.includes(tag)) {
                    tagsArray.push(tag);
                    renderTags();
                }
                $(this).val('');
            }
        });

        // Submit Form via AJAX
        $('#createCourseForm').on('submit', function(e) {
            e.preventDefault();

            var isValid = true;
            $('.required-field').each(function() {
                if ($(this).val().trim() === "") {
                    isValid = false;
                    $(this).closest('.custom-dropdown-wrapper').find('.custom-dropdown-btn').addClass('border-danger');
                    $(this).addClass('is-invalid');
                } else {
                    $(this).closest('.custom-dropdown-wrapper').find('.custom-dropdown-btn').removeClass('border-danger');
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

            // ส่งค่าราคาครอบคลุมทุกตัวแปร ป้องกัน DB รับค่า NULL
            var priceVal = $('#course_price').val() || '0';
            var salePriceVal = $('#course_sale_price').val() || '0';

            formData.append('pice', priceVal);
            formData.append('price', priceVal);
            formData.append('course_price', priceVal);
            formData.append('sale_price', salePriceVal);
            formData.append('course_sale_price', salePriceVal);

            // จัดการข้อมูล Outcomes และ Prereq
            formData.delete('outcomes');
            formData.delete('outcomes[]');
            formData.delete('prereq');
            formData.delete('prereq[]');

            $('input[name="outcomes"], input[name="outcomes[]"]').each(function() {
                if ($(this).val().trim() !== "") formData.append('outcomes[]', $(this).val().trim());
            });
            $('input[name="prereq"], input[name="prereq[]"]').each(function() {
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
                        Alerts.toast('error', response.message || 'เกิดข้อผิดพลาดในการบันทึก');
                    }
                },
                error: function(xhr, status, error) {
                    Alerts.toast('error', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้ (Status: ' + xhr.status + ')');
                }
            });
        });
    });

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

    function saveCourseDraft() {
        Alerts.saveDraft();
    }

    function generateAICourseDesc() {
        var title = $('#course_title').val();
        if (!title) {
            Alerts.toast('warning', 'กรุณาระบุชื่อคอร์สตรงช่องด้านบนก่อน');
            return;
        }

        Alerts.aiProcess('สร้างคำอธิบายคอร์สสั้นอัตโนมัติ', 1500, function() {
            $('#course_short_desc').val(`คอร์สเร่งรัดและเจาะลึกวิชา ${title} มุ่งเน้นการลงมือปฏิบัติจริงเพื่อเสริมทักษะระดับมืออาชีพ`);
        });
    }

    function runAIBlueprint() {
        var topic = $('#ai_blueprint_topic').val();
        if (!topic) {
            Alerts.toast('warning', 'กรุณากรอกชื่อเรื่องหรือคีย์เวิร์ด');
            return;
        }

        Alerts.aiProcess('คำนวณและประมวลผลพิมพ์เขียวโครงสร้างคอร์ส', 1800, function() {
            $('#ai_blueprint_result').removeClass('d-none');
            $('#ai_blueprint_text').html(`✨ [วิเคราะห์และวางรากฐานสำเร็จสำหรับวิชา: ${topic}]
• หมวดหมู่แนะนำ: Programming / Software Development
• ความยาวที่แนะนำ: 8 บทใหญ่ (16-20 ชั่วโมง)
• แล็บปฏิบัติการ: Coding Sandbox 3 จุด
• ผลลัพธ์: สามารถเขียนและ Deploy ชิ้นงานจริงได้`);
        });
    }

    function applyBlueprintToForm() {
        var topic = $('#ai_blueprint_topic').val();
        $('#course_title').val(topic);
        $('#course_short_desc').val(`คอร์สเรียนฉบับสมบูรณ์ปูพื้นฐานและลงลึกทุกมิติสำคัญสำหรับ ${topic}`);
        $('#aiBlueprintModal').modal('hide');
        switchStep(1);
        Alerts.toast('success', 'ปรับใช้ข้อมูลพิมพ์เขียวลงในฟอร์มเรียบร้อยแล้ว');
    }

    function selectTemplate(type) {
        if (type === 'Programming') {
            $('#course_title').val('Core Programming Architecture Mastery');
        } else if (type === 'DataScience') {
            $('#course_title').val('Data Intelligence & Advanced Applied AI Insights');
        } else {
            $('#course_title').val('Enterprise Cybersecurity Architect & DevOps Security');
        }
        $('#templateModal').modal('hide');
        switchStep(1);
        Alerts.toast('info', 'เลือกใช้งาน Template เรียบร้อย');
    }

    function previewCourseModal() {
        var title = $('#course_title').val() || '[ยังไม่ได้ระบุชื่อคอร์ส]';
        var price = $('#course_price').val() || 0;
        
        Alerts.toast('info', `ตัวอย่างคอร์ส: ${title} (ราคา ฿${price})`);
    }
</script>