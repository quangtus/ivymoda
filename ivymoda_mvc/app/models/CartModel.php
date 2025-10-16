<?php
/**
 * CartModel - Quản lý giỏ hàng (MIGRATED TO VARIANT SYSTEM)
 * 
 * Đã cập nhật để tương thích với tbl_product_variant
 * Giỏ hàng lưu variant_id thay vì product_id + size + color strings
 * 
 * @version 2.0 - Variant System Compatible
 */

class CartModel extends Model {
    
    /**
     * Thêm sản phẩm vào giỏ hàng
     * 
     * @param string $sessionId Session ID của khách hàng
     * @param int|null $userId User ID (null nếu chưa đăng nhập)
     * @param int $variantId ID của variant (size + color cụ thể)
     * @param int $quantity Số lượng
     * @return bool Success or failure
     * 
     * Ví dụ: addToCart('abc123', 1, 15, 2)
     * → Thêm 2 sản phẩm variant_id=15 vào giỏ
     */
    public function addToCart($sessionId, $userId, $variantId, $quantity = 1) {
        try {
            // Validate input
            if (!$sessionId || !$variantId || $quantity <= 0) {
                error_log("CartModel::addToCart - Invalid params: sessionId=$sessionId, variantId=$variantId, quantity=$quantity");
                return false;
            }
            
            // Kiểm tra variant có tồn tại không
            $checkSql = "SELECT ton_kho, trang_thai FROM tbl_product_variant WHERE variant_id = ?";
            $variantInfo = $this->getOne($checkSql, [$variantId]);
            
            if (!is_object($variantInfo)) {
                error_log("CartModel::addToCart - Variant not found: variant_id=$variantId");
                return false;
            }
            
            $variant = $variantInfo;
            
            // Kiểm tra tồn kho
            if ($variant->trang_thai != 1 || $variant->ton_kho < $quantity) {
                error_log("CartModel::addToCart - Insufficient stock: variant_id=$variantId, requested=$quantity, available={$variant->ton_kho}");
                return false;
            }
            
            // Kiểm tra xem variant đã có trong giỏ chưa
            $checkCartSql = "SELECT cart_id, quantity FROM tbl_cart 
                            WHERE session_id = ? AND variant_id = ?";
            $existingCart = $this->getOne($checkCartSql, [$sessionId, $variantId]);
            
            if (is_object($existingCart)) {
                // Đã có trong giỏ → cập nhật số lượng
                $cartItem = $existingCart;
                $newQuantity = $cartItem->quantity + $quantity;
                
                // Validate lại tồn kho với số lượng mới
                if ($newQuantity > $variant->ton_kho) {
                    error_log("CartModel::addToCart - Total quantity exceeds stock: variant_id=$variantId, new_total=$newQuantity, available={$variant->ton_kho}");
                    return false;
                }
                
                $updateSql = "UPDATE tbl_cart SET quantity = ? WHERE cart_id = ?";
                return $this->execute($updateSql, [$newQuantity, $cartItem->cart_id]);
            } else {
                // Chưa có trong giỏ → thêm mới
                $insertSql = "INSERT INTO tbl_cart (session_id, user_id, variant_id, quantity, created_at) 
                             VALUES (?, ?, ?, ?, NOW())";
                return $this->execute($insertSql, [$sessionId, $userId, $variantId, $quantity]);
            }
        } catch (Exception $e) {
            error_log("CartModel::addToCart - Exception: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy danh sách sản phẩm trong giỏ hàng (JOIN đầy đủ thông tin)
     * 
     * @param string $sessionId Session ID
     * @param int|null $userId User ID (optional)
     * @return array Mảng các cart items với thông tin đầy đủ
     * 
     * Trả về:
     * - cart_id: ID giỏ hàng
     * - variant_id: ID variant
     * - quantity: Số lượng trong giỏ
     * - sku: Mã SKU
     * - ton_kho: Tồn kho hiện tại
     * - gia_ban: Giá riêng của variant (nếu có)
     * - size_ten: Tên size (XS, S, M, L...)
     * - color_ten: Tên màu
     * - color_ma: Mã màu hex
     * - sanpham_id: ID sản phẩm
     * - sanpham_tieude: Tên sản phẩm
     * - sanpham_gia: Giá gốc sản phẩm
     * - sanpham_anh: Ảnh đại diện
     * - trang_thai: Trạng thái variant
     */
    public function getCartItems($sessionId, $userId = null) {
        try {
            $sql = "SELECT 
                        c.cart_id,
                        c.variant_id,
                        c.quantity,
                        v.sku,
                        v.ton_kho,
                        v.gia_ban,
                        v.trang_thai,
                        s.size_ten,
                        co.color_ten,
                        co.color_ma,
                        p.sanpham_id,
                        p.sanpham_tieude,
                        p.sanpham_gia,
                        p.sanpham_gia_goc,
                        p.sanpham_anh,
                        COALESCE(v.gia_ban, p.sanpham_gia) as gia_hien_tai
                    FROM tbl_cart c
                    JOIN tbl_product_variant v ON c.variant_id = v.variant_id
                    JOIN tbl_size s ON v.size_id = s.size_id
                    JOIN tbl_color co ON v.color_id = co.color_id
                    JOIN tbl_sanpham p ON v.sanpham_id = p.sanpham_id";
            
            $params = [];
            
            // Đăng nhập: lọc theo user_id; Khách: lọc theo session_id
            if ($userId) {
                $sql .= " WHERE c.user_id = ?";
                $params[] = $userId;
            } else {
                $sql .= " WHERE c.session_id = ?";
                $params[] = $sessionId;
            }
            
            $sql .= " ORDER BY c.created_at DESC";
            
            $cartItems = $this->getAll($sql, $params);
            
            // Validate tồn kho cho từng item
            if (!empty($cartItems)) {
                foreach ($cartItems as &$item) {
                    // Đánh dấu nếu số lượng trong giỏ vượt quá tồn kho
                    $item->is_valid = ($item->trang_thai == 1 && $item->ton_kho >= $item->quantity);
                    $item->max_available = min($item->quantity, $item->ton_kho);
                }
            }
            
            return $cartItems ?? [];
        } catch (Exception $e) {
            error_log("CartModel::getCartItems - Exception: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     * 
     * @param int $cartId ID của cart item
     * @param int $quantity Số lượng mới
     * @return bool Success or failure
     */
    public function updateQuantity($cartId, $quantity) {
        try {
            if ($quantity <= 0) {
                // Nếu quantity = 0, xóa luôn
                return $this->removeItem($cartId);
            }
            
            // Lấy thông tin cart item và variant
            $sql = "SELECT c.variant_id, v.ton_kho, v.trang_thai
                    FROM tbl_cart c
                    JOIN tbl_product_variant v ON c.variant_id = v.variant_id
                    WHERE c.cart_id = ?";
            $result = $this->getOne($sql, [$cartId]);
            
            if (!is_object($result)) {
                error_log("CartModel::updateQuantity - Cart item not found: cart_id=$cartId");
                return false;
            }
            
            $item = $result;
            
            // Validate tồn kho
            if ($item->trang_thai != 1 || $quantity > $item->ton_kho) {
                error_log("CartModel::updateQuantity - Insufficient stock: cart_id=$cartId, requested=$quantity, available={$item->ton_kho}");
                return false;
            }
            
            // Cập nhật số lượng
            $updateSql = "UPDATE tbl_cart SET quantity = ? WHERE cart_id = ?";
            return $this->execute($updateSql, [$quantity, $cartId]);
        } catch (Exception $e) {
            error_log("CartModel::updateQuantity - Exception: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Xóa một sản phẩm khỏi giỏ hàng (dùng cho AJAX)
     * 
     * @param int $cartId ID của cart item
     * @return bool Success or failure
     */
    public function removeItem($cartId) {
        try {
            // Kiểm tra cart item có tồn tại không
            $checkSql = "SELECT cart_id FROM tbl_cart WHERE cart_id = ?";
            $exists = $this->getOne($checkSql, [$cartId]);
            
            if (!is_object($exists)) {
                error_log("CartModel::removeItem - Cart item not found: cart_id=$cartId");
                return false;
            }
            
            $sql = "DELETE FROM tbl_cart WHERE cart_id = ?";
            return $this->execute($sql, [$cartId]);
        } catch (Exception $e) {
            error_log("CartModel::removeItem - Exception: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Xóa toàn bộ giỏ hàng (dùng sau khi checkout thành công)
     * 
     * @param string $sessionId Session ID
     * @param int|null $userId User ID (optional)
     * @return bool Success or failure
     */
    public function clearCart($sessionId, $userId = null) {
        try {
            $sql = "DELETE FROM tbl_cart WHERE session_id = ?";
            $params = [$sessionId];
            
            if ($userId) {
                $sql .= " OR user_id = ?";
                $params[] = $userId;
            }
            
            return $this->execute($sql, $params);
        } catch (Exception $e) {
            error_log("CartModel::clearCart - Exception: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Đếm số lượng items trong giỏ hàng
     * 
     * @param string $sessionId Session ID
     * @param int|null $userId User ID (optional)
     * @return int Tổng số items
     */
    public function getCartCount($sessionId, $userId = null) {
        try {
            if ($userId) {
                $sql = "SELECT COUNT(*) as total FROM tbl_cart WHERE user_id = ?";
                $params = [$userId];
            } else {
                $sql = "SELECT COUNT(*) as total FROM tbl_cart WHERE session_id = ?";
                $params = [$sessionId];
            }
            
            $result = $this->getOne($sql, $params);
            if (!is_object($result) || !isset($result->total)) {
                return 0;
            }
            return (int)$result->total;
        } catch (Exception $e) {
            error_log("CartModel::getCartCount - Exception: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Tính tổng giá trị giỏ hàng
     * 
     * @param string $sessionId Session ID
     * @param int|null $userId User ID (optional)
     * @return float Tổng tiền
     */
    public function getCartTotal($sessionId, $userId = null) {
        try {
            $sql = "SELECT 
                        SUM(c.quantity * COALESCE(v.gia_ban, p.sanpham_gia)) as total
                    FROM tbl_cart c
                    JOIN tbl_product_variant v ON c.variant_id = v.variant_id
                    JOIN tbl_sanpham p ON v.sanpham_id = p.sanpham_id";
            if ($userId) {
                $sql .= " WHERE c.user_id = ?";
                $params = [$userId];
            } else {
                $sql .= " WHERE c.session_id = ?";
                $params = [$sessionId];
            }
            
            $result = $this->getOne($sql, $params);
            if (!is_object($result) || empty($result->total)) {
                return 0;
            }
            return (float)$result->total;
        } catch (Exception $e) {
            error_log("CartModel::getCartTotal - Exception: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Sync giỏ hàng từ session sang user khi đăng nhập
     * 
     * @param string $sessionId Session ID
     * @param int $userId User ID
     * @return bool Success or failure
     */
    public function syncCartToUser($sessionId, $userId) {
        try {
            // Cập nhật tất cả cart items từ session_id sang user_id
            $sql = "UPDATE tbl_cart SET user_id = ? WHERE session_id = ? AND user_id IS NULL";
            return $this->execute($sql, [$userId, $sessionId]);
        } catch (Exception $e) {
            error_log("CartModel::syncCartToUser - Exception: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validate tất cả items trong giỏ hàng trước khi checkout
     * 
     * @param string $sessionId Session ID
     * @param int|null $userId User ID (optional)
     * @return array ['valid' => bool, 'errors' => array, 'items' => array]
     */
    public function validateCartForCheckout($sessionId, $userId = null) {
        $cartItems = $this->getCartItems($sessionId, $userId);
        $errors = [];
        $allValid = true;
        
        if (empty($cartItems)) {
            return [
                'valid' => false,
                'errors' => ['Giỏ hàng trống'],
                'items' => []
            ];
        }
        
        foreach ($cartItems as $item) {
            // Kiểm tra trạng thái
            if ($item->trang_thai != 1) {
                $errors[] = "Sản phẩm '{$item->sanpham_tieude}' ({$item->color_ten}, {$item->size_ten}) đã ngừng kinh doanh";
                $allValid = false;
                continue;
            }
            
            // Kiểm tra tồn kho
            if ($item->ton_kho < $item->quantity) {
                $errors[] = "Sản phẩm '{$item->sanpham_tieude}' ({$item->color_ten}, {$item->size_ten}) chỉ còn {$item->ton_kho} sản phẩm (bạn đang chọn {$item->quantity})";
                $allValid = false;
            }
        }
        
        return [
            'valid' => $allValid,
            'errors' => $errors,
            'items' => $cartItems
        ];
    }
    
    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function updateCartQuantity($cartId, $quantity, $sessionId, $userId = null) {
        try {
            // Validate input
            if (!$cartId || $quantity <= 0) {
                return false;
            }
            
            // Kiểm tra cart item có tồn tại không
            $checkSql = "SELECT c.cart_id, c.variant_id, v.ton_kho, v.trang_thai 
                        FROM tbl_cart c 
                        JOIN tbl_product_variant v ON c.variant_id = v.variant_id 
                        WHERE c.cart_id = ?";
            $params = [$cartId];
            
            if ($userId) {
                $checkSql .= " AND c.user_id = ?";
                $params[] = $userId;
            } else {
                $checkSql .= " AND c.session_id = ?";
                $params[] = $sessionId;
            }
            
            $cartItem = $this->getOne($checkSql, $params);
            
            if (!is_object($cartItem)) {
                return false;
            }
            
            // Kiểm tra tồn kho - CHỈ kiểm tra nếu variant còn hoạt động
            if ($cartItem->trang_thai != 1) {
                error_log("CartModel::updateCartQuantity - Variant not active: cart_id=$cartId, trang_thai={$cartItem->trang_thai}");
                return false;
            }
            
            // Kiểm tra tồn kho có đủ không
            if ($cartItem->ton_kho < $quantity) {
                error_log("CartModel::updateCartQuantity - Insufficient stock: cart_id=$cartId, requested=$quantity, available={$cartItem->ton_kho}");
                return false;
            }
            
            // Cập nhật số lượng
            $updateSql = "UPDATE tbl_cart SET quantity = ? WHERE cart_id = ?";
            return $this->execute($updateSql, [$quantity, $cartId]);
            
        } catch (Exception $e) {
            error_log("CartModel::updateCartQuantity - Exception: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function removeFromCart($cartId, $sessionId, $userId = null) {
        try {
            // Debug logging
            error_log("CartModel::removeFromCart - Starting: cart_id=$cartId, session_id=$sessionId, user_id=$userId");
            
            // Kiểm tra cart item có tồn tại và thuộc về session/user này không
            if ($userId) {
                $checkSql = "SELECT cart_id FROM tbl_cart WHERE cart_id = ? AND user_id = ?";
                $checkParams = [$cartId, $userId];
            } else {
                $checkSql = "SELECT cart_id FROM tbl_cart WHERE cart_id = ? AND session_id = ?";
                $checkParams = [$cartId, $sessionId];
            }
            
            $exists = $this->getOne($checkSql, $checkParams);
            if (!is_object($exists)) {
                error_log("CartModel::removeFromCart - Cart item not found or not authorized: cart_id=$cartId, session_id=$sessionId, user_id=$userId");
                return false;
            }
            
            // Xóa item
            $sql = "DELETE FROM tbl_cart WHERE cart_id = ?";
            $result = $this->execute($sql, [$cartId]);
            
            error_log("CartModel::removeFromCart - Delete result: " . ($result ? 'success' : 'failed'));
            return $result;
        } catch (Exception $e) {
            error_log("CartModel::removeFromCart - Exception: " . $e->getMessage());
            return false;
        }
    }
    
}
