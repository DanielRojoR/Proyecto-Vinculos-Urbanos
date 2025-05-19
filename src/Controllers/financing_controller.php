<?php

require_once __DIR__ . '/../Models/financing_model.php';

class FinancingController
{
    public function summaryByCausa(): void
    {
        $summary = FinancingModel::getSummaryByCausa();
        header(header: 'Content-Type: application/json');
        echo json_encode(value: $summary);
    }

    // Crear una nueva donación
    public function createDonation(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (
            isset($data['usuario_id']) &&
            isset($data['causa_id']) &&
            isset($data['monto']) &&
            isset($data['metodo_de_pago']) &&
            isset($data['anonimo'])
        ) {
            $result = FinancingModel::createDonation(
                usuario_id: $data['usuario_id'],
                causa_id: $data['causa_id'],
                monto: $data['monto'],
                metodo_de_pago: $data['metodo_de_pago'],
                anonimo: $data['anonimo']
            );
            header(header: 'Content-Type: application/json');
            echo json_encode(value: ['success' => $result]);
        } else {
            http_response_code(response_code: 400);
            echo json_encode(value: ['error' => 'Datos incompletos']);
        }
    }

    public function donationsByUser($usuario_id): void
    {
        $donations = FinancingModel::getDonationsByUser(usuario_id: $usuario_id);
        header(header: 'Content-Type: application/json');
        echo json_encode(value: $donations);
    }

    public function donationById($donacion_id): void
    {
        $donation = FinancingModel::getDonationById(donacion_id: $donacion_id);
        header(header: 'Content-Type: application/json');
        echo json_encode(value: $donation);
    }
}
?>