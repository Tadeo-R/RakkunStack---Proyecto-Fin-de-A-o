<?php

class LoginController {
    private $modelo;

    public function __construct($modeloUsuario) {
        $this->modelo = $modeloUsuario; 
    }

    public function procesarLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Recoger y limpiar entradas
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // 2. Validación básica
            if (empty($email) || empty($password)) {
                $error = "Todos los campos son obligatorios.";
                include 'view/auth/login.php';
                return;
            }

            // 3. Consultar al Modelo (el método que ya creaste)
            $usuario = $this->modelo->login($email, $password);

            if ($usuario) {
                // 4. Si las credenciales son correctas, guardamos en la sesión
                $_SESSION['usuario_id']     = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_email']  = $usuario['email'];

                // 5. Redireccionar directamente a la página de inicio
                header('Location: ' . BASE_URL . 'html/index.php');
                exit;
            } else {
                // Si falla el login, mostramos un error en la misma vista
                $error = "El correo electrónico o la contraseña son incorrectos.";
                include 'view/auth/login.php';
            }
        }
    }
}
