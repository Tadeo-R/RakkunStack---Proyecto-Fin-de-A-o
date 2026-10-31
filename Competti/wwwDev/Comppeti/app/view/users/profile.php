<?php
// 1. OBLIGATORIO: Iniciar sesión antes que cualquier otra cosa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirección unificada de seguridad usando el enrutador de la raíz
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /index.php?view=auth/login");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

try {
    // A. OBTENER EL ID DE ORGANIZADOR (Si es que el usuario ya creó algún torneo)
    $stmtGetOrg = $pdo->prepare("SELECT id_organizador FROM ORGANIZADOR WHERE id_usuario = :id LIMIT 1");
    $stmtGetOrg->execute([':id' => $id_usuario]);
    $id_organizador = $stmtGetOrg->fetchColumn();

    // B. LISTAR MIS TORNEOS CREADOS (Donde soy el Organizador oficial)
    $misTorneos = [];
    if ($id_organizador) {
        $misTorneos = $modeloTorneo->listarPorOrganizador($id_usuario);
    }
    $totalTorneos = count($misTorneos);

    // C. CONTEO DE TORNEOS EN LOS QUE PARTICIPO COMO JUGADOR (Tabla INSCRIBEN y PERTENECE)
    $stmtParticipando = $pdo->prepare("SELECT COUNT(DISTINCT i.id_torneo) FROM INSCRIBEN i
                                       INNER JOIN EQUIPO e ON i.id_equipo = e.id_equipo
                                       INNER JOIN PERTENECE p ON e.id_equipo = p.id_equipo
                                       INNER JOIN PARTICIPANTE pt ON p.id_participante = pt.id_participante
                                       WHERE pt.id_usuario = :id_usuario");
    $stmtParticipando->execute([':id_usuario' => $id_usuario]);
    $totalParticipando = (int) $stmtParticipando->fetchColumn();

    // D. PAGINACIÓN DE MIS EQUIPOS (De 3 en 3)
    $porPagina = 3;
    $paginaActualEq = isset($_GET['p_eq']) ? max(1, (int) $_GET['p_eq']) : 1;
    $offsetEq = ($paginaActualEq - 1) * $porPagina;

    // Consulta total para calcular páginas
    $stmtCountEq = $pdo->prepare("
        SELECT COUNT(DISTINCT e.id_equipo) FROM EQUIPO e
        LEFT JOIN ORGANIZADOR o ON e.id_organizador_creador = o.id_organizador
        LEFT JOIN PERTENECE p ON e.id_equipo = p.id_equipo
        LEFT JOIN PARTICIPANTE pt ON p.id_participante = pt.id_participante
        WHERE o.id_usuario = :id_user OR pt.id_usuario = :id_user2 OR e.nombreEquipo LIKE :filtro
    ");
    $stmtCountEq->execute([':id_user' => $id_usuario, ':id_user2' => $id_usuario, ':filtro' => 'Inscripción - ' . $_SESSION['usuario_nombre'] . '%']);
    $totalRegistrosEq = (int) $stmtCountEq->fetchColumn();
    $totalPaginasEq = ceil($totalRegistrosEq / $porPagina);

    // Consulta paginada real
    $stmtMisEq = $pdo->prepare("
        SELECT DISTINCT e.* FROM EQUIPO e
        LEFT JOIN ORGANIZADOR o ON e.id_organizador_creador = o.id_organizador
        LEFT JOIN PERTENECE p ON e.id_equipo = p.id_equipo
        LEFT JOIN PARTICIPANTE pt ON p.id_participante = pt.id_participante
        WHERE o.id_usuario = :id_user 
           OR pt.id_usuario = :id_user2 
           OR e.nombreEquipo LIKE :filtro
        ORDER BY e.id_equipo DESC
        LIMIT :limite OFFSET :offset
    ");
    $stmtMisEq->bindValue(':id_user', $id_usuario, PDO::PARAM_INT);
    $stmtMisEq->bindValue(':id_user2', $id_usuario, PDO::PARAM_INT);
    $stmtMisEq->bindValue(':filtro', 'Inscripción - ' . $_SESSION['usuario_nombre'] . '%', PDO::PARAM_STR);
    $stmtMisEq->bindValue(':limite', $porPagina, PDO::PARAM_INT);
    $stmtMisEq->bindValue(':offset', $offsetEq, PDO::PARAM_INT);
    $stmtMisEq->execute();
    $misEscuadrasPerfil = $stmtMisEq->fetchAll(PDO::FETCH_ASSOC);
    $totalEquipos = $totalRegistrosEq;

    // El contador de la caja de equipos lee el tamaño real de este listado seguro
    $totalEquipos = count($misEscuadrasPerfil);

} catch (PDOException $e) {
    // Respaldo de seguridad para que la página renderice limpia en caso de cualquier error relacional
    $totalTorneos = 0;
    $totalEquipos = 0;
    $totalParticipando = 0;
    $misTorneos = [];
    $misEscuadrasPerfil = [];
}

$tabActiva = $_GET['tab'] ?? 'torneos';
?>

<link rel="stylesheet" href="/css/global.css">
<link rel="stylesheet" href="/css/profile.css">

<body>
    <main class="main-content profile-layout">

        <!-- BARRA LATERAL DEL PERFIL -->
        <aside class="profile-sidebar">
            <div class="profile-card">

                <!-- Formulario de cambio de Avatar -->
                <form action="/index.php?action=upload_avatar" method="POST" enctype="multipart/form-data">
                    <div class="avatar-wrapper"
                        style="position: relative; display: inline-block; width: 100px; height: 100px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.2);">
                        <?php
                        // Si la sesión tiene un avatar (de Google o subido), lo usamos; de lo contrario, el por defecto
                        $avatar_actual = (!empty($_SESSION['usuario_avatar'])) ? $_SESSION['usuario_avatar'] : '/img/default-avatar.jpg';
                        ?>
                        <img src="<?= htmlspecialchars($avatar_actual) ?>" alt="Avatar" class="profile-avatar"
                            id="avatarPreview" style="width: 100%; height: 100%; object-fit: cover; display: block;"
                            onerror="this.src='/img/default-avatar.jpg'">

                        <input type="file" name="profile_picture" id="avatarInput" accept="image/*"
                            style="display: none;">

                        <button type="button" class="btn-change-avatar" id="btnTriggerInput" title="Cambiar foto">
                            <i class="fa-solid fa-camera"></i>
                        </button>

                        <button type="submit" class="btn-save-avatar" id="btnSaveAvatar" style="display: none;"
                            title="Guardar cambios">
                            <i class="fa-solid fa-check"></i>
                        </button>
                    </div>
                </form>

                <!-- BUSCA ESTE BLOQUE EN LA BARRA LATERAL DE TU PROFILE.PHP Y REEMPLÁZALO -->
                <div class="profile-info-header">
                    <h2 class="profile-name"><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></h2>

                    <!-- CORREGIDO: El rol cambia dinámicamente si el usuario no ha fundado torneos -->
                    <?php if ($totalTorneos > 0): ?>
                        <span class="profile-badge admin"
                            style="background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%);"><i
                                class="fa-solid fa-shield-halved"></i> Organizador</span>
                    <?php else: ?>
                        <span class="profile-badge admin"
                            style="background: linear-gradient(135deg, #34d399 0%, #3b82f6 100%);"><i
                                class="fa-solid fa-gamepad"></i> Competidor</span>
                    <?php endif; ?>
                </div>

                <hr class="divider">

                <div class="profile-meta">
                    <?php
                    if (!empty($_SESSION['usuario_fecha_registro'])) {
                        $fechaObj = new DateTime($_SESSION['usuario_fecha_registro']);
                        $meses = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"];
                        $mesTexto = $meses[(int) $fechaObj->format('m') - 1];
                        $añoTexto = $fechaObj->format('Y');
                        $fechaFormateada = ucfirst($mesTexto) . " de " . $añoTexto;
                    } else {
                        $fechaFormateada = "Reciente";
                    }
                    ?>
                    <p><i class="fa-regular fa-calendar-days"></i> Miembro desde: <?= $fechaFormateada ?></p>
                    <p id="sidebar-location-display">
                        <i class="fa-solid fa-location-dot"></i>
                        <?= !empty($_SESSION['usuario_ubicacion']) ? htmlspecialchars($_SESSION['usuario_ubicacion']) : 'No especificada' ?>
                    </p>
                </div>

                <hr class="divider">

                <div class="profile-stats-mini">
                    <div class="stat-mini-box" title="Torneos creados por ti">
                        <strong><?= (int) $totalTorneos ?></strong>
                        <span>Mis Torneos</span>
                    </div>
                    <div class="stat-mini-box" title="Equipos vinculados a tu cuenta">
                        <strong><?= (int) $totalEquipos ?></strong>
                        <span>Equipos</span>
                    </div>
                    <div class="stat-mini-box" title="Torneos en los que estás compitiendo actualmente">
                        <strong><?= (int) $totalParticipando ?></strong>
                        <span>Participando</span>
                    </div>
                </div>


        </aside>
        <!-- PANEL DE CONTENIDO PRINCIPAL INTERACTIVO -->
        <section class="profile-main-content">

            <!-- CONTENIDO CONTENEDOR DINÁMICO -->
            <div class="profile-tabs-wrapper">
                <div class="profile-tabs">
                    <button class="tab-btn <?= $tabActiva === 'torneos' ? 'active' : '' ?>" data-target="tab-torneos"><i
                            class="fa-solid fa-trophy"></i> Mis Torneos</button>
                    <button class="tab-btn <?= $tabActiva === 'equipos' ? 'active' : '' ?>" data-target="tab-equipos"><i
                            class="fa-solid fa-users"></i> Mis Equipos</button>
                    <button class="tab-btn <?= $tabActiva === 'ajustes' ? 'active' : '' ?>" data-target="tab-ajustes"><i
                            class="fa-solid fa-gear"></i> Ajustes</button>
                </div>
            </div>
            <div class="profile-tab-container">

                <!-- TAB 1: TORNEOS -->
                <div class="tab-content-panel <?= $tabActiva === 'torneos' ? 'active' : '' ?>" id="tab-torneos">
                    <div class="section-header">
                        <h3>Torneos Activos e Históricos</h3>
                        <span class="badge-count"><?= count($misTorneos) ?> Registrados</span>
                    </div>

                    <?php if (empty($misTorneos)): ?>
                        <!-- Mensaje de vacío si no hay registros -->
                        <div style="text-align: center; padding: 60px 20px; color: var(--auth-text-muted);">
                            <i class="fa-solid fa-trophy"
                                style="font-size: 2.8rem; margin-bottom: 16px; display: block; color: rgba(255, 255, 255, 0.15);"></i>
                            <h4 style="color: #fff; margin: 0 0 8px 0; font-size: 1.1rem;">No tienes torneos creados</h4>
                            <p style="margin: 0; font-size: 0.9rem; max-width: 360px; margin: 0 auto; line-height: 1.5;">
                                ¡Comienza la competencia! Cuando crees tu primer torneo deportivo o videojuego, aparecerá
                                listado en este panel.</p>
                        </div>
                    <?php else: ?>
                        <!-- Lista de tarjetas dinámicas reales extraídas de phpMyAdmin -->
                        <div class="profile-items-list"
                            style="display: flex; flex-direction: column; gap: 16px; width: 100%;">
                            <?php foreach ($misTorneos as $torneo):
                                $esLiga = ($torneo['formato'] === 'liga');
                                $iconoTorneo = ($torneo['categoria'] === 'esports') ? 'fa-gamepad' : 'fa-trophy';
                                ?>
                                <div class="profile-item-card">
                                    <div class="item-badge-status live"><?= htmlspecialchars($torneo['estado']) ?></div>
                                    <div class="item-header-row">
                                        <div class="item-icon text-blue"><i class="fa-solid <?= $iconoTorneo ?>"></i></div>
                                        <div class="item-details">
                                            <h4><?= htmlspecialchars($torneo['nombreTorneo']) ?></h4>
                                            <p class="item-subtext"><?= htmlspecialchars($torneo['juego']) ?> • Formato
                                                <?= htmlspecialchars($torneo['formato']) ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="item-footer-row">
                                        <span class="user-role-tag"><i class="fa-solid fa-user-gear"></i> Organizador</span>
                                        <?php
                                        $idRealPerfil = !empty($torneo['id_torneo']) ? $torneo['id_torneo'] : (!empty($torneo['id']) ? $torneo['id'] : null);
                                        ?>
                                        <a href="/index.php?view=torneos/detalles&id=<?= (int) $idRealPerfil ?>"
                                            class="btn-item-action">
                                            Administrar Torneo <i class="fa-solid fa-chevron-right"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- TAB 2: MIS EQUIPOS REGISTRADOS EN TU PROFILE.PHP -->
                <div class="tab-content-panel <?= $tabActiva === 'equipos' ? 'active' : '' ?>" id="tab-equipos">
                    <div class="section-header">
                        <h3>Mis Equipos Registrados</h3>
                        <span class="badge-count"><?= (int) $totalEquipos ?> Registrados</span>
                    </div>

                    <?php if (empty($misEscuadrasPerfil)): ?>
                        <div style="text-align: center; padding: 40px; color: var(--auth-text-muted);">
                            <i class="fa-solid fa-users-slash"
                                style="font-size: 2.5rem; margin-bottom: 12px; display: block;"></i>
                            <p>Aún no formas parte de ningún equipo en esta temporada.</p>
                        </div>
                    <?php else: ?>
                        <div class="profile-items-list"
                            style="display: flex; flex-direction: column; gap: 16px; width: 100%;">
                            <?php foreach ($misEscuadrasPerfil as $esc):
                                $nombreLimpioEsc = strpos($esc['nombreEquipo'], 'Inscripción -') !== false
                                    ? 'Escuadra de ' . htmlspecialchars($_SESSION['usuario_nombre'])
                                    : htmlspecialchars($esc['nombreEquipo']);
                                ?>
                                <div class="profile-item-card"
                                    style="background: rgba(255,255,255,0.01); border: 1px solid rgba(255,255,255,0.04); padding: 18px; border-radius: 14px; display: flex; justify-content: space-between; align-items: center; gap: 16px;">
                                    <div class="item-header-row" style="display: flex; align-items: center; gap: 14px;">
                                        <div class="item-icon text-blue" style="color: #c084fc; font-size: 1.5rem;"><i
                                                class="fa-solid fa-shield-halved"></i></div>
                                        <div class="item-details">
                                            <h4 style="margin: 0 0 4px 0; color: #fff; font-size: 1.05rem;">
                                                <?= $nombreLimpioEsc ?>
                                            </h4>
                                            <span class="item-subtext" style="color: #94a3b8; font-size: 0.85rem;">
                                                <i class="fa-solid fa-circle"
                                                    style="font-size: 0.5rem; color: #34d399; vertical-align: middle; margin-right: 4px;"></i>
                                                Club <?= htmlspecialchars(ucfirst($esc['estado'])) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- BOTÓN DE ACCIÓN PARA NAVEGAR AL PERFIL DEL EQUIPO -->
                                    <a href="/index.php?view=equipos/perfilEquipos&id=<?= (int) $esc['id_equipo'] ?>"
                                        class="btn-item-action" style="white-space: nowrap;">
                                        Ver Perfil del Equipo <i class="fa-solid fa-chevron-right"
                                            style="margin-left: 6px;"></i>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if ($totalPaginasEq > 1): ?>
                            <div style="display: flex; justify-content: center; gap: 8px; margin-top: 20px;">
                                <?php for ($i = 1; $i <= $totalPaginasEq; $i++): ?>
                                    <a href="/index.php?view=users/profile&tab=equipos&p_eq=<?= $i ?>"
                                        style="padding: 8px 14px; border-radius: 8px; font-weight: 700; text-decoration: none; background: <?= $i === $paginaActualEq ? '#a855f7' : 'rgba(255,255,255,0.05)' ?>; color: #fff; border: 1px solid rgba(255,255,255,0.1);">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                </div>

                <!-- TAB 3: NUEVO PANEL DE AJUSTES EDITABLES -->
                <div class="tab-content-panel <?= $tabActiva === 'ajustes' ? 'active' : '' ?>" id="tab-ajustes">
                    <div class="section-header">
                        <h3>Configuración del Perfil</h3>
                    </div>

                    <form action="/index.php?action=update_profile_data" method="POST"
                        style="display: flex; flex-direction: column; gap: 20px; width: 100%;">

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; width: 100%;">
                            <div class="input-group">
                                <label
                                    style="color: #cbd5e1; font-weight: 600; font-size: 0.85rem; text-transform: none; letter-spacing: normal;">Nombre
                                    Completo</label>
                                <input type="text" name="nombre"
                                    value="<?= htmlspecialchars($_SESSION['usuario_nombre']) ?>"
                                    style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 12px; border-radius: 8px;"
                                    required>
                            </div>

                            <div class="input-group">
                                <label
                                    style="color: #cbd5e1; font-weight: 600; font-size: 0.85rem; text-transform: none; letter-spacing: normal;">Ubicación</label>
                                <input type="text" name="ubicacion" <?= !empty($_SESSION['usuario_ubicacion']) ? htmlspecialchars($_SESSION['usuario_ubicacion']) : 'No especificada' ?>
                                    style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 12px; border-radius: 8px;">
                            </div>
                        </div>

                        <div class="input-group" style="width: 100%;">
                            <label
                                style="color: #cbd5e1; font-weight: 600; font-size: 0.85rem; text-transform: none; letter-spacing: normal;">Biografía
                                / Descripción corta</label>
                            <textarea name="biografia" rows="3"
                                style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 12px; border-radius: 8px; font-family: inherit; resize: none; width: 100%; box-sizing: border-box;"><?= htmlspecialchars($_SESSION['usuario_bio'] ?? '') ?></textarea>
                        </div>

                        <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.05); margin: 10px 0;">

                        <div class="section-header" style="padding: 0; margin: 0;">
                            <h4 style="color: #fff; margin: 0;">Cambiar Contraseña</h4>
                        </div>

                        <div
                            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; width: 100%; box-sizing: border-box;">
                            <div class="input-group" style="display: flex; flex-direction: column; width: 100%;">
                                <label
                                    style="color: #cbd5e1; font-weight: 600; font-size: 0.85rem; margin-bottom: 6px;">Nueva
                                    Contraseña</label>
                                <input type="password" name="nueva_password"
                                    placeholder="Dejar en blanco para mantener actual"
                                    style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 12px; border-radius: 8px; width: 100%; box-sizing: border-box;">
                            </div>
                            <div class="input-group" style="display: flex; flex-direction: column; width: 100%;">
                                <label
                                    style="color: #cbd5e1; font-weight: 600; font-size: 0.85rem; margin-bottom: 6px;">Confirmar
                                    Nueva Contraseña</label>
                                <input type="password" name="confirmar_nueva_password"
                                    placeholder="Repite la nueva contraseña"
                                    style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 12px; border-radius: 8px; width: 100%; box-sizing: border-box;">
                            </div>
                        </div>

                        <button type="submit" class="btn-auth-submit"
                            style="max-width: 200px; margin-top: 10px; align-self: flex-end; padding: 12px 24px; border-radius: 8px;">
                            Guardar Ajustes <i class="fa-solid fa-floppy-disk"></i>
                        </button>
                    </form>
                </div>

            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- GESTIÓN DINÁMICA DEL AVATAR ---
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

            // --- NAVEGACIÓN DINÁMICA DE PESTAÑAS (TABS) ---
            const tabButtons = document.querySelectorAll('.tab-btn');
            const contentPanels = document.querySelectorAll('.tab-content-panel');

            tabButtons.forEach(button => {
                button.addEventListener('click', function () {
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    const targetPanelId = this.getAttribute('data-target');

                    // Extraemos el nombre limpio de la pestaña (ej: 'equipos' de 'tab-equipos')
                    const nombreTab = targetPanelId.replace('tab-', '');

                    contentPanels.forEach(panel => {
                        panel.classList.remove('active');
                        if (panel.id === targetPanelId) {
                            panel.classList.add('active');
                        }
                    });

                    // Actualizamos la URL sin recargar la página para mantener la pestaña seleccionada
                    const nuevaURL = new URL(window.location);
                    nuevaURL.searchParams.set('tab', nombreTab);
                    window.history.replaceState({}, '', nuevaURL);
                });
            });
        });
    </script>

    <style>
        .tab-content-panel {
            display: none;
            width: 100%;
        }

        .tab-content-panel.active {
            display: flex;
            flex-direction: column;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</body>