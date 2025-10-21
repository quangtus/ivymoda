# IVY Moda - Installation Guide

## 🚀 Quick Setup (Automated)

### Windows

#### Option 1: PowerShell (Recommended for Windows 10+)
```powershell
# Run as Administrator
.\setup.ps1
```

#### Option 2: Batch Script
```cmd
setup.bat
```

### Linux/Mac
```bash
chmod +x setup.sh
./setup.sh
```

---

## 📋 Manual Installation

If you prefer to set up manually or the automated script doesn't work:

### Prerequisites

- **PHP** >= 7.4
- **MySQL/MariaDB** >= 5.7
- **Composer** (PHP package manager)
- **Web Server** (Apache/Nginx) or XAMPP

### Step-by-Step Instructions

#### 1. Install Composer (if not installed)

**Windows:**
- Download from: https://getcomposer.org/download/
- Run the installer

**Linux/Mac:**
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### 2. Install PHP Dependencies
```bash
composer install
```

#### 3. Configure Environment
```bash
# Copy environment file
cp .env.example .env

# Edit .env file with your settings
# - Database credentials
# - Email configuration
# - API keys
```

#### 4. Create Required Directories
```bash
# Linux/Mac
mkdir -p public/assets/uploads
mkdir -p logs
chmod -R 755 public/assets/uploads
chmod -R 755 logs

# Windows
mkdir public\assets\uploads
mkdir logs
```

#### 5. Setup Database

**Create Database:**
```sql
CREATE DATABASE ivymoda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Import Database:**
```bash
# Using MySQL command line
mysql -u root -p ivymoda < ivymoda_final.sql

# Or use phpMyAdmin
# 1. Open phpMyAdmin
# 2. Select 'ivymoda' database
# 3. Click 'Import' tab
# 4. Choose 'ivymoda_final.sql' file
# 5. Click 'Go'
```

#### 6. Configure Web Server

**Option A: XAMPP/WAMP**
- Copy project to `htdocs` or `www` folder
- Access via: `http://localhost/ivymoda/ivymoda_mvc/public/`

**Option B: PHP Built-in Server**
```bash
php -S localhost:8000 -t public/
```
Access via: `http://localhost:8000`

**Option C: Apache Virtual Host**

Edit `httpd-vhosts.conf`:
```apache
<VirtualHost *:80>
    ServerName ivymoda.local
    DocumentRoot "C:/xampp/htdocs/ivymoda/ivymoda_mvc/public"
    
    <Directory "C:/xampp/htdocs/ivymoda/ivymoda_mvc/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Edit `hosts` file:
```
127.0.0.1 ivymoda.local
```

#### 7. Verify Installation

Visit the following URLs to verify:
- Frontend: `http://localhost/` or `http://localhost:8000`
- Admin: `http://localhost/admin` or `http://localhost:8000/admin`

Default admin credentials (if seeded):
- Email: `admin@ivymoda.com`
- Password: `admin123`

---

## 🔧 Configuration

### Database (.env)
```env
DB_HOST=localhost
DB_NAME=ivymoda
DB_USER=root
DB_PASS=
```

### Email Configuration (.env)

For Gmail:
```env
SMTP_HOST=smtp.gmail.com
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
SMTP_PORT=587
SMTP_SECURE=tls
SMTP_FROM_EMAIL=your-email@gmail.com
SMTP_FROM_NAME=IVY Moda
```

**Note:** For Gmail, you need to:
1. Enable 2-factor authentication
2. Generate an App Password: https://myaccount.google.com/apppasswords

### Base URL (.env)
```env
# For XAMPP
BASE_URL=http://localhost/ivymoda/ivymoda_mvc/public/

# For PHP built-in server
BASE_URL=http://localhost:8000/

# For production
BASE_URL=https://yourdomain.com/
```

### MoMo Payment (Optional)

For testing, use the provided sandbox credentials in `.env.example`.

For production, update with your credentials:
```env
PROD=production
PROD_MOMO_ENDPOINT=https://payment.momo.vn
PROD_ACCESS_KEY=your_access_key
PROD_PARTNER_CODE=your_partner_code
PROD_SECRET_KEY=your_secret_key
```

### Gemini AI Chatbot (Optional)

Get API key from: https://makersuite.google.com/app/apikey

```env
GEMINI_API_KEY=your_api_key_here
```

---

## 🐛 Troubleshooting

### Class "admin\ChatbotController" not found

**Cause:** Composer autoloader not installed

**Solution:**
```bash
composer install
# or
composer dump-autoload
```

### Permission Denied Errors

**Linux/Mac:**
```bash
chmod -R 755 public/assets/uploads
chmod -R 755 logs
chown -R www-data:www-data public/assets/uploads
chown -R www-data:www-data logs
```

**Windows:**
- Right-click folder → Properties → Security
- Give "Users" full control

### Database Connection Failed

1. Check MySQL/MariaDB is running
2. Verify credentials in `.env`
3. Ensure database exists
4. Check port (default: 3306)

### Email Not Sending

1. Verify SMTP credentials
2. For Gmail, use App Password (not regular password)
3. Check firewall/antivirus blocking port 587
4. Test with a simple PHP mailer script

### 404 Errors / Routing Issues

1. Check `.htaccess` exists in `public/` folder
2. Enable `mod_rewrite` in Apache:
   ```bash
   # Linux
   sudo a2enmod rewrite
   sudo service apache2 restart
   
   # XAMPP: Edit httpd.conf
   # Uncomment: LoadModule rewrite_module modules/mod_rewrite.so
   ```

---

## 📚 Additional Resources

- [Chatbot AI Guide](CHATBOT_AI_GUIDE.md)
- [MoMo Integration Guide](MOMO_INTEGRATION_GUIDE.md)
- [Email System Guide](docs/EMAIL_SYSTEM_GUIDE.md)
- [Review System Guide](docs/REVIEW_SYSTEM_GUIDE.md)

---

## 🆘 Getting Help

If you encounter issues:

1. Check error logs in `logs/` folder
2. Enable error display in PHP (for development):
   ```php
   // In public/index.php (top of file)
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```
3. Check PHP error log
4. Contact development team

---

## ✅ Post-Installation Checklist

- [ ] Composer dependencies installed
- [ ] `.env` file configured
- [ ] Database created and imported
- [ ] Upload directories created with proper permissions
- [ ] Web server configured
- [ ] Frontend accessible
- [ ] Admin panel accessible
- [ ] Email system tested
- [ ] Payment integration tested (if applicable)
- [ ] Chatbot functional (if applicable)

---

**Congratulations! Your IVY Moda installation is complete! 🎉**
