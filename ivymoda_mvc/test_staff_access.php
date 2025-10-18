<?php
/**
 * Script test quyền truy cập của nhân viên vào hệ thống quản trị
 * Chạy script này để kiểm tra xem nhân viên có thể đăng nhập admin không
 */

// Khởi tạo session
session_start();

// Include các file cần thiết
require_once 'app/config/config.php';
require_once 'app/core/Database.php';
require_once 'app/models/UserModel.php';

echo "<h2>Test Quyền Truy Cập Nhân Viên - IVY moda</h2>";

try {
    // Kết nối database
    $db = new Database();
    $userModel = new UserModel();
    
    // Lấy thông tin nhân viên từ database
    $staff = $userModel->getUserByUsername('staff1');
    
    if ($staff) {
        echo "<h3>Thông tin nhân viên:</h3>";
        echo "<p><strong>Username:</strong> " . $staff->username . "</p>";
        echo "<p><strong>Email:</strong> " . $staff->email . "</p>";
        echo "<p><strong>Fullname:</strong> " . $staff->fullname . "</p>";
        echo "<p><strong>Role ID:</strong> " . $staff->role_id . "</p>";
        echo "<p><strong>Status:</strong> " . ($staff->status ? 'Active' : 'Inactive') . "</p>";
        
        // Test đăng nhập
        echo "<h3>Test đăng nhập:</h3>";
        $result = $userModel->login('staff1', 'admin123');
        
        if (is_object($result)) {
            echo "<p style='color: green;'>✅ Đăng nhập thành công!</p>";
            echo "<p><strong>Role ID:</strong> " . $result->role_id . "</p>";
            
            // Test quyền truy cập
            if ($result->role_id == 3) {
                echo "<p style='color: green;'>✅ Nhân viên có quyền truy cập admin (role_id = 3)</p>";
            } else {
                echo "<p style='color: red;'>❌ Nhân viên không có quyền truy cập admin</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Đăng nhập thất bại: " . $result . "</p>";
        }
        
        // Test các role khác
        echo "<h3>Test các role khác:</h3>";
        $admin = $userModel->getUserByUsername('admin');
        $customer = $userModel->getUserByUsername('customer1');
        
        if ($admin) {
            echo "<p><strong>Admin:</strong> role_id = " . $admin->role_id . " (có quyền admin)</p>";
        }
        
        if ($customer) {
            echo "<p><strong>Customer:</strong> role_id = " . $customer->role_id . " (không có quyền admin)</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Không tìm thấy nhân viên 'staff1' trong database</p>";
    }
    
    // Test URL truy cập
    echo "<h3>Test URL truy cập:</h3>";
    echo "<p><a href='" . URLROOT . "/admin/auth/login' target='_blank'>Đăng nhập Admin</a></p>";
    echo "<p><a href='" . URLROOT . "/admin/dashboard' target='_blank'>Dashboard Admin</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Lỗi: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><em>Script test hoàn thành. Xóa file này sau khi test xong.</em></p>";
?>
