<?php
// filepath: c:\xampp\htdocs\ivymoda\ivymoda_mvc\app\core\Model.php
/**
 * Base Model Class
 * Lớp cơ sở mà tất cả model kế thừa
 */
class Model {
    protected $db;
    protected $table;
    
    /**
     * Constructor - Khởi tạo kết nối database
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Tìm tất cả bản ghi
     * @param string $orderBy Trường sắp xếp
     * @param string $order Kiểu sắp xếp (ASC/DESC)
     * @return array Mảng các bản ghi
     */
    public function findAll($orderBy = '', $order = 'ASC') {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy} {$order}";
        }
        
        $this->db->query($sql);
        return $this->db->resultSet();
    }
    
    /**
     * Tìm bản ghi theo ID
     * @param int $id ID cần tìm
     * @return object Bản ghi tìm được
     */
    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    /**
     * Tìm bản ghi theo điều kiện
     * @param string $field Tên trường
     * @param mixed $value Giá trị
     * @return array Mảng các bản ghi
     */
    public function findBy($field, $value) {
        $sql = "SELECT * FROM {$this->table} WHERE {$field} = :{$field}";
        $this->db->query($sql);
        $this->db->bind(":{$field}", $value);
        return $this->db->resultSet();
    }
    
    /**
     * Thêm bản ghi mới
     * @param array $data Dữ liệu cần thêm
     * @return int ID của bản ghi mới
     */
    public function create($data) {
        // Chuẩn bị câu lệnh INSERT
        $fields = array_keys($data);
        $placeholders = array_map(function($field) {
            return ":{$field}";
        }, $fields);
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $this->db->query($sql);
        
        // Bind các giá trị
        foreach ($data as $key => $value) {
            $this->db->bind(":{$key}", $value);
        }
        
        // Thực thi và trả về ID mới
        $this->db->execute();
        return $this->db->lastInsertId();
    }
    
    /**
     * Cập nhật bản ghi
     * @param int $id ID của bản ghi cần cập nhật
     * @param array $data Dữ liệu cần cập nhật
     * @return bool Kết quả thực thi
     */
    public function update($id, $data) {
        // Chuẩn bị câu lệnh UPDATE
        $setClause = array_map(function($field) {
            return "{$field} = :{$field}";
        }, array_keys($data));
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClause) . " WHERE id = :id";
        
        $this->db->query($sql);
        
        // Bind các giá trị
        foreach ($data as $key => $value) {
            $this->db->bind(":{$key}", $value);
        }
        $this->db->bind(':id', $id);
        
        // Thực thi
        return $this->db->execute();
    }
    
    /**
     * Xóa bản ghi theo ID
     * @param int $id ID của bản ghi cần xóa
     * @return bool Kết quả thực thi
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    /**
     * Thực thi câu truy vấn và trả về một bản ghi
     * @param string $query Câu truy vấn SQL
     * @param array $params Mảng tham số để bind (tùy chọn)
     * @return array|false Mảng chứa một bản ghi hoặc false nếu lỗi
     */
    public function getOne($query, $params = []) {
        $this->db->query($query);
        
        // Bind parameters nếu có
        if (!empty($params)) {
            foreach ($params as $index => $value) {
                $this->db->bind($index + 1, $value);
            }
        }
        
        $this->db->execute();
        return $this->db->single();
    }
    
    /**
     * Thực thi câu truy vấn và trả về tất cả kết quả
     * @param string $query Câu truy vấn SQL
     * @param array $params Mảng tham số để bind (tùy chọn)
     * @return array Mảng kết quả
     */
    public function getAll($query, $params = []) {
        $this->db->query($query);
        
        // Bind parameters nếu có
        if (!empty($params)) {
            foreach ($params as $index => $value) {
                $this->db->bind($index + 1, $value);
            }
        }
        
        $this->db->execute();
        return $this->db->resultSet();
    }
    
    /**
     * Kiểm tra bảng có tồn tại trong database không
     * @param string $table Tên bảng cần kiểm tra
     * @return bool Kết quả kiểm tra
     */
    public function tableExists($table) {
        $query = "SHOW TABLES LIKE '$table'";
        $this->db->query($query);
        $this->db->execute();
        $result = $this->db->resultSet();
        return !empty($result);
    }
    
    /**
     * Thực thi một câu lệnh SQL
     * @param string $query Câu lệnh SQL
     * @param array $params Mảng tham số để bind (tùy chọn)
     * @return boolean Kết quả thực thi
     */
    public function execute($query, $params = []) {
        $this->db->query($query);
        
        // Bind parameters nếu có
        if (!empty($params)) {
            foreach ($params as $index => $value) {
                $this->db->bind($index + 1, $value);
            }
        }
        
        return $this->db->execute();
    }
    
    /**
     * Thoát các ký tự đặc biệt trong chuỗi SQL
     * @param string $string Chuỗi cần escape
     * @return string Chuỗi đã được escape
     */
    public function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Đếm số bản ghi theo điều kiện
     * @param string $table Tên bảng
     * @param string $condition Điều kiện (không bắt buộc)
     * @return int Số lượng bản ghi
     */
    public function count($table, $condition = '') {
        $query = "SELECT COUNT(*) as total FROM $table";
        
        if ($condition) {
            $query .= " WHERE $condition";
        }
        
        $this->db->query($query);
        $this->db->execute();
        $result = $this->db->single();
        
        return $result ? $result->total : 0;
    }
}