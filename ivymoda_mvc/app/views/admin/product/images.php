<?php
require_once ROOT_PATH . 'app/views/shared/admin/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        📸 Quản lý ảnh sản phẩm
                    </h4>
                    <div class="card-tools">
                        <a href="<?= BASE_URL ?>admin/product" class="btn btn-secondary">
                            ← Quay lại
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Thông tin sản phẩm -->
                    <div class="product-info mb-4">
                        <h5><?= htmlspecialchars($product->sanpham_tieude) ?></h5>
                        <p class="text-muted">Mã sản phẩm: <?= htmlspecialchars($product->sanpham_ma) ?></p>
                    </div>
                    
                    <!-- Form upload ảnh (hỗ trợ nhiều nhóm màu với JS clone) -->
                    <div class="upload-section mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Thêm ảnh mới</h6>
                            </div>
                            <div class="card-body">
                                <?php if(empty($availableColors)): ?>
                                <div class="alert alert-info">
                                    <strong>🎉 Không sao!</strong><br>
                                    Bạn có thể chọn màu bất kỳ từ dropdown phía trên để upload ảnh.<br>
                                    Màu sẽ tự động được thêm vào sản phẩm này.
                                </div>
                                <?php else: ?>
                                <form action="<?= BASE_URL ?>admin/productimage/upload" method="POST" enctype="multipart/form-data" id="upload-form">
                                    <input type="hidden" name="product_id" value="<?= $product->sanpham_id ?>">
                                    <div id="upload-group-container">
                                        <div class="upload-group" data-index="0">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="color_group_0">Chọn màu (nhóm ảnh):</label>
                                                        <select class="form-control color-select" id="color_group_0" name="color_group[0]">
                                                            <option value="">-- Không gán màu --</option>
                                                            <?php 
                                                            // Hiển thị TẤT CẢ màu trong hệ thống
                                                            $colorModel = new \ColorModel();
                                                            $allColors = $colorModel->getAllColors();
                                                            
                                                            if(!empty($allColors)):
                                                                foreach($allColors as $color):
                                                                    // Kiểm tra màu đã được gán chưa
                                                                    $isAssigned = false;
                                                                    if(!empty($availableColors)) {
                                                                        foreach($availableColors as $assignedColor) {
                                                                            if($assignedColor->color_id == $color->color_id) {
                                                                                $isAssigned = true;
                                                                                break;
                                                                            }
                                                                        }
                                                                    }
                                                                    
                                                                    $label = htmlspecialchars($color->color_ten);
                                                                    if(!$isAssigned) {
                                                                        $label .= ' (➕ Mới)';
                                                                    }
                                                            ?>
                                                                <option value="<?= $color->color_id ?>" 
                                                                        data-is-new="<?= !$isAssigned ? '1' : '0' ?>" 
                                                                        <?= (isset($selectedColorId) && $selectedColorId == $color->color_id) ? 'selected' : '' ?>>
                                                                    <?= $label ?>
                                                                </option>
                                                            <?php 
                                                                endforeach;
                                                            endif;
                                                            ?>
                                                        </select>
                                                        <small class="form-text text-success">
                                                            <strong>✨ Mới:</strong> Bạn có thể chọn bất kỳ màu nào. Màu mới sẽ tự động được gán vào sản phẩm.
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <div class="form-check mt-4">
                                                            <input type="checkbox" class="form-check-input" id="is_primary_0" name="is_primary[0]" value="1">
                                                            <label class="form-check-label" for="is_primary_0">Đặt làm ảnh chính</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="product_image_group_0">Chọn ảnh:</label>
                                                <input type="file" class="form-control-file" id="product_image_group_0" name="product_image_group[0][]" accept="image/*" multiple required>
                                                <small class="form-text text-muted">Hỗ trợ: JPG, PNG, GIF, WebP (tối đa 10MB/ảnh). Có thể chọn nhiều ảnh.</small>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-secondary" id="btn-add-group">➕ Thêm nhóm màu</button>
                                    <button type="submit" class="btn btn-primary">📤 Upload ảnh</button>
                                </form>
                                <?php endif; ?>
                                
                                <div class="alert alert-info mt-3 mb-0">
                                    <strong>💡 Mẹo:</strong> 
                                    <ul class="mb-0">
                                        <li><strong>Màu mới (➕):</strong> Màu chưa được gán cho sản phẩm. Khi upload ảnh, màu sẽ tự động được thêm.</li>
                                        <li><strong>Variant:</strong> Hệ thống tự động tạo variant mặc định (size S, tồn kho 0) cho màu mới.</li>
                                        <li><strong>Cập nhật tồn kho:</strong> Vào <a href="<?= BASE_URL ?>admin/product/edit/<?= $product->sanpham_id ?>" class="alert-link">Sửa sản phẩm</a> để quản lý chi tiết variant.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filter by color -->
                    <div class="mb-3">
                        <div class="form-row align-items-end">
                            <div class="col-md-4">
                                <label for="filter_color_id">Lọc theo màu:</label>
                                <select class="form-control" id="filter_color_id" name="color_id" onchange="filterByColor(this.value)">
                                    <option value="">Tất cả màu</option>
                                    <?php if(isset($availableColors) && !empty($availableColors)): ?>
                                        <?php foreach($availableColors as $color): ?>
                                            <option value="<?= $color->color_id ?>" <?= (isset($selectedColorId) && $selectedColorId == $color->color_id) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($color->color_ten) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <?php if(isset($selectedColorId) && $selectedColorId): ?>
                            <div class="col-md-4">
                                <form method="POST" action="<?= BASE_URL ?>admin/productimage/deleteColorGroup" onsubmit="return confirm('Xóa toàn bộ ảnh của màu đã chọn?');">
                                    <input type="hidden" name="product_id" value="<?= $product->sanpham_id ?>">
                                    <input type="hidden" name="color_id" value="<?= (int)$selectedColorId ?>">
                                    <button type="submit" class="btn btn-outline-danger mt-4">
                                        ❌ Xóa toàn bộ ảnh theo màu
                                    </button>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <script>
                    // Hàm lọc theo màu - Control URL chính xác
                    function filterByColor(colorId) {
                        const productId = <?= $product->sanpham_id ?>;
                        const baseUrl = '<?= BASE_URL ?>admin/productimage/' + productId;
                        
                        if(colorId && colorId !== '') {
                            // Redirect với color_id mới
                            window.location.href = baseUrl + '?color_id=' + colorId;
                        } else {
                            // Hiển thị tất cả màu (không có query string)
                            window.location.href = baseUrl;
                        }
                    }
                    </script>

                    <!-- Danh sách ảnh -->
                    <div class="images-section">
                        <?php 
                        // Lấy tên màu đang lọc
                        $selectedColorName = '';
                        if(isset($selectedColorId) && $selectedColorId && !empty($availableColors)) {
                            foreach($availableColors as $c) {
                                if($c->color_id == $selectedColorId) {
                                    $selectedColorName = $c->color_ten;
                                    break;
                                }
                            }
                        }
                        ?>
                        <h6 class="mb-3">
                            Danh sách ảnh (<?= count($productImages) ?> ảnh<?= $selectedColorName ? ' - Màu: ' . htmlspecialchars($selectedColorName) : '' ?>)
                        </h6>
                        
                        <?php if(!empty($productImages)): ?>
                            <div class="row">
                                <?php foreach($productImages as $image): ?>
                                    <div class="col-md-3 col-sm-4 col-6 mb-3">
                                        <div class="image-card <?= $image->is_primary ? 'primary' : '' ?>" data-image-id="<?= $image->anh_id ?>">
                                            <div class="image-preview" onclick="showImagePreview('<?= BASE_URL ?>assets/uploads/<?= $image->anh_path ?>', '<?= htmlspecialchars($product->sanpham_tieude) ?>')">
                                                <img src="<?= BASE_URL ?>assets/uploads/<?= $image->anh_path ?>" 
                                                     alt="Ảnh sản phẩm" 
                                                     class="img-fluid"
                                                     onerror="this.src='<?= BASE_URL ?>assets/images/no-image.jpg'">
                                                
                                                <?php if($image->is_primary): ?>
                                                    <div class="primary-badge">
                                                        ⭐ Ảnh chính
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="hover-overlay">
                                                    🔍
                                                </div>
                                                
                                                <div class="image-actions">
                                                    <?php if(!$image->is_primary): ?>
                                                        <form action="<?= BASE_URL ?>admin/productimage/setPrimary" method="POST" class="d-inline" onsubmit="return confirmSetPrimary('<?= $image->anh_path ?>')">
                                                            <input type="hidden" name="image_id" value="<?= $image->anh_id ?>">
                                                            <input type="hidden" name="product_id" value="<?= $product->sanpham_id ?>">
                                                            <button type="submit" class="btn btn-sm btn-warning" title="Đặt làm ảnh chính" style="padding: 8px 14px; font-size: 16px;">
                                                                ⭐
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    
                                                    <form action="<?= BASE_URL ?>admin/productimage/delete" method="POST" class="d-inline" 
                                                          onsubmit="return confirmDelete('<?= $image->anh_path ?>')">
                                                        <input type="hidden" name="image_id" value="<?= $image->anh_id ?>">
                                                        <input type="hidden" name="product_id" value="<?= $product->sanpham_id ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa ảnh" style="padding: 8px 14px; font-size: 16px;">
                                                            🗑️
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            
                                            <div class="image-info">
                                                <small class="text-muted">
                                                    <?= basename($image->anh_path) ?>
                                                    <?php if(isset($image->color_ten) && $image->color_ten): ?>
                                                        <br><span class="badge badge-info"><?= htmlspecialchars($image->color_ten) ?></span>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <div style="font-size: 72px; margin-bottom: 20px;">📷</div>
                                <h5 class="text-muted">Chưa có ảnh nào</h5>
                                <p class="text-muted">Hãy upload ảnh đầu tiên cho sản phẩm này</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal xem ảnh lớn -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xem ảnh</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" alt="" class="img-fluid" style="max-height: 70vh;">
            </div>
        </div>
    </div>
</div>

<style>
.image-card {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: white;
    cursor: pointer;
}

.image-card:hover {
    border-color: #007bff;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
    transform: translateY(-2px);
}

.image-card.primary {
    border-color: #ffc107;
    background: #fffbf0;
}

.image-card.primary:hover {
    border-color: #e0a800;
}

.image-preview {
    position: relative;
    height: 200px;
    overflow: hidden;
    cursor: pointer;
}

.image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.image-card:hover .image-preview img {
    transform: scale(1.05);
}

.hover-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.image-card:hover .hover-overlay {
    opacity: 1;
}

.hover-overlay i {
    color: white;
    font-size: 2rem;
}

.primary-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    background: #ffc107;
    color: #212529;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    z-index: 2;
}

.image-actions {
    position: absolute;
    bottom: 8px;
    right: 8px;
    display: flex;
    gap: 4px;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 3;
}

.image-card:hover .image-actions {
    opacity: 1;
}

.image-actions button {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.image-info {
    padding: 8px;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.upload-section .card {
    border: 2px dashed #dee2e6;
    background: #f8f9fa;
}

.upload-section .card:hover {
    border-color: #007bff;
    background: #f0f8ff;
}

#imagePreviewModal .modal-body {
    padding: 20px;
    background: #f8f9fa;
}
</style>

<script>
// Xem ảnh lớn
function showImagePreview(imageSrc, productName) {
    $('#previewImage').attr('src', imageSrc);
    $('#previewImage').attr('alt', productName);
    $('#imagePreviewModal').modal('show');
}

// Confirm đặt làm ảnh chính
function confirmSetPrimary(imageName) {
    return confirm('Đặt ảnh "' + imageName + '" làm ảnh chính?\n\nẢnh này sẽ được hiển thị đầu tiên ở trang sản phẩm.');
}

// Confirm xóa ảnh
function confirmDelete(imageName) {
    return confirm('Bạn có chắc chắn muốn xóa ảnh "' + imageName + '"?\n\nHành động này không thể hoàn tác.');
}

// Click image actions không trigger overlay click
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.image-actions button, .image-actions form').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
    
    // Handle "Thêm nhóm màu" button click - Clone upload group
    let groupIndex = 1; // Start from 1 since we already have group 0
    document.getElementById('btn-add-group').addEventListener('click', function() {
        const container = document.getElementById('upload-group-container');
        const firstGroup = container.querySelector('.upload-group');
        const newGroup = firstGroup.cloneNode(true);
        
        // Update all IDs and names with new index
        newGroup.setAttribute('data-index', groupIndex);
        
        // Update color select
        const colorSelect = newGroup.querySelector('select[name^="color_group"]');
        colorSelect.id = 'color_group_' + groupIndex;
        colorSelect.name = 'color_group[' + groupIndex + ']';
        colorSelect.selectedIndex = 0; // Reset to first option
        
        // Update primary checkbox
        const primaryCheckbox = newGroup.querySelector('input[type="checkbox"]');
        primaryCheckbox.id = 'is_primary_' + groupIndex;
        primaryCheckbox.name = 'is_primary[' + groupIndex + ']';
        primaryCheckbox.checked = false; // Uncheck
        const primaryLabel = newGroup.querySelector('label.form-check-label');
        primaryLabel.setAttribute('for', 'is_primary_' + groupIndex);
        
        // Update file input
        const fileInput = newGroup.querySelector('input[type="file"]');
        fileInput.id = 'product_image_group_' + groupIndex;
        fileInput.name = 'product_image_group[' + groupIndex + '][]';
        fileInput.value = ''; // Clear file selection
        const fileLabel = newGroup.querySelector('label[for^="product_image_group"]');
        fileLabel.setAttribute('for', 'product_image_group_' + groupIndex);
        
        // Add remove button for cloned groups
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-danger btn-sm mt-2';
        removeBtn.innerHTML = '<i class="fas fa-times"></i> Xóa nhóm này';
        removeBtn.addEventListener('click', function() {
            if (confirm('Xóa nhóm màu này?')) {
                newGroup.remove();
            }
        });
        newGroup.appendChild(removeBtn);
        
        // Add separator line
        const separator = document.createElement('hr');
        newGroup.insertBefore(separator, newGroup.firstChild);
        
        // Append to container
        container.appendChild(newGroup);
        groupIndex++;
        
        // Scroll to new group
        newGroup.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
});
</script>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>
