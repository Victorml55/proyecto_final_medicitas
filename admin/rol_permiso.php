<?php

require_once(__DIR__ . '/sistema.class.php');
require_once(__DIR__ . '/auth.php');
requerirLogin();
require_once(__DIR__ . '/models/rol_permiso.php');

$app    = new RolPermiso();
$id     = isset($_GET['id'])     ? (int)$_GET['id'] : null;
$accion = isset($_GET['accion']) ? $_GET['accion']   : null;
$error  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $accion === 'borrar') {
    switch ($accion) {

        case 'crear':
            $idRol     = (int)($_POST['id_rol']     ?? 0);
            $idPermiso = (int)($_POST['id_permiso'] ?? 0);
            if (!$idRol || !$idPermiso) {
                $error = 'Debes seleccionar un rol y un permiso.';
                break;
            }
            if ($app->asignacionExiste($idRol, $idPermiso)) {
                $error = 'Ese permiso ya está asignado a ese rol.';
                break;
            }
            $app->crear($_POST);
            header('Location: rol_permiso.php?accion=leer&ok=creado');
            exit;

        case 'actualizar':
            $idRol     = (int)($_POST['id_rol']     ?? 0);
            $idPermiso = (int)($_POST['id_permiso'] ?? 0);
            if (!$idRol || !$idPermiso || !$id) {
                $error = 'Debes seleccionar un rol y un permiso.';
                break;
            }
            if ($app->asignacionExiste($idRol, $idPermiso, $id)) {
                $error = 'Ese permiso ya está asignado a ese rol.';
                break;
            }
            $data = $_POST;
            $data['id_rol_permiso'] = $id;
            $app->actualizar($data);
            header('Location: rol_permiso.php?accion=leer&ok=actualizado');
            exit;

        case 'borrar':
            if ($id) {
                $app->borrar($id);
                header('Location: rol_permiso.php?accion=leer&ok=borrado');
                exit;
            }
            break;
    }
}

require(__DIR__ . '/views/header.php');

switch ($accion) {
    case 'crear':
        $roles    = $app->todosRoles();
        $permisos = $app->todosPermisos();
        require(__DIR__ . '/views/rol_permisos/formulario_crear.php');
        break;
    case 'actualizar':
        $asignacion = $id ? $app->leerUno($id) : null;
        $roles      = $app->todosRoles();
        $permisos   = $app->todosPermisos();
        require(__DIR__ . '/views/rol_permisos/formulario_actualizar.php');
        break;
    case 'leer':
    default:
        $asignaciones = $app->leer();
        $msgs = [
            'creado'      => ['success', 'Permiso asignado al rol correctamente.'],
            'actualizado' => ['success', 'Asignación actualizada correctamente.'],
            'borrado'     => ['danger',  'Asignación eliminada.'],
        ];
        if (isset($_GET['ok'], $msgs[$_GET['ok']])) {
            [$t, $m] = $msgs[$_GET['ok']];
            $app->alerta($t, $m);
        }
        require(__DIR__ . '/views/rol_permisos/index.php');
        break;
}

require(__DIR__ . '/views/footer.php');
