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
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/navigation.css">

<header class="main-header">
    <div class="header-container">
        <button class="sidebar-toggle" aria-label="Abrir menú">☰</button>

        <div class="logo">
            <span class="logo-icon">🏆</span>
            <div class="logo-text">
                <strong>TORNEOS</strong>
                <span>Compite. Conecta. Gana.</span>
            </div>
        </div>

        <nav class="desktop-nav">
            <?php foreach ($nav_links as $link): ?>
                <a href="<?= $link['href'] ?>"><?= $link['text'] ?></a>
            <?php endforeach; ?>
        </nav>

        <div class="header-actions">
            <button class="icon-btn search-trigger"><i class="fa-solid fa-magnifying-glass"></i></button>
            <button class="icon-btn theme-trigger"><i class="fa-regular fa-moon"></i></button>
            <button class="icon-btn notification-btn"><i class="fa-regular fa-bell"></i></button>

            <div class="auth-buttons">
                <a href="<?= BASE_URL ?>/app/view/auth/login.php" class="btn-login">Iniciar Sesión</a>
                <a href="<?= BASE_URL ?>/app/view/auth/register.php" class="btn-register">Registrarse</a>
            </div>
        </div>
    </div>
</header>

<div class="sidebar-overlay"></div>
<aside class="mobile-sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <span class="logo-icon">🏆</span>
            <div class="logo-text"><strong style="color: white;">TORNEOS</strong></div>
        </div>
        <button class="sidebar-close">×</button>
    </div>
    <nav class="sidebar-nav">
        <?php foreach ($nav_links as $link): ?>
            <a href="<?= $link['href'] ?>">
                <i class="fa-solid <?= $link['icon'] ?>"></i>
                <?= $link['text'] ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-auth-mobile">
        
        <a href="<?= BASE_URL ?>/app/view/auth/login.php" class="btn-auth-mobile btn-login-mobile">
            <i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión
        </a>
        <a href="<?= BASE_URL ?>/app/view/auth/register.php" class="btn-auth-mobile btn-register-mobile">
            <i class="fa-solid fa-user-plus"></i> Registrarse
        </a>
    </div>
</aside>

<nav class="bottom-nav">
    <a href="#" class="bottom-link active"><i class="fa-solid fa-house"></i><span>Inicio</span></a>
    <a href="#" class="bottom-link"><i class="fa-solid fa-trophy"></i><span>Torneos</span></a>
    <a href="#" class="bottom-link"><i class="fa-solid fa-users"></i><span>Equipos</span></a>
    <a href="#" class="bottom-link"><i class="fa-solid fa-chart-bar"></i><span>Resultados</span></a>
    <a href="#" class="bottom-link"><i class="fa-solid fa-user"></i><span>Perfil</span></a>
</nav>

<script>
    const burgerMenu = document.querySelector('.sidebar-toggle');
    const closeMenu = document.querySelector('.sidebar-close');
    const sidebar = document.querySelector('.mobile-sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    const body = document.body; // <-- Seleccionamos el body

    const toggleSidebar = () => {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');

        // Al usar toggle, si la clase existe la quita, y si no, la pone.
        body.classList.toggle('no-scroll');
    };

    burgerMenu.addEventListener('click', toggleSidebar);
    closeMenu.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', toggleSidebar);
</script>