<?php
class UserController extends Controller {
    private $userModel;
    private $orderModel;
    
    public function __construct() {
        $this->userModel = $this->model('UserModel');
        $this->orderModel = $this->model('OrderModel');
        
        // Kiểm tra đăng nhập
        if(!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            exit;
        }
    }
    
    /**
     * Hiển thị trang profile người dùng
     */
    public function profile() {
        $user_id = $_SESSION['user_id'];
        
        // Lấy thông tin người dùng
        $user_info = $this->userModel->getUserById($user_id);
        
        if(!$user_info) {
            $this->redirect('auth/login');
            exit;
        }
        
        // Lấy danh sách đơn hàng của user
        $orders = $this->orderModel->getOrdersByUser($user_id);

        $data = [
            'title' => 'Tài khoản của tôi - IVY moda',
            'user_info' => $user_info,
            'orders' => $orders,
            'error' => '',
            'success' => ''
        ];
        
        // Xử lý cập nhật thông tin profile
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
            $fullname = trim($_POST['fullname']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $address = trim($_POST['address']);
            
            if(empty($fullname) || empty($email)) {
                $data['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc';
            } else {
                $result = $this->userModel->updateProfile($user_id, $fullname, $email, $phone, $address);
                
                if($result == "success") {
                    $data['success'] = 'Cập nhật thông tin thành công!';
                    // Cập nhật lại thông tin hiển thị
                    $data['user_info'] = $this->userModel->getUserById($user_id);
                } else {
                    $data['error'] = $result;
                }
            }
        }
        
        // Xử lý đổi mật khẩu
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            
            if(empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $data['error'] = 'Vui lòng điền đầy đủ thông tin';
            } elseif($new_password !== $confirm_password) {
                $data['error'] = 'Mật khẩu xác nhận không khớp';
            } elseif(strlen($new_password) < 6) {
                $data['error'] = 'Mật khẩu mới phải có ít nhất 6 ký tự';
            } else {
                $result = $this->userModel->changePassword($user_id, $current_password, $new_password);
                
                if($result == "success") {
                    // Gửi email thông báo đổi mật khẩu thành công
                    $this->sendPasswordChangeNotificationEmail($user_info->email, $user_info->fullname);
                    $data['success'] = 'Đổi mật khẩu thành công! Email thông báo đã được gửi đến ' . $user_info->email;
                } else {
                    $data['error'] = $result;
                }
            }
        }
        
        $this->view('frontend/user/profile', $data);
    }
    
    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function orderDetail($order_id) {
        $order_id = (int)$order_id;
        $user_id = $_SESSION['user_id'];
        $order = $this->orderModel->getOrderById($order_id);
        if (!$order) {
            $this->redirect('user/profile');
            return;
        }
        $orderUserId = is_object($order) ? (int)$order->user_id : (int)$order['user_id'];
        if ($orderUserId !== (int)$user_id) {
            $this->redirect('user/profile');
            return;
        }
        $orderItems = $this->orderModel->getOrderItems($order_id);
        $data = [
            'title' => 'Chi tiết đơn hàng #' . (is_object($order) ? $order->order_code : $order['order_code']),
            'order' => $order,
            'orderItems' => $orderItems
        ];
        $this->view('frontend/user/order_detail', $data);
    }
    
    /**
     * Hủy đơn hàng
     */
    public function cancelOrder($order_id) {
        $order_id = (int)$order_id;
        $user_id = $_SESSION['user_id'];
        $order = $this->orderModel->getOrderById($order_id);
        if (!$order) {
            $this->redirect('user/profile');
            return;
        }
        $orderUserId = is_object($order) ? (int)$order->user_id : (int)$order['user_id'];
        if ($orderUserId !== (int)$user_id) {
            $this->redirect('user/profile');
            return;
        }
        // Chỉ cho phép hủy khi còn ở trạng thái chờ xử lý
        $orderStatus = is_object($order) ? (int)$order->order_status : (int)$order['order_status'];
        if ($orderStatus !== 0) {
            $this->redirect('user/orderDetail/' . $order_id . '?error=cannot_cancel');
            return;
        }
        $this->orderModel->updateOrderStatus($order_id, 3);
        $this->redirect('user/orderDetail/' . $order_id . '?success=cancelled');
    }
    
    /**
     * Quản lý email - hiển thị lịch sử email và cài đặt
     */
    public function emailSettings() {
        $user_id = $_SESSION['user_id'];
        $user_info = $this->userModel->getUserById($user_id);
        
        if(!$user_info) {
            $this->redirect('auth/login');
            exit;
        }
        
        // Load EmailModel để lấy lịch sử email
        require_once ROOT_PATH . 'app/models/EmailModel.php';
        $emailModel = new EmailModel();
        
        // Lấy lịch sử email của user
        $emailLogs = $emailModel->getUserEmailLogs($user_id, 20);
        
        $data = [
            'title' => 'Quản lý Email - IVY Moda',
            'user_info' => $user_info,
            'email_logs' => $emailLogs,
            'error' => '',
            'success' => ''
        ];
        
        // Xử lý cập nhật cài đặt email
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_email_settings'])) {
            $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
            $promotion_emails = isset($_POST['promotion_emails']) ? 1 : 0;
            
            $result = $this->userModel->updateEmailSettings($user_id, $email_notifications, $promotion_emails);
            
            if($result == "success") {
                $data['success'] = 'Cập nhật cài đặt email thành công!';
            } else {
                $data['error'] = $result;
            }
        }
        
        $this->view('frontend/user/email_settings', $data);
    }
    
    /**
     * Gửi email thông báo đổi mật khẩu thành công
     */
    private function sendPasswordChangeNotificationEmail($email, $name) {
        // Sử dụng EmailHelper để gửi email
        require_once ROOT_PATH . 'app/helpers/EmailHelper.php';
        $emailHelper = new EmailHelper();
        
        $subject = "Thông báo đổi mật khẩu thành công - IVY Moda";
        
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .footer { background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>IVY Moda</h2>
                </div>
                <div class='content'>
                    <p>Xin chào <strong>{$name}</strong>,</p>
                    <p>Mật khẩu tài khoản của bạn đã được thay đổi thành công vào lúc " . date('d/m/Y H:i') . ".</p>
                    <p>Nếu bạn không thực hiện thay đổi này, vui lòng liên hệ với chúng tôi ngay lập tức.</p>
                    <p>Để bảo mật tài khoản, chúng tôi khuyên bạn:</p>
                    <ul>
                        <li>Không chia sẻ mật khẩu với bất kỳ ai</li>
                        <li>Sử dụng mật khẩu mạnh và độc đáo</li>
                        <li>Thay đổi mật khẩu định kỳ</li>
                    </ul>
                </div>
                <div class='footer'>
                    <p>© 2025 IVY Moda. Tất cả quyền được bảo lưu.</p>
                </div>
            </div>
        </body>
        </html>";
        
        return $emailHelper->sendEmail($email, $subject, $body);
    }
}