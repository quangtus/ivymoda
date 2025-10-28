<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\controllers\admin\OrderController.php
namespace admin;

class OrderController extends \Controller {
    private $orderModel;
    
    public function __construct() {
        // Kiểm tra quyền nhân viên (admin + staff)
        $this->requireStaff();
        
        $this->orderModel = $this->model('OrderModel');
    }
    
    // Hiển thị tất cả đơn hàng
    public function index() {
        $orders = $this->orderModel->getAllOrders();
        
        $data = [
            'title' => 'Quản lý đơn hàng - Admin',
            'orders' => $orders
        ];
        
        $this->view('admin/order/index', $data);
    }
    
    // Hiển thị đơn hàng chưa xử lý
    public function pending() {
        $orders = $this->orderModel->getAllOrders(1, 50, 0); // status = 0: Chờ xử lý
        
        $data = [
            'title' => 'Đơn hàng chưa xử lý - Admin',
            'orders' => $orders
        ];
        
        $this->view('admin/order/index', $data);
    }
    
    // Hiển thị đơn hàng đã hoàn thành
    public function completed() {
        $orders = $this->orderModel->getAllOrders(1, 50, 2); // status = 2: Hoàn thành
        
        $data = [
            'title' => 'Đơn hàng đã hoàn thành - Admin',
            'orders' => $orders
        ];
        
        $this->view('admin/order/index', $data);
    }
    
    // Xem chi tiết đơn hàng
    public function detail($id = null) {
        if (!$id) {
            $this->redirect('admin/order');
        }
        
        $order = $this->orderModel->getOrderById($id);
        if (!$order) {
            $this->redirect('admin/order');
        }
        
        $orderItems = $this->orderModel->getOrderItems($id);
        
        $data = [
            'title' => 'Chi tiết đơn hàng - Admin',
            'order' => $order,
            'orderItems' => $orderItems
        ];
        
        $this->view('admin/order/detail', $data);
    }
    
    // Cập nhật trạng thái đơn hàng
    public function updateStatus($id = null) {
        if (!$id || !isset($_POST['status'])) {
            $this->redirect('admin/order');
        }
        
        $status = $_POST['status'];
        $result = $this->orderModel->updateOrderStatus($id, $status);
        
        if ($result === true) {
            // Gửi email thông báo cho khách hàng nếu cần
            $this->redirect('admin/order/detail/' . $id . '?success=updated');
        } else {
            $this->redirect('admin/order/detail/' . $id . '?error=update_failed');
        }
    }
}