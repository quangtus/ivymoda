<?php
// Try multiple paths for autoloader
$autoloadPaths = [
    __DIR__ . '/../../vendor/autoload.php',
    dirname(__DIR__, 2) . '/vendor/autoload.php',
    realpath(__DIR__ . '/../../vendor/autoload.php')
];

$autoloaderLoaded = false;
foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $autoloaderLoaded = true;
        break;
    }
}

if (!$autoloaderLoaded) {
    // Fallback: include PHPMailer directly
    require_once __DIR__ . '/../../vendor/PHPMailer/src/Exception.php';
    require_once __DIR__ . '/../../vendor/PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/../../vendor/PHPMailer/src/SMTP.php';
} else {
    // Autoloader loaded but PHPMailer classes might not be found, try direct includes
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        require_once __DIR__ . '/../../vendor/PHPMailer/src/Exception.php';
        require_once __DIR__ . '/../../vendor/PHPMailer/src/PHPMailer.php';
        require_once __DIR__ . '/../../vendor/PHPMailer/src/SMTP.php';
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailHelper {
    private $mail;
    private $emailModel;
    
    public function __construct() {
        $this->emailModel = new EmailModel();
        $this->initializeMailer();
    }
    
    /**
     * Khởi tạo PHPMailer
     */
    private function initializeMailer() {
        $this->mail = new PHPMailer(true);
        
        try {
            // Load environment variables
            require_once __DIR__ . '/EnvHelper.php';
            EnvHelper::load();
            
            // Server settings
            $this->mail->isSMTP();
            $this->mail->Host       = EnvHelper::get('SMTP_HOST', 'smtp.gmail.com');
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = EnvHelper::get('SMTP_USERNAME', '');
            $this->mail->Password   = EnvHelper::get('SMTP_PASSWORD', '');
            $this->mail->SMTPSecure = EnvHelper::get('SMTP_SECURE', 'tls');
            $this->mail->Port       = (int)EnvHelper::get('SMTP_PORT', 587);
            $this->mail->CharSet    = 'UTF-8';
            $this->mail->SMTPDebug  = 0; // Set to 2 for debugging
            
            // Sender
            $this->mail->setFrom(
                EnvHelper::get('SMTP_FROM_EMAIL', 'noreply@ivymoda.com'), 
                EnvHelper::get('SMTP_FROM_NAME', 'IVY Moda')
            );
            
        } catch (Exception $e) {
            error_log("EmailHelper initialization failed: " . $e->getMessage());
        }
    }
    
    /**
     * Gửi email xác nhận đăng ký
     */
    public function sendRegistrationConfirmation($email, $username, $activationToken) {
        // Kiểm tra xem user có muốn nhận email không
        if (!$this->checkUserEmailSettings($email, 'email_notifications')) {
            return true; // Không gửi nhưng vẫn trả về true để không báo lỗi
        }
        
        $template = $this->emailModel->getTemplate('registration_confirmation');
        if (!$template) {
            return $this->sendDefaultRegistrationEmail($email, $username, $activationToken);
        }
        
        $subject = $this->replaceVariables($template->subject, [
            'username' => $username,
            'email' => $email
        ]);
        
        $body = $this->replaceVariables($template->body, [
            'username' => $username,
            'email' => $email,
            'activation_link' => $this->getActivationLink($activationToken)
        ]);
        
        return $this->sendEmail($email, $subject, $body);
    }
    
    /**
     * Gửi email xác nhận đơn hàng
     */
    public function sendOrderConfirmation($email, $orderData) {
        // Kiểm tra xem user có muốn nhận email không
        if (!$this->checkUserEmailSettings($email, 'email_notifications')) {
            return true; // Không gửi nhưng vẫn trả về true để không báo lỗi
        }
        
        $template = $this->emailModel->getTemplate('order_confirmation');
        if (!$template) {
            return $this->sendDefaultOrderEmail($email, $orderData);
        }
        
        $subject = $this->replaceVariables($template->subject, [
            'order_code' => $orderData['order_code'],
            'customer_name' => $orderData['customer_name']
        ]);
        
        $body = $this->replaceVariables($template->body, [
            'customer_name' => $orderData['customer_name'],
            'order_code' => $orderData['order_code'],
            'order_total' => number_format($orderData['order_total'], 0, ',', '.') . ' ₫',
            'order_date' => date('d/m/Y H:i', strtotime($orderData['order_date'])),
            'customer_address' => $orderData['customer_address'],
            'payment_method' => $orderData['payment_method'],
            'order_items' => $this->formatOrderItems($orderData['items'])
        ]);
        
        return $this->sendEmail($email, $subject, $body);
    }
    
    /**
     * Gửi email khôi phục mật khẩu
     */
    public function sendPasswordReset($email, $username, $resetToken) {
        // Kiểm tra xem user có muốn nhận email không
        if (!$this->checkUserEmailSettings($email, 'email_notifications')) {
            return true; // Không gửi nhưng vẫn trả về true để không báo lỗi
        }
        
        $template = $this->emailModel->getTemplate('password_reset');
        if (!$template) {
            return $this->sendDefaultPasswordResetEmail($email, $username, $resetToken);
        }
        
        $subject = $this->replaceVariables($template->subject, [
            'username' => $username
        ]);
        
        $body = $this->replaceVariables($template->body, [
            'username' => $username,
            'reset_link' => $this->getResetPasswordLink($resetToken),
            'expiry_time' => '1 giờ'
        ]);
        
        return $this->sendEmail($email, $subject, $body);
    }
    
    /**
     * Gửi email khuyến mãi hàng loạt
     */
    public function sendPromotionEmail($promotionData, $recipients = null) {
        if (!$recipients) {
            $recipients = $this->emailModel->getCustomerEmails();
        }
        
        $results = [
            'sent' => 0,
            'failed' => 0,
            'errors' => []
        ];
        
        // Lấy template promotion
        $template = $this->emailModel->getTemplate('promotion');
        if (!$template) {
            // Sử dụng template mặc định nếu không có template
            $template = (object)[
                'subject' => 'Khuyến mãi đặc biệt - IVY Moda',
                'body' => $this->getDefaultPromotionTemplate()
            ];
        }
        
        foreach ($recipients as $recipient) {
            try {
                $subject = $this->replaceVariables($template->subject, [
                    'customer_name' => $recipient['fullname'] ?? 'Khách hàng',
                    'promotion_title' => $promotionData['title']
                ]);
                
                $body = $this->replaceVariables($template->body, [
                    'customer_name' => $recipient['fullname'] ?? 'Khách hàng',
                    'promotion_title' => $promotionData['title'],
                    'content' => $promotionData['content'],
                    'start_date' => date('d/m/Y', strtotime($promotionData['start_date'])),
                    'end_date' => date('d/m/Y', strtotime($promotionData['end_date']))
                ]);
                
                $success = $this->sendEmail($recipient['email'], $subject, $body);
                
                if ($success) {
                    $results['sent']++;
                    $this->emailModel->logPromotionEmail($promotionData['title'], $recipient['email'], $recipient['id'] ?? null, 'sent');
                } else {
                    $results['failed']++;
                    $results['errors'][] = "Failed to send to {$recipient['email']}";
                    $this->emailModel->logPromotionEmail($promotionData['title'], $recipient['email'], $recipient['id'] ?? null, 'failed', 'Unknown error');
                }
                
                // Giới hạn 100 email/phút
                if (($results['sent'] + $results['failed']) % 100 === 0) {
                    sleep(60);
                }
                
            } catch (Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Error sending to {$recipient['email']}: " . $e->getMessage();
                $this->emailModel->logPromotionEmail($promotionData['title'], $recipient['email'], $recipient['id'] ?? null, 'failed', $e->getMessage());
            }
        }
        
        return $results;
    }
    
    /**
     * Gửi email chung
     */
    public function sendEmail($to, $subject, $body, $isHTML = true) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to);
            $this->mail->Subject = $subject;
            $this->mail->isHTML($isHTML);
            $this->mail->Body = $body;
            
            $success = $this->mail->send();
            
            // Log email
            $this->emailModel->logEmail($to, $subject, $body, $success ? 'sent' : 'failed');
            
            return $success;
            
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            $this->emailModel->logEmail($to, $subject, $body, 'failed', $e->getMessage());
            return false;
        }
    }
    
    /**
     * Thay thế biến trong template
     */
    private function replaceVariables($content, $variables) {
        foreach ($variables as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }
        return $content;
    }
    
    /**
     * Tạo link kích hoạt tài khoản
     */
    private function getActivationLink($token) {
        $baseUrl = EnvHelper::get('BASE_URL', 'http://localhost/ivymoda/ivymoda_mvc/public/');
        return $baseUrl . 'auth/activate?token=' . $token;
    }
    
    /**
     * Tạo link đặt lại mật khẩu
     */
    private function getResetPasswordLink($token) {
        $baseUrl = EnvHelper::get('BASE_URL', 'http://localhost/ivymoda/ivymoda_mvc/public/');
        return $baseUrl . 'auth/reset-password?token=' . $token;
    }
    
    /**
     * Định dạng danh sách sản phẩm trong đơn hàng
     */
    private function formatOrderItems($items) {
        $html = '<table style="border-collapse: collapse; width: 100%; margin: 20px 0;">';
        $html .= '<tr style="background-color: #f5f5f5;">';
        $html .= '<th style="border: 1px solid #ddd; padding: 12px; text-align: left;">Sản phẩm</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Size</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Màu</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Số lượng</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 12px; text-align: right;">Giá</th>';
        $html .= '</tr>';
        
        foreach ($items as $item) {
            // Xử lý cả object và array
            $sanpham_ten = is_object($item) ? $item->sanpham_ten : $item['sanpham_ten'];
            $sanpham_size = is_object($item) ? $item->sanpham_size : $item['sanpham_size'];
            $sanpham_color = is_object($item) ? $item->sanpham_color : $item['sanpham_color'];
            $sanpham_soluong = is_object($item) ? $item->sanpham_soluong : $item['sanpham_soluong'];
            $sanpham_gia = is_object($item) ? $item->sanpham_gia : $item['sanpham_gia'];
            
            $html .= '<tr>';
            $html .= '<td style="border: 1px solid #ddd; padding: 12px;">' . htmlspecialchars($sanpham_ten) . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 12px; text-align: center;">' . htmlspecialchars($sanpham_size) . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 12px; text-align: center;">' . htmlspecialchars($sanpham_color) . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 12px; text-align: center;">' . $sanpham_soluong . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 12px; text-align: right;">' . number_format($sanpham_gia * $sanpham_soluong, 0, ',', '.') . ' ₫</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        return $html;
    }
    
    /**
     * Email đăng ký mặc định
     */
    private function sendDefaultRegistrationEmail($email, $username, $activationToken) {
        $subject = "Xác nhận đăng ký tài khoản - IVY Moda";
        $activationLink = $this->getActivationLink($activationToken);
        
        $body = "
        <html>
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
            <div class='container'>
                <div class='header'>
                    <h2>Chào mừng đến với IVY Moda!</h2>
                </div>
                <div class='content'>
                    <p>Xin chào <strong>{$username}</strong>,</p>
                    <p>Cảm ơn bạn đã đăng ký tài khoản tại IVY Moda. Để kích hoạt tài khoản, vui lòng click vào link bên dưới:</p>
                    <p style='text-align: center;'>
                        <a href='{$activationLink}' class='button'>Kích hoạt tài khoản</a>
                    </p>
                    <p>Link này có hiệu lực trong 24 giờ.</p>
                    <p>Nếu bạn không thực hiện đăng ký này, vui lòng bỏ qua email này.</p>
                </div>
                <div class='footer'>
                    <p>© 2025 IVY Moda. Tất cả quyền được bảo lưu.</p>
                </div>
            </div>
        </body>
        </html>";
        
        return $this->sendEmail($email, $subject, $body);
    }
    
    /**
     * Email đơn hàng mặc định
     */
    private function sendDefaultOrderEmail($email, $orderData) {
        $subject = "Xác nhận đơn hàng #{$orderData['order_code']} - IVY Moda";
        
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .order-info { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .footer { background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Đơn hàng của bạn đã được xác nhận!</h2>
                </div>
                <div class='content'>
                    <p>Xin chào <strong>{$orderData['customer_name']}</strong>,</p>
                    <p>Cảm ơn bạn đã mua sắm tại IVY Moda. Đơn hàng của bạn đã được xác nhận và đang được xử lý.</p>
                    
                    <div class='order-info'>
                        <h3>Thông tin đơn hàng</h3>
                        <p><strong>Mã đơn hàng:</strong> #{$orderData['order_code']}</p>
                        <p><strong>Ngày đặt:</strong> " . date('d/m/Y H:i', strtotime($orderData['order_date'])) . "</p>
                        <p><strong>Tổng tiền:</strong> " . number_format($orderData['order_total'], 0, ',', '.') . " ₫</p>
                        <p><strong>Phương thức thanh toán:</strong> {$orderData['payment_method']}</p>
                        <p><strong>Địa chỉ giao hàng:</strong> {$orderData['customer_address']}</p>
                    </div>
                    
                    <p>Chúng tôi sẽ thông báo cho bạn khi đơn hàng được giao.</p>
                </div>
                <div class='footer'>
                    <p>© 2025 IVY Moda. Tất cả quyền được bảo lưu.</p>
                </div>
            </div>
        </body>
        </html>";
        
        return $this->sendEmail($email, $subject, $body);
    }
    
    /**
     * Email đặt lại mật khẩu mặc định
     */
    private function sendDefaultPasswordResetEmail($email, $username, $resetToken) {
        $subject = "Đặt lại mật khẩu - IVY Moda";
        $resetLink = $this->getResetPasswordLink($resetToken);
        
        $body = "
        <html>
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
            <div class='container'>
                <div class='header'>
                    <h2>Đặt lại mật khẩu</h2>
                </div>
                <div class='content'>
                    <p>Xin chào <strong>{$username}</strong>,</p>
                    <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn. Click vào link bên dưới để đặt lại mật khẩu:</p>
                    <p style='text-align: center;'>
                        <a href='{$resetLink}' class='button'>Đặt lại mật khẩu</a>
                    </p>
                    <p>Link này có hiệu lực trong 1 giờ.</p>
                    <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
                </div>
                <div class='footer'>
                    <p>© 2025 IVY Moda. Tất cả quyền được bảo lưu.</p>
                </div>
            </div>
        </body>
        </html>";
        
        return $this->sendEmail($email, $subject, $body);
    }
    
    /**
     * Kiểm tra cài đặt email của user
     */
    private function checkUserEmailSettings($email, $settingType) {
        $sql = "SELECT {$settingType} FROM users WHERE email = ?";
        $result = $this->emailModel->query($sql, [$email]);
        return $result && $result->$settingType == 1;
    }
    
    /**
     * Template promotion mặc định
     */
    private function getDefaultPromotionTemplate() {
        return '<html>
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
</html>';
    }
}