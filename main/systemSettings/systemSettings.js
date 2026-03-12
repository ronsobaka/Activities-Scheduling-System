let allUsers = [];

document.addEventListener("DOMContentLoaded", function() {
    loadUsers();

    //search
    document.getElementById("searchInput").addEventListener("keyup", function() {
        filterUsers(this.value);
    })
})


function loadUsers() {
    fetch("phpRequests/userManagement.php")
    .then(response => response.json())
    .then(users => {
        allUsers = users;
        displayUsers(users)
    })
    .catch(error => console.error("Error loading staff:", error));
}

function displayUsers(users) {
    const tbody = document.querySelector("tbody");
    tbody.innerHTML = "";

    users.forEach(user=> {
        const row = document.createElement("tr");

        const isActive = user.status === "active";
        const buttonClass = isActive ? "btn-danger" : "btn-success";
        const buttonText = isActive ? "Disable" : "Enable";
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
    })
}

function filterUsers(searchText) {
    const filtered = allUsers.filter(user => {
        const fullName = `${user.firstName} ${user.lastName}` .toLowerCase();
        const email = user.email.toLowerCase();
        const search = searchText.toLowerCase();

        return fullName.includes(search) || email.includes(search);
    })

    displayUsers(filtered);
}

// Helper function to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('tr');
    div.textContent = text;
    return div.innerHTML;
}

function editUser(userID) {
    console.log('Edit user:', userID);
    // Open edit modal
}

function toggleUserStatus(userID, event) {
    const button = event.target;
    const currentStatus = button.textContent.trim() === 'Disable' ? 'active' : 'disabled';
    const newStatus = currentStatus === 'active' ? 'disabled' : 'active';

    if (confirm(`Are you sure you want to ${newStatus === 'active' ? 'active' : 'disabled'} this member of staff?`)) {
        fetch('phpRequests/userManagement.php',{
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                userID: userID,
                newStatus: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadUsers()
            } else {
                alert("Error updating user status: " + data.error);
            }
        })
        .catch(error => {
            console.error("Error: ", error);
            alert('Failed to update user status');
        });
    }
}