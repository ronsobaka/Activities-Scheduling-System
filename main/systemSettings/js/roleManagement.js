let allRoles = [];

function loadRoles() {
    fetch('phpRequests/roleManagement.php')
    .then(response => response.json())
    .then(roles => {
        allRoles = roles;
        displayRoles(roles);
    })
    .catch(error => console.error("Error loading roles:", error));
}

function displayRoles(roles) {
    const tbody = document.getElementById('rolesTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    roles.forEach(role => {
        const row = document.createElement('tr');
        
        row.innerHTML = `
            <td>${escapeHtml(role.roleName)}</td>
            <td>${escapeHtml(role.roleDescription)}</td>
            <td>
                <div style="width: 40px; height: 40px; background: ${role.colour}; border-radius: 6px; border: 1px solid #ddd;"></div>
            </td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="editRole(${role.roleID})">Edit</button>
                <button class="btn btn-sm btn-danger" onclick="deleteRole(${role.roleID})">Delete</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function deleteRole(roleID) {
    const role = allRoles.find(r => String(r.roleID) === String(roleID))

    if (!role) return;

    if (confirm(`Are you sure you want to delete the "${role.roleName}" role?`)) {
        fetch('phpRequests/roleManagement.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'delete',
                roleID: roleID
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadRoles();
            } else {
                if (data.assignedStaff && data.assignedStaff.length > 0) {
                    // Create a list of staff names
                    const staffList = data.assignedStaff.map(s => 
                        `${s.firstName} ${s.lastName}`
                    ).join('\n• ');
                    
                    alert(`Cannot delete "${role.roleName}" role because the following staff members are assigned to it:\n\n• ${staffList}\n\nPlease reassign these staff members to another role first.`);
                } else {
                    alert('Error: ' + data.error);
                }
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function editRole(roleID) {
    const role = allRoles.find(r => String(r.roleID) === String(roleID));
    if (!role) return;

    document.getElementById("roleModalTitle").textContent = role.roleName;
    document.getElementById("roleID").value = role.roleID;
    document.getElementById("roleName").value = role.roleName;
    document.getElementById("roleDescription").value = role.roleDescription;
    document.getElementById("roleColour").value = role.colour;

    const modal = new bootstrap.Modal(document.getElementById('roleModal'));
    modal.show();

}


function saveRole() {

    const roleID = document.getElementById("roleID").value;
    const roleName = document.getElementById("roleName").value;
    const roleDescription = document.getElementById("roleDescription").value;
    const colour = document.getElementById("roleColour").value;

    if (!roleName) {
        alert('Please enter a role name');
        return;
    }


    const roleData = {
        roleID: roleID,
        roleName: roleName,
        roleDescription: roleDescription,
        colour: colour
    };
    

    fetch('phpRequests/roleManagement.php', { 
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(roleData)
    })
    .then(response => {
        return response.json();
    })
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('roleModal')).hide();
            loadRoles();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => console.error('Error:', error));
}

function showAddRoleModal() {

    document.getElementById("roleModalTitle").textContent = "Add Role";
    document.getElementById("roleID").value = ""; 
    document.getElementById("roleName").value = "";
    document.getElementById("roleDescription").value = "";
    document.getElementById("roleColour").value = "#1c0696";
    
    const modal = new bootstrap.Modal(document.getElementById('roleModal'));
    modal.show();
}