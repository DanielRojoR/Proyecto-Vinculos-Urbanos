<?php
require_once __DIR__ . '/../models/user_model.php';

class AuthController {
    public function showLogin() {
        include __DIR__ . '/../views/login_view.php';
    }

    public function showRegister() {
        include __DIR__ . '/../views/register_view.php';
    }

    public function login() {
        session_start();
        $model = new UserModel();
        $user = $model->login($_POST['email'], $_POST['password']);

        if ($user) {
            $_SESSION['user'] = $user;
            header('Location: ../../public/index.php');
        } else {
            echo "Credenciales incorrectas.";
        }
    }

    public function register() {
        $model = new UserModel();
        if ($model->register($_POST['email'], $_POST['password'])) {
            echo "Registro exitoso. <a href='?pagina=login'>Iniciar sesión</a>";
        } else {
            echo "Error al registrar.";
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: ../../public/index.php');
    }
}
?>
