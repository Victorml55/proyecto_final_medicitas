<?php

require_once __DIR__ . '/sistema.class.php';
require_once __DIR__ . '/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Si ya está logueado como administrador, redirigir al panel
if (estaAutenticado() && esAdministrador()) {
    header('Location: dashboard.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        $error = 'Ingresa tu correo y contraseña.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo no tiene un formato válido.';
    } else {
        $app  = new Sistema();
        $app->conectar();

        // Buscar usuario activo
        $stmt = $app->db->prepare(
            'SELECT id_usuario, nombre, email, password_hash
             FROM usuarios
             WHERE email = ? AND activo = true
             LIMIT 1'
        );
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password_hash'])) {

            // Cargar roles del usuario
            $stmtRoles = $app->db->prepare(
                'SELECT r.nombre_rol
                 FROM roles r
                 JOIN usuario_roles ur ON ur.id_rol = r.id_rol
                 WHERE ur.id_usuario = ?'
            );
            $stmtRoles->execute([$usuario['id_usuario']]);
            $roles = $stmtRoles->fetchAll(PDO::FETCH_COLUMN);

            if (!in_array('Administrador', $roles, true)) {
                $error = 'No tienes permiso para acceder al panel de administración.';
            } else {
                // Cargar permisos del usuario (a través de sus roles)
                $stmtPermisos = $app->db->prepare(
                    'SELECT DISTINCT p.nombre_permiso
                     FROM permisos p
                     JOIN rol_permisos rp ON rp.id_permiso = p.id_permiso
                     JOIN usuario_roles ur ON ur.id_rol = rp.id_rol
                     WHERE ur.id_usuario = ?'
                );
                $stmtPermisos->execute([$usuario['id_usuario']]);
                $permisos = $stmtPermisos->fetchAll(PDO::FETCH_COLUMN);

                // Regenerar ID de sesión para prevenir session fixation
                session_regenerate_id(true);

                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nombre']     = $usuario['nombre'];
                $_SESSION['email']      = $usuario['email'];
                $_SESSION['roles']      = $roles;
                $_SESSION['permisos']   = $permisos;

                header('Location: dashboard.php');
                exit;
            }
        } else {
            // Mensaje genérico para no revelar si el email existe
            $error = 'Correo o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | MediCitas Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">
    <style>
        body { background: #f0f6fb; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-card { width: 100%; max-width: 400px; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,95,153,.12); }
        .login-card .card-header { background: #005f99; color: #fff; border-radius: 12px 12px 0 0; padding: 1.5rem; }
        .btn-primary { background: #005f99; border-color: #005f99; }
        .btn-primary:hover { background: #004c7f; border-color: #004c7f; }
    </style>
</head>
<body>
<div class="card login-card">
    <div class="card-header text-center">
        <span class="material-symbols-outlined" style="font-size:2rem;">local_hospital</span>
        <h4 class="mb-0 mt-1 fw-bold">MediCitas Admin</h4>
        <small class="opacity-75">Panel de administración</small>
    </div>
    <div class="card-body p-4">
        <?php if ($error): ?>
            <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" novalidate>
            <div class="mb-3">
                <label class="form-label fw-semibold">Correo electrónico</label>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="admin@medicitas.com" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Contraseña</label>
                <input type="password" name="password" class="form-control"
                       placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-semibold">
                <span class="material-symbols-outlined align-middle" style="font-size:18px">login</span>
                Iniciar sesión
            </button>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
