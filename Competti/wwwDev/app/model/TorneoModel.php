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
     * Lista torneos aplicando los filtros opcionales del buscador.
     * $disciplina = slug (futbol, valorant...), $estado = inscripciones | en_curso | finalizado
     */
    public function buscar($q = '', $disciplina = '', $estado = '')
    {
        $sql = "SELECT t.*,
                       d.slug   AS disciplina_slug,
                       d.nombre AS disciplina_nombre,
                       d.icono  AS disciplina_icono,
                       d.imagen AS disciplina_imagen
                FROM torneos t
                LEFT JOIN disciplinas d ON d.id = t.disciplina_id
                WHERE 1 = 1";
        $params = [];

        if ($q !== '') {
            $sql .= " AND t.nombre LIKE :q";
            $params[':q'] = '%' . addcslashes($q, '%_\\') . '%';
        }
        if ($disciplina !== '') {
            $sql .= " AND d.slug = :disciplina";
            $params[':disciplina'] = $disciplina;
        }
        if ($estado !== '' && in_array($estado, self::ESTADOS, true)) {
            $sql .= " AND t.estado = :estado";
            $params[':estado'] = $estado;
        }

        $sql .= " ORDER BY FIELD(t.estado, 'en_curso', 'inscripciones', 'finalizado'),
                           (t.fecha_inicio IS NULL), t.fecha_inicio ASC, t.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Disciplinas con la cantidad de torneos que no han finalizado. */
    public function disciplinasConActivos()
    {
        $sql = "SELECT d.*,
                       (SELECT COUNT(*) FROM torneos t
                         WHERE t.disciplina_id = d.id AND t.estado <> 'finalizado') AS activos
                FROM disciplinas d
                ORDER BY d.orden, d.id";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Lista simple (slug + nombre) para selects y sugerencias. */
    public function disciplinas()
    {
        return $this->db->query("SELECT slug, nombre FROM disciplinas ORDER BY orden, id")
                        ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Intenta asociar el texto libre del formulario ("Fútbol 7", "FIFA 24")
     * con una disciplina usando sus palabras clave. Devuelve el id o null.
     */
    public function resolverDisciplina($texto)
    {
        $normal = normalizar_texto($texto);
        if ($normal === '') {
            return null;
        }
        $palabras = preg_split('/[^a-z0-9]+/', $normal, -1, PREG_SPLIT_NO_EMPTY);

        $filas = $this->db->query("SELECT id, palabras_clave FROM disciplinas ORDER BY orden, id")
                          ->fetchAll(PDO::FETCH_ASSOC);

        foreach ($filas as $fila) {
            foreach (explode(',', $fila['palabras_clave']) as $clave) {
                $clave = trim($clave);
                if ($clave === '') {
                    continue;
                }
                if (in_array($clave, $palabras, true)) {
                    return (int) $fila['id'];
                }
                // Palabras largas: coincide aunque venga pegada ("futbol11")
                if (strlen($clave) >= 5 && strpos($normal, $clave) !== false) {
                    return (int) $fila['id'];
                }
            }
        }
        return null;
    }

    /** Inserta un torneo ya validado y devuelve su id. */
    public function crear(array $d)
    {
        $sql = "INSERT INTO torneos
                    (organizador_id, nombre, categoria, juego, disciplina_id, descripcion, formato,
                     pts_victoria, pts_empate, pts_derrota, tercer_puesto, modalidad, cupos,
                     fecha_inicio, streaming_url, premio1, premio2, estado)
                VALUES
                    (:organizador_id, :nombre, :categoria, :juego, :disciplina_id, :descripcion, :formato,
                     :pts_victoria, :pts_empate, :pts_derrota, :tercer_puesto, :modalidad, :cupos,
                     :fecha_inicio, :streaming_url, :premio1, :premio2, 'inscripciones')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':organizador_id' => $d['organizador_id'],
            ':nombre'         => $d['nombre'],
            ':categoria'      => $d['categoria'],
            ':juego'          => $d['juego'],
            ':disciplina_id'  => $d['disciplina_id'],
            ':descripcion'    => $d['descripcion'],
            ':formato'        => $d['formato'],
            ':pts_victoria'   => $d['pts_victoria'],
            ':pts_empate'     => $d['pts_empate'],
            ':pts_derrota'    => $d['pts_derrota'],
            ':tercer_puesto'  => $d['tercer_puesto'],
            ':modalidad'      => $d['modalidad'],
            ':cupos'          => $d['cupos'],
            ':fecha_inicio'   => $d['fecha_inicio'],
            ':streaming_url'  => $d['streaming_url'],
            ':premio1'        => $d['premio1'],
            ':premio2'        => $d['premio2'],
        ]);
        return (int) $this->db->lastInsertId();
    }
}
