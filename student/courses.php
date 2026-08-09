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

// กำหนดรูปภาพสำรองมาตรฐาน
$fallbackImg = 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=600&auto=format&fit=crop';

$course_details = [];
foreach ($raw_courses as $item) {
    $rawImg = $item['cover_file'] ?? $item['cover'] ?? $item['img'] ?? $item['image'] ?? $item['thumbnail'] ?? '';
    $courseImg = $fallbackImg;

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
// 2. ดึงข้อมูลหมวดหมู่ทั้งหมด
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

        $seen_names = [];
        foreach ($raw_categories as $cat) {
            $clean_name = trim(preg_replace('/[\s\xA0]+/u', ' ', $cat['name']));
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

<!-- เรียกใช้ Dropdown CSS & JS (กรณีไม่ได้เรียกใน header.php หรือกันเหนียว) -->
<link rel="stylesheet" href="<?= $ROOT_PATH ?>css/dropdown-select.css">
<script src="<?= $ROOT_PATH ?>js/lu-dropdown.js" defer></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- 🎨 Custom Styles -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap');

    :root, html[data-theme="dark"] {
        --lv-bg-dark: #090d1a;
        --lv-card-bg: rgba(20, 27, 45, 0.85);
        --lv-border-glow: rgba(99, 102, 241, 0.25);
        --lv-border-hover: rgba(6, 182, 212, 0.5);
        --lv-primary: #6366F1;
        --lv-secondary: #8B5CF6;
        --lv-cyan: #06b6d4;
        --lv-text-main: #f8fafc;
        --lv-text-muted: #cbd5e1;
        --lv-surface: rgba(20, 27, 45, 0.6);
        --lv-input-bg: rgba(15, 23, 42, 0.9);
        --lv-hero-grad: linear-gradient(180deg, rgba(15, 23, 42, 0.8) 0%, #0b1124 100%);
    }

    html[data-theme="light"] {
        --lv-bg-dark: #f8fafc;
        --lv-card-bg: rgba(255, 255, 255, 0.85);
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

    /* Filter Bar แนวนอน */
    .horizontal-filter-bar {
        background: var(--lv-surface) !important;
        border: 1px solid var(--lv-border-glow) !important;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1);
        border-radius: 16px !important;
        position: relative;
        z-index: 1050 !important;
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 1rem !important;
    }

    .filter-group-wrapper {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        gap: 0.75rem !important;
        flex-grow: 1;
    }

    .select-item-container {
        min-width: 180px;
    }

    .clear-btn-container {
        white-space: nowrap !important;
        margin-left: auto !important;
    }

    .btn-clear-glow {
        color: #06b6d4 !important;
        transition: all 0.2s;
    }
    
    .btn-clear-glow:hover {
        color: #38bdf8 !important;
        text-shadow: 0 0 8px rgba(6, 182, 212, 0.6);
    }

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

    /* Responsive adjustments */
    @media (max-width: 991px) {
        .horizontal-filter-bar {
            flex-wrap: wrap !important;
        }
        
        .filter-group-wrapper {
            flex-wrap: wrap !important;
        }
    }

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

        .select-item-container {
            width: 100% !important;
        }

        .clear-btn-container {
            width: 100% !important;
            text-align: right !important;
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

    <!-- ระบบแผงฟิลเตอร์แนวนอน (ใช้ select.lu-select เพื่อรองรับ lu-dropdown.js) -->
    <div class="horizontal-filter-bar p-3 mb-4">
        <div class="filter-group-wrapper">
            <span class="text-main fw-bold me-1 small text-nowrap">
                <i class="fas fa-filter text-info me-1"></i> คัดกรองภารกิจ:
            </span>

            <!-- Dropdown 1: หมวดหมู่เนื้อหา -->
            <div class="select-item-container">
                <select class="lu-select" id="category_filter">
                    <option value="">หมวดหมู่ทั้งหมด</option>
                    <?php if (!empty($category_data)): ?>
                        <?php foreach ($category_data as $category): ?>
                            <option value="<?= htmlspecialchars($category['name']); ?>"><?= htmlspecialchars($category['name']); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Dropdown 2: ระดับความท้าทาย -->
            <div class="select-item-container">
                <select class="lu-select" id="level_filter">
                    <option value="">ระดับทั้งหมด</option>
                    <option value="Beginner">Beginner</option>
                    <option value="Intermediate">Intermediate</option>
                    <option value="Advanced">Advanced</option>
                    <option value="Expert">Expert</option>
                </select>
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
                
                <div class="d-flex gap-2 align-items-center sort-wrapper" style="min-width: 220px;">
                    <label class="text-main text-nowrap mb-0 small"><i class="fas fa-sort-amount-down me-1"></i>เรียงตาม:</label>
                    <select class="lu-select" id="sort-select">
                        <option value="popular">คอร์สยอดนิยมสูงสุด</option>
                        <option value="latest">คอร์สใหม่ล่าสุด</option>
                    </select>
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
            this.fetchCourses();
        },

        parseUrlParams: function() {
            const urlParams = new URLSearchParams(window.location.search);
            const categoryParam = urlParams.get('category');
            const searchParam = urlParams.get('q');

            if (categoryParam) {
                const decodedCat = decodeURIComponent(categoryParam).trim();
                $('#category_filter').val(decodedCat).trigger('change');
            }

            if (searchParam) {
                $('#ai-search-input').val(decodeURIComponent(searchParam).trim());
            }
        },

        bindEvents: function() {
            const self = this;
            
            $('#category_filter, #level_filter, #sort-select').on('change', function() {
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

                if (typeof LevelUpAlert !== 'undefined') {
                    LevelUpAlert.confirm(
                        'ยืนยันการเริ่มเควส?',
                        `คุณต้องการเข้าสู่บทเรียน "${title}" (+${xp} XP) หรือไม่?`
                    ).then((isConfirmed) => {
                        if (isConfirmed) {
                            window.location.href = `course-detail.php?id=${id}`;
                        }
                    });
                } else {
                    if (confirm(`คุณต้องการเข้าสู่บทเรียน "${title}" (+${xp} XP) หรือไม่?`)) {
                        window.location.href = `course-detail.php?id=${id}`;
                    }
                }
            });
        },

        fetchCourses: function() {
            let selectedCategory = $('#category_filter').val();
            let selectedLevel = $('#level_filter').val();
            let sortBy = $('#sort-select').val();
            let keyword = $('#ai-search-input').val().toLowerCase().trim();

            let filtered = mockCoursesData.filter(course => {
                if (selectedCategory && selectedCategory !== "" && course.category !== selectedCategory) return false;
                if (selectedLevel && selectedLevel !== "" && course.level !== selectedLevel) return false;
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
            $('#category_filter').val('').trigger('change');
            $('#level_filter').val('').trigger('change');
            $('#sort-select').val('popular').trigger('change');
            $('#ai-search-input').val('');
            
            this.fetchCourses();

            if (typeof LevelUpAlert !== 'undefined') {
                LevelUpAlert.toast('ล้างตัวกรองทั้งหมดเรียบร้อยแล้ว', 'info');
            }
        },

        aiSearch: function() {
            let val = $('#ai-search-input').val().trim();
            if (!val) {
                if (typeof LevelUpAlert !== 'undefined') {
                    LevelUpAlert.warning('กรุณากรอกข้อมูล', 'โปรดระบุเป้าหมายอาชีพหรือทักษะที่ต้องการค้นหา');
                } else {
                    alert('โปรดระบุเป้าหมายอาชีพหรือทักษะที่ต้องการค้นหา');
                }
                return;
            }

            if (typeof LevelUpAlert !== 'undefined') {
                LevelUpAlert.loading('AI กำลังวิเคราะห์เควส...');
            }

            setTimeout(() => {
                if (typeof LevelUpAlert !== 'undefined') {
                    LevelUpAlert.close();
                }
                this.fetchCourses();
                if (typeof LevelUpAlert !== 'undefined') {
                    LevelUpAlert.toast('วิเคราะห์และจัดสรรคอร์สเรียนเรียบร้อย', 'success');
                }
            }, 500);
        }
    };

    $(document).ready(function() {
        CourseExplorer.init();
    });
</script>

<?php include_once $ROOT_PATH . 'includes/footer.php'; ?>