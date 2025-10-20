-- =====================================================
-- SQL Scripts for Chatbot Configuration
-- UC3.47 - Chatbot AI Tư vấn Sản phẩm
-- =====================================================

-- 1. Tạo table config cho Chatbot (nếu chưa tồn tại)
CREATE TABLE IF NOT EXISTS `tbl_chatbot_config` (
    `config_id` int(11) NOT NULL AUTO_INCREMENT,
    `config_key` varchar(100) NOT NULL UNIQUE COMMENT 'Khóa cấu hình',
    `config_value` longtext COMMENT 'Giá trị cấu hình',
    `config_type` varchar(50) DEFAULT 'string' COMMENT 'Loại dữ liệu: string, int, json, etc',
    `description` text COMMENT 'Mô tả cấu hình',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`config_id`),
    UNIQUE KEY `unique_config_key` (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cấu hình Chatbot';

-- 2. Tạo table lưu lịch sử hội thoại (nếu chưa tồn tại)
CREATE TABLE IF NOT EXISTS `tbl_chatbot_conversation` (
    `conversation_id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL COMMENT 'ID người dùng',
    `session_id` varchar(100) NOT NULL COMMENT 'Session ID',
    `user_message` text NOT NULL COMMENT 'Tin nhắn từ người dùng',
    `bot_response` longtext NOT NULL COMMENT 'Phản hồi từ chatbot',
    `context_data` json DEFAULT NULL COMMENT 'Dữ liệu context sử dụng',
    `suggested_products` json DEFAULT NULL COMMENT 'Sản phẩm gợi ý',
    `response_time` int(11) DEFAULT NULL COMMENT 'Thời gian phản hồi (ms)',
    `is_from_faq` tinyint(1) DEFAULT 0 COMMENT '1: Từ FAQ, 0: Từ Gemini AI',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`conversation_id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_session_id` (`session_id`),
    KEY `idx_created_at` (`created_at`),
    KEY `idx_is_from_faq` (`is_from_faq`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Lịch sử hội thoại chatbot';

-- 3. Tạo table FAQ (nếu chưa tồn tại)
CREATE TABLE IF NOT EXISTS `tbl_chatbot_faq` (
    `faq_id` int(11) NOT NULL AUTO_INCREMENT,
    `question` varchar(500) NOT NULL COMMENT 'Câu hỏi',
    `answer` longtext NOT NULL COMMENT 'Câu trả lời (có thể chứa HTML)',
    `category` varchar(100) NOT NULL COMMENT 'Danh mục FAQ',
    `display_order` int(11) DEFAULT 0 COMMENT 'Thứ tự hiển thị',
    `status` tinyint(1) DEFAULT 1 COMMENT '1: Active, 0: Inactive',
    `help_link` varchar(255) DEFAULT NULL COMMENT 'Link hướng dẫn chi tiết',
    `created_by` int(11) DEFAULT NULL COMMENT 'Admin tạo',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`faq_id`),
    KEY `idx_category` (`category`),
    KEY `idx_status_order` (`status`, `display_order`),
    KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FAQ cho chatbot';

-- 4. Tạo table user preferences (nếu chưa tồn tại)
CREATE TABLE IF NOT EXISTS `tbl_user_preferences` (
    `preference_id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `favorite_colors` varchar(255) DEFAULT NULL COMMENT 'Màu yêu thích',
    `favorite_categories` varchar(255) DEFAULT NULL COMMENT 'Danh mục yêu thích',
    `size_preference` varchar(50) DEFAULT NULL COMMENT 'Size thường mặc',
    `price_range` varchar(100) DEFAULT NULL COMMENT 'Khoảng giá',
    `skin_tone` varchar(50) DEFAULT NULL COMMENT 'Tông da',
    `height` varchar(20) DEFAULT NULL COMMENT 'Chiều cao',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`preference_id`),
    KEY `idx_user` (`user_id`),
    UNIQUE KEY `unique_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sở thích người dùng';

-- 5. Insert cấu hình Gemini API Key (THAY ĐỔI GIÁ TRỊ NẾUA CẦN)
INSERT INTO `tbl_chatbot_config` (`config_key`, `config_value`, `config_type`, `description`) 
VALUES 
    ('gemini_api_key', 'YOUR_GEMINI_API_KEY_HERE', 'string', 'API Key cho Gemini AI'),
    ('chatbot_enabled', '1', 'int', 'Bật/Tắt chatbot'),
    ('max_response_time', '3000', 'int', 'Thời gian tối đa phản hồi (ms)'),
    ('max_products_suggestion', '5', 'int', 'Tối đa sản phẩm gợi ý')
ON DUPLICATE KEY UPDATE 
    updated_at = CURRENT_TIMESTAMP;

-- 6. Insert dữ liệu FAQ mẫu
INSERT INTO `tbl_chatbot_faq` (`question`, `answer`, `category`, `display_order`, `status`) 
VALUES 
    (
        'Làm cách nào để đặt hàng?',
        'Để đặt hàng, bạn cần: 1. Tìm sản phẩm yêu thích 2. Chọn size, màu sắc 3. Thêm vào giỏ hàng 4. Tiến hành thanh toán 5. Chọn phương thức giao hàng',
        'Đặt hàng',
        1,
        1
    ),
    (
        'Chính sách hoàn trả hàng như thế nào?',
        'Bạn có 7 ngày để hoàn trả hàng nếu sản phẩm không phù hợp hoặc bị lỗi. Sản phẩm phải còn nguyên nhãn và chưa sử dụng.',
        'Chính sách',
        2,
        1
    ),
    (
        'Có những phương thức thanh toán nào?',
        'Chúng tôi hỗ trợ: Thanh toán khi nhận hàng, Chuyển khoản ngân hàng, Ví điện tử, Momo, Thẻ tín dụng',
        'Thanh toán',
        3,
        1
    ),
    (
        'Thời gian giao hàng mất bao lâu?',
        'Thời gian giao hàng thường từ 1-3 ngày tùy vào địa điểm. TP.HCM: 1-2 ngày. Các tỉnh khác: 2-3 ngày.',
        'Giao hàng',
        4,
        1
    ),
    (
        'Làm cách nào để kiểm tra tình trạng đơn hàng?',
        'Bạn có thể kiểm tra tình trạng đơn hàng bằng cách đăng nhập vào tài khoản và vào mục "Đơn hàng của tôi"',
        'Đơn hàng',
        5,
        1
    )
ON DUPLICATE KEY UPDATE 
    updated_at = CURRENT_TIMESTAMP;

-- 7. Kiểm tra dữ liệu đã insert
SELECT * FROM `tbl_chatbot_config`;
SELECT COUNT(*) as total_faqs FROM `tbl_chatbot_faq`;

-- =====================================================
-- Sau khi chạy script này:
-- 1. Cập nhật API Key trong tbl_chatbot_config
-- 2. Kiểm tra dữ liệu FAQđã được insert
-- 3. Mở file test: http://localhost/ivymoda/ivymoda_mvc/public/test-chatbot-fixed.html
-- =====================================================
