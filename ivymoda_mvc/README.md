# 🛍️ IVY MODA - E-Commerce System

Modern e-commerce platform built with PHP MVC architecture, featuring AI-powered chatbot and comprehensive product management.

## ⚡ Quick Start

### 1️⃣ Clone Project
```bash
git clone https://github.com/your-username/ivymoda.git
cd ivymoda/ivymoda_mvc
```

### 2️⃣ Run Setup (Windows)
```bash
setup.bat
```

### 3️⃣ Start XAMPP
- Start **Apache**
- Start **MySQL**

### 4️⃣ Access Website
- **Frontend:** http://localhost/ivymoda/ivymoda_mvc/public/
- **Admin:** http://localhost/ivymoda/ivymoda_mvc/public/admin
- **Credentials:** admin / admin123

---

## 📚 Full Documentation

**IMPORTANT:** Read the complete installation guide if you encounter issues:

👉 **[INSTALLATION_GUIDE.md](ivymoda_mvc/INSTALLATION_GUIDE.md)**

---

## ⚠️ Common Issues

### ❌ PHPMailer Error
```
Warning: include(vendor/phpmailer/...): Failed to open stream
```

**Solution:** Run `setup.bat` OR `composer install`

### ❌ 404 Not Found
**Solution:** Enable `mod_rewrite` in Apache `httpd.conf`

### ❌ Database Error
**Solution:** Import `ivymoda_final.sql` via phpMyAdmin

---

## 🛠️ Requirements

- **PHP:** 7.4 or higher
- **MySQL:** 5.7 or higher
- **Apache:** with mod_rewrite enabled
- **Composer:** for dependency management

---

## 📦 What's Included

- ✅ **MVC Architecture:** Clean separation of concerns
- ✅ **Admin Panel:** Product, order, user management
- ✅ **Shopping Cart:** Session + database synchronization
- ✅ **Payment Integration:** MoMo, COD
- ✅ **Email System:** PHPMailer for notifications
- ✅ **AI Chatbot:** Google Gemini integration
- ✅ **Review System:** Customer feedback with images
- ✅ **Promotion System:** Discount codes and campaigns

---

## 🎯 Features

### Customer Features
- Product browsing with filters
- Shopping cart with variant selection
- User registration and login
- Order tracking
- Product reviews
- AI-powered chatbot assistance

### Admin Features
- Dashboard with analytics
- Product management (CRUD)
- Order management
- User management
- Promotion campaigns
- Email templates
- FAQ management
- Report generation

---

## 📁 Project Structure

```
ivymoda_mvc/
├── app/
│   ├── controllers/    # MVC Controllers
│   ├── models/         # Database Models
│   ├── views/          # UI Templates
│   ├── core/           # Core Framework
│   ├── helpers/        # Utility Functions
│   └── services/       # Business Logic
├── config/             # Configuration Files
├── public/             # Public Assets
│   ├── assets/         # CSS, JS, Images
│   ├── ajax/           # AJAX Endpoints
│   └── index.php       # Entry Point
├── vendor/             # Composer Dependencies (auto-generated)
├── .env.example        # Environment Template
├── composer.json       # PHP Dependencies
├── ivymoda_final.sql   # Database Schema
└── setup.bat           # Auto Setup Script
```

---

## 🔧 Manual Installation

If automatic setup fails:

```bash
# 1. Install dependencies
composer install

# 2. Configure environment
copy .env.example .env
# Edit .env with your database credentials

# 3. Create directories
mkdir public\assets\uploads
mkdir logs

# 4. Import database
mysql -u root ivymoda < ivymoda_final.sql

# 5. Configure Apache
# Enable mod_rewrite
# Set AllowOverride All for htdocs
```

Full guide: [INSTALLATION_GUIDE.md](ivymoda_mvc/INSTALLATION_GUIDE.md)

---

## 🤝 Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

---

## 📄 License

This project is for educational purposes.

---

## 📞 Support

For issues and questions:
- Check [INSTALLATION_GUIDE.md](ivymoda_mvc/INSTALLATION_GUIDE.md)
- Review error logs in `logs/` folder
- Check Apache error log: `C:\xampp\apache\logs\error.log`

---

**Made with ❤️ by IVY Moda Team**
