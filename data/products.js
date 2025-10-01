// Sample product data structure
// You can modify this file and update server.js to use your own products

module.exports = [
    {
        id: 1,
        name: 'Áo Sơ Mi Trắng',
        category: 'shirt',
        price: 350000,
        description: 'Áo sơ mi trắng cao cấp, chất liệu cotton thoáng mát',
        image: 'images/shirt1.jpg',
        sizes: ['S', 'M', 'L', 'XL'],
        inStock: true,
        tags: ['công sở', 'thanh lịch', 'cotton']
    },
    {
        id: 2,
        name: 'Quần Jean Đen',
        category: 'pants',
        price: 450000,
        description: 'Quần jean đen form slim, co giãn tốt',
        image: 'images/pants1.jpg',
        sizes: ['28', '29', '30', '31', '32'],
        inStock: true,
        tags: ['casual', 'jean', 'co giãn']
    },
    {
        id: 3,
        name: 'Áo Thun Nam Basic',
        category: 'tshirt',
        price: 250000,
        description: 'Áo thun nam basic, nhiều màu sắc',
        image: 'images/tshirt1.jpg',
        sizes: ['S', 'M', 'L', 'XL'],
        inStock: true,
        tags: ['basic', 'casual', 'thoải mái']
    },
    {
        id: 4,
        name: 'Váy Công Sở',
        category: 'dress',
        price: 550000,
        description: 'Váy công sở thanh lịch, phù hợp môi trường văn phòng',
        image: 'images/dress1.jpg',
        sizes: ['S', 'M', 'L'],
        inStock: true,
        tags: ['công sở', 'thanh lịch', 'nữ']
    },
    {
        id: 5,
        name: 'Áo Khoác Dạ',
        category: 'jacket',
        price: 850000,
        description: 'Áo khoác dạ ấm áp, phong cách Hàn Quốc',
        image: 'images/jacket1.jpg',
        sizes: ['M', 'L', 'XL'],
        inStock: true,
        tags: ['mùa đông', 'ấm áp', 'Hàn Quốc']
    },
    // Add more products here...
    // {
    //     id: 6,
    //     name: 'Your Product Name',
    //     category: 'shirt|tshirt|pants|dress|jacket',
    //     price: 0,
    //     description: 'Product description',
    //     image: 'images/yourimage.jpg',
    //     sizes: ['S', 'M', 'L', 'XL'],
    //     inStock: true,
    //     tags: ['tag1', 'tag2']
    // }
];
