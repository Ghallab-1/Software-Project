<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../database/database.php';
$out = ['ok' => false, 'message' => '', 'rows' => []];
try {
    $dbo = new Database();
    $stmt = $dbo->conn->query('SELECT id, user_name, password, name FROM attendance_db.faculty_details');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $out['ok'] = true;
    $out['rows'] = $rows;
    $out['message'] = 'fetched faculty rows';
} catch (Exception $e) {
    $out['message'] = 'exception: ' . $e->getMessage();
}

echo json_encode($out, JSON_PRETTY_PRINT);
