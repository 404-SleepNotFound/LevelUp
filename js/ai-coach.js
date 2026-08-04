/**
 * LevelUp - AI Coach Dashboard Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // Elements
    const slider = document.getElementById('whatIfSlider');
    const hoursDisplay = document.getElementById('whatIfHoursDisplay');
    const resultDisplay = document.getElementById('whatIfResult');
    
    // Initialize Dashboard
    fetchDashboardData();

    // What-If Planner Event Listener
    slider.addEventListener('input', (e) => {
        const val = e.target.value;
        hoursDisplay.innerText = `${val} ชั่วโมง`;
    });

    slider.addEventListener('change', (e) => {
        const val = e.target.value;
        calculateWhatIf(val);
    });

    // --- Core Functions ---

    async function fetchDashboardData() {
        try {
            const res = await fetch('ajax_ai_coach.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_dashboard' })
            });
            const data = await res.json();
            if (data.status === 'success') {
                renderDashboard(data);
            }
        } catch (error) {
            console.error("Failed to load AI Coach data", error);
        }
    }

    function renderDashboard(data) {
        // User Stats & Burnout
        document.getElementById('motivationMsg').innerText = `"${data.user.motivation}"`;
        document.getElementById('dailyTime').innerText = data.user.dailyTime;
        document.getElementById('dailyXp').innerText = `+${data.user.dailyXp} XP`;
        
        if (data.user.burnoutDetected) {
            document.getElementById('burnoutAlert').classList.remove('d-none');
            document.getElementById('burnoutAlert').classList.add('d-flex');
        }

        // Circular Progress (Career Match)
        setTimeout(() => {
            document.getElementById('careerMatchRing').setAttribute('stroke-dasharray', `${data.user.careerMatch}, 100`);
            document.getElementById('careerMatchTxt').innerText = `${data.user.careerMatch}%`;
        }, 500); // Small delay for animation effect

        // Daily Plan
        const planHtml = data.dailyPlan.map(item => `
            <div class="todo-item ${item.completed ? 'done' : ''}">
                <div class="todo-checkbox"></div>
                <div>
                    <div class="title fw-bold text-white fs-sm">${item.title}</div>
                    ${item.note ? `<div class="fs-xs text-primary mt-1"><i class="bi bi-info-circle"></i> ${item.note}</div>` : ''}
                </div>
                <div class="ms-auto text-muted fs-xs">${item.type.toUpperCase()}</div>
            </div>
        `).join('');
        document.getElementById('dailyPlanList').innerHTML = planHtml;

        // Knowledge Assessment
        const knowledgeHtml = data.knowledge.map(k => {
            const color = k.score > 70 ? 'success' : (k.score > 40 ? 'primary' : 'warning');
            return `
            <div class="mb-3">
                <div class="d-flex justify-content-between fs-sm mb-1">
                    <span class="text-white">${k.topic} <span class="text-muted">(${k.level})</span></span>
                    <span class="fw-bold text-${color}">${k.score}%</span>
                </div>
                <div class="progress bg-dark" style="height: 6px;">
                    <div class="progress-bar bg-${color}" style="width: ${k.score}%"></div>
                </div>
            </div>
        `}).join('');
        document.getElementById('knowledgeList').innerHTML = knowledgeHtml;

        // Strengths & Weaknesses
        document.getElementById('strengthList').innerHTML = data.strengths.map(s => 
            `<div class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 py-2">${s}</div>`
        ).join('');
        
        document.getElementById('weaknessList').innerHTML = data.weaknesses.map(w => 
            `<div class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 py-2">${w}</div>`
        ).join('');

        // Spaced Repetition (Reviews)
        document.getElementById('reviewList').innerHTML = data.reviews.map(r => `
            <div class="d-flex justify-content-between align-items-center bg-dark p-2 rounded-3 border border-secondary border-opacity-50">
                <span class="fs-sm text-white">${r.topic}</span>
                <span class="fs-xs text-warning">${r.last_seen}</span>
            </div>
        `).join('');

        // Timeline
        document.getElementById('learningTimeline').innerHTML = data.timeline.map(t => `
            <div class="timeline-item">
                <div class="fs-xs text-primary fw-bold mb-1">${t.month}</div>
                <div class="fs-sm text-white">${t.event}</div>
            </div>
        `).join('');
    }

    async function calculateWhatIf(hours) {
        // Show loading state briefly
        resultDisplay.innerText = "Calculating...";
        resultDisplay.classList.add('opacity-50');

        try {
            const res = await fetch('ajax_ai_coach.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'calculate_whatif', hours: hours })
            });
            const data = await res.json();
            
            if (data.status === 'success') {
                resultDisplay.innerText = data.prediction;
                resultDisplay.classList.remove('opacity-50');
            }
        } catch (error) {
            console.error("Simulation failed", error);
        }
    }
});