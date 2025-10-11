<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\models\DashboardModel.php

class DashboardModel extends Model {
    // Đếm đơn hàng mới (chờ xử lý)
    public function countNewOrders() {
        $query = "SELECT COUNT(*) as total FROM tbl_order WHERE order_status = 0";
        $result = $this->getOne($query);
        
        // Kiểm tra xem $result là object hay array
        if($result) {
            return is_object($result) ? $result->total : (is_array($result) ? $result['total'] : 0);
        }
        return 0;
    }
    
    // Đếm tổng số sản phẩm
    public function countProducts() {
        $query = "SELECT COUNT(*) as total FROM tbl_sanpham";
        $result = $this->getOne($query);
        
        // Kiểm tra xem $result là object hay array
        if($result) {
            return is_object($result) ? $result->total : (is_array($result) ? $result['total'] : 0);
        }
        return 0;
    }
    
    // Đếm tổng số khách hàng
    public function countCustomers() {
        $query = "SELECT COUNT(*) as total FROM users WHERE role_id = 2";
        $result = $this->getOne($query);
        
        // Kiểm tra xem $result là object hay array
        if($result) {
            return is_object($result) ? $result->total : (is_array($result) ? $result['total'] : 0);
        }
        return 0;
    }
}