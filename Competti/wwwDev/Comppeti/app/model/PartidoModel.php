<?php

class PartidoModel
{
    private $db;

    public function __construct($conexionPDO)
    {
        $this->db = $conexionPDO;
    }

    /** Permite al Organizador fundar una escuadra oficial en la tabla EQUIPO */
    /** Permite al Organizador fundar una escuadra oficial en la tabla EQUIPO controlando duplicados */
    public function crearEquipo($nombreEquipo, $id_usuario_creador)
    {
        // 1. Buscamos si ya existe el id_organizador en la tabla relacional
        $stmt = $this->db->prepare("SELECT id_organizador FROM ORGANIZADOR WHERE id_usuario = :id LIMIT 1");
        $stmt->execute([':id' => $id_usuario_creador]);
        $id_organizador = $stmt->fetchColumn();

        if (!$id_organizador) {
            $stmtInsertOrg = $this->db->prepare("INSERT INTO ORGANIZADOR (id_usuario) VALUES (:id)");
            $stmtInsertOrg->execute([':id' => $id_usuario_creador]);
            $id_organizador = $this->db->lastInsertId();
        }

        // 2. VALIDACIÓN: Comprobar si ya existe un equipo activo con exactamente el mismo nombre para este organizador
        $stmtCheck = $this->db->prepare("SELECT COUNT(*) FROM EQUIPO 
                                       WHERE LOWER(nombreEquipo) = LOWER(:nombre) 
                                         AND id_organizador_creador = :id_org 
                                         AND estado = 'activo'");
        $stmtCheck->execute([
            ':nombre' => trim($nombreEquipo),
            ':id_org' => $id_organizador
        ]);

        if ($stmtCheck->fetchColumn() > 0) {
            // Ya existe un equipo con este nombre, retornamos false para detener la creación
            return false;
        }

        // 3. Inserción normal si el nombre está disponible
        $sql = "INSERT INTO EQUIPO (nombreEquipo, id_organizador_creador, estado) VALUES (:nombre, :id_org, 'activo')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => trim($nombreEquipo),
            ':id_org' => $id_organizador
        ]);
    }

    /** Permite a un participante salirse voluntariamente de un equipo */
    public function salirDeEquipo($id_usuario, $id_equipo)
    {
        $stmtPart = $this->db->prepare("SELECT id_participante FROM PARTICIPANTE WHERE id_usuario = :id LIMIT 1");
        $stmtPart->execute([':id' => $id_usuario]);
        $id_participante = $stmtPart->fetchColumn();

        if (!$id_participante)
            return false;

        $stmtDel = $this->db->prepare("DELETE FROM PERTENECE WHERE id_equipo = :id_eq AND id_participante = :id_part");
        return $stmtDel->execute([
            ':id_eq' => $id_equipo,
            ':id_part' => $id_participante
        ]);
    }

    /** Permite al Organizador eliminar un equipo por completo de la base de datos */
    public function eliminarEquipoCompleto($id_equipo)
    {
        try {
            $this->db->beginTransaction();

            // 1. Limpiar inscripciones en torneos
            $stmt1 = $this->db->prepare("DELETE FROM INSCRIBEN WHERE id_equipo = :id");
            $stmt1->execute([':id' => $id_equipo]);

            // 2. Limpiar membresías de integrantes
            $stmt2 = $this->db->prepare("DELETE FROM PERTENECE WHERE id_equipo = :id");
            $stmt2->execute([':id' => $id_equipo]);

            // 3. Borrar el equipo definitivo de la tabla EQUIPO
            $stmt3 = $this->db->prepare("DELETE FROM EQUIPO WHERE id_equipo = :id");
            $stmt3->execute([':id' => $id_equipo]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /** Procesa la inscripción de un competidor/equipo controlando estrictamente los cupos y relaciones */
    public function inscribirEnTorneo($id_usuario, $id_torneo, $id_equipo = null)
    {
        // 1. MANEJO POR MODALIDAD INDIVIDUAL (Si no viene un equipo seleccionado)
        if ($id_equipo === null) {
            $stmtGetOrg = $this->db->prepare("SELECT id_organizador FROM TORNEO WHERE id_torneo = :id LIMIT 1");
            $stmtGetOrg->execute([':id' => $id_torneo]);
            $id_organizador_torneo = $stmtGetOrg->fetchColumn() ?: 1;

            // Buscamos el nombre real del usuario para la visualización de la tabla
            $stmtU = $this->db->prepare("SELECT nombre FROM USUARIO WHERE id_usuario = :id LIMIT 1");
            $stmtU->execute([':id' => $id_usuario]);
            $nombreReal = $stmtU->fetchColumn() ?: 'Competidor';

            $sqlEquipo = "INSERT INTO EQUIPO (nombreEquipo, id_organizador_creador, estado) VALUES (:nombre, :id_user, 'activo')";
            $stmtEq = $this->db->prepare($sqlEquipo);
            $stmtEq->execute([
                ':nombre' => 'Inscripción - ' . $nombreReal,
                ':id_user' => (int) $id_organizador_torneo
            ]);
            $id_equipo = (int) $this->db->lastInsertId();
        }

        if ($id_equipo !== null) {
            $stmtMax = $this->db->prepare("SELECT modalidad, COALESCE(max_integrantes_equipo, 5) as max_ int FROM TORNEO WHERE id_torneo = :id LIMIT 1");
            // Nota: Asegúrate de adaptar la columna si la nombraste distinto en tu BD, o usa un límite por defecto de 5
        }

        // 2. ASEGURAR QUE EL USUARIO EXISTA EN LA TABLA PARTICIPANTE
        $stmtPart = $this->db->prepare("SELECT id_participante FROM PARTICIPANTE WHERE id_usuario = :id_usuario LIMIT 1");
        $stmtPart->execute([':id_usuario' => $id_usuario]);
        $id_participante = $stmtPart->fetchColumn();

        if (!$id_participante) {
            $stmtInsPart = $this->db->prepare("INSERT INTO PARTICIPANTE (id_usuario) VALUES (:id_usuario)");
            $stmtInsPart->execute([':id_usuario' => $id_usuario]);
            $id_participante = $this->db->lastInsertId();
        }

        // VALIDACIÓN: Si es un equipo existente, comprobar que no exceda el límite de integrantes del torneo
        if ($id_equipo !== null) {
            $stmtInfoTorneo = $this->db->prepare("SELECT COALESCE(max_integrantes_equipo, 5) as max_integrantes FROM TORNEO WHERE id_torneo = :id");
            $stmtInfoTorneo->execute([':id' => $id_torneo]);
            $limiteIntegrantes = (int) $stmtInfoTorneo->fetchColumn();

            $stmtConteoMiembros = $this->db->prepare("SELECT COUNT(*) FROM PERTENECE WHERE id_equipo = :id_equipo");
            $stmtConteoMiembros->execute([':id_equipo' => $id_equipo]);
            $miembrosActuales = (int) $stmtConteoMiembros->fetchColumn();

            if ($miembrosActuales >= $limiteIntegrantes) {
                // El equipo está lleno de integrantes
                return false;
            }
        }

        // 3. ASEGURAR QUE EL PARTICIPANTE PERTENEZCA A ESTE EQUIPO (Tabla PERTENECE)
        $stmtPer = $this->db->prepare("SELECT COUNT(*) FROM PERTENECE WHERE id_equipo = :id_equipo AND id_participante = :id_participante");
        $stmtPer->execute([
            ':id_equipo' => $id_equipo,
            ':id_participante' => $id_participante
        ]);

        if ($stmtPer->fetchColumn() == 0) {
            $stmtInsPer = $this->db->prepare("INSERT INTO PERTENECE (id_equipo, id_participante, fechaIngreso, esCapitan) VALUES (:id_equipo, :id_participante, CURDATE(), 1)");
            $stmtInsPer->execute([
                ':id_equipo' => $id_equipo,
                ':id_participante' => $id_participante
            ]);
        }

        // 4. VERIFICAR SI EL EQUIPO YA ESTÁ INSCRITO EN ESTE TORNEO (Evita duplicados en INSCRIBEN)
        $stmtCheck = $this->db->prepare("SELECT COUNT(*) FROM INSCRIBEN WHERE id_torneo = :id_torneo AND id_equipo = :id_equipo");
        $stmtCheck->execute([
            ':id_torneo' => $id_torneo,
            ':id_equipo' => $id_equipo
        ]);
        if ($stmtCheck->fetchColumn() > 0) {
            return false; // Ya está inscrito en este torneo
        }

        // 5. CONTROL DE CUPOS MÁXIMOS DEL TORNEO
        $stmtT = $this->db->prepare("SELECT cupos, modalidad FROM TORNEO WHERE id_torneo = :id LIMIT 1");
        $stmtT->execute([':id' => $id_torneo]);
        $torneoInfo = $stmtT->fetch(PDO::FETCH_ASSOC);

        if (!$torneoInfo)
            return false;

        $stmtCount = $this->db->prepare("SELECT COUNT(*) FROM INSCRIBEN WHERE id_torneo = :id");
        $stmtCount->execute([':id' => $id_torneo]);
        $actualesInscritos = (int) $stmtCount->fetchColumn();

        if (!empty($torneoInfo['cupos']) && $actualesInscritos >= (int) $torneoInfo['cupos']) {
            return false;
        }

        // 6. INSERCIÓN OFICIAL EN LA RELACIÓN INSCRIBEN
        try {
            $sql = "INSERT INTO INSCRIBEN (id_equipo, id_torneo, fechaInscripcion, estadoInscripcion) 
                    VALUES (:id_equipo, :id_torneo, CURDATE(), 'aprobado')";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id_equipo' => $id_equipo,
                ':id_torneo' => $id_torneo
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /** Obtiene los próximos encuentros y partidos agendados */
    public function proximos($usuarioId = 0)
    {
        // Consultamos los enfrentamientos cruzando con los nombres reales de los equipos y torneos
        // Nota: Si aún no tienes un campo de fecha o recordatorio en ENFRENTAMIENTO, 
        // ajustamos los campos según tu esquema real.
        $sql = "
            SELECT 
                ef.id_enfrentamiento AS id,
                COALESCE(ef.fecha, NOW()) AS fecha_hora,
                e1.nombreEquipo AS equipo_local,
                e2.nombreEquipo AS equipo_visitante,
                t.nombreTorneo AS etiqueta,
                0 AS recordado
            FROM ENFRENTAMIENTO ef
            LEFT JOIN EQUIPO e1 ON ef.id_equipo_local = e1.id_equipo
            LEFT JOIN EQUIPO e2 ON ef.id_equipo_visitante = e2.id_equipo
            LEFT JOIN TORNEO t ON ef.id_torneo = t.id_torneo
            ORDER BY ef.id_enfrentamiento ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerResultados()
    {
        $sql = "SELECT p.*, e1.nombreEquipo AS equipo_local, e2.nombreEquipo AS equipo_visitante 
            FROM PARTIDO p
            INNER JOIN EQUIPO e1 ON p.id_equipo_local = e1.id_equipo
            INNER JOIN EQUIPO e2 ON p.id_equipo_visitante = e2.id_equipo
            ORDER BY p.id_partido DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
