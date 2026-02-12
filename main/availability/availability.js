
const currentDate = new Date();
let currentMonth = currentDate.getMonth();
let currentYear = currentDate.getFullYear();

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


    let isMouseDown = false;
    let wasDragged = false;
    let dragAction = null;

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
        dayCell.classList.add('day' , 'available');
        dayCell.textContent = day;

        dayCell.addEventListener('mousedown', function(e) {
            e.preventDefault();

            if (this.classList.contains('available')) {
                dragAction = 'makeUnavailable';
                this.classList.remove('available');
                this.classList.add('unavailable');
            } else {
                dragAction = 'makeAvailable';
                this.classList.remove('unavailable');
                this.classList.add('available');
            }
        });

        dayCell.addEventListener('mouseenter', function() {
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

        calendarBody.appendChild(dayCell);     
    }
}

