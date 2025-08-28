<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db.php'; 

// ... (ส่วนโค้ดคำนวณยอดขายต่างๆ ยังอยู่เหมือนเดิม) ...
$sql_revenue = "SELECT SUM(total_amount) as total_revenue FROM orders WHERE DATE(created_at) = CURDATE()";
$result_revenue = $mysqli->query($sql_revenue);
$revenue_data = $result_revenue->fetch_assoc();
$todays_revenue = $revenue_data['total_revenue'] ?? 0;
// ... (โค้ดส่วนอื่นๆ)
$sql_orders = "SELECT COUNT(id) as total_orders FROM orders WHERE DATE(created_at) = CURDATE()";
$result_orders = $mysqli->query($sql_orders);
$orders_data = $result_orders->fetch_assoc();
$todays_orders = $orders_data['total_orders'] ?? 0;

$sql_low_stock = "SELECT COUNT(id) as low_stock_count FROM products WHERE stock_quantity <= low_stock_threshold AND stock_quantity > 0";
$result_low_stock = $mysqli->query($sql_low_stock);
$low_stock_data = $result_low_stock->fetch_assoc();
$low_stock_items = $low_stock_data['low_stock_count'] ?? 0;

$mysqli->close();

$current_page = 'dashboard'; // กำหนดว่าหน้านี้คือหน้า 'dashboard'
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mango Store</title>
    <link rel="stylesheet" href="css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="container">
        <?php include 'sidebar.php'; // ดึง Sidebar เข้ามาแสดง ?>

        <main class="main-content">
            <?php include 'header.php'; // ดึง Header เข้ามาแสดง ?>

            <section class="dashboard-overview">
                 <h1>ภาพรวม Dashboard</h1>
                <p>ติดตามประสิทธิภาพและตัวชี้วัดสำคัญของร้านคุณ</p>

                <div class="stats-container">
                    <div class="stat-card">
                        <div class="card-icon revenue">
                            <i class='bx bx-money'></i>
                        </div>
                        <div class="card-info">
                            <span class="stat-number"><?php echo number_format($todays_revenue, 2); ?> ฿</span>
                            <span class="stat-label">ยอดขายวันนี้</span>
                        </div>
                    </div>
                     <div class="stat-card">
                        <div class="card-icon orders">
                            <i class='bx bx-box'></i>
                        </div>
                        <div class="card-info">
                            <span class="stat-number"><?php echo $todays_orders; ?></span>
                            <span class="stat-label">ออเดอร์วันนี้</span>
                        </div>
                    </div>
                    <div class="stat-card">
                         <div class="card-icon low-stock">
                            <i class='bx bxs-error-circle' ></i>
                        </div>
                        <div class="card-info">
                            <span class="stat-number"><?php echo $low_stock_items; ?></span>
                            <span class="stat-label">สินค้าใกล้หมด</span>
                            <span class="stat-comparison">ต้องสั่งซื้อเพิ่ม</span>
                        </div>
                    </div>
                </div>

                <div class="chart-card">
                    <h2>สรุปยอดขายรายวัน</h2>
                    <div class="chart-placeholder">
                        <i class='bx bx-bar-chart-alt-2'></i>
                        <p>พื้นที่สำหรับแสดงกราฟ</p>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>