<?php

// 1. INCLUIR LOS ARCHIVOS DE PHPMAILER MANUALMENTE
// Ajusta la ruta ('libs/phpmailer/...') según dónde hayas guardado la carpeta src
require '../libs/phpMailer/Exception.php';
require '../libs/phpMailer/PHPMailer.php';
require '../libs/phpMailer/SMTP.php';

// 2. IMPORTAR LOS ESPACIOS DE NOMBRES
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ForgotPasswController {
    private $db;
    private $usuarioModel;

    public function __construct($conexionPDO, $usuarioModel) {
        $this->db = $conexionPDO;
        $this->usuarioModel = $usuarioModel;
    }

    /** Procesa la solicitud de recuperación enviando un correo real */
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

        if (!$this->usuarioModel->existeEmail($email)) {
            $this->cargarVistaConExito();
            return;
        }

        $token = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+2 hours'));

        try {
            $stmtDel = $this->db->prepare("DELETE FROM recuperacion_passwords WHERE email = :email");
            $stmtDel->execute([':email' => $email]);

            $sql = "INSERT INTO recuperacion_passwords (email, token, expira) VALUES (:email, :token, :expira)";
            $stmtIns = $this->db->prepare($sql);
            $stmtIns->execute([
                ':email' => $email,
                ':token' => $token,
                ':expira' => $expira
            ]);

            $enlaceRestablecer = "http://" . $_SERVER['HTTP_HOST'] . "/index.php?view=auth/resetPassw&token=" . $token;

            // ==========================================
            // CONFIGURACIÓN Y ENVÍO REAL CON PHPMAILER
            // ==========================================
            $mail = new PHPMailer(true);

            // Servidor SMTP de Gmail
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';                    
            $mail->SMTPAuth   = true;
            $mail->Username   = 'benjamingabrielzyk@gmail.com';       // <-- Reemplaza con tu correo emisor
            $mail->Password   = 'tbakgqdlycryxcnr';                // <-- Pega aquí tu contraseña de 16 caracteres de Google
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;       
            $mail->Port       = 587;                                  
            $mail->CharSet    = 'UTF-8';                              

            // Remitente y Destinatario
            $mail->setFrom('benjamingabrielzyk@gmail.com', 'COMPETTI');
            $mail->addAddress($email);                                

            // Contenido en Formato HTML limpio
            $mail->isHTML(true);
            $mail->Subject = 'Restablecer Contraseña - COMPETTI';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd;'>
                    <h2 style='color: #333;'>Hola,</h2>
                    <p>Has solicitado restablecer tu contraseña en COMPETTI.</p>
                    <p>Haz clic en el siguiente botón para cambiar tu contraseña (este enlace expira en 2 horas):</p>
                    <p style='text-align: center; margin: 30px 0;'>
                        <a href='{$enlaceRestablecer}' style='padding: 12px 25px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>Restablecer Contraseña</a>
                    </p>
                    <p style='color: #666; font-size: 12px;'>Si el botón no funciona, copia y pega este enlace en tu navegador:<br>{$enlaceRestablecer}</p>
                    <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                    <p style='color: #999; font-size: 12px;'>Si no solicitaste este cambio, puedes ignorar este correo con seguridad.</p>
                </div>
            ";
            $mail->AltBody = "Hola,\n\nHas solicitado restablecer tu contraseña en COMPETTI.\nHaz clic en el siguiente enlace:\n" . $enlaceRestablecer;

            // Resolver problemas de certificados SSL/TLS inválidos en entornos locales
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->send();


            $this->cargarVistaConExito();

        } catch (Exception $e) {
            // Si el envío falla, puedes ver el motivo exacto usando: $mail->ErrorInfo;
            $this->cargarVistaConError("Hubo un problema al enviar el correo de recuperación.");
        } catch (PDOException $e) {
            $this->cargarVistaConError("Hubo un problema en el servidor. Inténtalo más tarde.");
        }
    }

   private function cargarVistaConError($mensajeError) {
        // Almacenamos el error en la sesión para que sobreviva a la recarga
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['error_recuperacion'] = $mensajeError;
    
        // Redirigimos limpiamente de vuelta a la vista del formulario
        header('Location: /index.php?view=auth/forgotPassw');
        exit;
    }


    private function cargarVistaConExito() {
        echo "<script>
            alert('Si el correo se encuentra registrado, recibirás un enlace de recuperación en los próximos minutos.');
            window.location.href = '/index.php?view=auth/login';
        </script>";
        exit;
    }
}
