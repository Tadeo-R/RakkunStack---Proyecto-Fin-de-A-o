<?php

session_start();

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
            include 'view/auth/login.php';
        }
        break;

    case 'registro_vista':
    default:
        include 'view/auth/register.php';
        break;
}
