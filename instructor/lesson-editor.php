<?php
$ROOT_PATH = '../';
// [Security Framework & Session Verification]
if (session_status() === PHP_SESSION_NONE) { session_start(); }
// ห้ามใส่ include '../includes/header.php' หรือ footer เด็ดขาด เพราะหน้า instructor.php โหลดให้แล้ว
?>

<style>
    /* Scope Editor Styles ให้เข้ากับ Premium Dark/Light Theme */
    .editor-container {
        animation: fadeInEditor 0.35s ease-in-out;
    }
    
    /* ดึงตัวแปร --card-bg และ --card-border มาใช้ รองรับการสลับโหมดอัตโนมัติ */
    .editor-section-card {
        background: var(--card-bg, rgba(16, 24, 48, 0.65));
        border: 1px solid var(--card-border, rgba(255, 255, 255, 0.06));
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        backdrop-filter: blur(10px);
    }
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--body-text, #f8fafc);
        border-bottom: 1px solid var(--card-border, rgba(255, 255, 255, 0.08));
        padding-bottom: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-title i {
        color: #06b6d4;
    }

    /* High Contrast Legibility Controls */
    .editor-container .text-muted,
    .editor-container p.text-muted,
    .editor-subtitle {
        color: rgba(226, 232, 240, 0.75) !important;
        font-size: 0.88rem;
    }
    .editor-container .text-white-50 {
        color: rgba(241, 245, 249, 0.85) !important;
    }

    /* 🛠️ ล้างระบบกล่องจม: แก้ไข Input, Select, Textarea ทุกกล่องในหน้าจอ */
    .form-control-premium {
        background: var(--nav-input-bg, rgba(15, 23, 42, 0.8)) !important;
        border: 1px solid var(--nav-input-border, rgba(255, 255, 255, 0.12)) !important;
        color: var(--body-text, #f8fafc) !important;
        border-radius: 10px;
        padding: 10px 14px !important;
        line-height: 1.6 !important;
        min-height: 46px;
        transition: all 0.3s ease;
    }
    .form-control-premium:focus {
        border-color: #06b6d4 !important;
        box-shadow: 0 0 0 0.25rem rgba(6, 182, 212, 0.15) !important;
        background: var(--card-bg, #0f172a) !important;
    }

    /* ✨ FIX: แก้ไขปุ่ม Choose File เอียง และจัด Alignment กึ่งกลางสมดุล 100% */
    input[type="file"].form-control-premium {
        display: flex !important;
        align-items: center !important;
        padding: 6px 12px !important;
    }
    input[type="file"].form-control-premium::file-selector-button,
    input[type="file"].form-control-premium::-webkit-file-upload-button {
        background: rgba(6, 182, 212, 0.15) !important;
        color: #06b6d4 !important;
        border: 1px solid rgba(6, 182, 212, 0.3) !important;
        border-radius: 6px;
        padding: 6px 14px !important;
        margin-right: 12px !important;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        line-height: 1 !important;
        transition: all 0.2s ease-in-out;
    }
    input[type="file"].form-control-premium::file-selector-button:hover,
    input[type="file"].form-control-premium::-webkit-file-upload-button:hover {
        background: #06b6d4 !important;
        color: #ffffff !important;
    }
    
    textarea.form-control-premium {
        min-height: 90px !important;
    }

    /* Dropdown Select */
    select.form-control-premium {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 14px center !important;
        background-size: 12px 12px !important;
        padding-right: 40px !important;
    }

    .form-label-premium {
        color: #e2e8f0 !important; 
        font-weight: 500;
        margin-bottom: 6px;
        font-size: 0.88rem;
    }
    .form-control-premium::placeholder {
        color: #94a3b8 !important;
    }

    /* ปรับแต่งปุ่มกล่อง Interactive Buttons */
    .btn-interactive-item {
        background: var(--nav-input-bg, rgba(15, 23, 42, 0.6)) !important;
        border: 1px solid var(--nav-input-border, rgba(255, 255, 255, 0.1)) !important;
        color: var(--body-text, #f8fafc) !important;
        border-radius: 10px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        text-align: left;
        font-size: 0.88rem;
        transition: all 0.25s ease;
    }
    .btn-interactive-item:hover {
        background: rgba(6, 182, 212, 0.12) !important;
        border-color: rgba(6, 182, 212, 0.4) !important;
        color: #06b6d4 !important;
        transform: translateY(-1px);
    }

    /* Checkbox Card */
    .custom-checkbox-card {
        background: var(--nav-input-bg, rgba(15, 23, 42, 0.6));
        border: 1px solid var(--nav-input-border, rgba(255, 255, 255, 0.1));
        border-radius: 10px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        user-select: none;
        transition: all 0.2s ease;
    }
    .custom-checkbox-card:hover {
        border-color: rgba(6, 182, 212, 0.3);
        background: rgba(15, 23, 42, 0.8);
    }
    .custom-checkbox-card .form-check-input {
        width: 18px !important;
        height: 18px !important;
        margin: 0 !important;
        cursor: pointer;
        flex-shrink: 0;
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.25);
    }
    .custom-checkbox-card .form-check-input:checked {
        background-color: #06b6d4 !important;
        border-color: #06b6d4 !important;
    }
    .custom-checkbox-card label,
    .custom-checkbox-card span {
        cursor: pointer;
        margin: 0;
        font-size: 0.9rem;
        color: var(--body-text, #f8fafc);
    }

    .block-unit-item {
        background: var(--nav-dropdown-bg, rgba(0, 0, 0, 0.3));
        border: 1px solid var(--nav-dropdown-border, rgba(255, 255, 255, 0.1));
        border-radius: 12px;
        padding: 18px;
        margin-bottom: 12px;
        cursor: grab;
    }
    .block-unit-item:active { cursor: grabbing; }
    
    .rich-editor-toolbar .btn {
        color: var(--nav-text-muted, #cbd5e1);
        border-color: var(--nav-dropdown-border, rgba(255, 255, 255, 0.1));
        padding: 6px 12px;
    }
    .rich-editor-toolbar .btn:hover {
        color: var(--nav-dropdown-hover-text, #fff);
        background: var(--nav-dropdown-hover-bg, rgba(255, 255, 255, 0.08));
    }

    /* ☀️ LIGHT MODE OVERRIDES */
    html[data-theme="light"] .editor-container .text-white,
    html[data-theme="light"] .modal .text-white { color: #0f172a !important; }
    
    html[data-theme="light"] .editor-container .text-muted,
    html[data-theme="light"] .editor-container p.text-muted,
    html[data-theme="light"] .editor-subtitle { color: #475569 !important; }
    
    html[data-theme="light"] .editor-container .text-white-50,
    html[data-theme="light"] .modal .text-white-50 { color: #64748b !important; }
    
    html[data-theme="light"] .editor-container .bg-dark { background-color: #f1f5f9 !important; }
    html[data-theme="light"] .editor-container .bg-black { background-color: #e2e8f0 !important; }
    
    html[data-theme="light"] .editor-container .btn-dark { 
        background-color: #ffffff !important; 
        color: #0f172a !important; 
        border-color: #cbd5e1 !important; 
    }
    html[data-theme="light"] .editor-container .btn-dark:hover { background-color: #f1f5f9 !important; }
    html[data-theme="light"] .editor-container .border-secondary { border-color: #cbd5e1 !important; }
    html[data-theme="light"] .form-label-premium { color: #334155 !important; }

    /* UI Preview Modal */
    html[data-theme="light"] #livePreviewModal .modal-content { background-color: #ffffff !important; color: #0f172a !important; border-color: #e2e8f0 !important; }
    html[data-theme="light"] #livePreviewModal .modal-header { background-color: #f8fafc !important; border-bottom-color: #e2e8f0 !important; }
    html[data-theme="light"] #livePreviewModal .modal-body { background-color: #f1f5f9 !important; }
    html[data-theme="light"] #livePreviewModal .bg-dark { background-color: #ffffff !important; }
    html[data-theme="light"] #livePreviewModal .bg-black { background-color: #e2e8f0 !important; }
    html[data-theme="light"] #preview-viewport-box { background-color: #ffffff !important; border-color: #cbd5e1 !important; }
    html[data-theme="light"] .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }

    @keyframes fadeInEditor {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="editor-container">
    <!-- Top Action Info -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="text-white fw-bold mb-1"><i class="bi bi-pencil-square text-cyan me-2"></i>ระบบสร้างและแก้ไขบทเรียน (Lesson Editor)</h3>
            <p class="editor-subtitle mb-0">ปรับแต่งเนื้อหา วิดีโอ แล็บ ควิซ และใช้ระบบ AI ช่วยตรวจขัดเกลาเนื้อหาการเรียนรู้</p>
        </div>
        <div id="validation-quick-alert" class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 p-2 font-monospace">
            <i class="bi bi-shield-exclamation me-1"></i> 19. Validation: ตรวจพบลิงก์ภายนอกยังไม่ได้ทดสอบ
        </div>
    </div>

    <form id="lessonMasterForm" onsubmit="event.preventDefault();">
        
        <!-- ==========================================
             1. LESSON INFORMATION
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title">
                <i class="bi bi-info-circle-fill"></i> 1. Lesson Information (ข้อมูลพื้นฐานบทเรียน)
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label form-label-premium">ชื่อบทเรียน</label>
                    <input type="text" class="form-control form-control-premium" id="lesson_title" value="บทที่ 3: โครงสร้างและสโคปของตัวแปรใน Modern Programming">
                </div>
                <div class="col-md-3">
                    <label class="form-label form-label-premium">Chapter (บทหลัก)</label>
                    <select class="form-select form-control-premium">
                        <option>Chapter 1: Getting Started</option>
                        <option selected>Chapter 2: Variables & Syntax Core</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label form-label-premium">ลำดับบทเรียน (Order)</label>
                    <input type="number" class="form-control form-control-premium" value="3">
                </div>
                <div class="col-md-6">
                    <label class="form-label form-label-premium">คำอธิบายรายวิชาย่อย</label>
                    <textarea class="form-control form-control-premium" rows="2">เจาะลึกความต่างของการประกาศตัวแปรในขอบเขตการทำงานแบบต่างๆ</textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label form-label-premium">ประเภทบทเรียน</label>
                    <select class="form-select form-control-premium">
                        <option>ทฤษฎีทั่วไป (Theory Lecture)</option>
                        <option selected>ปฏิบัติการแล็บ (Lab Practical)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label form-label-premium">สถานะ (Status)</label>
                    <select class="form-select form-control-premium text-warning fw-bold">
                        <option value="draft" selected>🟡 Draft (แบบร่าง)</option>
                        <option value="published">🟢 Published (เผยแพร่แล้ว)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- ==========================================
             ⭐ FEATURE 1: BLOCK-BASED LESSON BUILDER
             ========================================== -->
        <div class="editor-section-card border border-info border-opacity-20">
            <div class="section-title text-cyan">
                <i class="bi bi-layers-half"></i> ⭐ Block-based Lesson Builder (ตัวจัดเรียงบล็อกเนื้อหาแบบ Dynamic)
            </div>
            <p class="editor-subtitle mt-0 mb-3">กดเพิ่มบล็อกด้านล่างเพื่อทำการแทรกกล่องเนื้อหาและลากจัดตำแหน่งได้อย่างอิสระ</p>
            <div class="d-flex flex-wrap gap-2 mb-3">
                <button type="button" class="btn btn-sm btn-dark border border-secondary" onclick="addNewContentBlock('text')">📝 Text Block</button>
                <button type="button" class="btn btn-sm btn-dark border border-secondary" onclick="addNewContentBlock('video')">🎥 Video Block</button>
                <button type="button" class="btn btn-sm btn-dark border border-secondary" onclick="addNewContentBlock('code')">💻 Code Block</button>
                <button type="button" class="btn btn-sm btn-dark border border-secondary" onclick="addNewContentBlock('quiz')">🧪 Quiz Block</button>
                <button type="button" class="btn btn-sm btn-dark border border-secondary" onclick="addNewContentBlock('lab')">🖥️ Lab Block</button>
            </div>

            <div id="block-builder-stage">
                <div class="block-unit-item" id="sample-rich-editor-block">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-bold text-white-50 font-monospace"><i class="bi bi-grid-3x2-gap-fill me-2"></i>2. Rich Text Editor Content Block</span>
                        <span class="badge bg-secondary small">Default Text Engine</span>
                    </div>
                    <div class="btn-toolbar rich-editor-toolbar bg-dark p-2 rounded-top border border-secondary border-opacity-30 gap-1" role="toolbar">
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 fw-bold text-white">H1</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 fw-bold text-white">H2</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2"><i class="bi bi-type-bold"></i></button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2"><i class="bi bi-type-italic"></i></button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2"><i class="bi bi-list-ul"></i></button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2"><i class="bi bi-list-ol"></i></button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2"><i class="bi bi-table"></i></button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2"><i class="bi bi-image"></i></button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2"><i class="bi bi-link-45deg"></i></button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2"><i class="bi bi-quote"></i></button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 font-monospace" title="LaTeX Equation">$$ LaTeX</button>
                    </div>
                    <textarea class="form-control form-control-premium rounded-0 rounded-bottom border-top-0 font-monospace small" rows="4" placeholder="ป้อนเนื้อหาบทเรียน รองรับย่อหน้า โค้ด ตาราง รูปภาพ หรือสมการคณิตศาสตร์ภายนอก เช่น $E = mc^2$ ..."></textarea>
                </div>
            </div>
        </div>

        <!-- ==========================================
             3. VIDEO EDITOR
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title">
                <i class="bi bi-camera-video-fill text-warning"></i> 3. Video Editor (จัดการระบบวิดีโอการสอน)
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label form-label-premium">Upload Video Source File</label>
                    <input type="file" class="form-control form-control-premium">
                </div>
                <div class="col-md-6">
                    <label class="form-label form-label-premium">หรือ Embed Link วิดีโอภายนอก (YouTube / Vimeo)</label>
                    <input type="text" class="form-control form-control-premium" placeholder="https://www.youtube.com/watch?v=...">
                </div>
                <div class="col-md-4">
                    <label class="form-label form-label-premium">รูปภาพหน้าปกวิดีโอ (Thumbnail)</label>
                    <input type="file" class="form-control form-control-premium">
                </div>
                <div class="col-md-4">
                    <label class="form-label form-label-premium">ไฟล์คำบรรยายซับไตเติล (Subtitle SRT / VTT)</label>
                    <input type="file" class="form-control form-control-premium">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-info w-100 btn-premium-action" style="min-height: 46px;" onclick="alert('จำลองการเปิดเล่นวิดีโอ Preview Video Player')"><i class="bi bi-play-circle me-1"></i> Preview Video</button>
                </div>
            </div>
        </div>

        <!-- ==========================================
             4. DOCUMENT MANAGEMENT
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title">
                <i class="bi bi-file-earmark-arrow-up-fill text-success"></i> 4. Document Management (อัปโหลดและจัดการเอกสาร)
            </div>
            <p class="editor-subtitle mt-0">รองรับระบบประมวลผลไฟล์สเปกตรัมกว้าง: PDF, Slide, ZIP, Source Code, Dataset, Image</p>
            <div class="p-4 border border-dashed border-secondary border-opacity-40 rounded-3 text-center bg-dark bg-opacity-20">
                <i class="bi bi-cloud-arrow-up fs-2 text-muted mb-2 d-block"></i>
                <span class="text-white-50 small d-block mb-2">ลากไฟล์เอกสารมาวางที่นี่ หรือคลิกเพื่อเลือกอัปโหลดไฟล์เข้าระบบคลาวด์คอร์ส</span>
                <input type="file" class="form-control form-control-premium mx-auto" style="max-width: 380px;" multiple>
            </div>
        </div>

        <!-- ==========================================
             5. CODE BLOCK
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title">
                <i class="bi bi-code-square text-info"></i> 5. Code Block (แทรกตัวอย่างซอร์สโค้ด)
            </div>
            <div class="row g-2 mb-2 align-items-center">
                <div class="col-md-4">
                    <select class="form-select form-control-premium small">
                        <option>PHP</option><option selected>JavaScript</option><option>Python</option><option>Java</option><option>C</option><option>C++</option><option>SQL</option><option>HTML/CSS</option>
                    </select>
                </div>
                <div class="col-md-8 text-end">
                    <button type="button" class="btn btn-sm btn-outline-light text-cyan border-secondary border-opacity-40 py-2" onclick="alert('คัดลอกโค้ดต้นแบบลงคลิปบอร์ดแล้ว!')"><i class="bi bi-copy me-1"></i> Copy Code Button</button>
                </div>
            </div>
            <textarea class="form-control form-control-premium font-monospace text-cyan small" rows="3">const currentScope = "Modern Block Scope Logic";&#10;console.log(`Executing within ${currentScope}`);</textarea>
        </div>

        <!-- ==========================================
             6. INTERACTIVE CONTENT
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title">
                <i class="bi bi-braces-asterisk text-purple"></i> 6. Interactive Content (เพิ่มเนื้อหาแบบโต้ตอบ)
            </div>
            <div class="row g-2.5">
                <div class="col-6 col-md-4"><button type="button" class="btn-interactive-item"><i class="bi bi-menu-button-wide text-info me-1"></i> + Accordion</button></div>
                <div class="col-6 col-md-4"><button type="button" class="btn-interactive-item"><i class="bi bi-segmented-nav text-info me-1"></i> + Tabs Menu</button></div>
                <div class="col-6 col-md-4"><button type="button" class="btn-interactive-item"><i class="bi bi-exclamation-octagon text-info me-1"></i> + Callout Box</button></div>
                <div class="col-6 col-md-4"><button type="button" class="btn-interactive-item"><i class="bi bi-clock-history text-info me-1"></i> + Timeline</button></div>
                <div class="col-6 col-md-4"><button type="button" class="btn-interactive-item"><i class="bi bi-diagram-2 text-info me-1"></i> + Diagram</button></div>
                <div class="col-6 col-md-4"><button type="button" class="btn-interactive-item"><i class="bi bi-globe text-info me-1"></i> + Embed Website</button></div>
            </div>
        </div>

        <!-- ==========================================
             7. QUIZ INTEGRATION
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title">
                <i class="bi bi-patch-question-fill text-warning"></i> 7. Quiz Integration (เชื่อมต่อชุดข้อสอบ)
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label form-label-premium">ดึงหัวข้อข้อสอบเดิมที่มีอยู่</label>
                    <select class="form-select form-control-premium">
                        <option selected>[กรุณาเลือกควิซจากคลังส่วนกลาง]</option>
                        <option>Quiz 1: Fundamentals of Block Scope</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label form-label-premium">กำหนดตำแหน่งแสดง</label>
                    <select class="form-select form-control-premium">
                        <option>ท้ายสุดของบทเรียน</option>
                        <option>แทรกกลางเนื้อหา (ก่อนขึ้นหัวข้อแล็บ)</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-success w-100 fw-medium" style="min-height: 46px;" onclick="alert('ระบบกำลังเปิด Modal สร้างควิซใหม่...')"><i class="bi bi-plus-lg"></i> สร้าง Quiz ใหม่</button>
                </div>
            </div>
        </div>

        <!-- ==========================================
             8. CODING LAB INTEGRATION
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title">
                <i class="bi bi-terminal-fill text-success"></i> 8. Coding Lab Integration (เชื่อมต่อห้องปฏิบัติการเขียนโค้ด)
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label form-label-premium">เลือก Coding Lab เดิมของหลักสูตร</label>
                    <select class="form-select form-control-premium">
                        <option selected>[ ค้นหาหรือเลือกแล็บคอมไพเลอร์ในระบบ ]</option>
                        <option>Lab Assignment 3.1: Fix Scope Bug</option>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end gap-2">
                    <button type="button" class="btn btn-dark border border-secondary w-50" style="min-height: 46px;" onclick="alert('เพิ่มไอเทมแล็บลงแผนบทเรียนเรียบร้อย')"><i class="bi bi-link-45deg"></i> ผูกมัดเชื่อม Lab ที่เลือก</button>
                    <button type="button" class="btn btn-outline-info w-50" style="min-height: 46px;" onclick="alert('เปลี่ยนเส้นทางไปโมดูล Lab Builder...')"><i class="bi bi-magic"></i> สร้าง Lab ใหม่เข้าคลัง</button>
                </div>
            </div>
        </div>

        <!-- ==========================================
             9. VIRTUAL LAB INTEGRATION
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title">
                <i class="bi bi-cloud-haze2-fill text-cyan"></i> 9. Virtual Lab Integration (ห้องแล็บจำลองระบบ Cloud Sandbox)
            </div>
            <p class="editor-subtitle mt-0 mb-3">เชื่อมโยงบทเรียนกับสภาพแวดล้อมระบบปฏิบัติการ คอนเทนเนอร์ หรือโครงสร้างเครือข่ายความปลอดภัยสูง</p>
            <div class="row g-2.5">
                <div class="col-6 col-md-4">
                    <label class="custom-checkbox-card">
                        <input type="checkbox" class="form-check-input">
                        <span>Linux Command CLI</span>
                    </label>
                </div>
                <div class="col-6 col-md-4">
                    <label class="custom-checkbox-card">
                        <input type="checkbox" class="form-check-input" checked>
                        <span>Docker Containers</span>
                    </label>
                </div>
                <div class="col-6 col-md-4">
                    <label class="custom-checkbox-card">
                        <input type="checkbox" class="form-check-input">
                        <span>SQL Server Sandbox</span>
                    </label>
                </div>
                <div class="col-6 col-md-4">
                    <label class="custom-checkbox-card">
                        <input type="checkbox" class="form-check-input">
                        <span>Network Topology</span>
                    </label>
                </div>
                <div class="col-6 col-md-4">
                    <label class="custom-checkbox-card">
                        <input type="checkbox" class="form-check-input">
                        <span>Cloud AWS/GCP Provider</span>
                    </label>
                </div>
                <div class="col-6 col-md-4">
                    <label class="custom-checkbox-card">
                        <input type="checkbox" class="form-check-input">
                        <span>Cybersecurity CTF Capture</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- ==========================================
             10. ASSIGNMENT INTEGRATION
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title">
                <i class="bi bi-file-earmark-medical-fill text-danger"></i> 10. Assignment Integration (การมอบหมายการบ้านภารกิจใหญ่)
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label form-label-premium">ประเภทการบ้านตรวจรับ</label>
                    <select class="form-select form-control-premium">
                        <option>ไม่มีภารกิจการบ้านภายนอก (เน้นทำแล็บอัตโนมัติ)</option>
                        <option selected>File Upload (นักเรียนอัปโหลดเอกสารหรือโปรเจกต์สรุป)</option>
                        <option>Coding Project Repo Submission</option>
                        <option>Mini Project Comprehensive Review</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label form-label-premium">เกณฑ์คะแนนและคำสั่งระบุภารกิจ (Instructions)</label>
                    <input type="text" class="form-control form-control-premium" placeholder="เช่น ส่งไฟล์ Zip โครงสร้างคลาสใน 7 วัน">
                </div>
            </div>
        </div>

        <!-- ==========================================
             ⭐ FEATURE 2 & 11: AI LESSON ASSISTANT & QUALITY CHECKER
             ========================================== -->
        <div class="editor-section-card border border-purple bg-purple bg-opacity-5" style="border: 1px solid rgba(168, 85, 247, 0.25);">
            <div class="d-flex justify-content-between align-items-center border-bottom border-secondary border-opacity-20 pb-2 mb-3">
                <div class="section-title mb-0 border-0 pb-0" style="color:#a855f7;">
                    <i class="bi bi-cpu-fill"></i> 11. AI Lesson Assistant & ⭐ AI Quality Checker
                </div>
                <span class="badge bg-purple bg-opacity-20 text-purple border border-purple border-opacity-30">Copilot Engine Online</span>
            </div>
            
            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <label class="form-label form-label-premium d-block">11. ฟังก์ชันตัวช่วยสร้างสรรค์ด้วยปัญญาประดิษฐ์ (AI Generative Matrix)</label>
                    <div class="row g-2">
                        <div class="col-6 col-md-4"><button type="button" class="btn btn-dark border border-secondary text-white w-100 py-2.5 small" onclick="triggerAIAction('content')"><i class="bi bi-magic me-1"></i> ช่วยสร้างเนื้อหา</button></div>
                        <div class="col-6 col-md-4"><button type="button" class="btn btn-dark border border-secondary text-white w-100 py-2.5 small" onclick="triggerAIAction('summarize')"><i class="bi bi-journal-text me-1"></i> สรุปบทเรียน</button></div>
                        <div class="col-6 col-md-4"><button type="button" class="btn btn-dark border border-secondary text-white w-100 py-2.5 small" onclick="triggerAIAction('language')"><i class="bi bi-chat-left-quote me-1"></i> ปรับภาษาให้อ่านง่าย</button></div>
                        <div class="col-6 col-md-4"><button type="button" class="btn btn-dark border border-secondary text-white w-100 py-2.5 small" onclick="triggerAIAction('spell')"><i class="bi bi-spellcheck me-1"></i> ตรวจคำสะกด</button></div>
                        <div class="col-6 col-md-4"><button type="button" class="btn btn-dark border border-secondary text-white w-100 py-2.5 small" onclick="triggerAIAction('quiz')"><i class="bi bi-patch-question me-1"></i> ออกแบบควิซช่วย</button></div>
                        <div class="col-6 col-md-4"><button type="button" class="btn btn-dark border border-secondary text-white w-100 py-2.5 small" onclick="triggerAIAction('flashcard')"><i class="bi bi-card-heading me-1"></i> สร้าง Flashcards</button></div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="p-3 rounded-3 bg-dark bg-opacity-60 border border-secondary border-opacity-40">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-white fw-bold">Lesson Quality Score</span>
                            <span class="text-success fw-bold font-monospace">94 / 100 ⭐⭐⭐⭐⭐</span>
                        </div>
                        <div class="progress mb-2 bg-secondary bg-opacity-20" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 94%"></div>
                        </div>
                        <div style="font-size: 0.78rem; line-height: 1.45;" class="text-white-50">
                            <span class="text-warning fw-bold">[คำแนะนำจาก AI]:</span> โครงสร้างและเวลาศึกษายอดเยี่ยมแล้ว แนะนำให้เพิ่มตัวอย่างบล็อกโค้ดฝั่งภาษา Python เพิ่มเติมเพื่อความครอบคลุมของผู้เรียนระดับต้น
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             12 - 16. SETTINGS & ADVANCED METADATA PARAMS
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title"><i class="bi bi-sliders2-vertical"></i> Advanced Configuration Modules (12 - 16)</div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label form-label-premium">12. Learning Objectives (เป้าหมายแยกบรรทัด)</label>
                    <textarea class="form-control form-control-premium small" rows="2" placeholder="ตัวอย่าง:&#10;- เข้าใจ Scope Variables&#10;- สามารถเขียน Function ได้"></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label form-label-premium">13. Prerequisite (ต้องเรียนบทไหนก่อน)</label>
                    <select class="form-select form-control-premium small" multiple style="min-height: 80px !important;">
                        <option selected>Lesson 1: Introduction Variables</option>
                        <option>Lesson 2: Setup Environment IDE</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label form-label-premium">14. Estimated Learning Time (เวลาเรียนรวม)</label>
                    <input type="text" class="form-control form-control-premium" value="อ่าน 15 นาที, ดูวิดีโอ 20 นาที, Lab 30 นาที">
                </div>
                <div class="col-md-6">
                    <label class="form-label form-label-premium">15. Resources (ลิงก์แนบ GitHub / Docs / ลิงก์ดาวน์โหลด)</label>
                    <input type="text" class="form-control form-control-premium small" value="GitHub: https://github.com/example/scope-lesson, Docs: https://mdn.io">
                </div>
                <div class="col-md-6">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label form-label-premium">URL Slug</label>
                            <input type="text" class="form-control form-control-premium small" value="modern-variables-scope-logic">
                        </div>
                        <div class="col-6">
                            <label class="form-label form-label-premium">Meta Title</label>
                            <input type="text" class="form-control form-control-premium small" value="เรียนรู้โครงสร้าง Block Scope ละเอียด">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             18. VERSION HISTORY MANAGEMENT LOGS
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title">
                <i class="bi bi-history text-secondary"></i> 18. Version History (ประวัติและตรรกะกู้คืนเวอร์ชัน)
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 font-monospace" style="font-size: 0.82rem;">
                <div class="text-white-50"><i class="bi bi-arrow-repeat text-success animate-spin"></i> Auto Save ล่าสุดเมื่อสักครู่...</div>
                <div class="d-flex gap-3 align-items-center">
                    <span class="editor-subtitle">v1.4 (บันทึกแบบร่างก่อนหน้า)</span>
                    <a href="#" class="text-cyan text-decoration-none fw-bold" onclick="alert('กู้คืนข้อมูลแบบร่างเสร็จสมบูรณ์!')"><i class="bi bi-arrow-counterclockwise"></i> Restore Version นี้</a>
                </div>
            </div>
        </div>

        <!-- ==========================================
             20. PUBLISH CONTROL PANEL BUTTONS
             ========================================== -->
        <div class="p-3 rounded-4 bg-dark bg-opacity-50 border border-secondary border-opacity-30 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <button type="button" class="btn btn-outline-danger btn-sm py-2" onclick="triggerBottomPublishAction('delete')"><i class="bi bi-trash-fill"></i> Delete Lesson</button>
                <button type="button" class="btn btn-outline-secondary btn-sm text-white-50 ms-2 py-2" onclick="triggerBottomPublishAction('unpublish')">Unpublish</button>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-info text-cyan px-3 py-2" data-bs-toggle="modal" data-bs-target="#livePreviewModal"><i class="bi bi-eye-fill me-1"></i> 17. Preview Mode</button>
                <button type="button" class="btn btn-secondary text-white px-3 py-2" onclick="triggerBottomPublishAction('save_draft')"><i class="bi bi-cloud-check-fill"></i> Save Draft</button>
                <button type="button" class="btn btn-cyan text-white px-4 py-2 fw-bold" onclick="triggerBottomPublishAction('publish')"><i class="bi bi-rocket-takeoff-fill"></i> Publish Lesson</button>
            </div>
        </div>

    </form>
</div>

<!-- PREVIEW MODAL LAYER -->
<div class="modal fade" id="livePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
        <div class="modal-content bg-dark text-white border border-secondary border-opacity-20">
            <div class="modal-header border-secondary border-opacity-20 bg-black bg-opacity-30">
                <h6 class="modal-title fw-bold text-cyan"><i class="bi bi-display-fill me-2"></i>17. Cross-Platform Preview Layout Device Simulator</h6>
                <div class="btn-group mx-auto" role="group">
                    <button type="button" class="btn btn-xs btn-outline-info text-white active" onclick="$('#preview-viewport-box').css('max-width', '100%')">Desktop</button>
                    <button type="button" class="btn btn-xs btn-outline-info text-white" onclick="$('#preview-viewport-box').css('max-width', '768px')">Tablet</button>
                    <button type="button" class="btn btn-xs btn-outline-info text-white" onclick="$('#preview-viewport-box').css('max-width', '375px')">Mobile</button>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 bg-black bg-opacity-20 d-flex justify-content-center">
                <div id="preview-viewport-box" class="bg-dark p-4 rounded-3 shadow w-100 transition-all border border-secondary border-opacity-20 overflow-y-auto" style="max-width: 100%; height: 500px;">
                    <h4 class="text-white fw-bold">บทที่ 3: โครงสร้างและสโคปของตัวแปรใน Modern Programming</h4>
                    <span class="editor-subtitle">⏱️ เวลาเรียน: อ่าน 15 นาที, วิดีโอ 20 นาที, Lab 30 นาที</span>
                    <hr class="border-secondary border-opacity-20">
                    <p class="text-white-50 small">[หน้าจำลองการเรนเดอร์เนื้อหาจริงที่จะถูกแสดงบนหน้าจอของผู้เรียนแบบ Responsive Screen]</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- EVENT ENGINE ACTIONS CONTROL -->
<script>
    function triggerAIAction(mode) {
        alert('เรียกใช้โมเดลโครงข่าย AI Assistant ส่วนกลางในการประมวลผล: ' + mode.toUpperCase());
    }

    function triggerBottomPublishAction(type) {
        alert('เรียกใช้งานโครงสร้างคำสั่งคลาวด์คอร์ส: ' + type.toUpperCase() + ' สำเร็จแล้วครับ!');
    }

    function addNewContentBlock(blockType) {
        var randomNum = Math.floor(Math.random() * 10000);
        var blockElementHtml = `
            <div class="block-unit-item bg-dark bg-opacity-50 border border-secondary border-opacity-30 p-3 rounded mb-2 animate-fade-in" id="new_block_${randomNum}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold text-white-50"><i class="bi bi-grid-3x2-gap-fill text-muted me-2"></i>DYNAMIC CONTENT BLOCK: [${blockType.toUpperCase()}]</span>
                    <button type="button" class="btn btn-xs p-0 text-danger" onclick="$(this).closest('.block-unit-item').remove()"><i class="bi bi-trash-fill"></i></button>
                </div>
                <input type="text" class="form-control form-control-premium form-control-sm small" placeholder="กำหนดค่าเนื้อหาหรือรหัสผ่านตัวแปรสำหรับผูกมัด ${blockType} ตัวนี้...">
            </div>
        `;
        $('#block-builder-stage').append(blockElementHtml);
    }
</script>