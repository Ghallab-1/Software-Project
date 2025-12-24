<?php

$envFile = __DIR__ . "/.env.php";
if (file_exists($envFile)) {
    require_once $envFile;
}

class Database
{
    public PDO $conn;

    public function __construct()
    {
        $host = getenv("DB_HOST");
        $db   = getenv("DB_NAME");
        $user = getenv("DB_USER");
        $pass = getenv("DB_PASS");
        $port = getenv("DB_PORT") ?: 3306;

        if (!$host || !$db || !$user || !$pass) {
            throw new RuntimeException("DB ENV VARIABLES MISSING");
        }

        $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $ca = __DIR__ . "/ca.pem";
        if (file_exists($ca)) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = $ca;
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        }

        $this->conn = new PDO($dsn, $user, $pass, $options);
    }
}
