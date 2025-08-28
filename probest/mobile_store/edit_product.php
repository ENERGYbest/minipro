<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db.php';

// 1. รับ ID จาก URL และตรวจสอบ
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Error: ไม่พบ ID สินค้า");
}
$product_id = $_GET['id'];

// 2. ดึงข้อมูลสินค้าเดิมจากฐานข้อมูล
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Error: ไม่พบสินค้าชิ้นนี้ในระบบ");
}
$product = $result->fetch_assoc();
$stmt->close();

$current_page = 'products';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขสินค้า - Mango Store</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .main-content { padding: 20px 40px !important; }
        .btn-warning { background-color: #FFC300; border-color: #FFC300; color:#34495E; font-weight: 500;}
        .btn-warning:hover { background-color: #d4a300; border-color: #d4a300; }
        .current-product-image { max-width: 150px; height: auto; border-radius: 8px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container-fluid p-0 d-flex">
        <?php include 'sidebar.php'; ?>

        <main class="main-content flex-grow-1">
            <?php include 'header.php'; ?>

            <h1 class="mb-4">แก้ไขสินค้า #<?php echo $product['id']; ?></h1>

            <div class="card">
                <div class="card-header">ฟอร์มแก้ไขข้อมูลสินค้า</div>
                <div class="card-body">
                    <form action="process_product.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">ประเภทสินค้า</label>
                                <select class="form-select" name="type" required>
                                    <option value="phone" <?php echo ($product['type'] == 'phone') ? 'selected' : ''; ?>>โทรศัพท์มือถือ</option>
                                    <option value="accessory" <?php echo ($product['type'] == 'accessory') ? 'selected' : ''; ?>>อุปกรณ์เสริม</option>
                                </select>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label">ชื่อสินค้า/รุ่น</label>
                                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ยี่ห้อ (ถ้าเป็นมือถือ)</label>
                                <input type="text" class="form-control" name="brand" value="<?php echo htmlspecialchars($product['brand'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">IMEI (ถ้าเป็นมือถือ)</label>
                                <input type="text" class="form-control" name="imei" value="<?php echo htmlspecialchars($product['imei'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="row">
                             <div class="col-md-4 mb-3">
                                <label class="form-label">ราคาทุน (บาท)</label>
                                <input type="number" step="0.01" class="form-control" name="cost_price" value="<?php echo $product['cost_price']; ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">ราคาขาย (บาท)</label>
                                <input type="number" step="0.01" class="form-control" name="selling_price" value="<?php echo $product['selling_price']; ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">จำนวนในสต็อก</label>
                                <input type="number" class="form-control" name="stock_quantity" value="<?php echo $product['stock_quantity']; ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">รูปภาพปัจจุบัน</label><br>
                            <?php if (!empty($product['image_url'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($product['image_url']); ?>" alt="Current Image" class="current-product-image">
                            <?php else: ?>
                                <p class="text-muted">ยังไม่มีรูปภาพสำหรับสินค้านี้</p>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="product_image" class="form-label">อัปโหลดรูปภาพใหม่ (ถ้าต้องการเปลี่ยน)</label>
                            <input type="file" class="form-control" id="product_image" name="product_image">
                            <div class="form-text">ถ้าไม่เลือกไฟล์ใหม่ ระบบจะใช้รูปภาพเดิม</div>
                        </div>
                        <button type="submit" class="btn btn-warning">บันทึกการเปลี่ยนแปลง</button>
                        <a href="products.php" class="btn btn-secondary">ยกเลิก</a>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
<?php $mysqli->close(); ?>