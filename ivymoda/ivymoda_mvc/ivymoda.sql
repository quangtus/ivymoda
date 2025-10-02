-- DATABASE TỐI ƯU CHO NGHIỆM THU (GIỮ QUẢN LÝ MÀU & ẢNH)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `ivymoda` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ivymoda`;

-- ============================================
-- 1. QUẢN TRỊ HỆ THỐNG
-- ============================================

-- Bảng users (UC1.1, UC1.2, UC1.4)
DROP TABLE IF EXISTS `users`;
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
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 2. QUẢN LÝ SẢN PHẨM (GIỮ ĐẦY ĐỦ)
-- ============================================

-- Bảng danh mục (UC2.1)
DROP TABLE IF EXISTS `tbl_danhmuc`;
CREATE TABLE `tbl_danhmuc` (
  `danhmuc_id` int(11) NOT NULL AUTO_INCREMENT,
  `danhmuc_ten` varchar(255) NOT NULL,
  `danhmuc_mo_ta` text DEFAULT NULL,
  `danhmuc_status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`danhmuc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng loại sản phẩm
DROP TABLE IF EXISTS `tbl_loaisanpham`;
CREATE TABLE `tbl_loaisanpham` (
  `loaisanpham_id` int(11) NOT NULL AUTO_INCREMENT,
  `danhmuc_id` int(11) NOT NULL,
  `loaisanpham_ten` varchar(255) NOT NULL,
  `loaisanpham_mo_ta` text DEFAULT NULL,
  PRIMARY KEY (`loaisanpham_id`),
  KEY `fk_loai_danhmuc` (`danhmuc_id`),
  CONSTRAINT `fk_loai_danhmuc` FOREIGN KEY (`danhmuc_id`) REFERENCES `tbl_danhmuc` (`danhmuc_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng màu sắc (GIỮ LẠI - QUAN TRỌNG)
DROP TABLE IF EXISTS `tbl_color`;
CREATE TABLE `tbl_color` (
  `color_id` int(11) NOT NULL AUTO_INCREMENT,
  `color_ten` varchar(255) NOT NULL,
  `color_anh` varchar(255) DEFAULT NULL,
  `color_ma` varchar(20) DEFAULT NULL COMMENT 'Mã màu hex (vd: #FF0000)',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`color_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng sản phẩm (UC2.2) - ĐƠN GIẢN HÓA
DROP TABLE IF EXISTS `tbl_sanpham`;
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
  `sanpham_size` varchar(100) DEFAULT 'S,M,L,XL' COMMENT 'Các size có sẵn',
  `sanpham_soluong` int(11) DEFAULT 0,
  `sanpham_status` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sanpham_id`),
  UNIQUE KEY `sanpham_ma` (`sanpham_ma`),
  KEY `fk_sp_danhmuc` (`danhmuc_id`),
  KEY `fk_sp_loai` (`loaisanpham_id`),
  CONSTRAINT `fk_sp_danhmuc` FOREIGN KEY (`danhmuc_id`) REFERENCES `tbl_danhmuc` (`danhmuc_id`),
  CONSTRAINT `fk_sp_loai` FOREIGN KEY (`loaisanpham_id`) REFERENCES `tbl_loaisanpham` (`loaisanpham_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng trung gian: Sản phẩm - Màu (QUAN TRỌNG - GIỮ LẠI)
DROP TABLE IF EXISTS `tbl_sanpham_color`;
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

-- Bảng ảnh sản phẩm theo màu (QUAN TRỌNG - GIỮ LẠI)
DROP TABLE IF EXISTS `tbl_anhsanpham`;
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

-- ============================================
-- 3. QUẢN LÝ GIỎ HÀNG & ĐỜN HÀNG
-- ============================================

-- Bảng giỏ hàng (UC2.3) - GỘP 2 BẢNG cũ
DROP TABLE IF EXISTS `tbl_cart`;
CREATE TABLE `tbl_cart` (
  `cart_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `sanpham_id` int(11) NOT NULL,
  `sanpham_tieude` varchar(255) NOT NULL,
  `sanpham_gia` decimal(10,2) NOT NULL,
  `sanpham_anh` varchar(255) NOT NULL,
  `sanpham_size` varchar(50) DEFAULT NULL,
  `sanpham_color` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_id`),
  KEY `idx_session` (`session_id`),
  KEY `idx_user` (`user_id`),
  KEY `fk_cart_sp` (`sanpham_id`),
  CONSTRAINT `fk_cart_sp` FOREIGN KEY (`sanpham_id`) REFERENCES `tbl_sanpham` (`sanpham_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng đơn hàng (UC2.4) - ĐƠN GIẢN HÓA
DROP TABLE IF EXISTS `tbl_order`;
CREATE TABLE `tbl_order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_code` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `customer_address` text NOT NULL COMMENT 'Địa chỉ đầy đủ',
  `order_total` decimal(15,2) NOT NULL,
  `order_status` tinyint(1) DEFAULT 0 COMMENT '0:Chờ,1:Đang giao,2:Hoàn thành,3:Hủy',
  `payment_method` varchar(50) DEFAULT 'COD',
  `shipping_method` varchar(50) DEFAULT 'Standard',
  `order_note` text DEFAULT NULL,
  `order_date` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `order_code` (`order_code`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`order_status`),
  CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng chi tiết đơn hàng
DROP TABLE IF EXISTS `tbl_order_items`;
CREATE TABLE `tbl_order_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `sanpham_id` int(11) NOT NULL,
  `sanpham_ten` varchar(255) NOT NULL,
  `sanpham_gia` decimal(10,2) NOT NULL,
  `sanpham_soluong` int(11) NOT NULL,
  `sanpham_size` varchar(50) DEFAULT NULL,
  `sanpham_color` varchar(100) DEFAULT NULL,
  `sanpham_anh` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `fk_item_order` (`order_id`),
  KEY `fk_item_sp` (`sanpham_id`),
  CONSTRAINT `fk_item_order` FOREIGN KEY (`order_id`) REFERENCES `tbl_order` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_item_sp` FOREIGN KEY (`sanpham_id`) REFERENCES `tbl_sanpham` (`sanpham_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng mã giảm giá (UC2.6)
DROP TABLE IF EXISTS `tbl_ma_giam_gia`;
CREATE TABLE `tbl_ma_giam_gia` (
  `ma_id` int(11) NOT NULL AUTO_INCREMENT,
  `ma_code` varchar(20) NOT NULL,
  `ma_loai` tinyint(1) DEFAULT 1 COMMENT '1:%, 2:VNĐ',
  `ma_giatri` decimal(10,2) NOT NULL,
  `ma_dieukien` decimal(15,2) DEFAULT 0 COMMENT 'Đơn hàng tối thiểu',
  `ma_batdau` datetime NOT NULL,
  `ma_ketthuc` datetime NOT NULL,
  `ma_soluong` int(11) DEFAULT NULL,
  `ma_dadung` int(11) DEFAULT 0,
  `ma_trangthai` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`ma_id`),
  UNIQUE KEY `ma_code` (`ma_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 4. BÁO CÁO (UC2.7)
-- ============================================

-- Bảng thống kê
DROP TABLE IF EXISTS `tbl_thong_ke`;
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
-- 5. EMAIL (UC1.3, UC3.3)
-- ============================================

-- Bảng template email
DROP TABLE IF EXISTS `tbl_email_template`;
CREATE TABLE `tbl_email_template` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(100) NOT NULL,
  `template_subject` varchar(255) NOT NULL,
  `template_body` text NOT NULL,
  `template_type` varchar(50) NOT NULL COMMENT 'order, promotion, password_reset',
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng log email
DROP TABLE IF EXISTS `tbl_email_log`;
CREATE TABLE `tbl_email_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `email_to` varchar(255) NOT NULL,
  `email_subject` varchar(255) NOT NULL,
  `email_body` text NOT NULL,
  `email_status` tinyint(1) DEFAULT 0 COMMENT '0:Thất bại, 1:Thành công',
  `sent_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- DỮ LIỆU MẪU
-- ============================================

-- Users
INSERT INTO `users` VALUES 
(1, 'admin', '$2y$10$b1iqdprgQ1A4opLXzatupuvtQAOHYPtppz4h/2l8biO5CAiEfnvvC', 'admin@ivymoda.com', 'Admin IVY', NULL, NULL, 1, 1, 0, NULL, NULL, NOW());

-- Roles
INSERT INTO `roles` VALUES 
(1, 'Admin', 'Quản trị viên'),
(2, 'Khách hàng', 'Khách hàng'),
(3, 'Nhân viên', 'Nhân viên');

-- Danh mục
INSERT INTO `tbl_danhmuc` VALUES 
(1, 'NỮ', 'Thời trang nữ', 1, NOW()),
(2, 'NAM', 'Thời trang nam', 1, NOW()),
(3, 'TRẺ EM', 'Thời trang trẻ em', 1, NOW());

-- Loại sản phẩm
INSERT INTO `tbl_loaisanpham` VALUES 
(1, 1, 'Áo Nữ', NULL),
(2, 1, 'Quần Nữ', NULL),
(3, 1, 'Đầm Nữ', NULL),
(4, 2, 'Áo Nam', NULL),
(5, 2, 'Quần Nam', NULL);

-- Màu sắc (QUAN TRỌNG)
INSERT INTO `tbl_color` VALUES 
(1, 'Trắng', 'white.png', '#FFFFFF', NOW()),
(2, 'Đen', 'black.png', '#000000', NOW()),
(3, 'Xanh Navy', 'navy.png', '#000080', NOW()),
(4, 'Đỏ', 'red.png', '#FF0000', NOW()),
(5, 'Be', 'beige.png', '#F5F5DC', NOW());

-- Sản phẩm mẫu
INSERT INTO `tbl_sanpham` VALUES 
(1, 'ÁO SƠ MI NAM TRẮNG', 'ASM-001', 2, 4, 499000, 599000, 16.69, 
 'Áo sơ mi nam trắng basic, chất liệu cotton cao cấp', 'Giặt máy, không tẩy', 
 'ao_somi_trang.jpg', 'S,M,L,XL', 100, 1, NOW(), NOW()),
 
(2, 'QUẦN JEANS NỮ ỐNG RỘNG', 'QJ-001', 1, 2, 699000, 899000, 22.25,
 'Quần jeans nữ ống rộng phong cách Hàn Quốc', 'Giặt lộn trái', 
 'quan_jeans.jpg', 'S,M,L,XL', 50, 1, NOW(), NOW());

-- Liên kết sản phẩm - màu
INSERT INTO `tbl_sanpham_color` VALUES 
(1, 1, 1, 1, NOW()), -- Áo sơ mi - Trắng (mặc định)
(2, 1, 2, 0, NOW()), -- Áo sơ mi - Đen
(3, 2, 2, 1, NOW()), -- Quần jeans - Đen (mặc định)
(4, 2, 3, 0, NOW()); -- Quần jeans - Xanh Navy

-- Ảnh sản phẩm theo màu
INSERT INTO `tbl_anhsanpham` VALUES 
(1, 1, 1, 'ao_somi_trang_1.jpg', 1, NOW()),
(2, 1, 1, 'ao_somi_trang_2.jpg', 0, NOW()),
(3, 1, 2, 'ao_somi_den_1.jpg', 1, NOW()),
(4, 2, 3, 'quan_jeans_den_1.jpg', 1, NOW()),
(5, 2, 4, 'quan_jeans_xanh_1.jpg', 1, NOW());

-- Email template
INSERT INTO `tbl_email_template` VALUES 
(1, 'Xác nhận đơn hàng', 'Đơn hàng #{order_code} đã được xác nhận', 
 '<p>Xin chào {customer_name},</p><p>Đơn hàng của bạn đã được xác nhận.</p>', 'order'),
(2, 'Khuyến mãi', 'Chương trình khuyến mãi đặc biệt', 
 '<p>Giảm giá lên đến 50%!</p>', 'promotion');

COMMIT;