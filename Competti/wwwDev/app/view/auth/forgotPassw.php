<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://fontawesome.com" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/auth.css">
    <title>Recuperar Contraseña - COMPETTI</title>
</head>

<body class="auth-page">

    <div class="auth-bg-overlay" id="dynamicBg"></div>

    <a href="/" class="back-home-btn">
        <i class="fa-solid fa-arrow-left"></i> <span>Volver</span>
    </a>

    <main class="auth-container">

        <div class="auth-sidebar">
            <div class="auth-brand">
                <img class="logoRueda" src="/img/LogoRueda.svg" alt="logo">
                <img class="logoTrebol" src="/img/LogoTrebol.svg" alt="logo">
                <h1 class="brand-name">COMPETTI</h1>
            </div>
            <div class="auth-sidebar-hero">
                <h2>Recupera tu acceso</h2>
                <p>No te preocupes por los olvidos. Sigue las instrucciones enviadas a tu casilla de correo electrónico para restablecer tus credenciales de forma cifrada y segura.</p>
            </div>
            <div class="auth-sidebar-footer">
                &copy; 2026 RakkunStack&trade;. Todos los derechos reservados.
            </div>
        </div>

        <section class="reg-card">
            <div class="mobile-brand-wrapper">
                <img src="/img/LogoRueda.svg" alt="logo">
                <span style="font-weight: 900; letter-spacing: 1px; font-size: 1.25rem;">COMPETTI</span>
            </div>

            <div class="reg-card-header">
                <h2>Recuperar Contraseña</h2>
                <p class="subtitle">Te enviaremos un e-mail para que actualices tu contraseña</p>
            </div>

            <form action="index.php?action=forgot_password" method="POST">
                <div class="input-group full-width-field">
                    <label for="email">Correo electrónico</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" required>
                    </div>
                </div>

                <button type="submit" class="btn-auth-submit">
                    <span>Enviar E-Mail</span>
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>

            <div class="card-footer">
                <p class="account">¿Recordaste tu cuenta? <a href="index.php?view=auth/login">Inicia Sesión</a></p>
            </div>
        </section>

    </main>

    <script src="/js/auth-bg.js"></script>
</body>

</html>
