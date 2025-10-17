<?php
// Load header
require_once ROOT_PATH . 'app/views/shared/admin/header.php';

// Load sidebar
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';
?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><?= $data['title'] ?></h3>
                </div>
                
                <div class="card-body">
                    <!-- Filter buttons -->
                    <div class="mb-3">
                        <a href="<?= BASE_URL ?>admin/order" class="btn btn-secondary">Tất cả</a>
                        <a href="<?= BASE_URL ?>admin/order/pending" class="btn btn-warning">Chờ xử lý</a>
                        <a href="<?= BASE_URL ?>admin/order/completed" class="btn btn-success">Hoàn thành</a>
                    </div>
                    
                    <!-- Orders table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Mã đơn hàng</th>
                                    <th>Khách hàng</th>
                                    <th>Điện thoại</th>
                                    <th>Địa chỉ</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày đặt</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['orders'])): ?>
                                    <?php foreach ($data['orders'] as $order): ?>
                                        <tr>
                                            <td><?= is_object($order) ? $order->order_id : $order['order_id'] ?></td>
                                            <td><strong><?= is_object($order) ? $order->order_code : $order['order_code'] ?></strong></td>
                                            <td><?= htmlspecialchars(is_object($order) ? $order->customer_name : $order['customer_name']) ?></td>
                                            <td><?= htmlspecialchars(is_object($order) ? $order->customer_phone : $order['customer_phone']) ?></td>
                                            <?php $addr = is_object($order) ? $order->customer_address : $order['customer_address']; ?>
                                            <td><?= htmlspecialchars(mb_strimwidth($addr, 0, 50, '...','UTF-8')) ?></td>
                                            <td><?= number_format(is_object($order) ? $order->order_total : $order['order_total'], 0, ',', '.') ?>đ</td>
                                            <td>
                                                <?php
                                                $statusClass = '';
                                                $statusText = '';
                                                $statusVal = (int)(is_object($order) ? $order->order_status : $order['order_status']);
                                                switch ($statusVal) {
                                                    case 0:
                                                        $statusClass = 'badge-warning';
                                                        $statusText = 'Chờ xử lý';
                                                        break;
                                                    case 1:
                                                        $statusClass = 'badge-info';
                                                        $statusText = 'Đang giao';
                                                        break;
                                                    case 2:
                                                        $statusClass = 'badge-success';
                                                        $statusText = 'Hoàn thành';
                                                        break;
                                                    case 3:
                                                        $statusClass = 'badge-danger';
                                                        $statusText = 'Đã hủy';
                                                        break;
                                                    default:
                                                        $statusClass = 'badge-secondary';
                                                        $statusText = 'Không xác định';
                                                }
                                                ?>
                                                <span class="badge <?= $statusClass ?> order-status"><?= $statusText ?></span>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime(is_object($order) ? $order->order_date : $order['order_date'])) ?></td>
                                            <td>
                                                <a href="<?= BASE_URL ?>admin/order/detail/<?= is_object($order) ? $order->order_id : $order['order_id'] ?>" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Xem
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center">Không có đơn hàng nào</td>
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

<?php
// Load footer
require_once ROOT_PATH . 'app/views/shared/admin/footer.php';
?>
