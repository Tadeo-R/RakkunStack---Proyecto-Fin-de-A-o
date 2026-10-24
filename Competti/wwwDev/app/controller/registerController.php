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
            // 1. Recoger y limpiar los datos del formulario
            $nombre = trim($_POST['user'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // 2. Validaciones básicas del Controlador
            if (empty($nombre) || empty($email) || empty($password) || empty($confirm_password)) {
                $error = "Todos los campos son obligatorios.";
                include 'view/auth/register.php';
                return;
            }

            if ($password !== $confirm_password) {
                $error = "Las contraseñas no coinciden.";
                include 'view/auth/register.php';
                return;
            }

            // 3. Consultar al Modelo si el email ya existe en phpMyAdmin
            if ($this->modelo->existeEmail($email)) {
                $error = "El correo electrónico ya está registrado.";
                include 'view/auth/register.php';
                return;
            }

            // 4. Delegar el registro efectivo al Modelo
            $registroExitoso = $this->modelo->registrar($nombre, $email, $password);

            if ($registroExitoso) {
                // 1. Buscamos al usuario recién creado para obtener su ID autogenerado
                // (O puedes usar el email directamente si es único)
                $usuarioLogueado = $this->modelo->login($email, $password);

                if ($usuarioLogueado) {
                    // 2. Guardamos sus datos en la sesión global
                    $_SESSION['usuario_id'] = $usuarioLogueado['id'];
                    $_SESSION['usuario_nombre'] = $usuarioLogueado['nombre'];
                    $_SESSION['usuario_email'] = $usuarioLogueado['email'];
                }

                // 3. Redireccionamos directamente a la página de inicio de tu proyecto
                header('Location: ' . BASE_URL . 'html/index.php');
                exit;
            } else {
                $error = "Hubo un error al registrar el usuario. Por favor, inténtalo de nuevo.";
                include 'view/auth/register.php';
            }
        }
    }
}