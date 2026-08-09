<?php
$ROOT_PATH = '../';
/**
 * student-management-complete.php
 * - Fully Optimized Light / Dark Mode System
 * - Fixed: Transparent/Dark Inline Style overlays on Light Mode
 * - Fixed: Text contrast for Light Mode (.text-white / .text-white-50)
 * - Fixed: Form Select & Progress Bar background styling
 */

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Mock Data
$stats = [
    'total' => ['title' => 'ผู้เรียนทั้งหมด', 'val' => '1,482', 'sub' => '+12% เดือนนี้', 'glow' => 'rgba(34, 197, 94, 0.12)', 'text_color' => '#4ade80', 'border_color' => 'rgba(34, 197, 94, 0.2)'],
    'new' => ['title' => 'ผู้เรียนใหม่', 'val' => '145', 'sub' => '7 วันล่าสุด', 'glow' => 'rgba(56, 189, 248, 0.12)', 'text_color' => '#38bdf8', 'border_color' => 'rgba(56, 189, 248, 0.2)'],
    'active' => ['title' => 'กำลังเรียน', 'val' => '340', 'sub' => '⚡ ใน 24 ชม.', 'glow' => 'rgba(234, 179, 8, 0.12)', 'text_color' => '#eab308', 'border_color' => 'rgba(234, 179, 8, 0.2)'],
    'completed' => ['title' => 'เรียนจบแล้ว', 'val' => '912', 'sub' => '78.5%', 'glow' => 'rgba(168, 85, 247, 0.12)', 'text_color' => '#a855f7', 'border_color' => 'rgba(168, 85, 247, 0.2)'],
    'absent' => ['title' => 'ขาดการเรียน', 'val' => '85', 'sub' => '> 14 วันขึ้นไป', 'glow' => 'rgba(249, 115, 22, 0.12)', 'text_color' => '#f97316', 'border_color' => 'rgba(249, 115, 22, 0.2)'],
    'risk' => ['title' => 'เสี่ยงถอนตัว', 'val' => '14', 'sub' => 'AI ตรวจจับ', 'glow' => 'rgba(239, 68, 68, 0.15)', 'text_color' => '#ef4444', 'border_color' => 'rgba(239, 68, 68, 0.4)']
];

$mock_students = [
    ['id' => 'STU001', 'name' => 'ณัฐนันท์ พรหมดี', 'email' => 'natthanan.p@academy.io', 'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=200&auto=format&fit=crop', 'progress' => 41, 'level' => 5, 'risk' => 'High'],
    ['id' => 'STU002', 'name' => 'กิตติภพ กุลวงศ์', 'email' => 'kittiphop.k@academy.io', 'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=200&auto=format&fit=crop', 'progress' => 84, 'level' => 12, 'risk' => 'Low'],
    ['id' => 'STU003', 'name' => 'รมิตา ศรีสุข', 'email' => 'ramita.s@academy.io', 'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=200&auto=format&fit=crop', 'progress' => 100, 'level' => 25, 'risk' => 'Low']
];

$high_risk_students = array_filter($mock_students, function($s) { return $s['risk'] === 'High'; });
$low_risk_students  = array_filter($mock_students, function($s) { return $s['risk'] === 'Low'; });
?>

<!-- Import Dropdown Select Stylesheet -->
<link rel="stylesheet" href="<?= $ROOT_PATH ?>css/dropdown-select.css">

<style>
    /* 🌓 LIGHT / DARK MODE CSS VARIABLES */
    :root, html[data-theme="dark"], [data-bs-theme="dark"] {
        --sm-bg-main: #0b0f19;
        --sm-text-main: #f1f5f9;
        --sm-text-muted: #94a3b8;
        --sm-card-bg: rgba(17, 24, 39, 0.75);
        --sm-card-border: rgba(255, 255, 255, 0.08);
        --sm-stat-bg: rgba(30, 41, 59, 0.45);
        --sm-input-bg: rgba(15, 23, 42, 0.6);
        --sm-input-border: rgba(255, 255, 255, 0.1);
        --sm-table-th: #94a3b8;
        --sm-table-border: rgba(255, 255, 255, 0.04);
        --sm-btn-bg: rgba(30, 41, 59, 0.6);
        --sm-btn-hover: rgba(56, 189, 248, 0.2);
        --sm-progress-bg: rgba(255, 255, 255, 0.1);
        --sm-badge-bg: rgba(30, 41, 59, 0.8);
        --sm-header-bg: rgba(255, 255, 255, 0.02);
        --sm-card-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);

        /* Chat variables */
        --lv-bg-dark: #0b0f19;
        --lv-card-bg: rgba(17, 24, 39, 0.7);
        --lv-border-glow: rgba(99, 102, 241, 0.2);
        --lv-cyan: #06b6d4;
        --lv-indigo: #6366f1;
        --lv-text-main: #f1f5f9;
        --lv-text-muted: #94a3b8;
    }

    html[data-theme="light"], [data-bs-theme="light"] {
        --sm-bg-main: #f8fafc;
        --sm-text-main: #0f172a;
        --sm-text-muted: #64748b;
        --sm-card-bg: #ffffff;
        --sm-card-border: #e2e8f0;
        --sm-stat-bg: #ffffff;
        --sm-input-bg: #ffffff;
        --sm-input-border: #cbd5e1;
        --sm-table-th: #475569;
        --sm-table-border: #f1f5f9;
        --sm-btn-bg: #f1f5f9;
        --sm-btn-hover: #e2e8f0;
        --sm-progress-bg: #e2e8f0;
        --sm-badge-bg: #f1f5f9;
        --sm-header-bg: #f8fafc;
        --sm-card-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);

        /* Chat variables */
        --lv-bg-dark: #f8fafc;
        --lv-card-bg: rgba(255, 255, 255, 0.85);
        --lv-border-glow: rgba(99, 102, 241, 0.15);
        --lv-text-main: #0f172a;
        --lv-text-muted: #64748b;
    }

    body {
        background-color: var(--sm-bg-main) !important;
        color: var(--sm-text-main) !important;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* ☀️ Overrides for hardcoded utility classes in Light Mode */
    html[data-theme="light"] .stu-mgmt-wrapper {
        color: var(--sm-text-main);
    }
    html[data-theme="light"] .stu-mgmt-wrapper .text-white { 
        color: var(--sm-text-main) !important; 
    }
    html[data-theme="light"] .stu-mgmt-wrapper .text-white-50 { 
        color: var(--sm-text-muted) !important; 
    }
    html[data-theme="light"] .stu-mgmt-wrapper .bg-dark { 
        background-color: var(--sm-badge-bg) !important; 
        color: var(--sm-text-main) !important;
    }
    html[data-theme="light"] .stu-mgmt-wrapper .bg-black { 
        background-color: var(--sm-progress-bg) !important; 
    }
    html[data-theme="light"] .stu-mgmt-wrapper .border-white { 
        border-color: rgba(0,0,0,0.1) !important; 
    }

    /* Chat Section Overrides */
    html[data-theme="light"] .chat-history-item {
        color: #475569;
    }
    html[data-theme="light"] .chat-history-item:hover {
        background: rgba(0, 0, 0, 0.05);
        color: #0f172a;
    }
    html[data-theme="light"] .chat-header h5 {
        color: #0f172a !important;
    }
    html[data-theme="light"] #chatInput {
        color: #0f172a !important;
    }

    /* Custom Dropdown Arrow for Light Mode */
    html[data-theme="light"] .form-select.input-premium {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23334155' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
    }

    /* 🎯 GLOBAL SYSTEM */
    .stu-mgmt-wrapper {
        color: var(--sm-text-main);
        width: 100%;
        max-width: 100vw;
        overflow-x: hidden;
        font-family: 'Inter', 'Kanit', system-ui, sans-serif !important;
        box-sizing: border-box;
        transition: color 0.3s ease;
    }
    
    .stu-card-dark {
        background: var(--sm-card-bg) !important;
        border: 1px solid var(--sm-card-border) !important;
        border-radius: 16px;
        backdrop-filter: blur(20px);
        margin-bottom: 28px;
        box-shadow: var(--sm-card-shadow);
        transition: background 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
    }

    /* 📊 TOP CARDS WITH DYNAMIC BORDERS */
    .stat-card-premium {
        background: var(--sm-stat-bg);
        border-radius: 14px;
        padding: 20px 14px;
        text-align: center;
        position: relative;
        overflow: hidden;
        height: 100%;
        box-shadow: var(--sm-card-shadow);
        transition: background 0.3s ease, border-color 0.3s ease;
    }
    .stat-glow-effect {
        position: absolute; width: 60px; height: 60px; top: -30px; left: 50%;
        transform: translateX(-50%); border-radius: 50%; filter: blur(30px); opacity: 0.4; pointer-events: none;
    }
    .stat-label-new { color: var(--sm-text-muted); font-size: 0.78rem; font-weight: 500; letter-spacing: 0.3px; }
    .stat-value-new { font-size: 1.75rem; font-weight: 700; margin: 6px 0; font-family: 'Inter', sans-serif; }
    .stat-sub-new { font-size: 0.72rem; opacity: 0.85; }

    /* 🔍 FILTER BAR */
    .filter-panel-flex {
        background: var(--sm-card-bg); border: 1px solid var(--sm-card-border);
        border-radius: 14px; padding: 20px; display: flex; flex-direction: column; gap: 12px;
        box-shadow: var(--sm-card-shadow);
        transition: background 0.3s ease, border-color 0.3s ease;
    }
    .filter-row-top { display: flex; flex-wrap: wrap; gap: 12px; width: 100%; }
    .flex-search-input { flex: 2 1 300px; }
    .flex-select-item { flex: 1 1 160px; }
    .flex-btn-item { flex: 1 1 130px; }

    .input-premium {
        background: var(--sm-input-bg) !important; border: 1px solid var(--sm-input-border) !important;
        border-radius: 10px !important; color: var(--sm-text-main) !important; font-size: 0.88rem; padding: 10px 16px; width: 100%; height: 44px;
        transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;
    }
    .input-premium::placeholder { color: var(--sm-text-muted) !important; }

    /* 🧠 AI DIAGNOSTICS BAR (HIGH CONTRAST) */
    .ai-horizontal-container { display: flex; gap: 20px; flex-wrap: wrap; width: 100%; }
    .ai-box-row { flex: 1 1 calc(50% - 10px); min-width: 300px; padding: 22px 24px !important; border-radius: 14px; display: flex; flex-direction: column; justify-content: center; box-sizing: border-box; }
    
    .ai-box-row.danger { 
        background: rgba(239, 68, 68, 0.08); 
        border: 1px solid rgba(239, 68, 68, 0.25); 
        border-left: 5px solid #ef4444; 
    }
    .ai-box-row.success { 
        background: rgba(34, 197, 94, 0.08); 
        border: 1px solid rgba(34, 197, 94, 0.25); 
        border-left: 5px solid #22c55e; 
    }
    html[data-theme="light"] .ai-box-row.danger { background: rgba(239, 68, 68, 0.05); }
    html[data-theme="light"] .ai-box-row.success { background: rgba(34, 197, 94, 0.05); }

    .ai-box-row .fw-bold { display: flex; align-items: center; gap: 10px; margin-bottom: 8px !important; font-size: 0.95rem; line-height: 1.4; }
    .ai-box-row .text-custom-desc { line-height: 1.6; font-size: 0.88rem !important; color: var(--sm-text-main); opacity: 0.85; font-weight: 400; }

    /* Header for Card Tables */
    .card-header-custom {
        padding: 16px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--sm-header-bg);
        border-bottom: 1px solid var(--sm-card-border);
    }

    /* 🖥️ DESKTOP TABLE SYSTEM */
    .matrix-container-fixed { width: 100%; overflow-x: auto; }
    .table-premium { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .table-premium th.col-student-info, .table-premium td.col-student-info { width: 35%; }
    .table-premium th.col-progress, .table-premium td.col-progress { width: 25%; }
    .table-premium th.col-level, .table-premium td.col-level { width: 15%; }
    .table-premium th.col-risk, .table-premium td.col-risk { width: 15%; }
    .table-premium th.col-actions, .table-premium td.col-actions { width: 10%; }
    .table-premium th { color: var(--sm-table-th); font-weight: 600; font-size: 0.85rem; padding: 18px 24px; border-bottom: 1px solid var(--sm-card-border); text-transform: uppercase; letter-spacing: 0.5px; }
    .table-premium td { padding: 20px 24px; vertical-align: middle; border-bottom: 1px solid var(--sm-table-border); color: var(--sm-text-main); }
    
    .progress-track-custom {
        background-color: var(--sm-progress-bg) !important;
        height: 7px;
        border-radius: 10px;
    }

    /* 🏷️ BADGES & CLICKABLE ACTION BUTTON */
    .risk-pill { padding: 6px 14px; border-radius: 8px; font-size: 0.78rem; font-weight: 600; display: inline-flex; }
    .risk-pill-low { background: rgba(34, 197, 94, 0.1); color: #16a34a; border: 1px solid rgba(34, 197, 94, 0.25); }
    .risk-pill-high { background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.25); }
    
    html[data-theme="dark"] .risk-pill-low { color: #4ade80; }
    html[data-theme="dark"] .risk-pill-high { color: #fca5a5; }

    .btn-action-premium {
        background: var(--sm-btn-bg); 
        border: 1px solid var(--sm-input-border);
        border-radius: 8px; 
        color: var(--sm-text-main); 
        padding: 8px 14px; 
        font-size: 0.85rem; 
        transition: all 0.2s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }
    .btn-action-premium:hover { 
        background: var(--sm-btn-hover) !important; 
        border-color: rgba(56, 189, 248, 0.4);
        color: var(--sm-text-main) !important; 
    }
    .btn-action-premium i {
        pointer-events: none;
    }

    /* 📱 MOBILE RESPONSIVE ENGINE */
    @media (max-width: 768px) {
        .table-premium thead { display: none; }
        .table-premium, .table-premium tbody, .table-premium tr, .table-premium td { display: block; width: 100% !important; }
        .table-premium tr { padding: 20px; border-bottom: 1px solid var(--sm-table-border); position: relative; }
        .table-premium td { padding: 8px 0 !important; border: none !important; text-align: left !important; }
        .table-premium td.col-student-info { margin-bottom: 12px; }
        .table-premium td.col-progress::before { content: "ความคืบหน้า: "; font-size: 0.78rem; color: var(--sm-text-muted); display: block; margin-bottom: 6px; }
        .table-premium td.col-level { display: inline-block; width: auto !important; margin-right: 12px; }
        .table-premium td.col-risk { display: inline-block; width: auto !important; }
        .table-premium td.col-actions { position: absolute; top: 20px; right: 20px; width: auto !important; padding: 0 !important; }
    }
</style>

<div class="stu-mgmt-wrapper container-fluid py-4 px-3">
    
    <!-- 1️⃣ TOP STATS CARDS (Fixed Border Glow & Responsive Theme) -->
    <div class="row g-3 mb-4">
        <?php foreach($stats as $key => $item): ?>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card-premium" style="border: 1px solid <?= $item['border_color'] ?>;">
                <div class="stat-glow-effect" style="background: <?= $item['glow'] ?>;"></div>
                <div class="stat-label-new text-truncate"><?= $item['title'] ?></div>
                <div class="stat-value-new" style="color: <?= $item['text_color'] ?>"><?= $item['val'] ?></div>
                <div class="stat-sub-new text-truncate" style="color: <?= $item['text_color'] ?>; opacity: 0.85;"><?= $item['sub'] ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- 2️⃣ AI DIAGNOSTICS BAR -->
    <div class="stu-card-dark p-4 border border-warning border-opacity-20 mb-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <span class="fw-bold text-warning" style="font-size:0.9rem; letter-spacing: 0.5px;"><i class="bi bi-cpu-fill me-2"></i>AI DIAGNOSTICS SYSTEM</span>
            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 px-2.5 py-1" style="font-size:0.65rem; letter-spacing: 0.3px;">LIVE STATUS</span>
        </div>
        <div class="ai-horizontal-container">
            <div class="ai-box-row danger">
                <div class="text-danger fw-bold">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>เสี่ยงถอนตัว: ณัฐนันท์ พรหมดี</span>
                </div>
                <div class="text-custom-desc">ดัชนีส่งงานลดลง 60% ใน Coding Lab 3.2 แนะนำเข้าช่วยเหลือด่วน</div>
            </div>
            <div class="ai-box-row success">
                <div class="text-success fw-bold">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Learning Path Optimization</span>
                </div>
                <div class="text-custom-desc">ผู้เรียนมี Logic ด้าน Docker ดีเยี่ยม แนะนำให้ข้ามไปเรียน Kubernetes ได้ล่วงหน้า</div>
            </div>
        </div>
    </div>

    <!-- 3️⃣ FILTER PANEL -->
    <div class="filter-panel-flex mb-4">
        <div class="filter-row-top">
            <div class="flex-search-input">
                <input type="text" class="form-control input-premium" placeholder="🔍 ค้นหา ชื่อ, Email หรือ Student ID...">
            </div>
            <div class="flex-select-item">
                <select class="form-select input-premium"><option>คอร์สทั้งหมด</option></select>
            </div>
            <div class="flex-select-item">
                <select class="form-select input-premium"><option>ความเสี่ยง: ทั้งหมด</option></select>
            </div>
            <div class="flex-btn-item">
                <button type="button" class="btn input-premium text-start d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-download me-2 text-info"></i>Export</span>
                    <i class="bi bi-chevron-down small text-muted"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- 4️⃣ TABLES AREA -->
    <div class="row">
        <div class="col-12">
            
            <!-- 🔴 HIGH RISK BOX -->
            <div class="stu-card-dark p-0 overflow-hidden border border-danger border-opacity-20 mb-4">
                <div class="card-header-custom">
                    <span class="fw-bold text-danger" style="font-size:0.88rem; letter-spacing: 0.3px;"><i class="bi bi-exclamation-octagon-fill me-2"></i>กลุ่มเสี่ยงถอนตัวสูง (High Risk กลุ่มเฝ้าระวัง)</span>
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 px-2.5 py-1" style="font-size: 0.75rem; font-weight: 600;"><?= count($high_risk_students) ?> รายชื่อ</span>
                </div>
                <div class="matrix-container-fixed">
                    <table class="table-premium">
                        <thead>
                            <tr>
                                <th class="text-start col-student-info">ข้อมูลผู้เรียน</th>
                                <th class="col-progress">ความคืบหน้า (Progress)</th>
                                <th class="col-level">ระดับการเรียนรู้</th>
                                <th class="col-risk">ระดับความเสี่ยง (AI)</th>
                                <th class="text-end col-actions">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($high_risk_students as $student): ?>
                            <tr>
                                <td class="col-student-info">
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" class="form-check-input bg-transparent border-secondary flex-shrink-0 me-2" style="transform: scale(1.05);">
                                        <img src="<?= $student['avatar'] ?>" class="rounded-circle border border-white border-opacity-10 flex-shrink-0" style="width:42px; height:42px; object-fit:cover; margin-left: 8px;">
                                        <div class="lh-sm" style="margin-left:14px; min-width:0;">
                                            <div class="fw-bold text-white mb-1 text-truncate" style="font-size:0.95rem;"><?= $student['name'] ?></div>
                                            <div class="text-white-50 small text-truncate" style="font-size:0.78rem;"><?= $student['email'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="col-progress">
                                    <div class="d-flex align-items-center gap-3" style="width:100%;">
                                        <span class="text-white fw-bold" style="min-width:40px; font-size:0.85rem; font-family:'Inter';"><?= $student['progress'] ?>%</span>
                                        <div class="progress progress-track-custom flex-grow-1">
                                            <div class="progress-bar bg-danger" style="width: <?= $student['progress'] ?>%; border-radius: 10px;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="col-level">
                                    <span class="badge bg-dark border border-warning text-warning px-3 py-1.5 font-monospace" style="font-size:0.75rem; letter-spacing: 0.5px;">LV. <?= $student['level'] ?></span>
                                </td>
                                <td class="col-risk">
                                    <span class="risk-pill risk-pill-high"><?= $student['risk'] ?> Risk</span>
                                </td>
                                <td class="text-end col-actions">
                                    <a href="student-detail.php?id=<?= $student['id'] ?>" class="btn btn-action-premium" title="ดูรายละเอียด">
                                        <i class="bi bi-eye text-info"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 🟢 LOW RISK BOX -->
            <div class="stu-card-dark p-0 overflow-hidden border border-success border-opacity-20 mb-4">
                <div class="card-header-custom">
                    <span class="fw-bold text-success" style="font-size:0.88rem; letter-spacing: 0.3px;"><i class="bi bi-shield-check me-2"></i>กลุ่มสถานะปกติ (Low Risk / สภาพแวดล้อมเสถียร)</span>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-2.5 py-1" style="font-size: 0.75rem; font-weight: 600;"><?= count($low_risk_students) ?> รายชื่อ</span>
                </div>
                <div class="matrix-container-fixed">
                    <table class="table-premium">
                        <thead>
                            <tr>
                                <th class="text-start col-student-info">ข้อมูลผู้เรียน</th>
                                <th class="col-progress">ความคืบหน้า (Progress)</th>
                                <th class="col-level">ระดับการเรียนรู้</th>
                                <th class="col-risk">ระดับความเสี่ยง (AI)</th>
                                <th class="text-end col-actions">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($low_risk_students as $student): ?>
                            <tr>
                                <td class="col-student-info">
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" class="form-check-input bg-transparent border-secondary flex-shrink-0 me-2" style="transform: scale(1.05);">
                                        <img src="<?= $student['avatar'] ?>" class="rounded-circle border border-white border-opacity-10 flex-shrink-0" style="width:42px; height:42px; object-fit:cover; margin-left: 8px;">
                                        <div class="lh-sm" style="margin-left:14px; min-width:0;">
                                            <div class="fw-bold text-white mb-1 text-truncate" style="font-size:0.95rem;"><?= $student['name'] ?></div>
                                            <div class="text-white-50 small text-truncate" style="font-size:0.78rem;"><?= $student['email'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="col-progress">
                                    <div class="d-flex align-items-center gap-3" style="width:100%;">
                                        <span class="text-white fw-bold" style="min-width:40px; font-size:0.85rem; font-family:'Inter';"><?= $student['progress'] ?>%</span>
                                        <div class="progress progress-track-custom flex-grow-1">
                                            <div class="progress-bar <?= $student['progress'] === 100 ? 'bg-success' : 'bg-info' ?>" style="width: <?= $student['progress'] ?>%; border-radius: 10px;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="col-level">
                                    <span class="badge bg-dark border border-warning text-warning px-3 py-1.5 font-monospace" style="font-size:0.75rem; letter-spacing: 0.5px;">LV. <?= $student['level'] ?></span>
                                </td>
                                <td class="col-risk">
                                    <span class="risk-pill risk-pill-low"><?= $student['risk'] ?> Risk</span>
                                </td>
                                <td class="text-end col-actions">
                                    <a href="student-detail.php?id=<?= $student['id'] ?>" class="btn btn-action-premium" title="ดูรายละเอียด">
                                        <i class="bi bi-eye text-info"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>