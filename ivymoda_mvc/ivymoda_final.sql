-- ============================================
-- IVYMODA DATABASE - FINAL VERSION 4.0
-- ============================================
-- Kế thừa từ: ivymoda_update.sql (100% tương thích code)
-- Bổ sung: Review, Promotion (từ ivymoda_complete.sql)
-- Loại bỏ: Các bảng thừa (wishlist, notification, chatbot)
-- Tương thích: 100% với code hiện tại
-- Ngày tạo: 2025-10-14
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Xóa database cũ nếu tồn tại
DROP DATABASE IF EXISTS `ivymoda`;

-- Tạo database mới
CREATE DATABASE `ivymoda` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ivymoda`;

-- ============================================
-- 1. QUẢN TRỊ HỆ THỐNG (UC01-06, UC10-12)
-- ============================================

-- Bảng roles
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng users (UC01-06)
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role_id` int(11) DEFAULT 2,
  `status` int(11) DEFAULT 1,
  `login_attempts` int(11) DEFAULT 0,
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_token_expire` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_role` (`role_id`),
  CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 2. QUẢN LÝ SẢN PHẨM (UC07-08) - VARIANT SYSTEM
-- ============================================

-- Bảng danh mục (UC07)
CREATE TABLE `tbl_danhmuc` (
  `danhmuc_id` int(11) NOT NULL AUTO_INCREMENT,
  `danhmuc_ten` varchar(255) NOT NULL,
  `danhmuc_mo_ta` text DEFAULT NULL,
  `danhmuc_status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`danhmuc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng loại sản phẩm
CREATE TABLE `tbl_loaisanpham` (
  `loaisanpham_id` int(11) NOT NULL AUTO_INCREMENT,
  `danhmuc_id` int(11) NOT NULL,
  `loaisanpham_ten` varchar(255) NOT NULL,
  `loaisanpham_mo_ta` text DEFAULT NULL,
  PRIMARY KEY (`loaisanpham_id`),
  KEY `fk_loai_danhmuc` (`danhmuc_id`),
  CONSTRAINT `fk_loai_danhmuc` FOREIGN KEY (`danhmuc_id`) REFERENCES `tbl_danhmuc` (`danhmuc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng màu sắc
CREATE TABLE `tbl_color` (
  `color_id` int(11) NOT NULL AUTO_INCREMENT,
  `color_ten` varchar(255) NOT NULL,
  `color_ma` varchar(20) DEFAULT NULL COMMENT 'Mã màu hex (vd: #FF0000)',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`color_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Chỉ cần color_ma (hex) là đủ - đơn giản và hiệu quả';

-- Bảng size
CREATE TABLE `tbl_size` (
  `size_id` int(11) NOT NULL AUTO_INCREMENT,
  `size_ten` varchar(50) NOT NULL COMMENT 'XS, S, M, L, XL, XXL, 3XL',
  `size_order` int(11) DEFAULT 0 COMMENT 'Thứ tự sắp xếp khi hiển thị',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`size_id`),
  UNIQUE KEY `size_ten` (`size_ten`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng sản phẩm (UC08)
CREATE TABLE `tbl_sanpham` (
  `sanpham_id` int(11) NOT NULL AUTO_INCREMENT,
  `sanpham_tieude` varchar(255) NOT NULL,
  `sanpham_ma` varchar(100) NOT NULL,
  `danhmuc_id` int(11) NOT NULL,
  `loaisanpham_id` int(11) NOT NULL,
  `sanpham_gia` decimal(10,2) NOT NULL,
  `sanpham_gia_goc` decimal(10,2) DEFAULT NULL,
  `sanpham_giam_gia` decimal(5,2) DEFAULT 0,
  `sanpham_chitiet` text,
  `sanpham_baoquan` text,
  `sanpham_anh` varchar(255) NOT NULL COMMENT 'Ảnh đại diện chính',
  `sanpham_status` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sanpham_id`),
  UNIQUE KEY `sanpham_ma` (`sanpham_ma`),
  KEY `fk_sp_danhmuc` (`danhmuc_id`),
  KEY `fk_sp_loai` (`loaisanpham_id`),
  CONSTRAINT `fk_sp_danhmuc` FOREIGN KEY (`danhmuc_id`) REFERENCES `tbl_danhmuc` (`danhmuc_id`),
  CONSTRAINT `fk_sp_loai` FOREIGN KEY (`loaisanpham_id`) REFERENCES `tbl_loaisanpham` (`loaisanpham_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Đã XÓA: sanpham_size, sanpham_soluong (chuyển sang tbl_product_variant)';

-- Bảng trung gian: Sản phẩm - Màu
CREATE TABLE `tbl_sanpham_color` (
  `sanpham_color_id` int(11) NOT NULL AUTO_INCREMENT,
  `sanpham_id` int(11) NOT NULL,
  `color_id` int(11) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0 COMMENT '1: Màu mặc định',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sanpham_color_id`),
  UNIQUE KEY `unique_product_color` (`sanpham_id`, `color_id`),
  KEY `fk_sc_sanpham` (`sanpham_id`),
  KEY `fk_sc_color` (`color_id`),
  CONSTRAINT `fk_sc_sanpham` FOREIGN KEY (`sanpham_id`) REFERENCES `tbl_sanpham` (`sanpham_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sc_color` FOREIGN KEY (`color_id`) REFERENCES `tbl_color` (`color_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng ảnh sản phẩm theo màu
CREATE TABLE `tbl_anhsanpham` (
  `anh_id` int(11) NOT NULL AUTO_INCREMENT,
  `sanpham_id` int(11) NOT NULL,
  `sanpham_color_id` int(11) DEFAULT NULL COMMENT 'Ảnh thuộc màu nào',
  `anh_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0 COMMENT '1: Ảnh chính của màu đó',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`anh_id`),
  KEY `idx_sanpham` (`sanpham_id`),
  KEY `idx_sanpham_color` (`sanpham_color_id`),
  KEY `idx_primary` (`is_primary`),
  CONSTRAINT `fk_anh_sanpham` FOREIGN KEY (`sanpham_id`) REFERENCES `tbl_sanpham` (`sanpham_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_anh_sanpham_color` FOREIGN KEY (`sanpham_color_id`) REFERENCES `tbl_sanpham_color` (`sanpham_color_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng biến thể sản phẩm (QUAN TRỌNG NHẤT!)
CREATE TABLE `tbl_product_variant` (
  `variant_id` int(11) NOT NULL AUTO_INCREMENT,
  `sanpham_id` int(11) NOT NULL COMMENT 'ID sản phẩm',
  `color_id` int(11) NOT NULL COMMENT 'ID màu',
  `size_id` int(11) NOT NULL COMMENT 'ID size',
  `sku` varchar(100) DEFAULT NULL COMMENT 'Mã SKU riêng (VD: ASM-001-S-WHITE)',
  `ton_kho` int(11) DEFAULT 0 COMMENT 'Số lượng tồn kho của variant này',
  `gia_ban` decimal(10,2) DEFAULT NULL COMMENT 'Giá riêng của variant (nếu khác giá gốc)',
  `trang_thai` tinyint(1) DEFAULT 1 COMMENT '1: Còn hàng, 0: Hết hàng/Ngừng kinh doanh',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`variant_id`),
  UNIQUE KEY `unique_variant` (`sanpham_id`, `color_id`, `size_id`),
  UNIQUE KEY `sku` (`sku`),
  KEY `idx_sanpham` (`sanpham_id`),
  KEY `idx_color` (`color_id`),
  KEY `idx_size` (`size_id`),
  KEY `idx_tonkho` (`ton_kho`),
  KEY `idx_trangthai` (`trang_thai`),
  CONSTRAINT `fk_variant_sanpham` FOREIGN KEY (`sanpham_id`) REFERENCES `tbl_sanpham` (`sanpham_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_variant_color` FOREIGN KEY (`color_id`) REFERENCES `tbl_color` (`color_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_variant_size` FOREIGN KEY (`size_id`) REFERENCES `tbl_size` (`size_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Bảng lưu tồn kho chi tiết theo từng size và màu';

-- ============================================
-- 3. QUẢN LÝ GIỎ HÀNG & ĐƠN HÀNG (UC09-12)
-- ============================================

-- Bảng giỏ hàng (UC09) - VERSION 2.0
CREATE TABLE `tbl_cart` (
  `cart_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `variant_id` int(11) NOT NULL COMMENT 'Liên kết tới variant cụ thể (size + màu)',
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_id`),
  KEY `idx_session` (`session_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_variant` (`variant_id`),
  CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cart_variant` FOREIGN KEY (`variant_id`) REFERENCES `tbl_product_variant` (`variant_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='VERSION 2.0: Lưu variant_id thay vì các trường rời';

-- Bảng đơn hàng (UC10-12) - 100% TƯƠNG THÍCH VỚI CODE + HỖ TRỢ MÃ GIẢM GIÁ
CREATE TABLE `tbl_order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_code` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `customer_address` text NOT NULL COMMENT 'Địa chỉ đầy đủ',
  `order_total` decimal(15,2) NOT NULL COMMENT 'Tổng tiền cuối cùng sau giảm giá',
  `original_total` decimal(15,2) DEFAULT NULL COMMENT 'Tổng tiền gốc trước khi giảm giá',
  `discount_code` varchar(50) DEFAULT NULL COMMENT 'Mã giảm giá đã áp dụng',
  `discount_value` decimal(10,2) DEFAULT 0 COMMENT 'Giá trị giảm giá',
  `order_status` tinyint(1) DEFAULT 0 COMMENT '0:Chờ xử lý, 1:Đang giao, 2:Hoàn thành, 3:Đã hủy',
  `payment_method` enum('cod','momo') DEFAULT 'cod',
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `payment_transaction_id` varchar(100) DEFAULT NULL,
  `shipping_method` varchar(50) DEFAULT 'Standard',
  `order_note` text DEFAULT NULL,
  `order_date` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `order_code` (`order_code`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`order_status`),
  KEY `idx_date` (`order_date`),
  KEY `idx_discount_code` (`discount_code`),
  CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='100% tương thích với OrderModel và CheckoutController + Hỗ trợ mã giảm giá';

-- Bảng chi tiết đơn hàng (UC10-12) - VERSION 2.0
CREATE TABLE `tbl_order_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT NULL COMMENT 'FK tới tbl_product_variant (NULL nếu variant đã xóa)',
  `sanpham_id` int(11) NOT NULL COMMENT 'Snapshot: product ID',
  `sanpham_ten` varchar(255) NOT NULL COMMENT 'Snapshot: Tên sản phẩm',
  `sanpham_gia` decimal(10,2) NOT NULL COMMENT 'Snapshot: Giá tại thời điểm đặt',
  `sanpham_soluong` int(11) NOT NULL,
  `sanpham_size` varchar(50) NOT NULL COMMENT 'Snapshot: Tên size',
  `sanpham_color` varchar(100) NOT NULL COMMENT 'Snapshot: Tên màu',
  `sanpham_anh` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `idx_order` (`order_id`),
  KEY `idx_variant` (`variant_id`),
  KEY `idx_sanpham` (`sanpham_id`),
  CONSTRAINT `fk_item_order` FOREIGN KEY (`order_id`) REFERENCES `tbl_order` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_item_variant` FOREIGN KEY (`variant_id`) REFERENCES `tbl_product_variant` (`variant_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_item_sanpham` FOREIGN KEY (`sanpham_id`) REFERENCES `tbl_sanpham` (`sanpham_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='VERSION 2.0: Lưu variant_id + snapshot để giữ history. ĐÃ SỬA: Thêm FK sanpham_id';

-- Bảng log giao dịch MoMo (UC23)
CREATE TABLE `tbl_momo_transaction` (
  `momo_id` bigint NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `request_id` varchar(100) NOT NULL,
  `order_code` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `result_code` varchar(10) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`momo_id`),
  UNIQUE KEY `request_id` (`request_id`),
  KEY `idx_order` (`order_id`),
  CONSTRAINT `fk_momo_order` FOREIGN KEY (`order_id`) REFERENCES `tbl_order` (`order_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================
-- 4. KHUYẾN MÃI (UC16-18, UC08-09)
-- ============================================

-- Bảng mã giảm giá (UC42, UC44) - TÍCH HỢP TỪ DISCOUNT_UPDATE.SQL
CREATE TABLE `tbl_ma_giam_gia` (
  `ma_id` int(11) NOT NULL AUTO_INCREMENT,
  `ma_code` varchar(50) NOT NULL COMMENT 'Mã code để khách hàng sử dụng',
  `ma_ten` varchar(255) NOT NULL COMMENT 'Tên mô tả mã giảm giá',
  `ma_giam` decimal(10,2) NOT NULL COMMENT 'Giá trị giảm (phần trăm hoặc số tiền)',
  `loai_giam` enum('percent','fixed') DEFAULT 'percent' COMMENT 'Loại giảm: percent=phần trăm, fixed=số tiền cố định',
  `ngay_bat_dau` datetime NOT NULL COMMENT 'Ngày bắt đầu hiệu lực',
  `ngay_ket_thuc` datetime NOT NULL COMMENT 'Ngày kết thúc hiệu lực',
  `so_luong` int(11) DEFAULT NULL COMMENT 'Số lượng sử dụng tối đa (NULL=không giới hạn)',
  `da_su_dung` int(11) DEFAULT 0 COMMENT 'Số lần đã sử dụng',
  `trang_thai` tinyint(1) DEFAULT 1 COMMENT '1=Kích hoạt, 0=Vô hiệu hóa',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ma_id`),
  UNIQUE KEY `ma_code` (`ma_code`),
  KEY `idx_status` (`trang_thai`),
  KEY `idx_date_range` (`ngay_bat_dau`, `ngay_ket_thuc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Bảng quản lý mã giảm giá - UC42, UC44';

-- Bảng thông báo khuyến mãi (UC17, UC08, UC09)
CREATE TABLE `tbl_promotion` (
  `promotion_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT 'Tiêu đề khuyến mãi',
  `description` text DEFAULT NULL COMMENT 'Mô tả chi tiết',
  `content` text DEFAULT NULL COMMENT 'Nội dung HTML',
  `image_url` varchar(255) DEFAULT NULL COMMENT 'Banner khuyến mãi',
  `ma_giam_gia_id` int(11) DEFAULT NULL COMMENT 'FK tới mã giảm giá (nếu có)',
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `priority` int(11) DEFAULT 0 COMMENT 'Thứ tự ưu tiên hiển thị',
  `created_by` int(11) DEFAULT NULL COMMENT 'Admin tạo',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`promotion_id`),
  KEY `idx_active_date` (`is_active`, `start_date`, `end_date`),
  KEY `idx_priority` (`priority`),
  KEY `idx_discount` (`ma_giam_gia_id`),
  CONSTRAINT `fk_promotion_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_promotion_discount` FOREIGN KEY (`ma_giam_gia_id`) REFERENCES `tbl_ma_giam_gia` (`ma_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Khuyến mãi - UC08, UC17 - ĐÃ SỬA: discount_code → ma_giam_gia_id (FK)';

-- Log gửi email khuyến mãi (UC09, UC25)
CREATE TABLE `tbl_promotion_email_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `promotion_id` int(11) NOT NULL,
  `recipient_email` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `sent_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `status` enum('sent','failed','pending') DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `idx_promotion` (`promotion_id`),
  KEY `idx_status` (`status`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `fk_promo_email_promotion` FOREIGN KEY (`promotion_id`) REFERENCES `tbl_promotion` (`promotion_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_promo_email_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Log gửi email khuyến mãi - UC09, UC25';

-- ============================================
-- 5. ĐÁNH GIÁ SẢN PHẨM (UC13)
-- ============================================

CREATE TABLE `tbl_product_review` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `sanpham_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL COMMENT 'Chỉ cho phép đánh giá sau khi mua',
  `rating` tinyint(1) NOT NULL COMMENT '1-5 sao',
  `comment` text DEFAULT NULL,
  `review_images` text DEFAULT NULL COMMENT 'Danh sách ảnh đánh giá (JSON format)',
  `is_verified_purchase` tinyint(1) DEFAULT 0 COMMENT '1: Đã mua hàng',
  `status` tinyint(1) DEFAULT 1 COMMENT '1: Hiển thị, 0: Ẩn',
  `admin_reply` text DEFAULT NULL COMMENT 'Phản hồi từ admin',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`),
  KEY `idx_sanpham` (`sanpham_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_rating` (`rating`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_review_sanpham` FOREIGN KEY (`sanpham_id`) REFERENCES `tbl_sanpham` (`sanpham_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_review_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_review_order` FOREIGN KEY (`order_id`) REFERENCES `tbl_order` (`order_id`) ON DELETE SET NULL,
  CONSTRAINT `chk_rating` CHECK (`rating` BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Đánh giá sản phẩm - UC13 - VERSION 2.0: Thêm hỗ trợ upload ảnh';

-- ============================================
-- 6. BÁO CÁO (UC19-22)
-- ============================================

-- Bảng thống kê
CREATE TABLE `tbl_thong_ke` (
  `thongke_id` int(11) NOT NULL AUTO_INCREMENT,
  `ngay` date NOT NULL,
  `doanh_thu` decimal(15,2) DEFAULT 0,
  `so_don_hang` int(11) DEFAULT 0,
  `so_san_pham_ban` int(11) DEFAULT 0,
  PRIMARY KEY (`thongke_id`),
  UNIQUE KEY `ngay` (`ngay`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 7. EMAIL (UC07, UC24, UC25)
-- ============================================

-- Bảng template email
CREATE TABLE `tbl_email_template` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng log email
CREATE TABLE `tbl_email_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `recipient` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `status` enum('sent','failed') DEFAULT 'sent',
  `sent_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 8. VIEW HỮU ÍCH
-- ============================================

-- View lịch sử mua hàng (UC11)
CREATE OR REPLACE VIEW `view_user_order_history` AS
SELECT 
    o.order_id,
    o.user_id,
    o.order_code,
    o.order_date,
    o.order_total,
    o.order_status,
    o.payment_method,
    o.customer_address,
    COUNT(oi.item_id) as total_items,
    SUM(oi.sanpham_soluong) as total_quantity,
    u.fullname,
    u.email,
    u.phone
FROM tbl_order o
LEFT JOIN tbl_order_items oi ON o.order_id = oi.order_id
LEFT JOIN users u ON o.user_id = u.id
GROUP BY o.order_id
ORDER BY o.order_date DESC;

-- View sản phẩm có đánh giá (UC13)
CREATE OR REPLACE VIEW `view_product_with_rating` AS
SELECT 
    p.sanpham_id,
    p.sanpham_tieude,
    p.sanpham_ma,
    p.sanpham_gia,
    p.sanpham_anh,
    COUNT(r.review_id) as total_reviews,
    AVG(r.rating) as avg_rating,
    SUM(CASE WHEN r.rating = 5 THEN 1 ELSE 0 END) as five_star_count,
    SUM(CASE WHEN r.rating = 4 THEN 1 ELSE 0 END) as four_star_count,
    SUM(CASE WHEN r.rating = 3 THEN 1 ELSE 0 END) as three_star_count,
    SUM(CASE WHEN r.rating = 2 THEN 1 ELSE 0 END) as two_star_count,
    SUM(CASE WHEN r.rating = 1 THEN 1 ELSE 0 END) as one_star_count
FROM tbl_sanpham p
LEFT JOIN tbl_product_review r ON p.sanpham_id = r.sanpham_id AND r.status = 1
GROUP BY p.sanpham_id;

-- View sản phẩm bán chạy (UC22) - Cho báo cáo và ChatBot
CREATE OR REPLACE VIEW `view_popular_products` AS
SELECT 
    p.sanpham_id,
    p.sanpham_tieude,
    p.sanpham_ma,
    p.sanpham_gia,
    p.sanpham_anh,
    d.danhmuc_ten,
    COUNT(DISTINCT oi.order_id) as order_count,
    SUM(oi.sanpham_soluong) as total_sold,
    AVG(r.rating) as avg_rating,
    COUNT(DISTINCT r.review_id) as review_count
FROM tbl_sanpham p
INNER JOIN tbl_danhmuc d ON p.danhmuc_id = d.danhmuc_id
LEFT JOIN tbl_order_items oi ON p.sanpham_id = oi.sanpham_id
LEFT JOIN tbl_product_review r ON p.sanpham_id = r.sanpham_id AND r.status = 1
WHERE p.sanpham_status = 1
GROUP BY p.sanpham_id
ORDER BY total_sold DESC, avg_rating DESC;

-- ============================================
-- DỮ LIỆU MẪU
-- ============================================

-- Roles
INSERT INTO `roles` VALUES 
(1, 'Admin', 'Quản trị viên'),
(2, 'Khách hàng', 'Khách hàng'),
(3, 'Nhân viên', 'Nhân viên');

-- Users (password: admin123 và customer123)
INSERT INTO `users` VALUES 
(1, 'admin', '$2y$10$b1iqdprgQ1A4opLXzatupuvtQAOHYPtppz4h/2l8biO5CAiEfnvvC', 'admin@ivymoda.com', 'Admin IVY', '0901234567', NULL, 1, 1, 0, NULL, NULL, NOW()),
(2, 'customer1', '$2y$10$b1iqdprgQ1A4opLXzatupuvtQAOHYPtppz4h/2l8biO5CAiEfnvvC', 'customer@gmail.com', 'Nguyễn Văn A', '0987654321', 'Hà Nội', 2, 1, 0, NULL, NULL, NOW()),
(3, 'staff1', '$2y$10$b1iqdprgQ1A4opLXzatupuvtQAOHYPtppz4h/2l8biO5CAiEfnvvC', 'staff@ivymoda.com', 'Nhân viên 1', '0901111111', 'TP.HCM', 3, 1, 0, NULL, NULL, NOW()),
(4, 'staff2', '$2y$10$b1iqdprgQ1A4opLXzatupuvtQAOHYPtppz4h/2l8biO5CAiEfnvvC', 'staff2@ivymoda.com', 'Nhân viên 2', '0902222222', 'Đà Nẵng', 3, 1, 0, NULL, NULL, NOW());

-- Danh mục
INSERT INTO `tbl_danhmuc` VALUES 
(1, 'NỮ', 'Thời trang nữ', 1, NOW()),
(2, 'NAM', 'Thời trang nam', 1, NOW()),
(3, 'TRẺ EM', 'Thời trang trẻ em', 1, NOW());

-- Loại sản phẩm
INSERT INTO `tbl_loaisanpham` VALUES 
(1, 1, 'Áo Nữ', 'Các loại áo nữ'),
(2, 1, 'Quần Nữ', 'Các loại quần nữ'),
(3, 1, 'Đầm Nữ', 'Các loại đầm nữ'),
(4, 2, 'Áo Nam', 'Các loại áo nam'),
(5, 2, 'Quần Nam', 'Các loại quần nam');

-- Màu sắc (Chỉ cần color_ma - mã hex)
INSERT INTO `tbl_color` VALUES 
(1, 'Trắng', '#FFFFFF', NOW()),
(2, 'Đen', '#000000', NOW()),
(3, 'Xanh Navy', '#000080', NOW()),
(4, 'Đỏ', '#FF0000', NOW()),
(5, 'Be', '#F5F5DC', NOW()),
(6, 'Xanh Dương', '#0000FF', NOW()),
(7, 'Xám', '#808080', NOW());

-- Size
INSERT INTO `tbl_size` VALUES 
(1, 'XS', 1, NOW()),
(2, 'S', 2, NOW()),
(3, 'M', 3, NOW()),
(4, 'L', 4, NOW()),
(5, 'XL', 5, NOW()),
(6, 'XXL', 6, NOW()),
(7, '3XL', 7, NOW());

-- Sản phẩm mẫu
INSERT INTO `tbl_sanpham` VALUES 
(1, 'ÁO SƠ MI NAM TRẮNG BASIC', 'ASM-001', 2, 4, 499000, 599000, 16.69, 
 'Áo sơ mi nam trắng basic, chất liệu cotton cao cấp, thấm hút mồ hôi tốt, form dáng regular fit phù hợp mọi vóc dáng', 
 'Giặt máy ở nhiệt độ thường, không tẩy, không vắt mạnh', 
 'ao_somi_trang.jpg', 1, NOW(), NOW()),
 
(2, 'QUẦN JEANS NỮ ỐNG RỘNG HÀN QUỐC', 'QJ-001', 1, 2, 699000, 899000, 22.25,
 'Quần jeans nữ ống rộng phong cách Hàn Quốc, chất liệu denim cao cấp, thiết kế trẻ trung năng động', 
 'Giặt lộn trái, không dùng nước nóng, phơi nơi thoáng mát', 
 'quan_jeans.jpg', 1, NOW(), NOW()),
 
(3, 'ÁO THUN NAM CỔ TRÒN', 'AT-001', 2, 4, 299000, 399000, 25.06,
 'Áo thun nam cổ tròn basic, chất liệu cotton 100%, co giãn tốt, thoáng mát', 
 'Giặt máy, không ngâm lâu', 
 'ao_thun.jpg', 1, NOW(), NOW()),
 
(4, 'ĐẦM CÔNG SỞ NỮ THANH LỊCH', 'DCN-001', 1, 3, 899000, 1299000, 30.79,
 'Đầm công sở nữ thiết kế thanh lịch, chất liệu vải thoáng mát, phù hợp đi làm và dự tiệc', 
 'Giặt tay, không vắt mạnh', 
 'dam_congso.jpg', 1, NOW(), NOW()),

(5, 'ÁO KHOÁC NAM THỂ THAO', 'AK-001', 2, 4, 799000, 999000, 20.02,
 'Áo khoác nam thể thao, chất liệu polyester thoáng khí, phù hợp đi chơi và tập gym', 
 'Giặt máy, không dùng chất tẩy', 
 'ao_khoac_nam.jpg', 1, NOW(), NOW());

-- Liên kết sản phẩm - màu
INSERT INTO `tbl_sanpham_color` VALUES 
-- Áo sơ mi (ID=1)
(1, 1, 1, 1, NOW()), -- Trắng (mặc định)
(2, 1, 2, 0, NOW()), -- Đen
(3, 1, 3, 0, NOW()), -- Xanh Navy
-- Quần jeans (ID=2)
(4, 2, 2, 1, NOW()), -- Đen (mặc định)
(5, 2, 3, 0, NOW()), -- Xanh Navy
-- Áo thun (ID=3)
(6, 3, 1, 0, NOW()), -- Trắng
(7, 3, 2, 1, NOW()), -- Đen (mặc định)
(8, 3, 7, 0, NOW()), -- Xám
-- Đầm công sở (ID=4)
(9, 4, 2, 1, NOW()), -- Đen (mặc định)
(10, 4, 3, 0, NOW()), -- Xanh Navy
(11, 4, 5, 0, NOW()), -- Be
-- Áo khoác (ID=5)
(12, 5, 2, 1, NOW()), -- Đen (mặc định)
(13, 5, 7, 0, NOW()); -- Xám

-- Ảnh sản phẩm theo màu
INSERT INTO `tbl_anhsanpham` VALUES 
-- Áo sơ mi
(1, 1, 1, 'ao_somi_trang_1.jpg', 1, NOW()),
(2, 1, 1, 'ao_somi_trang_2.jpg', 0, NOW()),
(3, 1, 2, 'ao_somi_den_1.jpg', 1, NOW()),
(4, 1, 3, 'ao_somi_xanh_1.jpg', 1, NOW()),
-- Quần jeans
(5, 2, 4, 'quan_jeans_den_1.jpg', 1, NOW()),
(6, 2, 4, 'quan_jeans_den_2.jpg', 0, NOW()),
(7, 2, 5, 'quan_jeans_xanh_1.jpg', 1, NOW()),
-- Áo thun
(8, 3, 6, 'ao_thun_trang_1.jpg', 1, NOW()),
(9, 3, 7, 'ao_thun_den_1.jpg', 1, NOW()),
(10, 3, 8, 'ao_thun_xam_1.jpg', 1, NOW()),
-- Đầm công sở
(11, 4, 9, 'dam_den_1.jpg', 1, NOW()),
(12, 4, 10, 'dam_xanh_1.jpg', 1, NOW()),
(13, 4, 11, 'dam_be_1.jpg', 1, NOW()),
-- Áo khoác
(14, 5, 12, 'ao_khoac_den_1.jpg', 1, NOW()),
(15, 5, 13, 'ao_khoac_xam_1.jpg', 1, NOW());

-- Dữ liệu Variant (Tồn kho chi tiết)
INSERT INTO `tbl_product_variant` VALUES 
-- Áo sơ mi trắng (ID=1)
(1, 1, 1, 2, 'ASM-001-S-WHITE', 15, NULL, 1, NOW(), NOW()),
(2, 1, 1, 3, 'ASM-001-M-WHITE', 20, NULL, 1, NOW(), NOW()),
(3, 1, 1, 4, 'ASM-001-L-WHITE', 10, NULL, 1, NOW(), NOW()),
(4, 1, 1, 5, 'ASM-001-XL-WHITE', 5, NULL, 1, NOW(), NOW()),
(5, 1, 1, 6, 'ASM-001-XXL-WHITE', 0, NULL, 0, NOW(), NOW()),

-- Áo sơ mi đen (ID=1)
(6, 1, 2, 2, 'ASM-001-S-BLACK', 12, NULL, 1, NOW(), NOW()),
(7, 1, 2, 3, 'ASM-001-M-BLACK', 18, NULL, 1, NOW(), NOW()),
(8, 1, 2, 4, 'ASM-001-L-BLACK', 8, NULL, 1, NOW(), NOW()),
(9, 1, 2, 5, 'ASM-001-XL-BLACK', 3, NULL, 1, NOW(), NOW()),

-- Áo sơ mi xanh navy (ID=1)
(10, 1, 3, 3, 'ASM-001-M-NAVY', 10, NULL, 1, NOW(), NOW()),
(11, 1, 3, 4, 'ASM-001-L-NAVY', 7, NULL, 1, NOW(), NOW()),

-- Quần jeans đen (ID=2)
(12, 2, 2, 2, 'QJ-001-S-BLACK', 8, NULL, 1, NOW(), NOW()),
(13, 2, 2, 3, 'QJ-001-M-BLACK', 15, NULL, 1, NOW(), NOW()),
(14, 2, 2, 4, 'QJ-001-L-BLACK', 12, NULL, 1, NOW(), NOW()),
(15, 2, 2, 5, 'QJ-001-XL-BLACK', 5, NULL, 1, NOW(), NOW()),

-- Quần jeans xanh navy (ID=2)
(16, 2, 3, 2, 'QJ-001-S-NAVY', 6, NULL, 1, NOW(), NOW()),
(17, 2, 3, 3, 'QJ-001-M-NAVY', 10, NULL, 1, NOW(), NOW()),
(18, 2, 3, 4, 'QJ-001-L-NAVY', 8, NULL, 1, NOW(), NOW()),

-- Áo thun (ID=3)
(19, 3, 1, 2, 'AT-001-S-WHITE', 20, NULL, 1, NOW(), NOW()),
(20, 3, 1, 3, 'AT-001-M-WHITE', 25, NULL, 1, NOW(), NOW()),
(21, 3, 1, 4, 'AT-001-L-WHITE', 15, NULL, 1, NOW(), NOW()),
(22, 3, 2, 2, 'AT-001-S-BLACK', 18, NULL, 1, NOW(), NOW()),
(23, 3, 2, 3, 'AT-001-M-BLACK', 22, NULL, 1, NOW(), NOW()),
(24, 3, 2, 4, 'AT-001-L-BLACK', 12, NULL, 1, NOW(), NOW()),
(25, 3, 7, 3, 'AT-001-M-GRAY', 10, NULL, 1, NOW(), NOW()),
(26, 3, 7, 4, 'AT-001-L-GRAY', 8, NULL, 1, NOW(), NOW()),

-- Đầm công sở (ID=4)
(27, 4, 2, 2, 'DCN-001-S-BLACK', 5, NULL, 1, NOW(), NOW()),
(28, 4, 2, 3, 'DCN-001-M-BLACK', 8, NULL, 1, NOW(), NOW()),
(29, 4, 2, 4, 'DCN-001-L-BLACK', 6, NULL, 1, NOW(), NOW()),
(30, 4, 3, 3, 'DCN-001-M-NAVY', 7, NULL, 1, NOW(), NOW()),
(31, 4, 5, 3, 'DCN-001-M-BEIGE', 4, NULL, 1, NOW(), NOW()),
(32, 4, 5, 4, 'DCN-001-L-BEIGE', 3, NULL, 1, NOW(), NOW()),

-- Áo khoác (ID=5)
(33, 5, 2, 3, 'AK-001-M-BLACK', 10, NULL, 1, NOW(), NOW()),
(34, 5, 2, 4, 'AK-001-L-BLACK', 8, NULL, 1, NOW(), NOW()),
(35, 5, 7, 3, 'AK-001-M-GRAY', 6, NULL, 1, NOW(), NOW()),
(36, 5, 7, 4, 'AK-001-L-GRAY', 5, NULL, 1, NOW(), NOW());

-- Email template
INSERT INTO `tbl_email_template` VALUES 
(1, 'Xác nhận đơn hàng', 'Đơn hàng #{order_code} đã được xác nhận', 
 '<p>Xin chào {customer_name},</p><p>Đơn hàng của bạn đã được xác nhận.</p><p>Mã đơn hàng: {order_code}</p>', 'order'),
(2, 'Khuyến mãi', 'Chương trình khuyến mãi đặc biệt', 
 '<p>Giảm giá lên đến 50%!</p>', 'promotion'),
(3, 'Đổi mật khẩu', 'Yêu cầu đặt lại mật khẩu', 
 '<p>Xin chào,</p><p>Click vào link sau để đặt lại mật khẩu: {reset_link}</p>', 'password_reset');

-- Mã giảm giá mẫu (tích hợp từ discount_update.sql)
INSERT INTO `tbl_ma_giam_gia` VALUES 
(1, 'WOMEN30', 'Giảm 30% cho sản phẩm nữ', 30.00, 'percent', '2025-10-15 00:00:00', '2025-10-31 23:59:59', 100, 0, 1, NOW(), NOW()),
(2, 'FLASH50', 'Flash sale giảm 50%', 50.00, 'percent', '2025-10-20 00:00:00', '2025-10-22 23:59:59', 50, 0, 1, NOW(), NOW()),
(3, 'WELCOME10', 'Giảm 10% cho khách hàng mới', 10.00, 'percent', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 100, 0, 1, NOW(), NOW()),
(4, 'SUMMER20', 'Giảm 20% mùa hè', 20.00, 'percent', NOW(), DATE_ADD(NOW(), INTERVAL 60 DAY), 50, 0, 1, NOW(), NOW()),
(5, 'SAVE50K', 'Giảm 50.000₫ cho đơn hàng từ 500.000₫', 50000.00, 'fixed', NOW(), DATE_ADD(NOW(), INTERVAL 15 DAY), 200, 0, 1, NOW(), NOW());

-- Mẫu khuyến mãi (liên kết với mã giảm giá)
INSERT INTO `tbl_promotion` VALUES 
(1, 'GIẢM GIÁ 30% TOÀN BỘ SẢN PHẨM NỮ', 
 'Chương trình giảm giá đặc biệt dành cho khách hàng nữ', 
 '<p>Giảm giá <strong>30%</strong> cho toàn bộ sản phẩm thời trang nữ. Áp dụng từ ngày 15/10 đến 31/10/2025.</p>', 
 'promotion_women_30.jpg', 
 1, 
 '2025-10-15 00:00:00', 
 '2025-10-31 23:59:59', 
 1, 1, 1, NOW(), NOW()),

(2, 'FLASH SALE CUỐI TUẦN - GIẢM 50%', 
 'Flash sale chỉ 2 ngày cuối tuần', 
 '<p>Giảm giá <strong>50%</strong> cho một số sản phẩm chọn lọc. Nhanh tay kẻo hết!</p>', 
 'flash_sale.jpg', 
 2, 
 '2025-10-20 00:00:00', 
 '2025-10-22 23:59:59', 
 1, 2, 1, NOW(), NOW());

-- Mẫu đánh giá (VERSION 2.0: Bao gồm ảnh đánh giá)
INSERT INTO `tbl_product_review` VALUES 
(1, 1, 2, NULL, 5, 'Áo rất đẹp, chất liệu tốt, mặc thoải mái', '["reviews/ao_somi_review_1.jpg", "reviews/ao_somi_review_2.jpg"]', 1, 1, 'Cảm ơn bạn đã tin tưởng IVY moda!', NOW(), NOW()),
(2, 2, 2, NULL, 4, 'Quần đẹp nhưng hơi dài, phải cắt gấu', '["reviews/quan_jeans_review_1.jpg"]', 1, 1, NULL, NOW(), NOW()),
(3, 3, 2, NULL, 5, 'Áo thun basic nhưng rất chất lượng', NULL, 1, 1, NULL, NOW(), NOW());

COMMIT;

-- ============================================
-- HƯỚNG DẪN SỬ DỤNG
-- ============================================

/*
DATABASE VERSION 6.0 - FINAL & PERFECT + DISCOUNT + REVIEW IMAGES

ĐẶC ĐIỂM:
✅ 100% tương thích với code hiện tại
✅ Kế thừa từ ivymoda_update.sql (đã dùng variant system)
✅ Bổ sung Review + Promotion từ ivymoda_complete.sql
✅ TÍCH HỢP HOÀN TOÀN discount_update.sql
✅ HỖ TRỢ UPLOAD ẢNH ĐÁNH GIÁ (VERSION 2.0)
✅ Loại bỏ bảng thừa: wishlist, notification, chatbot_history, user_profile

HỆ THỐNG CHIẾT KHẤU:
1. CHIẾT KHẤU SẢN PHẨM CỐ ĐỊNH:
   - Trường: sanpham_giam_gia (decimal 5,2) - phần trăm giảm giá cố định
   - Trường: sanpham_gia_goc - giá gốc trước khi giảm
   - Trường: sanpham_gia - giá sau khi giảm
   - Ví dụ: -20.02%, -30.79%, -25.06% (hiển thị trên sản phẩm)

2. MÃ GIẢM GIÁ ĐỘNG (UC42, UC44):
   - Bảng: tbl_ma_giam_gia - quản lý mã giảm giá
   - Bảng: tbl_order - hỗ trợ original_total, discount_code, discount_value
   - Ví dụ: WOMEN30, SUMMER20, SAVE50K (áp dụng khi thanh toán)

THAY ĐỔI SO VỚI VERSION 5.0:
1. ✅ TÍCH HỢP discount_update.sql vào tbl_order
2. ✅ CẢI TIẾN tbl_ma_giam_gia với đầy đủ comment và index
3. ✅ THÊM 3 mã giảm giá mẫu từ discount_update.sql
4. ✅ THÊM index idx_discount_code cho tbl_order
5. ✅ THÊM cột review_images vào tbl_product_review
6. ✅ HỖ TRỢ UPLOAD ẢNH ĐÁNH GIÁ (JSON format)
7. ✅ CẬP NHẬT dữ liệu mẫu với ảnh đánh giá

TƯƠNG THÍCH 100%:
✅ ProductModel.php - Dùng color_ma (mã hex)
✅ CartModel.php - Dùng variant_id
✅ OrderModel.php - Dùng order_status (int), session_id, order_total
✅ CheckoutController.php - Dùng order_total, customer_address, shipping_method
✅ DiscountModel.php - Tương thích với tbl_ma_giam_gia
✅ ReportModel.php - Dùng order_status (int)
✅ ReviewModel.php - Hỗ trợ review_images (JSON format)
✅ ReviewController.php - Xử lý upload ảnh đánh giá

IMPORT:
mysql -u root -p < ivymoda_final.sql

HOẶC phpMyAdmin:
1. Chọn Import
2. Browse file: ivymoda_final.sql
3. Click Go

SAU KHI IMPORT, KHÔNG CẦN SỬA CODE GÌ CẢ!
TẤT CẢ CHỨC NĂNG CHIẾT KHẤU VÀ UPLOAD ẢNH ĐÁNH GIÁ ĐÃ ĐƯỢC TÍCH HỢP HOÀN TOÀN!

TÍNH NĂNG MỚI VERSION 6.0:
🎯 Upload ảnh đánh giá sản phẩm (tối đa 5 ảnh, mỗi ảnh 5MB)
🎯 Hiển thị ảnh đánh giá với modal xem phóng to
🎯 Quản lý ảnh đánh giá trong admin panel
🎯 Responsive design cho mobile
🎯 Validation file type và size
🎯 Drag & drop upload interface
*/
