<?php

<<<<<<< HEAD
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
=======
session_start();
>>>>>>> b05b45f0b213ff829fee4907509413abf0f5e569

// Destruir todas las variables de la sesión
$_SESSION = array();

// Si se desea destruir la cookie de sesión, también se puede borrar
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión
session_destroy();

// Redirigir a la página de inicio
<<<<<<< HEAD
header("Location: /");
=======
header("Location: ../../../html/index.php");
>>>>>>> b05b45f0b213ff829fee4907509413abf0f5e569
exit;
