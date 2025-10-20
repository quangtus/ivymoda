#!/bin/bash
# 🔍 Verification Script for Chatbot Fixes
# Kiểm tra tất cả các sửa chữa đã được áp dụng

echo "==============================================="
echo "🔍 CHATBOT FIXES VERIFICATION"
echo "==============================================="
echo ""

PROJECT_PATH="/xampp/htdocs/ivymoda/ivymoda_mvc"

echo "📁 Checking files..."
echo ""

# 1. Check chatbot_ajax.php
echo "1️⃣  File: public/ajax/chatbot_ajax.php"
if grep -q "JSON_UNESCAPED_UNICODE" "$PROJECT_PATH/public/ajax/chatbot_ajax.php"; then
    echo "   ✅ JSON_UNESCAPED_UNICODE found"
else
    echo "   ❌ JSON_UNESCAPED_UNICODE NOT found"
fi

if grep -q "sanitizeArray" "$PROJECT_PATH/public/ajax/chatbot_ajax.php"; then
    echo "   ✅ sanitizeArray function found"
else
    echo "   ❌ sanitizeArray function NOT found"
fi

if grep -q "set_error_handler" "$PROJECT_PATH/public/ajax/chatbot_ajax.php"; then
    echo "   ✅ Error handler found"
else
    echo "   ❌ Error handler NOT found"
fi
echo ""

# 2. Check GeminiService.php
echo "2️⃣  File: app/services/GeminiService.php"
if grep -q "sanitizeString" "$PROJECT_PATH/app/services/GeminiService.php"; then
    echo "   ✅ sanitizeString method found"
else
    echo "   ❌ sanitizeString method NOT found"
fi

if grep -q "JSON_UNESCAPED_UNICODE" "$PROJECT_PATH/app/services/GeminiService.php"; then
    echo "   ✅ JSON_UNESCAPED_UNICODE found"
else
    echo "   ❌ JSON_UNESCAPED_UNICODE NOT found"
fi

if grep -q "mb_check_encoding" "$PROJECT_PATH/app/services/GeminiService.php"; then
    echo "   ✅ UTF-8 encoding check found"
else
    echo "   ❌ UTF-8 encoding check NOT found"
fi
echo ""

# 3. Check test file
echo "3️⃣  File: public/test-chatbot-fixed.html"
if [ -f "$PROJECT_PATH/public/test-chatbot-fixed.html" ]; then
    echo "   ✅ test-chatbot-fixed.html exists"
else
    echo "   ❌ test-chatbot-fixed.html does NOT exist"
fi
echo ""

# 4. Check documentation
echo "4️⃣  Documentation files"
if [ -f "$PROJECT_PATH/CHATBOT_FIX_GUIDE.md" ]; then
    echo "   ✅ CHATBOT_FIX_GUIDE.md exists"
else
    echo "   ❌ CHATBOT_FIX_GUIDE.md does NOT exist"
fi

if [ -f "$PROJECT_PATH/CHATBOT_SETUP.sql" ]; then
    echo "   ✅ CHATBOT_SETUP.sql exists"
else
    echo "   ❌ CHATBOT_SETUP.sql does NOT exist"
fi
echo ""

# 5. Check PHP syntax
echo "5️⃣  PHP Syntax Check"
php -l "$PROJECT_PATH/public/ajax/chatbot_ajax.php" > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "   ✅ chatbot_ajax.php syntax OK"
else
    echo "   ❌ chatbot_ajax.php has syntax errors"
    php -l "$PROJECT_PATH/public/ajax/chatbot_ajax.php"
fi

php -l "$PROJECT_PATH/app/services/GeminiService.php" > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "   ✅ GeminiService.php syntax OK"
else
    echo "   ❌ GeminiService.php has syntax errors"
    php -l "$PROJECT_PATH/app/services/GeminiService.php"
fi
echo ""

echo "==============================================="
echo "✅ Verification Complete!"
echo "==============================================="
echo ""
echo "📋 Next Steps:"
echo "1. Import CHATBOT_SETUP.sql to your database"
echo "2. Update Gemini API Key in database"
echo "3. Open: http://localhost/ivymoda/ivymoda_mvc/public/test-chatbot-fixed.html"
echo "4. Run all tests"
echo ""
