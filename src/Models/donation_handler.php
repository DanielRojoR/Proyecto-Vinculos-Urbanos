<?php

#IMPORTANTE, NO OLVIDAR CONFIGURAR WEBPAY PARA PRODUCCION CUANDO SE LANCE LA PAGINA

// config/transbank.php
require_once __DIR__ . '/../../vendor/autoload.php';
// require_once __DIR__ . '/Donations.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->safeLoad();


use Transbank\Webpay\WebpayPlus;
use Transbank\Webpay\WebpayPlus\Transaction;

class donation_handler {
    
    private $commerceCode;
    private $apiKeySecret;
    private $environment;
    
    public function __construct() {
        // Configuración para integración (ambiente de pruebas)
        $this->commerceCode = '597055555532';
        $this->apiKeySecret = '579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1C';
        $this->environment = $_ENV['TBK_ENVIROMENT']; // 'integration' o 'production'
    }
    /**
     * Crear una transacción de donación
     */
    public function createDonation($amount, $donorData = []) {
        try {
            // Generate compliant buy order (max 26 chars)
            $timestamp = substr(time(), -8); // Use last 8 digits of timestamp
            $uniqueId = substr(uniqid(), -8); // Use last 8 digits of uniqid
            $buyOrder = "DON{$timestamp}{$uniqueId}"; // Format: DON + 16 chars
            
            // Generate session ID (keep it simple)
            $sessionId = "SESS_" . $uniqueId;
            
            // URL de retorno después del pago
            $returnUrl = $this->getBaseUrl() . '/confirm';
            
            // Crear la transacción
            $transaction = new Transaction();
            $response = $transaction->create($buyOrder, $sessionId, $amount, $returnUrl);

            // Guardar datos de la donación en base de datos
            // $this->saveDonationData($buyOrder, $amount, $donorData, $response->getToken());
            
            return [
                'success' => true,
                'token' => $response->getToken(),
                'url' => $response->getUrl(),
                'buy_order' => $buyOrder
            ];
            
        } catch (Exception $e) {
            error_log("Transbank Error: " . $e->getMessage()); // Add logging
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Confirmar el pago después del retorno de Transbank
     */
    public function confirmDonation($token) {
        try {
            $transaction = new Transaction();
            $response = $transaction->commit($token);
            
            // Actualizar estado de la donación
            $this->updateDonationStatus($response->getBuyOrder(), $response);
            
            return [
                'success' => true,
                'response_code' => $response->getResponseCode(),
                'status' => $response->getStatus(),
                'amount' => $response->getAmount(),
                'buy_order' => $response->getBuyOrder(),
                'authorization_code' => $response->getAuthorizationCode(),
                'transaction_date' => $response->getTransactionDate()
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Guardar datos de donación en base de datos
     */
private function saveDonationData($buyOrder, $amount, $donorData, $token) {
    try {
        $donation = new Donations();
        
        $query = "INSERT INTO vinculosurbanosdb.donations 
                (buy_order, full_name, email, phone, token, is_anon) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $params = [
            $buyOrder,
            $donorData['name'] ?? '',     // Use array key 'name' with null coalescing
            $donorData['email'] ?? '',    // Use array key 'email' with null coalescing
            $donorData['phone'] ?? '',    // Use array key 'phone' with null coalescing
            $token,
            $donorData['is_anon'] ?? 0    // Default to 0 if not set
        ];
        
        return $donation->execute($query, $params);
        
    } catch (PDOException $e) {
        error_log("Error saving donation data: " . $e->getMessage());
        throw new Exception("Failed to save donation data: " . $e->getMessage());
    }
}
    
    /**
     * Actualizar estado de donación después de confirmación
     */
    private function updateDonationStatus($buyOrder, $response) {
        // Determinar estado basado en response_code
        $status = ($response->getResponseCode() == 0) ? 'approved' : 'rejected';
        
        // Aquí actualizarías en tu base de datos
        /*
        $pdo = $this->getDatabase();
        $stmt = $pdo->prepare("
            UPDATE donations 
            SET status = ?, response_code = ?, authorization_code = ?, transaction_date = ?, updated_at = NOW()
            WHERE buy_order = ?
        ");
        $stmt->execute([
            $status,
            $response->getResponseCode(),
            $response->getAuthorizationCode(),
            $response->getTransactionDate(),
            $buyOrder
        ]);
        */
    }
    
    private function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . '://' . $host;
    }
}

// Ejemplo de uso básico:

// Para crear una donación:
// $donationService = new donation_handler();
// $result = $donationService->createDonation(1000000000, [
//     'name' => 'Juan Pérez',
//     'email' => 'juan@email.com',
//     'phone' => '+56912345678'
// ]);

// if ($result['success']) {
//     // Redirigir al usuario a Transbank
//     header('Location: ' . $result['url'] . '?token_ws=' . $result['token']);
// } else {
//     echo 'Error: ' . $result['error'];
// }