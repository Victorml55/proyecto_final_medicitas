<?php

require_once(__DIR__ . '/sistema.class.php');
require_once(__DIR__ . '/auth.php');
requerirLogin();
require_once(__DIR__ . '/models/horario_medico.php');

$app    = new HorarioMedico();
$id     = isset($_GET['id'])     ? (int)$_GET['id'] : null;
$accion = isset($_GET['accion']) ? $_GET['accion']   : null;
$error  = null;

$diasValidos = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $accion === 'borrar') {
    switch ($accion) {

        case 'crear':
            $idMedico  = (int)($_POST['id_medico'] ?? 0);
            $dia       = $_POST['dia_semana']  ?? '';
            $horaIni   = $_POST['hora_inicio'] ?? '';
            $horaFin   = $_POST['hora_fin']    ?? '';

            if (!$idMedico || !$dia || !$horaIni || !$horaFin) {
                $error = 'Médico, día, hora inicio y hora fin son obligatorios.'; break;
            }
            if (!in_array($dia, $diasValidos, true)) { $error = 'Día de la semana inválido.'; break; }
            if ($horaFin <= $horaIni) { $error = 'La hora de fin debe ser mayor a la hora de inicio.'; break; }
            $app->crear($_POST);
            header('Location: horario_medico.php?accion=leer&ok=creado');
            exit;

        case 'actualizar':
            $idMedico  = (int)($_POST['id_medico'] ?? 0);
            $dia       = $_POST['dia_semana']  ?? '';
            $horaIni   = $_POST['hora_inicio'] ?? '';
            $horaFin   = $_POST['hora_fin']    ?? '';

            if (!$idMedico || !$dia || !$horaIni || !$horaFin || !$id) {
                $error = 'Médico, día, hora inicio y hora fin son obligatorios.'; break;
            }
            if (!in_array($dia, $diasValidos, true)) { $error = 'Día de la semana inválido.'; break; }
            if ($horaFin <= $horaIni) { $error = 'La hora de fin debe ser mayor a la hora de inicio.'; break; }
            $data = $_POST;
            $data['id_horario'] = $id;
            $app->actualizar($data);
            header('Location: horario_medico.php?accion=leer&ok=actualizado');
            exit;

        case 'borrar':
            if ($id) { $app->borrar($id); header('Location: horario_medico.php?accion=leer&ok=borrado'); exit; }
            break;
    }
}

require(__DIR__ . '/views/header.php');

switch ($accion) {
    case 'crear':
        $medicos      = $app->todosMedicos();
        $consultorios = $app->todosConsultorios();
        require(__DIR__ . '/views/horarios_medicos/formulario_crear.php');
        break;
    case 'actualizar':
        $horario      = $id ? $app->leerUno($id) : null;
        $medicos      = $app->todosMedicos();
        $consultorios = $app->todosConsultorios();
        require(__DIR__ . '/views/horarios_medicos/formulario_actualizar.php');
        break;
    case 'leer':
    default:
        $horarios = $app->leer();
        $msgs = [
            'creado'      => ['success', 'Horario registrado correctamente.'],
            'actualizado' => ['success', 'Horario actualizado correctamente.'],
            'borrado'     => ['danger',  'Horario eliminado.'],
        ];
        if (isset($_GET['ok'], $msgs[$_GET['ok']])) { [$t,$m] = $msgs[$_GET['ok']]; $app->alerta($t,$m); }
        require(__DIR__ . '/views/horarios_medicos/index.php');
        break;
}

require(__DIR__ . '/views/footer.php');
