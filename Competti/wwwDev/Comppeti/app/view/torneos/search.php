<?php
// Asegurar inicio de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Buscador de torneos con filtros (nombre, disciplina, estado).
$q = trim($_GET['q'] ?? '');
$disciplina = trim($_GET['disciplina'] ?? '');
$estado = trim($_GET['estado'] ?? '');

try {
    // Tu modeloTorneo ya fue adaptado previamente para buscar directo en la nueva estructura
    $torneos = $modeloTorneo->buscar($q, $disciplina, $estado);
    $disciplinas = $modeloTorneo->disciplinas();
} catch (PDOException $e) {
    $torneos = [];
    $disciplinas = [];
}

$estados = [
    'inscripciones' => 'Inscripciones Abiertas',
    'en_curso' => 'En Curso',
    'finalizado' => 'Finalizados',
];

$etiquetasEstado = [
    'inscripciones' => ['Inscripciones Abiertas', 'badge-open'],
    'en_curso' => ['En Curso', 'badge-live'],
    'finalizado' => ['Finalizado', ''],
];

$textoBoton = [
    'inscripciones' => 'Ver Detalles',
    'en_curso' => 'Ver Tabla & Fechas',
    'finalizado' => 'Ver Resultados',
];
?>
<link rel="stylesheet" href="/css/pages.css">

<main class="container" style="padding: 40px 20px; max-width: 1200px; margin: 0 auto;">

    <header class="page-header" style="margin-bottom: 30px;">
        <div>
            <h1 class="page-title" style="color: #fff; font-weight: 850; margin: 0 0 8px 0;">Explorar Torneos</h1>
            <p class="subtitle" style="color: #94a3b8; margin: 0;">Encuentra y únete a la próxima competencia activa</p>
        </div>
    </header>

    <!-- FORMULARIO DE FILTROS ADAPTADO AL ENRUTADOR CENTRAL -->
    <section class="filters-panel"
        style="background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(255, 255, 255, 0.06); padding: 24px; border-radius: 16px; margin-bottom: 40px; backdrop-filter: blur(12px);">
        <form action="/index.php" method="GET" class="filters-form"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; align-items: flex-end;">

            <!-- Crucial: Mantiene la vista activa en el index.php al enviar el formulario -->
            <input type="hidden" name="view" value="torneos/search">

            <div class="filter-group search-group" style="display: flex; flex-direction: column; gap: 8px;">
                <label for="search" style="color: #cbd5e1; font-size: 0.85rem; font-weight: 600;"><i
                        class="fa-solid fa-magnifying-glass"></i> Buscar por nombre</label>
                <input type="text" id="search" name="q" value="<?= htmlspecialchars($q) ?>"
                    placeholder="Ej: Copa Verano 2026..."
                    style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 12px; border-radius: 8px;">
            </div>

            <div class="filter-group" style="display: flex; flex-direction: column; gap: 8px;">
                <label for="disciplina" style="color: #cbd5e1; font-size: 0.85rem; font-weight: 600;"><i
                        class="fa-solid fa-trophy"></i> Disciplina</label>
                <select id="disciplina" name="disciplina"
                    style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 12px; border-radius: 8px; width: 100%;">
                    <option value="">Todas las disciplinas</option>
                    <?php foreach ($disciplinas as $d): ?>
                        <!-- CORREGIDO: Usamos el campo 'nombre' real de tu tabla DEPORTE en el value -->
                        <option value="<?= htmlspecialchars($d['nombre']) ?>" <?= $disciplina === $d['nombre'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group" style="display: flex; flex-direction: column; gap: 8px;">
                <label for="estado" style="color: #cbd5e1; font-size: 0.85rem; font-weight: 600;"><i
                        class="fa-solid fa-circle-dot"></i> Estado</label>
                <select id="estado" name="estado"
                    style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 12px; border-radius: 8px; width: 100%;">
                    <option value="">Todos los estados</option>
                    <?php foreach ($estados as $valor => $texto): ?>
                        <option value="<?= htmlspecialchars($valor) ?>" <?= $estado === $valor ? 'selected' : '' ?>>
                            <?= htmlspecialchars($texto) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group btn-group" style="width: 100%;">
                <button type="submit" class="btn-filter"
                    style="width: 100%; padding: 13px; border: none; border-radius: 8px; background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: white; font-weight: 700; cursor: pointer; transition: all 0.2s;">
                    <i class="fa-solid fa-sliders"></i> Filtrar
                </button>
            </div>
        </form>
    </section>

    <!-- EXPOSITOR DE RESULTADOS -->
    <?php if (!$torneos): ?>
        <div class="empty-state" style="text-align: center; padding: 60px; color: #94a3b8;">
            <i class="fa-solid fa-magnifying-glass"
                style="font-size: 3rem; margin-bottom: 16px; display: block; color: rgba(255,255,255,0.1);"></i>
            <p>No encontramos torneos con esos filtros.</p>
            <a class="btn-card" href="/index.php?view=search/search"
                style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block; margin-top: 10px;">Limpiar
                búsqueda</a>
        </div>
    <?php else: ?>
        <div class="grid-3" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
            <?php foreach ($torneos as $t):
                list($textoEstado, $claseEstado) = $etiquetasEstado[$t['estado']] ?? ['Inscripciones Abiertas', 'badge-open'];

                // Imagen decorativa estándar estable
                $imagen = !empty($t['streaming_url']) ? '/img/gaming-banner.jpg' : '/img/sports-banner.jpg';
                $unidad = $t['modalidad'] === 'equipos' ? 'Equipos' : 'Jugadores';

                // Parche de seguridad para capturar el ID de torneo sin importar la mayúscula de la BD
                $idRealTorneo = !empty($t['id_torneo']) ? $t['id_torneo'] : (!empty($t['idTorneo']) ? $t['idTorneo'] : null);
                ?>
                <article class="card"
                    style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; position: relative; backdrop-filter: blur(12px);">
                    <span class="card-badge"
                        style="position: absolute; top: 12px; left: 12px; background: #22c55e; color: white; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; z-index: 5;"><?= htmlspecialchars($textoEstado) ?></span>

                    <div style="width: 100%; height: 180px; background: #000;">
                        <img src="https://unsplash.com" alt="Banner"
                            style="width: 100%; height: 100%; object-fit: cover; opacity: 0.75;">
                    </div>

                    <div class="card-body"
                        style="padding: 24px; display: flex; flex-direction: column; flex-grow: 1; gap: 16px;">
                        <!-- CORREGIDO: Se cambia $t['nombre'] por $t['nombreTorneo'] real de tu BD -->
                        <h3 style="color: #fff; margin: 0; font-size: 1.3rem; font-weight: 800;">
                            <?= htmlspecialchars($t['nombreTorneo']) ?></h3>

                        <div class="card-meta"
                            style="display: flex; flex-direction: column; gap: 10px; color: #94a3b8; font-size: 0.875rem;">
                            <span style="display: flex; align-items: center; gap: 8px;"><i
                                    class="fa-solid fa-gamepad text-purple" style="color: #c084fc; width: 16px;"></i> Juego:
                                <?= htmlspecialchars($t['juego']) ?></span>

                            <span style="display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid <?= $t['modalidad'] === 'equipos' ? 'fa-users' : 'fa-user' ?>"
                                    style="width: 16px;"></i> Modalidad:
                                <?= htmlspecialchars(ucfirst($t['modalidad'])) ?>
                                <?php if (!empty($t['cupos'])): ?>
                                    (Máx: <?= (int) $t['cupos'] ?>)
                                <?php endif; ?>
                            </span>

                            <?php if ($t['fechaInicio']):
                                // CORREGIDO: Formateador nativo estable de fecha corta
                                $date = new DateTime($t['fechaInicio']);
                                $fechaFormateada = $date->format('d M, Y');
                                ?>
                                <span style="display: flex; align-items: center; gap: 8px;"><i class="fa-regular fa-calendar"
                                        style="width: 16px;"></i> Inicia: <?= $fechaFormateada ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Vincula de forma directa a tu nueva y flamante pantalla de detalles.php -->
                        <a href="/index.php?view=torneos/detalles&id=<?= (int) $idRealTorneo ?>" class="btn-card"
                            style="text-align: center; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 12px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 0.9rem; margin-top: auto; transition: all 0.2s;">
                            <?= htmlspecialchars($textoBoton[$t['estado']] ?? 'Ver Detalles') ?>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>