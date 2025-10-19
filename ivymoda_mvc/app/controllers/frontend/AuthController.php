<?php
// Đừng thêm namespace ở đây

class AuthController extends Controller {
    private $userModel;
    
    public function __construct() {
        $this->userModel = $this->model('UserModel');
    }
    
    // Hiển thị form đăng nhập
    public function login() {
        // Kiểm tra nếu người dùng đã đăng nhập
        if(isset($_SESSION['user_id'])) {
            $this->redirect('');
            return;
        }
        
        $data = [
            'title' => 'Đăng nhập - IVY moda',
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
                    // Đăng nhập thành công, lưu session
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $result->id;
                    $_SESSION['username'] = $result->username;
                    $_SESSION['role_id'] = $result->role_id;

                    // Đồng bộ giỏ hàng hiện tại về user sau khi có session_id mới
                    try {
                        $cartModel = $this->model('CartModel');
                        if ($cartModel) {
                            $cartModel->syncCartToUser(session_id(), $result->id);
                        }
                    } catch (Exception $e) {
                        error_log('AuthController login sync cart error: ' . $e->getMessage());
                    }
                    
                    // Chuyển hướng tới trang chủ hoặc admin
                    if($result->role_id == 1) {
                        $this->redirect('admin/dashboard');
                    } else {
                        $this->redirect('');
                    }
                    return;
                } else {
                    $data['error'] = $result; // Thông báo lỗi
                }
            }
        }
        
        $this->view('frontend/auth/login', $data);
    }
    
    // Hiển thị form đăng ký
    public function register() {
        // Kiểm tra nếu người dùng đã đăng nhập
        if(isset($_SESSION['user_id'])) {
            $this->redirect('');
            return;
        }
        
        $data = [
            'title' => 'Đăng ký - IVY moda',
            'error' => '',
            'success' => ''
        ];
        
        // Xử lý đăng ký
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $email = trim($_POST['email'] ?? '');
            $fullname = trim($_POST['fullname'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            
            // Validate thông tin
            if(empty($username) || empty($password) || empty($confirm_password) || empty($email) || empty($fullname)) {
                $data['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc';
            } elseif($password != $confirm_password) {
                $data['error'] = 'Mật khẩu xác nhận không khớp';
            } elseif(strlen($password) < 6) {
                $data['error'] = 'Mật khẩu phải có ít nhất 6 ký tự';
            } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data['error'] = 'Email không hợp lệ';
            } else {
                $result = $this->userModel->register($username, $password, $email, $fullname, $phone, $address);
                
                if($result === true) {
                    // Gửi email xác nhận đăng ký
                    $this->sendRegistrationConfirmationEmail($email, $fullname, $username);
                    $data['success'] = 'Đăng ký tài khoản thành công! Vui lòng kiểm tra email để xác nhận tài khoản.';
                } else {
                    $data['error'] = $result;
                }
            }
        }
        
        $this->view('frontend/auth/register', $data);
    }
    
    // Đăng xuất
    public function logout() {
        // Xóa session
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['role_id']);
        
        // Hủy session
        session_destroy();
        // Tạo session mới để tránh tái sử dụng session_id cũ
        session_start();
        session_regenerate_id(true);
        
        // Chuyển hướng về trang đăng nhập
        $this->redirect('auth/login');
    }
    
    // Quên mật khẩu
    public function forgotPassword() {
        // Kiểm tra nếu người dùng đã đăng nhập
        if(isset($_SESSION['user_id'])) {
            $this->redirect('');
            return;
        }
        
        $data = [
            'title' => 'Quên mật khẩu - IVY moda',
            'error' => '',
            'success' => ''
        ];
        
        // Xử lý yêu cầu đặt lại mật khẩu
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email'] ?? '');
            
            if(empty($email)) {
                $data['error'] = 'Vui lòng nhập email của bạn';
            } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data['error'] = 'Email không hợp lệ';
            } else {
                $result = $this->userModel->resetPassword($email);
                
                if(is_object($result) && isset($result->success)) {
                    // Thành công, gửi email đặt lại mật khẩu
                    $token = $result->token;
                    $user = $result->user;
                    $resetUrl = BASE_URL . 'auth/resetPassword?token=' . $token;
                    
                    // Gửi email
                    if($this->sendPasswordResetEmail($user->email, $user->fullname, $resetUrl)) {
                        $data['success'] = 'Một email đã được gửi đến địa chỉ email của bạn với hướng dẫn đặt lại mật khẩu.';
                    } else {
                        $data['error'] = 'Không thể gửi email. Vui lòng thử lại sau.';
                    }
                } else {
                    $data['error'] = $result;
                }
            }
        }
        
        $this->view('frontend/auth/forgot_password', $data);
    }
    
    // Đặt lại mật khẩu
    public function resetPassword() {
        // Kiểm tra nếu người dùng đã đăng nhập
        if(isset($_SESSION['user_id'])) {
            $this->redirect('');
            return;
        }
        
        $data = [
            'title' => 'Đặt lại mật khẩu - IVY moda',
            'error' => '',
            'success' => '',
            'token' => '',
            'validToken' => false
        ];
        
        // Kiểm tra token trong URL
        if(isset($_GET['token'])) {
            $token = $_GET['token'];
            $data['token'] = $token;
            
            // Kiểm tra token có hợp lệ không
            $user = $this->userModel->validateResetToken($token);
            
            if($user) {
                $data['validToken'] = true;
            } else {
                $data['error'] = 'Token không hợp lệ hoặc đã hết hạn';
            }
        }
        
        // Xử lý đặt lại mật khẩu
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $token = $_POST['token'] ?? '';
            $password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            if(empty($password) || empty($confirm_password)) {
                $data['error'] = 'Vui lòng nhập đầy đủ thông tin';
                $data['validToken'] = true;
                $data['token'] = $token;
            } elseif($password !== $confirm_password) {
                $data['error'] = 'Mật khẩu xác nhận không khớp';
                $data['validToken'] = true;
                $data['token'] = $token;
            } elseif(strlen($password) < 6) {
                $data['error'] = 'Mật khẩu phải có ít nhất 6 ký tự';
                $data['validToken'] = true;
                $data['token'] = $token;
            } else {
                $result = $this->userModel->resetPasswordWithToken($token, $password);
                
                file_put_contents(ROOT_PATH . 'logs/password_reset.log', 
                    date('Y-m-d H:i:s') . " - Token: $token, Result: " . (is_string($result) ? $result : json_encode($result)) . "\n",
                    FILE_APPEND);
                
                if($result === 'success') {
                    $data['success'] = 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập.';
                } else {
                    $data['error'] = $result;
                    $data['validToken'] = true;
                    $data['token'] = $token;
                }
            }
        }
        
        $this->view('frontend/auth/reset_password', $data);
    }
    
    // Gửi email xác nhận đăng ký
    private function sendRegistrationConfirmationEmail($email, $name, $username) {
        try {
            // Sử dụng EmailHelper để gửi email
            require_once ROOT_PATH . 'app/helpers/EmailHelper.php';
            $emailHelper = new EmailHelper();
            
            // Tạo token kích hoạt và lưu vào database
            $activationToken = $this->userModel->createActivationToken($email);
            
            if($activationToken) {
                // Gửi email xác nhận đăng ký
                return $emailHelper->sendRegistrationConfirmation($email, $username, $activationToken);
            } else {
                error_log("Cannot create activation token for email: $email");
                return false;
            }
        } catch (Exception $e) {
            error_log("Registration email error: " . $e->getMessage());
            return false;
        }
    }
    
    // Gửi email đặt lại mật khẩu
    private function sendPasswordResetEmail($email, $name, $resetUrl) {
        try {
            // Sử dụng EmailHelper để gửi email
            require_once ROOT_PATH . 'app/helpers/EmailHelper.php';
            $emailHelper = new EmailHelper();
            
            // Tạo token từ URL
            $token = basename(parse_url($resetUrl, PHP_URL_QUERY));
            $token = str_replace('token=', '', $token);
            
            // Gửi email đặt lại mật khẩu
            return $emailHelper->sendPasswordReset($email, $name, $token);
        } catch (Exception $e) {
            error_log("Password reset email error: " . $e->getMessage());
            return false;
        }
    }
    
    // Method mặc định - chuyển hướng đến login
    public function index() {
        $this->redirect('auth/login');
    }
    
    // Kích hoạt tài khoản qua email
    public function activate() {
        $data = [
            'title' => 'Kích hoạt tài khoản - IVY moda',
            'success' => '',
            'error' => ''
        ];
        
        if(isset($_GET['token'])) {
            $token = $_GET['token'];
            
            // Kiểm tra token và kích hoạt tài khoản
            $result = $this->userModel->activateAccount($token);
            
            if($result === true) {
                $data['success'] = 'Tài khoản đã được kích hoạt thành công! Bạn có thể đăng nhập ngay bây giờ.';
            } else {
                $data['error'] = $result;
            }
        } else {
            $data['error'] = 'Token kích hoạt không hợp lệ';
        }
        
        $this->view('frontend/auth/activate', $data);
    }
}