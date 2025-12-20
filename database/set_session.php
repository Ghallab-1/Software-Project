<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../database/database.php';

$dbo = new Database();
$out = ['ok' => false, 'message' => '', 'sessions' => []];

try {
    $dbo->conn->beginTransaction();

    // remove existing sessions
    $dbo->conn->exec("DELETE FROM session_details");

    // insert single requested session with id=1
    $stmt = $dbo->conn->prepare("INSERT INTO session_details (id,year,term) VALUES (1,2025,:term)");
    $stmt->execute([':term' => '2025-fall']);

    // point all registrations/allotments/attendance to the single session id
    $dbo->conn->exec("UPDATE course_registration SET session_id = 1");
    $dbo->conn->exec("UPDATE course_allotment SET session_id = 1");
    $dbo->conn->exec("UPDATE attendance_details SET session_id = 1");

    // ensure auto_increment continues after id=1
    $dbo->conn->exec("ALTER TABLE session_details AUTO_INCREMENT = 2");

    $dbo->conn->commit();

    // return current sessions
    $stmt2 = $dbo->conn->query("SELECT * FROM session_details");
    $out['sessions'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    $out['ok'] = true;
    $out['message'] = 'session set to 2025-fall; related tables updated to session_id=1';
} catch (Exception $e) {
    try { $dbo->conn->rollBack(); } catch (Exception $ee) {}
    $out['message'] = 'error: ' . $e->getMessage();
}

echo json_encode($out, JSON_PRETTY_PRINT);

?>
