<?php
session_start();

/* Unset all session variables */
$_SESSION = [];

/* Destroy the session completely */
session_destroy();

/* Always return JSON */
header('Content-Type: application/json; charset=utf-8');

echo json_encode(["status" => "logged_out"]);
exit;
