<?php

require_once(__DIR__ . '/../database/database.php');
require_once(__DIR__ . '/../database/sessionDetails.php');
require_once(__DIR__ . '/../database/facultyDetails.php');
require_once(__DIR__ . '/../database/courseRegistrationDetails.php');
require_once(__DIR__ . '/../database/attendanceDetails.php');

// ensure PHP session is available for AJAX handlers
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

function createCSVReport($list, $filename)
{
    $finalFileName = __DIR__ . '/../' . $filename;
    $fp = fopen($finalFileName, "w");
    foreach ($list as $line) {
        fputcsv($fp, $line);
    }
    fclose($fp);
}

try {
    if (!isset($_REQUEST['action'])) {
        echo json_encode(["error" => "No action provided"]);
        exit;
    }

    $action = $_REQUEST['action'];

/* ===================== GET SESSIONS ===================== */
if ($action === "getSession") {
    $dbo = new Database();
    $sobj = new SessionDetails();
    echo json_encode($sobj->getSessions($dbo));
    exit;
}

/* ===================== GET FACULTY COURSES ===================== */
if ($action === "getFacultyCourses") {

    $facid = (int)($_POST['facid'] ?? 0);
    $sessionid = (int)($_POST['sessionid'] ?? 0);

    // fallback to server-side logged-in faculty id if client didn't send it
    if ($facid === 0 && isset($_SESSION['current_user'])) {
        $facid = (int)$_SESSION['current_user'];
    }

    if ($facid === 0 || $sessionid === 0) {
        echo json_encode([]);
        exit;
    }

    $dbo = new Database();
    
    $fo  = new faculty_details(); // ✅ CASE FIXED

    $courses = $fo->getCoursesInASession($dbo, $sessionid, $facid);
    // if nothing returned, include debug info to help diagnose
    if (empty($courses)) {
        echo json_encode([]);
        exit;
    }

    echo json_encode($courses);
    exit;
}

/* ===================== GET STUDENT LIST ===================== */
if ($action === "getStudentList") {

    $classid   = (int)$_POST['classid'];
    $sessionid = (int)$_POST['sessionid'];
    $facid     = (int)$_POST['facid'];
    $ondate    = $_POST['ondate'];

    $dbo = new Database();

    $crgo = new CourseRegistrationDetails();
    $allstudents = $crgo->getRegisteredStudents($dbo, $sessionid, $classid);

    $ado = new AttendanceDetails();
    $presentStudents = $ado->getPresentListOfAClassByAFacOnADate(
        $dbo,
        $sessionid,
        $classid,
        $facid,
        $ondate
    );

    foreach ($allstudents as &$student) {
        $student['isPresent'] = 'NO';
        foreach ($presentStudents as $present) {
            if ($student['id'] == $present['student_id']) {
                $student['isPresent'] = 'YES';
                break;
            }
        }
    }

    echo json_encode($allstudents);
    exit;
}

/* ===================== SAVE ATTENDANCE ===================== */
if ($action === "saveattendance") {

    $dbo = new Database();
    $ado = new AttendanceDetails();

    $rv = $ado->saveAttendance(
        $dbo,
        $_POST['sessionid'],
        $_POST['courseid'],
        $_POST['facultyid'],
        $_POST['studentid'],
        $_POST['ondate'],
        $_POST['ispresent']
    );

    echo json_encode($rv);
    exit;
}

/* ===================== DOWNLOAD REPORT ===================== */
if ($action === "downloadReport") {

    $dbo = new Database();
    $ado = new AttendanceDetails();

    $list = $ado->getAttenDanceReport(
        $dbo,
        $_POST['sessionid'],
        $_POST['classid'],
        $_POST['facid']
    );

    if (!$list || !is_array($list)) {
        echo json_encode(["error" => "No data returned"]);
        exit;
    }

    $filename = "report.csv";
    createCSVReport($list, $filename);

    echo json_encode(["filename" => "/" . $filename]);
    exit;
}

    echo json_encode(["error" => "Invalid action"]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error", "details" => $e->getMessage()]);
    exit;
}


