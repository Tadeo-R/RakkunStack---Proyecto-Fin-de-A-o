<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Capturar y validar el ID del equipo por la URL
$id_equipo = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id_equipo) {
    echo "<script>window.location.href='/index.php?view=equipos';</script>";
    exit;
}

try {
    // 2. Obtener la información base del equipo (Tabla EQUIPO)
    $stmtEq = $pdo->prepare("SELECT * FROM EQUIPO WHERE id_equipo = :id LIMIT 1");
    $stmtEq->execute([':id' => $id_equipo]);
    $equipo = $stmtEq->fetch(PDO::FETCH_ASSOC);

    // Control de rescate si el club no exist
    if (!$equipo) {
        echo "<script>window.location.href='/index.php?view=equipos';</script>";
        exit;
    }

    // 3. Obtener los datos del Fundador/Organizador creador (Usuario y Ubicación)
    $stmtFundador = $pdo->prepare("SELECT u.id_usuario, u.nombre, u.ubicacion, u.fechaRegistro FROM ORGANIZADOR o 
                                   INNER JOIN USUARIO u ON o.id_usuario = u.id_usuario 
                                   WHERE o.id_organizador = :id_org LIMIT 1");
    $stmtFundador->execute([':id_org' => (int) $equipo['id_organizador_creador']]);
    $datosFundador = $stmtFundador->fetch(PDO::FETCH_ASSOC);

    $nombreFundador = $datosFundador['nombre'] ?? 'Administrador Institucional';

    // 4. CONSULTA DE INTEGRANTES: Une PERTENECE -> PARTICIPANTE -> USUARIO
    $stmtMiembros = $pdo->prepare("SELECT u.id_usuario, u.nombre, u.ubicacion, u.fechaRegistro, p.esCapitan 
                                   FROM PERTENECE p
                                   INNER JOIN PARTICIPANTE pt ON p.id_participante = pt.id_participante
                                   INNER JOIN USUARIO u ON pt.id_usuario = u.id_usuario
                                   WHERE p.id_equipo = :id_equipo");
    $stmtMiembros->execute([':id_equipo' => $id_equipo]);
    $integrantes = $stmtMiembros->fetchAll(PDO::FETCH_ASSOC);

    // Asegurarnos de que el fundador aparezca en el roster si no está duplicado
    $fundadorEnRoster = false;
    if ($datosFundador) {
        foreach ($integrantes as $ig) {
            if ($ig['id_usuario'] == $datosFundador['id_usuario']) {
                $fundadorEnRoster = true;
                break;
            }
        }
        // Si el creador no está en la tabla PERTENECE, lo inyectamos como Líder (esCapitan = 1)
        if (!$fundadorEnRoster) {
            array_unshift($integrantes, [
                'id_usuario' => $datosFundador['id_usuario'],
                'nombre' => $datosFundador['nombre'],
                'ubicacion' => $datosFundador['ubicacion'],
                'fechaRegistro' => $datosFundador['fechaRegistro'],
                'esCapitan' => 1
            ]);
        }
    }

    // 5. Listar torneos específicos en los que compite este equipo
    $stmtTorneosEq = $pdo->prepare("SELECT DISTINCT t.id_torneo, t.nombreTorneo, t.juego, t.estado 
                                    FROM INSCRIBEN i
                                    INNER JOIN TORNEO t ON i.id_torneo = t.id_torneo
                                    WHERE i.id_equipo = :id_equipo ORDER BY t.id_torneo DESC");
    $stmtTorneosEq->execute([':id_equipo' => $id_equipo]);
    $torneosActivos = $stmtTorneosEq->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("<div style='color:white; background:#ff4d4d; padding:20px; font-family:sans-serif; border-radius:10px;'><b>Error de Perfil de Club:</b> " . htmlspecialchars($e->getMessage()) . "</div>");
}

// Filtro estético para limpiar nombres
$nombreClubLimpio = strpos($equipo['nombreEquipo'], 'Inscripción -') !== false
    ? str_replace('Inscripción -', 'Escuadra de', $equipo['nombreEquipo'])
    : $equipo['nombreEquipo'];
?>

<link rel="stylesheet" href="/css/global.css">
<style>
    .team-profile-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
        color: #f8fafc;
        font-family: 'Inter', sans-serif;
        box-sizing: border-box;
    }

    .team-hero-card {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.6) 0%, rgba(30, 27, 75, 0.4) 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 40px;
        border-radius: 24px;
        margin-bottom: 35px;
        backdrop-filter: blur(12px);
        display: flex;
        align-items: center;
        gap: 30px;
        box-sizing: border-box;
    }

    .team-avatar-big {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        color: #fff;
        box-shadow: 0 8px 24px rgba(168, 85, 247, 0.25);
    }

    .team-title-info h1 {
        margin: 0 0 6px 0;
        font-size: 2.2rem;
        font-weight: 900;
        color: #fff;
    }

    .team-badge-status {
        display: inline-block;
        background: rgba(52, 211, 153, 0.1);
        border: 1px solid rgba(52, 211, 153, 0.2);
        color: #34d399;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-top: 6px;
    }

    /* CORREGIDO: align-items: start para evitar el desfase y estiramiento forzado de bloques */
    .profile-split {
        display: grid;
        grid-template-columns: 1fr;
        gap: 30px;
        width: 100%;
        box-sizing: border-box;
        align-items: start;
    }

    @media (min-width: 900px) {
        .profile-split {
            grid-template-columns: 60% 40%;
        }
    }

    .section-card {
        background: rgba(15, 23, 42, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 20px;
        padding: 24px;
        backdrop-filter: blur(12px);
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
    }

    .section-card h2 {
        font-size: 1.3rem;
        font-weight: 850;
        margin: 0 0 20px 0;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        padding-bottom: 12px;
    }

    .member-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.04);
        padding: 16px;
        border-radius: 14px;
        margin-bottom: 12px;
        box-sizing: border-box;
    }

    .member-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .member-icon {
        width: 42px;
        height: 42px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #cbd5e1;
    }

    .member-name {
        font-weight: 700;
        color: #fff;
        margin: 0 0 2px 0;
        font-size: 1rem;
    }

    .member-meta {
        margin: 0;
        color: #64748b;
        font-size: 0.82rem;
    }

    .tournament-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 8px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        box-sizing: border-box;
    }
</style>

<div class="team-profile-container">

    <!-- CABECERA HERO DEL EQUIPO -->
    <header class="team-hero-card">
        <div class="team-avatar-big">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <div class="team-title-info">
            <h1><?= htmlspecialchars($nombreClubLimpio) ?></h1>
            <p style="margin: 0; color: #94a3b8; font-size: 0.95rem;"><i class="fa-solid fa-crown"
                    style="color: #fbbf24; margin-right: 4px;"></i> Liderado por: <span
                    style="color:#fff; font-weight:700;"><?= htmlspecialchars($nombreFundador) ?></span></p>
            <span class="team-badge-status"><?= htmlspecialchars($equipo['estado']) ?> Club</span>
        </div>
    </header>

    <!-- CUERPO PRINCIPAL DIVIDIDO -->
    <div class="profile-split">

        <!-- COLUMNA IZQUIERDA: ROSTER / PLANTILLA DE JUGADORES -->
        <section class="section-card">
            <h2><i class="fa-solid fa-users text-purple" style="color: #c084fc;"></i> Roster / Plantilla de Jugadores
            </h2>

            <?php if (empty($integrantes)): ?>
                <div style="text-align: center; padding: 30px; color: #64748b;">
                    <p style="margin:0; font-size:0.88rem;">No hay miembros registrados en este equipo todavía.</p>
                </div>
            <?php else: ?>
                <?php foreach ($integrantes as $index => $jugador):
                    // CORRECCIÓN ESTRICTA: El líder absoluto es el primer usuario del roster ($index === 0) 
                    // O aquellos cuya base de datos marque explícitamente 1, pero protegiendo a los demás.
                    $esLiderReal = ($index === 0) || (!empty($jugador['esCapitan']) && (int) $jugador['esCapitan'] === 1);
                    ?>
                    <div class="member-row">
                        <div class="member-left">
                            <div class="member-icon"
                                style="<?= $esLiderReal ? 'background: rgba(168,85,247,0.1); color:#c084fc;' : '' ?>">
                                <i class="fa-solid <?= $esLiderReal ? 'fa-user-shield' : 'fa-user' ?>"></i>
                            </div>
                            <div>
                                <h4 class="member-name"><?= htmlspecialchars($jugador['nombre']) ?></h4>
                                <p class="member-meta">
                                    <i class="fa-solid fa-location-dot" style="font-size:0.75rem;"></i>
                                    <?= !empty($jugador['ubicacion']) ? htmlspecialchars($jugador['ubicacion']) : 'Ubicación Reservada' ?>
                                </p>
                            </div>
                        </div>
                        <?php if ($esLiderReal && $index === 0): ?>
                            <span
                                style="background: rgba(168,85,247,0.15); color: #c084fc; padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">LÍDER</span>
                        <?php else: ?>
                            <span style="color: #94a3b8; font-size: 0.8rem; font-weight: 600;">Miembro #<?= $index + 1 ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php
            // Comprobamos si el usuario actual es el creador del equipo
            $esOrganizadorCreador = false;
            if (isset($_SESSION['usuario_id'])) {
                $stmtVerifOrg = $pdo->prepare("SELECT COUNT(*) FROM ORGANIZADOR WHERE id_usuario = :id_user AND id_organizador = :id_org");
                $stmtVerifOrg->execute([
                    ':id_user' => $_SESSION['usuario_id'],
                    ':id_org' => (int) $equipo['id_organizador_creador']
                ]);
                $esOrganizadorCreador = ($stmtVerifOrg->fetchColumn() > 0);
            }
            ?>

            <?php if (!$esOrganizadorCreador): ?>
                <!-- Botón de salida solo visible para participantes comunes -->
                <div style="margin-top: 20px; width: 100%;">
                    <a href="/index.php?action=salir_equipo&id_equipo=<?= $id_equipo ?>"
                        style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444; width: 100%; padding: 12px; border-radius: 10px; font-weight: 700; font-size: 0.9rem; text-decoration: none; display: block; text-align: center; box-sizing: border-box;"
                        onclick="return confirm('¿Estás seguro de que deseas salirte de este equipo?');">
                        <i class="fa-solid fa-right-from-bracket"></i> Salirse del equipo
                    </a>
                </div>
            <?php endif; ?>
        </section>

        <!-- COLUMNA DERECHA: HISTORIAL DE TORNEOS DE ESTE CLUB -->
        <section class="section-card">
            <h2><i class="fa-solid fa-trophy" style="color: #6366f1;"></i> Competencias del Equipo</h2>

            <?php if (empty($torneosActivos)): ?>
                <div style="text-align: center; padding: 40px; color: #64748b; margin: auto;">
                    <i class="fa-solid fa-folder-open"
                        style="font-size: 2.2rem; margin-bottom: 10px; display: block; opacity: 0.2;"></i>
                    <p style="margin:0; font-size:0.88rem;">Este club no se ha inscrito a ningún torneo deportivo en esta
                        temporada de COMPETTI.</p>
                </div>
            <?php else: ?>
                <?php foreach ($torneosActivos as $tActive): ?>
                    <div class="tournament-item">
                        <div>
                            <h4 style="margin:0; font-size:1rem; color:#fff;"><?= htmlspecialchars($tActive['nombreTorneo']) ?>
                            </h4>
                            <p style="margin:0; font-size:0.82rem; color:#94a3b8;"><i class="fa-solid fa-gamepad"
                                    style="margin-right:4px;"></i> <?= htmlspecialchars($tActive['juego']) ?></p>
                        </div>
                        <span
                            style="background: <?= $tActive['estado'] === 'activo' ? 'rgba(52, 211, 153, 0.1)' : 'rgba(239, 68, 68, 0.1)' ?>; border: 1px solid <?= $tActive['estado'] === 'activo' ? 'rgba(52, 211, 153, 0.2)' : 'rgba(239, 68, 68, 0.2)' ?>; color: <?= $tActive['estado'] === 'activo' ? '#34d399' : '#ef4444' ?>; padding: 4px 12px; border-radius: 12px; font-size: 0.78rem; font-weight: 700; text-transform: uppercase;"><?= htmlspecialchars(strtoupper($tActive['estado'])) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </div>
</div>