<?php
header('Content-Type: application/json');
include 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT * FROM questions WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(["success" => true, "question" => $row]);
} else {
    echo json_encode(["success" => false, "message" => "ไม่พบข้อสอบ"]);
}
?>