<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <link rel="stylesheet" href="/css/auth.css">
    <link rel="stylesheet" href="/css/global.css">
=======
    <link rel="stylesheet" href="<?= BASE_URL ?>html/css/auth.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>html/css/global.css">
>>>>>>> b05b45f0b213ff829fee4907509413abf0f5e569
    <title>Iniciar Sesión - COMPETTI</title>
</head>

<body class="auth-page">

    <div class="auth-bg-overlay" id="dynamicBg"></div>

<<<<<<< HEAD
    <a href="/" class="back-home-btn" title="Volver al inicio">
=======
    <a href="<?= BASE_URL ?>html/index.php" class="back-home-btn" title="Volver al inicio">
>>>>>>> b05b45f0b213ff829fee4907509413abf0f5e569
        <i class="fa-solid fa-arrow-left"></i> <span>Volver</span>
    </a>

    <main class="auth-container">
        
        <div class="auth-brand">
<<<<<<< HEAD
            <img class="logoRueda" src="/img/LogoRueda.svg" alt="logo">
            <img class="logoTrebol" src="/img/LogoTrebol.svg" alt="logo">
=======
            <img class="logoRueda" src="<?= BASE_URL ?>html/img/LogoRueda.svg" alt="logo">
            <img class="logoTrebol" src="<?= BASE_URL ?>html/img/LogoTrebol.svg" alt="logo">
>>>>>>> b05b45f0b213ff829fee4907509413abf0f5e569
            <h1 class="brand-name">COMPETTI</h1>
        </div>

        <section class="reg-card">
<<<<<<< HEAD
            <form action="index.php?action=login" method="POST">
=======
            <form action="<?= BASE_URL ?>app/index.php?action=login" method="POST">
>>>>>>> b05b45f0b213ff829fee4907509413abf0f5e569
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
<<<<<<< HEAD
                        <a href="index.php?view=auth/forgotPassw" class="forgot-link">¿La olvidaste?</a>
=======
                        <a href="<?= BASE_URL ?>view/auth/forgetPassw.php" class="forgot-link">¿La olvidaste?</a>
>>>>>>> b05b45f0b213ff829fee4907509413abf0f5e569
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
<<<<<<< HEAD
                <p class="account">¿Aún no tienes cuenta? <a href="index.php?view=auth/register">Regístrate gratis</a></p>
=======
                <p class="account">¿Aún no tienes cuenta? <a href="<?= BASE_URL ?>view/auth/register.php">Regístrate gratis</a></p>
>>>>>>> b05b45f0b213ff829fee4907509413abf0f5e569
            </div>
        </section>

    </main>

<<<<<<< HEAD
    <script src="/js/auth-bg.js"></script>
=======
    <script src="<?= BASE_URL ?>html/js/auth-bg.js"></script>
>>>>>>> b05b45f0b213ff829fee4907509413abf0f5e569
</body>

</html>