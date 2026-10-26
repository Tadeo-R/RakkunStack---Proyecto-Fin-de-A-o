<?php

class LoginController
{
    private $modelo;

    public function __construct($modeloUsuario)
    {
        $this->modelo = $modeloUsuario;
    }

    public function procesarLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Asegurar que la sesión esté iniciada para poder guardar los errores
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // 1. Recoger y limpiar entradas
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // 2. Validación básica
            if (empty($email) || empty($password)) {
                $_SESSION['error_login'] = "Todos los campos son obligatorios.";
                header('Location: /index.php?view=auth/login');
                exit;
            }

            // 3. Consultar al Modelo
            $usuario = $this->modelo->login($email, $password);

            if ($usuario) {
                // 4. Si las credenciales son correctas, guardamos en la sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_email'] = $usuario['email'];

                // 5. Redireccionar directamente a la página de inicio
                header('Location: /');
                exit;
            } else {
                // Si falla el login, guardamos el error en la sesión y redirigimos de forma limpia
                $_SESSION['error_login'] = "El correo electrónico o la contraseña son incorrectos.";
                header('Location: /index.php?view=auth/login');
                exit;
            }
        }
    }
}