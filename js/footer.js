/**
 * LevelUp - Footer Interactions
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // ==========================================
    // Force Hide App Store & Google Play UI
    // ==========================================
    const hideAppButtons = () => {
        // 1. ค้นหา element ทั้งหมดที่มีข้อความ App Store หรือ Google Play
        const allElements = document.querySelectorAll('a, button, div, span, img');
        allElements.forEach(el => {
            const text = (el.innerText || el.textContent || '').trim();
            const alt = el.getAttribute('alt') || '';
            const href = el.getAttribute('href') || '';

            if (
                text.includes('App Store') || 
                text.includes('Google Play') || 
                alt.includes('App Store') || 
                alt.includes('Google Play') ||
                href.includes('apple.com') ||
                href.includes('play.google.com')
            ) {
                // ค้นหาปุ่มหลัก (แท็ก <a> หรือ <button>) แล้วสั่งซ่อนทันที
                const target = el.closest('a') || el.closest('button') || el;
                target.style.setProperty('display', 'none', 'important');
                target.style.setProperty('visibility', 'hidden', 'important');
            }
        });
    };

    // รันคำสั่งซ่อนปุ่มทันที
    hideAppButtons();
    // รันซ้ำอีกครั้งหลังจากโหลดผ่านไป 0.5 - 1 วินาที (ป้องกันกรณีเว็บโหลดช้า)
    setTimeout(hideAppButtons, 500);
    setTimeout(hideAppButtons, 1000);

    // ==========================================
    // 1. AI Tips Randomizer
    // ==========================================
    const aiTips = [
        "Doing labs immediately after lessons increases retention by 40%.",
        "Stuck on a bug? Ask the AI Tutor to explain the error, not just give the code.",
        "Consistency is key! A 15-minute daily streak is better than a 3-hour binge on Sunday.",
        "Use the 'Spaced Repetition' quizzes in your dashboard to memorize syntax faster.",
        "Write pseudocode before you type real code. It helps structure your logic."
    ];
    
    const tipElement = document.getElementById('dailyAiTip');
    if(tipElement) {
        // Pick random tip
        const randomTip = aiTips[Math.floor(Math.random() * aiTips.length)];
        tipElement.innerText = randomTip;
    }

    // ==========================================
    // 2. Back to Top Logic
    // ==========================================
    const backToTopBtn = document.getElementById('backToTopBtn');
    if(backToTopBtn) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 400) {
                backToTopBtn.classList.add('show');
                backToTopBtn.classList.remove('d-none');
            } else {
                backToTopBtn.classList.remove('show');
                setTimeout(() => {
                    if(window.scrollY <= 400) backToTopBtn.classList.add('d-none');
                }, 300);
            }
        });

        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // ==========================================
    // 3. Newsletter AJAX Submit
    // ==========================================
    const newsletterForm = document.getElementById('newsletterForm');
    if(newsletterForm) {
        newsletterForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnNewsletter');
            const emailInput = newsletterForm.querySelector('input[name="email"]');
            
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            btn.disabled = true;

            try {
                const formData = new FormData(newsletterForm);
                formData.append('action', 'subscribe_newsletter');

                const res = await fetch('api/ajax_footer.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await res.json();
                
                if(data.status === 'success') {
                    btn.innerHTML = '<i class="bi bi-check2"></i> Done';
                    btn.classList.replace('btn-primary', 'btn-success');
                    emailInput.value = '';
                } else {
                    btn.innerHTML = 'Error';
                    btn.classList.replace('btn-primary', 'btn-danger');
                }
            } catch (error) {
                btn.innerHTML = 'Failed';
            } finally {
                setTimeout(() => {
                    btn.innerHTML = 'Subscribe';
                    btn.disabled = false;
                    btn.classList.remove('btn-success', 'btn-danger');
                    btn.classList.add('btn-primary');
                }, 3000);
            }
        });
    }

    // ==========================================
    // 4. Feedback Modal AJAX Submit
    // ==========================================
    const feedbackForm = document.getElementById('feedbackForm');
    if(feedbackForm) {
        feedbackForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitFeedback');
            
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Sending...';
            btn.disabled = true;

            try {
                const formData = new FormData(feedbackForm);
                formData.append('action', 'submit_feedback');

                const res = await fetch('api/ajax_footer.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await res.json();
                
                if(data.status === 'success') {
                    btn.innerHTML = 'Sent!';
                    btn.classList.replace('btn-primary', 'btn-success');
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('feedbackModal')).hide();
                        feedbackForm.reset();
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                        btn.classList.replace('btn-success', 'btn-primary');
                    }, 1500);
                }
            } catch (error) {
                btn.innerHTML = 'Error. Try Again.';
                btn.disabled = false;
            }
        });
    }
});