<?php
session_start();
$order_id = $_GET['order_id'] ?? 'N/A';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สั่งซื้อสำเร็จ - Mango Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/frontend_style.css">
</head>
<body>
    <header class="frontend-header">
         <div class="store-logo"><a href="index.php"><i class='bx bxs-store-alt logo-icon'></i> Mango Store</a></div>
    </header>

    <main class="container mt-5">
        <div class="text-center p-5 border rounded bg-light">
            <i class='bx bxs-check-circle' style="font-size: 5rem; color: green;"></i>
            <h1 class="mt-3">ขอบคุณสำหรับคำสั่งซื้อ!</h1>
            <p class="lead">เราได้รับคำสั่งซื้อของคุณเรียบร้อยแล้ว</p>
            <h3>หมายเลขคำสั่งซื้อของคุณคือ: #<?php echo htmlspecialchars($order_id); ?></h3>
            <p>เราจะดำเนินการจัดส่งให้โดยเร็วที่สุด</p>
            <a href="index.php" class="btn btn-warning mt-3">กลับไปที่หน้าแรก</a>
        </div>
    </main>

    <footer class="text-center p-4 mt-5 bg-light">
        <p>&copy; <?php echo date('Y'); ?> Mango Store. All Rights Reserved.</p>
    </footer>
</body>
</html>