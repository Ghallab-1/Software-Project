<?php
session_start();

header("Content-Type: application/json; charset=utf-8");

require_once(__DIR__ . "/../database/database.php");
require_once(__DIR__ . "/../database/facultyDetails.php");

$action = $_REQUEST["action"] ?? "";

if ($action !== "verifyUser") {
    echo json_encode(["status" => "INVALID ACTION"]);
    exit;
}

$un = trim($_POST["user_name"] ?? "");
$pw = trim($_POST["password"] ?? "");

if ($un === "" || $pw === "") {
    echo json_encode([
        "status" => "ERROR",
        "message" => "Username and password required"
    ]);
    exit;
}

try {
    $dbo = new Database();
    $fdo = new faculty_details();
    $rv = $fdo->verifyUser($dbo, $un, $pw);

    if (($rv["status"] ?? "") === "ALL OK") {
        $_SESSION["current_user"] = $rv["id"];
    }

    echo json_encode($rv);
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "ERROR",
        "message" => "Server error"
    ]);
    exit;
}
