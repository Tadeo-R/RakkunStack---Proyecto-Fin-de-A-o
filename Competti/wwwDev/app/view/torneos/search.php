<?php
// Buscador de torneos con filtros (nombre, disciplina, estado).
$q          = trim($_GET['q'] ?? '');
$disciplina = trim($_GET['disciplina'] ?? '');
$estado     = trim($_GET['estado'] ?? '');

try {
    $torneos      = $modeloTorneo->buscar($q, $disciplina, $estado);
    $disciplinas  = $modeloTorneo->disciplinas();
} catch (PDOException $e) {
    $torneos = [];
    $disciplinas = [];
    flash_set('error', 'No se pudo realizar la búsqueda.');
}

$estados = [
    'inscripciones' => 'Inscripciones Abiertas',
    'en_curso'      => 'En Curso',
    'finalizado'    => 'Finalizados',
];
$etiquetasEstado = [
    'inscripciones' => ['Inscripciones Abiertas', 'badge-open'],
    'en_curso'      => ['En Curso', 'badge-live'],
    'finalizado'    => ['Finalizado', ''],
];
$textoBoton = [
    'inscripciones' => 'Ver Detalles',
    'en_curso'      => 'Ver Tabla & Fechas',
    'finalizado'    => 'Ver Resultados',
];

$pageTitle = 'Buscar Torneos';
$pageCss   = ['pages.css'];
require VIEW_PATH . '/include/page_start.php';
?>

    <main class="container">

        <header class="page-header">
            <div>
                <h1 class="page-title">Explorar Torneos</h1>
                <p class="subtitle">Encuentra y únete a la próxima competencia activa</p>
            </div>
        </header>

        <?= render_flash() ?>

        <section class="filters-panel">
            <form action="/index.php" method="GET" class="filters-form">
                <input type="hidden" name="view" value="search">

                <div class="filter-group search-group">
                    <label for="search"><i class="fa-solid fa-magnifying-glass"></i> Buscar por nombre</label>
                    <input type="text" id="search" name="q" value="<?= e($q) ?>" placeholder="Ej: Copa Verano 2026...">
                </div>

                <div class="filter-group">
                    <label for="disciplina"><i class="fa-solid fa-trophy"></i> Disciplina</label>
                    <select id="disciplina" name="disciplina">
                        <option value="">Todas las disciplinas</option>
                        <?php foreach ($disciplinas as $d): ?>
                            <option value="<?= e($d['slug']) ?>" <?= $disciplina === $d['slug'] ? 'selected' : '' ?>>
                                <?= e($d['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="estado"><i class="fa-solid fa-circle-dot"></i> Estado</label>
                    <select id="estado" name="estado">
                        <option value="">Todos los estados</option>
                        <?php foreach ($estados as $valor => $texto): ?>
                            <option value="<?= e($valor) ?>" <?= $estado === $valor ? 'selected' : '' ?>><?= e($texto) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group btn-group">
                    <button type="submit" class="btn-filter"><i class="fa-solid fa-sliders"></i> Filtrar</button>
                </div>
            </form>
        </section>

        <?php if (!$torneos): ?>
            <div class="empty-state">
                <i class="fa-solid fa-magnifying-glass"></i>
                <p>No encontramos torneos con esos filtros.</p>
                <a class="btn-card" href="<?= e(url('search')) ?>">Limpiar búsqueda</a>
            </div>
        <?php else: ?>
            <div class="grid-3">
                <?php foreach ($torneos as $t):
                    list($textoEstado, $claseEstado) = $etiquetasEstado[$t['estado']];
                    $imagen = !empty($t['disciplina_imagen']) ? $t['disciplina_imagen'] : imagen_tipo($t['categoria']);
                    $cantidad = $t['cupos']
                        ? (int) $t['inscriptos'] . '/' . (int) $t['cupos']
                        : (int) $t['inscriptos'];
                    $unidad = $t['modalidad'] === 'equipos' ? 'Equipos' : 'Jugadores';
                ?>
                    <article class="card">
                        <span class="card-badge <?= e($claseEstado) ?>"><?= e($textoEstado) ?></span>
                        <img src="<?= e($imagen) ?>" alt="<?= e($t['juego']) ?>" class="card-img">
                        <div class="card-body">
                            <h3><?= e($t['nombre']) ?></h3>
                            <div class="card-meta">
                                <?php if ($t['fecha_inicio']): ?>
                                    <span><i class="fa-regular fa-calendar"></i> Inicia: <?= e(fecha_corta($t['fecha_inicio'])) ?></span>
                                <?php endif; ?>
                                <span><i class="fa-solid fa-users"></i> <?= e($cantidad) ?> <?= e($unidad) ?></span>
                            </div>
                            <a href="#" class="btn-card"><?= e($textoBoton[$t['estado']]) ?></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>

<?php require VIEW_PATH . '/include/page_end.php'; ?>
