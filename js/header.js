/* =========================================
   LevelUp Frontend - header.js (Fixed & Optimized 100%)
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

    if (!searchInput || !autocompletePanel || !searchContent) return;

    let searchTimeout;

    // คำนวณตำแหน่ง Autocomplete Panel ให้แม่นยำ
    const positionPanel = () => {
        const rect = searchInput.getBoundingClientRect();
        autocompletePanel.style.top = `${rect.bottom + window.scrollY + 8}px`;

        if (window.innerWidth < 992) {
            autocompletePanel.style.left = '5%';
            autocompletePanel.style.width = '90vw';
            autocompletePanel.style.transform = 'none';
        } else {
            autocompletePanel.style.left = `${rect.left + window.scrollX}px`;
            autocompletePanel.style.width = `${Math.max(rect.width, 320)}px`;
            autocompletePanel.style.transform = 'none';
        }
    };

    // คีย์ลัด Ctrl + K / Cmd + K เพื่อ Focus ช่องค้นหา & Esc เพื่อปิด
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        } else if (e.key === 'Escape') {
            autocompletePanel.classList.add('d-none');
        }
    });

    // แสดง Panel เมื่อกด Focus
    searchInput.addEventListener('focus', () => {
        if (searchInput.value.trim().length > 0) {
            positionPanel();
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

        positionPanel();
        autocompletePanel.classList.remove('d-none');

        // แสดง Loading Spinner
        searchContent.innerHTML = `
            <div class="d-flex justify-content-center align-items-center p-3">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                <span class="fs-sm text-muted">กำลังค้นหา...</span>
            </div>`;

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const safeQuery = escapeHtml(query);

            const mockHTML = `
                <a href="#" class="d-flex align-items-center gap-3 mb-1 text-decoration-none lu-dropdown-item p-2 rounded">
                    <div class="bg-primary bg-opacity-10 text-primary rounded p-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                        <i class="bi bi-play-circle fs-5"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-main fw-medium fs-sm text-truncate">Fullstack ${safeQuery}</div>
                        <div class="text-muted fs-xs">Course • 15 Lessons</div>
                    </div>
                </a>
                <a href="#" class="d-flex align-items-center gap-3 text-decoration-none lu-dropdown-item p-2 rounded">
                    <div class="bg-warning bg-opacity-10 text-warning rounded p-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                        <i class="bi bi-briefcase fs-5"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-main fw-medium fs-sm text-truncate">Senior ${safeQuery} Developer</div>
                        <div class="text-muted fs-xs">Career Path</div>
                    </div>
                </a>
            `;
            searchContent.innerHTML = mockHTML;
        }, 300);
    });

    // ซ่อน Panel เมื่อคลิกภายนอก
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !autocompletePanel.contains(e.target)) {
            autocompletePanel.classList.add('d-none');
        }
    });

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

    if (!notifButton || !notifList) return;

    notifButton.addEventListener('show.bs.dropdown', () => {
        if (!notifLoaded) {
            setTimeout(() => {
                notifList.innerHTML = `
                    <a href="#" class="list-group-item list-group-item-action bg-transparent border-bottom border-color p-3">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="text-primary mt-1 flex-shrink-0"><i class="bi bi-robot fs-5"></i></div>
                            <div>
                                <div class="text-main fs-sm mb-1">AI Tutor reviewed your Python code.</div>
                                <div class="text-muted fs-xs">10 mins ago</div>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action bg-transparent p-3 border-0">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="text-warning mt-1 flex-shrink-0"><i class="bi bi-fire fs-5"></i></div>
                            <div>
                                <div class="text-main fs-sm mb-1">You reached a 15-day streak!</div>
                                <div class="text-muted fs-xs">2 hours ago</div>
                            </div>
                        </div>
                    </a>
                `;
                notifLoaded = true;
            }, 300);
        }
    });
}

/**
 * Helper: ป้องกัน XSS Injection
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