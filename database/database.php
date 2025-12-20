<?php
class Database
{
  private $servername = "127.0.0.1";
  private $username = "root";
  private $password = "";
  private $dbname = "attendance_db";
  public $conn = null;

  public function __construct() {
    try {
      $this->conn = new PDO(
        "mysql:host={$this->servername};port=3306;dbname={$this->dbname};charset=utf8mb4",
        $this->username,
        $this->password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
      );
    } catch (PDOException $e) {
      throw $e;
    }
  }
}
?>
