<?php
session_start();
require_once 'db.php';

// ตั้งค่า header ให้ส่งข้อมูลกลับไปเป็น JSON
header('Content-Type: application/json');

// ตรวจสอบว่ามีคำค้นหา (term) ส่งมาหรือไม่
if (!isset($_GET['term'])) {
    echo json_encode([]); // ถ้าไม่มี ส่ง array ว่างกลับไป
    exit();
}

$searchTerm = $_GET['term'];
$likeTerm = '%' . $searchTerm . '%';

// ค้นหาสินค้าที่มีชื่อหรือ imei ตรงกับคำค้น และมีของในสต็อก
$sql = "SELECT id, name, selling_price, imei, stock_quantity FROM products WHERE (name LIKE ? OR imei LIKE ?) AND stock_quantity > 0 LIMIT 10";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('ss', $likeTerm, $likeTerm);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    // สร้าง label สำหรับแสดงผล (ชื่อ + ราคา)
    $row['label'] = $row['name'] . ' (' . number_format($row['selling_price'], 2) . ' ฿)';
    $products[] = $row;
}

$stmt->close();
$mysqli->close();

// ส่งข้อมูลกลับไปเป็น JSON
echo json_encode($products);
?>