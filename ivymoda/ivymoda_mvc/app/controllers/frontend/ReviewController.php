<?php
class ReviewController extends Controller {
    private $reviewModel;
    private $productModel;
    private $orderModel;
    
    public function __construct() {
        $this->reviewModel = $this->model('ReviewModel');
        $this->productModel = $this->model('ProductModel');
        $this->orderModel = $this->model('OrderModel');
    }
    
    /**
     * Hiển thị form đánh giá sản phẩm
     */
    public function add($orderId, $productId) {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $orderId = (int)$orderId;
        $productId = (int)$productId;
        
        // Kiểm tra đơn hàng có thuộc về user không
        $order = $this->orderModel->getOrderById($orderId);
        if (!$order || (is_object($order) ? $order->user_id : $order['user_id']) != $userId) {
            $this->redirect('user/profile');
            return;
        }
        
        // Kiểm tra đơn hàng đã hoàn thành chưa
        $orderStatus = is_object($order) ? (int)$order->order_status : (int)$order['order_status'];
        if ($orderStatus != 2) { // 2 = Hoàn thành
            $_SESSION['error'] = 'Chỉ có thể đánh giá sản phẩm trong đơn hàng đã hoàn thành';
            $this->redirect('user/orderDetail/' . $orderId);
            return;
        }
        
        // Kiểm tra đã đánh giá chưa
        $existingReview = $this->reviewModel->checkUserReview($userId, $productId, $orderId);
        if ($existingReview) {
            $_SESSION['error'] = 'Bạn đã đánh giá sản phẩm này rồi';
            $this->redirect('user/orderDetail/' . $orderId);
            return;
        }
        
        // Lấy thông tin sản phẩm
        $product = $this->productModel->getProductById($productId);
        if (!$product) {
            $this->redirect('user/profile');
            return;
        }
        
        // Lấy thông tin đơn hàng
        $orderItems = $this->orderModel->getOrderItems($orderId);
        
        $data = [
            'title' => 'Đánh giá sản phẩm',
            'order' => $order,
            'product' => $product,
            'orderItems' => $orderItems,
            'orderId' => $orderId,
            'productId' => $productId
        ];
        
        $this->view('frontend/review/add', $data);
    }
    
    /**
     * Xử lý submit đánh giá
     */
    public function submit() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('user/profile');
            return;
        }
        
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $orderId = (int)$_POST['order_id'];
        $productId = (int)$_POST['product_id'];
        $rating = (int)$_POST['rating'];
        $comment = trim($_POST['comment']);
        
        // Process uploaded images
        $reviewImages = [];
        if (isset($_FILES['review_images']) && !empty($_FILES['review_images']['name'][0])) {
            $uploadDir = ROOT_PATH . 'public/assets/uploads/reviews/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB
            
            for ($i = 0; $i < count($_FILES['review_images']['name']); $i++) {
                if ($_FILES['review_images']['error'][$i] === UPLOAD_ERR_OK) {
                    $fileType = $_FILES['review_images']['type'][$i];
                    $fileSize = $_FILES['review_images']['size'][$i];
                    
                    // Validate file type and size
                    if (in_array($fileType, $allowedTypes) && $fileSize <= $maxFileSize) {
                        $fileName = uniqid() . '_' . time() . '_' . $i . '.' . pathinfo($_FILES['review_images']['name'][$i], PATHINFO_EXTENSION);
                        $filePath = $uploadDir . $fileName;
                        
                        if (move_uploaded_file($_FILES['review_images']['tmp_name'][$i], $filePath)) {
                            $reviewImages[] = 'reviews/' . $fileName;
                        }
                    }
                }
            }
        }
        
        // Validation
        if ($rating < 1 || $rating > 5) {
            $_SESSION['error'] = 'Điểm đánh giá phải từ 1 đến 5 sao';
            $this->redirect('review/add/' . $orderId . '/' . $productId);
            return;
        }
        
        if (empty($comment)) {
            $_SESSION['error'] = 'Vui lòng nhập nội dung đánh giá';
            $this->redirect('review/add/' . $orderId . '/' . $productId);
            return;
        }
        
        // Kiểm tra lại quyền đánh giá
        $order = $this->orderModel->getOrderById($orderId);
        if (!$order || (is_object($order) ? $order->user_id : $order['user_id']) != $userId) {
            $this->redirect('user/profile');
            return;
        }
        
        $orderStatus = is_object($order) ? (int)$order->order_status : (int)$order['order_status'];
        if ($orderStatus != 2) {
            $_SESSION['error'] = 'Chỉ có thể đánh giá sản phẩm trong đơn hàng đã hoàn thành';
            $this->redirect('user/orderDetail/' . $orderId);
            return;
        }
        
        // Kiểm tra đã đánh giá chưa
        $existingReview = $this->reviewModel->checkUserReview($userId, $productId, $orderId);
        if ($existingReview) {
            $_SESSION['error'] = 'Bạn đã đánh giá sản phẩm này rồi';
            $this->redirect('user/orderDetail/' . $orderId);
            return;
        }
        
        // Thêm đánh giá
        $reviewData = [
            'sanpham_id' => $productId,
            'user_id' => $userId,
            'order_id' => $orderId,
            'rating' => $rating,
            'comment' => $comment,
            'review_images' => !empty($reviewImages) ? json_encode($reviewImages) : null,
            'is_verified_purchase' => 1,
            'status' => 1
        ];
        
        if ($this->reviewModel->addReview($reviewData)) {
            $_SESSION['success'] = 'Cảm ơn bạn đã đánh giá sản phẩm!';
            $this->redirect('user/orderDetail/' . $orderId);
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
            $this->redirect('review/add/' . $orderId . '/' . $productId);
        }
    }
    
    /**
     * Hiển thị đánh giá của sản phẩm
     */
    public function productReviews($productId) {
        $productId = (int)$productId;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        // Lấy thông tin sản phẩm
        $product = $this->productModel->getProductById($productId);
        if (!$product) {
            $this->redirect('product/index');
            return;
        }
        
        // Lấy đánh giá
        $reviews = $this->reviewModel->getProductReviews($productId, 1, $limit, $offset);
        $ratingStats = $this->reviewModel->getProductRatingStats($productId);
        
        $data = [
            'title' => 'Đánh giá sản phẩm',
            'product' => $product,
            'reviews' => $reviews,
            'ratingStats' => $ratingStats,
            'currentPage' => $page,
            'totalReviews' => $ratingStats ? $ratingStats->total_reviews : 0
        ];
        
        $this->view('frontend/review/product_reviews', $data);
    }
}
