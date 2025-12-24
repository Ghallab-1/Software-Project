<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Database
{
    public PDO $conn;

    public function __construct()
    {
        // 🔒 ENV VARIABLES ONLY (Render / Aiven)
        $host = getenv("DB_HOST");
        $db   = getenv("DB_NAME");
        $user = getenv("DB_USER");
        $pass = getenv("DB_PASSWORD");
        $port = getenv("DB_PORT");

        if (!$host || !$db || !$user || !$pass || !$port) {
            http_response_code(500);
            die("DB ENV VARIABLES MISSING");
        }

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        // 🔐 Aiven SSL
        $ca = __DIR__ . "/ca.pem";
        if (!file_exists($ca)) {
            http_response_code(500);
            die("SSL CA FILE MISSING");
        }

        $options[PDO::MYSQL_ATTR_SSL_CA] = $ca;
        $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;

        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
            $this->conn = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            die("DB CONNECTION FAILED");
        }
    }
}
