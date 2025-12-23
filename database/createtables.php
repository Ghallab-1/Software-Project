<?php

require_once(__DIR__ . "/database.php");

/**
 * Clear all rows from a table
 * NOTE: Table name cannot be bound in PDO
 */
function clearTable($dbo, $tabName)
{
    // Whitelist allowed tables (security + correctness)
    $allowedTables = [
        "course_registration",
        "course_allotment",
        "attendance_details",
        "student_details",
        "faculty_details",
        "session_details",
        "course_details"
    ];

    if (!in_array($tabName, $allowedTables)) {
        return;
    }

    $c = "DELETE FROM `$tabName`";
    $s = $dbo->conn->prepare($c);

    try {
        $s->execute();
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
}

$dbo = new Database();

/* ================= TABLE CREATION ================= */

$c = "CREATE TABLE student_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    roll_no VARCHAR(20) UNIQUE,
    name VARCHAR(50)
)";
$dbo->conn->exec($c);

$c = "CREATE TABLE course_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE,
    title VARCHAR(50),
    credit INT
)";
$dbo->conn->exec($c);

$c = "CREATE TABLE faculty_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(20) UNIQUE,
    name VARCHAR(100),
    password VARCHAR(50)
)";
$dbo->conn->exec($c);

$c = "CREATE TABLE session_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year INT,
    term VARCHAR(50),
    UNIQUE (year, term)
)";
$dbo->conn->exec($c);

$c = "CREATE TABLE course_registration (
    student_id INT,
    course_id INT,
    session_id INT,
    PRIMARY KEY (student_id, course_id, session_id)
)";
$dbo->conn->exec($c);

$c = "CREATE TABLE course_allotment (
    faculty_id INT,
    course_id INT,
    session_id INT,
    PRIMARY KEY (faculty_id, course_id, session_id)
)";
$dbo->conn->exec($c);

$c = "CREATE TABLE attendance_details (
    faculty_id INT,
    course_id INT,
    session_id INT,
    student_id INT,
    on_date DATE,
    status VARCHAR(10),
    PRIMARY KEY (faculty_id, course_id, session_id, student_id, on_date)
)";
$dbo->conn->exec($c);

/* ================= SEED DATA ================= */

$dbo->conn->exec("
INSERT INTO faculty_details (id,user_name,password,name) VALUES
(1,'rcb','123','Ram Charan Baishya'),
(2,'arindam','123','Arindam Karmakar'),
(3,'pal','123','Pallabi'),
(4,'anuj','123','Anuj Agarwal'),
(5,'mriganka','123','Mriganka Sekhar'),
(6,'manooj','123','Manooj Hazarika')
");

$dbo->conn->exec("
INSERT INTO session_details (id,year,term) VALUES
(1,2023,'SPRING SEMESTER'),
(2,2023,'AUTUMN SEMESTER')
");

$dbo->conn->exec("
INSERT INTO course_details (id,title,code,credit) VALUES
(1,'Database management system lab','CO321',2),
(2,'Pattern Recognition','CO215',3),
(3,'Data Mining & Data Warehousing','CS112',4),
(4,'ARTIFICIAL INTELLIGENCE','CS670',4),
(5,'THEORY OF COMPUTATION','CO432',3),
(6,'DEMYSTIFYING NETWORKING','CS673',1)
");

/* ================= RANDOM ALLOTMENTS ================= */

clearTable($dbo, "course_registration");
$s = $dbo->conn->prepare(
    "INSERT INTO course_registration (student_id, course_id, session_id)
     VALUES (:sid, :cid, :sessid)"
);

for ($i = 1; $i <= 24; $i++) {
    for ($j = 0; $j < 3; $j++) {
        $s->execute([":sid"=>$i, ":cid"=>rand(1,6), ":sessid"=>1]);
        $s->execute([":sid"=>$i, ":cid"=>rand(1,6), ":sessid"=>2]);
    }
}

clearTable($dbo, "course_allotment");
$s = $dbo->conn->prepare(
    "INSERT INTO course_allotment (faculty_id, course_id, session_id)
     VALUES (:fid, :cid, :sessid)"
);

for ($i = 1; $i <= 6; $i++) {
    for ($j = 0; $j < 2; $j++) {
        $s->execute([":fid"=>$i, ":cid"=>rand(1,6), ":sessid"=>1]);
        $s->execute([":fid"=>$i, ":cid"=>rand(1,6), ":sessid"=>2]);
    }
}
