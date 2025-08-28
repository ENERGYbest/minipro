<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูล
    $user_id = $_SESSION['user_id'];
    $customer_name = $_POST['customer_name'] ?? null;
    $customer_phone = $_POST['customer_phone'] ?? null;
    $total_amount = $_POST['total_amount'];
    $items = json_decode($_POST['items_json'], true);

    // เริ่ม Transaction เพื่อให้แน่ใจว่าทุกอย่างสำเร็จหรือล้มเหลวพร้อมกัน
    $mysqli->begin_transaction();

    try {
        // 1. เพิ่มข้อมูลลูกค้า (ถ้ามี) หรือค้นหา
        $customer_id = null;
        if (!empty($customer_phone)) {
            // ลองหาลูกค้าจากเบอร์โทร
            $stmt = $mysqli->prepare("SELECT id FROM customers WHERE phone_number = ?");
            $stmt->bind_param("s", $customer_phone);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $customer_id = $row['id'];
            } else {
                // ถ้าไม่เจอก็สร้างใหม่
                $stmt = $mysqli->prepare("INSERT INTO customers (name, phone_number) VALUES (?, ?)");
                $stmt->bind_param("ss", $customer_name, $customer_phone);
                $stmt->execute();
                $customer_id = $mysqli->insert_id;
            }
            $stmt->close();
        }

        // 2. สร้างออเดอร์ในตาราง 'orders'
        $stmt = $mysqli->prepare("INSERT INTO orders (customer_id, user_id, total_amount, payment_status) VALUES (?, ?, ?, 'paid')");
        $stmt->bind_param("iid", $customer_id, $user_id, $total_amount);
        $stmt->execute();
        $order_id = $mysqli->insert_id; // ดึง ID ของออเดอร์ที่เพิ่งสร้าง
        $stmt->close();

        // 3. เพิ่มรายการสินค้าใน 'order_items' และอัปเดตสต็อก
        $stmt_item = $mysqli->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_per_unit) VALUES (?, ?, ?, ?)");
        $stmt_stock = $mysqli->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");

        foreach ($items as $item) {
            // เพิ่ม item
            $stmt_item->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
            $stmt_item->execute();

            // ตัดสต็อก
            $stmt_stock->bind_param("ii", $item['quantity'], $item['id']);
            $stmt_stock->execute();
        }
        $stmt_item->close();
        $stmt_stock->close();
        
        // ถ้าทุกอย่างสำเร็จ
        $mysqli->commit();
        
        // ส่งกลับไปหน้า Dashboard พร้อมข้อความสำเร็จ
        header("Location: dashboard.php?success=order_created");
        exit();

    } catch (Exception $e) {
        // ถ้ามีข้อผิดพลาดเกิดขึ้น
        $mysqli->rollback();
        die("เกิดข้อผิดพลาดในการบันทึกการขาย: " . $e->getMessage());
    }
}
?>