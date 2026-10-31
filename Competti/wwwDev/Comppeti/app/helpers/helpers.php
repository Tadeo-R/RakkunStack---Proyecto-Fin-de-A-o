<?php
/**
 * Funciones de apoyo compartidas por vistas y controladores.
 * Se carga una sola vez desde html/index.php.
 */

/** Escapa texto para imprimirlo de forma segura en HTML. */
function e($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

/** Construye una URL del enrutador: url('torneos', ['q' => 'copa']) */
function url($vista, array $params = [])
{
    return '/index.php?' . http_build_query(array_merge(['view' => $vista], $params));
}

/** Redirige y corta la ejecución (siempre antes de imprimir HTML). */
function redirigir($destino)
{
    header('Location: ' . $destino);
    exit;
}

function usuario_logueado()
{
    return isset($_SESSION['usuario_id']);
}

/* ---------------------------- CSRF ---------------------------- */

function csrf_token()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valido()
{
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], (string) $_POST['csrf']);
}

/* ------------------------- Mensajes flash ------------------------- */

function flash_set($clave, $valor)
{
    $_SESSION['flash'][$clave] = $valor;
}

function flash_get($clave, $defecto = null)
{
    $valor = isset($_SESSION['flash'][$clave]) ? $_SESSION['flash'][$clave] : $defecto;
    unset($_SESSION['flash'][$clave]);
    return $valor;
}

/** Imprime los avisos pendientes (éxito / errores) una única vez. */
function render_flash()
{
    $html = '';
    $ok = flash_get('ok');
    if ($ok) {
        $html .= '<div class="flash flash-ok"><i class="fa-solid fa-circle-check"></i> ' . e($ok) . '</div>';
    }
    $error = flash_get('error');
    if ($error) {
        $html .= '<div class="flash flash-error"><i class="fa-solid fa-circle-exclamation"></i> ' . e($error) . '</div>';
    }
    $errores = flash_get('errores');
    if (is_array($errores) && $errores) {
        $html .= '<div class="flash flash-error"><i class="fa-solid fa-circle-exclamation"></i><ul>';
        foreach ($errores as $linea) {
            $html .= '<li>' . e($linea) . '</li>';
        }
        $html .= '</ul></div>';
    }
    return $html;
}

/* --------------------------- Textos / fechas --------------------------- */

/** Minúsculas y sin tildes, para comparar textos libres. */
function normalizar_texto($texto)
{
    $texto = mb_strtolower((string) $texto, 'UTF-8');
    return strtr($texto, [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n',
    ]);
}

/**
 * Permite un formato mínimo en textos guardados en la BD:
 * **negrita** y ~cursiva~. Todo lo demás se escapa.
 */
function texto_enriquecido($texto)
{
    $seguro = e($texto);
    $seguro = preg_replace('/\*\*(.+?)\*\*/u', '<strong>$1</strong>', $seguro);
    $seguro = preg_replace('/~(.+?)~/u', '<em>$1</em>', $seguro);
    return $seguro;
}

function nombre_dia($timestamp)
{
    $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    return $dias[(int) date('w', $timestamp)];
}

function nombre_mes_corto($timestamp)
{
    $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    return $meses[(int) date('n', $timestamp) - 1];
}

/** "15 Ago 2026" */
function fecha_corta($fecha)
{
    $ts = strtotime($fecha);
    return date('d', $ts) . ' ' . nombre_mes_corto($ts) . ' ' . date('Y', $ts);
}

/** "Hoy", "Mañana", "Sábado 25" o "25 Oct" según la distancia. */
function etiqueta_dia($fechaHora)
{
    $ts = strtotime($fechaHora);
    $dias = (int) round((strtotime(date('Y-m-d', $ts)) - strtotime('today')) / 86400);

    if ($dias === 0) {
        return 'Hoy';
    }
    if ($dias === 1) {
        return 'Mañana';
    }
    if ($dias > 1 && $dias < 7) {
        return nombre_dia($ts) . ' ' . date('j', $ts);
    }
    return date('j', $ts) . ' ' . nombre_mes_corto($ts);
}

/** "18:00 hs" */
function hora_hs($fechaHora)
{
    return date('H:i', strtotime($fechaHora)) . ' hs';
}

/** "Hace 10 min", "Hace 1 hora", "Ayer", "Hace 3 días" */
function tiempo_relativo($fechaHora)
{
    $ts = strtotime($fechaHora);
    $dif = time() - $ts;

    if ($dif < 60) {
        return 'Hace un momento';
    }
    if ($dif < 3600) {
        return 'Hace ' . floor($dif / 60) . ' min';
    }
    if ($dif < 86400) {
        $h = (int) floor($dif / 3600);
        return 'Hace ' . $h . ($h === 1 ? ' hora' : ' horas');
    }
    $d = (int) floor($dif / 86400);
    if ($d === 1) {
        return 'Ayer';
    }
    if ($d < 30) {
        return 'Hace ' . $d . ' días';
    }
    return fecha_corta($fechaHora);
}

/* ------------------------- Iconos por categoría ------------------------- */

function icono_tipo($tipo)
{
    $mapa = ['deporte' => 'fa-shield-halved', 'esports' => 'fa-headset', 'mentales' => 'fa-chess-knight'];
    return isset($mapa[$tipo]) ? $mapa[$tipo] : 'fa-shield-halved';
}

function color_tipo($tipo)
{
    $mapa = ['deporte' => 'var(--primary-color)', 'esports' => '#6f42c1', 'mentales' => '#fd7e14'];
    return isset($mapa[$tipo]) ? $mapa[$tipo] : 'var(--primary-color)';
}

/** Imagen local de respaldo cuando el torneo no tiene disciplina con foto. */
function imagen_tipo($tipo)
{
    $mapa = ['deporte' => '/img/futbol.png', 'esports' => '/img/joystickPS5.png', 'mentales' => '/img/ajedrez.png'];
    return isset($mapa[$tipo]) ? $mapa[$tipo] : '/img/futbol.png';
}
