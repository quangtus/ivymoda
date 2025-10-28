<?php
// filepath: c:\xampp\htdocs\ivymoda\ivymoda_mvc\includes\functions.php

/**
 * File chứa các hàm helper sử dụng trong toàn bộ ứng dụng
 */

// Hàm debug
function debug($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

// Hàm chuyển hướng
function redirect($url) {
    header('Location: ' . BASE_URL . $url);
    exit;
}

// Hàm hiển thị thông báo
function showAlert($message, $type = 'success') {
    $_SESSION['alert'] = [
        'message' => $message,
        'type' => $type
    ];
}

// Hàm lấy thông báo
function getAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        unset($_SESSION['alert']);
        return $alert;
    }
    return null;
}

/**
 * Hàm định dạng giá tiền
 */
function format_currency($amount) {
    return number_format($amount, 0, ',', '.') . 'đ';
}

/**
 * Hàm lấy URL hiện tại
 */
function current_url() {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

/**
 * Hàm kiểm tra đăng nhập
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Hàm kiểm tra quyền admin
 */
function is_admin() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1;
}

/**
 * Hàm tạo slug từ chuỗi
 */
function create_slug($string) {
    $search = array(
        '#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#',
        '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#',
        '#(ì|í|ị|ỉ|ĩ)#',
        '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#',
        '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#',
        '#(ỳ|ý|ỵ|ỷ|ỹ)#',
        '#(đ)#',
        '#[^a-z0-9\-\_]#i',
        '#\-+#'
    );
    
    $replace = array(
        'a',
        'e',
        'i',
        'o',
        'u',
        'y',
        'd',
        '-',
        '-'
    );
    
    $string = strtolower(preg_replace($search, $replace, $string));
    $string = preg_replace('#\-+#', '-', $string);
    return trim($string, '-');
}

/**
 * Hàm cắt chuỗi với ba chấm
 */
function text_truncate($text, $limit = 50) {
    if (strlen($text) <= $limit) {
        return $text;
    }
    
    return substr($text, 0, $limit) . '...';
}

/**
 * Hàm lấy định dạng ngày tháng
 */
function format_date($date, $format = 'd/m/Y H:i') {
    return date($format, strtotime($date));
}

/**
 * Hàm tải file lên
 */
function upload_file($file, $target_dir, $allowed_types = ['jpg', 'jpeg', 'png', 'gif'], $max_size = 5242880) {
    // Kiểm tra lỗi
    if ($file['error'] != 0) {
        return [
            'success' => false,
            'message' => 'Lỗi tải file lên: ' . $file['error']
        ];
    }
    
    // Kiểm tra kích thước
    if ($file['size'] > $max_size) {
        return [
            'success' => false,
            'message' => 'Kích thước file quá lớn. Tối đa ' . ($max_size / 1024 / 1024) . 'MB'
        ];
    }
    
    // Kiểm tra loại file
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_types)) {
        return [
            'success' => false,
            'message' => 'Loại file không được phép. Chỉ chấp nhận: ' . implode(', ', $allowed_types)
        ];
    }
    
    // Tạo tên file mới để tránh trùng lặp
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Tạo thư mục nếu chưa tồn tại
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Tải file lên
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return [
            'success' => true,
            'filename' => $new_filename,
            'path' => $target_file
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Có lỗi khi tải file lên server'
        ];
    }
}

/**
 * Hàm lấy ngôn ngữ hiện tại
 */
function get_current_language() {
    return isset($_SESSION['language']) ? $_SESSION['language'] : 'vi';
}

/**
 * Hàm dịch chuỗi
 */
function translate($key) {
    $lang = get_current_language();
    $lang_file = ROOT_PATH . 'includes/language/' . $lang . '.php';
    
    if (file_exists($lang_file)) {
        include_once $lang_file;
        return isset($lang_text[$key]) ? $lang_text[$key] : $key;
    }
    
    return $key;
}

/**
 * Hàm format tiền tệ VND
 */
function formatCurrency($amount) {
    return number_format($amount, 0, ',', '.') . 'đ';
}

/**
 * Hàm tạo URL thân thiện
 */
function slugify($text) {
    // Chuyển đổi sang chữ thường
    $text = mb_strtolower($text, 'UTF-8');
    
    // Chuyển đổi các ký tự có dấu sang không dấu
    $text = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $text);
    $text = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $text);
    $text = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $text);
    $text = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $text);
    $text = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $text);
    $text = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $text);
    $text = preg_replace('/(đ)/', 'd', $text);
    
    // Xóa ký tự đặc biệt
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    // Xóa khoảng trắng thừa
    $text = preg_replace('/(\s+)/', '-', $text);
    // Xóa dấu gạch ngang ở đầu và cuối
    $text = preg_replace('/(^-+|-+$)/', '', $text);
    
    return $text;
}