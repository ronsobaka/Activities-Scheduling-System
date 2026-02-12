<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../main.css">
</head>
<body>
    <div class="topnav">
        <a href="../main.php">Home</a>
        <a class="active" href="#news">Availability</a>
        <a href="#contact">Contact</a>
        <a href="#about">About</a>
    </div>
    <div class="divider"></div>
    <div class="title">
        <h1>Your Availability</h1>
    </div>

    <div class="availability-section">
        <p>Please select your availability for the upcoming week:</p>
    </div>
    
    <div class="calendar-container">
        <div class="calendar-header">
            <button id="prevMonth">Previous Month</button>
            <h2 id="weekRange"></h2>
            <button id="nextMonth">Next Month</button>
        </div>
        <div id="calendarGrid" class="calendar-grid">
            <div class="calendar-weekdays">
                <div class="weekday">Monday</div>
                <div class="weekday">Tuesday</div>
                <div class="weekday">Wednesday</div>
                <div class="weekday">Thursday</div>
                <div class="weekday">Friday</div>
                <div class="weekday">Saturday</div>
                <div class="weekday">Sunday</div>
            </div>

            <div class="calendar-days" id="calendarDays">
                <!-- Calendar days will be generated here by JavaScript -->
            </div>

        </div>
    </div>  
        
    <script src="availability.js"></script> 
</body>
</html>