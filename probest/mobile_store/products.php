<?php
$page_title = "จัดการสต็อกสินค้า - Mango Store";
include 'layout_header_admin.php'; // ใช้ Layout Header ใหม่
require_once 'db.php';

$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $mysqli->query($sql);
?>

<h1 class="mb-4">จัดการสต็อกสินค้า</h1>
<div class="card mb-4">
    <div class="card-header">เพิ่มสินค้าใหม่</div>
    <div class="card-body">
        <form action="process_product.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">
            <div class="row">
                <div class="col-md-4 mb-3"><label class="form-label">ประเภทสินค้า</label><select class="form-select" name="type" required><option value="phone">โทรศัพท์มือถือ</option><option value="accessory">อุปกรณ์เสริม</option></select></div>
                <div class="col-md-8 mb-3"><label class="form-label">ชื่อสินค้า/รุ่น</label><input type="text" class="form-control" name="name" required></div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">ยี่ห้อ</label><input type="text" class="form-control" name="brand"></div>
                <div class="col-md-6 mb-3"><label class="form-label">IMEI</label><input type="text" class="form-control" name="imei"></div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3"><label class="form-label">ราคาทุน</label><input type="number" step="0.01" class="form-control" name="cost_price" required></div>
                <div class="col-md-4 mb-3"><label class="form-label">ราคาขาย</label><input type="number" step="0.01" class="form-control" name="selling_price" required></div>
                <div class="col-md-4 mb-3"><label class="form-label">จำนวนในสต็อก</label><input type="number" class="form-control" name="stock_quantity" required></div>
            </div>
            <div class="mb-3"><label class="form-label">รูปภาพสินค้า</label><input type="file" class="form-control" name="product_image"></div>
            <button type="submit" class="btn btn-warning">เพิ่มสินค้า</button>
        </form>
    </div>
</div>

<h2 class="mt-5">รายการสินค้าในคลัง</h2>
<table class="table table-striped table-hover align-middle">
    <thead>
        <tr>
            <th>#</th><th>รูปภาพ</th><th>ประเภท</th><th>ชื่อ/รุ่น</th><th>ราคาขาย</th><th>จำนวนคงเหลือ</th><th>จัดการ</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row["id"]; ?></td>
                    <td>
                        <?php if (!empty($row['image_url'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" style="width: 60px; height: auto; border-radius: 4px;">
                        <?php else: ?>
                            <span>ไม่มีรูป</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo ($row["type"] == 'phone' ? 'มือถือ' : 'อุปกรณ์เสริม'); ?></td>
                    <td><?php echo htmlspecialchars($row["name"]); ?></td>
                    <td><?php echo number_format($row["selling_price"], 2); ?></td>
                    <td><?php echo $row["stock_quantity"]; ?></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary">แก้ไข</a>
                        <a href="process_product.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(event, this.href)">ลบ</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8" class="text-center">ยังไม่มีสินค้าในระบบ</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'layout_footer_admin.php'; // ใช้ Layout Footer ใหม่ ?>