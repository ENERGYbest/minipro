<?php
session_start();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สมัครสมาชิก - Mango Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/frontend_style.css">
</head>
<body>
    <?php include 'frontend_header.php'; ?>

    <main class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-5">
                        <h1 class="text-center mb-4">สมัครสมาชิก</h1>
                        <?php if(isset($_GET['error'])): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                        <?php endif; ?>
                        <form id="registerForm" action="process_register.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">ชื่อ-นามสกุล</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">อีเมล</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">เบอร์โทรศัพท์</label>
                                <input type="tel" name="phone" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">รหัสผ่าน</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                             <div class="mb-3">
                                <label class="form-label">ยืนยันรหัสผ่าน</label>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-warning w-100">สมัครสมาชิก</button>
                        </form>
                        <div class="text-center mt-3">
                            <p>เป็นสมาชิกอยู่แล้ว? <a href="customer_login.php">เข้าสู่ระบบที่นี่</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="text-center p-4 mt-5 bg-light">
        <p>&copy; <?php echo date('Y'); ?> Mango Store. All Rights Reserved.</p>
    </footer>
    <script>
        const form = document.getElementById('registerForm');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        form.addEventListener('submit', function(e) {
            if (password.value !== confirmPassword.value) {
                e.preventDefault();
                alert('รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน');
            }
        });
    </script>
</body>
</html>