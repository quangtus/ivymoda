<!-- Chi tiết đơn hàng - Admin -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chi tiết đơn hàng #<?= $data['order']['order_code'] ?></h3>
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
                                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Điện thoại:</th>
                                    <td><?= htmlspecialchars($order['customer_phone']) ?></td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td><?= htmlspecialchars($order['customer_email'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Địa chỉ giao hàng:</th>
                                    <td><?= htmlspecialchars($order['customer_address']) ?></td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Thông tin đơn hàng</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Mã đơn hàng:</th>
                                    <td><strong><?= $order['order_code'] ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Ngày đặt:</th>
                                    <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Thanh toán:</th>
                                    <td><?= htmlspecialchars($order['payment_method']) ?></td>
                                </tr>
                                <tr>
                                    <th>Vận chuyển:</th>
                                    <td><?= htmlspecialchars($order['shipping_method']) ?></td>
                                </tr>
                                <tr>
                                    <th>Ghi chú:</th>
                                    <td><?= htmlspecialchars($order['order_note'] ?? 'Không có') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Trạng thái đơn hàng -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>Cập nhật trạng thái</h5>
                            <form action="<?= BASE_URL ?>admin/order/updateStatus/<?= $order['order_id'] ?>" method="POST">
                                <div class="form-group">
                                    <select name="status" class="form-control" style="max-width: 300px;">
                                        <option value="0" <?= $order['order_status'] == 0 ? 'selected' : '' ?>>Chờ xử lý</option>
                                        <option value="1" <?= $order['order_status'] == 1 ? 'selected' : '' ?>>Đang giao hàng</option>
                                        <option value="2" <?= $order['order_status'] == 2 ? 'selected' : '' ?>>Hoàn thành</option>
                                        <option value="3" <?= $order['order_status'] == 3 ? 'selected' : '' ?>>Đã hủy</option>
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
                                        $itemTotal = $item['sanpham_gia'] * $item['sanpham_soluong'];
                                        $subtotal += $itemTotal;
                                    ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['sanpham_ten']) ?></td>
                                            <td>
                                                <?php if (!empty($item['sanpham_anh'])): ?>
                                                    <img src="<?= BASE_URL ?>public/assets/uploads/<?= $item['sanpham_anh'] ?>" 
                                                         alt="<?= htmlspecialchars($item['sanpham_ten']) ?>" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($item['sanpham_size'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($item['sanpham_color'] ?? 'N/A') ?></td>
                                            <td><?= number_format($item['sanpham_gia'], 0, ',', '.') ?>đ</td>
                                            <td><?= $item['sanpham_soluong'] ?></td>
                                            <td><?= number_format($itemTotal, 0, ',', '.') ?>đ</td>
                                        </tr>
                                    <?php endforeach; ?>
                                    
                                    <tr class="font-weight-bold">
                                        <td colspan="6" class="text-right">Tổng cộng:</td>
                                        <td><?= number_format($order['order_total'], 0, ',', '.') ?>đ</td>
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
