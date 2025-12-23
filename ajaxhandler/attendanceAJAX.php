
header('Content-Type: application/json');
<?php
require_once(__DIR__ . '/../database/database.php');
require_once(__DIR__ . '/../database/sessionDetails.php');
require_once(__DIR__ . '/../database/facultyDetails.php');
require_once(__DIR__ . '/../database/courseRegistrationDetails.php');
require_once(__DIR__ . '/../database/attendanceDetails.php');

function createCSVReport($list, $filename)
{
    // Save file in project root (writable in Render)
    $finalFileName = __DIR__ . '/../' . $filename;

    try {
        $fp = fopen($finalFileName, "w");
        foreach ($list as $line) {
            fputcsv($fp, $line);
        }
        fclose($fp);
    } catch (Exception $e) {
        error_log("CSV creation error: " . $e->getMessage());
    }
}

if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];

    if ($action == "getSession") {
        $dbo = new Database();
        $sobj = new SessionDetails();
        echo json_encode($sobj->getSessions($dbo));
    }

   if ($action == "getFacultyCourses") {
    echo json_encode([
        "facid" => $_POST['facid'] ?? null,
        "sessionid" => $_POST['sessionid'] ?? null
    ]);
    exit;
}

    if ($action == "getStudentList") {
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
    }

    if ($action == "saveattendance") {
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
    }

    if ($action == "downloadReport") {
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
    }
}
?>
