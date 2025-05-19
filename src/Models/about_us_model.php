<?php

class AboutUsModel
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    // Obtener toda la información de "Sobre Nosotros"
    public function getAboutUs()
    {
        $query = "SELECT * FROM about_us LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar la información de "Sobre Nosotros"
    public function updateAboutUs($title, $description, $image)
    {
        $query = "UPDATE about_us SET title = :title, description = :description, image = :image WHERE id = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':image', $image);
        return $stmt->execute();
    }

    // Crear la información de "Sobre Nosotros" (si no existe)
    public function createAboutUs($title, $description, $image)
    {
        $query = "INSERT INTO about_us (title, description, image) VALUES (:title, :description, :image)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':image', $image);
        return $stmt->execute();
    }
}