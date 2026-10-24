<?php

class ForgotPasswController {
    private $db;
    private $usuarioModel;

    public function __construct($conexionPDO, $usuarioModel) {
        $this->db = $conexionPDO;
        $this->usuarioModel = $usuarioModel;
    }

    /** Procesa la solicitud de recuperación enviando un correo con un token */
    public function procesarRecuperacion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?view=auth/forgotPassw');
            exit;
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->cargarVistaConError("Por favor, introduce un correo electrónico válido.");
            return;
        }

        // 1. Verificar si el email existe usando tu UsuarioModel actual
        if (!$this->usuarioModel->existeEmail($email)) {
            // Por seguridad, es mejor decir que el correo fue enviado aunque no exista,
            // evitando que atacantes descubran qué correos están registrados.
            $this->cargarVistaConExito();
            return;
        }

        // 2. Generar un Token seguro y único de 32 bytes en formato hexadecimal (64 caracteres)
        $token = bin2hex(random_bytes(32));
        
        // El token expirará en 2 horas
        $expira = date('Y-m-d H:i:s', strtotime('+2 hours'));

        try {
            // Eliminar solicitudes de recuperación viejas de este mismo correo para no saturar la BD
            $stmtDel = $this->db->prepare("DELETE FROM recuperacion_passwords WHERE email = :email");
            $stmtDel->execute([':email' => $email]);

            // Guardar el nuevo token en la base de datos
            $sql = "INSERT INTO recuperacion_passwords (email, token, expira) VALUES (:email, :token, :expira)";
            $stmtIns = $this->db->prepare($sql);
            $stmtIns->execute([
                ':email' => $email,
                ':token' => $token,
                ':expira' => $expira
            ]);

            // 3. Crear el enlace de restablecimiento apuntando a tu enrutador
            $enlaceRestablecer = "http://" . $_SERVER['HTTP_HOST'] . "/index.php?view=auth/resetPassw&token=" . $token;

            // 4. Preparar el cuerpo del correo
            $asunto = "Restablecer Contraseña - COMPETTI";
            $mensaje = "Hola,\n\nHas solicitado restablecer tu contraseña en COMPETTI.\n";
            $mensaje .= "Haz clic en el siguiente enlace para cambiar tu contraseña (este enlace expira en 2 horas):\n\n";
            $mensaje .= $enlaceRestablecer . "\n\n";
            $mensaje .= "Si no solicitaste este cambio, puedes ignorar este correo con seguridad.\n";
            
            $cabeceras = "From: no-reply@competti.local\r\n" .
                         "Reply-To: no-reply@competti.local\r\n" .
                         "X-Mailer: PHP/" . phpversion();

            // Enviar el correo (XAMPP necesitará configurar sendmail para que salga a internet,
            // pero la función registrará la ejecución correctamente en tu entorno local)
            @mail($email, $asunto, $mensaje, $cabeceras);

            $this->cargarVistaConExito();

        } catch (PDOException $e) {
            $this->cargarVistaConError("Hubo un problema en el servidor. Inténtalo más tarde.");
        }
    }

    private function cargarVistaConError($mensajeError) {
        $error = $mensajeError;
        include VIEW_PATH . '/auth/forgotPassw.php';
        exit;
    }

    private function cargarVistaConExito() {
        // Redirige o muestra un mensaje informando que se envió el enlace de forma segura
        echo "<script>
            alert('Si el correo se encuentra registrado, recibirás un enlace de recuperación en los próximos minutos.');
            window.location.href = '/index.php?view=auth/login';
        </script>";
        exit;
    }
}
