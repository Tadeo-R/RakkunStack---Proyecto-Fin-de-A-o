<?php

class ProfileController {
    private $modelo;

    public function __construct($modeloUsuario) {
        $this->modelo = $modeloUsuario;
    }

    public function actualizarDatos() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $id_usuario = $_SESSION['usuario_id'];
            $nombre     = trim($_POST['nombre'] ?? '');
            $ubicacion  = trim($_POST['ubicacion'] ?? '');
            $biografia  = trim($_POST['biografia'] ?? '');
            $nueva_pass = $_POST['nueva_password'] ?? '';
            $conf_pass  = $_POST['confirmar_nueva_password'] ?? '';

            // Validar que el nombre no vaya vacío
            if (empty($nombre)) {
                header('Location: /index.php?view=users/profile&error=nombre_vacio');
                exit;
            }

            // Validar coincidencia de contraseñas si escribió algo
            if (!empty($nueva_pass) && $nueva_pass !== $conf_pass) {
                header('Location: /index.php?view=users/profile&error=passwords_no_coinciden');
                exit;
            }

            // Guardar en la Base de Datos
            $exito = $this->modelo->actualizarPerfil($id_usuario, $nombre, $ubicacion, $biografia, $nueva_pass);

            if ($exito) {
                // Actualizar inmediatamente las variables de sesión para que el Nav y el Sidebar cambien de forma dinámica
                $_SESSION['usuario_nombre']    = $nombre;
                $_SESSION['usuario_ubicacion'] = $ubicacion;
                $_SESSION['usuario_bio']       = $biografia;

                header('Location: /index.php?view=users/profile&success=true');
                exit;
            } else {
                header('Location: /index.php?view=users/profile&error=db_error');
                exit;
            }
        }
    }
}
