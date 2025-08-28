<?php
session_start();
require_once 'db.php';

// ป้องกัน: ถ้ายังไม่ Login ให้ส่งไปหน้า Login
if (!isset($_SESSION['customer_id'])) {
    // ส่ง URL ของหน้านี้ไปด้วย เพื่อให้ Login เสร็จแล้วกลับมาถูก
    header("Location: customer_login.php?redirect_url=my_account.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// ดึงข้อมูลประวัติการสั่งซื้อของลูกค้าคนนี้
$stmt = $mysqli->prepare("
    SELECT id, created_at, total_amount, payment_status 
    FROM orders 
    WHERE customer_id = ? 
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$orders_result = $stmt->get_result();
$stmt->close();
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>บัญชีของฉัน - Mango Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/frontend_style.css">
</head>
<body>
    <?php include 'frontend_header.php'; ?>

    <main class="container mt-5">
        <h1 class="mb-4">บัญชีของฉัน</h1>
        <p class="lead">สวัสดีคุณ <?php echo htmlspecialchars($_SESSION['customer_name']); ?>, นี่คือประวัติการสั่งซื้อของคุณ</p>
        
        <div class="card">
            <div class="card-header">
                ประวัติการสั่งซื้อ
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>หมายเลขออเดอร์</th>
                                <th>วันที่สั่งซื้อ</th>
                                <th class="text-end">ยอดรวม</th>
                                <th>สถานะการชำระเงิน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders_result->num_rows > 0): ?>
                                <?php while($order = $orders_result->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                        <td class="text-end"><?php echo number_format($order['total_amount'], 2); ?> ฿</td>
                                        <td>
                                            <span class="badge bg-<?php echo ($order['payment_status'] == 'paid') ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($order['payment_status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">คุณยังไม่มีประวัติการสั่งซื้อ</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <footer class="text-center p-4 mt-5 bg-light">
        <p>&copy; <?php echo date('Y'); ?> Mango Store. All Rights Reserved.</p>
    </footer>
</body>
</html>