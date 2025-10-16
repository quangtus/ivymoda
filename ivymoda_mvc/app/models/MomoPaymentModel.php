<?php
/**
 * MomoPaymentModel - Xử lý thanh toán Momo
 * 
 * Sử dụng Momo Sandbox API
 */

class MomoPaymentModel extends Model {
    
    // Momo Sandbox Configuration - Load from config
    private $partnerCode;
    private $accessKey;
    private $secretKey;
    private $endpoint;
    private $returnUrl;
    private $notifyUrl;
    
    public function __construct() {
        parent::__construct();
        
        // Load configuration from config.php (now uses .env file)
        $this->partnerCode = defined('MOMO_PARTNER_CODE') ? MOMO_PARTNER_CODE : 'MOMO';
        $this->accessKey = defined('MOMO_ACCESS_KEY') ? MOMO_ACCESS_KEY : 'F8BBA842ECF85';
        $this->secretKey = defined('MOMO_SECRET_KEY') ? MOMO_SECRET_KEY : 'K951B6PE1waDMi640xX08PD3vg6EkVlz';
        $this->endpoint = defined('MOMO_ENDPOINT') ? MOMO_ENDPOINT : 'https://test-payment.momo.vn/v2/gateway/api/create';
        $this->returnUrl = defined('MOMO_RETURN_URL') ? MOMO_RETURN_URL : BASE_URL . 'payment/momoReturn';
        $this->notifyUrl = defined('MOMO_NOTIFY_URL') ? MOMO_NOTIFY_URL : BASE_URL . 'payment/momoNotify';
    }
    
    /**
     * Tạo payment request
     */
    public function createPaymentRequest($orderData) {
        try {
            $requestId = time() . '';
            $orderId = $orderData['order_code'];
            $amount = (int)$orderData['order_total'];
            $orderInfo = "Thanh toán đơn hàng " . $orderId;
            $extraData = "";
            
            // Tạo raw hash
            $rawHash = "accessKey=" . $this->accessKey . 
                      "&amount=" . $amount . 
                      "&extraData=" . $extraData . 
                      "&ipnUrl=" . $this->notifyUrl . 
                      "&orderId=" . $orderId . 
                      "&orderInfo=" . $orderInfo . 
                      "&partnerCode=" . $this->partnerCode . 
                      "&redirectUrl=" . $this->returnUrl . 
                      "&requestId=" . $requestId . 
                      "&requestType=captureWallet";
            
            $signature = hash_hmac('sha256', $rawHash, $this->secretKey);
            
            $data = [
                'partnerCode' => $this->partnerCode,
                'partnerName' => 'IVY moda',
                'storeId' => 'IVY_MODA_STORE',
                'requestId' => $requestId,
                'amount' => $amount,
                'orderId' => $orderId,
                'orderInfo' => $orderInfo,
                'redirectUrl' => $this->returnUrl,
                'ipnUrl' => $this->notifyUrl,
                'lang' => 'vi',
                'extraData' => $extraData,
                'requestType' => 'captureWallet',
                'signature' => $signature
            ];
            
            // Gửi request đến Momo
            $response = $this->sendRequest($data);
            
            if ($response && isset($response['resultCode']) && $response['resultCode'] == 0) {
                return [
                    'success' => true,
                    'payUrl' => $response['payUrl'],
                    'requestId' => $requestId
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $response['message'] ?? 'Lỗi tạo thanh toán Momo'
                ];
            }
            
        } catch (Exception $e) {
            error_log("MomoPaymentModel::createPaymentRequest - Exception: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi hệ thống khi tạo thanh toán'
            ];
        }
    }
    
    /**
     * Verify payment result
     */
    public function verifyPayment($orderId, $requestId, $amount, $orderInfo, $orderType, $transId, $resultCode, $message, $payType, $responseTime, $extraData, $signature) {
        try {
            // Tạo raw hash để verify
            $rawHash = "accessKey=" . $this->accessKey . 
                      "&amount=" . $amount . 
                      "&extraData=" . $extraData . 
                      "&message=" . $message . 
                      "&orderId=" . $orderId . 
                      "&orderInfo=" . $orderInfo . 
                      "&orderType=" . $orderType . 
                      "&partnerCode=" . $this->partnerCode . 
                      "&payType=" . $payType . 
                      "&requestId=" . $requestId . 
                      "&responseTime=" . $responseTime . 
                      "&resultCode=" . $resultCode . 
                      "&transId=" . $transId;
            
            $expectedSignature = hash_hmac('sha256', $rawHash, $this->secretKey);
            
            // Verify signature
            if ($signature !== $expectedSignature) {
                return [
                    'valid' => false,
                    'message' => 'Invalid signature'
                ];
            }
            
            // Kiểm tra result code
            if ($resultCode == 0) {
                return [
                    'valid' => true,
                    'success' => true,
                    'message' => 'Thanh toán thành công',
                    'transId' => $transId
                ];
            } else {
                return [
                    'valid' => true,
                    'success' => false,
                    'message' => $message
                ];
            }
            
        } catch (Exception $e) {
            error_log("MomoPaymentModel::verifyPayment - Exception: " . $e->getMessage());
            return [
                'valid' => false,
                'message' => 'Lỗi hệ thống khi xác thực thanh toán'
            ];
        }
    }
    
    /**
     * Gửi request đến Momo API
     */
    private function sendRequest($data) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->endpoint);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($data))
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode == 200) {
                return json_decode($response, true);
            } else {
                error_log("Momo API Error - HTTP Code: $httpCode, Response: $response");
                return null;
            }
            
        } catch (Exception $e) {
            error_log("MomoPaymentModel::sendRequest - Exception: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Lưu payment log
     */
    public function logPayment($orderId, $requestId, $amount, $status, $response = null, $orderCode = null) {
        try {
            // Map to tbl_momo_transaction per final schema
            // Fields: order_id (nullable), request_id (unique), order_code, amount, result_code, message
            $resultCode = null;
            $message = null;
            if (is_array($response)) {
                $resultCode = isset($response['resultCode']) ? (string)$response['resultCode'] : ($status === 'success' ? '0' : null);
                $message = $response['message'] ?? $status;
            } elseif (is_string($response) && !empty($response)) {
                $message = $response;
            } else {
                $message = $status;
                $resultCode = $status === 'success' ? '0' : null;
            }
            
            $sql = "INSERT INTO tbl_momo_transaction (order_id, request_id, order_code, amount, result_code, message, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            
            return $this->execute($sql, [
                $orderId,
                $requestId,
                $orderCode,
                $amount,
                $resultCode,
                $message
            ]);
        } catch (Exception $e) {
            error_log("MomoPaymentModel::logPayment - Exception: " . $e->getMessage());
            return false;
        }
    }
}
