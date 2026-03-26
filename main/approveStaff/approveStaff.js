document.addEventListener('DOMContentLoaded', function() {
    loadPendingStaff();
});

function loadPendingStaff() {
    fetch('approveStaff.php?action=pending')  // Fixed: removed phpRequests/
    .then(response => response.json())
    .then(staff => {
        const tbody = document.querySelector('#pendingStaffTable tbody');
        tbody.innerHTML = '';
        
        if (staff.length === 0) {
            tbody.innerHTML = '\<td colspan="4" class="text-center text-muted">No pending staff registrations</td><\/tr>';
            return;
        }
        
        staff.forEach(user => {
            const row = document.createElement('tr');
            const registeredDate = new Date(user.created_at).toLocaleDateString();
            
            row.innerHTML = `
                <td>${escapeHtml(user.firstName)} ${escapeHtml(user.lastName)}<\/td>
                <td>${escapeHtml(user.email)}<\/td>
                <td>${registeredDate}<\/td>
                <td>
                    <button class="btn btn-sm btn-success me-1" onclick="approveStaff(${user.userID})">Approve<\/button>
                    <button class="btn btn-sm btn-danger" onclick="rejectStaff(${user.userID})">Reject<\/button>
                <\/td>
            `;
            tbody.appendChild(row);
        });
    })
    .catch(error => console.error('Error loading pending staff:', error));
}

function approveStaff(userID) {
    if (confirm('Approve this staff member?')) {
        const approveBtn = event.target;
        approveBtn.disabled = true;
        approveBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        
        fetch('approveStaff.php', {  // Fixed: removed phpRequests/
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'approve',
                userID: userID
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadPendingStaff();
            } else {
                alert('Error: ' + data.error);
                approveBtn.disabled = false;
                approveBtn.innerHTML = 'Approve';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            approveBtn.disabled = false;
            approveBtn.innerHTML = 'Approve';
            alert('Failed to approve staff');
        });
    }
}

function rejectStaff(userID) {
    if (confirm('Reject this staff member? They will be removed from the system.')) {
        const rejectBtn = event.target;
        rejectBtn.disabled = true;
        rejectBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        
        fetch('approveStaff.php', {  // Already correct
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'reject',
                userID: userID
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadPendingStaff();
            } else {
                alert('Error: ' + data.error);
                rejectBtn.disabled = false;
                rejectBtn.innerHTML = 'Reject';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            rejectBtn.disabled = false;
            rejectBtn.innerHTML = 'Reject';
            alert('Failed to reject staff');
        });
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}