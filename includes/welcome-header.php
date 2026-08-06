<?php
// บังคับตรวจสอบสิทธิ์ Session
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// โหลดไฟล์เชื่อมต่อฐานข้อมูลถ้ายังไม่มี
require_once __DIR__ . '/../config/db.php';

global $navUser, $conn;

// 🔍 STEP 1: หา ID ผู้ใช้จาก Session หรือ $navUser
$sessionData   = $_SESSION['user_login'] ?? null;
$currentUserId = $navUser['user_id'] ?? ($navUser['id'] ?? ($sessionData['id'] ?? ($_SESSION['user_id'] ?? null)));

// 🔍 STEP 2: คิวรีข้อมูลผู้ใช้ Real-time จากตาราง users พร้อม JOIN ตาราง status_users เพื่อดึง level และ exp
$dbUser = null;
if ($currentUserId && isset($conn)) {
    $stmtUser = $conn->prepare("
        SELECT u.*, su.level, su.exp 
        FROM `users` u 
        LEFT JOIN `status_users` su ON u.id = su.user_id 
        WHERE u.id = ?
    ");
    $stmtUser->execute([$currentUserId]);
    $dbUser = $stmtUser->fetch(PDO::FETCH_ASSOC);
}

// 🔍 STEP 3: แมปข้อมูลให้ตรงกับคอลัมน์ในฐานข้อมูลจริง (id, name, img, level, exp)
$user_id       = $dbUser['id'] ?? ($currentUserId ?? 'N');
$user_username = $dbUser['name'] ?? ($navUser['username'] ?? ($sessionData['name'] ?? 'Player One'));
$user_avatar   = !empty($dbUser['img']) 
                    ? $dbUser['img'] 
                    : ($navUser['avatar'] ?? ($sessionData['img'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($user_username)));

// ดึงค่า level และ exp จาก $dbUser (ตาราง status_users) ก่อน fallback ไปหา $navUser / session
$user_level    = $dbUser['level'] ?? ($navUser['level'] ?? ($sessionData['level'] ?? ($_SESSION['level'] ?? '1')));
$user_exp      = $dbUser['exp'] ?? ($navUser['exp'] ?? ($sessionData['exp'] ?? ($_SESSION['user_exp'] ?? '0')));

// คำนวณเปอร์เซ็นต์สำหรับหลอด EXP (แปลงค่า 'N' หรือ String ให้เป็นตัวเลขปลอดภัย)
$numeric_exp    = is_numeric($user_exp) ? (int)$user_exp : 0;
$max_exp        = 1000;
$exp_percentage = min(100, max(0, ($numeric_exp / $max_exp) * 100));
?>

<!-- 🎨 STYLES: COMPACT CYBERPUNK RPG EXP DASHBOARD (NO TICKS) -->
<style>
    :root, html[data-theme="dark"] {
        --dash-bg: rgba(11, 15, 25, 0.92);
        --dash-border: rgba(99, 102, 241, 0.25);
        --dash-inner-bg: rgba(18, 24, 40, 0.65);
        --dash-text-main: #ffffff;
        --dash-text-sub: #94a3b8;
        --dash-track-bg: rgba(0, 0, 0, 0.4);
        --dash-neon-cyan: #00f2fe;
        --dash-neon-purple: #9333ea;
        --dash-shadow: 0 12px 30px -8px rgba(0, 0, 0, 0.5);
    }

    html[data-theme="light"] {
        --dash-bg: #ffffff;
        --dash-border: rgba(99, 102, 241, 0.2);
        --dash-inner-bg: #f8fafc;
        --dash-text-main: #0f172a;
        --dash-text-sub: #64748b;
        --dash-track-bg: #cbd5e1;
        --dash-neon-cyan: #0284c7;
        --dash-neon-purple: #7c3aed;
        --dash-shadow: 0 8px 20px -4px rgba(99, 102, 241, 0.1);
    }

    /* Container Card */
    .dash-header-card {
        background: var(--dash-bg) !important;
        border: 1px solid var(--dash-border) !important;
        border-radius: 16px !important;
        padding: 16px 20px !important;
        box-shadow: var(--dash-shadow) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        font-family: 'Outfit', 'Kanit', sans-serif !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 12px !important;
        position: relative !important;
        overflow: hidden !important;
    }

    /* Top Row Layout */
    .dash-top-row {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 16px !important;
        flex-wrap: wrap !important;
    }

    .dash-profile-group {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
    }

    .dash-avatar-wrapper {
        position: relative !important;
        width: 54px !important;
        height: 54px !important;
        flex-shrink: 0 !important;
    }

    .dash-avatar-img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        border-radius: 50% !important;
        border: 2px solid var(--dash-neon-cyan) !important;
        box-shadow: 0 0 12px rgba(0, 242, 254, 0.35) !important;
    }

    .dash-user-details {
        display: flex !important;
        flex-direction: column !important;
        gap: 2px !important;
    }

    .dash-sub-title {
        font-size: 12px !important;
        color: var(--dash-text-sub) !important;
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
    }

    .dash-status-online {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        color: #10b981 !important;
        background: rgba(16, 185, 129, 0.12) !important;
        padding: 1px 6px !important;
        border-radius: 12px !important;
    }

    .dash-status-dot {
        width: 5px !important;
        height: 5px !important;
        background-color: #10b981 !important;
        border-radius: 50% !important;
        box-shadow: 0 0 5px #10b981 !important;
    }

    .dash-user-name {
        font-size: 18px !important;
        font-weight: 800 !important;
        color: var(--dash-text-main) !important;
        margin: 0 !important;
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
    }

    .dash-id-badge {
        font-size: 11px !important;
        color: var(--dash-neon-cyan) !important;
        background: rgba(6, 182, 212, 0.12) !important;
        border: 1px solid rgba(6, 182, 212, 0.3) !important;
        padding: 1px 5px !important;
        border-radius: 4px !important;
        font-weight: 700 !important;
    }

    /* Top-Right Stats Chips */
    .dash-stats-group {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        margin-left: auto !important;
    }

    .dash-stat-chip {
        background: var(--dash-inner-bg) !important;
        border: 1px solid rgba(255, 255, 255, 0.06) !important;
        padding: 6px 12px !important;
        border-radius: 10px !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }

    .dash-chip-icon {
        font-size: 14px !important;
        width: 26px !important;
        height: 26px !important;
        border-radius: 6px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .dash-chip-icon.streak { background: rgba(245, 158, 11, 0.15) !important; color: #f59e0b !important; }
    .dash-chip-icon.rank { background: rgba(99, 102, 241, 0.15) !important; color: #818cf8 !important; }

    .dash-chip-info {
        display: flex !important;
        flex-direction: column !important;
    }

    .dash-chip-label {
        font-size: 9px !important;
        color: var(--dash-text-sub) !important;
        font-weight: 700 !important;
    }

    .dash-chip-val {
        font-size: 12px !important;
        color: var(--dash-text-main) !important;
        font-weight: 800 !important;
    }

    /* Compact RPG EXP Hub */
    .rpg-exp-hub {
        background: var(--dash-inner-bg) !important;
        border: 1px solid rgba(255, 255, 255, 0.06) !important;
        border-radius: 12px !important;
        padding: 10px 14px !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 8px !important;
        position: relative !important;
    }

    .rpg-exp-top {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
    }

    .rpg-rank-title {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }

    .rpg-shield-icon {
        width: 24px !important;
        height: 24px !important;
        background: rgba(6, 182, 212, 0.15) !important;
        border: 1px solid var(--dash-neon-cyan) !important;
        border-radius: 6px !important;
        color: var(--dash-neon-cyan) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 12px !important;
    }

    .rpg-rank-text {
        font-size: 13px !important;
        font-weight: 800 !important;
        color: var(--dash-text-main) !important;
        letter-spacing: 0.5px !important;
    }

    /* Level Ring Badge Compact */
    .rpg-level-badge-container {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
    }

    .rpg-xp-val {
        font-size: 12px !important;
        color: var(--dash-text-sub) !important;
        font-weight: 700 !important;
    }

    .rpg-xp-val strong {
        color: var(--dash-neon-cyan) !important;
        font-size: 13px !important;
    }

    .rpg-level-ring {
        width: 34px !important;
        height: 34px !important;
        border-radius: 50% !important;
        background: radial-gradient(circle, rgba(6, 182, 212, 0.2) 0%, rgba(13, 18, 30, 0.8) 100%) !important;
        border: 2px solid var(--dash-neon-cyan) !important;
        box-shadow: 0 0 10px rgba(0, 242, 254, 0.4) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-weight: 900 !important;
        font-size: 14px !important;
        color: #ffffff !important;
    }

    /* Progress Track Compact */
    .rpg-track-wrapper {
        position: relative !important;
        padding-top: 14px !important;
    }

    .rpg-progress-track {
        background: var(--dash-track-bg) !important;
        height: 10px !important;
        border-radius: 50px !important;
        position: relative !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
    }

    .rpg-progress-fill {
        height: 100% !important;
        border-radius: 50px !important;
        background: linear-gradient(90deg, #7c3aed 0%, #6366f1 50%, #00f2fe 100%) !important;
        position: relative !important;
        transition: width 0.8s cubic-bezier(0.34, 1.25, 0.64, 1) !important;
    }

    .rpg-progress-fill::before {
        content: '' !important;
        position: absolute !important;
        top: 0 !important; left: 0; right: 0; bottom: 0 !important;
        background: repeating-linear-gradient(
            -45deg,
            transparent,
            transparent 6px,
            rgba(255, 255, 255, 0.2) 6px,
            rgba(255, 255, 255, 0.2) 12px
        ) !important;
        border-radius: 50px !important;
        animation: stripeMove 2s linear infinite !important;
    }

    @keyframes stripeMove {
        0% { background-position: 0 0; }
        100% { background-position: 24px 0; }
    }

    .rpg-fill-tip {
        position: absolute !important;
        right: -5px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        width: 12px !important;
        height: 12px !important;
        background: #ffffff !important;
        border: 2px solid var(--dash-neon-cyan) !important;
        border-radius: 50% !important;
        box-shadow: 0 0 10px var(--dash-neon-cyan) !important;
        animation: pulseOrb 1.5s infinite alternate !important;
        z-index: 4 !important;
    }

    @keyframes pulseOrb {
        0% { transform: translateY(-50%) scale(0.9); }
        100% { transform: translateY(-50%) scale(1.15); }
    }

    .rpg-floating-pin {
        position: absolute !important;
        top: -18px !important;
        right: -15px !important;
        background: linear-gradient(135deg, #00f2fe, #6366f1) !important;
        color: #ffffff !important;
        font-size: 9px !important;
        font-weight: 900 !important;
        padding: 1px 5px !important;
        border-radius: 4px !important;
        box-shadow: 0 2px 6px rgba(0, 242, 254, 0.4) !important;
        white-space: nowrap !important;
    }

    .rpg-floating-pin::after {
        content: '' !important;
        position: absolute !important;
        bottom: -3px !important;
        left: 50% !important;
        transform: translateX(-50%) !important;
        border-width: 3px 3px 0 3px !important;
        border-style: solid !important;
        border-color: #6366f1 transparent transparent transparent !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .dash-stats-group {
            margin-left: 0 !important;
            width: 100% !important;
            justify-content: space-between !important;
        }
        .dash-stat-chip { flex: 1 !important; }
    }

    @media (max-width: 576px) {
        .dash-profile-group {
            flex-direction: column !important;
            text-align: center !important;
            width: 100% !important;
        }
        .dash-user-name, .dash-sub-title { justify-content: center !important; }
    }
</style>

<!-- STRUCTURE: COMPACT DASHBOARD HEADER -->
<div class="dash-header-card">
    
    <!-- 1. Top Section -->
    <div class="dash-top-row">
        
        <!-- Profile Info -->
        <div class="dash-profile-group">
            <div class="dash-avatar-wrapper">
                <img class="dash-avatar-img" src="<?= htmlspecialchars($user_avatar) ?>" onerror="this.src='https://ui-avatars.com/api/?name=Player+One';" alt="Avatar">
            </div>

            <div class="dash-user-details">
                <div class="dash-sub-title">
                    <span>ยินดีต้อนรับกลับครับ</span>
                    <span class="dash-status-online"><i class="dash-status-dot"></i> ONLINE</span>
                </div>
                <h3 class="dash-user-name">
                    <?= htmlspecialchars($user_username) ?>
                    <span class="dash-id-badge">#<?= htmlspecialchars($user_id) ?></span>
                    <span style="font-size: 16px;">🚀</span>
                </h3>
            </div>
        </div>

        <!-- Right Stats Chips -->
        <div class="dash-stats-group">
            <div class="dash-stat-chip">
                <div class="dash-chip-icon streak">🔥</div>
                <div class="dash-chip-info">
                    <span class="dash-chip-label">STREAK</span>
                    <span class="dash-chip-val">3 วันต่อเนื่อง</span>
                </div>
            </div>

            <div class="dash-stat-chip">
                <div class="dash-chip-icon rank">🏆</div>
                <div class="dash-chip-info">
                    <span class="dash-chip-label">RANK</span>
                    <span class="dash-chip-val">Elite Coder</span>
                </div>
            </div>
        </div>

    </div>

    <!-- 2. Compact RPG EXP Hub -->
    <div class="rpg-exp-hub">
        
        <!-- Rank Title & Level Ring -->
        <div class="rpg-exp-top">
            <div class="rpg-rank-title">
                <div class="rpg-shield-icon">🛡️</div>
                <span class="rpg-rank-text">ELITE CODER</span>
            </div>

            <div class="rpg-level-badge-container">
                <div class="rpg-xp-val">
                    <strong><?= number_format($numeric_exp) ?></strong> / <?= number_format($max_exp) ?> XP
                </div>
                <div class="rpg-level-ring">
                    <?= htmlspecialchars($user_level) ?>
                </div>
            </div>
        </div>

        <!-- Progress Track -->
        <div class="rpg-track-wrapper">
            <div class="rpg-progress-track">
                
                <div class="rpg-progress-fill" style="width: <?= $exp_percentage ?>%;">
                    <div class="rpg-floating-pin">
                        <?= round($exp_percentage, 1) ?>%
                    </div>
                    <div class="rpg-fill-tip"></div>
                </div>

            </div>
        </div>

    </div>

</div>