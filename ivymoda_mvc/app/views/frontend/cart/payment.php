<?php
require_once ROOT_PATH . 'app/views/shared/frontend/header.php'; 
?>
<section class = "payment-section">
    <div class = "payment-container">
        <div class="payment-top-wrap">
            <div class="payment-top">
                <div class="payment-top-item">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="payment-top-item">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="payment-top-item chosen">
                    <i class="fas fa-money-check-alt"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="payment-container">
        <div class="payment-content row">
            <div class="payment-content-left">
                <div class="payment-content-left-method-delivery">
                    <p style="font-weight: bold;">Phương thức giao hàng</p>
                    <div class="payment-content-left-method-delivery-item">
                        <input checked type="radio">
                        <label for="">Giao hàng chuyển phát nhanh</label>
                    </div>
                </div>
                <div class="payment-content-left-method-payment">
                    <p style="font-weight: bold;">Phương thức thanh toán</p>
                    <p>Mọi giao dịch đều được bảo mật và mã hóa. Thông tin thẻ tín dụng sẽ không bao giờ được lưu lại.</p>
                    <div class="payment-content-left-method-payment-item">
                        <input name="method-payment" type="radio" checked>
                        <label for="">Thanh toán bằng thẻ tín dụng (OnePay)</label>
                    </div>
                    <div class="payment-content-left-method-payment-item-img">
                        <img src="<?= BASE_URL ?>assets/images/visa.png" alt="">
                    </div>
                    <div class="payment-content-left-method-payment-item">
                        <input name="method-payment" type="radio">
                        <label for="">Thanh toán bằng thẻ ATM (OnePay)</label>
                    </div>
                    <div class="payment-content-left-method-payment-item-img">
                        <img src="<?= BASE_URL ?>assets/images/vcb.png" alt="">
                    </div>
                    <div class="payment-content-left-method-payment-item">
                        <input name="method-payment" type="radio">
                        <label for="">Thanh toán Momo</label>
                    </div>
                    <div class="payment-content-left-method-payment-item-img">
                        <img src="<?= BASE_URL ?>assets/images/momo.png" alt="">
                    </div>
                    <div class="payment-content-left-method-payment-item">
                        <input name="method-payment" type="radio">
                        <label for="">Thu tiền tận nơi</label>
                    </div>
                </div>
            </div>
            <div class="payment-content-right">
                <div class="payment-content-right-button">
                    <input type="text" placeholder="Mã giảm giá/Quà tặng">
                    <button><i class="fas fa-check"></i></button>
                </div>
                <div class="payment-content-right-button">
                    <input type="text" placeholder="Mã cộng tác viên">
                    <button><i class="fas fa-check"></i></button>
                </div>
                <div class="payment-content-right-mnv">
                    <select name="" id="">
                        <option value="">Chọn mã nhân viên thân thiết</option>
                        <option value="">A123</option>
                        <option value="">B123</option>
                        <option value="">C123</option>
                    </select>
                </div>
                <div class="payment-content-right-table">
                    <table>
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th>Giảm giá</th>
                            <th>SL</th>
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
                        <tr class = "tr-border-top">
                            <td colspan="3">Tổng tiền hàng</td>
                            <td style="font-weight: bold;"><p>690.000<sup>đ</sup></p></td>
                        </tr>
                        <tr class = "tr-border-top">
                            <td colspan="3">Tạm tính</td>
                            <td><p>69.000<sup>đ</sup></p></td>
                        </tr>
                        <tr class = "tr-border-top">
                            <td colspan="3">Giao hàng chuyển phát nhanh - Chuyển phát nhanh</td>
                            <td><p>759.000<sup>đ</sup></p></td>
                        </tr>
                        <tr class = "tr-border-top">
                            <td style="font-weight: bold;" colspan="3">Tiền thanh toán</td>
                            <td style="font-weight: bold;"><p>759.000<sup>đ</sup></p></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="payment-content-button">
            <button><p style="font-weight: bold;">TIẾP TỤC THANH TOÁN</p></button>
        </div>
    </div>
</section>

<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/payment.css">

<?php 
require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; 
?>