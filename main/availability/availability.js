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
    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    
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
                
            });     
        }

        



        calendarBody.appendChild(dayCell);     
    }
}


function generateConditionWindow(dayCell) {
    
}
