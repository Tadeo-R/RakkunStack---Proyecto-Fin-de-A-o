<link rel="stylesheet" href="/css/auth.css">

<body class="auth-page">

    <div class="auth-bg-overlay" id="dynamicBg"></div>

    <a href="/" class="back-home-btn" title="Volver al inicio">
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
                <h2>Pensalos.<br>Crealos.<br>Disfrutalos.</h2>
                <p>Únete a la plataforma integral líder para la organización, gestión y seguimiento de torneos
                    deportivos, videojuegos y competencias grupales.</p>
            </div>

            <div class="auth-sidebar-footer">
                &copy; 2026 RakkunStack&trade;. Todos los derechos reservados.
            </div>
        </div>

        <!-- PANEL DERECHO: Tarjeta del Formulario Oscuro -->
        <section class="reg-card">

            <!-- Logotipo móvil alternativo (Solo visible en pantallas pequeñas) -->
            <div class="mobile-brand-wrapper">
                <img class="logoRueda" src="/img/LogoRueda.svg" alt="logo">
                <span style="font-weight: 900; letter-spacing: 1px; font-size: 1.25rem;">COMPETTI</span>
            </div>

            <div class="reg-card-header">
                <h2>Crear una cuenta</h2>
                <p class="subtitle">Gestiona tus torneos deportivos y competitivos</p>
            </div>

            <form action="index.php?action=register" method="POST" class="auth-grid-form">

                <div class="input-group">
                    <label for="user">Nombre de Usuario</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-user input-icon"></i>
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

                <!-- Contraseña ocupa ambas columnas internas para dar espacio al medidor -->
                <div class="input-group full-width-field">
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

                    <!-- Lista de requisitos dinámicos compacta -->
                    <ul class="password-requirements" id="requirementsList">
                        <li id="req-length"><i class="fa-solid fa-circle-dot"></i> Mínimo 8 caracteres</li>
                        <li id="req-upper"><i class="fa-solid fa-circle-dot"></i> Una mayúscula</li>
                        <li id="req-number"><i class="fa-solid fa-circle-dot"></i> Un número</li>
                        <li id="req-special"><i class="fa-solid fa-circle-dot"></i> Un caracter especial</li>
                    </ul>
                </div>

                <div class="input-group full-width-field">
                    <label for="confirm_password">Confirmar Contraseña</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-shield-halved input-icon"></i>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••"
                            required>
                    </div>
                </div>

                <?php
                $errorRegistro = $_SESSION['error_registro'] ?? null;
                unset($_SESSION['error_registro']);
                ?>

                <?php if ($errorRegistro): ?>
                    <div class="full-width-field"
                        style="color: #f87171; font-size: 14px; font-weight: bold; text-align: center; background: rgba(248, 113, 113, 0.1); padding: 10px; border-radius: 8px; border: 1px solid rgba(248, 113, 113, 0.2);">
                        <?php echo htmlspecialchars($errorRegistro); ?>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn-auth-submit">
                    <span>Registrarse</span>
                    <i class="fa-solid fa-user-plus"></i>
                </button>
            </form>

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
                <p class="account">¿Ya tienes una cuenta? <a href="index.php?view=auth/login">Inicia Sesión</a></p>
            </div>
            
        </section>

    </main>

    <script src="/js/auth-bg.js"></script>
    <script src="/js/password-strength.js"></script>
    
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