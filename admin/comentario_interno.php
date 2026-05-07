<?php

require_once(__DIR__ . '/sistema.class.php');
require_once(__DIR__ . '/auth.php');
requerirLogin();
require_once(__DIR__ . '/models/comentario_interno.php');

$app    = new ComentarioInterno();
$id     = isset($_GET['id'])     ? (int)$_GET['id'] : null;
$accion = isset($_GET['accion']) ? $_GET['accion']   : null;
$error  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $accion === 'borrar') {
    switch ($accion) {

        case 'crear':
            $idAutor    = (int)($_POST['id_usuario_autor'] ?? 0);
            $comentario = trim($_POST['comentario'] ?? '');
            if (!$idAutor || !$comentario) {
                $error = 'El autor y el comentario son obligatorios.'; break;
            }
            $app->crear($_POST);
            header('Location: comentario_interno.php?accion=leer&ok=creado');
            exit;

        case 'actualizar':
            $idAutor    = (int)($_POST['id_usuario_autor'] ?? 0);
            $comentario = trim($_POST['comentario'] ?? '');
            if (!$idAutor || !$comentario || !$id) {
                $error = 'El autor y el comentario son obligatorios.'; break;
            }
            $data = $_POST;
            $data['id_comentario'] = $id;
            $app->actualizar($data);
            header('Location: comentario_interno.php?accion=leer&ok=actualizado');
            exit;

        case 'borrar':
            if ($id) {
                $app->borrar($id);
                header('Location: comentario_interno.php?accion=leer&ok=borrado');
                exit;
            }
            break;
    }
}

require(__DIR__ . '/views/header.php');

switch ($accion) {
    case 'crear':
        $usuarios  = $app->todosUsuarios();
        $pacientes = $app->todosPacientes();
        require(__DIR__ . '/views/comentarios_internos/formulario_crear.php');
        break;
    case 'actualizar':
        $comentario = $id ? $app->leerUno($id) : null;
        $usuarios   = $app->todosUsuarios();
        $pacientes  = $app->todosPacientes();
        require(__DIR__ . '/views/comentarios_internos/formulario_actualizar.php');
        break;
    case 'leer':
    default:
        $comentarios = $app->leer();
        $msgs = [
            'creado'      => ['success', 'Comentario registrado.'],
            'actualizado' => ['success', 'Comentario actualizado.'],
            'borrado'     => ['danger',  'Comentario eliminado.'],
        ];
        if (isset($_GET['ok'], $msgs[$_GET['ok']])) { [$t,$m] = $msgs[$_GET['ok']]; $app->alerta($t,$m); }
        require(__DIR__ . '/views/comentarios_internos/index.php');
        break;
}

require(__DIR__ . '/views/footer.php');
