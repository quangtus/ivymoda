<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\admin\dashboard\index.php

// Load header
require_once ROOT_PATH . 'app/views/shared/admin/header.php';

// Load sidebar
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';
?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </h1>
            </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Total Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng người dùng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_users ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tổng đơn hàng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_orders ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Products Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tổng sản phẩm</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_products ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Message -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Chào mừng đến với IVY moda Admin</h6>
                </div>
                <div class="card-body">
                    <p>Chào mừng <strong><?= isset($_SESSION['username']) ? $_SESSION['username'] : $_SESSION['admin_name'] ?></strong> đến với khu vực quản trị hệ thống IVY moda.</p>
                    <p>Bạn có thể quản lý:</p>
                    <ul>
                        <li><a href="<?= ADMIN_URL ?>order"><i class="fas fa-clipboard-list text-primary"></i> Quản lý đơn hàng</a></li>
                        <li><a href="<?= ADMIN_URL ?>product"><i class="fas fa-box text-warning"></i> Quản lý sản phẩm</a></li>
                        <li><a href="<?= ADMIN_URL ?>category"><i class="fas fa-folder-open text-warning"></i> Quản lý danh mục & Phân loại</a></li>
                        <li><a href="<?= ADMIN_URL ?>color"><i class="fas fa-palette text-purple"></i> Quản lý màu sắc</a></li>
                        <li><a href="<?= ADMIN_URL ?>size"><i class="fas fa-ruler-combined text-secondary"></i> Quản lý size</a></li>
                        <li><a href="<?= ADMIN_URL ?>discount"><i class="fas fa-gift text-danger"></i> Quản lý khuyến mãi</a></li>
                        <li><a href="<?= ADMIN_URL ?>review"><i class="fas fa-star text-warning"></i> Quản lý đánh giá</a></li>
                        <li><a href="<?= ADMIN_URL ?>report/revenue"><i class="fas fa-chart-line text-success"></i> Báo cáo doanh thu</a></li>
                        <?php if(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
                        <li><a href="<?= ADMIN_URL ?>user"><i class="fas fa-users text-info"></i> Quản lý người dùng</a></li>
                        <li><a href="<?= ADMIN_URL ?>email"><i class="fas fa-envelope text-primary"></i> Quản lý Email</a></li>
                        <li><a href="<?= ADMIN_URL ?>chatbot"><i class="fas fa-robot text-purple"></i> Quản lý Chatbot FAQ</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>
</div>

<?php
// Load footer
require_once ROOT_PATH . 'app/views/shared/admin/footer.php';
?>
