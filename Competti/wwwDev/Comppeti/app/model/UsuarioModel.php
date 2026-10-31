<?php

class UsuarioModel {
    private $db;
    
    public function __construct($conexionPDO) {
        $this->db = $conexionPDO;
    }

    /** Registra un nuevo usuario en la base de datos */
    public function registrar($nombre, $email, $contrasena) {
        // 1. Encriptar la contraseña de forma segura
        $passwordHash = password_hash($contrasena, PASSWORD_BCRYPT);

        // 2. Preparar la consulta SQL con los nombres exactos de tu nueva tabla
        // CORREGIDO: Se reemplaza de forma definitiva 'apellido' por 'ubicacion'
        $sql = "INSERT INTO USUARIO (nombre, ubicacion, bio, email, contrasena, estado, id_rol) 
                VALUES (:nombre, :ubicacion, :bio, :email, :contrasena, :estado, :id_rol)";
        
        $stmt = $this->db->prepare($sql);

        // 3. Ejecutar pasando los datos limpios coincidentes con la captura de phpMyAdmin
        return $stmt->execute([
            ':nombre'     => $nombre,
            ':ubicacion'  => '', // Inicializa vacío para que el usuario lo edite luego en Ajustes
            ':bio'  => '',
            ':email'      => $email,
            ':contrasena' => $passwordHash, 
            ':estado'     => 'activo',      
            ':id_rol'     => 1              // Asocia por defecto al id_rol = 1 (Participante)
        ]);
    }

    /** Verifica las credenciales para iniciar sesión */
    public function login($email, $password) {
        // Aseguramos el SELECT * para traer tu nuevo campo 'ubicacion', 'bio' y 'fechaRegistro'
        $sql = "SELECT * FROM USUARIO WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar la contraseña cifrada usando la columna 'contrasena'
        if ($usuario && password_verify($password, $usuario['contrasena'])) {
            return $usuario; // Retornamos el array completo con todas las columnas
        }

        return false;
    }

    /** Verifica si un email ya está registrado */
    public function existeEmail($email) {
        // Tabla en MAYÚSCULAS para cumplir con el motor estricto de Linux (LAMPP)
        $sql = "SELECT id_usuario FROM USUARIO WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        
        return $stmt->fetch() ? true : false;
    }

    /** Actualiza los datos de perfil de un usuario */
    public function actualizarPerfil($id_usuario, $nombre, $ubicacion, $biografia, $nueva_password = null) {
        // Si el usuario decidió cambiar la contraseña, la añadimos al SQL cifrada
        if (!empty($nueva_password)) {
            $passwordHash = password_hash($nueva_password, PASSWORD_BCRYPT);
            // CORREGIDO: Se apunta de forma explícita a la columna real 'ubicacion'
            $sql = "UPDATE USUARIO SET nombre = :nombre, ubicacion = :ubicacion, bio = :biografia, contrasena = :contrasena WHERE id_usuario = :id_usuario";
            $params = [
                ':nombre'     => $nombre,
                ':ubicacion'  => $ubicacion, 
                ':biografia'  => $biografia,
                ':contrasena' => $passwordHash,
                ':id_usuario' => $id_usuario
            ];
        } else {
            // Si la dejó en blanco, solo actualizamos los textos informativos
            // CORREGIDO: Se apunta de forma explícita a la columna real 'ubicacion'
            $sql = "UPDATE USUARIO SET nombre = :nombre, ubicacion = :ubicacion, bio = :biografia WHERE id_usuario = :id_usuario";
            $params = [
                ':nombre'     => $nombre,
                ':ubicacion'  => $ubicacion,
                ':biografia'  => $biografia,
                ':id_usuario' => $id_usuario
            ];
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /** Busca un usuario por su dirección de correo electrónico */
    public function buscarPorEmail($email)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM USUARIO WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /** Busca un usuario por su ID único */
    public function buscarPorId($id_usuario)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM USUARIO WHERE id_usuario = :id LIMIT 1");
            $stmt->execute([':id' => (int)$id_usuario]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /** Registra un nuevo usuario mediante autenticación de Google */
    /** Registra un nuevo usuario mediante autenticación de Google */
    public function crearUsuarioGoogle($nombre, $username, $email, $avatar)
    {
        try {
            $passwordDummy = password_hash(uniqid(), PASSWORD_BCRYPT);

            $sql = "INSERT INTO USUARIO (nombre, email, contrasena, avatar, ubicacion, bio, estado, id_rol, fechaRegistro) 
                    VALUES (:nombre, :email, :contrasena, :avatar, :ubicacion, :bio, :estado, :id_rol, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nombre'    => $nombre,
                ':email'     => $email,
                ':contrasena'=> $passwordDummy,
                ':avatar'    => $avatar,
                ':ubicacion' => '',
                ':bio'       => '',
                ':estado'    => 'activo',
                ':id_rol'    => 1
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            // ESTO TE MOSTRARÁ EL ERROR REAL DE SQL EN PANTALLA
            die("Error SQL en Google Register: " . $e->getMessage());
        }
    }
}