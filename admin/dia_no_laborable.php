<?php

require_once(__DIR__ . '/sistema.class.php');
require_once(__DIR__ . '/auth.php');
requerirLogin();
require_once(__DIR__ . '/models/dia_no_laborable.php');

$app    = new DiaNoLaborable();
$id     = isset($_GET['id'])     ? (int)$_GET['id'] : null;
$accion = isset($_GET['accion']) ? $_GET['accion']   : null;
$error  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $accion === 'borrar') {
    switch ($accion) {

        case 'crear':
            $fecha = trim($_POST['fecha'] ?? '');
            if (!$fecha || !DateTime::createFromFormat('Y-m-d', $fecha)) {
                $error = 'La fecha es obligatoria y debe tener formato válido.'; break;
            }
            $app->crear($_POST);
            header('Location: dia_no_laborable.php?accion=leer&ok=creado');
            exit;

        case 'actualizar':
            $fecha = trim($_POST['fecha'] ?? '');
            if (!$fecha || !DateTime::createFromFormat('Y-m-d', $fecha) || !$id) {
                $error = 'La fecha es obligatoria y debe tener formato válido.'; break;
            }
            $data = $_POST;
            $data['id_dia_no_laborable'] = $id;
            $app->actualizar($data);
            header('Location: dia_no_laborable.php?accion=leer&ok=actualizado');
            exit;

        case 'borrar':
            if ($id) {
                $app->borrar($id);
                header('Location: dia_no_laborable.php?accion=leer&ok=borrado');
                exit;
            }
            break;
    }
}

require(__DIR__ . '/views/header.php');

switch ($accion) {
    case 'crear':
        $medicos = $app->todosMedicos();
        require(__DIR__ . '/views/dias_no_laborables/formulario_crear.php');
        break;
    case 'actualizar':
        $dia     = $id ? $app->leerUno($id) : null;
        $medicos = $app->todosMedicos();
        require(__DIR__ . '/views/dias_no_laborables/formulario_actualizar.php');
        break;
    case 'leer':
    default:
        $dias = $app->leer();
        $msgs = [
            'creado'      => ['success', 'Día no laborable registrado.'],
            'actualizado' => ['success', 'Día no laborable actualizado.'],
            'borrado'     => ['danger',  'Día no laborable eliminado.'],
        ];
        if (isset($_GET['ok'], $msgs[$_GET['ok']])) { [$t,$m] = $msgs[$_GET['ok']]; $app->alerta($t,$m); }
        require(__DIR__ . '/views/dias_no_laborables/index.php');
        break;
}

require(__DIR__ . '/views/footer.php');
