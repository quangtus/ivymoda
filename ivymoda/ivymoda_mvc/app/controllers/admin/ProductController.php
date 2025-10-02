<?php
namespace admin;

class ProductController extends \Controller {
    private $productModel;
    private $categoryModel;
    
    public function __construct() {
        $this->productModel = $this->model('ProductModel');
        $this->categoryModel = $this->model('CategoryModel');
        
        // Kiểm tra đăng nhập và quyền admin
        if(!isset($_SESSION['user_id'])) {
            $this->redirect('admin/auth/login');
            exit;
        }
        
        if($_SESSION['role_id'] != 1) {
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
        $colors = $this->productModel->getAllColors();
        
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
            'sanpham_tieude' => '',
            'sanpham_ma' => '',
            'danhmuc_id' => '',
            'loaisanpham_id' => '',
            'sanpham_gia' => '',
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
            $data['sanpham_gia'] = trim($_POST['sanpham_gia'] ?? '');
            $data['sanpham_chitiet'] = trim($_POST['sanpham_chitiet'] ?? '');
            $data['sanpham_baoquan'] = trim($_POST['sanpham_baoquan'] ?? '');

            // Validation
            if(empty($data['sanpham_tieude']) || empty($data['sanpham_ma']) || 
               empty($data['danhmuc_id']) || empty($data['loaisanpham_id']) || 
               empty($data['sanpham_gia'])) {
                $data['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc';
            } elseif(!is_numeric($data['sanpham_gia']) || $data['sanpham_gia'] <= 0) {
                $data['error'] = 'Giá sản phẩm phải là số dương';
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
                    // Thêm sản phẩm vào database
                    $result = $this->productModel->addProduct(
                        $data['sanpham_tieude'], 
                        $data['sanpham_ma'], 
                        $data['danhmuc_id'], 
                        $data['loaisanpham_id'], 
                        $data['sanpham_gia'], 
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
                        }

                        $_SESSION['success'] = 'Thêm sản phẩm thành công!';
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
        $colors = $this->productModel->getAllColors();
        
        // Lấy tất cả loại sản phẩm
        $subcategories = [];
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $subcategories[$category->danhmuc_id] = $this->categoryModel->getSubcategoriesByCategoryId($category->danhmuc_id);
            }
        }
        
        $productImages = $this->productModel->getProductImages((int)$id);
        $linkedColors = $this->productModel->getProductColors((int)$id);

        $data = [
            'title' => 'Sửa sản phẩm - IVY moda',
            'product' => $product,
            'categories' => $categories,
            'subcategories' => $subcategories,
            'colors' => $colors,
            'productColorIds' => $this->productModel->getProductColorIds((int)$id),
            'productImages' => $productImages,
            'linkedColors' => $linkedColors,
            'sanpham_tieude' => $product->sanpham_tieude,
            'sanpham_ma' => $product->sanpham_ma,
            'danhmuc_id' => $product->danhmuc_id,
            'loaisanpham_id' => $product->loaisanpham_id,
            'sanpham_gia' => $product->sanpham_gia,
            'sanpham_chitiet' => $product->sanpham_chitiet,
            'sanpham_baoquan' => $product->sanpham_baoquan,
            'sanpham_anh' => $product->sanpham_anh,
            'error' => '',
            'success' => ''
        ];
        
        // Xử lý cập nhật sản phẩm
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data['sanpham_tieude'] = trim($_POST['sanpham_tieude'] ?? '');
            $data['sanpham_ma'] = trim($_POST['sanpham_ma'] ?? '');
            $data['danhmuc_id'] = (int)($_POST['danhmuc_id'] ?? 0);
            $data['loaisanpham_id'] = (int)($_POST['loaisanpham_id'] ?? 0);
            $data['sanpham_gia'] = trim($_POST['sanpham_gia'] ?? '');
            $data['sanpham_chitiet'] = trim($_POST['sanpham_chitiet'] ?? '');
            $data['sanpham_baoquan'] = trim($_POST['sanpham_baoquan'] ?? '');
            
            // Validation
            if(empty($data['sanpham_tieude']) || empty($data['sanpham_ma']) || 
               empty($data['danhmuc_id']) || empty($data['loaisanpham_id']) || 
               empty($data['sanpham_gia'])) {
                $data['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc';
            } elseif(!is_numeric($data['sanpham_gia']) || $data['sanpham_gia'] <= 0) {
                $data['error'] = 'Giá sản phẩm phải là số dương';
            } else {
                // Xử lý upload ảnh mới (nếu có)
                $newImage = $product->sanpham_anh; // Giữ ảnh cũ
                if(isset($_FILES['sanpham_anh']) && $_FILES['sanpham_anh']['error'] == 0) {
                    $uploadResult = $this->handleImageUpload();
                    if($uploadResult['success']) {
                        $newImage = $uploadResult['filename'];
                        // Xóa ảnh cũ
                        $this->deleteOldImage($product->sanpham_anh);
                    } else {
                        $data['error'] = $uploadResult['error'];
                    }
                }
                
                if(empty($data['error'])) {
                    $result = $this->productModel->updateProduct(
                        $id,
                        $data['sanpham_tieude'],
                        $data['sanpham_ma'],
                        $data['danhmuc_id'],
                        $data['loaisanpham_id'],
                        $data['sanpham_gia'],
                        $data['sanpham_chitiet'],
                        $data['sanpham_baoquan'],
                        $newImage
                    );
                    
                    if($result === true) {
                        // Cập nhật danh sách màu bổ sung nếu có
                        $selectedColors = [];
                        if (isset($_POST['color_ids']) && is_array($_POST['color_ids'])) {
                            $selectedColors = array_map('intval', $_POST['color_ids']);
                        }
                        // Đảm bảo có ít nhất 1 màu cho sản phẩm
                        if (empty($selectedColors)) {
                            $selectedColors = [1]; // Màu mặc định
                        }
                        $this->productModel->setProductColors((int)$id, $selectedColors);
                        $_SESSION['success'] = 'Cập nhật sản phẩm thành công!';
                        $this->redirect('admin/product');
                        exit;
                    } else {
                        $data['error'] = $result;
                    }
                }
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
        
        $result = $this->productModel->deleteProduct($id);
        
        if($result === true) {
            // Xóa ảnh sản phẩm
            $this->deleteOldImage($product->sanpham_anh);
            $_SESSION['success'] = 'Xóa sản phẩm thành công';
        } else {
            $_SESSION['error'] = $result;
        }
        
        $this->redirect('admin/product');
    }
    
    /**
     * Xem chi tiết sản phẩm
     * @param int $id ID của sản phẩm
     */
    public function viewDetail($id) {
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
        
        $data = [
            'title' => 'Chi tiết sản phẩm - ' . $product->sanpham_tieude,
            'product' => $product
        ];
        
        $this->view('admin/product/view', $data);
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