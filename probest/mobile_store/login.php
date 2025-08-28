<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php"); // ถ้า Login แล้วให้ไปหน้า dashboard
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Login - Mango Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; background-color: #f4f6f9; }
        .login-card { max-width: 400px; width: 100%; }
    </style>
</head>
<body>
    <div class="card login-card shadow-sm">
        <div class="card-body p-5">
            <h1 class="text-center mb-4">Mango Store Login</h1>
            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger">อีเมลหรือรหัสผ่านไม่ถูกต้อง</div>
            <?php endif; ?>
            <form action="auth.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">อีเมล</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">รหัสผ่าน</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <button type="submit" class="btn btn-warning w-100">เข้าสู่ระบบ</button>
            </form>
        </div>
    </div>
</body>
</html>