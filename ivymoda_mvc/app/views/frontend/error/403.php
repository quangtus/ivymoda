<?php
require_once ROOT_PATH . 'app/views/shared/frontend/header.php'; 
?>

<div class="container" style="text-align:center; padding: 50px 0;">
    <div class="error-page">
        <h1>403 - Truy cập bị từ chối</h1>
        <p>Bạn không có quyền truy cập vào trang này.</p>
        <a href="<?= BASE_URL ?>" class="btn-primary">Về trang chủ</a>
    </div>
</div>

<?php 
require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; 
?>