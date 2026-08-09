<?php
$ROOT_PATH = '../';
// [Security Framework & Session Verification]
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>

<!-- 🎨 External Custom Scrollbar (If needed) -->
<link rel="stylesheet" href="<?php echo $ROOT_PATH; ?>css/custom-scrollbar.css">

<!-- Import Dropdown Select Stylesheet -->
<link rel="stylesheet" href="<?= $ROOT_PATH ?>css/dropdown-select.css">

<style>
    /* 🎨 CSS Variables Dynamic Theme (Light & Dark Mode) */
    :root, html[data-theme="dark"] {
        --lv-bg-dark: #0b0f19;
        --lv-card-bg: rgba(16, 24, 48, 0.65);
        --lv-card-border: rgba(255, 255, 255, 0.08);
        --lv-border-glow: rgba(99, 102, 241, 0.2);
        --lv-cyan: #06b6d4;
        --lv-indigo: #6366f1;
        --lv-text-main: #f8fafc;
        --lv-text-muted: rgba(255, 255, 255, 0.55);
        --lv-input-bg: rgba(15, 23, 42, 0.8);
        --lv-input-border: rgba(255, 255, 255, 0.12);
        --lv-input-text: #f8fafc;
        --lv-box-bg: rgba(0, 0, 0, 0.25);
        --lv-label: #cbd5e1;
        --lv-placeholder: #64748b;
    }

    html[data-theme="light"] {
        --lv-bg-dark: #f8fafc;
        --lv-card-bg: #ffffff;
        --lv-card-border: rgba(0, 0, 0, 0.08);
        --lv-border-glow: rgba(99, 102, 241, 0.15);
        --lv-cyan: #0284c7;
        --lv-indigo: #4f46e5;
        --lv-text-main: #0f172a;
        --lv-text-muted: #64748b;
        --lv-input-bg: #ffffff;
        --lv-input-border: #cbd5e1;
        --lv-input-text: #0f172a;
        --lv-box-bg: #f8fafc;
        --lv-label: #334155;
        --lv-placeholder: #94a3b8;
    }

    /* 🚀 Custom Interactive Dropdown CSS (แก้ไขเรื่อง Cursor แล้ว) */
    .custom-dropdown-wrapper,
    .custom-dropdown-btn,
    .custom-dropdown-menu,
    .custom-dropdown-item {
        /* ป้องกันไม่ให้เบราว์เซอร์เรียกเมาส์ OS มาแทนที่เมาส์จรวด */
        cursor: inherit !important; 
        user-select: none !important;
        -webkit-user-select: none !important;
    }

    /* หากใช้เมาส์จรวดแบบ JS Element บินตาม (Floating Rocket) ให้ปลดล็อกคอมเมนต์บรรทัดล่างนี้ครับ: */
    /*
    .custom-dropdown-wrapper, .custom-dropdown-btn, .custom-dropdown-menu, .custom-dropdown-item {
        cursor: none !important;
    }
    */

    .custom-dropdown-wrapper {
        position: relative;
        width: 100%;
    }

    .custom-dropdown-btn {
        background: var(--lv-input-bg);
        border: 1px solid var(--lv-input-border);
        color: var(--lv-input-text);
        border-radius: 10px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 52px;
        transition: all 0.2s ease;
        font-size: 0.95rem;
    }

    .custom-dropdown-btn:hover {
        border-color: var(--lv-cyan);
    }

    .custom-dropdown-btn i {
        transition: transform 0.2s ease;
        font-size: 0.8rem;
        opacity: 0.7;
    }

    .custom-dropdown-wrapper.active .custom-dropdown-btn i {
        transform: rotate(180deg);
    }

    .custom-dropdown-menu {
        display: none !important;
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        background: var(--lv-card-bg);
        border: 1px solid var(--lv-input-border);
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        z-index: 999 !important; /* ปรับ z-index เพื่อไม่ให้บัง Element จรวดลอย */
        max-height: 240px;
        overflow-y: auto;
        backdrop-filter: blur(16px);
        padding: 6px;
    }

    .custom-dropdown-wrapper.active .custom-dropdown-menu {
        display: block !important;
    }

    .custom-dropdown-item {
        padding: 10px 14px;
        border-radius: 6px;
        color: var(--lv-input-text);
        transition: background 0.15s ease;
        font-size: 0.9rem;
    }

    .custom-dropdown-item:hover {
        background: rgba(6, 182, 212, 0.15);
        color: var(--lv-cyan);
    }

    .custom-dropdown-item.selected {
        background: rgba(99, 102, 241, 0.2);
        font-weight: 600;
    }

    /* Scope Quiz Builder Styles */
    .quiz-container {
        animation: fadeInQuiz 0.35s ease-in-out;
        width: 100%;
        overflow-x: hidden;
        color: var(--lv-text-main);
    }
    .quiz-section-card {
        background: var(--lv-card-bg);
        border: 1px solid var(--lv-card-border);
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 20px;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }
    @media (min-width: 768px) {
        .quiz-section-card {
            padding: 24px;
            margin-bottom: 24px;
        }
    }
    .section-title {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--lv-text-main);
        border-bottom: 1px solid var(--lv-card-border);
        padding-bottom: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }
    @media (min-width: 768px) {
        .section-title {
            font-size: 1.1rem;
            align-items: center;
        }
    }
    .section-title i {
        color: var(--lv-cyan);
        margin-top: 2px;
    }
    @media (min-width: 768px) {
        .section-title i { margin-top: 0; }
    }

    .quiz-container .text-muted,
    .text-muted {
        color: var(--lv-text-muted) !important;
    }
    .quiz-container .text-white-50,
    .text-white-50 {
        color: var(--lv-text-muted) !important;
    }
    .quiz-container .text-cyan {
        color: var(--lv-cyan) !important;
    }

    .form-control-premium {
        background: var(--lv-input-bg) !important;
        border: 1px solid var(--lv-input-border) !important;
        color: var(--lv-input-text) !important;
        border-radius: 10px;
        padding-top: 13px !important;
        padding-bottom: 13px !important;
        padding-left: 16px !important;
        padding-right: 16px !important;
        line-height: 1.7 !important;
        height: auto !important; 
        min-height: 52px !important;
        font-size: 0.95rem !important;
        display: block;
        width: 100%;
        transition: border-color 0.2s ease, background-color 0.2s ease;
    }
    
    .form-control-premium:focus {
        border-color: var(--lv-cyan) !important;
        box-shadow: 0 0 0 0.25rem rgba(6, 182, 212, 0.15) !important;
    }
    
    textarea.form-control-premium {
        min-height: 110px !important;
        padding-top: 14px !important;
        padding-bottom: 14px !important;
    }

    .input-group .form-control-premium {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }
    .input-group-text {
        background: var(--lv-box-bg) !important;
        border: 1px solid var(--lv-input-border) !important;
        color: var(--lv-text-muted) !important;
        padding: 0 16px !important;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-label-premium {
        color: var(--lv-label) !important;
        font-weight: 500;
        margin-bottom: 8px;
        font-size: 0.88rem;
        display: inline-block;
    }
    .form-control-premium::placeholder {
        color: var(--lv-placeholder) !important;
    }

    .question-build-item {
        background: var(--lv-box-bg);
        border: 1px solid var(--lv-card-border);
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 16px;
    }
    @media (min-width: 768px) {
        .question-build-item {
            padding: 20px;
        }
    }

    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    @media (min-width: 992px) {
        .analytics-grid {
            grid-template-columns: repeat(5, 1fr);
        }
    }
    .analytics-card {
        background: var(--lv-box-bg);
        padding: 12px;
        border-radius: 8px;
        border: 1px solid var(--lv-card-border);
    }
    @media (min-width: 992px) {
        .analytics-card {
            border: none;
            border-right: 1px solid var(--lv-card-border);
            border-radius: 0;
            background: transparent;
            padding: 0 8px;
        }
        .analytics-card:last-child {
            border-right: none;
        }
    }

    .alert-badge-premium {
        white-space: normal;
        word-break: break-word;
        text-align: left;
        line-height: 1.4;
    }

    html[data-theme="light"] .quiz-container .text-white,
    html[data-theme="light"] #quizSimulatorModal .text-white {
        color: var(--lv-text-main) !important;
    }
    html[data-theme="light"] .quiz-container .bg-dark,
    html[data-theme="light"] #quizSimulatorModal .bg-dark {
        background-color: var(--lv-box-bg) !important;
    }
    html[data-theme="light"] .quiz-container .bg-black,
    html[data-theme="light"] #quizSimulatorModal .bg-black {
        background-color: rgba(0, 0, 0, 0.03) !important;
    }
    html[data-theme="light"] .quiz-container .btn-dark {
        background-color: #ffffff !important;
        color: var(--lv-text-main) !important;
        border-color: var(--lv-input-border) !important;
    }
    html[data-theme="light"] .quiz-container .btn-dark:hover {
        background-color: #e2e8f0 !important;
    }
    html[data-theme="light"] .quiz-container .border-secondary,
    html[data-theme="light"] #quizSimulatorModal .border-secondary {
        border-color: rgba(0, 0, 0, 0.1) !important;
    }
    html[data-theme="light"] #quizSimulatorModal .modal-content {
        background-color: #ffffff !important;
        color: var(--lv-text-main) !important;
        border-color: rgba(0, 0, 0, 0.1) !important;
    }
    html[data-theme="light"] #quizSimulatorModal .btn-close-white {
        filter: invert(1) grayscale(100%) brightness(0);
    }

    @keyframes fadeInQuiz {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="quiz-container">
    <div class="d-flex justify-content-between align-items-start mb-4 flex-column flex-md-row gap-3">
        <div class="w-100">
            <h3 class="text-white fw-bold mb-1 fs-4"><i class="bi bi-patch-question-fill text-cyan me-2"></i>ระบบสร้างและจัดการแบบทดสอบ</h3>
            <p class="text-muted small mb-0">ออกแบบชุดข้อสอบทั่วไป โค้ดดิ้งชาเลนจ์ สุ่มตัวเลือก และใช้ระบบ AI ช่วยประเมินความชัดเจนของคำถาม</p>
        </div>
        <div id="quiz-validation-alert" class="alert-badge-premium badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 p-2 font-monospace w-100 w-md-auto">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> 15. Validation: ตรวจพบตัวเลือกข้อสอบบางข้อยังไม่มีคำตอบเฉลยที่ถูก
        </div>
    </div>

    <form id="quizMasterForm" onsubmit="event.preventDefault();">

        <!-- 1 & 2. QUIZ CONFIGURATION -->
        <div class="quiz-section-card">
            <div class="section-title">
                <i class="bi bi-info-circle-fill"></i> 1 & 2. Quiz Configuration (ตั้งค่าข้อมูลทั่วไปหลักสูตร)
            </div>
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label form-label-premium">ชื่อแบบทดสอบ</label>
                    <input type="text" class="form-control form-control-premium" value="แบบทดสอบย่อยบทที่ 3: โครงสร้างและการประยุกต์ใช้งาน Array ในภาษาคอมพิวเตอร์">
                </div>
                
                <!-- 2. ประเภท Quiz -->
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label form-label-premium">2. ประเภท Quiz (Quiz Type)</label>
                    <div class="custom-dropdown-wrapper">
                        <div class="custom-dropdown-btn">
                            <span class="selected-text">Chapter Quiz (ประจำบทเรียน)</span>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="custom-dropdown-menu">
                            <div class="custom-dropdown-item" data-value="pretest">Pre-Test (ก่อนเรียน)</div>
                            <div class="custom-dropdown-item" data-value="posttest">Post-Test (หลังเรียน)</div>
                            <div class="custom-dropdown-item" data-value="practice">Practice Quiz (ฝึกฝนระหว่างเรียน)</div>
                            <div class="custom-dropdown-item selected" data-value="chapter">Chapter Quiz (ประจำบทเรียน)</div>
                            <div class="custom-dropdown-item" data-value="final">Final Exam (สอบไล่ปลายภาค)</div>
                            <div class="custom-dropdown-item" data-value="coding">Coding Challenge (โจทย์ท้าทายสปีดรัน)</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label form-label-premium">คะแนนเต็มของ Quiz</label>
                    <input type="number" class="form-control form-control-premium" value="50">
                </div>

                <!-- Chapter หลักที่ผูกมัด -->
                <div class="col-12 col-md-4">
                    <label class="form-label form-label-premium">Chapter หลักที่ผูกมัด</label>
                    <div class="custom-dropdown-wrapper">
                        <div class="custom-dropdown-btn">
                            <span class="selected-text">Chapter 2: Data Structure Core</span>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="custom-dropdown-menu">
                            <div class="custom-dropdown-item">Chapter 1: Introduction to Logic</div>
                            <div class="custom-dropdown-item selected">Chapter 2: Data Structure Core</div>
                        </div>
                    </div>
                </div>

                <!-- Lesson บทเรียนย่อย -->
                <div class="col-12 col-md-4">
                    <label class="form-label form-label-premium">Lesson (บทเรียนย่อยต้นทาง)</label>
                    <div class="custom-dropdown-wrapper">
                        <div class="custom-dropdown-btn">
                            <span class="selected-text">Lesson 3.2: Multidimensional Array Matrix</span>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="custom-dropdown-menu">
                            <div class="custom-dropdown-item">Lesson 3.1: Understanding Arrays Concept</div>
                            <div class="custom-dropdown-item selected">Lesson 3.2: Multidimensional Array Matrix</div>
                        </div>
                    </div>
                </div>

                <!-- สถานะการแสดงผล -->
                <div class="col-12 col-md-4">
                    <label class="form-label form-label-premium">สถานะการแสดงผล (Status)</label>
                    <div class="custom-dropdown-wrapper">
                        <div class="custom-dropdown-btn text-warning fw-bold">
                            <span class="selected-text">🟡 Draft (แบบร่างแก้ไข)</span>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="custom-dropdown-menu">
                            <div class="custom-dropdown-item selected text-warning fw-bold" data-value="draft">🟡 Draft (แบบร่างแก้ไข)</div>
                            <div class="custom-dropdown-item text-success fw-bold" data-value="published">🟢 Published (เผยแพร่สู่ผู้เรียน)</div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label form-label-premium">คำอธิบายโครงสร้างแบบทดสอบ</label>
                    <textarea class="form-control form-control-premium" rows="2">ใช้วัดระดับความเข้าใจตรรกะการจัดเรียงอาเรย์สองมิติ ห้ามเปิดดูอินเทอร์เน็ตระหว่างเข้าทดสอบ</textarea>
                </div>
            </div>
        </div>

        <!-- ⭐ FEATURE 2 & 3. QUESTION BUILDER -->
        <div class="quiz-section-card border border-info border-opacity-20">
            <div class="section-title text-cyan">
                <i class="bi bi-layout-text-sidebar-reverse"></i> ⭐ Visual Question Editor & 3. Question Type Builder
            </div>
            <p class="text-muted small mt-0 mb-3">กดเลือกประเภทโจทย์ด้านล่าง เพื่อเพิ่มลงกระดานข้อสอบ (รองรับภาพ, วิดีโอ, ตาราง, LaTeX หรือ Code Block)</p>
            
            <div class="d-flex flex-wrap gap-2 mb-4">
                <button type="button" class="btn btn-sm btn-dark border border-secondary text-white py-2 px-2.5" onclick="appendQuestionType('multiple_choice')">🔘 Multiple Choice</button>
                <button type="button" class="btn btn-sm btn-dark border border-secondary text-white py-2 px-2.5" onclick="appendQuestionType('true_false')">⚖️ True / False</button>
                <button type="button" class="btn btn-sm btn-dark border border-secondary text-white py-2 px-2.5" onclick="appendQuestionType('fill_blank')">✍️ Fill in the Blank</button>
                <button type="button" class="btn btn-sm btn-dark border border-secondary text-white py-2 px-2.5" onclick="appendQuestionType('short_answer')">📝 Short Answer</button>
                <button type="button" class="btn btn-sm btn-dark border border-secondary text-white py-2 px-2.5" onclick="appendQuestionType('essay')">📄 Essay</button>
                <button type="button" class="btn btn-sm btn-dark border border-secondary text-white py-2 px-2.5" onclick="appendQuestionType('coding_question')">💻 Coding Question</button>
            </div>

            <div id="questions-stage-container">
                <div class="question-build-item" id="q_sample_1">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <span class="small fw-bold text-white-50"><i class="bi bi-grid-3x2-gap-fill text-cyan me-2"></i>ข้อที่ 1 [ประเภท: Multiple Choice (ปรนัย)]</span>
                        <div class="btn-group">
                            <button type="button" class="btn btn-xs btn-outline-secondary py-1 text-white small" onclick="alert('คัดลอกข้อสอบข้อนี้แล้ว')"><i class="bi bi-copy"></i> Duplicate</button>
                            <button type="button" class="btn btn-xs btn-outline-danger py-1 small" onclick="$('#q_sample_1').remove()"><i class="bi bi-trash"></i> ลบ</button>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label form-label-premium">โจทย์คำถาม</label>
                            <textarea class="form-control form-control-premium font-monospace text-info small" rows="2">จากข้อมูล Array สองมิติที่กำหนดให้ จงคำนวณหาค่า Element ในตำแหน่ง index [1][0] ว่ามีค่าเท่ากับเท่าใด?</textarea>
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <div class="input-group mb-2">
                                <span class="input-group-text"><input type="radio" name="correct_q1" checked></span>
                                <input type="text" class="form-control form-control-premium" value="ตัวเลือก ก. 24 (คำตอบที่ถูก)">
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text"><input type="radio" name="correct_q1"></span>
                                <input type="text" class="form-control form-control-premium" value="ตัวเลือก ข. 12">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="input-group mb-2">
                                <span class="input-group-text"><input type="radio" name="correct_q1"></span>
                                <input type="text" class="form-control form-control-premium" value="ตัวเลือก ค. 9">
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text"><input type="radio" name="correct_q1"></span>
                                <input type="text" class="form-control form-control-premium" value="ตัวเลือก ง. ข้อมูลเป็น Null">
                            </div>
                        </div>

                        <div class="col-12 col-md-9">
                            <label class="form-label form-label-premium">คำอธิบายเฉลยคำตอบอย่างรายละเอียด (Explanation)</label>
                            <input type="text" class="form-control form-control-premium small" value="เนื่องจากอาเรย์แถวแรกคือ index 0 และแถวถัดมาคือ index 1 ค่าแรกของแถวจึงเป็น [1][0] ซึ่งตรงกับค่า 24">
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label form-label-premium">คะแนนข้อนี้</label>
                            <input type="number" class="form-control form-control-premium" value="5">
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <button type="button" class="btn btn-outline-light text-cyan border-secondary border-opacity-40 py-2 btn-sm w-100 w-sm-auto" onclick="alert('เปิดโมดูลคลังข้อสอบ 6. Question Bank')"><i class="bi bi-database-fill-down me-1"></i> นำเข้าข้อสอบจาก Question Bank</button>
            </div>
        </div>

        <!-- 7 & 8. QUIZ SETTINGS -->
        <div class="quiz-section-card">
            <div class="section-title">
                <i class="bi bi-gear-fill text-warning"></i> 7 & 8. Quiz Settings & Randomization Control
            </div>
            <div class="row g-3">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label form-label-premium">จำกัดเวลาสอบ (นาที)</label>
                    <input type="number" class="form-control form-control-premium" value="30">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label form-label-premium">เกณฑ์คะแนนผ่าน (%)</label>
                    <input type="number" class="form-control form-control-premium" value="60">
                </div>
                
                <!-- จำนวนครั้งที่อนุญาตให้สอบ -->
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label form-label-premium">จำนวนครั้งที่อนุญาตให้สอบ</label>
                    <div class="custom-dropdown-wrapper">
                        <div class="custom-dropdown-btn">
                            <span class="selected-text">3 ครั้ง</span>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="custom-dropdown-menu">
                            <div class="custom-dropdown-item">1 ครั้งเท่านั้น</div>
                            <div class="custom-dropdown-item selected">3 ครั้ง</div>
                            <div class="custom-dropdown-item">ไม่จำกัดจำนวนครั้ง</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label form-label-premium">การสุ่ม (Randomization)</label>
                    <div class="pt-1">
                        <div class="form-check form-check-inline small"><input type="checkbox" class="form-check-input" checked> สุ่มลำดับข้อ</div>
                        <div class="form-check form-check-inline small"><input type="checkbox" class="form-check-input" checked> สุ่มตัวเลือก</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 9. CODING QUIZ ENGINE -->
        <div class="quiz-section-card">
            <div class="section-title">
                <i class="bi bi-terminal-fill text-success"></i> 9. Coding Quiz Engine
            </div>
            <div class="row g-3">
                <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label form-label-premium">ภาษาโปรแกรมมิ่งที่รองรับ</label>
                    <div class="custom-dropdown-wrapper">
                        <div class="custom-dropdown-btn">
                            <span class="selected-text">JavaScript</span>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="custom-dropdown-menu">
                            <div class="custom-dropdown-item">PHP</div>
                            <div class="custom-dropdown-item">Python</div>
                            <div class="custom-dropdown-item">Java</div>
                            <div class="custom-dropdown-item selected">JavaScript</div>
                            <div class="custom-dropdown-item">C++</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label form-label-premium">Time Limit (วินาที)</label>
                    <input type="number" class="form-control form-control-premium" value="2">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label form-label-premium">Memory Limit (MB)</label>
                    <input type="number" class="form-control form-control-premium" value="256">
                </div>
            </div>
        </div>

        <!-- 11 & 17. ADVANCED PARAMETERS -->
        <div class="quiz-section-card">
            <div class="section-title"><i class="bi bi-sliders"></i> Advanced Parameters Matrix</div>
            <div class="row g-3">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label form-label-premium">11. ความยากเฉลี่ย</label>
                    <div class="custom-dropdown-wrapper">
                        <div class="custom-dropdown-btn text-info fw-bold">
                            <span class="selected-text">Medium (ปานกลาง)</span>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="custom-dropdown-menu">
                            <div class="custom-dropdown-item">Easy (ง่าย)</div>
                            <div class="custom-dropdown-item selected text-info fw-bold">Medium (ปานกลาง)</div>
                            <div class="custom-dropdown-item">Hard (ขั้นสูง)</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label form-label-premium">12. Tags & Skills</label>
                    <input type="text" class="form-control form-control-premium" value="DataStructure, Arrays">
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label form-label-premium">16. โอนย้ายไฟล์</label>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-dark border border-secondary w-50 py-2.5 small" onclick="alert('Import')">Import</button>
                        <button type="button" class="btn btn-dark border border-secondary w-50 py-2.5 small" onclick="alert('Export')">Export</button>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label form-label-premium">17. สิทธิ์การเข้าถึง</label>
                    <div class="custom-dropdown-wrapper">
                        <div class="custom-dropdown-btn">
                            <span class="selected-text">เฉพาะผู้ลงทะเบียนเรียนคอร์สนี้</span>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="custom-dropdown-menu">
                            <div class="custom-dropdown-item selected">เฉพาะผู้ลงทะเบียนเรียนคอร์สนี้</div>
                            <div class="custom-dropdown-item">ปลดล็อกอัตโนมัติหลังจบเนื้อหา</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PUBLISH CONTROL BUTTONS -->
        <div class="p-3 rounded-4 bg-dark bg-opacity-50 border border-secondary border-opacity-30 d-flex justify-content-between align-items-center flex-column flex-md-row gap-3">
            <div class="d-flex w-100 w-md-auto justify-content-between gap-2">
                <button type="button" class="btn btn-outline-danger btn-sm py-2 px-3"><i class="bi bi-trash-fill"></i> ลบ</button>
                <button type="button" class="btn btn-outline-secondary btn-sm text-white-50 py-2 px-3"><i class="bi bi-copy"></i> Duplicate</button>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
                <button type="button" class="btn btn-secondary text-white py-2 px-3"><i class="bi bi-cloud-check-fill"></i> Save Draft</button>
                <button type="button" class="btn btn-cyan text-white py-2 px-4 fw-bold"><i class="bi bi-rocket-takeoff-fill"></i> Publish Quiz</button>
            </div>
        </div>

    </form>
</div>

<!-- 🚀 Global Dynamic Dropdown Handler -->
<script>
    (function() {
        document.removeEventListener('click', handleCustomDropdownClick);
        document.addEventListener('click', handleCustomDropdownClick);

        function handleCustomDropdownClick(e) {
            const btn = e.target.closest('.custom-dropdown-btn');
            const item = e.target.closest('.custom-dropdown-item');

            if (btn) {
                e.stopPropagation();
                const wrapper = btn.closest('.custom-dropdown-wrapper');
                const isOpen = wrapper.classList.contains('active');

                document.querySelectorAll('.custom-dropdown-wrapper.active').forEach(w => {
                    w.classList.remove('active');
                });

                if (!isOpen) {
                    wrapper.classList.add('active');
                }
                return;
            }

            if (item) {
                const wrapper = item.closest('.custom-dropdown-wrapper');
                if (wrapper) {
                    const textSpan = wrapper.querySelector('.selected-text');
                    if (textSpan) {
                        textSpan.textContent = item.textContent.trim();
                    }

                    wrapper.querySelectorAll('.custom-dropdown-item').forEach(i => i.classList.remove('selected'));
                    item.classList.add('selected');

                    wrapper.classList.remove('active');
                }
                return;
            }

            document.querySelectorAll('.custom-dropdown-wrapper.active').forEach(w => {
                w.classList.remove('active');
            });
        }
    })();

    function appendQuestionType(type) {
        var uniqId = Math.floor(Math.random() * 99999);
        var customBlockNodeHtml = `
            <div class="question-build-item bg-dark bg-opacity-50 border border-secondary border-opacity-30 p-3 rounded mb-3" id="dynamic_q_${uniqId}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold text-white-50"><i class="bi bi-grid-3x2-gap-fill text-muted me-2"></i>ข้อสร้างใหม่ [ประเภท: ${type.toUpperCase()}]</span>
                    <button type="button" class="btn btn-xs p-0 text-danger" onclick="$(this).closest('.question-build-item').remove()"><i class="bi bi-trash-fill"></i></button>
                </div>
                <textarea class="form-control form-control-premium form-control-sm font-monospace small" rows="2" placeholder="พิมพ์คำถามเนื้อหาหลักสำหรับข้อสอบประเภท ${type}..."></textarea>
            </div>
        `;
        $('#questions-stage-container').append(customBlockNodeHtml);
    }
</script>