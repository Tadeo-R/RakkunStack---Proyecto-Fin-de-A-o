<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuarioId = isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : 0;

try {
    // Llamamos al método actualizado del modelo
    $proximos = $modeloPartido->proximos($usuarioId);
} catch (PDOException $e) {
    $proximos = [];
    if (function_exists('flash_set')) {
        flash_set('error', 'No se pudo cargar el calendario.');
    }
}
?>
<link rel="stylesheet" href="/css/pages.css">
<link rel="stylesheet" href="/css/special-pages.css">

<main class="calendar-container" style="max-width: 1000px; margin: 40px auto; padding: 0 20px; color: #f8fafc; font-family: 'Inter', sans-serif;">
    <div class="calendar-header" style="margin-bottom: 30px;">
        <h1 style="font-size: 2rem; font-weight: 850; background: linear-gradient(to right, #fff, #c084fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Próximos Encuentros</h1>
        <p style="color: #94a3b8; font-size: 0.95rem; margin-top: 6px;">Calendario oficial de llaves y enfrentamientos de la temporada en COMPETTI.</p>
    </div>

    <?php if (function_exists('render_flash')) echo render_flash(); ?>

    <?php if (empty($proximos)): ?>
        <div class="empty-state" style="text-align: center; padding: 60px 20px; background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(255,255,255,0.06); border-radius: 20px;">
            <i class="fa-regular fa-calendar" style="font-size: 3rem; color: #64748b; margin-bottom: 16px; display: block;"></i>
            <h3 style="color: #fff; margin: 0 0 6px 0; font-size: 1.1rem;">No hay encuentros programados</h3>
            <p style="color: #94a3b8; font-size: 0.9rem; margin: 0;">Los enfrentamientos aparecerán aquí en cuanto el organizador genere el fixture del torneo.</p>
        </div>
    <?php else: ?>
        <div class="timeline" style="display: flex; flex-direction: column; gap: 16px;">
            <?php foreach ($proximos as $p):
                $recordado = isset($p['recordado']) && (int) $p['recordado'] === 1;
            ?>
                <div class="timeline-item" style="background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(255,255,255,0.06); border-radius: 16px; padding: 20px; display: flex; align-items: center; justify-content: space-between; gap: 20px; backdrop-filter: blur(12px);">
                    
                    <div class="timeline-date" style="display: flex; flex-direction: column; min-width: 100px; text-align: center; border-right: 1px solid rgba(255,255,255,0.06); padding-right: 16px;">
                        <span class="day" style="font-weight: 800; color: #c084fc; font-size: 0.95rem;"><?= htmlspecialchars(etiqueta_dia($p['fecha_hora'])) ?></span>
                        <span class="time" style="color: #94a3b8; font-size: 0.85rem; font-weight: 600;"><?= htmlspecialchars(hora_hs($p['fecha_hora'])) ?></span>
                    </div>

                    <div class="timeline-info" style="flex-grow: 1;">
                        <div class="timeline-title" style="font-size: 1.1rem; font-weight: 700; color: #fff; margin-bottom: 4px;">
                            <?= htmlspecialchars($p['equipo_local'] ?? 'TBD') ?> <span style="color: #a855f7; font-weight: 800; margin: 0 6px;">VS</span> <?= htmlspecialchars($p['equipo_visitante'] ?? 'TBD') ?>
                        </div>
                        <div class="timeline-sub" style="color: #94a3b8; font-size: 0.85rem;">
                            <i class="fa-solid fa-trophy" style="color: #6366f1; margin-right: 4px;"></i> <?= htmlspecialchars($p['etiqueta']) ?>
                        </div>
                    </div>

                    <form class="inline-form" action="/index.php?action=toggle_reminder" method="POST" style="margin: 0;">
                        <?php if (function_exists('csrf_field')) echo csrf_field(); ?>
                        <input type="hidden" name="partido_id" value="<?= (int) $p['id'] ?>">
                        <button type="submit" class="btn-remind <?= $recordado ? 'is-active' : '' ?>" style="background: <?= $recordado ? 'rgba(52, 211, 153, 0.15)' : 'rgba(255,255,255,0.05)' ?>; border: 1px solid <?= $recordado ? 'rgba(52, 211, 153, 0.3)' : 'rgba(255,255,255,0.1)' ?>; color: <?= $recordado ? '#34d399' : '#fff' ?>; padding: 10px 16px; border-radius: 10px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; font-size: 0.85rem; transition: all 0.2s;">
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