const currentDate = new Date();
const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
const days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
let day = currentDate.getDate();
let month = monthNames[currentDate.getMonth()];
let year = currentDate.getFullYear();
let selectedView = "month";
let activities = {};
let staffAvailability = {};
let selectedStaff = [];

generateSchedule();

document.getElementById("viewDropdown").addEventListener("change", function(event) {
    selectedView = event.target.value;
    generateSchedule();
});

document.getElementById("prevMonth").addEventListener("click", function() {
    month = monthNames[(monthNames.indexOf(month) - 1 + 12) % 12];
    if (month === "December") {
        year--;
    }
    generateSchedule();
});

document.getElementById("nextMonth").addEventListener("click", function() {
    month = monthNames[(monthNames.indexOf(month) + 1) % 12];
    if (month === "January") {
        year++;
    }
    generateSchedule();
});

function generateSchedule() {
    document.getElementById("scheduleManagerTitle").textContent = `${month} ${year}`;
    if (selectedView === "month") {
        generateMonthView();
    } else if (selectedView === "week") {
        generateWeekView();
    } else if (selectedView === "day") {
        generateDayView();
    }
};

function generateMonthView() {
    console.log("Month view selected for: ", month, year);

    const monthIndex = monthNames.indexOf(month);
    let firstDay = new Date(year, monthIndex, 1).getDay();
    if (firstDay == 0) {
        firstDay = 6;
    } else {
        firstDay -= 1;
    }

    const container = document.getElementById("scheduleManagerContent");

    container.innerHTML = '';

    const grid = document.createElement('div');
    grid.className = "calendar-grid";
    
    days.forEach(day => {
        const header = document.createElement("div");
        header.className = "calendar-day-header";
        header.textContent = day;
        grid.appendChild(header);
    });

    for (let i = 0; i < firstDay; i ++) {
        const empty = document.createElement("div");
        empty.className = "calendar-cell empty";
        grid.appendChild(empty);
    }

    const daysInMonth = new Date(year, monthIndex + 1, 0).getDate();

    for (let j = 1; j <= daysInMonth; j++) {
        const cell = document.createElement("div");
        cell.className = "calendar-cell";

        const today = new Date();
        const cellDate = new Date(year, monthIndex, j);
        today.setHours(0,0,0,0);

        if (cellDate < today) {
            cell.classList.add("past");
        } else if (year === today.getFullYear() && monthIndex === today.getMonth() && j === today.getDate()) {
            cell.classList.add("today");
        }

        cell.dataset.date = `${year}-${monthIndex + 1}-${j}`;

        const dateDiv = document.createElement("div");
        dateDiv.className = "date";
        dateDiv.textContent = j;
        cell.appendChild(dateDiv);

        grid.appendChild(cell);
    }

    container.appendChild(grid);
    loadActivitiesForMonth(month, year);
}

function generateWeekView() {
    console.log("Week view selected");
}

function generateDayView() {
    console.log("Day view selected");
}


document.addEventListener("click", function(e) {
    if (e.target.closest(".calendar-cell:not(.empty)")) {
        const cell = e.target.closest(".calendar-cell");
        const date = cell.dataset.date;
        
        const [year, month, day] = date.split("-");

        const monthName = monthNames[parseInt(month) - 1];
        document.getElementById("modalDateTitle").textContent = 
            `${monthName} ${parseInt(day)}, ${year}`;
        

        document.getElementById("activityModal").dataset.currentDate = date;
        
        loadActivitiesForDate(date);
        

        document.getElementById("activityModal").classList.add("active");
    }
});

document.getElementById("closeActivityModal").addEventListener("click", function() {
    document.getElementById("activityModal").classList.remove("active");
    document.getElementById("activityForm").style.display = "none";
    document.getElementById("addActivityBtn").style.display = "block";
});


document.getElementById("addActivityBtn").addEventListener("click", function() {
    document.getElementById("activityForm").style.display = "block";
    this.style.display = "none";
});


document.getElementById("cancelActivityBtn").addEventListener("click", function() {
    document.getElementById("activityForm").style.display = "none";
    document.getElementById("addActivityBtn").style.display = "block";
});

document.getElementById("saveActivityBtn").addEventListener("click", function() {
    
    const activityData = {
        name: document.getElementById("activityName").value,
        startTime: document.getElementById("activityStart").value,
        endTime: document.getElementById("activityEnd").value,
        location: document.getElementById("activityLocation").value,
        equipment: document.getElementById("activityEquipment").value,
        notes: document.getElementById("activityNotes").value,
        selectedStaff: selectedStaff
    };

    const currentDate = document.getElementById("activityModal").dataset.currentDate;

    if (!activities[currentDate]) {
        activities[currentDate] = [];
    }

    const editingIndex = document.getElementById("activityModal").dataset.editingIndex;

    if (editingIndex !== undefined) {
        activities[currentDate][editingIndex] = activityData;
        delete document.getElementById("activityModal").dataset.editingIndex;
    } else {
        activities[currentDate].push(activityData);
    }

    updateCellPreview(currentDate);
    loadActivitiesForDate(currentDate);

    saveActivityToServer();

    clearForm();

    document.getElementById("activityForm").style.display = "none";
    document.getElementById("addActivityBtn").style.display = "block";
})

function editActivity(date, index) {
    const activity = activities[date][index];
    
    document.getElementById("activityName").value = activity.name || '';
    document.getElementById("activityStart").value = activity.startTime || '09:00';
    document.getElementById("activityEnd").value = activity.endTime || '17:00';
    document.getElementById("activityLocation").value = activity.location || '';
    document.getElementById("activityEquipment").value = activity.equipment || '';
    document.getElementById("activityNotes").value = activity.notes || '';
    
    document.getElementById("activityModal").dataset.editingIndex = index;

    document.getElementById("activityForm").style.display = "block";
    document.getElementById("addActivityBtn").style.display = "none";
}


function loadActivitiesForDate(date) {
    console.log("Loading activities for:", date);

    const activityList = document.getElementById("activitiesList");
    activityList.innerHTML = '';

    if (!activities[date] || activities[date].length === 0) {
        activityList.innerHTML = '<p class="no-activities">No activities scheduled for this day.</p>';
        return;
    }

    activities[date].forEach((activity, index) => {
        const activityItem = document.createElement("div");
        activityItem.className = "activity-item";
        activityItem.dataset.index = index;

        const startTime = activity.startTime.substring(0, 5);
        const endTime = activity.endTime.substring(0, 5);

        let detailsHTML = "";
        if (activity.location) {
            detailsHTML += `📍 ${activity.location} `;
        }


        activityItem.innerHTML = `
            <div style="display: flex; justify-content: space-between;">
                <div>
                    <div class="activity-time">${startTime} - ${endTime}</div>
                    <div class="activity-name">${activity.name}</div>
                    ${detailsHTML ? `<div class="activity-details">${detailsHTML}</div>` : ''}
                </div>
                <button class="delete-activity" data-index="${index}">🗑️</button>
            </div>
        `;

        activityItem.querySelector('.delete-activity').addEventListener('click', function(e) {
            e.stopPropagation();
            const index = this.dataset.index;
            
            if (confirm('Delete this activity?')) {
                activities[date].splice(index, 1);
                if (activities[date].length === 0) {
                    delete activities[date];
                }
                loadActivitiesForDate(date);
                updateCellPreview(date);
                saveActivityToServer();
            }
        });

        activityItem.addEventListener("click", function() {
            editActivity(date, index);
        });

        activityList.appendChild(activityItem);
    });
}


function updateCellPreview(date) {

    const cells = Array.from(document.querySelectorAll('.calendar-cell'));    
    let targetCell = null;

    cells.forEach(cell => {
        if (cell.dataset.date === date) {
            targetCell = cell;
        }
    });

    if (!targetCell) {
        return;
    }

    if (!activities[date] || activities[date].length === 0) {
        const dateDiv = targetCell.querySelector(".date");
        targetCell.innerHTML = '';
        targetCell.appendChild(dateDiv);
        return;
    }

    const dateDiv = targetCell.querySelector(".date");
    targetCell.innerHTML = '';
    targetCell.appendChild(dateDiv);

    const activitiesToShow = activities[date].slice(0, 2);
    activitiesToShow.forEach(activity => {
        const preview = document.createElement("div");
        preview.className = "activity-preview";
        preview.textContent = `${activity.startTime} - ${activity.endTime}: ${activity.name}`;
        targetCell.appendChild(preview);
    });

    if (activities[date].length > 2) {
        const more = document.createElement("div");
        more.className = "more-activities";
        more.textContent = `+${activities[date].length - 2} more`;
        targetCell.appendChild(more);
    }
}

function clearForm() {
    document.getElementById("activityName").value = '';
    document.getElementById("activityStart").value = '09:00';
    document.getElementById("activityEnd").value = '17:00';
    document.getElementById("activityLocation").value = '';
    document.getElementById("activityEquipment").value = '';
    document.getElementById("activityNotes").value = '';
}

function loadActivitiesForMonth(month, year) {
    fetch(`scheduleManager.php?month=${month}&year=${year}`)
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error("Error loading activities:", data.error);
            return;
        }

        const formattedData = {};
        for (let dbDate in data) {
            const parts = dbDate.split("-");
            const cellFormat = `${parts[0]}-${parseInt(parts[1])}-${parseInt(parts[2])}`;
            formattedData[cellFormat] = data[dbDate];
        }

        activities = {...activities, ...formattedData};
        
        const monthIndex = monthNames.indexOf(month);
        for (let day = 1; day <= new Date(year, monthIndex + 1, 0).getDate(); day++) {
            const date = `${year}-${monthIndex + 1}-${day}`;
            updateCellPreview(date);
        }
    })
    .catch(error => {
        console.error("Error fetching activities:", error);
    });
}

function saveActivityToServer() {
    const currentDate = document.getElementById("activityModal").dataset.currentDate;

    fetch('scheduleManager.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            date: currentDate,
            activities: activities[currentDate] || []
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error('Error saving:', data.error);
            alert('Failed to save: ' + data.error);
        } else {
            console.log('Saved successfully');
            loadActivitiesForMonth(month, year);
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Error saving to database');
    });
}





// Staff Assignment


function getStaffAvailability(date, activityID) {
    fetch(`getStaffAvailability.php?date=${date}&activityID=${activityID}`)
    .then(response => response.json())
    .then(data => {
        staffAvailability[date] = data;
        updateStaffAssignmentModal(date);
    })
    .catch(error => {
        console.error("Error fetching staff availability:", error);
    });
}

document.getElementById("assignStaffBtn").addEventListener("click", function() {
    document.getElementById("staffAssignmentModal").style.display = "block";
});



document.getElementById("closeStaffAssignmentModal").addEventListener("click", function() {
    document.getElementById("staffAssignmentModal").style.display = "none";
});