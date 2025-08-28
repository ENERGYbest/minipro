<?php
require_once 'db.php';

// 1. รับ ID สินค้าจาก URL และตรวจสอบ
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // ถ้าไม่มี ID หรือ ID ไม่ใช่ตัวเลข ให้แสดงว่าไม่พบหน้า
    http_response_code(404);
    die("Error: ไม่พบสินค้าที่ระบุ");
}
$product_id = (int)$_GET['id'];

// 2. ดึงข้อมูลสินค้าจากฐานข้อมูล
$stmt = $mysqli->prepare("SELECT * FROM products WHERE id = ? AND stock_quantity > 0");
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    die("Error: ไม่พบสินค้าชิ้นนี้ในระบบ หรือสินค้าหมดสต็อก");
}
$product = $result->fetch_assoc();
$stmt->close();
$mysqli->close();

// ตั้งชื่อ Page Title ตามชื่อสินค้า
$page_title = htmlspecialchars($product['name']) . " - Mango Store";
include 'layout_header.php';
include 'frontend_header.php';
?>

<main class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <div class="product-detail-image">
                <?php if (!empty($product['image_url'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($product['image_url']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php else: ?>
                    <div class="placeholder-image">Image Placeholder</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-6">
            <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="product-brand text-muted"><?php echo htmlspecialchars($product['brand'] ?? ''); ?></p>
            <h2 class="product-price"><?php echo number_format($product['selling_price'], 2); ?> ฿</h2>
            
            <div class="product-stock my-3">
                <span class="badge bg-success">มีสินค้าในสต็อก</span>
            </div>

            <?php if ($product['type'] == 'phone'): ?>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>สี:</strong> <?php echo htmlspecialchars($product['color'] ?? '-'); ?></li>
                <li class="list-group-item"><strong>ความจุ:</strong> <?php echo htmlspecialchars($product['storage_capacity'] ?? '-'); ?></li>
                <li class="list-group-item"><strong>สภาพ:</strong> <?php echo ($product['condition'] == 'new') ? 'เครื่องใหม่' : 'มือสอง'; ?></li>
            </ul>
            <?php endif; ?>

            <form action="cart_manager.php" method="POST" class="mt-4">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                <input type="hidden" name="product_price" value="<?php echo $product['selling_price']; ?>">
                
                <div class="row">
                    <div class="col-md-4">
                        <label for="quantity" class="form-label">จำนวน:</label>
                        <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                    </div>
                    <div class="col-md-8 d-flex align-items-end">
                        <button type="submit" class="btn btn-warning btn-lg w-100"><i class='bx bxs-cart-add'></i> หยิบใส่ตะกร้า</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include 'layout_footer.php'; ?>  