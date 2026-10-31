<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_torneo = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id_torneo) {
    echo "<script>window.location.href='/index.php?view=torneos/torneos';</script>";
    exit;
}

try {
    // 1. Consultar la información base del torneo actual
    $stmtT = $pdo->prepare("SELECT * FROM TORNEO WHERE id_torneo = :id LIMIT 1");
    $stmtT->execute([':id' => $id_torneo]);
    $torneo = $stmtT->fetch(PDO::FETCH_ASSOC);

    if (!$torneo) {
        echo "<script>window.location.href='/index.php?view=torneos/torneos';</script>";
        exit;
    }

    // 2. Puente relacional para buscar el ID de usuario del creador
    $stmtOwner = $pdo->prepare("SELECT o.id_usuario FROM TORNEO t 
                                INNER JOIN ORGANIZADOR o ON t.id_organizador = o.id_organizador 
                                WHERE t.id_torneo = :id_torneo LIMIT 1");
    $stmtOwner->execute([':id_torneo' => $id_torneo]);
    $id_usuario_creador_real = (int) $stmtOwner->fetchColumn();

    // 3. CONSULTA CORREGIDA: Traer los equipos asociados de forma directa a este torneo.
    // Si la modalidad es individual, leemos de la tabla inscriben.
    // Si es por equipos, listamos las escuadras fundadas por el id_organizador de este torneo.
    if ($torneo['modalidad'] === 'individual') {
        $stmtE = $pdo->prepare("SELECT e.* FROM INSCRIBEN i 
                                INNER JOIN EQUIPO e ON i.id_equipo = e.id_equipo 
                                WHERE i.id_torneo = :id");
        $stmtE->execute([':id' => $id_torneo]);
        $equiposInscritos = $stmtE->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Para modalidad equipos, mostramos en la tabla de posiciones todos los equipos que el organizador dio de alta para esta competencia
        $stmtE = $pdo->prepare("SELECT * FROM EQUIPO WHERE id_organizador_creador = :id_org AND estado = 'activo' ORDER BY id_equipo DESC");
        $stmtE->execute([':id_org' => (int) $torneo['id_organizador']]);
        $equiposInscritos = $stmtE->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Consultar el fixture agendado de la tabla 'enfrentamiento'
    $stmtF = $pdo->prepare("SELECT * FROM ENFRENTAMIENTO WHERE id_torneo = :id ORDER BY id_enfrentamiento ASC");
    $stmtF->execute([':id' => $id_torneo]);
    $fixture = $stmtF->fetchAll(PDO::FETCH_ASSOC);

    // 5. Comprobar si el usuario actual ya está inscrito en este torneo de forma infalible
    $yaInscrito = false;
    $nombreMiEquipo = '';

    if (isset($_SESSION['usuario_id'])) {
        $stmtCheckInsc = $pdo->prepare("
            SELECT DISTINCT e.nombreEquipo 
            FROM INSCRIBEN i 
            INNER JOIN EQUIPO e ON i.id_equipo = e.id_equipo
            LEFT JOIN PERTENECE p ON e.id_equipo = p.id_equipo
            LEFT JOIN PARTICIPANTE pt ON p.id_participante = pt.id_participante
            WHERE i.id_torneo = :id_torneo 
              AND (pt.id_usuario = :id_user OR e.id_organizador_creador = :id_user_org)
            LIMIT 1
        ");

        // Obtenemos también el id_organizador por si acaso fundó el equipo
        $stmtOrgId = $pdo->prepare("SELECT id_organizador FROM ORGANIZADOR WHERE id_usuario = :id LIMIT 1");
        $stmtOrgId->execute([':id' => $_SESSION['usuario_id']]);
        $id_org_session = $stmtOrgId->fetchColumn() ?: 0;

        $stmtCheckInsc->execute([
            ':id_torneo' => $id_torneo,
            ':id_user' => $_SESSION['usuario_id'],
            ':id_user_org' => $id_org_session
        ]);

        $infoInscripcion = $stmtCheckInsc->fetch(PDO::FETCH_ASSOC);
        if ($infoInscripcion) {
            $yaInscrito = true;
            $nombreMiEquipo = $infoInscripcion['nombreEquipo'];
        }
    }

} catch (PDOException $e) {
    die("<div style='color:white; background:#ff4d4d; padding:20px; font-family:sans-serif; border-radius:10px;'><b>Error de Base de Datos:</b> " . htmlspecialchars($e->getMessage()) . "</div>");
}
?>

<link rel="stylesheet" href="/css/global.css">
<style>
    .details-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
        color: #f8fafc;
        font-family: 'Inter', sans-serif;
        box-sizing: border-box;
    }

    .tournament-hero {
        background: linear-gradient(135deg, rgba(30, 27, 75, 0.4) 0%, rgba(15, 23, 42, 0.8) 100%);
        border: 1px solid rgba(255, 255, 255, 0.06);
        padding: 32px;
        border-radius: 20px;
        margin-bottom: 30px;
        backdrop-filter: blur(12px);
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-sizing: border-box;
    }

    .hero-info h1 {
        margin: 0 0 8px 0;
        font-size: 2rem;
        font-weight: 850;
        background: linear-gradient(to right, #fff, #c084fc);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-meta {
        display: flex;
        gap: 16px;
        color: #94a3b8;
        font-size: 0.9rem;
    }

    .hero-meta span {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .badge-status {
        background: #22c55e;
        color: #fff;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    /* DISEÑO ADAPTATIVO SIN DESFASES */
    .split-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        width: 100%;
        box-sizing: border-box;
        align-items: stretch;
        /* Estira ambas columnas para que siempre midan lo mismo */
    }

    @media (min-width: 900px) {
        .split-layout {
            grid-template-columns: 45% 53.4%;
        }
    }

    /* Convertimos las tarjetas en contenedores flex verticales que ocupan el 100% de la altura de la columna */
    .panel-card {
        background: rgba(15, 23, 42, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 20px;
        padding: 24px;
        backdrop-filter: blur(12px);
        box-sizing: border-box;
        width: 100%;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .panel-title {
        font-size: 1.25rem;
        font-weight: 800;
        margin: 0 0 20px 0;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        padding-bottom: 12px;
    }

    .fixture-match {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.04);
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 12px;
        box-sizing: border-box;
    }

    .match-team {
        font-weight: 600;
        font-size: 0.95rem;
        width: 40%;
    }

    .match-team.home {
        text-align: right;
    }

    .match-score-box {
        background: rgba(168, 85, 247, 0.1);
        border: 1px solid rgba(168, 85, 247, 0.2);
        padding: 6px 14px;
        border-radius: 8px;
        font-weight: 800;
        color: #c084fc;
        font-size: 1rem;
        text-align: center;
        min-width: 50px;
    }

    .table-row {
        display: grid;
        grid-template-columns: 15% 45% 20% 20%;
        padding: 14px 8px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        font-size: 0.9rem;
        align-items: center;
        box-sizing: border-box;
        width: 100%;
    }

    .table-header {
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
</style>

<div class="details-container">

    <!-- CABECERA HERO DEL TORNEO -->
    <header class="tournament-hero">
        <div class="hero-info">
            <h1><?= htmlspecialchars($torneo['nombreTorneo'] ?? '') ?></h1>
            <div class="hero-meta">
                <span><i class="fa-solid fa-gamepad"></i> <?= htmlspecialchars($torneo['juego'] ?? '') ?></span>
                <span><i class="fa-solid fa-sitemap"></i> Modalidad
                    <?= htmlspecialchars(ucfirst($torneo['modalidad'] ?? '')) ?></span>
                <span><i class="fa-solid fa-layer-group"></i>
                    <?= htmlspecialchars(ucfirst($torneo['formato'] ?? '')) ?></span>
            </div>
        </div>
        <div>
            <span class="badge-status"><?= htmlspecialchars($torneo['estado'] ?? 'inscripciones') ?></span>
        </div>
    </header>

    <?php if (($_GET['error'] ?? '') === 'nombre_duplicado'): ?>
        <div
            style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444; padding: 14px; border-radius: 12px; margin-bottom: 20px; font-weight: 700; text-align: center;">
            <i class="fa-solid fa-triangle-exclamation"></i> Ya existe un equipo registrado con ese nombre en este torneo.
            Elige uno diferente.
        </div>
    <?php endif; ?>

    <?php if (($_GET['error'] ?? '') === 'cupos_llenos' && !$yaInscrito): ?>
        <div
            style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444; padding: 14px; border-radius: 12px; margin-bottom: 20px; font-weight: 700; text-align: center;">
            <i class="fa-solid fa-triangle-exclamation"></i> ¡Inscripción denegada! El torneo ha alcanzado el límite máximo
            de cupos permitidos.
        </div>
    <?php endif; ?>

    <section style="margin-bottom: 30px; width: 100%; box-sizing: border-box;">

        <?php if (!isset($_SESSION['usuario_id'])): ?>
            <!-- CASO A: USUARIO DESLOGUEADO -->
            <div
                style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 14px; display: flex; justify-content: space-between; align-items: center; box-sizing: border-box; width: 100%;">
                <p style="margin: 0; color: #94a3b8; font-size: 0.95rem;"><i class="fa-solid fa-circle-user"
                        style="color: #6366f1; margin-right: 8px;"></i> ¿Quieres competir en este torneo? Inicia sesión en
                    la plataforma para asegurar tu plaza o unirte a un equipo.</p>
                <a href="/index.php?view=auth/login" class="badge-status"
                    style="background: #6366f1; text-decoration: none; text-align: center; min-width: 120px; box-sizing: border-box; display: inline-block;">Iniciar
                    Sesión</a>
            </div>

        <?php elseif ((int) $id_usuario_creador_real === (int) $_SESSION['usuario_id']): ?>
            <!-- CASO B: PANEL EXCLUSIVO DEL ORGANIZADOR (CON VALIDADOR DE CUPOS MÁXIMOS) -->
            <div
                style="background: rgba(168, 85, 247, 0.05); border: 1px solid rgba(168, 85, 247, 0.15); padding: 24px; border-radius: 14px; display: flex; flex-direction: column; gap: 16px; box-sizing: border-box; width: 100%;">
                <div>
                    <h4 style="margin: 0 0 4px 0; color: #fff; font-weight: 800; font-size: 1.1rem;">Panel de Gestión del
                        Organizador</h4>
                    <p style="margin: 0; color: #94a3b8; font-size: 0.88rem;">Modalidad activa: <span
                            style="color:#c084fc; font-weight:700;"><?= htmlspecialchars(ucfirst($torneo['modalidad'])) ?></span>.
                        Límite del campeonato: <span style="color:#34d399; font-weight:700;"><?= count($equiposInscritos) ?>
                            / <?= (int) ($torneo['cupos'] ?? 0) ?> Equipos</span>.</p>
                </div>

                <?php if ($torneo['modalidad'] === 'equipos'): ?>
                    <?php if (!empty($torneo['cupos']) && count($equiposInscritos) >= (int) $torneo['cupos']): ?>
                        <!-- CANDADO DE SEGURIDAD VISUAL AUTOMÁTICO -->
                        <div
                            style="background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.3); color: #34d399; padding: 12px; border-radius: 8px; font-weight: 700; text-align: center; width: 100%; box-sizing: border-box;">
                            <i class="fa-solid fa-circle-check"></i> Has alcanzado el límite máximo de cupos
                            (<?= (int) $torneo['cupos'] ?>) configurados para este torneo. No puedes crear más equipos.
                        </div>
                    <?php else: ?>
                        <!-- Formulario habilitado mientras no se sature el torneo -->
                        <form action="/index.php?action=admin_create_team" method="POST"
                            style="margin: 0; display: flex; gap: 12px; width: 100%; max-width: 500px; box-sizing: border-box;">
                            <input type="hidden" name="id_torneo" value="<?= $torneo['id_torneo'] ?>">
                            <input type="text" name="nombre_equipo" placeholder="Nombre del nuevo equipo..."
                                style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 10px 14px; border-radius: 8px; flex-grow: 1; outline: none; box-sizing: border-box;"
                                required>
                            <button type="submit"
                                style="background: #a855f7; color: white; border: none; padding: 10px 16px; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-plus"></i> Crear Equipo
                            </button>
                        </form>
                    <?php endif; ?>
                <?php else: ?>
                    <p style="margin: 0; color: #64748b; font-size: 0.85rem;"><i class="fa-solid fa-info-circle"></i> Los
                        torneos individuales no requieren creación de equipos; las inscripciones de los jugadores son directas.
                    </p>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <!-- CASO C: PARTICIPANTE EXAMINANDO EL SELECTOR -->
            <div
                style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 14px; display: flex; justify-content: space-between; align-items: center; box-sizing: border-box; width: 100%;">

                <?php if ($yaInscrito): ?>
                    <div
                        style="background: rgba(52, 211, 153, 0.05); border: 1px solid rgba(52, 211, 153, 0.2); padding: 20px; border-radius: 14px; display: flex; justify-content: space-between; align-items: center; box-sizing: border-box; width: 100%;">
                        <div>
                            <p style="margin: 0 0 4px 0; color: #34d399; font-weight: 700; font-size: 1rem;">
                                <i class="fa-solid fa-circle-check"></i> ¡Inscripción Confirmada!
                            </p>
                            <p style="margin: 0; color: #94a3b8; font-size: 0.88rem;">
                                Estás compitiendo de forma oficial en este torneo representando a:
                                <span
                                    style="color: #fff; font-weight: 700; background: rgba(255,255,255,0.05); padding: 4px 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.08); margin-left: 4px;">
                                    <?= !empty($nombreMiEquipo) ? htmlspecialchars($nombreMiEquipo) : 'Tu Escuadra' ?>
                                </span>
                            </p>
                        </div>
                        <span
                            style="background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); color: #34d399; padding: 8px 16px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.5px;">COMPITIENDO</span>
                    </div>
                <?php else: ?>

                    <?php if ($torneo['modalidad'] === 'individual'): ?>
                        <div>
                            <h4 style="margin: 0 0 4px 0; color: #fff; font-weight: 700;">¡Inscripciones Abiertas!</h4>
                            <p style="margin: 0; color: #94a3b8; font-size: 0.88rem;">Asegura tu plaza individual inmediata en este
                                campeonato.</p>
                        </div>
                        <form action="/index.php?action=join_tournament" method="POST" style="margin: 0;">
                            <input type="hidden" name="id_torneo" value="<?= $torneo['id_torneo'] ?>">
                            <button type="submit"
                                style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: #fff; border: none; padding: 12px 24px; border-radius: 10px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(168, 85, 247, 0.3);">
                                Inscribirme <i class="fa-solid fa-right-to-bracket" style="margin-left: 6px;"></i>
                            </button>
                        </form>
                    <?php else: ?>
                        <!-- FILTRADO EXCLUSIVO: El participante solo ve los equipos de este organizador/torneo -->
                        <div
                            style="width: 100%; display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap; box-sizing: border-box;">
                            <div>
                                <h4 style="margin: 0 0 4px 0; color: #fff; font-weight: 700;">Torneo por Equipos Oficial</h4>
                                <p style="margin: 0; color: #94a3b8; font-size: 0.88rem;">Selecciona a qué escuadra representar para
                                    sellar tu participación relacional.</p>
                            </div>

                            <?php if (empty($equiposInscritos)): ?>
                                <span style="color: #64748b; font-size: 0.88rem; font-style: italic;"><i
                                        class="fa-solid fa-hourglass-start"></i> Esperando que el organizador funde los
                                    equipos...</span>
                            <?php else: ?>
                                <form action="/index.php?action=join_tournament" method="POST"
                                    style="margin: 0; display: flex; gap: 10px; align-items: center; box-sizing: border-box;">
                                    <input type="hidden" name="id_torneo" value="<?= $torneo['id_torneo'] ?>">
                                    <select name="id_equipo"
                                        style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 12px; border-radius: 8px; outline: none;"
                                        required>
                                        <option value="" disabled selected>-- Elige tu Equipo --</option>
                                        <?php foreach ($equiposInscritos as $eq): ?>
                                            <option value="<?= $eq['id_equipo'] ?>"><?= htmlspecialchars($eq['nombreEquipo']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit"
                                        style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: #fff; border: none; padding: 12px 20px; border-radius: 8px; font-weight: 700; cursor: pointer;">
                                        Unirme
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- PANEL DIVIDIDO DE CONTROL -->
    <div class="split-layout">

        <!-- COLUMNA IZQUIERDA: FIXTURE DE ENFRENTAMIENTOS -->
        <section class="panel-card">
            <h2 class="panel-title"><i class="fa-solid fa-calendar-days" style="color: #6366f1;"></i> Calendario de
                Enfrentamientos</h2>

            <?php if (empty($fixture)): ?>
                <div style="text-align: center; padding: 40px; color: #64748b; margin: auto;">
                    <i class="fa-solid fa-timeline"
                        style="font-size: 2.5rem; margin-bottom: 12px; display: block; opacity: 0.3;"></i>
                    <p>El fixture no ha sido generado aún. Las llaves se armarán cuando cierren las inscripciones oficiales.
                    </p>
                </div>
            <?php else: ?>
                <?php foreach ($fixture as $partido): ?>
                    <div class="fixture-match">
                        <div class="match-team home">Equipo Local</div>
                        <div class="match-score-box">VS</div>
                        <div class="match-team away">Equipo Visitante</div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <!-- COLUMNA DERECHA: TABLA DE POSICIONES / PARTICIPANTES -->
        <section class="panel-card">
            <h2 class="panel-title"><i class="fa-solid fa-list-ol" style="color: #a855f7;"></i> Tabla de Posiciones</h2>

            <div class="table-row table-header">
                <div>Pos</div>
                <div>Equipo / Competidor</div>
                <div style="text-align: center;">PJ</div>
                <div style="text-align: right;">Pts</div>
            </div>

            <?php if (empty($equiposInscritos)): ?>
                <div style="text-align: center; padding: 40px; color: #64748b; margin: auto;">
                    <i class="fa-solid fa-users-slash"
                        style="font-size: 2.5rem; margin-bottom: 12px; display: block; opacity: 0.3;"></i>
                    <p>No hay competidores apuntados en este torneo actualmente.</p>
                </div>
            <?php else: ?>
                <?php foreach ($equiposInscritos as $pos => $equipo): ?>
                    <div class="table-row"
                        style="display: grid; grid-template-columns: 10% 45% 15% 15% 15%; align-items: center;">
                        <div style="font-weight: 800; color: #c084fc;">#<?= $pos + 1 ?></div>
                        <div style="font-weight: 600; color: #fff;"><?= htmlspecialchars($equipo['nombreEquipo']) ?></div>
                        <div style="text-align: center; color: #94a3b8;">0</div>
                        <div style="text-align: right; font-weight: 700; color: #34d399;">0</div>

                        <!-- Botón de eliminación solo visible para el Organizador del torneo -->
                        <div style="text-align: right;">
                            <?php if (isset($_SESSION['usuario_id']) && (int) $id_usuario_creador_real === (int) $_SESSION['usuario_id']): ?>
                                <a href="/index.php?action=eliminar_equipo&id_equipo=<?= $equipo['id_equipo'] ?>&id_torneo=<?= $id_torneo ?>"
                                    style="color: #ef4444; background: rgba(239,68,68,0.1); padding: 6px 10px; border-radius: 6px; text-decoration: none; font-size: 0.8rem;"
                                    title="Eliminar equipo por completo"
                                    onclick="return confirm('¿Eliminar este equipo borrará todas sus inscripciones y membresías permanentemente?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

    </div>
</div>