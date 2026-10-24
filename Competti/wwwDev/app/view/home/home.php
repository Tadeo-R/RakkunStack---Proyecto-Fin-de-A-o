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

    <?php require_once VIEW_PATH . '/include/nav.php'; ?>

    <main>

        <section class="hero">
            <div class="hero-info">
                <h1 class="animate__animated animate__fadeInDown">Pensalos<br>Crealos<br><span
                        class="highlight">Disfrutalos</span></h1>
                <p>La plataforma integral para la gesti&oacute;n de torneos deportivos, mentales y electr&oacute;nicos.
                </p>
                <div class="hero-buttons">
                    <a href="/index.php?view=torneos" class="btn-primary"><i class="fa-solid fa-trophy"></i> Explorar Torneos</a>
                    <a href="/index.php?view=crear-torneo" class="btn-secondary"><i class="fa-solid fa-plus"></i> Crear Torneo</a>
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
                <a href="/index.php?view=disciplinas" class="view-all">Ver todas las disciplinas &rarr;</a>
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
                <a href="/index.php?view=search">
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
                        <a href="/index.php?view=calendario" class="view-all">Ver calendario &rarr;</a>
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
                        <a href="/index.php?view=torneos" class="view-all">Ver todos los torneos &rarr;</a>
                    </div>
                    <div class="tournaments-grid">
                        <div class="tournament-card">
                            <div class="card-img" style="background-image: url('/img/pibble.jpg');">
                                <span class="badge badge-live">En curso</span>
                            </div>
                            <div class="card-body">
                                <h3>Liga Apertura 2024</h3>
                                <p><i class="fa-solid fa-soccer-ball"></i> F&uacute;tbol | <i
                                        class="fa-solid fa-users"></i> 16 Equipos</p>
                                <p><i class="fa-solid fa-calendar"></i> Inicio: 12/05/2024</p>
                                <a href="#" class="btn-details">Ver detalles</a>
                            </div>
                        </div>
                        <div class="tournament-card">
                            <div class="card-img" style="background-image: url('/img/joystickPS5.png');">
                                <span class="badge badge-upcoming">Pr&oacute;ximo</span>
                            </div>
                            <div class="card-body">
                                <h3>Torneo Gamer X</h3>
                                <p><i class="fa-solid fa-gamepad"></i> E-Sports | <i class="fa-solid fa-user"></i> 32
                                    Jugadores</p>
                                <p><i class="fa-solid fa-calendar"></i> Inicio: 01/06/2024</p>
                                <a href="#" class="btn-details">Ver detalles</a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <aside class="secondary-column">
                <section class="matches-section">
                    <div class="section-header">
                        <h2>Pr&oacute;ximos partidos</h2>
                        <a href="/index.php?view=calendario" class="view-all">Ver calendario &rarr;</a>
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
            <div class="testimonials-window">
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

    <?php require_once VIEW_PATH . '/include/footer.php'; ?>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const track = document.getElementById("testimonials-track");
            const windowContainer = document.querySelector(".testimonials-window");

            if (!track || !windowContainer) return;

            const originalCards = Array.from(track.children);


            for (let i = 0; i < 2; i++) {
                originalCards.forEach(card => {
                    const clone = card.cloneNode(true);
                    track.appendChild(clone);
                });
            }

      
            let speed = 1.5; 
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

                    // Calculamos cuánto mide el bloque de las tarjetas originales
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

                
                requestAnimationFrame(animate);
            }

            
            animate();
        });

    </script>
</body>

</html>