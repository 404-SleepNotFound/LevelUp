<?php
$ROOT_PATH = '../';
// [Security Framework & Session Verification]
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>

<style>
    /* Scope Lab Builder Styles ให้เข้ากับ Premium Dark Theme */
    .lab-container {
        animation: fadeInLab 0.35s ease-in-out;
        width: 100%;
        overflow-x: hidden;
    }
    .lab-section-card {
        background: rgba(16, 24, 48, 0.65);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 20px;
        backdrop-filter: blur(10px);
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
        color: #f8fafc;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
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
        color: #3b82f6; /* สีน้ำเงินแล็บคอร์ส */
        margin-top: 2px;
    }
    @media (min-width: 768px) {
        .section-title i { margin-top: 0; }
    }

    /* 🛠️ [FIXED] ดึงตัวหนังสือจาง/คำโปรยย่อย ให้เด่นชัดอ่านง่ายบนธีมมืด ไม่กลืนหาย */
    .text-muted {
        color: rgba(255, 255, 255, 0.52) !important;
    }
    .text-white-50 {
        color: rgba(255, 255, 255, 0.60) !important;
    }

    /* 🛠️ [SUPER FIX] ป้องกันสระและวรรณยุกต์ไทยจม/ขอบตัดในกล่องรับข้อมูล */
    .form-control-premium {
        background: rgba(15, 23, 42, 0.8) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        color: #f8fafc !important;
        border-radius: 10px;
        
        /* จัด Offset ของ Padding ใหม่เพื่อดันฟอนต์ไทยให้อยู่ตรงกลาง */
        padding-top: 13px !important;
        padding-bottom: 13px !important;
        padding-left: 16px !important;
        padding-right: 16px !important;
        
        /* ขยาย line-height และความสูงเพื่อรองรับสระบน-ล่าง */
        line-height: 1.7 !important;
        height: auto !important; 
        min-height: 52px !important;
        font-size: 0.95rem !important;
        display: block;
        width: 100%;
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

    /* จัดระเบียบ Dropdown Select ไม่ให้ข้อความจม */
    select.form-control-premium {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 16px center !important;
        background-size: 12px 12px !important;
        padding-right: 42px !important;
    }

    /* ปรับแต่ง Input Group ให้สมดุลไม่เบียดตัวอักษร */
    .input-group .form-control-premium {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }
    .input-group-text {
        background: rgba(30, 41, 59, 0.8) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        color: #94a3b8 !important;
        padding: 0 16px !important;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-label-premium {
        color: #cbd5e1 !important;
        font-weight: 500;
        margin-bottom: 8px;
        font-size: 0.88rem;
        display: inline-block;
    }
    .form-control-premium::placeholder {
        color: #64748b !important;
    }

    /* Visual Builder Drag & Drop Blocks Layout */
    .vlb-stage {
        background: rgba(0, 0, 0, 0.2);
        border: 2px dashed rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 16px;
        min-height: 180px;
    }
    .vlb-block-item {
        background: rgba(30, 41, 59, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        cursor: grab;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .vlb-block-item:active { cursor: grabbing; }

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
        background: rgba(0, 0, 0, 0.2);
        padding: 12px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.04);
    }
    @media (min-width: 992px) {
        .analytics-card {
            border: none;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 0;
            background: transparent;
            padding: 0 8px;
        }
        .analytics-card:last-child {
            border-right: none;
        }
    }

    @keyframes fadeInLab {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="lab-container">
    <!-- Top Action Info Bar -->
    <div class="d-flex justify-content-between align-items-start mb-4 flex-column flex-md-row gap-3">
        <div class="w-100">
            <h3 class="text-white fw-bold mb-1 fs-4"><i class="bi bi-terminal-fill text-primary me-2"></i>ระบบสร้างและจัดการห้องปฏิบัติการ (Lab Builder)</h3>
            <p class="text-muted small mb-0">สร้างและกำหนดสภาพแวดล้อมจำลอง (Sandbox) สำหรับจัดทำคอร์สปฏิบัติการ Coding, Linux, SQL, Docker และระบบความปลอดภัยไซเบอร์</p>
        </div>
    </div>

    <form id="labMasterForm" onsubmit="event.preventDefault();">

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
                    <input type="text" class="form-control form-control-premium" value="การจัดการและเชื่อมต่อ Container ด้วย Docker Network">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label form-label-premium">2. ประเภทปฏิบัติการ (Lab Type)</label>
                    <select class="form-select form-control-premium">
                        <option>Coding Lab</option>
                        <option>Linux Lab</option>
                        <option>SQL Lab</option>
                        <option selected>Docker Lab</option>
                        <option>Kubernetes Lab</option>
                        <option>Network Lab</option>
                        <option>Cybersecurity Lab</option>
                        <option>Cloud Lab</option>
                        <option>DevOps Lab</option>
                        <option>AI / Machine Learning Lab</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label form-label-premium">ความยาก (Difficulty)</label>
                    <select class="form-select form-control-premium text-info fw-bold">
                        <option>Beginner (ระดับต้น)</option>
                        <option selected>Intermediate (ระดับกลาง)</option>
                        <option>Advanced (ระดับสูง)</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label form-label-premium">Chapter ผูกมัดหลักสูตร</label>
                    <select class="form-select form-control-premium">
                        <option>Chapter 3: Virtualization Concepts</option>
                        <option selected>Chapter 4: Containerization Workflow</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label form-label-premium">Lesson (บทเรียนย่อย)</label>
                    <select class="form-select form-control-premium">
                        <option selected>Lesson 4.3: Custom Bridge Network Configuration</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label form-label-premium">สถานะ (Status)</label>
                    <select class="form-select form-control-premium text-warning fw-bold">
                        <option value="draft" selected>🟡 Draft (บันทึกร่าง)</option>
                        <option value="published">🟢 Published (เปิดใช้งานจริง)</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label form-label-premium">คำอธิบายรายละเอียด Lab (Description)</label>
                    <textarea class="form-control form-control-premium" rows="2">ฝึกฝนทักษะการสร้าง Custom Network, การใช้คำสั่ง --network และตรวจสอบการสื่อสารเชื่อมต่อระหว่าง Container</textarea>
                </div>
            </div>
        </div>

        <!-- ==========================================
             ⭐ FEATURE 2: AI ENVIRONMENT GENERATOR
             ========================================== -->
        <div class="lab-section-card bg-purple bg-opacity-5" style="border: 1px solid rgba(168, 85, 247, 0.25);">
            <div class="section-title text-purple" style="color: #a855f7 !important;">
                <i class="bi bi-cpu-fill"></i> ⭐ AI Environment Generator (ตัวช่วยติดตั้งระบบอัตโนมัติ)
            </div>
            <p class="text-muted small mt-0 mb-3">พิมพ์เป้าหมายของ Lab ที่ต้องการเพื่อให้ AI ช่วยร่างชุดสภาพแวดล้อม โจทย์ ซอร์สโค้ดเริ่มต้น และ Test Cases ทันที</p>
            <div class="input-group">
                <input type="text" class="form-control form-control-premium" id="aiPromptInput" placeholder='เช่น "สร้าง Linux Lab สำหรับฝึกใช้งานคำสั่งพื้นฐาน" หรือ "สร้าง Node.js API เชื่อมต่อ MongoDB Lab"'>
                <button type="button" class="btn btn-purple text-white px-4" onclick="alert('AI กำลังสร้างสภาพแวดล้อมจำลอง กรุณารอสักครู่...')"><i class="bi bi-magic me-1"></i> สั่งการ AI</button>
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
                    <select class="form-select form-control-premium"><option selected>Ubuntu 22.04 LTS</option><option>Alpine Linux</option><option>Debian 12</option></select>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label form-label-premium">Programming Language</label>
                    <select class="form-select form-control-premium"><option selected>Bash / Shell</option><option>Node.js</option><option>Python</option><option>PHP</option></select>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label form-label-premium">Docker Image</label>
                    <input type="text" class="form-control form-control-premium" value="docker:24.0-dind">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label form-label-premium">Database Engine</label>
                    <select class="form-select form-control-premium"><option selected>None (ไม่ใช้ฐานข้อมูล)</option><option>MySQL 8.0</option><option>PostgreSQL 15</option></select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label form-label-premium">RAM Limit</label>
                    <select class="form-select form-control-premium"><option>512 MB</option><option selected>1 GB</option><option>2 GB</option></select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label form-label-premium">CPU Core Allocation</label>
                    <select class="form-select form-control-premium"><option selected>0.5 Core</option><option>1.0 Core</option><option>2.0 Core</option></select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label form-label-premium">Disk Storage</label>
                    <select class="form-select form-control-premium"><option selected>2 GB</option><option>5 GB</option></select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label form-label-premium">Session Timeout (นาที)</label>
                    <input type="number" class="form-control form-control-premium" value="45">
                </div>
                <div class="col-12">
                    <label class="form-label form-label-premium">6. Terminal Configuration (รองรับหลายหน้าต่าง)</label>
                    <div class="p-2 border border-secondary border-opacity-20 rounded bg-dark bg-opacity-30">
                        <div class="form-check form-check-inline small"><input type="checkbox" class="form-check-input" checked> Bash Shell</div>
                        <div class="form-check form-check-inline small"><input type="checkbox" class="form-check-input"> Zsh</div>
                        <div class="form-check form-check-inline small"><input type="checkbox" class="form-check-input"> PowerShell</div>
                        <div class="form-check form-check-inline small"><input type="checkbox" class="form-check-input"> CMD</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             ⭐ FEATURE 1: VISUAL LAB BUILDER (DRAG & DROP BLOCK)
             ========================================== -->
        <div class="lab-section-card border border-info border-opacity-20">
            <div class="section-title text-cyan">
                <i class="bi bi-ui-checks-grid"></i> ⭐ ฟีเจอร์เด่น: Visual Lab Builder Layout
            </div>
            <p class="text-muted small mt-0 mb-3">ลากจัดวางบล็อกหน้าต่างการแสดงผลตามลำดับความคุ้นเคย เพื่อปรับแต่งหน้าการทำแล็บของผู้เรียนได้อย่างอิสระ</p>
            
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="p-3 bg-dark bg-opacity-40 rounded border border-secondary border-opacity-20">
                        <span class="small fw-bold text-white mb-2 d-block">กล่องเครื่องมือบล็อกชิ้นส่วน</span>
                        <div class="badge bg-dark border border-secondary p-2 mb-1 w-100 text-start">📝 Instruction Block</div>
                        <div class="badge bg-dark border border-secondary p-2 mb-1 w-100 text-start">💻 Code Editor Block</div>
                        <div class="badge bg-dark border border-secondary p-2 mb-1 w-100 text-start">🖥️ Terminal Block</div>
                        <div class="badge bg-dark border border-secondary p-2 mb-1 w-100 text-start">🗄️ Database Block</div>
                        <div class="badge bg-dark border border-secondary p-2 mb-1 w-100 text-start">🌐 Browser Preview Block</div>
                    </div>
                </div>
                <div class="col-12 col-md-8">
                    <div class="vlb-stage">
                        <span class="small text-muted d-block mb-2 font-monospace"><i class="bi bi-grid-1x2"></i> วางโครงสร้าง Layout เรียงลำดับลงที่นี่:</span>
                        <div class="vlb-block-item"><span><i class="bi bi-text-left text-info me-2"></i> 1. Instruction Block (แถบแสดงโจทย์สั่งงาน)</span> <i class="bi bi-list text-muted"></i></div>
                        <div class="vlb-block-item"><span><i class="bi bi-terminal text-success me-2"></i> 2. Terminal Block (หน้าจอรับคำสั่งคอมพิวเตอร์)</span> <i class="bi bi-list text-muted"></i></div>
                        <div class="vlb-block-item"><span><i class="bi bi-check-all text-warning me-2"></i> 3. Test Case & Result Block (ตรวจคำตอบ)</span> <i class="bi bi-list text-muted"></i></div>
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
                    <textarea class="form-control form-control-premium font-monospace text-cyan small" rows="3" placeholder="ตัวอย่างโครงไฟล์ เช่น docker-compose.yml หรือ README.md"># Lab Configuration File&#10;version: '3.8'&#10;services:&#10;  web_node:&#10;    image: nginx:alpine</textarea>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label form-label-premium">5. สเปกค่าเบื้องต้นของหน้าต่าง Code Editor</label>
                    <div class="row g-2">
                        <div class="col-6"><select class="form-select form-control-premium small"><option>Theme: VS Dark</option><option>Theme: Monokai</option></select></div>
                        <div class="col-6"><select class="form-select form-control-premium small"><option>Font Size: 14px</option><option>Font Size: 16px</option></select></div>
                        <div class="col-6"><select class="form-select form-control-premium small"><option>Auto Save: ON</option><option>Auto Save: OFF</option></select></div>
                        <div class="col-6"><select class="form-select form-control-premium small"><option>Minimap: Hidden</option><option>Minimap: Visible</option></select></div>
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
                    <textarea class="form-control form-control-premium font-monospace small text-white" rows="3">1. รันคอนเทนเนอร์ตัวแรกชื่อ `web-app` บนน็ตเวิร์กบริดจ์ทั่วไป&#10;2. ตรวจสอบการ Ping ค้นหาชื่อโฮสต์ปลายทางภายในกลุ่มจำลอง</textarea>
                </div>
                <div class="col-12 col-lg-4">
                    <label class="form-label form-label-premium">10. AI Lab Copilot Actions</label>
                    <div class="row g-2">
                        <div class="col-6"><button type="button" class="btn btn-dark border border-secondary text-white w-100 py-2 small" onclick="alert('AI เจน Test Case สำเร็จ')"><i class="bi bi-node-plus"></i> ช่วยคิดสคริปต์ตรวจ</button></div>
                        <div class="col-6"><button type="button" class="btn btn-dark border border-secondary text-white w-100 py-2 small" onclick="alert('AI ตรวจคำสะกดเรียบร้อย')"><i class="bi bi-spellcheck"></i> ตรวจคำอธิบาย</button></div>
                        <div class="col-6"><button type="button" class="btn btn-dark border border-secondary text-white w-100 py-2 small" onclick="alert('AI สร้างกล่องความช่วยเหลือสำเร็จ')"><i class="bi bi-lightbulb"></i> ออกแบบคำใบ้</button></div>
                        <div class="col-6"><button type="button" class="btn btn-dark border border-secondary text-white w-100 py-2 small" onclick="alert('AI ตรวจคำตอบจำลองสำเร็จ')"><i class="bi bi-check-circle"></i> จำลองเฉลยโค้ด</button></div>
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
                    <input type="text" class="form-control form-control-premium font-monospace text-success small" value="docker network inspect custom-bridge">
                </div>
                <div class="col-12 col-md-5">
                    <label class="form-label form-label-premium">Expected Output Match</label>
                    <input type="text" class="form-control form-control-premium font-monospace text-warning small" value='"Subnet": "172.20.0.0/16"'>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label form-label-premium">คะแนน Test Case นี้</label>
                    <input type="number" class="form-control form-control-premium" value="20">
                </div>
                <div class="col-12">
                    <div class="p-2 border border-secondary border-opacity-20 rounded bg-dark bg-opacity-30 small">
                        <span class="d-block mb-2 fw-bold text-white-50">9. กฎและเงื่อนไขการให้ผ่านแล็บ (Validation Rules)</span>
                        <div class="form-check form-check-inline"><input type="checkbox" class="form-check-input" checked> รันชุดคำสั่งผ่านคอมไพเลอร์สำเร็จ</div>
                        <div class="form-check form-check-inline"><input type="checkbox" class="form-check-input" checked> ผลลัพธ์ (Output) ตรงกันครอบคลุม</div>
                        <div class="form-check form-check-inline"><input type="checkbox" class="form-check-input"> โครงสร้างไฟล์ในระบบ Explorer ครบถ้วน</div>
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
                    <input type="text" class="form-control form-control-premium small mb-2" value="คำใบ้ที่ 1: ตรวจสอบการใช้งานคำสั่ง docker network create ก่อน">
                    <input type="text" class="form-control form-control-premium small" value="คำใบ้ที่ 2: สามารถระบุเลข Subnet โดยต่อท้ายคำสั่ง --subnet=172.20.0.0/16">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label form-label-premium">12. Solution Management (ระบบคลังเฉลยสำหรับอาจารย์)</label>
                    <textarea class="form-control form-control-premium font-monospace small" rows="2" placeholder="ใส่ขั้นตอนหรือโค้ดเฉลยที่สมบูรณ์แบบ...">docker network create --driver bridge --subnet 172.20.0.0/16 custom-bridge</textarea>
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
                    <input type="number" class="form-control form-control-premium" value="100">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label form-label-premium">14. จำกัดจำนวนสิทธิ์การรันแล็บ</label>
                    <select class="form-select form-control-premium">
                        <option>ไม่จำกัดจำนวนครั้ง</option>
                        <option selected>จำกัดสูงสุด 5 ครั้งต่อคน</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label form-label-premium">15. Resource Files แนบเอกสารเพิ่ม</label>
                    <button type="button" class="btn btn-dark border border-secondary text-white w-100 py-2.5 small" onclick="alert('เปิดหน้าต่างไฟล์แนบ')"><i class="bi bi-paperclip"></i> แนบ PDF / API doc</button>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label form-label-premium">16. Skill Mapping</label>
                    <input type="text" class="form-control form-control-premium" value="Docker, Networks, SysAdmin">
                </div>
            </div>
        </div>

        <!-- ==========================================
             19. LAB PERFORMANCE ANALYTICS
             ========================================== -->
        <div class="lab-section-card bg-dark bg-opacity-20 border border-secondary border-opacity-40">
            <div class="section-title text-white-50">
                <i class="bi bi-bar-chart-line-fill"></i> 19. Lab Realtime Analytics Module (สถิติปฏิบัติการ)
            </div>
            <div class="analytics-grid font-monospace text-center">
                <div class="analytics-card"><div class="text-muted small">จำนวนผู้ทำสะสม</div><h5 class="fw-bold text-white mb-0 fs-6 mt-1">89 คน</h5></div>
                <div class="analytics-card"><div class="text-muted small">อัตราการผ่านด่าน</div><h5 class="fw-bold text-success mb-0 fs-6 mt-1">64.8%</h5></div>
                <div class="analytics-card"><div class="text-muted small">เวลาเฉลี่ย/รอบ</div><h5 class="fw-bold text-cyan mb-0 fs-6 mt-1">26 นาที</h5></div>
                <div class="analytics-card"><div class="text-muted small">จุดผิดบ่อยที่สุด</div><h5 class="fw-bold text-danger mb-0 fs-6 mt-1">Test Case 2 (44%)</h5></div>
                <div class="analytics-card"><div class="text-muted small">สถิติการเปิดคำใบ้</div><h5 class="fw-bold text-warning mb-0 fs-6 mt-1">Hint 1 (72 ครั้ง)</h5></div>
            </div>
        </div>

        <!-- ==========================================
             18. VERSION HISTORY TRACKING
             ========================================== -->
        <div class="lab-section-card">
            <div class="section-title"><i class="bi bi-clock-history"></i> 18. Version Control Logs</div>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 font-monospace text-white-50 small">
                <div><i class="bi bi-shield-check text-success"></i> Auto Save: บันทึกข้อมูลสภาพแวดล้อมสำรองลงระบบ Cloud แล้ว</div>
                <div>เวอร์ชันประวัติ: <span class="text-cyan">v1.4 (Draft Updated)</span></div>
            </div>
        </div>

        <!-- ==========================================
             20. PUBLISH HUB HUB ACTION CONTROL PANEL
             ========================================== -->
        <div class="p-3 rounded-4 bg-dark bg-opacity-50 border border-secondary border-opacity-30 d-flex justify-content-between align-items-center flex-column flex-md-row gap-3">
            <div class="d-flex w-100 w-md-auto justify-content-between gap-2">
                <button type="button" class="btn btn-outline-danger btn-sm py-2 px-3" onclick="alert('ลบแล็บข้อนี้เรียบร้อย')"><i class="bi bi-trash-fill"></i> ลบ</button>
                <button type="button" class="btn btn-outline-secondary btn-sm text-white-50 py-2 px-3" onclick="alert('คัดลอกโคลนแล็บสำเร็จ')"><i class="bi bi-copy"></i> Duplicate</button>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
                <button type="button" class="btn btn-outline-primary text-cyan py-2 px-3" data-bs-toggle="modal" data-bs-target="#labSimulatorModal"><i class="bi bi-eye-fill me-1"></i> 17. Preview Lab</button>
                <button type="button" class="btn btn-secondary text-white py-2 px-3" onclick="alert('บันทึกร่างแล็บสำเร็จ')"><i class="bi bi-cloud-check-fill"></i> Save Draft</button>
                <button type="button" class="btn btn-primary text-white py-2 px-4 fw-bold" onclick="alert('เผยแพร่แล็บชุดปฏิบัติการสำเร็จ')"><i class="bi bi-rocket-takeoff-fill"></i> Publish Lab</button>
            </div>
        </div>

    </form>
</div>

<!-- 17. PREVIEW LAB SIMULATOR MODAL -->
<div class="modal fade" id="labSimulatorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content bg-dark text-white border border-secondary border-opacity-20">
            <div class="modal-header border-secondary border-opacity-20 bg-black bg-opacity-30">
                <h6 class="modal-title fw-bold text-primary"><i class="bi bi-laptop me-2"></i>17. Preview Mode (มุมมองจริงของผู้เรียน)</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 bg-black bg-opacity-20">
                <div class="row g-2">
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded bg-dark border border-secondary border-opacity-20 h-100">
                            <h6 class="text-white border-bottom border-secondary pb-2 small">📝 โจทย์และคำสั่งปฏิบัติการ</h6>
                            <p class="text-white-50 small mt-2">จงสร้าง Custom Bridge Network ในระบบ Docker โดยกำหนดชุดไอพี Subnet ให้เป็นไปตามโจทย์ระบุ</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-8">
                        <div class="p-3 rounded bg-black text-success font-monospace small mb-2 border border-secondary" style="min-height: 140px;">
                            root@sandbox-sandbox:~# <span class="text-white animate-blink">|</span>
                        </div>
                        <div class="p-2 rounded bg-dark border border-secondary border-opacity-40 text-muted small text-end">
                            <span class="me-2"><i class="bi bi-circle-fill text-danger small"></i> Test Case 1</span>
                            <span><i class="bi bi-circle-fill text-danger small"></i> Test Case 2</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
