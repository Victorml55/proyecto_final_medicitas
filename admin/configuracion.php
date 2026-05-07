<?php

require_once(__DIR__ . '/sistema.class.php');
require_once(__DIR__ . '/auth.php');
requerirLogin();
require_once(__DIR__ . '/models/configuracion.php');

$app    = new Configuracion();
$id     = isset($_GET['id'])     ? (int)$_GET['id'] : null;
$accion = isset($_GET['accion']) ? $_GET['accion']   : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $accion === 'borrar') {
    switch ($accion) {
        case 'crear':
            $clave    = trim($_POST['clave']    ?? '');
            $valor    = trim($_POST['valor']    ?? '');
            $tipo     = $_POST['tipo_dato']     ?? '';
            if ($clave !== '' && $valor !== '' && in_array($tipo, Configuracion::TIPOS, true)) {
                if ($tipo === 'int' && !ctype_digit($valor)) {
                    $error = 'El valor debe ser un número entero para tipo "int".';
                    break;
                }
                if ($tipo === 'boolean' && !in_array($valor, ['true','false','1','0'], true)) {
                    $error = 'El valor para tipo "boolean" debe ser true o false.';
                    break;
                }
                if ($tipo === 'json') {
                    json_decode($valor);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $error = 'El valor no es un JSON válido.';
                        break;
                    }
                }
                $app->crear($_POST);
                header('Location: configuracion.php?accion=leer&ok=creado');
                exit;
            }
            break;
        case 'actualizar':
            $clave = trim($_POST['clave'] ?? '');
            $valor = trim($_POST['valor'] ?? '');
            $tipo  = $_POST['tipo_dato'] ?? '';
            if ($clave !== '' && $valor !== '' && in_array($tipo, Configuracion::TIPOS, true) && $id) {
                if ($tipo === 'int' && !ctype_digit($valor)) {
                    $error = 'El valor debe ser un número entero para tipo "int".';
                    break;
                }
                if ($tipo === 'boolean' && !in_array($valor, ['true','false','1','0'], true)) {
                    $error = 'El valor para tipo "boolean" debe ser true o false.';
                    break;
                }
                if ($tipo === 'json') {
                    json_decode($valor);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $error = 'El valor no es un JSON válido.';
                        break;
                    }
                }
                $data = $_POST;
                $data['id_config'] = $id;
                $app->actualizar($data);
                header('Location: configuracion.php?accion=leer&ok=actualizado');
                exit;
            }
            break;
        case 'borrar':
            if ($id) {
                $app->borrar($id);
                header('Location: configuracion.php?accion=leer&ok=borrado');
                exit;
            }
            break;
    }
}

require(__DIR__ . '/views/header.php');

switch ($accion) {
    case 'crear':
        require(__DIR__ . '/views/configuracion/formulario_crear.php');
        break;
    case 'actualizar':
        $config = $id ? $app->leerUno($id) : null;
        require(__DIR__ . '/views/configuracion/formulario_actualizar.php');
        break;
    case 'leer':
    default:
        $configs = $app->leer();
        $msgs = ['creado' => ['success','Configuración creada.'], 'actualizado' => ['success','Configuración actualizada.'], 'borrado' => ['danger','Configuración eliminada.']];
        if (isset($_GET['ok'], $msgs[$_GET['ok']])) { [$t,$m] = $msgs[$_GET['ok']]; $app->alerta($t,$m); }
        require(__DIR__ . '/views/configuracion/index.php');
        break;
}

require(__DIR__ . '/views/footer.php');
