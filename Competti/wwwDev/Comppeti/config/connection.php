<?php

class Conexion {
    public static function conectar() {
        $host = getenv('DB_HOST') ?: 'localhost';
        $db_name = getenv('DB_NAME') ?: 'CompettiDB';
        $username = getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASSWORD') ?: '';

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
