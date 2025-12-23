<?php
// Read-only diagnostic script — safe to run locally or in browser
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/database.php';

$facid = isset($_GET['facid']) ? (int)$_GET['facid'] : (int)($_POST['facid'] ?? 0);
$sessionid = isset($_GET['sessionid']) ? (int)$_GET['sessionid'] : (int)($_POST['sessionid'] ?? 0);

$dbo = null;
try {
    $dbo = new Database();
} catch (Exception $e) {
    echo json_encode(['error' => 'DB connection failed', 'message' => $e->getMessage()]);
    exit;
}

$out = [];

try {
    $stmt = $dbo->conn->query("SELECT * FROM session_details ORDER BY id");
    $out['sessions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $out['sessions_error'] = $e->getMessage();
}

try {
    $stmt = $dbo->conn->query("SELECT id, code, title FROM course_details ORDER BY id");
    $out['courses'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $out['courses_error'] = $e->getMessage();
}

if ($facid > 0) {
    try {
        $stmt = $dbo->conn->prepare(
            "SELECT ca.*, cd.code AS course_code, cd.title AS course_title
             FROM course_allotment ca
             JOIN course_details cd ON ca.course_id = cd.id
             WHERE ca.faculty_id = :facid"
        );
        $stmt->execute([':facid' => $facid]);
        $out['allotments_for_faculty'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $out['allotments_error'] = $e->getMessage();
    }
}

if ($sessionid > 0) {
    try {
        $stmt = $dbo->conn->prepare("SELECT COUNT(*) AS c FROM course_allotment WHERE session_id = :sid");
        $stmt->execute([':sid' => $sessionid]);
        $out['allotment_count_for_session'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['c'];
    } catch (Exception $e) {
        $out['allotment_count_error'] = $e->getMessage();
    }
}

echo json_encode($out, JSON_PRETTY_PRINT);

?>
