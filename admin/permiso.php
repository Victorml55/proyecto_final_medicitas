<?php

require_once(__DIR__ . '/sistema.class.php');
require_once(__DIR__ . '/auth.php');
requerirLogin();
require_once(__DIR__ . '/models/permiso.php');

$app    = new Permiso();
$id     = isset($_GET['id'])     ? (int)$_GET['id'] : null;
$accion = isset($_GET['accion']) ? $_GET['accion']   : null;
$error  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $accion === 'borrar') {
    switch ($accion) {

        case 'crear':
            $nombre = trim($_POST['nombre_permiso'] ?? '');
            if (!$nombre) {
                $error = 'El nombre del permiso es obligatorio.';
                break;
            }
            if (strlen($nombre) > 100) {
                $error = 'El nombre no puede superar 100 caracteres.';
                break;
            }
            if ($app->nombreExiste($nombre)) {
                $error = 'Ya existe un permiso con ese nombre.';
                break;
            }
            $app->crear($_POST);
            header('Location: permiso.php?accion=leer&ok=creado');
            exit;

        case 'actualizar':
            $nombre = trim($_POST['nombre_permiso'] ?? '');
            if (!$nombre || !$id) {
                $error = 'El nombre del permiso es obligatorio.';
                break;
            }
            if (strlen($nombre) > 100) {
                $error = 'El nombre no puede superar 100 caracteres.';
                break;
            }
            if ($app->nombreExiste($nombre, $id)) {
                $error = 'Ya existe otro permiso con ese nombre.';
                break;
            }
            $data = $_POST;
            $data['id_permiso'] = $id;
            $app->actualizar($data);
            header('Location: permiso.php?accion=leer&ok=actualizado');
            exit;

        case 'borrar':
            if ($id) {
                $app->borrar($id);
                header('Location: permiso.php?accion=leer&ok=borrado');
                exit;
            }
            break;
    }
}

require(__DIR__ . '/views/header.php');

switch ($accion) {
    case 'crear':
        require(__DIR__ . '/views/permisos/formulario_crear.php');
        break;
    case 'actualizar':
        $permiso = $id ? $app->leerUno($id) : null;
        require(__DIR__ . '/views/permisos/formulario_actualizar.php');
        break;
    case 'leer':
    default:
        $permisos = $app->leer();
        $msgs = [
            'creado'      => ['success', 'Permiso creado correctamente.'],
            'actualizado' => ['success', 'Permiso actualizado correctamente.'],
            'borrado'     => ['danger',  'Permiso eliminado.'],
        ];
        if (isset($_GET['ok'], $msgs[$_GET['ok']])) {
            [$t, $m] = $msgs[$_GET['ok']];
            $app->alerta($t, $m);
        }
        require(__DIR__ . '/views/permisos/index.php');
        break;
}

require(__DIR__ . '/views/footer.php');
