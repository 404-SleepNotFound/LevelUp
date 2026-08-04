/**
 * LevelUp - Profile Page Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // Initialize profile data fetching
    fetchProfileData();

    // Event Listener for Edit Profile Form
    const saveProfileBtn = document.getElementById('saveProfileBtn');
    if (saveProfileBtn) {
        saveProfileBtn.addEventListener('click', async () => {
            const btn = saveProfileBtn;
            const form = document.getElementById('editProfileForm');
            
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';
            btn.disabled = true;

            const formData = {
                name: form.elements['name'] ? form.elements['name'].value : '',
                location: form.elements['location'] ? form.elements['location'].value : '',
                // ... gather other fields
            };

            try {
                const res = await fetch('ajax_profile.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'update_profile', data: formData })
                });
                const data = await res.json();
                
                if (data.status === 'success') {
                    // Update UI instantly
                    if (document.getElementById('userName')) {
                        document.getElementById('userName').innerText = data.newName || formData.name;
                    }
                    if (document.getElementById('userLocation')) {
                        document.getElementById('userLocation').innerText = formData.location;
                    }
                    
                    // Close modal
                    const modalEl = document.getElementById('editProfileModal');
                    if (modalEl) {
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                    }

                    // แสดง SweetAlert2 เมื่อบันทึกสำเร็จ (จะสลับธีมตาม Header อัตโนมัติ)
                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกข้อมูลสำเร็จ',
                        text: data.message || 'ปรับปรุงข้อมูลโปรไฟล์ของคุณเรียบร้อยแล้ว',
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#10b981'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: data.message || 'ไม่สามารถบันทึกข้อมูลได้',
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#ef4444'
                    });
                }
            } catch (error) {
                console.error("Update failed", error);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#ef4444'
                });
            } finally {
                btn.innerHTML = 'Save Changes';
                btn.disabled = false;
            }
        });
    }

    // --- Core Functions ---

    async function fetchProfileData() {
        try {
            const res = await fetch('ajax_profile.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_profile' })
            });
            const data = await res.json();
            
            if (data.status === 'success') {
                renderHero(data.user);
                renderAISummary(data.ai_summary);
                renderCareerReadiness(data.career_readiness);
                renderSkills(data.skills);
                renderStats(data.stats);
                renderPortfolio(data.portfolio);
                renderTimeline(data.timeline);
                renderBadges(data.badges);
            }
        } catch (error) {
            console.error("Failed to load profile data", error);
        }
    }

    function renderHero(user) {
        if (!user) return;
        if (document.getElementById('userName')) document.getElementById('userName').innerText = user.name || '';
        if (document.getElementById('userUsername')) document.getElementById('userUsername').innerText = user.username || '';
        if (document.getElementById('userLocation')) document.getElementById('userLocation').innerText = user.location || '';
        if (document.getElementById('userWebsite')) document.getElementById('userWebsite').innerText = user.website || '';
        if (document.getElementById('userJoinDate')) document.getElementById('userJoinDate').innerText = user.join_date || '';
        
        if (document.getElementById('statLevel')) document.getElementById('statLevel').innerText = user.level || '0';
        if (document.getElementById('statXP')) document.getElementById('statXP').innerText = user.xp || '0';
        if (document.getElementById('statStreak')) document.getElementById('statStreak').innerText = user.streak || '0';
        
        if (document.getElementById('githubUsername')) document.getElementById('githubUsername').innerText = `@${user.github || ''}`;
        
        // Update Chart URL dynamically based on username
        const ghChart = document.getElementById('githubChart');
        if (ghChart && user.github) {
            ghChart.src = `https://ghchart.rshah.org/8b5cf6/${user.github}`;
        }

        // Populate Edit Modal
        if (document.getElementById('editName')) document.getElementById('editName').value = user.name || '';
        if (document.getElementById('editLocation')) document.getElementById('editLocation').value = user.location || '';
    }

    function renderAISummary(summaryHtml) {
        const el = document.getElementById('aiSummaryText');
        if (el) el.innerHTML = summaryHtml || '';
    }

    function renderCareerReadiness(career) {
        if (!career) return;
        if (document.getElementById('careerRole')) document.getElementById('careerRole').innerText = career.role || '';
        if (document.getElementById('careerProgressText')) document.getElementById('careerProgressText').innerText = `${career.progress || 0}%`;
        
        const bar = document.getElementById('careerProgressBar');
        if (bar) {
            setTimeout(() => { bar.style.width = `${career.progress || 0}%`; }, 100);
        }

        if (career.missing && Array.isArray(career.missing)) {
            const missingHtml = career.missing.map(skill => 
                `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">${skill}</span>`
            ).join('');
            if (document.getElementById('missingSkills')) {
                document.getElementById('missingSkills').innerHTML = missingHtml;
            }
        }
    }

    function renderSkills(skills) {
        if (!skills || !Array.isArray(skills)) return;
        const html = skills.map(s => `
            <div>
                <div class="d-flex justify-content-between fs-sm mb-1 text-white">
                    <span>${s.name}</span>
                    <span class="text-${s.color}">${s.score}%</span>
                </div>
                <div class="progress bg-dark" style="height: 6px;">
                    <div class="progress-bar bg-${s.color}" style="width: ${s.score}%"></div>
                </div>
            </div>
        `).join('');
        if (document.getElementById('skillsList')) {
            document.getElementById('skillsList').innerHTML = html;
        }
    }

    function renderStats(stats) {
        if (!stats) return;
        if (document.getElementById('statCourses')) document.getElementById('statCourses').innerText = stats.courses || 0;
        if (document.getElementById('statLabs')) document.getElementById('statLabs').innerText = stats.labs || 0;
        if (document.getElementById('statTime')) document.getElementById('statTime').innerText = `${stats.time || 0} hrs`;
    }

    function renderPortfolio(projects) {
        if (!projects || !Array.isArray(projects)) return;
        const html = projects.map(p => `
            <div class="col-md-6">
                <div class="card card-dark portfolio-card rounded-4 border-secondary border-opacity-25 h-100">
                    <div class="portfolio-img-wrapper">
                        <img src="${p.image}" alt="${p.title}">
                    </div>
                    <div class="card-body p-3">
                        <h6 class="fw-bold text-white mb-1">${p.title}</h6>
                        <p class="text-muted fs-xs mb-3">${p.desc}</p>
                        <div class="d-flex flex-wrap gap-1">
                            ${p.tags.map(t => `<span class="badge bg-dark border border-secondary text-light fs-xs">${t}</span>`).join('')}
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
        if (document.getElementById('portfolioGrid')) {
            document.getElementById('portfolioGrid').innerHTML = html;
        }
    }

    function renderTimeline(events) {
        if (!events || !Array.isArray(events)) return;
        const html = events.map(e => `
            <div class="timeline-item">
                <div class="timeline-icon ${e.color} border-${e.color.split('-')[1]}"><i class="bi ${e.icon} fs-xs"></i></div>
                <div class="fs-xs text-muted mb-1">${e.date}</div>
                <div class="fs-sm text-white">${e.event}</div>
            </div>
        `).join('');
        if (document.getElementById('activityTimeline')) {
            document.getElementById('activityTimeline').innerHTML = html;
        }
    }

    function renderBadges(badges) {
        if (!badges || !Array.isArray(badges)) return;
        const html = badges.map(b => `
            <div class="badge-custom d-flex align-items-center gap-2" title="${b.name}">
                <span class="fs-5">${b.icon}</span> ${b.name}
            </div>
        `).join('');
        if (document.getElementById('badgesList')) {
            document.getElementById('badgesList').innerHTML = html;
        }
    }
});