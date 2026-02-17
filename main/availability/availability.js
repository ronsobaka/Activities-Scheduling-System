const today = new Date();
const todayDate = today.getDate();
const todayMonth = today.getMonth();
const todayYear = today.getFullYear();

let currentMonth = today.getMonth();
let currentYear = today.getFullYear();

let isMouseDown = false;
let wasDragged = false;
let dragAction = null;
let unavailableDates = {};
let conditionsData = {};
const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];



const nextMonthBtn = document.getElementById("nextMonth").addEventListener("click", function () {
    currentMonth += 1;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear += 1;
    }
    generateCalendar(currentMonth, currentYear);
});

const prevMonthBtn = document.getElementById("prevMonth").addEventListener("click", function() {
    currentMonth -= 1;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear -= 1;
    }
    generateCalendar(currentMonth, currentYear);
})

generateCalendar(currentMonth, currentYear);

function generateCalendar(month, year) {
    // Logic to generate days grid
    // First day of month, total days, etc.

    // Clear previous calendar
    const calendarBody = document.getElementById('calendarDays');
    calendarBody.innerHTML = '';

    const weekRange = document.getElementById('weekRange');    
    weekRange.textContent = `${monthNames[month]} ${year}`;
    
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const firstDay = new Date(year, month, 1).getDay();
    
    let adjustedFirstDay = firstDay - 1;
    if (adjustedFirstDay <= -1) {
        adjustedFirstDay = 6;
    }
    
    for (let i = 0; i < adjustedFirstDay; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.classList.add('day', 'empty');
        calendarBody.appendChild(emptyCell);
    }



    document.addEventListener("mousedown", function() {
        isMouseDown = true;
        wasDragged = false;
    });

    document.addEventListener("mouseup", function() {
        isMouseDown = false;
    })

    document.addEventListener("mouseleave", function() {
        isMouseDown = false;
    });



    for (let day = 1; day <= daysInMonth; day++) {

        const dayCell = document.createElement('div');

        const cellDate = new Date(year, month, day);
        const todayDateObj = new Date(todayYear, todayMonth, todayDate);

        if (cellDate < todayDateObj) {
            dayCell.classList.remove('available', 'today', 'unavailable');
            dayCell.classList.add('day', 'past');
        } else if (year === todayYear && month === todayMonth && day === todayDate) {
            dayCell.classList.add('day', 'today');
        } else {
            const dateKey = `${year}-${month + 1}-${day}`;
            if (unavailableDates[dateKey]) {
                dayCell.classList.add('day', 'unavailable');
            } else {
                dayCell.classList.add('day', 'available');
            }
        }

        dayCell.textContent = day;
        dayCell.addEventListener('mousedown', function(e) {
            e.preventDefault();
            
            if (this.classList.contains('past') || this.classList.contains('today')) {
                return;
            }

            const dateKey = `${year}-${month + 1}-${day}`;

            if (this.classList.contains('available')) {
                dragAction = 'makeUnavailable';
                this.classList.remove('available');
                this.classList.add('unavailable');
                unavailableDates[dateKey] = true;
            } else {
                dragAction = 'makeAvailable';
                this.classList.remove('unavailable');
                this.classList.add('available');
                delete unavailableDates[`${year}-${month + 1}-${day}`];
            }
        });

        dayCell.addEventListener('mouseenter', function() {

            if (this.classList.contains('past') || this.classList.contains('today')) {
                return;
            }

            if (isMouseDown) {
                if (dragAction === 'makeUnavailable') {
                    this.classList.remove('available');
                    this.classList.add('unavailable');
                } else {
                    this.classList.remove('unavailable');
                    this.classList.add('available');
                }
                wasDragged = true;
            }
        });

        if (!dayCell.classList.contains('past') && (!dayCell.classList.contains('today')) ) {

            const conditionBtn = document.createElement('span');
            conditionBtn.classList.add('conditionBtn');
            conditionBtn.textContent = 'Edit';
            dayCell.style.position = 'relative';
            dayCell.appendChild(conditionBtn);

            conditionBtn.addEventListener('mouseenter', function(e) {
                e.stopPropagation();
            });

            conditionBtn.addEventListener('mousedown', function(e) {
                e.stopPropagation();
            });

            conditionBtn.addEventListener('click', function() {
                generateConditionWindow(cellDate);
            });     
        }
        calendarBody.appendChild(dayCell);     
    }
}


/*Condition WIndow*/

document.getElementById("addConditionBtn").addEventListener("click", function() {
    document.getElementById("conditionForm").style.display = 'block';
    this.style.display = 'none';
})

document.getElementById("saveConditionBtn").addEventListener("click", function(){
    const start = document.getElementById("conditionStart").value;
    const end = document.getElementById("conditionEnd").value;
    const reason = document.getElementById("conditionReason").value;

    if (!reason.trim()) {
        document.getElementById("conditionReason").style.border = "2px solid red";
        return;
    } else {
        document.getElementById("conditionReason").style.border = "";
    }

    const titleText = document.getElementById("conditionsTitle").textContent;
    const dateKey = titleText.replace("Conditions for ", "");                         //getting date
     

    if (!conditionsData[dateKey]) {                                                  //initialising/adding data
        conditionsData[dateKey] = [];
    }
    conditionsData[dateKey].push({start, end, reason});

    loadConditionsForDate(dateKey);

    document.getElementById('conditionStart').value = '09:00';
    document.getElementById('conditionEnd').value = '17:00';
    document.getElementById('conditionReason').value = '';
    document.getElementById("conditionForm").style.display = 'none';
    document.getElementById("addConditionBtn").style.display = 'block';            //clearing form

});

function loadConditionsForDate(dateKey) {  
    const conditionsList = document.querySelector('.conditions-list')
    conditionsList.innerHTML = '';

    if (conditionsData[dateKey]) {
        conditionsData[dateKey].forEach((condition, index) => {
            addConditionItem(condition.start, condition.end, condition.reason, dateKey, index);
        });
    }
}

function addConditionItem(start, end, reason, dataKey, index) {
    const conditionItem = document.createElement("div");
    conditionItem.className = "condition-item";
    
    const timeSpan = document.createElement("strong");
    timeSpan.textContent = `${start} - ${end}`;

    const reasonP = document.createElement("p");
    reasonP.textContent = reason;

    const editBtn = document.createElement("button");
    editBtn.textContent = "Edit";
    editBtn.className = "edit-condition";

    const deleteBtn = document.createElement("button");
    deleteBtn.textContent = "🗑️";
    deleteBtn.className = "delete-condition";

    editBtn.addEventListener("click", function() {
        document.getElementById("conditionStart").value = start;
        document.getElementById("conditionEnd").value = end;
        document.getElementById("conditionReason").value = reason;

        conditionsData[dataKey].splice(index, 1);
        conditionItem.remove();

        document.getElementById("conditionForm").style.display = "block";
        document.getElementById("addConditionBtn").style.display = "none";
    })

    deleteBtn.addEventListener("click", function() {
        conditionItem.remove();

        const remainingConditions = document.querySelectorAll(".condition-item").length;

        if (remainingConditions === 0) {
            //add in updating dayCell
        }
    })

    conditionItem.appendChild(timeSpan);
    conditionItem.appendChild(reasonP);
    conditionItem.appendChild(editBtn);
    conditionItem.appendChild(deleteBtn);

    document.querySelector('.conditions-list').appendChild(conditionItem);
}
    
function generateConditionWindow(cellDate) {
    document.querySelector('.conditions-container').classList.add('active');

    const conditionsTitle = document.getElementById("conditionsTitle");
    
    const day = cellDate.getDate();
    const month = cellDate.getMonth();
    const year = cellDate.getFullYear();

    const dateKey = `${day} ${monthNames[month]}, ${year}`;
    conditionsTitle.textContent = `Conditions for ${day} ${monthNames[month]}, ${year}`;

    loadConditionsForDate(dateKey);
}