<?php

require_once __DIR__ . '/../../config.php';

session_start();

// Solo aceptar peticiones POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . BASE_URL . "/app/view/auth/login.php");
    exit();
}

// Obtener los datos del formulario
$email = trim($_POST["email"] ?? "");
$telefono = trim($_POST["telefono"] ?? "");

// Validar que no estén vacíos
if (empty($email) || empty($telefono)) {
    $_SESSION["error"] = "Todos los campos son obligatorios.";
    header("Location: " . BASE_URL . "/app/view/auth/login.php");
    exit();
}
