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
<link rel="stylesheet" href="<?= BASE_URL ?>/html/css/navigation.css">

<header class="main-header">
    <div class="header-container">

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

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-menu">
                    <button class="user-btn">
                        <i class="fa-regular fa-user"></i> <?= $_SESSION['user_name'] ?? 'Usuario' ?>
                    </button>
                </div>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="<?= BASE_URL ?>/app/view/auth/login.php" class="btn-login">Iniciar Sesión</a>
                    <a href="<?= BASE_URL ?>/app/view/auth/register.php" class="btn-register">Registrarse</a>
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
            <span class="logo-icon">🏆</span>
            <div class="logo-text"><strong style="color: white;">TORNEOS</strong></div>
        </div>
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

<script>
    const burgerMenu = document.querySelector('.hamburger');
    const sidebar = document.querySelector('.mobile-sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    const body = document.body;

    const toggleSidebar = () => {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        body.classList.toggle('no-scroll');
        burgerMenu.classList.toggle('is-active');
    };

    burgerMenu.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', toggleSidebar);
</script>