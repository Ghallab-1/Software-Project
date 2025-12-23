-- populate_sample_data.sql
USE defaultdb;


-- Insert 24 sample students (id will auto-increment)
INSERT INTO `student_details` (`roll_no`,`name`) VALUES
('231001234','Ahmed mohamed'),
('221001015','Fatima abdullah'),
('241001098','Mohammed basha'),
('231000157','Sara essam'),
('221000111','Omar hany'),
('241000123','Layla khaled'),
('231001078','Youssef Ahmed'),
('221001011','Aisha magdy'),
('231001055','Khalid muhammad'),
('231000010','Noor alaeen'),
('221000010','wael gomaa'),
('241000010','Mohamed salah'),
('231001010','omar marmoush'),
('221001010','Zainab ahmed ezz'),
('241001010','ahmed sadd'),
('231000001','leo messi'),
('221000001','mohamed el qahtany'),
('241000001','boshmhandes osama'),
('231001100','ibrahim hgar'),
('221001100','Hany Mohamed'),
('241001100','Faisal elseoudi'),
('231010000','youssef hassan'),
('221010000','Mario Sameh'),
('241010000','youssef ghallab')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Register all students to all courses for both sessions (Spring and Fall)
-- Session 1 = Spring, Session 2 = Fall
INSERT IGNORE INTO course_registration (student_id, course_id, session_id)
SELECT s.id, c.id, 1 FROM student_details s CROSS JOIN course_details c
UNION
SELECT s.id, c.id, 2 FROM student_details s CROSS JOIN course_details c;

-- Assign courses to faculties (course_allotment) for both sessions
-- Assign ALL courses to ALL faculty for both sessions
-- Assign to Spring (session 1)
INSERT IGNORE INTO course_allotment (faculty_id, course_id, session_id)
SELECT f.id as faculty_id, c.id as course_id, 1 as session_id
FROM (SELECT id FROM faculty_details) f
CROSS JOIN (SELECT id FROM course_details) c;

-- Assign to Fall (session 2)
INSERT IGNORE INTO course_allotment (faculty_id, course_id, session_id)
SELECT f.id as faculty_id, c.id as course_id, 2 as session_id
FROM (SELECT id FROM faculty_details) f
CROSS JOIN (SELECT id FROM course_details) c;

COMMIT;
