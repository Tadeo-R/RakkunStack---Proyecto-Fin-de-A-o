<?php
// Asegurar inicio de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$q = trim($_GET['q'] ?? '');
$disciplina = trim($_GET['disciplina'] ?? '');
$estado = trim($_GET['estado'] ?? '');

try {
    $torneos = $modeloTorneo->buscar($q, $disciplina, $estado);
} catch (PDOException $e) {
    $torneos = [];
}

$etiquetasEstado = [
    'inscripciones' => ['Inscripciones Abiertas', 'badge-open'],
    'en_curso'      => ['En Curso', 'badge-live'],
    'finalizado'    => ['Finalizado', ''],
];
?>
<link rel="stylesheet" href="/css/pages.css">

<main class="container" style="padding: 40px 20px; max-width: 1200px; margin: 0 auto; color: #f8fafc; font-family: 'Inter', sans-serif;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <h1 class="page-title" style="color: #fff; font-weight: 850; margin: 0; font-size: 2rem;">Todos los Torneos</h1>
        <a href="/index.php?view=torneos/crear"
           style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 0.9rem; box-shadow: 0 4px 14px rgba(168, 85, 247, 0.3);">
            <i class="fa-solid fa-plus" style="margin-right: 6px;"></i> Crear Torneo
        </a>
    </div>

    <?php if ($q !== '' || $disciplina !== '' || $estado !== ''): ?>
        <p class="filter-summary" style="color: #94a3b8; margin-bottom: 20px; font-size: 0.9rem;">
            <?= count($torneos) ?> resultado<?= count($torneos) === 1 ? '' : 's' ?> con los filtros aplicados.
            <a href="/index.php?view=torneos/torneos"
                style="color: #c084fc; margin-left: 10px; text-decoration: none; font-weight: 700;">Quitar filtros</a>
        </p>
    <?php endif; ?>

    <?php if (empty($torneos)): ?>
        <div class="empty-state" style="text-align: center; padding: 60px 20px; background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(255,255,255,0.06); border-radius: 20px;">
            <i class="fa-solid fa-trophy" style="font-size: 3rem; margin-bottom: 16px; display: block; color: rgba(255,255,255,0.15);"></i>
            <h3 style="color: #fff; margin: 0 0 6px 0; font-size: 1.1rem;">No hay torneos para mostrar</h3>
            <p style="color: #94a3b8; font-size: 0.9rem; margin: 0 0 16px 0;">Sé el primero en organizar una competencia oficial en la plataforma.</p>
            <a class="btn-card" href="/index.php?view=torneos/crear"
                style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: 700; display: inline-block; font-size: 0.9rem;">
                Crear el primero
            </a>
        </div>
    <?php else: ?>
        <div class="grid-3" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
            <?php foreach ($torneos as $t):
                list($textoEstado, $claseEstado) = $etiquetasEstado[$t['estado']] ?? ['Desconocido', ''];

                // Icono dinámico según categoría o juego
                $icono = ($t['categoria'] ?? '') === 'esports' ? 'fa-gamepad' : 'fa-trophy';
                
                // ID seguro del torneo
                $idRealTorneo = !empty($t['id_torneo']) ? (int)$t['id_torneo'] : (!empty($t['id']) ? (int)$t['id'] : 0);
            ?>
                <article class="card"
                    style="background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 20px; overflow: hidden; display: flex; flex-direction: column; position: relative; backdrop-filter: blur(12px);">

                    <!-- Insignia del estado del torneo -->
                    <span class="card-badge"
                        style="position: absolute; top: 14px; left: 14px; background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3); color: #34d399; padding: 6px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; z-index: 5; text-transform: uppercase;">
                        <?= htmlspecialchars($textoEstado) ?>
                    </span>

                    <!-- Cabecera visual decorativa limpia -->
                    <div style="width: 100%; height: 140px; background: linear-gradient(135deg, rgba(30, 27, 75, 0.8) 0%, rgba(15, 23, 42, 0.9) 100%); display: flex; align-items: center; justify-content: center; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <i class="fa-solid <?= $icono ?>" style="font-size: 3rem; color: rgba(192, 132, 252, 0.4);"></i>
                    </div>

                    <div class="card-body" style="padding: 24px; display: flex; flex-direction: column; flex-grow: 1; gap: 16px;">
                        <h3 style="color: #fff; margin: 0; font-size: 1.25rem; font-weight: 800; line-height: 1.3;">
                            <?= htmlspecialchars($t['nombreTorneo'] ?? 'Torneo Oficial') ?>
                        </h3>

                        <div class="card-meta" style="display: flex; flex-direction: column; gap: 10px; color: #94a3b8; font-size: 0.875rem;">
                            <span style="display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid <?= $icono ?>" style="color: #c084fc; width: 16px;"></i> Juego: <?= htmlspecialchars($t['juego'] ?? 'General') ?>
                            </span>

                            <span style="display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid <?= ($t['modalidad'] ?? '') === 'equipos' ? 'fa-users' : 'fa-user' ?>" style="width: 16px; color: #6366f1;"></i> Modalidad: <?= htmlspecialchars(ucfirst($t['modalidad'] ?? 'individual')) ?>
                                <?php if (!empty($t['cupos'])): ?>
                                    <span style="color: #64748b;">(Máx: <?= (int) $t['cupos'] ?>)</span>
                                <?php endif; ?>
                            </span>

                            <?php if (!empty($t['fechaInicio'])): 
                                $date = new DateTime($t['fechaInicio']);
                                $meses = ["ene", "feb", "mar", "abr", "may", "jun", "jul", "ago", "sep", "oct", "nov", "dic"];
                                $fechaFormateada = $date->format('d') . ' ' . $meses[(int)$date->format('m') - 1] . ', ' . $date->format('Y');
                            ?>
                                <span style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fa-regular fa-calendar" style="width: 16px; color: #34d399;"></i> Inicia: <?= $fechaFormateada ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- PANEL DE ACCIONES -->
                        <div style="display: flex; gap: 10px; width: 100%; margin-top: auto; padding-top: 10px;">
                            <a href="/index.php?view=torneos/detalles&id=<?= $idRealTorneo ?>" class="btn-card"
                                style="flex-grow: 1; text-align: center; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); color: #fff; padding: 12px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 0.9rem; transition: all 0.2s;">
                                Ver detalles
                            </a>

                            <?php 
                            // Verificación segura si el usuario en sesión es el creador del torneo
                            $esCreador = false;
                            if (isset($_SESSION['usuario_id']) && !empty($t['id_organizador'])) {
                                // Consultamos si el id_organizador del torneo pertenece al usuario logueado
                                $stmtOrgCheck = $pdo->prepare("SELECT COUNT(*) FROM ORGANIZADOR WHERE id_organizador = :id_org AND id_usuario = :id_user");
                                $stmtOrgCheck->execute([
                                    ':id_org' => (int)$t['id_organizador'],
                                    ':id_user' => (int)$_SESSION['usuario_id']
                                ]);
                                $esCreador = ($stmtOrgCheck->fetchColumn() > 0);
                            }
                            ?>

                            <?php if ($esCreador): ?>
                                <a href="/index.php?view=torneos/editar&id=<?= $idRealTorneo ?>"
                                    style="background: rgba(168, 85, 247, 0.15); border: 1px solid rgba(168, 85, 247, 0.3); color: #c084fc; padding: 12px; border-radius: 10px; display: flex; align-items: center; justify-content: center; width: 42px; box-sizing: border-box; text-decoration: none;"
                                    title="Editar Parámetros">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>