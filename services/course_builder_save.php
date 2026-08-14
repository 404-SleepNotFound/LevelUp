<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized: User not logged in.']);
    exit;
}

$current_user_id = $_SESSION['user_id'];

// Include database connection
$db_paths = [
    __DIR__ . '/../config/database.php',
    __DIR__ . '/../config/db.php',
    __DIR__ . '/../db.php'
];

$pdo = null;
foreach ($db_paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        if (isset($conn) && $conn instanceof PDO) {
            $pdo = $conn;
        } elseif (isset($pdo_obj) && $pdo_obj instanceof PDO) { // Assuming $pdo_obj might be used
            $pdo = $pdo_obj;
        }
        break;
    }
}

if (!$pdo) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON payload or missing action.']);
    exit;
}

$action = $data['action'];

switch ($action) {
    case 'save_course_structure':
        try {
            $course_id = $data['course_id'] ?? null;
            $visibility = $data['visibility'] ?? 'Draft';
            $is_strict_flow = $data['is_strict_flow'] ?? true;
            $chapters_data = $data['chapters'] ?? [];

            if (!$course_id) {
                throw new Exception('Course ID is required.');
            }

            // 1. Verify ownership of the course
            $stmt = $pdo->prepare("SELECT user_id FROM course WHERE id = ?");
            $stmt->execute([$course_id]);
            $course_owner_id = $stmt->fetchColumn();

            if ($course_owner_id != $current_user_id) {
                throw new Exception('You do not have permission to modify this course.');
            }

            $pdo->beginTransaction();

            // 2. Update main course details
            $stmt = $pdo->prepare("UPDATE course SET visibility = ?, is_strict_flow = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$visibility, $is_strict_flow, $course_id]);

            // 3. Delete existing chapters and lessons for this course
            // This is a simplified approach. A more robust solution would compare and update.
            // For now, we delete all and re-insert.
            $stmt = $pdo->prepare("DELETE FROM lessons WHERE course_id = ?");
            $stmt->execute([$course_id]);
            $stmt = $pdo->prepare("DELETE FROM chapters WHERE course_id = ?");
            $stmt->execute([$course_id]);

            // 4. Insert new chapters and lessons
            foreach ($chapters_data as $chapter_order => $chapter_info) {
                $chapter_title = $chapter_info['title'] ?? 'Untitled Chapter';
                $chapter_db_order = $chapter_info['order'] ?? ($chapter_order + 1);

                $stmt = $pdo->prepare("INSERT INTO chapters (course_id, title, chapter_order) VALUES (?, ?, ?)");
                $stmt->execute([$course_id, $chapter_title, $chapter_db_order]);
                $new_chapter_id = $pdo->lastInsertId();

                foreach ($chapter_info['lessons'] as $lesson_order => $lesson_info) {
                    $lesson_title = $lesson_info['title'] ?? 'Untitled Lesson';
                    $lesson_type = $lesson_info['type'] ?? 'theory';
                    $lesson_db_order = $lesson_info['order'] ?? ($lesson_order + 1);
                    // Default duration_minutes to 15, status to 'draft'
                    // In a real scenario, lesson-editor.php would handle full lesson details.
                    // Here, we're just saving the basic structure.

                    $stmt = $pdo->prepare("INSERT INTO lessons (course_id, chapter_id, title, lesson_type, lesson_order, duration_minutes, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$course_id, $new_chapter_id, $lesson_title, $lesson_type, $lesson_db_order, 15, 'draft']);
                }
            }

            $pdo->commit();
            echo json_encode([
                'status' => 'success',
                'message' => 'Course structure saved successfully.'
            ]);

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Unknown action.']);
        break;
}
?>