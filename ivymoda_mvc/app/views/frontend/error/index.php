<?php
require_once ROOT_PATH . 'app/views/shared/frontend/header.php'; 
?>

<div class="container" style="text-align:center; padding: 50px 0;">
    <div class="error-page">
        <h1>Đã xảy ra lỗi</h1>
        <p><?= isset($message) ? $message : 'Có lỗi xảy ra trong quá trình xử lý yêu cầu của bạn.' ?></p>
        <a href="<?= BASE_URL ?>" class="btn-primary">Về trang chủ</a>
    </div>
</div>

<?php 
require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; 
?>