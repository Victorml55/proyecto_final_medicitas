<?php

/**
 * auth.php — Sistema de autenticación y control de acceso
 * Incluir al inicio de CADA controlador del admin.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ── Helpers de sesión ──────────────────────────────────────────────── */

function estaAutenticado(): bool {
    return isset($_SESSION['id_usuario'], $_SESSION['email']);
}

function esAdministrador(): bool {
    return in_array('Administrador', $_SESSION['roles'] ?? [], true);
}

function requerirLogin(): void {
    if (!estaAutenticado() || !esAdministrador()) {
        header('Location: login.php');
        exit;
    }
}

function usuarioActual(): array {
    return [
        'id'     => $_SESSION['id_usuario']   ?? null,
        'nombre' => $_SESSION['nombre']        ?? '',
        'email'  => $_SESSION['email']         ?? '',
        'roles'  => $_SESSION['roles']         ?? [],
        'permisos' => $_SESSION['permisos']    ?? [],
    ];
}

/* ── Control de permisos ────────────────────────────────────────────── */

function tienePermiso(string $permiso): bool {
    return in_array($permiso, $_SESSION['permisos'] ?? [], true);
}

function tieneRol(string $rol): bool {
    return in_array($rol, $_SESSION['roles'] ?? [], true);
}

/**
 * Aborta con 403 si el usuario no tiene el permiso requerido.
 * Uso: requerirPermiso('usuarios.ver');
 */
function requerirPermiso(string $permiso): void {
    requerirLogin();
    if (!tienePermiso($permiso)) {
        http_response_code(403);
        include __DIR__ . '/views/403.php';
        exit;
    }
}

/* ── Cerrar sesión ──────────────────────────────────────────────────── */

function cerrarSesion(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    header('Location: login.php');
    exit;
}
