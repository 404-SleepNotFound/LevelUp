<?php
// ---------------------------------------------------------
// ตัวแปรรับข้อมูล $course_data (หรือรองรับ $course เป็น Fallback)
// ---------------------------------------------------------
$item = $course_data ?? $course ?? [];

if (empty($item)) {
    return; // ถ้าไม่มีข้อมูลส่งมา จะไม่แสดงผล
}

$id            = $item['id'] ?? '0';
$title         = $item['title'] ?? 'Untitled Course';
$instructor    = $item['instructor'] ?? 'Unknown Instructor';
$level         = $item['level'] ?? 'All Levels';
$category      = $item['category'] ?? 'General';
$lessons       = $item['lessons'] ?? 0;
$duration      = $item['duration'] ?? 'N/A';
$rating        = $item['rating'] ?? 0.0;
$reviews       = $item['reviews'] ?? $item['reviews_count'] ?? 0;
$price         = $item['price'] ?? 'Free';
$image         = $item['image'] ?? 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=500&q=80';
$badges        = $item['badges'] ?? [];
$is_bookmarked = $item['is_bookmarked'] ?? false;
?>

<div class="col-md-6 col-lg-4">
    <div class="card course-card-component h-100 border-0 rounded-4 overflow-hidden position-relative">
        <!-- Thumbnail & Badges -->
        <div class="position-relative overflow-hidden card-thumb-wrapper">
            <a href="course-detail.php?id=<?= htmlspecialchars($id) ?>" class="d-block">
                <img src="<?= htmlspecialchars($image) ?>" class="card-img-top object-fit-cover w-100 course-thumb" alt="<?= htmlspecialchars($title) ?>">
            </a>
            
            <!-- Category Badge -->
            <div class="position-absolute top-0 start-0 m-3 d-flex flex-wrap gap-1">
                <span class="badge bg-dark bg-opacity-75 text-info border border-info border-opacity-25 rounded-pill px-2.5 py-1 small fw-medium backdrop-blur">
                    <?= htmlspecialchars($category) ?>
                </span>
                <?php foreach($badges as $badge): ?>
                    <span class="badge bg-gradient-primary rounded-pill px-2.5 py-1 small fw-medium">
                        <?= htmlspecialchars($badge) ?>
                    </span>
                <?php endforeach; ?>
            </div>

            <!-- Bookmark Button -->
            <button class="btn btn-sm btn-bookmark position-absolute top-0 end-0 m-3 rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                    data-id="<?= htmlspecialchars($id) ?>"
                    title="บันทึกคอร์สนี้">
                <i class="bi <?= $is_bookmarked ? 'bi-bookmark-fill text-warning' : 'bi-bookmark text-white' ?>"></i>
            </button>
        </div>

        <!-- Card Body -->
        <div class="card-body d-flex flex-column p-3.5 p-md-4">
            <!-- Meta Info (Level & Duration) -->
            <div class="d-flex align-items-center justify-content-between text-white-50 small mb-2" style="font-size: 0.78rem;">
                <span><i class="bi bi-bar-chart me-1 text-info"></i><?= htmlspecialchars($level) ?></span>
                <span><i class="bi bi-play-circle me-1 text-info"></i><?= $lessons ? $lessons . ' บท • ' : '' ?><?= htmlspecialchars($duration) ?></span>
            </div>

            <!-- Title -->
            <h6 class="card-title fw-bold mb-2">
                <a href="course-detail.php?id=<?= htmlspecialchars($id) ?>" class="text-white text-decoration-none course-title-link line-clamp-2">
                    <?= htmlspecialchars($title) ?>
                </a>
            </h6>

            <!-- Instructor -->
            <p class="text-white-50 small mb-3" style="font-size: 0.8rem;">
                <i class="bi bi-person me-1 text-secondary"></i><?= htmlspecialchars($instructor) ?>
            </p>

            <!-- Rating & Price -->
            <div class="mt-auto pt-3 border-top border-white border-opacity-10 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-1 small">
                    <i class="bi bi-star-fill text-warning"></i>
                    <span class="fw-bold text-white" style="font-size: 0.85rem;"><?= number_format((float)$rating, 1) ?></span>
                    <span class="text-white-50" style="font-size: 0.75rem;">(<?= number_format($reviews) ?>)</span>
                </div>
                <div class="fw-bold text-cyan" style="font-size: 1rem; color: #06b6d4;">
                    <?= is_numeric($price) ? number_format($price) . ' ฿' : htmlspecialchars($price) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* CSS สำหรับ course-card.php */
.course-card-component {
    background: rgba(20, 27, 45, 0.75) !important;
    border: 1px solid rgba(99, 102, 241, 0.2) !important;
    backdrop-filter: blur(10px);
    transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
}

.course-card-component:hover {
    transform: translateY(-5px);
    border-color: rgba(6, 182, 212, 0.5) !important;
    box-shadow: 0 10px 25px rgba(6, 182, 212, 0.15);
}

.card-thumb-wrapper {
    height: 180px;
}

.course-thumb {
    height: 100%;
    transition: transform 0.4s ease;
}

.course-card-component:hover .course-thumb {
    transform: scale(1.05);
}

.backdrop-blur {
    backdrop-filter: blur(8px);
}

.btn-bookmark {
    width: 34px;
    height: 34px;
    background: rgba(9, 13, 26, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.15);
    transition: all 0.2s ease;
}

.btn-bookmark:hover {
    background: rgba(6, 182, 212, 0.3);
    border-color: #06b6d4;
}

.course-title-link {
    line-height: 1.4;
    transition: color 0.2s ease;
}

.course-title-link:hover {
    color: #06b6d4 !important;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>