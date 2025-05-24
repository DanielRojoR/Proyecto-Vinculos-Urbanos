<?php
require_once '..\..\vendor\autoload.php';

use Transbank\Webpay\WebpayPlus\Transaction;
use Transbank\Webpay\Options;

class DonationHandler {
    
    public function __construct() {
        $this->configureTransbank();
    }
    
    private function configureTransbank() {
        // Configuración para ambiente de pruebas
        // En producción, usa tus credenciales reales
        Transaction::setCommerceCode('597055555532');
        Transaction::setApiKey('579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1C');
        Transaction::setIntegrationType('TEST'); // Cambiar a 'LIVE' en producción
    }
    
    public function processDonation() {
        try {
            // Obtener datos del formulario
            $donationData = $this->validateAndGetDonationData();
            
            // Crear transacción
            $transaction = new Transaction();
            
            // Generar ID único para la orden
            $buyOrder = 'DONACION_' . time() . '_' . rand(1000, 9999);
            $sessionId = session_id() ?: 'session_' . time();
            
            // URLs de retorno
            $returnUrl = $this->getBaseUrl() . '/donation-return.php';
            
            // Crear la transacción en Transbank
            $response = $transaction->create(
                $buyOrder,
                $sessionId,
                $donationData['amount'],
                $returnUrl
            );
            
            // Guardar datos de la donación en la base de datos
            $this->saveDonationData($buyOrder, $donationData);
            
            // Redirigir a Transbank
            $redirectUrl = $response->getUrl() . '?token_ws=' . $response->getToken();
            
            return [
                'success' => true,
                'redirect_url' => $redirectUrl,
                'buy_order' => $buyOrder
            ];
            
        } catch (Exception $e) {
            error_log('Error procesando donación: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error procesando la donación. Por favor intenta nuevamente.'
            ];
        }
    }
    
    public function confirmDonation() {
        try {
            $token = $_POST['token_ws'] ?? $_GET['token_ws'];
            
            if (!$token) {
                throw new Exception('Token no encontrado');
            }
            
            $transaction = new Transaction();
            $response = $transaction->commit($token);
            
            // Verificar estado de la transacción
            if ($response->getStatus() === 'AUTHORIZED') {
                // Pago exitoso
                $this->updateDonationStatus($response->getBuyOrder(), 'completed', $response);
                
                return [
                    'success' => true,
                    'message' => 'Donación procesada exitosamente',
                    'authorization_code' => $response->getAuthorizationCode(),
                    'amount' => $response->getAmount(),
                    'buy_order' => $response->getBuyOrder()
                ];
            } else {
                // Pago fallido
                $this->updateDonationStatus($response->getBuyOrder(), 'failed', $response);
                
                return [
                    'success' => false,
                    'message' => 'La donación no pudo ser procesada'
                ];
            }
            
        } catch (Exception $e) {
            error_log('Error confirmando donación: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error confirmando la donación'
            ];
        }
    }
    
    private function validateAndGetDonationData() {
        $requiredFields = ['name', 'surname', 'email', 'confirmEmail', 'amount'];
        $data = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                throw new Exception("Campo requerido faltante: $field");
            }
            $data[$field] = trim($_POST[$field]);
        }
        
        // Validar emails coincidan
        if ($data['email'] !== $data['confirmEmail']) {
            throw new Exception('Los correos electrónicos no coinciden');
        }
        
        // Validar email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email inválido');
        }
        
        // Validar monto
        $amount = intval(str_replace(['.', ','], '', $data['amount']));
        if ($amount < 1000) {
            throw new Exception('El monto mínimo es $1.000');
        }
        
        $data['amount'] = $amount;
        $data['currency'] = $_POST['currency'] ?? 'CLP';
        
        return $data;
    }
    
    private function saveDonationData($buyOrder, $donationData) {
        // Conectar a la base de datos
        $pdo = $this->getDatabaseConnection();
        
        $sql = "INSERT INTO donations (
            buy_order, name, surname, email, amount, currency, 
            status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $buyOrder,
            $donationData['name'],
            $donationData['surname'],
            $donationData['email'],
            $donationData['amount'],
            $donationData['currency'],
            'pending'
        ]);
    }
    
    private function updateDonationStatus($buyOrder, $status, $transactionResponse = null) {
        $pdo = $this->getDatabaseConnection();
        
        $authCode = $transactionResponse ? $transactionResponse->getAuthorizationCode() : null;
        $cardNumber = $transactionResponse ? $transactionResponse->getCardDetail()['card_number'] ?? null : null;
        
        $sql = "UPDATE donations SET 
                status = ?, 
                authorization_code = ?, 
                card_number = ?, 
                updated_at = NOW() 
                WHERE buy_order = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$status, $authCode, $cardNumber, $buyOrder]);
    }
    
    private function getDatabaseConnection() {
        // Configurar según tu base de datos
        $host = 'localhost';
        $dbname = 'donations_db';
        $username = 'your_username';
        $password = 'your_password';
        
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            throw new Exception('Error conectando a la base de datos');
        }
    }
    
    private function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . '://' . $host;
    }
    
    public function sendDonationEmail($donationData) {
        // Enviar email de confirmación al donante
        $to = $donationData['email'];
        $subject = 'Confirmación de Donación';
        $message = "
        <html>
        <head>
            <title>Confirmación de Donación</title>
        </head>
        <body>
            <h2>¡Gracias por tu donación!</h2>
            <p>Hola {$donationData['name']},</p>
            <p>Hemos recibido tu donación por un monto de \${$donationData['amount']} CLP.</p>
            <p>Tu código de autorización es: {$donationData['authorization_code']}</p>
            <p>Gracias por ayudarnos a hacer la diferencia.</p>
        </body>
        </html>
        ";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: donaciones@tuorganizacion.cl' . "\r\n";
        
        mail($to, $subject, $message, $headers);
    }
}

// Archivos de endpoints

// process-donation.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    
    $donationHandler = new DonationHandler();
    $result = $donationHandler->processDonation();
    
    if ($result['success']) {
        header('Location: ' . $result['redirect_url']);
        exit;
    } else {
        // Mostrar error
        echo json_encode($result);
    }
}

// donation-return.php
session_start();

$donationHandler = new DonationHandler();
$result = $donationHandler->confirmDonation();

if ($result['success']) {
    // Enviar email de confirmación
    $donationHandler->sendDonationEmail($result);
    
    // Mostrar página de éxito
    echo "
    <!DOCTYPE html>
    <html>
    <head>
        <title>Donación Exitosa</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
            .success { color: green; font-size: 24px; margin-bottom: 20px; }
            .details { background: #f5f5f5; padding: 20px; border-radius: 10px; margin: 20px auto; max-width: 400px; }
        </style>
    </head>
    <body>
        <h1 class='success'>¡Donación Exitosa!</h1>
        <p>Gracias por tu generosa donación.</p>
        <div class='details'>
            <p><strong>Monto:</strong> \${$result['amount']} CLP</p>
            <p><strong>Código de Autorización:</strong> {$result['authorization_code']}</p>
            <p><strong>Orden:</strong> {$result['buy_order']}</p>
        </div>
        <p>Recibirás un email de confirmación en breve.</p>
        <a href='/'>Volver al inicio</a>
    </body>
    </html>
    ";
} else {
    // Mostrar página de error
    echo "
    <!DOCTYPE html>
    <html>
    <head>
        <title>Error en Donación</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
            .error { color: red; font-size: 24px; margin-bottom: 20px; }
        </style>
    </head>
    <body>
        <h1 class='error'>Error en la Donación</h1>
        <p>{$result['message']}</p>
        <a href='/'>Intentar nuevamente</a>
    </body>
    </html>
    ";
}

?>