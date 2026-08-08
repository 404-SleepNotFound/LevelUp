<?php
$ROOT_PATH = '../';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>

<style>
    /* Theme Variables Setup */
    :root, 
    html[data-theme="dark"], 
    [data-bs-theme="dark"] {
        --lab-bg-card: rgba(16, 24, 48, 0.65);
        --lab-border-card: rgba(255, 255, 255, 0.08);
        --lab-title-color: #f8fafc;
        --lab-muted-color: rgba(255, 255, 255, 0.60);
        --lab-label-color: #cbd5e1;
        
        --lab-input-bg: rgba(15, 23, 42, 0.8);
        --lab-input-border: rgba(255, 255, 255, 0.12);
        --lab-input-text: #f8fafc;
        --lab-input-ph: #64748b;
        
        --lab-sub-box-bg: rgba(0, 0, 0, 0.25);
        --lab-sub-box-border: rgba(255, 255, 255, 0.08);
        --lab-vlb-block-bg: rgba(30, 41, 59, 0.6);
        --lab-modal-bg: #0f172a;
    }

    html[data-theme="light"], 
    [data-bs-theme="light"] {
        --lab-bg-card: #ffffff;
        --lab-border-card: #e2e8f0;
        --lab-title-color: #0f172a;
        --lab-muted-color: #64748b;
        --lab-label-color: #334155;
        
        --lab-input-bg: #f8fafc;
        --lab-input-border: #cbd5e1;
        --lab-input-text: #0f172a;
        --lab-input-ph: #94a3b8;
        
        --lab-sub-box-bg: #f1f5f9;
        --lab-sub-box-border: #e2e8f0;
        --lab-vlb-block-bg: #ffffff;
        --lab-modal-bg: #ffffff;
    }

    /* Helper Utilities */
    .bg-purple {
        background-color: rgba(168, 85, 247, 0.12) !important;
    }
    .text-purple {
        color: #a855f7 !important;
    }
    .border-purple {
        border-color: rgba(168, 85, 247, 0.35) !important;
    }

    /* Scope Lab Builder Styles */
    .lab-container {
        animation: fadeInLab 0.35s ease-in-out;
        width: 100%;
        overflow-x: hidden;
    }
    
    .lab-main-title {
        color: var(--lab-title-color);
    }

    .lab-section-card {
        background: var(--lab-bg-card);
        border: 1px solid var(--lab-border-card);
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 20px;
        backdrop-filter: blur(10px);
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }
    @media (min-width: 768px) {
        .lab-section-card {
            padding: 24px;
            margin-bottom: 24px;
        }
    }
    
    .section-title {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--lab-title-color);
        border-bottom: 1px solid var(--lab-border-card);
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
        color: #3b82f6;
        margin-top: 2px;
    }
    @media (min-width: 768px) {
        .section-title i { margin-top: 0; }
    }

    .text-muted {
        color: var(--lab-muted-color) !important;
    }
    .text-white-50 {
        color: var(--lab-muted-color) !important;
    }

    /* Form Controls Adaptive */
    .form-control-premium {
        background: var(--lab-input-bg) !important;
        border: 1px solid var(--lab-input-border) !important;
        color: var(--lab-input-text) !important;
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
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    }
    
    .form-control-premium:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.15) !important;
    }
    
    textarea.form-control-premium {
        min-height: 110px !important;
        padding-top: 14px !important;
        padding-bottom: 14px !important;
    }

    select.form-control-premium {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 16px center !important;
        background-size: 12px 12px !important;
        padding-right: 42px !important;
    }

    .form-label-premium {
        color: var(--lab-label-color) !important;
        font-weight: 500;
        margin-bottom: 8px;
        font-size: 0.88rem;
        display: inline-block;
    }
    .form-control-premium::placeholder {
        color: var(--lab-input-ph) !important;
    }

    /* Sub Boxes & Cards Adaptive */
    .lab-sub-box {
        background: var(--lab-sub-box-bg);
        border: 1px solid var(--lab-sub-box-border);
        border-radius: 8px;
        padding: 12px;
    }

    .btn-purple {
        background-color: #a855f7;
        border-color: #a855f7;
        color: #ffffff;
    }
    .btn-purple:hover {
        background-color: #9333ea;
        border-color: #9333ea;
        color: #ffffff;
    }

    /* Visual Builder Drag & Drop Blocks Layout */
    .vlb-stage {
        background: var(--lab-sub-box-bg);
        border: 2px dashed var(--lab-sub-box-border);
        border-radius: 12px;
        padding: 16px;
        min-height: 220px;
    }
    .vlb-block-item {
        background: var(--lab-vlb-block-bg);
        border: 1px solid var(--lab-sub-box-border);
        color: var(--lab-title-color);
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        cursor: grab;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        user-select: none;
        transition: transform 0.15s ease, opacity 0.15s ease;
    }
    .vlb-block-item:active { 
        cursor: grabbing; 
    }
    .vlb-block-item.dragging {
        opacity: 0.4;
    }

    /* Analytics Summary Grid */
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
        background: var(--lab-sub-box-bg);
        padding: 12px;
        border-radius: 8px;
        border: 1px solid var(--lab-sub-box-border);
    }
    @media (min-width: 992px) {
        .analytics-card {
            border: none;
            border-right: 1px solid var(--lab-sub-box-border);
            border-radius: 0;
            background: transparent;
            padding: 0 8px;
        }
        .analytics-card:last-child {
            border-right: none;
        }
    }

    /* Modal Adaptive */
    .modal-content.lab-modal {
        background-color: var(--lab-modal-bg);
        color: var(--lab-title-color);
        border: 1px solid var(--lab-sub-box-border);
    }

    /* Terminal Cursor Animation */
    .animate-blink {
        animation: cursorBlink 1s infinite;
    }
    @keyframes cursorBlink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0; }
    }

    @keyframes fadeInLab {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .cursor-pointer { cursor: pointer; }
    .cursor-move { cursor: move; }
</style>

<div class="lab-container">
    <!-- Top Action Info Bar -->
    <div class="d-flex justify-content-between align-items-start mb-4 flex-column flex-md-row gap-3">
        <div class="w-100">
            <h3 class="lab-main-title fw-bold mb-1 fs-4"><i class="bi bi-terminal-fill text-primary me-2"></i>ระบบสร้างและจัดการห้องปฏิบัติการ (Lab Builder)</h3>
            <p class="text-muted small mb-0">สร้างและกำหนดสภาพแวดล้อมจำลอง (Sandbox) สำหรับจัดทำคอร์สปฏิบัติการ Coding, Linux, SQL, Docker และระบบความปลอดภัยไซเบอร์</p>
        </div>
    </div>

    <form id="labMasterForm">
        <!-- Hidden Inputs -->
        <input type="hidden" id="layout_structure" name="layout_structure">

        <!-- ==========================================
             1. LAB INFORMATION & 2. LAB TYPE
             ========================================== -->
        <div class="lab-section-card">
            <div class="section-title">
                <i class="bi bi-info-circle-fill"></i> 1 & 2. Lab Information & Identity
            </div>
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label form-label-premium">ชื่อห้องปฏิบัติการ (Lab Name)</label>
                    <input type="text" name="lab_name" class="form-control form-control-premium" value="การจัดการและเชื่อมต่อ Container ด้วย Docker Network" required>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label form-label-premium">2. ประเภทปฏิบัติการ (Lab Type)</label>
                    <select name="lab_type" class="form-select form-control-premium">
                        <option value="Coding">Coding Lab</option>
                        <option value="Linux">Linux Lab</option>
                        <option value="SQL">SQL Lab</option>
                        <option value="Docker" selected>Docker Lab</option>
                        <option value="Kubernetes">Kubernetes Lab</option>
                        <option value="Network">Network Lab</option>
                        <option value="Cybersecurity">Cybersecurity Lab</option>
                        <option value="Cloud">Cloud Lab</option>
                        <option value="DevOps">DevOps Lab</option>
                        <option value="AI">AI / Machine Learning Lab</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label form-label-premium">ความยาก (Difficulty)</label>
                    <select name="difficulty" class="form-select form-control-premium text-info fw-bold">
                        <option value="Beginner">Beginner (ระดับต้น)</option>
                        <option value="Intermediate" selected>Intermediate (ระดับกลาง)</option>
                        <option value="Advanced">Advanced (ระดับสูง)</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label form-label-premium">Chapter ผูกมัดหลักสูตร</label>
                    <select name="chapter_id" class="form-select form-control-premium">
                        <option value="3">Chapter 3: Virtualization Concepts</option>
                        <option value="4" selected>Chapter 4: Containerization Workflow</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label form-label-premium">Lesson (บทเรียนย่อย)</label>
                    <select name="lesson_id" class="form-select form-control-premium">
                        <option value="4.3" selected>Lesson 4.3: Custom Bridge Network Configuration</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label form-label-premium">สถานะ (Status)</label>
                    <select name="status" id="lab_status" class="form-select form-control-premium text-warning fw-bold">
                        <option value="draft" selected>🟡 Draft (บันทึกร่าง)</option>
                        <option value="published">🟢 Published (เปิดใช้งานจริง)</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label form-label-premium">คำอธิบายรายละเอียด Lab (Description)</label>
                    <textarea name="description" class="form-control form-control-premium" rows="2">ฝึกฝนทักษะการสร้าง Custom Network, การใช้คำสั่ง --network และตรวจสอบการสื่อสารเชื่อมต่อระหว่าง Container</textarea>
                </div>
            </div>
        </div>

        <!-- ==========================================
             FEATURE 2: AI ENVIRONMENT GENERATOR
             ========================================== -->
        <div class="lab-section-card bg-purple border-purple">
            <div class="section-title text-purple">
                <i class="bi bi-cpu-fill"></i> ⭐ AI Environment Generator (ตัวช่วยติดตั้งระบบอัตโนมัติ)
            </div>
            <p class="text-muted small mt-0 mb-3">พิมพ์เป้าหมายของ Lab ที่ต้องการเพื่อให้ AI ช่วยร่างชุดสภาพแวดล้อม โจทย์ ซอร์สโค้ดเริ่มต้น และ Test Cases ทันที</p>
            <div class="input-group">
                <input type="text" id="aiPromptInput" name="ai_prompt" class="form-control form-control-premium" placeholder='เช่น "สร้าง Linux Lab สำหรับฝึกใช้งานคำสั่งพื้นฐาน" หรือ "สร้าง Node.js API เชื่อมต่อ MongoDB Lab"'>
                <button type="button" class="btn btn-purple px-4" onclick="runAIGenerator()"><i class="bi bi-magic me-1"></i> สั่งการ AI</button>
            </div>
        </div>

        <!-- ==========================================
             3. ENVIRONMENT CONFIGURATION & 6. TERMINAL
             ========================================== -->
        <div class="lab-section-card">
            <div class="section-title"><i class="bi bi-hdd-network-fill"></i> 3 & 6. Environment & Virtual Hardware Engine</div>
            <div class="row g-3">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label form-label-premium">Operating System</label>
                    <select name="operating_system" class="form-select form-control-premium">
                        <option value="ubuntu-22.04" selected>Ubuntu 22.04 LTS</option>
                        <option value="alpine">Alpine Linux</option>
                        <option value="debian-12">Debian 12</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label form-label-premium">Programming Language</label>
                    <select name="programming_language" class="form-select form-control-premium">
                        <option value="bash" selected>Bash / Shell</option>
                        <option value="nodejs">Node.js</option>
                        <option value="python">Python</option>
                        <option value="php">PHP</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label form-label-premium">Docker Image</label>
                    <input type="text" name="docker_image" class="form-control form-control-premium" value="docker:24.0-dind">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label form-label-premium">Database Engine</label>
                    <select name="database_engine" class="form-select form-control-premium">
                        <option value="none" selected>None (ไม่ใช้ฐานข้อมูล)</option>
                        <option value="mysql-8.0">MySQL 8.0</option>
                        <option value="postgres-15">PostgreSQL 15</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label form-label-premium">RAM Limit</label>
                    <select name="ram_limit" class="form-select form-control-premium">
                        <option value="512MB">512 MB</option>
                        <option value="1GB" selected>1 GB</option>
                        <option value="2GB">2 GB</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label form-label-premium">CPU Core Allocation</label>
                    <select name="cpu_allocation" class="form-select form-control-premium">
                        <option value="0.5" selected>0.5 Core</option>
                        <option value="1.0">1.0 Core</option>
                        <option value="2.0">2.0 Core</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label form-label-premium">Disk Storage</label>
                    <select name="disk_storage" class="form-select form-control-premium">
                        <option value="2GB" selected>2 GB</option>
                        <option value="5GB">5 GB</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label form-label-premium">Session Timeout (นาที)</label>
                    <input type="number" name="session_timeout" class="form-control form-control-premium" value="45">
                </div>
                <div class="col-12">
                    <label class="form-label form-label-premium">6. Terminal Configuration (รองรับหลายหน้าต่าง)</label>
                    <div class="lab-sub-box">
                        <div class="form-check form-check-inline small"><input type="checkbox" name="terminals[]" value="Bash Shell" class="form-check-input" checked> <span class="lab-main-title">Bash Shell</span></div>
                        <div class="form-check form-check-inline small"><input type="checkbox" name="terminals[]" value="Zsh" class="form-check-input"> <span class="lab-main-title">Zsh</span></div>
                        <div class="form-check form-check-inline small"><input type="checkbox" name="terminals[]" value="PowerShell" class="form-check-input"> <span class="lab-main-title">PowerShell</span></div>
                        <div class="form-check form-check-inline small"><input type="checkbox" name="terminals[]" value="CMD" class="form-check-input"> <span class="lab-main-title">CMD</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             FEATURE 1: VISUAL LAB BUILDER (DRAG & DROP BLOCK)
             ========================================== -->
        <div class="lab-section-card border border-info border-opacity-20">
            <div class="section-title text-info">
                <i class="bi bi-ui-checks-grid"></i> ⭐ ฟีเจอร์เด่น: Visual Lab Builder Layout
            </div>
            <p class="text-muted small mt-0 mb-3">ลากจัดวางบล็อกหน้าต่างการแสดงผลตามลำดับความคุ้นเคย เพื่อปรับแต่งหน้าการทำแล็บของผู้เรียนได้อย่างอิสระ</p>
            
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="lab-sub-box">
                        <span class="small fw-bold lab-main-title mb-2 d-block">กล่องเครื่องมือบล็อกชิ้นส่วน</span>
                        <div class="vlb-block-item vlb-toolbox-item p-2 mb-1 w-100 text-start" draggable="true" data-type="instruction">📝 Instruction Block</div>
                        <div class="vlb-block-item vlb-toolbox-item p-2 mb-1 w-100 text-start" draggable="true" data-type="editor">💻 Code Editor Block</div>
                        <div class="vlb-block-item vlb-toolbox-item p-2 mb-1 w-100 text-start" draggable="true" data-type="terminal">🖥️ Terminal Block</div>
                        <div class="vlb-block-item vlb-toolbox-item p-2 mb-1 w-100 text-start" draggable="true" data-type="database">🗄️ Database Block</div>
                        <div class="vlb-block-item vlb-toolbox-item p-2 mb-1 w-100 text-start" draggable="true" data-type="browser">🌐 Browser Preview Block</div>
                        <div class="vlb-block-item vlb-toolbox-item p-2 mb-1 w-100 text-start" draggable="true" data-type="testcase">🧪 Test Case & Result Block</div>
                    </div>
                </div>
                <div class="col-12 col-md-8">
                    <div class="vlb-stage" id="vlbStage">
                        <span class="small text-muted d-block mb-2 font-monospace"><i class="bi bi-grid-1x2"></i> วางโครงสร้าง Layout เรียงลำดับลงที่นี่:</span>
                        
                        <div class="vlb-block-item vlb-stage-item" draggable="true" data-type="instruction">
                            <span>📝 1. Instruction Block (แถบแสดงโจทย์สั่งงาน)</span>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-list text-muted cursor-move"></i>
                                <i class="bi bi-trash text-danger ms-2 cursor-pointer" onclick="removeVLBBlock(this)" title="ลบบล็อก"></i>
                            </div>
                        </div>
                        <div class="vlb-block-item vlb-stage-item" draggable="true" data-type="terminal">
                            <span>🖥️ 2. Terminal Block (หน้าจอรับคำสั่งคอมพิวเตอร์)</span>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-list text-muted cursor-move"></i>
                                <i class="bi bi-trash text-danger ms-2 cursor-pointer" onclick="removeVLBBlock(this)" title="ลบบล็อก"></i>
                            </div>
                        </div>
                        <div class="vlb-block-item vlb-stage-item" draggable="true" data-type="testcase">
                            <span>🧪 3. Test Case & Result Block (ตรวจคำตอบ)</span>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-list text-muted cursor-move"></i>
                                <i class="bi bi-trash text-danger ms-2 cursor-pointer" onclick="removeVLBBlock(this)" title="ลบบล็อก"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             4. STARTER FILES & 5. CODE EDITOR CONFIGURATION
             ========================================== -->
        <div class="lab-section-card">
            <div class="section-title"><i class="bi bi-file-earmark-code-fill"></i> 4 & 5. Workspace Starter Files & Editor Specs</div>
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label form-label-premium">4. Starter Files (โครงสร้างไฟล์เริ่มต้นเขียนโค้ด)</label>
                    <textarea name="starter_files" class="form-control form-control-premium font-monospace text-info small" rows="3" placeholder="ตัวอย่างโครงไฟล์ เช่น docker-compose.yml หรือ README.md"># Lab Configuration File
version: '3.8'
services:
  web_node:
    image: nginx:alpine</textarea>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label form-label-premium">5. สเปกค่าเบื้องต้นของหน้าต่าง Code Editor</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <select name="editor_theme" class="form-select form-control-premium small">
                                <option value="vs-dark" selected>Theme: VS Dark</option>
                                <option value="monokai">Theme: Monokai</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <select name="font_size" class="form-select form-control-premium small">
                                <option value="14px" selected>Font Size: 14px</option>
                                <option value="16px">Font Size: 16px</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <select name="auto_save" class="form-select form-control-premium small">
                                <option value="1" selected>Auto Save: ON</option>
                                <option value="0">Auto Save: OFF</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <select name="minimap" class="form-select form-control-premium small">
                                <option value="0" selected>Minimap: Hidden</option>
                                <option value="1">Minimap: Visible</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             7. TASK INSTRUCTIONS & 10. AI LAB ASSISTANT
             ========================================== -->
        <div class="lab-section-card">
            <div class="section-title"><i class="bi bi-journal-text"></i> 7 & 10. Task Instructions Builder & AI Assistant Engine</div>
            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <label class="form-label form-label-premium">คำแนะนำขั้นตอนการทำแล็บ (รองรับตาราง, ภาพ, วิดีโอ หรือ Markdown)</label>
                    <textarea name="instructions" class="form-control form-control-premium font-monospace small" rows="3">1. รันคอนเทนเนอร์ตัวแรกชื่อ `web-app` บนน็ตเวิร์กบริดจ์ทั่วไป
2. ตรวจสอบการ Ping ค้นหาชื่อโฮสต์ปลายทางภายในกลุ่มจำลอง</textarea>
                </div>
                <div class="col-12 col-lg-4">
                    <label class="form-label form-label-premium">10. AI Lab Copilot Actions</label>
                    <div class="row g-2">
                        <div class="col-6"><button type="button" class="btn btn-outline-secondary lab-main-title border w-100 py-2 small" onclick="runAICopilotAction('เจน Test Case')"><i class="bi bi-node-plus"></i> ช่วยคิดสคริปต์ตรวจ</button></div>
                        <div class="col-6"><button type="button" class="btn btn-outline-secondary lab-main-title border w-100 py-2 small" onclick="runAICopilotAction('ตรวจคำสะกด')"><i class="bi bi-spellcheck"></i> ตรวจคำอธิบาย</button></div>
                        <div class="col-6"><button type="button" class="btn btn-outline-secondary lab-main-title border w-100 py-2 small" onclick="runAICopilotAction('สร้างคำใบ้')"><i class="bi bi-lightbulb"></i> ออกแบบคำใบ้</button></div>
                        <div class="col-6"><button type="button" class="btn btn-outline-secondary lab-main-title border w-100 py-2 small" onclick="runAICopilotAction('จำลองเฉลยโค้ด')"><i class="bi bi-check-circle"></i> จำลองเฉลยโค้ด</button></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             8. TEST CASES & 9. VALIDATION RULES
             ========================================== -->
        <div class="lab-section-card">
            <div class="section-title"><i class="bi bi-check2-square text-success"></i> 8 & 9. Test Cases & Validation Automation Logic</div>
            <div class="row g-3">
                <div class="col-12 col-md-5">
                    <label class="form-label form-label-premium">Input / Command Test Trigger</label>
                    <input type="text" name="test_command" class="form-control form-control-premium font-monospace text-success small" value="docker network inspect custom-bridge">
                </div>
                <div class="col-12 col-md-5">
                    <label class="form-label form-label-premium">Expected Output Match</label>
                    <input type="text" name="expected_output" class="form-control form-control-premium font-monospace text-warning small" value='"Subnet": "172.20.0.0/16"'>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label form-label-premium">คะแนน Test Case นี้</label>
                    <input type="number" name="test_points" class="form-control form-control-premium" value="20">
                </div>
                <div class="col-12">
                    <div class="lab-sub-box small">
                        <span class="d-block mb-2 fw-bold text-muted">9. กฎและเงื่อนไขการให้ผ่านแล็บ (Validation Rules)</span>
                        <div class="form-check form-check-inline"><input type="checkbox" name="validation_rules[]" value="compiler_pass" class="form-check-input" checked> <span class="lab-main-title">รันชุดคำสั่งผ่านคอมไพเลอร์สำเร็จ</span></div>
                        <div class="form-check form-check-inline"><input type="checkbox" name="validation_rules[]" value="output_match" class="form-check-input" checked> <span class="lab-main-title">ผลลัพธ์ (Output) ตรงกันครอบคลุม</span></div>
                        <div class="form-check form-check-inline"><input type="checkbox" name="validation_rules[]" value="explorer_structure" class="form-check-input"> <span class="lab-main-title">โครงสร้างไฟล์ในระบบ Explorer ครบถ้วน</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             11. HINT SYSTEM & 12. SOLUTION MANAGEMENT
             ========================================== -->
        <div class="lab-section-card">
            <div class="section-title"><i class="bi bi-lightbulb-fill text-warning"></i> 11 & 12. Hints & Solutions Repository</div>
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label form-label-premium">11. Hint System (ระบบคำใบ้แบบลดหลั่น)</label>
                    <input type="text" name="hints[]" class="form-control form-control-premium small mb-2" value="คำใบ้ที่ 1: ตรวจสอบการใช้งานคำสั่ง docker network create ก่อน">
                    <input type="text" name="hints[]" class="form-control form-control-premium small" value="คำใบ้ที่ 2: สามารถระบุเลข Subnet โดยต่อท้ายคำสั่ง --subnet=172.20.0.0/16">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label form-label-premium">12. Solution Management (ระบบคลังเฉลยสำหรับอาจารย์)</label>
                    <textarea name="solution" class="form-control form-control-premium font-monospace small" rows="2" placeholder="ใส่ขั้นตอนหรือโค้ดเฉลยที่สมบูรณ์แบบ...">docker network create --driver bridge --subnet 172.20.0.0/16 custom-bridge</textarea>
                </div>
            </div>
        </div>

        <!-- ==========================================
             13 - 16. SCORING, ACCESS, METADATA & MATRICES
             ========================================== -->
        <div class="lab-section-card">
            <div class="section-title"><i class="bi bi-sliders2-vertical"></i> Advanced Configuration Matrices</div>
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <label class="form-label form-label-premium">13. คะแนนรวมทั้งหมดของ Lab</label>
                    <input type="number" name="total_score" class="form-control form-control-premium" value="100">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label form-label-premium">14. จำกัดจำนวนสิทธิ์การรันแล็บ</label>
                    <select name="max_attempts" class="form-select form-control-premium">
                        <option value="unlimited">ไม่จำกัดจำนวนครั้ง</option>
                        <option value="5" selected>จำกัดสูงสุด 5 ครั้งต่อคน</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label form-label-premium">15. Resource Files แนบเอกสารเพิ่ม</label>
                    <button type="button" class="btn btn-outline-secondary lab-main-title border w-100 py-2 small" onclick="alert('เปิดหน้าต่างเลือกไฟล์แนบ')"><i class="bi bi-paperclip"></i> แนบ PDF / API doc</button>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label form-label-premium">16. Skill Mapping</label>
                    <input type="text" name="skills" class="form-control form-control-premium" value="Docker, Networks, SysAdmin">
                </div>
            </div>
        </div>

        <!-- ==========================================
             19. LAB PERFORMANCE ANALYTICS
             ========================================== -->
        <div class="lab-section-card">
            <div class="section-title">
                <i class="bi bi-bar-chart-line-fill"></i> 19. Lab Realtime Analytics Module (สถิติปฏิบัติการ)
            </div>
            <div class="analytics-grid font-monospace text-center">
                <div class="analytics-card"><div class="text-muted small">จำนวนผู้ทำสะสม</div><h5 class="fw-bold lab-main-title mb-0 fs-6 mt-1">89 คน</h5></div>
                <div class="analytics-card"><div class="text-muted small">อัตราการผ่านด่าน</div><h5 class="fw-bold text-success mb-0 fs-6 mt-1">64.8%</h5></div>
                <div class="analytics-card"><div class="text-muted small">เวลาเฉลี่ย/รอบ</div><h5 class="fw-bold text-info mb-0 fs-6 mt-1">26 นาที</h5></div>
                <div class="analytics-card"><div class="text-muted small">จุดผิดบ่อยที่สุด</div><h5 class="fw-bold text-danger mb-0 fs-6 mt-1">Test Case 2 (44%)</h5></div>
                <div class="analytics-card"><div class="text-muted small">สถิติการเปิดคำใบ้</div><h5 class="fw-bold text-warning mb-0 fs-6 mt-1">Hint 1 (72 ครั้ง)</h5></div>
            </div>
        </div>

        <!-- ==========================================
             18. VERSION HISTORY TRACKING
             ========================================== -->
        <div class="lab-section-card">
            <div class="section-title"><i class="bi bi-clock-history"></i> 18. Version Control Logs</div>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 font-monospace text-muted small">
                <div><i class="bi bi-shield-check text-success"></i> Auto Save: บันทึกข้อมูลสภาพแวดล้อมสำรองลงระบบ Cloud แล้ว</div>
                <div>เวอร์ชันประวัติ: <span class="text-info fw-bold">v1.4 (Draft Updated)</span></div>
            </div>
        </div>

        <!-- ==========================================
             20. PUBLISH HUB ACTION CONTROL PANEL
             ========================================== -->
        <div class="p-3 rounded-4 lab-sub-box d-flex justify-content-between align-items-center flex-column flex-md-row gap-3">
            <div class="d-flex w-100 w-md-auto justify-content-between gap-2">
                <button type="button" class="btn btn-outline-danger btn-sm py-2 px-3" onclick="deleteLab()"><i class="bi bi-trash-fill"></i> ลบ</button>
                <button type="button" class="btn btn-outline-secondary btn-sm lab-main-title py-2 px-3" onclick="duplicateLab()"><i class="bi bi-copy"></i> Duplicate</button>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
                <button type="button" class="btn btn-outline-primary text-info py-2 px-3" data-bs-toggle="modal" data-bs-target="#labSimulatorModal"><i class="bi bi-eye-fill me-1"></i> 17. Preview Lab</button>
                <button type="button" class="btn btn-secondary text-white py-2 px-3" onclick="saveLab('draft')"><i class="bi bi-cloud-check-fill"></i> Save Draft</button>
                <button type="button" class="btn btn-primary text-white py-2 px-4 fw-bold" onclick="saveLab('published')"><i class="bi bi-rocket-takeoff-fill"></i> Publish Lab</button>
            </div>
        </div>

    </form>
</div>

<!-- ==========================================
     17. PREVIEW LAB SIMULATOR MODAL
     ========================================== -->
<div class="modal fade" id="labSimulatorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content lab-modal shadow-lg">
            <div class="modal-header border-bottom border-secondary border-opacity-20">
                <h6 class="modal-title fw-bold text-primary"><i class="bi bi-laptop me-2"></i>17. Preview Mode (มุมมองจริงของผู้เรียน)</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="row g-2">
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded lab-sub-box h-100">
                            <h6 class="lab-main-title border-bottom border-secondary border-opacity-20 pb-2 small">📝 โจทย์และคำสั่งปฏิบัติการ</h6>
                            <p class="text-muted small mt-2">จงสร้าง Custom Bridge Network ในระบบ Docker โดยกำหนดชุดไอพี Subnet ให้เป็นไปตามโจทย์ระบุ</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-8">
                        <div class="p-3 rounded bg-dark text-success font-monospace small mb-2 border border-secondary" style="min-height: 140px;">
                            root@sandbox-sandbox:~# <span class="text-white animate-blink">|</span>
                        </div>
                        <div class="p-2 rounded lab-sub-box small text-end">
                            <span class="me-2 text-muted"><i class="bi bi-circle-fill text-danger small"></i> Test Case 1</span>
                            <span class="text-muted"><i class="bi bi-circle-fill text-danger small"></i> Test Case 2</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Visual Lab Builder Drag & Drop Functionality
    const vlbStage = document.getElementById('vlbStage');
    const toolboxItems = document.querySelectorAll('.vlb-toolbox-item');
    let draggedSource = null;
    let draggedElement = null;

    // ตั้งค่า Drag ให้กับเครื่องมือใน Toolbox ฝั่งซ้าย
    toolboxItems.forEach(item => {
        item.addEventListener('dragstart', function(e) {
            draggedSource = 'toolbox';
            draggedElement = this;
            e.dataTransfer.setData('text/plain', this.getAttribute('data-type'));
            e.dataTransfer.effectAllowed = 'copy';
        });
    });

    // เริ่มต้นบล็อกเดิมที่มีใน Stage
    initStageBlocks();

    if (vlbStage) {
        vlbStage.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = (draggedSource === 'toolbox') ? 'copy' : 'move';
            
            const afterElement = getDragAfterElement(vlbStage, e.clientY);
            if (draggedSource === 'stage' && draggedElement) {
                if (afterElement == null) {
                    vlbStage.appendChild(draggedElement);
                } else {
                    vlbStage.insertBefore(draggedElement, afterElement);
                }
            }
        });

        vlbStage.addEventListener('drop', function(e) {
            e.preventDefault();
            if (draggedSource === 'toolbox' && draggedElement) {
                const blockType = draggedElement.getAttribute('data-type');
                const blockText = draggedElement.innerText.trim();
                const afterElement = getDragAfterElement(vlbStage, e.clientY);

                const newBlock = createStageBlock(blockType, blockText);
                if (afterElement == null) {
                    vlbStage.appendChild(newBlock);
                } else {
                    vlbStage.insertBefore(newBlock, afterElement);
                }
                updateLayoutData();
            } else if (draggedSource === 'stage') {
                updateLayoutData();
            }
            draggedSource = null;
            draggedElement = null;
        });
    }

    function createStageBlock(type, title) {
        const div = document.createElement('div');
        div.className = 'vlb-block-item vlb-stage-item';
        div.setAttribute('draggable', 'true');
        div.setAttribute('data-type', type);
        div.innerHTML = `
            <span>${title}</span>
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-list text-muted cursor-move"></i>
                <i class="bi bi-trash text-danger ms-2 cursor-pointer" onclick="removeVLBBlock(this)" title="ลบบล็อก"></i>
            </div>
        `;
        attachStageBlockListeners(div);
        return div;
    }

    function attachStageBlockListeners(item) {
        item.addEventListener('dragstart', function(e) {
            draggedSource = 'stage';
            draggedElement = this;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        });

        item.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            draggedSource = null;
            draggedElement = null;
            updateLayoutData();
        });
    }

    function initStageBlocks() {
        document.querySelectorAll('#vlbStage .vlb-stage-item').forEach(item => {
            attachStageBlockListeners(item);
        });
        updateLayoutData();
    }

    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.vlb-stage-item:not(.dragging)')];
        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    // Submit Form Handler
    document.getElementById('labMasterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveLab(document.getElementById('lab_status').value);
    });
});

function removeVLBBlock(btn) {
    const item = btn.closest('.vlb-block-item');
    if (item) {
        item.remove();
        updateLayoutData();
    }
}

function updateLayoutData() {
    const blocks = [];
    document.querySelectorAll('#vlbStage .vlb-block-item').forEach(el => {
        const type = el.getAttribute('data-type') || 'unknown';
        const text = el.querySelector('span') ? el.querySelector('span').innerText.trim() : el.innerText.trim();
        blocks.push({ type: type, title: text });
    });
    const layoutInput = document.getElementById('layout_structure');
    if (layoutInput) {
        layoutInput.value = JSON.stringify(blocks);
    }
}

function saveLab(targetStatus) {
    updateLayoutData();
    if (targetStatus) {
        document.getElementById('lab_status').value = targetStatus;
    }

    const form = document.getElementById('labMasterForm');
    const formData = new FormData(form);

    fetch('services/lab.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message || 'บันทึกข้อมูลห้องปฏิบัติการเรียบร้อยแล้ว!');
    })
    .catch(err => {
        alert('บันทึกข้อมูลเรียบร้อยแล้ว! (จำลองการส่งข้อมูลแบบ Client-side)');
    });
}

function deleteLab() {
    if (confirm('คุณต้องการลบห้องปฏิบัติการนี้ใช่หรือไม่?')) {
        alert('ลบข้อมูลห้องปฏิบัติการเรียบร้อยแล้ว');
    }
}

function duplicateLab() {
    alert('คัดลอกและสร้างห้องปฏิบัติการใหม่เรียบร้อยแล้ว');
}

function runAIGenerator() {
    const prompt = document.getElementById('aiPromptInput').value.trim();
    if (!prompt) {
        alert('กรุณากรอกเป้าหมายหรือโจทย์ของ Lab ที่ต้องการ');
        return;
    }
    alert('AI กำลังประมวลผลและสร้างสภาพแวดล้อมจำลองจากโจทย์: "' + prompt + '"');
}

function runAICopilotAction(actionName) {
    alert('AI Copilot กำลังดำเนินการ: ' + actionName);
}
</script>