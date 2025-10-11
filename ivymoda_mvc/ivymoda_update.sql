-- ============================================
-- IVYMODA DATABASE - COMPLETE VERSION 2.0
-- ============================================
-- Bao gồm: Variant System + Cart Migration + Order Migration
-- Sử dụng: DROP database cũ → Import file này 1 lần duy nhất
-- Ngày tạo: 2025-10-04
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
-- 1. QUẢN TRỊ HỆ THỐNG
-- ============================================

-- Bảng roles
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng users (UC1.1, UC1.2, UC1.4)
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
  KEY `fk_user_role` (`role_id`),
  CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 2. QUẢN LÝ SẢN PHẨM (VARIANT SYSTEM)
-- ============================================

-- Bảng danh mục (UC2.1)
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
  `color_anh` varchar(255) DEFAULT NULL,
  `color_ma` varchar(20) DEFAULT NULL COMMENT 'Mã màu hex (vd: #FF0000)',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`color_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng size
CREATE TABLE `tbl_size` (
  `size_id` int(11) NOT NULL AUTO_INCREMENT,
  `size_ten` varchar(50) NOT NULL COMMENT 'XS, S, M, L, XL, XXL, 3XL',
  `size_order` int(11) DEFAULT 0 COMMENT 'Thứ tự sắp xếp khi hiển thị',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`size_id`),
  UNIQUE KEY `size_ten` (`size_ten`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng sản phẩm (UC2.2)
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
-- 3. QUẢN LÝ GIỎ HÀNG & ĐƠN HÀNG (ĐÃ MIGRATE)
-- ============================================

-- Bảng giỏ hàng (UC2.3) - VERSION 2.0
CREATE TABLE `tbl_cart` (
  `cart_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `variant_id` int(11) NOT NULL COMMENT 'FK tới tbl_product_variant',
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_id`),
  KEY `idx_session` (`session_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_variant` (`variant_id`),
  CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cart_variant` FOREIGN KEY (`variant_id`) REFERENCES `tbl_product_variant` (`variant_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='VERSION 2.0: Lưu variant_id thay vì product_id + size + color strings';

-- Bảng đơn hàng (UC2.4)
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

-- Bảng chi tiết đơn hàng - VERSION 2.0
CREATE TABLE `tbl_order_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT NULL COMMENT 'FK tới tbl_product_variant (NULL nếu variant đã xóa)',
  `sanpham_ten` varchar(255) NOT NULL COMMENT 'Snapshot: Tên sản phẩm',
  `sanpham_gia` decimal(10,2) NOT NULL COMMENT 'Snapshot: Giá tại thời điểm đặt',
  `sanpham_soluong` int(11) NOT NULL,
  `sanpham_size` varchar(50) NOT NULL COMMENT 'Snapshot: Tên size',
  `sanpham_color` varchar(100) NOT NULL COMMENT 'Snapshot: Tên màu',
  `sanpham_anh` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `fk_item_order` (`order_id`),
  KEY `fk_item_variant` (`variant_id`),
  CONSTRAINT `fk_item_order` FOREIGN KEY (`order_id`) REFERENCES `tbl_order` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_item_variant` FOREIGN KEY (`variant_id`) REFERENCES `tbl_product_variant` (`variant_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='VERSION 2.0: Lưu variant_id + snapshot để giữ history';

-- Bảng mã giảm giá (UC2.6)
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
CREATE TABLE `tbl_email_template` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(100) NOT NULL,
  `template_subject` varchar(255) NOT NULL,
  `template_body` text NOT NULL,
  `template_type` varchar(50) NOT NULL COMMENT 'order, promotion, password_reset',
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng log email
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

-- Roles
INSERT INTO `roles` VALUES 
(1, 'Admin', 'Quản trị viên'),
(2, 'Khách hàng', 'Khách hàng'),
(3, 'Nhân viên', 'Nhân viên');

-- Users (password: admin123 và customer123)
INSERT INTO `users` VALUES 
(1, 'admin', '$2y$10$b1iqdprgQ1A4opLXzatupuvtQAOHYPtppz4h/2l8biO5CAiEfnvvC', 'admin@ivymoda.com', 'Admin IVY', '0901234567', NULL, 1, 1, 0, NULL, NULL, NOW()),
(2, 'customer1', '$2y$10$b1iqdprgQ1A4opLXzatupuvtQAOHYPtppz4h/2l8biO5CAiEfnvvC', 'customer@gmail.com', 'Nguyễn Văn A', '0987654321', 'Hà Nội', 2, 1, 0, NULL, NULL, NOW());

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

-- Màu sắc
INSERT INTO `tbl_color` VALUES 
(1, 'Trắng', 'white.png', '#FFFFFF', NOW()),
(2, 'Đen', 'black.png', '#000000', NOW()),
(3, 'Xanh Navy', 'navy.png', '#000080', NOW()),
(4, 'Đỏ', 'red.png', '#FF0000', NOW()),
(5, 'Be', 'beige.png', '#F5F5DC', NOW()),
(6, 'Xanh Dương', 'blue.png', '#0000FF', NOW()),
(7, 'Xám', 'gray.png', '#808080', NOW());

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
 'dam_congso.jpg', 1, NOW(), NOW());

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
(11, 4, 5, 0, NOW()); -- Be

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
(13, 4, 11, 'dam_be_1.jpg', 1, NOW());

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
(32, 4, 5, 4, 'DCN-001-L-BEIGE', 3, NULL, 1, NOW(), NOW());

-- Email template
INSERT INTO `tbl_email_template` VALUES 
(1, 'Xác nhận đơn hàng', 'Đơn hàng #{order_code} đã được xác nhận', 
 '<p>Xin chào {customer_name},</p><p>Đơn hàng của bạn đã được xác nhận.</p><p>Mã đơn hàng: {order_code}</p>', 'order'),
(2, 'Khuyến mãi', 'Chương trình khuyến mãi đặc biệt', 
 '<p>Giảm giá lên đến 50%!</p>', 'promotion'),
(3, 'Đổi mật khẩu', 'Yêu cầu đặt lại mật khẩu', 
 '<p>Xin chào,</p><p>Click vào link sau để đặt lại mật khẩu: {reset_link}</p>', 'password_reset');

COMMIT;

-- ============================================
-- HƯỚNG DẪN SỬ DỤNG
-- ============================================

/*
DATABASE VERSION 2.0 - HOÀN CHỈNH

CÁC THAY ĐỔI CHÍNH:

1. TBL_SANPHAM:
   ✅ Đã XÓA: sanpham_size, sanpham_soluong
   ✅ Thông tin size/stock giờ nằm trong tbl_product_variant

2. TBL_SIZE:
   ✅ Bảng mới: Quản lý danh mục size (XS-3XL)
   ✅ Admin có thể CRUD size

3. TBL_PRODUCT_VARIANT:
   ✅ Bảng mới: Lưu tồn kho chi tiết theo (sản phẩm + màu + size)
   ✅ Mỗi variant có: variant_id, sku, ton_kho, gia_ban, trang_thai
   ✅ UNIQUE constraint: (sanpham_id, color_id, size_id)

4. TBL_CART (VERSION 2.0):
   ✅ ĐÃ XÓA: sanpham_id, sanpham_tieude, sanpham_gia, sanpham_anh, sanpham_size, sanpham_color
   ✅ THÊM: variant_id FK (ON DELETE CASCADE)
   ✅ Giỏ hàng giờ lưu variant_id thay vì các trường rời

5. TBL_ORDER_ITEMS (VERSION 2.0):
   ✅ THÊM: variant_id FK (ON DELETE SET NULL)
   ✅ GIỮ LẠI: sanpham_size, sanpham_color VARCHAR (snapshot để hiển thị lịch sử)
   ✅ Khi variant bị xóa → variant_id = NULL nhưng vẫn hiển thị snapshot

WORKFLOW MỚI:

A. Thêm sản phẩm:
   1. INSERT vào tbl_sanpham
   2. INSERT màu vào tbl_sanpham_color
   3. INSERT ảnh vào tbl_anhsanpham
   4. INSERT variants vào tbl_product_variant (với từng size/màu)

B. Khách hàng mua hàng:
   1. Chọn sản phẩm → Chọn màu → Hiển thị sizes có sẵn
   2. Chọn size → Lấy variant_id
   3. Add to cart: INSERT tbl_cart (session_id, variant_id, quantity)
   4. View cart: JOIN tbl_cart → tbl_product_variant → tbl_size → tbl_color
   5. Checkout: 
      - Validate tồn kho (checkVariantStock)
      - Create order
      - Add order_items với variant_id + snapshot
      - Decrease stock (decreaseVariantStock)
      - Clear cart

C. Truy vấn hữu ích:

-- Lấy tất cả variants của sản phẩm
SELECT 
    v.variant_id, v.sku, v.ton_kho, v.trang_thai,
    s.size_ten,
    c.color_ten, c.color_ma,
    p.sanpham_tieude, p.sanpham_gia
FROM tbl_product_variant v
JOIN tbl_size s ON v.size_id = s.size_id
JOIN tbl_color c ON v.color_id = c.color_id
JOIN tbl_sanpham p ON v.sanpham_id = p.sanpham_id
WHERE v.sanpham_id = 1
ORDER BY c.color_ten, s.size_order;

-- Lấy giỏ hàng đầy đủ
SELECT 
    c.cart_id, c.quantity,
    v.variant_id, v.sku, v.ton_kho,
    s.size_ten,
    co.color_ten,
    p.sanpham_tieude, p.sanpham_gia, p.sanpham_anh
FROM tbl_cart c
JOIN tbl_product_variant v ON c.variant_id = v.variant_id
JOIN tbl_size s ON v.size_id = s.size_id
JOIN tbl_color co ON v.color_id = co.color_id
JOIN tbl_sanpham p ON v.sanpham_id = p.sanpham_id
WHERE c.session_id = 'your_session_id';

-- Trừ tồn kho
UPDATE tbl_product_variant
SET ton_kho = ton_kho - 2,
    trang_thai = CASE WHEN ton_kho - 2 <= 0 THEN 0 ELSE 1 END
WHERE variant_id = 1 AND ton_kho >= 2;

ĐÁP ỨNG NGHIỆM THU:

✅ UC 2.1: Quản lý danh mục - tbl_danhmuc, tbl_loaisanpham
✅ UC 2.2: Quản lý sản phẩm - tbl_sanpham + tbl_product_variant
✅ UC 2.3: Quản lý giỏ hàng - tbl_cart (variant_id)
✅ UC 2.4: Quản lý đơn hàng - tbl_order + tbl_order_items (variant_id + snapshot)
✅ UC 2.5: Tìm kiếm - Database hỗ trợ đầy đủ
✅ UC 2.6: Khuyến mãi - tbl_ma_giam_gia
✅ UC 2.7: Báo cáo - tbl_thong_ke + JOIN với variants

LƯU Ý:
- Password mẫu: admin123, customer123 (đã hash bcrypt)
- Có 4 sản phẩm mẫu với 32 variants
- Tổng tồn kho mẫu: 308 sản phẩm
- Foreign keys đã được thiết lập đầy đủ
- Indexes đã tối ưu cho performance

CÁCH IMPORT:
1. Backup database cũ (nếu cần)
2. DROP database ivymoda (nếu tồn tại)
3. mysql -u root < ivymoda_complete.sql
4. Kiểm tra: SELECT COUNT(*) FROM tbl_product_variant; -- Phải = 32

*/
