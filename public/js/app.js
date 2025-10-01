// State management
let currentCategory = 'all';
let products = [];
let userProfile = {};
let cart = [];
let selectedSize = null;

// Initialize app
document.addEventListener('DOMContentLoaded', async () => {
    await loadProducts();
    await loadUserProfile();
    await loadCart();
    setupEventListeners();
    setupChatbot();
});

// Setup event listeners
function setupEventListeners() {
    // Navigation
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const section = e.target.dataset.section;
            switchSection(section);
        });
    });

    // Category filters
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            e.target.classList.add('active');
            const category = e.target.dataset.category;
            filterProducts(category);
        });
    });

    // Profile form
    document.getElementById('saveProfile').addEventListener('click', saveProfile);

    // Modal
    const modal = document.getElementById('productModal');
    document.querySelector('.close').addEventListener('click', () => {
        modal.style.display = 'none';
    });
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
}

// Section switching
function switchSection(section) {
    document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    
    document.getElementById(`${section}Section`).classList.add('active');
    document.querySelector(`[data-section="${section}"]`).classList.add('active');

    if (section === 'profile') {
        displayCurrentProfile();
    } else if (section === 'cart') {
        displayCart();
    }
}

// Load products
async function loadProducts() {
    try {
        const response = await fetch('/api/products');
        products = await response.json();
        displayProducts(products);
    } catch (error) {
        console.error('Error loading products:', error);
    }
}

// Filter products
async function filterProducts(category) {
    currentCategory = category;
    if (category === 'all') {
        displayProducts(products);
    } else {
        const filtered = products.filter(p => p.category === category);
        displayProducts(filtered);
    }
}

// Display products
function displayProducts(productsToDisplay) {
    const grid = document.getElementById('productGrid');
    grid.innerHTML = '';

    productsToDisplay.forEach(product => {
        const card = document.createElement('div');
        card.className = 'product-card';
        card.innerHTML = `
            <div class="product-image">👔</div>
            <h3>${product.name}</h3>
            <p>${product.description}</p>
            <div class="product-price">${formatPrice(product.price)}</div>
        `;
        card.addEventListener('click', () => showProductDetail(product));
        grid.appendChild(card);
    });
}

// Show product detail
function showProductDetail(product) {
    const modal = document.getElementById('productModal');
    const details = document.getElementById('productDetails');
    
    selectedSize = null;
    
    details.innerHTML = `
        <div class="product-detail">
            <div class="product-detail-image">👔</div>
            <h2>${product.name}</h2>
            <div class="product-detail-price">${formatPrice(product.price)}</div>
            <p class="product-detail-description">${product.description}</p>
            <div class="size-selector">
                <label>Chọn size:</label>
                <div class="size-buttons">
                    ${product.sizes.map(size => `
                        <button class="size-btn" data-size="${size}">${size}</button>
                    `).join('')}
                </div>
            </div>
            <button class="btn btn-primary add-to-cart-btn" data-product-id="${product.id}">
                Thêm vào giỏ hàng
            </button>
            <div id="addToCartMessage" class="message"></div>
        </div>
    `;

    // Size selection
    details.querySelectorAll('.size-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            details.querySelectorAll('.size-btn').forEach(b => b.classList.remove('selected'));
            e.target.classList.add('selected');
            selectedSize = e.target.dataset.size;
        });
    });

    // Add to cart
    details.querySelector('.add-to-cart-btn').addEventListener('click', async (e) => {
        const productId = parseInt(e.target.dataset.productId);
        if (!selectedSize) {
            showMessage('addToCartMessage', 'Vui lòng chọn size!', 'error');
            return;
        }
        await addToCart(productId, selectedSize);
    });

    modal.style.display = 'block';
}

// Add to cart
async function addToCart(productId, size) {
    try {
        const response = await fetch('/api/cart/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ productId, quantity: 1, size })
        });
        const data = await response.json();
        
        if (data.success) {
            cart = data.cart;
            updateCartBadge();
            showMessage('addToCartMessage', 'Đã thêm vào giỏ hàng!', 'success');
            
            // Update purchase history
            const product = products.find(p => p.id === productId);
            if (product && !userProfile.purchaseHistory.includes(product.name)) {
                userProfile.purchaseHistory.push(product.name);
                await updateUserProfile();
            }
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showMessage('addToCartMessage', 'Có lỗi xảy ra!', 'error');
    }
}

// Load cart
async function loadCart() {
    try {
        const response = await fetch('/api/cart');
        cart = await response.json();
        updateCartBadge();
    } catch (error) {
        console.error('Error loading cart:', error);
    }
}

// Display cart
function displayCart() {
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');

    if (cart.length === 0) {
        cartItems.innerHTML = '<p style="text-align: center; color: #999; padding: 40px;">Giỏ hàng trống</p>';
        cartTotal.textContent = '0đ';
        return;
    }

    cartItems.innerHTML = '';
    let total = 0;

    cart.forEach(item => {
        total += item.price * item.quantity;
        const cartItem = document.createElement('div');
        cartItem.className = 'cart-item';
        cartItem.innerHTML = `
            <div class="cart-item-info">
                <h4>${item.name}</h4>
                <p>Size: ${item.size} | Số lượng: ${item.quantity}</p>
            </div>
            <div class="cart-item-price">${formatPrice(item.price * item.quantity)}</div>
            <button class="cart-item-remove" data-product-id="${item.productId}">Xóa</button>
        `;
        
        cartItem.querySelector('.cart-item-remove').addEventListener('click', async (e) => {
            await removeFromCart(parseInt(e.target.dataset.productId));
        });
        
        cartItems.appendChild(cartItem);
    });

    cartTotal.textContent = formatPrice(total);
}

// Remove from cart
async function removeFromCart(productId) {
    try {
        const response = await fetch(`/api/cart/${productId}`, {
            method: 'DELETE'
        });
        const data = await response.json();
        
        if (data.success) {
            cart = data.cart;
            updateCartBadge();
            displayCart();
        }
    } catch (error) {
        console.error('Error removing from cart:', error);
    }
}

// Update cart badge
function updateCartBadge() {
    const badge = document.getElementById('cartBadge');
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    badge.textContent = totalItems;
}

// Load user profile
async function loadUserProfile() {
    try {
        const response = await fetch('/api/user/profile');
        userProfile = await response.json();
        displayCurrentProfile();
    } catch (error) {
        console.error('Error loading profile:', error);
    }
}

// Display current profile
function displayCurrentProfile() {
    const profileDiv = document.getElementById('currentProfile');
    profileDiv.innerHTML = `
        <p><strong>Tên:</strong> ${userProfile.name}</p>
        <p><strong>Sở thích:</strong> ${userProfile.preferences.length > 0 ? userProfile.preferences.join(', ') : 'Chưa có thông tin'}</p>
        <p><strong>Lịch sử mua hàng:</strong> ${userProfile.purchaseHistory.length > 0 ? userProfile.purchaseHistory.join(', ') : 'Chưa có đơn hàng nào'}</p>
    `;

    // Update form fields
    document.getElementById('userName').value = userProfile.name !== 'Khách hàng' ? userProfile.name : '';
    document.getElementById('userPreferences').value = userProfile.preferences.join(', ');
}

// Save profile
async function saveProfile() {
    const name = document.getElementById('userName').value || 'Khách hàng';
    const preferencesText = document.getElementById('userPreferences').value;
    const preferences = preferencesText.split(',').map(p => p.trim()).filter(p => p);

    try {
        const response = await fetch('/api/user/profile', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                name,
                preferences,
                purchaseHistory: userProfile.purchaseHistory || []
            })
        });
        const data = await response.json();
        
        if (data.success) {
            userProfile = data.profile;
            displayCurrentProfile();
            showMessage('profileMessage', 'Đã lưu hồ sơ thành công!', 'success');
        }
    } catch (error) {
        console.error('Error saving profile:', error);
        showMessage('profileMessage', 'Có lỗi xảy ra!', 'error');
    }
}

// Update user profile (for purchase history)
async function updateUserProfile() {
    try {
        await fetch('/api/user/profile', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(userProfile)
        });
    } catch (error) {
        console.error('Error updating profile:', error);
    }
}

// Setup chatbot
function setupChatbot() {
    const chatbotHeader = document.getElementById('chatbotHeader');
    const chatbotToggle = document.getElementById('chatbotToggle');
    const chatbotBody = document.getElementById('chatbotBody');
    const sendMessage = document.getElementById('sendMessage');
    const chatInput = document.getElementById('chatInput');

    chatbotToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        chatbotBody.classList.toggle('collapsed');
        chatbotToggle.textContent = chatbotBody.classList.contains('collapsed') ? '+' : '−';
    });

    sendMessage.addEventListener('click', () => sendChatMessage());
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            sendChatMessage();
        }
    });
}

// Send chat message
async function sendChatMessage() {
    const chatInput = document.getElementById('chatInput');
    const message = chatInput.value.trim();
    
    if (!message) return;

    // Display user message
    addChatMessage(message, 'user');
    chatInput.value = '';

    // Show loading
    const loadingId = addChatMessage('Đang suy nghĩ...', 'bot');

    try {
        const response = await fetch('/api/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message })
        });
        const data = await response.json();

        // Remove loading message
        document.getElementById(loadingId).remove();

        // Display bot response
        addChatMessage(data.response, 'bot');
    } catch (error) {
        console.error('Chat error:', error);
        document.getElementById(loadingId).remove();
        addChatMessage('Xin lỗi, có lỗi xảy ra. Vui lòng thử lại!', 'bot');
    }
}

// Add chat message
function addChatMessage(message, type) {
    const chatMessages = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    const messageId = 'msg-' + Date.now();
    messageDiv.id = messageId;
    messageDiv.className = type === 'user' ? 'user-message' : 'bot-message';
    
    if (type === 'user') {
        messageDiv.innerHTML = `<strong>Bạn:</strong><p>${message}</p>`;
    } else {
        messageDiv.innerHTML = `<strong>Trợ lý IvyModa:</strong><p>${message}</p>`;
    }
    
    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
    
    return messageId;
}

// Helper functions
function formatPrice(price) {
    return price.toLocaleString('vi-VN') + 'đ';
}

function showMessage(elementId, message, type) {
    const messageDiv = document.getElementById(elementId);
    messageDiv.textContent = message;
    messageDiv.className = `message ${type}`;
    messageDiv.style.display = 'block';
    
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 3000);
}
