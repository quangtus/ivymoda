<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\controllers\admin\DashboardController.php
namespace admin;

class DashboardController extends \Controller {
    private $userModel;
    private $dashboardModel;
    private $orderModel;
    
    public function __construct() {
        $this->userModel = $this->model('UserModel');
        $this->dashboardModel = $this->model('DashboardModel');
        $this->orderModel = $this->model('OrderModel');
        
        // Kiểm tra đăng nhập và quyền nhân viên (admin + staff)
        if(!isset($_SESSION['user_id'])) {
            $this->redirect('admin/auth/login');
            exit;
        }
        
        if($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 3) {
            $this->redirect('admin/auth/login');
            exit;
        }
    }
    
    /**
     * Trang chủ admin dashboard
     */
    public function index() {
        // Lấy thống kê từ DashboardModel
        $totalUsers = $this->dashboardModel->countCustomers(); // Đếm khách hàng (role_id = 2)
        $totalOrders = $this->dashboardModel->countTotalOrders(); // Đếm tất cả đơn hàng
        $totalProducts = $this->dashboardModel->countProducts(); // Đếm tổng số sản phẩm
        
        // Lấy danh sách đơn hàng gần đây
        $recentOrders = $this->orderModel->getRecentOrders(5);
        
        $data = [
            'title' => 'Dashboard - IVY moda Admin',
            'total_users' => $totalUsers,
            'total_orders' => $totalOrders,
            'total_products' => $totalProducts,
            'recent_orders' => $recentOrders
        ];
        
        $this->view('admin/dashboard/index', $data);
    }
}