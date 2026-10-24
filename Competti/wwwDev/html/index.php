<?php
<<<<<<< HEAD

// Fuerza la muestra de errores en el navegador aunque falle el arranque
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Definimos la ruta base hacia las vistas en la carpeta protegida
define('VIEW_PATH', dirname(__DIR__) . '/app/view');

// 1. CORRECCIÓN DE RUTAS INICIALES (Subiendo un nivel desde html/ para entrar a app/)
require_once __DIR__ . "/../config/config.php"; 
require_once __DIR__ . "/../config/connection.php"; 
require_once __DIR__ . "/../app/helpers/helpers.php";

// Modelos
require_once __DIR__ . "/../app/model/UsuarioModel.php"; 
require_once __DIR__ . "/../app/model/TorneoModel.php";
require_once __DIR__ . "/../app/model/EquipoModel.php";
require_once __DIR__ . "/../app/model/PartidoModel.php";
require_once __DIR__ . "/../app/model/NotificacionModel.php";

// Controladores
require_once __DIR__ . "/../app/controller/registerController.php"; 
require_once __DIR__ . "/../app/controller/loginController.php";
require_once __DIR__ . "/../app/controller/forgotPasswController.php";
require_once __DIR__ . "/../app/controller/resetPasswController.php";
require_once __DIR__ . "/../app/controller/torneoController.php";
require_once __DIR__ . "/../app/controller/partidoController.php";
require_once __DIR__ . "/../app/controller/notificacionController.php";

// Inicializar Dependencias
$pdo = Conexion::conectar();
$modeloUsuario      = new UsuarioModel($pdo);
$modeloTorneo       = new TorneoModel($pdo);
$modeloEquipo       = new EquipoModel($pdo);
$modeloPartido      = new PartidoModel($pdo);
$modeloNotificacion = new NotificacionModel($pdo);

// Instanciar ambos controladores pasándoles el mismo modelo
$registerController = new RegisterController($modeloUsuario); 
$loginController    = new LoginController($modeloUsuario);
$forgotPasswController = new ForgotPasswController($pdo, $modeloUsuario);
$resetPasswController = new ResetPasswController($pdo);
$torneoController       = new TorneoController($modeloTorneo, $modeloNotificacion);
$partidoController      = new PartidoController($modeloPartido, $modeloNotificacion);
$notificacionController = new NotificacionController($modeloNotificacion);

$action = $_GET['action'] ?? null;

if ($action !== null) {
    switch ($action) {
        case 'register':
            $registerController->procesarRegistro(); 
            break;

        case 'login':
            $loginController->procesarLogin();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                include VIEW_PATH . '/auth/login.php';
            }
            break;

        case 'forgot_password':
            $forgotPasswController->procesarRecuperacion();
            break;

        case 'update_password':
            $resetPasswController->actualizarPassword();
            break;
        
        case 'upload_avatar':
            include __DIR__ . '/../app/controller/UpdateProfilePic.php';
            break;

        // --- Torneos ---
        case 'create_tournament':
            $torneoController->crear();
            break;

        // --- Calendario ---
        case 'toggle_reminder':
            $partidoController->alternarRecordatorio();
            break;

        // --- Notificaciones ---
        case 'notif_leer':
            $notificacionController->marcarLeida();
            break;

        case 'notif_leer_todas':
            $notificacionController->marcarTodasLeidas();
            break;

        case 'notif_eliminar':
            $notificacionController->eliminar();
            break;

        case 'notif_limpiar':
            $notificacionController->limpiarLeidas();
            break;

        default:
            break;
    }
}

$view = isset($_GET['view']) ? $_GET['view'] : 'home';

// Solo se aceptan nombres como "torneos" o "auth/login" (sin ".." ni caracteres raros),
// y nunca los fragmentos internos de la carpeta include/.
if (!preg_match('#^[A-Za-z0-9_-]+(/[A-Za-z0-9_-]+)*$#', $view) || strpos($view, 'include/') === 0) {
    http_response_code(404);
    echo "<h1>404 Page Not Found</h1>";
    exit;
}

// Cerrar sesión necesita enviar cabeceras HTTP, así que se resuelve antes de imprimir HTML
if ($view === 'auth/logout') {
    require VIEW_PATH . '/auth/logout.php';
    exit;
}

// --- ENRUTADOR DE VISTAS (RENDERIZADO) ---
// Rutas amigables: ?view=torneos  ->  app/view/torneos/torneos.php
// Cualquier otra vista se busca como app/view/<view>.php (auth/login, users/profile, ...)
$rutas = [
    'home'           => 'home/home.php',
    'torneos'        => 'torneos/torneos.php',
    'crear-torneo'   => 'torneos/crear.php',
    'search'         => 'torneos/search.php',
    'disciplinas'    => 'torneos/disciplinas.php',
    'equipos'        => 'equipos/equipos.php',
    'resultados'     => 'partidos/resultados.php',
    'calendario'     => 'partidos/calendario.php',
    'notificaciones' => 'users/notificaciones.php',
    'quienesSomos'   => 'pages/quienesSomos.php',
];

$vistaRelativa = isset($rutas[$view]) ? $rutas[$view] : $view . '.php';
$viewFile = VIEW_PATH . '/' . $vistaRelativa;

if ($view === 'auth/forgotPassw') {
    $viewFile = VIEW_PATH . '/auth/forgotPassw.php';
}

if ($view === 'auth/resetPassw') {
    // Si el token no viene en la URL, bloqueamos el acceso directo
    if (!isset($_GET['token']) || empty($_GET['token'])) {
        header('Location: /index.php?view=auth/login');
        exit;
    }
    $viewFile = VIEW_PATH . '/auth/resetPassw.php';
}

// Vistas que solo puede ver un usuario con sesión iniciada (se decide antes de imprimir HTML)
$vistasProtegidas = ['torneos/crear.php', 'users/notificaciones.php', 'users/profile.php'];
if (in_array($vistaRelativa, $vistasProtegidas, true) && !usuario_logueado()) {
    header('Location: /index.php?view=auth/login');
    exit;
}

// Verificar si el archivo existe antes de incluirlo de forma segura
if (!file_exists($viewFile)) {
    http_response_code(404);
    echo "<h1>404 Page Not Found</h1>";
    echo "<p>La ruta no existe: " . htmlspecialchars($view) . "</p>";
    exit;
}

// La vista se ejecuta primero (en un búfer) para que pueda definir $pageTitle
// y hacer redirecciones antes de que salga el HTML de la plantilla.
ob_start();
require $viewFile;
$contenidoVista = ob_get_clean();

$tituloPagina = isset($pageTitle)
    ? $pageTitle . ' - COMPETTI'
    : 'Competti - Pensalos. Crealos. Disfrutalos.';

=======
require_once __DIR__ . '/../config/config.php';
session_start();
>>>>>>> b05b45f0b213ff829fee4907509413abf0f5e569
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title><?= htmlspecialchars($tituloPagina) ?></title>
    <script src="https://kit.fontawesome.com/3bd97052d9.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/hamburgers/1.2.1/hamburgers.min.css">
    
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/navigation.css">
    <link rel="stylesheet" href="/css/footer.css">
    
=======
    <title>Competti - Pensalos. Crealos. Disfrutalos.</title>
    <script src="https://kit.fontawesome.com/3bd97052d9.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/hamburgers/1.2.1/hamburgers.min.css">
    <link rel="stylesheet" href="<?php BASE_URL; ?>css/global.css">
    <link rel="stylesheet" href="<?php BASE_URL; ?>css/navigation.css">
    <link rel="stylesheet" href="<?php BASE_URL; ?>css/home.css">
    <link rel="stylesheet" href="<?php BASE_URL; ?>css/footer.css">
>>>>>>> b05b45f0b213ff829fee4907509413abf0f5e569
</head>
<body>

<<<<<<< HEAD
    <?= $contenidoVista ?>
=======
    <?php require_once ROOT_PATH . '/app/view/include/nav.php'; ?>

    <main>

        <section class="hero">
            <div class="hero-info">
                <h1 class="animate__animated animate__fadeInDown">Pensalos<br>Crealos<br><span
                        class="highlight">Disfrutalos</span></h1>
                <p>La plataforma integral para la gesti&oacute;n de torneos deportivos, mentales y electr&oacute;nicos.
                </p>
                <div class="hero-buttons">
                    <a href="torneos.html" class="btn-primary"><i class="fa-solid fa-trophy"></i> Explorar Torneos</a>
                    <a href="crearTorneo.html" class="btn-secondary"><i class="fa-solid fa-plus"></i> Crear Torneo</a>
                </div>
            </div>
            <div class="hero-gallery">
                <div class="gallery-item" style="background-image: url('<?php echo BASE_URL; ?>html/img/futbol.png');"></div>
                <div class="gallery-item" style="background-image: url('<?php echo BASE_URL; ?>html/img/joystickPS5.png');">
                </div>
                <div class="gallery-item" style="background-image: url('<?php echo BASE_URL; ?>html/img/ajedrez.png');">
                </div>
                <div class="gallery-item" style="background-image: url('<?php echo BASE_URL; ?>html/img/pingpong.png');">
                </div>
                <div class="gallery-item" style="background-image: url('<?php echo BASE_URL; ?>html/img/valorant.png');">
                </div>
            </div>
        </section>

        <section class="disciplines-section">
            <div class="section-header">
                <h2>Disciplinas</h2>
                <a href="disciplinas.html" class="view-all">Ver todas las disciplinas &rarr;</a>
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
                <a href="search.html">
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
                        <a href="calendario.html" class="view-all">Ver calendario &rarr;</a>
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
                        <a href="torneos.html" class="view-all">Ver todos los torneos &rarr;</a>
                    </div>
                    <div class="tournaments-grid">
                        <div class="tournament-card">
                            <div class="card-img" style="background-image: url('img/pibble.jpg');">
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
                            <div class="card-img" style="background-image: url('img/joystickPS5.png');">
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
                        <a href="calendario.html" class="view-all">Ver calendario &rarr;</a>
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

    <?php require_once ROOT_PATH . '/app/view/include/footer.php'; ?>
>>>>>>> b05b45f0b213ff829fee4907509413abf0f5e569

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
