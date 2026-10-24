<?php
// Listado de torneos. Acepta los mismos filtros que el buscador:

$q          = trim($_GET['q'] ?? '');
$disciplina = trim($_GET['disciplina'] ?? '');
$estado     = trim($_GET['estado'] ?? '');

try {
    $torneos = $modeloTorneo->buscar($q, $disciplina, $estado);
} catch (PDOException $e) {
    $torneos = [];
    flash_set('error', 'No se pudieron cargar los torneos.');
}

$etiquetasEstado = [
    'inscripciones' => ['Inscripciones Abiertas', 'badge-open'],
    'en_curso'      => ['En Curso', 'badge-live'],
    'finalizado'    => ['Finalizado', ''],
];

$pageTitle = 'Torneos';
$pageCss   = ['pages.css'];
require VIEW_PATH . '/include/page_start.php';
?>

    <main class="container">
        <h1 class="page-title">Todos los Torneos</h1>

        <?= render_flash() ?>

        <?php if ($q !== '' || $disciplina !== '' || $estado !== ''): ?>
            <p class="filter-summary">
                <?= count($torneos) ?> resultado<?= count($torneos) === 1 ? '' : 's' ?> con los filtros aplicados.
                <a href="<?= e(url('torneos')) ?>">Quitar filtros</a>
            </p>
        <?php endif; ?>

        <?php if (!$torneos): ?>
            <div class="empty-state">
                <i class="fa-solid fa-trophy"></i>
                <p>No hay torneos para mostrar.</p>
                <a class="btn-card" href="<?= e(url('crear-torneo')) ?>">Crear el primero</a>
            </div>
        <?php else: ?>
            <div class="grid-3">
                <?php foreach ($torneos as $t):
                    list($textoEstado, $claseEstado) = $etiquetasEstado[$t['estado']];
                    $imagen = !empty($t['disciplina_imagen']) ? $t['disciplina_imagen'] : imagen_tipo($t['categoria']);
                    $icono  = !empty($t['disciplina_icono']) ? $t['disciplina_icono'] : 'fa-trophy';
                    $unidad = $t['modalidad'] === 'equipos' ? 'equipos' : 'jugadores';
                ?>
                    <article class="card">
                        <span class="card-badge <?= e($claseEstado) ?>"><?= e($textoEstado) ?></span>
                        <img src="<?= e($imagen) ?>" alt="<?= e($t['juego']) ?>" class="card-img">
                        <div class="card-body">
                            <h3><?= e($t['nombre']) ?></h3>
                            <div class="card-meta">
                                <span><i class="fa-solid <?= e($icono) ?>"></i> <?= e($t['juego']) ?></span>
                                <span>
                                    <i class="fa-solid <?= $t['modalidad'] === 'equipos' ? 'fa-users' : 'fa-user' ?>"></i>
                                    <?php if ($t['cupos']): ?>
                                        <?= (int) $t['inscriptos'] ?>/<?= (int) $t['cupos'] ?> <?= $unidad ?>
                                    <?php else: ?>
                                        <?= (int) $t['inscriptos'] ?> <?= $unidad ?>
                                    <?php endif; ?>
                                </span>
                                <?php if ($t['fecha_inicio']): ?>
                                    <span><i class="fa-regular fa-calendar"></i> Inicia: <?= e(fecha_corta($t['fecha_inicio'])) ?></span>
                                <?php endif; ?>
                            </div>
                            <a href="#" class="btn-card">Ver detalles</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

<?php require VIEW_PATH . '/include/page_end.php'; ?>
