document.addEventListener('DOMContentLoaded', function() {
    loadDashboardStats();
    loadUpcomingShifts();
});

function loadDashboardStats() {
    fetch('dashboard.php?action=stats')
    .then(response => response.json())
    .then(data => {
        document.getElementById('upcomingShiftsCount').textContent = data.upcomingShifts || 0;
        document.getElementById('totalStaffCount').textContent = data.totalStaff || 0;
        document.getElementById('pendingApprovalsCount').textContent = data.pendingApprovals || 0;
        document.getElementById('activitiesThisWeek').textContent = data.activitiesThisWeek || 0;
    })
    .catch(error => console.error('Error loading stats:', error));
}

function loadUpcomingShifts() {
    fetch('dashboard.php?action=upcoming')
    .then(response => response.json())
    .then(shifts => {
        const tbody = document.getElementById('upcomingShiftsTable');
        tbody.innerHTML = '';
        
        if (shifts.length === 0) {
            tbody.innerHTML = '  <tr><td colspan="4" class="text-center text-muted">No upcoming shifts</td></tr>';
            return;
        }
        
        shifts.forEach(shift => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${formatDate(shift.activityDate)}</td>
                <td>${escapeHtml(shift.name)}</td>
                <td>${shift.startTime.substring(0,5)} - ${shift.endTime.substring(0,5)}</td>
                <td>${escapeHtml(shift.location || '-')}</td>
            `;
            tbody.appendChild(row);
        });
    })
    .catch(error => console.error('Error loading upcoming shifts:', error));
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB');
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}