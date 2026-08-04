<?php
$ROOT_PATH = '../';
// [Security Framework & Session Verification]
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>

<style>
    /* Scope Course Analytics Styles */
    .analytics-container {
        animation: fadeInAnalytics 0.4s ease-in-out;
        width: 100%;
        overflow-x: hidden;
        color: #f8fafc;
    }
    
    .analytics-section-card {
        background: rgba(16, 24, 48, 0.65);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 20px; 
        margin-bottom: 24px;
        backdrop-filter: blur(10px);
    }
    
    .section-title {
        font-size: 1.05rem;
        font-weight: 600;
        color: #f8fafc;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        padding-bottom: 14px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .section-title span {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .text-muted { color: rgba(255, 255, 255, 0.55) !important; }
    .text-white-50 { color: rgba(255, 255, 255, 0.65) !important; }

    /* KPI Cards */
    .kpi-card {
        background: rgba(30, 41, 59, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        padding: 18px;
        transition: transform 0.2s;
    }
    .kpi-card:hover { background: rgba(30, 41, 59, 0.6); }

    /* -------------------------------------
       [UPDATED] Modern Analytics Table (Transparent & Mobile Card)
       ------------------------------------- */
    .modern-analytics-table {
        width: 100%;
        border-collapse: collapse;
        color: #f8fafc;
        font-family: monospace;
        font-size: 0.9rem;
    }
    
    .modern-analytics-table th {
        font-weight: 500;
        color: rgba(255, 255, 255, 0.45);
        padding: 12px 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        text-align: left;
    }
    
    .modern-analytics-table th:last-child {
        text-align: right;
    }
    
    .modern-analytics-table td {
        padding: 18px 16px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        transition: background 0.2s;
    }
    
    .modern-analytics-table tbody tr:hover td {
        background: rgba(255, 255, 255, 0.03);
    }

    /* -------------------------------------
       [UPDATED] Thermal Heatmap (Green-Yellow-Red)
       ------------------------------------- */
    .heatmap-grid-container {
        margin-top: 20px;
        margin-bottom: 10px;
    }
    .heatmap-grid {
        display: grid;
        grid-template-columns: repeat(24, 1fr);
        gap: 4px;
    }
    .heatmap-cell {
        aspect-ratio: 1; 
        border-radius: 4px; 
        background: rgba(255, 255, 255, 0.05); /* ระดับ 0: ไม่มีคน */
        transition: transform 0.1s;
    }
    .heatmap-cell:hover {
        transform: scale(1.1);
        border: 1px solid rgba(255,255,255,0.5);
    }
    
    /* สี Heatmap โทน เขียว-เหลือง-แดง */
    .heatmap-cell.level-1 { background: #4ade80; } /* เขียวสว่าง (น้อย) */
    .heatmap-cell.level-2 { background: #eab308; } /* เหลืองทอง (ปานกลาง) */
    .heatmap-cell.level-3 { background: #ea580c; } /* ส้มแดง (เยอะ) */
    .heatmap-cell.level-4 { background: #b91c1c; } /* แดงเข้ม (มากสุด/Peak) */

    .heatmap-time-labels {
        display: flex;
        justify-content: space-between;
        margin-top: 8px;
        padding: 0 2px;
        color: rgba(255, 255, 255, 0.4);
        font-family: monospace;
        font-size: 0.75rem;
    }

    /* Animation สำหรับจุดสีเขียวออนไลน์ */
    @keyframes ping {
        75%, 100% { transform: scale(2); opacity: 0; }
    }
    .animate-ping { animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite; }

    /* Responsive adjustments */
    @media (max-width: 767px) {
        .heatmap-grid { gap: 2px; }
        .heatmap-cell { border-radius: 2px; }
        
        /* ปรับตารางเป็น Card Layout ในมือถือ ไม่ให้ชนขอบ */
        .modern-analytics-table, 
        .modern-analytics-table tbody, 
        .modern-analytics-table tr, 
        .modern-analytics-table td {
            display: block;
            width: 100%;
        }
        .modern-analytics-table thead {
            display: none;
        }
        .modern-analytics-table tbody tr {
            background: rgba(30, 41, 59, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            margin-bottom: 16px;
            padding: 16px;
        }
        .modern-analytics-table tbody tr:hover td {
            background: transparent;
        }
        .modern-analytics-table td {
            padding: 6px 0;
            border-bottom: none;
            text-align: left !important;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .modern-analytics-table td:nth-child(1) {
            font-weight: 600;
            font-size: 1rem;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.1);
            padding-bottom: 12px;
            margin-bottom: 8px;
        }
        .modern-analytics-table td:nth-child(3) {
            margin-top: 8px;
            font-weight: bold;
        }
    }
</style>

<div class="analytics-container">
    
    <!-- Header Title Bar & Real-time Users Monitoring -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-column flex-md-row gap-3 border-bottom border-secondary border-opacity-20 pb-4">
        <div>
            <h3 class="text-white fw-bold mb-2 fs-4"><i class="bi bi-graph-up-integrate text-info me-2"></i>คลังวิเคราะห์ข้อมูล (Course Analytics)</h3>
            <p class="text-muted small mb-0">เจาะลึกพฤติกรรมการเรียน, วัดอัตรา Drop-off, สถิติแล็บ, และ AI Copilot</p>
        </div>
        
        <!-- กล่อง Live Learners -->
        <div class="d-flex gap-3 align-items-center bg-dark bg-opacity-50 px-4 py-3 rounded-4 border border-success border-opacity-30 font-monospace shadow-sm">
            <div class="position-relative d-flex" style="width: 12px; height: 12px;">
                <span class="animate-ping position-absolute h-100 w-100 rounded-circle bg-success opacity-75"></span>
                <span class="position-relative rounded-circle h-100 w-100 bg-success"></span>
            </div>
            <div style="font-size: 0.85rem; line-height: 1.5;">
                <span class="text-success fw-bold fs-6">Live Learners Online: 42 คน</span><br>
                <span class="text-white-50">ทำควิซ: 8 <span class="mx-1 text-secondary">|</span> รันแล็บ: 14 <span class="mx-1 text-secondary">|</span> ใช้ AI: 20</span>
            </div>
        </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="text-muted small"><i class="bi bi-people-fill text-primary"></i> 2. ผู้เรียนสะสม</div>
                <div class="fs-3 fw-bold text-white mt-1">1,482 <span class="fs-6 fw-normal text-success">(+12%)</span></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="text-muted small"><i class="bi bi-award-fill text-success"></i> 3. อัตราเรียนจบ</div>
                <div class="fs-3 fw-bold text-success mt-1">78.5%</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="text-muted small"><i class="bi bi-chat-square-text-fill text-warning"></i> 11. รีวิวคอร์สเฉลี่ย</div>
                <div class="fs-3 fw-bold text-warning mt-1">4.87</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="text-muted small"><i class="bi bi-lightning-charge-fill" style="color:#a855f7;"></i> 8. เรียกใช้ AI</div>
                <div class="fs-3 fw-bold" style="color:#c084fc;">12,405</div>
            </div>
        </div>
    </div>

    <!-- Section ตาราง และ Revenue -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-12">
            <div class="analytics-section-card h-100">
                <div class="section-title">
                    <span><i class="bi bi-cpu-fill text-info"></i> 4, 5, 6, 7. รายละเอียดเจาะลึกระบบย่อยในคอร์ส</span>
                </div>
                
                <!-- ตารางแบบใหม่ (ไม่มีขอบทึบ กลืนไปกับพื้นหลัง) -->
                <div class="px-md-2">
                    <table class="modern-analytics-table">
                        <thead>
                            <tr>
                                <th>หมวดหมู่ระบบ</th>
                                <th>พารามิเตอร์ตรวจสอบหลัก</th>
                                <th>ผลลัพธ์ดัชนี</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><i class="bi bi-play-circle text-primary me-2"></i> 4. บทเรียน (Lessons)</td>
                                <td class="text-white-50">บทเรียนที่ถูกย้อนกลับมาดูบ่อยที่สุด (Re-watch Rate)</td>
                                <td class="text-md-end text-cyan">Lesson 3.2 (42%)</td>
                            </tr>
                            <tr>
                                <td><i class="bi bi-patch-question text-warning me-2"></i> 5. ควิซ (Quizzes)</td>
                                <td class="text-white-50">ข้อสอบข้อที่ทำผิดพลาดบ่อยที่สุด (Top Failure Item)</td>
                                <td class="text-md-end text-danger">ข้อ 4 (68% ผิด)</td>
                            </tr>
                            <tr>
                                <td><i class="bi bi-code-slash text-success me-2"></i> 6. โค้ดแล็บ (Coding)</td>
                                <td class="text-white-50">สัดส่วนความผิดพลาดระหว่าง Compile vs Runtime Error</td>
                                <td class="text-md-end text-white">65% / 35%</td>
                            </tr>
                            <tr>
                                <td><i class="bi bi-box-seam text-info me-2"></i> 7. แซนด์บ็อกซ์ (Virtual)</td>
                                <td class="text-white-50">การกดรีเซ็ตระบบปฏิบัติการ (Reset Containers Env)</td>
                                <td class="text-md-end text-warning">18 ครั้งสัปดาห์นี้</td>
                            </tr>
                            <tr>
                                <td><i class="bi bi-shield-check text-white me-2"></i> 13. ทักษะ (Skill Mapping)</td>
                                <td class="text-white-50">ช่องว่างระดับการเรียนรู้ที่แคบลงสูงสุด (Skill Gap Fixed)</td>
                                <td class="text-md-end text-info">Docker Commands</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- กล่อง Revenue -->
                <div class="mt-4 p-4 rounded-4 bg-dark bg-opacity-50 border border-secondary border-opacity-20 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted fw-bold"><i class="bi bi-currency-dollar text-success fs-5 align-middle"></i> 12. Revenue & Coupon Analytics</span>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-3 py-2 rounded-pill">คอร์สพรีเมียม</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-end font-monospace flex-wrap gap-2 mt-3">
                        <h2 class="fw-bold mb-0 text-white" style="letter-spacing: -0.5px;">฿242,500 <span class="fs-6 fw-normal text-muted ms-1">(เดือนนี้)</span></h2>
                        <span class="text-white-50 bg-black bg-opacity-30 px-3 py-2 rounded-3 small border border-white border-opacity-10">คืนเงิน (Refund): 0.2% <span class="mx-2">|</span> อัตราใช้คูปองลดราคา: 14%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 15. HEATMAP (GREEN-YELLOW-RED) -->
    <div class="analytics-section-card">
        <div class="section-title mb-1">
            <span><i class="bi bi-calendar3 text-success"></i> 15. แผนภูมิความหนาแน่นของผู้เรียน (Engagement Heatmap)</span>
        </div>
        <p class="text-muted small mt-0 mb-2">แสดงผลกราฟิกตามกระแสทราฟฟิกแต่ละชั่วโมงตลอด 24 ชั่วโมง</p>
        
        <!-- กล่องสีแยกทีละชั่วโมง -->
        <div class="heatmap-grid-container">
            <div class="heatmap-grid">
                <div class="heatmap-cell level-1"></div><div class="heatmap-cell"></div><div class="heatmap-cell"></div><div class="heatmap-cell"></div>
                <div class="heatmap-cell"></div><div class="heatmap-cell"></div><div class="heatmap-cell level-1"></div><div class="heatmap-cell level-2"></div>
                <div class="heatmap-cell level-2"></div><div class="heatmap-cell level-3"></div><div class="heatmap-cell level-1"></div><div class="heatmap-cell level-2"></div>
                <div class="heatmap-cell level-3"></div><div class="heatmap-cell level-4"></div><div class="heatmap-cell level-4"></div><div class="heatmap-cell level-4"></div>
                <div class="heatmap-cell level-3"></div><div class="heatmap-cell level-2"></div><div class="heatmap-cell level-3"></div><div class="heatmap-cell level-4"></div>
                <div class="heatmap-cell level-4"></div><div class="heatmap-cell level-3"></div><div class="heatmap-cell level-2"></div><div class="heatmap-cell level-1"></div>
            </div>
            <!-- ตัวเลขบอกเวลา -->
            <div class="heatmap-time-labels">
                <span>00:00</span>
                <span>06:00</span>
                <span>12:00</span>
                <span>18:00</span>
                <span>23:59</span>
            </div>
        </div>
        
        <!-- แถบอธิบายด้านล่าง (Legend) -->
        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top border-secondary border-opacity-10 font-monospace text-white-50 small flex-wrap gap-3">
            <div><i class="bi bi-clock-history me-1"></i> เวลาเฉลี่ยต่อเซสชัน: <strong class="text-white">42 นาที / คน</strong></div>
            
            <!-- ชุดสีตัวอย่าง -->
            <div class="d-flex align-items-center gap-2">
                <span>น้อย</span>
                <div class="d-flex gap-1">
                    <div class="heatmap-cell" style="width: 14px; height: 14px;"></div>
                    <div class="heatmap-cell level-1" style="width: 14px; height: 14px;"></div>
                    <div class="heatmap-cell level-2" style="width: 14px; height: 14px;"></div>
                    <div class="heatmap-cell level-3" style="width: 14px; height: 14px;"></div>
                    <div class="heatmap-cell level-4" style="width: 14px; height: 14px;"></div>
                </div>
                <span>มากสุด</span>
            </div>
        </div>
    </div>

</div>
