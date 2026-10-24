<?php
$usuarioId = usuario_logueado() ? (int) $_SESSION['usuario_id'] : 0;

try {
    $proximos = $modeloPartido->proximos($usuarioId);
} catch (PDOException $e) {
    $proximos = [];
    flash_set('error', 'No se pudo cargar el calendario.');
}

$pageTitle = 'Próximos Encuentros';
$pageCss   = ['pages.css', 'special-pages.css'];
require VIEW_PATH . '/include/page_start.php';
?>

    <main class="calendar-container">
        <div class="calendar-header">
            <h1>Próximos Encuentros</h1>
        </div>

        <?= render_flash() ?>

        <?php if (!$proximos): ?>
            <div class="empty-state">
                <i class="fa-regular fa-calendar"></i>
                <p>No hay encuentros programados por ahora.</p>
            </div>
        <?php else: ?>
            <div class="timeline">
                <?php foreach ($proximos as $p):
                    $recordado = (int) $p['recordado'] === 1;
                ?>
                    <div class="timeline-item">
                        <div class="timeline-date">
                            <span class="day"><?= e(etiqueta_dia($p['fecha_hora'])) ?></span>
                            <span class="time"><?= e(hora_hs($p['fecha_hora'])) ?></span>
                        </div>
                        <div class="timeline-info">
                            <div class="timeline-title"><?= e($p['equipo_local']) ?> vs <?= e($p['equipo_visitante']) ?></div>
                            <div class="timeline-sub"><?= e($p['etiqueta']) ?></div>
                        </div>
                        <form class="inline-form" action="/index.php?action=toggle_reminder" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="partido_id" value="<?= (int) $p['id'] ?>">
                            <button type="submit" class="btn-remind <?= $recordado ? 'is-active' : '' ?>">
                                <?php if ($recordado): ?>
                                    <i class="fa-solid fa-bell"></i> Recordado
                                <?php else: ?>
                                    <i class="fa-regular fa-bell"></i> Recordar
                                <?php endif; ?>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

<?php require VIEW_PATH . '/include/page_end.php'; ?>
