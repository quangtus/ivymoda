<?php
namespace admin;

class ColorController extends \Controller {
    private $colorModel;

    public function __construct() {
        // Kiểm tra đăng nhập và quyền nhân viên (admin + staff)
        if(!isset($_SESSION['user_id'])) {
            $this->redirect('admin/auth/login');
            exit;
        }
        
        if($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 3) {
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
            $hex = trim($_POST['color_ma'] ?? '');
            
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

    public function edit($id = null) {
        if(!$id) {
            $this->redirect('admin/color');
            return;
        }

        $color = $this->colorModel->getColorById($id);
        if(!$color) {
            $_SESSION['error'] = 'Không tìm thấy màu';
            $this->redirect('admin/color');
            return;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['color_ten'] ?? '');
            $hex = trim($_POST['color_ma'] ?? '');
            
            if(empty($name)) {
                $_SESSION['error'] = 'Vui lòng nhập tên màu';
            } else {
                if($this->colorModel->updateColor($id, $name, $hex)) {
                    $_SESSION['success'] = 'Cập nhật màu thành công';
                    $this->redirect('admin/color');
                    return;
                }
                $_SESSION['error'] = 'Không thể cập nhật màu';
            }
        }

        $this->view('admin/color/edit', [
            'title' => 'Sửa màu sắc',
            'color' => $color,
            'success' => $_SESSION['success'] ?? '',
            'error' => $_SESSION['error'] ?? ''
        ]);
        unset($_SESSION['success'], $_SESSION['error']);
    }

    public function delete($id = null) {
        if(!$id) {
            $_SESSION['error'] = 'ID màu không hợp lệ';
            $this->redirect('admin/color');
            return;
        }

        $result = $this->colorModel->deleteColor($id);
        if($result === true) {
            $_SESSION['success'] = 'Xóa màu thành công';
        } else {
            $_SESSION['error'] = $result; // Error message from model
        }
        
        $this->redirect('admin/color');
    }
}


