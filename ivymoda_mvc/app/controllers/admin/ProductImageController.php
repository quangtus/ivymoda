<?php
namespace admin;

class ProductImageController extends \Controller {
    private $productModel;
    
    public function __construct() {
        $this->productModel = $this->model('ProductModel');
        
        // Kiểm tra đăng nhập và quyền nhân viên (admin + staff)
        if(!isset($_SESSION['user_id'])) {
            $this->redirect('admin/auth/login');
            exit;
        }
        
        if($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 3) {
            $this->redirect('admin/auth/login');
            exit;
        }
    }
    
    /**
     * Hiển thị danh sách ảnh sản phẩm
     */
    public function index($product_id = null) {
        if(!$product_id) {
            $_SESSION['error'] = 'Không tìm thấy sản phẩm';
            $this->redirect('admin/product');
            exit;
        }
        
        $product = $this->productModel->getProductById($product_id);
        if(!$product) {
            $_SESSION['error'] = 'Không tìm thấy sản phẩm';
            $this->redirect('admin/product');
            exit;
        }
        
        $selectedColorId = isset($_GET['color_id']) && $_GET['color_id'] !== '' ? (int)$_GET['color_id'] : null;
        $productImages = $this->productModel->getProductImages($product_id, $selectedColorId);
        $availableColors = $this->productModel->getProductColors($product_id);
        
        $this->view('admin/product/images', [
            'title' => 'Quản lý ảnh sản phẩm - ' . $product->sanpham_tieude,
            'product' => $product,
            'productImages' => $productImages,
            'availableColors' => $availableColors,
            'selectedColorId' => $selectedColorId,
            'success' => $_SESSION['success'] ?? '',
            'error' => $_SESSION['error'] ?? ''
        ]);
        
        // Xóa thông báo sau khi hiển thị
        unset($_SESSION['success']);
        unset($_SESSION['error']);
    }
    
    /**
     * Upload ảnh sản phẩm
     */
    public function upload() {
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
            $product_id = (int)$_POST['product_id'];
            $color_id = isset($_POST['color_id']) && $_POST['color_id'] !== '' ? (int)$_POST['color_id'] : null;

            // New grouped upload by color (multi-image groups)
            if(isset($_POST['color_group']) && is_array($_POST['color_group'])) {
                $groupResult = $this->handleGroupedImageUploads($product_id);
                if($groupResult['success']) {
                    $_SESSION['success'] = 'Upload ảnh thành công! Đã tạo ' . count($groupResult['colors']) . ' nhóm màu.';
                    
                    // Redirect về màu cuối cùng (mới nhất) đã upload
                    $redirectUrl = 'admin/productimage/' . $product_id;
                    if (!empty($groupResult['colors'])) {
                        $lastColorId = end($groupResult['colors']);
                        if ($lastColorId !== null && $lastColorId !== '') {
                            $redirectUrl .= '?color_id=' . (int)$lastColorId;
                        }
                    }
                    $this->redirect($redirectUrl);
                } else {
                    $_SESSION['error'] = $groupResult['error'];
                    $this->redirect('admin/productimage/' . $product_id);
                }
                return;
            }

            // Legacy single/multi image upload
            $is_primary = isset($_POST['is_primary']) ? 1 : 0;
            
            // Convert color_id to sanpham_color_id if color is selected
            $sanpham_color_id = null;
            if ($color_id !== null) {
                $sanpham_color_id = $this->productModel->getSanphamColorId($product_id, $color_id);
                if ($sanpham_color_id === null) {
                    $_SESSION['error'] = 'Màu sắc chưa được gán cho sản phẩm này';
                    $this->redirect('admin/productimage/' . $product_id);
                    return;
                }
            }
            
            // Hỗ trợ upload nhiều ảnh
            $results = $this->handleMultipleImageUploads();
            if ($results['success'] && !empty($results['filenames'])) {
                $ok = true;
                foreach ($results['filenames'] as $idx => $filename) {
                    $flagPrimary = ($is_primary && $idx === 0) ? 1 : 0;
                    if (!$this->productModel->addProductImage($product_id, $filename, $flagPrimary, $sanpham_color_id)) {
                        $ok = false;
                    }
                }
                $_SESSION['success'] = $ok ? 'Upload ảnh thành công!' : 'Một số ảnh không thể lưu.';
            } else {
                $_SESSION['error'] = $results['error'] ?? 'Không thể upload ảnh';
            }
            
            $redirectUrl = 'admin/productimage/' . $product_id;
            if ($color_id !== null) {
                $redirectUrl .= '?color_id=' . $color_id;
            }
            $this->redirect($redirectUrl);
        }
    }

    /**
     * Xử lý upload ảnh theo nhóm màu (hỗ trợ nhiều ảnh cho mỗi màu)
     */
    private function handleGroupedImageUploads(int $productId): array {
        if(!isset($_POST['color_group']) || !is_array($_POST['color_group']) || !isset($_FILES['product_image_group'])) {
            return ['success' => false, 'error' => 'Dữ liệu upload theo nhóm không hợp lệ'];
        }
        $uploadDir = ROOT_PATH . 'public/assets/uploads/';
        if(!is_dir($uploadDir)) { mkdir($uploadDir, 0755, true); }
        $uploadedFiles = [];
        $uploadedColors = []; // Track uploaded colors
        foreach ($_POST['color_group'] as $idx => $colorValue) {
            $colorId = ($colorValue !== '' && $colorValue !== null) ? (int)$colorValue : null;
            // get group file lists for this index
            $names  = $_FILES['product_image_group']['name'][$idx] ?? null;
            $types  = $_FILES['product_image_group']['type'][$idx] ?? null;
            $temps  = $_FILES['product_image_group']['tmp_name'][$idx] ?? null;
            $errors = $_FILES['product_image_group']['error'][$idx] ?? null;
            $sizes  = $_FILES['product_image_group']['size'][$idx] ?? null;
            if($names === null) { continue; }
            // Normalize to arrays
            if(!is_array($names)) { $names = [$names]; $types = [$types]; $temps = [$temps]; $errors = [$errors]; $sizes = [$sizes]; }
            $groupFilenames = [];
            foreach ($names as $k => $nm) {
                if($errors[$k] != 0) continue;
                if(!in_array($types[$k], ['image/jpeg','image/jpg','image/png','image/gif','image/webp'])) continue;
                if($sizes[$k] > 10 * 1024 * 1024) continue;
                $extension = pathinfo($nm, PATHINFO_EXTENSION);
                $filename = 'product_' . time() . '_' . uniqid() . '.' . $extension;
                $filepath = $uploadDir . $filename;
                if(move_uploaded_file($temps[$k], $filepath)) {
                    $groupFilenames[] = $filename;
                }
            }
            if(!empty($groupFilenames)) {
                // TỰ ĐỘNG TẠO MÀU + VARIANT NẾU CHƯA CÓ
                $sanphamColorId = null;
                if($colorId !== null) {
                    $uploadedColors[] = $colorId; // Track màu này
                    $sanphamColorId = $this->productModel->getSanphamColorId($productId, $colorId);
                    
                    // Nếu màu chưa được gán cho sản phẩm → Tự động tạo
                    if ($sanphamColorId === null) {
                        // 1. Thêm màu vào sản phẩm (tbl_sanpham_color)
                        $result = $this->productModel->query(
                            "INSERT INTO tbl_sanpham_color (sanpham_id, color_id, created_at) VALUES (?, ?, NOW())",
                            [$productId, $colorId]
                        );
                        
                        if($result) {
                            $sanphamColorId = $this->productModel->getLastInsertId();
                            
                            // 2. Tự động tạo variant mặc định (Size S, tồn kho 0)
                            $defaultSizeId = $this->productModel->getOne(
                                "SELECT size_id FROM tbl_size WHERE size_ten = 'S' LIMIT 1"
                            );
                            
                            if($defaultSizeId) {
                                $sizeId = is_object($defaultSizeId) ? $defaultSizeId->size_id : $defaultSizeId['size_id'];
                                
                                // Lấy thông tin sản phẩm
                                $product = $this->productModel->getOne(
                                    "SELECT sanpham_ma FROM tbl_sanpham WHERE sanpham_id = ?",
                                    [$productId]
                                );
                                
                                $productCode = is_object($product) ? $product->sanpham_ma : $product['sanpham_ma'];
                                
                                // Lấy tên màu
                                $colorInfo = $this->productModel->getOne(
                                    "SELECT color_ten FROM tbl_color WHERE color_id = ?",
                                    [$colorId]
                                );
                                $colorName = is_object($colorInfo) ? $colorInfo->color_ten : $colorInfo['color_ten'];
                                
                                // Tạo SKU: MA_SAN_PHAM-SIZE-COLOR (ví dụ: AK-001-S-XANH)
                                $sku = strtoupper($productCode) . '-S-' . strtoupper(substr($colorName, 0, 5));
                                
                                // Thêm variant
                                $this->productModel->query(
                                    "INSERT INTO tbl_product_variant (sanpham_id, color_id, size_id, sku, ton_kho, trang_thai, created_at) 
                                     VALUES (?, ?, ?, ?, 0, 1, NOW())",
                                    [$productId, $colorId, $sizeId, $sku]
                                );
                            }
                        }
                    }
                }
                
                $groupPrimary = isset($_POST['is_primary'][$idx]) ? 1 : 0;
                $first = true;
                foreach ($groupFilenames as $fname) {
                    $flagPrimary = ($groupPrimary && $first) ? 1 : 0;
                    $first = false;
                    $this->productModel->addProductImage($productId, $fname, $flagPrimary, $sanphamColorId);
                    $uploadedFiles[] = $fname;
                }
            }
        }
        if(empty($uploadedFiles)) {
            return ['success' => false, 'error' => 'Không có ảnh hợp lệ được upload'];
        }
        return [
            'success' => true, 
            'filenames' => $uploadedFiles,
            'colors' => array_unique($uploadedColors) // Danh sách màu đã upload (loại trùng)
        ];
    }
    
    /**
     * Đặt ảnh làm ảnh chính
     */
    public function setPrimary() {
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['image_id']) && isset($_POST['product_id'])) {
            $image_id = (int)$_POST['image_id'];
            $product_id = (int)$_POST['product_id'];
            
            $result = $this->productModel->setPrimaryImage($product_id, $image_id);
            
            if($result) {
                $_SESSION['success'] = 'Đã đặt làm ảnh chính!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra';
            }
            
            $this->redirect('admin/productimage/' . $product_id);
        }
    }
    
    /**
     * Xóa ảnh sản phẩm
     */
    public function delete() {
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['image_id']) && isset($_POST['product_id'])) {
            $image_id = (int)$_POST['image_id'];
            $product_id = (int)$_POST['product_id'];
            
            // Lấy thông tin ảnh trước khi xóa
            $image = $this->productModel->getOne("SELECT * FROM tbl_anhsanpham WHERE anh_id = ?", [$image_id]);
            
            $result = $this->productModel->deleteProductImage($image_id);
            
            if($result) {
                // Xóa file ảnh khỏi server
                if($image && !empty($image->anh_path)) {
                    $filepath = ROOT_PATH . 'public/assets/uploads/' . $image->anh_path;
                    if(file_exists($filepath)) {
                        unlink($filepath);
                    }
                }
                
                $_SESSION['success'] = 'Xóa ảnh thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi xóa ảnh';
            }
            
            $this->redirect('admin/productimage/' . $product_id);
        }
    }

    /**
     * AJAX: Thêm variant mới (size + màu)
     */
    public function addVariantAjax() {
        header('Content-Type: application/json');
        
        if($_SERVER['REQUEST_METHOD'] != 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            exit;
        }
        
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $colorId = isset($_POST['color_id']) ? (int)$_POST['color_id'] : 0;
        $sizeId = isset($_POST['size_id']) ? (int)$_POST['size_id'] : 0;
        $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
        
        if(!$productId || !$colorId || !$sizeId) {
            echo json_encode(['success' => false, 'error' => 'Thiếu thông tin bắt buộc']);
            exit;
        }
        
        // Kiểm tra variant đã tồn tại chưa
        $existing = $this->productModel->getOne(
            "SELECT variant_id FROM tbl_product_variant WHERE sanpham_id = ? AND color_id = ? AND size_id = ?",
            [$productId, $colorId, $sizeId]
        );
        
        if($existing) {
            echo json_encode(['success' => false, 'error' => 'Variant này đã tồn tại']);
            exit;
        }
        
        // Lấy thông tin để tạo SKU
        $product = $this->productModel->getOne(
            "SELECT sanpham_ma FROM tbl_sanpham WHERE sanpham_id = ?",
            [$productId]
        );
        $productCode = is_object($product) ? $product->sanpham_ma : $product['sanpham_ma'];
        
        $colorInfo = $this->productModel->getOne(
            "SELECT color_ten FROM tbl_color WHERE color_id = ?",
            [$colorId]
        );
        $colorName = is_object($colorInfo) ? $colorInfo->color_ten : $colorInfo['color_ten'];
        
        $sizeInfo = $this->productModel->getOne(
            "SELECT size_ten FROM tbl_size WHERE size_id = ?",
            [$sizeId]
        );
        $sizeName = is_object($sizeInfo) ? $sizeInfo->size_ten : $sizeInfo['size_ten'];
        
        // Tạo SKU: MA_SAN_PHAM-SIZE-COLOR
        $sku = strtoupper($productCode) . '-' . strtoupper($sizeName) . '-' . strtoupper(substr($colorName, 0, 5));
        
        // Thêm variant
        $result = $this->productModel->execute(
            "INSERT INTO tbl_product_variant (sanpham_id, color_id, size_id, sku, ton_kho, trang_thai, created_at) 
             VALUES (?, ?, ?, ?, ?, 1, NOW())",
            [$productId, $colorId, $sizeId, $sku, $stock]
        );
        
        if($result) {
            echo json_encode(['success' => true, 'message' => 'Thêm variant thành công']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Lỗi khi thêm variant vào database']);
        }
        exit;
    }
    
    /**
     * Xóa toàn bộ ảnh theo nhóm màu
     */
    public function deleteColorGroup() {
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id']) && isset($_POST['color_id'])) {
            $product_id = (int)$_POST['product_id'];
            $color_id = (int)$_POST['color_id'];
            // Lấy danh sách ảnh để xóa file vật lý
            $images = $this->productModel->getAll("SELECT * FROM tbl_anhsanpham WHERE sanpham_id = ? AND color_id = ?", [$product_id, $color_id]);
            $result = $this->productModel->deleteImagesByProductAndColor($product_id, $color_id);
            if ($result) {
                if ($images) {
                    foreach ($images as $img) {
                        if(!empty($img->anh_path)) {
                            $filepath = ROOT_PATH . 'public/assets/uploads/' . $img->anh_path;
                            if(file_exists($filepath)) { @unlink($filepath); }
                        }
                    }
                }
                $_SESSION['success'] = 'Đã xóa toàn bộ ảnh cho nhóm màu.';
            } else {
                $_SESSION['error'] = 'Không thể xóa nhóm ảnh.';
            }
            $this->redirect('admin/productimage/' . $product_id);
        }
    }
    
    /**
     * Xử lý upload ảnh
     */
    private function handleImageUpload() {
        if(!isset($_FILES['product_image']) || $_FILES['product_image']['error'] != 0) {
            return ['success' => false, 'error' => 'Vui lòng chọn ảnh sản phẩm'];
        }
        
        $file = $_FILES['product_image'];
        $uploadDir = ROOT_PATH . 'public/assets/uploads/';
        
        // Tạo thư mục nếu chưa có
        if(!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Kiểm tra loại file
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if(!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Chỉ cho phép upload file ảnh (JPG, PNG, GIF, WebP)'];
        }
        
        // Kiểm tra kích thước file (max 10MB)
        if($file['size'] > 10 * 1024 * 1024) {
            return ['success' => false, 'error' => 'Kích thước file không được vượt quá 10MB'];
        }
        
        // Tạo tên file unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        if(move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'filename' => $filename];
        } else {
            return ['success' => false, 'error' => 'Lỗi khi upload file'];
        }
    }

    /**
     * Upload nhiều ảnh với input name=product_image (multiple)
     */
    private function handleMultipleImageUploads() {
        if(!isset($_FILES['product_image'])) {
            return ['success' => false, 'error' => 'Vui lòng chọn ảnh'];
        }
        $files = $_FILES['product_image'];
        $isMultiple = is_array($files['name']);
        $filenames = [];
        if (!$isMultiple) {
            $res = $this->handleImageUpload();
            if ($res['success']) return ['success' => true, 'filenames' => [$res['filename']]];
            return $res;
        }
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $uploadDir = ROOT_PATH . 'public/assets/uploads/';
        if(!is_dir($uploadDir)) { mkdir($uploadDir, 0755, true); }
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] !== 0) { continue; }
            if (!in_array($files['type'][$i], $allowedTypes)) { continue; }
            if ($files['size'][$i] > 10 * 1024 * 1024) { continue; }
            $extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $filename = 'product_' . time() . '_' . uniqid() . '.' . $extension;
            $filepath = $uploadDir . $filename;
            if (move_uploaded_file($files['tmp_name'][$i], $filepath)) {
                $filenames[] = $filename;
            }
        }
        if (empty($filenames)) {
            return ['success' => false, 'error' => 'Không có ảnh hợp lệ được upload'];
        }
        return ['success' => true, 'filenames' => $filenames];
    }
}
