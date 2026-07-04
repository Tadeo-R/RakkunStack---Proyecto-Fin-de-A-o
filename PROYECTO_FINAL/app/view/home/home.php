<?php
require_once __DIR__ . '/../../../config/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Torneos - Organiza, Compite, Gana</title>
    <script src="https://kit.fontawesome.com/3bd97052d9.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/global.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/home.css">
</head>

<body>

    <?php require_once __DIR__ . '/../include/nav.php'; ?>

    <main class="main-content">
        <section class="hero">
            <div class="hero-info">
                <h1>Organizá.<br>Competí.<br><span class="highlight">Ganá.</span></h1>
                <p>La plataforma integral para la gestión de torneos deportivos, mentales y electrónicos.</p>
                <div class="hero-buttons">
                    <a href="#" class="btn btn-primary"><i class="fa-solid fa-trophy"></i> Explorar Torneos</a>
                    <a href="#" class="btn btn-secondary"><i class="fa-solid fa-plus"></i> Crear Torneo</a>
                </div>
            </div>
            <div class="hero-gallery">
                <div class="gallery-item" style="background-image: url('<?= BASE_URL ?>/public/img/fubol.jpg');"></div>
                <div class="gallery-item" style="background-image: url('<?= BASE_URL ?>/public/img/ps5.jpg');"></div>
                <div class="gallery-item" style="background-image: url('<?= BASE_URL ?>/public/img/ajedre.jpg');"></div>
                <div class="gallery-item" style="background-image: url('<?= BASE_URL ?>/public/img/valo.jpg');"></div>
                <div class="gallery-item" style="background-image: url('<?= BASE_URL ?>/public/img/pinpon.jpg');"></div>
            </div>
        </section>

        <section class="search-filter-section">
            <div class="search-wrapper">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" placeholder="Buscar torneos, equipos o deportes...">
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

        <section class="disciplines-section">
            <div class="section-header">
                <h2>Disciplinas</h2>
                <a href="#" class="view-all">Ver todas las disciplinas &rarr;</a>
            </div>
            <div class="disciplines-grid">
                <div class="discipline-card active"><i class="fa-solid fa-soccer-ball"></i><span>Fútbol</span></div>
                <div class="discipline-card"><i class="fa-solid fa-basketball"></i><span>Básquetbol</span></div>
                <div class="discipline-card"><i class="fa-solid fa-volleyball"></i><span>Vóley</span></div>
                <div class="discipline-card"><i class="fa-solid fa-table-tennis-paddle-ball"></i><span>Tenis de
                        mesa</span></div>
                <div class="discipline-card"><i class="fa-solid fa-chess-knight"></i><span>Ajedrez</span></div>
                <div class="discipline-card"><i class="fa-solid fa-gamepad"></i><span>E-Sports</span></div>
                <div class="discipline-card"><i class="fa-solid fa-person-running"></i><span>Atletismo</span></div>
                <div class="discipline-card"><i class="fa-solid fa-ellipsis"></i><span>Otros</span></div>
            </div>
        </section>

        <div class="content-layout">

            <div class="primary-column">

                <section class="matches-section mobile-only">
                    <div class="section-header">
                        <h2>Próximos partidos</h2>
                        <a href="#" class="view-all">Ver calendario</a>
                    </div>
                    <div class="matches-list">
                        <div class="match-card">
                            <span class="match-time">Hoy 18:00</span>
                            <div class="match-teams">Los Leones <span>vs</span> Team Alpha</div>
                        </div>
                        <div class="match-card">
                            <span class="match-time">Mañana 16:30</span>
                            <div class="match-teams">Titanes FC <span>vs</span> Blue Lions</div>
                        </div>
                    </div>
                </section>

                <section class="featured-tournaments">
                    <div class="section-header">
                        <h2>Torneos destacados</h2>
                        <a href="#" class="view-all">Ver todos los torneos &rarr;</a>
                    </div>
                    <div class="tournaments-grid">
                        <div class="tournament-card">
                            <div class="card-img" style="background-image: url('<?= BASE_URL ?>/public/img/copa.jpg');">
                                <span class="badge badge-live">En curso</span>
                            </div>
                            <div class="card-body">
                                justify<h3>Liga Apertura 2024</h3>
                                <p><i class="fa-solid fa-soccer-ball"></i> Fútbol | <i class="fa-solid fa-users"></i> 16
                                    Equipos</p>
                                <p><i class="fa-solid fa-calendar"></i> Inicio: 12/05/2024</p>
                                <a href="#" class="btn-details">Ver detalles</a>
                            </div>
                        </div>
                        <div class="tournament-card">
                            <div class="card-img"
                                style="background-image: url('<?= BASE_URL ?>/public/img/gamer.jpg');">
                                <span class="badge badge-upcoming">Próximo</span>
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

                <section class="benefits-section">
                    <div class="benefits-grid">
                        <div class="benefit-item"><i class="fa-solid fa-shield-halved"></i>
                            <h4>Seguro</h4>
                            <p>Protegemos tus datos y tu información.</p>
                        </div>
                        <div class="benefit-item"><i class="fa-solid fa-clock"></i>
                            <h4>Simple</h4>
                            <p>Administrá torneos de forma fácil y rápida.</p>
                        </div>
                        <div class="benefit-item"><i class="fa-solid fa-mobile-screen"></i>
                            <h4>En cualquier lugar</h4>
                            <p>Accedé desde cualquier dispositivo.</p>
                        </div>
                        <div class="benefit-item"><i class="fa-solid fa-headset"></i>
                            <h4>Soporte</h4>
                            <p>Estamos para ayudarte siempre.</p>
                        </div>
                    </div>
                </section>
            </div>

            <aside class="secondary-column">

                <section class="matches-section">
                    <div class="section-header">
                        <h2>Próximos partidos</h2>
                        <a href="#" class="view-all">Ver calendario</a>
                    </div>
                    <div class="matches-list">

                        <div class="match-row">
                            <div class="time-block">
                                <span>Hoy</span>
                                <strong>18:00</strong>
                            </div>
                            <div class="teams-block">
                                <div class="team">
                                    <span class="team-name">Los Leones</span>
                                </div>
                                <span class="vs">vs</span>
                                <div class="team">
                                    <span class="team-name">Team Alpha</span>
                                </div>
                            </div>
                        </div>

                        <div class="match-row">
                            <div class="time-block">
                                <span>Mañana</span>
                                <strong>16:30</strong>
                            </div>
                            <div class="teams-block">
                                <div class="team">
                                    <span class="team-name">Titanes FC</span>
                                </div>
                                <span class="vs">vs</span>
                                <div class="team">
                                    <span class="team-name">Blue Lions</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>

                <section class="stats-section">
                    <h3>Estadísticas generales</h3>
                    <div class="stats-grid">
                        <div class="stat-box"><i
                                class="fa-solid fa-trophy text-blue"></i><strong>128</strong><span>Torneos</span></div>
                        <div class="stat-box"><i
                                class="fa-solid fa-users text-green"></i><strong>2.456</strong><span>Participantes</span>
                        </div>
                        <div class="stat-box"><i
                                class="fa-solid fa-user-group text-purple"></i><strong>346</strong><span>Equipos</span>
                        </div>
                        <div class="stat-box"><i
                                class="fa-solid fa-chart-simple text-orange"></i><strong>1.892</strong><span>Partidos</span>
                        </div>
                    </div>
                </section>

                <section class="quick-actions-section">
                    <h3>Acceso rápido</h3>
                    <div class="action-list">
                        <a href="#" class="action-item"><span><i class="fa-solid fa-plus-circle"></i> Crear
                                torneo</span> <i class="fa-solid fa-chevron-right"></i></a>
                        <a href="#" class="action-item"><span><i class="fa-solid fa-trophy"></i> Mis torneos</span> <i
                                class="fa-solid fa-chevron-right"></i></a>
                        <a href="#" class="action-item"><span><i class="fa-solid fa-users"></i> Mis equipos</span> <i
                                class="fa-solid fa-chevron-right"></i></a>
                        <a href="#" class="action-item"><span><i class="fa-solid fa-square-poll-vertical"></i>
                                Resultados</span> <i class="fa-solid fa-chevron-right"></i></a>
                    </div>
                </section>

                <section class="promo-banner">
                    <div class="promo-content">
                        <h4>Llevá tu torneo al siguiente nivel</h4>
                        <p>Herramientas profesionales para organizadores exigentes.</p>
                        <a href="#" class="btn btn-primary">Saber más</a>
                    </div>
                </section>
            </aside>

        </div>
    </main>

    <?php require_once __DIR__ . '/../include/footer.php'; ?>
</body>
</html>