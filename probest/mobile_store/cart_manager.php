<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'add') {
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];
        $quantity = (int)($_POST['quantity'] ?? 1);

        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'name' => $product_name,
                'price' => $product_price,
                'quantity' => $quantity
            ];
        }
    }
    
    // --- ส่วนที่เพิ่มเข้ามา ---
    if ($action == 'update') {
        $product_id = $_POST['product_id'];
        $quantity = (int)$_POST['quantity'];

        // ตรวจสอบว่ามีสินค้านี้ในตะกร้า และจำนวนที่ส่งมามากกว่า 0
        if (isset($_SESSION['cart'][$product_id]) && $quantity > 0) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        } else {
            // ถ้าจำนวนเป็น 0 หรือน้อยกว่า ให้ลบออก
            unset($_SESSION['cart'][$product_id]);
        }
    }

    if ($action == 'remove') {
        $product_id = $_POST['product_id'];
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    // --- สิ้นสุดส่วนที่เพิ่มเข้ามา ---
}

// หลังจากจัดการเสร็จ ให้กลับไปที่หน้าที่ส่งมา หรือหน้าตะกร้าถ้าไม่มี referer
$redirect_url = $_SERVER['HTTP_REFERER'] ?? 'cart.php';
header('Location: ' . $redirect_url);
exit();
?>