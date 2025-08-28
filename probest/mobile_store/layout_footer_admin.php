<?php
// ไฟล์: layout_footer_admin.php
?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    // --- Script สำหรับแสดง Flash Message ---
    <?php
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        $icon = $flash['type'];
        $title = $flash['message'];
        
        echo "Swal.fire({
            icon: '{$icon}',
            title: '{$title}',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true
        });";

        unset($_SESSION['flash_message']);
    }
    ?>

    // --- Script สำหรับยืนยันการลบ ---
    function confirmDelete(event, url) {
        event.preventDefault(); // หยุดการทำงานของลิงก์ปกติ
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณจะไม่สามารถกู้คืนข้อมูลนี้ได้!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // ถ้าผู้ใช้กดยืนยัน ให้ไปที่ URL สำหรับลบ
                window.location.href = url;
            }
        })
    }
    </script>
</body>
</html>