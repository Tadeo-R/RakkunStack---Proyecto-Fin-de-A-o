<?php
// Centro de notificaciones del usuario logueado (acceso protegido en html/index.php).
$usuarioId = (int) $_SESSION['usuario_id'];

$filtrosValidos = ['todas', 'sin_leer', 'torneos', 'sistema'];
$filtro = $_GET['filtro'] ?? 'todas';
$filtro = in_array($filtro, $filtrosValidos, true) ? $filtro : 'todas';
$q = trim($_GET['q'] ?? '');

try {
    $notificaciones = $modeloNotificacion->listar($usuarioId, $filtro, $q);
    $contadores     = $modeloNotificacion->contadores($usuarioId);
} catch (PDOException $e) {
    $notificaciones = [];
    $contadores = ['total' => 0, 'sin_leer' => 0];
    flash_set('error', 'No se pudieron cargar las notificaciones.');
}

// Clase de color e icono según el tipo
$estiloTipo = [
    'torneo'  => ['icon-trophy',  'fa-trophy'],
    'partido' => ['icon-game',    'fa-gamepad'],
    'alerta'  => ['icon-warning', 'fa-triangle-exclamation'],
    'sistema' => ['icon-system',  'fa-shield-halved'],
];

$pestanas = [
    'todas'    => 'Todas',
    'sin_leer' => 'Sin Leer',
    'torneos'  => 'Torneos',
    'sistema'  => 'Sistema',
];

?>
<link rel="stylesheet" href="/css/pages.css">

    <main class="container">

        <div class="main-panel">

            <header class="panel-header">
                <div>
                    <h1 class="page-title"><i class="fa-solid fa-bell"></i> Centro de Notificaciones</h1>
                    <p class="subtitle">Mantente al tanto de tus inscripciones, partidos y novedades</p>
                </div>
                <div class="header-actions">
                    <form class="inline-form" action="/index.php?action=notif_leer_todas" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="filtro" value="<?= e($filtro) ?>">
                        <input type="hidden" name="q" value="<?= e($q) ?>">
                        <button type="submit" class="btn-action-secondary" title="Marcar todas como leídas">
                            <i class="fa-solid fa-check-double"></i> Marcar leídas
                        </button>
                    </form>
                    <form class="inline-form" action="/index.php?action=notif_limpiar" method="POST"
                        onsubmit="return confirm('¿Eliminar todas las notificaciones leídas?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="filtro" value="<?= e($filtro) ?>">
                        <input type="hidden" name="q" value="<?= e($q) ?>">
                        <button type="submit" class="btn-action-danger" title="Eliminar leídas">
                            <i class="fa-regular fa-trash-can"></i> Limpiar
                        </button>
                    </form>
                </div>
            </header>

            <?= render_flash() ?>

            <form class="toolbar-panel" action="/index.php" method="GET">
                <input type="hidden" name="view" value="notificaciones">
                <!-- Botón por defecto (Enter en el buscador): conserva la pestaña actual -->
                <button type="submit" name="filtro" value="<?= e($filtro) ?>" class="visually-hidden"
                    tabindex="-1" aria-hidden="true">Buscar</button>

                <div class="filter-tabs">
                    <?php foreach ($pestanas as $clave => $texto): ?>
                        <button type="submit" name="filtro" value="<?= e($clave) ?>"
                            class="tab-btn <?= $filtro === $clave ? 'active' : '' ?>">
                            <?= e($texto) ?>
                            <?php if ($clave === 'todas'): ?>
                                <span class="tab-badge"><?= (int) $contadores['total'] ?></span>
                            <?php elseif ($clave === 'sin_leer'): ?>
                                <span class="tab-badge highlight"><?= (int) $contadores['sin_leer'] ?></span>
                            <?php endif; ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="toolbar-search">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" name="q" value="<?= e($q) ?>" placeholder="Buscar en notificaciones...">
                </div>
            </form>

            <div class="notifications-list">

                <?php if (!$notificaciones): ?>
                    <div class="empty-state">
                        <i class="fa-regular fa-bell-slash"></i>
                        <p><?= $q !== '' || $filtro !== 'todas' ? 'No hay notificaciones con ese filtro.' : 'No tenés notificaciones por ahora.' ?></p>
                    </div>
                <?php endif; ?>

                <?php foreach ($notificaciones as $n):
                    list($claseIcono, $icono) = $estiloTipo[$n['tipo']];
                    $noLeida = (int) $n['leida'] === 0;
                ?>
                    <div class="notification-item <?= $noLeida ? 'unread' : '' ?>">
                        <div class="notif-icon <?= e($claseIcono) ?>">
                            <i class="fa-solid <?= e($icono) ?>"></i>
                        </div>
                        <div class="notif-content">
                            <div class="notif-header">
                                <h4 class="notif-title"><?= e($n['titulo']) ?></h4>
                                <span class="notif-time"><i class="fa-regular fa-clock"></i> <?= e(tiempo_relativo($n['creado_en'])) ?></span>
                            </div>
                            <p class="notif-desc"><?= texto_enriquecido($n['mensaje']) ?></p>
                        </div>
                        <div class="notif-actions">
                            <?php if ($noLeida): ?>
                                <form class="inline-form" action="/index.php?action=notif_leer" method="POST">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $n['id'] ?>">
                                    <input type="hidden" name="filtro" value="<?= e($filtro) ?>">
                                    <input type="hidden" name="q" value="<?= e($q) ?>">
                                    <button type="submit" class="btn-circle" title="Marcar como leída"><i class="fa-solid fa-check"></i></button>
                                </form>
                            <?php endif; ?>
                            <form class="inline-form" action="/index.php?action=notif_eliminar" method="POST">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $n['id'] ?>">
                                <input type="hidden" name="filtro" value="<?= e($filtro) ?>">
                                <input type="hidden" name="q" value="<?= e($q) ?>">
                                <button type="submit" class="btn-circle danger" title="Eliminar"><i class="fa-solid fa-xmark"></i></button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>

        </div>

    </main>

