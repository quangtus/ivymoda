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
            $sql = "SELECT * FROM {$this->table} WHERE ma_code = ? AND ma_trangthai = 1";
            $result = $this->getOne($sql, [$code]);
            
            if (!$result) {
                return null;
            }
            
            // Kiểm tra thời gian hiệu lực
            $now = date('Y-m-d H:i:s');
            if ($now < $result->ma_batdau || $now > $result->ma_ketthuc) {
                return null;
            }
            
            // Kiểm tra số lượng còn lại
            if ($result->ma_soluong !== null && $result->ma_dadung >= $result->ma_soluong) {
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
            
            // Kiểm tra điều kiện đơn hàng tối thiểu
            if ($orderTotal < $discount->ma_dieukien) {
                return [
                    'valid' => false,
                    'message' => 'Đơn hàng tối thiểu ' . number_format($discount->ma_dieukien, 0, ',', '.') . ' ₫ để sử dụng mã này'
                ];
            }
            
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
            if ($discount->ma_loai == 1) {
                // Giảm theo phần trăm
                $discountValue = ($orderTotal * $discount->ma_giatri) / 100;
            } else {
                // Giảm theo số tiền cố định
                $discountValue = $discount->ma_giatri;
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
            $sql = "UPDATE {$this->table} SET ma_dadung = ma_dadung + 1 WHERE ma_code = ?";
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
            $sql = "SELECT * FROM {$this->table} ORDER BY ma_batdau DESC LIMIT ? OFFSET ?";
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
                    (ma_code, ma_loai, ma_giatri, ma_dieukien, ma_batdau, ma_ketthuc, ma_soluong, ma_trangthai) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            return $this->execute($sql, [
                $data['ma_code'],
                $data['ma_loai'],
                $data['ma_giatri'],
                $data['ma_dieukien'],
                $data['ma_batdau'],
                $data['ma_ketthuc'],
                $data['ma_soluong'],
                $data['ma_trangthai']
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
            if (isset($data['ma_loai'])) { $fields[] = "ma_loai = ?"; $params[] = $data['ma_loai']; }
            if (isset($data['ma_giatri'])) { $fields[] = "ma_giatri = ?"; $params[] = $data['ma_giatri']; }
            if (isset($data['ma_dieukien'])) { $fields[] = "ma_dieukien = ?"; $params[] = $data['ma_dieukien']; }
            if (isset($data['ma_batdau'])) { $fields[] = "ma_batdau = ?"; $params[] = $data['ma_batdau']; }
            if (isset($data['ma_ketthuc'])) { $fields[] = "ma_ketthuc = ?"; $params[] = $data['ma_ketthuc']; }
            if (isset($data['ma_soluong'])) { $fields[] = "ma_soluong = ?"; $params[] = $data['ma_soluong']; }
            if (isset($data['ma_trangthai'])) { $fields[] = "ma_trangthai = ?"; $params[] = $data['ma_trangthai']; }
            
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