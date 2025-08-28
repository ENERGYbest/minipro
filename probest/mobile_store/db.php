<?php
// ไฟล์: db.php

// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$db_host = "localhost"; // หรือ 127.0.0.1
$db_user = "root";      // username ของ MySQL ใน XAMPP โดยปกติคือ root
$db_pass = "";          // password ของ MySQL ใน XAMPP โดยปกติคือว่าง
$db_name = "mobile_store_db"; // ชื่อฐานข้อมูลที่เราสร้างไว้

// สร้างการเชื่อมต่อ
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

// ตรวจสอบการเชื่อมต่อ
if ($mysqli->connect_error) {
    die("ไม่สามารถเชื่อมต่อฐานข้อมูลได้: " . $mysqli->connect_error);
}

// ตั้งค่า character set เป็น utf8 เพื่อรองรับภาษาไทย
if (!$mysqli->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
    exit();
}

// ไม่ต้องปิดการเชื่อมต่อที่นี่ เดี๋ยวไฟล์อื่นจะเรียกใช้ต่อ
// mysqli_close($mysqli);
?>