<?php
require_once ROOT_PATH . 'app/views/shared/frontend/header.php'; 
?>

<div class="error-container" style="max-width: 800px; margin: 50px auto; text-align: center; padding: 20px;">
    <h1 style="font-size: 72px; margin-bottom: 20px; color: #333;">404</h1>
    <h2 style="font-size: 24px; margin-bottom: 20px; color: #555;">Không tìm thấy trang</h2>
    
    <p style="font-size: 16px; color: #777; margin-bottom: 30px;">
        <?php echo isset($message) ? $message : 'Trang bạn đang tìm kiếm không tồn tại hoặc đã bị di chuyển.'; ?>
    </p>
    
    <div>
        <a href="<?= BASE_URL ?>" style="display: inline-block; background-color: #221f20; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px; margin-right: 10px;">Về trang chủ</a>
        
        <button onclick="goBack()" style="background-color: #f8f9fa; color: #333; border: 1px solid #ddd; padding: 10px 20px; border-radius: 3px; cursor: pointer;">Quay lại</button>
    </div>
</div>

<script>
function goBack() {
    window.history.back();
}
</script>

<?php 
require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; 
?>