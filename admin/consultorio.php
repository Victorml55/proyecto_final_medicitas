<?php

require_once(__DIR__ . '/sistema.class.php');
require_once(__DIR__ . '/auth.php');
requerirLogin();
require_once(__DIR__ . '/models/consultorio.php');

$app    = new Consultorio();
$id     = isset($_GET['id'])     ? (int)$_GET['id'] : null;
$accion = isset($_GET['accion']) ? $_GET['accion']   : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $accion === 'borrar') {
    switch ($accion) {
        case 'crear':
            if (!empty(trim($_POST['numero_consultorio'] ?? ''))) {
                $app->crear($_POST);
                header('Location: consultorio.php?accion=leer&ok=creado');
                exit;
            }
            break;
        case 'actualizar':
            if (!empty(trim($_POST['numero_consultorio'] ?? '')) && $id) {
                $data = $_POST;
                $data['id_consultorio'] = $id;
                $app->actualizar($data);
                header('Location: consultorio.php?accion=leer&ok=actualizado');
                exit;
            }
            break;
        case 'borrar':
            if ($id) {
                $app->borrar($id);
                header('Location: consultorio.php?accion=leer&ok=borrado');
                exit;
            }
            break;
    }
}

require(__DIR__ . '/views/header.php');

switch ($accion) {
    case 'crear':
        require(__DIR__ . '/views/consultorios/formulario_crear.php');
        break;
    case 'actualizar':
        $consultorio = $id ? $app->leerUno($id) : null;
        require(__DIR__ . '/views/consultorios/formulario_actualizar.php');
        break;
    case 'leer':
    default:
        $consultorios = $app->leer();
        $msgs = ['creado' => ['success','Consultorio creado.'], 'actualizado' => ['success','Consultorio actualizado.'], 'borrado' => ['danger','Consultorio eliminado.']];
        if (isset($_GET['ok'], $msgs[$_GET['ok']])) { [$t,$m] = $msgs[$_GET['ok']]; $app->alerta($t,$m); }
        require(__DIR__ . '/views/consultorios/index.php');
        break;
}

require(__DIR__ . '/views/footer.php');
