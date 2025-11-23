@echo off
title IVY MODA SETUP
color 0A
cls

REM Save current code page to restore later
for /f "tokens=2 delims=:" %%a in ('chcp') do set "ORIGINAL_CP=%%a"
set "ORIGINAL_CP=%ORIGINAL_CP:~1%"

REM Set UTF-8 for proper character display during script execution
chcp 65001 >nul 2>&1

echo ==========================================
echo IVY MODA - SIMPLE SETUP
echo ==========================================

REM 1. Find PHP
set "PHP_CMD="
set "MYSQL_CMD="

if exist "C:\xampp\php\php.exe" (
    set "PHP_CMD=C:\xampp\php\php.exe"
    set "MYSQL_CMD=C:\xampp\mysql\bin\mysql.exe"
    echo [INFO] Found XAMPP in C:\xampp
)

if not defined PHP_CMD (
    if exist "D:\xampp\php\php.exe" (
        set "PHP_CMD=D:\xampp\php\php.exe"
        set "MYSQL_CMD=D:\xampp\mysql\bin\mysql.exe"
        echo [INFO] Found XAMPP in D:\xampp
    )
)

if not defined PHP_CMD (
    where php >nul 2>nul
    if %errorlevel% equ 0 (
        set "PHP_CMD=php"
        set "MYSQL_CMD=mysql"
        echo [INFO] Found PHP in System PATH
    )
)

if not defined PHP_CMD (
    echo [ERROR] Could not find PHP!
    echo Please install XAMPP in C:\xampp or D:\xampp
    pause
    exit /b
)

REM 2. Check Composer
echo.
echo [INFO] Checking Composer...

REM Delete old/corrupted composer.phar if exists to ensure fresh install
if exist "composer.phar" del "composer.phar"

where composer >nul 2>nul
if %errorlevel% equ 0 (
    set "COMPOSER_CMD=composer"
    echo [INFO] Using global Composer
) else (
    echo [INFO] Composer not found globally. Downloading...
    "%PHP_CMD%" -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    "%PHP_CMD%" composer-setup.php
    del composer-setup.php
    set "COMPOSER_CMD=%PHP_CMD% composer.phar"
)

REM 3. Install Dependencies
echo.
echo [INFO] Installing Dependencies...
REM Ensure code page stays consistent
chcp 65001 >nul 2>&1
call %COMPOSER_CMD% install --no-interaction
REM Restore code page after Composer
chcp 65001 >nul 2>&1
if %errorlevel% neq 0 (
    echo [WARNING] Composer install had issues.
    pause
)

REM 4. Create Directories
if not exist "logs" mkdir logs
if not exist "public\assets\uploads" mkdir "public\assets\uploads"
if not exist ".env" (
    if exist ".env.example" copy ".env.example" ".env"
)

REM 5. Database
echo.
echo [INFO] Setting up Database...
REM Ensure code page stays consistent
chcp 65001 >nul 2>&1
if exist "%MYSQL_CMD%" (
    "%MYSQL_CMD%" -u root -e "CREATE DATABASE IF NOT EXISTS ivymoda CHARACTER SET utf8mb4;"
    if exist "ivymoda_final.sql" (
        "%MYSQL_CMD%" -u root ivymoda < "ivymoda_final.sql"
        echo [INFO] Database imported.
    )
) else (
    echo [WARNING] MySQL not found. Please import ivymoda_final.sql manually.
)
REM Restore code page after MySQL
chcp 65001 >nul 2>&1

echo.
echo ==========================================
echo SETUP FINISHED
echo ==========================================

REM Restore original code page to fix font display
chcp %ORIGINAL_CP% >nul 2>&1

pause

