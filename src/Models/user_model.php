<?php
require_once __DIR__ . '/../config/DB_Connection.php';

class UserModel {
    private $conn;

    public function __construct() {
        $this->conn = DB::getConnection();
    }

    public function register($email, $password) {
        $stmt = $this->conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bind_param("ss", $email, $hash);
        return $stmt->execute();
    }

    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT id, email, password FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }

        return false;
    }
}
