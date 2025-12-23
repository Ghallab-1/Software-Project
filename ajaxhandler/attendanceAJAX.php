<?php
require_once(__DIR__ . '/../database/database.php');
require_once(__DIR__ . '/../database/sessionDetails.php');
require_once(__DIR__ . '/../database/facultyDetails.php');
require_once(__DIR__ . '/../database/courseRegistrationDetails.php');
require_once(__DIR__ . '/../database/attendanceDetails.php');

/* Always send JSON */
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

    $facid = $_POST['facid'] ?? null;
    $sessionid = $_POST['sessionid'] ?? null;

    if (!$facid || !$sessionid) {
        echo json_encode([]);
        exit;
    }

    $dbo = new Database();
    $fo = new faculty_details();
    $courses = $fo->getCoursesInASession($dbo, $sessionid, $facid);

    echo json_encode($courses);
    exit;
}

/* ===================== GET STUDENT LIST ===================== */
if ($action === "getStudentList") {

    $classid = $_POST['classid'];
    $sessionid = $_POST['sessionid'];
    $facid = $_POST['facid'];
    $ondate = $_POST['ondate'];

    $dbo = new Database();

    $crgo = new CourseRegistrationDetails();
    $allstudents = $crgo->getRegisteredStudents($dbo, $sessionid, $classid);

    $ado = new attendanceDetails();
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
    $ado = new attendanceDetails();

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
    $ado = new attendanceDetails();

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

/* ===================== UNKNOWN ACTION ===================== */
echo json_encode(["error" => "Invalid action"]);
exit;
