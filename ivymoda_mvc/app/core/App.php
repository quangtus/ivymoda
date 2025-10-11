<?php
/**
 * App Class - Main Router và Front Controller
 * Xử lý URL và điều hướng đến controller tương ứng
 */
class App {
    protected $controller = 'HomeController';  // Controller mặc định
    protected $action = 'index';              // Action mặc định
    protected $params = [];                   // Danh sách tham số
    protected $adminPrefix = 'admin';         // Tiền tố cho admin area
    protected $isAdmin = false;               // Flag kiểm tra có phải admin area không

    /**
     * Constructor - Khởi tạo router
     */
    public function __construct() {
        $url = $this->parseUrl();
        
        // Xác định xem có phải đang truy cập admin area không
        if (isset($url[0]) && $url[0] == $this->adminPrefix) {
            $this->isAdmin = true;
            array_shift($url);
        }
        
        // Xác định controller
        if (isset($url[0]) && !empty($url[0])) {
            $this->controller = ucfirst($url[0]) . 'Controller';
            array_shift($url);
        } else {
            $this->controller = 'HomeController';
        }
        
        // Xác định đường dẫn controller
        if ($this->isAdmin) {
            $controllerFile = 'admin/' . $this->controller;
            $namespace = 'admin\\';
        } else {
            $controllerFile = 'frontend/' . $this->controller;
            $namespace = '';
        }
        
        $controllerPath = ROOT_PATH . 'app/controllers/' . $controllerFile . '.php';
        
        // Kiểm tra xem file controller có tồn tại không
        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            $this->controller = $namespace . $this->controller;
            $this->controller = new $this->controller();
        } else {
            // Sử dụng ErrorController nếu không tìm thấy controller
            require_once ROOT_PATH . 'app/controllers/frontend/ErrorController.php';
            $this->controller = new ErrorController();
            $this->action = 'notFound';
            $this->params = [];
            
            // Gọi phương thức notFound
            call_user_func_array([$this->controller, $this->action], $this->params);
            return;
        }
        
        // Xác định action
        if (isset($url[0])) {
            if (method_exists($this->controller, $url[0])) {
                $this->action = $url[0];
                array_shift($url);
            }
        }
        
        // Các tham số còn lại
        $this->params = $url ? array_values($url) : [];
        
        // Gọi method của controller
        call_user_func_array([$this->controller, $this->action], $this->params);
    }
    
    /**
     * Phân tích URL và trả về mảng các thành phần
     * @return array URL parts
     */
    protected function parseUrl() {
        if (isset($_GET['url'])) {
            // Loại bỏ dấu / ở cuối URL và các ký tự không hợp lệ
            $url = filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL);
            // Phân tách URL thành mảng
            return explode('/', $url);
        }
        
        return [];
    }
}