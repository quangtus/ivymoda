-- ============================================
-- IVYMODA DATABASE - FINAL VERSION 7.2
-- ============================================
-- Kế thừa từ: ivymoda_update.sql (100% tương thích code)
-- Bổ sung: Review, Promotion (từ ivymoda_complete.sql)
-- Bổ sung: Email Activation System (VERSION 7.0)
-- Bổ sung: Chatbot System (VERSION 7.2 - UC3.47, UC3.48)
-- Loại bỏ: Các bảng thừa (wishlist, notification)
-- Tương thích: 100% với code hiện tại
-- Ngày tạo: 2025-01-14
-- Cập nhật: 2025-10-18 - Thêm hệ thống Chatbot
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

-- Bảng users (UC01-06) - VERSION 7.0: Hỗ trợ kích hoạt tài khoản qua email
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
  `activation_token` varchar(100) DEFAULT NULL COMMENT 'Token kích hoạt tài khoản qua email',
  `activation_token_expire` datetime DEFAULT NULL COMMENT 'Thời gian hết hạn token kích hoạt',
  `email_notifications` tinyint(1) DEFAULT 1 COMMENT '1: Nhận thông báo email, 0: Không nhận',
  `promotion_emails` tinyint(1) DEFAULT 1 COMMENT '1: Nhận email khuyến mãi, 0: Không nhận',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_role` (`role_id`),
  KEY `idx_activation_token` (`activation_token`),
  CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Bảng người dùng - Hỗ trợ kích hoạt tài khoản qua email';

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

-- Log gửi email khuyến mãi (UC3.50) - Đơn giản hóa
CREATE TABLE `tbl_promotion_email_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `promotion_title` varchar(255) NOT NULL COMMENT 'Tiêu đề khuyến mãi',
  `recipient_email` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `sent_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `status` enum('sent','failed','pending') DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `idx_status` (`status`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `fk_promo_email_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Log gửi email khuyến mãi - UC3.50 - Đơn giản hóa';

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
-- 7. EMAIL SYSTEM (UC3.50 - Tích hợp Email)
-- ============================================

-- Bảng template email - Chỉ giữ các template cơ bản theo UC
CREATE TABLE `tbl_email_template` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng log email - Đơn giản hóa theo UC
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
-- 8. CHATBOT SYSTEM (UC3.47, UC3.48)
-- ============================================

-- Bảng FAQ cho chatbot (UC3.48 - Chatbot hướng dẫn sử dụng hệ thống)
CREATE TABLE `tbl_chatbot_faq` (
  `faq_id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(500) NOT NULL COMMENT 'Câu hỏi hiển thị cho người dùng',
  `answer` text NOT NULL COMMENT 'Câu trả lời chi tiết (có thể chứa HTML)',
  `category` varchar(100) NOT NULL COMMENT 'Danh mục FAQ (Đăng ký, Đặt hàng, Thanh toán...)',
  `display_order` int(11) DEFAULT 0 COMMENT 'Thứ tự hiển thị',
  `status` tinyint(1) DEFAULT 1 COMMENT '1: Active, 0: Inactive',
  `help_link` varchar(255) DEFAULT NULL COMMENT 'Link hướng dẫn chi tiết',
  `created_by` int(11) DEFAULT NULL COMMENT 'Admin tạo FAQ',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`faq_id`),
  KEY `idx_category` (`category`),
  KEY `idx_status_order` (`status`, `display_order`),
  KEY `idx_created_by` (`created_by`),
  CONSTRAINT `fk_faq_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='FAQ cho chatbot hướng dẫn - UC3.48';

-- Bảng lịch sử hội thoại chatbot (UC3.47 - Chatbot tư vấn sản phẩm)
CREATE TABLE `tbl_chatbot_conversation` (
  `conversation_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'Null nếu khách vãng lai',
  `session_id` varchar(100) NOT NULL COMMENT 'Session ID để nhóm các tin nhắn',
  `user_message` text NOT NULL COMMENT 'Tin nhắn từ người dùng',
  `bot_response` text NOT NULL COMMENT 'Câu trả lời từ chatbot/Gemini AI',
  `context_data` text DEFAULT NULL COMMENT 'Context gửi cho Gemini (JSON)',
  `suggested_products` text DEFAULT NULL COMMENT 'Danh sách sản phẩm gợi ý (JSON)',
  `response_time` int(11) DEFAULT NULL COMMENT 'Thời gian phản hồi (milliseconds)',
  `is_from_faq` tinyint(1) DEFAULT 0 COMMENT '1: Câu trả lời từ FAQ, 0: Từ Gemini AI',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`conversation_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_session_id` (`session_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_conversation_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Lịch sử hội thoại chatbot - UC3.47';

-- Bảng cấu hình chatbot (UC3.47)
CREATE TABLE `tbl_chatbot_config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `config_key` varchar(100) NOT NULL COMMENT 'Tên cấu hình',
  `config_value` text NOT NULL COMMENT 'Giá trị cấu hình',
  `description` text DEFAULT NULL COMMENT 'Mô tả cấu hình',
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`config_id`),
  UNIQUE KEY `config_key` (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Cấu hình chatbot - UC3.47';

-- Bảng sở thích người dùng (Tùy chọn - UC3.47)
CREATE TABLE `tbl_user_preferences` (
  `preference_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `favorite_colors` varchar(255) DEFAULT NULL COMMENT 'Màu sắc yêu thích (JSON array)',
  `favorite_categories` varchar(255) DEFAULT NULL COMMENT 'Danh mục yêu thích (JSON array)',
  `size_preference` varchar(50) DEFAULT NULL COMMENT 'Size thường mặc',
  `price_range` varchar(100) DEFAULT NULL COMMENT 'Khoảng giá mong muốn',
  `skin_tone` varchar(50) DEFAULT NULL COMMENT 'Màu da',
  `height` varchar(20) DEFAULT NULL COMMENT 'Chiều cao',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`preference_id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `fk_pref_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Sở thích người dùng cho chatbot cá nhân hóa - UC3.47';

-- ============================================
-- 9. VIEW HỮU ÍCH
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

-- Users (password: admin123 và customer123) - VERSION 7.0: Thêm activation token
INSERT INTO `users` VALUES 
(1, 'admin', '$2y$10$b1iqdprgQ1A4opLXzatupuvtQAOHYPtppz4h/2l8biO5CAiEfnvvC', 'admin@ivymoda.com', 'Admin IVY', '0901234567', NULL, 1, 1, 0, NULL, NULL, NULL, NULL, 1, 1, NOW()),
(2, 'customer1', '$2y$10$b1iqdprgQ1A4opLXzatupuvtQAOHYPtppz4h/2l8biO5CAiEfnvvC', 'customer@gmail.com', 'Nguyễn Văn A', '0987654321', 'Hà Nội', 2, 1, 0, NULL, NULL, NULL, NULL, 1, 1, NOW()),
(3, 'staff1', '$2y$10$b1iqdprgQ1A4opLXzatupuvtQAOHYPtppz4h/2l8biO5CAiEfnvvC', 'staff@ivymoda.com', 'Nhân viên 1', '0901111111', 'TP.HCM', 3, 1, 0, NULL, NULL, NULL, NULL, 1, 1, NOW()),
(4, 'staff2', '$2y$10$b1iqdprgQ1A4opLXzatupuvtQAOHYPtppz4h/2l8biO5CAiEfnvvC', 'staff2@ivymoda.com', 'Nhân viên 2', '0902222222', 'Đà Nẵng', 3, 1, 0, NULL, NULL, NULL, NULL, 1, 1, NOW());

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

-- ============================================
-- EMAIL TEMPLATES - HOÀN CHỈNH THEO UC3.50
-- ============================================
-- Template xác nhận đăng ký
INSERT INTO `tbl_email_template` VALUES 
(1, 'registration_confirmation', 'Xác nhận đăng ký tài khoản - IVY Moda', 
'<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .button { display: inline-block; background-color: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Chào mừng đến với IVY Moda!</h2>
        </div>
        <div class="content">
            <p>Xin chào <strong>{username}</strong>,</p>
            <p>Cảm ơn bạn đã đăng ký tài khoản tại IVY Moda. Để kích hoạt tài khoản, vui lòng click vào link bên dưới:</p>
            <p style="text-align: center;">
                <a href="{activation_link}" class="button">Kích hoạt tài khoản</a>
            </p>
            <p>Link này có hiệu lực trong 24 giờ.</p>
            <p>Nếu bạn không thực hiện đăng ký này, vui lòng bỏ qua email này.</p>
        </div>
        <div class="footer">
            <p>© 2025 IVY Moda. Tất cả quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>', 'registration'),

-- Template xác nhận đơn hàng
(2, 'order_confirmation', 'Xác nhận đơn hàng #{order_code} - IVY Moda', 
'<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .order-info { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f5f5f5; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Đơn hàng của bạn đã được xác nhận!</h2>
        </div>
        <div class="content">
            <p>Xin chào <strong>{customer_name}</strong>,</p>
            <p>Cảm ơn bạn đã mua sắm tại IVY Moda. Đơn hàng của bạn đã được xác nhận và đang được xử lý.</p>
            
            <div class="order-info">
                <h3>Thông tin đơn hàng</h3>
                <p><strong>Mã đơn hàng:</strong> #{order_code}</p>
                <p><strong>Ngày đặt:</strong> {order_date}</p>
                <p><strong>Tổng tiền:</strong> {order_total} ₫</p>
                <p><strong>Phương thức thanh toán:</strong> {payment_method}</p>
                <p><strong>Địa chỉ giao hàng:</strong> {customer_address}</p>
            </div>
            
            <h3>Chi tiết sản phẩm:</h3>
            {order_items}
            
            <p>Chúng tôi sẽ thông báo cho bạn khi đơn hàng được giao.</p>
        </div>
        <div class="footer">
            <p>© 2025 IVY Moda. Tất cả quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>', 'order'),

-- Template đặt lại mật khẩu
(3, 'password_reset', 'Đặt lại mật khẩu - IVY Moda', 
'<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .button { display: inline-block; background-color: #dc3545; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Đặt lại mật khẩu</h2>
        </div>
        <div class="content">
            <p>Xin chào <strong>{username}</strong>,</p>
            <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn. Click vào link bên dưới để đặt lại mật khẩu:</p>
            <p style="text-align: center;">
                <a href="{reset_link}" class="button">Đặt lại mật khẩu</a>
            </p>
            <p>Link này có hiệu lực trong {expiry_time}.</p>
            <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
        </div>
        <div class="footer">
            <p>© 2025 IVY Moda. Tất cả quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>', 'password_reset'),

-- Template khuyến mãi
(4, 'promotion', '{promotion_title} - IVY Moda', 
'<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #e74c3c; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .promotion-box { background-color: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0; border: 2px solid #e74c3c; }
        .button { display: inline-block; background-color: #e74c3c; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: bold; }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🎉 {promotion_title}</h2>
        </div>
        <div class="content">
            <p>Xin chào <strong>{customer_name}</strong>,</p>
            <div class="promotion-box">
                <h3>Chương trình khuyến mãi đặc biệt dành riêng cho bạn!</h3>
                {content}
                <p><strong>Thời gian:</strong> {start_date} - {end_date}</p>
            </div>
            <p style="text-align: center;">
                <a href="#" class="button">MUA NGAY</a>
            </p>
            <p>Đừng bỏ lỡ cơ hội mua sắm với giá tốt nhất!</p>
        </div>
        <div class="footer">
            <p>© 2025 IVY Moda. Tất cả quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>', 'promotion');

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

-- Dữ liệu FAQ cho chatbot (UC3.48)
INSERT INTO `tbl_chatbot_faq` VALUES 
(1, 'Làm thế nào để đăng ký tài khoản?', 
 '<p>Để đăng ký tài khoản trên IVY Moda, bạn thực hiện các bước sau:</p><ol><li>Click vào nút <strong>"Đăng ký"</strong> ở góc trên cùng</li><li>Điền đầy đủ thông tin: Họ tên, Email, Số điện thoại, Mật khẩu</li><li>Click <strong>"Đăng ký"</strong></li><li>Kiểm tra email và click vào link kích hoạt tài khoản</li></ol><p>Tài khoản của bạn sẽ được kích hoạt sau khi xác thực email.</p>', 
 'Đăng ký & Đăng nhập', 1, 1, NULL, 1, NOW(), NOW()),

(2, 'Tôi quên mật khẩu, phải làm sao?', 
 '<p>Để khôi phục mật khẩu:</p><ol><li>Click vào <strong>"Quên mật khẩu?"</strong> ở trang đăng nhập</li><li>Nhập email đã đăng ký</li><li>Kiểm tra email và click vào link đặt lại mật khẩu</li><li>Nhập mật khẩu mới và xác nhận</li></ol><p>Link đặt lại mật khẩu có hiệu lực trong 1 giờ.</p>', 
 'Đăng ký & Đăng nhập', 2, 1, NULL, 1, NOW(), NOW()),

(3, 'Làm thế nào để đặt hàng?', 
 '<p>Quy trình đặt hàng rất đơn giản:</p><ol><li>Tìm kiếm và chọn sản phẩm bạn muốn mua</li><li>Chọn màu sắc và size phù hợp</li><li>Click <strong>"Thêm vào giỏ hàng"</strong></li><li>Vào giỏ hàng và click <strong>"Thanh toán"</strong></li><li>Điền thông tin giao hàng</li><li>Chọn phương thức thanh toán (COD hoặc MoMo)</li><li>Xác nhận đơn hàng</li></ol><p>Bạn sẽ nhận được email xác nhận đơn hàng ngay sau khi đặt thành công.</p>', 
 'Đặt hàng', 3, 1, NULL, 1, NOW(), NOW()),

(4, 'Có những phương thức thanh toán nào?', 
 '<p>IVY Moda hỗ trợ 2 phương thức thanh toán:</p><ul><li><strong>COD (Thanh toán khi nhận hàng):</strong> Bạn thanh toán bằng tiền mặt khi nhận được hàng</li><li><strong>MoMo:</strong> Thanh toán online qua ví điện tử MoMo</li></ul><p>Cả hai phương thức đều an toàn và bảo mật.</p>', 
 'Thanh toán', 4, 1, NULL, 1, NOW(), NOW()),

(5, 'Làm thế nào để theo dõi đơn hàng?', 
 '<p>Để theo dõi đơn hàng của bạn:</p><ol><li>Đăng nhập vào tài khoản</li><li>Vào mục <strong>"Đơn hàng của tôi"</strong></li><li>Xem danh sách tất cả đơn hàng và trạng thái</li></ol><p>Trạng thái đơn hàng bao gồm:</p><ul><li>Chờ xác nhận</li><li>Đã xác nhận</li><li>Đang giao</li><li>Đã giao</li><li>Đã hủy</li></ul>', 
 'Đơn hàng', 5, 1, NULL, 1, NOW(), NOW()),

(6, 'Tôi có thể hủy đơn hàng không?', 
 '<p>Có, bạn có thể hủy đơn hàng khi:</p><ul><li>Đơn hàng đang ở trạng thái <strong>"Chờ xác nhận"</strong></li><li>Đơn hàng chưa được giao cho đơn vị vận chuyển</li></ul><p>Để hủy đơn hàng:</p><ol><li>Vào <strong>"Đơn hàng của tôi"</strong></li><li>Chọn đơn hàng cần hủy</li><li>Click <strong>"Hủy đơn hàng"</strong></li></ol><p>Lưu ý: Đơn hàng đã xác nhận hoặc đang giao không thể hủy trực tuyến. Vui lòng liên hệ hotline để được hỗ trợ.</p>', 
 'Đơn hàng', 6, 1, NULL, 1, NOW(), NOW()),

(7, 'Làm thế nào để sử dụng mã giảm giá?', 
 '<p>Để áp dụng mã giảm giá:</p><ol><li>Thêm sản phẩm vào giỏ hàng</li><li>Vào trang <strong>"Thanh toán"</strong></li><li>Tìm ô <strong>"Nhập mã giảm giá"</strong></li><li>Nhập mã và click <strong>"Áp dụng"</strong></li></ol><p>Hệ thống sẽ tự động tính toán và hiển thị giá sau khi giảm.</p><p>Lưu ý: Mỗi đơn hàng chỉ áp dụng được 1 mã giảm giá.</p>', 
 'Khuyến mãi', 7, 1, NULL, 1, NOW(), NOW()),

(8, 'Chính sách đổi trả như thế nào?', 
 '<p>IVY Moda có chính sách đổi trả linh hoạt:</p><ul><li><strong>Thời gian:</strong> Trong vòng 7 ngày kể từ khi nhận hàng</li><li><strong>Điều kiện:</strong> Sản phẩm chưa qua sử dụng, còn nguyên tem mác, không bị hư hỏng</li><li><strong>Chi phí:</strong> Miễn phí đổi hàng (nếu lỗi từ nhà sản xuất), khách hàng chịu phí ship khi đổi size/màu</li></ul><p>Để đổi trả, vui lòng liên hệ hotline: <strong>0901234567</strong></p>', 
 'Chính sách', 8, 1, NULL, 1, NOW(), NOW()),

(9, 'Làm thế nào để xem size chart?', 
 '<p>Để xem bảng size chi tiết:</p><ol><li>Vào trang chi tiết sản phẩm</li><li>Tìm phần <strong>"Hướng dẫn chọn size"</strong></li><li>Click để xem bảng size</li></ol><p>Mỗi sản phẩm có bảng size riêng phù hợp với thiết kế. Nếu cần tư vấn thêm, hãy chat với chúng tôi!</p>', 
 'Sản phẩm', 9, 1, NULL, 1, NOW(), NOW()),

(10, 'Làm thế nào để liên hệ với bộ phận hỗ trợ?', 
 '<p>Bạn có thể liên hệ với chúng tôi qua:</p><ul><li><strong>Hotline:</strong> 0901234567 (8:00 - 22:00 hàng ngày)</li><li><strong>Email:</strong> support@ivymoda.com</li><li><strong>Chatbot:</strong> Ngay trên website này (góc dưới bên phải)</li><li><strong>Facebook:</strong> fb.com/ivymoda</li></ul><p>Chúng tôi luôn sẵn sàng hỗ trợ bạn!</p>', 
 'Hỗ trợ', 10, 1, NULL, 1, NOW(), NOW());

-- Cấu hình chatbot (UC3.47)
INSERT INTO `tbl_chatbot_config` VALUES 
(1, 'gemini_api_key', 'AIzaSyA6RZuA5V6DqAXWcdHMqXgn0Dxe_GEVIak', 'API key của Gemini AI để tư vấn sản phẩm', NOW()),
(2, 'max_products_suggest', '5', 'Số lượng sản phẩm gợi ý tối đa mỗi lần', NOW()),
(3, 'context_max_length', '2000', 'Độ dài context tối đa gửi cho Gemini (ký tự)', NOW()),
(4, 'response_timeout', '3000', 'Thời gian chờ phản hồi tối đa (milliseconds)', NOW()),
(5, 'chatbot_welcome_message', 'Xin chào! Tôi có thể giúp gì cho bạn hôm nay? 😊', 'Lời chào mặc định của chatbot', NOW()),
(6, 'enable_faq_mode', '1', 'Bật/tắt chế độ FAQ (1: bật, 0: tắt)', NOW()),
(7, 'enable_gemini_mode', '1', 'Bật/tắt chế độ Gemini AI (1: bật, 0: tắt)', NOW()),
(8, 'chatbot_position', 'bottom-right', 'Vị trí hiển thị chatbot (bottom-right, bottom-left)', NOW());

-- Dữ liệu mẫu sở thích người dùng (cho khách hàng ID=2)
INSERT INTO `tbl_user_preferences` VALUES 
(1, 2, '["Trắng", "Đen", "Be"]', '["Áo Nữ", "Đầm Nữ"]', 'M', '500000-1000000', 'Sáng', '160cm', NOW(), NOW());

COMMIT;

-- ============================================
-- HƯỚNG DẪN SỬ DỤNG
-- ============================================

/*
DATABASE VERSION 7.2 - CHATBOT SYSTEM INTEGRATION (UC3.47, UC3.48)

ĐẶC ĐIỂM:
✅ 100% tương thích với code hiện tại
✅ Kế thừa từ ivymoda_update.sql (đã dùng variant system)
✅ Bổ sung Review + Promotion từ ivymoda_complete.sql
✅ TÍCH HỢP HOÀN TOÀN discount_update.sql
✅ HỖ TRỢ UPLOAD ẢNH ĐÁNH GIÁ (VERSION 2.0)
✅ HỖ TRỢ KÍCH HOẠT TÀI KHOẢN QUA EMAIL (VERSION 7.0)
✅ EMAIL SYSTEM TỐI ƯU THEO UC3.50 (VERSION 7.1)
✅ CHATBOT SYSTEM (VERSION 7.2 - UC3.47, UC3.48)
✅ Loại bỏ bảng thừa: wishlist, notification

HỆ THỐNG CHATBOT (VERSION 7.2):
1. FAQ CHATBOT (UC3.48):
   - Bảng: tbl_chatbot_faq
   - Quản lý câu hỏi thường gặp
   - Admin có thể CRUD FAQ qua panel
   - Hỗ trợ phân loại theo category
   - Sắp xếp thứ tự hiển thị
   - 10 FAQ mẫu đã được thêm vào

2. GEMINI AI CHATBOT (UC3.47):
   - Bảng: tbl_chatbot_conversation - Lưu lịch sử chat
   - Bảng: tbl_chatbot_config - Cấu hình API key và settings
   - Tích hợp Gemini AI để tư vấn sản phẩm
   - Gợi ý sản phẩm dựa trên context database
   - Lưu lịch sử hội thoại theo session
   - Tracking response time

3. USER PREFERENCES (UC3.47):
   - Bảng: tbl_user_preferences
   - Lưu sở thích người dùng (màu, size, giá...)
   - Hỗ trợ chatbot cá nhân hóa
   - Tùy chọn, không bắt buộc

HỆ THỐNG EMAIL ACTIVATION (UC3.50):
1. KÍCH HOẠT TÀI KHOẢN QUA EMAIL:
   - Trường: activation_token (varchar 100) - Token kích hoạt tài khoản
   - Trường: activation_token_expire (datetime) - Thời gian hết hạn token (24h)
   - Index: idx_activation_token - Tìm kiếm nhanh token
   - Luồng: Đăng ký → Gửi email → Click link → Kích hoạt tài khoản

2. RESET PASSWORD QUA EMAIL:
   - Trường: reset_token (varchar 100) - Token đặt lại mật khẩu
   - Trường: reset_token_expire (datetime) - Thời gian hết hạn token (1h)
   - Luồng: Quên mật khẩu → Gửi email → Click link → Đặt lại mật khẩu

3. EMAIL SYSTEM TỐI ƯU (UC3.50):
   - Chỉ giữ 4 template cơ bản: registration, order, password_reset, promotion
   - Đơn giản hóa tbl_promotion_email_log (bỏ FK promotion_id)
   - Giữ nguyên tbl_email_log cho logging cơ bản
   - Loại bỏ các tính năng phức tạp không cần thiết

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

THAY ĐỔI SO VỚI VERSION 6.0:
1. ✅ THÊM activation_token và activation_token_expire vào bảng users
2. ✅ THÊM index idx_activation_token cho tìm kiếm nhanh
3. ✅ CẬP NHẬT comment bảng users với thông tin kích hoạt email
4. ✅ HỖ TRỢ HOÀN TOÀN chức năng kích hoạt tài khoản qua email
5. ✅ TƯƠNG THÍCH với AuthController::activate() và UserModel::activateAccount()

TƯƠNG THÍCH 100%:
✅ ProductModel.php - Dùng color_ma (mã hex)
✅ CartModel.php - Dùng variant_id
✅ OrderModel.php - Dùng order_status (int), session_id, order_total
✅ CheckoutController.php - Dùng order_total, customer_address, shipping_method
✅ DiscountModel.php - Tương thích với tbl_ma_giam_gia
✅ ReportModel.php - Dùng order_status (int)
✅ ReviewModel.php - Hỗ trợ review_images (JSON format)
✅ ReviewController.php - Xử lý upload ảnh đánh giá
✅ AuthController.php - Hỗ trợ activate() và index()
✅ UserModel.php - Hỗ trợ activateAccount() và createActivationToken()
✅ EmailHelper.php - Tạo activation link và gửi email

IMPORT:
mysql -u root -p < ivymoda_final.sql

HOẶC phpMyAdmin:
1. Chọn Import
2. Browse file: ivymoda_final.sql
3. Click Go

SAU KHI IMPORT, KHÔNG CẦN SỬA CODE GÌ CẢ!
TẤT CẢ CHỨC NĂNG EMAIL ACTIVATION ĐÃ ĐƯỢC TÍCH HỢP HOÀN TOÀN!

TÍNH NĂNG EMAIL SYSTEM VERSION 7.1 (UC3.50):
🎯 Kích hoạt tài khoản qua email với token bảo mật (24h)
🎯 Reset mật khẩu qua email với token có thời hạn (1h)
🎯 Gửi email xác nhận đăng ký tự động
🎯 Gửi email xác nhận đơn hàng
🎯 Gửi email khuyến mãi hàng loạt (đơn giản hóa)
🎯 Dashboard email với thống kê cơ bản
🎯 Xem log email với phân trang
🎯 Test email cho các loại email chính
🎯 Cấu hình SMTP cơ bản
🎯 Responsive design cho mobile
🎯 Validation token và bảo mật
🎯 Admin panel quản lý email tối ưu theo UC
*/
