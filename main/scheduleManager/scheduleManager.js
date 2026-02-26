const currentDate = new Date();
const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
const days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
let day = currentDate.getDate();
let month = monthNames[currentDate.getMonth()];
let year = currentDate.getFullYear();
let selectedView = "month";
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
    saveActivity();
    document.getElementById("activityForm").style.display = "none";
    document.getElementById("addActivityBtn").style.display = "block";
})

function loadActivitiesForDate(date) {
    console.log("Loading activities for:", date);
    document.querySelector(".no-activities").style.display = "block";
}

function saveActivity() {

}