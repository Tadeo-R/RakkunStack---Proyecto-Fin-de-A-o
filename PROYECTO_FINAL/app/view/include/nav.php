<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/sidebar.css">
    <title>Document</title>
</head>
<body>
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
</body>

<script>
    const btn = document.querySelector('.sidebar-btn');
    const closeBtn = document.querySelector('.close-btn');
    const sidebar = document.querySelector('.sidebar');

    btn.addEventListener('click', () => sidebar.classList.toggle('active'));
    closeBtn.addEventListener('click', () => sidebar.classList.toggle('active'));
</script>

</html>