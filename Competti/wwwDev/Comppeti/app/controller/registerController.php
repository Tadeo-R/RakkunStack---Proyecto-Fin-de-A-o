<?php

class RegisterController
{
    private $modelo;

    public function __construct($modeloUsuario)
    {
        $this->modelo = $modeloUsuario;
    }

    public function procesarRegistro()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $nombre = trim($_POST['user'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (empty($nombre) || empty($email) || empty($password) || empty($confirm_password)) {
                $_SESSION['error_registro'] = "Todos los campos son obligatorios.";
                header('Location: /index.php?view=auth/register');
                exit;
            }

            if ($password !== $confirm_password) {
                $_SESSION['error_registro'] = "Las contraseñas no coinciden.";
                header('Location: /index.php?view=auth/register');
                exit;
            }

            if ($this->modelo->existeEmail($email)) {
                $_SESSION['error_registro'] = "El correo electrónico ya está registrado.";
                header('Location: /index.php?view=auth/register');
                exit;
            }

            $registroExitoso = $this->modelo->registrar($nombre, $email, $password);

            $usuario = $this->modelo->login($email, $password);

            if ($usuario) {
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_username'] = $usuario['nombre'];
                $_SESSION['usuario_ubicacion']      = !empty($usuario['ubicacion']) ? trim($usuario['ubicacion']) : '';
                $_SESSION['usuario_bio'] = $usuario['bio'] ?? '';
                $_SESSION['usuario_avatar'] = $usuario['avatar'] ?? '/img/default-avatar.jpg';
                $_SESSION['usuario_fecha_registro'] = $usuario['fechaRegistro'] ?? null;

                header('Location: /');
                exit;
            } else {
                $_SESSION['error_registro'] = "Hubo un error al registrar el usuario. Por favor, inténtalo de nuevo.";
                header('Location: /index.php?view=auth/register');
                exit;
            }
        }
    }
}