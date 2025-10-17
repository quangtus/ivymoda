<?php
namespace admin;

class PromotionController extends \Controller {
    private $promotionModel;

    public function __construct() {
        // Require login and admin role
        if(!isset($_SESSION['user_id']) || !isset($_SESSION['role_id']) || (int)$_SESSION['role_id'] !== 1) {
            header('Location: ' . BASE_URL . 'admin/auth/login');
            exit;
        }
        $this->promotionModel = $this->model('PromotionModel');
    }

    public function index() {
        $promotions = $this->promotionModel->getAllPromotions(100, 0);
        $this->view('admin/promotion/index', [
            'title' => 'Quản lý khuyến mãi',
            'promotions' => $promotions
        ]);
    }

    public function add() {
        $data = [
            'title' => 'Thêm khuyến mãi',
            'error' => '',
            'success' => ''
        ];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $payload = $_POST;

            // Handle image upload (optional)
            if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = ROOT_PATH . 'public/assets/uploads/';
                if(!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = 'promotion_' . time() . '_' . rand(1000,9999) . '.' . strtolower($ext);
                $dest = $uploadDir . $filename;
                if(move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    $payload['image_url'] = $filename;
                }
            }

            if($this->promotionModel->create($payload)) {
                $data['success'] = 'Tạo chương trình khuyến mãi thành công';
            } else {
                $data['error'] = 'Không thể tạo khuyến mãi';
            }
        }

        $this->view('admin/promotion/add', $data);
    }

    public function edit($id = null) {
        if(!$id) { $this->redirect('admin/promotion'); return; }
        $promotion = $this->promotionModel->getById($id);
        if(!$promotion) { $this->redirect('admin/promotion'); return; }

        $data = [
            'title' => 'Sửa khuyến mãi',
            'promotion' => $promotion,
            'error' => '',
            'success' => ''
        ];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $payload = $_POST;
            // Handle image upload
            if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = ROOT_PATH . 'public/assets/uploads/';
                if(!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = 'promotion_' . time() . '_' . rand(1000,9999) . '.' . strtolower($ext);
                $dest = $uploadDir . $filename;
                if(move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    $payload['image_url'] = $filename;
                }
            }

            if($this->promotionModel->updatePromotion($id, $payload)) {
                $data['success'] = 'Cập nhật thành công';
                $data['promotion'] = $this->promotionModel->getById($id);
            } else {
                $data['error'] = 'Không thể cập nhật';
            }
        }

        $this->view('admin/promotion/edit', $data);
    }

    public function delete($id = null) {
        if(!$id) { $this->redirect('admin/promotion'); return; }
        $this->promotionModel->deletePromotion($id);
        $this->redirect('admin/promotion');
    }
}


