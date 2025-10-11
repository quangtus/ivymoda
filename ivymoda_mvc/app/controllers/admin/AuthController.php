<?php
namespace admin;

class AuthController extends \Controller {
    private $userModel;
    
    public function __construct() {
        $this->userModel = $this->model('UserModel');
    }
    
    /**
     * Hiển thị form đăng nhập admin
     */
    public function login() {
        // Kiểm tra nếu đã đăng nhập admin
        if(isset($_SESSION['user_id']) && $_SESSION['role_id'] == 1) {
            $this->redirect('admin/dashboard');
            return;
        }
        
        $data = [
            'title' => 'Đăng nhập Admin - IVY moda',
            'error' => ''
        ];
        
        // Xử lý đăng nhập
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if(empty($username) || empty($password)) {
                $data['error'] = 'Vui lòng nhập đầy đủ thông tin';
            } else {
                // Gọi hàm login từ model
                $result = $this->userModel->login($username, $password);
                
                if(is_object($result)) {
                    // Kiểm tra quyền admin
                    $role_id = is_object($result) ? $result->role_id : (is_array($result) ? $result['role_id'] : null);
                    
                    if($role_id == 1) {
                        // Đăng nhập thành công với quyền admin
                        $_SESSION['user_id'] = is_object($result) ? $result->id : $result['id'];
                        $_SESSION['username'] = is_object($result) ? $result->username : $result['username'];
                        $_SESSION['role_id'] = $role_id;
                        
                        $this->redirect('admin/dashboard');
                        return;
                    } else {
                        $data['error'] = 'Bạn không có quyền truy cập khu vực quản trị';
                    }
                } else {
                    $data['error'] = $result; // Thông báo lỗi
                }
            }
        }
        
        $this->view('admin/auth/login', $data);
    }
    
    /**
     * Đăng xuất admin
     */
    public function logout() {
        // Xóa session
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['role_id']);
        
        // Hủy session
        session_destroy();
        
        // Chuyển hướng về trang đăng nhập admin
        $this->redirect('admin/auth/login');
    }
}