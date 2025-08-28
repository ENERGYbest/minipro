<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db.php';

// --- 1. การจัดการช่วงวันที่ (Date Filtering) ---
$range = $_GET['range'] ?? 'this_month';
$start_date = '';
$end_date = '';

// ตั้งค่าวันที่ตามตัวเลือกที่ส่งมา
if (isset($_GET['start_date']) && isset($_GET['end_date']) && !empty($_GET['start_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'] ?? $start_date;
    $range = 'custom';
} else {
    switch ($range) {
        case 'today':
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d');
            break;
        case 'this_week':
            $start_date = date('Y-m-d', strtotime('monday this week'));
            $end_date = date('Y-m-d', strtotime('sunday this week'));
            break;
        case 'last_7_days':
            $start_date = date('Y-m-d', strtotime('-6 days'));
            $end_date = date('Y-m-d');
            break;
        case 'this_month':
        default:
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t');
            break;
    }
}


// --- 2. ดึงข้อมูลสรุป (KPIs) ---

// ยอดขายรวม และ จำนวนออเดอร์
$stmt = $mysqli->prepare("SELECT SUM(total_amount) as total_revenue, COUNT(id) as total_orders FROM orders WHERE DATE(created_at) BETWEEN ? AND ?");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$kpi_result = $stmt->get_result()->fetch_assoc();
$total_revenue = $kpi_result['total_revenue'] ?? 0;
$total_orders = $kpi_result['total_orders'] ?? 0;
$stmt->close();

// กำไรเบื้องต้น และ จำนวนสินค้าที่ขายได้
$stmt = $mysqli->prepare("
    SELECT 
        SUM(oi.quantity * oi.price_per_unit) as total_selling_price,
        SUM(oi.quantity * p.cost_price) as total_cost_price,
        SUM(oi.quantity) as total_items_sold
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    JOIN orders o ON oi.order_id = o.id
    WHERE DATE(o.created_at) BETWEEN ? AND ?
");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$profit_result = $stmt->get_result()->fetch_assoc();
$total_profit = ($profit_result['total_selling_price'] ?? 0) - ($profit_result['total_cost_price'] ?? 0);
$total_items_sold = $profit_result['total_items_sold'] ?? 0;
$stmt->close();


// --- 3. ดึงข้อมูลสำหรับกราฟ (Chart Data) ---
$stmt = $mysqli->prepare("
    SELECT DATE(created_at) as sale_date, SUM(total_amount) as daily_total 
    FROM orders 
    WHERE DATE(created_at) BETWEEN ? AND ?
    GROUP BY DATE(created_at) 
    ORDER BY sale_date ASC
");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$chart_result = $stmt->get_result();
$chart_labels = [];
$chart_data = [];
while ($row = $chart_result->fetch_assoc()) {
    $chart_labels[] = $row['sale_date'];
    $chart_data[] = $row['daily_total'];
}
$stmt->close();

// --- 4. ดึงข้อมูลสำหรับตาราง (Detailed Table) ---
$stmt = $mysqli->prepare("
    SELECT o.id, o.created_at, c.name as customer_name, o.total_amount 
    FROM orders o
    LEFT JOIN customers c ON o.customer_id = c.id
    WHERE DATE(o.created_at) BETWEEN ? AND ?
    ORDER BY o.id DESC
");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$orders_list = $stmt->get_result();
$stmt->close();

$mysqli->close();
$current_page = 'reports';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายงาน - Mango Store</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style> .main-content { padding: 20px 40px !important; } </style>
</head>
<body>
<div class="container-fluid p-0 d-flex">
    <?php include 'sidebar.php'; ?>
    <main class="main-content flex-grow-1">
        <?php include 'header.php'; ?>

        <h1 class="mb-4">รายงานสรุปผล</h1>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="reports.php" class="row g-3 align-items-center">
                    <div class="col-auto">
                        <a href="?range=today" class="btn btn-outline-secondary">วันนี้</a>
                        <a href="?range=this_week" class="btn btn-outline-secondary">สัปดาห์นี้</a>
                        <a href="?range=this_month" class="btn btn-outline-secondary">เดือนนี้</a>
                    </div>
                    <div class="col-auto">
                        <label for="start_date" class="form-label">ตั้งแต่วันที่</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
                    </div>
                    <div class="col-auto">
                        <label for="end_date" class="form-label">ถึงวันที่</label>
                        <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>">
                    </div>
                    <div class="col-auto mt-4">
                        <button type="submit" class="btn btn-warning">ดูรายงาน</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 mb-3"><div class="card card-body text-center shadow-sm"><h5>ยอดขายรวม</h5><h2><?php echo number_format($total_revenue, 2); ?> ฿</h2></div></div>
            <div class="col-md-3 mb-3"><div class="card card-body text-center shadow-sm"><h5>กำไรเบื้องต้น</h5><h2><?php echo number_format($total_profit, 2); ?> ฿</h2></div></div>
            <div class="col-md-3 mb-3"><div class="card card-body text-center shadow-sm"><h5>จำนวนออเดอร์</h5><h2><?php echo number_format($total_orders); ?></h2></div></div>
            <div class="col-md-3 mb-3"><div class="card card-body text-center shadow-sm"><h5>สินค้าที่ขายได้</h5><h2><?php echo number_format($total_items_sold); ?> ชิ้น</h2></div></div>
        </div>
        
        <div class="card mt-4 mb-4 shadow-sm">
            <div class="card-body">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
        
        <div class="card mt-4 shadow-sm">
            <div class="card-body">
                <h3 class="card-title">รายการขายทั้งหมด</h3>
                <table class="table">
                    <thead><tr><th>ID ออเดอร์</th><th>วันที่</th><th>ลูกค้า</th><th>ยอดรวม</th></tr></thead>
                    <tbody>
                        <?php while($order = $orders_list->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $order['id']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name'] ?? 'ลูกค้าทั่วไป'); ?></td>
                            <td><?php echo number_format($order['total_amount'], 2); ?> ฿</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // รับข้อมูลจาก PHP มาใส่ใน JavaScript
    const chartLabels = <?php echo json_encode($chart_labels); ?>;
    const chartData = <?php echo json_encode($chart_data); ?>;

    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'bar', // ชนิดของกราฟ
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'ยอดขายรายวัน (บาท)',
                data: chartData,
                backgroundColor: '#FFC300', // สีเหลืองมะม่วง
                borderColor: '#d4a300',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>
</body>
</html>