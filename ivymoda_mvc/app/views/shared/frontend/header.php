<?php
// filepath: c:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\shared\frontend\header.php
?>
<?php
// Kiểm tra xem user đã đăng nhập hay chưa
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'IVY moda' ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/mainstyle.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/blue-theme.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/image-fix.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/product-detail.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/custom-product-detail.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/cart.css">
    
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/54f0cb7e4a.js" crossorigin="anonymous"></script>
    
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="<?= BASE_URL ?>assets/js/image-handler.js"></script>
    <script src="<?= BASE_URL ?>assets/js/cart.js"></script>
</head>
<body>
    <header>
<div class="container">
            <div class="header-top">
                <div class="logo">
                    <a href="<?= BASE_URL ?>">
                        <img src="<?= BASE_URL ?>assets/images/logo.png" alt="IVY moda Logo">
                    </a>
                </div>
                
                <nav class="main-menu">
                    <ul>
                        <li><a href="<?= BASE_URL ?>home">TRANG CHỦ</a></li>
                        <li><a href="<?= BASE_URL ?>product">TẤT CẢ SẢN PHẨM</a></li>
                        <?php if(isset($categories) && !empty($categories)): ?>
                            <?php foreach($categories as $menuCategory): ?>
                                <li class="has-dropdown">
                                    <a href="<?= BASE_URL ?>product/category/<?= $menuCategory->danhmuc_id ?>">
                                        <?= htmlspecialchars($menuCategory->danhmuc_ten) ?>
                                    </a>
                                    <?php 
                                    // Lazy-fetch subcategories if available via CategoryModel when header is included in contexts that didn't supply them
                                    $headerSubcats = [];
                                    if (!isset($menuCategory->subcategories) && class_exists('CategoryModel')) {
                                        $__catModel = new CategoryModel();
                                        $headerSubcats = $__catModel->getSubcategoriesByCategoryId($menuCategory->danhmuc_id);
                                    } elseif(isset($menuCategory->subcategories)) {
                                        $headerSubcats = $menuCategory->subcategories;
                                    }
                                    ?>
                                    <?php if(!empty($headerSubcats)): ?>
                                        <ul class="dropdown-menu">
                                            <?php foreach($headerSubcats as $sub): ?>
                                                <li>
                                                    <a href="<?= BASE_URL ?>product/category/<?= $menuCategory->danhmuc_id ?>?type=<?= $sub->loaisanpham_id ?>">
                                                        <?= htmlspecialchars($sub->loaisanpham_ten) ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><a href="<?= BASE_URL ?>product/category/1">NỮ</a></li>
                            <li><a href="<?= BASE_URL ?>product/category/2">NAM</a></li>
                            <li><a href="<?= BASE_URL ?>product/category/3">TRẺ EM</a></li>
                            <li><a href="<?= BASE_URL ?>product/category/4">BỘ SƯU TẬP</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                
                <div class="header-actions">
                    <div class="search-box">
                        <form action="<?= BASE_URL ?>product/search" method="GET">
                            <input type="text" name="q" placeholder="Tìm kiếm sản phẩm..." class="search-input" value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
                            <button type="submit" class="search-button">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                    
                    <div class="user-actions">
                        <?php if($isLoggedIn): ?>
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle">
                                    <i class="fas fa-user"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?= BASE_URL ?>user/profile">Tài khoản của tôi</a></li>
                                    <?php if(isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 3)): ?>
                                        <li><a href="<?= BASE_URL ?>admin/dashboard">Quản trị hệ thống</a></li>
                                    <?php endif; ?>
                                    <li><a href="<?= BASE_URL ?>auth/logout">Đăng xuất</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle">
                                    <i class="fas fa-user-secret"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?= BASE_URL ?>auth/login">Đăng nhập</a></li>
                                    <li><a href="<?= BASE_URL ?>auth/register">Đăng ký</a></li>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <div class="cart">
                            <a href="<?= BASE_URL ?>cart">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="cart-count" style="display: <?= isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0 ? 'inline' : 'none' ?>;">
                                    <?= isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : 0 ?>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <div class="main-content">