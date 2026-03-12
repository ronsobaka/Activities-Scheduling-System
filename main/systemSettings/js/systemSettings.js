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
                    break;
            }
        });
    });
    
    // Load initial card (staff management)
    if (typeof loadUsers === 'function') loadUsers();
});
