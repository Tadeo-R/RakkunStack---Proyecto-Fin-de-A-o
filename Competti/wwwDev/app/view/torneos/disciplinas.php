<?php
try {
    $disciplinas = $modeloTorneo->disciplinasConActivos();
} catch (PDOException $e) {
    $disciplinas = [];
    flash_set('error', 'No se pudieron cargar las disciplinas.');
}

$etiquetasTipo = [
    'deporte'  => ['Deporte', ''],
    'esports'  => ['eSports', 'badge-esports'],
    'mentales' => ['Mesa', 'badge-mesa'],
];

$pageTitle = 'Disciplinas';
$pageCss   = ['pages.css'];
require VIEW_PATH . '/include/page_start.php';
?>

    <main class="container">

        <header class="page-header">
            <div>
                <h1 class="page-title">Disciplinas Deportivas &amp; eSports</h1>
                <p class="subtitle">Explora los torneos activos según tu deporte o juego favorito</p>
            </div>
        </header>

        <?= render_flash() ?>

        <?php if (!$disciplinas): ?>
            <div class="empty-state">
                <i class="fa-solid fa-flag"></i>
                <p>Todavía no hay disciplinas cargadas.</p>
            </div>
        <?php else: ?>
            <div class="grid-3">
                <?php foreach ($disciplinas as $d):
                    list($textoTipo, $claseTipo) = $etiquetasTipo[$d['tipo']];
                    $imagen = !empty($d['imagen']) ? $d['imagen'] : imagen_tipo($d['tipo']);
                    $activos = (int) $d['activos'];
                ?>
                    <article class="card discipline-card">
                        <span class="card-badge <?= e($claseTipo) ?>"><?= e($textoTipo) ?></span>
                        <img src="<?= e($imagen) ?>" alt="<?= e($d['nombre']) ?>" class="card-img">
                        <div class="card-body">
                            <div class="card-header-icon">
                                <i class="fa-solid <?= e($d['icono']) ?>"></i>
                                <h3><?= e($d['nombre']) ?></h3>
                            </div>
                            <p class="card-desc"><?= e($d['descripcion']) ?></p>
                            <div class="card-meta">
                                <span><i class="fa-solid fa-flag"></i> <?= $activos ?> Torneo<?= $activos === 1 ? '' : 's' ?> activo<?= $activos === 1 ? '' : 's' ?></span>
                            </div>
                            <a href="<?= e(url('torneos', ['disciplina' => $d['slug']])) ?>" class="btn-card">Ver Torneos</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

<?php require VIEW_PATH . '/include/page_end.php'; ?>
