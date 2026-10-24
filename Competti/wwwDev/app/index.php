<?php

session_start();

$appDir = __DIR__ . DIRECTORY_SEPARATOR;
$rootDir = dirname(__DIR__) . DIRECTORY_SEPARATOR;

// Cargar configuraciones globales
require_once $rootDir . 'config/config.php';
require_once $rootDir . 'config/connection.php';

// Cargar Modelos y Controladores
require_once $appDir . 'model/UsuarioModel.php';
require_once $appDir . 'controller/registerController.php';
require_once $appDir . 'controller/loginController.php';


// Instanciar ambos controladores pasándoles el mismo modelo
$registerController = new RegisterController($modeloUsuario);
$loginController = new LoginController($modeloUsuario);

$action = $_GET['action'] ?? 'registro_vista';

switch ($action) {
    case 'register':
        $registerController->procesarRegistro();
        break;

    case 'login':
        // Si el usuario envía el formulario por POST, se procesa.
        // Si entra directo por URL (GET), cargará visualmente la vista gracias al include del controlador
        $loginController->procesarLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            include $appDir . 'view/auth/login.php';
        }
        break;

    case 'upload_avatar':
        // Como index.php ya cargó 'config/connection.php' e inicializó $pdo,
        // podemos incluir el archivo directamente y tendrá acceso total a la BD
        include $appDir . 'controller/UpdateProfilePic.php';
        break;

    case 'registro_vista':
    default:
        include $appDir . 'view/auth/register.php';
        break;
}
