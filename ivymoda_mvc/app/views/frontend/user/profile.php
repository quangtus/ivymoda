<?php require_once ROOT_PATH . 'app/views/shared/frontend/header.php'; ?>

<section class = "user-profile-section">
    <div class="profile-container">
        <h1>Tài khoản của tôi</h1>
        
        <?php if(!empty($error)): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <?php if(!empty($success)): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
        </div>
        <?php endif; ?>
        
        <div class="profile-tabs">
            <div class="profile-tab active" data-tab="info">Thông tin cá nhân</div>
            <div class="profile-tab" data-tab="password">Đổi mật khẩu</div>
            <div class="profile-tab" data-tab="orders">Lịch sử mua hàng</div>
        </div>
        
        <div id="info" class="tab-content active">
            <form action="<?= BASE_URL ?>user/profile" method="post" class="profile-info-form">
                <input type="hidden" name="update_profile" value="1">
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" id="username" value="<?php echo isset($user_info->username) ? htmlspecialchars($user_info->username) : ''; ?>" readonly>
                    <small style="color: #6c757d; font-size: 12px;">Tên đăng nhập không thể thay đổi</small>
                </div>
                
                <div class="form-group">
                    <label for="fullname">Họ tên <span style="color: red;">*</span></label>
                    <input type="text" id="fullname" name="fullname" value="<?php echo isset($user_info->fullname) ? htmlspecialchars($user_info->fullname) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email <span style="color: red;">*</span></label>
                    <input type="email" id="email" name="email" value="<?php echo isset($user_info->email) ? htmlspecialchars($user_info->email) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Số điện thoại</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo isset($user_info->phone) ? htmlspecialchars($user_info->phone) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="address">Địa chỉ</label>
                    <textarea id="address" name="address" rows="3"><?php echo isset($user_info->address) ? htmlspecialchars($user_info->address) : ''; ?></textarea>
                </div>
                
                <button type="submit" class="btn-submit">Cập nhật thông tin</button>
            </form>
        </div>
        
        <div id="password" class="tab-content">
            <form action="<?= BASE_URL ?>user/profile" method="post">
                <input type="hidden" name="change_password" value="1">
                <div class="form-group">
                    <label for="current_password">Mật khẩu hiện tại <span style="color: red;">*</span></label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password">Mật khẩu mới <span style="color: red;">*</span></label>
                    <input type="password" id="new_password" name="new_password" required>
                    <small style="color: #6c757d; font-size: 12px;">Mật khẩu phải có ít nhất 6 ký tự</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Xác nhận mật khẩu mới <span style="color: red;">*</span></label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn-submit">Đổi mật khẩu</button>
            </form>
        </div>
        
        <div id="orders" class="tab-content">
            <h2>Lịch sử mua hàng</h2>
            <?php if(empty($orders)): ?>
                <p>Chưa có đơn hàng nào.</p>
            <?php else: ?>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Mã đơn hàng</th>
                            <th>Ngày đặt</th>
                            <th>Trạng thái</th>
                            <th>Tổng tiền</th>
                            <th>Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $order): ?>
                        <tr>
                            <td><?= is_object($order) ? $order->order_code : $order['order_code'] ?></td>
                            <td><?= date('d/m/Y', strtotime(is_object($order) ? $order->order_date : $order['order_date'])) ?></td>
                            <td>
                                <?php
                                $status = is_object($order) ? (int)$order->order_status : (int)$order['order_status'];
                                if ($status === 0) echo '<span class="status-pending">Chờ xử lý</span>';
                                elseif ($status === 1) echo '<span class="status-shipping">Đang giao</span>';
                                elseif ($status === 2) echo '<span class="status-completed">Hoàn thành</span>';
                                else echo '<span class="status-cancelled">Đã hủy</span>';
                                ?>
                            </td>
                            <td><?= number_format(is_object($order) ? $order->order_total : $order['order_total']) ?>đ</td>
                            <td>
                                <a href="<?= BASE_URL ?>user/orderDetail/<?= is_object($order) ? $order->order_id : $order['order_id'] ?>" class="btn-view">Xem</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Tab switching
                const tabs = document.querySelectorAll('.profile-tab');
                const tabContents = document.querySelectorAll('.tab-content');
                
                tabs.forEach(function(tab) {
                    tab.addEventListener('click', function() {
                        const tabId = this.getAttribute('data-tab');
                        
                        // Remove active class from all tabs and contents
                        tabs.forEach(t => {
                            t.classList.remove('active');
                        });
                        tabContents.forEach(c => {
                            c.classList.remove('active');
                            c.style.display = 'none';
                        });
                        
                        // Add active class to current tab and content
                        this.classList.add('active');
                        document.getElementById(tabId).classList.add('active');
                        document.getElementById(tabId).style.display = 'block';
                    });
                });
            });
        </script>
    </div>
</section>

<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/user-profile.css">

<?php require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; ?>