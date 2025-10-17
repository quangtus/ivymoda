<?php
// filepath: c:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\shared\admin\header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Admin - IVY moda' ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin-layout.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin-components.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/image-fix.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin-order.css">
    
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/54f0cb7e4a.js" crossorigin="anonymous"></script>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- jQuery và Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>assets/js/image-handler.js"></script>
</head>
<body>
    <section class="top space-between row">
        <div class="header-top-left">
            <a href="<?= BASE_URL ?>admin/dashboard"><img src="<?= BASE_URL ?>assets/images/logo.png" alt="Logo"></a>
        </div>
        <ul class="header-top-right">
            <li>Xin chào: <strong><?= isset($_SESSION['username']) ? $_SESSION['username'] : (isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin') ?></strong></li>
            <li><a href="<?= BASE_URL ?>admin/auth/logout">Đăng xuất</a></li>
        </ul>
    </section>