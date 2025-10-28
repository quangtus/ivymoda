<?php
class ReviewModel extends Model {
    protected $table = 'tbl_product_review';
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Thêm đánh giá mới
     */
    public function addReview($data) {
        $sql = "INSERT INTO {$this->table} (sanpham_id, user_id, order_id, rating, comment, review_images, is_verified_purchase, status) 
                VALUES (:sanpham_id, :user_id, :order_id, :rating, :comment, :review_images, :is_verified_purchase, :status)";
        
        $this->db->query($sql);
        $this->db->bind(':sanpham_id', $data['sanpham_id']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':order_id', $data['order_id']);
        $this->db->bind(':rating', $data['rating']);
        $this->db->bind(':comment', $data['comment']);
        $this->db->bind(':review_images', $data['review_images'] ?? null);
        $this->db->bind(':is_verified_purchase', $data['is_verified_purchase']);
        $this->db->bind(':status', $data['status']);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Lấy đánh giá theo sản phẩm
     */
    public function getProductReviews($productId, $status = 1, $limit = 10, $offset = 0) {
        $sql = "SELECT r.*, u.fullname, u.email, o.order_code 
                FROM {$this->table} r 
                LEFT JOIN users u ON r.user_id = u.id 
                LEFT JOIN tbl_order o ON r.order_id = o.order_id 
                WHERE r.sanpham_id = :product_id AND r.status = :status 
                ORDER BY r.created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        $this->db->bind(':status', $status);
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        
        return $this->db->resultSet();
    }
    
    /**
     * Lấy điểm trung bình và thống kê đánh giá
     */
    public function getProductRatingStats($productId) {
        $sql = "SELECT 
                    COUNT(*) as total_reviews,
                    AVG(rating) as avg_rating,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as 5_star,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as 4_star,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as 3_star,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as 2_star,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as 1_star
                FROM {$this->table} 
                WHERE sanpham_id = :product_id AND status = 1";
        
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        
        return $this->db->single();
    }
    
    /**
     * Kiểm tra xem user đã đánh giá sản phẩm trong đơn hàng chưa
     */
    public function checkUserReview($userId, $productId, $orderId) {
        $sql = "SELECT review_id FROM {$this->table} 
                WHERE user_id = :user_id AND sanpham_id = :product_id AND order_id = :order_id";
        
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':product_id', $productId);
        $this->db->bind(':order_id', $orderId);
        
        return $this->db->single();
    }
    
    /**
     * Lấy tất cả đánh giá cho admin
     */
    public function getAllReviews($status = null, $limit = 20, $offset = 0) {
        $whereClause = '';
        if ($status !== null) {
            $whereClause = "WHERE r.status = :status";
        }
        
        $sql = "SELECT r.*, u.fullname, u.email, p.sanpham_tieude, o.order_code 
                FROM {$this->table} r 
                LEFT JOIN users u ON r.user_id = u.id 
                LEFT JOIN tbl_sanpham p ON r.sanpham_id = p.sanpham_id 
                LEFT JOIN tbl_order o ON r.order_id = o.order_id 
                {$whereClause}
                ORDER BY r.created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        $this->db->query($sql);
        if ($status !== null) {
            $this->db->bind(':status', $status);
        }
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        
        return $this->db->resultSet();
    }
    
    /**
     * Đếm tổng số đánh giá
     */
    public function countReviews($status = null) {
        $whereClause = '';
        if ($status !== null) {
            $whereClause = "WHERE status = :status";
        }
        
        $sql = "SELECT COUNT(*) as total FROM {$this->table} {$whereClause}";
        $this->db->query($sql);
        
        if ($status !== null) {
            $this->db->bind(':status', $status);
        }
        
        $result = $this->db->single();
        return $result ? $result->total : 0;
    }
    
    /**
     * Cập nhật trạng thái đánh giá
     */
    public function updateReviewStatus($reviewId, $status) {
        $sql = "UPDATE {$this->table} SET status = :status WHERE review_id = :review_id";
        
        $this->db->query($sql);
        $this->db->bind(':status', $status);
        $this->db->bind(':review_id', $reviewId);
        
        return $this->db->execute();
    }
    
    /**
     * Thêm phản hồi admin
     */
    public function addAdminReply($reviewId, $reply) {
        $sql = "UPDATE {$this->table} SET admin_reply = :reply WHERE review_id = :review_id";
        
        $this->db->query($sql);
        $this->db->bind(':reply', $reply);
        $this->db->bind(':review_id', $reviewId);
        
        return $this->db->execute();
    }
    
    /**
     * Xóa đánh giá
     */
    public function deleteReview($reviewId) {
        $sql = "DELETE FROM {$this->table} WHERE review_id = :review_id";
        
        $this->db->query($sql);
        $this->db->bind(':review_id', $reviewId);
        
        return $this->db->execute();
    }
    
    /**
     * Lấy đánh giá theo ID
     */
    public function getReviewById($reviewId) {
        $sql = "SELECT r.*, u.fullname, u.email, p.sanpham_tieude, o.order_code 
                FROM {$this->table} r 
                LEFT JOIN users u ON r.user_id = u.id 
                LEFT JOIN tbl_sanpham p ON r.sanpham_id = p.sanpham_id 
                LEFT JOIN tbl_order o ON r.order_id = o.order_id 
                WHERE r.review_id = :review_id";
        
        $this->db->query($sql);
        $this->db->bind(':review_id', $reviewId);
        
        return $this->db->single();
    }
}
