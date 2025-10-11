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
                    $_SESSION['user_id'] = $result->id;
                    $_SESSION['username'] = $result->username;
                    $_SESSION['role_id'] = $result->role_id;
                    
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
                    $data['success'] = 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.';
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
    
    // Gửi email đặt lại mật khẩu
    private function sendPasswordResetEmail($email, $name, $resetUrl) {
        // Nội dung email với HTML formatting
        $subject = "Đặt lại mật khẩu - IVY moda";
        
        // Tạo URL đầy đủ với localhost hiển thị rõ
        $fullResetUrl = "http://localhost" . $resetUrl;
        
        // HTML email template
        $message = "
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333333;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    border: 1px solid #dddddd;
                    border-radius: 5px;
                }
                .header {
                    text-align: center;
                    padding: 20px 0;
                    border-bottom: 1px solid #eeeeee;
                }
                .header img {
                    max-height: 80px;
                }
                .content {
                    padding: 20px 0;
                }
                .button {
                    display: inline-block;
                    background-color: #221f20;
                    color: #ffffff !important;
                    text-decoration: none;
                    padding: 12px 30px;
                    margin: 20px 0;
                    border-radius: 4px;
                    font-weight: bold;
                }
                .footer {
                    text-align: center;
                    padding-top: 20px;
                    font-size: 12px;
                    color: #777777;
                    border-top: 1px solid #eeeeee;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>IVY moda</h2>
                </div>
                <div class='content'>
                    <p>Xin chào <strong>{$name}</strong>,</p>
                    <p>Bạn hoặc ai đó đã yêu cầu đặt lại mật khẩu cho tài khoản của bạn tại IVY moda.</p>
                    <p>Vui lòng nhấp vào nút bên dưới để đặt lại mật khẩu:</p>
                    <p style='text-align: center;'>
                        <a href='{$fullResetUrl}' class='button'>Đặt lại mật khẩu</a>
                    </p>
                    <p>Hoặc copy đường dẫn sau và dán vào trình duyệt của bạn:</p>
                    <p><a href='{$fullResetUrl}'>{$fullResetUrl}</a></p>
                    <p>Liên kết này sẽ hết hạn sau 24 giờ.</p>
                    <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
                </div>
                <div class='footer'>
                    <p>Trân trọng,<br>IVY moda</p>
                    <p>&copy; " . date('Y') . " IVY moda. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
        
        // Dùng PHPMailer
        require_once ROOT_PATH . 'vendor/PHPMailer/src/Exception.php';
        require_once ROOT_PATH . 'vendor/PHPMailer/src/PHPMailer.php';
        require_once ROOT_PATH . 'vendor/PHPMailer/src/SMTP.php';
        
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            // Cấu hình SMTP
            $mail->isSMTP();
            $mail->Host = EMAIL_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = EMAIL_USERNAME;
            $mail->Password = EMAIL_PASSWORD;
            $mail->SMTPSecure = 'tls';
            $mail->Port = EMAIL_PORT;
            $mail->CharSet = 'UTF-8';
            
            // Cấu hình email
            $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
            $mail->addAddress($email, $name);
            $mail->Subject = $subject;
            
            // Set email format to HTML
            $mail->isHTML(true);
            $mail->Body = $message;
            $mail->AltBody = strip_tags(str_replace("<br>", "\n", $message)); // Plain text alternative
            
            // Gửi email
            $mail->send();
            return true;
        } catch (\Exception $e) {
            error_log("Email sending failed: " . $mail->ErrorInfo);
            return false;
        }
    }
}