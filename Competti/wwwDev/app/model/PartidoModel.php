<?php

class PartidoModel
{
    private $db;

    public function __construct($conexionPDO)
    {
        $this->db = $conexionPDO;
    }

    /** Últimos partidos finalizados (más recientes primero). */
    public function resultados($limite = 50)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM partidos
             WHERE estado = 'finalizado'
             ORDER BY fecha_hora DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':limite', (int) $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Próximos encuentros. Si hay usuario, indica en "recordado" (0/1)
     * si ya tiene activado el recordatorio de cada partido.
     */
    public function proximos($usuarioId = 0, $limite = 50)
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, (r.usuario_id IS NOT NULL) AS recordado
             FROM partidos p
             LEFT JOIN recordatorios r
                    ON r.partido_id = p.id AND r.usuario_id = :usuario
             WHERE p.estado = 'programado' AND p.fecha_hora >= NOW()
             ORDER BY p.fecha_hora ASC
             LIMIT :limite"
        );
        $stmt->bindValue(':usuario', (int) $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':limite', (int) $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtener($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM partidos WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => (int) $id]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ? $fila : null;
    }

    /**
     * Activa o desactiva el recordatorio.
     * Devuelve true si quedó activado, false si se quitó.
     */
    public function alternarRecordatorio($usuarioId, $partidoId)
    {
        $stmt = $this->db->prepare(
            "DELETE FROM recordatorios WHERE usuario_id = :u AND partido_id = :p"
        );
        $stmt->execute([':u' => (int) $usuarioId, ':p' => (int) $partidoId]);

        if ($stmt->rowCount() > 0) {
            return false;
        }

        $stmt = $this->db->prepare(
            "INSERT INTO recordatorios (usuario_id, partido_id) VALUES (:u, :p)"
        );
        $stmt->execute([':u' => (int) $usuarioId, ':p' => (int) $partidoId]);
        return true;
    }
}
