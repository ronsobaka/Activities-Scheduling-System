const currentDate = new Date();
const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
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
    console.log("Month view selected");
}

function generateWeekView() {
    console.log("Week view selected");
}

function generateDayView() {
    console.log("Day view selected");
}