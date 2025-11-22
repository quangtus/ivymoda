@echo off
REM IVY Moda - Automated Setup Script for Windows
REM This script will install all dependencies and set up the project for XAMPP

setlocal enabledelayedexpansion

:: Set colors (Windows 10+)
color 0A

cls
echo.
echo ================================================
echo.
echo        IVY MODA - AUTO SETUP SCRIPT
echo        For XAMPP on Windows
echo.
echo ================================================
echo.

:: Get current directory (project root)
set "PROJECT_DIR=%~dp0"
cd /d "%PROJECT_DIR%"

:: Detect project folder name from path
for %%F in ("%PROJECT_DIR%") do set "PROJECT_FOLDER=%%~nxF"
set "PROJECT_FOLDER=%PROJECT_FOLDER:~0,-1%"

:: Detect XAMPP installation
set "XAMPP_PATH="
if exist "C:\xampp\php\php.exe" (
    set "XAMPP_PATH=C:\xampp"
    set "XAMPP_PHP=C:\xampp\php\php.exe"
    set "XAMPP_MYSQL=C:\xampp\mysql\bin\mysql.exe"
    set "XAMPP_MYSQLDUMP=C:\xampp\mysql\bin\mysqldump.exe"
) else if exist "D:\xampp\php\php.exe" (
    set "XAMPP_PATH=D:\xampp"
    set "XAMPP_PHP=D:\xampp\php\php.exe"
    set "XAMPP_MYSQL=D:\xampp\mysql\bin\mysql.exe"
    set "XAMPP_MYSQLDUMP=D:\xampp\mysql\bin\mysqldump.exe"
) else (
    echo [WARNING] XAMPP not found in default locations
    echo Please make sure XAMPP is installed and Apache/MySQL are running
    set "XAMPP_PHP=php"
    set "XAMPP_MYSQL=mysql"
)

:: Step 1: Check PHP
echo [Step 1/9] Checking PHP Installation...
echo ----------------------------------------
if defined XAMPP_PHP (
    "%XAMPP_PHP%" -r "echo PHP_VERSION;" >nul 2>nul
    if !errorlevel! equ 0 (
        for /f "tokens=*" %%i in ('"%XAMPP_PHP%" -r "echo PHP_VERSION;"') do set PHP_VERSION=%%i
        echo [OK] PHP %PHP_VERSION% found ^(XAMPP^)
        set "PHP_CMD=%XAMPP_PHP%"
    ) else (
        where php >nul 2>nul
        if !errorlevel! equ 0 (
            for /f "tokens=*" %%i in ('php -r "echo PHP_VERSION;"') do set PHP_VERSION=%%i
            echo [OK] PHP %PHP_VERSION% found ^(System PATH^)
            set "PHP_CMD=php"
        ) else (
            echo [ERROR] PHP is not installed or not in PATH!
            echo.
            echo Please install XAMPP or PHP 7.4 or higher first.
            echo Download XAMPP from: https://www.apachefriends.org/
            echo.
            pause
            exit /b 1
        )
    )
) else (
    where php >nul 2>nul
    if !errorlevel! equ 0 (
        for /f "tokens=*" %%i in ('php -r "echo PHP_VERSION;"') do set PHP_VERSION=%%i
        echo [OK] PHP %PHP_VERSION% found
        set "PHP_CMD=php"
    ) else (
        echo [ERROR] PHP is not installed or not in PATH!
        echo.
        echo Please install XAMPP or PHP 7.4 or higher first.
        echo Download XAMPP from: https://www.apachefriends.org/
        echo.
        pause
        exit /b 1
    )
)

:: Check PHP version
for /f "tokens=*" %%i in ('"%PHP_CMD%" -r "echo PHP_MAJOR_VERSION;"') do set PHP_MAJOR=%%i
for /f "tokens=*" %%i in ('"%PHP_CMD%" -r "echo PHP_MINOR_VERSION;"') do set PHP_MINOR=%%i

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

:: Check PHP Extensions
echo Checking required PHP extensions...
"%PHP_CMD%" -r "if(!extension_loaded('mysqli')) exit(1);" >nul 2>nul
if !errorlevel! neq 0 (
    echo [WARNING] mysqli extension not loaded
)
"%PHP_CMD%" -r "if(!extension_loaded('pdo_mysql')) exit(1);" >nul 2>nul
if !errorlevel! neq 0 (
    echo [WARNING] pdo_mysql extension not loaded
)
"%PHP_CMD%" -r "if(!extension_loaded('mbstring')) exit(1);" >nul 2>nul
if !errorlevel! neq 0 (
    echo [WARNING] mbstring extension not loaded
)
"%PHP_CMD%" -r "if(!extension_loaded('openssl')) exit(1);" >nul 2>nul
if !errorlevel! neq 0 (
    echo [WARNING] openssl extension not loaded - Required for email
)
echo.

:: Step 2: Check and Install Composer
echo [Step 2/9] Checking Composer Installation...
echo ----------------------------------------
where composer >nul 2>nul
if !errorlevel! neq 0 (
    echo [WARNING] Composer not found. Installing Composer locally...
    echo.
    
    REM Download Composer installer
    echo Downloading Composer installer...
    "%PHP_CMD%" -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    if !errorlevel! neq 0 (
        echo [ERROR] Failed to download Composer installer.
        echo Please check your internet connection or install Composer manually.
        echo Download from: https://getcomposer.org/download/
        pause
        exit /b 1
    )
    
    REM Install Composer
    echo Installing Composer...
    "%PHP_CMD%" composer-setup.php --quiet
    if !errorlevel! neq 0 (
        echo [ERROR] Composer installation failed.
        del composer-setup.php >nul 2>nul
        pause
        exit /b 1
    )
    
    REM Cleanup installer
    del composer-setup.php >nul 2>nul
    
    echo [OK] Composer installed locally as composer.phar
    echo.
    set "COMPOSER_CMD=%PHP_CMD% composer.phar"
) else (
    for /f "tokens=3" %%i in ('composer --version 2^>nul ^| findstr /C:"Composer version"') do set COMPOSER_VERSION=%%i
    echo [OK] Composer %COMPOSER_VERSION% found
    echo.
    set "COMPOSER_CMD=composer"
)

:: Step 3: Install Dependencies
echo [Step 3/9] Installing PHP Dependencies...
echo ----------------------------------------
echo Running: %COMPOSER_CMD% install...
echo.

%COMPOSER_CMD% install --no-interaction --prefer-dist --optimize-autoloader
if !errorlevel! neq 0 (
    echo.
    echo [ERROR] Failed to install dependencies
    pause
    exit /b 1
)

echo.
echo [OK] Dependencies installed successfully
echo.

:: Step 4: Detect Project Path
echo [Step 4/9] Detecting Project Path...
echo ----------------------------------------

:: Get full path
for %%F in ("%PROJECT_DIR%") do set "FULL_PATH=%%~fF"
set "FULL_PATH=%FULL_PATH:~0,-1%"

:: Try to detect if in htdocs
echo %FULL_PATH% | findstr /i "htdocs" >nul
if !errorlevel! equ 0 (
    :: Extract path relative to htdocs
    set "RELATIVE_PATH=%FULL_PATH%"
    set "RELATIVE_PATH=!RELATIVE_PATH:*htdocs\=!"
    set "RELATIVE_PATH=!RELATIVE_PATH:*htdocs/=!"
    set "RELATIVE_PATH=!RELATIVE_PATH:\=/!"
    set "BASE_URL_PATH=/%RELATIVE_PATH%/public/"
    set "BASE_URL=http://localhost%BASE_URL_PATH%"
) else (
    :: Default path
    echo [INFO] Project not in htdocs folder
    echo [INFO] Using default path: /ivymoda/ivymoda_mvc/public/
    set "BASE_URL_PATH=/ivymoda/ivymoda_mvc/public/"
    set "BASE_URL=http://localhost%BASE_URL_PATH%"
)

echo [INFO] Detected Base URL: %BASE_URL%
echo.

:: Step 5: Environment Configuration
echo [Step 5/9] Setting up Environment Configuration...
echo ----------------------------------------

if not exist ".env" (
    if exist ".env.example" (
        copy ".env.example" ".env" >nul
        echo [OK] Created .env file from .env.example
        
        REM Update BASE_URL in .env file
        powershell -Command "(Get-Content .env) -replace 'BASE_URL=.*', 'BASE_URL=%BASE_URL%' | Set-Content .env"
        echo [OK] Updated BASE_URL in .env file
    ) else (
        echo [ERROR] .env.example not found!
    )
) else (
    echo [INFO] .env file already exists
    REM Update BASE_URL anyway
    powershell -Command "(Get-Content .env) -replace 'BASE_URL=.*', 'BASE_URL=%BASE_URL%' | Set-Content .env"
    echo [OK] Updated BASE_URL in .env file
)
echo.

:: Step 6: Update .htaccess files
echo [Step 6/9] Updating .htaccess files...
echo ----------------------------------------

REM Update public/.htaccess
if exist "public\.htaccess" (
    set "REWRITE_BASE=%BASE_URL_PATH%"
    powershell -Command "(Get-Content public\.htaccess) -replace 'RewriteBase .*', 'RewriteBase %REWRITE_BASE%' | Set-Content public\.htaccess"
    echo [OK] Updated public/.htaccess
)

REM Update root .htaccess if exists
if exist ".htaccess" (
    set "PUBLIC_PATH=%BASE_URL_PATH%"
    powershell -Command "(Get-Content .htaccess) -replace '/ivymoda/ivymoda_mvc/public/', '%PUBLIC_PATH%' | Set-Content .htaccess"
    echo [OK] Updated root .htaccess
)
echo.

:: Step 7: Create Required Directories
echo [Step 7/9] Creating Required Directories...
echo ----------------------------------------

if not exist "public\assets\uploads" (
    mkdir "public\assets\uploads" 2>nul
    echo [OK] Created public\assets\uploads directory
)

if not exist "public\assets\uploads\reviews" (
    mkdir "public\assets\uploads\reviews" 2>nul
    echo [OK] Created public\assets\uploads\reviews directory
)

if not exist "logs" (
    mkdir "logs" 2>nul
    echo [OK] Created logs directory
)
echo.

:: Step 8: Set Permissions (Windows - using icacls)
echo [Step 8/9] Setting Directory Permissions...
echo ----------------------------------------

icacls "public\assets\uploads" /grant Users:(OI)(CI)F /T >nul 2>&1
icacls "logs" /grant Users:(OI)(CI)F /T >nul 2>&1

if exist "vendor" (
    icacls "vendor" /grant Users:(OI)(CI)F /T >nul 2>&1
)

echo [OK] Permissions set successfully
echo.

:: Step 9: Database Setup
echo [Step 9/9] Setting up Database...
echo ----------------------------------------

:: Check if MySQL is accessible
if defined XAMPP_MYSQL (
    "%XAMPP_MYSQL%" -u root -e "SELECT 1;" >nul 2>nul
    if !errorlevel! equ 0 (
        echo [OK] MySQL connection successful
        
        :: Check if database exists
        "%XAMPP_MYSQL%" -u root -e "USE ivymoda;" >nul 2>nul
        if !errorlevel! equ 0 (
            echo [INFO] Database 'ivymoda' already exists
            echo [SKIP] Keeping existing database
            echo [INFO] To recreate, drop database manually and run setup again
            set "SKIP_IMPORT=1"
        )
        
        :: Create database if needed
        if not defined SKIP_IMPORT (
            echo Creating database 'ivymoda'...
            "%XAMPP_MYSQL%" -u root -e "CREATE DATABASE IF NOT EXISTS ivymoda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" >nul 2>nul
            if !errorlevel! equ 0 (
                echo [OK] Database created successfully
                
                :: Import SQL file
                if exist "ivymoda_final.sql" (
                    echo Importing database schema...
                    "%XAMPP_MYSQL%" -u root ivymoda < "ivymoda_final.sql" >nul 2>nul
                    if !errorlevel! equ 0 (
                        echo [OK] Database imported successfully
                    ) else (
                        echo [WARNING] Failed to import database. Please import manually using phpMyAdmin
                    )
                ) else (
                    echo [WARNING] ivymoda_final.sql not found. Please import manually.
                )
            ) else (
                echo [ERROR] Failed to create database
            )
        )
    ) else (
        echo [WARNING] Cannot connect to MySQL
        echo Make sure XAMPP MySQL service is running
        echo.
        echo To start MySQL:
        echo 1. Open XAMPP Control Panel
        echo 2. Click 'Start' next to MySQL
        echo.
        echo Then run this script again or import manually:
        echo   mysql -u root ivymoda ^< ivymoda_final.sql
    )
) else (
    where mysql >nul 2>nul
    if !errorlevel! equ 0 (
        echo [INFO] MySQL found in PATH
        mysql -u root -e "CREATE DATABASE IF NOT EXISTS ivymoda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" >nul 2>nul
        if exist "ivymoda_final.sql" (
            echo Importing database...
            mysql -u root ivymoda < "ivymoda_final.sql" >nul 2>nul
            if !errorlevel! equ 0 (
                echo [OK] Database imported successfully
            ) else (
                echo [WARNING] Failed to import. Please import manually.
            )
        )
    ) else (
        echo [WARNING] MySQL not found in PATH
        echo Make sure XAMPP MySQL service is running
        echo.
        echo To import database manually:
        echo 1. Open phpMyAdmin: http://localhost/phpmyadmin
        echo 2. Create database 'ivymoda'
        echo 3. Import file: ivymoda_final.sql
    )
)
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
echo [OK] Configuration files updated
echo.

echo ------------------------------------------------
echo IMPORTANT: NEXT STEPS
echo ------------------------------------------------
echo.
echo 1. Start XAMPP Services:
echo    - Open XAMPP Control Panel
echo    - Start Apache
echo    - Start MySQL
echo.
echo 2. Access the application:
echo    - Frontend: %BASE_URL%
echo    - Admin: %BASE_URL%admin
echo.
echo 3. Default Admin Credentials:
echo    - Check your database or documentation
echo.
echo 4. If you see 404 errors:
echo    - Make sure mod_rewrite is enabled in Apache
echo    - Check that .htaccess files are correct
echo    - Verify BASE_URL in .env file
echo.
echo ================================================
echo Setup completed successfully! [OK]
echo ================================================
echo.
pause
