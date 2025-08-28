<?php
session_start();
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    header("Location: index.php");
    exit();
}

$grand_total = 0;
foreach ($cart as $product) {
    $grand_total += $product['price'] * $product['quantity'];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ชำระเงิน - Mango Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/frontend_style.css">
</head>
<body>
    <?php include 'frontend_header.php'; ?>

    <main class="container mt-5">
        <h1 class="mb-4">ข้อมูลการจัดส่งและชำระเงิน</h1>
        <div class="row">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">กรอกข้อมูลผู้รับ</h4>
                        <form action="place_order.php" method="POST">
                            <div class="mb-3">
                                <label for="customer_name" class="form-label">ชื่อ-นามสกุลผู้รับ</label>
                                <input type="text" name="customer_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="customer_phone" class="form-label">เบอร์โทรศัพท์</label>
                                <input type="tel" name="customer_phone" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="customer_address" class="form-label">ที่อยู่สำหรับจัดส่ง</label>
                                <textarea name="customer_address" class="form-control" rows="3" required></textarea>
                            </div>
                            <h4 class="mt-4">เลือกวิธีชำระเงิน</h4>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                <label class="form-check-label" for="cod">เก็บเงินปลายทาง (COD)</label>
                            </div>
                             <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="transfer" value="transfer">
                                <label class="form-check-label" for="transfer">โอนเงินผ่านธนาคาร</label>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-success btn-lg w-100">ยืนยันการสั่งซื้อ</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">สรุปรายการ</h4>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($cart as $product): ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span><?php echo htmlspecialchars($product['name']); ?> (x<?php echo $product['quantity']; ?>)</span>
                                <span><?php echo number_format($product['price'] * $product['quantity'], 2); ?> ฿</span>
                            </li>
                            <?php endforeach; ?>
                            <li class="list-group-item d-flex justify-content-between active">
                                <strong>ยอดรวมทั้งหมด</strong>
                                <strong><?php echo number_format($grand_total, 2); ?> ฿</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="text-center p-4 mt-5 bg-light">
        <p>&copy; <?php echo date('Y'); ?> Mango Store. All Rights Reserved.</p>
    </footer>
</body>
</html>