@echo off
REM Quick verification script for the slider fix
REM Run this after fixing chatbot.php to verify everything works

echo.
echo ========================================
echo   SLIDER FIX VERIFICATION SCRIPT
echo ========================================
echo.

echo [1/4] Checking if slider.js exists...
if exist "public\assets\js\slider.js" (
    echo     [OK] slider.js found
) else (
    echo     [ERROR] slider.js NOT FOUND!
    pause
    exit /b 1
)

echo.
echo [2/4] Checking slider.js file size...
for %%A in ("public\assets\js\slider.js") do set size=%%~zA
echo     File size: %size% bytes
if %size% LSS 1000 (
    echo     [WARNING] File seems small, might be corrupted
) else (
    echo     [OK] File size looks good
)

echo.
echo [3/4] Checking for ROOT_URL references in chatbot.php...
findstr /C:"ROOT_URL" "app\views\shared\frontend\chatbot.php" >nul 2>&1
if errorlevel 1 (
    echo     [OK] No ROOT_URL found ^(fixed!^)
) else (
    echo     [ERROR] ROOT_URL still exists in chatbot.php!
    pause
    exit /b 1
)

echo.
echo [4/4] Checking for ROOT_URL in all PHP files...
findstr /S /C:"ROOT_URL" "app\*.php" >nul 2>&1
if errorlevel 1 (
    echo     [OK] No ROOT_URL references found anywhere
) else (
    echo     [WARNING] Found ROOT_URL in some files, check manually
    findstr /S /N /C:"ROOT_URL" "app\*.php"
)

echo.
echo ========================================
echo   VERIFICATION COMPLETE
echo ========================================
echo.
echo Next steps:
echo   1. Hard refresh browser ^(Ctrl+Shift+R^)
echo   2. Open Console ^(F12^)
echo   3. Look for these logs:
echo      - "Footer loaded"
echo      - "Slider.js loaded - Version 5.0"
echo      - "Slider initialized successfully"
echo   4. Check Network tab for slider.js?v=7 ^(should be 200^)
echo.
echo If you still see errors, check:
echo   C:\xampp\apache\logs\error.log
echo.
pause
