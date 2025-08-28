<?php
session_start();
require_once 'db.php';
// ... (รับข้อมูลจากฟอร์ม) ...
$name = $_POST['name']; $email = $_POST['email']; $phone = $_POST['phone']; $password = $_POST['password']; $confirm_password = $_POST['confirm_password'];

if ($password !== $confirm_password) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'รหัสผ่านไม่ตรงกัน'];
    header("Location: register.php"); exit();
}

$stmt = $mysqli->prepare("SELECT id FROM customers WHERE email = ?");
// ... (โค้ดเช็คอีเมลซ้ำ)
if ($stmt->num_rows > 0) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'อีเมลนี้ถูกใช้งานแล้ว'];
    header("Location: register.php"); exit();
}
$stmt->close();

$stmt = $mysqli->prepare("SELECT id FROM customers WHERE phone_number = ?");
// ... (โค้ดเช็คเบอร์โทรซ้ำ)
if ($stmt->num_rows > 0) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'เบอร์โทรศัพท์นี้ถูกใช้งานแล้ว'];
    header("Location: register.php"); exit();
}
$stmt->close();

$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$stmt = $mysqli->prepare("INSERT INTO customers (name, email, password, phone_number) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $hashed_password, $phone);

if ($stmt->execute()) {
    $customer_id = $mysqli->insert_id;
    $_SESSION['customer_id'] = $customer_id;
    $_SESSION['customer_name'] = $name;
    $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'สมัครสมาชิกสำเร็จ!'];
    header("Location: index.php");
} else {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'];
    header("Location: register.php");
}
exit();
?>