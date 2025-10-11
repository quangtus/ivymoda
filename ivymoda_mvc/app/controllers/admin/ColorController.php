<?php
namespace admin;

class ColorController extends \Controller {
    private $colorModel;

    public function __construct() {
        // Auth basic check (reuse style from other admin controllers)
        if(!isset($_SESSION['user_id'])) {
            $this->redirect('admin/auth/login');
            exit;
        }
        $this->colorModel = $this->model('ColorModel');
    }

    public function index() {
        $colors = $this->colorModel->getAllColors();
        $this->view('admin/color/index', [
            'title' => 'Quản lý màu sắc',
            'colors' => $colors,
            'success' => $_SESSION['success'] ?? '',
            'error' => $_SESSION['error'] ?? ''
        ]);
        unset($_SESSION['success'], $_SESSION['error']);
    }

    public function add() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['color_ten'] ?? '');
            $hex = trim($_POST['color_hex'] ?? '');
            if(empty($name)) {
                $_SESSION['error'] = 'Vui lòng nhập tên màu';
            } else {
                if($this->colorModel->addColor($name, $hex)) {
                    $_SESSION['success'] = 'Thêm màu thành công';
                    $this->redirect('admin/color');
                    return;
                }
                $_SESSION['error'] = 'Không thể thêm màu';
            }
        }
        $this->view('admin/color/add', [
            'title' => 'Thêm màu sắc',
            'success' => $_SESSION['success'] ?? '',
            'error' => $_SESSION['error'] ?? ''
        ]);
        unset($_SESSION['success'], $_SESSION['error']);
    }
}


