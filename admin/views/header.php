<?php require_once __DIR__ . '/../auth.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | MediCitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">
    <style>
        body { background-color: #f8fafc; }
        .navbar-brand strong { color: #005f99; }
        .sidebar { min-height: calc(100vh - 56px); background: #fff; border-right: 1px solid #e2e8f0; padding-top: 1rem; }
        .sidebar .nav-link { color: #334155; font-weight: 500; border-radius: 6px; margin-bottom: 2px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #e6f0f9; color: #005f99; }
        .main-content { padding: 2rem; }
        .page-heading { font-size: 1.6rem; font-weight: 700; color: #005f99; margin-bottom: 1.5rem; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-4">
    <a class="navbar-brand" href="dashboard.php">MediCitas <strong>Admin</strong></a>
    <div class="ms-auto d-flex align-items-center gap-2">
        <?php $__u = usuarioActual(); ?>
        <span class="material-symbols-outlined text-secondary" style="font-size:20px">account_circle</span>
        <span class="text-secondary fw-semibold small"><?= htmlspecialchars($__u['nombre']) ?></span>
        <a href="logout.php" class="btn btn-sm btn-outline-danger ms-2">
            <span class="material-symbols-outlined align-middle" style="font-size:16px">logout</span>
            Salir
        </a>
    </div>
</nav>
<div class="container-fluid">
<div class="row">
    <nav class="col-md-2 sidebar d-flex flex-column">
        <?php
        $_cur = basename($_SERVER['PHP_SELF']);
        function _nav($file, $icon, $label) {
            global $_cur;
            $active = ($_cur === $file) ? ' active' : '';
            echo "<li class=\"nav-item\"><a class=\"nav-link{$active}\" href=\"{$file}?accion=leer\">"
               . "<span class=\"material-symbols-outlined align-middle me-1\" style=\"font-size:18px\">{$icon}</span>"
               . " " . htmlspecialchars($label) . "</a></li>\n";
        }
        function _sec($label) {
            echo "<li class=\"nav-item mt-2 px-2\" style=\"font-size:0.7rem;font-weight:700;"
               . "text-transform:uppercase;color:#94a3b8;letter-spacing:.05em;\">{$label}</li>\n";
        }
        ?>
        <ul class="nav flex-column px-2">

            <?php _sec('General'); ?>
            <?php _nav('dashboard.php', 'dashboard', 'Dashboard'); ?>

            <?php _sec('Usuarios'); ?>
            <?php _nav('usuario.php',     'person',           'Usuarios'); ?>
            <?php _nav('usuario_rol.php', 'switch_account',   'Usuario – Rol'); ?>

            <?php _sec('Seguridad'); ?>
            <?php _nav('rol.php',         'manage_accounts',  'Roles'); ?>
            <?php _nav('permiso.php',     'lock',             'Permisos'); ?>
            <?php _nav('rol_permiso.php', 'key',              'Rol – Permiso'); ?>

            <?php _sec('Citas'); ?>
            <?php _nav('cita.php', 'calendar_month', 'Citas'); ?>
            <?php _nav('pdf_cita.php', 'picture_as_pdf', 'PDF Cita'); ?>

            <?php _sec('Personal médico'); ?>
            <?php _nav('medico.php',        'stethoscope',    'Médicos'); ?>
            <?php _nav('paciente.php',      'personal_injury','Pacientes'); ?>
            <?php _nav('horario_medico.php','schedule',       'Horarios'); ?>

            <?php _sec('Operaciones'); ?>
            <?php _nav('pago.php',              'payments',       'Pagos'); ?>
            <?php _nav('receta.php',            'medication',     'Recetas'); ?>
            <?php _nav('valoracion.php',        'star',           'Valoraciones'); ?>
            <?php _nav('comentario_interno.php','comment',        'Comentarios internos'); ?>
            <?php _nav('archivo_adjunto.php',   'attach_file',    'Archivos adjuntos'); ?>
            <?php _nav('dia_no_laborable.php',  'event_busy',     'Días no laborables'); ?>

            <?php _sec('Notificaciones'); ?>
            <?php _nav('plantilla_notificacion.php', 'mark_email_read', 'Plantillas notif.'); ?>

            <?php _sec('Catálogos'); ?>
            <?php _nav('especialidad.php',  'medical_services', 'Especialidades'); ?>
            <?php _nav('consultorio.php',   'door_open',        'Consultorios'); ?>
            <?php _nav('estado_cita.php',   'flag',             'Estados de cita'); ?>
            <?php _nav('configuracion.php', 'settings',         'Configuración'); ?>

        </ul>
    </nav>
    <main class="col-md-10 main-content">
