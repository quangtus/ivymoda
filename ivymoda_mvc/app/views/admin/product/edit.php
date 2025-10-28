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

                                <!-- *** THÊM MỚI: Hệ thống giá với chiết khấu *** -->
                                <div class="card border-success mb-4">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="m-0 font-weight-bold">
                                            <i class="fas fa-tags"></i> Thông tin giá sản phẩm
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="sanpham_gia_goc" class="font-weight-bold">
                                                        Giá gốc <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number" 
                                                           class="form-control price-input" 
                                                           id="sanpham_gia_goc" 
                                                           name="sanpham_gia_goc" 
                                                           value="<?= htmlspecialchars($sanpham_gia_goc ?? $product->sanpham_gia_goc ?? '') ?>"
                                                           placeholder="Nhập giá gốc"
                                                           min="0"
                                                           step="1000"
                                                           required>
                                                    <small class="form-text text-muted">
                                                        Giá ban đầu của sản phẩm
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="sanpham_gia" class="font-weight-bold">
                                                        Giá bán <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number" 
                                                           class="form-control price-input" 
                                                           id="sanpham_gia" 
                                                           name="sanpham_gia" 
                                                           value="<?= htmlspecialchars($sanpham_gia) ?>"
                                                           placeholder="Nhập giá bán"
                                                           min="0"
                                                           step="1000"
                                                           required>
                                                    <small class="form-text text-muted">
                                                        Giá hiển thị cho khách hàng
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="sanpham_giam_gia" class="font-weight-bold">
                                                        % Giảm giá
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="number" 
                                                               class="form-control" 
                                                               id="sanpham_giam_gia" 
                                                               name="sanpham_giam_gia" 
                                                               value="<?= htmlspecialchars($sanpham_giam_gia ?? $product->sanpham_giam_gia ?? '') ?>"
                                                               placeholder="0.00"
                                                               min="0"
                                                               max="100"
                                                               step="0.01"
                                                               readonly>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        Tự động tính từ giá gốc và giá bán
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Preview giá -->
                                        <div class="alert alert-info" id="price-preview">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>Giá gốc:</strong> <span id="preview-gia-goc">0 ₫</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Giá bán:</strong> <span id="preview-gia-ban">0 ₫</span>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-md-6">
                                                    <strong>Tiết kiệm:</strong> <span id="preview-tiet-kiem" class="text-success">0 ₫</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Giảm giá:</strong> <span id="preview-giam-gia" class="text-danger">0%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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

                                <!-- *** THÊM MỚI: QUẢN LÝ VARIANTS *** -->
                                <div class="card border-info mb-4">
                                    <div class="card-header bg-gradient-info text-white">
                                        <h6 class="m-0 font-weight-bold">
                                            <i class="fas fa-warehouse"></i> Quản lý tồn kho theo Size & Màu
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> 
                                            <strong>Hướng dẫn:</strong> Cập nhật tồn kho cho từng size+màu. Click "Lưu" sau mỗi thay đổi hoặc "Xóa" để ngừng bán variant đó.
                                        </div>

                                        <!-- Bảng variants hiện có -->
                                        <?php if(!empty($variants) && is_array($variants) && count($variants) > 0): ?>
                                        <h6 class="font-weight-bold mb-3">📦 Tồn kho hiện tại:</h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th width="60">STT</th>
                                                        <th>SKU</th>
                                                        <th>Màu</th>
                                                        <th>Size</th>
                                                        <th width="120">Tồn kho</th>
                                                        <th width="100">Trạng thái</th>
                                                        <th width="180">Thao tác</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($variants as $index => $variant): ?>
                                                    <tr data-variant-id="<?= $variant->variant_id ?>" id="variant-row-<?= $variant->variant_id ?>">
                                                        <td class="text-center"><?= $index + 1 ?></td>
                                                        <td><code><?= htmlspecialchars($variant->sku) ?></code></td>
                                                        <td>
                                                            <span class="badge badge-primary">
                                                                <?= htmlspecialchars($variant->color_ten) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-secondary">
                                                                <?= htmlspecialchars($variant->size_ten) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <input type="number" 
                                                                   class="form-control form-control-sm text-center variant-stock-input" 
                                                                   value="<?= $variant->ton_kho ?>" 
                                                                   data-variant-id="<?= $variant->variant_id ?>"
                                                                   data-original-value="<?= $variant->ton_kho ?>"
                                                                   min="0"
                                                                   style="width: 100px; font-weight: bold;">
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if($variant->trang_thai == 1 && $variant->ton_kho > 0): ?>
                                                                <span class="badge badge-success">Còn hàng</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-danger">Hết hàng</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-primary update-variant-btn" 
                                                                    data-variant-id="<?= $variant->variant_id ?>"
                                                                    title="Cập nhật tồn kho">
                                                                <i class="fas fa-save"></i> Lưu
                                                            </button>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-danger delete-variant-btn" 
                                                                    data-variant-id="<?= $variant->variant_id ?>"
                                                                    title="Xóa variant">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr class="table-info">
                                                        <td colspan="4" class="text-right"><strong>Tổng tồn kho:</strong></td>
                                                        <td class="text-center">
                                                            <strong class="text-success" style="font-size: 1.1em;">
                                                                <?= array_sum(array_column($variants, 'ton_kho')) ?>
                                                            </strong>
                                                        </td>
                                                        <td colspan="2"></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <?php else: ?>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i> 
                                            <strong>Chưa có variant:</strong> Sản phẩm này chưa có tồn kho chi tiết. Thêm màu và nhập tồn kho bên dưới.
                                        </div>
                                        <?php endif; ?>

                                        <hr class="my-4">

                                        <!-- Form thêm variant mới -->
                                        <h6 class="font-weight-bold mb-3">➕ Thêm variant mới:</h6>
                                        <div id="variant-form-container">
                                            <p class="text-muted">Chọn màu ở phần "Màu sắc bổ sung" phía trên để hiển thị form thêm variant...</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save"></i> Cập nhật sản phẩm
                                    </button>
                                    <a href="<?= ADMIN_URL ?>product" class="btn btn-secondary btn-lg">
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
    
// Validate prices
const giaGoc = parseFloat(document.getElementById('sanpham_gia_goc').value);
const giaBan = parseFloat(document.getElementById('sanpham_gia').value);

if (isNaN(giaGoc) || giaGoc <= 0) {
    e.preventDefault();
    alert('Giá gốc phải là số dương');
    document.getElementById('sanpham_gia_goc').focus();
    return false;
}

if (isNaN(giaBan) || giaBan <= 0) {
    e.preventDefault();
    alert('Giá bán phải là số dương');
    document.getElementById('sanpham_gia').focus();
    return false;
}

if (giaBan > giaGoc) {
    e.preventDefault();
    alert('Giá bán không được cao hơn giá gốc');
    document.getElementById('sanpham_gia').focus();
    return false;
}
});

// *** THÊM MỚI: Logic tính toán giá với chiết khấu ***
const giaGocInput = document.getElementById('sanpham_gia_goc');
const giaBanInput = document.getElementById('sanpham_gia');
const giamGiaInput = document.getElementById('sanpham_giam_gia');

// Function tính toán và cập nhật preview
function updatePriceCalculation() {
    const giaGoc = parseFloat(giaGocInput.value) || 0;
    const giaBan = parseFloat(giaBanInput.value) || 0;
    
    // Cập nhật preview
    document.getElementById('preview-gia-goc').textContent = formatPrice(giaGoc);
    document.getElementById('preview-gia-ban').textContent = formatPrice(giaBan);
    
    if (giaGoc > 0 && giaBan > 0) {
        const tietKiem = giaGoc - giaBan;
        const phanTramGiam = giaGoc > giaBan ? ((giaGoc - giaBan) / giaGoc * 100) : 0;
        
        document.getElementById('preview-tiet-kiem').textContent = formatPrice(tietKiem);
        document.getElementById('preview-giam-gia').textContent = phanTramGiam.toFixed(2) + '%';
        
        // Cập nhật trường % giảm giá
        giamGiaInput.value = phanTramGiam.toFixed(2);
        
        // Validation
        if (giaBan > giaGoc) {
            giaBanInput.classList.add('is-invalid');
            document.getElementById('preview-giam-gia').innerHTML = '<span class="text-danger">Giá bán không được cao hơn giá gốc!</span>';
        } else {
            giaBanInput.classList.remove('is-invalid');
        }
    } else {
        document.getElementById('preview-tiet-kiem').textContent = '0 ₫';
        document.getElementById('preview-giam-gia').textContent = '0%';
        giamGiaInput.value = '';
    }
}

// Function format giá tiền
function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN').format(price) + ' ₫';
}

// Event listeners cho các trường giá
if (giaGocInput && giaBanInput) {
    giaGocInput.addEventListener('input', updatePriceCalculation);
    giaBanInput.addEventListener('input', updatePriceCalculation);
    
    // Khởi tạo preview
    updatePriceCalculation();
}

// ============================================
// QUẢN LÝ VARIANTS - AJAX OPERATIONS
// ============================================

// Cập nhật tồn kho variant
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('update-variant-btn') || e.target.closest('.update-variant-btn')) {
        const btn = e.target.classList.contains('update-variant-btn') ? e.target : e.target.closest('.update-variant-btn');
        const variantId = btn.getAttribute('data-variant-id');
        const row = document.querySelector(`tr[data-variant-id="${variantId}"]`);
        const stockInput = row.querySelector('.variant-stock-input');
        const newStock = parseInt(stockInput.value);
        
        if (isNaN(newStock) || newStock < 0) {
            alert('Tồn kho phải là số không âm');
            return;
        }
        
        // Disable button during request
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
        
        fetch('<?= ADMIN_URL ?>product/updateVariantStockAjax', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `variant_id=${variantId}&ton_kho=${newStock}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update original value
                stockInput.setAttribute('data-original-value', newStock);
                
                // Update status badge
                const statusCell = row.querySelector('td:nth-child(6)');
                if (newStock > 0) {
                    statusCell.innerHTML = '<span class="badge badge-success">Còn hàng</span>';
                } else {
                    statusCell.innerHTML = '<span class="badge badge-danger">Hết hàng</span>';
                }
                
                // Update total stock in footer
                updateTotalStock();
                
                // Show success message
                alert('✅ Cập nhật tồn kho thành công!');
            } else {
                alert('❌ Lỗi: ' + (data.message || 'Không thể cập nhật'));
                // Restore original value
                stockInput.value = stockInput.getAttribute('data-original-value');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Lỗi kết nối: ' + error.message);
            stockInput.value = stockInput.getAttribute('data-original-value');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Lưu';
        });
    }
});

// Xóa variant
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('delete-variant-btn') || e.target.closest('.delete-variant-btn')) {
        const btn = e.target.classList.contains('delete-variant-btn') ? e.target : e.target.closest('.delete-variant-btn');
        const variantId = btn.getAttribute('data-variant-id');
        const row = document.querySelector(`tr[data-variant-id="${variantId}"]`);
        const sku = row.querySelector('code').textContent;
        
        if (!confirm(`⚠️ Xác nhận xóa variant "${sku}"?\n\nLưu ý: Hành động này không thể hoàn tác!`)) {
            return;
        }
        
        // Disable button during request
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        fetch('<?= ADMIN_URL ?>product/deleteVariant', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `variant_id=${variantId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove row with animation
                row.style.transition = 'opacity 0.3s';
                row.style.opacity = '0';
                setTimeout(() => {
                    row.remove();
                    updateTotalStock();
                    
                    // Check if table is empty
                    const tbody = document.querySelector('tbody');
                    if (tbody.children.length === 0) {
                        location.reload();
                    }
                }, 300);
                
                alert('✅ Xóa variant thành công!');
            } else {
                alert('❌ Lỗi: ' + (data.message || 'Không thể xóa'));
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-trash"></i>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Lỗi kết nối: ' + error.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-trash"></i>';
        });
    }
});

// Cập nhật tổng tồn kho
function updateTotalStock() {
    const stockInputs = document.querySelectorAll('.variant-stock-input');
    let total = 0;
    stockInputs.forEach(input => {
        total += parseInt(input.value) || 0;
    });
    
    const totalCell = document.querySelector('tfoot td:nth-child(5) strong');
    if (totalCell) {
        totalCell.textContent = total;
    }
}

// Render form thêm variant mới dựa trên màu đã chọn
function renderVariantForm() {
    const colorCheckboxes = document.querySelectorAll('input[name="colors[]"]:checked');
    const container = document.getElementById('variant-form-container');
    
    if (colorCheckboxes.length === 0) {
        container.innerHTML = '<p class="text-muted">Chọn màu ở phần "Màu sắc bổ sung" phía trên để hiển thị form thêm variant...</p>';
        return;
    }
    
    // Get existing variants to avoid duplicates
    const existingVariants = new Set();
    document.querySelectorAll('tbody tr[data-variant-id]').forEach(row => {
        const colorBadge = row.querySelector('td:nth-child(3) .badge').textContent.trim();
        const sizeBadge = row.querySelector('td:nth-child(4) .badge').textContent.trim();
        existingVariants.add(`${colorBadge}_${sizeBadge}`);
    });
    
    // Get sizes from PHP
    const sizes = <?= json_encode(array_map(function($size) {
        return ['size_id' => $size->size_id, 'size_ten' => $size->size_ten];
    }, $sizes ?? [])) ?>;
    
    // Get colors info
    const colorsData = <?= json_encode(array_map(function($color) {
        return ['color_id' => $color->color_id, 'color_ten' => $color->color_ten];
    }, $colors ?? [])) ?>;
    
    let html = '<div class="alert alert-success"><i class="fas fa-plus-circle"></i> Chọn size để thêm vào tồn kho:</div>';
    
    colorCheckboxes.forEach(checkbox => {
        const colorId = checkbox.value;
        const colorData = colorsData.find(c => c.color_id == colorId);
        if (!colorData) return;
        
        const colorName = colorData.color_ten;
        
        html += `
            <div class="card mb-3 border-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0">Màu: ${colorName}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
        `;
        
        sizes.forEach(size => {
            const key = `${colorName}_${size.size_ten}`;
            const exists = existingVariants.has(key);
            
            if (!exists) {
                html += `
                    <div class="col-md-3 mb-3">
                        <div class="card border-secondary">
                            <div class="card-body text-center p-2">
                                <h6 class="mb-2">${size.size_ten}</h6>
                                <label class="small text-muted">Tồn kho:</label>
                                <input type="number" 
                                       name="new_variants[${colorId}][${size.size_id}][ton_kho]" 
                                       class="form-control form-control-sm text-center" 
                                       value="0" 
                                       min="0"
                                       placeholder="0">
                            </div>
                        </div>
                    </div>
                `;
            }
        });
        
        html += `
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Lắng nghe thay đổi color checkboxes
document.addEventListener('change', function(e) {
    if (e.target.matches('input[name="colors[]"]')) {
        renderVariantForm();
    }
});

// Render form on page load
document.addEventListener('DOMContentLoaded', function() {
    renderVariantForm();
});
</script>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>
