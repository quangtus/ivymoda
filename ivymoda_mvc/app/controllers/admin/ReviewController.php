<?php
namespace admin;

class ReviewController extends \Controller {
    private $reviewModel;
    
    public function __construct() {
        $this->reviewModel = $this->model('ReviewModel');
        
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
     * Hiển thị danh sách đánh giá
     */
    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $status = isset($_GET['status']) ? (int)$_GET['status'] : null;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $reviews = $this->reviewModel->getAllReviews($status, $limit, $offset);
        $totalReviews = $this->reviewModel->countReviews($status);
        $totalPages = ceil($totalReviews / $limit);
        
        $data = [
            'title' => 'Quản lý đánh giá sản phẩm',
            'reviews' => $reviews,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalReviews' => $totalReviews,
            'currentStatus' => $status,
            'success' => $_SESSION['success'] ?? '',
            'error' => $_SESSION['error'] ?? ''
        ];
        
        // Xóa thông báo sau khi hiển thị
        unset($_SESSION['success']);
        unset($_SESSION['error']);
        
        $this->view('admin/review/index', $data);
    }
    
    /**
     * Cập nhật trạng thái đánh giá
     */
    public function updateStatus($reviewId) {
        $reviewId = (int)$reviewId;
        $status = isset($_POST['status']) ? (int)$_POST['status'] : 0;
        
        if ($this->reviewModel->updateReviewStatus($reviewId, $status)) {
            $_SESSION['success'] = 'Cập nhật trạng thái đánh giá thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật trạng thái';
        }
        
        $this->redirect('admin/review/index');
    }
    
    /**
     * Thêm phản hồi admin
     */
    public function reply($reviewId) {
        $reviewId = (int)$reviewId;
        $reply = trim($_POST['admin_reply'] ?? '');
        
        if (empty($reply)) {
            $_SESSION['error'] = 'Vui lòng nhập nội dung phản hồi';
            $this->redirect('admin/review/index');
            return;
        }
        
        if ($this->reviewModel->addAdminReply($reviewId, $reply)) {
            $_SESSION['success'] = 'Thêm phản hồi thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi thêm phản hồi';
        }
        
        $this->redirect('admin/review/index');
    }
    
    /**
     * Xóa đánh giá
     */
    public function delete($reviewId) {
        $reviewId = (int)$reviewId;
        
        if ($this->reviewModel->deleteReview($reviewId)) {
            $_SESSION['success'] = 'Xóa đánh giá thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa đánh giá';
        }
        
        $this->redirect('admin/review/index');
    }
    
    /**
     * Xem chi tiết đánh giá
     */
    public function viewDetail($reviewId) {
        $reviewId = (int)$reviewId;
        $review = $this->reviewModel->getReviewById($reviewId);
        
        if (!$review) {
            $_SESSION['error'] = 'Không tìm thấy đánh giá';
            $this->redirect('admin/review/index');
            return;
        }
        
        $data = [
            'title' => 'Chi tiết đánh giá',
            'review' => $review
        ];
        
        $this->view('admin/review/view', $data);
    }
}
