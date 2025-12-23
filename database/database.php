<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Database
{
    public $conn;

    public function __construct()
    {
        $host = getenv("DB_HOST");
        $db   = getenv("DB_NAME");
        $user = getenv("DB_USER");
        $pass = getenv("DB_PASS");
        $port = getenv("DB_PORT") ?: 3306;

        // Aiven SSL cert (must exist in /database/ca.pem)
        $ca = __DIR__ . "/ca.pem";

        try {
            $this->conn = new PDO(
                "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

                    // AIVEN SSL
                    PDO::MYSQL_ATTR_SSL_CA => $ca,
                    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
                ]
            );
        } catch (PDOException $e) {
            die("DB CONNECTION FAILED: " . $e->getMessage());
        }
    }
}
