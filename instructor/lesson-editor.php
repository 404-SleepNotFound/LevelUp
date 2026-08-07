<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Lesson & Content Block Builder</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts (Kanit) -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; }
        .font-mono-code { font-family: 'Fira Code', monospace; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col">

    <!-- Top Navigation Header -->
    <header class="bg-slate-900 border-b border-slate-800 sticky top-0 z-30 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-indigo-600 text-white p-2.5 rounded-xl shadow-lg shadow-indigo-500/30">
                    <i class="fa-solid fa-cubes text-xl"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white tracking-wide">Course Builder Pro</h1>
                    <p class="text-xs text-slate-400">ระบบสร้างเนื้อหาบทเรียนแบบ Dynamic Block</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 sm:gap-3">
                <button onclick="toggleViewMode()" id="viewModeBtn" class="px-3.5 py-2 rounded-lg text-sm font-medium bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-eye text-indigo-400"></i>
                    <span id="viewModeText">ดูมุมมองนักเรียน</span>
                </button>
                <button onclick="exportJSON()" class="px-3.5 py-2 rounded-lg text-sm font-medium bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-code text-emerald-400"></i>
                    <span>ส่งออก JSON</span>
                </button>
                <button onclick="saveCourse()" class="px-4 py-2 rounded-lg text-sm font-medium bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>บันทึกบทเรียน</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Left Sidebar: Controls & Add Block Toolbar -->
        <aside class="lg:col-span-1 space-y-6">
            <!-- Course Info Settings -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl">
                <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-gear text-indigo-400"></i> ตั้งค่าบทเรียน
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">ชื่อบทเรียน</label>
                        <input type="text" id="courseTitle" value="การเขียนโปรแกรมเชิงวัตถุด้วย JavaScript" 
                            class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">คำอธิบายย่อ</label>
                        <textarea id="courseDesc" rows="2" 
                            class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500 transition resize-none">ทำความเข้าใจแนวคิด OOP และการนำไปประยุกต์ใช้งานในโปรเจกต์จริง</textarea>
                    </div>
                </div>
            </div>

            <!-- Add Block Control Panel -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl sticky top-24">
                <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle text-indigo-400"></i> เพิ่ม Block เนื้อหา
                </h2>
                <div class="grid grid-cols-1 gap-2.5">
                    <button onclick="addBlock('text')" class="group flex items-center gap-3 p-3 rounded-xl bg-slate-800/60 hover:bg-slate-800 border border-slate-700/60 hover:border-blue-500/50 transition text-left">
                        <div class="w-9 h-9 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-align-left text-sm"></i>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-200">Text Block</div>
                            <div class="text-[11px] text-slate-400">ข้อความ, คำอธิบาย, เนื้อหาหลัก</div>
                        </div>
                    </button>

                    <button onclick="addBlock('video')" class="group flex items-center gap-3 p-3 rounded-xl bg-slate-800/60 hover:bg-slate-800 border border-slate-700/60 hover:border-red-500/50 transition text-left">
                        <div class="w-9 h-9 rounded-lg bg-red-500/10 text-red-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-video text-sm"></i>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-200">Video Block</div>
                            <div class="text-[11px] text-slate-400">ฝังวิดีโอ YouTube หรือ URL</div>
                        </div>
                    </button>

                    <button onclick="addBlock('code')" class="group flex items-center gap-3 p-3 rounded-xl bg-slate-800/60 hover:bg-slate-800 border border-slate-700/60 hover:border-emerald-500/50 transition text-left">
                        <div class="w-9 h-9 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-code text-sm"></i>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-200">Code Snippet</div>
                            <div class="text-[11px] text-slate-400">โค้ดตัวอย่างพร้อมเลือกภาษา</div>
                        </div>
                    </button>

                    <button onclick="addBlock('quiz')" class="group flex items-center gap-3 p-3 rounded-xl bg-slate-800/60 hover:bg-slate-800 border border-slate-700/60 hover:border-purple-500/50 transition text-left">
                        <div class="w-9 h-9 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-circle-question text-sm"></i>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-200">Quiz Block</div>
                            <div class="text-[11px] text-slate-400">คำถามปรนัยเพื่อทดสอบความเข้าใจ</div>
                        </div>
                    </button>

                    <button onclick="addBlock('lab')" class="group flex items-center gap-3 p-3 rounded-xl bg-slate-800/60 hover:bg-slate-800 border border-slate-700/60 hover:border-amber-500/50 transition text-left">
                        <div class="w-9 h-9 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-flask text-sm"></i>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-200">Interactive Lab</div>
                            <div class="text-[11px] text-slate-400">โจทย์ฝึกปฏิบัติการและภารกิจ</div>
                        </div>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Right Content Area: Canvas / Blocks List -->
        <section class="lg:col-span-3 space-y-6">
            
            <!-- EDIT MODE CANVAS -->
            <div id="editCanvas" class="space-y-4">
                <div id="blocksContainer" class="space-y-4">
                    <!-- Dynamic Blocks Will Be Rendered Here -->
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="hidden border-2 border-dashed border-slate-800 rounded-2xl p-12 text-center">
                    <div class="w-16 h-16 bg-slate-900 rounded-2xl text-slate-600 flex items-center justify-center mx-auto mb-4 border border-slate-800">
                        <i class="fa-solid fa-layer-group text-2xl"></i>
                    </div>
                    <h3 class="text-base font-medium text-slate-300">ยังไม่มี Block เนื้อหาในบทเรียนนี้</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">กดปุ่มในแถบ "เพิ่ม Block เนื้อหา" ด้านซ้ายมือเพื่อเริ่มต้นสร้างบทเรียนของคุณ</p>
                </div>
            </div>

            <!-- STUDENT PREVIEW MODE CANVAS -->
            <div id="previewCanvas" class="hidden bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 space-y-8 shadow-2xl">
                <div class="border-b border-slate-800 pb-6">
                    <span class="px-2.5 py-1 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 text-xs rounded-full font-medium mb-3 inline-block">
                        Student Preview Mode
                    </span>
                    <h1 id="previewTitle" class="text-2xl sm:text-3xl font-bold text-white mb-2"></h1>
                    <p id="previewDesc" class="text-slate-400 text-sm leading-relaxed"></p>
                </div>

                <!-- Rendered Student Content -->
                <div id="studentBlocksContent" class="space-y-8"></div>
            </div>

        </section>
    </main>

    <!-- JSON Export Modal -->
    <div id="jsonModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <h3 class="text-base font-semibold text-white flex items-center gap-2">
                    <i class="fa-solid fa-code text-emerald-400"></i> ข้อมูลบทเรียน JSON
                </h3>
                <button onclick="closeJSONModal()" class="text-slate-400 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <p class="text-xs text-slate-400">คุณสามารถคัดลอกโครงสร้างข้อมูล JSON นี้ไปจัดเก็บไว้ใน Database หรือ API ของคุณได้</p>
            <textarea id="jsonOutput" readonly class="w-full h-64 bg-slate-950 border border-slate-800 rounded-xl p-4 font-mono-code text-xs text-emerald-400 focus:outline-none resize-none"></textarea>
            <div class="flex justify-end gap-3 pt-2">
                <button onclick="copyJSON()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-sm font-medium transition flex items-center gap-2">
                    <i class="fa-regular fa-copy"></i> คัดลอก JSON
                </button>
                <button onclick="closeJSONModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">
                    ตกลง
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-6 right-6 bg-emerald-600 text-white px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 border border-emerald-500/30 transform translate-y-20 opacity-0 transition-all duration-300 z-50">
        <i class="fa-solid fa-circle-check text-lg"></i>
        <span id="toastMsg" class="text-sm font-medium">บันทึกข้อมูลเรียบร้อยแล้ว</span>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        // State Management
        let blocks = [
            {
                id: 'b-1',
                type: 'text',
                title: 'บทนำเกี่ยวกับ Object-Oriented Programming',
                content: 'การเขียนโปรแกรมเชิงวัตถุ (OOP) เป็นรูปแบบหนึ่งของการเขียนโปรแกรมที่มองทุกสิ่งเป็น "วัตถุ" (Object) โดยรวมเอาคุณลักษณะ (Attribute) และพฤติกรรม (Method) ไว้ด้วยกัน เพื่อให้โค้ดมีความเป็นระเบียบและนำกลับมาใช้ใหม่ได้ง่าย'
            },
            {
                id: 'b-2',
                type: 'code',
                title: 'ตัวอย่างการสร้าง Class ใน JavaScript',
                language: 'javascript',
                code: 'class Student {\n  constructor(name, major) {\n    this.name = name;\n    this.major = major;\n  }\n\n  introduce() {\n    console.log(`สวัสดีครับ ผมชื่อ ${this.name} เรียนสาขา ${this.major}`);\n  }\n}\n\nconst student1 = new Student("สมชาย", "วิทยาการคอมพิวเตอร์");\nstudent1.introduce();'
            },
            {
                id: 'b-3',
                type: 'quiz',
                question: 'คำสั่งใดใช้สำหรับการสร้าง Class ใน ES6 JavaScript?',
                options: ['function Class()', 'class Person {}', 'create.class()', 'new Class()'],
                correctIndex: 1,
                explanation: 'ใน ES6 เราใช้คีย์เวิร์ด `class` ในการประกาศ Class โครงสร้างพื้นฐาน'
            }
        ];

        let isPreviewMode = false;

        // Initialize App
        document.addEventListener('DOMContentLoaded', () => {
            renderBlocks();
        });

        // Add New Block
        function addBlock(type) {
            const id = 'b-' + Date.now();
            let newBlock = { id, type };

            switch(type) {
                case 'text':
                    newBlock.title = 'หัวข้อเนื้อหาใหม่';
                    newBlock.content = 'พิมพ์รายละเอียดเนื้อหาที่นี่...';
                    break;
                case 'video':
                    newBlock.title = 'วิดีโอประกอบการเรียนรู้';
                    newBlock.url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
                    break;
                case 'code':
                    newBlock.title = 'ตัวอย่าง โค้ดโปรแกรม';
                    newBlock.language = 'javascript';
                    newBlock.code = '// เขียนโค้ดตัวอย่างที่นี่\nconsole.log("Hello World");';
                    break;
                case 'quiz':
                    newBlock.question = 'พิมพ์คำถามการทดสอบความเข้าใจที่นี่?';
                    newBlock.options = ['ตัวเลือกที่ 1', 'ตัวเลือกที่ 2', 'ตัวเลือกที่ 3', 'ตัวเลือกที่ 4'];
                    newBlock.correctIndex = 0;
                    newBlock.explanation = 'อธิบายเหตุผลว่าทำไมข้อนี้ถึงถูกต้อง';
                    break;
                case 'lab':
                    newBlock.title = 'ภารกิจ Hands-on Lab';
                    newBlock.objective = 'เขียนโปรแกรมคำนวณเกรดเฉลี่ยสะสม (GPA)';
                    newBlock.timeMinutes = 20;
                    newBlock.instructions = '1. สร้าง ฟังก์ชัน calculateGPA()\n2. รับพารามิเตอร์เป็น Array ของคะแนน\n3. คืนค่าผลลัพธ์เป็นเกรดเฉลี่ยแบบ ทศนิยม 2 ตำแหน่ง';
                    break;
            }

            blocks.push(newBlock);
            renderBlocks();
            showToast('เพิ่ม Block เนื้อหาใหม่เรียบร้อยแล้ว');
            
            // Auto-scroll to bottom block
            setTimeout(() => {
                const el = document.getElementById(id);
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        }

        // Delete Block
        function deleteBlock(id) {
            if (confirm('คุณต้องการลบ Block เนื้อหานี้ใช่หรือไม่?')) {
                blocks = blocks.filter(b => b.id !== id);
                renderBlocks();
                showToast('ลบ Block เรียบร้อยแล้ว');
            }
        }

        // Move Block Up/Down
        function moveBlock(index, direction) {
            const targetIndex = index + direction;
            if (targetIndex < 0 || targetIndex >= blocks.length) return;
            
            const temp = blocks[index];
            blocks[index] = blocks[targetIndex];
            blocks[targetIndex] = temp;
            
            renderBlocks();
        }

        // Duplicate Block
        function duplicateBlock(index) {
            const original = blocks[index];
            const copy = JSON.parse(JSON.stringify(original));
            copy.id = 'b-' + Date.now();
            copy.title = (copy.title || 'Block') + ' (สำเนา)';
            
            blocks.splice(index + 1, 0, copy);
            renderBlocks();
            showToast('คัดลอก Block สำเร็จ');
        }

        // Update Field Value in State
        function updateBlockField(id, key, value) {
            const block = blocks.find(b => b.id === id);
            if (block) {
                block[key] = value;
            }
        }

        // Update Quiz Options
        function updateQuizOption(id, optionIndex, value) {
            const block = blocks.find(b => b.id === id);
            if (block && block.options) {
                block.options[optionIndex] = value;
            }
        }

        // Render All Blocks
        function renderBlocks() {
            const container = document.getElementById('blocksContainer');
            const emptyState = document.getElementById('emptyState');
            
            container.innerHTML = '';

            if (blocks.length === 0) {
                emptyState.classList.remove('hidden');
                return;
            } else {
                emptyState.classList.add('hidden');
            }

            blocks.forEach((block, index) => {
                const blockEl = document.createElement('div');
                blockEl.id = block.id;
                blockEl.className = 'bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl transition hover:border-slate-700/80 space-y-4';
                
                // Get Type Info (Icon & Color)
                const meta = getBlockMeta(block.type);

                blockEl.innerHTML = `
                    <!-- Block Header -->
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg ${meta.badgeBg} ${meta.badgeColor} flex items-center justify-center text-xs">
                                <i class="${meta.icon}"></i>
                            </span>
                            <span class="text-xs font-semibold text-slate-300 uppercase tracking-wider">${meta.label}</span>
                            <span class="text-[10px] bg-slate-800 text-slate-400 px-2 py-0.5 rounded-full font-mono">#${index + 1}</span>
                        </div>

                        <!-- Action Toolbar -->
                        <div class="flex items-center gap-1">
                            <button onclick="moveBlock(${index}, -1)" ${index === 0 ? 'disabled class="opacity-30 cursor-not-allowed text-slate-500 p-1.5"' : 'class="text-slate-400 hover:text-white p-1.5 rounded hover:bg-slate-800 transition"'} title="เลื่อนขึ้น">
                                <i class="fa-solid fa-arrow-up text-xs"></i>
                            </button>
                            <button onclick="moveBlock(${index}, 1)" ${index === blocks.length - 1 ? 'disabled class="opacity-30 cursor-not-allowed text-slate-500 p-1.5"' : 'class="text-slate-400 hover:text-white p-1.5 rounded hover:bg-slate-800 transition"'} title="เลื่อนลง">
                                <i class="fa-solid fa-arrow-down text-xs"></i>
                            </button>
                            <div class="w-px h-3 bg-slate-800 mx-1"></div>
                            <button onclick="duplicateBlock(${index})" class="text-slate-400 hover:text-indigo-400 p-1.5 rounded hover:bg-slate-800 transition" title="คัดลอก Block">
                                <i class="fa-regular fa-clone text-xs"></i>
                            </button>
                            <button onclick="deleteBlock('${block.id}')" class="text-slate-400 hover:text-red-400 p-1.5 rounded hover:bg-slate-800 transition" title="ลบ Block">
                                <i class="fa-regular fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Dynamic Body Content depending on Type -->
                    <div class="space-y-3">
                        ${renderBlockBodyForm(block)}
                    </div>
                `;

                container.appendChild(blockEl);
            });
        }

        // Return Block Badge Metainfo
        function getBlockMeta(type) {
            switch(type) {
                case 'text': return { label: 'Text Block', icon: 'fa-solid fa-align-left', badgeBg: 'bg-blue-500/10', badgeColor: 'text-blue-400' };
                case 'video': return { label: 'Video Block', icon: 'fa-solid fa-video', badgeBg: 'bg-red-500/10', badgeColor: 'text-red-400' };
                case 'code': return { label: 'Code Snippet', icon: 'fa-solid fa-code', badgeBg: 'bg-emerald-500/10', badgeColor: 'text-emerald-400' };
                case 'quiz': return { label: 'Quiz Block', icon: 'fa-solid fa-circle-question', badgeBg: 'bg-purple-500/10', badgeColor: 'text-purple-400' };
                case 'lab': return { label: 'Interactive Lab', icon: 'fa-solid fa-flask', badgeBg: 'bg-amber-500/10', badgeColor: 'text-amber-400' };
            }
        }

        // Render Form Controls based on Block Type
        function renderBlockBodyForm(block) {
            if (block.type === 'text') {
                return `
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">หัวข้อข้อความ</label>
                        <input type="text" value="${escapeHtml(block.title || '')}" oninput="updateBlockField('${block.id}', 'title', this.value)" 
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">เนื้อหาแบบละเอียด</label>
                        <textarea rows="3" oninput="updateBlockField('${block.id}', 'content', this.value)" 
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-slate-200 focus:border-indigo-500 focus:outline-none">${escapeHtml(block.content || '')}</textarea>
                    </div>
                `;
            } else if (block.type === 'video') {
                return `
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">ชื่อคลิปวิดีโอ</label>
                        <input type="text" value="${escapeHtml(block.title || '')}" oninput="updateBlockField('${block.id}', 'title', this.value)" 
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">URL วิดีโอ (YouTube / MP4)</label>
                        <input type="text" value="${escapeHtml(block.url || '')}" oninput="updateBlockField('${block.id}', 'url', this.value)" 
                            placeholder="https://www.youtube.com/watch?v=..."
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none">
                    </div>
                `;
            } else if (block.type === 'code') {
                return `
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-slate-400 mb-1">ชื่อคำอธิบายโค้ด</label>
                            <input type="text" value="${escapeHtml(block.title || '')}" oninput="updateBlockField('${block.id}', 'title', this.value)" 
                                class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">ภาษาโปรแกรม</label>
                            <select onchange="updateBlockField('${block.id}', 'language', this.value)" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-slate-300 focus:border-indigo-500 focus:outline-none">
                                <option value="javascript" ${block.language === 'javascript' ? 'selected' : ''}>JavaScript</option>
                                <option value="python" ${block.language === 'python' ? 'selected' : ''}>Python</option>
                                <option value="html" ${block.language === 'html' ? 'selected' : ''}>HTML/CSS</option>
                                <option value="sql" ${block.language === 'sql' ? 'selected' : ''}>SQL</option>
                                <option value="cpp" ${block.language === 'cpp' ? 'selected' : ''}>C++ / C#</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Source Code</label>
                        <textarea rows="5" oninput="updateBlockField('${block.id}', 'code', this.value)" 
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg p-3 font-mono-code text-xs text-emerald-400 focus:border-indigo-500 focus:outline-none leading-relaxed">${escapeHtml(block.code || '')}</textarea>
                    </div>
                `;
            } else if (block.type === 'quiz') {
                return `
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">คำถาม (Quiz Prompt)</label>
                        <input type="text" value="${escapeHtml(block.question || '')}" oninput="updateBlockField('${block.id}', 'question', this.value)" 
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-medium text-slate-400">ตัวเลือกคำตอบ (เลือก Radio หน้ารายการเพื่อระบุข้อที่ถูกต้อง)</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            ${(block.options || []).map((opt, optIdx) => `
                                <div class="flex items-center gap-2 bg-slate-950 border border-slate-800 rounded-lg px-3 py-1.5">
                                    <input type="radio" name="correct_${block.id}" ${block.correctIndex === optIdx ? 'checked' : ''} 
                                        onchange="updateBlockField('${block.id}', 'correctIndex', ${optIdx})" class="accent-indigo-500">
                                    <input type="text" value="${escapeHtml(opt)}" oninput="updateQuizOption('${block.id}', ${optIdx}, this.value)" 
                                        class="w-full bg-transparent text-xs text-slate-200 focus:outline-none">
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">คำอธิบายเฉลยคำตอบ</label>
                        <input type="text" value="${escapeHtml(block.explanation || '')}" oninput="updateBlockField('${block.id}', 'explanation', this.value)" 
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-300 focus:border-indigo-500 focus:outline-none">
                    </div>
                `;
            } else if (block.type === 'lab') {
                return `
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-slate-400 mb-1">ชื่อกิจกรรม Lab</label>
                            <input type="text" value="${escapeHtml(block.title || '')}" oninput="updateBlockField('${block.id}', 'title', this.value)" 
                                class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">เวลาที่กำหนด (นาที)</label>
                            <input type="number" value="${block.timeMinutes || 15}" oninput="updateBlockField('${block.id}', 'timeMinutes', parseInt(this.value))" 
                                class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-amber-400 font-semibold focus:border-indigo-500 focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">เป้าหมายของ Lab (Objective)</label>
                        <input type="text" value="${escapeHtml(block.objective || '')}" oninput="updateBlockField('${block.id}', 'objective', this.value)" 
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-300 focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">ขั้นตอน/คำสั่งการทำ Lab</label>
                        <textarea rows="3" oninput="updateBlockField('${block.id}', 'instructions', this.value)" 
                            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-300 focus:border-indigo-500 focus:outline-none leading-relaxed">${escapeHtml(block.instructions || '')}</textarea>
                    </div>
                `;
            }
        }

        // Toggle Student Preview Mode
        function toggleViewMode() {
            isPreviewMode = !isPreviewMode;
            const editCanvas = document.getElementById('editCanvas');
            const previewCanvas = document.getElementById('previewCanvas');
            const btnText = document.getElementById('viewModeText');

            if (isPreviewMode) {
                editCanvas.classList.add('hidden');
                previewCanvas.classList.remove('hidden');
                btnText.textContent = 'กลับสู่มุมมองแก้ไข';
                renderStudentPreview();
            } else {
                previewCanvas.classList.add('hidden');
                editCanvas.classList.remove('hidden');
                btnText.textContent = 'ดูมุมมองนักเรียน';
            }
        }

        // Render Student View
        function renderStudentPreview() {
            const courseTitle = document.getElementById('courseTitle').value;
            const courseDesc = document.getElementById('courseDesc').value;
            
            document.getElementById('previewTitle').textContent = courseTitle || 'ไม่มีชื่อบทเรียน';
            document.getElementById('previewDesc').textContent = courseDesc || '';

            const container = document.getElementById('studentBlocksContent');
            container.innerHTML = '';

            blocks.forEach((block, idx) => {
                const div = document.createElement('div');
                div.className = 'space-y-3';

                if (block.type === 'text') {
                    div.innerHTML = `
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span> ${escapeHtml(block.title)}
                        </h3>
                        <p class="text-slate-300 text-sm leading-relaxed whitespace-pre-line">${escapeHtml(block.content)}</p>
                    `;
                } else if (block.type === 'video') {
                    div.innerHTML = `
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <i class="fa-solid fa-video text-red-400 text-base"></i> ${escapeHtml(block.title)}
                        </h3>
                        <div class="aspect-video bg-slate-950 rounded-xl border border-slate-800 flex items-center justify-center text-slate-500 text-sm overflow-hidden">
                            <div class="text-center p-4">
                                <i class="fa-brands fa-youtube text-4xl text-red-500 mb-2 block"></i>
                                <p class="text-xs text-slate-400">Video Player Framework (${escapeHtml(block.url || 'No URL')})</p>
                            </div>
                        </div>
                    `;
                } else if (block.type === 'code') {
                    div.innerHTML = `
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-slate-300 flex items-center gap-2">
                                <i class="fa-solid fa-code text-emerald-400"></i> ${escapeHtml(block.title)}
                            </h3>
                            <span class="text-[10px] bg-slate-800 text-emerald-400 px-2 py-0.5 rounded font-mono uppercase">${block.language}</span>
                        </div>
                        <pre class="bg-slate-950 border border-slate-800 rounded-xl p-4 font-mono-code text-xs text-emerald-400 overflow-x-auto leading-relaxed">${escapeHtml(block.code)}</pre>
                    `;
                } else if (block.type === 'quiz') {
                    div.innerHTML = `
                        <div class="bg-slate-950/60 border border-purple-500/30 rounded-2xl p-5 space-y-4">
                            <div class="flex items-center gap-2 text-purple-400 text-xs font-semibold">
                                <i class="fa-solid fa-circle-question"></i> แบบทดสอบความรู้
                            </div>
                            <h4 class="text-base font-medium text-white">${escapeHtml(block.question)}</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                ${(block.options || []).map((opt, i) => `
                                    <button onclick="checkAnswer(this, ${i === block.correctIndex}, '${escapeHtml(block.explanation)}')" 
                                        class="quiz-opt-btn p-3 rounded-xl border border-slate-800 bg-slate-900 hover:bg-slate-800 text-slate-300 text-xs text-left transition flex items-center gap-2">
                                        <span class="w-5 h-5 rounded-full bg-slate-800 flex items-center justify-center text-[10px] text-slate-400 font-mono">${String.fromCharCode(65+i)}</span>
                                        ${escapeHtml(opt)}
                                    </button>
                                `).join('')}
                            </div>
                            <div class="quiz-feedback hidden p-3 rounded-xl text-xs"></div>
                        </div>
                    `;
                } else if (block.type === 'lab') {
                    div.innerHTML = `
                        <div class="bg-gradient-to-br from-slate-900 to-amber-950/20 border border-amber-500/30 rounded-2xl p-5 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-amber-400 flex items-center gap-2">
                                    <i class="fa-solid fa-flask"></i> Interactive Lab Task
                                </span>
                                <span class="text-xs bg-amber-500/10 text-amber-400 border border-amber-500/20 px-2.5 py-0.5 rounded-full">
                                    <i class="fa-regular fa-clock mr-1"></i> ${block.timeMinutes} นาที
                                </span>
                            </div>
                            <h4 class="text-base font-bold text-white">${escapeHtml(block.title)}</h4>
                            <p class="text-xs text-slate-300"><strong class="text-amber-400">เป้าหมาย:</strong> ${escapeHtml(block.objective)}</p>
                            <div class="bg-slate-950/80 rounded-xl p-3 border border-slate-800/80">
                                <div class="text-[11px] font-semibold text-slate-400 mb-1">ขั้นตอนปฏิบัติ:</div>
                                <div class="text-xs text-slate-300 whitespace-pre-line leading-relaxed font-mono">${escapeHtml(block.instructions)}</div>
                            </div>
                        </div>
                    `;
                }

                container.appendChild(div);
            });
        }

        // Quiz Click Handler in Preview Mode
        function checkAnswer(btn, isCorrect, explanation) {
            const parent = btn.closest('.space-y-4');
            const allBtns = parent.querySelectorAll('.quiz-opt-btn');
            const feedback = parent.querySelector('.quiz-feedback');

            allBtns.forEach(b => {
                b.classList.remove('border-emerald-500', 'bg-emerald-500/10', 'border-red-500', 'bg-red-500/10');
            });

            feedback.classList.remove('hidden', 'bg-emerald-500/10', 'text-emerald-400', 'border-emerald-500/30', 'bg-red-500/10', 'text-red-400', 'border-red-500/30');

            if (isCorrect) {
                btn.classList.add('border-emerald-500', 'bg-emerald-500/10');
                feedback.classList.add('bg-emerald-500/10', 'text-emerald-400', 'border', 'border-emerald-500/30');
                feedback.innerHTML = `<i class="fa-solid fa-circle-check mr-1.5"></i> <strong>ถูกต้อง!</strong> ${explanation}`;
            } else {
                btn.classList.add('border-red-500', 'bg-red-500/10');
                feedback.classList.add('bg-red-500/10', 'text-red-400', 'border', 'border-red-500/30');
                feedback.innerHTML = `<i class="fa-solid fa-circle-xmark mr-1.5"></i> <strong>ยังไม่ถูกต้อง!</strong> ลองใหม่อีกครั้งนะครับ`;
            }
        }

        // Export JSON Modal
        function exportJSON() {
            const courseData = {
                title: document.getElementById('courseTitle').value,
                description: document.getElementById('courseDesc').value,
                updatedAt: new Date().toISOString(),
                blocks: blocks
            };

            document.getElementById('jsonOutput').value = JSON.stringify(courseData, null, 2);
            document.getElementById('jsonModal').classList.remove('hidden');
        }

        function closeJSONModal() {
            document.getElementById('jsonModal').classList.add('hidden');
        }

        function copyJSON() {
            const textarea = document.getElementById('jsonOutput');
            textarea.select();
            document.execCommand('copy');
            showToast('คัดลอกโครงสร้าง JSON เรียบร้อยแล้ว');
        }

        function saveCourse() {
            showToast('บันทึกบทเรียนสำเร็จเรียบร้อยแล้ว!');
        }

        // Helper: Toast Message
        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMsg').textContent = msg;
            toast.classList.remove('translate-y-20', 'opacity-0');
            
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 2500);
        }

        // Helper: Escape HTML to prevent XSS
        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/&/g, "&amp;")
                      .replace(/</g, "&lt;")
                      .replace(/>/g, "&gt;")
                      .replace(/"/g, "&quot;")
                      .replace(/'/g, "&#039;");
        }
    </script>
</body>
</html>