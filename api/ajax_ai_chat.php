<?php
session_start();
header('Content-Type: application/json');

// หน่วงเวลาจำลอง AI Processing (1.5 - 2 วินาที)
usleep(rand(1500000, 2500000)); 

$message =$_POST['message'] ?? '';
$mentor_mode = filter_var($_POST['mentor_mode'] ?? 'false', FILTER_VALIDATE_BOOLEAN);
$context =$_SESSION['current_context'] ?? [];

$response_markdown = "";

// 1. ตรวจสอบ Keyword เพื่อจำลองการตอบ (AI Context Awareness)
$lower_msg = mb_strtolower($message);

if (strpos($lower_msg, 'แก้บั๊ก') !== false || strpos($lower_msg, 'importerror') !== false \vert{}\vert{} strpos($lower_msg, 'error') !== false) {
    if ($mentor_mode) {$response_markdown = "### วิเคราะห์บั๊ก: `ImportError`\n\nผมเห็นว่าคุณกำลังเจอปัญหา `ImportError: No module named flask` ในบทเรียน REST API\n\n**🔍 คำใบ้ (Hint):**\n1. ปัญหานี้มักเกิดจากการที่ตัวแปลภาษา (Python) หา Library ไม่เจอ\n2. ลองเช็คดูว่าใน Virtual Environment ของคุณได้ติดตั้งแพ็กเกจนี้หรือยัง?\n3. ลองนึกถึงคำสั่ง `pip ...` ที่เราใช้จัดการแพ็กเกจดูครับ\n\n*ถ้าอยากให้เฉลย พิมพ์ว่า \"ขอวิธีแก้เลย\" หรือปิด Mentor Mode ครับ*";
    } else {
        $response_markdown = "### สาเหตุและวิธีแก้ `ImportError: No module named flask`\n\nปัญหานี้เกิดจากการที่โปรเจกต์ของคุณยังไม่ได้ติดตั้งไลบรารี `Flask` ครับ\n\n**✅ วิธีแก้ปัญหา (Solution):**\nเปิด Terminal ขึ้นมาแล้วรันคำสั่งนี้เพื่อติดตั้ง:\n\n```bash\npip install Flask\n```\n\nหลังจากติดตั้งเสร็จ ลองรันโค้ดดูอีกครั้งครับ หากคุณใช้ `requirements.txt` อย่าลืมรัน `pip install -r requirements.txt` ด้วยนะ";
    }
} 
elseif (strpos($lower_msg, 'อธิบาย') !== false && strpos($lower_msg, 'สรุป') !== false) {
    // 7. AI Lesson Summary & 15. AI Whiteboard
    $response_markdown = "### สรุปบทเรียน: " . ($context['lesson'] ?? 'REST API') . "\n\nเพื่อให้เห็นภาพรวมชัดเจน ลองดู Flowchart สถาปัตยกรรมนี้ครับ:\n\n```mermaid\ngraph LR\n    A[Client/Browser] -->|HTTP GET/POST| B(Flask Router)\n    B --> C{Controller}\n    C -->|Query| D[(Database)]\n    D -->|Data| C\n    C -->|JSON| B\n    B -->|Response| A\n```\n\n**Key Points:**\n- **Client** ส่ง Request (เช่น พิมพ์ URL)\n- **Flask Router** รับหน้าที่แยกแยะว่า URL ไหนต้องให้ฟังก์ชันไหนทำงาน\n- ข้อมูลจะถูกตอบกลับเป็นรูปแบบ **JSON**";
}
elseif (strpos($lower_msg, 'โจทย์') !== false \vert{}\vert{} strpos($lower_msg, 'แบบฝึกหัด') !== false) {
    // 8. Exercise Generator
    $response_markdown = "### 🎯 Mini Challenge: สร้าง API เบื้องต้น\n\n**ระดับ:** Easy (ปรับแต่งตามประวัติของคุณ)\n\n**โจทย์:**\nจงเขียนโค้ด Flask เพื่อสร้าง Endpoint `/api/hello` ที่เมื่อเรียกใช้งานผ่าน `GET` จะคืนค่า JSON ดังนี้:\n```json\n{\n  \"status\": \"success\",\n  \"message\": \"Hello, AI!\"\n}\n```\n\nลองเขียนโค้ดของคุณส่งมาให้ผมตรวจได้เลยครับ (ถ้าติดตรงไหน ถามขอคำใบ้ได้นะ)";
}
else {
    // Default Response
    $response_markdown = "รับทราบครับ คุณหมายถึงเรื่องที่เกี่ยวกับบทเรียน **" . ($context['lesson'] ?? 'ปัจจุบัน') . "** ใช่ไหมครับ?\n\nเพื่อคำตอบที่แม่นยำที่สุด คุณสามารถวางโค้ดที่สงสัย, ถ่ายภาพหน้าจอ Error, หรือขอให้ผมสรุปแนวคิดเฉพาะจุดให้ฟังได้เลยครับ";
}

echo json_encode([
    'status' => 'success',
    'reply' => $response_markdown,
    'mentor_mode_active' => $mentor_mode
]);
exit;