<?php

class InitiativesModel
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    // Obtener todas las iniciativas
    public function getAllInitiatives()
    {
        $stmt = $this->db->prepare("SELECT * FROM initiatives");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener una iniciativa por ID
    public function getInitiativeById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM initiatives WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear una nueva iniciativa
    public function createInitiative($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO initiatives (title, description, author, created_at) VALUES (:title, :description, :author, NOW())"
        );
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':author', $data['author']);
        return $stmt->execute();
    }

    // Actualizar una iniciativa existente
    public function updateInitiative($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE initiatives SET title = :title, description = :description, author = :author WHERE id = :id"
        );
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':author', $data['author']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Eliminar una iniciativa
    public function deleteInitiative($id)
    {
        $stmt = $this->db->prepare("DELETE FROM initiatives WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}