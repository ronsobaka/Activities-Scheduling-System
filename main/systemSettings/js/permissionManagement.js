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
    
}

// Helper function
function escapeHTML(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}