<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Consulta limpia directa para traer de forma masiva los equipos de tu amigo
try {
    // MySQL en Linux es sensible a mayúsculas: llamamos a la tabla EQUIPO tal cual está en phpMyAdmin
    $stmtTodosLosEq = $pdo->query("SELECT * FROM EQUIPO WHERE estado = 'activo' ORDER BY id_equipo DESC");
    $listadoGlobalEquipos = $stmtTodosLosEq->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $listadoGlobalEquipos = [];
}
?>
<link rel="stylesheet" href="/css/pages.css">

<main class="container"
    style="padding: 40px 20px; max-width: 1200px; margin: 0 auto; color: #f8fafc; font-family: 'Inter', sans-serif;">

    <header class="page-header" style="margin-bottom: 30px;">
        <h1 class="page-title" style="color: #fff; font-weight: 850; margin: 0 0 8px 0; font-size: 2rem;">Equipos
            Registrados</h1>
        <p class="subtitle" style="color: #94a3b8; margin: 0; font-size: 1rem;">Explora las escuadras y comunidades
            deportivas que compiten en la plataforma</p>
    </header>

    <!-- REMOVIDO: Se elimina el render_flash() obsoleto -->

    <?php if (empty($listadoGlobalEquipos)): ?>
        <!-- Mensaje de Estado Vacío si no hay registros cargados -->
        <div class="empty-state"
            style="text-align: center; padding: 60px; color: #94a3b8; background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 20px; backdrop-filter: blur(12px);">
            <i class="fa-solid fa-users"
                style="font-size: 3rem; margin-bottom: 16px; display: block; color: rgba(255,255,255,0.1);"></i>
            <p style="margin: 0; font-size: 1.1rem; color: #fff; font-weight: 600;">Todavía no hay equipos registrados</p>
            <p style="margin: 6px 0 0 0; font-size: 0.9rem; color: #64748b;">Cuando los organizadores o competidores funden
                sus plantillas, aparecerán aquí.</p>
        </div>
    <?php else: ?>
        <!-- Grilla de Tarjetas de Escuadras Oficiales -->
        <div class="grid-3"
            style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px; box-sizing: border-box; width: 100%;">
            <?php foreach ($listadoGlobalEquipos as $eq):
                // Filtro para ocultar estéticamente el texto del sistema de los registros individuales de usuarios
                $nombreLimpio = strpos($eq['nombreEquipo'], 'Inscripción -') !== false
                    ? str_replace('Inscripción -', 'Escuadra de', $eq['nombreEquipo'])
                    : $eq['nombreEquipo'];
                ?>
                <div class="card card-body"
                    style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 16px; padding: 30px 24px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; backdrop-filter: blur(12px); box-sizing: border-box; width: 100%;">

                    <!-- Ícono de Escudo Competitivo con degradado gamer/deportivo -->
                    <div
                        style="font-size: 3rem; margin-bottom: 16px; background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block;">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>

                    <!-- CORREGIDO: Mapeamos la columna 'nombreEquipo' real de phpMyAdmin -->
                    <h3 style="color: #fff; margin: 0 0 6px 0; font-size: 1.25rem; font-weight: 800; line-height: 1.3;">
                        <?= htmlspecialchars($nombreLimpio) ?></h3>

                    <p style="color: #94a3b8; font-size: 0.85rem; margin: 0; display: flex; align-items: center; gap: 6px;">
                        <!-- Colocamos una etiqueta de estado genérica basada en tu columna 'estado' -->
                        <span
                            style="background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); color: #34d399; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                            <?= htmlspecialchars($eq['estado']) ?>
                        </span>
                        • Club Oficial COMPETTI
                    </p>

                    <div style="margin-top: 24px; width: 100%; box-sizing: border-box;">
                        <!-- Transformado a una etiqueta 'a' enrutada dinámicamente con el id_equipo real de tu BD -->
                        <a href="/index.php?view=equipos/perfilEquipos&id=<?= (int) $eq['id_equipo'] ?>" class="btn btn-secondary"
                            style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); color: #fff; width: 100%; padding: 12px; border-radius: 10px; font-weight: 700; font-size: 0.9rem; cursor: pointer; text-decoration: none; display: block; text-align: center; box-sizing: border-box; transition: background 0.2s;">
                            Ver perfil del equipo
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>