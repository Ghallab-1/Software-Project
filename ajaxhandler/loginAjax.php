<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json; charset=utf-8");

require_once(__DIR__ . "/../database/database.php");
require_once(__DIR__ . "/../database/facultyDetails.php");

echo json_encode(["step" => "faculty_included"]);
exit;
