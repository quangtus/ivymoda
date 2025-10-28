#!/bin/bash

# IVY Moda - Automated Setup Script for Linux/Mac
# This script will install all dependencies and set up the project

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Print with color
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

print_header() {
    echo ""
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${BLUE}  $1${NC}"
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""
}

# Main setup function
main() {
    clear
    echo -e "${BLUE}"
    echo "╔═══════════════════════════════════════════════╗"
    echo "║                                               ║"
    echo "║        IVY MODA - AUTO SETUP SCRIPT          ║"
    echo "║                                               ║"
    echo "╚═══════════════════════════════════════════════╝"
    echo -e "${NC}"
    
    # Step 1: Check PHP
    print_header "Step 1: Checking PHP Installation"
    if ! command -v php &> /dev/null; then
        print_error "PHP is not installed!"
        print_info "Please install PHP 7.4 or higher first"
        exit 1
    fi
    
    PHP_VERSION=$(php -r 'echo PHP_VERSION;')
    print_success "PHP $PHP_VERSION found"
    
    # Check PHP version
    PHP_MAJOR=$(php -r 'echo PHP_MAJOR_VERSION;')
    PHP_MINOR=$(php -r 'echo PHP_MINOR_VERSION;')
    
    if [ "$PHP_MAJOR" -lt 7 ] || ([ "$PHP_MAJOR" -eq 7 ] && [ "$PHP_MINOR" -lt 4 ]); then
        print_error "PHP version must be 7.4 or higher. Current: $PHP_VERSION"
        exit 1
    fi
    
    # Step 2: Check Composer
    print_header "Step 2: Checking Composer Installation"
    if ! command -v composer &> /dev/null; then
        print_warning "Composer not found. Installing Composer..."
        
        # Download and install Composer
        php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
        php composer-setup.php --quiet
        rm composer-setup.php
        
        if [ -f "composer.phar" ]; then
            sudo mv composer.phar /usr/local/bin/composer
            sudo chmod +x /usr/local/bin/composer
            print_success "Composer installed successfully"
        else
            print_error "Failed to install Composer"
            print_info "Please install Composer manually from https://getcomposer.org/"
            exit 1
        fi
    else
        COMPOSER_VERSION=$(composer --version 2>&1 | grep -oP 'Composer version \K[0-9.]+' || echo "unknown")
        print_success "Composer $COMPOSER_VERSION found"
    fi
    
    # Step 3: Install Dependencies
    print_header "Step 3: Installing PHP Dependencies"
    print_info "Running: composer install..."
    
    if composer install --no-interaction --prefer-dist --optimize-autoloader; then
        print_success "Dependencies installed successfully"
    else
        print_error "Failed to install dependencies"
        exit 1
    fi
    
    # Step 4: Environment Configuration
    print_header "Step 4: Setting up Environment Configuration"
    
    if [ ! -f ".env" ]; then
        if [ -f ".env.example" ]; then
            cp .env.example .env
            print_success "Created .env file from .env.example"
            print_warning "Please update .env file with your database and email credentials"
        else
            print_error ".env.example not found!"
        fi
    else
        print_info ".env file already exists (skipping)"
    fi
    
    # Step 5: Create Required Directories
    print_header "Step 5: Creating Required Directories"
    
    mkdir -p public/assets/uploads
    mkdir -p logs
    
    print_success "Created public/assets/uploads directory"
    print_success "Created logs directory"
    
    # Step 6: Set Permissions
    print_header "Step 6: Setting Directory Permissions"
    
    chmod -R 755 public/assets/uploads
    chmod -R 755 logs
    
    if [ -d "vendor" ]; then
        chmod -R 755 vendor
    fi
    
    print_success "Permissions set successfully"
    
    # Step 7: Check MySQL/MariaDB
    print_header "Step 7: Checking Database"
    
    if command -v mysql &> /dev/null; then
        MYSQL_VERSION=$(mysql --version 2>&1 | grep -oP 'Ver \K[0-9.]+' || echo "unknown")
        print_success "MySQL/MariaDB $MYSQL_VERSION found"
        print_warning "Don't forget to import ivymoda_final.sql into your database"
    else
        print_warning "MySQL/MariaDB not found in PATH"
        print_info "Make sure you have a MySQL/MariaDB server running"
    fi
    
    # Step 8: Final Setup Summary
    print_header "Setup Complete!"
    
    echo -e "${GREEN}✓ All dependencies installed${NC}"
    echo -e "${GREEN}✓ Project structure created${NC}"
    echo -e "${GREEN}✓ Permissions configured${NC}"
    echo ""
    
    print_info "Next steps:"
    echo ""
    echo "  1. Update .env file with your configuration:"
    echo "     - Database credentials (DB_HOST, DB_NAME, DB_USER, DB_PASS)"
    echo "     - Email settings (SMTP_USERNAME, SMTP_PASSWORD)"
    echo "     - Base URL (BASE_URL)"
    echo ""
    echo "  2. Import the database:"
    echo "     mysql -u root -p ivymoda < ivymoda_final.sql"
    echo ""
    echo "  3. Start the development server:"
    echo "     php -S localhost:8000 -t public/"
    echo ""
    echo "  4. Access the application:"
    echo "     Frontend: http://localhost:8000"
    echo "     Admin: http://localhost:8000/admin"
    echo ""
    
    print_success "Setup completed successfully! 🎉"
}

# Run main function
main
