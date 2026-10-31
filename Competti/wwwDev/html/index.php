<?php

// Fuerza la muestra de errores en el navegador aunque falle el arranque
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . "/../app/helpers/helpers.php";

session_start();

// Definimos la ruta base hacia las vistas en la carpeta protegida
define('VIEW_PATH', dirname(__DIR__) . '/app/view');

// 1. CORRECCIÓN DE RUTAS INICIALES (Subiendo un nivel desde html/ para entrar a app/)
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../config/connection.php";

require_once __DIR__ . "/../app/model/UsuarioModel.php";
require_once __DIR__ . "/../app/model/TorneoModel.php";
require_once __DIR__ . "/../app/model/EquipoModel.php";
require_once __DIR__ . "/../app/model/NotificacionModel.php";
require_once __DIR__ . "/../app/model/PartidoModel.php";

require_once __DIR__ . "/../app/controller/registerController.php";
require_once __DIR__ . "/../app/controller/loginController.php";
require_once __DIR__ . "/../app/controller/forgotPasswController.php";
require_once __DIR__ . "/../app/controller/resetPasswController.php";
require_once __DIR__ . "/../app/controller/notificacionController.php";
require_once __DIR__ . "/../app/controller/partidoController.php";
require_once __DIR__ . "/../app/controller/torneoController.php";
require_once __DIR__ . "/../app/controller/profileController.php";


// Inicializar Dependencias
$pdo = Conexion::conectar();
$modeloUsuario = new UsuarioModel($pdo);
$modeloTorneo = new TorneoModel($pdo);
$modeloEquipo = new EquipoModel($pdo);
$modeloNotificacion = new NotificacionModel($pdo);
$modeloPartido = new PartidoModel($pdo);

// Instanciar ambos controladores pasándoles el mismo modelo
$registerController = new RegisterController($modeloUsuario);
$loginController = new LoginController($modeloUsuario);
$forgotPasswController = new ForgotPasswController($pdo, $modeloUsuario);
$resetPasswController = new ResetPasswController($pdo);
$profileController = new ProfileController($modeloUsuario);
$torneoController = new TorneoController($modeloTorneo, $modeloNotificacion);
$partidoController = new PartidoController($modeloPartido, $modeloNotificacion);

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

        case 'google_login':
            $loginController->procesarGoogleLogin();
            break;

        case 'logout':
            $_SESSION = array();
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }
            session_destroy();
            header('Location: /');
            exit;

        case 'forgot_password':
            $forgotPasswController->procesarRecuperacion();
            break;

        case 'update_password':
            $resetPasswController->actualizarPassword();
            break;

        case 'upload_avatar':
            include __DIR__ . '/../app/controller/UpdateProfilePic.php';
            break;

        case 'update_profile_data':
            $profileController->actualizarDatos();
            break;

        case 'create_tournament':
            $torneoController->crear();
            break;

        case 'update_tournament':
            $torneoController->actualizar();
            break;

        // --- AÑADE ESTOS DOS CASOS NUEVOS AQUÍ ---
        case 'join_tournament':
            $partidoController->unirseATorneo();
            break;

        case 'admin_create_team':
            $partidoController->registrarEquipo(); // Despierta el guardado de escuadras
            break;

        case 'salir_equipo': 
            $partidoController->salirEquipo(); 
            break;

        case 'eliminar_equipo': 
            $partidoController->eliminarEquipoOrganizador(); 
            break;

        default:
            break;
    }
}

$view = isset($_GET['view']) ? $_GET['view'] : 'home';

// --- PROTECCIÓN PREVENTIVA DE VISTAS (Antes de renderizar HTML) ---
if ($view === 'torneos/crear' || $view === 'users/profile') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: /index.php?view=auth/login');
        exit;
    }
}

// --- ENRUTADOR DE VISTAS (RENDERIZADO) ---
if ($view === 'home') {
    $viewFile = VIEW_PATH . '/home/home.php';
} else {
    $viewFile = VIEW_PATH . '/' . $view . '.php';
}

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

// Verificar si el archivo existe antes de incluirlo de forma segura
if (!file_exists($viewFile)) {
    http_response_code(404);
    echo "<h1>404 Page Not Found</h1>";
    echo "<p>La ruta no existe: " . htmlspecialchars($viewFile) . "</p>";
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="img/LogoPagina.png">
    <title>Competti - Pensalos. Crealos. Disfrutalos.</title>
    <script src="https://kit.fontawesome.com/3bd97052d9.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/hamburgers/1.2.1/hamburgers.min.css">

    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/navigation.css">
    <link rel="stylesheet" href="/css/footer.css">

</head>

<body>
    <?php
    // Definimos las vistas que NO deben llevar menú ni pie de página
    $vistasSinPlantilla = [
        'auth/login',
        'auth/register',
        'auth/forgotPassw',
        'auth/resetPassw'
    ];

    // Solo se incluye el menú de navegación si la vista actual no está en la lista de exclusión
    if (!in_array($view, $vistasSinPlantilla)) {
        require_once VIEW_PATH . '/include/nav.php';
    }
    ?>

    <?php
    // Se incluye el contenido específico de la página solicitada de forma normal
    require $viewFile;
    ?>

    <?php
    // Solo se incluye el pie de página si la vista actual no está en la lista de exclusión
    if (!in_array($view, $vistasSinPlantilla)) {
        require_once VIEW_PATH . '/include/footer.php';
    }
    ?>
</body>


</html>