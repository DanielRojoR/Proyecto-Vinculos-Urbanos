<?php

class FinancingModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Obtener todos los financiamientos
    public function getAllFinancings()
    {
        $stmt = $this->db->prepare("SELECT * FROM financings");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un financiamiento por ID
    public function getFinancingById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM financings WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo financiamiento
    public function createFinancing($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO financings (name, amount, description, created_at)
            VALUES (:name, :amount, :description, NOW())
        ");
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':amount', $data['amount']);
        $stmt->bindParam(':description', $data['description']);
        return $stmt->execute();
    }

    // Actualizar un financiamiento existente
    public function updateFinancing($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE financings
            SET name = :name, amount = :amount, description = :description
            WHERE id = :id
        ");
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':amount', $data['amount']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Eliminar un financiamiento
    public function deleteFinancing($id)
    {
        $stmt = $this->db->prepare("DELETE FROM financings WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>