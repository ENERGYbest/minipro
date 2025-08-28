<?php
session_start(); // เริ่มการใช้งาน session
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // ใช้ prepared statement เพื่อความปลอดภัย
    $sql = "SELECT id, name, password, role FROM users WHERE email = ? LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // ตรวจสอบรหัสผ่านที่เข้ารหัสไว้
        if (password_verify($password, $user['password'])) {
            // รหัสผ่านถูกต้อง!
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            header("Location: dashboard.php"); // ไปยังหน้า dashboard
            exit();
        }
    }
    
    // ถ้ามาถึงตรงนี้แปลว่า Login ไม่สำเร็จ
    header("Location: login.php?error=1");
    exit();
}
?>