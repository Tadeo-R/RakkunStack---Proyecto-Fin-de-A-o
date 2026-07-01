<?php
require_once __DIR__ . '/../../config/config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/3bd97052d9.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/home.css">
    <title>Document</title>
</head>
<header>
    <button class="sidebar-btn">☰</button>
    <aside class="sidebar">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
            <img src="<?= BASE_URL ?>/public/img/pibble.jpg" alt="pibble" style="width: 20px;">
            <h2 style="color: white; margin: 0;">Torneos</h2>
        </div>
        <button class="close-btn">×</button>
        <nav>
            <a href="#">Inicio</a>
            <a href="#">Servicios</a>
            <a href="#">Portafolio</a>
            <a href="#">Contacto</a>
        </nav>
    </aside>
    <i class="fa-regular fa-bell"></i>
</header>

<body>
    <main>
        <section class="hero">
            <div class="hero-images">
                <img src="<?= BASE_URL ?>/public/img/fubol.jpg" alt="Fútbol">
                <img src="<?= BASE_URL ?>/public/img/ps5.jpg" alt="PS5">
                <img src="<?= BASE_URL ?>/public/img/ajedre.jpg" alt="Ajedrez">
                <img src="<?= BASE_URL ?>/public/img/valo.jpg" alt="Valorant">
                <img src="<?= BASE_URL ?>/public/img/pinpon.jpg" alt="Ping Pong">
            </div>
            <div class="hero-info">
                <h1>Organizá.<br>Competí.<br><span class="highlight">Ganá.</span></h1>
                <p>La plataforma integral para la gestión de torneos deportivos, mentales y electrónicos.</p>
                <div class="hero-buttons">
                    <?php
                    if (isset($_SESSION['usuario'])) {
                        echo '<a href="' . BASE_URL . '/torneos.php" class="btn btn-primary">Ver Torneos</a>';
                    } else {
                        echo '<a href="' . BASE_URL . '/app/view/auth/login.php" class="btn btn-primary">Iniciar Sesión</a>';
                    }
                    ?>
                </div>
            </div>
        </section>
    </main>
</body>

<footer>
    <p>© 2026 RakkunStack. Torneos is a product of RakkunStack. All rights reserved.</p>
</footer>

<script>
    const btn = document.querySelector('.sidebar-btn');
    const closeBtn = document.querySelector('.close-btn');
    const sidebar = document.querySelector('.sidebar');

    btn.addEventListener('click', () => sidebar.classList.toggle('active'));
    closeBtn.addEventListener('click', () => sidebar.classList.toggle('active'));
</script>

</html>