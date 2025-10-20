<?php
/**
 * ChatbotController - Frontend Chatbot Controller
 * UC3.47 - Chatbot tư vấn sản phẩm với Gemini AI
 * UC3.48 - Chatbot hướng dẫn sử dụng hệ thống (FAQ)
 */

class ChatbotController extends Controller {
    
    private $chatbotFaqModel;
    private $chatbotModel;
    private $geminiService;
    
    public function __construct() {
        $this->chatbotFaqModel = new ChatbotFaqModel();
        $this->chatbotModel = new ChatbotModel();
        
        // Khởi tạo Gemini Service
        $apiKey = $this->chatbotModel->getConfig('gemini_api_key');
        if ($apiKey) {
            $this->geminiService = new GeminiService($apiKey);
        }
    }
    
    /**
     * Hiển thị chatbot widget
     */
    public function index() {
        // Lấy danh sách FAQ active
        $faqs = $this->chatbotFaqModel->getFaqs();
        
        // Lấy categories
        $categories = $this->chatbotFaqModel->getCategories();
        
        $data = [
            'title' => 'Chatbot Hỗ trợ',
            'faqs' => $faqs,
            'categories' => $categories
        ];
        
        $this->view('chatbot/index', $data);
    }
    
    /**
     * Lấy danh sách FAQ theo AJAX
     */
    public function getFaqs() {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            $category = $_GET['category'] ?? null;
            $faqs = $this->chatbotFaqModel->getFaqs($category);
            
            // Format data cho frontend
            $formattedFaqs = [];
            foreach ($faqs as $faq) {
                $formattedFaqs[] = [
                    'id' => $faq->faq_id,
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                    'category' => $faq->category,
                    'help_link' => $faq->help_link
                ];
            }
            
            echo json_encode([
                'success' => true,
                'faqs' => $formattedFaqs,
                'total' => count($formattedFaqs)
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi tải FAQ: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Tìm kiếm FAQ theo từ khóa
     */
    public function searchFaqs() {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            $keyword = $_GET['keyword'] ?? '';
            
            if (empty($keyword)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Vui lòng nhập từ khóa tìm kiếm'
                ]);
                return;
            }
            
            $faqs = $this->chatbotFaqModel->searchFaqs($keyword);
            
            // Format data cho frontend
            $formattedFaqs = [];
            foreach ($faqs as $faq) {
                $formattedFaqs[] = [
                    'id' => $faq->faq_id,
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                    'category' => $faq->category,
                    'help_link' => $faq->help_link
                ];
            }
            
            echo json_encode([
                'success' => true,
                'faqs' => $formattedFaqs,
                'total' => count($formattedFaqs),
                'keyword' => $keyword
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi tìm kiếm FAQ: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Lấy FAQ theo ID
     */
    public function getFaqById() {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            $faqId = $_GET['id'] ?? 0;
            
            if (!$faqId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID FAQ không hợp lệ'
                ]);
                return;
            }
            
            $faq = $this->chatbotFaqModel->getFaqById($faqId);
            
            if (!$faq) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Không tìm thấy FAQ'
                ]);
                return;
            }
            
            echo json_encode([
                'success' => true,
                'faq' => [
                    'id' => $faq->faq_id,
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                    'category' => $faq->category,
                    'help_link' => $faq->help_link
                ]
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi lấy FAQ: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Lấy danh sách categories
     */
    public function getCategories() {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            $categories = $this->chatbotFaqModel->getCategories();
            
            $formattedCategories = [];
            foreach ($categories as $category) {
                $formattedCategories[] = $category->category;
            }
            
            echo json_encode([
                'success' => true,
                'categories' => $formattedCategories
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi lấy danh mục: ' . $e->getMessage()
            ]);
        }
    }
    
    // ============================================
    // UC3.47 - CHATBOT TƯ VẤN SẢN PHẨM VỚI GEMINI AI
    // ============================================
    
    /**
     * Xử lý tin nhắn từ người dùng với Gemini AI
     */
    public function chatWithAI() {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            // Kiểm tra method
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                return;
            }
            
            // Lấy dữ liệu từ POST
            $input = json_decode(file_get_contents('php://input'), true);
            $userMessage = $input['message'] ?? '';
            $sessionId = $input['session_id'] ?? session_id();
            $userId = $_SESSION['user_id'] ?? null;
            
            if (empty($userMessage)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập tin nhắn']);
                return;
            }
            
            // Kiểm tra Gemini Service
            if (!$this->geminiService) {
                echo json_encode(['success' => false, 'message' => 'Chatbot AI tạm thời không khả dụng']);
                return;
            }
            
            // Lấy context từ database
            $context = $this->buildContext($userId);
            
            // Đo thời gian phản hồi
            $startTime = microtime(true);
            
            // Gọi Gemini AI
            $aiResponse = $this->geminiService->generateResponse($userMessage, $context);
            
            $responseTime = round((microtime(true) - $startTime) * 1000);
            
            if (!$aiResponse['success']) {
                echo json_encode([
                    'success' => false,
                    'message' => $aiResponse['error'] ?? 'Chatbot AI tạm thời không khả dụng'
                ]);
                return;
            }
            
            // Lưu lịch sử hội thoại
            $this->chatbotModel->saveConversation([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'user_message' => $userMessage,
                'bot_response' => $aiResponse['response'],
                'context_data' => json_encode($context),
                'suggested_products' => json_encode($context['products'] ?? []),
                'response_time' => $responseTime,
                'is_from_faq' => 0
            ]);
            
            echo json_encode([
                'success' => true,
                'response' => $aiResponse['response'],
                'suggested_products' => $context['products'] ?? [],
                'response_time' => $responseTime
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Xây dựng context cho Gemini AI
     * @param int|null $userId
     * @return array
     */
    private function buildContext($userId = null) {
        $context = [];
        
        // Lấy sản phẩm bán chạy
        $context['products'] = $this->chatbotModel->getPopularProducts(5);
        
        // Lấy danh mục
        $categories = $this->chatbotModel->getAvailableCategories();
        $context['categories'] = array_column($categories, 'danhmuc_ten');
        
        // Lấy màu sắc
        $colors = $this->chatbotModel->getAvailableColors();
        $context['colors'] = array_column($colors, 'color_ten');
        
        // Lấy size
        $sizes = $this->chatbotModel->getAvailableSizes();
        $context['sizes'] = array_column($sizes, 'size_ten');
        
        // Lấy sở thích người dùng nếu có
        if ($userId) {
            $preferences = $this->chatbotModel->getUserPreferences($userId);
            if ($preferences) {
                $context['user_preferences'] = [
                    'favorite_colors' => $preferences->favorite_colors,
                    'favorite_categories' => $preferences->favorite_categories,
                    'size_preference' => $preferences->size_preference,
                    'price_range' => $preferences->price_range,
                    'skin_tone' => $preferences->skin_tone,
                    'height' => $preferences->height
                ];
            }
        }
        
        return $context;
    }
    
    /**
     * Lưu sở thích người dùng
     */
    public function saveUserPreferences() {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                return;
            }
            
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
                return;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $input['user_id'] = $userId;
            
            $success = $this->chatbotModel->saveUserPreferences($input);
            
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Đã lưu sở thích']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu sở thích']);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Lấy lịch sử hội thoại
     */
    public function getConversationHistory() {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            $sessionId = $_GET['session_id'] ?? session_id();
            $limit = $_GET['limit'] ?? 10;
            
            $history = $this->chatbotModel->getConversationHistory($sessionId, $limit);
            
            // Format dữ liệu
            $formattedHistory = [];
            foreach ($history as $conversation) {
                $formattedHistory[] = [
                    'user_message' => $conversation->user_message,
                    'bot_response' => $conversation->bot_response,
                    'suggested_products' => json_decode($conversation->suggested_products, true) ?? [],
                    'response_time' => $conversation->response_time,
                    'is_from_faq' => $conversation->is_from_faq,
                    'created_at' => $conversation->created_at
                ];
            }
            
            echo json_encode([
                'success' => true,
                'history' => $formattedHistory
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi lấy lịch sử: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Test kết nối Gemini AI
     */
    public function testGemini() {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            if (!$this->geminiService) {
                echo json_encode(['success' => false, 'message' => 'Gemini Service không được khởi tạo']);
                return;
            }
            
            $isConnected = $this->geminiService->testConnection();
            
            echo json_encode([
                'success' => $isConnected,
                'message' => $isConnected ? 'Kết nối Gemini AI thành công' : 'Không thể kết nối Gemini AI'
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi test Gemini AI: ' . $e->getMessage()
            ]);
        }
    }
}
