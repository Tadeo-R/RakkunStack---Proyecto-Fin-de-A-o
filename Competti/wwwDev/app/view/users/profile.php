<?php
// 1. OBLIGATORIO: Iniciar sesión antes que cualquier otra cosa para que el Nav reconozca al usuario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../config/config.php';

// Redirección de seguridad: Si no hay sesión activa, mandarlo al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: " . BASE_URL . "app/view/auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Torneos</title>
    <script src="https://kit.fontawesome.com/3bd97052d9.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>html/css/global.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>html/css/profile.css">
</head>

<body>

    <?php require_once ROOT_PATH . '/app/view/include/nav.php'; ?>

    <main class="main-content profile-layout">

        <aside class="profile-sidebar">
            <div class="profile-card">

                <!-- MODIFICADO: Agregamos un formulario real para enviar la imagen al servidor -->
                <!-- REEMPLAZA TU ETIQUETA FORM ACTUAL POR ESTA: -->
                <form action="<?= BASE_URL ?>app/index.php?action=upload_avatar" method="POST" enctype="multipart/form-data">

                    <div class="avatar-wrapper">
                        <!-- Mostramos la imagen de la sesión si existe, de lo contrario la default -->
                        <?php
                        $avatar_actual = (!empty($_SESSION['usuario_avatar'])) ? $_SESSION['usuario_avatar'] : 'html/img/default-avatar.jpg';
                        ?>
                        <img src="<?= BASE_URL . $avatar_actual ?>" alt="Avatar" class="profile-avatar"
                            id="avatarPreview">

                        <!-- El atributo "name" es clave para que PHP detecte el archivo -->
                        <input type="file" name="profile_picture" id="avatarInput" accept="image/*"
                            style="display: none;">

                        <button type="button" class="btn-change-avatar" id="btnTriggerInput" title="Cambiar foto">
                            <i class="fa-solid fa-camera"></i>
                        </button>

                        <!-- MODIFICADO: Cambiado a type="submit" para que envíe el formulario -->
                        <button type="submit" class="btn-save-avatar" id="btnSaveAvatar" style="display: none;"
                            title="Guardar cambios">
                            <i class="fa-solid fa-check"></i>
                        </button>
                    </div>
                </form>

                <div class="profile-info-header">
                    <!-- Dinamizamos el nombre usando la sesión activa real -->
                    <h2 class="profile-name"><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></h2>
                    <p class="profile-username">@<?= htmlspecialchars($_SESSION['usuario_username'] ?? 'usuario') ?></p>
                    <span class="profile-badge admin"><i class="fa-solid fa-shield-halved"></i> Organizador</span>
                </div>

                <hr class="divider">

                <div class="profile-meta">
                    <p><i class="fa-regular fa-calendar-days"></i> Miembro desde: May 2024</p>
                    <p><i class="fa-solid fa-location-dot"></i> Buenos Aires, Argentina</p>
                </div>

                <hr class="divider">

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

        <section class="profile-main-content">

            <div class="profile-tabs-wrapper">
                <div class="profile-tabs">
                    <button class="tab-btn active"><i class="fa-solid fa-trophy"></i> Mis Torneos</button>
                    <button class="tab-btn"><i class="fa-solid fa-users"></i> Mis Equipos</button>
                    <button class="tab-btn"><i class="fa-solid fa-gear"></i> Ajustes</button>
                </div>
            </div>

            <div class="tab-content">
                <div class="section-header">
                    <h3>Torneos Activos e Históricos</h3>
                    <span class="badge-count">3 Registrados</span>
                </div>

                <div class="profile-items-list">
                    <div class="profile-item-card">
                        <div class="item-badge-status live">En curso</div>
                        <div class="item-header-row">
                            <div class="item-icon text-blue"><i class="fa-solid fa-futbol"></i></div>
                            <div class="item-details">
                                <h4>Liga Apertura 2026</h4>
                                <p class="item-subtext">Fútbol 11 • Formato Liga</p>
                            </div>
                        </div>
                        <div class="item-footer-row">
                            <span class="user-role-tag"><i class="fa-solid fa-user"></i> Jugador</span>
                            <a href="#" class="btn-item-action">Ver torneo <i class="fa-solid fa-chevron-right"></i></a>
                        </div>
                    </div>

                    <div class="profile-item-card">
                        <div class="item-badge-status upcoming">Próximo</div>
                        <div class="item-header-row">
                            <div class="item-icon text-purple"><i class="fa-solid fa-gamepad"></i></div>
                            <div class="item-details">
                                <h4>Torneo Gamer X</h4>
                                <p class="item-subtext">eSports • Eliminación Directa</p>
                            </div>
                        </div>
                        <div class="item-footer-row">
                            <span class="user-role-tag admin-role"><i class="fa-solid fa-crown"></i> Organizador</span>
                            <a href="#" class="btn-item-action">Gestionar <i class="fa-solid fa-chevron-right"></i></a>
                        </div>
                    </div>

                    <div class="profile-item-card">
                        <div class="item-badge-status ended">Finalizado</div>
                        <div class="item-header-row">
                            <div class="item-icon text-orange"><i class="fa-solid fa-chess-knight"></i></div>
                            <div class="item-details">
                                <h4>Master de Ajedrez Nocturno</h4>
                                <p class="item-subtext">Juegos Mentales • Sistema Suizo</p>
                            </div>
                        </div>
                        <div class="item-footer-row">
                            <span class="user-role-tag"><i class="fa-solid fa-user"></i> Jugador</span>
                            <a href="#" class="btn-item-action">Posiciones <i class="fa-solid fa-chevron-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php require_once ROOT_PATH . '/app/view/include/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const avatarInput = document.getElementById('avatarInput');
            const btnTriggerInput = document.getElementById('btnTriggerInput');
            const avatarPreview = document.getElementById('avatarPreview');
            const btnSaveAvatar = document.getElementById('btnSaveAvatar');

            btnTriggerInput.addEventListener('click', function () {
                avatarInput.click();
            });

            avatarInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    if (!file.type.startsWith('image/')) {
                        alert('Por favor, selecciona un archivo de imagen válido.');
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        avatarPreview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);

                    btnTriggerInput.style.display = 'none';
                    btnSaveAvatar.style.display = 'flex';
                }
            });
        });
    </script>
</body>

</html>