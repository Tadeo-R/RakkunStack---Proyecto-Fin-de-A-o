<?php
// app/controller/ResetPasswController.php

class ResetPasswController {
    private $db;

    public function __construct($conexionPDO) {
        $this->db = $conexionPDO;
    }

    /** Procesa el cambio final de contraseña */
    public function actualizarPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?view=auth/login');
            exit;
        }

        $token = trim($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($token)) {
            $this->cargarVistaConError("El token de recuperación es inválido o ha expirado.", $token);
            return;
        }

        try {
            // 1. Validar el token en la base de datos y comprobar que no haya expirado el tiempo
            $sql = "SELECT email FROM recuperacion_passwords WHERE token = :token AND expira > NOW() LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':token' => $token]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {
                $this->cargarVistaConError("El enlace ha expirado o ya fue utilizado. Por favor, solicita uno nuevo.", $token);
                return;
            }

            $email = $registro['email'];

            // 2. Encriptar la nueva contraseña con el mismo estándar seguro del registro
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            // 3. Actualizar la contraseña en la tabla de usuarios
            $sqlUpdate = "UPDATE usuarios SET password = :password WHERE email = :email";
            $stmtUpdate = $this->db->prepare($sqlUpdate);
            $stmtUpdate->execute([
                ':password' => $passwordHash,
                ':email' => $email
            ]);

            // 4. Borrar el token para que no se pueda volver a utilizar por seguridad
            $stmtDelete = $this->db->prepare("DELETE FROM recuperacion_passwords WHERE email = :email");
            $stmtDelete->execute([':email' => $email]);

            // Mostrar mensaje de éxito y redirigir
            echo "<script>
                alert('¡Contraseña actualizada con éxito! Ya puedes iniciar sesión.');
                window.location.href = '/index.php?view=auth/login';
            </script>";
            exit;

        } catch (PDOException $e) {
            $this->cargarVistaConError("Ocurrió un error en el servidor. Inténtalo de nuevo.", $token);
        }
    }

    private function cargarVistaConError($mensajeError, $token) {
        $error = $mensajeError;
        // Volvemos a inyectar la vista pasándole el token para que no se pierda en el formulario oculto
        $_GET['token'] = $token;
        include VIEW_PATH . '/auth/resetPassw.php';
        exit;
    }
}
