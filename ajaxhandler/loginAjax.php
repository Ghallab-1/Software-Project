<?php
session_start();
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/../database/database.php";
require_once __DIR__ . "/../database/facultyDetails.php";

$action = isset($_REQUEST["action"]) ? $_REQUEST["action"] : "";

if ($action === "verifyUser") {
  $un = isset($_POST["user_name"]) ? trim($_POST["user_name"]) : "";
  $pw = isset($_POST["password"]) ? trim($_POST["password"]) : "";

  try {
    $dbo = new Database();
    $fdo = new faculty_details();
    $rv = $fdo->verifyUser($dbo, $un, $pw);

    if (isset($rv["status"]) && $rv["status"] === "ALL OK") {
      if (session_status() === PHP_SESSION_NONE) session_start();
      $_SESSION["current_user"] = isset($rv["id"]) ? $rv["id"] : null;
    }

    echo json_encode($rv);
    exit;
  } catch (Exception $e) {
    http_response_code(500);
    $msg = $e->getMessage();
    echo json_encode(["status" => "ERROR", "message" => "Server error: " . $msg]);
    exit;
  }
}

echo json_encode(["status" => "INVALID ACTION"]);
exit;
?>