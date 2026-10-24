<?php
try {
    $equipos = $modeloEquipo->listar();
} catch (PDOException $e) {
    $equipos = [];
    flash_set('error', 'No se pudieron cargar los equipos.');
}

$pageTitle = 'Equipos';
$pageCss   = ['pages.css'];
require VIEW_PATH . '/include/page_start.php';
?>

    <main class="container">
        <h1 class="page-title">Equipos Registrados</h1>

        <?= render_flash() ?>

        <?php if (!$equipos): ?>
            <div class="empty-state">
                <i class="fa-solid fa-users"></i>
                <p>Todavía no hay equipos registrados.</p>
            </div>
        <?php else: ?>
            <div class="grid-3">
                <?php foreach ($equipos as $eq): ?>
                    <div class="card card-body" style="text-align: center;">
                        <div style="font-size: 3rem; color: <?= e(color_tipo($eq['tipo'])) ?>; margin-bottom: 10px;">
                            <i class="fa-solid <?= e(icono_tipo($eq['tipo'])) ?>"></i>
                        </div>
                        <h3><?= e($eq['nombre']) ?></h3>
                        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 5px;">
                            <?= e($eq['deporte']) ?> • <?= (int) $eq['integrantes'] ?> Integrante<?= (int) $eq['integrantes'] === 1 ? '' : 's' ?>
                        </p>
                        <div style="margin-top: 15px;">
                            <button class="btn btn-secondary"
                                style="color: var(--text-main); border-color: #ccc; width: 100%; justify-content: center;">Ver
                                perfil</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

<?php require VIEW_PATH . '/include/page_end.php'; ?>
