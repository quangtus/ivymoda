# IVY Moda - Automated Setup Script for Windows PowerShell
# This script will install all dependencies and set up the project

# Require Administrator privileges for some operations
#Requires -RunAsAdministrator

# Set error action
$ErrorActionPreference = "Stop"

# Colors
function Write-Success {
    param([string]$Message)
    Write-Host "✓ $Message" -ForegroundColor Green
}

function Write-Error-Custom {
    param([string]$Message)
    Write-Host "✗ $Message" -ForegroundColor Red
}

function Write-Warning-Custom {
    param([string]$Message)
    Write-Host "⚠ $Message" -ForegroundColor Yellow
}

function Write-Info {
    param([string]$Message)
    Write-Host "ℹ $Message" -ForegroundColor Cyan
}

function Write-Header {
    param([string]$Message)
    Write-Host ""
    Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Blue
    Write-Host "  $Message" -ForegroundColor Blue
    Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Blue
    Write-Host ""
}

# Main setup function
function Main {
    Clear-Host
    
    Write-Host ""
    Write-Host "╔═══════════════════════════════════════════════╗" -ForegroundColor Blue
    Write-Host "║                                               ║" -ForegroundColor Blue
    Write-Host "║        IVY MODA - AUTO SETUP SCRIPT          ║" -ForegroundColor Blue
    Write-Host "║                  PowerShell                   ║" -ForegroundColor Blue
    Write-Host "║                                               ║" -ForegroundColor Blue
    Write-Host "╚═══════════════════════════════════════════════╝" -ForegroundColor Blue
    Write-Host ""
    
    try {
        # Step 1: Check PHP
        Write-Header "Step 1: Checking PHP Installation"
        
        $phpPath = Get-Command php -ErrorAction SilentlyContinue
        if (-not $phpPath) {
            Write-Error-Custom "PHP is not installed or not in PATH!"
            Write-Info "Please install PHP 7.4 or higher first"
            Write-Info "Download from: https://windows.php.net/download/"
            exit 1
        }
        
        $phpVersion = php -r "echo PHP_VERSION;"
        Write-Success "PHP $phpVersion found"
        
        # Check PHP version
        $phpMajor = [int](php -r "echo PHP_MAJOR_VERSION;")
        $phpMinor = [int](php -r "echo PHP_MINOR_VERSION;")
        
        if ($phpMajor -lt 7 -or ($phpMajor -eq 7 -and $phpMinor -lt 4)) {
            Write-Error-Custom "PHP version must be 7.4 or higher. Current: $phpVersion"
            exit 1
        }
        
        # Step 2: Check Composer
        Write-Header "Step 2: Checking Composer Installation"
        
        $composerPath = Get-Command composer -ErrorAction SilentlyContinue
        if (-not $composerPath) {
            Write-Warning-Custom "Composer not found!"
            Write-Info "Please install Composer from: https://getcomposer.org/download/"
            Write-Info "After installation, run this script again."
            exit 1
        }
        
        $composerVersion = (composer --version 2>&1 | Select-String "Composer version").ToString().Split()[2]
        Write-Success "Composer $composerVersion found"
        
        # Step 3: Install Dependencies
        Write-Header "Step 3: Installing PHP Dependencies"
        Write-Info "Running: composer install..."
        Write-Host ""
        
        $composerResult = composer install --no-interaction --prefer-dist --optimize-autoloader
        if ($LASTEXITCODE -ne 0) {
            Write-Error-Custom "Failed to install dependencies"
            exit 1
        }
        
        Write-Success "Dependencies installed successfully"
        
        # Step 4: Environment Configuration
        Write-Header "Step 4: Setting up Environment Configuration"
        
        if (-not (Test-Path ".env")) {
            if (Test-Path ".env.example") {
                Copy-Item ".env.example" ".env"
                Write-Success "Created .env file from .env.example"
                Write-Warning-Custom "Please update .env file with your database and email credentials"
            } else {
                Write-Error-Custom ".env.example not found!"
            }
        } else {
            Write-Info ".env file already exists (skipping)"
        }
        
        # Step 5: Create Required Directories
        Write-Header "Step 5: Creating Required Directories"
        
        $dirs = @(
            "public\assets\uploads",
            "logs"
        )
        
        foreach ($dir in $dirs) {
            if (-not (Test-Path $dir)) {
                New-Item -ItemType Directory -Path $dir -Force | Out-Null
                Write-Success "Created $dir directory"
            } else {
                Write-Info "$dir already exists"
            }
        }
        
        # Step 6: Set Permissions
        Write-Header "Step 6: Setting Directory Permissions"
        
        $aclDirs = @(
            "public\assets\uploads",
            "logs"
        )
        
        if (Test-Path "vendor") {
            $aclDirs += "vendor"
        }
        
        foreach ($dir in $aclDirs) {
            if (Test-Path $dir) {
                $acl = Get-Acl $dir
                $rule = New-Object System.Security.AccessControl.FileSystemAccessRule(
                    "Users", "FullControl", "ContainerInherit,ObjectInherit", "None", "Allow"
                )
                $acl.SetAccessRule($rule)
                Set-Acl $dir $acl
            }
        }
        
        Write-Success "Permissions set successfully"
        
        # Step 7: Check MySQL/MariaDB
        Write-Header "Step 7: Checking Database"
        
        $mysqlPath = Get-Command mysql -ErrorAction SilentlyContinue
        if ($mysqlPath) {
            $mysqlVersion = (mysql --version 2>&1).ToString()
            Write-Success "MySQL/MariaDB found"
        } else {
            Write-Warning-Custom "MySQL/MariaDB not found in PATH"
            Write-Info "Make sure XAMPP or MySQL server is running"
        }
        
        Write-Warning-Custom "Don't forget to import ivymoda_final.sql into your database"
        
        # Final Summary
        Write-Header "Setup Complete!"
        
        Write-Success "All dependencies installed"
        Write-Success "Project structure created"
        Write-Success "Permissions configured"
        Write-Host ""
        
        Write-Info "Next steps:"
        Write-Host ""
        Write-Host "  1. Update .env file with your configuration:" -ForegroundColor White
        Write-Host "     - Database credentials (DB_HOST, DB_NAME, DB_USER, DB_PASS)" -ForegroundColor Gray
        Write-Host "     - Email settings (SMTP_USERNAME, SMTP_PASSWORD)" -ForegroundColor Gray
        Write-Host "     - Base URL (BASE_URL)" -ForegroundColor Gray
        Write-Host ""
        Write-Host "  2. Import the database:" -ForegroundColor White
        Write-Host "     - Open phpMyAdmin, or use MySQL command:" -ForegroundColor Gray
        Write-Host "       mysql -u root -p ivymoda < ivymoda_final.sql" -ForegroundColor Gray
        Write-Host ""
        Write-Host "  3. Start XAMPP or use PHP built-in server:" -ForegroundColor White
        Write-Host "     php -S localhost:8000 -t public" -ForegroundColor Gray
        Write-Host ""
        Write-Host "  4. Access the application:" -ForegroundColor White
        Write-Host "     Frontend: http://localhost:8000" -ForegroundColor Gray
        Write-Host "     Admin: http://localhost:8000/admin" -ForegroundColor Gray
        Write-Host ""
        
        Write-Success "Setup completed successfully! 🎉"
        
    } catch {
        Write-Error-Custom "An error occurred: $_"
        exit 1
    }
}

# Run main function
Main

# Pause at the end
Write-Host ""
Write-Host "Press any key to exit..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
