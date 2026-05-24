<?php

require_once(__DIR__ . '/sistema.class.php');
require_once(__DIR__ . '/auth.php');
requerirLogin();
require_once(__DIR__ . '/models/usuario.php');
require_once(__DIR__ . '/helpers/subir_foto.php');
require_once(__DIR__ . '/models/usuario_rol.php');
require_once(__DIR__ . '/models/medico.php');
require_once(__DIR__ . '/models/horario_medico.php');
require_once(__DIR__ . '/services/MailService.php');

$app        = new Usuario();
$appRol     = new UsuarioRol();
$appMedico  = new Medico();
$appHorario = new HorarioMedico();
$id         = isset($_GET['id'])     ? (int)$_GET['id'] : null;
$accion     = isset($_GET['accion']) ? $_GET['accion']   : null;
$error      = null;

$generosValidos = ['M', 'F', 'Otro'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $accion === 'borrar') {
    switch ($accion) {
        case 'crear':
            $nombre   = trim($_POST['nombre']          ?? '');
            $apPat    = trim($_POST['apellido_paterno'] ?? '');
            $email    = trim($_POST['email']            ?? '');
            $password = trim($_POST['password']         ?? '');
            $genero   = $_POST['genero']                ?? '';
            $fnac     = trim($_POST['fecha_nacimiento'] ?? '');

            if (!$nombre || !$apPat || !$email || !$password) {
                $error = 'Nombre, apellido paterno, email y contraseña son obligatorios.';
                break;
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'El email no tiene un formato válido.';
                break;
            }
            if (strlen($password) < 8) {
                $error = 'La contraseña debe tener al menos 8 caracteres.';
                break;
            }
            if ($genero && !in_array($genero, $generosValidos, true)) {
                $error = 'El género seleccionado no es válido.';
                break;
            }
            if ($fnac && !DateTime::createFromFormat('Y-m-d', $fnac)) {
                $error = 'La fecha de nacimiento no es válida.';
                break;
            }
            $idRol = (int)($_POST['id_rol'] ?? 0);
            if (!$idRol) {
                $error = 'Debes seleccionar un rol para el usuario.';
                break;
            }
            if ($app->emailExiste($email)) {
                $error = 'Ya existe un usuario con ese email.';
                break;
            }
            $nuevoId = $app->crear($_POST);
            $appRol->crear(['id_usuario' => $nuevoId, 'id_rol' => $idRol]);

            // Si el rol seleccionado es "Médico", crear perfil en medicos
            $rolSeleccionado = '';
            foreach ($appRol->todosRoles() as $r) {
                if ((int)$r['id_rol'] === $idRol) { $rolSeleccionado = strtolower($r['nombre_rol']); break; }
            }
            if (str_contains($rolSeleccionado, 'médico') || str_contains($rolSeleccionado, 'medico')) {
                $cedula = trim($_POST['cedula_profesional'] ?? '');
                $idEsp  = (int)($_POST['id_especialidad']  ?? 0);
                $costo  = trim($_POST['costo_consulta']    ?? '');
                if (!$cedula || !$idEsp || $costo === '') {
                    $error = 'Para el rol Médico debes completar: cédula, especialidad y costo de consulta.';
                    break;
                }
                $idMedico = $appMedico->crear(array_merge($_POST, ['id_usuario' => $nuevoId]));

                // Insertar horarios marcados
                $horarios = $_POST['horario'] ?? [];
                foreach ($horarios as $dia => $datos) {
                    if (empty($datos['hora_inicio']) || empty($datos['hora_fin'])) continue;
                    $appHorario->crear([
                        'id_medico'      => $idMedico,
                        'id_consultorio' => null,
                        'dia_semana'     => $dia,
                        'hora_inicio'    => $datos['hora_inicio'],
                        'hora_fin'       => $datos['hora_fin'],
                        'activo'         => true,
                    ]);
                }
            }

            $passwordPlano  = trim($_POST['password']);
            $nombreCompleto = trim($_POST['nombre']) . ' ' . trim($_POST['apellido_paterno']);
            MailService::bienvenidaUsuario($email, $nombreCompleto, $passwordPlano);

            header('Location: usuario.php?accion=leer&ok=creado');
            exit;

        case 'actualizar':
            $nombre   = trim($_POST['nombre']          ?? '');
            $apPat    = trim($_POST['apellido_paterno'] ?? '');
            $email    = trim($_POST['email']            ?? '');
            $password = trim($_POST['password']         ?? '');
            $genero   = $_POST['genero']                ?? '';
            $fnac     = trim($_POST['fecha_nacimiento'] ?? '');

            if (!$nombre || !$apPat || !$email || !$id) {
                $error = 'Nombre, apellido paterno y email son obligatorios.';
                break;
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'El email no tiene un formato válido.';
                break;
            }
            if ($password && strlen($password) < 8) {
                $error = 'La contraseña debe tener al menos 8 caracteres.';
                break;
            }
            if ($genero && !in_array($genero, $generosValidos, true)) {
                $error = 'El género seleccionado no es válido.';
                break;
            }
            if ($fnac && !DateTime::createFromFormat('Y-m-d', $fnac)) {
                $error = 'La fecha de nacimiento no es válida.';
                break;
            }
            if ($app->emailExiste($email, $id)) {
                $error = 'Ya existe otro usuario con ese email.';
                break;
            }
            $data = $_POST;
            $data['id_usuario'] = $id;
            $app->actualizar($data);
            procesarFotoPerfil($id, $app->db);
            header('Location: usuario.php?accion=leer&ok=actualizado');
            exit;

        case 'borrar':
            if ($id) {
                $app->borrar($id);
                header('Location: usuario.php?accion=leer&ok=borrado');
                exit;
            }
            break;
    }
}

require(__DIR__ . '/views/header.php');

switch ($accion) {
    case 'crear':
        $roles         = $appRol->todosRoles();
        $especialidades = $appMedico->todasEspecialidades();
        require(__DIR__ . '/views/usuarios/formulario_crear.php');
        break;
    case 'actualizar':
        $usuario    = $id ? $app->leerUno($id) : null;
        $fotoActual = $usuario['foto_perfil'] ?? null;
        require(__DIR__ . '/views/usuarios/formulario_actualizar.php');
        break;
    case 'leer':
    default:
        $usuarios = $app->leer();
        $msgs = ['creado' => ['success','Usuario creado.'], 'actualizado' => ['success','Usuario actualizado.'], 'borrado' => ['danger','Usuario eliminado.']];
        if (isset($_GET['ok'], $msgs[$_GET['ok']])) { [$t,$m] = $msgs[$_GET['ok']]; $app->alerta($t,$m); }
        require(__DIR__ . '/views/usuarios/index.php');
        break;
}

require(__DIR__ . '/views/footer.php');
