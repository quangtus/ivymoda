<?php
// Load header
require_once ROOT_PATH . 'app/views/shared/admin/header.php';

// Load sidebar
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';
?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chi tiết đơn hàng #<?= is_object($data['order']) ? $data['order']->order_code : $data['order']['order_code'] ?></h3>
                    <div class="card-tools">
                        <a href="<?= BASE_URL ?>admin/order" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <?php $order = $data['order']; ?>
                    
                    <!-- Thông tin đơn hàng -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Thông tin khách hàng</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Họ tên:</th>
                                    <td><?= htmlspecialchars(is_object($order) ? $order->customer_name : $order['customer_name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Điện thoại:</th>
                                    <td><?= htmlspecialchars(is_object($order) ? $order->customer_phone : $order['customer_phone']) ?></td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td><?= htmlspecialchars(is_object($order) ? ($order->customer_email ?? 'N/A') : ($order['customer_email'] ?? 'N/A')) ?></td>
                                </tr>
                                <tr>
                                    <th>Địa chỉ giao hàng:</th>
                                    <td><?= htmlspecialchars(is_object($order) ? $order->customer_address : $order['customer_address']) ?></td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Thông tin đơn hàng</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Mã đơn hàng:</th>
                                    <td><strong><?= is_object($order) ? $order->order_code : $order['order_code'] ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Ngày đặt:</th>
                                    <td><?= date('d/m/Y H:i', strtotime(is_object($order) ? $order->order_date : $order['order_date'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Thanh toán:</th>
                                    <td><?= htmlspecialchars(is_object($order) ? $order->payment_method : $order['payment_method']) ?></td>
                                </tr>
                                <tr>
                                    <th>Vận chuyển:</th>
                                    <td><?= htmlspecialchars(is_object($order) ? $order->shipping_method : $order['shipping_method']) ?></td>
                                </tr>
                                <tr>
                                    <th>Ghi chú:</th>
                                    <td><?= htmlspecialchars(is_object($order) ? ($order->order_note ?? 'Không có') : ($order['order_note'] ?? 'Không có')) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Trạng thái đơn hàng -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>Cập nhật trạng thái</h5>
                            <form action="<?= BASE_URL ?>admin/order/updateStatus/<?= is_object($order) ? $order->order_id : $order['order_id'] ?>" method="POST">
                                <div class="form-group">
                                    <?php $st = (int)(is_object($order) ? $order->order_status : $order['order_status']); ?>
                                    <select name="status" class="form-control" style="max-width: 300px;">
                                        <option value="0" <?= $st == 0 ? 'selected' : '' ?>>Chờ xử lý</option>
                                        <option value="1" <?= $st == 1 ? 'selected' : '' ?>>Đang giao hàng</option>
                                        <option value="2" <?= $st == 2 ? 'selected' : '' ?>>Hoàn thành</option>
                                        <option value="3" <?= $st == 3 ? 'selected' : '' ?>>Đã hủy</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Cập nhật trạng thái</button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Chi tiết sản phẩm -->
                    <div class="row">
                        <div class="col-12">
                            <h5>Chi tiết sản phẩm</h5>
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Hình ảnh</th>
                                        <th>Size</th>
                                        <th>Màu</th>
                                        <th>Đơn giá</th>
                                        <th>Số lượng</th>
                                        <th>Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $subtotal = 0;
                                    foreach ($data['orderItems'] as $item): 
                                        $price = is_object($item) ? $item->sanpham_gia : $item['sanpham_gia'];
                                        $qty = is_object($item) ? $item->sanpham_soluong : $item['sanpham_soluong'];
                                        $name = is_object($item) ? $item->sanpham_ten : $item['sanpham_ten'];
                                        $img = is_object($item) ? ($item->sanpham_anh ?? '') : ($item['sanpham_anh'] ?? '');
                                        $size = is_object($item) ? ($item->sanpham_size ?? 'N/A') : ($item['sanpham_size'] ?? 'N/A');
                                        $color = is_object($item) ? ($item->sanpham_color ?? 'N/A') : ($item['sanpham_color'] ?? 'N/A');
                                        $itemTotal = $price * $qty;
                                        $subtotal += $itemTotal;
                                    ?>
                                        <tr>
                                            <td><?= htmlspecialchars($name) ?></td>
                                            <td>
                                                <?php if (!empty($img)): ?>
                                                    <img src="<?= BASE_URL ?>assets/uploads/<?= $img ?>" 
                                                         alt="<?= htmlspecialchars($name) ?>" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($size) ?></td>
                                            <td><?= htmlspecialchars($color) ?></td>
                                            <td><?= number_format($price, 0, ',', '.') ?>đ</td>
                                            <td><?= $qty ?></td>
                                            <td><?= number_format($itemTotal, 0, ',', '.') ?>đ</td>
                                        </tr>
                                    <?php endforeach; ?>
                                    
                                    <tr class="font-weight-bold">
                                        <td colspan="6" class="text-right">Tổng cộng:</td>
                                        <td><?= number_format(is_object($order) ? $order->order_total : $order['order_total'], 0, ',', '.') ?>đ</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
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
