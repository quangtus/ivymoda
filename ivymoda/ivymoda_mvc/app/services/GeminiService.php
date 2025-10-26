<?php
/**
 * GeminiService - Service tích hợp Gemini AI
 * UC3.47 - Chatbot tư vấn sản phẩm với Gemini AI
 */

class GeminiService {
    
    private $apiKey;
    private $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
    
    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }
    
    /**
     * Gửi request đến Gemini AI
     * @param string $prompt
     * @param array $context
     * @return array
     */
    public function generateResponse($prompt, $context = []) {
        try {
            $contextString = $this->buildContextString($context);
            $fullPrompt = $this->buildPrompt($prompt, $contextString);
            
            $data = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $fullPrompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ],
                'safetySettings' => [
                    [
                        'category' => 'HARM_CATEGORY_HARASSMENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_HATE_SPEECH',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ]
                ]
            ];
            
            $response = $this->makeRequest($data);
            
            if ($response && isset($response['candidates'][0]['content']['parts'][0]['text'])) {
                $aiResponse = $response['candidates'][0]['content']['parts'][0]['text'];
                $aiResponse = $this->sanitizeString($aiResponse);
                
                return [
                    'success' => true,
                    'response' => $aiResponse,
                    'usage' => $response['usageMetadata'] ?? null
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Không thể tạo phản hồi từ Gemini AI'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Lỗi khi gọi Gemini AI: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Xây dựng context string từ dữ liệu
     * @param array $context
     * @return string
     */
    private function buildContextString($context) {
        $contextParts = [];
        
        // Thêm thông tin sản phẩm
        if (isset($context['products']) && !empty($context['products'])) {
            $contextParts[] = "SẢN PHẨM CÓ SẴN:";
            foreach ($context['products'] as $product) {
                $title = $this->getField($product, 'sanpham_tieude', '');
                $title = $this->sanitizeString($title);
                $category = $this->getField($product, 'danhmuc_ten', '');
                $category = $this->sanitizeString($category);
                $priceRaw = $this->getField($product, 'sanpham_gia', 0);
                $price = is_numeric($priceRaw) ? number_format((float)$priceRaw) : $this->sanitizeString((string)$priceRaw);
                
                $contextParts[] = "- {$title} ({$category}) - Giá: {$price}₫";
            }
        }
        
        // Thêm thông tin danh mục
        if (isset($context['categories']) && !empty($context['categories'])) {
            $categories = array_map([$this, 'sanitizeString'], $context['categories']);
            $contextParts[] = "\nDANH MỤC SẢN PHẨM: " . implode(', ', $categories);
        }
        
        // Thêm thông tin màu sắc
        if (isset($context['colors']) && !empty($context['colors'])) {
            $colors = array_map([$this, 'sanitizeString'], $context['colors']);
            $contextParts[] = "\nMÀU SẮC CÓ SẴN: " . implode(', ', $colors);
        }
        
        // Thêm thông tin size
        if (isset($context['sizes']) && !empty($context['sizes'])) {
            $sizes = array_map([$this, 'sanitizeString'], $context['sizes']);
            $contextParts[] = "\nSIZE CÓ SẴN: " . implode(', ', $sizes);
        }
        
        // Thêm sở thích người dùng
        if (isset($context['user_preferences']) && $context['user_preferences']) {
            $prefs = $context['user_preferences'];
            $contextParts[] = "\nSỞ THÍCH NGƯỜI DÙNG:";
            if (!empty($prefs['favorite_colors'])) {
                $colors = $this->sanitizeString($prefs['favorite_colors']);
                $contextParts[] = "- Màu yêu thích: " . $colors;
            }
            if (!empty($prefs['size_preference'])) {
                $size = $this->sanitizeString($prefs['size_preference']);
                $contextParts[] = "- Size thường mặc: " . $size;
            }
            if (!empty($prefs['price_range'])) {
                $price = $this->sanitizeString($prefs['price_range']);
                $contextParts[] = "- Khoảng giá: " . $price;
            }
        }
        
        return implode("\n", $contextParts);
    }
    
    /**
     * Xây dựng prompt hoàn chỉnh
     * @param string $userMessage
     * @param string $contextString
     * @return string
     */
    private function buildPrompt($userMessage, $contextString) {
        $systemPrompt = "Bạn là chatbot tư vấn sản phẩm thời trang của IVY Moda. Nhiệm vụ của bạn là:

1. Tư vấn sản phẩm phù hợp cho khách hàng dựa trên yêu cầu của họ
2. Đưa ra lời khuyên về style, màu sắc, size
3. Gợi ý sản phẩm cụ thể từ danh sách có sẵn
4. Trả lời bằng tiếng Việt, thân thiện và chuyên nghiệp
5. Nếu không có sản phẩm phù hợp, hãy gợi ý sản phẩm tương tự

THÔNG TIN SẢN PHẨM HIỆN CÓ:
" . $this->sanitizeString($contextString) . "

Hãy trả lời câu hỏi của khách hàng một cách hữu ích và gợi ý sản phẩm phù hợp.";

        return $systemPrompt . "\n\nCâu hỏi của khách hàng: " . $this->sanitizeString($userMessage);
    }
    
    /**
     * Gửi request HTTP đến Gemini API
     * @param array $data
     * @return array|null
     */
    private function makeRequest($data) {
        $url = $this->baseUrl . '?key=' . $this->apiKey;
        
        // Sử dụng cURL thay vì file_get_contents
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'User-Agent: IVY-Moda-Chatbot/1.0'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($response === false || !empty($curlError)) {
            throw new Exception('Không thể kết nối đến Gemini API: ' . $curlError);
        }
        
        if ($httpCode !== 200) {
            throw new Exception('HTTP Error ' . $httpCode . ': ' . $response);
        }
        
        $decodedResponse = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Lỗi khi decode response từ Gemini API: ' . json_last_error_msg());
        }
        
        return $decodedResponse;
    }
    
    /**
     * Kiểm tra kết nối API
     * @return bool
     */
    public function testConnection() {
        try {
            $testPrompt = "Xin chào, bạn có thể giúp tôi tư vấn sản phẩm không?";
            $result = $this->generateResponse($testPrompt);
            return $result['success'];
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Truy xuất trường an toàn cho cả array hoặc stdClass
     */
    private function getField($item, $key, $default = null) {
        if (is_array($item)) {
            return isset($item[$key]) ? $item[$key] : $default;
        }
        if (is_object($item)) {
            return isset($item->$key) ? $item->$key : $default;
        }
        return $default;
    }

    /**
     * Sanitize string để tránh lỗi JSON
     * @param string $string
     * @return string
     */
    private function sanitizeString($string) {
        if (!is_string($string)) {
            return $string;
        }
        
        // Remove control characters
        $string = preg_replace('/[\x00-\x1F\x80-\x9F]/u', '', $string);
        
        // Trim whitespace
        $string = trim($string);
        
        // Convert to valid UTF-8
        if (!mb_check_encoding($string, 'UTF-8')) {
            $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        }
        
        return $string;
    }
}
