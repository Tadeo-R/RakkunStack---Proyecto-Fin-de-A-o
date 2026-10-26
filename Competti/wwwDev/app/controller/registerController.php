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
            // Asegurar que la sesión esté iniciada para poder guardar los errores
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // 1. Recoger y limpiar los datos del formulario
            $nombre = trim($_POST['user'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // 2. Validaciones básicas del Controlador
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

            // 3. Consultar al Modelo si el email ya existe en phpMyAdmin
            if ($this->modelo->existeEmail($email)) {
                $_SESSION['error_registro'] = "El correo electrónico ya está registrado.";
                header('Location: /index.php?view=auth/register');
                exit;
            }

            // 4. Delegar el registro efectivo al Modelo
            $registroExitoso = $this->modelo->registrar($nombre, $email, $password);

            if ($registroExitoso) {
                // Buscamos al usuario recién creado para obtener su ID autogenerado
                $usuarioLogueado = $this->modelo->login($email, $password);

                if ($usuarioLogueado) {
                    // Guardamos sus datos en la sesión global
                    $_SESSION['usuario_id'] = $usuarioLogueado['id'];
                    $_SESSION['usuario_nombre'] = $usuarioLogueado['nombre'];
                    $_SESSION['usuario_email'] = $usuarioLogueado['email'];
                }

                // Redireccionamos directamente a la página de inicio de tu proyecto
                header('Location: /index.php?view=home');
                exit;
            } else {
                $_SESSION['error_registro'] = "Hubo un error al registrar el usuario. Por favor, inténtalo de nuevo.";
                header('Location: /index.php?view=auth/register');
                exit;
            }
        }
    }
}