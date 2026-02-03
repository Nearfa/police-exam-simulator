<?php
error_reporting(0); 
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=UTF-8');

include 'config.php';

// 3. รับค่า user_id
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if ($user_id <= 0) {
    echo json_encode([]); // ส่งอาเรย์ว่างกลับไปถ้าไม่มี ID
    exit;
}

// 4. เตรียมคำสั่ง SQL (ตรวจสอบชื่อตารางให้ตรงกับในฐานข้อมูลจริงๆ)
$sql = "SELECT id, subject, score, total_questions, created_at 
        FROM exam_history 
        WHERE user_id = ? 
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $history = [];
    while($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    
    // ส่งข้อมูลออกไปเป็น JSON แท้ๆ
    echo json_encode($history, JSON_UNESCAPED_UNICODE);
} else {
    // กรณี SQL พัง ให้ส่งเป็น JSON Error แทน HTML
    echo json_encode(["success" => false, "message" => "SQL Error: " . $conn->error]);
}
?>