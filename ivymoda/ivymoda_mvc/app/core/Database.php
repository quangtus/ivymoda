<?php
// filepath: c:\xampp\htdocs\ivymoda\ivymoda_mvc\app\core\Database.php

/**
 * Database Class - PDO Database Handler
 * Xử lý tất cả truy vấn database sử dụng PDO
 */
class Database {
    private static $instance = null;  // Singleton instance
    
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    
    private $dbh;        // Database handler
    private $stmt;       // Statement
    private $error;      // Error message
    
    /**
     * Get singleton instance
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor - Khởi tạo kết nối PDO
     */
    public function __construct() {
        // Set DSN
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8mb4';
        
        // Set PDO options
        $options = [
            PDO::ATTR_PERSISTENT => true,           // Persistent connection
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Throw exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, // Return objects
            PDO::ATTR_EMULATE_PREPARES => false     // Use real prepared statements
        ];
        
        // Create PDO instance
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            error_log("Database Connection Error: " . $this->error);
            die("Kết nối database thất bại: " . $this->error);
        }
    }
    
    /**
     * Chuẩn bị câu lệnh SQL
     * @param string $sql Câu lệnh SQL cần chuẩn bị
     * @param array $params Mảng tham số để bind và thực thi (tùy chọn)
     * @return bool|void Trả về kết quả nếu có params, void nếu không
     */
    public function query($sql, $params = null) {
        $this->stmt = $this->dbh->prepare($sql);
        
        // Nếu có params, bind và execute luôn
        if ($params !== null && is_array($params)) {
            foreach ($params as $index => $value) {
                $this->bind($index + 1, $value);
            }
            return $this->execute();
        }
    }
    
    /**
     * Bind giá trị cho prepared statement
     * @param string $param Tên tham số
     * @param mixed $value Giá trị cần bind
     * @param mixed $type Kiểu dữ liệu (optional)
     * @return void
     */
    public function bind($param, $value, $type = null) {
        // Xác định kiểu dữ liệu nếu không chỉ định
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        
        // Bind giá trị
        $this->stmt->bindValue($param, $value, $type);
    }
    
    /**
     * Thực thi prepared statement
     * @return boolean Kết quả thực thi
     */
    public function execute() {
        try {
            return $this->stmt->execute();
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            error_log("Query Execution Error: " . $this->error);
            return false;
        }
    }
    
    /**
     * Lấy tất cả kết quả dưới dạng mảng đối tượng
     * @return array Mảng các đối tượng
     */
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll();
    }
    
    /**
     * Lấy một kết quả duy nhất dưới dạng đối tượng
     * @return object Đối tượng kết quả
     */
    public function single() {
        $this->execute();
        return $this->stmt->fetch();
    }
    
    /**
     * Lấy số bản ghi bị ảnh hưởng
     * @return int Số bản ghi
     */
    public function rowCount() {
        return $this->stmt->rowCount();
    }
    
    /**
     * Lấy ID của bản ghi mới nhất được chèn
     * @return int ID mới
     */
    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }
    
    /**
     * Bắt đầu transaction
     * @return void
     */
    public function beginTransaction() {
        $this->dbh->beginTransaction();
    }
    
    /**
     * Commit transaction
     * @return void
     */
    public function commit() {
        $this->dbh->commit();
    }
    
    /**
     * Rollback transaction
     * @return void
     */
    public function rollBack() {
        $this->dbh->rollBack();
    }
    
    /**
     * Get PDO connection instance
     * @return PDO
     */
    public function getConnection() {
        return $this->dbh;
    }
    
    /**
     * Debug: Dump parameters bound to the prepared statement
     * @return void
     */
    public function debugParams() {
        return $this->stmt->debugDumpParams();
    }
}