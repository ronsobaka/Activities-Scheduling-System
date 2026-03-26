document.addEventListener('DOMContentLoaded', function() {
    loadDashboardStats();
    loadUpcomingShifts();
    loadTodaysActivities();
    loadStaffOnsite();
});

function loadDashboardStats() {
    fetch('dashboard.php?action=stats')
    .then(response => response.json())
    .then(data => {
        // Manager-only stats
        if (document.getElementById('totalStaffCount')) {
            document.getElementById('totalStaffCount').textContent = data.totalStaff || 0;
            document.getElementById('pendingApprovalsCount').textContent = data.pendingApprovals || 0;
            document.getElementById('activitiesThisWeek').textContent = data.activitiesThisWeek || 0;
        }
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
            tbody.innerHTML = '   <tr><td colspan="4" class="text-center text-muted">No upcoming shifts</td></tr>';
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

function loadTodaysActivities() {
    fetch('dashboard.php?action=today')
    .then(response => response.json())
    .then(activities => {
        const container = document.getElementById('todaysActivities');
        
        if (activities.length === 0) {
            container.innerHTML = '<p class="text-muted">No activities scheduled for today.</p>';
            return;
        }
        
        let html = '<div class="list-group">';
        activities.forEach(activity => {
            html += `
                <div class="list-group-item">
                    <strong>${escapeHtml(activity.name)}</strong><br>
                    <small class="text-muted">${activity.startTime.substring(0,5)} - ${activity.endTime.substring(0,5)}</small><br>
                    <small>📍 ${escapeHtml(activity.location || 'No location')}</small>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    })
    .catch(error => console.error('Error loading today\'s activities:', error));
}

function loadStaffOnsite() {
    fetch('dashboard.php?action=onsite')
    .then(response => response.json())
    .then(staff => {
        const container = document.getElementById('staffOnsite');
        
        if (staff.length === 0) {
            container.innerHTML = '<p class="text-muted">No staff onsite today.</p>';
            return;
        }
        
        let html = '<div class="list-group">';
        staff.forEach(person => {
            html += `
                <div class="list-group-item">
                    <strong>${escapeHtml(person.firstName)} ${escapeHtml(person.lastName)}</strong><br>
                    <small class="text-muted">${escapeHtml(person.activity)}</small><br>
                    <small>⏰ ${person.startTime.substring(0,5)} - ${person.endTime.substring(0,5)}</small>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    })
    .catch(error => console.error('Error loading staff onsite:', error));
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