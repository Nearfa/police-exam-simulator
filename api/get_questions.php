<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
include 'config.php';

$subject_thai = isset($_GET['subject']) ? trim($_GET['subject']) : '';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;

$mapping = [
    "เทคโนโลยีสารสนเทศ" => "IT",
    "วิชากฎหมาย" => "Law",
    "สังคมและวัฒนธรรม" => "Law",
    "ภาษาไทย" => "Thai",
    "คณิตศาสตร์" => "Math",
    "ภาษาอังกฤษ" => "Eng"
];

$subject_to_query = isset($mapping[$subject_thai]) ? $mapping[$subject_thai] : $subject_thai;

if ($subject_to_query == 'ทั้งหมด') {
    $sql = "
        (SELECT * FROM questions WHERE subject = 'Thai' ORDER BY RAND() LIMIT 25)
        UNION ALL
        (SELECT * FROM questions WHERE subject = 'Math' ORDER BY RAND() LIMIT 30)
        UNION ALL
        (SELECT * FROM questions WHERE subject = 'Eng' ORDER BY RAND() LIMIT 30)
        UNION ALL
        (SELECT * FROM questions WHERE subject = 'IT' ORDER BY RAND() LIMIT 25)
        UNION ALL
        (SELECT * FROM questions WHERE subject = 'Law' ORDER BY RAND() LIMIT 40)
    ";
    $stmt = $conn->prepare($sql);
} 
elseif ($subject_to_query != '') {
    $stmt = $conn->prepare("SELECT * FROM questions WHERE subject = ? ORDER BY RAND() LIMIT ?");
    $stmt->bind_param("si", $subject_to_query, $limit);
} 
else {
    $stmt = $conn->prepare("SELECT * FROM questions ORDER BY RAND() LIMIT ?");
    $stmt->bind_param("i", $limit);
}

$stmt->execute();
$result = $stmt->get_result();
$questions = array();

while($row = $result->fetch_assoc()) {
    $questions[] = $row;
}

echo json_encode($questions, JSON_UNESCAPED_UNICODE);
?>