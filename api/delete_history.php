<?php
header('Content-Type: application/json');
include 'config.php';

$id = $_GET['id'];
$user_id = $_GET['user_id'];

$sql = "DELETE FROM exam_history WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $user_id);

if($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>