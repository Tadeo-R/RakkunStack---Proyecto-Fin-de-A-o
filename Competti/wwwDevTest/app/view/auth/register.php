<?php
require_once __DIR__ . '/../../../config/config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/html/css/auth.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/html/css/global.css">
    <title>Registrarse - Torneos</title>
</head>

<body>
    <main class="auth-container">
        <section class="reg-card">
            <form action="<?= BASE_URL ?>/app/controller/registerController.php" method="POST">
                <h2>Crear una cuenta</h2>
                <p class="subtitle">Gestiona tus torneos deportivos y competitivos</p>

                <div class="input-group">
                    <label for="user">Usuario</label>
                    <input type="text" id="user" name="user" placeholder="Ej. JoaquinPérez" required>
                </div>

                <div class="input-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" required>
                </div>

                <div class="input-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>

                <div class="input-group">
                    <label for="confirm_password">Confirmar contraseña</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••"
                        required>
                </div>

                <button type="submit">Registrarse</button>

                <a class="account" href="<?= BASE_URL ?>/app/view/auth/login.php">
                    ¿Ya tienes una cuenta? <span>Inicia Sesión</span>
                </a>
            </form>
        </section>
    </main>
</body>

</html>