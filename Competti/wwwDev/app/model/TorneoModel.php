<?php

class TorneoModel
{
    const CATEGORIAS  = ['deporte', 'esports', 'mentales'];
    const FORMATOS    = ['liga', 'eliminacion', 'suizo', 'grupos_playoffs'];
    const MODALIDADES = ['individual', 'equipos'];
    const ESTADOS     = ['inscripciones', 'en_curso', 'finalizado'];

    private $db;

    public function __construct($conexionPDO)
    {
        $this->db = $conexionPDO;
    }

    /**
     * Lista torneos aplicando los filtros opcionales del buscador avanzado.
     * Sincronizado letra por letra con las nuevas columnas y tablas de la Base de Datos.
     */
    public function buscar($q = '', $disciplina = '', $estado = '')
    {
        $sql = "SELECT * FROM TORNEO WHERE 1 = 1";
        $params = [];

        if ($q !== '') {
            $sql .= " AND (nombreTorneo LIKE :q OR juego LIKE :q)";
            $params[':q'] = '%' . addcslashes($q, '%_\\') . '%';
        }
        
        if ($estado !== '' && in_array($estado, self::ESTADOS, true)) {
            $sql .= " AND estado = :estado";
            $params[':estado'] = $estado;
        }

        $sql .= " ORDER BY id_torneo DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Disciplinas (Deportes) activos con la cantidad de torneos que no han terminado. */
    public function disciplinasConActivos()
    {
        $sql = "SELECT d.*,
                       (SELECT COUNT(*) FROM TORNEO t
                         WHERE t.id_deporte = d.id_deporte AND t.estado <> 'finalizado') AS activos
                FROM DEPORTE d";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Lista simple de deportes para renderizar en selects de formularios y barras de filtros. */
    public function disciplinas()
    {
        return $this->db->query("SELECT id_deporte, nombreDeporte AS nombre FROM DEPORTE ORDER BY id_deporte")
                        ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Intenta asociar el texto libre del formulario ("Fútbol 7", "FIFA 24")
     * con un deporte registrado. Si no hay coincidencias exactas, devuelve 1 (Fútbol) de respaldo.
     */
        /**
     * Intenta asociar el texto libre del formulario con un deporte registrado.
     * Si no existe, lo crea automáticamente en la tabla DEPORTE y devuelve su ID válido.
     */
    public function resolverDisciplina($texto)
    {
        $nombreDeporte = trim($texto);
        if ($nombreDeporte === '') {
            $nombreDeporte = 'General / Multideporte';
        }

        $textoNormalizado = mb_strtolower($nombreDeporte);

        // 1. VERIFICACIÓN: Buscar si el deporte ya existe (evitando duplicados)
        $filas = $this->db->query("SELECT id_deporte, nombreDeporte FROM DEPORTE")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($filas as $fila) {
            $nombreBD = mb_strtolower($fila['nombreDeporte']);
            if (strpos($textoNormalizado, $nombreBD) !== false || strpos($nombreBD, $textoNormalizado) !== false) {
                return (int) $fila['id_deporte']; // Retorna el ID existente
            }
        }
        
        // 2. CREACIÓN AUTOMÁTICA: Si no existe el juego (ej. "Maicra"), lo damos de alta en la BD
        $stmtInsertDep = $this->db->prepare("INSERT INTO DEPORTE (nombreDeporte) VALUES (:nombreDeporte)");
        $stmtInsertDep->execute([':nombreDeporte' => $nombreDeporte]);
        
        // Retorna el ID real autogenerado por MySQL
        return (int)$this->db->lastInsertId();
    }

    /** Inserta un torneo asegurando primero todas las claves foráneas (Organizador y Deporte) */
    public function crear(array $d)
    {
        $id_usuario = (int)$d['id_organizador'];

        // 1. Asegurar la relación en la tabla ORGANIZADOR
        $stmtCheck = $this->db->prepare("SELECT id_organizador FROM ORGANIZADOR WHERE id_usuario = :id_usuario LIMIT 1");
        $stmtCheck->execute([':id_usuario' => $id_usuario]);
        $organizador = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($organizador) {
            $id_organizador_real = (int)$organizador['id_organizador'];
        } else {
            $stmtInsertOrg = $this->db->prepare("INSERT INTO ORGANIZADOR (id_usuario) VALUES (:id_usuario)");
            $stmtInsertOrg->execute([':id_usuario' => $id_usuario]);
            $id_organizador_real = (int)$this->db->lastInsertId();
        }

        // 2. RESOLVER DEPORTE DINÁMICAMENTE: Garantiza que el id_deporte exista físicamente
        $id_deporte_real = $this->resolverDisciplina($d['juego']);

        // 3. INSERCIÓN FINAL DEL TORNEO: Guardamos el torneo con claves foráneas verificadas
        $sql = "INSERT INTO TORNEO
                    (nombreTorneo, modalidad, fechaInicio, id_deporte, id_organizador, estado,
                     categoria, juego, descripcion, formato, pts_victoria, pts_empate, pts_derrota, 
                     tercer_puesto, cupos, streaming_url, premio1, premio2)
                VALUES
                    (:nombreTorneo, :modalidad, :fechaInicio, :id_deporte, :id_organizador, 'inscripciones',
                     :categoria, :juego, :descripcion, :formato, :pts_victoria, :pts_empate, :pts_derrota, 
                     :tercer_puesto, :cupos, :streaming_url, :premio1, :premio2)";
                     
        $stmt = $this->db->prepare($sql);
        
        $stmt->execute([
            ':nombreTorneo'   => $d['nombre'],
            ':modalidad'      => $d['modalidad'],
            ':fechaInicio'    => !empty($d['fecha_inicio']) ? $d['fecha_inicio'] : null,
            ':id_deporte'     => $id_deporte_real, // Mapeamos el ID real asegurado
            ':id_organizador' => $id_organizador_real,
            ':categoria'      => $d['categoria'],
            ':juego'          => $d['juego'],
            ':descripcion'    => $d['descripcion'],
            ':formato'        => $d['formato'],
            ':pts_victoria'   => (int)$d['pts_victoria'],
            ':pts_empate'     => (int)$d['pts_empate'],
            ':pts_derrota'    => (int)$d['pts_derrota'],
            ':tercer_puesto'  => (int)$d['tercer_puesto'],
            ':cupos'          => $d['cupos'],
            ':streaming_url'  => $d['streaming_url'],
            ':premio1'        => $d['premio1'],
            ':premio2'        => $d['premio2']
        ]);
        
        return (int)$this->db->lastInsertId();
    }

    /**
     * Busca y lista los torneos de un organizador específico para renderizar en su perfil
     */
    public function listarPorOrganizador($id_usuario)
    {
        // Consulta híbrida de rescate: busca si coincide con su id_usuario de forma directa 
        // o a través del puente de la tabla ORGANIZADOR. Así no se pierde ningún torneo de prueba.
        $sql = "SELECT * FROM TORNEO 
                WHERE id_organizador = :id_user 
                OR id_organizador IN (SELECT id_organizador FROM ORGANIZADOR WHERE id_usuario = :id_user2)
                ORDER BY id_torneo DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_user'   => (int)$id_usuario,
            ':id_user2'  => (int)$id_usuario
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Actualiza los parámetros oficiales de un torneo existente */
    public function actualizarTorneo($id_torneo, $datos)
    {
        try {
            $sql = "UPDATE TORNEO 
                    SET nombreTorneo = :nombre,
                        modalidad    = :modalidad,
                        juego        = :juego,
                        formato      = :formato,
                        cupos        = :cupos,
                        fechaInicio  = :fechaInicio,
                        estado       = :estado
                    WHERE id_torneo  = :id_torneo";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nombre'      => trim($datos['nombreTorneo']),
                ':modalidad'   => $datos['modalidad'],
                ':juego'       => trim($datos['juego']),
                ':formato'     => $datos['formato'],
                ':cupos'       => (int)$datos['cupos'],
                ':fechaInicio' => $datos['fechaInicio'],
                ':estado'      => $datos['estado'],
                ':id_torneo'   => (int)$id_torneo
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
}