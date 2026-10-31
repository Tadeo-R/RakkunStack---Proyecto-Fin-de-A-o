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
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $_SESSION['error_login'] = "Todos los campos son obligatorios.";
                header('Location: /index.php?view=auth/login');
                exit;
            }

            $usuario = $this->modelo->login($email, $password);

            if ($usuario) {
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_email'] = $usuario['email'];

                // CORREGIDO: Mapeamos los campos reales de la nueva Base de Datos a la sesión
                $_SESSION['usuario_username'] = $usuario['nombre']; // Usamos el nombre como nick provisional
                $_SESSION['usuario_ubicacion']      = !empty($usuario['ubicacion']) ? trim($usuario['ubicacion']) : '';
                $_SESSION['usuario_bio'] = $usuario['bio'] ?? '';
                $_SESSION['usuario_avatar'] = $usuario['avatar'] ?? '/img/default-avatar.jpg';
                $_SESSION['usuario_fecha_registro'] = $usuario['fechaRegistro'] ?? null;

                header('Location: /');
                exit;
            } else {
                $_SESSION['error_login'] = "El correo electrónico o la contraseña son incorrectos.";
                header('Location: /index.php?view=auth/login');
                exit;
            }
        }
    }

    public function procesarGoogleLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['credential'])) {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $jwt = $_POST['credential'];
            $parts = explode('.', $jwt);

            if (count($parts) !== 3) {
                $_SESSION['error_login'] = 'Token de Google inválido (estructura incorrecta).';
                header('Location: /index.php?view=auth/login');
                exit;
            }

            // Decodificar payload del JWT de forma segura con soporte para padding
            $base64Url = $parts[1];
            $base64 = str_replace(['-', '_'], ['+', '/'], $base64Url);
            $padding = strlen($base64) % 4;
            if ($padding > 0) {
                $base64 .= str_repeat('=', 4 - $padding);
            }
            
            $payload = json_decode(base64_decode($base64), true);

            if (!$payload || !isset($payload['email'])) {
                $_SESSION['error_login'] = 'No se pudo leer la información del correo de Google.';
                header('Location: /index.php?view=auth/login');
                exit;
            }

            $email = $payload['email'];
            $nombre = $payload['name'] ?? $payload['given_name'] ?? 'Usuario';
            $avatar = $payload['picture'] ?? null;

            // 1. Buscar si el usuario ya existe
            $usuario = $this->modelo->buscarPorEmail($email);

            if (!$usuario) {
                // 2. Si no existe, lo creamos automáticamente
                $username = strtolower(explode('@', $email)[0]) . rand(100, 999);
                $idUsuario = $this->modelo->crearUsuarioGoogle($nombre, $username, $email, $avatar);
                
                if (!$idUsuario) {
                    $_SESSION['error_login'] = 'Error al registrar la cuenta de Google en la base de datos.';
                    header('Location: /index.php?view=auth/login');
                    exit;
                }

                $usuario = $this->modelo->buscarPorId($idUsuario);
            }

            if (!$usuario) {
                $_SESSION['error_login'] = 'No se pudo inicializar la sesión del usuario.';
                header('Location: /index.php?view=auth/login');
                exit;
            }

            // 3. Iniciar sesión global con los datos reales de tu tabla USUARIO
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_avatar'] = $usuario['avatar'] ?? $avatar;

            // Redirigir al perfil con éxito
            header('Location: /index.php?view=users/profile');
            exit;
        }
    }
}