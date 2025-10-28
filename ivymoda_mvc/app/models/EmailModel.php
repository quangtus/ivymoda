<?php
class EmailModel extends Model {
    protected $table = 'tbl_email_log';
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Lưu log email
     */
    public function logEmail($recipient, $subject, $body, $status = 'sent', $errorMessage = null) {
        // Note: error_message column doesn't exist in tbl_email_log table
        // We'll include error info in the body if there's an error
        if ($errorMessage && $status === 'failed') {
            $body = $body . "\n\nError: " . $errorMessage;
        }
        
        $sql = "INSERT INTO tbl_email_log (recipient, subject, body, status, sent_at) VALUES (?, ?, ?, ?, NOW())";
        $params = [$recipient, $subject, $body, $status];
        
        return $this->execute($sql, $params);
    }
    
    /**
     * Lấy template email theo tên
     */
    public function getTemplate($templateName) {
        $sql = "SELECT * FROM tbl_email_template WHERE template_name = ?";
        return $this->query($sql, [$templateName]);
    }
    
    /**
     * Lấy template email theo ID
     */
    public function getTemplateById($templateId) {
        $sql = "SELECT * FROM tbl_email_template WHERE template_id = ?";
        return $this->query($sql, [$templateId]);
    }
    
    /**
     * Lấy danh sách template
     */
    public function getTemplates() {
        $sql = "SELECT * FROM tbl_email_template ORDER BY template_name";
        return $this->queryAll($sql);
    }
    
    /**
     * Thêm template mới
     */
    public function addTemplate($templateName, $subject, $body, $type = null) {
        $sql = "INSERT INTO tbl_email_template (template_name, subject, body, type) VALUES (?, ?, ?, ?)";
        return $this->execute($sql, [$templateName, $subject, $body, $type]);
    }
    
    /**
     * Cập nhật template
     */
    public function updateTemplate($templateId, $templateName, $subject, $body, $type = null) {
        $sql = "UPDATE tbl_email_template SET template_name = ?, subject = ?, body = ?, type = ? WHERE template_id = ?";
        return $this->execute($sql, [$templateName, $subject, $body, $type, $templateId]);
    }
    
    /**
     * Xóa template
     */
    public function deleteTemplate($templateId) {
        $sql = "DELETE FROM tbl_email_template WHERE template_id = ?";
        return $this->execute($sql, [$templateId]);
    }
    
    /**
     * Lấy log email
     */
    public function getEmailLogs($limit = 50, $offset = 0) {
        $sql = "SELECT * FROM tbl_email_log ORDER BY sent_at DESC LIMIT ? OFFSET ?";
        return $this->queryAll($sql, [$limit, $offset]);
    }
    
    /**
     * Đếm tổng số log email
     */
    public function getEmailLogCount() {
        $sql = "SELECT COUNT(*) as total FROM tbl_email_log";
        $result = $this->query($sql);
        return $result->total ?? 0;
    }
    
    /**
     * Lấy thống kê email
     */
    public function getEmailStats() {
        $sql = "SELECT 
                    COUNT(*) as total_emails,
                    SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent_emails,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_emails,
                    COUNT(DISTINCT recipient) as unique_recipients
                FROM tbl_email_log";
        $result = $this->query($sql);
        
        // Convert object to array
        if ($result) {
            return [
                'total_emails' => (int)$result->total_emails,
                'sent_emails' => (int)$result->sent_emails,
                'failed_emails' => (int)$result->failed_emails,
                'unique_recipients' => (int)$result->unique_recipients
            ];
        }
        
        return [
            'total_emails' => 0,
            'sent_emails' => 0,
            'failed_emails' => 0,
            'unique_recipients' => 0
        ];
    }
    
    /**
     * Lấy danh sách khuyến mãi
     */
    public function getPromotions() {
        $sql = "SELECT * FROM tbl_promotion WHERE is_active = 1 AND start_date <= NOW() AND end_date >= NOW() ORDER BY priority DESC, created_at DESC";
        return $this->queryAll($sql);
    }
    
    /**
     * Lấy danh sách email khách hàng để gửi khuyến mãi
     */
    public function getCustomerEmails() {
        $sql = "SELECT DISTINCT email, fullname FROM users WHERE email IS NOT NULL AND email != '' AND role_id = 2 AND promotion_emails = 1 ORDER BY fullname";
        return $this->queryAll($sql);
    }
    
    /**
     * Lưu log email khuyến mãi
     */
    public function logPromotionEmail($promotionTitle, $recipientEmail, $userId = null, $status = 'sent', $errorMessage = null) {
        $sql = "INSERT INTO tbl_promotion_email_log (promotion_title, recipient_email, user_id, status, error_message, sent_at) VALUES (?, ?, ?, ?, ?, NOW())";
        return $this->execute($sql, [$promotionTitle, $recipientEmail, $userId, $status, $errorMessage]);
    }
    
    /**
     * Lấy log email khuyến mãi
     */
    public function getPromotionEmailLogs($promotionTitle = null) {
        $sql = "SELECT pel.*, u.fullname 
                FROM tbl_promotion_email_log pel 
                LEFT JOIN users u ON pel.user_id = u.id";
        
        $params = [];
        if ($promotionTitle) {
            $sql .= " WHERE pel.promotion_title = ?";
            $params[] = $promotionTitle;
        }
        
        $sql .= " ORDER BY pel.sent_at DESC";
        
        return $this->queryAll($sql, $params);
    }
    
    /**
     * Lấy log email theo ID
     */
    public function getEmailLogById($logId) {
        $sql = "SELECT * FROM tbl_email_log WHERE log_id = ?";
        return $this->query($sql, [$logId]);
    }
    
    /**
     * Lấy lịch sử email của user cụ thể
     */
    public function getUserEmailLogs($userId, $limit = 20) {
        $sql = "SELECT * FROM tbl_email_log 
                WHERE recipient = (SELECT email FROM users WHERE id = ?)
                ORDER BY sent_at DESC 
                LIMIT ?";
        return $this->queryAll($sql, [$userId, $limit]);
    }
    
    /**
     * Lấy khuyến mãi theo ID
     */
    public function getPromotionById($promotionId) {
        $sql = "SELECT * FROM tbl_promotion WHERE promotion_id = ?";
        return $this->query($sql, [$promotionId]);
    }
    
    /**
     * Lấy thống kê email khách hàng
     */
    public function getCustomerEmailStats() {
        $sql = "SELECT 
                    COUNT(*) as total_customers,
                    SUM(CASE WHEN promotion_emails = 1 THEN 1 ELSE 0 END) as promotion_enabled
                FROM users 
                WHERE role_id = 2 AND email IS NOT NULL AND email != ''";
        $result = $this->query($sql);
        
        if ($result) {
            return [
                'total_customers' => (int)$result->total_customers,
                'promotion_enabled' => (int)$result->promotion_enabled
            ];
        }
        
        return [
            'total_customers' => 0,
            'promotion_enabled' => 0
        ];
    }
}