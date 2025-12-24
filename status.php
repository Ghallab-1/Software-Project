<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/database/database.php';

try {
    $db = new Database();
    $pdo = $db->conn;
    $dbName = $pdo->query('SELECT DATABASE()')->fetchColumn();
    echo json_encode(['ok' => true, 'database' => $dbName]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
