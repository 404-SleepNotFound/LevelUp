<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// รับ Data จาก Client
$request = json_decode(file_get_contents('php://input'), true);
$action = $request['action'] ?? '';

// Mock Data ภารกิจ (Tasks)
$tasks = [
    ['id' => 1, 'title' => 'ตรวจสอบ Network ปัจจุบัน', 'cmd' => 'docker network ls', 'done' => false],
    ['id' => 2, 'title' => 'สร้าง Network ชื่อ app-net', 'cmd' => 'docker network create app-net', 'done' => false],
    ['id' => 3, 'title' => 'รัน Container', 'cmd' => 'docker-compose up -d', 'done' => false]
];

if (!isset($_SESSION['lab_tasks'])) {
    $_SESSION['lab_tasks'] = $tasks;
}

switch ($action) {
    case 'execute_command':
        $cmd = trim($request['command']);
        $response = ['status' => 'success', 'output' => '', 'taskCompleted' => null, 'ai_trigger' => null];
        
        // จำลองการทำงานของ Linux / Docker CLI
        if ($cmd === 'ls') {
            $response['output'] = "docker-compose.yml  setup.sh  README.md";
        } elseif ($cmd === 'docker network ls') {
            $response['output'] = "NETWORK ID     NAME      DRIVER    SCOPE\n1a2b3c4d5e6f   bridge    bridge    local\n7g8h9i0j1k2l   host      host      local";
            checkTaskCompletion(1, $response);
        } elseif ($cmd === 'docker network create app-net') {
            $response['output'] = "a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6";
            checkTaskCompletion(2, $response);
        } elseif ($cmd === 'docker-compose up -d') {
            $response['output'] = "[+] Running 2/2\n ✔ Container web Started\n ✔ Container db Started";
            checkTaskCompletion(3, $response);
        } elseif (empty($cmd)) {
             $response['output'] = "";
        } else {
            $response['output'] = "bash: $cmd: command not found";
            // AI Trigger เมื่อเจอ Error
            $response['ai_trigger'] = "ตรวจพบข้อผิดพลาด: ไม่พบคำสั่ง `$cmd` คุณอาจพิมพ์ผิด หรือแพ็กเกจยังไม่ได้ถูกติดตั้งครับ";
        }
        
        echo json_encode($response);
        break;

    case 'get_tasks':
        echo json_encode(['status' => 'success', 'tasks' => $_SESSION['lab_tasks']]);
        break;

    case 'analyze_error':
        // จำลอง AI วิเคราะห์
        echo json_encode([
            'status' => 'success',
            'message' => "<b>สาเหตุ:</b> Container `web` และ `db` อยู่คนละ Network กัน ทำให้มองไม่เห็น IP ของกันและกัน<br><br><b>วิธีแก้:</b> ระบุ `networks:` ในไฟล์ docker-compose.yml ให้ทั้งคู่เชื่อมต่อเข้าข่ายเดียวกันครับ"
        ]);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}

// Helper Function อัปเดต Task
function checkTaskCompletion($taskId, &$response) {
    foreach ($_SESSION['lab_tasks'] as &$task) {
        if ($task['id'] == $taskId && !$task['done']) {
            $task['done'] = true;
            $response['taskCompleted'] = $taskId;
            break;
        }
    }
}
?>