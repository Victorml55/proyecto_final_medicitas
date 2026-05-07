<?php

require_once(__DIR__ . '/sistema.class.php');
require_once(__DIR__ . '/auth.php');
requerirLogin();
require_once(__DIR__ . '/models/valoracion.php');

$app    = new Valoracion();
$id     = isset($_GET['id'])     ? (int)$_GET['id'] : null;
$accion = isset($_GET['accion']) ? $_GET['accion']   : null;
$error  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $accion === 'borrar') {
    switch ($accion) {

        case 'crear':
            $idCita       = (int)($_POST['id_cita']       ?? 0);
            $idPaciente   = (int)($_POST['id_paciente']   ?? 0);
            $idMedico     = (int)($_POST['id_medico']     ?? 0);
            $calificacion = (int)($_POST['calificacion']  ?? 0);

            if (!$idCita || !$idPaciente || !$idMedico) {
                $error = 'Debes seleccionar una cita.'; break;
            }
            if ($calificacion < 1 || $calificacion > 5) {
                $error = 'La calificación debe estar entre 1 y 5.'; break;
            }
            if ($app->citaExisteValoracion($idCita)) {
                $error = 'Esa cita ya tiene una valoración registrada.'; break;
            }
            $app->crear($_POST);
            header('Location: valoracion.php?accion=leer&ok=creado');
            exit;

        case 'actualizar':
            $calificacion = (int)($_POST['calificacion'] ?? 0);
            if ($calificacion < 1 || $calificacion > 5 || !$id) {
                $error = 'La calificación debe estar entre 1 y 5.'; break;
            }
            $data = $_POST;
            $data['id_valoracion'] = $id;
            $app->actualizar($data);
            header('Location: valoracion.php?accion=leer&ok=actualizado');
            exit;

        case 'borrar':
            if ($id) {
                $app->borrar($id);
                header('Location: valoracion.php?accion=leer&ok=borrado');
                exit;
            }
            break;
    }
}

require(__DIR__ . '/views/header.php');

switch ($accion) {
    case 'crear':
        $citas = $app->todasCitas();
        require(__DIR__ . '/views/valoraciones/formulario_crear.php');
        break;
    case 'actualizar':
        $valoracion = $id ? $app->leerUno($id) : null;
        require(__DIR__ . '/views/valoraciones/formulario_actualizar.php');
        break;
    case 'leer':
    default:
        $valoraciones = $app->leer();
        $msgs = [
            'creado'      => ['success', 'Valoración registrada.'],
            'actualizado' => ['success', 'Valoración actualizada.'],
            'borrado'     => ['danger',  'Valoración eliminada.'],
        ];
        if (isset($_GET['ok'], $msgs[$_GET['ok']])) { [$t,$m] = $msgs[$_GET['ok']]; $app->alerta($t,$m); }
        require(__DIR__ . '/views/valoraciones/index.php');
        break;
}

require(__DIR__ . '/views/footer.php');
