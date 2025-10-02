<?php
/**
 * Base Controller Class
 * Lớp cơ sở mà tất cả controller kế thừa
 */
class Controller {
    /**
     * Load model tương ứng
     * @param string $model Tên của model cần load
     * @return object Instance của model
     */
    protected function model($model) {
        if (file_exists(ROOT_PATH . 'app/models/' . $model . '.php')) {
            require_once ROOT_PATH . 'app/models/' . $model . '.php';
            return new $model();
        }
        return null;
    }
    
    /**
     * Load view tương ứng với dữ liệu được truyền vào
     * @param string $view Đường dẫn đến file view
     * @param array $data Dữ liệu truyền vào view
     * @param boolean $includeLayout Có include layout chung không
     * @return void
     */
    protected function view($view, $data = [], $includeLayout = true) {
        // Kiểm tra xem view có tồn tại không
        $view_path = ROOT_PATH . 'app/views/' . $view . '.php';
        if (file_exists($view_path)) {
            // Extract biến từ mảng data để sử dụng trong view
            if (!empty($data)) {
                extract($data);
            }
            
            // Include view
            require_once $view_path;
        } else {
            // View không tồn tại, hiển thị thông báo lỗi
            echo "View not found: " . $view_path;
        }
    }
    
    /**
     * Kiểm tra quyền admin
     * @return void
     */
    protected function requireAdmin() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
            $this->redirect('admin/auth/login');
            exit();
        }
    }
    
    /**
     * Chuyển hướng đến URL cụ thể
     * @param string $url URL cần chuyển hướng đến
     * @return void
     */
    protected function redirect($url) {
        header('Location: ' . URLROOT . '/' . $url);
        exit();
    }
    
    /**
     * Trả về dữ liệu dưới dạng JSON (cho AJAX)
     * @param mixed $data Dữ liệu trả về
     * @param int $status HTTP status code
     * @return void
     */
    protected function json($data, $status = 200) {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit();
    }
}