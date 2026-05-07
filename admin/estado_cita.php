<?php

require_once(__DIR__ . '/sistema.class.php');
require_once(__DIR__ . '/auth.php');
requerirLogin();
require_once(__DIR__ . '/models/estado_cita.php');

$app    = new EstadoCita();
$id     = isset($_GET['id'])     ? (int)$_GET['id'] : null;
$accion = isset($_GET['accion']) ? $_GET['accion']   : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $accion === 'borrar') {
    switch ($accion) {
        case 'crear':
            if (!empty(trim($_POST['nombre_estado'] ?? ''))) {
                $color = trim($_POST['color'] ?? '');
                if ($color !== '' && !preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
                    $error = 'El color debe ser un hexadecimal válido (ej. #FF5733).';
                    break;
                }
                $app->crear($_POST);
                header('Location: estado_cita.php?accion=leer&ok=creado');
                exit;
            }
            break;
        case 'actualizar':
            if (!empty(trim($_POST['nombre_estado'] ?? '')) && $id) {
                $color = trim($_POST['color'] ?? '');
                if ($color !== '' && !preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
                    $error = 'El color debe ser un hexadecimal válido (ej. #FF5733).';
                    break;
                }
                $data = $_POST;
                $data['id_estado'] = $id;
                $app->actualizar($data);
                header('Location: estado_cita.php?accion=leer&ok=actualizado');
                exit;
            }
            break;
        case 'borrar':
            if ($id) {
                $app->borrar($id);
                header('Location: estado_cita.php?accion=leer&ok=borrado');
                exit;
            }
            break;
    }
}

require(__DIR__ . '/views/header.php');

switch ($accion) {
    case 'crear':
        require(__DIR__ . '/views/estados_cita/formulario_crear.php');
        break;
    case 'actualizar':
        $estado = $id ? $app->leerUno($id) : null;
        require(__DIR__ . '/views/estados_cita/formulario_actualizar.php');
        break;
    case 'leer':
    default:
        $estados = $app->leer();
        $msgs = ['creado' => ['success','Estado creado.'], 'actualizado' => ['success','Estado actualizado.'], 'borrado' => ['danger','Estado eliminado.']];
        if (isset($_GET['ok'], $msgs[$_GET['ok']])) { [$t,$m] = $msgs[$_GET['ok']]; $app->alerta($t,$m); }
        require(__DIR__ . '/views/estados_cita/index.php');
        break;
}

require(__DIR__ . '/views/footer.php');
