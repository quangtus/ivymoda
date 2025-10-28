<?php
/**
 * ReportModel - Xử lý báo cáo và thống kê (UC2.7)
 * Bảng: tbl_thong_ke, tbl_order
 */
class ReportModel extends Model {
    protected $table = 'tbl_thong_ke';
    protected $orderTable = 'tbl_order';
    protected $orderItemTable = 'tbl_order_items';
    protected $productTable = 'tbl_sanpham';
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Lấy thống kê theo ngày
     */
    public function getStatsByDate($date) {
        $date = $this->escape($date);
        $query = "SELECT * FROM {$this->table} WHERE ngay = '$date'";
        return $this->getOne($query);
    }
    
    /**
     * Lấy thống kê theo khoảng thời gian
     */
    public function getStatsByDateRange($fromDate, $toDate) {
        $fromDate = $this->escape($fromDate);
        $toDate = $this->escape($toDate);
        $query = "SELECT * FROM {$this->table} 
                  WHERE ngay BETWEEN '$fromDate' AND '$toDate' 
                  ORDER BY ngay ASC";
        return $this->getAll($query);
    }
    
    /**
     * Cập nhật hoặc tạo thống kê cho một ngày
     */
    public function updateOrCreateStats($date) {
        $date = $this->escape($date);
        
        // Tính toán doanh thu và số đơn hàng
        $orderQuery = "SELECT 
                       COUNT(*) as so_don_hang,
                       COALESCE(SUM(order_total), 0) as doanh_thu
                       FROM {$this->orderTable}
                       WHERE DATE(order_date) = '$date' 
                       AND order_status != 3"; // Không tính đơn hàng bị hủy
        
        $orderStats = $this->getOne($orderQuery);
        
        // Tính số sản phẩm bán ra
        $productQuery = "SELECT COALESCE(SUM(oi.sanpham_soluong), 0) as so_san_pham_ban
                        FROM {$this->orderItemTable} oi
                        INNER JOIN {$this->orderTable} o ON oi.order_id = o.order_id
                        WHERE DATE(o.order_date) = '$date'
                        AND o.order_status != 3";
        
        $productStats = $this->getOne($productQuery);
        
        $doanh_thu = is_object($orderStats) ? $orderStats->doanh_thu : ($orderStats['doanh_thu'] ?? 0);
        $so_don_hang = is_object($orderStats) ? $orderStats->so_don_hang : ($orderStats['so_don_hang'] ?? 0);
        $so_san_pham_ban = is_object($productStats) ? $productStats->so_san_pham_ban : ($productStats['so_san_pham_ban'] ?? 0);
        
        // Kiểm tra xem đã có thống kê chưa
        $exists = $this->getStatsByDate($date);
        
        if ($exists) {
            // Cập nhật
            $query = "UPDATE {$this->table} SET 
                      doanh_thu = $doanh_thu,
                      so_don_hang = $so_don_hang,
                      so_san_pham_ban = $so_san_pham_ban
                      WHERE ngay = '$date'";
        } else {
            // Tạo mới
            $query = "INSERT INTO {$this->table} 
                      (ngay, doanh_thu, so_don_hang, so_san_pham_ban) 
                      VALUES ('$date', $doanh_thu, $so_don_hang, $so_san_pham_ban)";
        }
        
        return $this->execute($query);
    }
    
    /**
     * Lấy tổng doanh thu theo khoảng thời gian
     */
    public function getTotalRevenue($fromDate = null, $toDate = null) {
        $where = "order_status != 3"; // Không tính đơn hàng bị hủy
        
        if ($fromDate && $toDate) {
            $fromDate = $this->escape($fromDate);
            $toDate = $this->escape($toDate);
            $where .= " AND DATE(order_date) BETWEEN '$fromDate' AND '$toDate'";
        }
        
        $query = "SELECT COALESCE(SUM(order_total), 0) as total 
                  FROM {$this->orderTable} 
                  WHERE $where";
        
        $result = $this->getOne($query);
        return is_object($result) ? $result->total : ($result['total'] ?? 0);
    }
    
    /**
     * Lấy số đơn hàng theo khoảng thời gian
     */
    public function getTotalOrders($fromDate = null, $toDate = null, $status = null) {
        $where = "1=1";
        
        if ($fromDate && $toDate) {
            $fromDate = $this->escape($fromDate);
            $toDate = $this->escape($toDate);
            $where .= " AND DATE(order_date) BETWEEN '$fromDate' AND '$toDate'";
        }
        
        if ($status !== null) {
            $status = (int)$status;
            $where .= " AND order_status = $status";
        }
        
        $query = "SELECT COUNT(*) as total FROM {$this->orderTable} WHERE $where";
        
        $result = $this->getOne($query);
        return is_object($result) ? $result->total : ($result['total'] ?? 0);
    }
    
    /**
     * Lấy sản phẩm bán chạy nhất
     */
    public function getTopSellingProducts($limit = 10, $fromDate = null, $toDate = null) {
        $where = "o.order_status != 3"; // Không tính đơn hàng bị hủy
        
        if ($fromDate && $toDate) {
            $fromDate = $this->escape($fromDate);
            $toDate = $this->escape($toDate);
            $where .= " AND DATE(o.order_date) BETWEEN '$fromDate' AND '$toDate'";
        }
        
        $limit = (int)$limit;
        
        $query = "SELECT 
                  p.sanpham_id,
                  p.sanpham_tieude,
                  p.sanpham_anh,
                  p.sanpham_gia,
                  SUM(oi.sanpham_soluong) as total_sold,
                  SUM(oi.sanpham_gia * oi.sanpham_soluong) as total_revenue
                  FROM {$this->orderItemTable} oi
                  INNER JOIN {$this->orderTable} o ON oi.order_id = o.order_id
                  INNER JOIN {$this->productTable} p ON oi.sanpham_id = p.sanpham_id
                  WHERE $where
                  GROUP BY oi.sanpham_id
                  ORDER BY total_sold DESC
                  LIMIT $limit";
        
        return $this->getAll($query);
    }
    
    /**
     * Lấy doanh thu theo tháng trong năm
     */
    public function getMonthlyRevenue($year) {
        $year = (int)$year;
        
        $query = "SELECT 
                  MONTH(order_date) as month,
                  SUM(order_total) as revenue,
                  COUNT(*) as orders
                  FROM {$this->orderTable}
                  WHERE YEAR(order_date) = $year 
                  AND order_status != 3
                  GROUP BY MONTH(order_date)
                  ORDER BY month ASC";
        
        return $this->getAll($query);
    }
    
    /**
     * Doanh thu theo ngày trong khoảng from-to (YYYY-MM-DD)
     */
    public function getDailyRevenue($fromDate, $toDate) {
        $fromDate = $this->escape($fromDate);
        $toDate = $this->escape($toDate);
        $query = "SELECT 
                  DATE(order_date) as ngay,
                  SUM(order_total) as doanh_thu,
                  COUNT(*) as orders
                  FROM {$this->orderTable}
                  WHERE DATE(order_date) BETWEEN '$fromDate' AND '$toDate'
                  AND order_status != 3
                  GROUP BY DATE(order_date)
                  ORDER BY ngay ASC";
        return $this->getAll($query);
    }

    /**
     * Doanh thu theo từng ngày trong một tháng cụ thể
     */
    public function getDailyRevenueForMonth($year, $month) {
        $year = (int)$year;
        $month = (int)$month;
        $query = "SELECT 
                  DAY(order_date) as day,
                  SUM(order_total) as revenue,
                  COUNT(*) as orders
                  FROM {$this->orderTable}
                  WHERE YEAR(order_date) = $year
                  AND MONTH(order_date) = $month
                  AND order_status != 3
                  GROUP BY DAY(order_date)
                  ORDER BY day ASC";
        return $this->getAll($query);
    }
    
    /**
     * Lấy số lượng đơn hàng theo trạng thái
     */
    public function getOrdersByStatus() {
        $query = "SELECT 
                  order_status,
                  COUNT(*) as count,
                  CASE 
                      WHEN order_status = 0 THEN 'Chờ xử lý'
                      WHEN order_status = 1 THEN 'Đang giao'
                      WHEN order_status = 2 THEN 'Hoàn thành'
                      WHEN order_status = 3 THEN 'Đã hủy'
                      ELSE 'Không xác định'
                  END as status_name
                  FROM {$this->orderTable}
                  GROUP BY order_status
                  ORDER BY order_status ASC";
        
        return $this->getAll($query);
    }
    
    /**
     * Lấy thống kê tổng quan
     */
    public function getDashboardStats() {
        // Tổng doanh thu
        $revenueQuery = "SELECT COALESCE(SUM(order_total), 0) as total 
                        FROM {$this->orderTable} 
                        WHERE order_status != 3";
        $revenue = $this->getOne($revenueQuery);
        $totalRevenue = is_object($revenue) ? $revenue->total : ($revenue['total'] ?? 0);
        
        // Tổng đơn hàng
        $ordersQuery = "SELECT COUNT(*) as total FROM {$this->orderTable}";
        $orders = $this->getOne($ordersQuery);
        $totalOrders = is_object($orders) ? $orders->total : ($orders['total'] ?? 0);
        
        // Đơn hàng chờ xử lý
        $pendingQuery = "SELECT COUNT(*) as total 
                        FROM {$this->orderTable} 
                        WHERE order_status = 0";
        $pending = $this->getOne($pendingQuery);
        $pendingOrders = is_object($pending) ? $pending->total : ($pending['total'] ?? 0);
        
        // Sản phẩm đã bán
        $productQuery = "SELECT COALESCE(SUM(oi.sanpham_soluong), 0) as total
                        FROM {$this->orderItemTable} oi
                        INNER JOIN {$this->orderTable} o ON oi.order_id = o.order_id
                        WHERE o.order_status != 3";
        $products = $this->getOne($productQuery);
        $totalProducts = is_object($products) ? $products->total : ($products['total'] ?? 0);
        
        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'total_products_sold' => $totalProducts
        ];
    }
}
