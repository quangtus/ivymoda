<!-- Danh sách đơn hàng - Admin -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
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
                                            <td><?= $order['order_id'] ?></td>
                                            <td><strong><?= $order['order_code'] ?></strong></td>
                                            <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                            <td><?= htmlspecialchars($order['customer_phone']) ?></td>
                                            <td><?= htmlspecialchars(substr($order['customer_address'], 0, 50)) ?>...</td>
                                            <td><?= number_format($order['order_total'], 0, ',', '.') ?>đ</td>
                                            <td>
                                                <?php
                                                $statusClass = '';
                                                $statusText = '';
                                                switch ($order['order_status']) {
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
                                                <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                            <td>
                                                <a href="<?= BASE_URL ?>admin/order/detail/<?= $order['order_id'] ?>" 
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
