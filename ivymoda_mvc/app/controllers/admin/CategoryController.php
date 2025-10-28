<?php
namespace admin;

class CategoryController extends \Controller {
    private $categoryModel;
    
    public function __construct() {
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
     * Hiển thị danh sách danh mục sản phẩm
     */
    public function index() {
        $categories = $this->categoryModel->getAllCategories();
        
        $data = [
            'title' => 'Quản lý danh mục sản phẩm - IVY moda',
            'categories' => $categories,
            'success' => $_SESSION['success'] ?? '',
            'error' => $_SESSION['error'] ?? ''
        ];
        
        // Xóa thông báo sau khi hiển thị
        unset($_SESSION['success'], $_SESSION['error']);
        
        $this->view('admin/category/index', $data);
    }
    
    /**
     * Thêm danh mục mới
     */
    public function add() {
        $data = [
            'title' => 'Thêm danh mục sản phẩm - IVY moda',
            'danhmuc_ten' => '',
            'error' => '',
            'success' => ''
        ];
        
        // Xử lý thêm danh mục
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $danhmuc_ten = trim($_POST['danhmuc_ten'] ?? '');
            
            if(empty($danhmuc_ten)) {
                $data['error'] = 'Vui lòng nhập tên danh mục';
            } else {
                $result = $this->categoryModel->addCategory($danhmuc_ten);
                
                if($result === true) {
                    $data['success'] = 'Thêm danh mục thành công!';
                    $data['danhmuc_ten'] = ''; // Reset form
                } else {
                    $data['error'] = $result;
                }
            }
        }
        
        $this->view('admin/category/add', $data);
    }
    
    /**
     * Sửa danh mục
     * @param int $id ID của danh mục
     */
    public function edit($id = null) {
        if(!$id) {
            $_SESSION['error'] = 'Không tìm thấy danh mục';
            $this->redirect('admin/category');
            exit;
        }
        
        $category = $this->categoryModel->getCategoryById($id);
        
        if(!$category) {
            $_SESSION['error'] = 'Không tìm thấy danh mục';
            $this->redirect('admin/category');
            exit;
        }
        
        $data = [
            'title' => 'Sửa danh mục sản phẩm - IVY moda',
            'category' => $category,
            'danhmuc_ten' => $category->danhmuc_ten,
            'error' => '',
            'success' => ''
        ];
        
        // Xử lý cập nhật danh mục
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $danhmuc_ten = trim($_POST['danhmuc_ten'] ?? '');
            
            if(empty($danhmuc_ten)) {
                $data['error'] = 'Vui lòng nhập tên danh mục';
            } else {
                $result = $this->categoryModel->updateCategory($id, $danhmuc_ten);
                
                if($result === true) {
                    $data['success'] = 'Cập nhật danh mục thành công!';
                    // Cập nhật lại thông tin category
                    $category = $this->categoryModel->getCategoryById($id);
                    $data['category'] = $category;
                    $data['danhmuc_ten'] = $category->danhmuc_ten;
                } else {
                    $data['error'] = $result;
                }
            }
        }
        
        $this->view('admin/category/edit', $data);
    }
    
    /**
     * Xóa danh mục
     * @param int $id ID của danh mục
     */
    public function delete($id) {
        if(!$id) {
            $_SESSION['error'] = 'Không tìm thấy danh mục';
            $this->redirect('admin/category');
            exit;
        }
        
        // Kiểm tra xem danh mục có sản phẩm không
        $hasProducts = $this->categoryModel->hasProducts($id);
        
        if($hasProducts) {
            $_SESSION['error'] = 'Không thể xóa danh mục đang có sản phẩm';
        } else {
            $result = $this->categoryModel->deleteCategory($id);
            
            if($result === true) {
                $_SESSION['success'] = 'Xóa danh mục thành công';
            } else {
                $_SESSION['error'] = $result;
            }
        }
        
        $this->redirect('admin/category');
    }
    
    /**
     * Quản lý loại sản phẩm trong danh mục
     * @param int $id ID của danh mục
     */
    public function subcategories($id = null) {
        if(!$id) {
            $_SESSION['error'] = 'Không tìm thấy danh mục';
            $this->redirect('admin/category');
            exit;
        }
        
        $category = $this->categoryModel->getCategoryById($id);
        $subcategories = $this->categoryModel->getSubcategoriesByCategoryId($id);
        
        if(!$category) {
            $_SESSION['error'] = 'Không tìm thấy danh mục';
            $this->redirect('admin/category');
            exit;
        }
        
        $data = [
            'title' => 'Quản lý loại sản phẩm - ' . $category->danhmuc_ten,
            'category' => $category,
            'subcategories' => $subcategories,
            'loaisanpham_ten' => '',
            'error' => '',
            'success' => ''
        ];
        
        // Xử lý thêm loại sản phẩm
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_subcategory'])) {
            $loaisanpham_ten = trim($_POST['loaisanpham_ten'] ?? '');
            
            if(empty($loaisanpham_ten)) {
                $data['error'] = 'Vui lòng nhập tên loại sản phẩm';
            } else {
                $result = $this->categoryModel->addSubcategory($id, $loaisanpham_ten);
                
                if($result === true) {
                    $data['success'] = 'Thêm loại sản phẩm thành công!';
                    $data['loaisanpham_ten'] = ''; // Reset form
                    // Cập nhật lại danh sách
                    $data['subcategories'] = $this->categoryModel->getSubcategoriesByCategoryId($id);
                } else {
                    $data['error'] = $result;
                }
            }
        }
        
        $this->view('admin/category/subcategories', $data);
    }
    
    /**
     * Sửa loại sản phẩm
     * @param int $id ID của loại sản phẩm
     */
    public function editSubcategory($id) {
        if(!$id) {
            $_SESSION['error'] = 'Không tìm thấy loại sản phẩm';
            $this->redirect('admin/category');
            exit;
        }
        
        $subcategory = $this->categoryModel->getSubcategoryById($id);
        
        if(!$subcategory) {
            $_SESSION['error'] = 'Không tìm thấy loại sản phẩm';
            $this->redirect('admin/category');
            exit;
        }
        
        // Lấy thông tin danh mục
        $category = $this->categoryModel->getCategoryById($subcategory->danhmuc_id);
        
        if(!$category) {
            $_SESSION['error'] = 'Không tìm thấy danh mục';
            $this->redirect('admin/category');
            exit;
        }
        
        // Xử lý cập nhật loại sản phẩm
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_subcategory'])) {
            $loaisanpham_ten = trim($_POST['loaisanpham_ten'] ?? '');
            
            if(empty($loaisanpham_ten)) {
                $_SESSION['error'] = 'Vui lòng nhập tên loại sản phẩm';
            } else {
                $result = $this->categoryModel->updateSubcategory($id, $loaisanpham_ten);
                
                if($result === true) {
                    $_SESSION['success'] = 'Cập nhật loại sản phẩm thành công!';
                } else {
                    $_SESSION['error'] = $result;
                }
            }
        }
        
        $this->redirect('admin/category/subcategories/' . $category->danhmuc_id);
    }
    
    /**
     * Xóa loại sản phẩm
     * @param int $id ID của loại sản phẩm
     */
    public function deleteSubcategory($id) {
        if(!$id) {
            $_SESSION['error'] = 'Không tìm thấy loại sản phẩm';
            $this->redirect('admin/category');
            exit;
        }
        
        // Lấy thông tin loại sản phẩm để redirect về đúng danh mục
        $subcategory = $this->categoryModel->getSubcategoryById($id);
        
        if(!$subcategory) {
            $_SESSION['error'] = 'Không tìm thấy loại sản phẩm';
            $this->redirect('admin/category');
            exit;
        }
        
        // Kiểm tra xem loại sản phẩm có sản phẩm không
        $hasProducts = $this->categoryModel->hasProductsInSubcategory($id);
        
        if($hasProducts) {
            $_SESSION['error'] = 'Không thể xóa loại sản phẩm đang có sản phẩm';
        } else {
            $result = $this->categoryModel->deleteSubcategory($id);
            
            if($result === true) {
                $_SESSION['success'] = 'Xóa loại sản phẩm thành công';
            } else {
                $_SESSION['error'] = $result;
            }
        }
        
        $this->redirect('admin/category/subcategories/' . $subcategory->danhmuc_id);
    }
}
