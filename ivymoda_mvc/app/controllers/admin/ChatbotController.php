<?php
namespace admin;

require_once __DIR__ . '/../../services/GeminiService.php';

/**
 * ChatbotController - Admin Chatbot Controller
 * UC3.47 & UC3.48 - Quản lý chatbot AI và FAQ
 */

class ChatbotController extends \Controller {
    
    private $chatbotFaqModel;
    private $chatbotModel;
    
    public function __construct() {
        $this->chatbotFaqModel = $this->model('ChatbotFaqModel');
        $this->chatbotModel = $this->model('ChatbotModel');
    }
    
    /**
     * Dashboard chatbot
     */
    public function index() {
        // Lấy thống kê
        $faqStats = $this->chatbotFaqModel->getFaqStats();
        $chatbotStats = $this->chatbotModel->getChatbotStats();
        
        $data = [
            'title' => 'Quản lý Chatbot',
            'faq_stats' => $faqStats,
            'chatbot_stats' => $chatbotStats
        ];
        
        $this->view('admin/chatbot/dashboard', $data);
    }
    
    /**
     * Quản lý FAQ
     */
    public function faq() {
        $faqs = $this->chatbotFaqModel->getFaqs(null, null); // Lấy tất cả
        
        $data = [
            'title' => 'Quản lý FAQ',
            'faqs' => $faqs
        ];
        
        $this->view('admin/chatbot/faq', $data);
    }
    
    /**
     * Thêm FAQ
     */
    public function addFaq() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'question' => $_POST['question'],
                'answer' => $_POST['answer'],
                'category' => $_POST['category'],
                'display_order' => $_POST['display_order'] ?? 0,
                'status' => $_POST['status'] ?? 1,
                'help_link' => $_POST['help_link'] ?? null,
                'created_by' => $_SESSION['user_id']
            ];
            
            $result = $this->chatbotFaqModel->addFaq($data);
            
            if ($result) {
                $_SESSION['success'] = 'Thêm FAQ thành công';
            } else {
                $_SESSION['error'] = 'Lỗi khi thêm FAQ';
            }
            
            header('Location: /ivymoda/ivymoda_mvc/public/admin/chatbot/faq');
            exit;
        }
        
        $categories = $this->chatbotFaqModel->getCategories();
        
        $data = [
            'title' => 'Thêm FAQ',
            'categories' => $categories
        ];
        
        $this->view('admin/chatbot/add_faq', $data);
    }
    
    /**
     * Sửa FAQ
     */
    public function editFaq($id) {
        $faq = $this->chatbotFaqModel->getFaqById($id);
        
        if (!$faq) {
            $_SESSION['error'] = 'FAQ không tồn tại';
            header('Location: /ivymoda/ivymoda_mvc/public/admin/chatbot/faq');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'question' => $_POST['question'],
                'answer' => $_POST['answer'],
                'category' => $_POST['category'],
                'display_order' => $_POST['display_order'] ?? 0,
                'status' => $_POST['status'] ?? 1,
                'help_link' => $_POST['help_link'] ?? null
            ];
            
            $result = $this->chatbotFaqModel->updateFaq($id, $data);
            
            if ($result) {
                $_SESSION['success'] = 'Cập nhật FAQ thành công';
            } else {
                $_SESSION['error'] = 'Lỗi khi cập nhật FAQ';
            }
            
            header('Location: /ivymoda/ivymoda_mvc/public/admin/chatbot/faq');
            exit;
        }
        
        $categories = $this->chatbotFaqModel->getCategories();
        
        $data = [
            'title' => 'Sửa FAQ',
            'faq' => $faq,
            'categories' => $categories
        ];
        
        $this->view('admin/chatbot/edit_faq', $data);
    }
    
    /**
     * Xóa FAQ
     */
    public function deleteFaq($id) {
        $result = $this->chatbotFaqModel->deleteFaq($id);
        
        if ($result) {
            $_SESSION['success'] = 'Xóa FAQ thành công';
        } else {
            $_SESSION['error'] = 'Lỗi khi xóa FAQ';
        }
        
        header('Location: /ivymoda/ivymoda_mvc/public/admin/chatbot/faq');
        exit;
    }
    
    /**
     * Cập nhật trạng thái FAQ
     */
    public function toggleFaqStatus($id) {
        $faq = $this->chatbotFaqModel->getFaqById($id);
        
        if ($faq) {
            $newStatus = $faq->status ? 0 : 1;
            $result = $this->chatbotFaqModel->updateStatus($id, $newStatus);
            
            if ($result) {
                $_SESSION['success'] = 'Cập nhật trạng thái thành công';
            } else {
                $_SESSION['error'] = 'Lỗi khi cập nhật trạng thái';
            }
        } else {
            $_SESSION['error'] = 'FAQ không tồn tại';
        }
        
        header('Location: /ivymoda/ivymoda_mvc/public/admin/chatbot/faq');
        exit;
    }
    
    /**
     * Quản lý cấu hình chatbot
     */
    public function config() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Cập nhật các cấu hình
            $configs = [
                'gemini_api_key' => $_POST['gemini_api_key'] ?? '',
                'max_products_suggest' => $_POST['max_products_suggest'] ?? '5',
                'context_max_length' => $_POST['context_max_length'] ?? '2000',
                'response_timeout' => $_POST['response_timeout'] ?? '3000',
                'chatbot_welcome_message' => $_POST['chatbot_welcome_message'] ?? '',
                'enable_faq_mode' => $_POST['enable_faq_mode'] ?? '1',
                'enable_gemini_mode' => $_POST['enable_gemini_mode'] ?? '1',
                'chatbot_position' => $_POST['chatbot_position'] ?? 'bottom-right'
            ];
            
            $success = true;
            foreach ($configs as $key => $value) {
                if (!$this->chatbotModel->updateConfig($key, $value)) {
                    $success = false;
                }
            }
            
            if ($success) {
                $_SESSION['success'] = 'Cập nhật cấu hình thành công';
            } else {
                $_SESSION['error'] = 'Lỗi khi cập nhật cấu hình';
            }
            
            header('Location: /ivymoda/ivymoda_mvc/public/admin/chatbot/config');
            exit;
        }
        
        // Lấy các cấu hình hiện tại
        $configs = [
            'gemini_api_key' => $this->chatbotModel->getConfig('gemini_api_key'),
            'max_products_suggest' => $this->chatbotModel->getConfig('max_products_suggest'),
            'context_max_length' => $this->chatbotModel->getConfig('context_max_length'),
            'response_timeout' => $this->chatbotModel->getConfig('response_timeout'),
            'chatbot_welcome_message' => $this->chatbotModel->getConfig('chatbot_welcome_message'),
            'enable_faq_mode' => $this->chatbotModel->getConfig('enable_faq_mode'),
            'enable_gemini_mode' => $this->chatbotModel->getConfig('enable_gemini_mode'),
            'chatbot_position' => $this->chatbotModel->getConfig('chatbot_position')
        ];
        
        $data = [
            'title' => 'Cấu hình Chatbot',
            'configs' => $configs
        ];
        
        $this->view('admin/chatbot/config', $data);
    }
    
    /**
     * Lịch sử hội thoại
     */
    public function conversations() {
        $page = $_GET['page'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        // Lấy lịch sử hội thoại (cần thêm method vào model)
        $conversations = $this->getConversations($limit, $offset);
        $totalConversations = $this->getTotalConversations();
        
        $data = [
            'title' => 'Lịch sử hội thoại',
            'conversations' => $conversations,
            'current_page' => $page,
            'total_pages' => ceil($totalConversations / $limit),
            'total_conversations' => $totalConversations
        ];
        
        $this->view('admin/chatbot/conversations', $data);
    }
    
    /**
     * Test Gemini AI
     */
    public function testGemini() {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            $apiKey = $this->chatbotModel->getConfig('gemini_api_key');
            
            if (!$apiKey) {
                echo json_encode(['success' => false, 'message' => 'API key không được cấu hình']);
                return;
            }
            
            $geminiService = new GeminiService($apiKey);
            $isConnected = $geminiService->testConnection();
            
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
    
    /**
     * Lấy danh sách hội thoại
     */
    private function getConversations($limit, $offset) {
        $sql = "SELECT c.*, u.fullname, u.email 
                FROM tbl_chatbot_conversation c
                LEFT JOIN users u ON c.user_id = u.id
                ORDER BY c.created_at DESC 
                LIMIT ? OFFSET ?";
        
        return $this->chatbotModel->getAll($sql, [$limit, $offset]);
    }
    
    /**
     * Lấy tổng số hội thoại
     */
    private function getTotalConversations() {
        $sql = "SELECT COUNT(*) as total FROM tbl_chatbot_conversation";
        $result = $this->chatbotModel->getOne($sql);
        return $result ? $result->total : 0;
    }
}