<?php
header('Content-Type: application/json');
error_reporting(0); 
ini_set('display_errors', 0);

if (file_exists('config.php')) {
    include 'config.php';
} else {
    echo json_encode(["success" => false, "message" => "ไม่พบไฟล์ config.php"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id       = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $subject       = isset($_POST['subject']) ? $_POST['subject'] : '';
    $question_ids  = isset($_POST['question_ids']) ? $_POST['question_ids'] : '';
    $user_answers  = isset($_POST['user_answers']) ? $_POST['user_answers'] : '';
    $score         = isset($_POST['score']) ? (int)$_POST['score'] : 0;
    $total         = isset($_POST['total']) ? (int)$_POST['total'] : 0;
    $weakness_json = isset($_POST['weakness_json']) ? $_POST['weakness_json'] : '{}';

    if ($user_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสผู้ใช้']);
        exit;
    }

    $sql = "INSERT INTO exam_history (user_id, subject, question_ids, user_answers, score, total_questions, weakness_json) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("isssiis", $user_id, $subject, $question_ids, $user_answers, $score, $total, $weakness_json);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'บันทึกประวัติและคำตอบเรียบร้อย']);
        } else {
            echo json_encode(['success' => false, 'message' => 'บันทึกไม่สำเร็จ: ' . $stmt->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'SQL Prepare Error: ' . $conn->error]);
    }
}
?>