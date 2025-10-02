<?php
/**
 * EmailModel - Xử lý email template và log (UC1.3, UC3.3)
 * Bảng: tbl_email_template, tbl_email_log
 */
class EmailModel extends Model {
    protected $templateTable = 'tbl_email_template';
    protected $logTable = 'tbl_email_log';
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Lấy tất cả email template
     */
    public function getAllTemplates() {
        $query = "SELECT * FROM {$this->templateTable} ORDER BY template_id ASC";
        return $this->getAll($query);
    }
    
    /**
     * Lấy template theo tên
     */
    public function getTemplateByName($name) {
        $name = $this->escape($name);
        $query = "SELECT * FROM {$this->templateTable} WHERE template_name = '$name'";
        return $this->getOne($query);
    }
    
    /**
     * Lấy template theo loại
     */
    public function getTemplateByType($type) {
        $type = $this->escape($type);
        $query = "SELECT * FROM {$this->templateTable} WHERE template_type = '$type' LIMIT 1";
        return $this->getOne($query);
    }
    
    /**
     * Lấy template theo ID
     */
    public function getTemplateById($id) {
        $id = (int)$id;
        $query = "SELECT * FROM {$this->templateTable} WHERE template_id = $id";
        return $this->getOne($query);
    }
    
    /**
     * Thêm email template
     */
    public function addTemplate($data) {
        $name = $this->escape($data['template_name']);
        $subject = $this->escape($data['template_subject']);
        $body = $this->escape($data['template_body']);
        $type = $this->escape($data['template_type']);
        
        $query = "INSERT INTO {$this->templateTable} 
                  (template_name, template_subject, template_body, template_type) 
                  VALUES ('$name', '$subject', '$body', '$type')";
        
        if ($this->execute($query)) {
            return true;
        } else {
            return "Thêm template thất bại";
        }
    }
    
    /**
     * Cập nhật email template
     */
    public function updateTemplate($id, $data) {
        $id = (int)$id;
        $subject = $this->escape($data['template_subject']);
        $body = $this->escape($data['template_body']);
        
        $query = "UPDATE {$this->templateTable} SET 
                  template_subject = '$subject',
                  template_body = '$body'
                  WHERE template_id = $id";
        
        if ($this->execute($query)) {
            return true;
        } else {
            return "Cập nhật template thất bại";
        }
    }
    
    /**
     * Xóa template
     */
    public function deleteTemplate($id) {
        $id = (int)$id;
        $query = "DELETE FROM {$this->templateTable} WHERE template_id = $id";
        
        if ($this->execute($query)) {
            return true;
        } else {
            return "Xóa template thất bại";
        }
    }
    
    /**
     * Thay thế biến trong template
     * @param string $template Nội dung template
     * @param array $variables Mảng biến cần thay thế ['customer_name' => 'John', ...]
     * @return string Template đã được thay thế biến
     */
    public function replaceVariables($template, $variables) {
        foreach ($variables as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        return $template;
    }
    
    /**
     * Gửi email sử dụng template
     * @param string $to Email người nhận
     * @param string $templateType Loại template (order, promotion, password_reset)
     * @param array $variables Biến để thay thế trong template
     * @return bool|string True nếu thành công, string message nếu thất bại
     */
    public function sendEmailWithTemplate($to, $templateType, $variables = []) {
        // Lấy template
        $template = $this->getTemplateByType($templateType);
        
        if (!$template) {
            return "Template không tồn tại";
        }
        
        $subject = is_object($template) ? $template->template_subject : $template['template_subject'];
        $body = is_object($template) ? $template->template_body : $template['template_body'];
        
        // Thay thế biến
        $subject = $this->replaceVariables($subject, $variables);
        $body = $this->replaceVariables($body, $variables);
        
        // Gửi email (sử dụng EmailHelper nếu có)
        if (class_exists('EmailHelper')) {
            $result = EmailHelper::sendEmail($to, '', $subject, $body);
            
            // Log email
            $this->logEmail($to, $subject, $body, $result ? 1 : 0);
            
            return $result;
        }
        
        // Fallback: sử dụng mail() của PHP
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: IVY moda <noreply@ivymoda.com>" . "\r\n";
        
        $success = mail($to, $subject, $body, $headers);
        
        // Log email
        $this->logEmail($to, $subject, $body, $success ? 1 : 0);
        
        return $success;
    }
    
    /**
     * Lưu log email
     */
    public function logEmail($to, $subject, $body, $status = 0) {
        $to = $this->escape($to);
        $subject = $this->escape($subject);
        $body = $this->escape($body);
        $status = (int)$status;
        
        $query = "INSERT INTO {$this->logTable} 
                  (email_to, email_subject, email_body, email_status) 
                  VALUES ('$to', '$subject', '$body', $status)";
        
        return $this->execute($query);
    }
    
    /**
     * Lấy log email
     */
    public function getEmailLogs($limit = 50) {
        $limit = (int)$limit;
        $query = "SELECT * FROM {$this->logTable} 
                  ORDER BY sent_at DESC 
                  LIMIT $limit";
        return $this->getAll($query);
    }
    
    /**
     * Lấy log email theo người nhận
     */
    public function getEmailLogsByRecipient($email, $limit = 20) {
        $email = $this->escape($email);
        $limit = (int)$limit;
        $query = "SELECT * FROM {$this->logTable} 
                  WHERE email_to = '$email' 
                  ORDER BY sent_at DESC 
                  LIMIT $limit";
        return $this->getAll($query);
    }
    
    /**
     * Đếm số email đã gửi thành công/thất bại
     */
    public function getEmailStats() {
        $successQuery = "SELECT COUNT(*) as count FROM {$this->logTable} WHERE email_status = 1";
        $failQuery = "SELECT COUNT(*) as count FROM {$this->logTable} WHERE email_status = 0";
        
        $success = $this->getOne($successQuery);
        $fail = $this->getOne($failQuery);
        
        return [
            'success' => is_object($success) ? $success->count : ($success['count'] ?? 0),
            'failed' => is_object($fail) ? $fail->count : ($fail['count'] ?? 0)
        ];
    }
    
    /**
     * Gửi email thông báo đơn hàng
     */
    public function sendOrderEmail($orderData) {
        $variables = [
            'customer_name' => $orderData['customer_name'],
            'order_code' => $orderData['order_code'],
            'order_total' => number_format($orderData['order_total']) . 'đ',
            'order_date' => date('d/m/Y H:i', strtotime($orderData['order_date'])),
            'customer_address' => $orderData['customer_address'],
            'customer_phone' => $orderData['customer_phone']
        ];
        
        return $this->sendEmailWithTemplate(
            $orderData['customer_email'],
            'order',
            $variables
        );
    }
    
    /**
     * Gửi email khuyến mãi
     */
    public function sendPromotionEmail($email, $promotionData) {
        $variables = [
            'promotion_title' => $promotionData['title'] ?? 'Chương trình khuyến mãi',
            'promotion_content' => $promotionData['content'] ?? '',
            'discount_code' => $promotionData['code'] ?? '',
            'valid_from' => $promotionData['valid_from'] ?? '',
            'valid_to' => $promotionData['valid_to'] ?? ''
        ];
        
        return $this->sendEmailWithTemplate($email, 'promotion', $variables);
    }
}
