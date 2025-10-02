<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\controllers\frontend\ProductController.php

class ProductController extends Controller {
    private $productModel;
    private $categoryModel;
    
    public function __construct() {
        // Khởi tạo model
        $this->productModel = $this->model('ProductModel');
        $this->categoryModel = $this->model('CategoryModel');
    }
    
    /**
     * Hiển thị danh sách tất cả sản phẩm
     */
    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        $products = [];
        $categories = [];
        $totalProducts = 0;
        $totalPages = 0;
        
        try {
            // Lấy tất cả sản phẩm
            if ($this->productModel) {
                $products = $this->productModel->getAllProductsWithPagination($limit, $offset);
                $totalProducts = $this->productModel->getTotalProducts();
                $totalPages = ceil($totalProducts / $limit);
            }
            
            // Lấy danh mục cho menu
            if ($this->categoryModel) {
                $categories = $this->categoryModel->getAllCategories();
            }
            
        } catch (Exception $e) {
            error_log("ProductController Error: " . $e->getMessage());
        }
        
        $data = [
            'title' => 'Tất cả sản phẩm - IVY moda',
            'products' => $products,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts
        ];
        
        $this->view('frontend/product/index', $data);
    }
    
    /**
     * Hiển thị sản phẩm theo danh mục
     */
    public function category($category_id = null) {
        if (!$category_id) {
            $this->redirect('product');
            return;
        }
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        $category = null;
        $products = [];
        $categories = [];
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
                
                // Đếm tổng số sản phẩm
                $allProducts = $this->productModel->getProductsByCategory($category_id, 1000, 0);
                $totalProducts = count($allProducts);
                $totalPages = ceil($totalProducts / $limit);
            }
            
            // Lấy danh mục cho menu
            if ($this->categoryModel) {
                $categories = $this->categoryModel->getAllCategories();
            }
            
        } catch (Exception $e) {
            error_log("ProductController Error: " . $e->getMessage());
        }
        
        if (!$category) {
            $this->redirect('product');
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
    public function detail($product_id = null) {
        if (!$product_id) {
            $this->redirect('product');
            return;
        }
        
        $product = $this->productModel->getProductById($product_id);
        
        if ($product) {
            // Lấy các màu được gán cho sản phẩm
            $product->linkedColors = $this->productModel->getProductColors((int)$product_id);
            
            // Lấy tất cả ảnh của sản phẩm
            $productImages = $this->productModel->getProductImages($product_id);
            
            // Nhóm ảnh theo color_id
            $imagesByColor = [];
            foreach ($productImages as $image) {
                $colorId = isset($image->color_id) && $image->color_id !== null 
                    ? (int)$image->color_id 
                    : 0;
                
                if (!isset($imagesByColor[$colorId])) {
                    $imagesByColor[$colorId] = [];
                }
                $imagesByColor[$colorId][] = $image;
            }
            
            $product->imagesByColor = $imagesByColor;
            
            // Tăng lượt xem (method đã sửa không query sanpham_luot_xem nữa)
            $this->productModel->incrementViewCount($product_id);
        }
        
        $this->view('frontend/product/detail', [
            'title' => $product ? $product->sanpham_tieude . ' - IVY moda' : 'Sản phẩm không tồn tại',
            'product' => $product
        ]);
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
