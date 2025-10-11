<?php
require_once ROOT_PATH . 'app/views/shared/frontend/header.php'; 
?>
<section class = "cart-section">
    <div class = "cart-container">
        <div class="cart-top-wrap">
            <div class="cart-top">
                <div class="cart-top-item chosen">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="cart-top-item">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="cart-top-item">
                    <i class="fas fa-money-check-alt"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="cart-container">
        <div class = "cart-content row">
            <div class="cart-content-left">
                <table>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Tên sản phẩm</th>
                        <th>Màu</th>
                        <th>Size</th>
                        <th>SL</th>
                        <th>Thành tiền</th>
                        <th>Xóa</th>
                    </tr>
                    <tr>
                        <td><img src="<?= BASE_URL ?>assets/images/sp1.1.jpg" alt=""></td>
                        <td><p>Quần sooc bò đen MS 123456</p></td>
                        <td><img src="<?= BASE_URL ?>assets/images/spcolor.png" alt=""></td>
                        <td><p>L</p></td>
                        <td><input type = "number" value="1" min = "1"></td>
                        <td><p>490.000 <sup>đ</sup></p></td>
                        <td><span>X</span></td>
                    </tr>
                    <tr>
                        <td><img src="<?= BASE_URL ?>assets/images/sp1.2.jpg" alt=""></td>
                        <td><p>Quần sooc bò đen MS 123456</p></td>
                        <td><img src="<?= BASE_URL ?>assets/images/spcolor.png" alt=""></td>
                        <td><p>L</p></td>
                        <td><input type = "number" value="1" min = "1"></td>
                        <td><p>490.000 <sup>đ</sup></p></td>
                        <td><span>X</span></td>
                    </tr>
                    <tr>
                        <td><img src="<?= BASE_URL ?>assets/images/sp1.3.jpg" alt=""></td>
                        <td><p>Quần sooc bò đen MS 123456</p></td>
                        <td><img src="<?= BASE_URL ?>assets/images/spcolor.png" alt=""></td>
                        <td><p>L</p></td>
                        <td><input type = "number" value="1" min = "1"></td>
                        <td><p>490.000 <sup>đ</sup></p></td>
                        <td><span>X</span></td>
                    </tr>
                    <tr>
                        <td><img src="<?= BASE_URL ?>assets/images/sp1.4.jpg" alt=""></td>
                        <td><p>Quần sooc bò đen MS 123456</p></td>
                        <td><img src="<?= BASE_URL ?>assets/images/spcolor.png" alt=""></td>
                        <td><p>L</p></td>
                        <td><input type = "number" value="1" min = "1"></td>
                        <td><p>490.000 <sup>đ</sup></p></td>
                        <td><span>X</span></td>
                    </tr>
                </table>
            </div>
            <div class="cart-content-right">
                <table>
                    <tr>
                        <th colspan="2">TỔNG TIỀN GIỎ HÀNG</th>
                    </tr>
                    <tr>
                        <td>TỔNG SẢN PHẨM</td>
                        <td>2</td>
                    </tr>
                    <tr>
                        <td>TỔNG TIỀN HÀNG</td>
                        <td><p>490.000 <sup>đ</sup></p></td>
                    </tr>
                    <tr>
                        <td>THÀNH TIỀN</td>
                        <td><p>490.000 <sup>đ</sup></p></td>
                    </tr>
                    <tr>
                        <td>TẠM TÍNH</td>
                        <td><p style="color: black; font-weight: bold;">490.000 <sup>đ</sup></p></td>
                    </tr>
                </table>
                <div class="cart-content-right-text">
                    <p>Phí vận chuyển và thuế sẽ được tính khi thanh toán</p>
                    <p style="color: red;">Mua thêm <span style="font-weight: bold; font-size: 18px;">110.000<sup>đ</sup></span> để được miễn phí SHIP</p>
                </div>
                <div class="cart-content-right-button">
                    <button>TIẾP TỤC MUA SẮM</button>
                    <button id="btnCheckout">THANH TOÁN</button>
                </div>
                <div class="cart-content-right-login">
                    <p>TÀI KHOẢN IVY</p>
                    <p>Hãy <a href ="">Đăng nhập</a> để nhận nhiều ưu đãi hấp dẫn</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('btnCheckout').addEventListener('click', function(){
    window.location.href = '<?= BASE_URL ?>delivery/index.php';
});
</script>

<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/cart-index.css">

<?php 
require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; 
?>