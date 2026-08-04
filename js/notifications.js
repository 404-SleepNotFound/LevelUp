/**
 * LevelUp - Notifications Page Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    
    const container = document.getElementById('notificationsContainer');
    let allNotifications = [];

    // Init fetch
    fetchNotifications();

    // Mark All As Read
    document.getElementById('btnMarkAllRead').addEventListener('click', async (e) => {
        const btn = e.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';
        btn.disabled = true;

        try {
            await fetch('ajax_notifications.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'mark_all_read' })
            });
            // Update local state and re-render
            allNotifications.forEach(n => n.is_read = true);
            renderNotifications(allNotifications);
            updateBadges();
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });

    // Event Delegation for Notification Actions (Mark read, Delete)
    container.addEventListener('click', async (e) => {
        // Mark as read click
        if (e.target.closest('.mark-read-btn')) {
            const btn = e.target.closest('.mark-read-btn');
            const item = btn.closest('.notification-item');
            const id = item.dataset.id;
            
            await fetch('ajax_notifications.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'mark_read', id: id })
            });

            // Update UI
            item.classList.remove('unread', 'priority-critical', 'priority-important', 'priority-normal');
            btn.remove(); // Remove the mark as read button
            const notif = allNotifications.find(n => n.id == id);
            if (notif) notif.is_read = true;
            updateBadges();
        }

        // Delete click
        if (e.target.closest('.delete-btn')) {
            const item = e.target.closest('.notification-item');
            const id = item.dataset.id;
            
            item.style.opacity = '0';
            setTimeout(() => { item.remove(); }, 300);

            await fetch('ajax_notifications.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete', id: id })
            });
            
            allNotifications = allNotifications.filter(n => n.id != id);
            updateBadges();
        }
    });

    // Filter Navigation Click
    document.querySelectorAll('.filter-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Remove active classes
            if(link.closest('#categoryFilters')) {
                document.querySelectorAll('#categoryFilters .filter-link span').forEach(el => {
                    el.classList.remove('text-light', 'fw-bold');
                    el.classList.add('text-muted');
                });
            } else {
                document.querySelectorAll('#statusFilters .filter-link span').forEach(el => {
                    el.classList.remove('text-light', 'fw-bold');
                    if(!el.classList.contains('badge')) el.classList.add('text-muted');
                });
            }
            
            // Add active class
            const textSpan = link.querySelector('span:not(.badge)');
            textSpan.classList.remove('text-muted');
            textSpan.classList.add('text-light', 'fw-bold');

            applyFilters();
        });
    });

    // Search Input
    document.getElementById('searchInput').addEventListener('input', applyFilters);

    // --- Core Functions ---

    async function fetchNotifications() {
        try {
            const res = await fetch('ajax_notifications.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'fetch_notifications' })
            });
            const data = await res.json();
            
            if (data.status === 'success') {
                allNotifications = data.data;
                renderNotifications(allNotifications);
                updateBadges();
            }
        } catch (error) {
            container.innerHTML = `<div class="text-danger text-center">Failed to load notifications.</div>`;
        }
    }

    function applyFilters() {
        let filtered = [...allNotifications];
        
        // Category Filter
        const activeCat = document.querySelector('#categoryFilters .text-light')?.closest('a').dataset.cat || 'all';
        if (activeCat !== 'all') {
            filtered = filtered.filter(n => n.category === activeCat);
        }

        // Status Filter
        const activeStatus = document.querySelector('#statusFilters .text-light')?.closest('a').dataset.filter || 'all';
        if (activeStatus === 'unread') {
            filtered = filtered.filter(n => !n.is_read);
        }

        // Search Filter
        const term = document.getElementById('searchInput').value.toLowerCase();
        if (term) {
            filtered = filtered.filter(n => 
                n.title.toLowerCase().includes(term) || 
                n.message.toLowerCase().includes(term)
            );
        }

        renderNotifications(filtered);
    }

    function renderNotifications(data) {
        if (data.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-bell-slash fs-1 mb-3 d-block opacity-50"></i>
                    <h5>No notifications found</h5>
                    <p class="fs-sm">You are all caught up!</p>
                </div>
            `;
            return;
        }

        // Group by Date Group
        const grouped = data.reduce((acc, obj) => {
            let key = obj.date_group;
            if (!acc[key]) acc[key] = [];
            acc[key].push(obj);
            return acc;
        }, {});

        let html = '';
        for (const [group, items] of Object.entries(grouped)) {
            html += `<div class="date-group-header">${group}</div>`;
            
            items.forEach(n => {
                const unreadClass = n.is_read ? '' : `unread priority-${n.priority}`;
                const actionBtn = n.action ? `<a href="${n.action.url}" class="btn ${n.action.btn_type} btn-sm rounded-pill mt-2 px-3 fw-bold">${n.action.label}</a>` : '';
                const markReadBtn = !n.is_read ? `<button class="btn btn-sm btn-link text-muted mark-read-btn p-0 me-2" title="Mark as read"><i class="bi bi-circle"></i></button>` : '';

                html += `
                    <div class="card card-dark border-secondary border-opacity-25 rounded-4 mb-3 notification-item ${unreadClass}" data-id="${n.id}">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex gap-3 align-items-start">
                                <div class="notif-icon-wrapper flex-shrink-0">
                                    <i class="bi ${n.icon} fs-4 ${n.icon_color}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="text-white fw-bold mb-1">${n.title}</h6>
                                        <span class="fs-xs text-muted ms-3 whitespace-nowrap">${n.time}</span>
                                    </div>
                                    <p class="text-light opacity-75 fs-sm mb-1">${n.message}</p>
                                    ${actionBtn}
                                </div>
                                <div class="notif-actions d-flex align-items-center">
                                    ${markReadBtn}
                                    <button class="btn btn-sm btn-link text-danger delete-btn p-0" title="Delete"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        container.innerHTML = html;
        document.getElementById('btnLoadMore').style.display = data.length >= 5 ? 'inline-block' : 'none';
    }

    function updateBadges() {
        const total = allNotifications.length;
        const unreadCount = allNotifications.filter(n => !n.is_read).length;
        
        document.querySelector('#statusFilters a[data-filter="all"] .badge').innerText = total;
        document.querySelector('#statusFilters a[data-filter="unread"] .badge').innerText = unreadCount;
        
        // Hide unread badge if 0
        const unreadBadge = document.querySelector('#statusFilters a[data-filter="unread"] .badge');
        unreadBadge.style.display = unreadCount === 0 ? 'none' : 'inline-block';
    }
});