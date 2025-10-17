<?php
class OrderModel extends Model {
    protected $table = 'tbl_order';
    
    public function getAllOrders($page = 1, $limit = 20, $status = null) {
        $limit = (int)$limit;
        $page = (int)$page;
        $offset = max(0, ($page - 1) * $limit);
        // IMPORTANT: MySQL native prepared statements do not allow binding LIMIT/OFFSET
        if ($status !== null) {
            $sql = "SELECT * FROM {$this->table} WHERE order_status = ? ORDER BY order_date DESC LIMIT $limit OFFSET $offset";
            return $this->getAll($sql, [$status]);
        }
        $sql = "SELECT * FROM {$this->table} ORDER BY order_date DESC LIMIT $limit OFFSET $offset";
        return $this->getAll($sql);
    }
    
    public function countOrders($status = null) {
        if ($status !== null) {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE order_status = ?";
            $result = $this->getOne($sql, [$status]);
        } else {
            $sql = "SELECT COUNT(*) as total FROM {$this->table}";
            $result = $this->getOne($sql);
        }
        if (is_object($result)) {
            return $result->total ?? 0;
        }
        return $result['total'] ?? 0;
    }
    
    public function getOrderById($orderId) {
        $sql = "SELECT * FROM {$this->table} WHERE order_id = ?";
        return $this->getOne($sql, [$orderId]);
    }
    
    public function getOrderByCode($orderCode) {
        $sql = "SELECT * FROM {$this->table} WHERE order_code = ?";
        return $this->getOne($sql, [$orderCode]);
    }
    
    public function getOrdersByUser($userId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY order_date DESC";
        return $this->getAll($sql, [$userId]);
    }
    
    public function getOrderItems($orderId) {
        $sql = "SELECT * FROM tbl_order_items WHERE order_id = ?";
        return $this->getAll($sql, [$orderId]);
    }
    
    public function createOrder($orderData) {
        $orderCode = 'ORD-' . time() . '-' . rand(1000, 9999);
        
        // Xử lý mã giảm giá nếu có
        $discountCode = $orderData['discount_code'] ?? null;
        $discountValue = $orderData['discount_value'] ?? 0;
        $originalTotal = $orderData['original_total'] ?? $orderData['order_total'];
        $finalTotal = $orderData['order_total']; // Đã tính giảm giá
        
        $sql = "INSERT INTO {$this->table} (order_code, user_id, session_id, customer_name, customer_phone, customer_email, customer_address, order_total, original_total, discount_code, discount_value, order_status, payment_method, shipping_method, order_note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        // Normalize payment method to match enum('cod','momo')
        $paymentMethod = strtolower($orderData['payment_method'] ?? 'cod');
        $params = [
            $orderCode,
            $orderData['user_id'] ?? null,
            $orderData['session_id'] ?? null,
            $orderData['customer_name'],
            $orderData['customer_phone'],
            $orderData['customer_email'] ?? null,
            $orderData['customer_address'],
            $finalTotal, // Tổng tiền cuối cùng sau giảm giá
            $originalTotal, // Tổng tiền gốc
            $discountCode,
            $discountValue,
            $orderData['order_status'] ?? 0,
            $paymentMethod,
            $orderData['shipping_method'] ?? 'Standard',
            $orderData['order_note'] ?? null
        ];
        
        // Use base Model::execute to prepare and bind params properly
        $result = $this->execute($sql, $params);
        if ($result) {
            return ['success' => true, 'order_id' => $this->db->lastInsertId(), 'order_code' => $orderCode];
        }
        return ['success' => false];
    }
    
    /**
     * Thêm order item (MIGRATED TO VARIANT SYSTEM)
     * 
     * @param array $itemData Dữ liệu item từ cart
     * Format:
     * - order_id: ID đơn hàng
     * - variant_id: ID variant
     * - sanpham_ten: Tên sản phẩm (snapshot)
     * - sanpham_gia: Giá (snapshot)
     * - sanpham_soluong: Số lượng
     * - sanpham_size: Tên size (snapshot)
     * - sanpham_color: Tên màu (snapshot)
     * - sanpham_anh: Ảnh (snapshot)
     * 
     * @return bool Success or failure
     */
    public function addOrderItem($itemData) {
        $sql = "INSERT INTO tbl_order_items 
                (order_id, variant_id, sanpham_id, sanpham_ten, sanpham_gia, sanpham_soluong, 
                 sanpham_size, sanpham_color, sanpham_anh) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        // Use base Model::execute to prepare and bind params properly
        return $this->execute($sql, [
            $itemData['order_id'],
            $itemData['variant_id'],
            $itemData['sanpham_id'],
            $itemData['sanpham_ten'],
            $itemData['sanpham_gia'],
            $itemData['sanpham_soluong'],
            $itemData['sanpham_size'],
            $itemData['sanpham_color'],
            $itemData['sanpham_anh'] ?? null
        ]);
    }
    
    public function updateOrderStatus($orderId, $status) {
        $sql = "UPDATE {$this->table} SET order_status = ?, updated_at = NOW() WHERE order_id = ?";
        return $this->execute($sql, [$status, $orderId]);
    }
    
    /**
     * Cập nhật trạng thái thanh toán cho đơn hàng
     */
    public function setPaymentStatus($orderId, $paymentStatus, $transactionId = null) {
        $sql = "UPDATE {$this->table} SET payment_status = ?, payment_transaction_id = ?, updated_at = NOW() WHERE order_id = ?";
        return $this->execute($sql, [$paymentStatus, $transactionId, $orderId]);
    }

    public function updateOrder($orderId, $data) {
        $fields = [];
        $params = [];
        if (isset($data['customer_name'])) { $fields[] = "customer_name = ?"; $params[] = $data['customer_name']; }
        if (isset($data['customer_phone'])) { $fields[] = "customer_phone = ?"; $params[] = $data['customer_phone']; }
        if (isset($data['customer_address'])) { $fields[] = "customer_address = ?"; $params[] = $data['customer_address']; }
        if (isset($data['order_note'])) { $fields[] = "order_note = ?"; $params[] = $data['order_note']; }
        if (empty($fields)) return false;
        $fields[] = "updated_at = NOW()";
        $params[] = $orderId;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE order_id = ?";
        return $this->execute($sql, $params);
    }
    
    public function deleteOrder($orderId) {
        $sql = "DELETE FROM {$this->table} WHERE order_id = ?";
        return $this->execute($sql, [$orderId]);
    }
    
    public function countOrdersByStatus() {
        $sql = "SELECT order_status, COUNT(*) as count FROM {$this->table} GROUP BY order_status";
        return $this->getAll($sql);
    }
    
    public function getRevenue($startDate = null, $endDate = null) {
        if ($startDate && $endDate) {
            $sql = "SELECT SUM(order_total) as revenue FROM {$this->table} WHERE order_status = 2 AND order_date BETWEEN ? AND ?";
            $result = $this->getOne($sql, [$startDate, $endDate]);
        } else {
            $sql = "SELECT SUM(order_total) as revenue FROM {$this->table} WHERE order_status = 2";
            $result = $this->getOne($sql);
        }
        if (is_object($result)) {
            return $result->revenue ?? 0;
        }
        return $result['revenue'] ?? 0;
    }
    
    // Lấy đơn hàng gần đây
    public function getRecentOrders($limit = 5) {
        $limit = (int)$limit;
        $sql = "SELECT * FROM {$this->table} ORDER BY order_date DESC LIMIT $limit";
        return $this->getAll($sql);
    }
}
