<?php
// filepath: c:\xampp\htdocs\ivymoda\ivymoda_mvc\app\controllers\frontend\HomeController.php

class HomeController extends Controller {
    private $productModel;
    private $categoryModel;
    private $promotionModel;
    
    public function __construct() {
        // Khởi tạo model
        $this->productModel = $this->model('ProductModel');
        $this->categoryModel = $this->model('CategoryModel');
        $this->promotionModel = $this->model('PromotionModel');
    }
    
    public function index() {
        // Lấy tất cả sản phẩm với đầy đủ thông tin (không phân loại nữa vì DB không có field)
        $featuredProducts = $this->productModel->getFeaturedProductsWithDetails(12);
        
        // Lấy danh mục
        $categories = $this->categoryModel->getAllCategories();
        // Lấy banner khuyến mãi đang chạy
        $promotions = $this->promotionModel ? $this->promotionModel->getActivePromotions(5) : [];
        
        $this->view('frontend/home/index', [
            'title' => 'IVY moda - Trang chủ',
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'promotions' => $promotions
        ]);
    }
    
    /**
     * Hiển thị sản phẩm theo danh mục
     */
    public function category($category_id = null) {
        if (!$category_id) {
            $this->redirect('home');
            return;
        }
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        $category = null;
        $products = [];
        $totalProducts = 0;
        $totalPages = 0;
        
        try {
            // Lấy thông tin danh mục
            if ($this->categoryModel) {
                $category = $this->categoryModel->getCategoryById($category_id);
            }
            
            if ($category && $this->productModel) {
                // Lấy sản phẩm theo danh mục
                $products = $this->productModel->getProductsByCategory($category_id, $limit, $offset);
                
                // Đếm tổng số sản phẩm (cần implement method này)
                $allProducts = $this->productModel->getProductsByCategory($category_id, 1000, 0);
                $totalProducts = count($allProducts);
                $totalPages = ceil($totalProducts / $limit);
            }
            
            // Lấy danh mục cho menu
            $categories = [];
            if ($this->categoryModel) {
                $categories = $this->categoryModel->getAllCategories();
            }
            
        } catch (Exception $e) {
            error_log("CategoryController Error: " . $e->getMessage());
        }
        
        if (!$category) {
            $this->redirect('home');
            return;
        }
        
        $data = [
            'title' => $category->danhmuc_ten . ' - IVY moda',
            'category' => $category,
            'products' => $products,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts
        ];
        
        $this->view('frontend/product/category', $data);
    }
    
    /**
     * Hiển thị chi tiết sản phẩm
     */
    public function product($product_id = null) {
        if (!$product_id) {
            $this->redirect('home');
            return;
        }
        
        $product = null;
        $productImages = [];
        $relatedProducts = [];
        $categories = [];
        
        try {
            // Lấy thông tin sản phẩm
            if ($this->productModel) {
                $product = $this->productModel->getProductById($product_id);
                
                if ($product) {
                    // Lấy danh sách ảnh sản phẩm
                    $productImages = $this->productModel->getProductImages($product_id);
                    
                    // Nếu chưa có ảnh trong bảng ảnh, sử dụng ảnh chính từ sản phẩm
                    if (empty($productImages) && !empty($product->sanpham_anh)) {
                        $productImages = [
                            (object)[
                                'anh_id' => 0,
                                'sanpham_id' => $product_id,
                                'anh_path' => $product->sanpham_anh,
                                'is_primary' => 1
                            ]
                        ];
                    }
                    
                    // Lấy sản phẩm liên quan
                    $relatedProducts = $this->productModel->getRelatedProducts($product_id, $product->danhmuc_id, 4);
                }
            }
            
            // Lấy danh mục cho menu
            if ($this->categoryModel) {
                $categories = $this->categoryModel->getAllCategories();
            }
            
        } catch (Exception $e) {
            error_log("ProductController Error: " . $e->getMessage());
        }
        
        if (!$product) {
            $this->redirect('home');
            return;
        }
        
        $data = [
            'title' => $product->sanpham_tieude . ' - IVY moda',
            'product' => $product,
            'productImages' => $productImages,
            'relatedProducts' => $relatedProducts,
            'categories' => $categories
        ];
        
        $this->view('frontend/product/detail', $data);
    }
    
    /**
     * Tìm kiếm sản phẩm
     */
    public function search() {
        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
        $category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
        $min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
        $max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        $products = [];
        $categories = [];
        $totalProducts = 0;
        $totalPages = 0;
        
        try {
            if ($keyword && $this->productModel) {
                $products = $this->productModel->searchProducts($keyword, $category_id, $min_price, $max_price, $limit, $offset);
                
                // Đếm tổng số kết quả
                $allResults = $this->productModel->searchProducts($keyword, $category_id, $min_price, $max_price, 1000, 0);
                $totalProducts = count($allResults);
                $totalPages = ceil($totalProducts / $limit);
            }
            
            // Lấy danh mục cho filter
            if ($this->categoryModel) {
                $categories = $this->categoryModel->getAllCategories();
            }
            
        } catch (Exception $e) {
            error_log("SearchController Error: " . $e->getMessage());
        }
        
        $data = [
            'title' => 'Tìm kiếm: ' . $keyword . ' - IVY moda',
            'keyword' => $keyword,
            'products' => $products,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'filters' => [
                'category_id' => $category_id,
                'min_price' => $min_price,
                'max_price' => $max_price
            ]
        ];
        
        $this->view('frontend/product/search', $data);
    }
}