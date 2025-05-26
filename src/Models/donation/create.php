<?php
// src/Models/donation/create.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit();
}

// Incluir autoloader de Composer (ajusta la ruta según tu estructura)
require_once __DIR__ . '/../../../vendor/autoload.php';

use Transbank\Webpay\WebpayPlus;
use Transbank\Webpay\WebpayPlus\Transaction;

class TransbankDonationService {
    
    private $commerceCode;
    private $apiKeySecret;
    private $environment;
    
    public function __construct() {
        // Configuración para integración (ambiente de pruebas)
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
            error_log("Error configurando Webpay: " . $e->getMessage());
            throw new Exception("Error de configuración del sistema de pagos");
        }
    }
    
    public function createDonation($amount, $donorData = []) {
        try {
            // Validar monto
            if ($amount < 1000) {
                throw new Exception('El monto mínimo de donación es $1.000');
            }
            
            // Generar IDs únicos
            $buyOrder = 'DON_' . uniqid() . '_' . time();
            $sessionId = 'SESS_' . uniqid();
            
            // URL de retorno - ajusta según tu dominio
            $returnUrl = $this->getBaseUrl() . '/src/Models/donation/confirm.php';
            
            // Log para debug
            error_log("Creando donación - Monto: $amount, Buy Order: $buyOrder, Return URL: $returnUrl");
            
            // Crear la transacción
            $transaction = new Transaction();
            $response = $transaction->create($buyOrder, $sessionId, $amount, $returnUrl);
            
            // Guardar en base de datos (implementar según necesites)
            $this->saveDonationData($buyOrder, $amount, $donorData, $response->getToken());
            
            return [
                'success' => true,
                'token' => $response->getToken(),
                'url' => $response->getUrl(),
                'buy_order' => $buyOrder,
                'amount' => $amount
            ];
            
        } catch (Exception $e) {
            error_log("Error creando donación: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function saveDonationData($buyOrder, $amount, $donorData, $token) {
        // Aquí implementarías el guardado en base de datos
        // Por ahora, guardaremos en un archivo JSON para testing
        $donation = [
            'buy_order' => $buyOrder,
            'amount' => $amount,
            'donor_name' => $donorData['name'] ?? '',
            'donor_email' => $donorData['email'] ?? '',
            'donor_phone' => $donorData['phone'] ?? '',
            'token' => $token,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $dataFile = __DIR__ . '/donations.json';
        $donations = [];
        
        if (file_exists($dataFile)) {
            $donations = json_decode(file_get_contents($dataFile), true) ?: [];
        }
        
        $donations[$buyOrder] = $donation;
        file_put_contents($dataFile, json_encode($donations, JSON_PRETTY_PRINT));
    }
    
    private function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . '://' . $host;
    }
}

try {
    // Leer datos del POST
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Datos de entrada inválidos');
    }
    
    // Validar campos requeridos
    if (!isset($input['amount']) || !is_numeric($input['amount'])) {
        throw new Exception('Monto es requerido y debe ser numérico');
    }
    
    $amount = (int)$input['amount'];
    
    // Datos del donante (opcionales)
    $donorData = [
        'name' => $input['donor_name'] ?? '',
        'email' => $input['donor_email'] ?? '',
        'phone' => $input['donor_phone'] ?? ''
    ];
    
    // Crear servicio y procesar donación
    $donationService = new TransbankDonationService();
    $result = $donationService->createDonation($amount, $donorData);
    
    // Responder
    if ($result['success']) {
        http_response_code(200);
    } else {
        http_response_code(400);
    }
    
    echo json_encode($result);
    
} catch (Exception $e) {
    error_log("Error general en create.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}
?>