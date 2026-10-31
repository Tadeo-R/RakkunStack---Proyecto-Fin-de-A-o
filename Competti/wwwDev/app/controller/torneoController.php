<?php

class TorneoController
{
    private $torneos;
    private $notificaciones;

    public function __construct($torneoModel, $notificacionModel)
    {
        $this->torneos = $torneoModel;
        $this->notificaciones = $notificacionModel;
    }

    public function crear()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // SEGURIDAD: Si no hay sesión iniciada, redirigimos aquí antes de enviar HTML
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: /index.php?view=auth/login");
            exit();
        }

        // Si es GET, mostramos la vista de creación de manera segura
        require_once VIEW_PATH . '/torneos/crear.php';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?view=home');
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verificación de seguridad de usuario logueado
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /index.php?view=auth/login');
            exit;
        }

        $misTorneosActivos = $this->torneos->listarPorOrganizador($_SESSION['usuario_id']);
        $contadorActivos = 0;
        foreach ($misTorneosActivos as $t) {
            if ($t['estado'] !== 'finalizado') {
                $contadorActivos++;
            }
        }

        if ($contadorActivos >= 3) {
            $_SESSION['errores_torneo'] = ['Has alcanzado el límite máximo permitido de 3 torneos activos en simultáneo. Debes finalizar uno antes de crear otro.'];
            header('Location: /index.php?view=torneos/crear');
            exit;
        }

        $errores = [];

        // --- 1. Recoger Información General ---
        $nombre = trim($_POST['nombre'] ?? '');
        $categoria = $_POST['categoria'] ?? '';
        $juego = trim($_POST['deporte_nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 120) {
            $errores[] = 'El nombre del torneo debe tener entre 3 y 120 caracteres.';
        }
        if (!in_array($categoria, TorneoModel::CATEGORIAS, true)) {
            $errores[] = 'Seleccioná un tipo de disciplina válido.';
        }
        if (mb_strlen($juego) < 2 || mb_strlen($juego) > 100) {
            $errores[] = 'Indicá la disciplina o juego específico (entre 2 y 100 caracteres).';
        }

        // --- 2. Formato y Puntuación ---
        $formato = $_POST['formato'] ?? '';
        if (!in_array($formato, TorneoModel::FORMATOS, true)) {
            $errores[] = 'Seleccioná un formato de juego válido.';
        }

        $puntos = [];
        foreach (['pts_victoria' => 'victoria', 'pts_empate' => 'empate', 'pts_derrota' => 'derrota'] as $campo => $etiqueta) {
            $valor = filter_var($_POST[$campo] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 100]]);
            if ($valor === false) {
                $errores[] = "Los puntos por $etiqueta deben ser un número entre 0 y 100.";
                $valor = 0;
            }
            $puntos[$campo] = $valor;
        }
        $tercerPuesto = isset($_POST['tercer_puesto']) ? 1 : 0;

        // --- 3. Modalidad y Participantes ---
        $modalidad = $_POST['modalidad'] ?? '';
        if (!in_array($modalidad, TorneoModel::MODALIDADES, true)) {
            $errores[] = 'Seleccioná una modalidad de participación válida.';
        }

        $cupos = null;
        $cuposTxt = trim($_POST['cupos'] ?? '');
        if ($cuposTxt !== '') {
            $cupos = filter_var($cuposTxt, FILTER_VALIDATE_INT, ['options' => ['min_range' => 2, 'max_range' => 256]]);
        }

        $fechaInicio = null;
        $fechaTxt = trim($_POST['fecha_inicio'] ?? '');
        if ($fechaTxt !== '') {
            $fecha = DateTime::createFromFormat('Y-m-d', $fechaTxt);
            if ($fecha) {
                $fechaInicio = $fechaTxt;
            }
        }

        // --- 4. Módulos Opcionales (Streaming y Premios) ---
        $streaming = null;
        if (isset($_POST['enableStreaming'])) {
            $streaming = trim($_POST['twitch'] ?? null);
        }

        $premio1 = null;
        $premio2 = null;
        if (isset($_POST['enablePrizes'])) {
            $premio1 = trim($_POST['premio1'] ?? null);
            $premio2 = trim($_POST['premio2'] ?? null);
        }

        // Si hay errores de datos, rebotamos guardando la lista en sesión
        if (!empty($errores)) {
            $_SESSION['errores_torneo'] = $errores;
            header('Location: /index.php?view=torneos/crear');
            exit;
        }

        try {
            // 1. Guardamos el torneo (Esto ya funciona perfecto)
            $id = $this->torneos->crear([
                'id_organizador' => (int) $_SESSION['usuario_id'],
                'nombre'         => $nombre,
                'categoria'      => $categoria,
                'juego'          => $juego,
                'id_deporte'     => $this->torneos->resolverDisciplina($juego),
                'descripcion'    => $descripcion !== '' ? $descripcion : null,
                'formato'        => $formato,
                'pts_victoria'   => $puntos['pts_victoria'],
                'pts_empate'     => $puntos['pts_empate'],
                'pts_derrota'    => $puntos['pts_derrota'],
                'tercer_puesto'  => $tercerPuesto,
                'modalidad'      => $modalidad,
                'cupos'          => $cupos,
                'fecha_inicio'   => $fechaInicio,
                'streaming_url'  => $streaming,
                'premio1'        => $premio1,
                'premio2'        => $premio2,
            ]);

            // 2. SISTEMA DE ALERTA PROTEGIDO (Evita que el controlador muera si falla la tabla de tu amigo)
            try {
                if ($this->notificaciones && method_exists($this->notificaciones, 'crear')) {
                    $this->notificaciones->crear(
                        $_SESSION['usuario_id'],
                        'torneo',
                        'Torneo publicado',
                        'Tu torneo **' . $nombre . '** ya está visible en la lista de torneos.'
                    );
                }
            } catch (Exception $eNotif) {
                // Si la tabla NOTIFICACION no existe o falla, silenciamos el error 
                // para que NO rompa la experiencia principal y te deje avanzar
            }

            // REDIRECCIÓN EXITOSA DE COMPETTI: Nos vamos al perfil
            header('Location: /index.php?view=users/profile&tournament_created=true');
            exit;

        } catch (PDOException $e) {
            $_SESSION['errores_torneo'] = ['Error crítico de base de datos al guardar: ' . $e->getMessage()];
            header('Location: /index.php?view=torneos/crear');
            exit;
        }
    }

    /** Procesa los cambios editados de un torneo (POST ?action=update_tournament) */
    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?view=torneos/torneos');
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Validar autenticación básica
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /index.php?view=auth/login');
            exit;
        }

        $id_torneo = (int)($_POST['id_torneo'] ?? 0);

        if ($id_torneo <= 0) {
            header('Location: /index.php?view=torneos/torneos');
            exit;
        }

        // 2. Recolectar y limpiar los datos del formulario
        $datosActualizados = [
            'nombreTorneo' => trim($_POST['nombreTorneo'] ?? ''),
            'modalidad'    => $_POST['modalidad'] ?? 'individual',
            'juego'        => trim($_POST['juego'] ?? ''),
            'formato'      => $_POST['formato'] ?? 'liga',
            'cupos'        => (int)($_POST['cupos'] ?? 16),
            'fechaInicio'  => $_POST['fechaInicio'] ?? date('Y-m-d'),
            'estado'       => $_POST['estado'] ?? 'inscripciones'
        ];

        // 3. Ejecutar actualización en la base de datos
        $exito = $this->torneos->actualizarTorneo($id_torneo, $datosActualizados);

        if ($exito) {
            // Redirigir a la vista de detalles del torneo con mensaje de éxito
            header("Location: /index.php?view=torneos/detalles&id={$id_torneo}&updated=true");
        } else {
            // Redirigir de nuevo a la vista de edición con mensaje de error
            header("Location: /index.php?view=torneos/editar&id={$id_torneo}&error=actualizacion_fallida");
        }
        exit;
    }

}