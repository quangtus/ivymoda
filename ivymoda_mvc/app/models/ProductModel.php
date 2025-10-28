<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\models\ProductModel.php

class ProductModel extends Model {
    protected $table = 'tbl_sanpham';
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Lấy tất cả sản phẩm với phân trang
     */
    public function getAllProductsWithPagination($limit = 10, $offset = 0) {
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        $query = "SELECT p.*, c.danhmuc_ten, b.loaisanpham_ten,
                  COALESCE(
                      (SELECT ap.anh_path FROM tbl_anhsanpham ap 
                       WHERE ap.sanpham_id = p.sanpham_id 
                       ORDER BY ap.is_primary DESC, ap.anh_id ASC LIMIT 1),
                      p.sanpham_anh
                  ) as first_image
                  FROM {$this->table} p
                  LEFT JOIN tbl_danhmuc c ON p.danhmuc_id = c.danhmuc_id
                  LEFT JOIN tbl_loaisanpham b ON p.loaisanpham_id = b.loaisanpham_id
                  ORDER BY p.sanpham_id DESC
                  LIMIT $offset, $limit";
        
        return $this->getAll($query);
    }
    
    /**
     * Lấy tổng số sản phẩm
     */
    public function getTotalProducts() {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->getOne($query);
        
        if ($result) {
            if (is_object($result) && isset($result->total)) {
                return $result->total;
            } elseif (is_array($result) && isset($result['total'])) {
                return $result['total'];
            }
        }
        
        return 0;
    }
    
    /**
     * Lấy danh sách ảnh của sản phẩm
     * @param int $productId ID sản phẩm
     * @param int|null $colorId ID màu (tùy chọn)
     * @param bool $includeDefault Có bao gồm ảnh mặc định nếu không có ảnh theo màu không
     * @return array Danh sách ảnh sản phẩm
     */
    public function getProductImages($productId, $colorId = null, $includeDefault = true) {
        $params = [$productId];
        $query = "SELECT ap.*, sc.color_id, c.color_ten, c.color_ma 
                  FROM tbl_anhsanpham ap
                  LEFT JOIN tbl_sanpham_color sc ON ap.sanpham_color_id = sc.sanpham_color_id
                  LEFT JOIN tbl_color c ON sc.color_id = c.color_id
                  WHERE ap.sanpham_id = ?";
        
        // Lọc theo màu nếu được chỉ định
        if ($colorId !== null) {
            $query .= " AND sc.color_id = ?";
            $params[] = (int)$colorId;
        }
        
        $query .= " ORDER BY ap.is_primary DESC, ap.anh_id ASC";
        
        $images = $this->getAll($query, $params);
        
        // Nếu không có ảnh và yêu cầu ảnh mặc định
        if (empty($images) && $includeDefault) {
            // Lấy thông tin sản phẩm để lấy ảnh chính
            $product = $this->getOne("SELECT sanpham_anh FROM tbl_sanpham WHERE sanpham_id = ?", [$productId]);
            
            // getOne() có thể trả về object hoặc array tùy thuộc vào cấu hình PDO::FETCH_OBJ hoặc PDO::FETCH_ASSOC
            if ($product) {
                $sanpham_anh = is_object($product) ? $product->sanpham_anh : $product['sanpham_anh'];
                if (!empty($sanpham_anh)) {
                    $images = [
                        (object)[
                            'anh_id' => 0,
                            'sanpham_id' => $productId,
                            'anh_path' => $sanpham_anh,
                            'is_primary' => 1,
                            'color_id' => null,
                            'color_ten' => null,
                            'color_ma' => null
                        ]
                    ];
                }
            }
        }
        
        return $images;
    }
    
    /**
     * Lấy ảnh chính của sản phẩm
     */
    public function getPrimaryProductImage($productId) {
        $query = "SELECT * FROM tbl_anhsanpham 
                  WHERE sanpham_id = ? AND is_primary = 1 
                  LIMIT 1";
        
        return $this->getOne($query, [$productId]);
    }
    
    /**
     * Thêm ảnh sản phẩm
     */
    public function addProductImage($productId, $imagePath, $isPrimary = 0, $sanphamColorId = null) {
        $query = "INSERT INTO tbl_anhsanpham (sanpham_id, sanpham_color_id, anh_path, is_primary) 
                  VALUES (?, ?, ?, ?)";
        
        return $this->db->query($query, [$productId, $sanphamColorId, $imagePath, $isPrimary]);
    }

    /**
     * Lấy danh sách màu có ảnh cho sản phẩm
     */
    public function getProductAvailableColors($productId) {
        $query = "SELECT DISTINCT c.color_id, c.color_ten, c.color_ma
                  FROM tbl_anhsanpham ap
                  INNER JOIN tbl_sanpham_color sc ON ap.sanpham_color_id = sc.sanpham_color_id
                  INNER JOIN tbl_color c ON sc.color_id = c.color_id
                  WHERE ap.sanpham_id = ?
                  ORDER BY c.color_ten";
        return $this->getAll($query, [$productId]);
    }

    /**
     * Lấy danh sách màu được gán cho sản phẩm (từ bảng liên kết)
     */
    public function getProductColors(int $productId) {
        $this->ensureProductColorPivot();
        $query = "SELECT c.color_id, c.color_ten, c.color_ma
                  FROM tbl_sanpham_color spc
                  INNER JOIN tbl_color c ON spc.color_id = c.color_id
                  WHERE spc.sanpham_id = ?
                  ORDER BY c.color_ten";
        return $this->getAll($query, [$productId]);
    }
    
    /**
     * Cập nhật ảnh chính của sản phẩm
     */
    public function setPrimaryImage($productId, $imageId) {
        try {
            // Bỏ primary của tất cả ảnh sản phẩm
            $query1 = "UPDATE tbl_anhsanpham SET is_primary = 0 WHERE sanpham_id = ?";
            $this->db->query($query1, [$productId]);
            
            // Set ảnh được chọn làm primary
            $query2 = "UPDATE tbl_anhsanpham SET is_primary = 1 WHERE anh_id = ? AND sanpham_id = ?";
            $result = $this->db->query($query2, [$imageId, $productId]);
            
            return $result !== false; // Return true if successful
        } catch (Exception $e) {
            error_log("setPrimaryImage error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Xóa ảnh sản phẩm
     */
    public function deleteProductImage($imageId) {
        $query = "DELETE FROM tbl_anhsanpham WHERE anh_id = ?";
        return $this->db->query($query, [$imageId]);
    }

    /**
     * Xóa tất cả ảnh theo sản phẩm và màu
     */
    public function deleteImagesByProductAndColor(int $productId, int $colorId) {
        $productId = (int)$productId;
        $colorId = (int)$colorId;
        
        // Phải xóa qua sanpham_color_id vì bảng tbl_anhsanpham không có trực tiếp color_id
        $query = "DELETE ap FROM tbl_anhsanpham ap
                  INNER JOIN tbl_sanpham_color sc ON ap.sanpham_color_id = sc.sanpham_color_id
                  WHERE sc.sanpham_id = ? AND sc.color_id = ?";
        
        return $this->db->query($query, [$productId, $colorId]);
    }
    
    /**
     * Lấy tất cả sản phẩm
     */
    public function getAllProducts() {
        $query = "SELECT p.*, c.danhmuc_ten, b.loaisanpham_ten,
                  COALESCE(
                      (SELECT ap.anh_path FROM tbl_anhsanpham ap 
                       WHERE ap.sanpham_id = p.sanpham_id 
                       ORDER BY ap.is_primary DESC, ap.anh_id ASC LIMIT 1),
                      p.sanpham_anh
                  ) as first_image
                  FROM {$this->table} p
                  LEFT JOIN tbl_danhmuc c ON p.danhmuc_id = c.danhmuc_id
                  LEFT JOIN tbl_loaisanpham b ON p.loaisanpham_id = b.loaisanpham_id
                  ORDER BY p.sanpham_id DESC";
        
        return $this->getAll($query);
    }
    
    /**
     * Lấy sản phẩm nổi bật cho frontend
     */
    public function getFeaturedProducts($limit = 8) {
        $query = "SELECT p.*, c.danhmuc_ten,
                  COALESCE(
                      (SELECT ap.anh_path FROM tbl_anhsanpham ap 
                       WHERE ap.sanpham_id = p.sanpham_id 
                       ORDER BY ap.is_primary DESC, ap.anh_id ASC LIMIT 1),
                      p.sanpham_anh
                  ) as first_image
                  FROM tbl_sanpham p 
                  LEFT JOIN tbl_danhmuc c ON p.danhmuc_id = c.danhmuc_id
                  WHERE p.sanpham_status = 1 
                  ORDER BY p.sanpham_id DESC 
                  LIMIT $limit";
                  
        return $this->getAll($query);
    }
    
    /**
     * Lấy sản phẩm theo danh mục
     */
    public function getProductsByCategory($category_id, $limit = 12, $offset = 0) {
        $category_id = (int)$category_id;
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        $query = "SELECT p.*, c.danhmuc_ten, b.loaisanpham_ten,
                  COALESCE(
                      (SELECT ap.anh_path FROM tbl_anhsanpham ap 
                       WHERE ap.sanpham_id = p.sanpham_id 
                       ORDER BY ap.is_primary DESC, ap.anh_id ASC LIMIT 1),
                      p.sanpham_anh
                  ) as first_image
                  FROM {$this->table} p
                  LEFT JOIN tbl_danhmuc c ON p.danhmuc_id = c.danhmuc_id
                  LEFT JOIN tbl_loaisanpham b ON p.loaisanpham_id = b.loaisanpham_id
                  WHERE p.danhmuc_id = $category_id
                  ORDER BY p.sanpham_id DESC
                  LIMIT $offset, $limit";
        
        return $this->getAll($query);
    }

    /**
     * Lấy sản phẩm theo bộ lọc trong một danh mục
     * $filters = [
     *   'subcategory_id' => int|null,
     *   'size_id' => int|null,
     *   'price_range' => string|null, // lt500 | 500-1000 | gt1000
     * ]
     */
    public function getFilteredProductsByCategory(int $categoryId, array $filters = [], int $limit = 12, int $offset = 0) {
        $categoryId = (int)$categoryId;
        $limit = (int)$limit;
        $offset = (int)$offset;

        // Base query
        $query = "SELECT DISTINCT p.*, c.danhmuc_ten, b.loaisanpham_ten,
                  COALESCE(
                      (SELECT ap.anh_path FROM tbl_anhsanpham ap 
                       WHERE ap.sanpham_id = p.sanpham_id 
                       ORDER BY ap.is_primary DESC, ap.anh_id ASC LIMIT 1),
                      p.sanpham_anh
                  ) as first_image
                  FROM {$this->table} p
                  LEFT JOIN tbl_danhmuc c ON p.danhmuc_id = c.danhmuc_id
                  LEFT JOIN tbl_loaisanpham b ON p.loaisanpham_id = b.loaisanpham_id";

        // Join variant if filter by size
        $needVariantJoin = isset($filters['size_id']) && $filters['size_id'];
        if ($needVariantJoin) {
            $sizeId = (int)$filters['size_id'];
            $query .= " INNER JOIN tbl_product_variant pv ON pv.sanpham_id = p.sanpham_id AND pv.trang_thai = 1 AND pv.ton_kho > 0 AND pv.size_id = $sizeId";
        }

        $query .= " WHERE p.danhmuc_id = $categoryId";

        // Subcategory filter
        if (!empty($filters['subcategory_id'])) {
            $subcategoryId = (int)$filters['subcategory_id'];
            $query .= " AND p.loaisanpham_id = $subcategoryId";
        }

        // Price filter
        if (!empty($filters['price_range'])) {
            $range = $this->escape($filters['price_range']);
            if ($range === 'lt500') {
                $query .= " AND CAST(p.sanpham_gia AS DECIMAL(10,2)) < 500000";
            } elseif ($range === '500-1000') {
                $query .= " AND CAST(p.sanpham_gia AS DECIMAL(10,2)) BETWEEN 500000 AND 1000000";
            } elseif ($range === 'gt1000') {
                $query .= " AND CAST(p.sanpham_gia AS DECIMAL(10,2)) > 1000000";
            }
        }

        $query .= " ORDER BY p.sanpham_id DESC LIMIT $offset, $limit";

        return $this->getAll($query);
    }

    /**
     * Đếm tổng số sản phẩm theo bộ lọc trong danh mục
     */
    public function countFilteredProductsByCategory(int $categoryId, array $filters = []) {
        $categoryId = (int)$categoryId;

        $query = "SELECT COUNT(DISTINCT p.sanpham_id) as total
                  FROM {$this->table} p
                  LEFT JOIN tbl_danhmuc c ON p.danhmuc_id = c.danhmuc_id
                  LEFT JOIN tbl_loaisanpham b ON p.loaisanpham_id = b.loaisanpham_id";

        $needVariantJoin = isset($filters['size_id']) && $filters['size_id'];
        if ($needVariantJoin) {
            $sizeId = (int)$filters['size_id'];
            $query .= " INNER JOIN tbl_product_variant pv ON pv.sanpham_id = p.sanpham_id AND pv.trang_thai = 1 AND pv.ton_kho > 0 AND pv.size_id = $sizeId";
        }

        $query .= " WHERE p.danhmuc_id = $categoryId";

        if (!empty($filters['subcategory_id'])) {
            $subcategoryId = (int)$filters['subcategory_id'];
            $query .= " AND p.loaisanpham_id = $subcategoryId";
        }

        if (!empty($filters['price_range'])) {
            $range = $this->escape($filters['price_range']);
            if ($range === 'lt500') {
                $query .= " AND CAST(p.sanpham_gia AS DECIMAL(10,2)) < 500000";
            } elseif ($range === '500-1000') {
                $query .= " AND CAST(p.sanpham_gia AS DECIMAL(10,2)) BETWEEN 500000 AND 1000000";
            } elseif ($range === 'gt1000') {
                $query .= " AND CAST(p.sanpham_gia AS DECIMAL(10,2)) > 1000000";
            }
        }

        $result = $this->getOne($query);
        if ($result) {
            return is_object($result) ? (int)$result->total : (int)$result['total'];
        }
        return 0;
    }
    
    /**
     * Lấy sản phẩm theo loại sản phẩm
     */
    public function getProductsBySubcategory($subcategory_id, $limit = 12, $offset = 0) {
        $subcategory_id = (int)$subcategory_id;
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        $query = "SELECT p.*, c.danhmuc_ten, b.loaisanpham_ten,
                  COALESCE(
                      (SELECT ap.anh_path FROM tbl_anhsanpham ap 
                       WHERE ap.sanpham_id = p.sanpham_id 
                       ORDER BY ap.is_primary DESC, ap.anh_id ASC LIMIT 1),
                      p.sanpham_anh
                  ) as first_image
                  FROM {$this->table} p
                  LEFT JOIN tbl_danhmuc c ON p.danhmuc_id = c.danhmuc_id
                  LEFT JOIN tbl_loaisanpham b ON p.loaisanpham_id = b.loaisanpham_id
                  WHERE p.loaisanpham_id = $subcategory_id
                  ORDER BY p.sanpham_id DESC
                  LIMIT $offset, $limit";
        
        return $this->getAll($query);
    }
    
    /**
     * Lấy chi tiết sản phẩm
     */
    public function getProductById($product_id) {
        $product_id = (int)$product_id;
        
        $query = "SELECT p.*, c.danhmuc_ten, b.loaisanpham_ten,
                  COALESCE(
                      (SELECT ap.anh_path FROM tbl_anhsanpham ap 
                       WHERE ap.sanpham_id = p.sanpham_id 
                       ORDER BY ap.is_primary DESC, ap.anh_id ASC LIMIT 1),
                      p.sanpham_anh
                  ) as first_image
                  FROM {$this->table} p
                  LEFT JOIN tbl_danhmuc c ON p.danhmuc_id = c.danhmuc_id
                  LEFT JOIN tbl_loaisanpham b ON p.loaisanpham_id = b.loaisanpham_id
                  WHERE p.sanpham_id = $product_id";
        
        return $this->getOne($query);
    }
    
    /**
     * Thêm sản phẩm mới
     */
    public function addProduct($title, $code, $category_id, $subcategory_id, $gia_goc, $gia_ban, $giam_gia, $description, $care_instructions, $image) {
        // Escape dữ liệu
        $title = $this->escape($title);
        $code = $this->escape($code);
        $category_id = (int)$category_id;
        $subcategory_id = (int)$subcategory_id;
        $gia_goc = $this->escape($gia_goc);
        $gia_ban = $this->escape($gia_ban);
        $giam_gia = (float)$giam_gia;
        $description = $this->escape($description);
        $care_instructions = $this->escape($care_instructions);
        $image = $this->escape($image);
        
        // Kiểm tra mã sản phẩm trùng lặp
        $check_query = "SELECT * FROM {$this->table} WHERE sanpham_ma = '$code'";
        if($this->getOne($check_query)) {
            return "Mã sản phẩm đã tồn tại";
        }
        
        $query = "INSERT INTO {$this->table} (
                    sanpham_tieude, 
                    sanpham_ma, 
                    danhmuc_id, 
                    loaisanpham_id, 
                    sanpham_gia_goc,
                    sanpham_gia, 
                    sanpham_giam_gia,
                    sanpham_chitiet, 
                    sanpham_baoquan,
                    sanpham_anh
                  ) VALUES (
                    '$title', 
                    '$code', 
                    $category_id, 
                    $subcategory_id, 
                    '$gia_goc',
                    '$gia_ban', 
                    $giam_gia,
                    '$description', 
                    '$care_instructions',
                    '$image'
                  )";
                  
        if($this->execute($query)) {
            // Trả về ID sản phẩm vừa thêm để có thể gán nhiều màu
            $result = $this->getOne("SELECT LAST_INSERT_ID() as id");
            if ($result) {
                // Handle both array and object result
                $id = is_array($result) ? $result['id'] : $result->id;
                return (int)$id;
            }
            return true;
        } else {
            return "Thêm sản phẩm thất bại";
        }
    }
    
    /**
     * Cập nhật sản phẩm
     */
    public function updateProduct($id, $title, $code, $category_id, $subcategory_id, $price, $description, $care_instructions, $image) {
        // Escape dữ liệu
        $id = (int)$id;
        $title = $this->escape($title);
        $code = $this->escape($code);
        $category_id = (int)$category_id;
        $subcategory_id = (int)$subcategory_id;
        $price = $this->escape($price);
        $description = $this->escape($description);
        $care_instructions = $this->escape($care_instructions);
        $image = $this->escape($image);
        
        // Kiểm tra mã sản phẩm trùng lặp (trừ sản phẩm hiện tại)
        $check_query = "SELECT * FROM {$this->table} WHERE sanpham_ma = '$code' AND sanpham_id != $id";
        if($this->getOne($check_query)) {
            return "Mã sản phẩm đã tồn tại";
        }
        
        $query = "UPDATE {$this->table} SET 
                    sanpham_tieude = '$title', 
                    sanpham_ma = '$code', 
                    danhmuc_id = $category_id, 
                    loaisanpham_id = $subcategory_id,
                    sanpham_gia = '$price', 
                    sanpham_chitiet = '$description', 
                    sanpham_baoquan = '$care_instructions'";
                    
        // Chỉ cập nhật ảnh nếu có
        if (!empty($image)) {
            $query .= ", sanpham_anh = '$image'";
        }
        
        $query .= " WHERE sanpham_id = $id";
        
        if($this->execute($query)) {
            return true;
        } else {
            return "Cập nhật sản phẩm thất bại";
        }
    }
    
    /**
     * Xóa sản phẩm
     */
    public function deleteProduct($id) {
        $id = (int)$id;
        
        $query = "DELETE FROM {$this->table} WHERE sanpham_id = $id";
        
        if($this->execute($query)) {
            return true;
        } else {
            return "Xóa sản phẩm thất bại";
        }
    }
    
    

    /**
     * Đảm bảo bảng liên kết sản phẩm - màu sắc tồn tại
     */
    private function ensureProductColorPivot(): void {
        $this->execute(
            "CREATE TABLE IF NOT EXISTS tbl_sanpham_color (
                sanpham_id INT NOT NULL,
                color_id INT NOT NULL,
                PRIMARY KEY (sanpham_id, color_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    }

    /**
     * Thiết lập danh sách màu cho sản phẩm (nhiều màu)
     * @param int $productId
     * @param array $colorIds
     */
    public function setProductColors(int $productId, array $colorIds): bool {
        $this->ensureProductColorPivot();
        $productId = (int)$productId;

        // Xóa các mapping cũ
        $this->execute("DELETE FROM tbl_sanpham_color WHERE sanpham_id = $productId");

        // Lọc và thêm mapping mới
        $values = [];
        foreach ($colorIds as $cid) {
            $cid = (int)$cid;
            if ($cid > 0) {
                $values[] = "($productId, $cid)";
            }
        }
        if (empty($values)) {
            return true;
        }
        $insertQuery = "INSERT INTO tbl_sanpham_color (sanpham_id, color_id) VALUES " . implode(', ', $values);
        return $this->execute($insertQuery);
    }

    /**
     * Lấy danh sách ID màu của sản phẩm
     */
    public function getProductColorIds(int $productId): array {
        $this->ensureProductColorPivot();
        $productId = (int)$productId;
        $rows = $this->getAll("SELECT color_id FROM tbl_sanpham_color WHERE sanpham_id = $productId ORDER BY color_id");
        $ids = [];
        if ($rows) {
            foreach ($rows as $row) {
                $ids[] = (int)$row->color_id;
            }
        }
        return $ids;
    }

    /**
     * Get sanpham_color_id from product_id and color_id
     * This is needed to link images to specific product-color combinations
     */
    public function getSanphamColorId(int $productId, int $colorId): ?int {
        $productId = (int)$productId;
        $colorId = (int)$colorId;
        $result = $this->getOne(
            "SELECT sanpham_color_id FROM tbl_sanpham_color 
             WHERE sanpham_id = ? AND color_id = ? LIMIT 1",
            [$productId, $colorId]
        );
        if (!$result) {
            return null;
        }
        // Handle both object and array return types
        $colorId = is_array($result) ? $result['sanpham_color_id'] : $result->sanpham_color_id;
        return (int)$colorId;
    }
    
    /**
     * Tìm kiếm sản phẩm
     */
    public function searchProducts($keyword, $category_id = null, $min_price = null, $max_price = null, $limit = 12, $offset = 0) {
        $keyword = $this->escape($keyword);
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        $query = "SELECT p.*, c.danhmuc_ten, b.loaisanpham_ten,
                  COALESCE(
                      (SELECT ap.anh_path FROM tbl_anhsanpham ap 
                       WHERE ap.sanpham_id = p.sanpham_id 
                       ORDER BY ap.is_primary DESC, ap.anh_id ASC LIMIT 1),
                      p.sanpham_anh
                  ) as first_image
                  FROM {$this->table} p
                  LEFT JOIN tbl_danhmuc c ON p.danhmuc_id = c.danhmuc_id
                  LEFT JOIN tbl_loaisanpham b ON p.loaisanpham_id = b.loaisanpham_id
                  WHERE (p.sanpham_tieude LIKE '%$keyword%' OR p.sanpham_chitiet LIKE '%$keyword%')";
        
        if($category_id) {
            $category_id = (int)$category_id;
            $query .= " AND p.danhmuc_id = $category_id";
        }
        
        if($min_price) {
            $min_price = (float)$min_price;
            $query .= " AND CAST(p.sanpham_gia AS DECIMAL) >= $min_price";
        }
        
        if($max_price) {
            $max_price = (float)$max_price;
            $query .= " AND CAST(p.sanpham_gia AS DECIMAL) <= $max_price";
        }
        
        $query .= " ORDER BY p.sanpham_id DESC LIMIT $offset, $limit";
        
        return $this->getAll($query);
    }
    
    /**
     * Lấy sản phẩm liên quan
     */
    public function getRelatedProducts($product_id, $category_id, $limit = 4) {
        $product_id = (int)$product_id;
        $category_id = (int)$category_id;
        $limit = (int)$limit;
        
        $query = "SELECT p.*, c.danhmuc_ten, b.loaisanpham_ten,
                  COALESCE(
                      (SELECT ap.anh_path FROM tbl_anhsanpham ap 
                       WHERE ap.sanpham_id = p.sanpham_id 
                       ORDER BY ap.is_primary DESC, ap.anh_id ASC LIMIT 1),
                      p.sanpham_anh
                  ) as first_image
                  FROM {$this->table} p
                  LEFT JOIN tbl_danhmuc c ON p.danhmuc_id = c.danhmuc_id
                  LEFT JOIN tbl_loaisanpham b ON p.loaisanpham_id = b.loaisanpham_id
                  WHERE p.danhmuc_id = $category_id AND p.sanpham_id != $product_id
                  ORDER BY p.sanpham_id DESC
                  LIMIT $limit";
        
        return $this->getAll($query);
    }

    /**
     * Lấy sản phẩm với chi tiết (Admin - hiển thị tất cả sản phẩm)
     */
    public function getProductsWithDetails($limit = 10, $offset = 0) {
        $query = "SELECT p.*, c.danhmuc_ten, l.loaisanpham_ten,
                  COALESCE(
                      (SELECT ap.anh_path FROM tbl_anhsanpham ap 
                       WHERE ap.sanpham_id = p.sanpham_id 
                       ORDER BY ap.is_primary DESC, ap.anh_id ASC LIMIT 1),
                      p.sanpham_anh
                  ) as first_image
                  FROM tbl_sanpham p
                  LEFT JOIN tbl_danhmuc c ON p.danhmuc_id = c.danhmuc_id
                  LEFT JOIN tbl_loaisanpham l ON p.loaisanpham_id = l.loaisanpham_id
                  ORDER BY p.sanpham_id DESC 
                  LIMIT $limit OFFSET $offset";
        
        return $this->getAll($query);
    }
    
    /**
     * Lấy sản phẩm nổi bật với đầy đủ thông tin (Frontend - hiển thị tất cả sản phẩm active)
     */
    public function getFeaturedProductsWithDetails($limit = 8) {
        $query = "SELECT p.*, c.danhmuc_ten, l.loaisanpham_ten,
                  COALESCE(
                      (SELECT ap.anh_path FROM tbl_anhsanpham ap 
                       WHERE ap.sanpham_id = p.sanpham_id 
                       ORDER BY ap.is_primary DESC, ap.anh_id ASC LIMIT 1),
                      p.sanpham_anh
                  ) as first_image
                  FROM tbl_sanpham p
                  LEFT JOIN tbl_danhmuc c ON p.danhmuc_id = c.danhmuc_id
                  LEFT JOIN tbl_loaisanpham l ON p.loaisanpham_id = l.loaisanpham_id
                  WHERE p.sanpham_status = 1
                  ORDER BY p.sanpham_id DESC 
                  LIMIT $limit";
        
        return $this->getAll($query);
    }
    
    /**
     * Lấy sản phẩm bán chạy
     */
    public function getBestSellingProducts($limit = 8) {
        $query = "SELECT p.*, c.danhmuc_ten, l.loaisanpham_ten,
                  COALESCE(
                      (SELECT ap.anh_path FROM tbl_anhsanpham ap 
                       WHERE ap.sanpham_id = p.sanpham_id 
                       ORDER BY ap.is_primary DESC, ap.anh_id ASC LIMIT 1),
                      p.sanpham_anh
                  ) as first_image
                  FROM tbl_sanpham p
                  LEFT JOIN tbl_danhmuc c ON p.danhmuc_id = c.danhmuc_id
                  LEFT JOIN tbl_loaisanpham l ON p.loaisanpham_id = l.loaisanpham_id
                  WHERE p.sanpham_status = 1
                  ORDER BY p.sanpham_id DESC 
                  LIMIT $limit";
        
        return $this->getAll($query);
    }
    
    /**
     * Lấy sản phẩm mới
     */
    public function getNewProducts($limit = 8) {
        $query = "SELECT p.*, c.danhmuc_ten, l.loaisanpham_ten,
                  COALESCE(
                      (SELECT ap.anh_path FROM tbl_anhsanpham ap 
                       WHERE ap.sanpham_id = p.sanpham_id 
                       ORDER BY ap.is_primary DESC, ap.anh_id ASC LIMIT 1),
                      p.sanpham_anh
                  ) as first_image
                  FROM tbl_sanpham p
                  LEFT JOIN tbl_danhmuc c ON p.danhmuc_id = c.danhmuc_id
                  LEFT JOIN tbl_loaisanpham l ON p.loaisanpham_id = l.loaisanpham_id
                  WHERE p.sanpham_status = 1
                  ORDER BY p.sanpham_id DESC 
                  LIMIT $limit";
        
        return $this->getAll($query);
    }
    
    /**
     * Tăng lượt xem sản phẩm
     */
    public function incrementViewCount($product_id) {
        $product_id = (int)$product_id;
        // Note: sanpham_luot_xem column doesn't exist in current database schema
        // This method is kept for future implementation when the column is added
        return true;
    }

    /**
     * Đếm tất cả sản phẩm
     */
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM tbl_sanpham";
        $result = $this->getOne($query);
        
        if ($result) {
            if (is_object($result) && isset($result->total)) {
                return $result->total;
            } elseif (is_array($result) && isset($result['total'])) {
                return $result['total'];
            }
        }
        
        return 0;
    }
    
    // ============================================
    // METHODS MỚI CHO VARIANT SYSTEM (DATABASE v2.0)
    // ============================================
    
    /**
     * Lấy tất cả size
     */
    public function getAllSizes() {
        $query = "SELECT * FROM tbl_size ORDER BY size_order ASC";
        return $this->getAll($query);
    }
    
    /**
     * Lấy thông tin size theo ID
     */
    public function getSizeById($sizeId) {
        $sizeId = (int)$sizeId;
        $query = "SELECT * FROM tbl_size WHERE size_id = $sizeId";
        return $this->getOne($query);
    }
    
    /**
     * Lấy thông tin màu theo ID
     */
    public function getColorById($colorId) {
        $colorId = (int)$colorId;
        $query = "SELECT * FROM tbl_color WHERE color_id = $colorId";
        return $this->getOne($query);
    }
    
    /**
     * Thêm product variant
     */
    public function addProductVariant($data) {
        $sanphamId = (int)$data['sanpham_id'];
        $colorId = (int)$data['color_id'];
        $sizeId = (int)$data['size_id'];
        $sku = $this->escape($data['sku'] ?? '');
        $tonKho = (int)($data['ton_kho'] ?? 0);
        $giaBan = isset($data['gia_ban']) ? $this->escape($data['gia_ban']) : 'NULL';
        $trangThai = (int)($data['trang_thai'] ?? 1);
        
        $query = "INSERT INTO tbl_product_variant 
                  (sanpham_id, color_id, size_id, sku, ton_kho, gia_ban, trang_thai) 
                  VALUES ($sanphamId, $colorId, $sizeId, '$sku', $tonKho, $giaBan, $trangThai)";
        
        return $this->execute($query);
    }
    
    /**
     * Lấy tất cả variants của sản phẩm
     */
    public function getProductVariants($sanphamId) {
        $sanphamId = (int)$sanphamId;
        
        $query = "SELECT v.*, 
                         s.size_ten, s.size_order,
                         c.color_ten, c.color_ma
                  FROM tbl_product_variant v
                  JOIN tbl_size s ON v.size_id = s.size_id
                  JOIN tbl_color c ON v.color_id = c.color_id
                  WHERE v.sanpham_id = $sanphamId
                  ORDER BY c.color_ten, s.size_order";
        
        return $this->getAll($query);
    }
    
    /**
     * Lấy variant theo ID
     */
    public function getVariantById($variantId) {
        $variantId = (int)$variantId;
        
        $query = "SELECT v.*, 
                         s.size_ten,
                         c.color_ten, c.color_ma,
                         p.sanpham_tieude, p.sanpham_gia, p.sanpham_anh
                  FROM tbl_product_variant v
                  JOIN tbl_size s ON v.size_id = s.size_id
                  JOIN tbl_color c ON v.color_id = c.color_id
                  JOIN tbl_sanpham p ON v.sanpham_id = p.sanpham_id
                  WHERE v.variant_id = $variantId";
        
        return $this->getOne($query);
    }
    
    /**
     * Lấy variants theo sản phẩm và màu
     */
    public function getVariantsByProductAndColor($sanphamId, $colorId) {
        $sanphamId = (int)$sanphamId;
        $colorId = (int)$colorId;
        
        $query = "SELECT v.*, 
                         s.size_ten, s.size_order,
                         c.color_ten, c.color_ma
                  FROM tbl_product_variant v
                  JOIN tbl_size s ON v.size_id = s.size_id
                  JOIN tbl_color c ON v.color_id = c.color_id
                  WHERE v.sanpham_id = $sanphamId AND v.color_id = $colorId
                  ORDER BY s.size_order";
        
        return $this->getAll($query);
    }
    
    /**
     * Cập nhật tồn kho variant (SET giá trị mới, không cộng trừ)
     */
    public function updateVariantStock($variantId, $quantity) {
        $variantId = (int)$variantId;
        $quantity = (int)$quantity;
        
        $query = "UPDATE tbl_product_variant 
                  SET ton_kho = $quantity, 
                      trang_thai = CASE WHEN $quantity > 0 THEN 1 ELSE 0 END,
                      updated_at = NOW()
                  WHERE variant_id = $variantId";
        
        return $this->execute($query);
    }
    
    /**
     * Cập nhật tồn kho variant (tăng/giảm từ giá trị hiện tại)
     */
    public function adjustVariantStock($variantId, $adjustment) {
        $variantId = (int)$variantId;
        $adjustment = (int)$adjustment;
        
        $query = "UPDATE tbl_product_variant 
                  SET ton_kho = ton_kho + $adjustment, 
                      trang_thai = CASE WHEN ton_kho + $adjustment > 0 THEN 1 ELSE 0 END,
                      updated_at = NOW()
                  WHERE variant_id = $variantId";
        
        return $this->execute($query);
    }
    
    /**
     * Giảm tồn kho khi bán hàng
     */
    public function decreaseVariantStock($variantId, $quantity) {
        $variantId = (int)$variantId;
        $quantity = (int)$quantity;
        
        $query = "UPDATE tbl_product_variant 
                  SET ton_kho = ton_kho - $quantity, 
                      trang_thai = CASE WHEN ton_kho - $quantity <= 0 THEN 0 ELSE 1 END,
                      updated_at = NOW()
                  WHERE variant_id = $variantId AND ton_kho >= $quantity";
        
        return $this->execute($query);
    }
    
    /**
     * Kiểm tra tồn kho variant
     */
    public function checkVariantStock($variantId) {
        $variantId = (int)$variantId;
        
        $query = "SELECT ton_kho, trang_thai FROM tbl_product_variant WHERE variant_id = $variantId";
        return $this->getOne($query);
    }
    
    /**
     * Cập nhật variant
     */
    public function updateVariant($variantId, $data) {
        $variantId = (int)$variantId;
        $tonKho = (int)($data['ton_kho'] ?? 0);
        $giaBan = isset($data['gia_ban']) && $data['gia_ban'] !== '' ? $this->escape($data['gia_ban']) : 'NULL';
        $trangThai = (int)($data['trang_thai'] ?? 1);
        
        $query = "UPDATE tbl_product_variant 
                  SET ton_kho = $tonKho,
                      gia_ban = $giaBan,
                      trang_thai = $trangThai,
                      updated_at = NOW()
                  WHERE variant_id = $variantId";
        
        return $this->execute($query);
    }
    
    /**
     * Xóa variant
     */
    public function deleteVariant($variantId) {
        $variantId = (int)$variantId;
        $query = "DELETE FROM tbl_product_variant WHERE variant_id = $variantId";
        return $this->execute($query);
    }
    
    /**
     * Xóa tất cả variants của sản phẩm
     */
    public function deleteProductVariants($sanphamId) {
        $sanphamId = (int)$sanphamId;
        $query = "DELETE FROM tbl_product_variant WHERE sanpham_id = $sanphamId";
        return $this->execute($query);
    }
    
    /**
     * Kiểm tra variant có tồn tại không
     */
    public function variantExists($sanphamId, $colorId, $sizeId) {
        $sanphamId = (int)$sanphamId;
        $colorId = (int)$colorId;
        $sizeId = (int)$sizeId;
        
        $query = "SELECT variant_id FROM tbl_product_variant 
                  WHERE sanpham_id = $sanphamId AND color_id = $colorId AND size_id = $sizeId";
        $result = $this->getOne($query);
        
        return $result ? true : false;
    }
    
    /**
     * Lấy tổng tồn kho của sản phẩm (tất cả variants)
     */
    public function getTotalStock($sanphamId) {
        $sanphamId = (int)$sanphamId;
        
        $query = "SELECT SUM(ton_kho) as total_stock 
                  FROM tbl_product_variant 
                  WHERE sanpham_id = $sanphamId";
        $result = $this->getOne($query);
        
        if ($result) {
            return is_object($result) ? (int)$result->total_stock : (int)$result['total_stock'];
        }
        
        return 0;
    }
    
    // ============================================
    // SIZE MANAGEMENT METHODS
    // ============================================
    
    /**
     * Lấy size theo tên
     */
    public function getSizeByName($sizeName) {
        $sizeName = $this->escape($sizeName);
        $query = "SELECT * FROM tbl_size WHERE size_ten = '$sizeName'";
        return $this->getOne($query);
    }
    
    /**
     * Thêm size mới
     */
    public function addSize($data) {
        $sizeTen = $this->escape($data['size_ten']);
        $sizeOrder = (int)($data['size_order'] ?? 0);
        
        $query = "INSERT INTO tbl_size (size_ten, size_order) 
                  VALUES ('$sizeTen', $sizeOrder)";
        
        return $this->execute($query);
    }
    
    /**
     * Cập nhật size
     */
    public function updateSize($data) {
        $sizeId = (int)$data['size_id'];
        $sizeTen = $this->escape($data['size_ten']);
        $sizeOrder = (int)($data['size_order'] ?? 0);
        
        $query = "UPDATE tbl_size 
                  SET size_ten = '$sizeTen', 
                      size_order = $sizeOrder 
                  WHERE size_id = $sizeId";
        
        return $this->execute($query);
    }
    
    /**
     * Xóa size
     */
    public function deleteSize($sizeId) {
        $sizeId = (int)$sizeId;
        $query = "DELETE FROM tbl_size WHERE size_id = $sizeId";
        return $this->execute($query);
    }
    
    /**
     * Đếm số variant sử dụng size này
     */
    public function countVariantsBySize($sizeId) {
        $sizeId = (int)$sizeId;
        $query = "SELECT COUNT(*) as count FROM tbl_product_variant WHERE size_id = $sizeId";
        $result = $this->getOne($query);
        
        if ($result) {
            return is_object($result) ? (int)$result->count : (int)$result['count'];
        }
        
        return 0;
    }
    
    /**
     * Lấy sản phẩm với bộ lọc nâng cao
     */
    public function getFilteredProducts($filters = [], $limit = 12, $offset = 0) {
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        $query = "SELECT DISTINCT p.*, c.danhmuc_ten, l.loaisanpham_ten,
                  COALESCE(
                      (SELECT ap.anh_path FROM tbl_anhsanpham ap 
                       WHERE ap.sanpham_id = p.sanpham_id 
                       ORDER BY ap.is_primary DESC, ap.anh_id ASC LIMIT 1),
                      p.sanpham_anh
                  ) as first_image
                  FROM tbl_sanpham p
                  LEFT JOIN tbl_danhmuc c ON p.danhmuc_id = c.danhmuc_id
                  LEFT JOIN tbl_loaisanpham l ON p.loaisanpham_id = l.loaisanpham_id";
        
        $whereConditions = ["p.sanpham_status = 1"];
        $params = [];
        
        // Filter by category
        if (!empty($filters['category_id'])) {
            $categoryId = (int)$filters['category_id'];
            $whereConditions[] = "p.danhmuc_id = $categoryId";
        }
        
        // Filter by product type
        if (!empty($filters['product_type'])) {
            $productType = (int)$filters['product_type'];
            $whereConditions[] = "p.loaisanpham_id = $productType";
        }
        
        // Filter by price range
        if (!empty($filters['price_range'])) {
            $priceRange = $this->escape($filters['price_range']);
            if (strpos($priceRange, '-') !== false) {
                list($minPrice, $maxPrice) = explode('-', $priceRange);
                $minPrice = (float)$minPrice;
                $maxPrice = (float)$maxPrice;
                $whereConditions[] = "CAST(p.sanpham_gia AS DECIMAL) >= $minPrice AND CAST(p.sanpham_gia AS DECIMAL) <= $maxPrice";
            }
        }
        
        // Filter by size - requires joining with product variants
        if (!empty($filters['size'])) {
            $sizeId = (int)$filters['size'];
            $query .= " INNER JOIN tbl_product_variant pv ON p.sanpham_id = pv.sanpham_id";
            $whereConditions[] = "pv.size_id = $sizeId AND pv.trang_thai = 1";
        }
        
        $query .= " WHERE " . implode(' AND ', $whereConditions);
        
        // Sorting
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc':
                    $query .= " ORDER BY CAST(p.sanpham_gia AS DECIMAL) ASC";
                    break;
                case 'price_desc':
                    $query .= " ORDER BY CAST(p.sanpham_gia AS DECIMAL) DESC";
                    break;
                case 'name_asc':
                    $query .= " ORDER BY p.sanpham_tieude ASC";
                    break;
                case 'name_desc':
                    $query .= " ORDER BY p.sanpham_tieude DESC";
                    break;
                default:
                    $query .= " ORDER BY p.sanpham_id DESC";
            }
        } else {
            $query .= " ORDER BY p.sanpham_id DESC";
        }
        
        $query .= " LIMIT $offset, $limit";
        
        return $this->getAll($query);
    }
    
    /**
     * Lấy tất cả loại sản phẩm theo danh mục
     */
    public function getProductTypesByCategory($categoryId) {
        $categoryId = (int)$categoryId;
        $query = "SELECT * FROM tbl_loaisanpham WHERE danhmuc_id = $categoryId ORDER BY loaisanpham_ten ASC";
        return $this->getAll($query);
    }
    
    /**
     * Lấy variant theo product_id, color_id, size_id
     */
    public function getVariantByProductColorSize($productId, $colorId, $sizeId) {
        $productId = (int)$productId;
        $colorId = (int)$colorId;
        $sizeId = (int)$sizeId;
        
        $query = "SELECT * FROM tbl_product_variant 
                  WHERE sanpham_id = $productId AND color_id = $colorId AND size_id = $sizeId";
        
        return $this->getOne($query);
    }
    
    /**
     * Cập nhật sản phẩm với mảng dữ liệu
     */
    public function updateProductArray($data) {
        $id = (int)$data['sanpham_id'];
        
        $sets = [];
        if(isset($data['sanpham_tieude'])) $sets[] = "sanpham_tieude = '" . addslashes($data['sanpham_tieude']) . "'";
        if(isset($data['sanpham_ma'])) $sets[] = "sanpham_ma = '" . addslashes($data['sanpham_ma']) . "'";
        if(isset($data['danhmuc_id'])) $sets[] = "danhmuc_id = " . (int)$data['danhmuc_id'];
        if(isset($data['loaisanpham_id'])) $sets[] = "loaisanpham_id = " . (int)$data['loaisanpham_id'];
        if(isset($data['sanpham_gia_goc'])) $sets[] = "sanpham_gia_goc = " . floatval($data['sanpham_gia_goc']);
        if(isset($data['sanpham_gia'])) $sets[] = "sanpham_gia = " . floatval($data['sanpham_gia']);
        if(isset($data['sanpham_giam_gia'])) $sets[] = "sanpham_giam_gia = " . floatval($data['sanpham_giam_gia']);
        if(isset($data['sanpham_chitiet'])) $sets[] = "sanpham_chitiet = '" . addslashes($data['sanpham_chitiet']) . "'";
        if(isset($data['sanpham_baoquan'])) $sets[] = "sanpham_baoquan = '" . addslashes($data['sanpham_baoquan']) . "'";
        if(isset($data['sanpham_anh'])) $sets[] = "sanpham_anh = '" . addslashes($data['sanpham_anh']) . "'";
        if(isset($data['sanpham_status'])) $sets[] = "sanpham_status = " . (int)$data['sanpham_status'];
        
        if(empty($sets)) return false;
        
        $query = "UPDATE tbl_sanpham SET " . implode(', ', $sets) . " WHERE sanpham_id = $id";
        return $this->execute($query);
    }
    
    /**
     * Xóa tất cả màu của sản phẩm
     */
    public function deleteProductColors($productId) {
        $productId = (int)$productId;
        $query = "DELETE FROM tbl_sanpham_color WHERE sanpham_id = $productId";
        return $this->execute($query);
    }
    
    /**
     * Thêm màu cho sản phẩm
     */
    public function addProductColor($productId, $colorId, $isDefault = 0) {
        $productId = (int)$productId;
        $colorId = (int)$colorId;
        $isDefault = (int)$isDefault;
        
        $query = "INSERT INTO tbl_sanpham_color (sanpham_id, color_id, is_default) 
                  VALUES ($productId, $colorId, $isDefault)";
        
        return $this->execute($query);
    }
}