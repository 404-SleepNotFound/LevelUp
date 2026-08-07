<?php
$ROOT_PATH = '../';
// [Security Framework & Session Verification]
if (session_status() === PHP_SESSION_NONE) { session_start(); }
// ห้ามใส่ include '../includes/header.php' หรือ footer เด็ดขาด เพราะหน้า instructor.php โหลดให้แล้ว
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lesson Editor UI</title>
    <!-- เพิ่ม Bootstrap & Icons สำหรับการแสดงผล (ลบออกได้หากระบบหลักมีอยู่แล้ว) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        /* ---------------------------------------------------
           Fallback Theme Variables (กรณีเปิดไฟล์นี้เดี่ยวๆ)
           --------------------------------------------------- */
        :root {
            --lv-bg-dark: #1e293b;
            --lv-card-bg: #0f172a;
            --lv-text-main: #f8fafc;
            --lv-text-muted: #94a3b8;
            --lv-border-glow: #334155;
            --lv-cyan: #06b6d4;
        }
        
        body {
            background-color: #020617; /* Background สำหรับทดสอบ */
            padding: 20px;
        }

        /* ---------------------------------------------------
           Theme Variables Support (Dark/Light Mode)
           --------------------------------------------------- */
        .theme-bg-dark { background-color: var(--lv-bg-dark) !important; }
        .theme-bg-card { background-color: var(--lv-card-bg) !important; }
        .theme-text-main { color: var(--lv-text-main) !important; }
        .theme-text-muted { color: var(--lv-text-muted) !important; }
        .theme-border { border-color: var(--lv-border-glow) !important; }
        
        .btn-theme-dark {
            background-color: var(--lv-bg-dark);
            color: var(--lv-text-main);
            border: 1px solid var(--lv-border-glow);
        }
        .btn-theme-dark:hover {
            background-color: var(--lv-border-glow);
            color: var(--lv-text-main);
        }

        /* ---------------------------------------------------
           Scope Editor Styles
           --------------------------------------------------- */
        .editor-container {
            animation: fadeInEditor 0.35s ease-in-out;
            max-width: 1200px;
            margin: 0 auto;
        }
        .editor-section-card {
            background: var(--lv-card-bg);
            border: 1px solid var(--lv-border-glow);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            backdrop-filter: blur(10px);
            color: var(--lv-text-main);
        }
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--lv-text-main);
            border-bottom: 1px solid var(--lv-border-glow);
            padding-bottom: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-title i {
            color: var(--lv-cyan);
        }

        /* 🛠️ ล้างระบบกล่องจม: แก้ไข Input, Select, Textarea ทุกกล่องในหน้าจอ */
        .form-control-premium {
            background: var(--lv-bg-dark) !important;
            border: 1px solid var(--lv-border-glow) !important;
            color: var(--lv-text-main) !important;
            border-radius: 10px;
            padding: 10px 14px !important;
            line-height: 1.6 !important;
            min-height: 46px; 
        }
        .form-control-premium:focus {
            border-color: var(--lv-cyan) !important;
            box-shadow: 0 0 0 0.25rem rgba(6, 182, 212, 0.15) !important;
        }
        
        textarea.form-control-premium {
            min-height: 90px !important;
        }

        select.form-control-premium {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 14px center !important;
            background-size: 12px 12px !important;
            padding-right: 40px !important;
        }

        /* สไตล์สีตัวหนังสือและ Placeholder ให้ลอยเด่นเห็นชัดเจน */
        .form-label-premium {
            color: var(--lv-text-main) !important; 
            font-weight: 500;
            margin-bottom: 6px;
            font-size: 0.88rem;
        }
        .form-control-premium::placeholder {
            color: var(--lv-text-muted) !important;
        }

        .block-unit-item {
            background: var(--lv-bg-dark);
            border: 1px solid var(--lv-border-glow);
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 12px;
            color: var(--lv-text-main);
            transition: all 0.2s ease;
        }
        
        .block-unit-item:hover {
            border-color: rgba(6, 182, 212, 0.5);
        }

        .cursor-grab {
            cursor: grab;
        }
        .cursor-grab:active {
            cursor: grabbing;
        }
        
        .rich-editor-toolbar .btn {
            color: var(--lv-text-muted);
            border-color: var(--lv-border-glow);
            padding: 6px 12px;
        }
        .rich-editor-toolbar .btn:hover {
            color: var(--lv-text-main);
            background: var(--lv-border-glow);
        }
        
        @keyframes fadeInEditor {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideInBlock {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-in {
            animation: slideInBlock 0.3s ease-out forwards;
        }
    </style>
</head>
<body>

<div class="editor-container">
    <!-- Top Action Info -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="theme-text-main fw-bold mb-1"><i class="bi bi-pencil-square text-cyan me-2" style="color: var(--lv-cyan);"></i>ระบบสร้างและแก้ไขบทเรียน (Lesson Editor)</h3>
            <p class="theme-text-muted small mb-0">ปรับแต่งเนื้อหา วิดีโอ แล็บ ควิซ และใช้ระบบ AI ช่วยตรวจขัดเกลาเนื้อหาการเรียนรู้</p>
        </div>
        <!-- 17. Preview & 19. Validation Alert Badge -->
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
        <div class="editor-section-card border border-info border-opacity-20" style="border-color: rgba(6, 182, 212, 0.3) !important;">
            <div class="section-title text-info" style="color: var(--lv-cyan) !important;">
                <i class="bi bi-layers-half"></i> ⭐ Block-based Lesson Builder (ตัวจัดเรียงบล็อกเนื้อหาแบบ Dynamic)
            </div>
            <p class="theme-text-muted small mt-0 mb-3">กดเพิ่มบล็อกด้านล่างเพื่อทำการแทรกกล่องเนื้อหาและลากจัดตำแหน่งได้อย่างอิสระ</p>
            <div class="d-flex flex-wrap gap-2 mb-4 p-3 rounded theme-bg-dark border theme-border align-items-center">
                <span class="theme-text-muted small fw-bold me-2"><i class="bi bi-plus-circle me-1"></i> Add Block:</span>
                <button type="button" class="btn btn-sm btn-theme-dark border-secondary" onclick="addNewContentBlock('text')">📝 Text Block</button>
                <button type="button" class="btn btn-sm btn-theme-dark border-secondary" onclick="addNewContentBlock('video')">🎥 Video Block</button>
                <button type="button" class="btn btn-sm btn-theme-dark border-secondary" onclick="addNewContentBlock('code')">💻 Code Block</button>
                <button type="button" class="btn btn-sm btn-theme-dark border-secondary" onclick="addNewContentBlock('quiz')">🧪 Quiz Block</button>
                <button type="button" class="btn btn-sm btn-theme-dark border-secondary" onclick="addNewContentBlock('lab')">🖥️ Lab Block</button>
            </div>

            <!-- Dynamic Containers Drop Target -->
            <div id="block-builder-stage">
                <!-- 2. Rich Text Editor Block Sample Included inside builder -->
                <div class="block-unit-item" id="sample-rich-editor-block">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-bold theme-text-muted font-monospace d-flex align-items-center gap-2">
                            <i class="bi bi-grip-vertical fs-5 cursor-grab"></i>
                            <i class="bi bi-grid-3x2-gap-fill text-info"></i>2. Rich Text Editor Content Block
                        </span>
                        <span class="badge bg-secondary small">Default Text Engine</span>
                    </div>
                    <!-- 2. Rich Text Editor Toolbar Controls -->
                    <div class="btn-toolbar rich-editor-toolbar theme-bg-card p-2 rounded-top border theme-border gap-1" role="toolbar">
                        <button type="button" class="btn btn-xs py-0 px-2 fw-bold theme-text-main">H1</button>
                        <button type="button" class="btn btn-xs py-0 px-2 fw-bold theme-text-main">H2</button>
                        <button type="button" class="btn btn-xs py-0 px-2"><i class="bi bi-type-bold"></i></button>
                        <button type="button" class="btn btn-xs py-0 px-2"><i class="bi bi-type-italic"></i></button>
                        <button type="button" class="btn btn-xs py-0 px-2"><i class="bi bi-list-ul"></i></button>
                        <button type="button" class="btn btn-xs py-0 px-2"><i class="bi bi-list-ol"></i></button>
                        <button type="button" class="btn btn-xs py-0 px-2"><i class="bi bi-table"></i></button>
                        <button type="button" class="btn btn-xs py-0 px-2"><i class="bi bi-image"></i></button>
                        <button type="button" class="btn btn-xs py-0 px-2"><i class="bi bi-link-45deg"></i></button>
                        <button type="button" class="btn btn-xs py-0 px-2"><i class="bi bi-quote"></i></button>
                        <button type="button" class="btn btn-xs py-0 px-2 font-monospace" title="LaTeX Equation">$$ LaTeX</button>
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
            <p class="theme-text-muted small mt-0">รองรับระบบประมวลผลไฟล์สเปกตรัมกว้าง: PDF, Slide, ZIP, Source Code, Dataset, Image</p>
            <div class="p-4 border border-secondary border-opacity-50 rounded-3 text-center theme-bg-dark" style="border-style: dashed !important;">
                <i class="bi bi-cloud-arrow-up fs-2 theme-text-muted mb-2 d-block"></i>
                <span class="theme-text-muted small d-block mb-3">ลากไฟล์เอกสารมาวางที่นี่ หรือคลิกเพื่อเลือกอัปโหลดไฟล์เข้าระบบคลาวด์คอร์ส</span>
                <input type="file" class="form-control form-control-premium mx-auto" style="max-width: 350px;" multiple>
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
                    <button type="button" class="btn btn-sm btn-outline-info theme-border py-2" onclick="alert('คัดลอกโค้ดต้นแบบลงคลิปบอร์ดแล้ว!')"><i class="bi bi-copy me-1"></i> Copy Code Button</button>
                </div>
            </div>
            <textarea class="form-control form-control-premium font-monospace text-info small" rows="3">const currentScope = "Modern Block Scope Logic";&#10;console.log(`Executing within ${currentScope}`);</textarea>
        </div>

        <!-- ==========================================
             6. INTERACTIVE CONTENT
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title">
                <i class="bi bi-braces-asterisk" style="color:#a855f7;"></i> 6. Interactive Content (เพิ่มเนื้อหาแบบโต้ตอบ)
            </div>
            <div class="row g-2">
                <div class="col-6 col-md-4"><button type="button" class="btn btn-theme-dark w-100 text-start py-2 small"><i class="bi bi-menu-button-wide me-2 text-info"></i>+ Accordion</button></div>
                <div class="col-6 col-md-4"><button type="button" class="btn btn-theme-dark w-100 text-start py-2 small"><i class="bi bi-segmented-nav me-2 text-info"></i>+ Tabs Menu</button></div>
                <div class="col-6 col-md-4"><button type="button" class="btn btn-theme-dark w-100 text-start py-2 small"><i class="bi bi-exclamation-octagon me-2 text-info"></i>+ Callout Box</button></div>
                <div class="col-6 col-md-4"><button type="button" class="btn btn-theme-dark w-100 text-start py-2 small"><i class="bi bi-clock-history me-2 text-info"></i>+ Timeline</button></div>
                <div class="col-6 col-md-4"><button type="button" class="btn btn-theme-dark w-100 text-start py-2 small"><i class="bi bi-diagram-2 me-2 text-info"></i>+ Diagram</button></div>
                <div class="col-6 col-md-4"><button type="button" class="btn btn-theme-dark w-100 text-start py-2 small"><i class="bi bi-globe me-2 text-info"></i>+ Embed Website</button></div>
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
                    <button type="button" class="btn btn-theme-dark w-50" style="min-height: 46px;" onclick="alert('เพิ่มไอเทมแล็บลงแผนบทเรียนเรียบร้อย')"><i class="bi bi-link-45deg"></i> ผูกมัดเชื่อม Lab ที่เลือก</button>
                    <button type="button" class="btn btn-outline-info w-50" style="min-height: 46px;" onclick="alert('เปลี่ยนเส้นทางไปโมดูล Lab Builder...')"><i class="bi bi-magic"></i> สร้าง Lab ใหม่เข้าคลัง</button>
                </div>
            </div>
        </div>

        <!-- ==========================================
             9. VIRTUAL LAB INTEGRATION
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title">
                <i class="bi bi-cloud-haze2-fill text-info"></i> 9. Virtual Lab Integration (ห้องแล็บจำลองระบบ Cloud Sandbox)
            </div>
            <p class="theme-text-muted small mt-0">เชื่อมโยงบทเรียนกับสภาพแวดล้อมระบบปฏิบัติการ คอนเทนเนอร์ หรือโครงสร้างเครือข่ายความปลอดภัยสูง</p>
            <div class="row g-2">
                <div class="col-6 col-md-4"><div class="p-2 border theme-border rounded theme-bg-dark d-flex align-items-center"><input type="checkbox" class="form-check-input me-2"> Linux Command CLI</div></div>
                <div class="col-6 col-md-4"><div class="p-2 border theme-border rounded theme-bg-dark d-flex align-items-center"><input type="checkbox" class="form-check-input me-2" checked> Docker Containers</div></div>
                <div class="col-6 col-md-4"><div class="p-2 border theme-border rounded theme-bg-dark d-flex align-items-center"><input type="checkbox" class="form-check-input me-2"> SQL Server Sandbox</div></div>
                <div class="col-6 col-md-4"><div class="p-2 border theme-border rounded theme-bg-dark d-flex align-items-center"><input type="checkbox" class="form-check-input me-2"> Network Topology</div></div>
                <div class="col-6 col-md-4"><div class="p-2 border theme-border rounded theme-bg-dark d-flex align-items-center"><input type="checkbox" class="form-check-input me-2"> Cloud AWS/GCP Provider</div></div>
                <div class="col-6 col-md-4"><div class="p-2 border theme-border rounded theme-bg-dark d-flex align-items-center"><input type="checkbox" class="form-check-input me-2"> Cybersecurity CTF Capture</div></div>
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
        <div class="editor-section-card border" style="border-color: rgba(168, 85, 247, 0.4) !important; background-color: color-mix(in srgb, var(--lv-card-bg) 97%, #a855f7); ">
            <div class="d-flex justify-content-between align-items-center border-bottom border-secondary border-opacity-25 pb-2 mb-3">
                <div class="section-title mb-0 border-0 pb-0" style="color:#a855f7;">
                    <i class="bi bi-cpu-fill"></i> 11. AI Lesson Assistant & ⭐ AI Quality Checker
                </div>
                <span class="badge" style="color:#a855f7; background: rgba(168, 85, 247, 0.15); border: 1px solid rgba(168, 85, 247, 0.3);">Copilot Engine Online</span>
            </div>
            
            <div class="row g-3">
                <!-- 11. AI Lesson Actions Generator Layout Menu -->
                <div class="col-12 col-lg-8">
                    <label class="form-label form-label-premium d-block">11. ฟังก์ชันตัวช่วยสร้างสรรค์ด้วยปัญญาประดิษฐ์ (AI Generative Matrix)</label>
                    <div class="row g-2">
                        <div class="col-6 col-md-4"><button type="button" class="btn btn-theme-dark w-100 py-2 small"><i class="bi bi-magic me-1 text-warning"></i> ช่วยสร้างเนื้อหา</button></div>
                        <div class="col-6 col-md-4"><button type="button" class="btn btn-theme-dark w-100 py-2 small"><i class="bi bi-journal-text me-1 text-info"></i> สรุปบทเรียน</button></div>
                        <div class="col-6 col-md-4"><button type="button" class="btn btn-theme-dark w-100 py-2 small"><i class="bi bi-chat-left-quote me-1 text-success"></i> ปรับภาษาให้อ่านง่าย</button></div>
                        <div class="col-6 col-md-4"><button type="button" class="btn btn-theme-dark w-100 py-2 small"><i class="bi bi-spellcheck me-1 text-primary"></i> ตรวจคำสะกด</button></div>
                        <div class="col-6 col-md-4"><button type="button" class="btn btn-theme-dark w-100 py-2 small"><i class="bi bi-patch-question me-1 text-danger"></i> ออกแบบควิซช่วย</button></div>
                        <div class="col-6 col-md-4"><button type="button" class="btn btn-theme-dark w-100 py-2 small"><i class="bi bi-card-heading me-1 text-purple"></i> สร้าง Flashcards</button></div>
                    </div>
                </div>

                <!-- ⭐ FEATURE 2: AI QUALITY SCORE AUDITOR PANEL -->
                <div class="col-12 col-lg-4">
                    <div class="p-3 rounded-3 theme-bg-dark border theme-border h-100 d-flex flex-column justify-content-center">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small theme-text-main fw-bold">Lesson Quality Score</span>
                            <span class="text-success fw-bold font-monospace">94 / 100 ⭐</span>
                        </div>
                        <div class="progress mb-2 theme-bg-card border theme-border" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 94%"></div>
                        </div>
                        <div style="font-size: 0.75rem; line-height: 1.45;" class="theme-text-muted mt-1">
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
            <div class="section-title"><i class="bi bi-sliders2-vertical text-secondary"></i> Advanced Configuration Modules (12 - 16)</div>
            <div class="row g-3">
                <!-- 12. Learning Objectives -->
                <div class="col-md-4">
                    <label class="form-label form-label-premium">12. Learning Objectives (เป้าหมายแยกบรรทัด)</label>
                    <textarea class="form-control form-control-premium small" rows="2" placeholder="ตัวอย่าง:&#10;- เข้าใจ Scope Variables&#10;- สามารถเขียน Function ได้"></textarea>
                </div>
                <!-- 13. Prerequisite Matrix Links -->
                <div class="col-md-4">
                    <label class="form-label form-label-premium">13. Prerequisite (ต้องเรียนบทไหนก่อน)</label>
                    <select class="form-select form-control-premium small" multiple style="min-height: 80px !important;">
                        <option selected>Lesson 1: Introduction Variables</option>
                        <option>Lesson 2: Setup Environment IDE</option>
                    </select>
                </div>
                <!-- 14. Estimated Learning Time Control -->
                <div class="col-md-4">
                    <label class="form-label form-label-premium">14. Estimated Learning Time (เวลาเรียนรวม)</label>
                    <input type="text" class="form-control form-control-premium" value="อ่าน 15 นาที, ดูวิดีโอ 20 นาที, Lab 30 นาที">
                </div>
                <!-- 15. Resources Links Setup -->
                <div class="col-md-6">
                    <label class="form-label form-label-premium">15. Resources (ลิงก์แนบ GitHub / Docs / ลิงก์ดาวน์โหลด)</label>
                    <input type="text" class="form-control form-control-premium small" value="GitHub: https://github.com/example/scope-lesson, Docs: https://mdn.io">
                </div>
                <!-- 16. SEO & Metadata Center Routing -->
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
                <i class="bi bi-clock-history text-secondary"></i> 18. Version History (ประวัติและตรรกะกู้คืนเวอร์ชัน)
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 font-monospace" style="font-size: 0.85rem;">
                <div class="theme-text-muted"><i class="bi bi-arrow-repeat text-success spinner-border spinner-border-sm me-1" style="border-width: 2px;"></i> Auto Save ล่าสุดเมื่อสักครู่...</div>
                <div class="d-flex gap-3 align-items-center bg-dark bg-opacity-25 px-3 py-1 rounded">
                    <span class="theme-text-muted">v1.4 (บันทึกแบบร่างก่อนหน้า)</span>
                    <a href="#" class="text-info text-decoration-none fw-bold" onclick="alert('กู้คืนข้อมูลแบบร่างเสร็จสมบูรณ์!')"><i class="bi bi-arrow-counterclockwise"></i> Restore Version นี้</a>
                </div>
            </div>
        </div>

        <!-- ==========================================
             20. PUBLISH CONTROL PANEL BUTTONS
             ========================================== -->
        <div class="p-3 rounded-4 theme-bg-dark border theme-border d-flex justify-content-between align-items-center flex-wrap gap-3 sticky-bottom mb-4 shadow-lg">
            <div>
                <button type="button" class="btn btn-outline-danger btn-sm py-2" onclick="triggerBottomPublishAction('delete')"><i class="bi bi-trash-fill"></i> Delete Lesson</button>
                <button type="button" class="btn btn-outline-secondary btn-sm theme-text-muted ms-2 py-2" onclick="triggerBottomPublishAction('unpublish')">Unpublish</button>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-info text-info px-3 py-2 border-info" data-bs-toggle="modal" data-bs-target="#livePreviewModal"><i class="bi bi-eye-fill me-1"></i> 17. Preview Mode</button>
                <button type="button" class="btn btn-secondary text-white px-3 py-2" onclick="triggerBottomPublishAction('save_draft')"><i class="bi bi-cloud-check-fill me-1"></i> Save Draft</button>
                <button type="button" class="btn text-dark px-4 py-2 fw-bold" onclick="triggerBottomPublishAction('publish')" style="background-color: var(--lv-cyan); border: none;"><i class="bi bi-rocket-takeoff-fill me-1"></i> Publish Lesson</button>
            </div>
        </div>

    </form>
</div>

<!-- =================================================================================
     17. PREVIEW SIMULATOR FULLSCREEN SCREEN MODAL LAYER
     ================================================================================= -->
<div class="modal fade" id="livePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
        <div class="modal-content theme-bg-card theme-text-main border theme-border">
            <div class="modal-header theme-border theme-bg-dark">
                <h6 class="modal-title fw-bold text-info"><i class="bi bi-display-fill me-2"></i>17. Cross-Platform Preview Layout Device Simulator</h6>
                <div class="btn-group mx-auto" role="group">
                    <button type="button" class="btn btn-sm btn-outline-info text-white active" onclick="document.getElementById('preview-viewport-box').style.maxWidth = '100%'">Desktop</button>
                    <button type="button" class="btn btn-sm btn-outline-info text-white" onclick="document.getElementById('preview-viewport-box').style.maxWidth = '768px'">Tablet</button>
                    <button type="button" class="btn btn-sm btn-outline-info text-white" onclick="document.getElementById('preview-viewport-box').style.maxWidth = '375px'">Mobile</button>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 theme-bg-dark d-flex justify-content-center">
                <div id="preview-viewport-box" class="theme-bg-card p-4 rounded-3 shadow w-100 border theme-border overflow-y-auto" style="max-width: 100%; height: 600px; transition: max-width 0.3s ease;">
                    <h4 class="theme-text-main fw-bold">บทที่ 3: โครงสร้างและสโคปของตัวแปรใน Modern Programming</h4>
                    <span class="theme-text-muted small">⏱️ เวลาเรียน: อ่าน 15 นาที, วิดีโอ 20 นาที, Lab 30 นาที</span>
                    <hr class="theme-border my-4">
                    <div class="p-5 text-center theme-text-muted border border-secondary border-opacity-25 rounded-3" style="border-style: dashed !important;">
                        <i class="bi bi-laptop fs-1 mb-3 d-block opacity-50"></i>
                        [หน้าจำลองการเรนเดอร์เนื้อหาจริงที่จะถูกแสดงบนหน้าจอของผู้เรียนแบบ Responsive Screen]
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- สคริปต์การทำงานของระบบ UI -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function triggerAIAction(mode) {
        alert('เรียกใช้โมเดลโครงข่าย AI Assistant ส่วนกลางในการประมวลผล: ' + mode.toUpperCase());
    }

    function triggerBottomPublishAction(type) {
        alert('เรียกใช้งานโครงสร้างคำสั่งคลาวด์คอร์ส: ' + type.toUpperCase() + ' สำเร็จแล้วครับ!');
    }

    // ฟังก์ชันสร้าง Dynamic Block ที่ถูกแก้ไขให้สมบูรณ์
    function addNewContentBlock(blockType) {
        var randomNum = Math.floor(Math.random() * 10000);
        var blockId = 'dyn-block-' + randomNum;
        
        var iconHtml, titleText, contentHtml;

        // กำหนด UI ภายในกล่องตามประเภทที่กด
        switch(blockType) {
            case 'text':
                iconHtml = '<i class="bi bi-file-text-fill text-primary"></i>';
                titleText = 'Text Block (Content)';
                contentHtml = '<textarea class="form-control form-control-premium mt-3" rows="3" placeholder="พิมพ์เนื้อหาที่นี่..."></textarea>';
                break;
            case 'video':
                iconHtml = '<i class="bi bi-camera-video-fill text-warning"></i>';
                titleText = 'Video Block (Embed)';
                contentHtml = '<input type="text" class="form-control form-control-premium mt-3" placeholder="วางลิงก์ YouTube / Vimeo หรือ URL วิดีโอ">';
                break;
            case 'code':
                iconHtml = '<i class="bi bi-code-slash text-info"></i>';
                titleText = 'Code Snippet Block';
                contentHtml = '<textarea class="form-control form-control-premium mt-3 font-monospace text-info" rows="3" placeholder="เขียนซอร์สโค้ด..."></textarea>';
                break;
            case 'quiz':
                iconHtml = '<i class="bi bi-patch-question-fill text-danger"></i>';
                titleText = 'Quiz Integration Block';
                contentHtml = '<select class="form-select form-control-premium mt-3"><option>เลือกชุดควิซที่มีอยู่...</option><option>Quiz: Basic Scope</option></select>';
                break;
            case 'lab':
                iconHtml = '<i class="bi bi-terminal-fill text-success"></i>';
                titleText = 'Interactive Lab Block';
                contentHtml = '<select class="form-select form-control-premium mt-3"><option>เลือกห้องแล็บ...</option><option>Lab 3: Fix Variables</option></select>';
                break;
            default:
                iconHtml = '<i class="bi bi-box-fill text-secondary"></i>';
                titleText = 'Custom Block';
                contentHtml = '<div class="p-3 text-muted">เนื้อหาทั่วไป</div>';
        }

        // โครงสร้าง HTML ของ Block ใหม่
        var blockElementHtml = `
            <div class="block-unit-item theme-bg-dark border theme-border p-3 rounded mb-3 animate-slide-in" id="${blockId}">
                <div class="d-flex justify-content-between align-items-center border-bottom theme-border pb-2">
                    <span class="small fw-bold theme-text-main d-flex align-items-center gap-2">
                        <i class="bi bi-grip-vertical text-muted fs-5 cursor-grab"></i>
                        ${iconHtml} ${titleText}
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 border-0" onclick="removeContentBlock('${blockId}')" title="ลบบล็อกนี้">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </div>
                ${contentHtml}
            </div>
        `;

        // นำไปต่อท้ายใน Stage
        var stage = document.getElementById('block-builder-stage');
        stage.insertAdjacentHTML('beforeend', blockElementHtml);
        
        // เลื่อนหน้าจอลงมาที่บล็อกใหม่
        var newBlock = document.getElementById(blockId);
        newBlock.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // ฟังก์ชันลบ Block
    function removeContentBlock(blockId) {
        var block = document.getElementById(blockId);
        if(block) {
            block.style.opacity = '0';
            block.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                block.remove();
            }, 200);
        }
    }
</script>

</body>
</html>