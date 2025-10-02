<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\admin\product\add.php

// Load header
require_once ROOT_PATH . 'app/views/shared/admin/header.php';

// Load sidebar
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';
?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Thêm sản phẩm mới</h1>
                <a href="<?= ADMIN_URL ?>product" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại
                </a>
            </div>

            <!-- Thông báo -->
            <?php if(!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><i class="fas fa-exclamation-triangle"></i> Không thể thêm sản phẩm!</strong><br>
                <?= htmlspecialchars($error) ?>
                <?php if(strpos($error, 'Mã sản phẩm') !== false): ?>
                <hr>
                <small>
                    <strong>Gợi ý:</strong>
                    <ul class="mb-0">
                        <li>Thay đổi mã sản phẩm thành mã chưa sử dụng</li>
                        <li>Hoặc tìm và chỉnh sửa sản phẩm có mã này trong danh sách</li>
                    </ul>
                </small>
                <?php endif; ?>
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
                            <form method="POST" action="<?= ADMIN_URL ?>product/add" enctype="multipart/form-data">
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

                                <!-- Upload ảnh theo nhóm màu -->
                                <div class="form-group">
                                    <label class="font-weight-bold">Ảnh sản phẩm <span class="text-danger">*</span></label>
                                    <div id="image-upload-container">
                                        <div class="image-upload-group" data-group-index="0">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Chọn màu cho nhóm ảnh:</label>
                                                        <select name="image_color_groups[0]" class="form-control">
                                                            <option value="">-- Không chọn màu --</option>
                                                            <?php foreach($colors as $color): ?>
                                                                <option value="<?= $color->color_id ?>">
                                                                    <?= htmlspecialchars($color->color_ten) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Chọn ảnh:</label>
                                                        <input type="file" 
                                                               name="product_images[0][]" 
                                                               class="form-control-file" 
                                                               multiple 
                                                               accept="image/jpeg,image/png,image/gif,image/webp">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" id="add-image-group" class="btn btn-secondary mt-2">
                                        <i class="fas fa-plus"></i> Thêm nhóm ảnh
                                    </button>
                                </div>

                                <div class="form-group">
                                    <label for="sanpham_chitiet" class="font-weight-bold">
                                        Mô tả chi tiết
                                    </label>
                                    <textarea 
                                        class="form-control" 
                                        id="sanpham_chitiet" 
                                        name="sanpham_chitiet" 
                                        rows="5" 
                                        placeholder="Nhập mô tả chi tiết sản phẩm"
                                    ><?= htmlspecialchars($sanpham_chitiet) ?></textarea>
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
                                        <i class="fas fa-save"></i> Thêm sản phẩm
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
                            <h6 class="m-0 font-weight-bold text-primary">Hướng dẫn</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <i class="fas fa-plus-circle fa-3x text-primary mb-3"></i>
                                <h5>Tạo sản phẩm mới</h5>
                                <p class="text-muted">
                                    Thêm sản phẩm mới vào hệ thống với đầy đủ thông tin.
                                </p>
                            </div>
                            
                            <hr>
                            
                            <h6 class="font-weight-bold">Thông tin bắt buộc:</h6>
                            <ul class="small">
                                <li>Tên sản phẩm</li>
                                <li>Mã sản phẩm (không trùng lặp)</li>
                                <li>Danh mục và loại sản phẩm</li>
                                <li>Giá sản phẩm</li>
                                <li>Ảnh sản phẩm</li>
                            </ul>

                            <hr>

                            <h6 class="font-weight-bold">Lưu ý:</h6>
                            <ul class="small text-muted">
                                <li>Mã sản phẩm phải duy nhất</li>
                                <li>Ảnh sẽ được resize tự động</li>
                                <li>Giá nhập bằng số nguyên</li>
                                <li>Có thể chỉnh sửa sau khi tạo</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-warning">Preview ảnh</h6>
                        </div>
                        <div class="card-body">
                            <div id="imagePreview" class="text-center">
                                <i class="fas fa-image fa-3x text-muted"></i>
                                <p class="text-muted mt-2">Chọn ảnh để xem trước</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto focus vào input tên sản phẩm
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('sanpham_tieude').focus();
});

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
                subcategorySelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
});

// Preview ảnh
document.getElementById('sanpham_anh').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" class="img-fluid" style="max-height: 200px;">';
        };
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '<i class="fas fa-image fa-3x text-muted"></i><p class="text-muted mt-2">Chọn ảnh để xem trước</p>';
    }
});

// Quản lý nhóm ảnh động
document.getElementById('add-image-group').addEventListener('click', function() {
    const container = document.getElementById('image-upload-container');
    const groups = container.querySelectorAll('.image-upload-group');
    const newIndex = groups.length;

    const newGroup = document.createElement('div');
    newGroup.className = 'image-upload-group';
    newGroup.setAttribute('data-group-index', newIndex);
    newGroup.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Chọn màu cho nhóm ảnh:</label>
                    <select name="image_color_groups[${newIndex}]" class="form-control">
                        <option value="">-- Không chọn màu --</option>
                        <?php foreach($colors as $color): ?>
                            <option value="<?= $color->color_id ?>">
                                <?= htmlspecialchars($color->color_ten) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Chọn ảnh:</label>
                    <input type="file" 
                           name="product_images[${newIndex}][]" 
                           class="form-control-file" 
                           multiple 
                           accept="image/jpeg,image/png,image/gif,image/webp">
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-danger btn-sm remove-image-group mt-2">
            <i class="fas fa-trash"></i> Xóa nhóm
        </button>
    `;

    container.appendChild(newGroup);

    // Thêm sự kiện xóa nhóm
    newGroup.querySelector('.remove-image-group').addEventListener('click', function() {
        container.removeChild(newGroup);
    });
});

// Validation form
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = ['sanpham_tieude', 'sanpham_ma', 'danhmuc_id', 'loaisanpham_id', 'color_id', 'sanpham_gia', 'sanpham_anh'];
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
