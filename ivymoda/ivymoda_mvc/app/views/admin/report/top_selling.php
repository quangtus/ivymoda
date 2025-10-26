<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\admin\report\top_selling.php

require_once ROOT_PATH . 'app/views/shared/admin/header.php';
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';
?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Sản phẩm bán chạy</h1>
            </div>

            <form method="get" action="" class="report-filter-form">
                <input type="hidden" name="url" value="admin/report/topSelling" />
                <div>
                    <label>Kiểu</label>
                    <select name="type">
                        <option value="day" <?= $type==='day'?'selected':'' ?>>Ngày</option>
                        <option value="month" <?= $type==='month'?'selected':'' ?>>Tháng</option>
                        <option value="year" <?= $type==='year'?'selected':'' ?>>Năm</option>
                    </select>
                </div>
                <div <?= $type==='day'?'':'style=\"display:none\"' ?>>
                    <label>Từ ngày</label>
                    <input type="date" name="from" value="<?= htmlspecialchars($from) ?>" />
                </div>
                <div <?= $type==='day'?'':'style=\"display:none\"' ?>>
                    <label>Đến ngày</label>
                    <input type="date" name="to" value="<?= htmlspecialchars($to) ?>" />
                </div>
                <div <?= $type!=='day'?'':'style=\"display:none\"' ?>>
                    <label>Năm</label>
                    <input type="number" name="year" min="2000" max="2100" value="<?= (int)$year ?>" />
                </div>
                <div <?= $type==='month'?'':'style=\"display:none\"' ?>>
                    <label>Tháng</label>
                    <input type="number" name="month" min="1" max="12" value="<?= (int)$month ?>" />
                </div>
                <div>
                    <label>Giới hạn</label>
                    <input type="number" name="limit" min="1" max="100" value="<?= (int)$limit ?>" />
                </div>
                <div>
                    <button type="submit">Lọc</button>
                </div>
            </form>

            <div class="card shadow-sm report-table">
                <table border="1" cellpadding="8" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Sản phẩm</th>
                            <th>Ảnh</th>
                            <th>Giá</th>
                            <th>Số lượng bán</th>
                            <th>Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($products)) { $i=1; foreach ($products as $p) { 
                            $title = is_object($p) ? $p->sanpham_tieude : $p['sanpham_tieude'];
                            $img = is_object($p) ? $p->sanpham_anh : $p['sanpham_anh'];
                            $price = (float)(is_object($p) ? $p->sanpham_gia : $p['sanpham_gia']);
                            $sold = (int)(is_object($p) ? $p->total_sold : $p['total_sold']);
                            $revenue = (float)(is_object($p) ? $p->total_revenue : $p['total_revenue']);
                        ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= htmlspecialchars($title) ?></td>
                                <td>
                                    <?php if (!empty($img)) { ?>
                                        <img src="<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars($img) ?>" alt="" style="width:60px;height:60px;object-fit:cover" />
                                    <?php } ?>
                                </td>
                                <td><?= number_format($price) ?> ₫</td>
                                <td><?= $sold ?></td>
                                <td><?= number_format($revenue) ?> ₫</td>
                            </tr>
                        <?php } } else { ?>
                            <tr><td colspan="6">Không có dữ liệu</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>


