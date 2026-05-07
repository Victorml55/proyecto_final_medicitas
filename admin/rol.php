<?php

require_once(__DIR__ . '/sistema.class.php');
require_once(__DIR__ . '/auth.php');
requerirLogin();
require_once(__DIR__ . '/models/rol.php');

$app    = new Rol();
$id     = isset($_GET['id'])     ? (int)$_GET['id'] : null;
$accion = isset($_GET['accion']) ? $_GET['accion']   : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $accion === 'borrar') {
    switch ($accion) {
        case 'crear':
            if (!empty(trim($_POST['nombre_rol'] ?? ''))) {
                $app->crear($_POST);
                header('Location: rol.php?accion=leer&ok=creado');
                exit;
            }
            break;
        case 'actualizar':
            if (!empty(trim($_POST['nombre_rol'] ?? '')) && $id) {
                $data = $_POST;
                $data['id_rol'] = $id;
                $app->actualizar($data);
                header('Location: rol.php?accion=leer&ok=actualizado');
                exit;
            }
            break;
        case 'borrar':
            if ($id) {
                $app->borrar($id);
                header('Location: rol.php?accion=leer&ok=borrado');
                exit;
            }
            break;
    }
}

require(__DIR__ . '/views/header.php');

switch ($accion) {
    case 'crear':
        require(__DIR__ . '/views/roles/formulario_crear.php');
        break;
    case 'actualizar':
        $rol = $id ? $app->leerUno($id) : null;
        require(__DIR__ . '/views/roles/formulario_actualizar.php');
        break;
    case 'leer':
    default:
        $roles = $app->leer();
        $msgs = ['creado' => ['success','Rol creado.'], 'actualizado' => ['success','Rol actualizado.'], 'borrado' => ['danger','Rol eliminado.']];
        if (isset($_GET['ok'], $msgs[$_GET['ok']])) { [$t,$m] = $msgs[$_GET['ok']]; $app->alerta($t,$m); }
        require(__DIR__ . '/views/roles/index.php');
        break;
}

require(__DIR__ . '/views/footer.php');
