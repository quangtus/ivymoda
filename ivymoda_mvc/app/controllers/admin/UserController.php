<?php
namespace admin;

class UserController extends \Controller {
    private $userModel;
    
    public function __construct() {
        $this->userModel = $this->model('UserModel');
        
        // Kiểm tra đăng nhập và quyền admin
        if(!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            exit;
        }
        
        if($_SESSION['role_id'] != 1) {
            $this->redirect('error/forbidden');
            exit;
        }
    }
    
    /**
     * Hiển thị danh sách người dùng
     */
    public function index() {
        $users = $this->userModel->getAllUsers();
        
        $data = [
            'title' => 'Quản lý người dùng - IVY moda',
            'users' => $users
        ];
        
        $this->view('admin/user/index', $data);
    }
    
    /**
     * Thêm người dùng mới
     */
    public function add() {
        $roles = $this->userModel->getAllRoles();
        
        // Khởi tạo các biến trống để tránh lỗi undefined
        $data = [
            'title' => 'Thêm người dùng - IVY moda',
            'roles' => $roles,
            'username' => '',
            'email' => '',
            'fullname' => '',
            'phone' => '',
            'address' => '',
            'error' => '',
            'success' => ''
        ];
        
        // Xử lý thêm người dùng
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data['username'] = trim($_POST['username']);
            $password = $_POST['password'];
            $data['email'] = trim($_POST['email']);
            $data['fullname'] = trim($_POST['fullname']);
            $data['phone'] = trim($_POST['phone']);
            $data['address'] = trim($_POST['address']);
            $role_id = $_POST['role_id'];
            
            if(empty($data['username']) || empty($password) || empty($data['email']) || empty($data['fullname'])) {
                $data['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc';
            } elseif(strlen($password) < 6) {
                $data['error'] = 'Mật khẩu phải có ít nhất 6 ký tự';
            } else {
                $result = $this->userModel->register($data['username'], $password, $data['email'], $data['fullname'], $data['phone'], $data['address']);
                
                if($result === true) {
                    // Cập nhật vai trò nếu không phải khách hàng (role_id = 2)
                    if($role_id != 2) {
                        $user = $this->userModel->getUserByUsername($data['username']);
                        if($user) {
                            $this->userModel->updateUserRole($user->id, $role_id);
                        }
                    }
                    $data['success'] = 'Thêm người dùng thành công!';
                    // Reset form
                    $data['username'] = '';
                    $data['email'] = '';
                    $data['fullname'] = '';
                    $data['phone'] = '';
                    $data['address'] = '';
                } else {
                    $data['error'] = $result;
                }
            }
        }
        
        $this->view('admin/user/add', $data);
    }
    
    /**
     * Sửa thông tin người dùng
     * @param int $id ID của người dùng
     */
    public function edit($id = null) {
        if(!$id) {
            $_SESSION['error'] = 'Không tìm thấy người dùng';
            $this->redirect('admin/user');
            exit;
        }
        
        $user = $this->userModel->getUserById($id);
        $roles = $this->userModel->getAllRoles();
        
        if(!$user) {
            $_SESSION['error'] = 'Không tìm thấy người dùng';
            $this->redirect('admin/user');
            exit;
        }
        
        $data = [
            'title' => 'Cập nhật thông tin người dùng',
            'user' => $user,
            'roles' => $roles,
            'username' => $user->username,  // Thêm các biến này để tránh undefined
            'fullname' => $user->fullname,
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'address' => $user->address ?? '',
            'error' => '',
            'success' => ''
        ];
        
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
            $data['fullname'] = trim($_POST['fullname']);
            $data['email'] = trim($_POST['email']);
            $data['phone'] = trim($_POST['phone']);
            $data['address'] = trim($_POST['address']);
            $role_id = $_POST['role_id'] ?? $user->role_id;
            $status = $_POST['status'] ?? $user->status;
            
            // Kiểm tra thông tin
            if(empty($data['fullname']) || empty($data['email'])) {
                $data['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc';
            } else {
                $result = $this->userModel->updateUser($id, $data['fullname'], $data['email'], $data['phone'], $data['address'], $role_id, $status);
                
                if($result == "success") {
                    $data['success'] = 'Cập nhật thông tin thành công!';
                    // Cập nhật lại thông tin user
                    $user = $this->userModel->getUserById($id);
                    $data['user'] = $user;
                } else {
                    $data['error'] = $result;
                }
            }
        }
        
        // Xử lý reset password
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
            $new_password = trim($_POST['new_password']);
            
            if(empty($new_password)) {
                $data['error'] = 'Vui lòng nhập mật khẩu mới';
            } elseif(strlen($new_password) < 6) {
                $data['error'] = 'Mật khẩu phải có ít nhất 6 ký tự';
            } else {
                $result = $this->userModel->adminResetPassword($id, $new_password);
                
                if($result == "success") {
                    $data['success'] = 'Đặt lại mật khẩu thành công!';
                } else {
                    $data['error'] = $result;
                }
            }
        }
        
        $this->view('admin/user/edit', $data);
    }
    
    /**
     * Xóa người dùng
     * @param int $id ID của người dùng
     */
    public function delete($id) {
        // Không cho phép xóa tài khoản admin đang đăng nhập
        if($id == $_SESSION['user_id']) {
            $_SESSION['error'] = 'Không thể xóa tài khoản đang đăng nhập';
            $this->redirect('admin/user');
            exit;
        }
        
        $result = $this->userModel->deleteUser($id);
        
        if($result == "success") {
            $_SESSION['success'] = 'Xóa người dùng thành công';
        } else {
            $_SESSION['error'] = $result;
        }
        
        $this->redirect('admin/user');
    }
    
    /**
     * Quản lý vai trò người dùng
     */
    public function roles() {
        $roles = $this->userModel->getAllRolesWithUserCount();
        
        $data = [
            'title' => 'Quản lý vai trò - IVY moda',
            'roles' => $roles,
            'error' => '',
            'success' => '',
            'role_name' => '', // Thêm biến này để tránh lỗi undefined
            'description' => '' // Thêm biến này để tránh lỗi undefined
        ];
        
        // Xử lý thêm vai trò
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_role'])) {
            $role_name = trim($_POST['role_name']);
            $description = trim($_POST['description']);
            
            // Cập nhật giá trị vào data để giữ lại khi form bị lỗi
            $data['role_name'] = $role_name;
            $data['description'] = $description;
            
            if(empty($role_name)) {
                $data['error'] = 'Vui lòng nhập tên vai trò';
            } else {
                $result = $this->userModel->addRole($role_name, $description);
                
                if($result == "success") {
                    $data['success'] = 'Thêm vai trò thành công!';
                    // Cập nhật lại danh sách vai trò
                    $data['roles'] = $this->userModel->getAllRolesWithUserCount();
                    // Reset form
                    $data['role_name'] = '';
                    $data['description'] = '';
                } else {
                    $data['error'] = $result;
                }
            }
        }
        
        $this->view('admin/user/roles', $data);
    }
    
    /**
     * Chỉnh sửa vai trò
     * @param int $id ID của vai trò
     */
    public function editRole($id = null) {
        if(!$id) {
            $_SESSION['error'] = 'Không tìm thấy vai trò';
            $this->redirect('admin/user/roles');
            exit;
        }
        
        $role = $this->userModel->getRoleById($id);
        
        if(!$role) {
            $_SESSION['error'] = 'Không tìm thấy vai trò';
            $this->redirect('admin/user/roles');
            exit;
        }
        
        $data = [
            'title' => 'Chỉnh sửa vai trò - IVY moda',
            'role' => $role,
            'role_name' => $role->role_name,
            'description' => $role->description,
            'error' => '',
            'success' => ''
        ];
        
        // Xử lý cập nhật vai trò
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_role'])) {
            $role_name = trim($_POST['role_name']);
            $description = trim($_POST['description']);
            
            // Cập nhật giá trị vào data để giữ lại khi form bị lỗi
            $data['role_name'] = $role_name;
            $data['description'] = $description;
            
            if(empty($role_name)) {
                $data['error'] = 'Vui lòng nhập tên vai trò';
            } else {
                $result = $this->userModel->updateRole($id, $role_name, $description);
                
                if($result == "success") {
                    $_SESSION['success'] = 'Cập nhật vai trò thành công!';
                    $this->redirect('admin/user/roles');
                    exit;
                } else {
                    $data['error'] = $result;
                }
            }
        }
        
        $this->view('admin/user/edit_role', $data);
    }
    
    /**
     * Xóa vai trò
     * @param int $id ID của vai trò
     */
    public function deleteRole($id) {
        // Không cho phép xóa vai trò mặc định (1-admin, 2-customer, 3-staff)
        if($id <= 3) {
            $_SESSION['error'] = 'Không thể xóa vai trò mặc định của hệ thống';
        } else {
            $result = $this->userModel->deleteRole($id);
            
            if($result == "success") {
                $_SESSION['success'] = 'Xóa vai trò thành công';
            } else {
                $_SESSION['error'] = $result;
            }
        }
        
        $this->redirect('admin/user/roles');
    }
}