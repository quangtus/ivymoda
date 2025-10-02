//------------------------------------Menu-item-------------------
const toP = document.querySelector(".top")
window.addEventListener("scroll", function() {
        const X = this.pageYOffset;
        if (X > 1) { toP.classList.add("active") } else {
            toP.classList.remove("active")
        }
    })
    //------------------------------------Menu-SLIDEBAR-CARTEGORY-------------------
const itemSlidebar = document.querySelectorAll(".cartegory-left-li")
itemSlidebar.forEach(function(menu, index) {
        menu.addEventListener("click", function() {
            menu.classList.toggle("block")
        })
    })
    //------------------------------------PRODUCT-------------------
const bigImg = document.querySelector(".product-content-left-big-img img")
const smallImg = document.querySelectorAll(".product-content-left-small-img img")

smallImg.forEach(function(imgItem, X) {
    imgItem.addEventListener("click", function() {
        console.log(imgItem)
        bigImg.src = imgItem.src
    })
})

const baoQuan = document.querySelector(".baoquan")
const chiTiet = document.querySelector(".chitiet")
if (baoQuan) {
    baoQuan.addEventListener("click", function() {
        document.querySelector(".product-content-right-bottom-content-chitiet").style.display = "none"
        document.querySelector(".product-content-right-bottom-content-baoquan").style.display = "block"
    })
}
if (chiTiet) {
    chiTiet.addEventListener("click", function() {
        document.querySelector(".product-content-right-bottom-content-chitiet").style.display = "block"
        document.querySelector(".product-content-right-bottom-content-baoquan").style.display = "none"
    })
}


const buTton = document.querySelector(".product-content-right-bottom-top")
if (buTton) {
    buTton.addEventListener("click", function() {
        document.querySelector(".product-content-right-bottom-content-big").classList.toggle("activeB")
    })
}

//------------------------------------DELIVERTY-------------------
// const checkEd2 = document.querySelector(".delivery-content-left-khachle input")
// const checkEd1 = document.querySelector(".delivery-content-left-dangky input")
// checkEd1.addEventListener("click", function(){
//     document.querySelector(".delivery-content-left-input-password").style.display = "block"  
//    })
// checkEd2.addEventListener("click", function(){
//     document.querySelector(".delivery-content-left-input-password").style.display = "none"  
//    })

//----------------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý dropdown menu
    const dropdowns = document.querySelectorAll('.dropdown');

    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');

        // Nếu đang ở mobile, sử dụng click thay vì hover
        if (window.innerWidth < 768) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Đóng tất cả các menu dropdown khác
                dropdowns.forEach(d => {
                    if (d !== dropdown) {
                        d.querySelector('.dropdown-menu').style.display = 'none';
                    }
                });

                // Toggle menu hiện tại
                if (menu.style.display === 'block') {
                    menu.style.display = 'none';
                } else {
                    menu.style.display = 'block';
                }
            });
        }
    });

    // Đóng tất cả dropdown khi click bên ngoài
    document.addEventListener('click', function() {
        if (window.innerWidth < 768) {
            dropdowns.forEach(dropdown => {
                dropdown.querySelector('.dropdown-menu').style.display = 'none';
            });
        }
    });

    // Xử lý menu mobile
    const mobileMenuToggle = document.createElement('button');
    mobileMenuToggle.classList.add('mobile-menu-toggle');
    mobileMenuToggle.innerHTML = '<i class="fas fa-bars"></i>';

    const headerTop = document.querySelector('.header-top');
    const mainMenu = document.querySelector('.main-menu');

    if (headerTop && mainMenu) {
        headerTop.insertBefore(mobileMenuToggle, mainMenu);

        mobileMenuToggle.addEventListener('click', function() {
            if (mainMenu.style.display === 'block') {
                mainMenu.style.display = 'none';
            } else {
                mainMenu.style.display = 'block';
            }
        });

        // Ẩn menu mobile mặc định nếu ở viewport nhỏ
        if (window.innerWidth < 768) {
            mainMenu.style.display = 'none';
        }
    }

    // Xử lý thêm vào giỏ hàng
    const addToCartButtons = document.querySelectorAll('.add-to-cart');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const productId = this.getAttribute('data-id');

            // Gọi Ajax để thêm vào giỏ hàng
            fetch(BASE_URL + 'cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'product_id=' + productId + '&quantity=1'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Đã thêm sản phẩm vào giỏ hàng!');
                        // Cập nhật số lượng sản phẩm trong giỏ hàng (nếu có hiển thị)
                        if (document.querySelector('.cart-count')) {
                            document.querySelector('.cart-count').textContent = data.cart_count;
                        }
                    } else {
                        alert(data.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi thêm vào giỏ hàng.');
                });
        });
    });
});