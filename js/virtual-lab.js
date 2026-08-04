/**
 * LevelUp Virtual Lab Module
 * จัดการ Environment, Terminal Interaction, AI Chat และ Task Progress
 */

const labApp = (function() {
    
    // Elements
    const termOutput = document.getElementById('terminalOutput');
    const termInput = document.getElementById('terminalInput');
    const termLoader = document.getElementById('terminalLoader');
    const taskContainer = document.getElementById('taskListContainer');
    const aiChatContainer = document.getElementById('aiChatContainer');
    const aiChatForm = document.getElementById('aiLabChatForm');
    const aiChatInput = document.getElementById('aiChatInput');
    
    // State
    let timerInterval;
    let secondsElapsed = 0;
    
    // ==========================================
    // 1. Initialization
    // ==========================================
    function init() {
        startTimer();
        loadTasks();
        bindEvents();
    }
    
    function bindEvents() {
        // Handle Terminal Input (Enter key)
        termInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const cmd = this.value;
                this.value = '';
                executeCommand(cmd);
            }
        });
        
        // Handle AI Chat Form
        aiChatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const msg = aiChatInput.value.trim();
            if(msg) {
                appendAiChat('user', msg);
                aiChatInput.value = '';
                // จำลอง AI คิด
                showAiTyping();
                setTimeout(() => {
                    removeAiTyping();
                    appendAiChat('ai', "นี่คือ Lab แบบจำลอง หากต้องการทำ Requirement กรุณาพิมพ์คำสั่งใน Terminal ด้านซ้ายครับ");
                }, 1500);
            }
        });
        
        // Focus Terminal on click
        document.querySelector('.terminal-section').addEventListener('click', () => termInput.focus());
    }

    // ==========================================
    // 2. Terminal & Command Execution
    // ==========================================
    async function executeCommand(cmd) {
        // Echo command to UI
        const cmdLine = document.createElement('div');
        cmdLine.innerHTML = `<span class="text-success fw-bold">root@levelup:~$</span> <span class="text-white">${cmd}</span>`;
        termOutput.appendChild(cmdLine);
        scrollToBottom(termOutput);
        
        termLoader.classList.remove('d-none'); // Show Loading
        
        try {
            const response = await fetch('ajax_virtual_lab.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'execute_command', command: cmd })
            });
            const data = await response.json();
            
            // Print Output
            if (data.output) {
                const outLine = document.createElement('div');
                outLine.className = 'text-light mb-2 whitespace-pre-wrap';
                // Convert \n to <br>
                outLine.innerHTML = data.output.replace(/\n/g, '<br>');
                termOutput.appendChild(outLine);
            }
            
            // Check if task completed
            if (data.taskCompleted) {
                markTaskComplete(data.taskCompleted);
            }
            
            // Auto AI Trigger (Proactive Assistance)
            if (data.ai_trigger) {
                setTimeout(() => {
                    appendAiChat('ai', data.ai_trigger);
                }, 500);
            }
            
        } catch (error) {
            console.error("Terminal Error:", error);
        } finally {
            termLoader.classList.add('d-none');
            scrollToBottom(termOutput);
        }
    }
    
    // ==========================================
    // 3. Task Management
    // ==========================================
    async function loadTasks() {
        try {
            const response = await fetch('ajax_virtual_lab.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_tasks' })
            });
            const data = await response.json();
            renderTasks(data.tasks);
        } catch (error) {
            console.error("Task Load Error:", error);
        }
    }
    
    function renderTasks(tasks) {
        taskContainer.innerHTML = '';
        let completed = 0;
        
        tasks.forEach(task => {
            if(task.done) completed++;
            const isDone = task.done ? 'completed' : '';
            const html = `
                <div class="task-item d-flex gap-3 bg-light-alt p-3 rounded-3 border ${isDone}" id="task-${task.id}">
                    <div class="checkbox-custom mt-1 flex-shrink-0"></div>
                    <div>
                        <h6 class="task-title mb-1 fw-bold fs-sm text-main">${task.title}</h6>
                        <code class="fs-xs bg-dark text-success px-2 py-1 rounded">${task.cmd}</code>
                    </div>
                </div>
            `;
            taskContainer.insertAdjacentHTML('beforeend', html);
        });
        
        updateProgress(completed, tasks.length);
    }
    
    function markTaskComplete(id) {
        const el = document.getElementById(`task-${id}`);
        if(el && !el.classList.contains('completed')) {
            el.classList.add('completed');
            
            // Play Sound (Optional)
            // new Audio('assets/sounds/success.mp3').play();
            
            // Recalculate Progress
            const total = document.querySelectorAll('.task-item').length;
            const completed = document.querySelectorAll('.task-item.completed').length;
            updateProgress(completed, total);
        }
    }
    
    function updateProgress(completed, total) {
        const percent = Math.round((completed / total) * 100);
        document.getElementById('labProgressBar').style.width = `${percent}%`;
        document.getElementById('labProgressText').innerText = `${percent}%`;
        document.getElementById('taskCount').innerText = `${completed}/${total}`;
        
        if(percent === 100) {
            setTimeout(() => {
                const modal = new bootstrap.Modal(document.getElementById('achievementModal'));
                modal.show();
            }, 1000);
        }
    }

    // ==========================================
    // 4. AI Assistant
    // ==========================================
    function appendAiChat(sender, htmlMessage) {
        let chatHtml = '';
        if (sender === 'ai') {
            chatHtml = `
            <div class="d-flex gap-2 ai-message jump-anim">
                <div class="ai-bubble bg-light-alt p-3 rounded-4 rounded-top-0 border fs-sm text-main shadow-sm w-100">
                    ${htmlMessage}
                </div>
            </div>`;
        } else {
            chatHtml = `
            <div class="d-flex justify-content-end mb-2">
                <div class="bg-primary text-white p-2 px-3 rounded-4 rounded-bottom-0 fs-sm shadow-sm max-w-75">
                    ${htmlMessage}
                </div>
            </div>`;
        }
        aiChatContainer.insertAdjacentHTML('beforeend', chatHtml);
        scrollToBottom(aiChatContainer);
    }
    
    function showAiTyping() {
        const typingHtml = `
            <div class="d-flex gap-2 ai-message typing-indicator" id="aiTyping">
                <div class="ai-bubble bg-light-alt p-3 rounded-4 rounded-top-0 border fs-sm text-muted">
                    <span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span>
                </div>
            </div>`;
        aiChatContainer.insertAdjacentHTML('beforeend', typingHtml);
        scrollToBottom(aiChatContainer);
    }
    
    function removeAiTyping() {
        const el = document.getElementById('aiTyping');
        if (el) el.remove();
    }
    
    async function analyzeError() {
        appendAiChat('user', 'ช่วยวิเคราะห์ Error ล่าสุดให้หน่อย');
        showAiTyping();
        
        try {
            const response = await fetch('ajax_virtual_lab.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'analyze_error' })
            });
            const data = await response.json();
            
            setTimeout(() => {
                removeAiTyping();
                appendAiChat('ai', data.message);
            }, 1000);
            
        } catch (e) {
            removeAiTyping();
            appendAiChat('ai', 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์ AI');
        }
    }

    // ==========================================
    // 5. Utilities
    // ==========================================
    function startTimer() {
        const timerEl = document.getElementById('labTimer');
        timerInterval = setInterval(() => {
            secondsElapsed++;
            const hrs = Math.floor(secondsElapsed / 3600);
            const mins = Math.floor((secondsElapsed % 3600) / 60);
            const secs = secondsElapsed % 60;
            timerEl.innerText = 
                String(hrs).padStart(2, '0') + ':' + 
                String(mins).padStart(2, '0') + ':' + 
                String(secs).padStart(2, '0');
        }, 1000);
    }
    
    function scrollToBottom(element) {
        element.scrollTop = element.scrollHeight;
    }
    
    function resetEnvironment() {
        if(confirm('คุณต้องการ Reset Lab ทั้งหมดใช่หรือไม่? ข้อมูลที่ทำมาจะหายไป')) {
            location.reload(); // ในระบบจริงจะส่ง AJAX ไปล้าง Docker Container ก่อน
        }
    }

    // Public API
    return {
        init,
        analyzeError,
        resetEnvironment,
        createSnapshot: () => alert('Snapshot Saved! ระบบได้บันทึกสถานะ Container ปัจจุบันไว้แล้ว'),
        submitLab: () => alert('ระบบกำลังรัน Automated Test สคริปต์เพื่อตรวจคำตอบ...'),
        setAiMode: (mode) => console.log('AI Mode changed to: ' + mode),
        replayAnalysis: () => alert('Replay Analysis (AI Mentor) - กำลังจำลองภาพย้อนหลังการกระทำของคุณ...')
    };
})();

// Start Application on Load
document.addEventListener('DOMContentLoaded', labApp.init);