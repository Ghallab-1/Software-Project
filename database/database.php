<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Database
{
  private $servername;
  private $username;
  private $password;
  private $dbname;
  private $port;
  public $conn = null;

  public function __construct() {

    $this->servername = getenv("DB_HOST");
    $this->username   = getenv("DB_USER");
    $this->password   = getenv("DB_PASS");
    $this->dbname     = getenv("DB_NAME");
    $this->port       = getenv("DB_PORT") ?: 3306;

    try {

  // 👇 ADD THIS LINE (before new PDO)
  $caPath = __DIR__ . "/ca.pem";

  $this->conn = new PDO(
    "mysql:host={$this->servername};
     port={$this->port};
     dbname={$this->dbname};
     charset=utf8mb4",
    $this->username,
    $this->password,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

      // ✅ AIVEN SSL SETTINGS
      PDO::MYSQL_ATTR_SSL_CA => $caPath,
      PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
    ]
  );

} catch (PDOException $e) {
  die("Database connection failed: " . $e->getMessage());
}

  }
}
