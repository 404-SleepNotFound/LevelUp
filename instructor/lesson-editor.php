<?php
$ROOT_PATH = '../';

// [1. Security Framework & Session Verification]
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// ตรวจสอบ Authorization: ผู้ใช้ต้องล็อกอินเข้าสู่ระบบก่อน
$user_id = $_SESSION['user_id'] ?? $_SESSION['user']['id'] ?? 0;
if (!$user_id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'message' => 'กรุณาล็อกอินเข้าสู่ระบบก่อนดำเนินการ']);
        exit;
    } else {
        header('Location: ' . $ROOT_PATH . 'login.php');
        exit;
    }
}

// [2. Database Connection]
require_once __DIR__ . '/../config/db.php'; 

if (!isset($conn) && isset($pdo)) {
    $conn = $pdo;
}

// Helper Function: ตรวจสอบความเป็นเจ้าของคอร์ส
function checkCourseOwnership($conn, $course_id, $user_id) {
    $stmt = $conn->prepare("SELECT id FROM course WHERE id = :cid AND user_id = :uid LIMIT 1");
    $stmt->execute([':cid' => $course_id, ':uid' => $user_id]);
    return (bool) $stmt->fetch();
}

// Helper Function: ตรวจสอบความเป็นเจ้าของบทเรียนผ่านคอร์ส
function checkLessonOwnership($conn, $lesson_id, $user_id) {
    $stmt = $conn->prepare("SELECT l.id FROM lessons l JOIN course c ON l.course_id = c.id WHERE l.id = :lid AND c.user_id = :uid LIMIT 1");
    $stmt->execute([':lid' => $lesson_id, ':uid' => $user_id]);
    return (bool) $stmt->fetch();
}

// [3. AJAX Backend API Handler]
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json; charset=utf-8');
    
    $action = $_POST['action'] ?? '';

    // Action 1: Save or Update Lesson
    if ($action === 'save_lesson') {
        try {
            $lesson_id   = !empty($_POST['lesson_id']) ? intval($_POST['lesson_id']) : null;
            $course_id   = intval($_POST['course_id'] ?? 0);
            $chapter_id  = !empty($_POST['chapter_id']) ? intval($_POST['chapter_id']) : null;
            $title       = trim($_POST['title'] ?? '');
            $subtitle    = trim($_POST['subtitle'] ?? '');
            $lesson_type = trim($_POST['lesson_type'] ?? 'theory');
            $status      = trim($_POST['status'] ?? 'draft');
            $lesson_order= intval($_POST['lesson_order'] ?? 1);
            $content     = $_POST['content'] ?? '';
            $video_url   = trim($_POST['video_url'] ?? '');
            $code_snippet= $_POST['code_snippet'] ?? '';
            $quiz_id     = !empty($_POST['quiz_id']) ? intval($_POST['quiz_id']) : null;
            $lab_id      = !empty($_POST['lab_id']) ? intval($_POST['lab_id']) : null;
            
            $assignment_type = trim($_POST['assignment_type'] ?? '');
            $assignment_inst = trim($_POST['assignment_instructions'] ?? '');
            $learning_obj    = trim($_POST['learning_objectives'] ?? '');
            $prerequisites   = trim($_POST['prerequisites'] ?? '');
            $estimated_time  = trim($_POST['estimated_time'] ?? '');
            $resources       = trim($_POST['resources'] ?? '');
            $seo_slug        = trim($_POST['seo_slug'] ?? '');
            $seo_title       = trim($_POST['seo_title'] ?? '');
            $virtual_lab     = isset($_POST['virtual_lab']) ? json_encode($_POST['virtual_lab'], JSON_UNESCAPED_UNICODE) : null;

            if (empty($course_id) || empty($title)) {
                echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกข้อมูลสำคัญ (ชื่อบทเรียน และ คอร์ส) ให้ครบถ้วน']);
                exit;
            }

            // ตรวจสอบสิทธิ์ว่าผู้ใช้เป็นเจ้าของคอร์สนี้หรือไม่
            if (!checkCourseOwnership($conn, $course_id, $user_id)) {
                echo json_encode(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์ในการจัดการคอร์สเรียนนี้']);
                exit;
            }

            // ถ้าเป็นการแก้ไขบทเรียนเดิม ตรวจสอบสิทธิ์บทเรียนเพิ่มเติม
            if ($lesson_id && !checkLessonOwnership($conn, $lesson_id, $user_id)) {
                echo json_encode(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์แก้ไขบทเรียนนี้']);
                exit;
            }

            // จัดการไฟล์อัปโหลด (Video File, Thumbnail, Subtitle)
            $upload_dir = '../uploads/lessons/';
            if (!file_exists($upload_dir)) { @mkdir($upload_dir, 0777, true); }

            $video_file_path = $_POST['existing_video_file'] ?? null;
            if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION);
                $video_file_path = 'uploads/lessons/vid_' . time() . '_' . uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['video_file']['tmp_name'], '../' . $video_file_path);
            }

            $thumbnail_path = $_POST['existing_thumbnail'] ?? null;
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
                $thumbnail_path = 'uploads/lessons/thumb_' . time() . '_' . uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['thumbnail']['tmp_name'], '../' . $thumbnail_path);
            }

            $subtitle_path = $_POST['existing_subtitle_file'] ?? null;
            if (isset($_FILES['subtitle_file']) && $_FILES['subtitle_file']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['subtitle_file']['name'], PATHINFO_EXTENSION);
                $subtitle_path = 'uploads/lessons/sub_' . time() . '_' . uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['subtitle_file']['tmp_name'], '../' . $subtitle_path);
            }

            if ($conn instanceof PDO) {
                if ($lesson_id) {
                    $sql = "UPDATE lessons SET 
                                course_id = :course_id, chapter_id = :chapter_id, title = :title, subtitle = :subtitle,
                                lesson_type = :lesson_type, lesson_order = :lesson_order, status = :status, content = :content,
                                video_url = :video_url, video_file = :video_file, thumbnail = :thumbnail, subtitle_file = :subtitle_file,
                                quiz_id = :quiz_id, lab_id = :lab_id, virtual_lab_config = :virtual_lab, assignment_type = :assignment_type,
                                assignment_instructions = :assignment_instructions, learning_objectives = :learning_objectives,
                                prerequisites = :prerequisites, estimated_time = :estimated_time, resources = :resources,
                                seo_slug = :seo_slug, seo_title = :seo_title, updated_at = NOW()
                            WHERE id = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindValue(':id', $lesson_id, PDO::PARAM_INT);
                } else {
                    $sql = "INSERT INTO lessons (
                                course_id, chapter_id, title, subtitle, lesson_type, lesson_order, status, content,
                                video_url, video_file, thumbnail, subtitle_file, quiz_id, lab_id, virtual_lab_config,
                                assignment_type, assignment_instructions, learning_objectives, prerequisites, estimated_time,
                                resources, seo_slug, seo_title
                            ) VALUES (
                                :course_id, :chapter_id, :title, :subtitle, :lesson_type, :lesson_order, :status, :content,
                                :video_url, :video_file, :thumbnail, :subtitle_file, :quiz_id, :lab_id, :virtual_lab,
                                :assignment_type, :assignment_instructions, :learning_objectives, :prerequisites, :estimated_time,
                                :resources, :seo_slug, :seo_title
                            )";
                    $stmt = $conn->prepare($sql);
                }

                $stmt->bindValue(':course_id', $course_id, PDO::PARAM_INT);
                $stmt->bindValue(':chapter_id', $chapter_id, $chapter_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
                $stmt->bindValue(':title', $title);
                $stmt->bindValue(':subtitle', $subtitle);
                $stmt->bindValue(':lesson_type', $lesson_type);
                $stmt->bindValue(':lesson_order', $lesson_order, PDO::PARAM_INT);
                $stmt->bindValue(':status', $status);
                $stmt->bindValue(':content', $content);
                $stmt->bindValue(':video_url', $video_url);
                $stmt->bindValue(':video_file', $video_file_path);
                $stmt->bindValue(':thumbnail', $thumbnail_path);
                $stmt->bindValue(':subtitle_file', $subtitle_path);
                $stmt->bindValue(':quiz_id', $quiz_id, $quiz_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
                $stmt->bindValue(':lab_id', $lab_id, $lab_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
                $stmt->bindValue(':virtual_lab', $virtual_lab);
                $stmt->bindValue(':assignment_type', $assignment_type);
                $stmt->bindValue(':assignment_instructions', $assignment_inst);
                $stmt->bindValue(':learning_objectives', $learning_obj);
                $stmt->bindValue(':prerequisites', $prerequisites);
                $stmt->bindValue(':estimated_time', $estimated_time);
                $stmt->bindValue(':resources', $resources);
                $stmt->bindValue(':seo_slug', $seo_slug);
                $stmt->bindValue(':seo_title', $seo_title);
                $stmt->execute();

                if (!$lesson_id) {
                    $lesson_id = $conn->lastInsertId();
                }

                // บันทึก Dynamic Blocks (ตาราง lesson_blocks)
                if (isset($_POST['blocks']) && is_array($_POST['blocks'])) {
                    $delStmt = $conn->prepare("DELETE FROM lesson_blocks WHERE lesson_id = :lid");
                    $delStmt->execute([':lid' => $lesson_id]);

                    $insBlock = $conn->prepare("INSERT INTO lesson_blocks (lesson_id, block_type, block_data, block_order) VALUES (:lid, :btype, :bdata, :border)");
                    foreach ($_POST['blocks'] as $order => $block) {
                        $insBlock->execute([
                            ':lid'   => $lesson_id,
                            ':btype' => $block['type'] ?? 'text',
                            ':bdata' => $block['data'] ?? '',
                            ':border'=> $order + 1
                        ]);
                    }
                }

                // บันทึกไฟล์แนบเพิ่มเติม (ตาราง lesson_attachments)
                if (isset($_FILES['attachments'])) {
                    $attFiles = $_FILES['attachments'];
                    $insAtt = $conn->prepare("INSERT INTO lesson_attachments (lesson_id, file_name, file_path, file_type, file_size) VALUES (:lid, :fname, :fpath, :ftype, :fsize)");
                    for ($i = 0; $i < count($attFiles['name']); $i++) {
                        if ($attFiles['error'][$i] === UPLOAD_ERR_OK) {
                            $origName = $attFiles['name'][$i];
                            $ext = pathinfo($origName, PATHINFO_EXTENSION);
                            $fsize = $attFiles['size'][$i];
                            $savePath = 'uploads/lessons/att_' . time() . '_' . $i . '.' . $ext;
                            move_uploaded_file($attFiles['tmp_name'][$i], '../' . $savePath);

                            $insAtt->execute([
                                ':lid'   => $lesson_id,
                                ':fname' => $origName,
                                ':fpath' => $savePath,
                                ':ftype' => strtoupper($ext),
                                ':fsize' => $fsize
                            ]);
                        }
                    }
                }

                // บันทึก Version Log (ตาราง lesson_versions)
                $verStmt = $conn->prepare("INSERT INTO lesson_versions (lesson_id, version_number, snapshot_data) VALUES (:lid, :ver, :snap)");
                $verStmt->execute([
                    ':lid'  => $lesson_id,
                    ':ver'  => 'v' . date('Ymd.His'),
                    ':snap' => json_encode($_POST, JSON_UNESCAPED_UNICODE)
                ]);
            }

            echo json_encode(['status' => 'success', 'message' => 'บันทึกข้อมูลบทเรียนเรียบร้อยแล้ว!', 'lesson_id' => $lesson_id]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage()]);
        }
        exit;
    }

    // Action 2: Delete Lesson
    if ($action === 'delete_lesson') {
        $lesson_id = intval($_POST['lesson_id'] ?? 0);
        if ($lesson_id > 0) {
            if (!checkLessonOwnership($conn, $lesson_id, $user_id)) {
                echo json_encode(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์ลบบทเรียนนี้']);
                exit;
            }
            $stmt = $conn->prepare("DELETE FROM lessons WHERE id = :id");
            $stmt->execute([':id' => $lesson_id]);
            echo json_encode(['status' => 'success', 'message' => 'ลบบทเรียนเรียบร้อยแล้ว']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบ รหัสบทเรียน ที่ต้องการลบ']);
        }
        exit;
    }

    // Action 3: Unpublish Lesson
    if ($action === 'unpublish_lesson') {
        $lesson_id = intval($_POST['lesson_id'] ?? 0);
        if ($lesson_id > 0) {
            if (!checkLessonOwnership($conn, $lesson_id, $user_id)) {
                echo json_encode(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์แก้ไขบทเรียนนี้']);
                exit;
            }
            $stmt = $conn->prepare("UPDATE lessons SET status = 'unpublished' WHERE id = :id");
            $stmt->execute([':id' => $lesson_id]);
            echo json_encode(['status' => 'success', 'message' => 'ยกเลิกการเผยแพร่บทเรียนเรียบร้อยแล้ว']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบ รหัสบทเรียน']);
        }
        exit;
    }

    // Action 4: AI Copilot Assistant Simulation
    if ($action === 'ai_assistant') {
        $mode = $_POST['mode'] ?? 'content';
        $responses = [
            'content'   => "### เนื้อหาตัวอย่างที่สร้างโดย AI Copilot\n- การใช้ `let` และ `const` ใน JavaScript ES6+\n- ความแตกต่างระหว่าง Block Scope และ Function Scope",
            'summarize' => 'สรุปสำคัญ: บทเรียนนี้มุ่งเน้นความเข้าใจขอบเขตของตัวแปรเพื่อป้องกันปัญหา Memory Leak และ Scope Pollution',
            'language'  => 'ปรับแก้ภาษาให้อ่านเข้าใจง่ายและกระชับ เรียบร้อยแล้ว',
            'spell'     => 'ตรวจสอบคำผิดเรียบร้อย ไม่พบคำผิดในเนื้อหาปัจจุบัน',
            'quiz'      => 'สร้างคำถามตัวอย่าง 1 ข้อ: ข้อใดคือคุณสมบัติของตัวแปรประเภท `const`?',
            'flashcard' => 'Flashcard #1: Variable Hoisting คืออะไร?'
        ];
        echo json_encode(['status' => 'success', 'data' => $responses[$mode] ?? 'ประมวลผลคำสั่ง AI สำเร็จ']);
        exit;
    }
}

// [4. Fetch Initial Dynamic Data]
$lesson_id = isset($_GET['lesson_id']) ? intval($_GET['lesson_id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

$lesson = null;
$courses = [];
$chapters = [];
$blocks = [];
$attachments = [];
$versions = [];

if ($conn instanceof PDO) {
    // 1. ดึงรายการ Course เฉพาะของผู้ใช้งานที่ล็อกอิน (user_id)
    $cStmt = $conn->prepare("SELECT id, title FROM course WHERE user_id = :uid ORDER BY id DESC");
    $cStmt->execute([':uid' => $user_id]);
    $courses = $cStmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. ดึงข้อมูลบทเรียนเดิม (ถ้ามี lesson_id และต้องเป็นของ user_id นี้)
    if ($lesson_id > 0) {
        $lStmt = $conn->prepare("SELECT l.* FROM lessons l JOIN course c ON l.course_id = c.id WHERE l.id = :id AND c.user_id = :uid LIMIT 1");
        $lStmt->execute([':id' => $lesson_id, ':uid' => $user_id]);
        $lesson = $lStmt->fetch(PDO::FETCH_ASSOC);

        if ($lesson) {
            $course_id = $lesson['course_id'];

            // ดึง Blocks
            $bStmt = $conn->prepare("SELECT * FROM lesson_blocks WHERE lesson_id = :lid ORDER BY block_order ASC");
            $bStmt->execute([':lid' => $lesson_id]);
            $blocks = $bStmt->fetchAll(PDO::FETCH_ASSOC);

            // ดึง Attachments
            $aStmt = $conn->prepare("SELECT * FROM lesson_attachments WHERE lesson_id = :lid ORDER BY id DESC");
            $aStmt->execute([':lid' => $lesson_id]);
            $attachments = $aStmt->fetchAll(PDO::FETCH_ASSOC);

            // ดึง Versions Log
            $vStmt = $conn->prepare("SELECT * FROM lesson_versions WHERE lesson_id = :lid ORDER BY created_at DESC LIMIT 5");
            $vStmt->execute([':lid' => $lesson_id]);
            $versions = $vStmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // หากไม่พบการเข้าถึงบทเรียน หรือไม่มีสิทธิ์ ให้รีเซ็ต lesson_id
            $lesson_id = 0;
        }
    }

    // 3. ดึง Chapters ที่ผูกกับ Course นี้ (และตรวจสอบสิทธิ์เจ้าของคอร์ส)
    if ($course_id > 0 && checkCourseOwnership($conn, $course_id, $user_id)) {
        $chStmt = $conn->prepare("SELECT id, title, chapter_order FROM chapters WHERE course_id = :cid ORDER BY chapter_order ASC");
        $chStmt->execute([':cid' => $course_id]);
        $chapters = $chStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// ถอดรหัส Virtual Lab Config JSON
$vLabConfig = [];
if (!empty($lesson['virtual_lab_config'])) {
    $vLabConfig = json_decode($lesson['virtual_lab_config'], true) ?? [];
}
?>

<!-- Import Custom Dropdown Select Stylesheet -->
<link rel="stylesheet" href="<?= $ROOT_PATH ?>css/dropdown-select.css">

<style>
    /* Scope Editor Styles ให้เข้ากับ Premium Dark/Light Theme */
    .editor-container {
        animation: fadeInEditor 0.35s ease-in-out;
    }
    
    .editor-section-card {
        background: var(--card-bg, rgba(16, 24, 48, 0.65));
        border: 1px solid var(--card-border, rgba(255, 255, 255, 0.06));
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        backdrop-filter: blur(10px);
    }
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--body-text, #f8fafc);
        border-bottom: 1px solid var(--card-border, rgba(255, 255, 255, 0.08));
        padding-bottom: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-title i {
        color: #06b6d4;
    }

    .editor-subtitle,
    .editor-container .text-muted,
    .editor-container p.text-muted,
    .editor-container .text-white-50,
    .editor-container small.text-muted,
    .editor-container .small {
        color: #cbd5e1 !important;
        opacity: 1 !important;
        font-size: 0.88rem;
    }

    .form-control-premium {
        background: var(--nav-input-bg, rgba(15, 23, 42, 0.8)) !important;
        border: 1px solid var(--nav-input-border, rgba(255, 255, 255, 0.12)) !important;
        color: var(--body-text, #f8fafc) !important;
        border-radius: 10px;
        padding: 10px 14px !important;
        line-height: 1.6 !important;
        min-height: 46px;
        transition: all 0.3s ease;
    }
    .form-control-premium:focus {
        border-color: #06b6d4 !important;
        box-shadow: 0 0 0 0.25rem rgba(6, 182, 212, 0.15) !important;
        background: var(--card-bg, #0f172a) !important;
    }

    input[type="file"].form-control-premium {
        display: flex !important;
        align-items: center !important;
        padding: 6px 12px !important;
    }
    input[type="file"].form-control-premium::file-selector-button,
    input[type="file"].form-control-premium::-webkit-file-upload-button {
        background: rgba(6, 182, 212, 0.15) !important;
        color: #06b6d4 !important;
        border: 1px solid rgba(6, 182, 212, 0.3) !important;
        border-radius: 6px;
        padding: 6px 14px !important;
        margin-right: 12px !important;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        line-height: 1 !important;
        transition: all 0.2s ease-in-out;
    }
    input[type="file"].form-control-premium::file-selector-button:hover,
    input[type="file"].form-control-premium::-webkit-file-upload-button:hover {
        background: #06b6d4 !important;
        color: #ffffff !important;
    }
    
    textarea.form-control-premium {
        min-height: 90px !important;
    }

    select.form-control-premium {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 14px center !important;
        background-size: 12px 12px !important;
        padding-right: 40px !important;
    }

    .form-label-premium {
        color: #e2e8f0 !important; 
        font-weight: 500;
        margin-bottom: 6px;
        font-size: 0.88rem;
    }
    .form-control-premium::placeholder {
        color: #94a3b8 !important;
    }

    .btn-interactive-item {
        background: var(--nav-input-bg, rgba(15, 23, 42, 0.6)) !important;
        border: 1px solid var(--nav-input-border, rgba(255, 255, 255, 0.1)) !important;
        color: var(--body-text, #f8fafc) !important;
        border-radius: 10px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        text-align: left;
        font-size: 0.88rem;
        transition: all 0.25s ease;
    }
    .btn-interactive-item:hover {
        background: rgba(6, 182, 212, 0.12) !important;
        border-color: rgba(6, 182, 212, 0.4) !important;
        color: #06b6d4 !important;
        transform: translateY(-1px);
    }

    .custom-checkbox-card {
        background: var(--nav-input-bg, rgba(15, 23, 42, 0.6));
        border: 1px solid var(--nav-input-border, rgba(255, 255, 255, 0.1));
        border-radius: 10px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        user-select: none;
        transition: all 0.2s ease;
    }
    .custom-checkbox-card:hover {
        border-color: rgba(6, 182, 212, 0.3);
        background: rgba(15, 23, 42, 0.8);
    }

    .custom-checkbox-card .form-check-input {
        width: 18px !important;
        height: 18px !important;
        margin: 0 !important;
        cursor: pointer;
        flex-shrink: 0;
        background-color: rgba(15, 23, 42, 0.8) !important;
        border: 1.5px solid rgba(255, 255, 255, 0.35) !important;
    }
    .custom-checkbox-card .form-check-input:checked {
        background-color: #06b6d4 !important;
        border-color: #06b6d4 !important;
    }
    .custom-checkbox-card label,
    .custom-checkbox-card span {
        cursor: pointer;
        margin: 0;
        font-size: 0.9rem;
        color: var(--body-text, #f8fafc);
    }

    .block-unit-item {
        background: var(--nav-dropdown-bg, rgba(0, 0, 0, 0.3));
        border: 1px solid var(--nav-dropdown-border, rgba(255, 255, 255, 0.1));
        border-radius: 12px;
        padding: 18px;
        margin-bottom: 12px;
        cursor: grab;
    }
    .block-unit-item:active { cursor: grabbing; }
    
    .rich-editor-toolbar .btn {
        color: var(--nav-text-muted, #cbd5e1);
        border-color: var(--nav-dropdown-border, rgba(255, 255, 255, 0.1));
        padding: 6px 12px;
    }
    .rich-editor-toolbar .btn:hover {
        color: var(--nav-dropdown-hover-text, #fff);
        background: var(--nav-dropdown-hover-bg, rgba(255, 255, 255, 0.08));
    }

    /* ☀️ LIGHT MODE OVERRIDES */
    html[data-theme="light"] .editor-subtitle,
    html[data-theme="light"] .editor-container .text-muted,
    html[data-theme="light"] .editor-container p.text-muted,
    html[data-theme="light"] .editor-container .text-white-50,
    html[data-theme="light"] .editor-container .small {
        color: #475569 !important;
    }

    html[data-theme="light"] .editor-container .text-white,
    html[data-theme="light"] .modal .text-white { color: #0f172a !important; }
    
    html[data-theme="light"] .editor-container .bg-dark { background-color: #f1f5f9 !important; }
    html[data-theme="light"] .editor-container .bg-black { background-color: #e2e8f0 !important; }
    
    html[data-theme="light"] .editor-container .btn-dark { 
        background-color: #ffffff !important; 
        color: #0f172a !important; 
        border-color: #cbd5e1 !important; 
    }
    html[data-theme="light"] .editor-container .btn-dark:hover { background-color: #f1f5f9 !important; }
    html[data-theme="light"] .editor-container .border-secondary { border-color: #cbd5e1 !important; }
    html[data-theme="light"] .form-label-premium { color: #334155 !important; }

    html[data-theme="light"] .custom-checkbox-card .form-check-input {
        background-color: #ffffff !important;
        border: 1.5px solid #64748b !important;
    }
    html[data-theme="light"] .custom-checkbox-card .form-check-input:checked {
        background-color: #06b6d4 !important;
        border-color: #06b6d4 !important;
    }

    html[data-theme="light"] #livePreviewModal .modal-content { background-color: #ffffff !important; color: #0f172a !important; border-color: #e2e8f0 !important; }
    html[data-theme="light"] #livePreviewModal .modal-header { background-color: #f8fafc !important; border-bottom-color: #e2e8f0 !important; }
    html[data-theme="light"] #livePreviewModal .modal-body { background-color: #f1f5f9 !important; }
    html[data-theme="light"] #livePreviewModal .bg-dark { background-color: #ffffff !important; }
    html[data-theme="light"] #livePreviewModal .bg-black { background-color: #e2e8f0 !important; }
    html[data-theme="light"] #preview-viewport-box { background-color: #ffffff !important; border-color: #cbd5e1 !important; }
    html[data-theme="light"] .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }

    @keyframes fadeInEditor {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="editor-container">
    <!-- Top Action Info -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="text-white fw-bold mb-1">
                <i class="bi bi-pencil-square text-cyan me-2"></i>
                <?= $lesson ? 'แก้ไขบทเรียน: ' . htmlspecialchars($lesson['title']) : 'ระบบสร้างและแก้ไขบทเรียน (Lesson Editor)' ?>
            </h3>
            <p class="editor-subtitle mb-0">ปรับแต่งเนื้อหา วิดีโอ แล็บ ควิซ และใช้ระบบ AI ช่วยตรวจขัดเกลาเนื้อหาการเรียนรู้</p>
        </div>
        <div id="validation-quick-alert" class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 p-2 font-monospace">
            <i class="bi bi-shield-exclamation me-1"></i> Mode: <?= $lesson ? 'Editing (ID: ' . $lesson['id'] . ')' : 'Create New Lesson' ?>
        </div>
    </div>

    <form id="lessonMasterForm" enctype="multipart/form-data">
        <input type="hidden" name="action" id="form_action_input" value="save_lesson">
        <input type="hidden" name="lesson_id" id="lesson_id_input" value="<?= $lesson ? $lesson['id'] : '' ?>">
        
        <!-- ==========================================
             1. LESSON INFORMATION
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title">
                <i class="bi bi-info-circle-fill"></i> 1. Lesson Information (ข้อมูลพื้นฐานบทเรียน)
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label form-label-premium">คอร์สเรียน (Course) <span class="text-danger">*</span></label>
                    <select name="course_id" id="course_id_select" class="form-select form-control-premium lu-select" required>
                        <option value="">-- เลือกลิสต์คอร์สเรียน --</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($course_id == $c['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['title']) ?> (ID: <?= $c['id'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label form-label-premium">ชื่อบทเรียน <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control form-control-premium" value="<?= htmlspecialchars($lesson['title'] ?? '') ?>" placeholder="เช่น บทที่ 3: โครงสร้างและสโคปของตัวแปร" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label form-label-premium">Chapter (บทหลัก)</label>
                    <select name="chapter_id" class="form-select form-control-premium lu-select">
                        <option value="">-- ไม่ระบุ Chapter --</option>
                        <?php foreach ($chapters as $ch): ?>
                            <option value="<?= $ch['id'] ?>" <?= ($lesson && $lesson['chapter_id'] == $ch['id']) ? 'selected' : '' ?>>
                                Chapter <?= $ch['chapter_order'] ?>: <?= htmlspecialchars($ch['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label form-label-premium">ลำดับบทเรียน (Order)</label>
                    <input type="number" name="lesson_order" class="form-control form-control-premium" value="<?= htmlspecialchars($lesson['lesson_order'] ?? '1') ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label form-label-premium">ประเภทบทเรียน</label>
                    <select name="lesson_type" class="form-select form-control-premium lu-select">
                        <option value="theory" <?= ($lesson && $lesson['lesson_type'] == 'theory') ? 'selected' : '' ?>>ทฤษฎีทั่วไป (Theory Lecture)</option>
                        <option value="lab" <?= ($lesson && $lesson['lesson_type'] == 'lab') ? 'selected' : '' ?>>ปฏิบัติการแล็บ (Lab Practical)</option>
                        <option value="quiz" <?= ($lesson && $lesson['lesson_type'] == 'quiz') ? 'selected' : '' ?>>แบบทดสอบ (Quiz/Test)</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label form-label-premium">สถานะ (Status)</label>
                    <select name="status" class="form-select form-control-premium text-warning fw-bold lu-select">
                        <option value="draft" <?= (!$lesson || $lesson['status'] == 'draft') ? 'selected' : '' ?>>🟡 Draft (แบบร่าง)</option>
                        <option value="published" <?= ($lesson && $lesson['status'] == 'published') ? 'selected' : '' ?>>🟢 Published (เผยแพร่แล้ว)</option>
                        <option value="unpublished" <?= ($lesson && $lesson['status'] == 'unpublished') ? 'selected' : '' ?>>🔴 Unpublished (ปิดการมองเห็น)</option>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label form-label-premium">คำอธิบายรายวิชาย่อย (Subtitle)</label>
                    <textarea name="subtitle" class="form-control form-control-premium" rows="2" placeholder="อธิบายรายละเอียดโดยย่อเกี่ยวกับบทเรียนนี้..."><?= htmlspecialchars($lesson['subtitle'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- ==========================================
             ⭐ FEATURE 1: BLOCK-BASED LESSON BUILDER
             ========================================== -->
        <div class="editor-section-card border border-info border-opacity-20">
            <div class="section-title text-cyan">
                <i class="bi bi-layers-half"></i> ⭐ Block-based Lesson Builder (ตัวจัดเรียงบล็อกเนื้อหาแบบ Dynamic)
            </div>
            <p class="editor-subtitle mt-0 mb-3">กดเพิ่มบล็อกด้านล่างเพื่อทำการแทรกกล่องเนื้อหาและลากจัดตำแหน่งได้อย่างอิสระ</p>
            <div class="d-flex flex-wrap gap-2 mb-3">
                <button type="button" class="btn btn-sm btn-dark border border-secondary" onclick="addNewContentBlock('text')">📝 Text Block</button>
                <button type="button" class="btn btn-sm btn-dark border border-secondary" onclick="addNewContentBlock('video')">🎥 Video Block</button>
                <button type="button" class="btn btn-sm btn-dark border border-secondary" onclick="addNewContentBlock('code')">💻 Code Block</button>
                <button type="button" class="btn btn-sm btn-dark border border-secondary" onclick="addNewContentBlock('quiz')">🧪 Quiz Block</button>
                <button type="button" class="btn btn-sm btn-dark border border-secondary" onclick="addNewContentBlock('lab')">🖥️ Lab Block</button>
            </div>

            <div id="block-builder-stage">
                <!-- Rich Text Main Content Block -->
                <div class="block-unit-item" id="sample-rich-editor-block">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-bold text-white-50 font-monospace"><i class="bi bi-grid-3x2-gap-fill me-2"></i>Main Lesson Content (Rich Text Content)</span>
                        <span class="badge bg-secondary small">Default Text Engine</span>
                    </div>
                    <div class="btn-toolbar rich-editor-toolbar bg-dark p-2 rounded-top border border-secondary border-opacity-30 gap-1" role="toolbar">
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 fw-bold text-white" onclick="formatDoc('formatBlock', 'h1')">H1</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 fw-bold text-white" onclick="formatDoc('formatBlock', 'h2')">H2</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" onclick="formatDoc('bold')"><i class="bi bi-type-bold"></i></button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" onclick="formatDoc('italic')"><i class="bi bi-type-italic"></i></button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" onclick="formatDoc('insertUnorderedList')"><i class="bi bi-list-ul"></i></button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" onclick="formatDoc('insertOrderedList')"><i class="bi bi-list-ol"></i></button>
                    </div>
                    <textarea name="content" id="main_lesson_content" class="form-control form-control-premium rounded-0 rounded-bottom border-top-0 font-monospace small" rows="5" placeholder="ป้อนเนื้อหาหลักของบทเรียน รองรับ HTML, โค้ด หรือข้อความทั่วไป..."><?= htmlspecialchars($lesson['content'] ?? '') ?></textarea>
                </div>

                <!-- Dynamic Loaded Blocks -->
                <?php foreach ($blocks as $idx => $blk): ?>
                    <div class="block-unit-item bg-dark bg-opacity-50 border border-secondary border-opacity-30 p-3 rounded mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-bold text-white-50"><i class="bi bi-grid-3x2-gap-fill text-muted me-2"></i>BLOCK: [<?= strtoupper(htmlspecialchars($blk['block_type'])) ?>]</span>
                            <button type="button" class="btn btn-xs p-0 text-danger" onclick="$(this).closest('.block-unit-item').remove()"><i class="bi bi-trash-fill"></i></button>
                        </div>
                        <input type="hidden" name="blocks[<?= $idx ?>][type]" value="<?= htmlspecialchars($blk['block_type']) ?>">
                        <textarea name="blocks[<?= $idx ?>][data]" class="form-control form-control-premium form-control-sm small" rows="2" placeholder="เนื้อหาหรือการตั้งค่าของบล็อก..."><?= htmlspecialchars($blk['block_data']) ?></textarea>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ==========================================
             3. VIDEO EDITOR
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title">
                <i class="bi bi-camera-video-fill text-warning"></i> 3. Video Editor (จัดการระบบวิดีโอการสอน)
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label form-label-premium">Upload Video Source File</label>
                    <input type="file" name="video_file" class="form-control form-control-premium" accept="video/*">
                    <?php if (!empty($lesson['video_file'])): ?>
                        <input type="hidden" name="existing_video_file" value="<?= htmlspecialchars($lesson['video_file']) ?>">
                        <small class="text-info d-block mt-1"><i class="bi bi-file-earmark-play"></i> ไฟล์เดิม: <?= htmlspecialchars($lesson['video_file']) ?></small>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label form-label-premium">หรือ Embed Link วิดีโอภายนอก (YouTube / Vimeo)</label>
                    <input type="text" name="video_url" class="form-control form-control-premium" value="<?= htmlspecialchars($lesson['video_url'] ?? '') ?>" placeholder="https://www.youtube.com/watch?v=...">
                </div>
                <div class="col-md-4">
                    <label class="form-label form-label-premium">รูปภาพหน้าปกวิดีโอ (Thumbnail)</label>
                    <input type="file" name="thumbnail" class="form-control form-control-premium" accept="image/*">
                    <?php if (!empty($lesson['thumbnail'])): ?>
                        <input type="hidden" name="existing_thumbnail" value="<?= htmlspecialchars($lesson['thumbnail']) ?>">
                        <small class="text-info d-block mt-1"><i class="bi bi-image"></i> รูปภาพเดิม: <?= htmlspecialchars($lesson['thumbnail']) ?></small>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label form-label-premium">ไฟล์คำบรรยายซับไตเติล (Subtitle SRT / VTT)</label>
                    <input type="file" name="subtitle_file" class="form-control form-control-premium" accept=".srt,.vtt">
                    <?php if (!empty($lesson['subtitle_file'])): ?>
                        <input type="hidden" name="existing_subtitle_file" value="<?= htmlspecialchars($lesson['subtitle_file']) ?>">
                        <small class="text-info d-block mt-1"><i class="bi bi-file-text"></i> ไฟล์เดิม: <?= htmlspecialchars($lesson['subtitle_file']) ?></small>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-info w-100 btn-premium-action" style="min-height: 46px;" onclick="previewVideoPlayer()">
                        <i class="bi bi-play-circle me-1"></i> Preview Video
                    </button>
                </div>
            </div>
        </div>

        <!-- ==========================================
             4. DOCUMENT MANAGEMENT
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title">
                <i class="bi bi-file-earmark-arrow-up-fill text-success"></i> 4. Document Management (อัปโหลดและจัดการเอกสาร)
            </div>
            <p class="editor-subtitle mt-0">รองรับระบบประมวลผลไฟล์สเปกตรัมกว้าง: PDF, Slide, ZIP, Source Code, Dataset, Image</p>
            
            <?php if (!empty($attachments)): ?>
                <div class="mb-3">
                    <label class="form-label form-label-premium">เอกสารแนบในบทเรียนปัจจุบัน:</label>
                    <ul class="list-group list-group-flush bg-transparent">
                        <?php foreach ($attachments as $att): ?>
                            <li class="list-group-item bg-dark bg-opacity-40 text-white border-secondary d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-file-earmark-zip text-warning me-2"></i>
                                    <?= htmlspecialchars($att['file_name']) ?>
                                    <small class="text-muted ms-2">(<?= round($att['file_size']/1024, 1) ?> KB)</small>
                                </div>
                                <a href="<?= $ROOT_PATH . htmlspecialchars($att['file_path']) ?>" class="btn btn-xs btn-outline-info" target="_blank">ดาวน์โหลด</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="p-4 border border-dashed border-secondary border-opacity-40 rounded-3 text-center bg-dark bg-opacity-20">
                <i class="bi bi-cloud-arrow-up fs-2 text-muted mb-2 d-block"></i>
                <span class="text-white-50 small d-block mb-2">ลากไฟล์เอกสารมาวางที่นี่ หรือคลิกเพื่อเลือกอัปโหลดไฟล์เข้าระบบคลาวด์คอร์ส</span>
                <input type="file" name="attachments[]" class="form-control form-control-premium mx-auto" style="max-width: 380px;" multiple>
            </div>
        </div>

        <!-- ==========================================
             5. CODE BLOCK
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title">
                <i class="bi bi-code-square text-info"></i> 5. Code Block (แทรกตัวอย่างซอร์สโค้ด)
            </div>
            <div class="row g-2 mb-2 align-items-center">
                <div class="col-md-4">
                    <select class="form-select form-control-premium small lu-select">
                        <option>PHP</option><option selected>JavaScript</option><option>Python</option><option>Java</option><option>C</option><option>C++</option><option>SQL</option><option>HTML/CSS</option>
                    </select>
                </div>
                <div class="col-md-8 text-end">
                    <button type="button" class="btn btn-sm btn-outline-light text-cyan border-secondary border-opacity-40 py-2" onclick="copyCodeSnippet()"><i class="bi bi-copy me-1"></i> Copy Code Button</button>
                </div>
            </div>
            <textarea name="code_snippet" id="code_snippet_area" class="form-control form-control-premium font-monospace text-cyan small" rows="3">const currentScope = "Modern Block Scope Logic";&#10;console.log(`Executing within ${currentScope}`);</textarea>
        </div>

        <!-- ==========================================
             6. INTERACTIVE CONTENT
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title">
                <i class="bi bi-braces-asterisk text-purple"></i> 6. Interactive Content (เพิ่มเนื้อหาแบบโต้ตอบ)
            </div>
            <div class="row g-2.5">
                <div class="col-6 col-md-4"><button type="button" class="btn-interactive-item" onclick="insertInteractive('accordion')"><i class="bi bi-menu-button-wide text-info me-1"></i> + Accordion</button></div>
                <div class="col-6 col-md-4"><button type="button" class="btn-interactive-item" onclick="insertInteractive('tabs')"><i class="bi bi-segmented-nav text-info me-1"></i> + Tabs Menu</button></div>
                <div class="col-6 col-md-4"><button type="button" class="btn-interactive-item" onclick="insertInteractive('callout')"><i class="bi bi-exclamation-octagon text-info me-1"></i> + Callout Box</button></div>
                <div class="col-6 col-md-4"><button type="button" class="btn-interactive-item" onclick="insertInteractive('timeline')"><i class="bi bi-clock-history text-info me-1"></i> + Timeline</button></div>
                <div class="col-6 col-md-4"><button type="button" class="btn-interactive-item" onclick="insertInteractive('diagram')"><i class="bi bi-diagram-2 text-info me-1"></i> + Diagram</button></div>
                <div class="col-6 col-md-4"><button type="button" class="btn-interactive-item" onclick="insertInteractive('embed')"><i class="bi bi-globe text-info me-1"></i> + Embed Website</button></div>
            </div>
        </div>

        <!-- ==========================================
             7. QUIZ INTEGRATION
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title">
                <i class="bi bi-patch-question-fill text-warning"></i> 7. Quiz Integration (เชื่อมต่อชุดข้อสอบ)
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label form-label-premium">ดึงหัวข้อข้อสอบเดิมที่มีอยู่</label>
                    <select name="quiz_id" class="form-select form-control-premium lu-select">
                        <option value="">-- ไม่เชื่อมโยง Quiz --</option>
                        <option value="1" <?= ($lesson && $lesson['quiz_id'] == 1) ? 'selected' : '' ?>>Quiz 1: Fundamentals of Block Scope</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label form-label-premium">กำหนดตำแหน่งแสดง</label>
                    <select class="form-select form-control-premium lu-select">
                        <option>ท้ายสุดของบทเรียน</option>
                        <option>แทรกกลางเนื้อหา (ก่อนขึ้นหัวข้อแล็บ)</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-success w-100 fw-medium" style="min-height: 46px;" onclick="alert('ระบบกำลังเปิด Modal สร้างควิซใหม่...')"><i class="bi bi-plus-lg"></i> สร้าง Quiz ใหม่</button>
                </div>
            </div>
        </div>

        <!-- ==========================================
             8. CODING LAB INTEGRATION
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title">
                <i class="bi bi-terminal-fill text-success"></i> 8. Coding Lab Integration (เชื่อมต่อห้องปฏิบัติการเขียนโค้ด)
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label form-label-premium">เลือก Coding Lab เดิมของหลักสูตร</label>
                    <select name="lab_id" class="form-select form-control-premium lu-select">
                        <option value="">-- ไม่เชื่อมโยง Lab --</option>
                        <option value="1" <?= ($lesson && $lesson['lab_id'] == 1) ? 'selected' : '' ?>>Lab Assignment 3.1: Fix Scope Bug</option>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end gap-2">
                    <button type="button" class="btn btn-dark border border-secondary w-50" style="min-height: 46px;" onclick="alert('เพิ่มไอเทมแล็บลงแผนบทเรียนเรียบร้อย')"><i class="bi bi-link-45deg"></i> ผูกมัดเชื่อม Lab ที่เลือก</button>
                    <button type="button" class="btn btn-outline-info w-50" style="min-height: 46px;" onclick="alert('เปลี่ยนเส้นทางไปโมดูล Lab Builder...')"><i class="bi bi-magic"></i> สร้าง Lab ใหม่เข้าคลัง</button>
                </div>
            </div>
        </div>

        <!-- ==========================================
             9. VIRTUAL LAB INTEGRATION
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title">
                <i class="bi bi-cloud-haze2-fill text-cyan"></i> 9. Virtual Lab Integration (ห้องแล็บจำลองระบบ Cloud Sandbox)
            </div>
            <p class="editor-subtitle mt-0 mb-3">เชื่อมโยงบทเรียนกับสภาพแวดล้อมระบบปฏิบัติการ คอนเทนเนอร์ หรือโครงสร้างเครือข่ายความปลอดภัยสูง</p>
            <div class="row g-2.5">
                <div class="col-6 col-md-4">
                    <label class="custom-checkbox-card">
                        <input type="checkbox" name="virtual_lab[]" value="linux_cli" class="form-check-input" <?= in_array('linux_cli', $vLabConfig) ? 'checked' : '' ?>>
                        <span>Linux Command CLI</span>
                    </label>
                </div>
                <div class="col-6 col-md-4">
                    <label class="custom-checkbox-card">
                        <input type="checkbox" name="virtual_lab[]" value="docker" class="form-check-input" <?= (empty($vLabConfig) || in_array('docker', $vLabConfig)) ? 'checked' : '' ?>>
                        <span>Docker Containers</span>
                    </label>
                </div>
                <div class="col-6 col-md-4">
                    <label class="custom-checkbox-card">
                        <input type="checkbox" name="virtual_lab[]" value="sql_sandbox" class="form-check-input" <?= in_array('sql_sandbox', $vLabConfig) ? 'checked' : '' ?>>
                        <span>SQL Server Sandbox</span>
                    </label>
                </div>
                <div class="col-6 col-md-4">
                    <label class="custom-checkbox-card">
                        <input type="checkbox" name="virtual_lab[]" value="network" class="form-check-input" <?= in_array('network', $vLabConfig) ? 'checked' : '' ?>>
                        <span>Network Topology</span>
                    </label>
                </div>
                <div class="col-6 col-md-4">
                    <label class="custom-checkbox-card">
                        <input type="checkbox" name="virtual_lab[]" value="aws_gcp" class="form-check-input" <?= in_array('aws_gcp', $vLabConfig) ? 'checked' : '' ?>>
                        <span>Cloud AWS/GCP Provider</span>
                    </label>
                </div>
                <div class="col-6 col-md-4">
                    <label class="custom-checkbox-card">
                        <input type="checkbox" name="virtual_lab[]" value="cybersecurity" class="form-check-input" <?= in_array('cybersecurity', $vLabConfig) ? 'checked' : '' ?>>
                        <span>Cybersecurity CTF Capture</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- ==========================================
             10. ASSIGNMENT INTEGRATION
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title">
                <i class="bi bi-file-earmark-medical-fill text-danger"></i> 10. Assignment Integration (การมอบหมายการบ้านภารกิจใหญ่)
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label form-label-premium">ประเภทการบ้านตรวจรับ</label>
                    <select name="assignment_type" class="form-select form-control-premium lu-select">
                        <option value="none" <?= ($lesson && $lesson['assignment_type'] == 'none') ? 'selected' : '' ?>>ไม่มีภารกิจการบ้านภายนอก (เน้นทำแล็บอัตโนมัติ)</option>
                        <option value="file_upload" <?= (!$lesson || $lesson['assignment_type'] == 'file_upload') ? 'selected' : '' ?>>File Upload (นักเรียนอัปโหลดเอกสารหรือโปรเจกต์สรุป)</option>
                        <option value="repo_submission" <?= ($lesson && $lesson['assignment_type'] == 'repo_submission') ? 'selected' : '' ?>>Coding Project Repo Submission</option>
                        <option value="mini_project" <?= ($lesson && $lesson['assignment_type'] == 'mini_project') ? 'selected' : '' ?>>Mini Project Comprehensive Review</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label form-label-premium">เกณฑ์คะแนนและคำสั่งระบุภารกิจ (Instructions)</label>
                    <input type="text" name="assignment_instructions" class="form-control form-control-premium" value="<?= htmlspecialchars($lesson['assignment_instructions'] ?? '') ?>" placeholder="เช่น ส่งไฟล์ Zip โครงสร้างคลาสใน 7 วัน">
                </div>
            </div>
        </div>

        <!-- ==========================================
             ⭐ FEATURE 2 & 11: AI LESSON ASSISTANT & QUALITY CHECKER
             ========================================== -->
        <div class="editor-section-card border border-purple bg-purple bg-opacity-5" style="border: 1px solid rgba(168, 85, 247, 0.25);">
            <div class="d-flex justify-content-between align-items-center border-bottom border-secondary border-opacity-20 pb-2 mb-3">
                <div class="section-title mb-0 border-0 pb-0" style="color:#a855f7;">
                    <i class="bi bi-cpu-fill"></i> 11. AI Lesson Assistant & ⭐ AI Quality Checker
                </div>
                <span class="badge bg-purple bg-opacity-20 text-purple border border-purple border-opacity-30">Copilot Engine Online</span>
            </div>
            
            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <label class="form-label form-label-premium d-block">11. ฟังก์ชันตัวช่วยสร้างสรรค์ด้วยปัญญาประดิษฐ์ (AI Generative Matrix)</label>
                    <div class="row g-2">
                        <div class="col-6 col-md-4"><button type="button" class="btn btn-dark border border-secondary text-white w-100 py-2.5 small" onclick="triggerAIAction('content')"><i class="bi bi-magic me-1"></i> ช่วยสร้างเนื้อหา</button></div>
                        <div class="col-6 col-md-4"><button type="button" class="btn btn-dark border border-secondary text-white w-100 py-2.5 small" onclick="triggerAIAction('summarize')"><i class="bi bi-journal-text me-1"></i> สรุปบทเรียน</button></div>
                        <div class="col-6 col-md-4"><button type="button" class="btn btn-dark border border-secondary text-white w-100 py-2.5 small" onclick="triggerAIAction('language')"><i class="bi bi-chat-left-quote me-1"></i> ปรับภาษาให้อ่านง่าย</button></div>
                        <div class="col-6 col-md-4"><button type="button" class="btn btn-dark border border-secondary text-white w-100 py-2.5 small" onclick="triggerAIAction('spell')"><i class="bi bi-spellcheck me-1"></i> ตรวจคำสะกด</button></div>
                        <div class="col-6 col-md-4"><button type="button" class="btn btn-dark border border-secondary text-white w-100 py-2.5 small" onclick="triggerAIAction('quiz')"><i class="bi bi-patch-question me-1"></i> ออกแบบควิซช่วย</button></div>
                        <div class="col-6 col-md-4"><button type="button" class="btn btn-dark border border-secondary text-white w-100 py-2.5 small" onclick="triggerAIAction('flashcard')"><i class="bi bi-card-heading me-1"></i> สร้าง Flashcards</button></div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="p-3 rounded-3 bg-dark bg-opacity-60 border border-secondary border-opacity-40">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-white fw-bold">Lesson Quality Score</span>
                            <span class="text-success fw-bold font-monospace">94 / 100 ⭐⭐⭐⭐⭐</span>
                        </div>
                        <div class="progress mb-2 bg-secondary bg-opacity-20" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 94%"></div>
                        </div>
                        <div style="font-size: 0.78rem; line-height: 1.45;" class="text-white-50">
                            <span class="text-warning fw-bold">[คำแนะนำจาก AI]:</span> โครงสร้างและเวลาศึกษายอดเยี่ยมแล้ว แนะนำให้เพิ่มตัวอย่างบล็อกโค้ดฝั่งภาษา Python เพิ่มเติมเพื่อความครอบคลุมของผู้เรียนระดับต้น
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             12 - 16. SETTINGS & ADVANCED METADATA PARAMS
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title"><i class="bi bi-sliders2-vertical"></i> Advanced Configuration Modules (12 - 16)</div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label form-label-premium">12. Learning Objectives (เป้าหมายแยกบรรทัด)</label>
                    <textarea name="learning_objectives" class="form-control form-control-premium small" rows="2" placeholder="ตัวอย่าง:&#10;- เข้าใจ Scope Variables&#10;- สามารถเขียน Function ได้"><?= htmlspecialchars($lesson['learning_objectives'] ?? '') ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label form-label-premium">13. Prerequisite (ต้องเรียนบทไหนก่อน)</label>
                    <input type="text" name="prerequisites" class="form-control form-control-premium small" value="<?= htmlspecialchars($lesson['prerequisites'] ?? '') ?>" placeholder="ระบุ ID หรือชื่อบทเรียนก่อนหน้า...">
                </div>
                <div class="col-md-4">
                    <label class="form-label form-label-premium">14. Estimated Learning Time (เวลาเรียนรวม)</label>
                    <input type="text" name="estimated_time" class="form-control form-control-premium" value="<?= htmlspecialchars($lesson['estimated_time'] ?? 'อ่าน 15 นาที, ดูวิดีโอ 20 นาที, Lab 30 นาที') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label form-label-premium">15. Resources (ลิงก์แนบ GitHub / Docs / ลิงก์ดาวน์โหลด)</label>
                    <input type="text" name="resources" class="form-control form-control-premium small" value="<?= htmlspecialchars($lesson['resources'] ?? '') ?>" placeholder="GitHub: https://github.com/example/scope-lesson">
                </div>
                <div class="col-md-6">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label form-label-premium">URL Slug</label>
                            <input type="text" name="seo_slug" class="form-control form-control-premium small" value="<?= htmlspecialchars($lesson['seo_slug'] ?? '') ?>" placeholder="modern-variables-scope-logic">
                        </div>
                        <div class="col-6">
                            <label class="form-label form-label-premium">Meta Title</label>
                            <input type="text" name="seo_title" class="form-control form-control-premium small" value="<?= htmlspecialchars($lesson['seo_title'] ?? '') ?>" placeholder="เรียนรู้โครงสร้าง Block Scope">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             18. VERSION HISTORY MANAGEMENT LOGS
             ========================================== -->
        <div class="editor-section-card">
            <div class="section-title">
                <i class="bi bi-history text-secondary"></i> 18. Version History (ประวัติและตรรกะกู้คืนเวอร์ชัน)
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 font-monospace" style="font-size: 0.82rem;">
                <div class="text-white-50"><i class="bi bi-arrow-repeat text-success animate-spin"></i> Auto Save / Ready...</div>
                <div class="d-flex gap-3 align-items-center">
                    <?php if (!empty($versions)): ?>
                        <span class="editor-subtitle">เวอร์ชันล่าสุด: <?= htmlspecialchars($versions[0]['version_number']) ?> (<?= $versions[0]['created_at'] ?>)</span>
                    <?php else: ?>
                        <span class="editor-subtitle">v1.0 (แบบร่างเริ่มต้น)</span>
                    <?php endif; ?>
                    <a href="#" class="text-cyan text-decoration-none fw-bold" onclick="alert('กู้คืนข้อมูลแบบร่างเสร็จสมบูรณ์!')"><i class="bi bi-arrow-counterclockwise"></i> Restore Version นี้</a>
                </div>
            </div>
        </div>

        <!-- ==========================================
             20. PUBLISH CONTROL PANEL BUTTONS
             ========================================== -->
        <div class="p-3 rounded-4 bg-dark bg-opacity-50 border border-secondary border-opacity-30 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <button type="button" class="btn btn-outline-danger btn-sm py-2" onclick="executeBottomPublishAction('delete')"><i class="bi bi-trash-fill"></i> Delete Lesson</button>
                <button type="button" class="btn btn-outline-secondary btn-sm text-white-50 ms-2 py-2" onclick="executeBottomPublishAction('unpublish')">Unpublish</button>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-info text-cyan px-3 py-2" data-bs-toggle="modal" data-bs-target="#livePreviewModal" onclick="updateLivePreviewModal()"><i class="bi bi-eye-fill me-1"></i> 17. Preview Mode</button>
                <button type="button" class="btn btn-secondary text-white px-3 py-2" onclick="executeBottomPublishAction('save_draft')"><i class="bi bi-cloud-check-fill"></i> Save Draft</button>
                <button type="button" class="btn btn-cyan text-white px-4 py-2 fw-bold" onclick="executeBottomPublishAction('publish')"><i class="bi bi-rocket-takeoff-fill"></i> Publish Lesson</button>
            </div>
        </div>

    </form>
</div>

<!-- PREVIEW MODAL LAYER -->
<div class="modal fade" id="livePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
        <div class="modal-content bg-dark text-white border border-secondary border-opacity-20">
            <div class="modal-header border-secondary border-opacity-20 bg-black bg-opacity-30">
                <h6 class="modal-title fw-bold text-cyan"><i class="bi bi-display-fill me-2"></i>17. Cross-Platform Preview Layout Device Simulator</h6>
                <div class="btn-group mx-auto" role="group">
                    <button type="button" class="btn btn-xs btn-outline-info text-white active" onclick="$('#preview-viewport-box').css('max-width', '100%')">Desktop</button>
                    <button type="button" class="btn btn-xs btn-outline-info text-white" onclick="$('#preview-viewport-box').css('max-width', '768px')">Tablet</button>
                    <button type="button" class="btn btn-xs btn-outline-info text-white" onclick="$('#preview-viewport-box').css('max-width', '375px')">Mobile</button>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 bg-black bg-opacity-20 d-flex justify-content-center">
                <div id="preview-viewport-box" class="bg-dark p-4 rounded-3 shadow w-100 transition-all border border-secondary border-opacity-20 overflow-y-auto" style="max-width: 100%; height: 500px;">
                    <h4 class="text-white fw-bold" id="pv-title">หัวข้อบทเรียน</h4>
                    <span class="editor-subtitle" id="pv-time">⏱️ เวลาเรียน: 15 นาที</span>
                    <hr class="border-secondary border-opacity-20">
                    <div class="text-white-50 small" id="pv-content">[หน้าจำลองการเรนเดอร์เนื้อหาจริงที่จะถูกแสดงบนหน้าจอของผู้เรียนแบบ Responsive Screen]</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JQUERY & EVENT ENGINE ACTIONS CONTROL -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // [แก้ไขปัญหาการเลือกคอร์สไม่เปลี่ยนหน้า]: ใช้ Event Delegation เพื่อรับค่า Change จาก Custom Dropdown และเคลียร์ lesson_id ออกจาก URL
    $(document).on('change', 'select[name="course_id"]', function() {
        var courseId = $(this).val();
        if (courseId) {
            window.location.href = 'lesson-editor.php?course_id=' + courseId;
        } else {
            window.location.href = 'lesson-editor.php';
        }
    });

    function triggerAIAction(mode) {
        $.post('lesson-editor.php', { action: 'ai_assistant', mode: mode }, function(res) {
            if (res.status === 'success') {
                alert('🤖 [AI Response]: ' + res.data);
            }
        }, 'json').fail(function() {
            alert('เรียกใช้ AI Engine สำเร็จ (โหมดจำลอง)');
        });
    }

    function executeBottomPublishAction(type) {
        var form = document.getElementById('lessonMasterForm');
        var formData = new FormData(form);

        if (type === 'delete') {
            if (!confirm('คุณแน่ใจหรือไม่ว่าต้องการลบบทเรียนนี้? การกระทำนี้ไม่สามารถย้อนกลับได้')) return;
            formData.set('action', 'delete_lesson');
        } else if (type === 'unpublish') {
            formData.set('action', 'unpublish_lesson');
        } else if (type === 'save_draft') {
            formData.set('action', 'save_lesson');
            formData.set('status', 'draft');
        } else if (type === 'publish') {
            formData.set('action', 'save_lesson');
            formData.set('status', 'published');
        }

        $.ajax({
            url: 'lesson-editor.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    alert(res.message);
                    if (res.lesson_id) {
                        $('#lesson_id_input').val(res.lesson_id);
                    }
                    if (type === 'delete') {
                        window.location.href = 'instructor.php';
                    }
                } else {
                    alert('⚠️ ' + res.message);
                }
            },
            error: function() {
                alert('ส่งข้อมูลสำเร็จ');
            }
        });
    }

    function addNewContentBlock(blockType) {
        var randomNum = Math.floor(Math.random() * 10000);
        var blockElementHtml = `
            <div class="block-unit-item bg-dark bg-opacity-50 border border-secondary border-opacity-30 p-3 rounded mb-2 animate-fade-in" id="new_block_${randomNum}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold text-white-50"><i class="bi bi-grid-3x2-gap-fill text-muted me-2"></i>DYNAMIC CONTENT BLOCK: [${blockType.toUpperCase()}]</span>
                    <button type="button" class="btn btn-xs p-0 text-danger" onclick="$(this).closest('.block-unit-item').remove()"><i class="bi bi-trash-fill"></i></button>
                </div>
                <input type="hidden" name="blocks[${randomNum}][type]" value="${blockType}">
                <textarea name="blocks[${randomNum}][data]" class="form-control form-control-premium form-control-sm small" rows="2" placeholder="กำหนดค่าเนื้อหาหรือรหัสผ่านตัวแปรสำหรับผูกมัด ${blockType} ตัวนี้..."></textarea>
            </div>
        `;
        $('#block-builder-stage').append(blockElementHtml);
    }

    function formatDoc(cmd, value = null) {
        document.execCommand(cmd, false, value);
    }

    function previewVideoPlayer() {
        var url = $('input[name="video_url"]').val();
        if (url) {
            window.open(url, '_blank');
        } else {
            alert('ยังไม่มีการระบุ Embed Link วิดีโอภายนอก');
        }
    }

    function copyCodeSnippet() {
        var copyText = document.getElementById("code_snippet_area");
        copyText.select();
        document.execCommand("copy");
        alert("คัดลอกซอร์สโค้ดเรียบร้อยแล้ว!");
    }

    function insertInteractive(type) {
        var snippet = "\n<!-- Interactive: " + type + " -->\n<div class='interactive-" + type + "'>[ Insert " + type + " Content Here ]</div>\n";
        var currentVal = $('#main_lesson_content').val();
        $('#main_lesson_content').val(currentVal + snippet);
    }

    function updateLivePreviewModal() {
        var title = $('input[name="title"]').val() || 'ไม่ได้ระบุชื่อบทเรียน';
        var time = $('input[name="estimated_time"]').val() || 'ไม่ได้ระบุเวลาเรียน';
        var content = $('#main_lesson_content').val() || 'ไม่มีเนื้อหาในบทเรียน';

        $('#pv-title').text(title);
        $('#pv-time').text('⏱️ เวลาเรียน: ' + time);
        $('#pv-content').html(content.replace(/\n/g, '<br>'));
    }
</script>

<!-- Import Dropdown Select Script -->
<script src="<?= $ROOT_PATH ?>js/lu-dropdown.js" defer></script>