<?php
require_once __DIR__ . "/Database.php";
class Model extends Database {
    protected $table;
    protected $primaryKey = 'id';
    
    // Método para obtener todos los registros
    public function all() {
        $sql = "SELECT * FROM {$this->table}";
        return $this->query($sql);
    }
    
    // Método para encontrar un registro por ID
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->query($sql, [$id])[0] ?? null;
    }
    
    // Método para buscar por una columna específica
    public function findBy($column, $value) {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        return $this->query($sql, [$value]);
    }
    
    // Método para insertar datos
    public function create($data) {
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$values})";
        return $this->execute($sql, array_values($data));
    }
    
    // Método para actualizar datos
    public function update($id, $data) {
        $set = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = ?";
        $values = array_values($data);
        $values[] = $id;
        return $this->execute($sql, $values);
    }
    
    // Método para eliminar un registro
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->execute($sql, [$id]);
    }
    
    // Método para consultas personalizadas
    public function query(string $sql, array $params = []): array {
        return parent::query($sql, $params);
    }
}