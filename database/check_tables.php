<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../database/database.php';

$dbo = new Database();
$tables = [
    'student_details', 'faculty_details', 'session_details',
    'course_details', 'course_registration', 'course_allotment', 'attendance_details'
];
$out = ['ok' => true, 'tables' => []];
foreach ($tables as $t) {
    try {
        $stmt = $dbo->conn->prepare("SELECT COUNT(*) AS c FROM information_schema.TABLES WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :table");
        $stmt->execute([':db' => 'attendance_db', ':table' => $t]);
        $exists = (int)$stmt->fetch(PDO::FETCH_ASSOC)['c'] > 0;
        $count = null;
        if ($exists) {
            $stmt2 = $dbo->conn->prepare("SELECT COUNT(*) AS c FROM `" . $t . "`");
            $stmt2->execute();
            $count = (int)$stmt2->fetch(PDO::FETCH_ASSOC)['c'];
        }
        $out['tables'][$t] = ['exists' => $exists, 'rows' => $count];
    } catch (Exception $e) {
        $out['tables'][$t] = ['exists' => false, 'error' => $e->getMessage()];
    }
}

// show session rows
try {
    $stmt = $dbo->conn->query("SELECT * FROM session_details");
    $out['sessions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $out['sessions'] = ['error' => $e->getMessage()];
}

echo json_encode($out, JSON_PRETTY_PRINT);

?>
