<?php require_once ROOT_PATH . 'app/views/shared/frontend/header.php'; ?>

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
        <div class="profile-tab" data-tab="orders">Đơn hàng của tôi</div>
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
        <h2>Đơn hàng của tôi</h2>
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
                        <td><?= $order->order_code ?></td>
                        <td><?= date('d/m/Y', strtotime($order->created_at)) ?></td>
                        <td>
                            <?php
                            switch($order->status) {
                                case 0: echo '<span class="status-pending">Chờ xử lý</span>'; break;
                                case 1: echo '<span class="status-completed">Hoàn thành</span>'; break;
                                case 2: echo '<span class="status-cancelled">Đã hủy</span>'; break;
                            }
                            ?>
                        </td>
                        <td><?= number_format($order->total_amount) ?>đ</td>
                        <td><a href="<?= BASE_URL ?>user/orderDetail/<?= $order->id ?>" class="btn-view">Xem</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <style>
        /* Style cho trang profile với màu xanh nước biển nhạt */
        .profile-container {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            border: 1px solid #e1f0ff;
        }
        
        .profile-container h1 {
            color: #1971c2;
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
        }
        
        .profile-tabs {
            display: flex;
            border-bottom: 1px solid #a5d8ff;
            margin-bottom: 20px;
        }
        
        .profile-tab {
            padding: 12px 24px;
            cursor: pointer;
            margin-right: 5px;
            border-radius: 5px 5px 0 0;
            color: #333;
            background-color: #f8f9fa;
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        
        .profile-tab:hover {
            background-color: #e6f4ff;
            color: #1971c2;
        }
        
        .profile-tab.active {
            background-color: #4dabf7;
            color: white;
            border-color: #4dabf7;
            font-weight: bold;
        }
        
        .tab-content {
            display: none;
            padding: 20px;
            background-color: #fff;
            border-radius: 0 0 5px 5px;
            border: 1px solid #e1f0ff;
            border-top: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #444;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #c5e3fa;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #4dabf7;
            box-shadow: 0 0 0 3px rgba(77, 171, 247, 0.2);
        }
        
        .form-group input[readonly] {
            background-color: #f1f8ff;
            cursor: not-allowed;
        }
        
        .btn-submit {
            background-color: #4dabf7;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        .btn-submit:hover {
            background-color: #1971c2;
        }
        
        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background-color: #fff1f0;
            border: 1px solid #ffccc7;
            color: #cf1322;
        }
        
        .alert-success {
            background-color: #f6ffed;
            border: 1px solid #b7eb8f;
            color: #52c41a;
        }
        
        /* Style cho bảng đơn hàng */
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .orders-table th,
        .orders-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e1f0ff;
        }
        
        .orders-table th {
            background-color: #f1f8ff;
            color: #1971c2;
            font-weight: bold;
        }
        
        .btn-view {
            display: inline-block;
            padding: 5px 10px;
            background-color: #4dabf7;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
        }
        
        .btn-view:hover {
            background-color: #1971c2;
            color: white;
        }
        
        .status-pending {
            color: #fa8c16;
        }
        
        .status-completed {
            color: #52c41a;
        }
        
        .status-cancelled {
            color: #f5222d;
        }
    </style>
    
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

<?php require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; ?>