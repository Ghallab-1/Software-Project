<?php
require_once(__DIR__ . "/database.php");

$dbo = new Database();

$stmt = $dbo->conn->query("SELECT DATABASE() AS db");
$row = $stmt->fetch();

echo "CONNECTED DATABASE: " . $row['db'];
