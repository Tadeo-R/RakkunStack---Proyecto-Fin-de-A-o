<?php
require_once __DIR__ . '/../../config/config.php';

// Validar que la petición venga por POST y que exista el archivo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];

    // Validar errores de subida
    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $file['tmp_name'];
        $fileName = $file['name'];
        $fileSize = $file['size'];
        $fileType = $file['type'];

        // Validar extensiones permitidas
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($fileExtension, $allowedExtensions)) {
            // Limitar tamaño (Ej: Máximo 2MB)
            if ($fileSize <= 2 * 1024 * 1024) {

                // Generar un nombre único para evitar duplicados
                // Idealmente aquí usarías el ID del usuario de la sesión: "user_" . $_SESSION['user_id']
                $newFileName = 'avatar_' . uniqid() . '.' . $fileExtension;

                // Ruta física del servidor donde se guardará
                $uploadDir = __DIR__ . '/../../html/img/avatars/';

                // Crear el directorio si no existe
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $destPath = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {

                    // 1. AQUÍ HACES TU QUERY SQL (Ejemplo):
                    // $db->query("UPDATE usuarios SET avatar = '$newFileName' WHERE id = " . $_SESSION['usuario']['id']);

                    // 2. ACTUALIZA LA SESIÓN INMEDIATAMENTE:
                    // Si usas sesiones para los datos del usuario, actualiza el valor de la foto:
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    $_SESSION['usuario']['avatar'] = $newFileName;

                    // 3. Redireccionar de vuelta
                    header('Location: ' . BASE_URL . '/app/view/users/profile.php?status=success');
                    exit;

                } else {
                    echo "Error al mover el archivo al directorio de destino.";
                }
            } else {
                echo "El archivo es demasiado grande. Máximo 2MB.";
            }
        } else {
            echo "Formato no permitido. Usa JPG, JPEG, PNG o WEBP.";
        }
    } else {
        echo "Error en la subida del archivo.";
    }
} else {
    header('Location: ' . BASE_URL);
    exit;
}