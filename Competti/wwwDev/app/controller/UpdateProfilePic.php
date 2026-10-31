<?php
// 1. Validar seguridad e inicio de sesión
if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['profile_picture'])) {
    // CORREGIDO: Redirección unificada a través del enrutador central index.php
    header("Location: /index.php?view=users/profile");
    exit();
}

$file = $_FILES['profile_picture'];
$userId = $_SESSION['usuario_id'];

if ($file['error'] === UPLOAD_ERR_OK) {
    $fileType = $file['type'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

    if (in_array($fileType, $allowedTypes)) {
        // Carpeta física real en el disco del servidor Linux (/opt/lampp/htdocs/.../html/uploads/)
        $uploadDir = dirname(__DIR__, 2) . '/html/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true); // Permisos seguros de lectura/escritura para LAMPP
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFileName = 'avatar_' . $userId . '_' . time() . '.' . $extension;
        $destination = $uploadDir . $newFileName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            
            // CORREGIDO: Ruta relativa web limpia. Como htdocs/html es tu raíz, 
            // el navegador accederá directamente desde '/uploads/nombre_archivo.jpg'
            $webPath = '/uploads/' . $newFileName;

            try {
                // CORREGIDO: Ajustado a las mayúsculas de la tabla 'USUARIO' y columnas de tu nueva BD
                $stmt = $pdo->prepare("UPDATE USUARIO SET avatar = :avatar WHERE id_usuario = :id_usuario");
                $stmt->execute([
                    ':avatar'     => $webPath,
                    ':id_usuario' => $userId
                ]);

                // Sincronizar la variable de sesión para que el <header> actualice el avatar al instante
                $_SESSION['usuario_avatar'] = $webPath;

            } catch (Exception $e) {
                // El error se silencia para continuar con la redirección limpia
            }
        }
    }
}

// CORREGIDO: Regresar de forma segura al perfil usando el enrutador unificado
header("Location: /index.php?view=users/profile");
exit();