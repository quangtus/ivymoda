<?php
require_once ROOT_PATH . 'app/views/shared/admin/header.php';
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';
?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Thêm màu sắc</h1>
                <a href="<?= BASE_URL ?>admin/color" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
            </div>

            <?php if(!empty($success)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if(!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="color_ten" class="font-weight-bold">Tên màu</label>
                            <input type="text" class="form-control" id="color_ten" name="color_ten" placeholder="Ví dụ: Đen, Trắng, Đỏ" required>
                        </div>
                        <div class="form-group">
                            <label for="color_hex" class="font-weight-bold">Mã màu (Color Picker)</label>
                            <div class="d-flex align-items-center" style="gap:12px;">
                                <input type="color" id="color_hex_picker" value="#000000" style="width:48px; height:38px; padding:0; border:none; background:transparent;">
                                <input type="text" class="form-control" id="color_hex" name="color_hex" placeholder="#000000" value="#000000" maxlength="7" pattern="^#([A-Fa-f0-9]{6})$" title="Định dạng: #RRGGBB">
                            </div>
                            <small class="form-text text-muted">Chọn màu để lưu mã hex (ví dụ: #FF0000). Sẽ dùng để hiển thị swatch.</small>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const picker = document.getElementById('color_hex_picker');
    const input = document.getElementById('color_hex');
    picker.addEventListener('input', function(){
        input.value = picker.value.toUpperCase();
    });
    input.addEventListener('change', function(){
        if(/^#([A-Fa-f0-9]{6})$/.test(input.value)) {
            picker.value = input.value;
        }
    });
});
</script>
