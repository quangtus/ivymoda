<?php
class SessionHelper {
    /**
     * Khởi tạo session
     */
    public static function init() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Thiết lập giá trị session
     */
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Lấy giá trị session
     */
    public static function get($key) {
        if(isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return false;
    }
    
    /**
     * Kiểm tra session tồn tại
     */
    public static function exists($key) {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Xóa session
     */
    public static function unset($key) {
        if(isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    /**
     * Hủy tất cả session
     */
    public static function destroy() {
        session_destroy();
    }
    
    /**
     * Kiểm tra người dùng đã đăng nhập chưa
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Kiểm tra người dùng có quyền admin không
     */
    public static function isAdmin() {
        return isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1;
    }
    
    /**
     * Kiểm tra người dùng có quyền nhân viên không
     */
    public static function isStaff() {
        return isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 3);
    }
    
    /**
     * Yêu cầu đăng nhập để truy cập
     */
    public static function requireLogin() {
        if(!self::isLoggedIn()) {
            header('Location: ' . URLROOT . '/auth/login');
            exit();
        }
    }
    
    /**
     * Yêu cầu quyền admin để truy cập
     */
    public static function requireAdmin() {
        self::requireLogin();
        
        if(!self::isAdmin()) {
            header('Location: ' . URLROOT . '/auth/login?error=access_denied');
            exit();
        }
    }
    
    /**
     * Yêu cầu quyền nhân viên để truy cập
     */
    public static function requireStaff() {
        self::requireLogin();
        
        if(!self::isStaff()) {
            header('Location: ' . URLROOT . '/auth/login?error=access_denied');
            exit();
        }
    }
    
    /**
     * Kiểm tra có quyền quản lý tài khoản không (chỉ admin)
     */
    public static function canManageUsers() {
        return self::isAdmin();
    }
    
    /**
     * Kiểm tra có quyền truy cập admin area không (admin + nhân viên)
     */
    public static function canAccessAdmin() {
        return self::isStaff();
    }
    
    /**
     * Tạo flash message
     */
    public static function setFlash($name, $message, $class = 'alert-success') {
        $_SESSION[$name] = [
            'message' => $message,
            'class' => $class
        ];
    }
    
    /**
     * Hiển thị flash message
     */
    public static function flash($name) {
        if(isset($_SESSION[$name])) {
            $flash = $_SESSION[$name];
            unset($_SESSION[$name]);
            return '<div class="alert ' . $flash['class'] . ' alert-dismissible fade show" role="alert">
                ' . $flash['message'] . '
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>';
        }
        return '';
    }
}