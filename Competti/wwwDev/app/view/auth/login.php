<?php
require_once __DIR__ . '/../../../config/config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>html/css/auth.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>html/css/global.css">
    <title>Iniciar Sesión - COMPETTI</title>
</head>

<body class="auth-page">

    <div class="auth-bg-overlay" id="dynamicBg"></div>

    <a href="index.html" class="back-home-btn" title="Volver al inicio">
        <i class="fa-solid fa-arrow-left"></i> <span>Volver</span>
    </a>

    <main class="auth-container">
        
        <div class="auth-brand">
            <h1 class="brand-name">COMPETTI</h1>
        </div>

        <section class="reg-card">
            <form action="<?= BASE_URL ?>app/index.php?action=login" method="POST">
                <h2>Iniciar Sesión</h2>
                <p class="subtitle">Ingresa tus datos para gestionar tus torneos</p>

                <div class="input-group">
                    <label for="email">Correo Electrónico</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" required>
                    </div>
                </div>

                <div class="input-group">
                    <div class="label-row">
                        <label for="password">Contraseña</label>
                        <a href="<?= BASE_URL ?>view/auth/forgetPassw.php" class="forgot-link">¿La olvidaste?</a>
                    </div>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn-auth-submit">
                    <span>Entrar a mi cuenta</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <div class="card-footer">
                <p class="account">¿Aún no tienes cuenta? <a href="<?= BASE_URL ?>view/auth/register.html">Regístrate gratis</a></p>
            </div>
        </section>

    </main>

    <script src="<?= BASE_URL ?>html/js/auth-bg.js"></script>
</body>

</html>