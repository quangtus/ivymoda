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
        $productTypes = [];
        $sizes = [];
        $totalProducts = 0;
        $totalPages = 0;
        
        try {
            // Lấy tất cả sản phẩm
            if ($this->productModel) {
                $products = $this->productModel->getAllProductsWithPagination($limit, $offset);
                $totalProducts = $this->productModel->getTotalProducts();
                $totalPages = ceil($totalProducts / $limit);
                if (method_exists($this->productModel, 'getAllSizes')) {
                    $sizes = $this->productModel->getAllSizes();
                }
            }
            
            // Lấy danh mục cho menu
            if ($this->categoryModel) {
                $categories = $this->categoryModel->getAllCategories();
                // Lấy toàn bộ loại sản phẩm để phục vụ filter ở trang tất cả sản phẩm
                if (method_exists($this->categoryModel, 'getAllSubcategories')) {
                    $productTypes = $this->categoryModel->getAllSubcategories();
                }
            }
            
        } catch (Exception $e) {
            error_log("ProductController Error: " . $e->getMessage());
        }
        
        $data = [
            'title' => 'Tất cả sản phẩm - IVY moda',
            'products' => $products,
            'categories' => $categories,
            'productTypes' => $productTypes,
            'sizes' => $sizes,
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

        // Read filters from query string
        $filters = [
            'subcategory_id' => isset($_GET['type']) ? (int)$_GET['type'] : null,
            'size_id' => isset($_GET['size']) ? (int)$_GET['size'] : null,
            'price_range' => isset($_GET['price']) ? $_GET['price'] : null,
        ];
        
        $category = null;
        $products = [];
        $categories = [];
        $subcategories = [];
        $sizes = [];
        $totalProducts = 0;
        $totalPages = 0;
        
        try {
            // Lấy thông tin danh mục
            if ($this->categoryModel) {
                $category = $this->categoryModel->getCategoryById($category_id);
            }
            
            if ($category && $this->productModel) {
                // Lấy sản phẩm theo bộ lọc trong danh mục
                if (method_exists($this->productModel, 'getFilteredProductsByCategory')) {
                    $products = $this->productModel->getFilteredProductsByCategory($category_id, $filters, $limit, $offset);
                    $totalProducts = $this->productModel->countFilteredProductsByCategory($category_id, $filters);
                    $totalPages = (int)ceil($totalProducts / $limit);
                } else {
                    // Fallback
                    $products = $this->productModel->getProductsByCategory($category_id, $limit, $offset);
                    $allProducts = $this->productModel->getProductsByCategory($category_id, 1000, 0);
                    $totalProducts = count($allProducts);
                    $totalPages = ceil($totalProducts / $limit);
                }
            }
            
            // Lấy danh mục cho menu
            if ($this->categoryModel) {
                $categories = $this->categoryModel->getAllCategories();
                // Lấy loại sản phẩm theo danh mục để filter
                if (method_exists($this->categoryModel, 'getSubcategoriesByCategoryId')) {
                    $subcategories = $this->categoryModel->getSubcategoriesByCategoryId($category_id);
                }
            }
            
            // Lấy danh sách size cho filter
            if ($this->productModel && method_exists($this->productModel, 'getAllSizes')) {
                $sizes = $this->productModel->getAllSizes();
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
            'subcategories' => $subcategories,
            'sizes' => $sizes,
            'filters' => $filters,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts
        ];
        
        $this->view('frontend/product/category', $data);
    }
    
    /**
     * Lọc sản phẩm với các bộ lọc nâng cao
     */
    public function filter() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        // Lấy các tham số filter
        $category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
        $product_type = isset($_GET['product_type']) ? (int)$_GET['product_type'] : null;
        $price_range = isset($_GET['price_range']) ? $_GET['price_range'] : null;
        $size = isset($_GET['size']) ? (int)$_GET['size'] : null;
        $sort = isset($_GET['sort']) ? $_GET['sort'] : null;
        
        $products = [];
        $categories = [];
        $productTypes = [];
        $sizes = [];
        $category = null;
        $totalProducts = 0;
        $totalPages = 0;
        
        try {
            if ($this->productModel) {
                // Lấy sản phẩm với filter
                $products = $this->productModel->getFilteredProducts([
                    'category_id' => $category_id,
                    'product_type' => $product_type,
                    'price_range' => $price_range,
                    'size' => $size,
                    'sort' => $sort
                ], $limit, $offset);
                
                // Đếm tổng số sản phẩm
                $allProducts = $this->productModel->getFilteredProducts([
                    'category_id' => $category_id,
                    'product_type' => $product_type,
                    'price_range' => $price_range,
                    'size' => $size,
                    'sort' => $sort
                ], 1000, 0);
                $totalProducts = count($allProducts);
                $totalPages = ceil($totalProducts / $limit);
            }
            
            // Lấy thông tin category nếu có
            if ($category_id && $this->categoryModel) {
                $category = $this->categoryModel->getCategoryById($category_id);
            }
            
            // Lấy danh mục cho menu
            if ($this->categoryModel) {
                $categories = $this->categoryModel->getAllCategories();
                
                // Lấy product types theo category
                if ($category_id) {
                    $productTypes = $this->categoryModel->getSubcategoriesByCategoryId($category_id);
                } else {
                    // Nếu không có category, lấy tất cả product types
                    $productTypes = $this->categoryModel->getAllSubcategories();
                }
            }
            
            // Lấy sizes cho filter
            if ($this->productModel && method_exists($this->productModel, 'getAllSizes')) {
                $sizes = $this->productModel->getAllSizes();
            }
            
        } catch (Exception $e) {
            error_log("ProductController Filter Error: " . $e->getMessage());
        }
        
        $filters = [
            'category_id' => $category_id,
            'product_type' => $product_type,
            'price_range' => $price_range,
            'size' => $size,
            'sort' => $sort
        ];
        
        $data = [
            'title' => ($category ? $category->danhmuc_ten . ' - ' : '') . 'Bộ lọc sản phẩm - IVY moda',
            'category' => $category,
            'products' => $products,
            'categories' => $categories,
            'productTypes' => $productTypes,
            'sizes' => $sizes,
            'filters' => $filters,
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
        $categories = [];
        
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
        
        // Lấy categories cho header menu
        if ($this->categoryModel) {
            $categories = $this->categoryModel->getAllCategories();
        }
        
        $this->view('frontend/product/detail', [
            'title' => $product ? $product->sanpham_tieude . ' - IVY moda' : 'Sản phẩm không tồn tại',
            'product' => $product,
            'categories' => $categories
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
