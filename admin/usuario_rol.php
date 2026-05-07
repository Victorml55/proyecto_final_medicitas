<?php

require_once(__DIR__ . '/sistema.class.php');
require_once(__DIR__ . '/auth.php');
requerirLogin();
require_once(__DIR__ . '/models/usuario_rol.php');

$app    = new UsuarioRol();
$id     = isset($_GET['id'])     ? (int)$_GET['id'] : null;
$accion = isset($_GET['accion']) ? $_GET['accion']   : null;
$error  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $accion === 'borrar') {
    switch ($accion) {

        case 'crear':
            $idUsuario = (int)($_POST['id_usuario'] ?? 0);
            $idRol     = (int)($_POST['id_rol']     ?? 0);
            if (!$idUsuario || !$idRol) {
                $error = 'Debes seleccionar un usuario y un rol.';
                break;
            }
            if ($app->asignacionExiste($idUsuario, $idRol)) {
                $error = 'Ese usuario ya tiene asignado ese rol.';
                break;
            }
            $app->crear($_POST);
            header('Location: usuario_rol.php?accion=leer&ok=creado');
            exit;

        case 'actualizar':
            $idUsuario = (int)($_POST['id_usuario'] ?? 0);
            $idRol     = (int)($_POST['id_rol']     ?? 0);
            if (!$idUsuario || !$idRol || !$id) {
                $error = 'Debes seleccionar un usuario y un rol.';
                break;
            }
            if ($app->asignacionExiste($idUsuario, $idRol, $id)) {
                $error = 'Ese usuario ya tiene asignado ese rol.';
                break;
            }
            $data = $_POST;
            $data['id_usuario_rol'] = $id;
            $app->actualizar($data);
            header('Location: usuario_rol.php?accion=leer&ok=actualizado');
            exit;

        case 'borrar':
            if ($id) {
                $app->borrar($id);
                header('Location: usuario_rol.php?accion=leer&ok=borrado');
                exit;
            }
            break;
    }
}

require(__DIR__ . '/views/header.php');

switch ($accion) {
    case 'crear':
        $usuarios = $app->todosUsuarios();
        $roles    = $app->todosRoles();
        require(__DIR__ . '/views/usuario_roles/formulario_crear.php');
        break;
    case 'actualizar':
        $asignacion = $id ? $app->leerUno($id) : null;
        $usuarios   = $app->todosUsuarios();
        $roles      = $app->todosRoles();
        require(__DIR__ . '/views/usuario_roles/formulario_actualizar.php');
        break;
    case 'leer':
    default:
        $asignaciones = $app->leer();
        $msgs = [
            'creado'      => ['success', 'Rol asignado al usuario correctamente.'],
            'actualizado' => ['success', 'Asignación actualizada correctamente.'],
            'borrado'     => ['danger',  'Asignación eliminada.'],
        ];
        if (isset($_GET['ok'], $msgs[$_GET['ok']])) {
            [$t, $m] = $msgs[$_GET['ok']];
            $app->alerta($t, $m);
        }
        require(__DIR__ . '/views/usuario_roles/index.php');
        break;
}

require(__DIR__ . '/views/footer.php');
