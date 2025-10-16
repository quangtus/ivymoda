<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\models\UserModel.php

class UserModel extends Model {
    // Thay đổi từ private sang protected để phù hợp với lớp cha Model
    protected $table = 'users';
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Lấy tất cả người dùng kèm theo tên vai trò
     */
    public function getAllUsers() {
        $query = "SELECT u.*, r.role_name FROM {$this->table} u 
                 LEFT JOIN roles r ON u.role_id = r.id 
                 ORDER BY u.id";
        return $this->getAll($query);
    }
    
    /**
     * Lấy thông tin người dùng theo ID kèm theo tên vai trò
     */
    public function getUserById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = $id";
        return $this->getOne($query);
    }
    
    /**
     * Lấy thông tin người dùng theo username
     */
    public function getUserByUsername($username) {
        $username = $this->escape($username);
        $query = "SELECT * FROM {$this->table} WHERE username = '$username'";
        return $this->getOne($query);
    }
    
    /**
     * Tìm người dùng theo username
     */
    public function findUserByUsername($username) {
        $username = $this->escape($username);
        $query = "SELECT * FROM {$this->table} WHERE username = '$username'";
        return $this->getOne($query);
    }
    
    /**
     * Tìm người dùng theo email
     */
    public function findUserByEmail($email) {
        $email = $this->escape($email);
        $query = "SELECT * FROM {$this->table} WHERE email = '$email'";
        return $this->getOne($query);
    }
    
    /**
     * Thêm người dùng mới
     */
    public function addUser($data) {
        // Sử dụng phương thức create từ Model cha
        return $this->create($data);
    }
    
    /**
     * Cập nhật thông tin người dùng
     */
    public function updateUser($id, $fullname, $email, $phone, $address, $role_id, $status) {
        $fullname = $this->escape($fullname);
        $email = $this->escape($email);
        $phone = $this->escape($phone);
        $address = $this->escape($address);
        
        // Kiểm tra email đã tồn tại chưa (trừ email của chính user này)
        $check_email = "SELECT * FROM {$this->table} WHERE email = '$email' AND id != $id";
        if($this->getOne($check_email)) {
            return "Email đã tồn tại";
        }
        
        $query = "UPDATE {$this->table} SET 
                fullname = '$fullname', 
                email = '$email', 
                phone = '$phone', 
                address = '$address',
                role_id = $role_id,
                status = $status
                WHERE id = $id";
        
        if($this->execute($query)) {
            return "success";
        } else {
            return "Cập nhật thông tin thất bại";
        }
    }
    
    /**
     * Xóa người dùng
     */
    public function deleteUser($user_id) {
        $query = "DELETE FROM {$this->table} WHERE id = $user_id";
        
        if($this->execute($query)) {
            return "success";
        } else {
            return "Xóa người dùng thất bại";
        }
    }
    
    /**
     * Lấy tất cả các vai trò
     */
    public function getAllRoles() {
        $query = "SELECT * FROM roles ORDER BY id";
        $result = $this->getAll($query);
        return $result ? $result : [];
    }
    
    /**
     * Lấy tất cả các vai trò kèm theo số lượng người dùng
     */
    public function getAllRolesWithUserCount() {
        $query = "SELECT r.*, COUNT(u.id) as user_count 
                  FROM roles r 
                  LEFT JOIN {$this->table} u ON r.id = u.role_id 
                  GROUP BY r.id 
                  ORDER BY r.id";
        $result = $this->getAll($query);
        return $result ? $result : [];
    }
    
    /**
     * Lấy vai trò theo ID
     */
    public function getRoleById($id) {
        $query = "SELECT * FROM roles WHERE id = $id";
        return $this->getOne($query);
    }
    
    /**
     * Cập nhật vai trò
     */
    public function updateRole($id, $role_name, $description) {
        $role_name = $this->escape($role_name);
        $description = $this->escape($description);
        
        // Kiểm tra xem vai trò đã tồn tại chưa (trừ chính nó)
        $check_query = "SELECT COUNT(*) as count FROM roles WHERE role_name = '$role_name' AND id != $id";
        $result = $this->getOne($check_query);
        
        $count = 0;
        if ($result) {
            if (is_object($result) && isset($result->count)) {
                $count = $result->count;
            } elseif (is_array($result) && isset($result['count'])) {
                $count = $result['count'];
            }
        }
        
        if($count > 0) {
            return "Vai trò này đã tồn tại";
        }
        
        $query = "UPDATE roles SET role_name = '$role_name', description = '$description' WHERE id = $id";
        
        if($this->execute($query)) {
            return "success";
        } else {
            return "Cập nhật vai trò thất bại";
        }
    }
    
    /**
     * Thêm vai trò mới
     */
    public function addRole($role_name, $description) {
        $role_name = $this->escape($role_name);
        $description = $this->escape($description);
        
        // Kiểm tra xem vai trò đã tồn tại chưa
        $check_query = "SELECT COUNT(*) as count FROM roles WHERE role_name = '$role_name'";
        $result = $this->getOne($check_query);
        
        $count = 0;
        if ($result) {
            if (is_object($result) && isset($result->count)) {
                $count = $result->count;
            } elseif (is_array($result) && isset($result['count'])) {
                $count = $result['count'];
            }
        }
        
        if($count > 0) {
            return "Vai trò này đã tồn tại";
        }
        
        $query = "INSERT INTO roles (role_name, description) VALUES ('$role_name', '$description')";
        
        if($this->execute($query)) {
            return "success";
        } else {
            return "Thêm vai trò thất bại";
        }
    }
    
    /**
     * Kiểm tra xem vai trò có đang được sử dụng bởi người dùng nào không
     */
    public function isRoleInUse($role_id) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE role_id = $role_id";
        $result = $this->getOne($query);
        
        // Sử dụng kiểm tra an toàn cho thuộc tính
        if ($result) {
            if (is_object($result) && isset($result->count)) {
                return $result->count > 0;
            } elseif (is_array($result) && isset($result['count'])) {
                return $result['count'] > 0;
            }
        }
        
        return false;
    }
    
    /**
     * Xóa vai trò
     */
    public function deleteRole($role_id) {
        // Kiểm tra xem có người dùng nào đang dùng vai trò này không
        $check_query = "SELECT COUNT(*) as count FROM {$this->table} WHERE role_id = $role_id";
        $result = $this->getOne($check_query);
        
        // Kiểm tra an toàn cho cả object và array
        $count = 0;
        if ($result) {
            if (is_object($result) && isset($result->count)) {
                $count = $result->count;
            } elseif (is_array($result) && isset($result['count'])) {
                $count = $result['count'];
            }
        }
        
        if($count > 0) {
            return "Không thể xóa vai trò đang được sử dụng bởi người dùng";
        }
        
        $query = "DELETE FROM roles WHERE id = $role_id";
        
        if($this->execute($query)) {
            return "success";
        } else {
            return "Xóa vai trò thất bại";
        }
    }
    
    /**
     * Đăng nhập
     */
    public function login($username, $password) {
        $username = $this->escape($username);
        $user = $this->getUserByUsername($username);
        
        if($user) {
            // Kiểm tra an toàn cho thuộc tính status
            $status = is_object($user) ? $user->status : (is_array($user) ? $user['status'] : null);
            $user_password = is_object($user) ? $user->password : (is_array($user) ? $user['password'] : null);
            $user_id = is_object($user) ? $user->id : (is_array($user) ? $user['id'] : null);
            
            // Kiểm tra tài khoản bị khóa
            if($status == 2) {
                return "Tài khoản đã bị khóa";
            } elseif($status == 0) {
                return "Tài khoản đã bị vô hiệu hóa";
            }
            
            // Kiểm tra mật khẩu
            if(password_verify($password, $user_password)) {
                // Reset số lần đăng nhập sai
                $this->resetLoginAttempts($user_id);
                return $user;
            } else {
                // Tăng số lần đăng nhập sai
                $this->incrementLoginAttempts($username);
                return "Tên đăng nhập hoặc mật khẩu không đúng";
            }
        }
        
        return "Tên đăng nhập hoặc mật khẩu không đúng";
    }
    
    /**
     * Tăng số lần đăng nhập sai
     */
    private function incrementLoginAttempts($username) {
        $username = $this->escape($username);
        
        // Tăng số lần đăng nhập sai
        $query = "UPDATE {$this->table} SET login_attempts = login_attempts + 1 WHERE username = '$username'";
        $this->execute($query);
        
        // Kiểm tra nếu quá MAX_LOGIN_ATTEMPTS lần thì khóa tài khoản
        $check_query = "SELECT login_attempts FROM {$this->table} WHERE username = '$username'";
        $user = $this->getOne($check_query);
        
        // Kiểm tra an toàn cho thuộc tính login_attempts
        $login_attempts = 0;
        if ($user) {
            if (is_object($user) && isset($user->login_attempts)) {
                $login_attempts = $user->login_attempts;
            } elseif (is_array($user) && isset($user['login_attempts'])) {
                $login_attempts = $user['login_attempts'];
            }
        }
        
        if($login_attempts >= MAX_LOGIN_ATTEMPTS) {
            // Khóa tài khoản (status = 2)
            $lock_query = "UPDATE {$this->table} SET status = 2 WHERE username = '$username'";
            $this->execute($lock_query);
            
            // Ghi log
            $this->logUserAction($username, 'account_locked', 'Tài khoản bị khóa do đăng nhập sai nhiều lần');
        }
    }
    
    /**
     * Reset số lần đăng nhập sai
     */
    private function resetLoginAttempts($user_id) {
        $query = "UPDATE {$this->table} SET login_attempts = 0 WHERE id = $user_id";
        $this->execute($query);
    }
    
    /**
     * Ghi log hành động người dùng
     */
    private function logUserAction($username, $action, $description = '') {
        $username = $this->escape($username);
        $action = $this->escape($action);
        $description = $this->escape($description);
        $ip = $_SERVER['REMOTE_ADDR'];
        
        $query = "INSERT INTO user_logs (username, action, description, ip_address, created_at) 
                  VALUES ('$username', '$action', '$description', '$ip', NOW())";
        $this->execute($query);
    }
    
    /**
     * Đăng ký người dùng mới
     */
    public function register($username, $password, $email, $fullname, $phone = '', $address = '') {
        $username = $this->escape($username);
        $email = $this->escape($email);
        $fullname = $this->escape($fullname);
        $phone = $this->escape($phone);
        $address = $this->escape($address);
        
        // Kiểm tra username đã tồn tại chưa
        $check_user = "SELECT * FROM {$this->table} WHERE username = '$username'";
        if($this->getOne($check_user)) {
            return "Username đã tồn tại";
        }
        
        // Kiểm tra email đã tồn tại chưa
        $check_email = "SELECT * FROM {$this->table} WHERE email = '$email'";
        if($this->getOne($check_email)) {
            return "Email đã tồn tại";
        }
        
        // Mã hóa mật khẩu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Thêm người dùng mới
        $query = "INSERT INTO {$this->table} (username, password, email, fullname, phone, address, role_id, status, login_attempts) 
                VALUES ('$username', '$hashed_password', '$email', '$fullname', '$phone', '$address', 2, 1, 0)";
                
        if($this->execute($query)) {
            return true;
        } else {
            return "Đăng ký thất bại";
        }
    }
    
    /**
     * Cập nhật thông tin hồ sơ người dùng
     */
    public function updateProfile($user_id, $fullname, $email, $phone, $address) {
        $fullname = $this->escape($fullname);
        $email = $this->escape($email);
        $phone = $this->escape($phone);
        $address = $this->escape($address);
        
        // Kiểm tra email đã tồn tại chưa (trừ email của chính user này)
        $check_email = "SELECT * FROM {$this->table} WHERE email = '$email' AND id != $user_id";
        if($this->getOne($check_email)) {
            return "Email đã tồn tại";
        }
        
        $query = "UPDATE {$this->table} SET 
                fullname = '$fullname', 
                email = '$email', 
                phone = '$phone', 
                address = '$address' 
                WHERE id = $user_id";
        
        if($this->execute($query)) {
            return "success";
        } else {
            return "Cập nhật thông tin thất bại";
        }
    }
    
    /**
     * Đổi mật khẩu
     */
    public function changePassword($user_id, $current_password, $new_password) {
        // Lấy mật khẩu hiện tại
        $query = "SELECT password FROM {$this->table} WHERE id = $user_id";
        $user = $this->getOne($query);
        
        if($user) {
            // Kiểm tra an toàn cho thuộc tính password
            $password = is_object($user) ? $user->password : (is_array($user) ? $user['password'] : null);
            
            // Kiểm tra mật khẩu hiện tại
            if(password_verify($current_password, $password)) {
                // Mã hóa mật khẩu mới
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Cập nhật mật khẩu
                $update_query = "UPDATE {$this->table} SET password = '$hashed_password' WHERE id = $user_id";
                
                if($this->execute($update_query)) {
                    return "success";
                } else {
                    return "Đổi mật khẩu thất bại";
                }
            } else {
                return "Mật khẩu hiện tại không đúng";
            }
        } else {
            return "Người dùng không tồn tại";
        }
    }
    
    /**
     * Yêu cầu đặt lại mật khẩu
     */
    public function resetPassword($email) {
        $email = $this->escape($email);
        
        // Kiểm tra email tồn tại
        $query = "SELECT * FROM {$this->table} WHERE email = '$email'";
        $user = $this->getOne($query);
        
        if(!$user) {
            return "Email không tồn tại trong hệ thống";
        }
        
        // Tạo token reset password
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 86400); // Token hết hạn sau 24 giờ
        
        // Lấy ID an toàn
        $user_id = is_object($user) ? $user->id : (is_array($user) ? $user['id'] : null);
        
        // Lưu token vào database
        $update_query = "UPDATE {$this->table} SET reset_token = '$token', reset_token_expire = '$expires' WHERE id = $user_id";
        
        if($this->execute($update_query)) {
            $result = new \stdClass();
            $result->success = true;
            $result->token = $token;
            $result->user = $user;
            return $result;
        } else {
            return "Có lỗi xảy ra khi tạo yêu cầu đặt lại mật khẩu";
        }
    }
    
    /**
     * Xác thực token đặt lại mật khẩu
     */
    public function validateResetToken($token) {
        $token = $this->escape($token);
        
        $query = "SELECT * FROM {$this->table} WHERE reset_token = '$token' AND reset_token_expire > NOW()";
        return $this->getOne($query);
    }
    
    /**
     * Đặt lại mật khẩu bằng token
     */
    public function resetPasswordWithToken($token, $new_password) {
        $token = $this->escape($token);
        
        // Kiểm tra token hợp lệ
        $user = $this->validateResetToken($token);
        
        if(!$user) {
            return "Token không hợp lệ hoặc đã hết hạn";
        }
        
        // Mã hóa mật khẩu mới
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Lấy ID an toàn
        $user_id = is_object($user) ? $user->id : (is_array($user) ? $user['id'] : null);
        
        if(!$user_id) {
            return "Có lỗi xảy ra khi xác định người dùng";
        }
        
        // Cập nhật mật khẩu và xóa token
        $query = "UPDATE {$this->table} SET 
            password = '$hashed_password',
            reset_token = NULL,
            reset_token_expire = NULL,
            login_attempts = 0,
            status = 1
            WHERE id = $user_id";
    
        if($this->execute($query)) {
            return "success";
        } else {
            return "Đặt lại mật khẩu thất bại";
        }
    }
    
    /**
     * Cập nhật vai trò người dùng
     */
    public function updateUserRole($user_id, $role_id) {
        $query = "UPDATE {$this->table} SET role_id = $role_id WHERE id = $user_id";
        
        if($this->execute($query)) {
            return "success";
        } else {
            return "Cập nhật vai trò thất bại";
        }
    }
    
    /**
     * Cập nhật trạng thái người dùng
     */
    public function updateUserStatus($user_id, $status) {
        $query = "UPDATE {$this->table} SET status = $status";
        
        // Nếu đang mở khóa tài khoản, reset số lần đăng nhập sai
        if($status == 1) {
            $query .= ", login_attempts = 0";
        }
        
        $query .= " WHERE id = $user_id";
        
        if($this->execute($query)) {
            return "success";
        } else {
            return "Cập nhật trạng thái thất bại";
        }
    }
    
    /**
     * Admin reset password cho user
     */
    public function adminResetPassword($user_id, $new_password) {
        // Mã hóa mật khẩu mới
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $query = "UPDATE {$this->table} SET 
                password = '$hashed_password',
                login_attempts = 0,
                status = 1
                WHERE id = $user_id";
        
        if($this->execute($query)) {
            return "success";
        } else {
            return "Đặt lại mật khẩu thất bại";
        }
    }
}