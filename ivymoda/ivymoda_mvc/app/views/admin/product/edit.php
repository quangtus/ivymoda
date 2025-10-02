<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\admin\product\edit.php

// Load header
require_once ROOT_PATH . 'app/views/shared/admin/header.php';

// Load sidebar
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';
?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Sửa sản phẩm</h1>
                <a href="<?= ADMIN_URL ?>product" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại
                </a>
            </div>

            <!-- Thông báo -->
            <?php if(!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>
            
            <?php if(!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?= $success ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thông tin sản phẩm</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?= ADMIN_URL ?>product/edit/<?= $product->sanpham_id ?>" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sanpham_tieude" class="font-weight-bold">
                                                Tên sản phẩm <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control <?= !empty($error) ? 'is-invalid' : '' ?>" 
                                                   id="sanpham_tieude" 
                                                   name="sanpham_tieude" 
                                                   value="<?= htmlspecialchars($sanpham_tieude) ?>"
                                                   placeholder="Nhập tên sản phẩm"
                                                   required
                                                   maxlength="255">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sanpham_ma" class="font-weight-bold">
                                                Mã sản phẩm <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control <?= !empty($error) ? 'is-invalid' : '' ?>" 
                                                   id="sanpham_ma" 
                                                   name="sanpham_ma" 
                                                   value="<?= htmlspecialchars($sanpham_ma) ?>"
                                                   placeholder="Nhập mã sản phẩm"
                                                   required
                                                   maxlength="50">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="danhmuc_id" class="font-weight-bold">
                                                Danh mục <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control" id="danhmuc_id" name="danhmuc_id" required>
                                                <option value="">Chọn danh mục</option>
                                                <?php foreach($categories as $category): ?>
                                                    <option value="<?= $category->danhmuc_id ?>" 
                                                            <?= $danhmuc_id == $category->danhmuc_id ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($category->danhmuc_ten) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="loaisanpham_id" class="font-weight-bold">
                                                Loại sản phẩm <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control" id="loaisanpham_id" name="loaisanpham_id" required>
                                                <option value="">Chọn loại sản phẩm</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="sanpham_gia" class="font-weight-bold">
                                        Giá sản phẩm <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control <?= !empty($error) ? 'is-invalid' : '' ?>" 
                                           id="sanpham_gia" 
                                           name="sanpham_gia" 
                                           value="<?= htmlspecialchars($sanpham_gia) ?>"
                                           placeholder="Nhập giá sản phẩm"
                                           min="0"
                                           step="1000"
                                           required>
                                    <small class="form-text text-muted">
                                        Nhập giá bằng số (Ví dụ: 199000)
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">
                                        Ảnh sản phẩm hiện tại
                                    </label>
                                    
                                    <?php if(!empty($productImages)): ?>
                                    <div class="current-image d-flex flex-wrap mb-3" style="gap:10px;">
                                        <?php foreach($productImages as $img): ?>
                                            <div class="position-relative">
                                                <img src="<?= BASE_URL ?>assets/uploads/<?= $img->anh_path ?>" 
                                                     alt="<?= htmlspecialchars($product->sanpham_tieude) ?>" 
                                                     title="<?= $img->is_primary ? 'Ảnh chính' : '' ?> - <?= $img->color_ten ?? 'Không có màu' ?>"
                                                     style="width: 90px; height: 90px; object-fit: cover; border-radius: 4px; border: <?= $img->is_primary ? '2px solid #ffc107' : '1px solid #e9ecef' ?>;">
                                                <?php if($img->is_primary): ?>
                                                    <span class="badge badge-warning" style="position: absolute; top: 2px; right: 2px; font-size: 10px;">Chính</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php elseif(!empty($sanpham_anh)): ?>
                                    <div class="mb-3">
                                        <img src="<?= BASE_URL ?>assets/uploads/<?= $sanpham_anh ?>" 
                                             alt="Ảnh hiện tại" 
                                             style="max-width: 200px; max-height: 200px; object-fit: cover; border-radius: 4px;">
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> 
                                        Để quản lý ảnh theo màu (thêm, sửa, xóa), vui lòng sử dụng nút bên dưới.
                                    </div>
                                    
                                    <a href="<?= ADMIN_URL ?>productimage/<?= $product->sanpham_id ?>" 
                                       class="btn btn-primary btn-block">
                                        <i class="fas fa-images"></i> Quản lý album ảnh theo màu
                                    </a>
                                </div>

                                <div class="form-group">
                                    <label for="sanpham_chitiet" class="font-weight-bold">
                                        Mô tả chi tiết
                                    </label>
                                    <textarea class="form-control" 
                                              id="sanpham_chitiet" 
                                              name="sanpham_chitiet" 
                                              rows="4" 
                                              placeholder="Nhập mô tả chi tiết sản phẩm"><?= htmlspecialchars($sanpham_chitiet) ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="sanpham_baoquan" class="font-weight-bold">
                                        Hướng dẫn bảo quản
                                    </label>
                                    <textarea class="form-control" 
                                              id="sanpham_baoquan" 
                                              name="sanpham_baoquan" 
                                              rows="3" 
                                              placeholder="Nhập hướng dẫn bảo quản sản phẩm"><?= htmlspecialchars($sanpham_baoquan) ?></textarea>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Cập nhật sản phẩm
                                    </button>
                                    <a href="<?= ADMIN_URL ?>product" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Hủy
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thông tin sản phẩm</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <i class="fas fa-box fa-3x text-primary mb-3"></i>
                                <h5><?= htmlspecialchars($product->sanpham_tieude) ?></h5>
                                <p class="text-muted">ID: <?= $product->sanpham_id ?></p>
                            </div>
                            
                            <hr>
                            
                            <h6 class="font-weight-bold">Thông tin hiện tại:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-folder text-info"></i> Danh mục: <?= htmlspecialchars($product->danhmuc_ten ?? 'N/A') ?></li>
                                <li><i class="fas fa-tags text-secondary"></i> Loại: <?= htmlspecialchars($product->loaisanpham_ten ?? 'N/A') ?></li>
                                
                                <li><i class="fas fa-money-bill text-success"></i> Giá: <?= number_format($product->sanpham_gia, 0, ',', '.') ?>đ</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-warning">Lưu ý</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Chú ý:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Thay đổi sẽ được áp dụng ngay lập tức</li>
                                    <li>Ảnh mới sẽ thay thế ảnh cũ</li>
                                    <li>Mã sản phẩm không được trùng lặp</li>
                                    <li>Giá phải là số dương</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load loại sản phẩm khi chọn danh mục
document.getElementById('danhmuc_id').addEventListener('change', function() {
    const categoryId = this.value;
    const subcategorySelect = document.getElementById('loaisanpham_id');
    
    // Clear current options
    subcategorySelect.innerHTML = '<option value="">Chọn loại sản phẩm</option>';
    
    if (categoryId) {
        // Load subcategories via AJAX
        fetch('<?= ADMIN_URL ?>product/getSubcategoriesByCategory', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'category_id=' + categoryId
        })
        .then(response => response.json())
        .then(data => {
            data.forEach(function(subcategory) {
                const option = document.createElement('option');
                option.value = subcategory.loaisanpham_id;
                option.textContent = subcategory.loaisanpham_ten;
                // Select current subcategory if it matches
                if (subcategory.loaisanpham_id == <?= $loaisanpham_id ?>) {
                    option.selected = true;
                }
                subcategorySelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
});

// Load subcategories on page load
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('danhmuc_id');
    if (categorySelect.value) {
        categorySelect.dispatchEvent(new Event('change'));
    }
});

// Preview ảnh mới
document.getElementById('sanpham_anh').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            if (!preview) {
                const previewDiv = document.createElement('div');
                previewDiv.id = 'imagePreview';
                previewDiv.className = 'mt-2';
                previewDiv.innerHTML = '<label class="font-weight-bold">Ảnh mới:</label><br><img src="' + e.target.result + '" class="img-fluid" style="max-height: 200px;">';
                document.getElementById('sanpham_anh').parentNode.appendChild(previewDiv);
            } else {
                preview.innerHTML = '<label class="font-weight-bold">Ảnh mới:</label><br><img src="' + e.target.result + '" class="img-fluid" style="max-height: 200px;">';
            }
        };
        reader.readAsDataURL(file);
    }
});

// Validation form
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = ['sanpham_tieude', 'sanpham_ma', 'danhmuc_id', 'loaisanpham_id', 'sanpham_gia'];
    let isValid = true;
    
    requiredFields.forEach(function(fieldName) {
        const field = document.querySelector('[name="' + fieldName + '"]');
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('is-invalid');
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Vui lòng điền đầy đủ thông tin bắt buộc');
        return false;
    }
    
    // Validate price
    const price = parseFloat(document.getElementById('sanpham_gia').value);
    if (isNaN(price) || price <= 0) {
        e.preventDefault();
        alert('Giá sản phẩm phải là số dương');
        document.getElementById('sanpham_gia').focus();
        return false;
    }
});
</script>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>
