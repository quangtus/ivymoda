<?php
// filepath: c:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\shared\admin\sidebar.php
?>
<section class="admin-content row space-between">
    <div class="admin-content-left">
        <ul>
            <li><a href="<?php echo BASE_URL; ?>admin/dashboard"> <img style="width:20px" src="<?php echo BASE_URL; ?>assets/images/icon/hi.png" alt="">Chào: <span style="color:blueviolet; font-size:22px"><?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin'); ?></span><span style="color: red; font-size:20px">&#10084;</span></a></li>
            
            <!-- Đơn hàng -->
            <li><a href="#"><img style="width:30px" src="<?php echo BASE_URL; ?>assets/images/icon/note.svg" alt="">Đơn hàng</a>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>admin/order">📋 Tất cả đơn hàng</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/order/pending">⏳ Đơn chờ xử lý</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/order/completed">✅ Đã hoàn thành</a></li>
                </ul>
            </li>
            
            <!-- Sản phẩm -->
            <li><a href="#"><img style="width:20px" src="<?php echo BASE_URL; ?>assets/images/icon/article.png" alt="">Sản phẩm</a>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>admin/product">📦 Danh sách sản phẩm</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/product/add">➕ Thêm sản phẩm</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/product-image">🖼️ Quản lý ảnh</a></li>
                </ul>
            </li>
            
            <!-- Danh mục & Phân loại -->
            <li><a href="#"><img style="width:20px" src="<?php echo BASE_URL; ?>assets/images/icon/options.png" alt="">Danh mục & Phân loại</a>
                <ul>
                    <li><a href="<?php echo ADMIN_URL; ?>category">🗂️ Danh mục chính</a></li>
                    <li><a href="<?php echo ADMIN_URL; ?>category/add">➕ Thêm danh mục</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/brand">📁 Loại sản phẩm</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/brand/add">➕ Thêm loại</a></li>
                </ul>
            </li>

            <!-- Màu sắc -->
            <li><a href="#"><img style="width:20px" src="<?php echo BASE_URL; ?>assets/images/icon/options.png" alt="">Màu sắc</a>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>admin/color">🎨 Danh sách màu</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/color/add">➕ Thêm màu mới</a></li>
                </ul>
            </li>
            
            <!-- Size -->
            <li><a href="#"><img style="width:20px" src="<?php echo BASE_URL; ?>assets/images/icon/options.png" alt="">Size</a>
                <ul>
                    <li><a href="<?php echo ADMIN_URL; ?>size">📏 Danh sách size</a></li>
                    <li><a href="<?php echo ADMIN_URL; ?>size/add">➕ Thêm size mới</a></li>
                </ul>
            </li>
            
            <!-- Khuyến mãi -->
            <li><a href="#"><img style="width:20px" src="<?php echo BASE_URL; ?>assets/images/icon/picture.png" alt="">Khuyến mãi</a>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>admin/discount">🎁 Mã giảm giá</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/discount/add">➕ Tạo mã mới</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/promotion">🖼️ Banner khuyến mãi</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/promotion/add">➕ Thêm banner</a></li>
                </ul>
            </li>
            
            <!-- Đánh giá sản phẩm -->
            <li><a href="#"><img style="width:20px" src="<?php echo BASE_URL; ?>assets/images/icon/options.png" alt="">Đánh giá</a>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>admin/review">⭐ Tất cả đánh giá</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/review?status=1">✅ Đánh giá hiển thị</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/review?status=0">❌ Đánh giá ẩn</a></li>
                </ul>
            </li>
            
            <!-- Báo cáo -->
            <li><a href="#"><img style="width:20px" src="<?php echo BASE_URL; ?>assets/images/icon/options.png" alt="">Báo cáo</a>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>admin/report/revenue">📈 Doanh thu</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/report/topSelling">🏆 SP bán chạy</a></li>
                </ul>
            </li>
            
            <!-- Quản lý Email - CHỈ HIỂN THỊ CHO ADMIN -->
            <?php if(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
            <li><a href="#"><img style="width:20px" src="<?php echo BASE_URL; ?>assets/images/icon/options.png" alt="">Quản lý Email</a>
                <ul>
                    <li><a href="<?= BASE_URL ?>admin/email">📧 Dashboard Email</a></li>
                    <li><a href="<?= BASE_URL ?>admin/email/templates">📝 Quản lý Template</a></li>
                    <li><a href="<?= BASE_URL ?>admin/email/send-promotion">📢 Gửi Email Khuyến Mãi</a></li>
                    <li><a href="<?= BASE_URL ?>admin/email/logs">📋 Xem Log Email</a></li>
                    <li><a href="<?= BASE_URL ?>admin/email/smtp-config">⚙️ Cấu hình SMTP</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Chatbot FAQ - CHỈ HIỂN THỊ CHO ADMIN -->
            <?php if(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
            <li><a href="#"><img style="width:20px" src="<?php echo BASE_URL; ?>assets/images/icon/options.png" alt="">Chatbot FAQ</a>
                <ul>
                    <li><a href="<?= BASE_URL ?>admin/chatbot">🤖 Quản lý FAQ</a></li>
                    <li><a href="<?= BASE_URL ?>admin/chatbot/add">➕ Thêm FAQ mới</a></li>
                    <li><a href="<?= BASE_URL ?>admin/chatbot/config">⚙️ Cấu hình Chatbot</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Quản lý tài khoản - CHỈ HIỂN THỊ CHO ADMIN -->
            <?php if(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
            <li class="has-sub">
                <a href="<?php echo BASE_URL; ?>admin/user">
                    <img style="width:20px" src="<?php echo BASE_URL; ?>assets/images/icon/user.png" alt="">Tài khoản
                </a>
                <ul class="submenu">
                    <li><a href="<?php echo BASE_URL; ?>admin/user">👥 Danh sách tài khoản</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/user/add">➕ Thêm tài khoản</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/user/roles">🔐 Quản lý vai trò</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
        </ul>
    </div>
    <div class="admin-content-right">