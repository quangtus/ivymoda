<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\admin\discount\edit.php

// Load header
require_once ROOT_PATH . 'app/views/shared/admin/header.php';

// Load sidebar
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';
?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-edit"></i> Sửa mã giảm giá
                </h1>
                <a href="<?= ADMIN_URL ?>discount" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại
                </a>
            </div>

            <!-- Thông báo -->
            <?php if(!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?= $success ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>

            <?php if(!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin mã giảm giá</h6>
                </div>
                    <form method="POST" action="<?= ADMIN_URL ?>discount/edit/<?= $discount->ma_id ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ma_code" class="form-label">Mã Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="ma_code" name="ma_code" 
                                           value="<?= htmlspecialchars($_POST['ma_code'] ?? $discount->ma_code) ?>" 
                                           placeholder="VD: WELCOME10, SUMMER20" required>
                                    <div class="form-text">Mã code duy nhất để khách hàng sử dụng</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ma_ten" class="form-label">Tên mã giảm giá <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="ma_ten" name="ma_ten" 
                                           value="<?= htmlspecialchars($_POST['ma_ten'] ?? $discount->ma_ten) ?>" 
                                           placeholder="VD: Giảm 10% cho khách hàng mới" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="loai_giam" class="form-label">Loại giảm giá <span class="text-danger">*</span></label>
                                    <select class="form-select" id="loai_giam" name="loai_giam" required>
                                        <option value="percent" <?= (($_POST['loai_giam'] ?? $discount->loai_giam) === 'percent') ? 'selected' : '' ?>>
                                            Phần trăm (%)
                                        </option>
                                        <option value="fixed" <?= (($_POST['loai_giam'] ?? $discount->loai_giam) === 'fixed') ? 'selected' : '' ?>>
                                            Số tiền cố định (₫)
                                        </option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ma_giam" class="form-label">Giá trị giảm <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="ma_giam" name="ma_giam" 
                                           value="<?= htmlspecialchars($_POST['ma_giam'] ?? $discount->ma_giam) ?>" 
                                           min="0" step="0.01" required>
                                    <div class="form-text">
                                        <span id="discount-help">Nhập phần trăm (1-100)</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="so_luong" class="form-label">Số lượng</label>
                                    <input type="number" class="form-control" id="so_luong" name="so_luong" 
                                           value="<?= htmlspecialchars($_POST['so_luong'] ?? $discount->so_luong) ?>" 
                                           min="1">
                                    <div class="form-text">Để trống = không giới hạn</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ngay_bat_dau" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="ngay_bat_dau" name="ngay_bat_dau" 
                                           value="<?= htmlspecialchars($_POST['ngay_bat_dau'] ?? date('Y-m-d\TH:i', strtotime($discount->ngay_bat_dau))) ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ngay_ket_thuc" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="ngay_ket_thuc" name="ngay_ket_thuc" 
                                           value="<?= htmlspecialchars($_POST['ngay_ket_thuc'] ?? date('Y-m-d\TH:i', strtotime($discount->ngay_ket_thuc))) ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="trang_thai" name="trang_thai" 
                                       <?= (isset($_POST['trang_thai']) || (!isset($_POST) && $discount->trang_thai)) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="trang_thai">
                                    Kích hoạt mã giảm giá
                                </label>
                            </div>
                        </div>
                        
                        <!-- Hiển thị thông tin sử dụng -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Thông tin sử dụng</h6>
                            <p class="mb-0">
                                Mã này đã được sử dụng <strong><?= $discount->da_su_dung ?? 0 ?></strong> lần
                                <?php if ($discount->so_luong): ?>
                                    trên tổng số <strong><?= $discount->so_luong ?></strong> lần cho phép
                                <?php endif; ?>
                            </p>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= ADMIN_URL ?>discount" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật mã giảm giá
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loaiGiamSelect = document.getElementById('loai_giam');
    const maGiamInput = document.getElementById('ma_giam');
    const discountHelp = document.getElementById('discount-help');
    
    function updateDiscountHelp() {
        if (loaiGiamSelect.value === 'percent') {
            discountHelp.textContent = 'Nhập phần trăm (1-100)';
            maGiamInput.max = 100;
            maGiamInput.placeholder = 'VD: 10';
        } else {
            discountHelp.textContent = 'Nhập số tiền giảm (VNĐ)';
            maGiamInput.removeAttribute('max');
            maGiamInput.placeholder = 'VD: 50000';
        }
    }
    
    loaiGiamSelect.addEventListener('change', updateDiscountHelp);
    updateDiscountHelp(); // Initialize
});
</script>

<?php
// Load footer
require_once ROOT_PATH . 'app/views/shared/admin/footer.php';
?>
