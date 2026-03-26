let allFeatures = [];

function loadPermissionsRoles() {
    // Get Roles
    fetch('phpRequests/roleManagement.php')
    .then(response => response.json())
    .then(roles => {
        allRoles = roles;
        
        const select = document.getElementById("permissionRoleSelect");
        select.innerHTML = ''; // Clear existing options
        
        const defaultOption = document.createElement("option");
        defaultOption.value = "";
        defaultOption.textContent = "Select a role";
        select.appendChild(defaultOption);
        
        allRoles.forEach(role => {
            const option = document.createElement("option");
            option.value = role.roleID; // Set value to roleID, not empty
            option.textContent = role.roleName;
            select.appendChild(option);
        });
        
        loadFeatures();
    })
    .catch(error => console.error("Error loading roles:", error));   
}

function loadFeatures() {
    fetch('phpRequests/permissionManagement.php?action=features')
    .then(response => response.json())
    .then(features => {
        allFeatures = features;
    })
    .catch(error => console.error("Error loading features:", error));  
}

document.getElementById("permissionRoleSelect").addEventListener("change", function(e) {
    const roleID = e.target.value;
    if (!roleID) {
        document.getElementById("permissionsTable").style.display = "none";
        return;
    }
    document.getElementById("permissionsTable").style.display = "block";
    loadPermissionsForRole(roleID);
});

function loadPermissionsForRole(roleID) {
    fetch(`phpRequests/permissionManagement.php?action=getPermissions&roleID=${roleID}`)
    .then(response => response.json())
    .then(permissions => {
        const hasPermission = permissions.map(p => p.featureID);
        displayPermissions(hasPermission);
    })
    .catch(error => console.error("Error loading permissions:", error));
}

function displayPermissions(hasPermission) {
    const tbody = document.getElementById('permissionsTableBody');
    tbody.innerHTML = '';
    
    allFeatures.forEach(feature => {
        const row = document.createElement('tr');
        const checked = hasPermission.includes(feature.featureID) ? 'checked' : '';
        
        row.innerHTML = `
            <td>${escapeHTML(feature.name)}</td>
            <td>${escapeHTML(feature.description || '')}</td>
            <td>
                <input type="checkbox" class="form-check-input" 
                       data-feature-id="${feature.featureID}" ${checked}>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function savePermissions() {
    const roleID = document.getElementById("permissionRoleSelect").value;
    if (!roleID) {
        alert("Please select a role first");
        return;
    }
    
    const checkboxes = document.querySelectorAll('#permissionsTableBody input[type="checkbox"]:checked');
    const featureIDs = Array.from(checkboxes).map(cb => parseInt(cb.dataset.featureId));
    
    if (!confirm("Save these permissions for this role?")) {
        return;
    }
    
    const saveBtn = document.getElementById("savePermissionsBtn");
    const originalText = saveBtn.innerHTML;
    
    // Show loading state
    saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
    saveBtn.disabled = true;
    
    fetch('phpRequests/permissionManagement.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'save',
            roleID: roleID,
            featureIDs: featureIDs
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Success - turn button green with checkmark
            saveBtn.innerHTML = '✓ Saved!';
            saveBtn.classList.remove('btn-primary');
            saveBtn.classList.add('btn-success');
            
            // Reset button after 2 seconds
            setTimeout(() => {
                saveBtn.innerHTML = originalText;
                saveBtn.classList.remove('btn-success');
                saveBtn.classList.add('btn-primary');
                saveBtn.disabled = false;
            }, 2000);
        } else {
            // Error - show error state
            saveBtn.innerHTML = '✗ Error';
            saveBtn.classList.remove('btn-primary');
            saveBtn.classList.add('btn-danger');
            
            setTimeout(() => {
                saveBtn.innerHTML = originalText;
                saveBtn.classList.remove('btn-danger');
                saveBtn.classList.add('btn-primary');
                saveBtn.disabled = false;
            }, 2000);
            alert("Error: " + data.error);
        }
    })
    .catch(error => {
        console.error("Error saving permissions:", error);
        saveBtn.innerHTML = '✗ Failed';
        saveBtn.classList.remove('btn-primary');
        saveBtn.classList.add('btn-danger');
        
        setTimeout(() => {
            saveBtn.innerHTML = originalText;
            saveBtn.classList.remove('btn-danger');
            saveBtn.classList.add('btn-primary');
            saveBtn.disabled = false;
        }, 2000);
        alert("Failed to save permissions");
    });
}
// Helper function
function escapeHTML(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}