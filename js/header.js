/* =========================================
   LevelUp Frontend - header.js
   Handles: Global Search Autocomplete (with Ctrl+K shortcut),
   dynamic panel positioning, and AJAX Notifications lazy loading.
   ========================================= */
'use strict';

document.addEventListener('DOMContentLoaded', () => {
    initGlobalSearchHeader();
    initNotificationsHeader();
});

/* =========================================
   1. Global Search & Autocomplete
   ========================================= */
function initGlobalSearchHeader() {
    const searchInput = document.getElementById('globalSearchInput');
    const autocompletePanel = document.getElementById('searchAutocompletePanel');
    const searchContent = document.getElementById('searchAutocompleteContent');

    // Guard Clause: ถ้าหน้านี้ไม่มีองค์ประกอบ Search ให้ข้ามการทำงาน ป้องกัน Script ค้าง/พัง
    if (!searchInput || !autocompletePanel || !searchContent) return;

    let searchTimeout;

    // คํานวณตำแหน่งของ Autocomplete Panel ให้พอดีกับหน้าจอทั้ง Desktop และ Mobile
    const positionPanel = () => {
        const rect = searchInput.getBoundingClientRect();
        autocompletePanel.style.top = `${rect.bottom + 10}px`;

        if (window.innerWidth < 992) {
            autocompletePanel.style.left = '50%';
            autocompletePanel.style.transform = 'translateX(-50%)';
            autocompletePanel.style.width = '95vw';
        } else {
            autocompletePanel.style.left = `${rect.left}px`;
            autocompletePanel.style.transform = 'none';
        }
    };

    // คีย์ลัด Ctrl + K หรือ Cmd + K เพื่อ Focus ช่องค้นหา
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            searchInput.focus();
        }
    });

    // แสดง Panel เมื่อกด Focus
    searchInput.addEventListener('focus', () => {
        positionPanel();
        if (searchInput.value.trim().length > 0) {
            autocompletePanel.classList.remove('d-none');
        }
    });

    // ค้นหาและจำลอง AJAX Response
    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.trim();

        if (query.length === 0) {
            autocompletePanel.classList.add('d-none');
            return;
        }

        autocompletePanel.classList.remove('d-none');
        positionPanel();

        // แสดง Loading Spinner
        searchContent.innerHTML = `
            <div class="d-flex justify-content-center p-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            </div>`;

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const safeQuery = escapeHtml(query); // ป้องกัน XSS Injection

            // Mockup ผลลัพธ์จากการค้นหา
            const mockHTML = `
                <a href="#" class="d-flex align-items-center gap-3 mb-2 text-decoration-none lu-dropdown-item p-2 rounded">
                    <div class="bg-primary bg-opacity-10 text-primary rounded p-2"><i class="bi bi-play-circle"></i></div>
                    <div>
                        <div class="text-white fw-medium fs-6">Fullstack ${safeQuery}</div>
                        <div class="text-secondary" style="font-size: 0.75rem;">Course • 15 Lessons</div>
                    </div>
                </a>
                <a href="#" class="d-flex align-items-center gap-3 text-decoration-none lu-dropdown-item p-2 rounded">
                    <div class="bg-warning bg-opacity-10 text-warning rounded p-2"><i class="bi bi-briefcase"></i></div>
                    <div>
                        <div class="text-white fw-medium fs-6">Senior ${safeQuery} Developer</div>
                        <div class="text-secondary" style="font-size: 0.75rem;">Career Path</div>
                    </div>
                </a>
            `;
            searchContent.innerHTML = mockHTML;
        }, 400); // Debounce 400ms
    });

    // ซ่อน Panel เมื่อคลิกพื้นที่อื่นภายนอก
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !autocompletePanel.contains(e.target)) {
            autocompletePanel.classList.add('d-none');
        }
    });

    // คำนวณตำแหน่งใหม่เมื่อปรับขนาดหน้าจอ
    window.addEventListener('resize', () => {
        if (!autocompletePanel.classList.contains('d-none')) {
            positionPanel();
        }
    });
}

/* =========================================
   2. Notifications Dropdown (Lazy Load)
   ========================================= */
function initNotificationsHeader() {
    const notifButton = document.getElementById('notifButton');
    const notifList = document.getElementById('notifList');
    let notifLoaded = false;

    // Guard Clause
    if (!notifButton || !notifList) return;

    notifButton.addEventListener('show.bs.dropdown', () => {
        if (!notifLoaded) {
            // จำลองการดึงข้อมูลการแจ้งเตือนจาก AJAX
            setTimeout(() => {
                notifList.innerHTML = `
                    <a href="#" class="list-group-item list-group-item-action bg-transparent border-bottom border-dark p-3">
                        <div class="d-flex gap-3">
                            <div class="text-primary mt-1"><i class="bi bi-robot fs-5"></i></div>
                            <div>
                                <div class="text-white fs-7 mb-1">AI Tutor reviewed your Python code.</div>
                                <div class="text-secondary" style="font-size: 0.7rem;">10 mins ago</div>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action bg-transparent p-3 border-0">
                        <div class="d-flex gap-3">
                            <div class="text-warning mt-1"><i class="bi bi-fire fs-5"></i></div>
                            <div>
                                <div class="text-white fs-7 mb-1">You reached a 15-day streak!</div>
                                <div class="text-secondary" style="font-size: 0.7rem;">2 hours ago</div>
                            </div>
                        </div>
                    </a>
                `;
                notifLoaded = true;
            }, 500);
        }
    });
}

/**
 * Helper: Escapes HTML characters to prevent XSS Attacks
 */
function escapeHtml(str) {
    return str.replace(/[&<>"']/g, function(m) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[m];
    });
}