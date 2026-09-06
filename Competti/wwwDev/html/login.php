<?php
require_once __DIR__ . '/../config/config.php'; 
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/3bd97052d9.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/auth.css">
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
            <form action="#" method="POST">
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
                        <a href="forgetPassw.html" class="forgot-link">¿La olvidaste?</a>
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
                <p class="account">¿Aún no tienes cuenta? <a href="register.html">Regístrate gratis</a></p>
            </div>
        </section>

    </main>

    <script src="js/auth-bg.js"></script>
</body>



</html>