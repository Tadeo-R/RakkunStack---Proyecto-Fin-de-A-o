<?php

class NotificacionModel
{
    // Tipos que agrupa cada pestaña del panel
    const TIPOS_TORNEOS = ['torneo', 'partido', 'alerta'];

    private $db;

    public function __construct($conexionPDO)
    {
        $this->db = $conexionPDO;
    }

    /**
     * Notificaciones del usuario.
     * $filtro: todas | sin_leer | torneos | sistema   $q: texto a buscar
     */
    public function listar($usuarioId, $filtro = 'todas', $q = '')
    {
        $sql = "SELECT * FROM notificaciones WHERE usuario_id = :usuario";
        $params = [':usuario' => (int) $usuarioId];

        if ($filtro === 'sin_leer') {
            $sql .= " AND leida = 0";
        } elseif ($filtro === 'torneos') {
            $sql .= " AND tipo IN ('torneo','partido','alerta')";
        } elseif ($filtro === 'sistema') {
            $sql .= " AND tipo = 'sistema'";
        }

        if ($q !== '') {
            $sql .= " AND (titulo LIKE :q1 OR mensaje LIKE :q2)";
            $like = '%' . addcslashes($q, '%_\\') . '%';
            $params[':q1'] = $like;
            $params[':q2'] = $like;
        }

        $sql .= " ORDER BY creado_en DESC, id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Totales para los contadores de las pestañas. */
    public function contadores($usuarioId)
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS total, COALESCE(SUM(leida = 0), 0) AS sin_leer
             FROM notificaciones WHERE usuario_id = :usuario"
        );
        $stmt->execute([':usuario' => (int) $usuarioId]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'total'    => (int) $fila['total'],
            'sin_leer' => (int) $fila['sin_leer'],
        ];
    }

    public function crear($usuarioId, $tipo, $titulo, $mensaje)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje)
             VALUES (:usuario, :tipo, :titulo, :mensaje)"
        );
        return $stmt->execute([
            ':usuario' => (int) $usuarioId,
            ':tipo'    => $tipo,
            ':titulo'  => $titulo,
            ':mensaje' => $mensaje,
        ]);
    }

    public function marcarLeida($usuarioId, $id)
    {
        $stmt = $this->db->prepare(
            "UPDATE notificaciones SET leida = 1 WHERE id = :id AND usuario_id = :usuario"
        );
        return $stmt->execute([':id' => (int) $id, ':usuario' => (int) $usuarioId]);
    }

    public function marcarTodasLeidas($usuarioId)
    {
        $stmt = $this->db->prepare(
            "UPDATE notificaciones SET leida = 1 WHERE usuario_id = :usuario AND leida = 0"
        );
        return $stmt->execute([':usuario' => (int) $usuarioId]);
    }

    public function eliminar($usuarioId, $id)
    {
        $stmt = $this->db->prepare(
            "DELETE FROM notificaciones WHERE id = :id AND usuario_id = :usuario"
        );
        return $stmt->execute([':id' => (int) $id, ':usuario' => (int) $usuarioId]);
    }

    public function eliminarLeidas($usuarioId)
    {
        $stmt = $this->db->prepare(
            "DELETE FROM notificaciones WHERE usuario_id = :usuario AND leida = 1"
        );
        return $stmt->execute([':usuario' => (int) $usuarioId]);
    }
}
