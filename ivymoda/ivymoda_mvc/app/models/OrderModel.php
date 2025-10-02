<?php
class OrderModel extends Model {
    protected $table = 'tbl_order';
    
    public function getAllOrders($page = 1, $limit = 20, $status = null) {
        $offset = ($page - 1) * $limit;
        if ($status !== null) {
            $sql = "SELECT * FROM {$this->table} WHERE order_status = ? ORDER BY order_date DESC LIMIT ? OFFSET ?";
            return $this->db->query($sql, [$status, $limit, $offset]);
        }
        $sql = "SELECT * FROM {$this->table} ORDER BY order_date DESC LIMIT ? OFFSET ?";
        return $this->db->query($sql, [$limit, $offset]);
    }
    
    public function countOrders($status = null) {
        if ($status !== null) {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE order_status = ?";
            $result = $this->db->query($sql, [$status]);
        } else {
            $sql = "SELECT COUNT(*) as total FROM {$this->table}";
            $result = $this->db->query($sql);
        }
        return $result[0]['total'] ?? 0;
    }
    
    public function getOrderById($orderId) {
        $sql = "SELECT * FROM {$this->table} WHERE order_id = ?";
        $result = $this->db->query($sql, [$orderId]);
        return $result[0] ?? null;
    }
    
    public function getOrderByCode($orderCode) {
        $sql = "SELECT * FROM {$this->table} WHERE order_code = ?";
        $result = $this->db->query($sql, [$orderCode]);
        return $result[0] ?? null;
    }
    
    public function getOrdersByUser($userId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY order_date DESC";
        return $this->db->query($sql, [$userId]);
    }
    
    public function getOrderItems($orderId) {
        $sql = "SELECT * FROM tbl_order_items WHERE order_id = ?";
        return $this->db->query($sql, [$orderId]);
    }
    
    public function createOrder($orderData) {
        $orderCode = 'ORD-' . time() . '-' . rand(1000, 9999);
        $sql = "INSERT INTO {$this->table} (order_code, user_id, session_id, customer_name, customer_phone, customer_email, customer_address, order_total, order_status, payment_method, shipping_method, order_note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [$orderCode, $orderData['user_id'] ?? null, $orderData['session_id'] ?? null, $orderData['customer_name'], $orderData['customer_phone'], $orderData['customer_email'] ?? null, $orderData['customer_address'], $orderData['order_total'], $orderData['order_status'] ?? 0, $orderData['payment_method'] ?? 'COD', $orderData['shipping_method'] ?? 'Standard', $orderData['order_note'] ?? null];
        $result = $this->db->execute($sql, $params);
        if ($result) {
            return ['success' => true, 'order_id' => $this->db->lastInsertId(), 'order_code' => $orderCode];
        }
        return ['success' => false];
    }
    
    public function addOrderItem($itemData) {
        $sql = "INSERT INTO tbl_order_items (order_id, sanpham_id, sanpham_ten, sanpham_gia, sanpham_soluong, sanpham_size, sanpham_color, sanpham_anh) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->db->execute($sql, [$itemData['order_id'], $itemData['sanpham_id'], $itemData['sanpham_ten'], $itemData['sanpham_gia'], $itemData['sanpham_soluong'], $itemData['sanpham_size'] ?? null, $itemData['sanpham_color'] ?? null, $itemData['sanpham_anh'] ?? null]);
    }
    
    public function updateOrderStatus($orderId, $status) {
        $sql = "UPDATE {$this->table} SET order_status = ?, updated_at = NOW() WHERE order_id = ?";
        return $this->db->execute($sql, [$status, $orderId]);
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
        return $this->db->execute($sql, $params);
    }
    
    public function deleteOrder($orderId) {
        $sql = "DELETE FROM {$this->table} WHERE order_id = ?";
        return $this->db->execute($sql, [$orderId]);
    }
    
    public function countOrdersByStatus() {
        $sql = "SELECT order_status, COUNT(*) as count FROM {$this->table} GROUP BY order_status";
        return $this->db->query($sql);
    }
    
    public function getRevenue($startDate = null, $endDate = null) {
        if ($startDate && $endDate) {
            $sql = "SELECT SUM(order_total) as revenue FROM {$this->table} WHERE order_status = 2 AND order_date BETWEEN ? AND ?";
            $result = $this->db->query($sql, [$startDate, $endDate]);
        } else {
            $sql = "SELECT SUM(order_total) as revenue FROM {$this->table} WHERE order_status = 2";
            $result = $this->db->query($sql);
        }
        return $result[0]['revenue'] ?? 0;
    }
    
    // Lấy đơn hàng gần đây
    public function getRecentOrders($limit = 5) {
        $sql = "SELECT * FROM {$this->table} ORDER BY order_date DESC LIMIT ?";
        return $this->db->query($sql, [$limit]);
    }
}
