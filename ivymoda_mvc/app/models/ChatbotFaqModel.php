<?php
/**
 * ChatbotFaqModel - Quản lý FAQ cho chatbot
 * UC3.48 - Chatbot hướng dẫn sử dụng hệ thống (FAQ)
 */

class ChatbotFaqModel extends Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Lấy danh sách FAQ theo category và status
     * @param string $category Danh mục FAQ (optional)
     * @param int $status Trạng thái (1: Active, 0: Inactive)
     * @return array
     */
    public function getFaqs($category = null, $status = 1) {
        $sql = "SELECT * FROM tbl_chatbot_faq WHERE status = ?";
        $params = [$status];
        
        if ($category) {
            $sql .= " AND category = ?";
            $params[] = $category;
        }
        
        $sql .= " ORDER BY display_order ASC, created_at DESC";
        
        return $this->getAll($sql, $params);
    }
    
    /**
     * Lấy FAQ theo ID
     * @param int $faqId
     * @return object|false
     */
    public function getFaqById($faqId) {
        $sql = "SELECT * FROM tbl_chatbot_faq WHERE faq_id = ?";
        return $this->getOne($sql, [$faqId]);
    }
    
    /**
     * Lấy danh sách categories
     * @return array
     */
    public function getCategories() {
        $sql = "SELECT DISTINCT category FROM tbl_chatbot_faq WHERE status = 1 ORDER BY category";
        return $this->getAll($sql);
    }
    
    /**
     * Thêm FAQ mới
     * @param array $data
     * @return int|false ID của FAQ mới
     */
    public function addFaq($data) {
        $sql = "INSERT INTO tbl_chatbot_faq (question, answer, category, display_order, status, help_link, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['question'],
            $data['answer'],
            $data['category'],
            $data['display_order'] ?? 0,
            $data['status'] ?? 1,
            $data['help_link'] ?? null,
            $data['created_by'] ?? null
        ];
        
        if ($this->execute($sql, $params)) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Cập nhật FAQ
     * @param int $faqId
     * @param array $data
     * @return bool
     */
    public function updateFaq($faqId, $data) {
        $sql = "UPDATE tbl_chatbot_faq SET 
                question = ?, 
                answer = ?, 
                category = ?, 
                display_order = ?, 
                status = ?, 
                help_link = ?,
                updated_at = CURRENT_TIMESTAMP
                WHERE faq_id = ?";
        
        $params = [
            $data['question'],
            $data['answer'],
            $data['category'],
            $data['display_order'] ?? 0,
            $data['status'] ?? 1,
            $data['help_link'] ?? null,
            $faqId
        ];
        
        return $this->execute($sql, $params);
    }
    
    /**
     * Xóa FAQ
     * @param int $faqId
     * @return bool
     */
    public function deleteFaq($faqId) {
        $sql = "DELETE FROM tbl_chatbot_faq WHERE faq_id = ?";
        return $this->execute($sql, [$faqId]);
    }
    
    /**
     * Cập nhật trạng thái FAQ
     * @param int $faqId
     * @param int $status
     * @return bool
     */
    public function updateStatus($faqId, $status) {
        $sql = "UPDATE tbl_chatbot_faq SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE faq_id = ?";
        return $this->execute($sql, [$status, $faqId]);
    }
    
    /**
     * Tìm kiếm FAQ theo từ khóa
     * @param string $keyword
     * @return array
     */
    public function searchFaqs($keyword) {
        $sql = "SELECT * FROM tbl_chatbot_faq 
                WHERE status = 1 
                AND (question LIKE ? OR answer LIKE ?) 
                ORDER BY display_order ASC";
        
        $searchTerm = "%{$keyword}%";
        return $this->getAll($sql, [$searchTerm, $searchTerm]);
    }
    
    /**
     * Lấy thống kê FAQ
     * @return object
     */
    public function getFaqStats() {
        $sql = "SELECT 
                    COUNT(*) as total_faqs,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active_faqs,
                    SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as inactive_faqs,
                    COUNT(DISTINCT category) as total_categories
                FROM tbl_chatbot_faq";
        
        return $this->getOne($sql);
    }
    
    /**
     * Lấy FAQ theo category với phân trang
     * @param string $category
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getFaqsByCategory($category, $limit = 10, $offset = 0) {
        $sql = "SELECT * FROM tbl_chatbot_faq 
                WHERE category = ? AND status = 1 
                ORDER BY display_order ASC, created_at DESC 
                LIMIT ? OFFSET ?";
        
        return $this->getAll($sql, [$category, $limit, $offset]);
    }
}
