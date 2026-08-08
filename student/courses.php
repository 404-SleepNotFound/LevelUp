<?php
$ROOT_PATH = '../';
$page_title = "Explore Courses - LevelUp";

include_once $ROOT_PATH . 'includes/header.php';

// ----------------------------------------------------------------------
// 1. ดึงข้อมูลคอร์สเรียน (รองรับทั้ง PDO และ MySQLi)
// ----------------------------------------------------------------------

if (!isset($conn) || !$conn) {
    if (file_exists($ROOT_PATH . 'config/db.php')) {
        require_once $ROOT_PATH . 'config/db.php';
    } elseif (file_exists($ROOT_PATH . 'db.php')) {
        require_once $ROOT_PATH . 'db.php';
    }
}

$db = $conn ?? $pdo ?? null;
$raw_courses = [];

if ($db) {
    try {
        if ($db instanceof PDO) {
            try {
                $sql = "SELECT c.*, COALESCE(cat.name, 'ทั่วไป') AS category_name 
                        FROM `course` c 
                        LEFT JOIN `category` cat ON cat.id = c.category_id";
                $stmt = $db->query($sql);
                if ($stmt) $raw_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $sql = "SELECT c.*, COALESCE(cat.name, 'ทั่วไป') AS category_name 
                        FROM `courses` c 
                        LEFT JOIN `category` cat ON cat.id = c.category_id";
                $stmt = $db->query($sql);
                if ($stmt) $raw_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } elseif ($db instanceof mysqli) {
            $res = @$db->query("SELECT c.*, COALESCE(cat.name, 'ทั่วไป') AS category_name FROM `course` c LEFT JOIN `category` cat ON cat.id = c.category_id");
            if (!$res) {
                $res = @$db->query("SELECT c.*, COALESCE(cat.name, 'ทั่วไป') AS category_name FROM `courses` c LEFT JOIN `category` cat ON cat.id = c.category_id");
            }
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $raw_courses[] = $row;
                }
            }
        }
    } catch (Exception $e) {
        error_log("Course Query Error: " . $e->getMessage());
    }
}

// กำหนดรูปภาพสำรองมาตรฐาน (เหมือนใน index.php)
$fallbackImg = 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=600&auto=format&fit=crop';

$course_details = [];
foreach ($raw_courses as $item) {
    $rawImg = $item['cover_file'] ?? $item['cover'] ?? $item['img'] ?? $item['image'] ?? $item['thumbnail'] ?? '';
    $courseImg = $fallbackImg;

    // ระบบจัดการ Path รูปภาพ (ถอดแบบมาจาก index.php)
    if (!empty($rawImg)) {
        if (filter_var($rawImg, FILTER_VALIDATE_URL)) {
            $courseImg = $rawImg;
        } elseif (file_exists($ROOT_PATH . $rawImg)) {
            $courseImg = $ROOT_PATH . $rawImg;
        } elseif (file_exists($ROOT_PATH . 'uploads/covers/' . basename($rawImg))) {
            $courseImg = $ROOT_PATH . 'uploads/covers/' . basename($rawImg);
        } elseif (file_exists($ROOT_PATH . 'uploads/' . basename($rawImg))) {
            $courseImg = $ROOT_PATH . 'uploads/' . basename($rawImg);
        } elseif (file_exists($ROOT_PATH . 'assets/images/default-course.jpg')) {
            $courseImg = $ROOT_PATH . 'assets/images/default-course.jpg';
        }
    } elseif (file_exists($ROOT_PATH . 'assets/images/default-course.jpg')) {
        $courseImg = $ROOT_PATH . 'assets/images/default-course.jpg';
    }

    $course_details[] = [
        'course_id'     => $item['id'] ?? $item['course_id'] ?? 0,
        'course_name'   => $item['title'] ?? $item['course_title'] ?? $item['name'] ?? $item['course_name'] ?? 'ไม่มีชื่อคอร์ส',
        'course_img'    => $courseImg,
        'difficulty'    => $item['difficulty'] ?? $item['course_difficulty'] ?? $item['level'] ?? 'Beginner',
        'price'         => isset($item['price']) ? (float)$item['price'] : 0,
        'rating'        => isset($item['rating']) ? (float)$item['rating'] : 5.0,
        'category_name' => $item['category_name'] ?? 'ทั่วไป'
    ];
}

// ----------------------------------------------------------------------
// 2. ดึงข้อมูลหมวดหมู่ทั้งหมด (เพิ่มระบบป้องกันข้อมูลซ้ำ)
// ----------------------------------------------------------------------
$category_data = [];
if ($db) {
    try {
        $raw_categories = [];
        if ($db instanceof PDO) {
            try {
                $cat_sql = "SELECT id, name FROM `category` WHERE name IS NOT NULL AND TRIM(name) != '' AND status = 1 ORDER BY id ASC";
                $stmt = $db->query($cat_sql);
                if ($stmt) $raw_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $cat_sql = "SELECT id, name FROM `category` WHERE name IS NOT NULL AND TRIM(name) != '' ORDER BY id ASC";
                $stmt = $db->query($cat_sql);
                if ($stmt) $raw_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } elseif ($db instanceof mysqli) {
            $res = @$db->query("SELECT id, name FROM `category` WHERE name IS NOT NULL AND TRIM(name) != '' AND status = 1 ORDER BY id ASC");
            if (!$res) {
                $res = @$db->query("SELECT id, name FROM `category` WHERE name IS NOT NULL AND TRIM(name) != '' ORDER BY id ASC");
            }
            if ($res) {
                while ($row = $res->fetch_assoc()) $raw_categories[] = $row;
            }
        }

        $seen_names = []; // Array สำหรับตรวจสอบชื่อซ้ำ
        foreach ($raw_categories as $cat) {
            $clean_name = trim(preg_replace('/[\s\xA0]+/u', ' ', $cat['name']));
            // กรองข้อมูลไม่ให้ชื่อหมวดหมู่ซ้ำกัน
            if (!empty($clean_name) && !in_array($clean_name, $seen_names, true)) {
                $seen_names[] = $clean_name;
                $category_data[] = [
                    'id'   => $cat['id'],
                    'name' => $clean_name
                ];
            }
        }
    } catch (Exception $e) {
        error_log("Category Query Error: " . $e->getMessage());
    }
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- 🎨 Custom Styles -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap');

    :root, html[data-theme="dark"] {
        --lv-bg-dark: #090d1a;
        --lv-card-bg: rgba(20, 27, 45, 0.85);
        --lv-card-hover: rgba(28, 38, 62, 0.95);
        --lv-border-glow: rgba(99, 102, 241, 0.25);
        --lv-border-hover: rgba(6, 182, 212, 0.5);
        --lv-primary: #6366F1;
        --lv-secondary: #8B5CF6;
        --lv-cyan: #06b6d4;
        --lv-success: #10B981;
        --lv-warning: #F59E0B;
        --lv-text-main: #f8fafc;
        --lv-text-muted: #cbd5e1;
        --lv-surface: rgba(20, 27, 45, 0.6);
        --lv-input-bg: rgba(15, 23, 42, 0.9);
        --lv-hero-grad: linear-gradient(180deg, rgba(15, 23, 42, 0.8) 0%, #0b1124 100%);
    }

    html[data-theme="light"] {
        --lv-bg-dark: #f8fafc;
        --lv-card-bg: rgba(255, 255, 255, 0.85);
        --lv-card-hover: rgba(240, 244, 248, 0.95);
        --lv-border-glow: rgba(99, 102, 241, 0.2);
        --lv-border-hover: rgba(99, 102, 241, 0.5);
        --lv-text-main: #0f172a;
        --lv-text-muted: #475569;
        --lv-surface: rgba(255, 255, 255, 0.8);
        --lv-input-bg: rgba(255, 255, 255, 0.9);
        --lv-hero-grad: linear-gradient(180deg, rgba(241, 245, 249, 0.8) 0%, #e2e8f0 100%);
    }

    body {
        background-color: var(--lv-bg-dark) !important;
        font-family: 'Outfit', 'Kanit', sans-serif;
        color: var(--lv-text-main) !important;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .text-main { color: var(--lv-text-main) !important; }
    .text-muted-custom { color: var(--lv-text-muted) !important; }

    /* AI Hero Section */
    .ai-hero-section {
        background: radial-gradient(circle at 50% 90%, rgba(99, 102, 241, 0.18) 0%, transparent 60%),
            var(--lv-hero-grad) !important;
        border: 1px solid var(--lv-border-glow);
        position: relative;
        overflow: hidden;
        border-radius: 16px;
    }

    .ai-hero-section p.ai-desc-text {
        color: var(--lv-text-muted) !important;
        font-weight: 500;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .ai-search-box {
        background: var(--lv-input-bg) !important;
        border: 1px solid rgba(6, 182, 212, 0.4) !important;
        border-radius: 50px;
        padding: 6px;
        box-shadow: 0 0 25px rgba(6, 182, 212, 0.15);
    }

    .ai-search-box input {
        background: transparent !important;
        color: var(--lv-text-main) !important;
    }

    .ai-search-box input::placeholder {
        color: var(--lv-text-muted) !important;
        font-weight: 300;
    }

    /* Filter Bar */
    .horizontal-filter-bar {
        background: var(--lv-surface) !important;
        border: 1px solid var(--lv-border-glow) !important;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(20px);
        border-radius: 16px !important;
        position: relative;
        z-index: 1000 !important;
    }


    /* Page-specific custom dropdown styles are now centralized in css/dropdown-select.css */

    /* Card Layout */
    .course-gamified-card {
        background: var(--lv-card-bg) !important;
        border: 1px solid var(--lv-border-glow) !important;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .course-gamified-card:hover {
        transform: translateY(-5px);
        border-color: var(--lv-border-hover) !important;
        box-shadow: 0 15px 30px rgba(6, 182, 212, 0.15);
    }

    .course-image-wrapper {
        position: relative;
        height: 160px;
        background: linear-gradient(135deg, #1e1b4b 0%, #311042 100%);
    }

    .course-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .reward-badge-pill {
        position: absolute;
        top: 12px;
        right: 12px;
        background: linear-gradient(135deg, var(--lv-secondary) 0%, #4f46e5 100%);
        color: #fff !important;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 30px;
        z-index: 2;
    }

    .tier-indicator {
        position: absolute;
        top: 12px;
        left: 12px;
        background: rgba(11, 15, 25, 0.9);
        color: #fff !important;
        padding: 4px 10px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
        z-index: 2;
    }

    .tier-beginner { color: #f59e0b !important; }
    .tier-intermediate { color: #06b6d4 !important; }
    .tier-advanced { color: #ec4899 !important; }

    .shimmer-card {
        background: var(--lv-surface);
        border: 1px solid var(--lv-border-glow);
        border-radius: 16px;
        height: 340px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .horizontal-filter-bar {
            flex-direction: column !important;
            align-items: stretch !important;
            padding: 1rem !important;
        }

        .filter-group-wrapper {
            width: 100% !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 10px !important;
        }

        .clear-btn-container {
            width: 100% !important;
            text-align: center !important;
            margin-top: 6px;
            padding-top: 10px;
            border-top: 1px dashed var(--lv-border-glow);
        }

        .ai-search-box {
            flex-direction: column !important;
            border-radius: 18px !important;
            padding: 10px !important;
        }

        .ai-search-box input {
            width: 100% !important;
            text-align: center;
            font-size: 0.85rem !important;
            padding: 8px 12px !important;
        }

        .ai-search-box button {
            width: 100% !important;
            margin: 4px 0 0 0 !important;
            border-radius: 12px !important;
        }

        .top-action-bar {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
            text-align: center;
        }

        .top-action-bar .sort-wrapper {
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding-top: 10px;
            border-top: 1px dashed var(--lv-border-glow);
        }
    }
</style>

<div class="container py-4">
    <!-- Include Welcome Header Panel -->
    <?php include_once $ROOT_PATH . 'includes/welcome-header.php'; ?>

    <!-- AI Hero Section -->
    <section class="ai-hero-section p-4 text-center text-main mb-4">
        <h2 class="fw-bold mb-2 text-main" style="font-size: 1.8rem;">
            <i class="fas fa-sparkles text-warning me-2"></i> AI Course Pathfinder
        </h2>
        <p class="small mb-3 ai-desc-text" style="max-width: 600px; margin: 0 auto;">บอกเป้าหมายอาชีพของคุณ ระบบ AI จะทำการวิเคราะห์และจัดสรรแผนการเก็บเลเวลไอเทมวิชาให้ทันที</p>
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="ai-search-box d-flex align-items-center">
                    <i class="fas fa-robot text-info ms-3 fs-5 d-none d-md-inline-block"></i>
                    <input type="text" id="ai-search-input" class="form-control shadow-none border-0 py-2 ps-3 text-main" placeholder="เช่น 'อยากทำงานเป็น Data Analyst ภายใน 6 เดือน'...">
                    <button class="btn btn-info rounded-pill text-nowrap flex-shrink-0 px-4 m-1 text-white fw-bold" onclick="CourseExplorer.aiSearch()" style="background: linear-gradient(135deg, var(--lv-cyan) 0%, var(--lv-primary) 100%); border:none; min-height: 44px;">วิเคราะห์เควส</button>
                </div>
            </div>
        </div>
    </section>

    <!-- ระบบแผงฟิลเตอร์แนวนอน -->
    <div class="horizontal-filter-bar p-3 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex flex-wrap align-items-center gap-2 filter-group-wrapper">
            <span class="text-muted-custom fw-medium me-2 small d-none d-md-inline-block"><i class="fas fa-filter text-info me-1"></i> คัดกรองภารกิจ:</span>

            <!-- Dropdown 1: หมวดหมู่เนื้อหา -->
            <div class="dropdown custom-filter-dropdown lu-dropdown--compact">
                <button class="btn btn-dropdown-trigger dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                    <span><i class="fas fa-th-large text-primary me-2"></i>หมวดหมู่เนื้อหา</span>
                    <i class="fas fa-chevron-down small ms-2 opacity-50"></i>
                </button>
                <div class="dropdown-menu">
                    <div class="form-check mb-2">
                        <input class="form-check-input filter-radio" type="radio" name="category_filter" value="" id="cat_all" checked>
                        <label class="form-check-label ms-1" for="cat_all"><i class="fas fa-layer-group text-info me-1"></i> ทั้งหมด</label>
                    </div>
                    <?php if (!empty($category_data)): ?>
                        <?php foreach ($category_data as $category): ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input filter-radio" type="radio" name="category_filter" value="<?= htmlspecialchars($category['name']); ?>" id="cat_<?= htmlspecialchars($category['id']); ?>">
                                <label class="form-check-label ms-1" for="cat_<?= htmlspecialchars($category['id']); ?>"><?= htmlspecialchars($category['name']); ?></label>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="px-2 py-1 text-muted-custom small">ไม่พบหมวดหมู่เนื้อหา</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Dropdown 2: ระดับความท้าทาย -->
            <div class="dropdown custom-filter-dropdown lu-dropdown--compact">
                <button class="btn btn-dropdown-trigger dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                    <span><i class="fas fa-trophy text-warning me-2"></i>ระดับความท้าทาย</span>
                    <i class="fas fa-chevron-down small ms-2 opacity-50"></i>
                </button>
                <div class="dropdown-menu">
                    <div class="form-check mb-2">
                        <input class="form-check-input filter-radio" type="radio" name="level_filter" value="" id="lvl_all" checked>
                        <label class="form-check-label ms-1" for="lvl_all"><i class="fas fa-layer-group text-info me-1"></i> ทั้งหมด</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input filter-radio" type="radio" name="level_filter" value="Beginner" id="lvl1">
                        <label class="form-check-label ms-1" for="lvl1"><i class="fas fa-shield-alt tier-beginner me-1"></i> Beginner</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input filter-radio" type="radio" name="level_filter" value="Intermediate" id="lvl2">
                        <label class="form-check-label ms-1" for="lvl2"><i class="fas fa-shield-alt tier-intermediate me-1"></i> Intermediate</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input filter-radio" type="radio" name="level_filter" value="Advanced" id="lvl3">
                        <label class="form-check-label ms-1" for="lvl3"><i class="fas fa-shield-alt tier-advanced me-1"></i> Advanced</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input filter-radio" type="radio" name="level_filter" value="Expert" id="lvl4">
                        <label class="form-check-label ms-1" for="lvl4"><i class="fas fa-shield-alt tier-advanced me-1"></i> Expert</label>
                    </div>
                </div>
            </div>

            <!-- Dropdown 3: ฟังก์ชันห้องแล็บเสริม -->
            <div class="dropdown custom-filter-dropdown lu-dropdown--compact">
                <button class="btn btn-dropdown-trigger dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                    <span><i class="fas fa-flask text-success me-2"></i>ฟังก์ชันห้องแล็บเสริม</span>
                    <i class="fas fa-chevron-down small ms-2 opacity-50"></i>
                </button>
                <div class="dropdown-menu">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input filter-checkbox" type="checkbox" id="feat1">
                        <label class="form-check-label ms-1" for="feat1"><i class="fas fa-robot text-info me-1"></i> ระบบ AI Tutor</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input filter-checkbox" type="checkbox" id="feat2">
                        <label class="form-check-label ms-1" for="feat2"><i class="fas fa-terminal text-success me-1"></i> Coding Lab</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="clear-btn-container">
            <button class="btn btn-sm btn-link btn-clear-glow text-decoration-none p-0 fw-bold" onclick="CourseExplorer.resetFilters()">
                <i class="fas fa-sync-alt me-1"></i> ล้างทั้งหมด
            </button>
        </div>
    </div>

    <!-- รายการคอร์สและกริดหลัก -->
    <div class="row">
        <main class="col-12">
            <!-- Top Action Bar -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 bg-surface p-3 rounded-4 shadow-sm gap-2 top-action-bar">
                <div class="text-main fw-medium small">
                    ตรวจพบภารกิจการเรียนรู้ทั้งหมด <span id="course-count" class="text-info fw-bold">0</span> รายการ
                </div>
                
                <div class="d-flex gap-2 align-items-center sort-wrapper">
                    <label class="text-main text-nowrap mb-0 small"><i class="fas fa-sort-amount-down me-1"></i>เรียงตาม:</label>
                    <div class="dropdown custom-filter-dropdown" id="sort-dropdown-container">
                        <button class="btn btn-dropdown-trigger dropdown-toggle py-1.5 px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="min-width: 170px;">
                            <span id="selected-sort-text">คอร์สยอดนิยมสูงสุด</span>
                            <i class="fas fa-chevron-down small ms-2 opacity-50"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow">
                            <div class="dropdown-item-custom sort-item active" data-value="popular">
                                <span>คอร์สยอดนิยมสูงสุด</span>
                                <i class="fas fa-check small text-info check-icon"></i>
                            </div>
                            <div class="dropdown-item-custom sort-item" data-value="latest">
                                <span>คอร์สใหม่ล่าสุด</span>
                                <i class="fas fa-check small text-info check-icon d-none"></i>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="sort-select" value="popular">
                </div>
            </div>

            <!-- Course Grid -->
            <div class="row g-4" id="course-grid">
                <div class="col-12 col-md-6 col-lg-4 col-xl-3"><div class="shimmer-card"></div></div>
                <div class="col-12 col-md-6 col-lg-4 col-xl-3"><div class="shimmer-card"></div></div>
                <div class="col-12 col-md-6 col-lg-4 col-xl-3"><div class="shimmer-card"></div></div>
                <div class="col-12 col-md-6 col-lg-4 col-xl-3"><div class="shimmer-card"></div></div>
            </div>
        </main>
    </div>
</div>

<!-- JavaScript Integration -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bundle.min.js"></script>
<script>
    <?php
    $mapped_courses = array_map(function ($item) use ($fallbackImg) {
        $rawDifficulty = trim($item['difficulty'] ?? 'Beginner');
        $difficulty = ucfirst(strtolower($rawDifficulty));

        switch ($difficulty) {
            case 'Beginner':
                $xpValue = 100;
                break;
            case 'Intermediate':
                $xpValue = 250;
                break;
            case 'Advanced':
                $xpValue = 500;
                break;
            case 'Expert':
                $xpValue = 1000;
                break;
            default:
                $xpValue = 100;
                break;
        }

        return [
            'id'       => (int)($item['course_id'] ?? 0),
            'title'    => $item['course_name'] ?? 'ไม่มีชื่อคอร์ส',
            'category' => trim(preg_replace('/[\s\xA0]+/u', ' ', $item['category_name'] ?? 'ทั่วไป')),
            'level'    => $difficulty,
            'xp'       => $xpValue,
            'img'      => !empty($item['course_img']) ? $item['course_img'] : $fallbackImg,
            'rating'   => isset($item['rating']) ? (float)$item['rating'] : 5.0,
            'price'    => isset($item['price']) ? (float)$item['price'] : 0,
            'ai'       => true,
            'lab'      => true
        ];
    }, $course_details);
    ?>

    const fallbackImage = "<?= $fallbackImg ?>";
    const mockCoursesData = <?= json_encode($mapped_courses, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    window.CourseExplorer = {
        init: function() {
            this.parseUrlParams();
            this.bindEvents();
            this.updateDropdownGlows();
            this.fetchCourses();
        },

        parseUrlParams: function() {
            const urlParams = new URLSearchParams(window.location.search);
            const categoryParam = urlParams.get('category');
            const searchParam = urlParams.get('q');

            if (categoryParam) {
                const decodedCat = decodeURIComponent(categoryParam).trim();

                let $targetRadio = $('input[name="category_filter"]').filter(function() {
                    return $(this).val().trim() === decodedCat || $(this).attr('id') === `cat_${decodedCat}`;
                });

                if ($targetRadio.length > 0) {
                    $('input[name="category_filter"]').prop('checked', false);
                    $targetRadio.prop('checked', true);
                } else {
                    $('#cat_all').prop('checked', true);
                }
            } else {
                if ($('input[name="category_filter"]:checked').length === 0) {
                    $('#cat_all').prop('checked', true);
                }
            }

            if (searchParam) {
                $('#ai-search-input').val(decodeURIComponent(searchParam).trim());
            }
        },

        bindEvents: function() {
            const self = this;
            
            $('.filter-checkbox, .filter-radio').on('change', function() {
                self.updateDropdownGlows();
                self.fetchCourses();
            });

            $('.sort-item').on('click', function() {
                let val = $(this).data('value');
                let text = $(this).find('span').text();
                
                $('#sort-select').val(val);
                $('#selected-sort-text').text(text);

                $('.sort-item').removeClass('active');
                $('.sort-item .check-icon').addClass('d-none');

                $(this).addClass('active');
                $(this).find('.check-icon').removeClass('d-none');

                self.fetchCourses();
            });

            $('#ai-search-input').on('keyup', function(e) {
                if (e.which === 13) {
                    self.aiSearch();
                } else {
                    self.fetchCourses();
                }
            });

            $(document).on('click', '.btn-start-quest', function() {
                let id = $(this).data('id');
                let title = $(this).data('title');
                let xp = $(this).data('xp');
                let price = $(this).data('price');

                if (window.CourseAlerts && typeof window.CourseAlerts.confirmStartQuest === 'function') {
                    window.CourseAlerts.confirmStartQuest(id, title, xp, price);
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'ยืนยันการเริ่มเควส?',
                        text: `คุณต้องการเข้าสู่บทเรียน "${title}" (+${xp} XP) หรือไม่?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#6366F1',
                        cancelButtonColor: '#64748B',
                        confirmButtonText: 'เข้าสู่เควส',
                        cancelButtonText: 'ยกเลิก',
                        customClass: {
                            popup: 'levelup-swal-popup',
                            title: 'levelup-swal-title',
                            htmlContainer: 'levelup-swal-text'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `course_detail.php?id=${id}`;
                        }
                    });
                } else {
                    if (confirm(`คุณต้องการเริ่มเรียนคอร์ส "${title}" หรือไม่?`)) {
                        window.location.href = `course_detail.php?id=${id}`;
                    }
                }
            });
        },

        updateDropdownGlows: function() {
            $('.custom-filter-dropdown').each(function() {
                let hasCheckedCheckbox = $(this).find('.filter-checkbox:checked').length > 0;
                let selectedRadioVal = $(this).find('.filter-radio:checked').val();
                let hasSelectedRadio = selectedRadioVal && selectedRadioVal !== "";

                let $triggerBtn = $(this).find('.btn-dropdown-trigger');
                if (hasCheckedCheckbox || hasSelectedRadio) {
                    $triggerBtn.addClass('active-filter');
                } else {
                    $triggerBtn.removeClass('active-filter');
                }
            });
        },

        fetchCourses: function() {
            let selectedCategory = $('input[name="category_filter"]:checked').val();
            let selectedLevel = $('input[name="level_filter"]:checked').val();
            let sortBy = $('#sort-select').val();
            let keyword = $('#ai-search-input').val().toLowerCase().trim();

            let filtered = mockCoursesData.filter(course => {
                if (selectedCategory && selectedCategory !== "" && course.category !== selectedCategory) return false;
                if (selectedLevel && selectedLevel !== "" && course.level !== selectedLevel) return false;
                if ($('#feat1').is(':checked') && !course.ai) return false;
                if ($('#feat2').is(':checked') && !course.lab) return false;
                if (keyword !== "" && !course.title.toLowerCase().includes(keyword) && !course.category.toLowerCase().includes(keyword)) return false;
                return true;
            });

            if (sortBy === 'popular') {
                filtered.sort((a, b) => b.rating - a.rating);
            } else if (sortBy === 'latest') {
                filtered.sort((a, b) => b.id - a.id);
            }

            this.renderCourseGrid(filtered);
        },

        renderCourseGrid: function(courses) {
            $('#course-count').text(courses.length);
            if (courses.length === 0) {
                $('#course-grid').html(`<div class="col-12 text-center py-5"><h6 class="text-muted-custom">ไม่พบภารกิจเรียนรู้ที่ตรงกับเงื่อนไข</h6></div>`);
                return;
            }

            let html = '';
            courses.forEach(course => {
                let tierClass = course.level === 'Beginner' ? 'tier-beginner' : (course.level === 'Intermediate' ? 'tier-intermediate' : 'tier-advanced');

                html += `
                <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                    <div class="course-gamified-card">
                        <div class="course-image-wrapper">
                            <span class="reward-badge-pill">+${course.xp} XP</span>
                            <span class="tier-indicator ${tierClass}"><i class="fas fa-shield-alt"></i> ${course.level}</span>
                            <img src="${course.img}" alt="${course.title}" onerror="this.onerror=null; this.src='${fallbackImage}';">
                        </div>
                        <div class="p-3 d-flex flex-column flex-grow-1 justify-content-between">
                            <div>
                                <span class="text-info font-monospace small" style="font-size:0.75rem;">${course.category}</span>
                                <h6 class="fw-bold text-main mt-1 mb-2" style="font-size:0.95rem; line-height:1.4; min-height:40px;">${course.title}</h6>
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="text-warning small"><i class="fas fa-star me-1"></i>${course.rating}</span>
                                    <span class="text-success small fw-bold">${course.price === 0 ? 'FREE' : '฿' + course.price.toLocaleString()}</span>
                                </div>
                            </div>
                            <div class="pt-2 border-top" style="border-color: var(--lv-border-glow) !important;">
                               <button type="button" 
                                       data-id="${course.id}" 
                                       data-title="${course.title.replace(/"/g, '&quot;')}" 
                                       data-xp="${course.xp}" 
                                       data-price="${course.price}" 
                                       class="btn btn-sm w-100 rounded-2 text-main fw-medium py-2 btn-start-quest" 
                                       style="background: var(--lv-surface); font-size:0.85rem;">เริ่มเควส</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            });
            $('#course-grid').html(html);
        },

        resetFilters: function() {
            const self = this;
            let performReset = function() {
                $('.filter-checkbox').prop('checked', false);
                $('#cat_all').prop('checked', true);
                $('#lvl_all').prop('checked', true);
                $('#ai-search-input').val('');
                
                $('#sort-select').val('popular');
                $('#selected-sort-text').text('คอร์สยอดนิยมสูงสุด');
                $('.sort-item').removeClass('active');
                $('.sort-item[data-value="popular"]').addClass('active');
                $('.sort-item .check-icon').addClass('d-none');
                $('.sort-item[data-value="popular"] .check-icon').removeClass('d-none');

                self.updateDropdownGlows();
                self.fetchCourses();
            };

            if (window.CourseAlerts && typeof window.CourseAlerts.confirmResetFilters === 'function') {
                window.CourseAlerts.confirmResetFilters(performReset);
            } else {
                performReset();
            }
        },

        aiSearch: function() {
            let val = $('#ai-search-input').val();
            if (window.CourseAlerts && typeof window.CourseAlerts.aiSearch === 'function') {
                window.CourseAlerts.aiSearch(val);
            } else {
                this.fetchCourses();
            }
        }
    };

    $(document).ready(function() {
        CourseExplorer.init();
    });
</script>

<?php include_once $ROOT_PATH . 'includes/footer.php'; ?>