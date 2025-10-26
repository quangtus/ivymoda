<?php
/**
 * ChatbotModel - Model xử lý chatbot AI tư vấn sản phẩm
 * UC3.47 - Chatbot tư vấn sản phẩm với Gemini AI
 */

class ChatbotModel extends Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Lấy cấu hình chatbot
     * @param string $key
     * @return string|null
     */
    public function getConfig($key) {
        $sql = "SELECT config_value FROM tbl_chatbot_config WHERE config_key = ?";
        $result = $this->getOne($sql, [$key]);
        return $result ? $result->config_value : null;
    }
    
    /**
     * Cập nhật cấu hình chatbot
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function updateConfig($key, $value) {
        $sql = "UPDATE tbl_chatbot_config SET config_value = ?, updated_at = CURRENT_TIMESTAMP WHERE config_key = ?";
        return $this->execute($sql, [$value, $key]);
    }
    
    /**
     * Lưu lịch sử hội thoại
     * @param array $data
     * @return int|false
     */
    public function saveConversation($data) {
        $sql = "INSERT INTO tbl_chatbot_conversation 
                (user_id, session_id, user_message, bot_response, context_data, suggested_products, response_time, is_from_faq) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['user_id'] ?? null,
            $data['session_id'],
            $data['user_message'],
            $data['bot_response'],
            $data['context_data'] ?? null,
            $data['suggested_products'] ?? null,
            $data['response_time'] ?? null,
            $data['is_from_faq'] ?? 0
        ];
        
        if ($this->execute($sql, $params)) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Lấy lịch sử hội thoại theo session
     * @param string $sessionId
     * @param int $limit
     * @return array
     */
    public function getConversationHistory($sessionId, $limit = 10) {
        $sql = "SELECT * FROM tbl_chatbot_conversation 
                WHERE session_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?";
        
        return $this->getAll($sql, [$sessionId, $limit]);
    }
    
    /**
     * Lấy sản phẩm bán chạy cho context
     * @param int $limit
     * @return array
     */
    public function getPopularProducts($limit = 5) {
        $sql = "SELECT 
                    p.sanpham_id,
                    p.sanpham_tieude,
                    p.sanpham_ma,
                    p.sanpham_gia,
                    p.sanpham_anh,
                    d.danhmuc_ten,
                    COUNT(DISTINCT oi.order_id) as order_count,
                    SUM(oi.sanpham_soluong) as total_sold
                FROM tbl_sanpham p
                INNER JOIN tbl_danhmuc d ON p.danhmuc_id = d.danhmuc_id
                LEFT JOIN tbl_order_items oi ON p.sanpham_id = oi.sanpham_id
                WHERE p.sanpham_status = 1
                GROUP BY p.sanpham_id
                ORDER BY total_sold DESC, order_count DESC
                LIMIT ?";
        
        return $this->getAll($sql, [$limit]);
    }
    
    /**
     * Lấy sản phẩm theo danh mục
     * @param string $category
     * @param int $limit
     * @return array
     */
    public function getProductsByCategory($category, $limit = 5) {
        $sql = "SELECT 
                    p.sanpham_id,
                    p.sanpham_tieude,
                    p.sanpham_ma,
                    p.sanpham_gia,
                    p.sanpham_anh,
                    d.danhmuc_ten
                FROM tbl_sanpham p
                INNER JOIN tbl_danhmuc d ON p.danhmuc_id = d.danhmuc_id
                WHERE p.sanpham_status = 1 
                AND d.danhmuc_ten LIKE ?
                ORDER BY p.created_at DESC
                LIMIT ?";
        
        return $this->getAll($sql, ["%{$category}%", $limit]);
    }
    
    /**
     * Tìm kiếm sản phẩm theo từ khóa
     * @param string $keyword
     * @param int $limit
     * @return array
     */
    public function searchProducts($keyword, $limit = 5) {
        $sql = "SELECT 
                    p.sanpham_id,
                    p.sanpham_tieude,
                    p.sanpham_ma,
                    p.sanpham_gia,
                    p.sanpham_anh,
                    d.danhmuc_ten
                FROM tbl_sanpham p
                INNER JOIN tbl_danhmuc d ON p.danhmuc_id = d.danhmuc_id
                WHERE p.sanpham_status = 1 
                AND (p.sanpham_tieude LIKE ? OR p.sanpham_ma LIKE ?)
                ORDER BY p.created_at DESC
                LIMIT ?";
        
        $searchTerm = "%{$keyword}%";
        return $this->getAll($sql, [$searchTerm, $searchTerm, $limit]);
    }
    
    /**
     * Lấy sở thích người dùng
     * @param int $userId
     * @return object|null
     */
    public function getUserPreferences($userId) {
        $sql = "SELECT * FROM tbl_user_preferences WHERE user_id = ?";
        return $this->getOne($sql, [$userId]);
    }
    
    /**
     * Lưu/cập nhật sở thích người dùng
     * @param array $data
     * @return bool
     */
    public function saveUserPreferences($data) {
        $sql = "INSERT INTO tbl_user_preferences 
                (user_id, favorite_colors, favorite_categories, size_preference, price_range, skin_tone, height) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                favorite_colors = VALUES(favorite_colors),
                favorite_categories = VALUES(favorite_categories),
                size_preference = VALUES(size_preference),
                price_range = VALUES(price_range),
                skin_tone = VALUES(skin_tone),
                height = VALUES(height),
                updated_at = CURRENT_TIMESTAMP";
        
        $params = [
            $data['user_id'],
            $data['favorite_colors'] ?? null,
            $data['favorite_categories'] ?? null,
            $data['size_preference'] ?? null,
            $data['price_range'] ?? null,
            $data['skin_tone'] ?? null,
            $data['height'] ?? null
        ];
        
        return $this->execute($sql, $params);
    }
    
    /**
     * Lấy thống kê chatbot
     * @return object
     */
    public function getChatbotStats() {
        $sql = "SELECT 
                    COUNT(*) as total_conversations,
                    COUNT(DISTINCT session_id) as unique_sessions,
                    COUNT(DISTINCT user_id) as unique_users,
                    AVG(response_time) as avg_response_time,
                    SUM(CASE WHEN is_from_faq = 1 THEN 1 ELSE 0 END) as faq_responses,
                    SUM(CASE WHEN is_from_faq = 0 THEN 1 ELSE 0 END) as ai_responses
                FROM tbl_chatbot_conversation";
        
        return $this->getOne($sql);
    }
    
    /**
     * Lấy danh sách màu sắc có sẵn
     * @return array
     */
    public function getAvailableColors() {
        $sql = "SELECT DISTINCT color_ten FROM tbl_color ORDER BY color_ten";
        return $this->getAll($sql);
    }
    
    /**
     * Lấy danh sách size có sẵn
     * @return array
     */
    public function getAvailableSizes() {
        $sql = "SELECT DISTINCT size_ten FROM tbl_size ORDER BY size_order";
        return $this->getAll($sql);
    }
    
    /**
     * Lấy danh sách danh mục
     * @return array
     */
    public function getAvailableCategories() {
        $sql = "SELECT DISTINCT danhmuc_ten FROM tbl_danhmuc WHERE danhmuc_status = 1 ORDER BY danhmuc_ten";
        return $this->getAll($sql);
    }
}
