<?php
// Asegurar inicio de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Validar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /index.php?view=auth/login");
    exit;
}

// 2. Capturar y validar el ID del torneo por la URL
$id_torneo = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id_torneo) {
    header("Location: /index.php?view=torneos/torneos");
    exit;
}

try {
    // 3. Obtener los datos actuales del torneo
    $stmt = $pdo->prepare("SELECT * FROM TORNEO WHERE id_torneo = :id LIMIT 1");
    $stmt->execute([':id' => $id_torneo]);
    $torneo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$torneo) {
        header("Location: /index.php?view=torneos/torneos");
        exit;
    }

    // 4. Verificar permisos: Comprobar si el usuario en sesión es el organizador creador del torneo
    $stmtOrg = $pdo->prepare("SELECT COUNT(*) FROM ORGANIZADOR WHERE id_organizador = :id_org AND id_usuario = :id_user");
    $stmtOrg->execute([
        ':id_org'  => (int)$torneo['id_organizador'],
        ':id_user' => (int)$_SESSION['usuario_id']
    ]);
    $esPropietario = ($stmtOrg->fetchColumn() > 0);

    if (!$esPropietario) {
        // Si no es el dueño, lo redirigimos fuera con un aviso o al listado
        header("Location: /index.php?view=torneos/detalles&id=" . $id_torneo);
        exit;
    }

} catch (PDOException $e) {
    die("<div style='color:white; background:#ef4444; padding:20px; border-radius:12px; margin:20px;'><b>Error de Base de Datos:</b> " . htmlspecialchars($e->getMessage()) . "</div>");
}
?>

<link rel="stylesheet" href="/css/pages.css">

<main class="container" style="max-width: 900px; margin: 40px auto; padding: 0 20px;">
    
    <!-- ENCABEZADO DE LA PÁGINA -->
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-pen-to-square" style="color: #c084fc; margin-right: 8px;"></i> Editar Torneo</h1>
            <p class="subtitle">Modifica los parámetros oficiales de tu competencia en tiempo real.</p>
        </div>
        <a href="/index.php?view=torneos/detalles&id=<?= $id_torneo ?>" class="btn-action-secondary" style="text-decoration: none;">
            <i class="fa-solid fa-arrow-left"></i> Volver a Detalles
        </a>
    </div>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'actualizacion_fallida'): ?>
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444; padding: 14px; border-radius: 12px; margin-bottom: 20px; font-weight: 700; text-align: center;">
            <i class="fa-solid fa-triangle-exclamation"></i> Ocurrió un error al intentar actualizar los datos del torneo. Inténtalo nuevamente.
        </div>
    <?php endif; ?>

    <!-- PANEL PRINCIPAL DE EDICIÓN -->
    <div class="main-panel">
        <form action="/index.php?action=update_tournament" method="POST" class="filters-form" style="display: flex; flex-direction: column; gap: 20px;">
            
            <input type="hidden" name="id_torneo" value="<?= (int)$torneo['id_torneo'] ?>">

            <!-- Nombre del Torneo -->
            <div class="filter-group search-group">
                <label for="nombreTorneo"><i class="fa-solid fa-trophy"></i> Nombre del Torneo</label>
                <input type="text" id="nombreTorneo" name="nombreTorneo" value="<?= htmlspecialchars($torneo['nombreTorneo']) ?>" required>
            </div>

            <!-- Fila de Modalidad y Juego -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; width: 100%;">
                
                <div class="filter-group">
                    <label for="modalidad"><i class="fa-solid fa-sitemap"></i> Modalidad</label>
                    <select id="modalidad" name="modalidad" required>
                        <option value="individual" <?= $torneo['modalidad'] === 'individual' ? 'selected' : '' ?>>Individual (1v1)</option>
                        <option value="equipos" <?= $torneo['modalidad'] === 'equipos' ? 'selected' : '' ?>>Equipos vs Equipos</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="juego"><i class="fa-solid fa-gamepad"></i> Videojuego / Disciplina</label>
                    <input type="text" id="juego" name="juego" value="<?= htmlspecialchars($torneo['juego']) ?>" required>
                </div>

            </div>

            <!-- Fila de Formato y Cupos -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; width: 100%;">
                
                <div class="filter-group">
                    <label for="formato"><i class="fa-solid fa-layer-group"></i> Formato de Competencia</label>
                    <select id="formato" name="formato" required>
                        <option value="liga" <?= $torneo['formato'] === 'liga' ? 'selected' : '' ?>>Liga (Todos contra todos)</option>
                        <option value="eliminacion" <?= $torneo['formato'] === 'eliminacion' ? 'selected' : '' ?>>Eliminación Directa</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="cupos"><i class="fa-solid fa-users-rectangle"></i> Límite de Cupos (Equipos / Jugadores)</label>
                    <input type="number" id="cupos" name="cupos" min="2" max="128" value="<?= (int)($torneo['cupos'] ?? 16) ?>" required>
                </div>

            </div>

            <!-- Fila de Fechas y Estado -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; width: 100%;">
                
                <div class="filter-group">
                    <label for="fechaInicio"><i class="fa-regular fa-calendar-days"></i> Fecha de Inicio</label>
                    <input type="date" id="fechaInicio" name="fechaInicio" value="<?= htmlspecialchars($torneo['fechaInicio'] ?? '') ?>" required>
                </div>

                <div class="filter-group">
                    <label for="estado"><i class="fa-solid fa-circle-info"></i> Estado del Torneo</label>
                    <select id="estado" name="estado" required>
                        <option value="inscripciones" <?= $torneo['estado'] === 'inscripciones' ? 'selected' : '' ?>>Inscripciones Abiertas</option>
                        <option value="en_curso" <?= $torneo['estado'] === 'en_curso' ? 'selected' : '' ?>>En Curso (Activo)</option>
                        <option value="finalizado" <?= $torneo['estado'] === 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                    </select>
                </div>

            </div>

            <!-- Botones de Acción -->
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 20px;">
                <a href="/index.php?view=torneos/detalles&id=<?= $id_torneo ?>" class="btn-action-secondary" style="padding: 12px 20px; text-decoration: none;">
                    Cancelar
                </a>
                <button type="submit" class="btn-filter" style="width: auto; padding: 12px 28px;">
                    Guardar Cambios <i class="fa-solid fa-floppy-disk" style="margin-left: 6px;"></i>
                </button>
            </div>

        </form>
    </div>
</main>