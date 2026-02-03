<?php
header("Content-Type: application/json; charset=UTF-8");
include 'config.php';

session_start();

$user = isset($_POST['username']) ? $_POST['username'] : '';
$pass = isset($_POST['password']) ? $_POST['password'] : '';

$response = array("success" => false, "message" => "");

if ($user != '' && $pass != '') {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $dbPass = $row['password'];

        $loginSuccess = false;

        if (password_verify($pass, $dbPass)) {
            $loginSuccess = true;
        } 
        else if ($pass === $dbPass) {
            $loginSuccess = true;
        }

        if ($loginSuccess) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];

            $response["success"] = true;
            $response["user"] = array(
                "id" => $row['id'],
                "username" => $row['username'],
                "role" => $row['role']
            );
        } else {
            $response["message"] = "รหัสผ่านไม่ถูกต้อง";
        }
    } else {
        $response["message"] = "ไม่พบชื่อผู้ใช้นี้ในระบบ";
    }
} else {
    $response["message"] = "กรุณากรอกข้อมูลให้ครบถ้วน";
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>