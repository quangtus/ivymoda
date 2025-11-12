<?php
// filepath: c:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\shared\admin\sidebar.php
?>
<section class="admin-content row space-between">
    <div class="admin-content-left">
        <ul>
            <li class="admin-welcome">
                <a href="<?php echo BASE_URL; ?>admin/dashboard">
                    <i class="fas fa-hand-sparkles"></i> Chào: 
                    <span class="admin-name"><?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin'); ?></span> 
                    <i class="fas fa-heart heart-icon"></i>
                </a>
            </li>
            
            <!-- Đơn hàng -->
            <li><a href="#"><i class="fas fa-clipboard-list"></i> Đơn hàng</a>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>admin/order"><i class="fas fa-list-alt"></i> Tất cả đơn hàng</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/order/pending"><i class="fas fa-clock"></i> Đơn chờ xử lý</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/order/completed"><i class="fas fa-check-circle"></i> Đã hoàn thành</a></li>
                </ul>
            </li>
            
            <!-- Sản phẩm -->
            <li><a href="#"><i class="fas fa-box"></i> Sản phẩm</a>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>admin/product"><i class="fas fa-boxes"></i> Danh sách sản phẩm</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/product/add"><i class="fas fa-plus-circle"></i> Thêm sản phẩm</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/product-image"><i class="fas fa-images"></i> Quản lý ảnh</a></li>
                </ul>
            </li>
            
            <!-- Danh mục & Phân loại -->
            <li><a href="#"><i class="fas fa-folder-open"></i> Danh mục & Phân loại</a>
                <ul>
                    <li><a href="<?php echo ADMIN_URL; ?>category"><i class="fas fa-folder"></i> Danh mục chính</a></li>
                    <li><a href="<?php echo ADMIN_URL; ?>category/add"><i class="fas fa-folder-plus"></i> Thêm danh mục</a></li>
                </ul>
            </li>

            <!-- Màu sắc -->
            <li><a href="#"><i class="fas fa-palette"></i> Màu sắc</a>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>admin/color"><i class="fas fa-swatchbook"></i> Danh sách màu</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/color/add"><i class="fas fa-plus-square"></i> Thêm màu mới</a></li>
                </ul>
            </li>
            
            <!-- Size -->
            <li><a href="#"><i class="fas fa-ruler-combined"></i> Size</a>
                <ul>
                    <li><a href="<?php echo ADMIN_URL; ?>size"><i class="fas fa-ruler"></i> Danh sách size</a></li>
                    <li><a href="<?php echo ADMIN_URL; ?>size/add"><i class="fas fa-plus"></i> Thêm size mới</a></li>
                </ul>
            </li>
            
            <!-- Khuyến mãi -->
            <li><a href="#"><i class="fas fa-gift"></i> Khuyến mãi</a>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>admin/discount"><i class="fas fa-ticket-alt"></i> Mã giảm giá</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/discount/add"><i class="fas fa-tag"></i> Tạo mã mới</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/promotion"><i class="fas fa-image"></i> Banner khuyến mãi</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/promotion/add"><i class="fas fa-plus-circle"></i> Thêm banner</a></li>
                </ul>
            </li>
            
            <!-- Đánh giá sản phẩm -->
            <li><a href="#"><i class="fas fa-star"></i> Đánh giá</a>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>admin/review"><i class="fas fa-comments"></i> Tất cả đánh giá</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/review?status=1"><i class="fas fa-check"></i> Đánh giá hiển thị</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/review?status=0"><i class="fas fa-eye-slash"></i> Đánh giá ẩn</a></li>
                </ul>
            </li>
            
            <!-- Báo cáo -->
            <li><a href="#"><i class="fas fa-chart-line"></i> Báo cáo</a>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>admin/report/revenue"><i class="fas fa-dollar-sign"></i> Doanh thu</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/report/topSelling"><i class="fas fa-trophy"></i> SP bán chạy</a></li>
                </ul>
            </li>
            
            <!-- Quản lý Email - CHỈ HIỂN THỊ CHO ADMIN -->
            <?php if(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
            <li><a href="#"><i class="fas fa-envelope"></i> Quản lý Email</a>
                <ul>
                    <li><a href="<?= BASE_URL ?>admin/email"><i class="fas fa-tachometer-alt"></i> Dashboard Email</a></li>
                    <li><a href="<?= BASE_URL ?>admin/email/templates"><i class="fas fa-file-alt"></i> Quản lý Template</a></li>
                    <li><a href="<?= BASE_URL ?>admin/email/send-promotion"><i class="fas fa-paper-plane"></i> Gửi Email Khuyến Mãi</a></li>
                    <li><a href="<?= BASE_URL ?>admin/email/logs"><i class="fas fa-history"></i> Xem Log Email</a></li>
                    <li><a href="<?= BASE_URL ?>admin/email/smtp-config"><i class="fas fa-cog"></i> Cấu hình SMTP</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Chatbot FAQ - CHỈ HIỂN THỊ CHO ADMIN -->
            <?php if(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
            <li><a href="#"><i class="fas fa-robot"></i> Chatbot FAQ</a>
                <ul>
                    <li><a href="<?= BASE_URL ?>admin/chatbot"><i class="fas fa-question-circle"></i> Quản lý FAQ</a></li>
                    <li><a href="<?= BASE_URL ?>admin/chatbot/add"><i class="fas fa-plus-circle"></i> Thêm FAQ mới</a></li>
                    <li><a href="<?= BASE_URL ?>admin/chatbot/config"><i class="fas fa-tools"></i> Cấu hình Chatbot</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Quản lý tài khoản - CHỈ HIỂN THỊ CHO ADMIN -->
            <?php if(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
            <li class="has-sub">
                <a href="<?php echo BASE_URL; ?>admin/user"><i class="fas fa-users"></i> Tài khoản</a>
                <ul class="submenu">
                    <li><a href="<?php echo BASE_URL; ?>admin/user"><i class="fas fa-user-friends"></i> Danh sách tài khoản</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/user/add"><i class="fas fa-user-plus"></i> Thêm tài khoản</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/user/roles"><i class="fas fa-shield-alt"></i> Quản lý vai trò</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
        </ul>
    </div>
    <div class="admin-content-right">