<?php
session_start();

$cart = $_SESSION['cart'] ?? [];
$grand_total = 0;
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตะกร้าสินค้า - Mango Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/frontend_style.css">
</head>
<body>

    <?php include 'frontend_header.php'; ?>

    <main class="container mt-5">
        <h1 class="mb-4">ตะกร้าสินค้าของคุณ</h1>
        <?php if (empty($cart)): ?>
            <div class="text-center p-5 border rounded">
                <h3>ตะกร้าของคุณว่างเปล่า</h3>
                <p>กลับไปเลือกซื้อสินค้าต่อได้เลย</p>
                <a href="index.php" class="btn btn-warning">กลับไปที่ร้านค้า</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>สินค้า</th>
                            <th class="text-end">ราคาต่อหน่วย</th>
                            <th class="text-center" style="width: 150px;">จำนวน</th>
                            <th class="text-end">ราคารวม</th>
                            <th class="text-center">ลบ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart as $id => $product): ?>
                            <?php 
                                $subtotal = $product['price'] * $product['quantity'];
                                $grand_total += $subtotal;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td class="text-end"><?php echo number_format($product['price'], 2); ?> ฿</td>
                                <td>
                                    <form action="cart_manager.php" method="POST" class="d-flex justify-content-center">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                        <input type="number" name="quantity" class="form-control text-center" value="<?php echo $product['quantity']; ?>" min="1" onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td class="text-end"><?php echo number_format($subtotal, 2); ?> ฿</td>
                                <td class="text-center">
                                    <form action="cart_manager.php" method="POST">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">&times;</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="row justify-content-end mt-4">
                <div class="col-md-5">
                    <div class="card card-body">
                        <h3 class="mb-3">สรุปรายการสั่งซื้อ</h3>
                        <div class="d-flex justify-content-between">
                            <h4>ยอดรวมทั้งหมด:</h4>
                            <h4><?php echo number_format($grand_total, 2); ?> ฿</h4>
                        </div>
                        <hr>
                        <a href="checkout.php" class="btn btn-success btn-lg w-100">ดำเนินการชำระเงิน</a>
                        <a href="index.php" class="btn btn-outline-secondary w-100 mt-2">เลือกซื้อสินค้าต่อ</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>
    
    <footer class="text-center p-4 mt-5 bg-light">
        <p>&copy; <?php echo date('Y'); ?> Mango Store. All Rights Reserved.</p>
    </footer>

</body>
</html>