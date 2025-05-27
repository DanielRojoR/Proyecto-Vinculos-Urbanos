<?php
// src/Models/donation/confirm.php

// Incluir autoloader de Composer
require_once __DIR__ . '/../../../vendor/autoload.php';

use Transbank\Webpay\WebpayPlus;
use Transbank\Webpay\WebpayPlus\Transaction;

class TransbankConfirmService {
    
    private $commerceCode;
    private $apiKeySecret;
    private $environment;
    
    public function __construct() {
        $this->commerceCode = '597055555532';
        $this->apiKeySecret = '579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1C';
        $this->environment = 'integration';
        
        $this->configureWebpay();
    }
    
    private function configureWebpay() {
        try {
            if ($this->environment === 'integration') {
                WebpayPlus::configureForIntegration($this->commerceCode, $this->apiKeySecret);
            } else {
                WebpayPlus::configureForProduction($this->commerceCode, $this->apiKeySecret);
            }
        } catch (Exception $e) {
            error_log("Error configurando Webpay en confirm: " . $e->getMessage());
            throw new Exception("Error de configuración del sistema de pagos");
        }
    }
    
    public function confirmDonation($token) {
        try {
            if (empty($token)) {
                throw new Exception('Token no recibido');
            }
            
            error_log("Confirmando donación con token: $token");
            
            $transaction = new Transaction();
            $response = $transaction->commit($token);
            
            // Log de la respuesta para debug
            error_log("Respuesta de Transbank: " . json_encode([
                'buy_order' => $response->getBuyOrder(),
                'response_code' => $response->getResponseCode(),
                'amount' => $response->getAmount(),
                'status' => $response->getStatus()
            ]));
            
            // Actualizar estado en base de datos
            $this->updateDonationStatus($response->getBuyOrder(), $response);
            
            return [
                'success' => true,
                'response_code' => $response->getResponseCode(),
                'status' => $response->getStatus(),
                'amount' => $response->getAmount(),
                'buy_order' => $response->getBuyOrder(),
                'authorization_code' => $response->getAuthorizationCode(),
                'transaction_date' => $response->getTransactionDate(),
                'approved' => $response->getResponseCode() == 0
            ];
            
        } catch (Exception $e) {
            error_log("Error confirmando donación: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function updateDonationStatus($buyOrder, $response) {
        // Leer archivo de donaciones
        $dataFile = __DIR__ . '/donations.json';
        $donations = [];
        
        if (file_exists($dataFile)) {
            $donations = json_decode(file_get_contents($dataFile), true) ?: [];
        }
        
        if (isset($donations[$buyOrder])) {
            $donations[$buyOrder]['status'] = $response->getResponseCode() == 0 ? 'approved' : 'rejected';
            $donations[$buyOrder]['response_code'] = $response->getResponseCode();
            $donations[$buyOrder]['authorization_code'] = $response->getAuthorizationCode();
            $donations[$buyOrder]['transaction_date'] = $response->getTransactionDate();
            $donations[$buyOrder]['updated_at'] = date('Y-m-d H:i:s');
            
            file_put_contents($dataFile, json_encode($donations, JSON_PRETTY_PRINT));
        }
    }
    
    private function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . '://' . $host;
    }
}

try {
    // Manejar tanto GET como POST (Transbank puede usar ambos)
    $token = $_POST['token_ws'] ?? $_GET['token_ws'] ?? null;
    
    if (!$token) {
        throw new Exception('Token no recibido en la confirmación');
    }
    
    $confirmService = new TransbankConfirmService();
    $result = $confirmService->confirmDonation($token);
    
    if ($result['success']) {
        if ($result['approved']) {
            // Pago exitoso - redirigir a página de éxito
            $successUrl = $confirmService->getBaseUrl() . '/src/Models/donation/success.php?buy_order=' . urlencode($result['buy_order']) . '&amount=' . $result['amount'];
            header('Location: ' . $successUrl);
            exit();
        } else {
            // Pago rechazado
            $errorUrl = $confirmService->getBaseUrl() . '/src/Models/donation/error.php?reason=payment_rejected&buy_order=' . urlencode($result['buy_order']);
            header('Location: ' . $errorUrl);
            exit();
        }
    } else {
        // Error en el sistema
        $errorUrl = $confirmService->getBaseUrl() . '/src/Models/donation/error.php?reason=system_error&message=' . urlencode($result['error']);
        header('Location: ' . $errorUrl);
        exit();
    }
    
} catch (Exception $e) {
    error_log("Error general en confirm.php: " . $e->getMessage());
    $errorUrl = '/src/Models/donation/error.php?reason=system_error&message=' . urlencode($e->getMessage());
    header('Location: ' . $errorUrl);
    exit();
}
?>