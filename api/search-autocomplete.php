<?php
header('Content-Type: application/json');

// Mock data: ปกติจะรับค่า $_GET['q'] แล้ว Query หาจากฐานข้อมูล (Full-text search หรือ Vector Search สำหรับ AI)
$query = isset($_GET['q']) ? strtolower($_GET['q']) : '';
$results = [];

if($query !== '') {
    // สมมติ AI วิเคราะห์ความหมาย (Semantic Search)
    if (strpos($query, 'web') !== false || strpos($query, 'เว็บไซต์') !== false) {
        $results[] = ['title' => 'Complete Web Development Bootcamp', 'desc' => 'เรียนสร้างเว็บตั้งแต่ Zero to Hero', 'url' => 'student/course-detail.php?id=2', 'type' => 'normal'];
        $results[] = ['title' => 'Frontend Developer Roadmap', 'desc' => 'เส้นทางสู่การเป็น Frontend', 'url' => 'learning-path.php?path=frontend', 'type' => 'ai'];
    } elseif (strpos($query, 'python') !== false) {
        $results[] = ['title' => 'Python for AI & Machine Learning', 'desc' => 'พื้นฐาน Python สำหรับ AI', 'url' => 'student/course-detail.php?id=1', 'type' => 'normal'];
    } else {
        // ผลลัพธ์ทั่วไป
        $results[] = ['title' => 'ผลการค้นหาสำหรับ: ' . htmlspecialchars($_GET['q']), 'desc' => 'ดูคอร์สเรียนทั้งหมดที่เกี่ยวข้อง', 'url' => 'courses.php?q='.urlencode($_GET['q']), 'type' => 'normal'];
    }
}

echo json_encode($results);
exit;