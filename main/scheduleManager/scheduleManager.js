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
    const activityID = document.getElementById("activityModal").dataset.activityID;
    const originalSelectedStaff = JSON.parse(document.getElementById("activityModal").dataset.originalSelectedStaff || "[]");

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

    saveActivityToServer().then(savedActivities => {
        console.log("Saved activities from server:", savedActivities);
        console.log("editingIndex:", editingIndex);
        console.log("selectedStaff:", selectedStaff);
        console.log("activityID from modal:", activityID);
        
        // For NEW activities (no editingIndex) with staff selected
        if (savedActivities && savedActivities.length > 0 && !editingIndex && selectedStaff.length > 0) {
            const newActivityID = savedActivities[savedActivities.length - 1].id;
            console.log("New activity ID:", newActivityID);
            if (newActivityID) {
                saveStaffAssignments(newActivityID, selectedStaff);
            } else {
                console.log("New activity ID is null/undefined");
            }
        } else if (activityID && selectedStaff.length > 0 && editingIndex !== undefined) {

            // EXISTING activity
            const index = parseInt(editingIndex);
            const updatedActivityID = savedActivities[index]?.id;
            
            if (!updatedActivityID){
                return;
            } 
            
            // Check what changed
            const currentSorted = [...selectedStaff].sort();
            const originalSorted = [...originalSelectedStaff].sort();
            const staffChanged = JSON.stringify(currentSorted) !== JSON.stringify(originalSorted);
            
            if (staffChanged && selectedStaff.length > 0) {
                // Staff changed and there are staff selected
                console.log("Staff assignments changed, saving...");
                saveStaffAssignments(updatedActivityID, selectedStaff);
            } else if (staffChanged && selectedStaff.length === 0 && originalSelectedStaff.length > 0) {
                // All staff were removed
                console.log("All staff removed, clearing...");
                saveStaffAssignments(updatedActivityID, []);
            } else {
                // No changes - do nothing
                console.log("No changes to staff, skipping save");
                saveStaffAssignments(updatedActivityID, selectedStaff);
            }
        }
    });

    delete document.getElementById("activityModal").dataset.originalSelectedStaff;

    clearForm();


    document.getElementById("activityForm").style.display = "none";
    document.getElementById("addActivityBtn").style.display = "block";
});

function editActivity(date, index) {
    const activity = activities[date][index];
    
    document.getElementById("activityName").value = activity.name || '';
    document.getElementById("activityStart").value = activity.startTime || '09:00';
    document.getElementById("activityEnd").value = activity.endTime || '17:00';
    document.getElementById("activityLocation").value = activity.location || '';
    document.getElementById("activityEquipment").value = activity.equipment || '';
    document.getElementById("activityNotes").value = activity.notes || '';
    document.getElementById("activityModal").dataset.activityID = activity.id || '';
    document.getElementById("activityModal").dataset.editingIndex = index;

    const originalSelectedStaff = activity.selectedStaff ? [...activity.selectedStaff] : [];
    document.getElementById("activityModal").dataset.originalSelectedStaff = JSON.stringify(originalSelectedStaff);

    if (activity.selectedStaff && activity.selectedStaff.length > 0) {
        selectedStaff = [...activity.selectedStaff];
    } else {
        selectedStaff = [];
    }

    // Show IDs immediately
    updateActivityFormSelectedStaff();

    // Then fetch real names and update when available
    const activityID = activity.id || 0;
    fetch(`staffAvailability.php?date=${date}&activityID=${activityID}`)
    .then(response => response.json())
    .then(data => {
        if (data && data.staff) {
            staffAvailability[date] = data;
            // Update the display with real names
            updateActivityFormSelectedStaff();
        }
    })
    .catch(error => {
        console.error("Error fetching staff availability:", error);
    });
    

    document.getElementById("activityForm").style.display = "block";
    document.getElementById("addActivityBtn").style.display = "none";
}


function loadActivitiesForDate(date) {

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
            formattedData[cellFormat] = data[dbDate].map(activity => ({
                id: activity.id,
                name: activity.name,
                startTime: activity.startTime.substring(0, 5),
                endTime: activity.endTime.substring(0, 5),
                location: activity.location,
                equipment: activity.equipment,
                notes: activity.notes,
                selectedStaff: activity.selectedStaff || []
            }));
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

    const activitiesForDate = activities[currentDate] || [];

    const saveData = {
        date: currentDate,
        activities: activitiesForDate.map(activity => ({
            id: activity.id || null,
            name: activity.name,
            startTime: activity.startTime,
            endTime: activity.endTime,
            location: activity.location,
            equipment: activity.equipment,
            notes: activity.notes,
            selectedStaff: activity.selectedStaff || []
        }))
    };

    return fetch('scheduleManager.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(saveData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error('Error saving:', data.error);
            alert('Failed to save: ' + data.error);
            return null;
        } else {
            console.log('Saved successfully');

            if (data.activities && data.activities[currentDate]) {
                activities[currentDate] = data.activities[currentDate].map((serverActivity, index) => {
                    const originalActivity = activities[currentDate][index] || {};
                        return {
                            id: serverActivity.id,
                            name: serverActivity.name,
                            startTime: serverActivity.startTime,
                            endTime: serverActivity.endTime,
                            location: serverActivity.location,
                            equipment: serverActivity.equipment,
                            notes: serverActivity.notes,
                            selectedStaff: originalActivity.selectedStaff || []
                        
                        }
                    });

                if (activities[currentDate].length > 0) {
                    const lastActivity = activities[currentDate][activities[currentDate].length - 1];
                    document.getElementById("activityModal").dataset.activityID = lastActivity.id;
                }
                return data.activities[currentDate];
            }

            return null;
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Error saving to database');
        return null;
    });
}


// Staff Assignment


function getStaffAvailability(date, activityID) {
    fetch(`staffAvailability.php?date=${date}&activityID=${activityID}`)
    .then(response => response.json())
    .then(data => {
        if (data && data.staff) {
            staffAvailability[date] = data;
            updateStaffAssignmentModal(date);
        } else {
            console.error("Invalid staff availability data:", data);
        }
        
    })
    .catch(error => {
        console.error("Error fetching staff availability:", error);
    });
}

function updateStaffAssignmentModal(date) {
    const data = staffAvailability[date];
    if (!data) {
        return;
    }

    document.getElementById("selectedStaffList").innerHTML = "<h4>Selected Staff</h4>";
    document.getElementById("availableStaffList").innerHTML = "<h4>Available Staff</h4>";
    document.getElementById("conditionedStaffList").innerHTML = "<h4>Conditioned Staff</h4>";
    document.getElementById("unavailableStaffList").innerHTML = "<h4>Unavailable Staff</h4>";

    data.staff.forEach(staff => {
        const staffElement = createStaffElement(staff); 

        if (selectedStaff.includes(String(staff.userID))) {
            staffElement.classList.add("selected");
            addToSelectedList(staff);
        }

        if (staff.availability === "available") {
            document.getElementById("availableStaffList").appendChild(staffElement);
        } else if (staff.availability === "conditioned") {
            document.getElementById("conditionedStaffList").appendChild(staffElement);
        } else {
            staffElement.classList.add("unavailable");
            document.getElementById("unavailableStaffList").appendChild(staffElement);
        }
    });
    window.otherAssignments = data.otherAssignments;
}

function createStaffElement(staff, isForSelectedList = false) {
    const div = document.createElement("div");
    div.className = "staff-item";
    div.dataset.userId = staff.userID;

    if (staff.roleColour) {
        div.style.backgroundColor = staff.roleColour + '75';
        div.style.borderLeftColor = staff.roleColour;
    }

    if (staff.selected) {
        div.classList.add("selected");
    }        

    const infoDiv = document.createElement("div");
    infoDiv.className = "staff-info";
    infoDiv.innerHTML = `
        <div class="staff-name">${staff.firstName} ${staff.lastName}</div>
        ${staff.conditions.length > 0 ? 
            `<div class="staff-condition">⚠️ ${staff.conditions[0].reason}</div>` : ''}
    `;
    div.appendChild(infoDiv);

    if (!isForSelectedList) {
        div.addEventListener("click", function() {
            const userID = this.dataset.userId;
            if (this.classList.contains("selected")) {
                this.classList.remove("selected");
                selectedStaff = selectedStaff.filter(id => String(id) !== String(userID));
                removeFromSelectedList(userID);
            } else {
                if (staff.availability !== "unavailable") {
                    this.classList.add("selected");
                    selectedStaff.push(String(userID));
                    addToSelectedList(staff);
                }
            }

        });
    
    }
    return div;
}

function addToSelectedList(staff) {
    const container = document.getElementById("selectedStaffList");
    
    const selectedElement = document.createElement("div");
    selectedElement.className = "staff-item selected";
    selectedElement.dataset.userId = staff.userID;
    selectedElement.setAttribute("data-user-id", staff.userID);

    if (staff.roleColor) {
        selectedElement.style.backgroundColor = staff.roleColor + '75';
        selectedElement.style.borderLeftColor = staff.roleColor;
    }

    const infoDiv = document.createElement("div");
    infoDiv.className = "staff-info";
    if (staff.conditions && staff.conditions.length > 0) {
        infoDiv.innerHTML = `<div class="staff-name">${staff.firstName} ${staff.lastName} ⚠️ ${staff.conditions[0].reason}</div>`
    } else {
        infoDiv.innerHTML = `<div class="staff-name">${staff.firstName} ${staff.lastName} </div>`;
    }
    selectedElement.appendChild(infoDiv);

    const removeBtn = document.createElement("button");
    removeBtn.className = "remove-staff-btn";
    removeBtn.textContent = "×";
    removeBtn.addEventListener("click", function(e) {
        e.stopPropagation();
        removeStaff(staff.userID);
    });
    selectedElement.appendChild(removeBtn);

    container.appendChild(selectedElement);
    
    selectedElement.style.display = "flex";
}

function removeFromSelectedList(userID) {
    const container = document.getElementById("selectedStaffList");
    const staffItem = container.querySelector(`.staff-item[data-user-id="${userID}"]`);
    if (staffItem) {
        container.removeChild(staffItem);
    }
}

function removeStaff(staffID) {
    selectedStaff = selectedStaff.filter(id => id !== staffID);
    removeFromSelectedList(staffID);
    const staffItem = document.querySelector(`.staff-item[data-user-id="${staffID}"]`);
    if (staffItem) {
        staffItem.classList.remove("selected");
    }
    updateActivityFormSelectedStaff();
}

function createStaffTag(staffID, displayName) {
    const tag = document.createElement("span");
    tag.className = "staff-tag";
    tag.textContent = displayName;

    const removeBtn = document.createElement("button");
    removeBtn.className = "remove-staff-btn";
    removeBtn.textContent = "×";
    removeBtn.addEventListener("click", function(e) {
        e.stopPropagation();
        e.preventDefault();
        removeStaff(staffID);
    });
    tag.appendChild(removeBtn);
    return tag;
}


function updateActivityFormSelectedStaff() {
    const container = document.getElementById("selectedStaffView");
    container.innerHTML = '';

    if (selectedStaff.length === 0) {
        container.innerHTML = '<p class="no-staff">No staff assigned yet.</p>';
        return;
    }

    const date = document.getElementById("activityModal").dataset.currentDate;
    const staffDataAvailable = staffAvailability[date] && staffAvailability[date].staff;

    selectedStaff.forEach(staffID => {
        let displayName;
        let warningText = "";
        
        if (staffDataAvailable) {
            const staffData = staffAvailability[date].staff.find(s => String(s.userID) === String(staffID));
            if (staffData) {
                if (staffData.conditions && staffData.conditions.length > 0) {
                    warningText = ` ⚠️ ${staffData.conditions[0].reason}`;
                }
                displayName =  `${staffData.firstName} ${staffData.lastName}`;
            } else {
                displayName = `Staff ID: ${staffID}`;
            }
        } else {
            displayName = `Staff ID: ${staffID}`;
        }
        
        container.appendChild(createStaffTag(staffID, displayName + warningText));
    });
}

document.getElementById("assignStaffBtn").addEventListener("click", function() {
    document.getElementById("staffAssignmentModal").classList.add("active");

    const date = document.getElementById("activityModal").dataset.currentDate;
    const activityID = document.getElementById("activityModal").dataset.activityID || 0;

    getStaffAvailability(date, activityID);
});



document.getElementById("closeStaffAssignmentModal").addEventListener("click", function() {
    document.getElementById("staffAssignmentModal").classList.remove("active");
});
document.getElementById("cancelStaffAssignmentBtn").addEventListener("click", function() {
    document.getElementById("staffAssignmentModal").classList.remove("active");
});

document.getElementById("saveStaffAssignments").addEventListener("click", function() {
    updateActivityFormSelectedStaff();
    document.getElementById("staffAssignmentModal").classList.remove("active");
});

function saveStaffAssignments(activityID, staffIDs) {
    if (!activityID) {
        return;
    }

    fetch('staffAvailability.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            activityID: activityID,
            staffIds: staffIDs
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error("Error saving staff assignments:", data.error);
        } else {
            console.log("Staff assignments saved successfully.");
        }
    })
    .catch(error => {
        console.error("Error saving staff assignments:", error);
    });
}
