-- init_attendance_db.sql
-- Create database and tables for Attendance App

CREATE DATABASE IF NOT EXISTS `attendance_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `attendance_db`;

-- Students
CREATE TABLE IF NOT EXISTS `student_details` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `roll_no` VARCHAR(20) UNIQUE,
  `name` VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Courses
CREATE TABLE IF NOT EXISTS `course_details` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(20) UNIQUE,
  `title` VARCHAR(200),
  `credit` INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Faculty
CREATE TABLE IF NOT EXISTS `faculty_details` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_name` VARCHAR(50) UNIQUE,
  `name` VARCHAR(100),
  `password` VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sessions
CREATE TABLE IF NOT EXISTS `session_details` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `year` INT,
  `term` VARCHAR(100),
  UNIQUE KEY `ux_year_term` (`year`, `term`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Course registration
CREATE TABLE IF NOT EXISTS `course_registration` (
  `student_id` INT,
  `course_id` INT,
  `session_id` INT,
  PRIMARY KEY (`student_id`,`course_id`,`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Course allotment
CREATE TABLE IF NOT EXISTS `course_allotment` (
  `faculty_id` INT,
  `course_id` INT,
  `session_id` INT,
  PRIMARY KEY (`faculty_id`,`course_id`,`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Attendance details
CREATE TABLE IF NOT EXISTS `attendance_details` (
  `faculty_id` INT,
  `course_id` INT,
  `session_id` INT,
  `student_id` INT,
  `on_date` DATE,
  `status` VARCHAR(10),
  PRIMARY KEY (`faculty_id`,`course_id`,`session_id`,`student_id`,`on_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data (6 faculty accounts matching the app expectations)
INSERT INTO `faculty_details` (`user_name`,`password`,`name`) VALUES
('ghallab','123','Youssef Ghallab'),
('sameh','123','Mario Sameh'),
('khaleed','123','Khaled Mohamed'),
('hany','123','Omar Hany'),
('wafa','123','Omar Wafa'),
('hasan','123','Youssef Hassan')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `password`=VALUES(`password`);

-- Sample sessions
INSERT INTO `session_details` (`year`,`term`) VALUES
(2025,'Spring'),
(2025,'Fall')
ON DUPLICATE KEY UPDATE `term`=VALUES(`term`);

-- Sample courses
INSERT INTO `course_details` (`code`,`title`,`credit`) VALUES
('CSCI123','Database management system lab',2),
('CSCI456','Pattern Recognition',3),
('CSCI789','Data Mining & Data Warehousing',4),
('CSCI234','Artificial Intelligence',4),
('CSCI567','Theory Of Computation',3),
('CSCI890','Demystifying Networking',1),
('MATH145','Linear Algebra',3),
('MATH267','Calculus III',4),
('MATH389','Differential Equations',3),
('MATH412','Probability and Statistics',4),
('MATH523','Discrete Mathematics',3),
('MATH678','Numerical Analysis',3)
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`), `credit`=VALUES(`credit`);

-- (You can add students, registrations and allotments as needed)

COMMIT;
