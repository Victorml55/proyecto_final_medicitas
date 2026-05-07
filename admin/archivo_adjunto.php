<?php

require_once(__DIR__ . '/sistema.class.php');
require_once(__DIR__ . '/auth.php');
requerirLogin();
require_once(__DIR__ . '/models/archivo_adjunto.php');

$app    = new ArchivoAdjunto();
$id     = isset($_GET['id'])     ? (int)$_GET['id'] : null;
$accion = isset($_GET['accion']) ? $_GET['accion']   : null;
$error  = null;

$extPermitidas  = ['pdf','jpg','jpeg','png','gif','doc','docx','xls','xlsx','txt'];
$maxSizeBytes   = 5 * 1024 * 1024; // 5 MB

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $accion === 'borrar') {
    switch ($accion) {

        case 'crear':
            $idPaciente = (int)($_POST['id_paciente'] ?? 0);
            if (!$idPaciente) { $error = 'Selecciona un paciente.'; break; }

            if (empty($_FILES['archivo']['name'])) { $error = 'Debes seleccionar un archivo.'; break; }

            $file = $_FILES['archivo'];
            if ($file['error'] !== UPLOAD_ERR_OK) { $error = 'Error al subir el archivo.'; break; }
            if ($file['size'] > $maxSizeBytes)     { $error = 'El archivo supera el límite de 5 MB.'; break; }

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $extPermitidas, true)) {
                $error = 'Tipo de archivo no permitido. Permitidos: ' . implode(', ', $extPermitidas);
                break;
            }

            try {
                $app->crear($_POST, $file);
                header('Location: archivo_adjunto.php?accion=leer&ok=creado');
                exit;
            } catch (RuntimeException $e) {
                $error = $e->getMessage();
            }
            break;

        case 'actualizar':
            $idPaciente = (int)($_POST['id_paciente'] ?? 0);
            if (!$idPaciente || !$id) { $error = 'Datos incompletos.'; break; }
            $data = $_POST;
            $data['id_archivo'] = $id;
            $app->actualizar($data);
            header('Location: archivo_adjunto.php?accion=leer&ok=actualizado');
            exit;

        case 'borrar':
            if ($id) {
                $app->borrar($id);
                header('Location: archivo_adjunto.php?accion=leer&ok=borrado');
                exit;
            }
            break;
    }
}

require(__DIR__ . '/views/header.php');

switch ($accion) {
    case 'crear':
        $pacientes = $app->todosPacientes();
        require(__DIR__ . '/views/archivos_adjuntos/formulario_crear.php');
        break;
    case 'actualizar':
        $archivo   = $id ? $app->leerUno($id) : null;
        $pacientes = $app->todosPacientes();
        require(__DIR__ . '/views/archivos_adjuntos/formulario_actualizar.php');
        break;
    case 'leer':
    default:
        $archivos = $app->leer();
        $msgs = [
            'creado'      => ['success', 'Archivo subido correctamente.'],
            'actualizado' => ['success', 'Archivo actualizado.'],
            'borrado'     => ['danger',  'Archivo eliminado.'],
        ];
        if (isset($_GET['ok'], $msgs[$_GET['ok']])) { [$t,$m] = $msgs[$_GET['ok']]; $app->alerta($t,$m); }
        require(__DIR__ . '/views/archivos_adjuntos/index.php');
        break;
}

require(__DIR__ . '/views/footer.php');
