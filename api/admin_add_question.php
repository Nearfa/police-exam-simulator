<?php
header('Content-Type: application/json');
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject  = $_POST['subject'];
    $question = $_POST['question'];
    $c1       = $_POST['choice_1'];
    $c2       = $_POST['choice_2'];
    $c3       = $_POST['choice_3'];
    $c4       = $_POST['choice_4'];
    $answer   = $_POST['answer'];

    $sql = "INSERT INTO questions (subject, question, choice_1, choice_2, choice_3, choice_4, answer) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $subject, $question, $c1, $c2, $c3, $c4, $answer);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => $conn->error]);
    }
}
?>