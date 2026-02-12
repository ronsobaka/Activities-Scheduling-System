
const currentDate = new Date();
const currentMonth = currentDate.getMonth();
const currentYear = currentDate.getFullYear();

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

    for (let i = 0; i < firstDay; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.classList.add('empty-cell');
        calendarBody.appendChild(emptyCell);
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const dayCell = document.createElement('div');
        dayCell.classList.add('day');
        dayCell.textContent = day;
        calendarBody.appendChild(dayCell);
        
    }
}