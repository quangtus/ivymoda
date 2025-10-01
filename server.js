const express = require('express');
const bodyParser = require('body-parser');
const session = require('express-session');
const path = require('path');
const dotenv = require('dotenv');
const { GoogleGenerativeAI } = require('@google/generative-ai');

dotenv.config();

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));
app.use(express.static('public'));
app.use(session({
    secret: process.env.SESSION_SECRET || 'ivymoda-secret-key',
    resave: false,
    saveUninitialized: true,
    cookie: { secure: false }
}));

// Initialize Gemini AI
const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY || '');

// Sample product data
const products = [
    {
        id: 1,
        name: 'Áo Sơ Mi Trắng',
        category: 'shirt',
        price: 350000,
        description: 'Áo sơ mi trắng cao cấp, chất liệu cotton thoáng mát',
        image: 'images/shirt1.jpg',
        sizes: ['S', 'M', 'L', 'XL'],
        inStock: true
    },
    {
        id: 2,
        name: 'Quần Jean Đen',
        category: 'pants',
        price: 450000,
        description: 'Quần jean đen form slim, co giãn tốt',
        image: 'images/pants1.jpg',
        sizes: ['28', '29', '30', '31', '32'],
        inStock: true
    },
    {
        id: 3,
        name: 'Áo Thun Nam Basic',
        category: 'tshirt',
        price: 250000,
        description: 'Áo thun nam basic, nhiều màu sắc',
        image: 'images/tshirt1.jpg',
        sizes: ['S', 'M', 'L', 'XL'],
        inStock: true
    },
    {
        id: 4,
        name: 'Váy Công Sở',
        category: 'dress',
        price: 550000,
        description: 'Váy công sở thanh lịch, phù hợp môi trường văn phòng',
        image: 'images/dress1.jpg',
        sizes: ['S', 'M', 'L'],
        inStock: true
    },
    {
        id: 5,
        name: 'Áo Khoác Dạ',
        category: 'jacket',
        price: 850000,
        description: 'Áo khoác dạ ấm áp, phong cách Hàn Quốc',
        image: 'images/jacket1.jpg',
        sizes: ['M', 'L', 'XL'],
        inStock: true
    }
];

// Routes
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

app.get('/api/products', (req, res) => {
    const { category } = req.query;
    if (category) {
        const filtered = products.filter(p => p.category === category);
        res.json(filtered);
    } else {
        res.json(products);
    }
});

app.get('/api/products/:id', (req, res) => {
    const product = products.find(p => p.id === parseInt(req.params.id));
    if (product) {
        res.json(product);
    } else {
        res.status(404).json({ error: 'Product not found' });
    }
});

// User profile endpoints
app.post('/api/user/profile', (req, res) => {
    const { name, preferences, purchaseHistory } = req.body;
    req.session.userProfile = {
        name: name || 'Khách hàng',
        preferences: preferences || [],
        purchaseHistory: purchaseHistory || [],
        chatHistory: req.session.userProfile?.chatHistory || []
    };
    res.json({ success: true, profile: req.session.userProfile });
});

app.get('/api/user/profile', (req, res) => {
    const profile = req.session.userProfile || {
        name: 'Khách hàng',
        preferences: [],
        purchaseHistory: [],
        chatHistory: []
    };
    res.json(profile);
});

// Shopping cart endpoints
app.post('/api/cart/add', (req, res) => {
    if (!req.session.cart) {
        req.session.cart = [];
    }
    const { productId, quantity, size } = req.body;
    const product = products.find(p => p.id === productId);
    
    if (product) {
        const existingItem = req.session.cart.find(
            item => item.productId === productId && item.size === size
        );
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            req.session.cart.push({
                productId,
                name: product.name,
                price: product.price,
                quantity,
                size,
                image: product.image
            });
        }
        res.json({ success: true, cart: req.session.cart });
    } else {
        res.status(404).json({ error: 'Product not found' });
    }
});

app.get('/api/cart', (req, res) => {
    res.json(req.session.cart || []);
});

app.delete('/api/cart/:productId', (req, res) => {
    if (req.session.cart) {
        req.session.cart = req.session.cart.filter(
            item => item.productId !== parseInt(req.params.productId)
        );
    }
    res.json({ success: true, cart: req.session.cart || [] });
});

// Chatbot endpoint with personalization
app.post('/api/chat', async (req, res) => {
    try {
        const { message } = req.body;
        const userProfile = req.session.userProfile || {
            name: 'Khách hàng',
            preferences: [],
            purchaseHistory: [],
            chatHistory: []
        };

        if (!process.env.GEMINI_API_KEY) {
            return res.json({
                response: 'Xin chào! Tôi là trợ lý mua sắm của IvyModa. Để sử dụng chatbot, vui lòng cấu hình GEMINI_API_KEY trong file .env. Hiện tại tôi có thể giúp bạn:\n\n- Xem danh sách sản phẩm\n- Tìm kiếm theo danh mục\n- Thêm sản phẩm vào giỏ hàng\n- Xem giỏ hàng của bạn\n\nHãy cho tôi biết bạn cần gì nhé!'
            });
        }

        // Build context from user profile
        let context = `Bạn là trợ lý mua sắm thông minh của IvyModa - một cửa hàng quần áo trực tuyến. 

Thông tin về khách hàng:
- Tên: ${userProfile.name}
- Sở thích: ${userProfile.preferences.length > 0 ? userProfile.preferences.join(', ') : 'Chưa có thông tin'}
- Lịch sử mua hàng: ${userProfile.purchaseHistory.length > 0 ? userProfile.purchaseHistory.join(', ') : 'Chưa có đơn hàng nào'}

Danh sách sản phẩm hiện có:
${products.map(p => `- ${p.name} (${p.category}): ${p.price.toLocaleString('vi-VN')}đ - ${p.description}`).join('\n')}

Hãy trả lời câu hỏi của khách hàng một cách thân thiện, chuyên nghiệp và cá nhân hóa dựa trên thông tin của họ. 
Nếu khách hàng hỏi về sản phẩm, hãy đề xuất sản phẩm phù hợp với sở thích của họ.
Trả lời bằng tiếng Việt.`;

        // Add chat history for context
        if (userProfile.chatHistory && userProfile.chatHistory.length > 0) {
            context += '\n\nLịch sử trò chuyện gần đây:\n';
            userProfile.chatHistory.slice(-5).forEach(chat => {
                context += `Khách: ${chat.user}\nTrợ lý: ${chat.bot}\n`;
            });
        }

        const model = genAI.getGenerativeModel({ model: 'gemini-pro' });
        const result = await model.generateContent(`${context}\n\nCâu hỏi của khách hàng: ${message}`);
        const response = await result.response;
        const botResponse = response.text();

        // Save to chat history
        if (!req.session.userProfile) {
            req.session.userProfile = userProfile;
        }
        if (!req.session.userProfile.chatHistory) {
            req.session.userProfile.chatHistory = [];
        }
        req.session.userProfile.chatHistory.push({
            user: message,
            bot: botResponse,
            timestamp: new Date()
        });

        // Keep only last 20 messages
        if (req.session.userProfile.chatHistory.length > 20) {
            req.session.userProfile.chatHistory = req.session.userProfile.chatHistory.slice(-20);
        }

        res.json({ response: botResponse });
    } catch (error) {
        console.error('Chat error:', error);
        res.status(500).json({ 
            error: 'Có lỗi xảy ra khi xử lý tin nhắn. Vui lòng thử lại.',
            details: error.message 
        });
    }
});

app.get('/api/chat/history', (req, res) => {
    const chatHistory = req.session.userProfile?.chatHistory || [];
    res.json(chatHistory);
});

// Start server
app.listen(PORT, () => {
    console.log(`IvyModa server running on port ${PORT}`);
    console.log(`Visit http://localhost:${PORT}`);
});
