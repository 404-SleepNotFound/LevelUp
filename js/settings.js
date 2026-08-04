/**
 * LevelUp - Settings Page Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    
    const toastEl = document.getElementById('liveToast');
    const toast = new bootstrap.Toast(toastEl);
    const toastMsg = document.getElementById('toastMessage');

    function showToast(message, type = 'success') {
        toastMsg.innerText = message;
        toastEl.className = `toast align-items-center border-0 rounded-3 shadow-lg text-bg-${type}`;
        toast.show();
    }

    // 1. Handle Tab Persistence via URL Hash
    const hash = window.location.hash;
    if (hash) {
        const targetTab = document.querySelector(`[data-bs-target="${hash}"]`);
        if (targetTab) {
            new bootstrap.Tab(targetTab).show();
        }
    }
    
    document.querySelectorAll('.settings-nav .nav-link').forEach(tab => {
        tab.addEventListener('shown.bs.tab', (e) => {
            const target = e.target.getAttribute('data-bs-target');
            history.pushState(null, null, target);
        });
    });

    // 2. Profile Form Submission
    document.getElementById('profileForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('btnSaveProfile');
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());
        data.action = 'update_profile';

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

        try {
            const res = await fetch('ajax_settings.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await res.json();
            showToast(result.message, result.status);
        } catch (error) {
            showToast('An error occurred while saving.', 'danger');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Save Changes';
        }
    });

    // 3. Password Form Submission
    document.getElementById('passwordForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        // Basic frontend validation
        const newPwd = document.getElementById('new_pwd').value;
        const confPwd = document.querySelector('[name="confirm_pwd"]').value;
        
        if(newPwd !== confPwd) {
            showToast('Passwords do not match.', 'danger');
            return;
        }

        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());
        data.action = 'update_password';

        try {
            const res = await fetch('ajax_settings.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await res.json();
            showToast(result.message, result.status);
            if(result.status === 'success') e.target.reset();
        } catch (error) {
            showToast('An error occurred.', 'danger');
        }
    });

    // 4. AJAX Toggle Switches (Auto-save)
    document.querySelectorAll('.ajax-toggle').forEach(toggle => {
        toggle.addEventListener('change', async (e) => {
            const settingName = e.target.dataset.setting || 'general_toggle';
            const state = e.target.checked;

            try {
                const res = await fetch('ajax_settings.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'toggle_setting', setting: settingName, state: state })
                });
                const result = await res.json();
                showToast(result.message, 'success');
            } catch (error) {
                e.target.checked = !state; // Revert visually on error
                showToast('Failed to save preference.', 'danger');
            }
        });
    });

    // 5. AI Persona Selection (Auto-save)
    document.querySelectorAll('input[name="ai_persona"]').forEach(radio => {
        radio.addEventListener('change', async (e) => {
            if(e.target.checked) {
                try {
                    const res = await fetch('ajax_settings.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'update_persona', persona: e.target.value })
                    });
                    const result = await res.json();
                    showToast(result.message, 'success');
                } catch (error) {
                    showToast('Failed to update AI Persona.', 'danger');
                }
            }
        });
    });

    // 6. Delete Account Logic
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
    const deleteInput = document.getElementById('deleteConfirmInput');
    const btnConfirmDelete = document.getElementById('confirmDeleteBtn');

    document.getElementById('btnDeleteAccount').addEventListener('click', () => {
        deleteInput.value = '';
        btnConfirmDelete.disabled = true;
        deleteModal.show();
    });

    deleteInput.addEventListener('input', (e) => {
        btnConfirmDelete.disabled = (e.target.value !== 'DELETE');
    });

    btnConfirmDelete.addEventListener('click', () => {
        // Mock API call to delete
        btnConfirmDelete.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Deleting...';
        setTimeout(() => {
            window.location.href = 'login.php?deleted=true';
        }, 1500);
    });

});