<?php
session_start();

// ลบ session ของลูกค้า
unset($_SESSION['customer_id']);
unset($_SESSION['customer_name']);

// ส่งกลับไปหน้าแรก
header("Location: index.php");
exit();
?>