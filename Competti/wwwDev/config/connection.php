<?php

class Conexion {
    public static function conectar() {
        // Datos por defecto si usas XAMPP
        $host = "localhost";
        $db_name = "prueba";          // El nombre que pusiste en phpMyAdmin
        $username = "root";           // Usuario por defecto en local
        $password = "";               // Contraseña por defecto (vacía en XAMPP)

        try {
            $link = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
            // Configurar PDO para que lance excepciones en caso de error
            $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $link;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}
