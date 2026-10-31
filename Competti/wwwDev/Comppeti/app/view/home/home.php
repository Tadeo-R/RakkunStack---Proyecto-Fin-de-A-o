<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obtener los torneos activos reales desde el modelo
$torneosDestacados = [];
try {
    // Traemos los últimos torneos creados para destacarlos en el home
    $stmtHome = $pdo->prepare("SELECT * FROM TORNEO ORDER BY id_torneo DESC LIMIT 3");
    $stmtHome->execute();
    $torneosDestacados = $stmtHome->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $torneosDestacados = [];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Competti - Pensalos. Crealos. Disfrutalos.</title>
    <script src="https://kit.fontawesome.com/3bd97052d9.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/hamburgers/1.2.1/hamburgers.min.css">
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/navigation.css">
    <link rel="stylesheet" href="/css/home.css">
    <link rel="stylesheet" href="/css/footer.css">
</head>

<body>

    <main>

        <section class="hero">
            <div class="hero-info">
                <h1 class="animate__animated animate__fadeInDown">Pensalos<br>Crealos<br><span
                        class="highlight">Disfrutalos</span></h1>
                <p>La plataforma integral para la gesti&oacute;n de torneos deportivos, mentales y electr&oacute;nicos.
                </p>
                <div class="hero-buttons">
                    <a href="/index.php?view=torneos/torneos" class="btn-primary"><i class="fa-solid fa-trophy"></i> Explorar Torneos</a>
                    <a href="/index.php?view=torneos/crear" class="btn-secondary"><i class="fa-solid fa-plus"></i> Crear Torneo</a>
                </div>
            </div>
            <div class="hero-gallery">
                <div class="gallery-item" style="background-image: url('/img/futbol.png');"></div>
                <div class="gallery-item" style="background-image: url('/img/joystickPS5.png');">
                </div>
                <div class="gallery-item" style="background-image: url('/img/ajedrez.png');">
                </div>
                <div class="gallery-item" style="background-image: url('/img/pingpong.png');">
                </div>
                <div class="gallery-item" style="background-image: url('/img/valorant.png');">
                </div>
            </div>
        </section>

        <section class="disciplines-section">
            <div class="section-header">
                <h2>Disciplinas</h2>
                <a href="/index.php?view=torneos/disciplinas" class="view-all">Ver todas las disciplinas &rarr;</a>
            </div>
            <div class="disciplines-grid">
                <div class="discipline-card"><i class="fa-solid fa-soccer-ball"></i><span>F&uacute;tbol</span></div>
                <div class="discipline-card"><i class="fa-solid fa-basketball"></i><span>B&aacute;squetbol</span></div>
                <div class="discipline-card"><i class="fa-solid fa-volleyball"></i><span>V&oacute;ley</span></div>
                <div class="discipline-card"><i class="fa-solid fa-table-tennis-paddle-ball"></i><span>Tenis de
                        mesa</span></div>
                <div class="discipline-card"><i class="fa-solid fa-chess-knight"></i><span>Ajedrez</span></div>
                <div class="discipline-card"><i class="fa-solid fa-gamepad"></i><span>E-Sports</span></div>
                <div class="discipline-card"><i class="fa-solid fa-person-running"></i><span>Atletismo</span></div>
                <div class="discipline-card"><i class="fa-solid fa-ellipsis"></i><span>Otros</span></div>
            </div>
        </section>

        <section class="search-filter-section">
            <div class="search-wrapper">
                <a href="/index.php?view=torneos/search">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                </a>
                <input type="search" placeholder="Buscar torneos, equipos o deportes...">
            </div>
            <div class="filter-controls">
                <select>
                    <option>Deporte</option>
                </select>
                <select>
                    <option>Estado</option>
                </select>
                <button class="btn btn-primary">Buscar</button>
            </div>
        </section>

        <div class="content-layout">
            <div class="primary-column">
                <section class="matches-section">
                    <div class="section-header">
                        <h2>Pr&oacute;ximos partidos</h2>
                        <a href="/index.php?view=partidos/calendario" class="view-all">Ver calendario &rarr;</a>
                    </div>
                    <div class="matches-list">
                        <div class="match-row">
                            <div class="time-block"><span>Hoy</span><strong>18:00</strong></div>
                            <div class="teams-block">
                                <div class="team"><span class="team-name">Los Leones</span></div>
                                <span class="vs">vs</span>
                                <div class="team"><span class="team-name">Team Alpha</span></div>
                            </div>
                        </div>
                        <div class="match-row">
                            <div class="time-block"><span>Ma&ntilde;ana</span><strong>16:30</strong></div>
                            <div class="teams-block">
                                <div class="team"><span class="team-name">Titanes FC</span></div>
                                <span class="vs">vs</span>
                                <div class="team"><span class="team-name">Blue Lions</span></div>
                            </div>
                        </div>
                        <div class="match-row">
                            <div class="time-block"><span>Ma&ntilde;ana</span><strong>20:00</strong></div>
                            <div class="teams-block">
                                <div class="team"><span class="team-name">Sauce&ntilde;os</span></div>
                                <span class="vs">vs</span>
                                <div class="team"><span class="team-name">Montevideanos</span></div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="featured-tournaments">
                    <div class="section-header">
                        <h2>Torneos destacados</h2>
                        <a href="/index.php?view=torneos/torneos" class="view-all">Ver todos los torneos &rarr;</a>
                    </div>
                    
                    <?php if (empty($torneosDestacados)): ?>
                        <div style="text-align: center; padding: 40px; color: #94a3b8; background: rgba(15, 23, 42, 0.4); border-radius: 16px; border: 1px solid rgba(255,255,255,0.06);">
                            <p>No hay torneos activos en este momento. ¡Crea el primero!</p>
                            <a href="/index.php?view=torneos/crear" class="btn-primary" style="margin-top: 12px; display: inline-block;">Crear Torneo</a>
                        </div>
                    <?php else: ?>
                        <div class="tournaments-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                            <?php foreach ($torneosDestacados as $t): 
                                $iconoCat = ($t['categoria'] ?? '') === 'esports' ? 'fa-gamepad' : 'fa-trophy';
                                $estadoClase = $t['estado'] === 'inscripciones' ? 'badge-open' : 'badge-live';
                                $estadoTexto = $t['estado'] === 'inscripciones' ? 'Inscripciones Abiertas' : ucfirst(str_replace('_', ' ', $t['estado']));
                            ?>
                                <div class="tournament-card" style="background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; overflow: hidden; display: flex; flex-direction: column;">
                                    <div class="card-img" style="height: 140px; background: linear-gradient(135deg, rgba(30, 27, 75, 0.8) 0%, rgba(15, 23, 42, 0.9) 100%); display: flex; align-items: center; justify-content: center; position: relative;">
                                        <i class="fa-solid <?= $iconoCat ?>" style="font-size: 2.5rem; color: rgba(192, 132, 252, 0.5);"></i>
                                        <span class="badge <?= $estadoClase ?>" style="position: absolute; top: 10px; left: 10px; padding: 4px 10px; border-radius: 12px; font-size: 0.7rem; font-weight: 700; background: #22c55e; color: #fff;"><?= htmlspecialchars($estadoTexto) ?></span>
                                    </div>
                                    <div class="card-body" style="padding: 18px; display: flex; flex-direction: column; flex-grow: 1; gap: 10px;">
                                        <h3 style="color: #fff; margin: 0; font-size: 1.15rem; font-weight: 700;"><?= htmlspecialchars($t['nombreTorneo']) ?></h3>
                                        <p style="color: #94a3b8; font-size: 0.85rem; margin: 0;">
                                            <i class="fa-solid <?= $iconoCat ?>" style="color: #c084fc;"></i> <?= htmlspecialchars($t['juego']) ?> | 
                                            <i class="fa-solid <?= $t['modalidad'] === 'equipos' ? 'fa-users' : 'fa-user' ?>"></i> <?= htmlspecialchars(ucfirst($t['modalidad'])) ?>
                                        </p>
                                        <p style="color: #94a3b8; font-size: 0.85rem; margin: 0;">
                                            <i class="fa-regular fa-calendar"></i> Inicio: <?= htmlspecialchars($t['fechaInicio'] ?? 'Próximamente') ?>
                                        </p>
                                        <a href="/index.php?view=torneos/detalles&id=<?= (int)$t['id_torneo'] ?>" class="btn-details" style="margin-top: auto; text-align: center; background: rgba(168, 85, 247, 0.15); border: 1px solid rgba(168, 85, 247, 0.3); color: #c084fc; padding: 10px; border-radius: 8px; font-weight: 700; text-decoration: none; font-size: 0.85rem;">Ver detalles</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
            <aside class="secondary-column">
                <section class="matches-section">
                    <div class="section-header">
                        <h2>Pr&oacute;ximos partidos</h2>
                        <a href="/index.php?view=partidos/calendario" class="view-all">Ver calendario &rarr;</a>
                    </div>
                    <div class="matches-list">
                        <div class="match-row">
                            <div class="time-block"><span>Hoy</span><strong>18:00</strong></div>
                            <div class="teams-block">
                                <div class="team"><span class="team-name">Los Leones</span></div><span
                                    class="vs">vs</span>
                                <div class="team"><span class="team-name">Team Alpha</span></div>
                            </div>
                        </div>
                        <div class="match-row">
                            <div class="time-block"><span>Ma&ntilde;ana</span><strong>16:30</strong></div>
                            <div class="teams-block">
                                <div class="team"><span class="team-name">Titanes FC</span></div><span
                                    class="vs">vs</span>
                                <div class="team"><span class="team-name">Blue Lions</span></div>
                            </div>
                        </div>
                        <div class="match-row">
                            <div class="time-block"><span>Ma&ntilde;ana</span><strong>20:00</strong></div>
                            <div class="teams-block">
                                <div class="team"><span class="team-name">Sauce&ntilde;os</span></div><span
                                    class="vs">vs</span>
                                <div class="team"><span class="team-name">Montevideanos</span></div>
                            </div>
                        </div>
                    </div>
                </section>
            </aside>
        </div>

        <section class="testimonials-section">
            <div class="section-header">
                <h2>Lo que dice la comunidad</h2>
            </div>
            <!-- 1. Este es el contenedor principal que oculta el desborde -->
            <div class="testimonials-window">
                <!-- 2. Este es el "riel" que moverá las tarjetas -->
                <div class="testimonials-grid" id="testimonials-track">

                    <div class="testimonial-card">
                        <i class="fa-solid fa-quote-left"></i>
                        <p class="testimonial-text">Organizamos la liga del barrio y en nada ya estaba creado. El
                            calendario automatico nos ahorro mucho quilombo.</p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">TR</div>
                            <div>
                                <p class="testimonial-name">Tadeo Rodriguez</p>
                                <p class="testimonial-role">Capitan, Los Leones</p>
                            </div>
                        </div>
                    </div>

                    <div class="testimonial-card">
                        <i class="fa-solid fa-quote-left"></i>
                        <p class="testimonial-text">Como Arbitro, cargar resultados y ver las estadisticas enseguida nos
                            ahorro mucho trabajo.</p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">BC</div>
                            <div>
                                <p class="testimonial-name">Benja copetti</p>
                                <p class="testimonial-role">Arbitro, Liga Apertura</p>
                            </div>
                        </div>
                    </div>

                    <div class="testimonial-card">
                        <i class="fa-solid fa-quote-left"></i>
                        <p class="testimonial-text">Armamos un torneo de e-sports con inscripciones abiertas y ahora
                            tenemos a 32 jugadores en la semana.</p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">GB</div>
                            <div>
                                <p class="testimonial-name">Gahel Bordon</p>
                                <p class="testimonial-role">Organizador, Torneo Gamer X</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

    </main>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const track = document.getElementById("testimonials-track");
            const windowContainer = document.querySelector(".testimonials-window");

            if (!track || !windowContainer) return;

            // 1. Clonamos suficientes tarjetas al inicio para llenar la pantalla y evitar huecos vacíos
            const originalCards = Array.from(track.children);

            // Clonamos el set de tarjetas 2 veces para asegurar cobertura en pantallas ultra anchas
            for (let i = 0; i < 2; i++) {
                originalCards.forEach(card => {
                    const clone = card.cloneNode(true);
                    track.appendChild(clone);
                });
            }

            // 2. Variables de control de la animación
            let speed = 1.5; // Velocidad del scroll (1 píxel por fotograma). Sube a 1.5 o 2 si quieres que vaya más rápido.
            let scrollPosition = 0;
            let isPaused = false;

            // 3. Pausar al poner el mouse encima
            windowContainer.addEventListener("mouseenter", () => isPaused = true);
            windowContainer.addEventListener("mouseleave", () => isPaused = false);

            // Pausar también en dispositivos táctiles si mantienen presionado
            windowContainer.addEventListener("touchstart", () => isPaused = true);
            windowContainer.addEventListener("touchend", () => isPaused = false);

            // 4. Función del bucle de animación de alto rendimiento
            function animate() {
                if (!isPaused) {
                    scrollPosition += speed;

                    // Calculamos cuánto mide el bloque de las tarjetas originales (incluyendo el gap de 20px)
                    // Tomamos la primera tarjeta original para medir su tamaño exacto actual
                    const firstCardWidth = originalCards[0].getBoundingClientRect().width + 20;
                    const totalOriginalWidth = firstCardWidth * originalCards.length;

                    // Si nos hemos desplazado el equivalente a todo el set original, volvemos a cero de forma imperceptible
                    if (scrollPosition >= totalOriginalWidth) {
                        scrollPosition = 0;
                    }

                    // Aplicamos el movimiento por hardware con translate3d
                    track.style.transform = `translate3d(-${scrollPosition}px, 0, 0)`;
                }

                // Ejecuta la animación en sincronía con los hercios (Hz) de la pantalla
                requestAnimationFrame(animate);
            }

            // Iniciar el carrusel
            animate();
        });

    </script>
</body>

</html>