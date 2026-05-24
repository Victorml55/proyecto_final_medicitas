<?php

require_once(__DIR__ . '/sistema.class.php');
require_once(__DIR__ . '/auth.php');
requerirLogin();
require_once(__DIR__ . '/models/medico.php');

require_once(__DIR__ . '/helpers/subir_foto.php');

$app    = new Medico();
$id     = isset($_GET['id'])     ? (int)$_GET['id'] : null;
$accion = isset($_GET['accion']) ? $_GET['accion']   : null;
$error  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $accion === 'borrar') {
    switch ($accion) {

        case 'crear':
            $idUsuario  = (int)($_POST['id_usuario']      ?? 0);
            $idEsp      = (int)($_POST['id_especialidad'] ?? 0);
            $cedula     = trim($_POST['cedula_profesional'] ?? '');
            $costo      = $_POST['costo_consulta'] ?? '';
            $duracion   = (int)($_POST['duracion_consulta'] ?? 30);

            if (!$idUsuario || !$idEsp || !$cedula || $costo === '') {
                $error = 'Usuario, especialidad, cédula y costo son obligatorios.'; break;
            }
            if (!is_numeric($costo) || (float)$costo < 0) {
                $error = 'El costo de consulta debe ser un número positivo.'; break;
            }
            if ($duracion <= 0) {
                $error = 'La duración debe ser mayor a 0 minutos.'; break;
            }
            if ($app->cedulaExiste($cedula)) {
                $error = 'Ya existe un médico con esa cédula profesional.'; break;
            }
            if ($app->usuarioYaEsMedico($idUsuario)) {
                $error = 'Ese usuario ya está registrado como médico.'; break;
            }
            $nuevoId = $app->crear($_POST);
            if ($nuevoId) {
                procesarFotoPerfil($idUsuario, $app->db);
            }
            header('Location: medico.php?accion=leer&ok=creado');
            exit;

        case 'actualizar':
            $idUsuario  = (int)($_POST['id_usuario']      ?? 0);
            $idEsp      = (int)($_POST['id_especialidad'] ?? 0);
            $cedula     = trim($_POST['cedula_profesional'] ?? '');
            $costo      = $_POST['costo_consulta'] ?? '';
            $duracion   = (int)($_POST['duracion_consulta'] ?? 30);

            if (!$idUsuario || !$idEsp || !$cedula || $costo === '' || !$id) {
                $error = 'Usuario, especialidad, cédula y costo son obligatorios.'; break;
            }
            if (!is_numeric($costo) || (float)$costo < 0) {
                $error = 'El costo de consulta debe ser un número positivo.'; break;
            }
            if ($duracion <= 0) {
                $error = 'La duración debe ser mayor a 0 minutos.'; break;
            }
            if ($app->cedulaExiste($cedula, $id)) {
                $error = 'Ya existe otro médico con esa cédula profesional.'; break;
            }
            if ($app->usuarioYaEsMedico($idUsuario, $id)) {
                $error = 'Ese usuario ya está registrado como médico.'; break;
            }
            $data = $_POST;
            $data['id_medico'] = $id;
            $app->actualizar($data);
            procesarFotoPerfil($idUsuario, $app->db);
            header('Location: medico.php?accion=leer&ok=actualizado');
            exit;

        case 'borrar':
            if ($id) { $app->borrar($id); header('Location: medico.php?accion=leer&ok=borrado'); exit; }
            break;
    }
}

require(__DIR__ . '/views/header.php');

switch ($accion) {
    case 'crear':
        $usuarios       = $app->todosUsuarios();
        $especialidades = $app->todasEspecialidades();
        $consultorios   = $app->todosConsultorios();
        require(__DIR__ . '/views/medicos/formulario_crear.php');
        break;
    case 'actualizar':
        $medico         = $id ? $app->leerUno($id) : null;
        $usuarios       = $app->todosUsuarios();
        $especialidades = $app->todasEspecialidades();
        $consultorios   = $app->todosConsultorios();
        $fotoActual     = null;
        if ($medico) {
            $stmtFoto = $app->db->prepare('SELECT foto_perfil FROM usuarios WHERE id_usuario = ?');
            $stmtFoto->execute([$medico['id_usuario']]);
            $fotoActual = $stmtFoto->fetchColumn() ?: null;
        }
        require(__DIR__ . '/views/medicos/formulario_actualizar.php');
        break;
    case 'leer':
    default:
        $medicos = $app->leer();
        $msgs = [
            'creado'      => ['success', 'Médico registrado correctamente.'],
            'actualizado' => ['success', 'Médico actualizado correctamente.'],
            'borrado'     => ['danger',  'Médico eliminado.'],
        ];
        if (isset($_GET['ok'], $msgs[$_GET['ok']])) { [$t,$m] = $msgs[$_GET['ok']]; $app->alerta($t,$m); }
        require(__DIR__ . '/views/medicos/index.php');
        break;
}

require(__DIR__ . '/views/footer.php');
