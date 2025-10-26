<?php
/**
 * CategoryModel - Xử lý dữ liệu danh mục sản phẩm
 */
class CategoryModel extends Model {
    protected $table = 'tbl_danhmuc';
    protected $subcategoryTable = 'tbl_loaisanpham';
    protected $productTable = 'tbl_sanpham';
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Lấy tất cả danh mục sản phẩm
     */
    public function getAllCategories() {
        $query = "SELECT * FROM {$this->table} ORDER BY danhmuc_id";
        return $this->getAll($query);
    }
    
    /**
     * Lấy danh mục theo ID
     * @param int $id ID của danh mục
     */
    public function getCategoryById($id) {
        $id = (int)$id;
        // Use prepared statement and ensure a single deterministic row
        $query = "SELECT * FROM {$this->table} WHERE danhmuc_id = ? LIMIT 1";
        return $this->getOne($query, [$id]);
    }
    
    /**
     * Thêm danh mục mới
     * @param string $danhmuc_ten Tên danh mục
     */
    public function addCategory($danhmuc_ten) {
        $danhmuc_ten = $this->escape($danhmuc_ten);
        
        // Kiểm tra tên danh mục đã tồn tại chưa
        $check_query = "SELECT * FROM {$this->table} WHERE danhmuc_ten = '$danhmuc_ten'";
        if($this->getOne($check_query)) {
            return "Tên danh mục đã tồn tại";
        }
        
        $query = "INSERT INTO {$this->table} (danhmuc_ten) VALUES ('$danhmuc_ten')";
        
        if($this->execute($query)) {
            return true;
        } else {
            return "Thêm danh mục thất bại";
        }
    }
    
    /**
     * Cập nhật danh mục
     * @param int $id ID của danh mục
     * @param string $danhmuc_ten Tên danh mục mới
     */
    public function updateCategory($id, $danhmuc_ten) {
        $id = (int)$id;
        $danhmuc_ten = $this->escape($danhmuc_ten);
        
        // Kiểm tra tên danh mục đã tồn tại chưa (trừ danh mục hiện tại)
        $check_query = "SELECT * FROM {$this->table} WHERE danhmuc_ten = '$danhmuc_ten' AND danhmuc_id != $id";
        if($this->getOne($check_query)) {
            return "Tên danh mục đã tồn tại";
        }
        
        $query = "UPDATE {$this->table} SET danhmuc_ten = '$danhmuc_ten' WHERE danhmuc_id = $id";
        
        if($this->execute($query)) {
            return true;
        } else {
            return "Cập nhật danh mục thất bại";
        }
    }
    
    /**
     * Xóa danh mục
     * @param int $id ID của danh mục
     */
    public function deleteCategory($id) {
        $id = (int)$id;
        
        // Kiểm tra xem danh mục có sản phẩm không
        if($this->hasProducts($id)) {
            return "Không thể xóa danh mục đang có sản phẩm";
        }
        
        // Xóa các loại sản phẩm thuộc danh mục này trước
        $delete_subcategories = "DELETE FROM {$this->subcategoryTable} WHERE danhmuc_id = $id";
        $this->execute($delete_subcategories);
        
        // Xóa danh mục
        $query = "DELETE FROM {$this->table} WHERE danhmuc_id = $id";
        
        if($this->execute($query)) {
            return true;
        } else {
            return "Xóa danh mục thất bại";
        }
    }
    
    /**
     * Kiểm tra danh mục có sản phẩm không
     * @param int $id ID của danh mục
     */
    public function hasProducts($id) {
        $id = (int)$id;
        $query = "SELECT COUNT(*) as count FROM {$this->productTable} WHERE danhmuc_id = $id";
        $result = $this->getOne($query);
        
        if ($result) {
            if (is_object($result) && isset($result->count)) {
                return $result->count > 0;
            } elseif (is_array($result) && isset($result['count'])) {
                return $result['count'] > 0;
            }
        }
        
        return false;
    }
    
    /**
     * Lấy tất cả loại sản phẩm theo danh mục
     * @param int $danhmuc_id ID của danh mục
     */
    public function getSubcategoriesByCategoryId($danhmuc_id) {
        $danhmuc_id = (int)$danhmuc_id;
        $query = "SELECT * FROM {$this->subcategoryTable} WHERE danhmuc_id = $danhmuc_id ORDER BY loaisanpham_id";
        return $this->getAll($query);
    }
    
    /**
     * Lấy tất cả loại sản phẩm
     */
    public function getAllSubcategories() {
        $query = "SELECT * FROM {$this->subcategoryTable} ORDER BY danhmuc_id, loaisanpham_ten";
        return $this->getAll($query);
    }
    
    /**
     * Lấy loại sản phẩm theo ID
     * @param int $id ID của loại sản phẩm
     */
    public function getSubcategoryById($id) {
        $id = (int)$id;
        $query = "SELECT * FROM {$this->subcategoryTable} WHERE loaisanpham_id = $id";
        return $this->getOne($query);
    }
    
    /**
     * Thêm loại sản phẩm mới
     * @param int $danhmuc_id ID của danh mục
     * @param string $loaisanpham_ten Tên loại sản phẩm
     */
    public function addSubcategory($danhmuc_id, $loaisanpham_ten) {
        $danhmuc_id = (int)$danhmuc_id;
        $loaisanpham_ten = $this->escape($loaisanpham_ten);
        
        // Kiểm tra tên loại sản phẩm đã tồn tại trong danh mục chưa
        $check_query = "SELECT * FROM {$this->subcategoryTable} WHERE danhmuc_id = $danhmuc_id AND loaisanpham_ten = '$loaisanpham_ten'";
        if($this->getOne($check_query)) {
            return "Tên loại sản phẩm đã tồn tại trong danh mục này";
        }
        
        $query = "INSERT INTO {$this->subcategoryTable} (danhmuc_id, loaisanpham_ten) VALUES ($danhmuc_id, '$loaisanpham_ten')";
        
        if($this->execute($query)) {
            return true;
        } else {
            return "Thêm loại sản phẩm thất bại";
        }
    }
    
    /**
     * Cập nhật loại sản phẩm
     * @param int $id ID của loại sản phẩm
     * @param string $loaisanpham_ten Tên loại sản phẩm mới
     */
    public function updateSubcategory($id, $loaisanpham_ten) {
        $id = (int)$id;
        $loaisanpham_ten = $this->escape($loaisanpham_ten);
        
        // Lấy thông tin loại sản phẩm hiện tại
        $subcategory = $this->getSubcategoryById($id);
        if(!$subcategory) {
            return "Không tìm thấy loại sản phẩm";
        }
        
        $danhmuc_id = is_object($subcategory) ? $subcategory->danhmuc_id : $subcategory['danhmuc_id'];
        
        // Kiểm tra tên loại sản phẩm đã tồn tại trong danh mục chưa (trừ loại hiện tại)
        $check_query = "SELECT * FROM {$this->subcategoryTable} WHERE danhmuc_id = $danhmuc_id AND loaisanpham_ten = '$loaisanpham_ten' AND loaisanpham_id != $id";
        if($this->getOne($check_query)) {
            return "Tên loại sản phẩm đã tồn tại trong danh mục này";
        }
        
        $query = "UPDATE {$this->subcategoryTable} SET loaisanpham_ten = '$loaisanpham_ten' WHERE loaisanpham_id = $id";
        
        if($this->execute($query)) {
            return true;
        } else {
            return "Cập nhật loại sản phẩm thất bại";
        }
    }
    
    /**
     * Xóa loại sản phẩm
     * @param int $id ID của loại sản phẩm
     */
    public function deleteSubcategory($id) {
        $id = (int)$id;
        
        // Kiểm tra xem loại sản phẩm có sản phẩm không
        if($this->hasProductsInSubcategory($id)) {
            return "Không thể xóa loại sản phẩm đang có sản phẩm";
        }
        
        $query = "DELETE FROM {$this->subcategoryTable} WHERE loaisanpham_id = $id";
        
        if($this->execute($query)) {
            return true;
        } else {
            return "Xóa loại sản phẩm thất bại";
        }
    }
    
    /**
     * Kiểm tra loại sản phẩm có sản phẩm không
     * @param int $id ID của loại sản phẩm
     */
    public function hasProductsInSubcategory($id) {
        $id = (int)$id;
        $query = "SELECT COUNT(*) as count FROM {$this->productTable} WHERE loaisanpham_id = $id";
        $result = $this->getOne($query);
        
        if ($result) {
            if (is_object($result) && isset($result->count)) {
                return $result->count > 0;
            } elseif (is_array($result) && isset($result['count'])) {
                return $result['count'] > 0;
            }
        }
        
        return false;
    }
    
    /**
     * Lấy thống kê danh mục
     * @param int $id ID của danh mục
     */
    public function getCategoryStats($id) {
        $id = (int)$id;
        
        // Đếm số loại sản phẩm
        $subcategories_query = "SELECT COUNT(*) as subcategories_count FROM {$this->subcategoryTable} WHERE danhmuc_id = $id";
        $subcategories_result = $this->getOne($subcategories_query);
        
        // Đếm số sản phẩm
        $products_query = "SELECT COUNT(*) as products_count FROM {$this->productTable} WHERE danhmuc_id = $id";
        $products_result = $this->getOne($products_query);
        
        $stats = new stdClass();
        $stats->subcategories_count = 0;
        $stats->products_count = 0;
        
        if ($subcategories_result) {
            if (is_object($subcategories_result) && isset($subcategories_result->subcategories_count)) {
                $stats->subcategories_count = $subcategories_result->subcategories_count;
            } elseif (is_array($subcategories_result) && isset($subcategories_result['subcategories_count'])) {
                $stats->subcategories_count = $subcategories_result['subcategories_count'];
            }
        }
        
        if ($products_result) {
            if (is_object($products_result) && isset($products_result->products_count)) {
                $stats->products_count = $products_result->products_count;
            } elseif (is_array($products_result) && isset($products_result['products_count'])) {
                $stats->products_count = $products_result['products_count'];
            }
        }
        
        return $stats;
    }
}
