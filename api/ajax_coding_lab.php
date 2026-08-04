<?php
session_start();
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
$code = $_POST['code'] ?? '';
$language = $_POST['language'] ?? 'python';

// หน่วงเวลาจำลอง
usleep(1000000); 

$response = ['status' => 'success'];

switch ($action) {
    case 'run':
        // จำลองการรันโค้ด
        if (strpos($code, 'syntax_error') !== false) {
            $response['console'] = "<span class='text-danger'>SyntaxError: invalid syntax (line 3)</span>";
        } else {
            $response['console'] = "Execution successful.\nOutput: [0, 1]\n\n<span class='text-muted'>--- Time: 45ms | Memory: 14.2 MB ---</span>";
        }
        break;

    case 'submit':
        // จำลองผล Test Cases
        $html = '
            <div class="d-flex flex-column gap-2">
                <div class="p-2 border border-success bg-success bg-opacity-10 rounded-3 border-opacity-50">
                    <div class="d-flex justify-content-between mb-1"><span class="fw-bold fs-sm">Test Case 1 (Public)</span><span class="badge-pass">PASS</span></div>
                    <div class="fs-xs text-muted">Input: nums = [2,7,11,15], target = 9 <br> Expected: [0,1] | Output: [0,1]</div>
                </div>
                <div class="p-2 border border-success bg-success bg-opacity-10 rounded-3 border-opacity-50">
                    <div class="d-flex justify-content-between mb-1"><span class="fw-bold fs-sm">Test Case 2 (Public)</span><span class="badge-pass">PASS</span></div>
                    <div class="fs-xs text-muted">Input: nums = [3,2,4], target = 6 <br> Expected: [1,2] | Output: [1,2]</div>
                </div>
                <div class="p-2 border border-danger bg-danger bg-opacity-10 rounded-3 border-opacity-50">
                    <div class="d-flex justify-content-between mb-1"><span class="fw-bold fs-sm">Test Case 3 (Hidden)</span><span class="badge-fail">FAIL</span></div>
                    <div class="fs-xs text-muted">Time Limit Exceeded (Expected O(n))</div>
                </div>
            </div>';
        $response['test_html'] = $html;
        break;

    case 'ai_review':
        // จำลอง AI Code Review
        $response['markdown'] = "### 🔍 AI Code Review\n\nจากการวิเคราะห์โค้ดของคุณ:\n\n*   ✅ **Logic:** ทำงานได้ถูกต้องในกรณีทั่วไป\n*   ⚠️ **Performance:** ความซับซ้อนของเวลา (Time Complexity) ปัจจุบันคือ `O(n²)` เนื่องจากใช้ Loop ซ้อนกัน\n*   💡 **Best Practice:** แนะนำให้ใช้ `Dictionary` (หรือ Hash Map) ในการเก็บค่าที่เคยวนลูปผ่านมาแล้ว เพื่อลด Complexity เหลือ `O(n)`";
        break;

    case 'ai_hint':
        $level = $_POST['level'] ?? 1;
        if ($level == 1) {
            $response['html'] = "<b>Hint 1:</b> แทนที่จะวนลูป 2 รอบเพื่อหาคู่ ลองวนลูปแค่รอบเดียว แล้วเก็บเป้าหมายที่ต้องการ (target - num) ไว้ในตัวแปรแบบ Dictionary ดูสิครับ";
        } else {
            $response['html'] = "<b>Hint 2:</b> โครงสร้างคือ: `seen = {}`. ถัดมา `for i, num in enumerate(nums):`. เช็คว่า `target - num` มีใน `seen` หรือไม่";
        }
        break;
        
    case 'ai_explain':
        $response['markdown'] = "### 📖 การทำงานของโค้ดนี้\n\nโค้ดที่คุณเขียนคืออัลกอริทึม **Brute Force** สำหรับแก้ปัญหา Two Sum\n1. โปรแกรมจะหยิบตัวเลขตัวแรกมา\n2. จากนั้นวนลูปเทียบกับตัวเลขที่เหลือทั้งหมดใน Array\n3. ถ้าผลบวกเท่ากับ `target` จะรีเทิร์นค่า Index ทันที\n\n*แม้จะเข้าใจง่าย แต่จะช้ามากหากมีข้อมูลหลักแสนตัว*";
        break;
}

echo json_encode($response);
exit;