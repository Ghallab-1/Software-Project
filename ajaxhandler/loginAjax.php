<?php
session_start();

/* FORCE ERROR OUTPUT */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json; charset=utf-8");

/* SIMPLE PROOF PHP IS RUNNING */
echo json_encode(["step" => "file_loaded"]);
exit;
