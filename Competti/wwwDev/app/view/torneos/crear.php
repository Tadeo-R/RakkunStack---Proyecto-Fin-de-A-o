<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Rescatar errores acumulados en el controlador si es que hubo fallas reales
$erroresTorneo = $_SESSION['errores_torneo'] ?? [];
unset($_SESSION['errores_torneo']); 

// ==========================================
// PARCHE DE COMPATIBILIDAD CON TU FORMULARIO
// ==========================================
$old = $_POST ?? []; // Captura los datos enviados de forma nativa si falla algo

$valor = function ($campo, $defecto = '') use ($old) {
    return isset($old[$campo]) ? $old[$campo] : $defecto;
};

$marcado = function ($campo, $porDefecto = false) use ($old) {
    if (!empty($old)) {
        return isset($old[$campo]) ? 'checked' : '';
    }
    return $porDefecto ? 'checked' : '';
};

$elegido = function ($campo, $opcion, $porDefecto = '') use ($old) {
    $actual = isset($old[$campo]) ? $old[$campo] : $porDefecto;
    return $actual === $opcion ? 'selected' : '';
};
// ==========================================

try {
    // Rellenar tu datalist con la nueva BD
    $sugerencias = $modeloTorneo->disciplinas();
} catch (PDOException $e) {
    $sugerencias = [];
}
?>
<link rel="stylesheet" href="/css/createTournament.css">

<main class="create-tournament-page">
    <div class="container">

        <header class="page-header">
            <h1 class="page-title">Crear Nuevo Torneo</h1>
            <p class="page-subtitle">Configura los parámetros, formato de competencia y módulos de tu torneo.</p>
        </header>

        <!-- Bloque de impresión nativa de errores del Controlador -->
        <?php if (!empty($erroresTorneo)): ?>
            <div
                style="background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); color: #f87171; padding: 16px; border-radius: 12px; margin-bottom: 24px; list-style-position: inside;">
                <p style="font-weight: bold; margin: 0 0 8px 0;"><i class="fa-solid fa-triangle-exclamation"></i> Por favor
                    corrige los siguientes campos:</p>
                <ul style="margin: 0; padding-left: 10px;">
                    <?php foreach ($erroresTorneo as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Formulario sincronizado al controlador -->
        <form class="tournament-form" id="tournamentForm" action="/index.php?action=create_tournament" method="POST">

            <!-- REMOVIDO: Se elimina el csrf_field() obsoleto que rompía la ejecución -->

            <section class="card form-module">
                <div class="module-header">
                    <h2><i class="fa-solid fa-circle-info"></i> Información General</h2>
                    <span class="badge badge-live">Obligatorio</span>
                </div>
                <div class="card-body">
                    <div class="form-group full-width">
                        <label for="nombre">Nombre del Torneo</label>
                        <!-- CORREGIDO: Cambiado e() por htmlspecialchars() nativo de PHP -->
                        <input type="text" id="nombre" name="nombre" class="form-control"
                            placeholder="Ej: Torneo Apertura 2026" maxlength="120" required>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="categoria">Tipo de Disciplina</label>
                            <select id="categoria" name="categoria" class="form-control" required>
                                <option value="" disabled selected>Selecciona un tipo</option>
                                <option value="deporte">Deporte Físico (Fútbol, Tenis de Mesa, etc.)</option>
                                <option value="esports">eSports / Electrónico (Valorant, FIFA, etc.)</option>
                                <option value="mentales">Juegos Mentales / Mesa (Ajedrez, Cartas, etc.)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="deporte_nombre">Disciplina / Juego Específico</label>
                            <input type="text" id="deporte_nombre" name="deporte_nombre" class="form-control"
                                placeholder="Ej: Ajedrez / FIFA 24 / Fútbol 7" maxlength="100" list="lista-disciplinas"
                                required>
                            <datalist id="lista-disciplinas">
                                <?php foreach ($sugerencias as $s): ?>
                                    <option value="<?= htmlspecialchars($s['nombre'] ?? '') ?>"></option>
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="descripcion">Descripción y Reglamento</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="3" maxlength="2000"
                            placeholder="Puntos particulares del reglamento, lugar o aclaraciones..."></textarea>
                    </div>
                </div>
            </section>

            <section class="card form-module">
                <div class="module-header">
                    <h2><i class="fa-solid fa-sitemap"></i> Formato de Competencia</h2>
                    <span class="badge badge-live">Obligatorio</span>
                </div>
                <div class="card-body">
                    <div class="form-group full-width">
                        <label for="formato">Sistema / Formato de Juego</label>
                        <select id="formato" name="formato" class="form-control" required>
                            <option value="liga" <?= $elegido('formato', 'liga', 'liga') ?>>Liga (Todos contra todos)
                            </option>
                            <option value="eliminacion" <?= $elegido('formato', 'eliminacion') ?>>Eliminación Directa
                                (Playoffs)</option>
                            <option value="suizo" <?= $elegido('formato', 'suizo') ?>>Sistema Suizo</option>
                            <option value="grupos_playoffs" <?= $elegido('formato', 'grupos_playoffs') ?>>Fase de Grupos
                                + Eliminatoria (Opcional)</option>
                        </select>
                    </div>

                    <div class="format-settings-box">
                        <p class="settings-title"><i class="fa-solid fa-sliders"></i> Ajustes de Puntuación y Reglas
                            del Formato</p>

                        <div class="form-grid-3">
                            <div class="form-group">
                                <label for="pts_victoria">Pts. por Victoria</label>
                                <input type="number" id="pts_victoria" name="pts_victoria" class="form-control"
                                    value="<?= e($valor('pts_victoria', 3)) ?>" min="0" max="100">
                            </div>
                            <div class="form-group">
                                <label for="pts_empate">Pts. por Empate</label>
                                <input type="number" id="pts_empate" name="pts_empate" class="form-control"
                                    value="<?= e($valor('pts_empate', 1)) ?>" min="0" max="100">
                            </div>
                            <div class="form-group">
                                <label for="pts_derrota">Pts. por Derrota</label>
                                <input type="number" id="pts_derrota" name="pts_derrota" class="form-control"
                                    value="<?= e($valor('pts_derrota', 0)) ?>" min="0" max="100">
                            </div>
                        </div>

                        <div class="form-group checkbox-group" style="margin-top: 10px;">
                            <label class="custom-checkbox">
                                <input type="checkbox" id="tercer_puesto" name="tercer_puesto"
                                    <?= $marcado('tercer_puesto', true) ?>>
                                <span>Incluir partido/disputa por el 3.º y 4.º puesto</span>
                            </label>
                        </div>
                    </div>
                </div>
            </section>

            <section class="card form-module">
                <div class="module-header">
                    <h2><i class="fa-solid fa-users"></i> Modalidad y Participantes</h2>
                </div>
                <div class="card-body">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="modalidad">Modalidad de Participación</label>
                            <select id="modalidad" name="modalidad" class="form-control">
                                <option value="individual" <?= $elegido('modalidad', 'individual', 'individual') ?>>1 vs
                                    1 (Individual)</option>
                                <option value="equipos" <?= $elegido('modalidad', 'equipos') ?>>Por Equipos</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="cupos">Límite de Cupos / Participantes</label>
                            <input type="number" id="cupos" name="cupos" class="form-control" min="2" max="256"
                                placeholder="Ej: 16" value="<?= e($valor('cupos')) ?>">
                        </div>

                        <div class="form-group">
                            <label for="fecha_inicio">Fecha de inicio</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control"
                                value="<?= e($valor('fecha_inicio')) ?>">
                        </div>

                    </div>
                </div>
            </section>

            <section class="card form-module modular-section">
                <div class="module-header">
                    <label class="toggle-container" for="enableStreaming">
                        <input type="checkbox" id="enableStreaming" name="enableStreaming" class="module-toggle"
                            <?= $marcado('enableStreaming') ?>>
                        <h2><i class="fa-solid fa-tv"></i> Módulo de Transmisión / Streaming</h2>
                    </label>
                    <span class="badge badge-ended">Opcional</span>
                </div>
                <div class="card-body collapsible">
                    <div class="form-group full-width">
                        <label for="twitch">Enlace de Canal (Twitch / YouTube / Kick)</label>
                        <input type="url" id="twitch" name="twitch" class="form-control" maxlength="255"
                            placeholder="https://twitch.tv/tu_canal" value="<?= e($valor('twitch')) ?>">
                    </div>
                </div>
            </section>

            <section class="card form-module modular-section">
                <div class="module-header">
                    <label class="toggle-container" for="enablePrizes">
                        <input type="checkbox" id="enablePrizes" name="enablePrizes" class="module-toggle"
                            <?= $marcado('enablePrizes') ?>>
                        <h2><i class="fa-solid fa-trophy"></i> Módulo de Premiación</h2>
                    </label>
                    <span class="badge badge-ended">Opcional</span>
                </div>
                <div class="card-body collapsible">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="premio1">1.º Lugar</label>
                            <input type="text" id="premio1" name="premio1" class="form-control" maxlength="100"
                                placeholder="Ej: $500 + Copa" value="<?= e($valor('premio1')) ?>">
                        </div>
                        <div class="form-group">
                            <label for="premio2">2.º Lugar</label>
                            <input type="text" id="premio2" name="premio2" class="form-control" maxlength="100"
                                placeholder="Ej: $200 + Medalla" value="<?= e($valor('premio2')) ?>">
                        </div>
                    </div>
                </div>
            </section>

            <div class="form-actions">
                <a href="/index?view=torneos/torneos" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Publicar
                    Torneo</button>
            </div>

        </form>
    </div>
</main>