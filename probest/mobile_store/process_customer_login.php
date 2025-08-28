<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") { exit('Invalid request'); }

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $mysqli->prepare("SELECT id, name, password FROM customers WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $customer = $result->fetch_assoc();
    if (password_verify($password, $customer['password'])) {
        $_SESSION['customer_id'] = $customer['id'];
        $_SESSION['customer_name'] = $customer['name'];
        
        $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'เข้าสู่ระบบสำเร็จ!'];

        $redirect_url = $_POST['redirect_url'] ?? 'index.php';
        if(empty($redirect_url)) { $redirect_url = 'index.php'; }
        header("Location: " . $redirect_url);
        exit();
    }
}

$_SESSION['flash_message'] = ['type' => 'error', 'message' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง'];
header("Location: customer_login.php");
exit();
?>