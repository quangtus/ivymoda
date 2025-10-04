<?php
namespace admin;

class SizeController extends \Controller {
    private $productModel;
    
    public function __construct() {
        $this->productModel = $this->model('ProductModel');
        
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
     * Hiển thị danh sách sizes
     */
    public function index() {
        $sizes = $this->productModel->getAllSizes();
        
        $this->view('admin/size/index', [
            'title' => 'Quản lý Size',
            'sizes' => $sizes,
            'success' => $_SESSION['success'] ?? '',
            'error' => $_SESSION['error'] ?? ''
        ]);
        
        // Xóa thông báo sau khi hiển thị
        unset($_SESSION['success']);
        unset($_SESSION['error']);
    }
    
    /**
     * Form thêm size mới
     */
    public function add() {
        $data = [
            'title' => 'Thêm Size mới',
            'size_ten' => '',
            'size_order' => '',
            'error' => '',
            'success' => ''
        ];
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data['size_ten'] = trim($_POST['size_ten'] ?? '');
            $data['size_order'] = (int)($_POST['size_order'] ?? 0);
            
            // Validation
            if(empty($data['size_ten'])) {
                $data['error'] = 'Vui lòng nhập tên size';
            } else {
                // Kiểm tra trùng tên
                $existingSize = $this->productModel->getSizeByName($data['size_ten']);
                if($existingSize) {
                    $data['error'] = 'Size "' . $data['size_ten'] . '" đã tồn tại';
                } else {
                    // Thêm size mới
                    if($this->productModel->addSize($data)) {
                        $_SESSION['success'] = 'Thêm size thành công!';
                        $this->redirect('admin/size');
                        exit;
                    } else {
                        $data['error'] = 'Có lỗi xảy ra khi thêm size';
                    }
                }
            }
        }
        
        $this->view('admin/size/add', $data);
    }
    
    /**
     * Form sửa size
     */
    public function edit($sizeId = null) {
        if(!$sizeId) {
            $_SESSION['error'] = 'Không tìm thấy size';
            $this->redirect('admin/size');
            exit;
        }
        
        $size = $this->productModel->getSizeById($sizeId);
        if(!$size) {
            $_SESSION['error'] = 'Size không tồn tại';
            $this->redirect('admin/size');
            exit;
        }
        
        $data = [
            'title' => 'Sửa Size',
            'size' => $size,
            'size_ten' => $size->size_ten,
            'size_order' => $size->size_order,
            'error' => '',
            'success' => ''
        ];
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data['size_ten'] = trim($_POST['size_ten'] ?? '');
            $data['size_order'] = (int)($_POST['size_order'] ?? 0);
            
            // Validation
            if(empty($data['size_ten'])) {
                $data['error'] = 'Vui lòng nhập tên size';
            } else {
                // Kiểm tra trùng tên (trừ chính nó)
                $existingSize = $this->productModel->getSizeByName($data['size_ten']);
                if($existingSize && $existingSize->size_id != $sizeId) {
                    $data['error'] = 'Size "' . $data['size_ten'] . '" đã tồn tại';
                } else {
                    // Cập nhật size
                    $updateData = [
                        'size_id' => $sizeId,
                        'size_ten' => $data['size_ten'],
                        'size_order' => $data['size_order']
                    ];
                    
                    if($this->productModel->updateSize($updateData)) {
                        $_SESSION['success'] = 'Cập nhật size thành công!';
                        $this->redirect('admin/size');
                        exit;
                    } else {
                        $data['error'] = 'Có lỗi xảy ra khi cập nhật size';
                    }
                }
            }
        }
        
        $this->view('admin/size/edit', $data);
    }
    
    /**
     * Xóa size
     */
    public function delete($sizeId = null) {
        if(!$sizeId) {
            $_SESSION['error'] = 'Không tìm thấy size';
            $this->redirect('admin/size');
            exit;
        }
        
        $size = $this->productModel->getSizeById($sizeId);
        if(!$size) {
            $_SESSION['error'] = 'Size không tồn tại';
            $this->redirect('admin/size');
            exit;
        }
        
        // Kiểm tra xem size có đang được sử dụng không
        $variantCount = $this->productModel->countVariantsBySize($sizeId);
        if($variantCount > 0) {
            $_SESSION['error'] = "Không thể xóa size \"{$size->size_ten}\" vì đang có {$variantCount} sản phẩm sử dụng size này";
            $this->redirect('admin/size');
            exit;
        }
        
        // Xóa size
        if($this->productModel->deleteSize($sizeId)) {
            $_SESSION['success'] = 'Xóa size thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa size';
        }
        
        $this->redirect('admin/size');
        exit;
    }
}
