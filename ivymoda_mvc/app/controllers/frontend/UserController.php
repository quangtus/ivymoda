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
                    $data['success'] = 'Đổi mật khẩu thành công!';
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
}