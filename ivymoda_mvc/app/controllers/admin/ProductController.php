<?php
namespace admin;

class ProductController extends \Controller {
    private $productModel;
    private $categoryModel;
    
    public function __construct() {
        $this->productModel = $this->model('ProductModel');
        $this->categoryModel = $this->model('CategoryModel');
        
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
     * Hiển thị danh sách sản phẩm
     */
    public function index() {
        // Kiểm tra phân trang
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10; // Số sản phẩm mỗi trang
        $offset = ($page - 1) * $limit;
        
        // Lấy danh sách sản phẩm với thông tin chi tiết
        $products = $this->productModel->getProductsWithDetails($limit, $offset);
        
        // Đếm tổng số sản phẩm để tính phân trang
        $totalProducts = $this->productModel->countAll();
        $totalPages = ceil($totalProducts / $limit);
        
        $this->view('admin/product/index', [
            'title' => 'Quản lý sản phẩm',
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'success' => $_SESSION['success'] ?? '',
            'error' => $_SESSION['error'] ?? ''
        ]);
        
        // Xóa thông báo sau khi hiển thị
        unset($_SESSION['success']);
        unset($_SESSION['error']);
    }
    
    /**
     * Thêm sản phẩm mới
     */
    public function add() {
        $categories = $this->categoryModel->getAllCategories();
        $colorModel = $this->model('ColorModel');
        $colors = $colorModel->getAllColors();
        $sizes = $this->productModel->getAllSizes(); // *** THÊM MỚI ***
        
        $subcategories = [];
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $subcategories[$category->danhmuc_id] = $this->categoryModel->getSubcategoriesByCategoryId($category->danhmuc_id);
            }
        }
        
        $data = [
            'title' => 'Thêm sản phẩm - IVY moda',
            'categories' => $categories,
            'subcategories' => $subcategories,
            'colors' => $colors,
            'sizes' => $sizes, // *** THÊM MỚI ***
            'sanpham_tieude' => '',
            'sanpham_ma' => '',
            'danhmuc_id' => '',
            'loaisanpham_id' => '',
            'sanpham_gia_goc' => '',
            'sanpham_gia' => '',
            'sanpham_giam_gia' => '',
            'sanpham_chitiet' => '',
            'sanpham_baoquan' => '',
            'error' => '',
            'success' => ''
        ];
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data['sanpham_tieude'] = trim($_POST['sanpham_tieude'] ?? '');
            $data['sanpham_ma'] = trim($_POST['sanpham_ma'] ?? '');
            $data['danhmuc_id'] = (int)($_POST['danhmuc_id'] ?? 0);
            $data['loaisanpham_id'] = (int)($_POST['loaisanpham_id'] ?? 0);
            $data['sanpham_gia_goc'] = trim($_POST['sanpham_gia_goc'] ?? '');
            $data['sanpham_gia'] = trim($_POST['sanpham_gia'] ?? '');
            $data['sanpham_giam_gia'] = trim($_POST['sanpham_giam_gia'] ?? '');
            $data['sanpham_chitiet'] = trim($_POST['sanpham_chitiet'] ?? '');
            $data['sanpham_baoquan'] = trim($_POST['sanpham_baoquan'] ?? '');

            // Validation
            if(empty($data['sanpham_tieude']) || empty($data['sanpham_ma']) || 
               empty($data['danhmuc_id']) || empty($data['loaisanpham_id']) || 
               empty($data['sanpham_gia_goc']) || empty($data['sanpham_gia'])) {
                $data['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc';
            } elseif(!is_numeric($data['sanpham_gia_goc']) || $data['sanpham_gia_goc'] <= 0) {
                $data['error'] = 'Giá gốc phải là số dương';
            } elseif(!is_numeric($data['sanpham_gia']) || $data['sanpham_gia'] <= 0) {
                $data['error'] = 'Giá bán phải là số dương';
            } elseif($data['sanpham_gia'] > $data['sanpham_gia_goc']) {
                $data['error'] = 'Giá bán không được cao hơn giá gốc';
            } else {
                // Xử lý upload ảnh theo nhóm màu
                $uploadDir = ROOT_PATH . 'public/assets/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $productImagesByColor = []; // [color_id => [image_paths...]]
                $firstImagePath = null;

                // Xử lý từng nhóm ảnh theo màu
                if (isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'])) {
                    $imageColorGroups = $_POST['image_color_groups'] ?? [];
                    
                    foreach ($_FILES['product_images']['name'] as $groupIndex => $filesArray) {
                        if (!is_array($filesArray)) continue;
                        
                        $colorId = isset($imageColorGroups[$groupIndex]) && $imageColorGroups[$groupIndex] !== '' 
                            ? (int)$imageColorGroups[$groupIndex] 
                            : null;
                        
                        foreach ($filesArray as $fileIndex => $fileName) {
                            if (empty($fileName)) continue;
                            
                            $tmpName = $_FILES['product_images']['tmp_name'][$groupIndex][$fileIndex];
                            $error = $_FILES['product_images']['error'][$groupIndex][$fileIndex];
                            
                            if ($error === UPLOAD_ERR_OK) {
                                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                
                                if (in_array($fileExtension, $allowedExtensions)) {
                                    $newFileName = uniqid() . '_' . time() . '.' . $fileExtension;
                                    $targetPath = $uploadDir . $newFileName;
                                    
                                    if (move_uploaded_file($tmpName, $targetPath)) {
                                        if ($colorId !== null) {
                                            if (!isset($productImagesByColor[$colorId])) {
                                                $productImagesByColor[$colorId] = [];
                                            }
                                            $productImagesByColor[$colorId][] = $newFileName;
                                        }
                                        
                                        if ($firstImagePath === null) {
                                            $firstImagePath = $newFileName;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                // Nếu không có ảnh nào, báo lỗi
                if ($firstImagePath === null) {
                    $data['error'] = 'Vui lòng upload ít nhất 1 ảnh sản phẩm';
                } else {
                    // Tính toán phần trăm giảm giá
                    $giaGoc = (float)$data['sanpham_gia_goc'];
                    $giaBan = (float)$data['sanpham_gia'];
                    $phanTramGiam = $giaGoc > 0 ? (($giaGoc - $giaBan) / $giaGoc * 100) : 0;
                    
                    // Thêm sản phẩm vào database
                    $result = $this->productModel->addProduct(
                        $data['sanpham_tieude'], 
                        $data['sanpham_ma'], 
                        $data['danhmuc_id'], 
                        $data['loaisanpham_id'], 
                        $data['sanpham_gia_goc'],
                        $data['sanpham_gia'], 
                        $phanTramGiam,
                        $data['sanpham_chitiet'], 
                        $data['sanpham_baoquan'], 
                        $firstImagePath
                    );

                    if($result === true || is_int($result)) {
                        $newProductId = is_int($result) ? $result : $this->productModel->getLastInsertId();

                        // Lưu các màu của sản phẩm
                        $selectedColors = isset($_POST['color_ids']) && is_array($_POST['color_ids']) 
                            ? array_map('intval', $_POST['color_ids']) 
                            : [];
                        
                        // Thêm màu từ các nhóm ảnh vào danh sách màu
                        foreach (array_keys($productImagesByColor) as $cid) {
                            if (!in_array($cid, $selectedColors, true)) {
                                $selectedColors[] = $cid;
                            }
                        }
                        
                        // Đảm bảo có ít nhất 1 màu
                        if (empty($selectedColors)) {
                            $selectedColors = [1]; // Màu mặc định
                        }

                        if ($newProductId) {
                            $this->productModel->setProductColors($newProductId, $selectedColors);

                            // Lưu ảnh theo từng màu
                            foreach ($productImagesByColor as $colorId => $imagePaths) {
                                // Lấy sanpham_color_id từ bảng liên kết
                                $colorLink = $this->productModel->getOne(
                                    "SELECT sanpham_color_id FROM tbl_sanpham_color 
                                     WHERE sanpham_id = ? AND color_id = ?", 
                                    [$newProductId, $colorId]
                                );
                                
                                if ($colorLink) {
                                    $sanphamColorId = $colorLink->sanpham_color_id;
                                    
                                    foreach ($imagePaths as $idx => $imagePath) {
                                        $isPrimary = ($idx === 0 && $imagePath === $firstImagePath) ? 1 : 0;
                                        $this->productModel->addProductImage(
                                            $newProductId, 
                                            $imagePath, 
                                            $isPrimary, 
                                            $sanphamColorId
                                        );
                                    }
                                }
                            }
                            
                            // *** THÊM MỚI: Xử lý variants (size + màu + tồn kho) ***
                            $variantCount = 0;
                            if (isset($_POST['variants']) && is_array($_POST['variants'])) {
                                foreach ($_POST['variants'] as $colorId => $sizeData) {
                                    foreach ($sizeData as $sizeId => $variantData) {
                                        $tonKho = (int)($variantData['ton_kho'] ?? 0);
                                        
                                        // Chỉ tạo variant nếu được nhập thông tin
                                        if ($tonKho >= 0) {
                                            // Lấy thông tin màu và size để tạo SKU
                                            $colorInfo = $this->productModel->getColorById($colorId);
                                            $sizeInfo = $this->productModel->getSizeById($sizeId);
                                            
                                            if ($colorInfo && $sizeInfo) {
                                                // Tạo SKU tự động: ASM-001-M-WHITE
                                                $colorName = strtoupper(str_replace(' ', '', $colorInfo->color_ten));
                                                $sizeName = strtoupper($sizeInfo->size_ten);
                                                $sku = strtoupper($data['sanpham_ma']) . '-' . $sizeName . '-' . $colorName;
                                                
                                                // Insert variant
                                                $this->productModel->addProductVariant([
                                                    'sanpham_id' => $newProductId,
                                                    'color_id' => $colorId,
                                                    'size_id' => $sizeId,
                                                    'sku' => $sku,
                                                    'ton_kho' => $tonKho,
                                                    'trang_thai' => $tonKho > 0 ? 1 : 0
                                                ]);
                                                
                                                $variantCount++;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        $_SESSION['success'] = "Thêm sản phẩm thành công với $variantCount variants!";
                        $this->redirect('admin/product');
                        exit;
                    } else {
                        $data['error'] = $result;
                    }
                }
            }
        }
        
        $this->view('admin/product/add', $data);
    }
    
    /**
     * Sửa sản phẩm
     * @param int $id ID của sản phẩm
     */
    public function edit($id = null) {
        if(!$id) {
            $_SESSION['error'] = 'Không tìm thấy sản phẩm';
            $this->redirect('admin/product');
            exit;
        }
        
        $product = $this->productModel->getProductById($id);
        
        if(!$product) {
            $_SESSION['error'] = 'Không tìm thấy sản phẩm';
            $this->redirect('admin/product');
            exit;
        }
        
        $categories = $this->categoryModel->getAllCategories();
        $colorModel = $this->model('ColorModel');
        $allColors = $colorModel->getAllColors();
        $sizes = $this->productModel->getAllSizes(); // *** THÊM MỚI ***
        
        // Lấy tất cả loại sản phẩm
        $subcategories = [];
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $subcategories[$category->danhmuc_id] = $this->categoryModel->getSubcategoriesByCategoryId($category->danhmuc_id);
            }
        }
        
        // Lấy thông tin hiện tại
        $productImages = $this->productModel->getProductImages((int)$id);
        $productColors = $this->productModel->getProductColors((int)$id);
        $variants = $this->productModel->getProductVariants($id); // *** THÊM MỚI ***

        // *** EXTRACT BIẾN TỪ PRODUCT OBJECT ***
        $sanpham_tieude = $product->sanpham_tieude ?? '';
        $sanpham_ma = $product->sanpham_ma ?? '';
        $sanpham_chitiet = $product->sanpham_chitiet ?? '';
        $sanpham_baoquan = $product->sanpham_baoquan ?? '';
        $danhmuc_id = $product->danhmuc_id ?? 0;
        $loaisanpham_id = $product->loaisanpham_id ?? 0;
        $sanpham_gia = $product->sanpham_gia ?? 0;
        $sanpham_gia_goc = $product->sanpham_gia_goc ?? 0;
        $sanpham_giam_gia = $product->sanpham_giam_gia ?? 0;
        $sanpham_anh = $product->sanpham_anh ?? '';
        $sanpham_status = $product->sanpham_status ?? 1;

        $data = [
            'title' => 'Sửa sản phẩm - ' . $product->sanpham_tieude,
            'product' => $product,
            'sanpham_tieude' => $sanpham_tieude,
            'sanpham_ma' => $sanpham_ma,
            'sanpham_chitiet' => $sanpham_chitiet,
            'sanpham_baoquan' => $sanpham_baoquan,
            'danhmuc_id' => $danhmuc_id,
            'loaisanpham_id' => $loaisanpham_id,
            'sanpham_gia' => $sanpham_gia,
            'sanpham_gia_goc' => $sanpham_gia_goc,
            'sanpham_giam_gia' => $sanpham_giam_gia,
            'sanpham_anh' => $sanpham_anh,
            'sanpham_status' => $sanpham_status,
            'categories' => $categories,
            'subcategories' => $subcategories,
            'colors' => $allColors,
            'productColors' => $productColors,
            'productColorIds' => $this->productModel->getProductColorIds((int)$id),
            'productImages' => $productImages,
            'sizes' => $sizes, // *** THÊM MỚI ***
            'variants' => $variants, // *** THÊM MỚI ***
            'error' => '',
            'success' => ''
        ];
        
        // Xử lý cập nhật sản phẩm
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $updateData = [
                'sanpham_id' => $id,
                'sanpham_tieude' => trim($_POST['sanpham_tieude'] ?? ''),
                'sanpham_ma' => trim($_POST['sanpham_ma'] ?? ''),
                'danhmuc_id' => (int)($_POST['danhmuc_id'] ?? 0),
                'loaisanpham_id' => (int)($_POST['loaisanpham_id'] ?? 0),
                'sanpham_gia_goc' => trim($_POST['sanpham_gia_goc'] ?? ''),
                'sanpham_gia' => trim($_POST['sanpham_gia'] ?? ''),
                'sanpham_giam_gia' => trim($_POST['sanpham_giam_gia'] ?? ''),
                'sanpham_chitiet' => trim($_POST['sanpham_chitiet'] ?? ''),
                'sanpham_baoquan' => trim($_POST['sanpham_baoquan'] ?? ''),
                'sanpham_status' => isset($_POST['sanpham_status']) ? 1 : 0
            ];
            
            // Validation
            if(empty($updateData['sanpham_tieude']) || empty($updateData['sanpham_ma']) || 
               empty($updateData['danhmuc_id']) || empty($updateData['loaisanpham_id']) || 
               empty($updateData['sanpham_gia_goc']) || empty($updateData['sanpham_gia'])) {
                $data['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc';
                $this->view('admin/product/edit', $data);
                return;
            }
            
            if(!is_numeric($updateData['sanpham_gia_goc']) || $updateData['sanpham_gia_goc'] <= 0) {
                $data['error'] = 'Giá gốc phải là số dương';
                $this->view('admin/product/edit', $data);
                return;
            }
            
            if(!is_numeric($updateData['sanpham_gia']) || $updateData['sanpham_gia'] <= 0) {
                $data['error'] = 'Giá bán phải là số dương';
                $this->view('admin/product/edit', $data);
                return;
            }
            
            if($updateData['sanpham_gia'] > $updateData['sanpham_gia_goc']) {
                $data['error'] = 'Giá bán không được cao hơn giá gốc';
                $this->view('admin/product/edit', $data);
                return;
            }
            
            // Xử lý upload ảnh mới (nếu có)
            if(isset($_FILES['sanpham_anh']) && $_FILES['sanpham_anh']['error'] == 0) {
                $uploadResult = $this->handleImageUpload();
                if($uploadResult['success']) {
                    $this->deleteOldImage($product->sanpham_anh);
                    $updateData['sanpham_anh'] = $uploadResult['filename'];
                } else {
                    $data['error'] = $uploadResult['error'];
                    $this->view('admin/product/edit', $data);
                    return;
                }
            }
            
            // Tính toán lại phần trăm giảm giá
            $giaGoc = (float)$updateData['sanpham_gia_goc'];
            $giaBan = (float)$updateData['sanpham_gia'];
            $updateData['sanpham_giam_gia'] = $giaGoc > 0 ? (($giaGoc - $giaBan) / $giaGoc * 100) : 0;
            
            // Cập nhật thông tin cơ bản
            $result = $this->productModel->updateProductArray($updateData);
            
            if($result) {
                // Xử lý cập nhật màu sắc - CHỈ CẬP NHẬT KHI CÓ DỮ LIỆU
                if(isset($_POST['colors']) && is_array($_POST['colors']) && !empty($_POST['colors'])) {
                    $this->productModel->deleteProductColors($id);
                    foreach($_POST['colors'] as $colorId) {
                        $this->productModel->addProductColor($id, $colorId);
                    }
                }
                // Nếu không có colors trong POST, giữ nguyên màu sắc hiện tại
                
                // Xử lý upload ảnh nhiều màu (nếu có) - TODO: Implement nếu cần
                // if(isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'])) {
                //     // Xử lý tương tự như trong add()
                // }
                
                // Xử lý cập nhật variants - CHỈ CẬP NHẬT KHI CÓ DỮ LIỆU
                if(isset($_POST['variants']) && is_array($_POST['variants']) && !empty($_POST['variants'])) {
                    foreach($_POST['variants'] as $colorId => $sizes) {
                        foreach($sizes as $sizeId => $variantData) {
                            $tonKho = (int)($variantData['ton_kho'] ?? 0);
                            
                            // Kiểm tra xem variant đã tồn tại chưa
                            $existingVariant = $this->productModel->getVariantByProductColorSize($id, $colorId, $sizeId);
                            
                            if($existingVariant) {
                                // Cập nhật tồn kho
                                $this->productModel->updateVariantStock($existingVariant->variant_id, $tonKho);
                            } else {
                                // Tạo variant mới
                                $color = $this->productModel->getColorById($colorId);
                                $size = $this->productModel->getSizeById($sizeId);
                                
                                if($color && $size) {
                                    $sku = strtoupper($updateData['sanpham_ma']) . '-' . $size->size_ten . '-' . $color->color_ten;
                                    
                                    $this->productModel->addProductVariant([
                                        'sanpham_id' => $id,
                                        'color_id' => $colorId,
                                        'size_id' => $sizeId,
                                        'sku' => $sku,
                                        'ton_kho' => $tonKho,
                                        'gia_ban' => null,
                                        'trang_thai' => $tonKho > 0 ? 1 : 0
                                    ]);
                                }
                            }
                        }
                    }
                }
                // Nếu không có variants trong POST, giữ nguyên variants hiện tại
                
                // Xử lý thêm variants mới từ form (new_variants)
                // Format: $_POST['new_variants'][$colorId][$sizeId]['ton_kho']
                if(isset($_POST['new_variants']) && is_array($_POST['new_variants'])) {
                    foreach($_POST['new_variants'] as $colorId => $sizes) {
                        foreach($sizes as $sizeId => $variantData) {
                            $tonKho = (int)($variantData['ton_kho'] ?? 0);
                            
                            // Chỉ tạo variant nếu tồn kho > 0 (tránh tạo variant rỗng)
                            if($tonKho > 0) {
                                // Kiểm tra xem variant đã tồn tại chưa (tránh trùng lặp)
                                $existingVariant = $this->productModel->getVariantByProductColorSize($id, $colorId, $sizeId);
                                
                                if(!$existingVariant) {
                                    // Lấy thông tin color và size
                                    $color = $this->productModel->getColorById($colorId);
                                    $size = $this->productModel->getSizeById($sizeId);
                                    
                                    if($color && $size) {
                                        // Tạo SKU
                                        $sku = strtoupper($updateData['sanpham_ma']) . '-' . $size->size_ten . '-' . $color->color_ten;
                                        
                                        // Thêm variant mới
                                        $this->productModel->addProductVariant([
                                            'sanpham_id' => $id,
                                            'color_id' => $colorId,
                                            'size_id' => $sizeId,
                                            'sku' => $sku,
                                            'ton_kho' => $tonKho,
                                            'gia_ban' => null, // Có thể để null hoặc dùng giá sản phẩm
                                            'trang_thai' => 1 // Kích hoạt vì ton_kho > 0
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
                
                $_SESSION['success'] = 'Cập nhật sản phẩm thành công!';
                $this->redirect('admin/product');
                return;
            } else {
                $data['error'] = 'Lỗi khi cập nhật sản phẩm';
            }
        }
        
        $this->view('admin/product/edit', $data);
    }
    
    /**
     * Xóa sản phẩm
     * @param int $id ID của sản phẩm
     */
    public function delete($id) {
        if(!$id) {
            $_SESSION['error'] = 'Không tìm thấy sản phẩm';
            $this->redirect('admin/product');
            exit;
        }
        
        $product = $this->productModel->getProductById($id);
        
        if(!$product) {
            $_SESSION['error'] = 'Không tìm thấy sản phẩm';
            $this->redirect('admin/product');
            exit;
        }
        
        // Xóa ảnh đại diện
        $this->deleteOldImage($product->sanpham_anh);
        
        // Xóa tất cả ảnh liên quan
        $images = $this->productModel->getProductImages($id);
        if($images && is_array($images)) {
            foreach($images as $image) {
                $this->deleteOldImage($image->anh_path);
            }
        }
        
        // Xóa sản phẩm (variants sẽ tự động xóa do CASCADE trong database)
        $result = $this->productModel->deleteProduct($id);
        
        if($result === true || $result) {
            $_SESSION['success'] = 'Xóa sản phẩm thành công';
        } else {
            $_SESSION['error'] = 'Lỗi khi xóa sản phẩm';
        }
        
        $this->redirect('admin/product');
    }
    
    /**
     * Xem chi tiết sản phẩm
     */
    public function viewDetail($id = null) {
        if(!$id) {
            $_SESSION['error'] = 'Không tìm thấy sản phẩm';
            $this->redirect('admin/product');
            return;
        }
        
        $product = $this->productModel->getProductById($id);
        
        if(!$product) {
            $_SESSION['error'] = 'Sản phẩm không tồn tại';
            $this->redirect('admin/product');
            return;
        }
        
        // Lấy thông tin bổ sung
        $colors = $this->productModel->getProductColors($id);
        $variants = $this->productModel->getProductVariants($id);
        $images = $this->productModel->getProductImages($id);
        $category = $this->categoryModel->getCategoryById($product->danhmuc_id);
        $subcategory = $this->categoryModel->getSubcategoryById($product->loaisanpham_id);
        
        // Tính tổng tồn kho
        $totalStock = $this->productModel->getTotalStock($id);
        
        $this->view('admin/product/view', [
            'title' => 'Chi tiết sản phẩm - ' . $product->sanpham_tieude,
            'product' => $product,
            'colors' => $colors,
            'variants' => $variants,
            'images' => $images,
            'category' => $category,
            'subcategory' => $subcategory,
            'totalStock' => $totalStock
        ]);
    }
    
    /**
     * AJAX: Xóa variant
     */
    public function deleteVariant() {
        header('Content-Type: application/json');
        
        if($_SERVER['REQUEST_METHOD'] != 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        $variantId = (int)($_POST['variant_id'] ?? 0);
        
        if(!$variantId) {
            echo json_encode(['success' => false, 'message' => 'Variant ID is required']);
            exit;
        }
        
        $result = $this->productModel->deleteVariant($variantId);
        
        if($result) {
            echo json_encode(['success' => true, 'message' => 'Xóa variant thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa variant']);
        }
        exit;
    }
    
    /**
     * AJAX: Cập nhật tồn kho variant
     */
    public function updateVariantStockAjax() {
        header('Content-Type: application/json');
        
        if($_SERVER['REQUEST_METHOD'] != 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        $variantId = (int)($_POST['variant_id'] ?? 0);
        $tonKho = (int)($_POST['ton_kho'] ?? 0);
        
        if(!$variantId) {
            echo json_encode(['success' => false, 'message' => 'Variant ID is required']);
            exit;
        }
        
        $result = $this->productModel->updateVariantStock($variantId, $tonKho);
        
        if($result) {
            echo json_encode(['success' => true, 'message' => 'Cập nhật tồn kho thành công', 'new_stock' => $tonKho]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật tồn kho']);
        }
        exit;
    }
    
    /**
     * AJAX: Lấy danh sách variants của sản phẩm
     */
    public function getVariantsByProduct() {
        header('Content-Type: application/json');
        
        if($_SERVER['REQUEST_METHOD'] != 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        $productId = (int)($_POST['product_id'] ?? 0);
        
        if(!$productId) {
            echo json_encode(['success' => false, 'message' => 'Product ID is required']);
            exit;
        }
        
        $variants = $this->productModel->getProductVariants($productId);
        
        echo json_encode(['success' => true, 'variants' => $variants]);
        exit;
    }
    
    /**
     * Xử lý upload ảnh
     */
    private function handleImageUpload() {
        if(!isset($_FILES['sanpham_anh']) || $_FILES['sanpham_anh']['error'] != 0) {
            return ['success' => false, 'error' => 'Vui lòng chọn ảnh sản phẩm'];
        }
        
        $file = $_FILES['sanpham_anh'];
        $uploadDir = ROOT_PATH . 'public/assets/uploads/';
        
        // Tạo thư mục nếu chưa có
        if(!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Kiểm tra loại file
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if(!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Chỉ cho phép upload file ảnh (JPG, PNG, GIF)'];
        }
        
        // Kiểm tra kích thước file (max 5MB)
        if($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'error' => 'Kích thước file không được vượt quá 5MB'];
        }
        
        // Tạo tên file unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        if(move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'filename' => $filename];
        } else {
            return ['success' => false, 'error' => 'Lỗi khi upload file'];
        }
    }
    
    /**
     * Xóa ảnh cũ
     */
    private function deleteOldImage($filename) {
        if(!empty($filename)) {
            $filepath = ROOT_PATH . 'public/assets/uploads/' . $filename;
            if(file_exists($filepath)) {
                unlink($filepath);
            }
        }
    }
    
    /**
     * AJAX: Lấy loại sản phẩm theo danh mục
     */
    public function getSubcategoriesByCategory() {
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['category_id'])) {
            $category_id = (int)$_POST['category_id'];
            $subcategories = $this->categoryModel->getSubcategoriesByCategoryId($category_id);
            
            header('Content-Type: application/json');
            echo json_encode($subcategories);
            exit;
        }
    }
}