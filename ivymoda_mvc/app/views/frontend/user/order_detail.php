<?php require_once ROOT_PATH . 'app/views/shared/frontend/header.php'; ?>
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/frontend-components.css">

<section class="order-detail-section">
    <div class="container order-detail-container">
        <div class="order-detail-header">
            <h1 class="order-code">Đơn hàng #<?= htmlspecialchars(is_object($order) ? $order->order_code : $order['order_code']) ?></h1>
        </div>

        <div class="order-summary">
            <div class="summary-row"><span class="label">Ngày đặt:</span><span class="value"><?= date('d/m/Y H:i', strtotime(is_object($order) ? $order->order_date : $order['order_date'])) ?></span></div>
            <div class="summary-row"><span class="label">Trạng thái:</span><span class="value">
                <?php
                $status = (int)(is_object($order) ? $order->order_status : $order['order_status']);
                if ($status === 0) echo 'Chờ xử lý';
                elseif ($status === 1) echo 'Đang giao';
                elseif ($status === 2) echo 'Hoàn thành';
                else echo 'Đã hủy';
                ?>
            </span></div>
            <div class="summary-row"><span class="label">Tổng tiền:</span><span class="value total"><?= number_format(is_object($order) ? $order->order_total : $order['order_total']) ?>đ</span></div>
            <div class="summary-row"><span class="label">Phương thức thanh toán:</span><span class="value text-uppercase"><?= strtoupper(is_object($order) ? $order->payment_method : $order['payment_method']) ?></span></div>
            <div class="summary-row"><span class="label">Địa chỉ giao hàng:</span><span class="value address"><?= nl2br(htmlspecialchars(is_object($order) ? $order->customer_address : $order['customer_address'])) ?></span></div>
        </div>

        <?php if (isset($_GET['success']) && $_GET['success'] === 'cancelled'): ?>
            <div class="alert alert-success">Đã hủy đơn hàng thành công.</div>
        <?php elseif (isset($_GET['error']) && $_GET['error'] === 'cannot_cancel'): ?>
            <div class="alert alert-danger">Không thể hủy đơn hàng ở trạng thái hiện tại.</div>
        <?php endif; ?>

        <?php if ((int)(is_object($order) ? $order->order_status : $order['order_status']) === 0): ?>
            <form action="<?= BASE_URL ?>user/cancelOrder/<?= (int)(is_object($order) ? $order->order_id : $order['order_id']) ?>" method="post" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">
                <button type="submit" class="btn btn-danger">Hủy đơn hàng</button>
            </form>
            <hr>
        <?php endif; ?>

        <h2 class="section-title">Chi tiết sản phẩm</h2>
        <div class="table-responsive order-items-wrapper">
            <table class="table order-items-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Màu</th>
                        <th>Size</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderItems as $item): ?>
                        <tr>
                            <td>
                                <?php 
                                $itemImg = is_object($item) ? ($item->sanpham_anh ?? '') : ($item['sanpham_anh'] ?? '');
                                if (!empty($itemImg)): ?>
                                    <img class="item-thumb" src="<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars($itemImg) ?>" alt="">
                                <?php endif; ?>
                                <?= htmlspecialchars(is_object($item) ? $item->sanpham_ten : $item['sanpham_ten']) ?>
                            </td>
                            <td><?= htmlspecialchars(is_object($item) ? $item->sanpham_color : $item['sanpham_color']) ?></td>
                            <td><?= htmlspecialchars(is_object($item) ? $item->sanpham_size : $item['sanpham_size']) ?></td>
                            <td><?= number_format(is_object($item) ? $item->sanpham_gia : $item['sanpham_gia']) ?>đ</td>
                            <td><?= (int)(is_object($item) ? $item->sanpham_soluong : $item['sanpham_soluong']) ?></td>
                            <?php 
                                $gia = is_object($item) ? $item->sanpham_gia : $item['sanpham_gia'];
                                $sl = (int)(is_object($item) ? $item->sanpham_soluong : $item['sanpham_soluong']);
                            ?>
                            <td><?= number_format($gia * $sl) ?>đ</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="order-actions">
            <a href="<?= BASE_URL ?>user/profile" class="btn btn-secondary back-link">Quay lại lịch sử mua hàng</a>
        </div>
    </div>
</section>

<?php require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; ?>


