<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/3bd97052d9.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/auth.css">
    <title>Registrarse - COMPETTI</title>
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
            <form action="index.php?action=register" method="POST">
                <h2>Crear una cuenta</h2>
                <p class="subtitle">Gestiona tus torneos deportivos y competitivos</p>

                <div class="input-group">
                    <label for="user">Nombre de Usuario</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-user input-icon"></i>
                        <!-- name="user" coincide con tu formulario -->
                        <input type="text" id="user" name="user" placeholder="Ej. JoaquinPerez" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="email">Correo Electrónico</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="password">Contraseña</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                    </div>
                    
                    <!-- Barra visual de fuerza -->
                    <div class="strength-meter-container">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <span class="strength-text" id="strengthText">Seguridad de la contraseña</span>

                    <!-- Lista de requisitos dinámicos -->
                    <ul class="password-requirements" id="requirementsList">
                        <li id="req-length"><i class="fa-solid fa-circle-dot"></i> Mínimo 8 caracteres</li>
                        <li id="req-upper"><i class="fa-solid fa-circle-dot"></i> Una mayúscula</li>
                        <li id="req-number"><i class="fa-solid fa-circle-dot"></i> Un número</li>
                        <li id="req-special"><i class="fa-solid fa-circle-dot"></i> Un caracter especial (@,$,!,%,*,#,?,&)</li>
                    </ul>
                </div>

                <div class="input-group">
                    <label for="confirm_password">Confirmar Contraseña</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-shield-halved input-icon"></i>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••"
                            required>
                    </div>
                </div>

                <?php 
                    // Capturamos el error guardado en la sesión por el controlador de registro
                    $errorRegistro = $_SESSION['error_registro'] ?? null; 
                    unset($_SESSION['error_registro']); // Lo borramos para que no se quede pegado al refrescar la página
                ?>

                <!-- Si existe un error de registro, lo pintamos en pantalla -->
                <?php if ($errorRegistro): ?>
                    <div style="color: #ff4d4d; margin-bottom: 15px; font-size: 14px; font-weight: bold; text-align: center;">
                        <?php echo htmlspecialchars($errorRegistro); ?>
                    </div>
                <?php endif; ?>


                <button type="submit" class="btn-auth-submit">
                    <span>Registrarse</span>
                    <i class="fa-solid fa-user-plus"></i>
                </button>
            </form>

            <div class="card-footer">
                <p class="account">¿Ya tienes una cuenta? <a href="index.php?view=auth/login">Inicia Sesión</a></p>
            </div>
        </section>

    </main>

    <script src="/js/auth-bg.js"></script>
    <script src="/js/password-strength.js"></script>
</body>

</html>