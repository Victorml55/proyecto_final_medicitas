<?php

require_once(__DIR__ . '/sistema.class.php');
require_once(__DIR__ . '/auth.php');
requerirLogin();
require_once(__DIR__ . '/models/cita.php');
require_once(__DIR__ . '/services/MailService.php');

$app    = new Cita();
$id     = isset($_GET['id'])     ? (int)$_GET['id'] : null;
$accion = isset($_GET['accion']) ? $_GET['accion']   : null;
$error  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $accion === 'borrar') {
    switch ($accion) {
        case 'crear':
            $fecha      = trim($_POST['fecha_cita']   ?? '');
            $horaIni    = trim($_POST['hora_inicio']  ?? '');
            $horaFin    = trim($_POST['hora_fin']     ?? '');
            $idPaciente = (int)($_POST['id_paciente'] ?? 0);
            $idMedico   = (int)($_POST['id_medico']   ?? 0);
            $idEstado   = (int)($_POST['id_estado']   ?? 0);

            if (!$fecha || !$horaIni || !$horaFin || !$idPaciente || !$idMedico || !$idEstado) {
                $error = 'Paciente, médico, estado, fecha y horas son obligatorios.';
                break;
            }
            if (!DateTime::createFromFormat('Y-m-d', $fecha)) {
                $error = 'La fecha no es válida.';
                break;
            }
            if ($horaFin <= $horaIni) {
                $error = 'La hora de fin debe ser mayor que la hora de inicio.';
                break;
            }
            $app->crear($_POST);

            // ── Sprint 06: Correo de confirmación de cita con PHPMailer ──
            $datosPaciente = $app->datosPacienteParaCorreo((int)$_POST['id_paciente']);
            $datosMedico   = $app->datosMedicoParaCorreo((int)$_POST['id_medico']);
            if ($datosPaciente) {
                MailService::confirmacionCita(
                    $datosPaciente['email'],
                    $datosPaciente['nombre'],
                    [
                        'fecha'  => $fecha,
                        'hora'   => $horaIni,
                        'medico' => $datosMedico['nombre'] ?? 'N/A',
                        'motivo' => trim($_POST['motivo_consulta'] ?? 'Consulta general'),
                    ]
                );
            }
            // ─────────────────────────────────────────────────────────────

            header('Location: cita.php?accion=leer&ok=creado');
            exit;

        case 'actualizar':
            $fecha      = trim($_POST['fecha_cita']   ?? '');
            $horaIni    = trim($_POST['hora_inicio']  ?? '');
            $horaFin    = trim($_POST['hora_fin']     ?? '');
            $idPaciente = (int)($_POST['id_paciente'] ?? 0);
            $idMedico   = (int)($_POST['id_medico']   ?? 0);
            $idEstado   = (int)($_POST['id_estado']   ?? 0);

            if (!$fecha || !$horaIni || !$horaFin || !$idPaciente || !$idMedico || !$idEstado || !$id) {
                $error = 'Paciente, médico, estado, fecha y horas son obligatorios.';
                break;
            }
            if (!DateTime::createFromFormat('Y-m-d', $fecha)) {
                $error = 'La fecha no es válida.';
                break;
            }
            if ($horaFin <= $horaIni) {
                $error = 'La hora de fin debe ser mayor que la hora de inicio.';
                break;
            }
            $data = $_POST;
            $data['id_cita'] = $id;
            $app->actualizar($data);
            header('Location: cita.php?accion=leer&ok=actualizado');
            exit;

        case 'borrar':
            if ($id) {
                $app->borrar($id);
                header('Location: cita.php?accion=leer&ok=borrado');
                exit;
            }
            break;
    }
}

require(__DIR__ . '/views/header.php');

switch ($accion) {
    case 'crear':
        $pacientes    = $app->todosPacientes();
        $medicos      = $app->todosMedicos();
        $consultorios = $app->todosConsultorios();
        $estados      = $app->todosEstados();
        require(__DIR__ . '/views/citas/formulario_crear.php');
        break;

    case 'actualizar':
        $cita         = $id ? $app->leerUno($id) : null;
        $pacientes    = $app->todosPacientes();
        $medicos      = $app->todosMedicos();
        $consultorios = $app->todosConsultorios();
        $estados      = $app->todosEstados();
        require(__DIR__ . '/views/citas/formulario_actualizar.php');
        break;

    case 'leer':
    default:
        $citas = $app->leer();
        $msgs  = [
            'creado'      => ['success', 'Cita creada correctamente.'],
            'actualizado' => ['success', 'Cita actualizada correctamente.'],
            'borrado'     => ['danger',  'Cita eliminada.'],
        ];
        if (isset($_GET['ok'], $msgs[$_GET['ok']])) {
            [$t, $m] = $msgs[$_GET['ok']];
            $app->alerta($t, $m);
        }
        require(__DIR__ . '/views/citas/index.php');
        break;
}

require(__DIR__ . '/views/footer.php');
