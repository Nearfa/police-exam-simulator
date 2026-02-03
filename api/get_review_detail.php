<?php
header('Content-Type: application/json');
include 'config.php';

$history_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($history_id > 0) {
    $sql_h = "SELECT * FROM exam_history WHERE id = ?";
    $stmt_h = $conn->prepare($sql_h);
    $stmt_h->bind_param("i", $history_id);
    $stmt_h->execute();
    $history = $stmt_h->get_result()->fetch_assoc();

    if ($history) {
        $q_ids = $history['question_ids'];
        $sql_q = "SELECT * FROM questions WHERE id IN ($q_ids) ORDER BY FIELD(id, $q_ids)";
        $result_q = $conn->query($sql_q);
        
        $questions = [];
        while($row = $result_q->fetch_assoc()) {
            $questions[] = $row;
        }

        echo json_encode([
            "success" => true,
            "history" => $history,
            "questions" => $questions
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "ไม่พบประวัตินี้"]);
    }
}
?>