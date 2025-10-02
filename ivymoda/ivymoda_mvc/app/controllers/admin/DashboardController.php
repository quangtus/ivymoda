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
        
        // Kiểm tra đăng nhập và quyền admin
        if(!isset($_SESSION['user_id'])) {
            $this->redirect('admin/auth/login');
            exit;
        }
        
        if($_SESSION['role_id'] != 1) {
            $this->redirect('admin/auth/login');
            exit;
        }
    }
    
    /**
     * Trang chủ admin dashboard
     */
    public function index() {
        $data = [
            'title' => 'Dashboard - IVY moda Admin',
            'total_users' => 0,
            'total_orders' => 0,
            'total_products' => 0,
            'recent_orders' => []
        ];
        
        // Lấy thống kê cơ bản
        $users = $this->userModel->getAllUsers();
        $data['total_users'] = $users ? count($users) : 0;
        
        // Lấy các thống kê dashboard
        $newOrders = $this->dashboardModel->countNewOrders();
        $totalProducts = $this->dashboardModel->countProducts();
        $totalCustomers = $this->dashboardModel->countCustomers();
        
        // Lấy danh sách đơn hàng gần đây
        $recentOrders = $this->orderModel->getRecentOrders(5);
        
        $data['newOrders'] = $newOrders;
        $data['totalProducts'] = $totalProducts;
        $data['totalCustomers'] = $totalCustomers;
        $data['recentOrders'] = $recentOrders;
        
        $this->view('admin/dashboard/index', $data);
    }
}