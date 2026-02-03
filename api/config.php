<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "police_db_012";
$port = 3306;

mysqli_report(MYSQLI_REPORT_OFF);

$conn = @new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        "success" => false, 
        "message" => "Database Error: " . $conn->connect_error
    ]);
    exit();
}

$conn->set_charset("utf8mb4");
?>