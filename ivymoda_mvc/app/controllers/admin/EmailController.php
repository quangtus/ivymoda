<?php
namespace admin;

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../models/EmailModel.php';
require_once __DIR__ . '/../../helpers/EmailHelper.php';

class EmailController extends \Controller {
    private $emailModel;
    private $emailHelper;
    
    public function __construct() {
        $this->emailModel = new \EmailModel();
        $this->emailHelper = new \EmailHelper();
    }
    
    /**
     * Trang chính quản lý email - Dashboard
     */
    public function index() {
        $this->checkAdminAuth();
        
        $stats = $this->emailModel->getEmailStats();
        $recentLogs = $this->emailModel->getEmailLogs(10);
        
        $data = [
            'title' => 'Dashboard Email',
            'stats' => $stats,
            'recentLogs' => $recentLogs
        ];
        
        $this->view('admin/email/index', $data);
    }
    
    /**
     * Xem log email - UC requirement
     */
    public function logs() {
        $this->checkAdminAuth();
        
        $page = $_GET['page'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $logs = $this->emailModel->getEmailLogs($limit, $offset);
        $totalLogs = $this->emailModel->getEmailLogCount();
        $totalPages = ceil($totalLogs / $limit);
        
        $data = [
            'title' => 'Xem Log Email',
            'logs' => $logs,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalLogs' => $totalLogs
        ];
        
        $this->view('admin/email/logs', $data);
    }
    
    
    /**
     * Gửi Email Khuyến Mãi - UC requirement
     */
    public function sendPromotion() {
        $this->checkAdminAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $promotionId = trim($_POST['promotion_id'] ?? '');
            $sendType = $_POST['send_type'] ?? 'all';
            $customRecipients = $_POST['custom_recipients'] ?? '';
            
            if (empty($promotionId)) {
                $this->setFlashMessage('Vui lòng chọn khuyến mãi', 'error');
            } else {
                // Lấy thông tin khuyến mãi
                $promotion = $this->emailModel->getPromotionById($promotionId);
                if (!$promotion) {
                    $this->setFlashMessage('Khuyến mãi không tồn tại', 'error');
                } else {
                    $recipients = null;
                    
                    // Nếu có danh sách email tùy chỉnh
                    if ($sendType === 'custom' && !empty($customRecipients)) {
                        $emails = array_filter(array_map('trim', explode("\n", $customRecipients)));
                        $recipients = [];
                        foreach ($emails as $email) {
                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $recipients[] = ['email' => $email, 'fullname' => 'Khách hàng'];
                            }
                        }
                    }
                    
                    // Gửi email khuyến mãi
                    $results = $this->emailHelper->sendPromotionEmail([
                        'promotion_id' => $promotion->promotion_id,
                        'title' => $promotion->title,
                        'content' => $promotion->content,
                        'start_date' => $promotion->start_date,
                        'end_date' => $promotion->end_date
                    ], $recipients);
                    
                    $message = "Đã gửi thành công {$results['sent']} email";
                    if ($results['failed'] > 0) {
                        $message .= ", {$results['failed']} email thất bại";
                    }
                    
                    $this->setFlashMessage($message, $results['failed'] > 0 ? 'warning' : 'success');
                }
            }
        }
        
        // Lấy danh sách khuyến mãi
        $promotions = $this->emailModel->getPromotions();
        
        // Lấy thống kê khách hàng
        $customerStats = $this->emailModel->getCustomerEmailStats();
        
        $data = [
            'title' => 'Gửi Email Khuyến Mãi',
            'promotions' => $promotions,
            'customerStats' => $customerStats
        ];
        
        $this->view('admin/email/send_promotion', $data);
    }
    
    
    /**
     * Cấu hình SMTP - UC requirement
     */
    public function smtpConfig() {
        $this->checkAdminAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $smtpHost = trim($_POST['smtp_host'] ?? '');
            $smtpUsername = trim($_POST['smtp_username'] ?? '');
            $smtpPassword = trim($_POST['smtp_password'] ?? '');
            $smtpPort = trim($_POST['smtp_port'] ?? '587');
            $smtpSecure = trim($_POST['smtp_secure'] ?? 'tls');
            $fromEmail = trim($_POST['from_email'] ?? '');
            $fromName = trim($_POST['from_name'] ?? '');
            
            // Cập nhật file .env
            $envFile = __DIR__ . '/../../../.env';
            if (file_exists($envFile)) {
                $envContent = file_get_contents($envFile);
                
                $envUpdates = [
                    'SMTP_HOST' => $smtpHost,
                    'SMTP_USERNAME' => $smtpUsername,
                    'SMTP_PASSWORD' => $smtpPassword,
                    'SMTP_PORT' => $smtpPort,
                    'SMTP_SECURE' => $smtpSecure,
                    'SMTP_FROM_EMAIL' => $fromEmail,
                    'SMTP_FROM_NAME' => $fromName
                ];
                
                foreach ($envUpdates as $key => $value) {
                    $pattern = "/^{$key}=.*/m";
                    $replacement = "{$key}={$value}";
                    
                    if (preg_match($pattern, $envContent)) {
                        $envContent = preg_replace($pattern, $replacement, $envContent);
                    } else {
                        $envContent .= "\n{$replacement}";
                    }
                }
                
                if (file_put_contents($envFile, $envContent)) {
                    $this->setFlashMessage('Cập nhật cấu hình SMTP thành công', 'success');
                } else {
                    $this->setFlashMessage('Có lỗi xảy ra khi cập nhật cấu hình', 'error');
                }
            } else {
                $this->setFlashMessage('File .env không tồn tại', 'error');
            }
        }
        
        // Đọc cấu hình hiện tại
        $config = [
            'smtp_host' => $_ENV['SMTP_HOST'] ?? '',
            'smtp_username' => $_ENV['SMTP_USERNAME'] ?? '',
            'smtp_password' => $_ENV['SMTP_PASSWORD'] ?? '',
            'smtp_port' => $_ENV['SMTP_PORT'] ?? '587',
            'smtp_secure' => $_ENV['SMTP_SECURE'] ?? 'tls',
            'from_email' => $_ENV['SMTP_FROM_EMAIL'] ?? '',
            'from_name' => $_ENV['SMTP_FROM_NAME'] ?? ''
        ];
        
        $data = [
            'title' => 'Cấu hình SMTP',
            'config' => $config
        ];
        
        $this->view('admin/email/smtp_config', $data);
    }
    
    /**
     * Kiểm tra quyền admin
     */
    private function checkAdminAuth() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
            $this->redirect('/admin/auth/login');
        }
    }
    
    /**
     * Thiết lập flash message
     */
    private function setFlashMessage($message, $type = 'info') {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    
    /**
     * Quản lý Template Email
     */
    public function templates() {
        $this->checkAdminAuth();
        
        $templates = $this->emailModel->getTemplates();
        
        $data = [
            'title' => 'Quản lý Template Email',
            'templates' => $templates
        ];
        
        $this->view('admin/email/templates', $data);
    }
    
    /**
     * Thêm Template Email
     */
    public function addTemplate() {
        $this->checkAdminAuth();
        
        // Khởi tạo dữ liệu mặc định
        $data = [
            'title' => 'Thêm Template Email',
            'template_name' => '',
            'subject' => '',
            'body' => '',
            'type' => ''
        ];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $templateName = trim($_POST['template_name'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $body = $_POST['body'] ?? '';
            $type = trim($_POST['type'] ?? '');
            
            // Cập nhật dữ liệu để hiển thị lại form
            $data['template_name'] = $templateName;
            $data['subject'] = $subject;
            $data['body'] = $body;
            $data['type'] = $type;
            
            if (empty($templateName) || empty($subject) || empty($body)) {
                $this->setFlashMessage('Vui lòng điền đầy đủ thông tin', 'error');
            } else {
                $result = $this->emailModel->addTemplate($templateName, $subject, $body, $type);
                if ($result) {
                    $this->setFlashMessage('Thêm template thành công', 'success');
                    $this->redirect(BASE_URL . 'admin/email/templates');
                } else {
                    $this->setFlashMessage('Có lỗi xảy ra khi thêm template', 'error');
                }
            }
        }
        
        $this->view('admin/email/add_template', $data);
    }
    
    /**
     * Sửa Template Email
     */
    public function editTemplate($templateId) {
        $this->checkAdminAuth();
        
        $template = $this->emailModel->getTemplateById($templateId);
        if (!$template) {
            $this->setFlashMessage('Template không tồn tại', 'error');
            $this->redirect(BASE_URL . 'admin/email/templates');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $templateName = trim($_POST['template_name'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $body = $_POST['body'] ?? '';
            $type = trim($_POST['type'] ?? '');
            
            if (empty($templateName) || empty($subject) || empty($body)) {
                $this->setFlashMessage('Vui lòng điền đầy đủ thông tin', 'error');
            } else {
                $result = $this->emailModel->updateTemplate($templateId, $templateName, $subject, $body, $type);
                if ($result) {
                    $this->setFlashMessage('Cập nhật template thành công', 'success');
                    $this->redirect(BASE_URL . 'admin/email/templates');
                } else {
                    $this->setFlashMessage('Có lỗi xảy ra khi cập nhật template', 'error');
                }
            }
        }
        
        $data = [
            'title' => 'Sửa Template Email',
            'template' => $template
        ];
        
        $this->view('admin/email/edit_template', $data);
    }
    
    /**
     * Xóa Template Email
     */
    public function deleteTemplate($templateId) {
        $this->checkAdminAuth();
        
        $result = $this->emailModel->deleteTemplate($templateId);
        if ($result) {
            $this->setFlashMessage('Xóa template thành công', 'success');
        } else {
            $this->setFlashMessage('Có lỗi xảy ra khi xóa template', 'error');
        }
        
        $this->redirect(BASE_URL . 'admin/email/templates');
    }
    
    /**
     * Xem nội dung email log
     */
    public function viewLog($logId) {
        $this->checkAdminAuth();
        
        $log = $this->emailModel->getEmailLogById($logId);
        if (!$log) {
            $this->setFlashMessage('Log email không tồn tại', 'error');
            $this->redirect(BASE_URL . 'admin/email/logs');
        }
        
        $data = [
            'title' => 'Xem nội dung Email',
            'log' => $log
        ];
        
        $this->view('admin/email/view_log', $data);
    }
    
    /**
     * Xem log email khuyến mãi
     */
    public function promotionLogs() {
        $this->checkAdminAuth();
        
        $page = $_GET['page'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $logs = $this->emailModel->getPromotionEmailLogs();
        $totalLogs = count($logs);
        $totalPages = ceil($totalLogs / $limit);
        
        // Phân trang
        $logs = array_slice($logs, $offset, $limit);
        
        $data = [
            'title' => 'Log Email Khuyến Mãi',
            'logs' => $logs,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalLogs' => $totalLogs,
            'stats' => [
                'sent' => count(array_filter($logs, function($log) { 
                    return ($log->status ?? $log['status'] ?? '') === 'sent'; 
                })),
                'failed' => count(array_filter($logs, function($log) { 
                    return ($log->status ?? $log['status'] ?? '') === 'failed'; 
                })),
                'pending' => count(array_filter($logs, function($log) { 
                    return ($log->status ?? $log['status'] ?? '') === 'pending'; 
                }))
            ]
        ];
        
        $this->view('admin/email/promotion_logs', $data);
    }
    
    /**
     * Redirect
     */
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
}
