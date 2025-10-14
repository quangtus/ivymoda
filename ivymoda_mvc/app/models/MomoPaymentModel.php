<?php
/**
 * MomoPaymentModel - Xử lý thanh toán Momo
 * 
 * Sử dụng Momo Sandbox API
 */

class MomoPaymentModel extends Model {
    
    // Momo Sandbox Configuration
    private $partnerCode = 'MOMO5RGX20191128';
    private $accessKey = 'klm05TvNBzhg7h7j';
    private $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
    private $endpoint = 'https://test-payment.momo.vn/v2/gateway/api/create';
    private $returnUrl = '';
    private $notifyUrl = '';
    
    public function __construct() {
        parent::__construct();
        $this->returnUrl = BASE_URL . 'payment/momo/return';
        $this->notifyUrl = BASE_URL . 'payment/momo/notify';
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
    public function logPayment($orderId, $requestId, $amount, $status, $response = null) {
        try {
            $sql = "INSERT INTO payment_logs (order_id, request_id, amount, status, response, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            
            return $this->execute($sql, [
                $orderId,
                $requestId,
                $amount,
                $status,
                $response ? json_encode($response) : null
            ]);
        } catch (Exception $e) {
            error_log("MomoPaymentModel::logPayment - Exception: " . $e->getMessage());
            return false;
        }
    }
}
