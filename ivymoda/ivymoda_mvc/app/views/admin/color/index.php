<?php
require_once ROOT_PATH . 'app/views/shared/admin/header.php';
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';
?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Quản lý màu sắc</h1>
                <a href="<?= BASE_URL ?>admin/color/add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Thêm màu</a>
            </div>

            <?php if(!empty($success)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if(!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:80px">ID</th>
                                    <th>Tên màu</th>
                                    <th style="width:140px">Mã màu</th>
                                    <th style="width:120px">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($colors)): ?>
                                    <?php foreach($colors as $color): ?>
                                        <tr>
                                            <td><?= (int)$color->color_id ?></td>
                                            <td><?= htmlspecialchars($color->color_ten) ?></td>
                                            <td>
                                                <?php $hex = isset($color->color_ma) ? trim($color->color_ma) : ''; ?>
                                                <?php if($hex && preg_match('/^#?[A-Fa-f0-9]{6}$/', $hex)): ?>
                                                    <?php $hexVal = strpos($hex, '#') === 0 ? $hex : ('#' . $hex); ?>
                                                    <div style="width:36px;height:24px;border:1px solid #ccc;border-radius:4px;background: <?= htmlspecialchars($hexVal) ?>;"></div>
                                                    <small class="text-muted d-block mt-1"><?= htmlspecialchars(strtoupper($hexVal)) ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">(chưa có mã màu)</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= BASE_URL ?>admin/color/edit/<?= $color->color_id ?>" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> Sửa
                                                </a>
                                                <a href="<?= BASE_URL ?>admin/color/delete/<?= $color->color_id ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Bạn có chắc muốn xóa màu này?')">
                                                    <i class="fas fa-trash"></i> Xóa
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Chưa có màu nào</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>


