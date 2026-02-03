<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json; charset=UTF-8");

if (!file_exists('config.php')) {
    echo json_encode(["success" => false, "message" => "ไม่พบไฟล์ config.php ในโฟลเดอร์ api"]);
    exit;
}

include 'config.php';

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$email    = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

$response = array("success" => false, "message" => "เริ่มต้นระบบ");

if ($username != '' && $email != '' && $password != '') {
    try {
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $response["message"] = "ชื่อผู้ใช้งานหรืออีเมลนี้มีในระบบแล้วครับ";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $password, $email);

            if ($stmt->execute()) {
                $response["success"] = true;
                $response["message"] = "สมัครสมาชิกสำเร็จ";
            } else {
                $response["message"] = "SQL Error: " . $conn->error;
            }
        }
    } catch (Exception $e) {
        $response["message"] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
} else {
    $response["message"] = "ข้อมูลไม่ครบถ้วน กรุณากรอกให้ครบครับ";
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>