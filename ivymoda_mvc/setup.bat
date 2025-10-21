@echo off
REM IVY Moda - Automated Setup Script for Windows
REM This script will install all dependencies and set up the project

setlocal enabledelayedexpansion

:: Set colors (Windows 10+)
color 0A

cls
echo.
echo ================================================
echo.
echo        IVY MODA - AUTO SETUP SCRIPT
echo.
echo ================================================
echo.

:: Step 1: Check PHP
echo [Step 1/7] Checking PHP Installation...
echo ----------------------------------------
where php >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] PHP is not installed or not in PATH!
    echo.
    echo Please install PHP 7.4 or higher first.
    echo Download from: https://windows.php.net/download/
    echo.
    pause
    exit /b 1
)

for /f "tokens=*" %%i in ('php -r "echo PHP_VERSION;"') do set PHP_VERSION=%%i
echo [OK] PHP %PHP_VERSION% found
echo.

:: Check PHP version
for /f "tokens=*" %%i in ('php -r "echo PHP_MAJOR_VERSION;"') do set PHP_MAJOR=%%i
for /f "tokens=*" %%i in ('php -r "echo PHP_MINOR_VERSION;"') do set PHP_MINOR=%%i

if %PHP_MAJOR% LSS 7 (
    echo [ERROR] PHP version must be 7.4 or higher. Current: %PHP_VERSION%
    pause
    exit /b 1
)

if %PHP_MAJOR% EQU 7 if %PHP_MINOR% LSS 4 (
    echo [ERROR] PHP version must be 7.4 or higher. Current: %PHP_VERSION%
    pause
    exit /b 1
)

:: Step 2: Check Composer
echo [Step 2/7] Checking Composer Installation...
echo ----------------------------------------
where composer >nul 2>nul
if %errorlevel% neq 0 (
    echo [WARNING] Composer not found!
    echo.
    echo Please install Composer from: https://getcomposer.org/download/
    echo.
    echo After installation, run this script again.
    pause
    exit /b 1
)

for /f "tokens=3" %%i in ('composer --version 2^>nul ^| findstr /C:"Composer version"') do set COMPOSER_VERSION=%%i
echo [OK] Composer %COMPOSER_VERSION% found
echo.

:: Step 3: Install Dependencies
echo [Step 3/7] Installing PHP Dependencies...
echo ----------------------------------------
echo Running: composer install...
echo.

composer install --no-interaction --prefer-dist --optimize-autoloader
if %errorlevel% neq 0 (
    echo.
    echo [ERROR] Failed to install dependencies
    pause
    exit /b 1
)

echo.
echo [OK] Dependencies installed successfully
echo.

:: Step 4: Environment Configuration
echo [Step 4/7] Setting up Environment Configuration...
echo ----------------------------------------

if not exist ".env" (
    if exist ".env.example" (
        copy ".env.example" ".env" >nul
        echo [OK] Created .env file from .env.example
        echo [WARNING] Please update .env file with your credentials
    ) else (
        echo [ERROR] .env.example not found!
    )
) else (
    echo [INFO] .env file already exists (skipping)
)
echo.

:: Step 5: Create Required Directories
echo [Step 5/7] Creating Required Directories...
echo ----------------------------------------

if not exist "public\assets\uploads" (
    mkdir "public\assets\uploads"
    echo [OK] Created public\assets\uploads directory
)

if not exist "logs" (
    mkdir "logs"
    echo [OK] Created logs directory
)
echo.

:: Step 6: Set Permissions (Windows - using icacls)
echo [Step 6/7] Setting Directory Permissions...
echo ----------------------------------------

icacls "public\assets\uploads" /grant Users:(OI)(CI)F /T >nul 2>&1
icacls "logs" /grant Users:(OI)(CI)F /T >nul 2>&1

if exist "vendor" (
    icacls "vendor" /grant Users:(OI)(CI)F /T >nul 2>&1
)

echo [OK] Permissions set successfully
echo.

:: Step 7: Check MySQL
echo [Step 7/7] Checking Database...
echo ----------------------------------------

where mysql >nul 2>nul
if %errorlevel% equ 0 (
    for /f "tokens=*" %%i in ('mysql --version 2^>^&1') do set MYSQL_VERSION=%%i
    echo [OK] MySQL/MariaDB found
) else (
    echo [WARNING] MySQL/MariaDB not found in PATH
    echo Make sure XAMPP or MySQL server is running
)
echo.
echo [WARNING] Don't forget to import ivymoda_final.sql
echo.

:: Final Summary
echo.
echo ================================================
echo           SETUP COMPLETE!
echo ================================================
echo.
echo [OK] All dependencies installed
echo [OK] Project structure created
echo [OK] Permissions configured
echo.
echo ------------------------------------------------
echo NEXT STEPS:
echo ------------------------------------------------
echo.
echo 1. Update .env file with your configuration:
echo    - Database credentials
echo    - Email settings
echo    - Base URL
echo.
echo 2. Import the database:
echo    - Open phpMyAdmin or use MySQL command:
echo      mysql -u root -p ivymoda ^< ivymoda_final.sql
echo.
echo 3. Start XAMPP:
echo    - Make sure Apache and MySQL are running
echo.
echo 4. Access the application:
echo    - Frontend: http://localhost/ivymoda/ivymoda_mvc/public/
echo    - Admin: http://localhost/ivymoda/ivymoda_mvc/public/admin
echo.
echo    OR use PHP built-in server:
echo    - Run: php -S localhost:8000 -t public
echo    - Frontend: http://localhost:8000
echo    - Admin: http://localhost:8000/admin
echo.
echo ================================================
echo Setup completed successfully! [OK]
echo ================================================
echo.
pause
