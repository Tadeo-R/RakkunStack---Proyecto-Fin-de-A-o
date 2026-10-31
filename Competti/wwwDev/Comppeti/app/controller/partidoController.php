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

    /** El Organizador crea un equipo oficial (POST ?action=admin_create_team) */
    public function registrarEquipo()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $nombreEquipo = trim($_POST['nombre_equipo'] ?? '');
            $id_torneo    = (int)($_POST['id_torneo'] ?? 0);

            if (!empty($nombreEquipo) && $id_torneo > 0) {
                // Intentamos crear el equipo mediante el modelo
                $creado = $this->partidos->crearEquipo($nombreEquipo, $_SESSION['usuario_id']);
                
                if (!$creado) {
                    // Si falló por duplicidad, redirigimos con un parámetro de error en la URL
                    header("Location: /index.php?view=torneos/detalles&id=" . $id_torneo . "&error=nombre_duplicado");
                    exit;
                }
            }

            header("Location: /index.php?view=torneos/detalles&id=" . $id_torneo);
            exit;
        }
    }

    public function salirEquipo()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $id_equipo = (int)($_GET['id_equipo'] ?? 0);

        if ($id_equipo && isset($_SESSION['usuario_id'])) {
            $this->partidos->salirDeEquipo($_SESSION['usuario_id'], $id_equipo);
        }
        
        // Redirigimos al listado general de equipos o al perfil según prefieras
        header("Location: /index.php?view=equipos/equipos");
        exit;
    }

    public function eliminarEquipoOrganizador()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $id_equipo = (int)($_GET['id_equipo'] ?? 0);
        $id_torneo = (int)($_GET['id_torneo'] ?? 0);

        if ($id_equipo) {
            $this->partidos->eliminarEquipoCompleto($id_equipo);
        }
        header("Location: /index.php?view=torneos/detalles&id=" . $id_torneo);
        exit;
    }

    /** Permite a un participante unirse de forma individual o seleccionando un equipo */
    public function unirseATorneo()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?view=torneos/torneos');
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) session_start();

        $id_torneo = (int)($_POST['id_torneo'] ?? 0);
        $id_equipo = !empty($_POST['id_equipo']) ? (int)$_POST['id_equipo'] : null;

        // Ejecutar proceso relacional seguro con candado de cupos
        $exito = $this->partidos->inscribirEnTorneo($_SESSION['usuario_id'], $id_torneo, $id_equipo);

        if ($exito) {
            if ($id_equipo) {
                // Guardamos en la sesión global qué ID de equipo elegiste para este id_torneo concreto
                $_SESSION['mi_equipo_id_' . $id_torneo] = $id_equipo;
            }
            header("Location: /index.php?view=torneos/detalles&id=$id_torneo&joined=true");
        } else {
            header("Location: /index.php?view=torneos/detalles&id=$id_torneo&error=cupos_llenos");
        }
        exit;
    }

}
