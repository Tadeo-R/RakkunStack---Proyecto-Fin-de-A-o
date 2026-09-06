<?php
// Enlaces compartidos para mantener consistencia entre mobile y desktop
$nav_links = [
    ['href' => BASE_URL . '/index.php', 'text' => 'Inicio', 'icon' => 'fa-home'],
    ['href' => '#', 'text' => 'Torneos', 'icon' => 'fa-trophy'],
    ['href' => '#', 'text' => 'Equipos', 'icon' => 'fa-users'],
    ['href' => '#', 'text' => 'Resultados', 'icon' => 'fa-chart-bar'],
    ['href' => '#', 'text' => 'Calendario', 'icon' => 'fa-calendar-alt'],
    ['href' => '#', 'text' => 'Quiénes somos', 'icon' => 'fa-info-circle']
];
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/hamburgers/1.2.1/hamburgers.min.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>html/css/navigation.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>html/css/global.css">

<header class="main-header">
    <div class="header-container">
        <div class="logo">
            <img class="logoRueda" src="<?php echo BASE_URL; ?>html/img/LogoRueda.svg" alt="logo">
            <img class="logoTrebol" src="<?php echo BASE_URL; ?>html/img/LogoTrebol.svg" alt="logo">
            <div class="logo-text">
                <strong>COMPETTI</strong>
                <span>Pensalos. Crealos. Disfrutalos.</span>
            </div>
        </div>
        <nav class="desktop-nav">
            <a class="a-btn" href="<?php echo BASE_URL; ?>index.php">Inicio</a>
            <a class="a-btn" href="<?php echo BASE_URL; ?>torneos.php">Torneos</a>
            <a class="a-btn" href="<?php echo BASE_URL; ?>equipos.php">Equipos</a>
            <a class="a-btn" href="<?php echo BASE_URL; ?>resultados.php">Resultados</a>
            <a class="a-btn" href="<?php echo BASE_URL; ?>calendario.php">Calendario</a>
            <a class="a-btn" href="<?php echo BASE_URL; ?>quienesSomos.php">Quienes Somos</a>
        </nav>
        <div class="header-actions">
            <a href="<?php echo BASE_URL; ?>search.php">
                <button class="icon-btn search-trigger"><i class="fa-solid fa-magnifying-glass"></i></button>
            </a>
            <button class="icon-btn theme-trigger"><i class="fa-regular fa-moon"></i></button>
            <a href="<?php echo BASE_URL; ?>notificaciones.php">
                <button class="icon-btn notification-btn"><i class="fa-regular fa-bell"></i></button>
            </a>
            <a href="<?php echo BASE_URL; ?>profile.php">
                <button class="icon-btn user-btn"><i class="fa-regular fa-user"></i></button>
            </a>
            <div class="auth-buttons">
                <a href="<?php echo BASE_URL; ?>view/auth/login.php" class="btn-secondary">Iniciar Sesi&oacute;n</a>
                <a href="<?php echo BASE_URL; ?>view(auth/register.php" class="btn-primary">Registrarse</a>
            </div>
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
            <span class="logo-icon">&#127942;</span>
            <div class="logo-text"><strong style="color: white;">TORNEOS</strong></div>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a class="enlace-nav" href="index.html"><i class="fa-regular fa-home"></i>Inicio</a>
        <a class="enlace-nav" href="torneos.html"><i class="fa-regular fa-chess-knight"></i>Torneos</a>
        <a class="enlace-nav" href="equipos.html"><i class="fa-regular fa-handshake"></i>Equipos</a>
        <a class="enlace-nav" href="resultados.html"><i class="fa-regular fa-chart-bar"></i>Resultados</a>
        <a class="enlace-nav" href="calendario.html"><i class="fa-regular fa-calendar-alt"></i>Calendario</a>
        <a class="enlace-nav" href="quienesSomos.html"><i class="fa-regular fa-lightbulb"></i>Quienes Somos</a>
    </nav>
    <div class="sidebar-auth-mobile">
        <a href="login.html" class="btn-auth-mobile btn-login-mobile">
            <i class="fa-solid fa-right-to-bracket"></i> Iniciar Sesi&oacute;n
        </a>
        <a href="register.html" class="btn-auth-mobile btn-register-mobile">
            <i class="fa-solid fa-user-plus"></i> Registrarse
        </a>
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