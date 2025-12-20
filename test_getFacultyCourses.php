<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../database/facultyDetails.php';

$facid = isset($_GET['facid']) ? intval($_GET['facid']) : 1;
$sessionid = isset($_GET['sessionid']) ? intval($_GET['sessionid']) : 1;

$out = ['ok' => false, 'message' => '', 'rows' => []];
try {
    $dbo = new Database();
    $fo = new faculty_details();
    $rows = $fo->getCoursesInASession($dbo, $sessionid, $facid);
    $out['ok'] = true;
    $out['rows'] = $rows;
} catch (Exception $e) {
    $out['message'] = $e->getMessage();
}

echo json_encode($out, JSON_PRETTY_PRINT);

?>