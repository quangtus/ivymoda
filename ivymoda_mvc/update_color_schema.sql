-- ============================================
-- SCRIPT CẬP NHẬT DATABASE - BỎ TRƯỜNG COLOR_ANH
-- ============================================
-- Chạy script này để cập nhật database hiện tại
-- Ngày tạo: 2025-01-16
-- ============================================

USE `ivymoda`;

-- Kiểm tra xem cột color_anh có tồn tại không
SET @column_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'ivymoda' 
    AND TABLE_NAME = 'tbl_color' 
    AND COLUMN_NAME = 'color_anh'
);

-- Nếu cột color_anh tồn tại, xóa nó
SET @sql = IF(@column_exists > 0, 
    'ALTER TABLE `tbl_color` DROP COLUMN `color_anh`', 
    'SELECT "Cột color_anh không tồn tại, không cần xóa" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Cập nhật dữ liệu mẫu nếu cần
-- Đảm bảo tất cả màu đều có mã hex
UPDATE `tbl_color` SET `color_ma` = '#FFFFFF' WHERE `color_ten` = 'Trắng' AND (`color_ma` IS NULL OR `color_ma` = '');
UPDATE `tbl_color` SET `color_ma` = '#000000' WHERE `color_ten` = 'Đen' AND (`color_ma` IS NULL OR `color_ma` = '');
UPDATE `tbl_color` SET `color_ma` = '#000080' WHERE `color_ten` = 'Xanh Navy' AND (`color_ma` IS NULL OR `color_ma` = '');
UPDATE `tbl_color` SET `color_ma` = '#FF0000' WHERE `color_ten` = 'Đỏ' AND (`color_ma` IS NULL OR `color_ma` = '');
UPDATE `tbl_color` SET `color_ma` = '#F5F5DC' WHERE `color_ten` = 'Be' AND (`color_ma` IS NULL OR `color_ma` = '');
UPDATE `tbl_color` SET `color_ma` = '#0000FF' WHERE `color_ten` = 'Xanh Dương' AND (`color_ma` IS NULL OR `color_ma` = '');
UPDATE `tbl_color` SET `color_ma` = '#808080' WHERE `color_ten` = 'Xám' AND (`color_ma` IS NULL OR `color_ma` = '');

-- Hiển thị kết quả
SELECT 'Cập nhật database thành công!' as message;
SELECT COUNT(*) as total_colors FROM `tbl_color`;
SELECT `color_id`, `color_ten`, `color_ma` FROM `tbl_color` ORDER BY `color_id`;
