<?php
/**
 * AJAX Endpoint: Lấy danh sách sizes theo product_id và color_id
 * 
 * Trả về JSON array chứa thông tin các sizes có sẵn cho màu đã chọn:
 * - size_id: ID của size
 * - size_ten: Tên size (XS, S, M, L, XL, 2XL, 3XL)
 * - ton_kho: Số lượng tồn kho
 * - variant_id: ID của variant (dùng để add to cart)
 * - trang_thai: 1=còn hàng, 0=hết hàng
 * 
 * @param GET product_id - ID sản phẩm
 * @param GET color_id - ID màu sắc
 * @return JSON {success: bool, data: array, message: string}
 */

// Set header JSON
header('Content-Type: application/json; charset=utf-8');

// Include config và Database
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/core/Model.php';
require_once __DIR__ . '/../../app/models/ProductModel.php';

try {
    // Validate input
    if (!isset($_GET['product_id']) || !isset($_GET['color_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Thiếu tham số product_id hoặc color_id',
            'data' => []
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $productId = (int)$_GET['product_id'];
    $colorId = (int)$_GET['color_id'];
    
    if ($productId <= 0 || $colorId <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'product_id và color_id phải là số dương',
            'data' => []
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Khởi tạo ProductModel
    $productModel = new ProductModel();
    
    // Lấy danh sách sizes theo product và color
    $sizes = $productModel->getVariantsByProductAndColor($productId, $colorId);
    
    if (empty($sizes)) {
        echo json_encode([
            'success' => true,
            'message' => 'Không có size nào cho màu này',
            'data' => []
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Format dữ liệu trả về
    $formattedSizes = [];
    foreach ($sizes as $size) {
        $formattedSizes[] = [
            'size_id' => (int)$size->size_id,
            'size_ten' => $size->size_ten,
            'variant_id' => (int)$size->variant_id,
            'ton_kho' => (int)$size->ton_kho,
            'gia_ban' => $size->gia_ban ? (float)$size->gia_ban : null,
            'trang_thai' => (int)$size->trang_thai,
            'sku' => $size->sku ?? ''
        ];
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Lấy danh sách sizes thành công',
        'data' => $formattedSizes
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi server: ' . $e->getMessage(),
        'data' => []
    ], JSON_UNESCAPED_UNICODE);
}
