<?php

require_once __DIR__ . '/../DB_Connection.php';

class FinancingModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public static function getSummaryByCausa()
    {
        $db = DB::getConnection();
        $sql = "SELECT c.causa_id, c.nombre, SUM(d.monto) as total_recaudado
                FROM Donaciones d
                INNER JOIN Causa c ON d.causa_id = c.causa_id
                GROUP BY c.causa_id, c.nombre";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function createDonation($usuario_id, $causa_id, $monto, $metodo_de_pago, $anonimo)
    {
        $db = DB::getConnection();
        $sql = "INSERT INTO Donaciones (usuario_id, causa_id, monto, fecha, metodo_de_pago, anonimo)
                VALUES (?, ?, ?, NOW(), ?, ?)";

        $stmt = $db->prepare($sql);
        $stmt->bind_param("iidsi", $usuario_id, $causa_id, $monto, $metodo_de_pago, $anonimo);
        return $stmt->execute();
    }

    public static function getDonationsByUser($usuario_id)
    {
        $db = DB::getConnection();
        $sql = "SELECT d.*, c.nombre as causa_nombre
                FROM Donaciones d
                INNER JOIN Causa c ON d.causa_id = c.causa_id
                WHERE d.usuario_id = ?";

        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function getDonationById($donacion_id)
    {
        $db = DB::getConnection();
        $sql = "SELECT * FROM Donaciones WHERE donacion_id = ?";

        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $donacion_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
?>
