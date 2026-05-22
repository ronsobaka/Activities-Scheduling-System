# Sailing Centre Staff Scheduling System

Full-stack web app for managing staff schedules, availability, and shift assignments for an activities centre. Built with PHP, MySQL, Bootstrap, and jQuery.

## Tech Stack

- **Backend:** PHP 7.4+ (prepared statements), MySQL
- **Frontend:** Bootstrap 5, JavaScript, jQuery
- **Email:** PHPMailer + Gmail SMTP
- **Local Dev:** XAMPP

## Features

**Authentication:** Login with brute force protection (5 attempts → 15 min lockout), email verification, password reset

**Role-Based Access:** Admin, Manager, Instructor, Trainee roles with granular permissions per feature

**Availability Calendar:** Drag-select interface to mark available/unavailable dates with condition notes (holiday, medical, exam)

**Schedule Manager:** Create activities (date, time, location, equipment), assign staff with automatic availability checking

**Staff Approval:** Manager approval workflow for new registrations

**System Settings:** Configure site name, session timeout, date format, self-registration rules, maintenance mode

**Email Notifications:** Verification emails, password reset links, shift assignment alerts

## Database Schema (Core Tables)

SQL Tables
user                -- userID, email, password, firstName, lastName, roleID, status
roles               -- roleID, roleName, colour
features            -- featureID, name (manageUsers, createSchedules, etc.)
rolepermissions     -- roleID, featureID (junction)
activities          -- id, userID (creator), activityDate, name, startTime, endTime, location
activityassignments -- id, activityID, userID, status (assigned/accepted/cancelled)
unavailableDates    -- id, userID, unavailableDate
conditions          -- id, userID, conditionDate, startTime, endTime, reason
systemsettings      -- id, siteName, sessionTimeout, dateFormat, allowSelfRegistration
login_attempts      -- id, ip_address, attempted_at

## Folder Structure

finalProject/
├── globalFunctions.php          # Auth, RBAC, DB, CSRF, settings
├── login/                       # Login + password reset flow
├── Registration/                # Signup + email verification
└── main/
    ├── dashboardHTML.php
    ├── availability/            # Drag-select calendar
    ├── scheduleManager/         # Create activities, assign staff
    ├── approveStaff/            # Manager approval queue
    ├── systemSettings/          # RBAC matrix + site config
    └── profile/                 # User profile management

1. Installation
Clone repository

git clone https://github.com/yourusername/activites-scheduling-system.git
# Move to htdocs (XAMPP), MAMP/htdocs, or /var/www/html

2. CREATE DATABASE sailing_schedule;
-- Import your existing schema or create tables from structure above
   
3. Configure Database
$host = 'localhost';
$dbname = 'sailing_schedule';
$username = 'root';
$password = '';

4. Configure Email
Add Gmail App Password to three files:

register.php

login/forgotPassword/forgotPassword.php

main/scheduleManager/staffAvailability.php

php
$mail->Username = 'your-email@gmail.com';
$mail->Password = 'xxxx xxxx xxxx xxxx';  // App password with spaces

5. Access
http://localhost/finalProject/login/loginHTML.php

## Security
bcrypt password hashing

Prepared statements on all queries

CSRF tokens on every form

Session regeneration after login

Login attempt tracking (5 fails = 15 min lockout)

Email verification required before approval

## Key Functions (globalFunctions.php)

isAuthenticated()           // Checks login + session timeout
canAccess($roleID, $feature) // RBAC permission check
getSetting($key)            // Fetches from systemsettings table
getNavbar($activePage)      // Renders navigation with site name
csrfField()                 // Outputs CSRF hidden input


