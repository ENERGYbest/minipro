<?php
session_start();
require_once 'db.php';

// ตรวจสอบว่าเป็นการส่งข้อมูลมา และตะกร้าไม่ว่าง
if ($_SERVER["REQUEST_METHOD"] != "POST" || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

// รับข้อมูลจากฟอร์ม
$customer_name = $_POST['customer_name'];
$customer_phone = $_POST['customer_phone'];
$customer_address = $_POST['customer_address'];
$payment_method = $_POST['payment_method'];

// คำนวณยอดรวมจากตะกร้าอีกครั้งเพื่อความปลอดภัย
$cart = $_SESSION['cart'];
$total_amount = 0;
foreach ($cart as $product) {
    $total_amount += $product['price'] * $product['quantity'];
}

// กำหนดสถานะการชำระเงิน
$payment_status = ($payment_method == 'transfer') ? 'unpaid' : 'unpaid'; // สำหรับ COD ก็ถือว่ายังไม่จ่ายจนกว่าจะได้รับของ

// ใช้ Transaction เพื่อให้แน่ใจว่าถ้ามีอะไรผิดพลาด ข้อมูลจะไม่ถูกบันทึกแค่บางส่วน
$mysqli->begin_transaction();

try {
    // 1. ค้นหาหรือสร้างข้อมูลลูกค้าใหม่
    $customer_id = null;
    $stmt = $mysqli->prepare("SELECT id FROM customers WHERE phone_number = ?");
    $stmt->bind_param("s", $customer_phone);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $customer_id = $row['id'];
    } else {
        $stmt = $mysqli->prepare("INSERT INTO customers (name, phone_number, address) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $customer_name, $customer_phone, $customer_address);
        $stmt->execute();
        $customer_id = $mysqli->insert_id;
    }
    $stmt->close();

    // 2. สร้างออเดอร์ในตาราง 'orders'
    // user_id = 1 สมมติว่าเป็น Admin หรือ ID ของระบบ
    $stmt = $mysqli->prepare("INSERT INTO orders (customer_id, user_id, total_amount, payment_status) VALUES (?, 1, ?, ?)");
    $stmt->bind_param("ids", $customer_id, $total_amount, $payment_status);
    $stmt->execute();
    $order_id = $mysqli->insert_id;
    $stmt->close();

    // 3. เพิ่มรายการสินค้าใน 'order_items' และอัปเดตสต็อก
    $stmt_item = $mysqli->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_per_unit) VALUES (?, ?, ?, ?)");
    $stmt_stock = $mysqli->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
    foreach ($cart as $id => $item) {
        // เพิ่ม item
        $stmt_item->bind_param("iiid", $order_id, $id, $item['quantity'], $item['price']);
        $stmt_item->execute();
        // ตัดสต็อก
        $stmt_stock->bind_param("ii", $item['quantity'], $id);
        $stmt_stock->execute();
    }
    $stmt_item->close();
    $stmt_stock->close();
    
    // ถ้าทุกอย่างสำเร็จ ให้ยืนยันการเปลี่ยนแปลงทั้งหมด
    $mysqli->commit();
    
    // ล้างตะกร้าสินค้า
    unset($_SESSION['cart']);
    
    // ส่งไปหน้า "สั่งซื้อสำเร็จ"
    header("Location: order_success.php?order_id=" . $order_id);
    exit();

} catch (Exception $e) {
    // ถ้ามีข้อผิดพลาดเกิดขึ้น ให้ยกเลิกการเปลี่ยนแปลงทั้งหมด
    $mysqli->rollback();
    die("เกิดข้อผิดพลาดในการบันทึกคำสั่งซื้อ: " . $e->getMessage());
}
?>