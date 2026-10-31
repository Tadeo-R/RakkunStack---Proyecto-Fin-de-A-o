<?php

class NotificacionController
{
    private $modelo;

    public function __construct($notificacionModel)
    {
        $this->modelo = $notificacionModel;
    }

    /** Marca una notificación como leída (POST ?action=notif_leer). */
    public function marcarLeida()
    {
        $this->ejecutar(function ($uid) {
            $this->modelo->marcarLeida($uid, (int) ($_POST['id'] ?? 0));
        });
    }

    /** Marca todas como leídas (POST ?action=notif_leer_todas). */
    public function marcarTodasLeidas()
    {
        $this->ejecutar(function ($uid) {
            $this->modelo->marcarTodasLeidas($uid);
        });
    }

    /** Elimina una notificación (POST ?action=notif_eliminar). */
    public function eliminar()
    {
        $this->ejecutar(function ($uid) {
            $this->modelo->eliminar($uid, (int) ($_POST['id'] ?? 0));
        });
    }

    /** Elimina todas las leídas (POST ?action=notif_limpiar). */
    public function limpiarLeidas()
    {
        $this->ejecutar(function ($uid) {
            $this->modelo->eliminarLeidas($uid);
        });
    }

    /** Valida método, sesión y CSRF; ejecuta la acción y vuelve al panel con el mismo filtro. */
    private function ejecutar($accion)
    {
        $filtros = ['todas', 'sin_leer', 'torneos', 'sistema'];
        $filtro = $_POST['filtro'] ?? 'todas';
        $filtro = in_array($filtro, $filtros, true) ? $filtro : 'todas';
        $q = trim($_POST['q'] ?? '');

        $params = ['filtro' => $filtro];
        if ($q !== '') {
            $params['q'] = $q;
        }
        $volver = url('notificaciones', $params);

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

        try {
            $accion((int) $_SESSION['usuario_id']);
        } catch (PDOException $e) {
            flash_set('error', 'No se pudo completar la acción. Intentá de nuevo.');
        }

        redirigir($volver);
    }
}
