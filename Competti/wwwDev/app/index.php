<?php

session_start();

<<<<<<< HEAD
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
=======
// Cargar configuraciones globales y base de datos
require_once "../config/config.php"; 
require_once "../config/connection.php"; 

// Cargar Modelos y Controladores
require_once "model/UsuarioModel.php"; 
require_once "controller/registerController.php"; 
require_once "controller/loginController.php";

// Inicializar Dependencias
$pdo = Conexion::conectar();
$modeloUsuario = new UsuarioModel($pdo);

// Instanciar ambos controladores pasándoles el mismo modelo
$registerController = new RegisterController($modeloUsuario); 
$loginController    = new LoginController($modeloUsuario);
>>>>>>> b05b45f0b213ff829fee4907509413abf0f5e569

$action = $_GET['action'] ?? 'registro_vista';

switch ($action) {
    case 'register':
<<<<<<< HEAD
        $registerController->procesarRegistro();
        break;

    case 'login':
        // Si el usuario envía el formulario por POST, se procesa.
        // Si entra directo por URL (GET), cargará visualmente la vista gracias al include del controlador
        $loginController->procesarLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            include $appDir . 'view/auth/login.php';
=======
        $registerController->procesarRegistro(); 
        break;

    case 'login':
        // Si el usuario envía el formulario por POST, se procesa. 
        // Si entra directo por URL (GET), cargará visualmente la vista gracias al include del controlador
        $loginController->procesarLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            include 'view/auth/login.php';
>>>>>>> b05b45f0b213ff829fee4907509413abf0f5e569
        }
        break;

    case 'upload_avatar':
        // Como index.php ya cargó 'config/connection.php' e inicializó $pdo,
        // podemos incluir el archivo directamente y tendrá acceso total a la BD
<<<<<<< HEAD
        include $appDir . 'controller/UpdateProfilePic.php';
=======
        include 'controller/UpdateProfilePic.php';
>>>>>>> b05b45f0b213ff829fee4907509413abf0f5e569
        break;

    case 'registro_vista':
    default:
<<<<<<< HEAD
        include $appDir . 'view/auth/register.php';
=======
        include 'view/auth/register.php';
>>>>>>> b05b45f0b213ff829fee4907509413abf0f5e569
        break;
}
