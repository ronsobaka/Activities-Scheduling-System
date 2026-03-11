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
        displayUsers
    })
    .catch(error => console.error("Error loading users:", error));
}

// Helper function to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function editUser(userID) {
    console.log('Edit user:', userID);
    // Open edit modal
}

function toggleUserStatus(userID) {
    console.log('Toggle status for user:', userID);
    // Toggle user status
}