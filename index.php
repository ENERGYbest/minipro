<?php 
$page_title = "ยินดีต้อนรับสู่ Mango Store";
include 'layout_header.php'; 
require_once 'db.php';
$sql = "SELECT * FROM products WHERE stock_quantity > 0 ORDER BY id DESC";
$result = $mysqli->query($sql);
?>

<div class="top-bar">
    เลือกซื้อสินค้าชิ้นโปรดของคุณไปกับ Specialist พร้อมรับบริการจัดส่งฟรีและอีกมากมาย
</div>
<?php include 'frontend_header.php'; ?>

<section class="hero-section">
    <video autoplay loop muted playsinline class="hero-video-bg">
        <source src="images/my-awesome-video.mp4" type="video/mp4">
        เบราว์เซอร์ของคุณไม่รองรับวิดีโอ.
    </video>

    <div class="hero-content">
        <h1>คอลเลกชันล่าสุด</h1>
        <p>นวัตกรรม สมาร์ทโฟน และแรงบันดาลใจ</p>
        <a href="#products" class="btn btn-light">เลือกซื้อเลย</a>
    </div>
</section>

<section id="products" class="product-section">
    <div class="container">
        <h2 class="text-center mb-5">สินค้าของเรา</h2>
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while($product = $result->fetch_assoc()): ?>
                    <div class="col-md-4 col-lg-3 mb-4">
                        <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="card product-card h-100 text-decoration-none">
                            <div class="product-image">
                                <?php if (!empty($product['image_url'])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php else: ?>
                                    <span>Image Placeholder</span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body d-flex flex-column text-center">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($product['brand'] ?? ''); ?></p>
                                <h6 class="card-subtitle mb-2 mt-auto fs-5"><?php echo number_format($product['selling_price'], 2); ?> ฿</h6>
                                <div class="btn btn-outline-warning mt-2">ดูรายละเอียด</div>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">ขออภัย ขณะนี้ยังไม่มีสินค้าในร้าน</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'layout_footer.php'; ?>
<?php $mysqli->close(); ?>