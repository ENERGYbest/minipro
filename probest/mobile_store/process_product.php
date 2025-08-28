<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db.php';

// --- ฟังก์ชันสำหรับจัดการการอัปโหลดไฟล์ ---
function handle_upload($file_input_name) {
    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == 0) {
        $target_dir = "uploads/";
        $new_filename = uniqid() . '_' . basename($_FILES[$file_input_name]["name"]);
        $target_file = $target_dir . $new_filename;
        
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ["jpg", "png", "jpeg", "gif"];
        if (!in_array($imageFileType, $allowed_types)) {
            return null; // ไม่ใช่ไฟล์รูปภาพ
        }

        if (move_uploaded_file($_FILES[$file_input_name]["tmp_name"], $target_file)) {
            return $new_filename; // คืนค่าชื่อไฟล์ใหม่
        }
    }
    return null; // ไม่มีการอัปโหลดหรือเกิดข้อผิดพลาด
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Action: เพิ่มสินค้า (Add)
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $image_filename = handle_upload('product_image');
        
        $type = $_POST['type'];
        $name = $_POST['name'];
        $brand = !empty($_POST['brand']) ? $_POST['brand'] : null;
        $imei = !empty($_POST['imei']) ? $_POST['imei'] : null;
        $cost_price = $_POST['cost_price'];
        $selling_price = $_POST['selling_price'];
        $stock_quantity = $_POST['stock_quantity'];

        $sql = "INSERT INTO products (type, name, brand, imei, cost_price, selling_price, stock_quantity, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('ssssddis', $type, $name, $brand, $imei, $cost_price, $selling_price, $stock_quantity, $image_filename);
        
        if ($stmt->execute()) {
            header("Location: products.php");
            exit();
        } else {
            die("เกิดข้อผิดพลาดในการเพิ่มข้อมูล: " . $stmt->error);
        }
        $stmt->close();
    }

    // Action: แก้ไขสินค้า (Edit)
    if (isset($_POST['action']) && $_POST['action'] == 'edit') {
        $id = $_POST['id'];
        $image_filename = handle_upload('product_image');
        
        $type = $_POST['type'];
        $name = $_POST['name'];
        $brand = !empty($_POST['brand']) ? $_POST['brand'] : null;
        $imei = !empty($_POST['imei']) ? $_POST['imei'] : null;
        $cost_price = $_POST['cost_price'];
        $selling_price = $_POST['selling_price'];
        $stock_quantity = $_POST['stock_quantity'];

        if ($image_filename) {
            // กรณีมีการอัปโหลดรูปใหม่
            $sql = "UPDATE products SET type=?, name=?, brand=?, imei=?, cost_price=?, selling_price=?, stock_quantity=?, image_url=? WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('ssssddisi', $type, $name, $brand, $imei, $cost_price, $selling_price, $stock_quantity, $image_filename, $id);
        } else {
            // กรณีไม่มีการอัปโหลดรูปใหม่ (ไม่ต้องอัปเดตคอลัมน์ image_url)
            $sql = "UPDATE products SET type=?, name=?, brand=?, imei=?, cost_price=?, selling_price=?, stock_quantity=? WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('ssssddii', $type, $name, $brand, $imei, $cost_price, $selling_price, $stock_quantity, $id);
        }
        
        if ($stmt->execute()) {
            header("Location: products.php");
            exit();
        } else {
            die("เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $stmt->error);
        }
        $stmt->close();
    }
}

// Action: ลบสินค้า (Delete)
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        header("Location: products.php");
        exit();
    } else {
        die("เกิดข้อผิดพลาดในการลบข้อมูล: " . $stmt->error);
    }
    $stmt->close();
}

$mysqli->close();
?>