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
            <div class="d-sm-flex align-it                        }
                // Thêm event listener cho dropdown màu mới (sử dụng jQuery delegation nên không cần)
            // jQuery đã handle event delegation ở ngoài rồi: $(document).on('change', 'select[name^="image_color_groups"]'...)
            console.log('✅ Event listener cho dropdown màu sẽ được xử lý bởi jQuery delegation');
        }, { once: false }); // Không dùng once vì cần thêm nhiều nhóm
    }    }, 100);
                });
            }
        });
    }

    // Validation formcontent-between mb-4">
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
                                                           value="<?= htmlspecialchars($sanpham_gia_goc ?? '') ?>"
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
                                                               value="<?= htmlspecialchars($sanpham_giam_gia ?? '') ?>"
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

                                <!-- Upload ảnh theo nhóm màu -->
                                <div class="form-group">
                                    <label class="font-weight-bold">Ảnh sản phẩm <span class="text-danger">*</span></label>
                                    <div id="image-upload-container">
                                        <div class="image-upload-group" data-group-index="0">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Chọn màu cho nhóm ảnh:</label>
                                                        <select name="image_color_groups[0]" class="form-control color-group-select">
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

                                <!-- *** THÊM MỚI: Quản lý size & tồn kho (VARIANT SYSTEM) *** -->
                                <div class="card border-info mb-4">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="m-0 font-weight-bold">
                                            <i class="fas fa-box"></i> Quản lý tồn kho theo Size & Màu
                                            <small class="float-right" style="font-size: 0.85em;">* Chọn màu ở trên trước</small>
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> 
                                            <strong>Hướng dẫn:</strong> Nhập số lượng tồn kho cho từng size và màu. Để trống = không kinh doanh size đó.
                                        </div>
                                        
                                        <div id="variant-container">
                                            <!-- JavaScript sẽ tự động tạo form dựa trên màu đã chọn -->
                                            <div class="text-center text-muted py-4">
                                                <i class="fas fa-palette fa-3x mb-3"></i>
                                                <p>Vui lòng chọn màu ở phần "Chọn màu cho nhóm ảnh" phía trên để hiển thị form nhập tồn kho</p>
                                            </div>
                                        </div>
                                    </div>
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
                                <li>Giá gốc và giá bán</li>
                                <li>Ảnh sản phẩm</li>
                            </ul>

                            <hr>

                            <h6 class="font-weight-bold">Lưu ý:</h6>
                            <ul class="small text-muted">
                                <li>Mã sản phẩm phải duy nhất</li>
                                <li>Ảnh sẽ được resize tự động</li>
                                <li>Giá nhập bằng số nguyên</li>
                                <li>% giảm giá tự động tính từ giá gốc và giá bán</li>
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
// ============================================
// WRAP TẤT CẢ VANILLA JS TRONG DOMContentLoaded
// ============================================
console.log('%c🚀 Script add.php được load', 'background: #222; color: #bada55; font-size: 14px; padding: 5px;');

let addPhpDOMLoadedCount = 0;

document.addEventListener('DOMContentLoaded', function() {
    addPhpDOMLoadedCount++;
    console.log(`%c⚡ DOMContentLoaded trong add.php - Lần thứ: ${addPhpDOMLoadedCount}`, addPhpDOMLoadedCount > 1 ? 'background: red; color: white; font-weight: bold; padding: 5px;' : 'background: green; color: white; padding: 5px;');
    
    if(addPhpDOMLoadedCount > 1) {
        console.error('❌❌❌ DOMContentLoaded BỊ GỌI NHIỀU HƠN 1 LẦN! Đây là nguyên nhân của bug!');
        return; // Dừng lại để tránh attach event 2 lần
    }
    
    console.log('✅ DOM đã load xong');
    
    // Auto focus vào input tên sản phẩm
    const sanphamTieudeInput = document.getElementById('sanpham_tieude');
    if(sanphamTieudeInput) {
        sanphamTieudeInput.focus();
    }

    // Load loại sản phẩm khi chọn danh mục
    const danhmucSelect = document.getElementById('danhmuc_id');
    if(danhmucSelect) {
        danhmucSelect.addEventListener('change', function() {
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
    }

    // Preview ảnh
    const sanphamAnhInput = document.getElementById('sanpham_anh');
    if(sanphamAnhInput) {
        sanphamAnhInput.addEventListener('change', function(e) {
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
    }

    // Quản lý nhóm ảnh động
    const addImageGroupBtn = document.getElementById('add-image-group');
    if(addImageGroupBtn) {
        // Xóa tất cả event listeners cũ trước khi thêm mới
        const newBtn = addImageGroupBtn.cloneNode(true);
        addImageGroupBtn.parentNode.replaceChild(newBtn, addImageGroupBtn);
        
        newBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            console.log('🖼️ Thêm nhóm màu mới - Event triggered');
            
            const container = document.getElementById('image-upload-container');
            const groups = container.querySelectorAll('.image-upload-group');
            const newIndex = groups.length;

            const newGroup = document.createElement('div');
            newGroup.className = 'image-upload-group';
            newGroup.setAttribute('data-group-index', newIndex);
            newGroup.innerHTML = `
                <hr class="my-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Chọn màu cho nhóm ảnh:</label>
                            <select name="image_color_groups[${newIndex}]" class="form-control color-group-select">
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
            console.log('✅ Đã thêm nhóm màu index:', newIndex);

            // Thêm sự kiện xóa nhóm
            const removeBtn = newGroup.querySelector('.remove-image-group');
            if(removeBtn) {
                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('🗑️ Xóa nhóm màu index:', newIndex);
                    container.removeChild(newGroup);
                    
                    // Cập nhật lại variant form sau khi xóa (với debounce)
                    if (updateVariantFormTimer) {
                        clearTimeout(updateVariantFormTimer);
                    }
                    
                    updateVariantFormTimer = setTimeout(function() {
                        if(typeof updateVariantForm === 'function') {
                            console.log('🔄 Cập nhật variant form sau khi xóa nhóm màu');
                            updateVariantForm();
                        }
                        updateVariantFormTimer = null;
                    }, 200);
                });
            }
            
            // Thêm event listener cho dropdown màu mới
            const newColorSelect = newGroup.querySelector('.color-group-select');
            if(newColorSelect) {
                newColorSelect.addEventListener('change', function() {
                    console.log('� Màu mới được chọn:', this.value);
                    if(typeof updateVariantForm === 'function') {
                        updateVariantForm();
                    }
                });
            }
        });
    }

    // Validation form
    const formElement = document.querySelector('form');
    if(formElement) {
        formElement.addEventListener('submit', function(e) {
            const requiredFields = ['sanpham_tieude', 'sanpham_ma', 'danhmuc_id', 'loaisanpham_id', 'sanpham_gia_goc', 'sanpham_gia'];
            let isValid = true;
            
            requiredFields.forEach(function(fieldName) {
                const field = document.querySelector('[name="' + fieldName + '"]');
                if (field && !field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else if(field) {
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
    }
    
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
    
    console.log('✅ Tất cả event listeners đã được thiết lập');
});

// ============================================
// *** THÊM MỚI: VARIANT MANAGER (SIZE & COLOR) ***
// ============================================

// Danh sách size từ PHP
const sizes = <?= json_encode(array_map(function($s) {
    return ['id' => $s->size_id, 'name' => $s->size_ten];
}, $sizes ?? [])) ?>;

// Debug: Kiểm tra xem sizes có dữ liệu không
console.log('🔍 DEBUG: Sizes from PHP:', sizes);
console.log('🔍 DEBUG: Số lượng sizes:', sizes.length);

// Danh sách màu từ PHP
const allColors = <?= json_encode(array_map(function($c) {
    return ['id' => $c->color_id, 'name' => $c->color_ten];
}, $colors ?? [])) ?>;

console.log('🔍 DEBUG: Colors from PHP:', allColors);
console.log('🔍 DEBUG: Số lượng colors:', allColors.length);

// Debounce timer để tránh gọi updateVariantForm() quá nhiều lần
let updateVariantFormTimer = null;

// Lắng nghe sự kiện thay đổi màu trong các nhóm ảnh
$(document).on('change', 'select[name^="image_color_groups"]', function() {
    console.log('🎨 Dropdown màu thay đổi');
    
    // Clear timer cũ
    if (updateVariantFormTimer) {
        clearTimeout(updateVariantFormTimer);
    }
    
    // Đặt timer mới để gọi updateVariantForm sau 200ms
    updateVariantFormTimer = setTimeout(function() {
        updateVariantForm();
        updateVariantFormTimer = null;
    }, 200);
});

// Cập nhật form variant dựa trên màu đã chọn
function updateVariantForm() {
    console.log('🔄 updateVariantForm() được gọi');
    
    // Lấy tất cả màu đã chọn từ các dropdowns
    const selectedColors = [];
    $('select[name^="image_color_groups"]').each(function() {
        const colorId = $(this).val();
        console.log('  - Kiểm tra dropdown:', $(this).attr('name'), '→ Giá trị:', colorId);
        if (colorId && !selectedColors.includes(colorId)) {
            selectedColors.push(colorId);
        }
    });
    
    console.log('✅ Màu đã chọn:', selectedColors);
    console.log('📏 Sizes available:', sizes.length, 'items');
    
    // Nếu không có màu nào được chọn, hiện thông báo
    if (selectedColors.length === 0) {
        $('#variant-container').html(`
            <div class="text-center text-muted py-4">
                <i class="fas fa-palette fa-3x mb-3"></i>
                <p>Vui lòng chọn màu ở phần "Chọn màu cho nhóm ảnh" phía trên để hiển thị form nhập tồn kho</p>
            </div>
        `);
        console.log('⚠️ Không có màu nào được chọn');
        return;
    }
    
    // Kiểm tra xem có sizes không
    if (!sizes || sizes.length === 0) {
        $('#variant-container').html(`
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Lỗi:</strong> Không có sizes trong hệ thống. 
                <a href="<?= ADMIN_URL ?>size/add" target="_blank" class="alert-link">Thêm size ngay</a>
            </div>
        `);
        console.error('❌ Không có sizes!');
        return;
    }
    
    // Tạo form nhập tồn kho cho từng màu
    let html = '';
    
    selectedColors.forEach(colorId => {
        const color = allColors.find(c => c.id == colorId);
        if (!color) {
            console.error('❌ Không tìm thấy color:', colorId);
            return;
        }
        
        console.log('✅ Tạo form cho màu:', color.name);
        
        html += `
        <div class="card mb-3 border-left-primary">
            <div class="card-header bg-gradient-primary text-white">
                <strong><i class="fas fa-palette"></i> Màu: ${color.name}</strong>
            </div>
            <div class="card-body">
                <div class="row">
        `;
        
        sizes.forEach(size => {
            html += `
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><strong>${size.name}</strong></span>
                        </div>
                        <input type="number" 
                               name="variants[${colorId}][${size.id}][ton_kho]" 
                               class="form-control" 
                               placeholder="SL"
                               min="0"
                               value="0">
                        <input type="hidden" 
                               name="variants[${colorId}][${size.id}][color_id]" 
                               value="${colorId}">
                        <input type="hidden" 
                               name="variants[${colorId}][${size.id}][size_id]" 
                               value="${size.id}">
                    </div>
                    <small class="text-muted">Để 0 = hết hàng</small>
                </div>
            `;
        });
        
        html += `
                </div>
            </div>
        </div>
        `;
    });
    
    $('#variant-container').html(html);
    console.log('✅ Form đã được cập nhật thành công');
}

// Khởi tạo
$(document).ready(function() {
    updateVariantForm();
});
</script>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>
