<aside class="sidebar">
    <div class="sidebar-header">
        <i class='bx bxs-store-alt logo-icon'></i>
        <span class="logo-text">Mango Store</span>
    </div>
    <ul class="sidebar-menu">
        <li class="<?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">
            <a href="dashboard.php"><i class='bx bxs-dashboard'></i><span>ภาพรวม (Dashboard)</span></a>
        </li>
        <li class="<?php echo ($current_page == 'products') ? 'active' : ''; ?>">
            <a href="products.php"><i class='bx bxs-package'></i><span>จัดการสต็อกสินค้า</span></a>
        </li>
        <li class="<?php echo ($current_page == 'orders') ? 'active' : ''; ?>">
            <a href="orders.php"><i class='bx bxs-cart'></i><span>จัดการการขาย</span></a>
        </li>
        <li class="<?php echo ($current_page == 'reports') ? 'active' : ''; ?>">
            <a href="reports.php"><i class='bx bxs-report'></i><span>รายงาน</span></a>
        </li>
    </ul>
</aside>