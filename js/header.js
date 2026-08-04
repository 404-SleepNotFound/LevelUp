/**
 * Header Components Scripts (Search, Notifications)
 */
document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. Search Autocomplete Logic ---
    const searchInput = document.getElementById('globalSearchInput');
    const autocompletePanel = document.getElementById('searchAutocompletePanel');
    const searchContent = document.getElementById('searchAutocompleteContent');
    let searchTimeout;

    // Position the panel relative to input
    const positionPanel = () => {
        const rect = searchInput.getBoundingClientRect();
        autocompletePanel.style.top = `${rect.bottom + 10}px`;
        // Check if mobile to center it, otherwise align left with input
        if (window.innerWidth < 992) {
            autocompletePanel.style.left = '50%';
            autocompletePanel.style.transform = 'translateX(-50%)';
            autocompletePanel.style.width = '95vw';
        } else {
            autocompletePanel.style.left = `${rect.left}px`;
            autocompletePanel.style.transform = 'none';
        }
    };

    // Keyboard Shortcut Ctrl+K
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
        }
    });

    // Show Panel on Focus
    searchInput.addEventListener('focus', () => {
        positionPanel();
        if (searchInput.value.trim().length > 0) {
            autocompletePanel.classList.remove('d-none');
        }
    });

    // Handle Typing (AJAX Simulation)
    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.trim();
        
        if (query.length === 0) {
            autocompletePanel.classList.add('d-none');
            return;
        }

        autocompletePanel.classList.remove('d-none');
        positionPanel();
        
        // Show Loading
        searchContent.innerHTML = `
            <div class="d-flex justify-content-center p-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            </div>`;

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            // Mocking AJAX Fetch Response
            const mockHTML = `
                <a href="#" class="d-flex align-items-center gap-3 mb-2 text-decoration-none lu-dropdown-item p-2 rounded">
                    <div class="bg-primary bg-opacity-10 text-primary rounded p-2"><i class="bi bi-play-circle"></i></div>
                    <div>
                        <div class="text-white fw-medium fs-6">Fullstack ${query}</div>
                        <div class="text-secondary" style="font-size: 0.75rem;">Course • 15 Lessons</div>
                    </div>
                </a>
                <a href="#" class="d-flex align-items-center gap-3 text-decoration-none lu-dropdown-item p-2 rounded">
                    <div class="bg-warning bg-opacity-10 text-warning rounded p-2"><i class="bi bi-briefcase"></i></div>
                    <div>
                        <div class="text-white fw-medium fs-6">Senior ${query} Developer</div>
                        <div class="text-secondary" style="font-size: 0.75rem;">Career Path</div>
                    </div>
                </a>
            `;
            searchContent.innerHTML = mockHTML;
        }, 400); // 400ms Debounce
    });

    // Hide panel when clicking outside
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

    // --- 2. Notifications Logic (AJAX Load on click) ---
    const notifButton = document.getElementById('notifButton');
    const notifList = document.getElementById('notifList');
    let notifLoaded = false;

    if (notifButton) {
        notifButton.addEventListener('show.bs.dropdown', () => {
            if (!notifLoaded) {
                // Mocking AJAX Load
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
});