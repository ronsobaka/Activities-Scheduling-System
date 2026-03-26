document.addEventListener("DOMContentLoaded", function() {
    // Sidebar navigation
    document.querySelectorAll('.list-group-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Update active state
            document.querySelectorAll('.list-group-item').forEach(link => {
                link.classList.remove('active');
            });
            this.classList.add('active');
            
            // Hide all cards
            document.getElementById('staffManagementCard').style.display = 'none';
            document.getElementById('roleManagementCard').style.display = 'none';
            document.getElementById('permissionsCard').style.display = 'none';
            document.getElementById('systemSettingsCard').style.display = 'none';
            
            // Show selected card
            const card = this.dataset.card;
            switch(card) {
                case 'staff':
                    document.getElementById('staffManagementCard').style.display = 'block';
                    if (typeof loadUsers === 'function') loadUsers();
                    break;
                case 'roles':
                    document.getElementById('roleManagementCard').style.display = 'block';
                    if (typeof loadRoles === 'function') loadRoles();
                    break;
                case 'permissions':
                    document.getElementById('permissionsCard').style.display = 'block';
                    if (typeof loadPermissionsRoles === 'function') loadPermissionsRoles();
                    if (typeof loadFeatures === 'function') loadFeatures();
                    break;
                case 'system':
                    document.getElementById('systemSettingsCard').style.display = 'block';
                    if (typeof loadSystemSettings === 'function') loadSystemSettings();
                    break;
            }
        });
    });
    
    // Load initial card (staff management)
    if (typeof loadUsers === 'function') loadUsers();
});

// Load system settings when the card is shown
function loadSystemSettings() {
    fetch('phpRequests/systemSettings.php?action=get')
    .then(response => response.json())
    .then(settings => {
        if (settings) {
            document.getElementById('siteName').value = settings.siteName || 'Staff Scheduling System';
            document.getElementById('siteEmail').value = settings.siteEmail || 'admin@example.com';
            document.getElementById('defaultRole').value = settings.defaultRole || '4';
            document.getElementById('sessionTimeout').value = settings.sessionTimeout || '30';
            document.getElementById('dateFormat').value = settings.dateFormat || 'd/m/Y';
            document.getElementById('allowSelfRegistration').checked = settings.allowSelfRegistration == 1;
            document.getElementById('requireApproval').checked = settings.requireApproval == 1;
            document.getElementById('maintenanceMessage').value = settings.maintenanceMessage || '';
        }
    })
    .catch(error => console.error('Error loading system settings:', error));
}

// Save system settings
document.getElementById('saveSystemSettingsBtn').addEventListener('click', function() {
    const settingsData = {
        siteName: document.getElementById('siteName').value,
        siteEmail: document.getElementById('siteEmail').value,
        defaultRole: document.getElementById('defaultRole').value,
        sessionTimeout: document.getElementById('sessionTimeout').value,
        dateFormat: document.getElementById('dateFormat').value,
        allowSelfRegistration: document.getElementById('allowSelfRegistration').checked ? 1 : 0,
        requireApproval: document.getElementById('requireApproval').checked ? 1 : 0,
        maintenanceMessage: document.getElementById('maintenanceMessage').value
    };
    
    const saveBtn = document.getElementById('saveSystemSettingsBtn');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
    saveBtn.disabled = true;
    
    fetch('phpRequests/systemSettings.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(settingsData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            saveBtn.innerHTML = '✓ Saved!';
            saveBtn.classList.remove('btn-primary');
            saveBtn.classList.add('btn-success');
            setTimeout(() => {
                saveBtn.innerHTML = originalText;
                saveBtn.classList.remove('btn-success');
                saveBtn.classList.add('btn-primary');
                saveBtn.disabled = false;
            }, 2000);
        } else {
            saveBtn.innerHTML = '✗ Error';
            saveBtn.classList.remove('btn-primary');
            saveBtn.classList.add('btn-danger');
            setTimeout(() => {
                saveBtn.innerHTML = originalText;
                saveBtn.classList.remove('btn-danger');
                saveBtn.classList.add('btn-primary');
                saveBtn.disabled = false;
            }, 2000);
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error saving settings:', error);
        saveBtn.innerHTML = '✗ Failed';
        saveBtn.classList.remove('btn-primary');
        saveBtn.classList.add('btn-danger');
        setTimeout(() => {
            saveBtn.innerHTML = originalText;
            saveBtn.classList.remove('btn-danger');
            saveBtn.classList.add('btn-primary');
            saveBtn.disabled = false;
        }, 2000);
        alert('Failed to save settings');
    });
});
