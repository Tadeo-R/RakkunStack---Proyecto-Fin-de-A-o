<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://fontawesome.com" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/auth.css">
    <title>Iniciar Sesión - COMPETTI</title>
</head>

<body class="auth-page">

    <div class="auth-bg-overlay" id="dynamicBg"></div>

    <a href="/" class="back-home-btn">
        <i class="fa-solid fa-arrow-left"></i> <span>Volver</span>
    </a>

    <main class="auth-container">

        <!-- Banner de Marca Lateral -->
        <div class="auth-sidebar">
            <div class="auth-brand">
                <img class="logoRueda" src="/img/LogoRueda.svg" alt="logo">
                <img class="logoTrebol" src="/img/LogoTrebol.svg" alt="logo">
                <h1 class="brand-name">COMPETTI</h1>
            </div>
            <div class="auth-sidebar-hero">
                <h2>¡Hola de nuevo!</h2>
                <p>Ingresa a tu panel para administrar tus estadísticas de juego, seguir las llaves de tus torneos
                    activos y comunicarte con tu equipo.</p>
            </div>
            <div class="auth-sidebar-footer">
                &copy; 2026 RakkunStack&trade;. Todos los derechos reservados.
            </div>
        </div>

        <!-- Tarjeta del Formulario -->
        <section class="reg-card">
            <div class="mobile-brand-wrapper">
                <img src="/img/LogoRueda.svg" alt="logo">
                <span style="font-weight: 900; letter-spacing: 1px; font-size: 1.25rem;">COMPETTI</span>
            </div>

            <div class="reg-card-header">
                <h2>Iniciar Sesión</h2>
                <p class="subtitle">Ingresa tus datos para gestionar tus torneos</p>
            </div>

            <form action="index.php?action=login" method="POST">
                <div class="input-group full-width-field">
                    <label for="email">Correo Electrónico</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" required>
                    </div>
                </div>

                <div class="input-group full-width-field">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <label for="password">Contraseña</label>
                        <a href="index.php?view=auth/forgotPassw"
                            style="font-size: 0.8rem; color: #c084fc; text-decoration: none;">¿La olvidaste?</a>
                    </div>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>

                <?php
                $errorLogin = $_SESSION['error_login'] ?? null;
                unset($_SESSION['error_login']);
                ?>
                <?php if ($errorLogin): ?>
                    <div class="full-width-field"
                        style="color: #f87171; font-size: 14px; font-weight: bold; text-align: center; background: rgba(248, 113, 113, 0.1); padding: 10px; border-radius: 8px;">
                        <?php echo htmlspecialchars($errorLogin); ?>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn-auth-submit">
                    <span>Entrar a mi cuenta</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <?php
            $errorLogin = $_SESSION['error_login'] ?? null;
            unset($_SESSION['error_login']);
            ?>
            <?php if ($errorLogin): ?>
                <div
                    style="color: #f87171; background: rgba(248, 113, 113, 0.1); padding: 10px; border-radius: 8px; margin-bottom: 15px; text-align: center; font-weight: bold;">
                    <?= htmlspecialchars($errorLogin); ?>
                </div>
            <?php endif; ?>

            <!-- Divisor estético elegante -->
            <div style="display: flex; align-items: center; margin: 20px 0; gap: 10px;">
                <div style="flex: 1; height: 1px; background: rgba(255,255,255,0.08);"></div>
                <span style="color: #94a3b8; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px;">O CONTINÚA CON</span>
                <div style="flex: 1; height: 1px; background: rgba(255,255,255,0.08);"></div>
            </div>

            <!-- Contenedor Estilizado para Integrar el Botón de Google de forma limpia -->
            <div style="display: flex; justify-content: center; width: 100%; background: rgba(15, 23, 42, 0.6); padding: 4px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.08); overflow: hidden;">
                <div id="g_id_onload"
                    data-client_id="381406866827-2jva2ivmr9b3fhe95pl9tonatcgih4n2.apps.googleusercontent.com"
                    data-callback="handleCredentialResponse" data-auto_prompt="false">
                </div>

                <div class="g_id_signin" 
                    data-type="standard" 
                    data-size="large" 
                    data-theme="filled_black"
                    data-text="continue_with" 
                    data-shape="rectangular" 
                    data-logo_alignment="center" 
                    data-width="400">
                </div>
            </div>

            <div class="card-footer">
                <p class="account">¿Aún no tienes cuenta? <a href="index.php?view=auth/register">Regístrate gratis</a>
                </p>
            </div>
        </section>

    </main>

    <script src="/js/auth-bg.js"></script>

    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script>
        function handleCredentialResponse(response) {
            // Enviar el JWT de Google a nuestro servidor PHP para autenticar
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/index.php?action=google_login';

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'credential';
            input.value = response.credential;

            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>

</html>