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

    /** Procesa el formulario "Crear Nuevo Torneo" (POST ?action=create_tournament). */
    public function crear()
    {
        $volver = url('crear-torneo');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir($volver);
        }
        if (!usuario_logueado()) {
            redirigir(url('auth/login'));
        }
        if (!csrf_valido()) {
            flash_set('error', 'El formulario expiró. Volvé a intentarlo.');
            redirigir($volver);
        }

        $errores = [];

        // --- Información general ---
        $nombre      = trim($_POST['nombre'] ?? '');
        $categoria   = $_POST['categoria'] ?? '';
        $juego       = trim($_POST['deporte_nombre'] ?? '');
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
        if (mb_strlen($descripcion) > 2000) {
            $errores[] = 'La descripción no puede superar los 2000 caracteres.';
        }

        // --- Formato y puntuación ---
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

        // --- Modalidad y participantes ---
        $modalidad = $_POST['modalidad'] ?? '';
        if (!in_array($modalidad, TorneoModel::MODALIDADES, true)) {
            $errores[] = 'Seleccioná una modalidad de participación válida.';
        }

        $cupos = null;
        $cuposTxt = trim($_POST['cupos'] ?? '');
        if ($cuposTxt !== '') {
            $cupos = filter_var($cuposTxt, FILTER_VALIDATE_INT, ['options' => ['min_range' => 2, 'max_range' => 256]]);
            if ($cupos === false) {
                $errores[] = 'El límite de cupos debe ser un número entre 2 y 256.';
                $cupos = null;
            }
        }

        $fechaInicio = null;
        $fechaTxt = trim($_POST['fecha_inicio'] ?? '');
        if ($fechaTxt !== '') {
            $fecha = DateTime::createFromFormat('Y-m-d', $fechaTxt);
            if (!$fecha || $fecha->format('Y-m-d') !== $fechaTxt) {
                $errores[] = 'La fecha de inicio no es válida.';
            } else {
                $fechaInicio = $fechaTxt;
            }
        }

        // --- Módulos opcionales (solo se guardan si están activados) ---
        $streaming = null;
        if (isset($_POST['enableStreaming'])) {
            $urlTxt = trim($_POST['twitch'] ?? '');
            if ($urlTxt !== '') {
                $esUrl = filter_var($urlTxt, FILTER_VALIDATE_URL)
                    && preg_match('#^https?://#i', $urlTxt)
                    && mb_strlen($urlTxt) <= 255;
                if ($esUrl) {
                    $streaming = $urlTxt;
                } else {
                    $errores[] = 'El enlace de transmisión debe ser una URL válida (http:// o https://).';
                }
            }
        }

        $premio1 = null;
        $premio2 = null;
        if (isset($_POST['enablePrizes'])) {
            $premio1 = trim($_POST['premio1'] ?? '');
            $premio2 = trim($_POST['premio2'] ?? '');
            if (mb_strlen($premio1) > 100 || mb_strlen($premio2) > 100) {
                $errores[] = 'Cada premio puede tener hasta 100 caracteres.';
            }
            $premio1 = $premio1 !== '' ? $premio1 : null;
            $premio2 = $premio2 !== '' ? $premio2 : null;
        }

        if ($errores) {
            flash_set('errores', $errores);
            flash_set('old', $_POST);
            redirigir($volver);
        }

        try {
            $id = $this->torneos->crear([
                'organizador_id' => (int) $_SESSION['usuario_id'],
                'nombre'         => $nombre,
                'categoria'      => $categoria,
                'juego'          => $juego,
                'disciplina_id'  => $this->torneos->resolverDisciplina($juego),
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

            $this->notificaciones->crear(
                $_SESSION['usuario_id'],
                'torneo',
                'Torneo publicado',
                'Tu torneo **' . $nombre . '** ya está visible en la lista de torneos.'
            );
        } catch (PDOException $e) {
            flash_set('error', 'No se pudo guardar el torneo. Intentá de nuevo en unos minutos.');
            flash_set('old', $_POST);
            redirigir($volver);
        }

        flash_set('ok', 'Torneo "' . $nombre . '" publicado correctamente.');
        redirigir(url('torneos'));
    }
}
