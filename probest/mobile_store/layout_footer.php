<?php
// ไฟล์: layout_footer.php
?>
    <footer class="text-center p-4 mt-5 bg-light">
        <p>&copy; <?php echo date('Y'); ?> Mango Store. All Rights Reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    // --- Script สำหรับแสดง Flash Message ---
    <?php
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        $icon = $flash['type']; // 'success', 'error', 'warning', 'info'
        $title = $flash['message'];
        
        echo "Swal.fire({
            icon: '{$icon}',
            title: '{$title}',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true
        });";

        // ลบ message ออกจาก session เพื่อไม่ให้แสดงซ้ำ
        unset($_SESSION['flash_message']);
    }
    ?>
    </script>
</body>
</html>