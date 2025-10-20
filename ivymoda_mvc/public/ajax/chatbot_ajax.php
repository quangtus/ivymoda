<?php
/**
 * Chatbot AJAX Endpoint - UC3.47 & UC3.48
 * Xử lý các request AJAX cho chatbot FAQ và AI
 */

// ============================================
// 1. INITIALIZATION - Phải đúng thứ tự!
// ============================================

// 1A. DISABLE ERROR OUTPUT NGAY LẬP TỨC
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__DIR__, 2) . '/logs/chatbot_ajax_error.log');

// 1B. START OUTPUT BUFFERING ĐẦU TIÊN
ob_start();

// 1C. SET HEADER JSON
header('Content-Type: application/json; charset=utf-8');

// 1D. DEFINE CONSTANTS
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__, 2) . '/');
}

// 1E. START SESSION
session_start();

// 1F. ERROR HANDLER - Convert lỗi PHP thành JSON
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    error_log("[$errno] $errstr in $errfile:$errline");
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống',
        'debug' => (ENVIRONMENT === 'development' ? "$errstr in $errfile:$errline" : '')
    ], JSON_UNESCAPED_UNICODE);
    exit;
});

// 1G. EXCEPTION HANDLER
set_exception_handler(function($e) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    error_log("Exception: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống',
        'debug' => (ENVIRONMENT === 'development' ? $e->getMessage() : '')
    ], JSON_UNESCAPED_UNICODE);
    exit;
});

// Define ENVIRONMENT if not defined
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'development');
}

// ============================================
// 2. LOAD DEPENDENCIES
// ============================================

try {
    require_once ROOT_PATH . 'config/config.php';
    require_once ROOT_PATH . 'app/core/Database.php';
    require_once ROOT_PATH . 'app/core/Model.php';
    require_once ROOT_PATH . 'app/models/ChatbotFaqModel.php';
    require_once ROOT_PATH . 'app/models/ChatbotModel.php';
    require_once ROOT_PATH . 'app/services/GeminiService.php';
} catch (Exception $e) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    error_log('Load Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi tải dependencies'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ============================================
// 3. REQUEST VALIDATION
// ============================================

if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    echo json_encode(['success' => false, 'message' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

// ============================================
// 4. INITIALIZE MODELS
// ============================================

try {
    $chatbotFaqModel = new ChatbotFaqModel();
    $chatbotModel = new ChatbotModel();
} catch (Exception $e) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    error_log('Model init error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khởi tạo models'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ============================================
// 5. GET ACTION
// ============================================

$action = $_REQUEST['action'] ?? '';

// Nếu POST, thử lấy từ JSON body
if (empty($action) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
}

// ============================================
// 6. ROUTE TO HANDLER
// ============================================

try {
    switch ($action) {
        case 'get_faqs':
            getFaqs($chatbotFaqModel);
            break;
        case 'search_faqs':
            searchFaqs($chatbotFaqModel);
            break;
        case 'get_faq_by_id':
            getFaqById($chatbotFaqModel);
            break;
        case 'get_categories':
            getCategories($chatbotFaqModel);
            break;
        case 'chat_with_ai':
            chatWithAI($chatbotModel);
            break;
        case 'save_user_preferences':
            saveUserPreferences($chatbotModel);
            break;
        case 'get_conversation_history':
            getConversationHistory($chatbotModel);
            break;
        case 'test_gemini':
            testGemini($chatbotModel);
            break;
        default:
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            echo json_encode([
                'success' => false,
                'message' => 'Action không hợp lệ: ' . $action
            ], JSON_UNESCAPED_UNICODE);
            exit;
    }
} catch (Exception $e) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    error_log('Handler error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi xử lý yêu cầu'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ============================================
// HANDLER FUNCTIONS
// ============================================

function cleanOutput() {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
}

function sendJSON($data) {
    cleanOutput();
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * GET FAQs
 */
function getFaqs($chatbotFaqModel) {
    $category = $_GET['category'] ?? null;
    $status = $_GET['status'] ?? 1;
    
    $faqs = $chatbotFaqModel->getFaqs($category, (int)$status);
    
    $formattedFaqs = [];
    foreach ($faqs as $faq) {
        $formattedFaqs[] = [
            'id' => $faq->faq_id,
            'question' => $faq->question,
            'answer' => $faq->answer,
            'category' => $faq->category,
            'display_order' => $faq->display_order,
            'help_link' => $faq->help_link,
            'created_at' => $faq->created_at
        ];
    }
    
    sendJSON([
        'success' => true,
        'faqs' => $formattedFaqs,
        'total' => count($formattedFaqs),
        'category' => $category,
        'status' => $status
    ]);
}

/**
 * SEARCH FAQs
 */
function searchFaqs($chatbotFaqModel) {
    $keyword = $_GET['keyword'] ?? '';
    
    if (empty($keyword)) {
        sendJSON([
            'success' => false,
            'message' => 'Vui lòng nhập từ khóa'
        ]);
    }
    
    $faqs = $chatbotFaqModel->searchFaqs($keyword);
    
    $formattedFaqs = [];
    foreach ($faqs as $faq) {
        $formattedFaqs[] = [
            'id' => $faq->faq_id,
            'question' => $faq->question,
            'answer' => $faq->answer,
            'category' => $faq->category,
            'display_order' => $faq->display_order,
            'help_link' => $faq->help_link,
            'created_at' => $faq->created_at
        ];
    }
    
    sendJSON([
        'success' => true,
        'faqs' => $formattedFaqs,
        'total' => count($formattedFaqs),
        'keyword' => $keyword
    ]);
}

/**
 * GET FAQ BY ID
 */
function getFaqById($chatbotFaqModel) {
    $faqId = $_GET['id'] ?? 0;
    
    if (!$faqId) {
        sendJSON([
            'success' => false,
            'message' => 'ID FAQ không hợp lệ'
        ]);
    }
    
    $faq = $chatbotFaqModel->getFaqById($faqId);
    
    if (!$faq) {
        sendJSON([
            'success' => false,
            'message' => 'Không tìm thấy FAQ'
        ]);
    }
    
    sendJSON([
        'success' => true,
        'faq' => [
            'id' => $faq->faq_id,
            'question' => $faq->question,
            'answer' => $faq->answer,
            'category' => $faq->category,
            'display_order' => $faq->display_order,
            'status' => $faq->status,
            'help_link' => $faq->help_link,
            'created_at' => $faq->created_at,
            'updated_at' => $faq->updated_at
        ]
    ]);
}

/**
 * GET CATEGORIES
 */
function getCategories($chatbotFaqModel) {
    $categories = $chatbotFaqModel->getCategories();
    
    $formattedCategories = [];
    foreach ($categories as $category) {
        $formattedCategories[] = $category->category;
    }
    
    sendJSON([
        'success' => true,
        'categories' => $formattedCategories,
        'total' => count($formattedCategories)
    ]);
}

/**
 * CHAT WITH AI - UC3.47
 */
function chatWithAI($chatbotModel) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJSON(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    $userMessage = trim($input['message'] ?? '');
    $sessionId = trim($input['session_id'] ?? session_id());
    $userId = $_SESSION['user_id'] ?? null;
    
    if (empty($userMessage)) {
        sendJSON(['success' => false, 'message' => 'Vui lòng nhập tin nhắn']);
    }
    
    $apiKey = $chatbotModel->getConfig('gemini_api_key');
    if (!$apiKey) {
        sendJSON(['success' => false, 'message' => 'Chatbot AI tạm không khả dụng']);
    }
    
    $startTime = microtime(true);
    
    try {
        $geminiService = new GeminiService($apiKey);
        $context = buildContext($chatbotModel, $userId);
        $aiResponse = $geminiService->generateResponse($userMessage, $context);
        
        $responseTime = round((microtime(true) - $startTime) * 1000);
        
        if (!$aiResponse['success']) {
            sendJSON([
                'success' => false,
                'message' => $aiResponse['error'] ?? 'Không thể tạo phản hồi'
            ]);
        }
        
        // Lưu hội thoại
        $chatbotModel->saveConversation([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'user_message' => $userMessage,
            'bot_response' => $aiResponse['response'],
            'context_data' => json_encode($context, JSON_UNESCAPED_UNICODE),
            'suggested_products' => json_encode($context['products'] ?? [], JSON_UNESCAPED_UNICODE),
            'response_time' => $responseTime,
            'is_from_faq' => 0
        ]);
        
        // Chuẩn hóa suggested_products: đảm bảo là mảng các bản ghi dạng array
        $productsToReturn = [];
        if (isset($context['products']) && is_array($context['products'])) {
            foreach ($context['products'] as $p) {
                if (is_object($p)) {
                    $productsToReturn[] = get_object_vars($p);
                } elseif (is_array($p)) {
                    $productsToReturn[] = $p;
                }
            }
        }
        
        sendJSON([
            'success' => true,
            'response' => $aiResponse['response'],
            'suggested_products' => $productsToReturn,
            'response_time' => $responseTime
        ]);
        
    } catch (Exception $e) {
        error_log('Chat AI Error: ' . $e->getMessage());
        sendJSON([
            'success' => false,
            'message' => 'Lỗi khi xử lý yêu cầu'
        ]);
    }
}

/**
 * BUILD CONTEXT FOR AI
 */
function buildContext($chatbotModel, $userId = null) {
    $context = [];
    
    try {
        // Get products and convert to array if needed
        $products = $chatbotModel->getPopularProducts(5) ?? [];
        $context['products'] = [];
        if (is_array($products)) {
            $context['products'] = $products;
        } elseif (is_object($products)) {
            // If object, convert to array
            $context['products'] = (array)$products;
        }
        
        // Get categories - returns array of objects
        $categories = $chatbotModel->getAvailableCategories() ?? [];
        $context['categories'] = [];
        if (is_array($categories) && count($categories) > 0) {
            foreach ($categories as $cat) {
                if (is_object($cat) && isset($cat->danhmuc_ten)) {
                    $context['categories'][] = $cat->danhmuc_ten;
                }
            }
        }
        
        // Get colors - returns array of objects
        $colors = $chatbotModel->getAvailableColors() ?? [];
        $context['colors'] = [];
        if (is_array($colors) && count($colors) > 0) {
            foreach ($colors as $col) {
                if (is_object($col) && isset($col->color_ten)) {
                    $context['colors'][] = $col->color_ten;
                }
            }
        }
        
        // Get sizes - returns array of objects
        $sizes = $chatbotModel->getAvailableSizes() ?? [];
        $context['sizes'] = [];
        if (is_array($sizes) && count($sizes) > 0) {
            foreach ($sizes as $size) {
                if (is_object($size) && isset($size->size_ten)) {
                    $context['sizes'][] = $size->size_ten;
                }
            }
        }
        
        // Get user preferences if logged in
        if ($userId) {
            $prefs = $chatbotModel->getUserPreferences($userId);
            if ($prefs) {
                $context['user_preferences'] = [
                    'favorite_colors' => $prefs->favorite_colors ?? '',
                    'favorite_categories' => $prefs->favorite_categories ?? '',
                    'size_preference' => $prefs->size_preference ?? '',
                    'price_range' => $prefs->price_range ?? '',
                    'skin_tone' => $prefs->skin_tone ?? '',
                    'height' => $prefs->height ?? ''
                ];
            }
        }
    } catch (Exception $e) {
        error_log('Build Context Error: ' . $e->getMessage());
        // Return context as is, even if incomplete
    }
    
    return $context;
}

/**
 * SAVE USER PREFERENCES
 */
function saveUserPreferences($chatbotModel) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJSON(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    }
    
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        sendJSON(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    $input['user_id'] = $userId;
    
    $success = $chatbotModel->saveUserPreferences($input);
    
    sendJSON([
        'success' => $success,
        'message' => $success ? 'Đã lưu sở thích' : 'Lỗi khi lưu sở thích'
    ]);
}

/**
 * GET CONVERSATION HISTORY
 */
function getConversationHistory($chatbotModel) {
    $sessionId = $_GET['session_id'] ?? session_id();
    $limit = $_GET['limit'] ?? 10;
    
    try {
        $history = $chatbotModel->getConversationHistory($sessionId, $limit);
        
        $formattedHistory = [];
        foreach ($history as $conversation) {
            $formattedHistory[] = [
                'user_message' => $conversation->user_message ?? '',
                'bot_response' => $conversation->bot_response ?? '',
                'suggested_products' => json_decode($conversation->suggested_products ?? '[]', true),
                'response_time' => $conversation->response_time ?? 0,
                'is_from_faq' => $conversation->is_from_faq ?? 0,
                'created_at' => $conversation->created_at ?? ''
            ];
        }
        
        sendJSON([
            'success' => true,
            'history' => $formattedHistory
        ]);
    } catch (Exception $e) {
        error_log('Get History Error: ' . $e->getMessage());
        sendJSON([
            'success' => false,
            'message' => 'Lỗi khi lấy lịch sử'
        ]);
    }
}

/**
 * TEST GEMINI CONNECTION
 */
function testGemini($chatbotModel) {
    try {
        $apiKey = $chatbotModel->getConfig('gemini_api_key');
        if (!$apiKey) {
            sendJSON(['success' => false, 'message' => 'API key chưa được cấu hình']);
        }
        
        $geminiService = new GeminiService($apiKey);
        $isConnected = $geminiService->testConnection();
        
        sendJSON([
            'success' => $isConnected,
            'message' => $isConnected 
                ? 'Kết nối Gemini AI thành công' 
                : 'Không thể kết nối Gemini AI'
        ]);
    } catch (Exception $e) {
        error_log('Test Gemini Error: ' . $e->getMessage());
        sendJSON([
            'success' => false,
            'message' => 'Lỗi khi test Gemini AI'
        ]);
    }
}
