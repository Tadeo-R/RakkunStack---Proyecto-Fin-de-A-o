<?php
try {
    $partidos = $modeloPartido->resultados();
} catch (PDOException $e) {
    $partidos = [];
    flash_set('error', 'No se pudieron cargar los resultados.');
}

$iconosPartido = ['deporte' => 'fa-shield-halved', 'esports' => 'fa-gamepad', 'mentales' => 'fa-chess-knight'];

$pageTitle = 'Últimos Resultados';
$pageCss   = ['pages.css', 'special-pages.css'];
require VIEW_PATH . '/include/page_start.php';
?>

    <main class="results-container">
        <div class="results-header">
            <h1>Últimos Resultados</h1>
        </div>

        <?= render_flash() ?>

        <?php if (!$partidos): ?>
            <div class="empty-state">
                <i class="fa-solid fa-chart-bar"></i>
                <p>Todavía no hay resultados publicados.</p>
            </div>
        <?php else: ?>
            <div class="results-list">
                <?php foreach ($partidos as $p):
                    $icono = isset($iconosPartido[$p['tipo']]) ? $iconosPartido[$p['tipo']] : 'fa-shield-halved';
                ?>
                    <article class="match-card">
                        <span class="match-category"><i class="fa-solid <?= e($icono) ?>"></i> <?= e($p['etiqueta']) ?></span>
                        <div class="match-teams">
                            <div class="team home">
                                <i class="fa-solid <?= e($icono) ?>"></i>
                                <span><?= e($p['equipo_local']) ?></span>
                            </div>
                            <div class="match-score"><?= (int) $p['puntos_local'] ?> - <?= (int) $p['puntos_visitante'] ?></div>
                            <div class="team away">
                                <span><?= e($p['equipo_visitante']) ?></span>
                                <i class="fa-solid <?= e($icono) ?>"></i>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

<?php require VIEW_PATH . '/include/page_end.php'; ?>
