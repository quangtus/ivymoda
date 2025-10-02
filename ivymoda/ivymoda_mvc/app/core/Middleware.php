<?php
class Middleware {
    /**
     * Kiểm tra người dùng đã đăng nhập chưa
     */
    public static function requireLogin() {
        if(!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit();
        }
    }
    
    /**
     * Kiểm tra người dùng có quyền admin không
     */
    public static function requireAdmin() {
        self::requireLogin();
        
        if($_SESSION['role_id'] != 1) {
            header('Location: ' . URLROOT . '/auth/login?error=access_denied');
            exit();
        }
    }
    
    /**
     * Kiểm tra người dùng có quyền nhân viên không
     */
    public static function requireStaff() {
        self::requireLogin();
        
        if($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 3) {
            header('Location: ' . URLROOT . '/auth/login?error=access_denied');
            exit();
        }
    }
    
    /**
     * Chuyển hướng người dùng đã đăng nhập (nếu truy cập vào trang đăng nhập/đăng ký)
     */
    public static function redirectLoggedIn() {
        if(isset($_SESSION['user_id'])) {
            if($_SESSION['role_id'] == 1) {
                header('Location: ' . URLROOT . '/admin/dashboard');
            } else {
                header('Location: ' . URLROOT . '/');
            }
            exit();
        }
    }
}