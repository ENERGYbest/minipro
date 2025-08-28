<?php
// ไฟล์: layout_header_admin.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Mango Store Admin'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .main-content { padding: 20px 40px !important; }
        .btn-warning { background-color: #FFC300; border-color: #FFC300; color:#34495E; font-weight: 500;}
        .btn-warning:hover { background-color: #d4a300; border-color: #d4a300; }
    </style>
</head>
<body>
    <div class="container-fluid p-0 d-flex">
        <?php include 'sidebar.php'; ?>
        <main class="main-content flex-grow-1">
            <?php include 'header.php'; ?>