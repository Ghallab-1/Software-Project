# Attendance Management System

A PHP-based web application designed for faculty members to manage student attendance efficiently. The system allows faculty to mark attendance, view student lists, and generate comprehensive attendance reports for their assigned courses.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Project Structure](#project-structure)
- [Usage](#usage)
- [Default Credentials](#default-credentials)
- [Class Diagram](#class-diagram)
- [Technology Stack](#technology-stack)
- [File Structure](#file-structure)

##  Features

- **Faculty Authentication**: Secure login system for faculty members
- **Session Management**: Support for multiple academic sessions (e.g., Spring, Fall)
- **Course Management**: View and manage courses assigned to faculty
- **Student Attendance**: Mark attendance for students in real-time
- **Attendance Reports**: Generate comprehensive attendance reports with statistics
- **CSV Export**: Export attendance reports in CSV format
- **Responsive Design**: Modern, user-friendly interface

## Requirements

- **Web Server**: Apache (via XAMPP recommended)
- **PHP**: Version 7.0 or higher
- **MySQL**: Version 5.7 or higher
- **Browser**: Modern browser with JavaScript enabled


### Database Tables

The system uses the following tables:
- `student_details` - Student information
- `faculty_details` - Faculty credentials and information
- `course_details` - Course information
- `session_details` - Academic session information
- `course_registration` - Student course registrations
- `course_allotment` - Faculty course assignments
- `attendance_details` - Attendance records


##  Usage

### Accessing the Application

1. Start your web server (Apache) and MySQL
2. Open your browser and navigate to:
   ```
   http://localhost/attendanceapp/fronend/login.php
   ```

### Login Process

1. Enter your faculty username and password
2. Click the "LOGIN" button
3. Upon successful login, you'll be redirected to the attendance page

### Marking Attendance

1. **Select Session**: Choose an academic session from the dropdown
2. **Select Course**: Click on a course card to view its details
3. **Select Date**: Choose the date for attendance
4. **Mark Students**: Check/uncheck students to mark them present/absent
5. **Save**: Attendance is automatically saved when you check/uncheck a student

### Generating Reports

1. Select a session and course
2. Click the "Download Report" button (if available)
3. The system generates a CSV file with attendance statistics

##  Default Credentials

The system comes with sample faculty accounts (from `init_attendance_db.sql`):

| Username | Password | Name |
|----------|----------|------|
| ghallab  | 123      | Youssef Ghallab |
| sameh    | 123      | Mario Sameh |
| khaleed  | 123      | Khaled Mohamed |
| hany     | 123      | Omar Hany |
| wafa     | 123      | Omar Wafa |
| hasan    | 123      | Youssef Hassan |



##  File Structure Details

### Backend 

- **`database/database.php`**: Establishes PDO connection to MySQL
- **`database/facultyDetails.php`**: Handles faculty login and course retrieval
- **`database/sessionDetails.php`**: Manages academic sessions
- **`database/courseRegistrationDetails.php`**: Retrieves registered students
- **`database/attendanceDetails.php`**: Core attendance operations (save, retrieve, reports)

### AJAX Handlers

- **`ajaxhandler/loginAjax.php`**: Processes login requests
- **`ajaxhandler/attendanceAJAX.php`**: Handles all attendance-related AJAX requests
- **`ajaxhandler/logoutAjax.php`**: Handles logout requests

### Frontend

- **`fronend/login.php`**: Login page with authentication form
- **`fronend/attendance.php`**: Main attendance management interface
- **`fronend/css/`**: Stylesheets for UI
- **`fronend/js/`**: JavaScript for interactivity


### Common Issues

1. **Database Connection Error**
   - Check if MySQL is running
   - Verify database credentials in `database/database.php`
   - Ensure database `attendance_db` exists

2. **Page Not Found (404)**
   - Verify Apache is running
   - Check file path in browser URL
   - Ensure project is in correct directory (htdocs/www)

3. **Login Not Working**
   - Verify database has faculty records
   - Check browser console for JavaScript errors
   - Ensure session is enabled in PHP

4. **Attendance Not Saving**
   - Check browser console for AJAX errors
   - Verify database connection
   - Check PHP error logs







