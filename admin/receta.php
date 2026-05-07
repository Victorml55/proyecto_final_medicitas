<?php

require_once(__DIR__ . '/sistema.class.php');
require_once(__DIR__ . '/auth.php');
requerirLogin();
require_once(__DIR__ . '/models/receta.php');

$app    = new Receta();
$id     = isset($_GET['id'])     ? (int)$_GET['id'] : null;
$accion = isset($_GET['accion']) ? $_GET['accion']   : null;
$error  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $accion === 'borrar') {
    switch ($accion) {

        case 'crear':
            $idCita = (int)($_POST['id_cita'] ?? 0);
            if (!$idCita) { $error = 'Debes seleccionar una cita.'; break; }

            // Validar que haya al menos un medicamento con nombre y dosis
            $nombres = $_POST['nombre_medicamento'] ?? [];
            $dosis   = $_POST['dosis']              ?? [];
            $frecuencias = $_POST['frecuencia']     ?? [];
            $hayMed = false;
            foreach ($nombres as $i => $nom) {
                if (trim($nom) !== '' && trim($dosis[$i] ?? '') !== '' && trim($frecuencias[$i] ?? '') !== '') {
                    $hayMed = true; break;
                }
            }
            if (!$hayMed) { $error = 'Agrega al menos un medicamento con nombre, dosis y frecuencia.'; break; }

            $idReceta = $app->crear($_POST);
            foreach ($nombres as $i => $nom) {
                if (trim($nom) === '') continue;
                $app->crearMedicamento($idReceta, [
                    'nombre_medicamento' => $nom,
                    'presentacion'       => $_POST['presentacion'][$i] ?? '',
                    'dosis'              => $dosis[$i] ?? '',
                    'frecuencia'         => $frecuencias[$i] ?? '',
                    'duracion'           => $_POST['duracion'][$i] ?? '',
                    'indicaciones'       => $_POST['indicaciones_med'][$i] ?? '',
                ]);
            }
            header('Location: receta.php?accion=leer&ok=creado');
            exit;

        case 'actualizar':
            $idCita = (int)($_POST['id_cita'] ?? 0);
            if (!$idCita || !$id) { $error = 'Debes seleccionar una cita.'; break; }

            $nombres = $_POST['nombre_medicamento'] ?? [];
            $dosis   = $_POST['dosis']              ?? [];
            $frecuencias = $_POST['frecuencia']     ?? [];
            $hayMed = false;
            foreach ($nombres as $i => $nom) {
                if (trim($nom) !== '' && trim($dosis[$i] ?? '') !== '' && trim($frecuencias[$i] ?? '') !== '') {
                    $hayMed = true; break;
                }
            }
            if (!$hayMed) { $error = 'Agrega al menos un medicamento con nombre, dosis y frecuencia.'; break; }

            $data = $_POST;
            $data['id_receta'] = $id;
            $app->actualizar($data);
            $app->borrarMedicamentos($id);
            foreach ($nombres as $i => $nom) {
                if (trim($nom) === '') continue;
                $app->crearMedicamento($id, [
                    'nombre_medicamento' => $nom,
                    'presentacion'       => $_POST['presentacion'][$i] ?? '',
                    'dosis'              => $dosis[$i] ?? '',
                    'frecuencia'         => $frecuencias[$i] ?? '',
                    'duracion'           => $_POST['duracion'][$i] ?? '',
                    'indicaciones'       => $_POST['indicaciones_med'][$i] ?? '',
                ]);
            }
            header('Location: receta.php?accion=leer&ok=actualizado');
            exit;

        case 'borrar':
            if ($id) { $app->borrar($id); header('Location: receta.php?accion=leer&ok=borrado'); exit; }
            break;
    }
}

require(__DIR__ . '/views/header.php');

switch ($accion) {
    case 'crear':
        $citas = $app->todasCitas();
        require(__DIR__ . '/views/recetas/formulario_crear.php');
        break;
    case 'actualizar':
        $receta       = $id ? $app->leerUno($id) : null;
        $medicamentos = $id ? $app->leerMedicamentos($id) : [];
        $citas        = $app->todasCitas();
        require(__DIR__ . '/views/recetas/formulario_actualizar.php');
        break;
    case 'leer':
    default:
        $recetas = $app->leer();
        $msgs = [
            'creado'      => ['success', 'Receta creada correctamente.'],
            'actualizado' => ['success', 'Receta actualizada correctamente.'],
            'borrado'     => ['danger',  'Receta eliminada.'],
        ];
        if (isset($_GET['ok'], $msgs[$_GET['ok']])) { [$t,$m] = $msgs[$_GET['ok']]; $app->alerta($t,$m); }
        require(__DIR__ . '/views/recetas/index.php');
        break;
}

require(__DIR__ . '/views/footer.php');
