<?php
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// [1. Security & Prerequisities]
// Check user authentication
$user_id = $_SESSION['user_id'] ?? $_SESSION['user']['id'] ?? 0;
if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Authentication required. Please log in.']);
    exit;
}

// Include database connection
require_once __DIR__ . '/../config/db.php';
$conn = $conn ?? $pdo;

// Action dispatcher
$action = $_GET['action'] ?? '';

if (!$conn instanceof PDO) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit;
}

try {
    switch ($action) {
        case 'get_chapters':
            handle_get_chapters($conn, $user_id);
            break;

        case 'get_lessons':
            handle_get_lessons($conn, $user_id);
            break;

        case 'get_lesson_details':
            handle_get_lesson_details($conn, $user_id);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action specified.']);
            break;
    }
} catch (Exception $e) {
    // Log the error message to a file or monitoring service in a real application
    // error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'A server error occurred. Please try again later.']);
}

exit;


// [2. Action Handlers]

/**
 * Fetches chapters for a given course ID, ensuring the user owns the course.
 */
function handle_get_chapters($conn, $user_id) {
    $course_id = filter_input(INPUT_GET, 'course_id', FILTER_VALIDATE_INT);
    if (!$course_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid or missing Course ID.']);
        return;
    }

    // Security Check: Verify course ownership
    $stmt = $conn->prepare("SELECT id FROM course WHERE id = :cid AND user_id = :uid LIMIT 1");
    $stmt->execute([':cid' => $course_id, ':uid' => $user_id]);
    if (!$stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Access denied. You do not own this course.']);
        return;
    }

    // Fetch chapters
    $stmt = $conn->prepare("SELECT id, title, chapter_order FROM chapters WHERE course_id = :cid ORDER BY chapter_order ASC");
    $stmt->execute([':cid' => $course_id]);
    $chapters = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'chapters' => $chapters]);
}

/**
 * Fetches lessons for a given chapter ID.
 * The course ownership is implicitly checked via the chapter's course_id.
 */
function handle_get_lessons($conn, $user_id) {
    $chapter_id = filter_input(INPUT_GET, 'chapter_id', FILTER_VALIDATE_INT);
    if (!$chapter_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid or missing Chapter ID.']);
        return;
    }

    // Security Check: Verify the user owns the course this chapter belongs to.
    $stmt = $conn->prepare("
        SELECT ch.id 
        FROM chapters ch
        JOIN course c ON ch.course_id = c.id
        WHERE ch.id = :chid AND c.user_id = :uid 
        LIMIT 1
    ");
    $stmt->execute([':chid' => $chapter_id, ':uid' => $user_id]);
    if (!$stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Access denied. You do not have permission for this chapter.']);
        return;
    }

    // Fetch lessons
    $stmt = $conn->prepare("
        SELECT id, title, lesson_type, lesson_order 
        FROM lessons 
        WHERE chapter_id = :chid 
        ORDER BY lesson_order ASC
    ");
    $stmt->execute([':chid' => $chapter_id]);
    $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'lessons' => $lessons]);
}

/**
 * Fetches the full details for a specific lesson ID.
 */
function handle_get_lesson_details($conn, $user_id) {
    $lesson_id = filter_input(INPUT_GET, 'lesson_id', FILTER_VALIDATE_INT);
    if (!$lesson_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid or missing Lesson ID.']);
        return;
    }

    // Security Check: Verify lesson ownership
    $stmt = $conn->prepare("
        SELECT l.* 
        FROM lessons l
        JOIN course c ON l.course_id = c.id
        WHERE l.id = :lid AND c.user_id = :uid
        LIMIT 1
    ");
    $stmt->execute([':lid' => $lesson_id, ':uid' => $user_id]);
    $lesson = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lesson) {
        echo json_encode(['status' => 'error', 'message' => 'Lesson not found or you do not have permission to access it.']);
        return;
    }
    
    // Also fetch related blocks and attachments for a complete view
    $blockStmt = $conn->prepare("SELECT * FROM lesson_blocks WHERE lesson_id = :lid ORDER BY block_order ASC");
    $blockStmt->execute([':lid' => $lesson_id]);
    $blocks = $blockStmt->fetchAll(PDO::FETCH_ASSOC);

    $attachStmt = $conn->prepare("SELECT * FROM lesson_attachments WHERE lesson_id = :lid ORDER BY id DESC");
    $attachStmt->execute([':lid' => $lesson_id]);
    $attachments = $attachStmt->fetchAll(PDO::FETCH_ASSOC);

    // Decode JSON fields before sending
    if (!empty($lesson['virtual_lab_config'])) {
        $lesson['virtual_lab_config'] = json_decode($lesson['virtual_lab_config'], true);
    }

    echo json_encode([
        'status' => 'success', 
        'lesson' => $lesson,
        'blocks' => $blocks,
        'attachments' => $attachments
    ]);
}
?>
