<?php
class ErrorController extends Controller {
    public function __construct() {
        // Không cần khởi tạo model
    }
    
    // Action mặc định
    public function index() {
        $data = [
            'title' => 'Lỗi - IVY moda',
            'message' => 'Có lỗi xảy ra'
        ];
        
        $this->view('frontend/error/index', $data);
    }
    
    // Action 404 - không tìm thấy trang
    public function notFound() {
        $data = [
            'title' => '404 - Không tìm thấy trang',
            'message' => 'Trang bạn đang tìm kiếm không tồn tại hoặc đã bị di chuyển.'
        ];
        
        $this->view('frontend/error/404', $data);
    }
    
    // Action 403 - truy cập bị từ chối
    public function forbidden() {
        $data = [
            'title' => '403 - Truy cập bị từ chối',
            'message' => 'Bạn không có quyền truy cập trang này.'
        ];
        
        $this->view('frontend/error/403', $data);
    }
}