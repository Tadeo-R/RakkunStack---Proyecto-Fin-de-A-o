<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://fontawesome.com" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/auth.css">
    <title>Nueva Contraseña - COMPETTI</title>
</head>
<body class="auth-page">

    <div class="auth-bg-overlay" id="dynamicBg"></div>

    <main class="auth-container">
        <div class="auth-brand">
            <img class="logoRueda" src="/img/LogoRueda.svg" alt="logo">
            <img class="logoTrebol" src="/img/LogoTrebol.svg" alt="logo">
            <h1 class="brand-name">COMPETTI</h1>
        </div>

        <section class="reg-card">
            <form action="index.php?action=update_password" method="POST" id="registerForm">
                <!-- Enviamos el token de forma oculta en el formulario -->
                <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">

                <h2>Restablecer Contraseña</h2>
                <p class="subtitle">Ingresa tu nueva contraseña para acceder al sistema</p>

                <!-- Campo nueva contraseña con tu medidor de fortaleza -->
                <div class="input-group">
                    <label for="password">Nueva Contraseña</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                    </div>
                    
                    <div class="strength-meter-container">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <span class="strength-text" id="strengthText">Seguridad de la contraseña</span>

                    <ul class="password-requirements" id="requirementsList">
                        <li id="req-length"><i class="fa-solid fa-circle-dot"></i> Mínimo 8 caracteres</li>
                        <li id="req-upper"><i class="fa-solid fa-circle-dot"></i> Una mayúscula</li>
                        <li id="req-number"><i class="fa-solid fa-circle-dot"></i> Un número</li>
                        <li id="req-special"><i class="fa-solid fa-circle-dot"></i> Un caracter especial (@,$,!,%,*,#,?,&)</li>
                    </ul>
                </div>

                <div class="input-group">
                    <label for="confirm_password">Confirmar Nueva Contraseña</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-shield-halved input-icon"></i>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required>
                    </div>
                    <span class="match-text" id="matchText"></span>
                </div>

                <?php if (isset($error)): ?>
                    <p style="color: red; text-align: center; margin: 5px 0 0 0; font-size: 0.85rem; font-weight: 600;"><?= $error ?></p>
                <?php endif; ?>

                <button type="submit" class="btn-auth-submit" id="submitBtn">
                    <span>Actualizar Contraseña</span>
                    <i class="fa-solid fa-key"></i>
                </button>
            </form>
        </section>
    </main>

    <script src="/js/auth-bg.js"></script>
    <script src="/js/password-strength.js"></script>
</body>
</html>
