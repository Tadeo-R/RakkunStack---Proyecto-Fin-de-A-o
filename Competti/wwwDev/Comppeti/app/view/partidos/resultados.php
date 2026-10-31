<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $partidos = $modeloPartido->obtenerResultados();
} catch (PDOException $e) {
    $partidos = [];
    if (function_exists('flash_set')) {
        flash_set('error', 'No se pudieron cargar los resultados.');
    }
}

$iconosPartido = [
    'deporte' => 'fa-shield-halved', 
    'esports' => 'fa-gamepad', 
    'mentales' => 'fa-chess-knight'
];
?>
<link rel="stylesheet" href="/css/global.css">
<link rel="stylesheet" href="/css/pages.css">
<link rel="stylesheet" href="/css/special-pages.css">

<main class="results-container" style="max-width: 1200px; margin: 40px auto; padding: 0 20px; color: #f8fafc; font-family: 'Inter', sans-serif;">
    <div class="results-header" style="margin-bottom: 30px;">
        <h1 style="font-size: 2.2rem; font-weight: 900; color: #fff; margin: 0;">Últimos Resultados</h1>
    </div>

    <?php if (function_exists('render_flash')) { echo render_flash(); } ?>

    <?php if (empty($partidos)): ?>
        <div class="empty-state" style="text-align: center; padding: 50px; background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 20px; color: #64748b;">
            <i class="fa-solid fa-chart-bar" style="font-size: 2.5rem; margin-bottom: 12px; display: block; opacity: 0.3; color: #c084fc;"></i>
            <p style="margin:0; font-size:0.95rem;">Todavía no hay resultados publicados en esta temporada.</p>
        </div>
    <?php else: ?>
        <div class="results-list" style="display: flex; flex-direction: column; gap: 16px;">
            <?php foreach ($partidos as $p):
                $tipo = $p['tipo'] ?? 'deporte';
                $icono = $iconosPartido[$tipo] ?? 'fa-shield-halved';
            ?>
                <article class="match-card" style="background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 16px; padding: 20px; backdrop-filter: blur(12px); box-sizing: border-box;">
                    <span class="match-category" style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.78rem; font-weight: 700; color: #c084fc; text-transform: uppercase; margin-bottom: 12px;">
                        <i class="fa-solid <?= htmlspecialchars($icono) ?>"></i> 
                        <?= htmlspecialchars($p['etiqueta'] ?? 'Competencia') ?>
                    </span>
                    
                    <div class="match-teams" style="display: flex; align-items: center; justify-content: space-between; gap: 15px;">
                        <!-- Equipo Local -->
                        <div class="team home" style="display: flex; align-items: center; gap: 12px; flex: 1; justify-content: flex-end;">
                            <span style="font-weight: 700; font-size: 1.05rem; color: #fff; text-align: right;"><?= htmlspecialchars($p['equipo_local'] ?? 'Local') ?></span>
                            <div style="width: 36px; height: 36px; background: rgba(255, 255, 255, 0.05); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #cbd5e1;">
                                <i class="fa-solid <?= htmlspecialchars($icono) ?>"></i>
                            </div>
                        </div>

                        <!-- Marcador -->
                        <div class="match-score" style="background: rgba(168, 85, 247, 0.1); border: 1px solid rgba(168, 85, 247, 0.2); color: #fff; padding: 8px 20px; border-radius: 12px; font-size: 1.2rem; font-weight: 900; letter-spacing: 2px;">
                            <?= (int) ($p['puntos_local'] ?? 0) ?> - <?= (int) ($p['puntos_visitante'] ?? 0) ?>
                        </div>

                        <!-- Equipo Visitante -->
                        <div class="team away" style="display: flex; align-items: center; gap: 12px; flex: 1; justify-content: flex-start;">
                            <div style="width: 36px; height: 36px; background: rgba(255, 255, 255, 0.05); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #cbd5e1;">
                                <i class="fa-solid <?= htmlspecialchars($icono) ?>"></i>
                            </div>
                            <span style="font-weight: 700; font-size: 1.05rem; color: #fff; text-align: left;"><?= htmlspecialchars($p['equipo_visitante'] ?? 'Visitante') ?></span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>