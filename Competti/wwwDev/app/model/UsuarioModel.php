<?php

class UsuarioModel {
    private $db;
    public function __construct($conexionPDO) {
        $this->db = $conexionPDO;
    }

    /** Registra un nuevo usuario en la base de datos */
    public function registrar($nombre, $email, $password) {
        // 1. Encriptar la contraseña de forma segura
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // 2. Preparar la consulta SQL para evitar inyecciones SQL
        $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)";
        $stmt = $this->db->prepare($sql);

        // 3. Ejecutar pasando los datos limpios
        return $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':password' => $passwordHash
        ]);
    }

    /** Verifica las credenciales para iniciar sesión */
    public function login($email, $password) {
        // 1. Buscar al usuario por su email
        $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Si el usuario existe, verificar la contraseña encriptada
        if ($usuario && password_verify($password, $usuario['password'])) {
            // Retorna los datos del usuario (excepto el password por seguridad)
            unset($usuario['password']);
            return $usuario; 
        }

        // Si las credenciales están mal, retorna falso
        return false;
    }

    /** Verifica si un email ya está registrado */
    public function existeEmail($email) {
        $sql = "SELECT id FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        
        return $stmt->fetch() ? true : false;
    }
}