<?php

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

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloPagina) ?></title>
    <script src="https://kit.fontawesome.com/3bd97052d9.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/hamburgers/1.2.1/hamburgers.min.css">
    
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/navigation.css">
    <link rel="stylesheet" href="/css/footer.css">
    
</head>
<body>

    <?= $contenidoVista ?>

</body>
</html>
