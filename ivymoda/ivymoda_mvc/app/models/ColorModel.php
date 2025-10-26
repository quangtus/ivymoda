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

    public function addColor($name, $colorCode = null) {
        $name = $this->escape($name);
        $colorCode = $colorCode ? $this->escape($colorCode) : null;
        
        if (empty($name)) {
            return false;
        }
        
        // Chỉ sử dụng color_ten và color_ma (mã hex)
        $fields = ['color_ten'];
        $values = ["'$name'"];
        
        if ($colorCode !== null && $colorCode !== '') {
            $fields[] = 'color_ma';
            $values[] = "'$colorCode'";
        }
        
        $query = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                  VALUES (" . implode(', ', $values) . ")";
        
        return $this->execute($query);
    }

    public function updateColor($id, $name, $colorCode = null) {
        $id = (int)$id;
        $name = $this->escape($name);
        $colorCode = $colorCode ? $this->escape($colorCode) : null;
        
        $updates = ["color_ten = '$name'"];
        
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


