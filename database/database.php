<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Database
{
    public $conn;

    public function __construct()
    {
        // Prefer environment variables (for production), but provide
        // sensible local defaults so the app works on XAMPP/localhost.
        $host = getenv("DB_HOST") ?: 'mysql-3f776c4a-khaled-6290.e.aivencloud.com';
        $db   = getenv("DB_NAME") ?: 'attendance_db';
        $user = getenv("DB_USER") ?: 'avnadmin';
        $pass = getenv("DB_PASS") ?: 'AVNS_RWEj07KgVFgDc4MF8Yj';
        $port = (int)(getenv("DB_PORT") ?: 11891);

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        // Only add SSL options if environment indicates a remote DB host
        // and the CA file exists. This avoids SSL-related failures on local setups.
        $ca = __DIR__ . "/ca.pem";
        if (getenv("DB_HOST") && file_exists($ca)) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = $ca;
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        }

        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
            $this->conn = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // use a clear message to help local debugging
            die("DB CONNECTION FAILED (host={$host} db={$db} user={$user}): " . $e->getMessage());
        }
    }
}
