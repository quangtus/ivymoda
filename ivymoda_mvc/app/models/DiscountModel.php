<?php
/**
 * DiscountModel - Xử lý mã giảm giá (UC2.6)
 * Bảng: tbl_ma_giam_gia
 */
class DiscountModel extends Model {
    protected $table = 'tbl_ma_giam_gia';
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Lấy tất cả mã giảm giá
     */
    public function getAllDiscounts() {
        $query = "SELECT * FROM {$this->table} ORDER BY ma_batdau DESC";
        return $this->getAll($query);
    }
    
    /**
     * Lấy mã giảm giá đang hoạt động
     */
    public function getActiveDiscounts() {
        $now = date('Y-m-d H:i:s');
        $query = "SELECT * FROM {$this->table} 
                  WHERE ma_trangthai = 1 
                  AND ma_batdau <= '$now' 
                  AND ma_ketthuc >= '$now'
                  AND (ma_soluong IS NULL OR ma_dadung < ma_soluong)
                  ORDER BY ma_batdau DESC";
        return $this->getAll($query);
    }
    
    /**
     * Lấy mã giảm giá theo code
     */
    public function getDiscountByCode($code) {
        $code = $this->escape($code);
        $query = "SELECT * FROM {$this->table} WHERE ma_code = '$code'";
        return $this->getOne($query);
    }
    
    /**
     * Kiểm tra mã giảm giá có hợp lệ không
     * @param string $code Mã giảm giá
     * @param float $orderTotal Tổng giá trị đơn hàng
     * @return array ['valid' => true/false, 'message' => 'thông báo', 'discount' => object]
     */
    public function validateDiscount($code, $orderTotal) {
        $discount = $this->getDiscountByCode($code);
        
        if (!$discount) {
            return ['valid' => false, 'message' => 'Mã giảm giá không tồn tại'];
        }
        
        // Chuyển đổi object/array về dạng chuẩn
        $ma_trangthai = is_object($discount) ? $discount->ma_trangthai : $discount['ma_trangthai'];
        $ma_batdau = is_object($discount) ? $discount->ma_batdau : $discount['ma_batdau'];
        $ma_ketthuc = is_object($discount) ? $discount->ma_ketthuc : $discount['ma_ketthuc'];
        $ma_dieukien = is_object($discount) ? $discount->ma_dieukien : $discount['ma_dieukien'];
        $ma_soluong = is_object($discount) ? $discount->ma_soluong : $discount['ma_soluong'];
        $ma_dadung = is_object($discount) ? $discount->ma_dadung : $discount['ma_dadung'];
        
        // Kiểm tra trạng thái
        if ($ma_trangthai != 1) {
            return ['valid' => false, 'message' => 'Mã giảm giá đã bị vô hiệu hóa'];
        }
        
        // Kiểm tra thời gian
        $now = date('Y-m-d H:i:s');
        if ($now < $ma_batdau) {
            return ['valid' => false, 'message' => 'Mã giảm giá chưa có hiệu lực'];
        }
        if ($now > $ma_ketthuc) {
            return ['valid' => false, 'message' => 'Mã giảm giá đã hết hạn'];
        }
        
        // Kiểm tra điều kiện đơn hàng tối thiểu
        if ($orderTotal < $ma_dieukien) {
            return ['valid' => false, 'message' => 'Đơn hàng chưa đủ điều kiện (tối thiểu ' . number_format($ma_dieukien) . 'đ)'];
        }
        
        // Kiểm tra số lượng
        if ($ma_soluong !== null && $ma_dadung >= $ma_soluong) {
            return ['valid' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng'];
        }
        
        return ['valid' => true, 'message' => 'Mã giảm giá hợp lệ', 'discount' => $discount];
    }
    
    /**
     * Tính số tiền giảm giá
     * @param object $discount Thông tin mã giảm giá
     * @param float $orderTotal Tổng giá trị đơn hàng
     * @return float Số tiền được giảm
     */
    public function calculateDiscount($discount, $orderTotal) {
        $ma_loai = is_object($discount) ? $discount->ma_loai : $discount['ma_loai'];
        $ma_giatri = is_object($discount) ? $discount->ma_giatri : $discount['ma_giatri'];
        
        if ($ma_loai == 1) {
            // Giảm theo phần trăm
            return ($orderTotal * $ma_giatri) / 100;
        } else {
            // Giảm số tiền cố định
            return min($ma_giatri, $orderTotal);
        }
    }
    
    /**
     * Tăng số lần sử dụng mã giảm giá
     */
    public function incrementUsage($code) {
        $code = $this->escape($code);
        $query = "UPDATE {$this->table} SET ma_dadung = ma_dadung + 1 WHERE ma_code = '$code'";
        return $this->execute($query);
    }
    
    /**
     * Thêm mã giảm giá mới
     */
    public function addDiscount($data) {
        $code = $this->escape($data['ma_code']);
        $loai = (int)$data['ma_loai'];
        $giatri = (float)$data['ma_giatri'];
        $dieukien = (float)($data['ma_dieukien'] ?? 0);
        $batdau = $this->escape($data['ma_batdau']);
        $ketthuc = $this->escape($data['ma_ketthuc']);
        $soluong = isset($data['ma_soluong']) && $data['ma_soluong'] !== '' ? (int)$data['ma_soluong'] : 'NULL';
        $trangthai = (int)($data['ma_trangthai'] ?? 1);
        
        // Kiểm tra mã đã tồn tại chưa
        $check = "SELECT * FROM {$this->table} WHERE ma_code = '$code'";
        if ($this->getOne($check)) {
            return "Mã giảm giá đã tồn tại";
        }
        
        $query = "INSERT INTO {$this->table} 
                  (ma_code, ma_loai, ma_giatri, ma_dieukien, ma_batdau, ma_ketthuc, ma_soluong, ma_trangthai) 
                  VALUES ('$code', $loai, $giatri, $dieukien, '$batdau', '$ketthuc', $soluong, $trangthai)";
        
        if ($this->execute($query)) {
            return true;
        } else {
            return "Thêm mã giảm giá thất bại";
        }
    }
    
    /**
     * Cập nhật mã giảm giá
     */
    public function updateDiscount($id, $data) {
        $id = (int)$id;
        $loai = (int)$data['ma_loai'];
        $giatri = (float)$data['ma_giatri'];
        $dieukien = (float)($data['ma_dieukien'] ?? 0);
        $batdau = $this->escape($data['ma_batdau']);
        $ketthuc = $this->escape($data['ma_ketthuc']);
        $soluong = isset($data['ma_soluong']) && $data['ma_soluong'] !== '' ? (int)$data['ma_soluong'] : 'NULL';
        $trangthai = (int)($data['ma_trangthai'] ?? 1);
        
        $query = "UPDATE {$this->table} SET 
                  ma_loai = $loai,
                  ma_giatri = $giatri,
                  ma_dieukien = $dieukien,
                  ma_batdau = '$batdau',
                  ma_ketthuc = '$ketthuc',
                  ma_soluong = $soluong,
                  ma_trangthai = $trangthai
                  WHERE ma_id = $id";
        
        if ($this->execute($query)) {
            return true;
        } else {
            return "Cập nhật mã giảm giá thất bại";
        }
    }
    
    /**
     * Xóa mã giảm giá
     */
    public function deleteDiscount($id) {
        $id = (int)$id;
        $query = "DELETE FROM {$this->table} WHERE ma_id = $id";
        
        if ($this->execute($query)) {
            return true;
        } else {
            return "Xóa mã giảm giá thất bại";
        }
    }
    
    /**
     * Lấy mã giảm giá theo ID
     */
    public function getDiscountById($id) {
        $id = (int)$id;
        $query = "SELECT * FROM {$this->table} WHERE ma_id = $id";
        return $this->getOne($query);
    }
}
