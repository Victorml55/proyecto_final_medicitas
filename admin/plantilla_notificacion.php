<?php

require_once(__DIR__ . '/sistema.class.php');
require_once(__DIR__ . '/auth.php');
requerirLogin();
require_once(__DIR__ . '/models/plantilla_notificacion.php');

$app    = new PlantillaNotificacion();
$id     = isset($_GET['id'])     ? (int)$_GET['id'] : null;
$accion = isset($_GET['accion']) ? $_GET['accion']   : null;
$error  = null;

$canalesValidos = ['Email', 'SMS', 'Sistema'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $accion === 'borrar') {
    switch ($accion) {

        case 'crear':
            $codigo  = trim($_POST['codigo_plantilla']  ?? '');
            $nombre  = trim($_POST['nombre_plantilla']  ?? '');
            $asunto  = trim($_POST['asunto']            ?? '');
            $cuerpo  = trim($_POST['cuerpo_mensaje']    ?? '');
            $canal   = $_POST['tipo_canal']             ?? '';

            if (!$codigo || !$nombre || !$asunto || !$cuerpo || !$canal) {
                $error = 'Código, nombre, asunto, cuerpo y canal son obligatorios.'; break;
            }
            if (!preg_match('/^[A-Z0-9_]+$/', $codigo)) {
                $error = 'El código solo puede contener letras mayúsculas, números y guiones bajos.'; break;
            }
            if (!in_array($canal, $canalesValidos, true)) { $error = 'Canal inválido.'; break; }
            if ($app->codigoExiste($codigo)) { $error = 'Ya existe una plantilla con ese código.'; break; }
            $app->crear($_POST);
            header('Location: plantilla_notificacion.php?accion=leer&ok=creado');
            exit;

        case 'actualizar':
            $codigo  = trim($_POST['codigo_plantilla']  ?? '');
            $nombre  = trim($_POST['nombre_plantilla']  ?? '');
            $asunto  = trim($_POST['asunto']            ?? '');
            $cuerpo  = trim($_POST['cuerpo_mensaje']    ?? '');
            $canal   = $_POST['tipo_canal']             ?? '';

            if (!$codigo || !$nombre || !$asunto || !$cuerpo || !$canal || !$id) {
                $error = 'Código, nombre, asunto, cuerpo y canal son obligatorios.'; break;
            }
            if (!preg_match('/^[A-Z0-9_]+$/', $codigo)) {
                $error = 'El código solo puede contener letras mayúsculas, números y guiones bajos.'; break;
            }
            if (!in_array($canal, $canalesValidos, true)) { $error = 'Canal inválido.'; break; }
            if ($app->codigoExiste($codigo, $id)) { $error = 'Ya existe otra plantilla con ese código.'; break; }
            $data = $_POST;
            $data['id_plantilla'] = $id;
            $app->actualizar($data);
            header('Location: plantilla_notificacion.php?accion=leer&ok=actualizado');
            exit;

        case 'borrar':
            if ($id) {
                $app->borrar($id);
                header('Location: plantilla_notificacion.php?accion=leer&ok=borrado');
                exit;
            }
            break;
    }
}

require(__DIR__ . '/views/header.php');

switch ($accion) {
    case 'crear':
        require(__DIR__ . '/views/plantillas_notificaciones/formulario_crear.php');
        break;
    case 'actualizar':
        $plantilla = $id ? $app->leerUno($id) : null;
        require(__DIR__ . '/views/plantillas_notificaciones/formulario_actualizar.php');
        break;
    case 'leer':
    default:
        $plantillas = $app->leer();
        $msgs = [
            'creado'      => ['success', 'Plantilla creada correctamente.'],
            'actualizado' => ['success', 'Plantilla actualizada correctamente.'],
            'borrado'     => ['danger',  'Plantilla eliminada.'],
        ];
        if (isset($_GET['ok'], $msgs[$_GET['ok']])) { [$t,$m] = $msgs[$_GET['ok']]; $app->alerta($t,$m); }
        require(__DIR__ . '/views/plantillas_notificaciones/index.php');
        break;
}

require(__DIR__ . '/views/footer.php');
