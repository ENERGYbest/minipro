<?php 
$page_title = "เข้าสู่ระบบ - Mango Store";
include 'layout_header.php'; // ใช้ Layout Header ของหน้าบ้าน

// ถ้า Login อยู่แล้ว ให้ไปหน้าแรกเลย
if (isset($_SESSION['customer_id'])) {
    header("Location: index.php");
    exit();
}
?>
<main class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body p-5">
                    <h1 class="text-center mb-4">เข้าสู่ระบบ</h1>
                    
                    <form action="process_customer_login.php" method="POST">
                        <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_GET['redirect_url'] ?? ''); ?>">
                        <div class="mb-3">
                            <label class="form-label">อีเมล</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">รหัสผ่าน</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-warning w-100">เข้าสู่ระบบ</button>
                    </form>
                    <div class="text-center mt-3">
                        <p>ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิกที่นี่</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include 'layout_footer.php'; // ใช้ Layout Footer ของหน้าบ้าน ?>