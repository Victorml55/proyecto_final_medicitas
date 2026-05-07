<?php

require_once(__DIR__ . '/sistema.class.php');
require_once(__DIR__ . '/auth.php');
requerirLogin();
require_once(__DIR__ . '/models/paciente.php');

$app    = new Paciente();
$id     = isset($_GET['id'])     ? (int)$_GET['id'] : null;
$accion = isset($_GET['accion']) ? $_GET['accion']   : null;
$error  = null;

$tiposSangre = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $accion === 'borrar') {
    switch ($accion) {

        case 'crear':
            $idUsuario = (int)($_POST['id_usuario'] ?? 0);
            $exp       = trim($_POST['numero_expediente'] ?? '');
            $sangre    = $_POST['tipo_sangre'] ?? '';

            if (!$idUsuario) { $error = 'Debes seleccionar un usuario.'; break; }
            if ($sangre && !in_array($sangre, $tiposSangre, true)) { $error = 'Tipo de sangre inválido.'; break; }
            if ($app->usuarioYaEsPaciente($idUsuario)) { $error = 'Ese usuario ya está registrado como paciente.'; break; }
            if ($exp && $app->expedienteExiste($exp)) { $error = 'Ya existe un paciente con ese número de expediente.'; break; }
            $app->crear($_POST);
            header('Location: paciente.php?accion=leer&ok=creado');
            exit;

        case 'actualizar':
            $idUsuario = (int)($_POST['id_usuario'] ?? 0);
            $exp       = trim($_POST['numero_expediente'] ?? '');
            $sangre    = $_POST['tipo_sangre'] ?? '';

            if (!$idUsuario || !$id) { $error = 'Debes seleccionar un usuario.'; break; }
            if ($sangre && !in_array($sangre, $tiposSangre, true)) { $error = 'Tipo de sangre inválido.'; break; }
            if ($app->usuarioYaEsPaciente($idUsuario, $id)) { $error = 'Ese usuario ya está registrado como paciente.'; break; }
            if ($exp && $app->expedienteExiste($exp, $id)) { $error = 'Ya existe otro paciente con ese número de expediente.'; break; }
            $data = $_POST;
            $data['id_paciente'] = $id;
            $app->actualizar($data);
            header('Location: paciente.php?accion=leer&ok=actualizado');
            exit;

        case 'borrar':
            if ($id) { $app->borrar($id); header('Location: paciente.php?accion=leer&ok=borrado'); exit; }
            break;
    }
}

require(__DIR__ . '/views/header.php');

switch ($accion) {
    case 'crear':
        $usuarios    = $app->todosUsuarios();
        require(__DIR__ . '/views/pacientes/formulario_crear.php');
        break;
    case 'actualizar':
        $paciente = $id ? $app->leerUno($id) : null;
        $usuarios = $app->todosUsuarios();
        require(__DIR__ . '/views/pacientes/formulario_actualizar.php');
        break;
    case 'leer':
    default:
        $pacientes = $app->leer();
        $msgs = [
            'creado'      => ['success', 'Paciente registrado correctamente.'],
            'actualizado' => ['success', 'Paciente actualizado correctamente.'],
            'borrado'     => ['danger',  'Paciente eliminado.'],
        ];
        if (isset($_GET['ok'], $msgs[$_GET['ok']])) { [$t,$m] = $msgs[$_GET['ok']]; $app->alerta($t,$m); }
        require(__DIR__ . '/views/pacientes/index.php');
        break;
}

require(__DIR__ . '/views/footer.php');
