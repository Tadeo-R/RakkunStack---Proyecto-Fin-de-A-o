<?php

require_once __DIR__ . '/../../../config/config.php';


$nav_links = [
    ['href' => BASE_URL . 'html/index.php', 'text' => 'Inicio', 'icon' => 'fa-home'],
    ['href' => BASE_URL . '/torneos.php', 'text' => 'Torneos', 'icon' => 'fa-trophy'],
    ['href' => BASE_URL . '/equipos.php', 'text' => 'Equipos', 'icon' => 'fa-users'],
    ['href' => BASE_URL . '/resultados.php', 'text' => 'Resultados', 'icon' => 'fa-chart-bar'],
    ['href' => BASE_URL . '/calendario.php', 'text' => 'Calendario', 'icon' => 'fa-calendar-alt'],
    ['href' => BASE_URL . '/quienesSomos.php', 'text' => 'Quiénes somos', 'icon' => 'fa-info-circle']
];
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/hamburgers/1.2.1/hamburgers.min.css">
<script src="https://kit.fontawesome.com/3bd97052d9.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="/css/navigation.css">
<link rel="stylesheet" href="/css/global.css">

<header class="main-header">
    <div class="header-container">
        <div class="logo">
            <img class="logoRueda" src="/img/LogoRueda.svg" alt="logo">
            <img class="logoTrebol" src="/img/LogoTrebol.svg" alt="logo">
            <div class="logo-text">
                <strong>COMPETTI</strong>
                <span>Pensalos. Crealos. Disfrutalos.</span>
            </div>
        </div>
        <nav class="desktop-nav">
            <a class="a-btn" href="/">Inicio</a>
            <a class="a-btn" href="/index.php?view=torneos">Torneos</a>
            <a class="a-btn" href="/index.php?view=equipos">Equipos</a>
            <a class="a-btn" href="/index.php?view=resultados">Resultados</a>
            <a class="a-btn" href="/index.php?view=calendario">Calendario</a>
            <a class="a-btn" href="/index.php?view=quienesSomos">Quienes Somos</a>
        </nav>
        <div class="header-actions">
            <a href="/index.php?view=search">
                <button class="icon-btn search-trigger"><i class="fa-solid fa-magnifying-glass"></i></button>
            </a>
            <button class="icon-btn theme-trigger" id="theme-toggle"><i class="fa-solid fa-moon"
                    id="theme-icon"></i></button>
            <!-- CONDICIONAL DE AUTENTICACIÓN DE LA COMPU -->
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="/index.php?view=notificaciones">
                    <button class="icon-btn notification-btn"><i class="fa-regular fa-bell"></i></button>
                </a>
                <a href="/index.php?view=users/profile">
                    <button class="icon-btn user-btn"><i class="fa-regular fa-user"></i></button>
                </a>
                
                <div class="user-logged-info">
                    <span style="color: white; font-weight: bold;">Hola,
                        <?= htmlspecialchars($_SESSION['usuario_nombre']) ?>!</span>
                    <a href="/index.php?view=auth/logout" class="btn-secondary"
                        style="background-color: rgba(220, 53, 69, 0.5); color: white;">Salir</a>
                </div>
            <?php else: ?>
                
                <div class="auth-buttons">
                    <a href="/index.php?view=auth/login" class="btn-secondary">Iniciar Sesi&oacute;n</a>
                    <a href="/index.php?view=auth/register" class="btn-primary">Registrarse</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>

<button class="hamburger hamburger--emphatic" type="button">
    <span class="hamburger-box">
        <span class="hamburger-inner"></span>
    </span>
</button>

<div class="sidebar-overlay"></div>

<aside class="mobile-sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <img class="logoRueda" src="/img/LogoRueda.svg" alt="logo">
            <img class="logoTrebol" src="/img/LogoTrebol.svg" alt="logo">
            <div class="logo-text"><strong style="color: white;">COMPETTI</strong></div>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a class="enlace-nav" href="/"><i class="fa-regular fa-home"></i>Inicio</a>
        <a class="enlace-nav" href="/index.php?view=torneos"><i class="fa-regular fa-chess-knight"></i>Torneos</a>
        <a class="enlace-nav" href="/index.php?view=equipos"><i class="fa-regular fa-handshake"></i>Equipos</a>
        <a class="enlace-nav" href="/index.php?view=resultados"><i class="fa-regular fa-chart-bar"></i>Resultados</a>
        <a class="enlace-nav" href="/index.php?view=calendario"><i class="fa-regular fa-calendar-alt"></i>Calendario</a>
        <a class="enlace-nav" href="/index.php?view=quienesSomos"><i class="fa-regular fa-lightbulb"></i>Quienes Somos</a>
    </nav>
    <!-- CONDICIONAL DE AUTENTICACIÓN del cel-->
    <div class="sidebar-auth-mobile">
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <!-- Si está logueado -->
            <p style="color: white; text-align: center; margin-bottom: 10px;">Sesión activa:
                <strong><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></strong>
            </p>
            <a href="/index.php?view=auth/logout" class="btn-auth-mobile btn-login-mobile"
                style="background-color: #dc3545;">
                <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
            </a>
        <?php else: ?>
            <a href="/index.php?view=auth/login" class="btn-auth-mobile btn-login-mobile">
                <i class="fa-solid fa-right-to-bracket"></i> Iniciar Sesi&oacute;n
            </a>
            <a href="/index.php?view=auth/register" class="btn-auth-mobile btn-register-mobile">
                <i class="fa-solid fa-user-plus"></i> Registrarse
            </a>
        <?php endif; ?>
    </div>
</aside>

<script>
    const burgerMenu = document.querySelector('.hamburger'); // Botón del menu hamburguesa
    const sidebar = document.querySelector('.mobile-sidebar'); // Menu lateral
    const overlay = document.querySelector('.sidebar-overlay'); // Overlay que aparece cuando el menu lateral está activo
    const body = document.body;
    const toggleSidebar = () => {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        body.classList.toggle('no-scroll');
        burgerMenu.classList.toggle('is-active');
    };
    burgerMenu.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', toggleSidebar);

    document.querySelectorAll('.enlace-nav').forEach(enlace => {
        // Si el enlace coincide con la URL actual de la pagina
        if (enlace.href === window.location.href) {
            const icono = enlace.querySelector('i');
            // Intercambia las clases de Font Awesome
            icono.classList.remove('fa-regular');
            icono.classList.add('fa-solid');
        }
    });

</script>
<script src="/js/theme-toggle.js"></script>