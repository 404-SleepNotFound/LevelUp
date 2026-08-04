/**
 * LevelUp Adaptive Quiz System
 */

const quizApp = (function() {
    
    // State Variables
    let questions = [];
    let currentIdx = 0;
    let answers = [];
    let totalTime = 0;
    let timerInterval = null;
    let currentConfidence = null;

    // Elements
    const quizContainer = document.getElementById('quizContainer');
    const resultContainer = document.getElementById('resultContainer');
    const questionArea = document.getElementById('questionArea');
    const actionArea = document.getElementById('actionArea');
    const btnNext = document.getElementById('btnNext');
    const progressBar = document.getElementById('quizProgressBar');
    const progressText = document.getElementById('progressText');
    const timerEl = document.getElementById('quizTimer');

    // ==========================================
    // 1. Initialization & Timer
    // ==========================================
    async function init() {
        bindEvents();
        await fetchQuestions();
    }

    function bindEvents() {
        btnNext.addEventListener('click', nextQuestion);
        
        // Confidence Buttons Selection
        document.querySelectorAll('.btn-confidence').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.btn-confidence').forEach(b => b.classList.remove('selected'));
                this.classList.add('selected');
                currentConfidence = this.getAttribute('data-level');
                btnNext.disabled = false; // Enable next button only after confidence is selected
            });
        });

        // Anti-Cheat (Visibility API)
        document.addEventListener('visibilitychange', function() {
            if(document.visibilityState === 'hidden' && questions.length > 0 && answers.length < questions.length) {
                console.warn("Anti-Cheat: ตรวจพบการสลับหน้าจอ");
                // ในระบบจริงอาจจะหักคะแนน หรือเด้ง Warning Modal
            }
        });
    }

    function startTimer(durationInSeconds) {
        let timeRemaining = durationInSeconds;
        totalTime = 0;
        
        timerInterval = setInterval(() => {
            timeRemaining--;
            totalTime++;
            
            const mins = String(Math.floor(timeRemaining / 60)).padStart(2, '0');
            const secs = String(timeRemaining % 60).padStart(2, '0');
            timerEl.innerText = `${mins}:${secs}`;

            if(timeRemaining <= 60) {
                timerEl.classList.remove('text-warning');
                timerEl.classList.add('text-danger', 'animate__animated', 'animate__pulse', 'animate__infinite');
            }

            if(timeRemaining <= 0) {
                clearInterval(timerInterval);
                submitQuiz(); // Auto submit
            }
        }, 1000);
    }

    // ==========================================
    // 2. Fetch & Render Questions
    // ==========================================
    async function fetchQuestions() {
        try {
            const res = await fetch('ajax_quiz.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_quiz' })
            });
            const data = await res.json();
            
            if(data.status === 'success') {
                questions = data.questions;
                startTimer(data.timeLimit);
                renderQuestion();
            }
        } catch (e) {
            questionArea.innerHTML = '<div class="alert alert-danger">Error loading quiz. Please try again.</div>';
        }
    }

    function renderQuestion() {
        const q = questions[currentIdx];
        
        // Update Progress
        const progressPercent = ((currentIdx) / questions.length) * 100;
        progressBar.style.width = `${progressPercent}%`;
        progressText.innerText = `Question ${currentIdx + 1} of ${questions.length}`;

        // Reset States
        currentConfidence = null;
        btnNext.disabled = true;
        document.querySelectorAll('.btn-confidence').forEach(b => b.classList.remove('selected'));
        actionArea.classList.add('d-none'); // Hide confidence until answer is selected
        actionArea.classList.remove('slide-up-anim');

        // Parse Question Text (Check for Markdown Code Blocks)
        let qTextHtml = q.question;
        if(qTextHtml.includes('```')) {
            const parts = qTextHtml.split('```');
            // Assuming format: text ```lang code ``` text
            qTextHtml = `${parts[0]} <div class="quiz-code-block">${parts[1].replace(/^python\n/, '')}</div> ${parts[2] || ''}`;
        }

        // Render Options
        let optionsHtml = '';
        q.options.forEach(opt => {
            optionsHtml += `<button class="quiz-option" data-id="${opt.id}">${opt.text}</button>`;
        });

        // Inject HTML (with slight fade animation)
        questionArea.style.opacity = '0';
        setTimeout(() => {
            questionArea.innerHTML = `
                <div class="mb-2"><span class="badge bg-secondary opacity-75">${q.category}</span></div>
                <h4 class="fw-bold mb-4">${qTextHtml}</h4>
                <div class="options-container d-flex flex-column gap-2" id="optionsContainer">
                    ${optionsHtml}
                </div>
            `;
            questionArea.style.opacity = '1';

            // Bind Options Click
            document.querySelectorAll('.quiz-option').forEach(opt => {
                opt.addEventListener('click', function() {
                    document.querySelectorAll('.quiz-option').forEach(o => o.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Show Confidence Area
                    actionArea.classList.remove('d-none');
                    void actionArea.offsetWidth; // trigger reflow
                    actionArea.classList.add('slide-up-anim');
                    
                    // Auto scroll to confidence area
                    setTimeout(() => actionArea.scrollIntoView({ behavior: 'smooth', block: 'end' }), 100);
                });
            });

            // Change button text if last question
            if(currentIdx === questions.length - 1) {
                btnNext.innerHTML = 'ส่งคำตอบ <i class="bi bi-check-circle"></i>';
                btnNext.classList.replace('btn-primary', 'btn-success');
            }
        }, 200);
    }

    function nextQuestion() {
        const selectedOption = document.querySelector('.quiz-option.active');
        if(!selectedOption || !currentConfidence) return;

        // Save Answer
        answers.push({
            questionId: questions[currentIdx].id,
            selectedId: selectedOption.getAttribute('data-id'),
            confidenceLevel: currentConfidence
        });

        if (currentIdx < questions.length - 1) {
            currentIdx++;
            renderQuestion();
        } else {
            submitQuiz();
        }
    }

    // ==========================================
    // 3. Submit & Show Results
    // ==========================================
    async function submitQuiz() {
        clearInterval(timerInterval);
        
        // Show Loading State on UI
        quizContainer.innerHTML = `
            <div class="d-flex flex-column justify-content-center align-items-center min-vh-100 text-main">
                <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
                <h4 class="fw-bold">AI กำลังวิเคราะห์ผลลัพธ์...</h4>
                <p class="text-muted">กำลังสร้าง Knowledge Gap Map และประเมินจุดแข็งของคุณ</p>
            </div>
        `;

        try {
            const res = await fetch('ajax_quiz.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'submit_quiz', answers: answers })
            });
            const data = await res.json();
            
            if(data.status === 'success') {
                renderResults(data);
            }
        } catch (error) {
            alert('เกิดข้อผิดพลาดในการส่งคำตอบ');
        }
    }

    function renderResults(data) {
        // Hide Quiz, Show Results
        quizContainer.classList.add('d-none');
        resultContainer.classList.remove('d-none');

        // Render Score Circle Animation
        const scorePath = document.getElementById('scorePath');
        const scoreText = document.getElementById('scoreText');
        
        setTimeout(() => {
            scorePath.setAttribute('stroke-dasharray', `${data.percentage}, 100`);
            scoreText.textContent = `${data.percentage}%`;
        }, 500); // delay for visual effect

        // Time used
        const mins = String(Math.floor(totalTime / 60)).padStart(2, '0');
        const secs = String(totalTime % 60).padStart(2, '0');
        document.getElementById('timeUsedText').innerText = `${mins}:${secs}`;

        // AI Feedback
        document.getElementById('aiFeedbackContent').innerHTML = data.aiFeedback;

        // Knowledge Gap Map
        const gapContainer = document.getElementById('gapMapContent');
        gapContainer.innerHTML = '<div class="fw-bold mb-2 text-main">Python Fundamentals</div>';
        
        data.gapMap.forEach((node) => {
            gapContainer.innerHTML += `
                <div class="gap-node">
                    <span class="${node.color} fw-bold">${node.category}</span>
                    <span class="gap-status">${node.status}</span>
                </div>
            `;
        });
    }

    return { init };
})();

// Initialize on Load
document.addEventListener('DOMContentLoaded', quizApp.init);