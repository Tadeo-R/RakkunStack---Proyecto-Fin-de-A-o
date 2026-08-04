<?php

require_once __DIR__ . '/../../config/config.php';

session_start();

// Solo aceptar peticiones POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . BASE_URL . "/app/view/auth/login.php");
    exit();
}

// Obtener los datos del formulario
$email = trim($_POST["email"] ?? "");

// Validar que no estén vacíos
if (empty($email)) {
    $_SESSION["error"] = "El correo electrónico es obligatorio.";
    header("Location: " . BASE_URL . "/app/view/auth/login.php");
    exit();
}
