<?php 
$error = $_SESSION['error_recuperacion'] ?? null; 
unset($_SESSION['error_recuperacion']); // Borrar para que no aparezca de nuevo al refrescar
?>

<!-- app/view/auth/forgotPassw.php -->
<link rel="stylesheet" href="/css/auth.css">

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
        <form action="/index.php?action=forgot_password" method="POST">
            <h2>Recuperar Contraseña</h2>
            <p class="subtitle">Te enviaremos un e-mail para que actualices tu contraseña</p>
            
            <?php if (isset($error)): ?>
                <div style="color: #ff4d4d; margin-bottom: 15px; font-size: 14px; font-weight: bold;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="input-group">
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
            <p class="account">¿Recordaste tu cuenta? <a href="/index.php?view=auth/login">Inicia Sesión</a></p>
        </div>
    </section>
</main>

<script src="/js/auth-bg.js"></script>
