
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <div class="topnav">
        <a>Home</a>
        <a href="availability/availabilityHTML.php">Availability</a>
        <a href="#contact">Contact</a>
        <a href="#about">About</a>
    </div>
    <div class="divider"></div>
    <div class="title">
        <h1>Welcome, <?php echo htmlspecialchars($user['firstName']);?>!</h1>
    </div>
  
    

    <script src="main.js"></script> 
</body>
</html>