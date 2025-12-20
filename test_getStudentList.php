<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../database/courseRegistrationDetails.php';
require_once __DIR__ . '/../database/attendanceDetails.php';

$sessionid = isset($_GET['sessionid']) ? intval($_GET['sessionid']) : 1;
$classid = isset($_GET['classid']) ? intval($_GET['classid']) : 1;
$facid = isset($_GET['facid']) ? intval($_GET['facid']) : 1;
$ondate = isset($_GET['ondate']) ? $_GET['ondate'] : date('Y-m-d');

$out = ['ok' => false, 'message' => '', 'rows' => []];
try {
    $dbo = new Database();
    $cr = new CourseRegistrationDetails();
    $allstudents = $cr->getRegisteredStudents($dbo, $sessionid, $classid);
    $ad = new attendanceDetails();
    $present = $ad->getPresentListOfAClassByAFacOnADate($dbo, $sessionid, $classid, $facid, $ondate);
    // mark present
    $presentIds = [];
    foreach ($present as $p) $presentIds[] = $p['student_id'];
    foreach ($allstudents as &$s) {
        $s['isPresent'] = in_array($s['id'], $presentIds) ? 'YES' : 'NO';
    }
    $out['ok'] = true;
    $out['rows'] = $allstudents;
} catch (Exception $e) {
    $out['message'] = $e->getMessage();
}

echo json_encode($out, JSON_PRETTY_PRINT);

?>