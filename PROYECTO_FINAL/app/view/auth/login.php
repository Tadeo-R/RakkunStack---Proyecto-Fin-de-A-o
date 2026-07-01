<?php
require_once __DIR__ . '/../../../config/config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/login.css">
    <title>Document</title>
</head>
<body>
<main>
    <section class="card">
        <form action="<?= BASE_URL ?>/app/controller/LoginController.php" method="POST">
            <h2>Iniciar Sesión</h2>
            <input type="text" name="user" placeholder="Nombre" required>
            <input type="text" name="user" placeholder="E-mail" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Iniciar Sesión</button>
            <a href="<?= BASE_URL ?>/app/view/auth/register.php">Registrarse</a>
        </form>
    </section>
</main>
</body>
</html>