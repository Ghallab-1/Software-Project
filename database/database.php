<?php

// Load local env (XAMPP)
$envFile = __DIR__ . "/.env.php";
if (file_exists($envFile)) {
    require_once $envFile;
}

class Database
{
    public PDO $conn;

    public function __construct()
{
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    echo "<pre>";

    var_dump([
        'DB_HOST' => getenv("DB_HOST"),
        'DB_NAME' => getenv("DB_NAME"),
        'DB_USER' => getenv("DB_USER"),
        'DB_PASS' => getenv("DB_PASS"),
        'DB_PORT' => getenv("DB_PORT"),
    ]);

    echo "</pre>";
    die("STOP HERE");
}

}
