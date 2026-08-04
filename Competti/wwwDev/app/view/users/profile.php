<?php
require_once __DIR__ . '/../../../config/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$avatarActual = !empty($_SESSION['usuario']['avatar']) ? $_SESSION['usuario']['avatar'] : __DIR__ . '../../../html/img/default-avatar.jpg';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Torneos</title>
    <script src="https://kit.fontawesome.com/3bd97052d9.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/html/css/global.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/html/css/profile.css">
</head>

<body>

    <?php require_once __DIR__ . '/../include/nav.php'; ?>

    <main class="main-content profile-layout">

        <!-- COLUMNA IZQUIERDA: Tarjeta de Usuario -->
        <aside class="profile-sidebar">
            <div class="profile-card">
                <div class="avatar-wrapper">
                    <!-- Ahora el src es dinámico y busca en la carpeta correcta -->
                    <img src="<?= BASE_URL ?>/html/img/avatars/<?= $avatarActual ?>" alt="Avatar"
                        class="profile-avatar" id="avatarPreview">

                    <form id="avatarForm" action="<?= BASE_URL ?>/app/controller/UpdateProfilePic.php" method="POST"
                        enctype="multipart/form-data">
                        <input type="file" name="profile_picture" id="avatarInput" accept="image/*"
                            style="display: none;">

                        <button type="button" class="btn-change-avatar" id="btnTriggerInput" title="Cambiar foto">
                            <i class="fa-solid fa-camera"></i>
                        </button>

                        <button type="submit" class="btn-save-avatar" id="btnSaveAvatar" style="display: none;"
                            title="Guardar cambios">
                            <i class="fa-solid fa-check"></i>
                        </button>
                    </form>
                </div>
                <h2 class="profile-name">Joaquín Pérez</h2>
                <p class="profile-username">@joaquinperez99</p>
                <span class="profile-badge admin">Organizador</span>

                <div class="profile-meta">
                    <p><i class="fa-solid fa-calendar-days"></i> Miembro desde: May 2024</p>
                    <p><i class="fa-solid fa-location-dot"></i> Buenos Aires, Argentina</p>
                </div>

                <hr class="divider">

                <!-- Estadísticas rápidas -->
                <div class="profile-stats-mini">
                    <div class="stat-mini-box">
                        <strong>12</strong>
                        <span>Torneos</span>
                    </div>
                    <div class="stat-mini-box">
                        <strong>4</strong>
                        <span>Equipos</span>
                    </div>
                    <div class="stat-mini-box">
                        <strong>28</strong>
                        <span>Partidos</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- COLUMNA DERECHA: Contenido Principal (Pestañas / Listados) -->
        <section class="profile-main-content">

            <!-- Navegación interna (Tabs) -->
            <div class="profile-tabs">
                <button class="tab-btn active"><i class="fa-solid fa-trophy"></i> Mis Torneos</button>
                <button class="tab-btn"><i class="fa-solid fa-users"></i> Mis Equipos</button>
                <button class="tab-btn"><i class="fa-solid fa-gear"></i> Ajustes</button>
            </div>

            <!-- Contenedor del contenido del Tab activo -->
            <div class="tab-content">
                <div class="section-header">
                    <h3>Torneos en los que participas u organizas</h3>
                </div>

                <!-- Lista de torneos del usuario (Formato compacto) -->
                <div class="profile-items-list">

                    <div class="profile-item-row">
                        <div class="item-icon text-blue"><i class="fa-solid fa-soccer-ball"></i></div>
                        <div class="item-info">
                            <h4>Liga Apertura 2024</h4>
                            <p>Estado: <span class="status-indicator live">En curso</span> • Rol: Jugador</p>
                        </div>
                        <a href="#" class="btn-item-action">Ver torneo <i class="fa-solid fa-chevron-right"></i></a>
                    </div>

                    <div class="profile-item-row">
                        <div class="item-icon text-purple"><i class="fa-solid fa-gamepad"></i></div>
                        <div class="item-info">
                            <h4>Torneo Gamer X</h4>
                            <p>Estado: <span class="status-indicator upcoming">Próximo</span> • Rol: Organizador</p>
                        </div>
                        <a href="#" class="btn-item-action">Gestionar <i class="fa-solid fa-chevron-right"></i></a>
                    </div>

                    <div class="profile-item-row">
                        <div class="item-icon text-orange"><i class="fa-solid fa-chess-knight"></i></div>
                        <div class="item-info">
                            <h4>Master de Ajedrez Nocturno</h4>
                            <p>Estado: <span class="status-indicator ended">Finalizado</span> • Rol: Jugador</p>
                        </div>
                        <a href="#" class="btn-item-action">Ver posiciones <i class="fa-solid fa-chevron-right"></i></a>
                    </div>

                </div>
            </div>

        </section>

    </main>

    <?php require_once __DIR__ . '/../include/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const avatarInput = document.getElementById('avatarInput');
            const btnTriggerInput = document.getElementById('btnTriggerInput');
            const avatarPreview = document.getElementById('avatarPreview');
            const btnSaveAvatar = document.getElementById('btnSaveAvatar');

            // 1. Al hacer clic en el botón de la cámara, simula el clic en el input file oculto
            btnTriggerInput.addEventListener('click', function () {
                avatarInput.click();
            });

            // 2. Capturar el cambio cuando el usuario selecciona una imagen
            avatarInput.addEventListener('change', function () {
                const file = this.files[0];

                if (file) {
                    // Validar que sea una imagen
                    if (!file.type.startsWith('image/')) {
                        alert('Por favor, selecciona un archivo de imagen válido.');
                        return;
                    }

                    // Mostrar previsualización en tiempo real
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        avatarPreview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);

                    // Ocultar icono de cámara y mostrar botón de confirmación verde
                    btnTriggerInput.style.display = 'none';
                    btnSaveAvatar.style.display = 'flex';
                }
            });
        });
    </script>
</body>

</html>