<?php
require_once __DIR__ . '/../../../config/config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Fuente Formal y Moderna (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/html/css/auth.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/html/css/global.css">
    <title>Recruperar Contraseña - Torneos</title>
</head>
<body>
<main class="auth-container">
    <section class="reg-card">
        <form action="<?= BASE_URL ?>/app/controller/forgePasswController.php" method="POST">
            <h2>Recuperar Contraseña</h2>
            <p class="subtitle">Te enviaremos un e-mail para que actualices tu contraseña</p>
            
            <div class="input-group">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" required>
            </div>
            
            <button type="submit">Enviar E-Mail</button>

        </form>
    </section>
</main>
</body>
</html>