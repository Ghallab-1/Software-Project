<?php
class Database
{
  private $servername;
  private $username;
  private $password;
  private $dbname;
  private $port;
  public $conn = null;

  public function __construct() {
    // Read from environment variables
    $this->servername = getenv("DB_HOST") ?: "127.0.0.1";
    $this->username   = getenv("DB_USER") ?: "root";
    $this->password   = getenv("DB_PASS") ?: "";
    $this->dbname     = getenv("DB_NAME") ?: "attendance_db";
    $this->port       = getenv("DB_PORT") ?: 3306;

    try {
      $this->conn = new PDO(
  "mysql:host={$this->servername};
   port={$this->port};
   dbname={$this->dbname};
   charset=utf8mb4;
   sslmode=REQUIRED",

        $this->username,
        $this->password,
        [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
      );
    } catch (PDOException $e) {
      die("Database connection failed: " . $e->getMessage());
    }
  }
}
