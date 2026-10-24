<?php

class EquipoModel
{
    private $db;

    public function __construct($conexionPDO)
    {
        $this->db = $conexionPDO;
    }

    /** Todos los equipos registrados, ordenados alfabéticamente. */
    public function listar()
    {
        return $this->db->query("SELECT id, nombre, deporte, tipo, integrantes FROM equipos ORDER BY nombre")
                        ->fetchAll(PDO::FETCH_ASSOC);
    }
}
