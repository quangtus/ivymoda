<?php
/**
 * DiscountModel - Quản lý mã giảm giá
 * 
 * Tương thích với tbl_ma_giam_gia trong database
 */

class DiscountModel extends Model {
    protected $table = 'tbl_ma_giam_gia';
    
    /**
     * Lấy mã giảm giá theo code
     */
    public function getDiscountByCode($code) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE ma_code = ? AND trang_thai = 1";
            $result = $this->getOne($sql, [$code]);
            
            if (!$result) {
                return null;
            }
            
            // Kiểm tra thời gian hiệu lực
            $now = date('Y-m-d H:i:s');
            if ($now < $result->ngay_bat_dau || $now > $result->ngay_ket_thuc) {
                return null;
            }
            
            // Kiểm tra số lượng còn lại
            if ($result->so_luong !== null && $result->da_su_dung >= $result->so_luong) {
                return null;
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("DiscountModel::getDiscountByCode - Exception: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Validate mã giảm giá
     */
    public function validateDiscount($code, $orderTotal) {
        try {
            $discount = $this->getDiscountByCode($code);
            
            if (!$discount) {
                return [
                    'valid' => false,
                    'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn'
                ];
            }
            
            // Database final không còn trường điều kiện tối thiểu
            
            return [
                'valid' => true,
                'discount' => $discount,
                'message' => 'Mã giảm giá hợp lệ'
            ];
        } catch (Exception $e) {
            error_log("DiscountModel::validateDiscount - Exception: " . $e->getMessage());
            return [
                'valid' => false,
                'message' => 'Lỗi hệ thống khi kiểm tra mã giảm giá'
            ];
        }
    }
    
    /**
     * Tính giá trị giảm giá
     */
    public function calculateDiscountValue($discount, $orderTotal) {
        try {
            $type = is_object($discount) ? $discount->loai_giam : ($discount['loai_giam'] ?? 'percent');
            $value = is_object($discount) ? $discount->ma_giam : ($discount['ma_giam'] ?? 0);
            
            if ($type === 'percent') {
                $discountValue = ($orderTotal * (float)$value) / 100;
            } else {
                $discountValue = (float)$value;
            }
            
            // Đảm bảo không giảm quá tổng tiền đơn hàng
            return min($discountValue, $orderTotal);
        } catch (Exception $e) {
            error_log("DiscountModel::calculateDiscountValue - Exception: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Sử dụng mã giảm giá (tăng số lần đã sử dụng)
     */
    public function useDiscount($code) {
        try {
            $sql = "UPDATE {$this->table} SET da_su_dung = da_su_dung + 1 WHERE ma_code = ?";
            return $this->execute($sql, [$code]);
        } catch (Exception $e) {
            error_log("DiscountModel::useDiscount - Exception: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy tất cả mã giảm giá (admin)
     */
    public function getAllDiscounts($page = 1, $limit = 20) {
        try {
            $offset = ($page - 1) * $limit;
            $sql = "SELECT * FROM {$this->table} ORDER BY ngay_bat_dau DESC LIMIT ? OFFSET ?";
            return $this->getAll($sql, [$limit, $offset]);
        } catch (Exception $e) {
            error_log("DiscountModel::getAllDiscounts - Exception: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Tạo mã giảm giá mới (admin)
     */
    public function createDiscount($data) {
        try {
            $sql = "INSERT INTO {$this->table} 
                    (ma_code, ma_ten, ma_giam, loai_giam, ngay_bat_dau, ngay_ket_thuc, so_luong, trang_thai) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            return $this->execute($sql, [
                $data['ma_code'],
                $data['ma_ten'],
                $data['ma_giam'],
                $data['loai_giam'],
                $data['ngay_bat_dau'],
                $data['ngay_ket_thuc'],
                $data['so_luong'],
                $data['trang_thai']
            ]);
        } catch (Exception $e) {
            error_log("DiscountModel::createDiscount - Exception: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cập nhật mã giảm giá (admin)
     */
    public function updateDiscount($id, $data) {
        try {
            $fields = [];
            $params = [];
            
            if (isset($data['ma_code'])) { $fields[] = "ma_code = ?"; $params[] = $data['ma_code']; }
            if (isset($data['ma_ten'])) { $fields[] = "ma_ten = ?"; $params[] = $data['ma_ten']; }
            if (isset($data['ma_giam'])) { $fields[] = "ma_giam = ?"; $params[] = $data['ma_giam']; }
            if (isset($data['loai_giam'])) { $fields[] = "loai_giam = ?"; $params[] = $data['loai_giam']; }
            if (isset($data['ngay_bat_dau'])) { $fields[] = "ngay_bat_dau = ?"; $params[] = $data['ngay_bat_dau']; }
            if (isset($data['ngay_ket_thuc'])) { $fields[] = "ngay_ket_thuc = ?"; $params[] = $data['ngay_ket_thuc']; }
            if (isset($data['so_luong'])) { $fields[] = "so_luong = ?"; $params[] = $data['so_luong']; }
            if (isset($data['trang_thai'])) { $fields[] = "trang_thai = ?"; $params[] = $data['trang_thai']; }
            
            if (empty($fields)) {
                return false;
            }
            
            $params[] = $id;
            $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE ma_id = ?";
            
            return $this->execute($sql, $params);
        } catch (Exception $e) {
            error_log("DiscountModel::updateDiscount - Exception: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Xóa mã giảm giá (admin)
     */
    public function deleteDiscount($id) {
        try {
            $sql = "DELETE FROM {$this->table} WHERE ma_id = ?";
            return $this->execute($sql, [$id]);
        } catch (Exception $e) {
            error_log("DiscountModel::deleteDiscount - Exception: " . $e->getMessage());
            return false;
        }
    }
}