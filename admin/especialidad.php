<?php

require_once(__DIR__ . '/sistema.class.php');
require_once(__DIR__ . '/auth.php');
requerirLogin();
require_once(__DIR__ . '/models/especialidad.php');

$app    = new Especialidad();
$id     = isset($_GET['id'])     ? (int)$_GET['id'] : null;
$accion = isset($_GET['accion']) ? $_GET['accion']   : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $accion === 'borrar') {
    switch ($accion) {
        case 'crear':
            if (!empty(trim($_POST['nombre_especialidad'] ?? ''))) {
                $app->crear($_POST);
                header('Location: especialidad.php?accion=leer&ok=creado');
                exit;
            }
            break;
        case 'actualizar':
            if (!empty(trim($_POST['nombre_especialidad'] ?? '')) && $id) {
                $data = $_POST;
                $data['id_especialidad'] = $id;
                $app->actualizar($data);
                header('Location: especialidad.php?accion=leer&ok=actualizado');
                exit;
            }
            break;
        case 'borrar':
            if ($id) {
                $app->borrar($id);
                header('Location: especialidad.php?accion=leer&ok=borrado');
                exit;
            }
            break;
    }
}

require(__DIR__ . '/views/header.php');

switch ($accion) {
    case 'crear':
        require(__DIR__ . '/views/especialidad/formulario_crear.php');
        break;
    case 'actualizar':
        $especialidad = $id ? $app->leerUno($id) : null;
        require(__DIR__ . '/views/especialidad/formulario_actualizar.php');
        break;
    case 'leer':
    default:
        $especialidades = $app->leer();
        $msgs = ['creado' => ['success','Especialidad creada.'], 'actualizado' => ['success','Especialidad actualizada.'], 'borrado' => ['danger','Especialidad eliminada.']];
        if (isset($_GET['ok'], $msgs[$_GET['ok']])) { [$t,$m] = $msgs[$_GET['ok']]; $app->alerta($t,$m); }
        require(__DIR__ . '/views/especialidad/index.php');
        break;
}

require(__DIR__ . '/views/footer.php');
