<!-- app/view/auth/forgot_password.php -->
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

    <a href="/index.php?view=auth/login" class="back-home-btn" title="Volver al login">
        <i class="fa-solid fa-arrow-left"></i> <span>Volver</span>
    </a>

    <main class="auth-container">
        <div class="auth-brand">
            <img class="logoRueda" src="/img/LogoRueda.svg" alt="logo">
            <img class="logoTrebol" src="/img/LogoTrebol.svg" alt="logo">
            <h1 class="brand-name">COMPETTI</h1>
        </div>

        <section class="reg-card">
            <!-- CORREGIDO: Redirige la acción a través de tu enrutador general -->
            <form action="index.php?action=forgot_password" method="POST">
                <h2>Recuperar Contraseña</h2>
                <p class="subtitle">Te enviaremos un e-mail para que actualices tu contraseña</p>
                
                <div class="input-group">
                    <label for="email">Correo electrónico</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" required>
                    </div>
                </div>
                
                <!-- Clases de tus botones originales añadidas -->
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
