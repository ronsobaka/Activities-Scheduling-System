let allUsers = [];

function loadUsers() {
    fetch('phpRequests/userManagement.php?action=users')
    .then(response => response.json())
    .then(users => {
        allUsers = users;
        displayUsers(users);
    })
    .catch(error => console.error("Error loading users:", error));
}

function displayUsers(users) {
    const tbody = document.getElementById('usersTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    users.forEach(user => {
        const row = document.createElement('tr');
        const isActive = user.status === 'active';
        const buttonClass = isActive ? 'btn-danger' : 'btn-success';
        const buttonText = isActive ? 'Disable' : 'Enable';
        
        row.innerHTML = `
            <td>${escapeHtml(user.firstName)} ${escapeHtml(user.lastName)}</td>
            <td>${escapeHtml(user.email)}</td>
            <td>${escapeHtml(user.roleName || 'No Role')}</td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="editUser(${user.userID})">Edit</button>
                <button class="btn btn-sm ${buttonClass}" onclick="toggleUserStatus(${user.userID}, event)">${buttonText}</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function editUser(userID) {
    const user = allUsers.find(u => String(u.userID) === String(userID));
    if (!user) return;
    
    document.getElementById('editUserID').value = user.userID;
    document.getElementById('editFirstName').value = user.firstName;
    document.getElementById('editLastName').value = user.lastName;
    document.getElementById('editEmail').value = user.email;
    document.getElementById('editStatus').value = user.status;
    
    loadRolesForUser(user.roleID);
    
    const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
    modal.show();
}

function loadRolesForUser(selectedRoleID) {
    fetch('phpRequests/userManagement.php?action=roles') 
    .then(response => response.json())
    .then(roles => {
        const select = document.getElementById('editRole');
        select.innerHTML = '';
        
        roles.forEach(role => {
            const option = document.createElement('option');
            option.value = role.roleID;
            option.textContent = role.roleName;
            if (String(role.roleID) === String(selectedRoleID)) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    })
    .catch(error => console.error('Error loading roles:', error));
}

function saveUserChanges() {
    const userData = {
        userID: document.getElementById('editUserID').value,
        firstName: document.getElementById('editFirstName').value,
        lastName: document.getElementById('editLastName').value,
        email: document.getElementById('editEmail').value,
        roleID: document.getElementById('editRole').value,
        status: document.getElementById('editStatus').value
    };
    
    fetch('phpRequests/userManagement.php', { 
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(userData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
            loadUsers();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => console.error('Error:', error));
}

function toggleUserStatus(userID, event) {
    const button = event.target;
    const currentStatus = button.textContent.trim() === 'Disable' ? 'active' : 'disabled';
    const newStatus = currentStatus === 'active' ? 'disabled' : 'active';
    
    if (confirm(`Are you sure you want to ${newStatus === 'active' ? 'enable' : 'disable'} this user?`)) {
        fetch('phpRequests/userManagement.php', { 
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'status',
                userID: userID,
                newStatus: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadUsers();
            } else {
                alert('Error: ' + data.error);
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    loadUsers();

    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            filterUsers(e.target.value);
        });
    }
});

function filterUsers(searchText) {
    const filtered = allUsers.filter(user => {
        const fullName = `${user.firstName} ${user.lastName}`.toLowerCase();
        const email = user.email.toLowerCase();
        const search = searchText.toLowerCase();
        return fullName.includes(search) || email.includes(search);
    });
    displayUsers(filtered);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}