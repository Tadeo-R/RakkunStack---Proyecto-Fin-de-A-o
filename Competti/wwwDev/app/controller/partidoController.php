<?php

class PartidoController
{
    private $partidos;
    private $notificaciones;

    public function __construct($partidoModel, $notificacionModel)
    {
        $this->partidos = $partidoModel;
        $this->notificaciones = $notificacionModel;
    }

    /** Botón "Recordar" del calendario (POST ?action=toggle_reminder). */
    public function alternarRecordatorio()
    {
        $volver = url('calendario');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir($volver);
        }
        if (!usuario_logueado()) {
            redirigir(url('auth/login'));
        }
        if (!csrf_valido()) {
            flash_set('error', 'La sesión del formulario expiró. Volvé a intentarlo.');
            redirigir($volver);
        }

        $partido = $this->partidos->obtener($_POST['partido_id'] ?? 0);
        if (!$partido || $partido['estado'] !== 'programado') {
            flash_set('error', 'Ese encuentro ya no está disponible.');
            redirigir($volver);
        }

        try {
            $activo = $this->partidos->alternarRecordatorio($_SESSION['usuario_id'], $partido['id']);

            if ($activo) {
                $this->notificaciones->crear(
                    $_SESSION['usuario_id'],
                    'partido',
                    'Recordatorio guardado',
                    'Guardaste un recordatorio para **' . $partido['equipo_local'] . ' vs ' . $partido['equipo_visitante']
                        . '** (' . etiqueta_dia($partido['fecha_hora']) . ', ' . hora_hs($partido['fecha_hora']) . ').'
                );
                flash_set('ok', 'Recordatorio guardado.');
            } else {
                flash_set('ok', 'Recordatorio eliminado.');
            }
        } catch (PDOException $e) {
            flash_set('error', 'No se pudo actualizar el recordatorio.');
        }

        redirigir($volver);
    }
}
