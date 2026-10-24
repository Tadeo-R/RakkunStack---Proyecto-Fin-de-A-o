<?php
// Si no está logueado o no se envió un archivo válido, rebotarlo a la vista de perfil
if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['profile_picture'])) {
    header("Location: view/users/profile.php"); // Medido desde 'app/'
    exit();
}

$file = $_FILES['profile_picture'];
$userId = $_SESSION['usuario_id'];

if ($file['error'] === UPLOAD_ERR_OK) {
    $fileType = $file['type'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

    if (in_array($fileType, $allowedTypes)) {
        // Buscamos la carpeta subiendo un nivel para salir de 'app/' e ir a la raíz donde está 'html/'
        $uploadDir = dirname(__DIR__, 2) . '/html/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFileName = 'avatar_' . $userId . '_' . time() . '.' . $extension;
        $destination = $uploadDir . $newFileName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            
            // Ruta de acceso web que consumirá la etiqueta <img>
            $relativePath = 'html/uploads/' . $newFileName;

            try {
                // $pdo funciona heredado desde index.php
                $stmt = $pdo->prepare("UPDATE usuarios SET avatar = :avatar WHERE id = :id");
                $stmt->execute([
                    ':avatar' => $relativePath,
                    ':id' => $userId
                ]);

                // Sincronizamos de inmediato el avatar de la sesión activa
                $_SESSION['usuario_avatar'] = $relativePath;

            } catch (Exception $e) {
                // Manejo de errores
            }
        }
    }
}

// Redirección final segura medida desde el contexto de 'app/'
header("Location: view/users/profile.php");
exit();
