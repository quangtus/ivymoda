<?php
/**
 * Helper class for sending emails
 */
class EmailHelper {
    /**
     * Send an email
     *
     * @param string $to Recipient email
     * @param string $toName Recipient name
     * @param string $subject Email subject
     * @param string $message Email message
     * @param string $altMessage Plain text version (optional)
     * @return boolean Success or failure
     */
    public static function sendEmail($to, $toName, $subject, $message, $altMessage = '') {
        // Use PHPMailer if available
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            require_once ROOT_PATH . 'vendor/autoload.php';
            
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = EMAIL_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = EMAIL_USERNAME;
                $mail->Password = EMAIL_PASSWORD;
                $mail->SMTPSecure = 'tls';
                $mail->Port = EMAIL_PORT;
                $mail->CharSet = 'UTF-8';
                
                $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
                $mail->addAddress($to, $toName);
                $mail->Subject = $subject;
                
                // HTML email
                $mail->isHTML(true);
                $mail->Body = $message;
                $mail->AltBody = $altMessage ?: strip_tags($message);
                
                $mail->send();
                return true;
            } catch (Exception $e) {
                error_log("Email sending failed: " . $mail->ErrorInfo);
                // Fallback to mail() function
            }
        }
        
        // Fallback to PHP mail function
        $headers = "From: " . EMAIL_FROM_NAME . " <" . EMAIL_FROM . ">\r\n";
        $headers .= "Reply-To: " . EMAIL_FROM . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        // Log email content
        file_put_contents(ROOT_PATH . 'logs/email.log', 
            date('Y-m-d H:i:s') . " - To: $to, Subject: $subject\n",
            FILE_APPEND);
            
        return mail($to, $subject, $message, $headers);
    }
    
    /**
     * Send order notification email
     *
     * @param array $order Order information
     * @param array $orderItems Order items
     * @param array $customer Customer information
     * @return boolean Success or failure
     */
    public static function sendOrderNotification($order, $orderItems, $customer) {
        $subject = "Đơn hàng #" . $order['order_code'] . " đã được đặt thành công";
        
        // Build HTML message
        $message = "<html><body>";
        $message .= "<h2>Cảm ơn bạn đã đặt hàng tại IVY moda</h2>";
        $message .= "<p>Xin chào " . $customer['fullname'] . ",</p>";
        $message .= "<p>Đơn hàng #" . $order['order_code'] . " của bạn đã được đặt thành công.</p>";
        $message .= "<h3>Chi tiết đơn hàng:</h3>";
        
        $message .= "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse;'>";
        $message .= "<tr><th>Sản phẩm</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th></tr>";
        
        $total = 0;
        foreach ($orderItems as $item) {
            $message .= "<tr>";
            $message .= "<td>" . $item['product_name'] . "</td>";
            $message .= "<td align='center'>" . $item['quantity'] . "</td>";
            $message .= "<td align='right'>" . number_format($item['price'], 0, ',', '.') . "đ</td>";
            $message .= "<td align='right'>" . number_format($item['price'] * $item['quantity'], 0, ',', '.') . "đ</td>";
            $message .= "</tr>";
            
            $total += $item['price'] * $item['quantity'];
        }
        
        $message .= "<tr><td colspan='3' align='right'><strong>Tổng cộng:</strong></td>";
        $message .= "<td align='right'><strong>" . number_format($total, 0, ',', '.') . "đ</strong></td></tr>";
        $message .= "</table>";
        
        $message .= "<h3>Thông tin giao hàng:</h3>";
        $message .= "<p>Họ tên: " . $customer['fullname'] . "</p>";
        $message .= "<p>Địa chỉ: " . $customer['address'] . "</p>";
        $message .= "<p>Điện thoại: " . $customer['phone'] . "</p>";
        
        $message .= "<p>Cảm ơn bạn đã mua sắm tại IVY moda!</p>";
        $message .= "</body></html>";
        
        return self::sendEmail($customer['email'], $customer['fullname'], $subject, $message);
    }
    
    /**
     * Send promotion notification email
     *
     * @param string $to Recipient email
     * @param string $toName Recipient name
     * @param array $promotion Promotion details
     * @return boolean Success or failure
     */
    public static function sendPromotionNotification($to, $toName, $promotion) {
        $subject = "Thông báo khuyến mãi: " . $promotion['title'];
        
        // Build HTML message
        $message = "<html><body>";
        $message .= "<h2>" . $promotion['title'] . "</h2>";
        $message .= "<p>Xin chào " . $toName . ",</p>";
        $message .= "<p>" . $promotion['description'] . "</p>";
        
        if (!empty($promotion['code'])) {
            $message .= "<p>Sử dụng mã: <strong style='background-color: #f5f5f5; padding: 5px 10px; font-size: 18px;'>" . $promotion['code'] . "</strong></p>";
        }
        
        $message .= "<p>Thời gian áp dụng: " . date('d/m/Y', strtotime($promotion['start_date'])) . " - " . date('d/m/Y', strtotime($promotion['end_date'])) . "</p>";
        
        if (!empty($promotion['image'])) {
            $message .= "<img src='" . BASE_URL . "assets/uploads/" . $promotion['image'] . "' style='max-width: 600px;' />";
        }
        
        $message .= "<p><a href='" . BASE_URL . "promotion/" . $promotion['id'] . "' style='background-color: #221f20; color: white; padding: 10px 15px; text-decoration: none; border-radius: 3px;'>Xem chi tiết</a></p>";
        
        $message .= "<p>Trân trọng,<br>IVY moda</p>";
        $message .= "</body></html>";
        
        return self::sendEmail($to, $toName, $subject, $message);
    }
}