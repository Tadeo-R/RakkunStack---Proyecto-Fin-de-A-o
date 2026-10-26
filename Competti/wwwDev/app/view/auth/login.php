<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/auth.css">
    <link rel="stylesheet" href="/css/global.css">
    <title>Iniciar Sesión - COMPETTI</title>
</head>

<body class="auth-page">

    <div class="auth-bg-overlay" id="dynamicBg"></div>

    <a href="/" class="back-home-btn" title="Volver al inicio">
        <i class="fa-solid fa-arrow-left"></i> <span>Volver</span>
    </a>

    <main class="auth-container">

        <div class="auth-brand">
            <img class="logoRueda" src="/img/LogoRueda.svg" alt="logo">
            <img class="logoTrebol" src="/img/LogoTrebol.svg" alt="logo">
            <h1 class="brand-name">COMPETTI</h1>
        </div>

        <section class="reg-card">
            <form action="index.php?action=login" method="POST">
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
                        <a href="index.php?view=auth/forgotPassw" class="forgot-link">¿La olvidaste?</a>
                    </div>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>

                <?php
                // Capturamos el error guardado en la sesión por el controlador
                $errorLogin = $_SESSION['error_login'] ?? null;
                unset($_SESSION['error_login']); // Lo borramos de inmediato para que no se quede pegado al refrescar
                ?>

                <!-- Si existe un error, lo pintamos en pantalla -->
                <?php if ($errorLogin): ?>
                    <div
                        style="color: #ff4d4d; margin-bottom: 15px; font-size: 14px; font-weight: bold; text-align: center;">
                        <?php echo htmlspecialchars($errorLogin); ?>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn-auth-submit">
                    <span>Entrar a mi cuenta</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <div class="card-footer">
                <p class="account">¿Aún no tienes cuenta? <a href="index.php?view=auth/register">Regístrate gratis</a>
                </p>
            </div>
        </section>

    </main>

    <script src="/js/auth-bg.js"></script>
</body>

</html>