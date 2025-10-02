<?php

class ColorModel extends Model {
    protected $table = 'tbl_color';

    public function __construct() {
        parent::__construct();
    }

    public function getAllColors() {
        $query = "SELECT * FROM {$this->table} ORDER BY color_id DESC";
        return $this->getAll($query);
    }

    public function addColor($name, $colorImage = null, $colorCode = null) {
        $name = $this->escape($name);
        $colorImage = $colorImage ? $this->escape($colorImage) : null;
        $colorCode = $colorCode ? $this->escape($colorCode) : null;
        
        if (empty($name)) {
            return false;
        }
        
        // Cột color_anh: lưu ảnh màu
        // Cột color_ma: lưu mã màu hex (#FFFFFF)
        $fields = ['color_ten'];
        $values = ["'$name'"];
        
        if ($colorImage !== null && $colorImage !== '') {
            $fields[] = 'color_anh';
            $values[] = "'$colorImage'";
        }
        
        if ($colorCode !== null && $colorCode !== '') {
            $fields[] = 'color_ma';
            $values[] = "'$colorCode'";
        }
        
        $query = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                  VALUES (" . implode(', ', $values) . ")";
        
        return $this->execute($query);
    }

    public function updateColor($id, $name, $colorImage = null, $colorCode = null) {
        $id = (int)$id;
        $name = $this->escape($name);
        $colorImage = $colorImage ? $this->escape($colorImage) : null;
        $colorCode = $colorCode ? $this->escape($colorCode) : null;
        
        $updates = ["color_ten = '$name'"];
        
        if ($colorImage !== null && $colorImage !== '') {
            $updates[] = "color_anh = '$colorImage'";
        }
        
        if ($colorCode !== null && $colorCode !== '') {
            $updates[] = "color_ma = '$colorCode'";
        }
        
        $query = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE color_id = $id";
        
        return $this->execute($query);
    }
    
    /**
     * Xóa màu
     */
    public function deleteColor($id) {
        $id = (int)$id;
        
        // Kiểm tra xem màu có đang được sử dụng không
        $check = "SELECT COUNT(*) as count FROM tbl_sanpham_color WHERE color_id = $id";
        $result = $this->getOne($check);
        $count = is_object($result) ? $result->count : ($result['count'] ?? 0);
        
        if ($count > 0) {
            return "Không thể xóa màu đang được sử dụng bởi sản phẩm";
        }
        
        $query = "DELETE FROM {$this->table} WHERE color_id = $id";
        
        if ($this->execute($query)) {
            return true;
        } else {
            return "Xóa màu thất bại";
        }
    }

    public function getColorById($id) {
        $id = (int)$id;
        $query = "SELECT * FROM {$this->table} WHERE color_id = $id";
        return $this->getOne($query);
    }
}


