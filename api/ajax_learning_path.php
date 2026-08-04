<?php
session_start();
header('Content-Type: application/json');

// หน่วงเวลาจำลอง AI Processing (1.5 วินาที)
usleep(1500000); 

$career = $_POST['career'] ?? 'backend';
$hours = (int)($_POST['hours'] ?? 2);

// จำลองการคำนวณเวลา (AI Simulator logic)
// สมมติ base time = 10 เดือน ถ้าเรียนวันละ 1 ชม.
$base_months = 10;
$estimated_months = max(1, round($base_months / $hours, 1));
$estimated_text = $estimated_months . ' เดือน';

// สร้าง Mockup HTML สำหรับ Roadmap ใหม่
$roadmap_html = '';
if ($career == 'data') {
    $roadmap_html = '
    <div class="text-center py-4 text-primary">
        <i class="bi bi-stars fs-1"></i>
        <h5 class="mt-3 fw-bold">AI กำหนด Roadmap ใหม่สำหรับ Data Scientist สำเร็จ!</h5>
        <p class="text-muted">เพิ่มวิชา Statistics, Data Visualization และ Machine Learning</p>
    </div>
    <!-- โครงสร้าง Node ต้นไม้ใหม่ (ย่อไว้เพื่อความกระชับ) -->
    <div class="roadmap-tree position-relative mt-4">
        <div class="roadmap-node completed d-flex">
            <div class="node-indicator"><div class="node-icon bg-success text-white"><i class="bi bi-check-lg"></i></div><div class="node-line bg-border"></div></div>
            <div class="node-content dashboard-card border-success border-opacity-50 p-3 rounded-4 w-100 ms-4 mb-4">
                <h5 class="fw-bold mb-1">Python Basics (โอนหน่วยกิต)</h5>
                <div class="fs-xs text-muted">AI พบว่าคุณเรียนผ่านแล้ว นำมาใช้อ้างอิงได้ทันที</div>
            </div>
        </div>
        <div class="roadmap-node active d-flex">
            <div class="node-indicator"><div class="node-icon bg-primary text-white"><i class="bi bi-play-fill"></i></div><div class="node-line bg-border"></div></div>
            <div class="node-content dashboard-card border-primary p-3 rounded-4 w-100 ms-4 mb-4">
                <h4 class="fw-bold mb-1">Statistics for Data Science</h4>
                <div class="fs-xs text-muted">เริ่มเรียนวิชาใหม่ เพื่อปูพื้นฐานสู่ ML</div>
            </div>
        </div>
    </div>';
} else {
    // โครงสร้าง Default จำลอง
    $roadmap_html = '<div class="text-center py-5 text-muted"><p>Roadmap อัปเดตข้อมูลเป็นสาย <b>'.$career.'</b> เรียบร้อย</p></div>';
}

echo json_encode([
    'status' => 'success',
    'estimated_time' => $estimated_text,
    'roadmap_html' => $roadmap_html
]);
exit;