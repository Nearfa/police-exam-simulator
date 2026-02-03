<?php
header("Content-Type: application/json; charset=UTF-8");
include 'config.php';

$email = isset($_POST['email']) ? $_POST['email'] : '';
$step = isset($_POST['step']) ? $_POST['step'] : 1;
$new_pass = isset($_POST['new_password']) ? $_POST['new_password'] : '';

$res = ["success" => false, "message" => ""];

if ($step == 1) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $res["success"] = true;
    } else {
        $res["message"] = "ไม่พบอีเมลนี้ในระบบสมาชิก";
    }
} else {
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $new_pass, $email);
    if ($stmt->execute()) {
        $res["success"] = true;
    } else {
        $res["message"] = "ไม่สามารถเปลี่ยนรหัสผ่านได้";
    }
}

echo json_encode($res, JSON_UNESCAPED_UNICODE);
?>