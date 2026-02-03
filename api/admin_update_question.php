<?php
header('Content-Type: application/json');
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $subject = $_POST['subject'];
    $q = $_POST['question'];
    $c1 = $_POST['choice_1'];
    $c2 = $_POST['choice_2'];
    $c3 = $_POST['choice_3'];
    $c4 = $_POST['choice_4'];
    $ans = $_POST['answer'];

    $sql = "UPDATE questions SET subject=?, question=?, choice_1=?, choice_2=?, choice_3=?, choice_4=?, answer=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssii", $subject, $q, $c1, $c2, $c3, $c4, $ans, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => $conn->error]);
    }
}
?>