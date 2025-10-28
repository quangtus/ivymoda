<?php
namespace admin;

class DiscountController extends \Controller {
    private $discountModel;
    
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
        
        $this->discountModel = $this->model('DiscountModel');
    }
    
    /**
     * Hiển thị danh sách mã giảm giá
     */
    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        
        $discounts = $this->discountModel->getAllDiscounts($page, $limit);
        
        $data = [
            'title' => 'Quản lý mã giảm giá',
            'discounts' => $discounts,
            'currentPage' => $page,
            'success' => $_SESSION['success'] ?? '',
            'error' => $_SESSION['error'] ?? ''
        ];
        
        // Xóa thông báo sau khi hiển thị
        unset($_SESSION['success'], $_SESSION['error']);
        
        $this->view('admin/discount/index', $data);
    }
    
    /**
     * Form thêm mã giảm giá mới
     */
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'ma_code' => trim($_POST['ma_code'] ?? ''),
                'ma_ten' => trim($_POST['ma_ten'] ?? ''),
                'ma_giam' => (float)($_POST['ma_giam'] ?? 0),
                'loai_giam' => $_POST['loai_giam'] ?? 'percent',
                'ngay_bat_dau' => $_POST['ngay_bat_dau'] ?? '',
                'ngay_ket_thuc' => $_POST['ngay_ket_thuc'] ?? '',
                'so_luong' => !empty($_POST['so_luong']) ? (int)$_POST['so_luong'] : null,
                'trang_thai' => isset($_POST['trang_thai']) ? 1 : 0
            ];
            
            // Validation
            $errors = [];
            if (empty($data['ma_code'])) {
                $errors[] = 'Mã code không được để trống';
            }
            if (empty($data['ma_ten'])) {
                $errors[] = 'Tên mã giảm giá không được để trống';
            }
            if ($data['ma_giam'] <= 0) {
                $errors[] = 'Giá trị giảm giá phải lớn hơn 0';
            }
            if ($data['loai_giam'] === 'percent' && $data['ma_giam'] > 100) {
                $errors[] = 'Phần trăm giảm giá không được vượt quá 100%';
            }
            if (empty($data['ngay_bat_dau'])) {
                $errors[] = 'Ngày bắt đầu không được để trống';
            }
            if (empty($data['ngay_ket_thuc'])) {
                $errors[] = 'Ngày kết thúc không được để trống';
            }
            if ($data['ngay_bat_dau'] >= $data['ngay_ket_thuc']) {
                $errors[] = 'Ngày kết thúc phải sau ngày bắt đầu';
            }
            
            if (empty($errors)) {
                if ($this->discountModel->createDiscount($data)) {
                    $_SESSION['success'] = 'Thêm mã giảm giá thành công';
                    $this->redirect('admin/discount');
                    return;
                } else {
                    $_SESSION['error'] = 'Không thể thêm mã giảm giá';
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errors);
            }
        }
        
        $this->view('admin/discount/add', [
            'title' => 'Thêm mã giảm giá',
            'success' => $_SESSION['success'] ?? '',
            'error' => $_SESSION['error'] ?? ''
        ]);
        
        unset($_SESSION['success'], $_SESSION['error']);
    }
    
    /**
     * Form sửa mã giảm giá
     */
    public function edit($id = null) {
        if (!$id) {
            $this->redirect('admin/discount');
            return;
        }
        
        $discount = $this->discountModel->getDiscountById($id);
        if (!$discount) {
            $_SESSION['error'] = 'Không tìm thấy mã giảm giá';
            $this->redirect('admin/discount');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'ma_code' => trim($_POST['ma_code'] ?? ''),
                'ma_ten' => trim($_POST['ma_ten'] ?? ''),
                'ma_giam' => (float)($_POST['ma_giam'] ?? 0),
                'loai_giam' => $_POST['loai_giam'] ?? 'percent',
                'ngay_bat_dau' => $_POST['ngay_bat_dau'] ?? '',
                'ngay_ket_thuc' => $_POST['ngay_ket_thuc'] ?? '',
                'so_luong' => !empty($_POST['so_luong']) ? (int)$_POST['so_luong'] : null,
                'trang_thai' => isset($_POST['trang_thai']) ? 1 : 0
            ];
            
            // Validation
            $errors = [];
            if (empty($data['ma_code'])) {
                $errors[] = 'Mã code không được để trống';
            }
            if (empty($data['ma_ten'])) {
                $errors[] = 'Tên mã giảm giá không được để trống';
            }
            if ($data['ma_giam'] <= 0) {
                $errors[] = 'Giá trị giảm giá phải lớn hơn 0';
            }
            if ($data['loai_giam'] === 'percent' && $data['ma_giam'] > 100) {
                $errors[] = 'Phần trăm giảm giá không được vượt quá 100%';
            }
            if (empty($data['ngay_bat_dau'])) {
                $errors[] = 'Ngày bắt đầu không được để trống';
            }
            if (empty($data['ngay_ket_thuc'])) {
                $errors[] = 'Ngày kết thúc không được để trống';
            }
            if ($data['ngay_bat_dau'] >= $data['ngay_ket_thuc']) {
                $errors[] = 'Ngày kết thúc phải sau ngày bắt đầu';
            }
            
            if (empty($errors)) {
                if ($this->discountModel->updateDiscount($id, $data)) {
                    $_SESSION['success'] = 'Cập nhật mã giảm giá thành công';
                    $this->redirect('admin/discount');
                    return;
                } else {
                    $_SESSION['error'] = 'Không thể cập nhật mã giảm giá';
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errors);
            }
        }
        
        $this->view('admin/discount/edit', [
            'title' => 'Sửa mã giảm giá',
            'discount' => $discount,
            'success' => $_SESSION['success'] ?? '',
            'error' => $_SESSION['error'] ?? ''
        ]);
        
        unset($_SESSION['success'], $_SESSION['error']);
    }
    
    /**
     * Xóa mã giảm giá
     */
    public function delete($id = null) {
        if (!$id) {
            $_SESSION['error'] = 'ID không hợp lệ';
            $this->redirect('admin/discount');
            return;
        }
        
        if ($this->discountModel->deleteDiscount($id)) {
            $_SESSION['success'] = 'Xóa mã giảm giá thành công';
        } else {
            $_SESSION['error'] = 'Không thể xóa mã giảm giá';
        }
        
        $this->redirect('admin/discount');
    }
    
    /**
     * Toggle trạng thái mã giảm giá
     */
    public function toggle($id = null) {
        if (!$id) {
            $_SESSION['error'] = 'ID không hợp lệ';
            $this->redirect('admin/discount');
            return;
        }
        
        $discount = $this->discountModel->getDiscountById($id);
        if (!$discount) {
            $_SESSION['error'] = 'Không tìm thấy mã giảm giá';
            $this->redirect('admin/discount');
            return;
        }
        
        $newStatus = $discount->trang_thai == 1 ? 0 : 1;
        if ($this->discountModel->updateDiscount($id, ['trang_thai' => $newStatus])) {
            $statusText = $newStatus ? 'kích hoạt' : 'vô hiệu hóa';
            $_SESSION['success'] = "Đã {$statusText} mã giảm giá thành công";
        } else {
            $_SESSION['error'] = 'Không thể thay đổi trạng thái mã giảm giá';
        }
        
        $this->redirect('admin/discount');
    }
}
