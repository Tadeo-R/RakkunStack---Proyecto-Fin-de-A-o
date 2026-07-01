<?php

require_once __DIR__ . '/../../config.php';

session_start();

// Solo aceptar solicitudes POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . BASE_URL . "/app/view/auth/register.php");
    exit();
}

// Obtener datos del formulario
$usuario = trim($_POST["usuario"] ?? "");
$email = trim($_POST["email"] ?? "");
$telefono = trim($_POST["telefono"] ?? "");
$password = trim($_POST["password"] ?? "");

// Validar campos
if (
    empty($usuario) ||
    empty($email) ||
    empty($telefono) ||
    empty($password)
) {
    $_SESSION["error"] = "Todos los campos son obligatorios.";
    header("Location: " . BASE_URL . "/app/view/auth/register.php");
    exit();
}
