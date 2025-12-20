<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/database/database.php';

$out = ['ok' => false, 'message' => '', 'details' => []];
try {
    $dbo = new Database();
    $out['details']['connected'] = true;

    // check database exists
    $stmt = $dbo->conn->query("SELECT SCHEMA() AS dbname");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $out['details']['current_db'] = $row ? $row['dbname'] : null;

    // check attendance_db presence
    $stmt = $dbo->conn->prepare("SELECT COUNT(*) AS c FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = :dbname");
    $stmt->execute([':dbname' => 'attendance_db']);
    $has = (int)$stmt->fetch(PDO::FETCH_ASSOC)['c'];
    $out['details']['attendance_db_exists'] = $has > 0;

    // check faculty_details table
    if ($has > 0) {
        $stmt = $dbo->conn->prepare("SELECT COUNT(*) AS c FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'attendance_db' AND TABLE_NAME = 'faculty_details'");
        $stmt->execute();
        $hasTable = (int)$stmt->fetch(PDO::FETCH_ASSOC)['c'];
        $out['details']['faculty_details_exists'] = $hasTable > 0;

        if ($hasTable > 0) {
            $stmt = $dbo->conn->query("SELECT COUNT(*) AS c FROM attendance_db.faculty_details");
            $out['details']['faculty_count'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['c'];
        }
    }

    $out['ok'] = true;
    $out['message'] = 'diagnostics completed';
} catch (Exception $e) {
    $out['message'] = 'exception: ' . $e->getMessage();
}

echo json_encode($out, JSON_PRETTY_PRINT);
