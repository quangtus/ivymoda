<?php
require_once ROOT_PATH . 'app/views/shared/admin/header.php';
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';
?>

<div class="admin-content-right-main">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Chương trình khuyến mãi</h3>
            <a class="btn btn-primary" href="<?= BASE_URL ?>admin/promotion/add">Thêm mới</a>
        </div>

        <table class="table table-striped table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Ảnh</th>
                    <th>Thời gian</th>
                    <th>Trạng thái</th>
                    <th>Ưu tiên</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($promotions)): foreach($promotions as $p): ?>
                <tr>
                    <td><?= $p->promotion_id ?></td>
                    <td><?= htmlspecialchars($p->title) ?></td>
                    <td>
                        <?php if(!empty($p->image_url)): ?>
                            <img src="<?= BASE_URL ?>assets/uploads/<?= $p->image_url ?>" alt="" style="height:50px">
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= date('d/m/Y H:i', strtotime($p->start_date)) ?> -
                        <?= date('d/m/Y H:i', strtotime($p->end_date)) ?>
                    </td>
                    <td><?= $p->is_active ? 'Đang hiển thị' : 'Ẩn' ?></td>
                    <td><?= (int)$p->priority ?></td>
                    <td>
                        <a class="btn btn-sm btn-info" href="<?= BASE_URL ?>admin/promotion/edit/<?= $p->promotion_id ?>">Sửa</a>
                        <a class="btn btn-sm btn-danger" href="<?= BASE_URL ?>admin/promotion/delete/<?= $p->promotion_id ?>" onclick="return confirm('Xóa khuyến mãi này?')">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="7" class="text-center">Chưa có khuyến mãi</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>


