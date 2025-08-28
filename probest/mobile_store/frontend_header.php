<?php
// ไฟล์นี้จะถูก include ไปใช้ จึงไม่ต้องมี session_start() ซ้ำ
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<header class="frontend-header">
    <div class="store-logo">
        <a href="index.php"><i class='bx bxs-store-alt logo-icon'></i> Mango Store</a>
    </div>
    <div class="d-flex align-items-center">
        <div class="cart-icon me-3">
            <a href="cart.php">
                <i class='bx bxs-cart'></i>
                <?php if ($cart_count > 0): ?>
                    <span class="cart-count"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>
        </div>
        <div class="user-actions">
            <?php if (isset($_SESSION['customer_id'])): ?>
                <span class="me-3">สวัสดี, <?php echo htmlspecialchars($_SESSION['customer_name']); ?></span>
                <a href="my_account.php" class="btn btn-sm btn-outline-secondary">บัญชีของฉัน</a>
                <a href="customer_logout.php" class="btn btn-sm btn-danger">ออกจากระบบ</a>
            <?php else: ?>
                <a href="customer_login.php" class="btn btn-sm btn-primary">เข้าสู่ระบบ</a>
                <a href="register.php" class="btn btn-sm btn-outline-secondary">สมัครสมาชิก</a>
            <?php endif; ?>
        </div>
    </div>
</header>