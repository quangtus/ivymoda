<?php
require_once ROOT_PATH . 'app/views/shared/frontend/header.php'; 
?>
<section class = "delivery-section">
    <div class = "delivery-container">
        <div class="delivery-top-wrap">
            <div class="delivery-top">
                <div class="delivery-top-item">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="delivery-top-item chosen">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="delivery-top-item">
                    <i class="fas fa-money-check-alt"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="delivery-container">
        <div class="delivery-content row">
            <div class="delivery-content-left">
                <p>Vui lòng chọn địa chỉ giao hàng</p>
                <div class="delivery-content-left-login row">
                    <i class = "fas fa-sign-in-alt"></i>
                    <p>Đăng nhập (Nếu bạn đã có tài khoản của IVY)</p>
                </div>
                <div class="delivery-content-left-retail-customer row">
                    <input checked name="typeOfCustomer" type = "radio">
                    <p> <span style="font-weight: bold;">Khách lẻ</span> (Nếu bạn không muốn lưu lại thông in)</p>
                </div>
                <div class="delivery-content-left-sign-in row">
                    <input name="typeOfCustomer" type = "radio">
                    <p> <span style="font-weight: bold;">Đăng ký</span> (Tạo mới tài khoản với thông tin bên dưới)</p>
                </div>
                <div class="delivery-content-left-input-top row">
                    <div class="delivery-content-left-input-top-item">
                        <label>Họ tên <span style="color: red;">*</span></label>
                        <input type="text" placeholder="Nguyễn Văn A">
                    </div>
                    <div class="delivery-content-left-input-top-item">
                        <label>Điện thoại <span style="color: red;">*</span></label>
                        <input type="text" placeholder="0123456789">
                    </div>
                    <div class="delivery-content-left-input-top-item">
                        <label>Tỉnh/TP <span style="color: red;">*</span></label>
                        <input type="text" placeholder="Hà Nội">
                    </div>
                    <div class="delivery-content-left-input-top-item">
                        <label>Quận/Huyện <span style="color: red;">*</span></label>
                        <input type="text" placeholder="Cầu Giấy">
                    </div>
                </div>
                <div class="delivery-content-left-input-bottom">
                    <label>Địa chỉ <span style="color: red;">*</span></label>
                    <input type="text" placeholder="Cầu Giấy - Hà Nội">
                </div>
                <div class="delivery-content-left-button row">
                    <a href=""><span>&#171;</span><p>Quay lại giỏ hàng</p></a>
                    <button><p style="font-weight: bold;">THANH TOÁN VÀ GIAO HÀNG</p></button>
                </div>
            </div>
            <div class="delivery-content-right">
                <table>
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Giảm giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                    </tr>
                    <tr>
                        <td>Áp polo kẻ ngang MS 123456</td>
                        <td>-30%</td>
                        <td>1</td>
                        <td>500.000<sup>đ</sup></td>
                    </tr>
                    <tr>
                        <td>Áp Nam kẻ ngang MS 123456</td>
                        <td>-20%</td>
                        <td>1</td>
                        <td><p>690.000<sup>đ</sup></p></td>
                    </tr>
                    <tr class="tr-border-top">
                        <td style="font-weight: bold;" colspan="3">Tổng</td>
                        <td style="font-weight: bold;"><p>690.000<sup>đ</sup></p></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;" colspan="3">Thuế VAT</td>
                        <td style="font-weight: bold;"><p>69.000<sup>đ</sup></p></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;" colspan="3">Tổng tiền hàng</td>
                        <td style="font-weight: bold;"><p>759.000<sup>đ</sup></p></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</section>

<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/delivery.css">

<?php 
require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; 
?>