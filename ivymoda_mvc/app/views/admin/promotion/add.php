<?php
require_once ROOT_PATH . 'app/views/shared/admin/header.php';
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';
?>

<div class="admin-content-right-main">
    <div class="container-fluid">
        <h3>Thêm chương trình khuyến mãi</h3>

        <?php if(!empty($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <?php if(!empty($success)): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="mt-3">
            <div class="form-group">
                <label>Tiêu đề</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Mô tả ngắn</label>
                <textarea name="description" class="form-control" rows="2"></textarea>
            </div>
            <div class="form-group">
                <label>Nội dung HTML</label>
                <textarea name="content" class="form-control" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label>Ảnh banner (1920x600 khuyến nghị)</label>
                <input type="file" name="image" accept="image/*" class="form-control-file">
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Mã giảm giá (ID)</label>
                    <input type="number" name="ma_giam_gia_id" class="form-control" placeholder="Tùy chọn">
                </div>
                <div class="form-group col-md-3">
                    <label>Ngày bắt đầu</label>
                    <input type="datetime-local" name="start_date" class="form-control" value="<?= date('Y-m-d\TH:i') ?>">
                </div>
                <div class="form-group col-md-3">
                    <label>Ngày kết thúc</label>
                    <input type="datetime-local" name="end_date" class="form-control" value="<?= date('Y-m-d\TH:i', time()+86400*7) ?>">
                </div>
                <div class="form-group col-md-2">
                    <label>Ưu tiên</label>
                    <input type="number" name="priority" class="form-control" value="0">
                </div>
                <div class="form-group col-md-1">
                    <label>Hiển thị</label>
                    <select name="is_active" class="form-control">
                        <option value="1">Có</option>
                        <option value="0">Không</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Lưu</button>
            <a href="<?= BASE_URL ?>admin/promotion" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
</div>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>


