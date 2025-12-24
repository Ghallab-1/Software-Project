<?php
$dsn = "mysql:host=YOUR_HOST;port=11891;dbname=attendance_db";
try {
    $pdo = new PDO($dsn, "avnadmin", "YOUR_PASSWORD", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "CONNECTED OK";
} catch (Throwable $e) {
    echo $e->getMessage();
}
