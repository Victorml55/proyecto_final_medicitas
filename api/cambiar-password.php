<?php
/**
 * POST /api/cambiar-password.php  → cambia la contraseña del usuario en sesión
 */

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/config.php';

if (!isset($_SESSION['id_usuario'])) {
    responder(401, ['error' => 'No autenticado']);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, ['error' => 'Método no permitido']);
}

$body              = leerBody();
$password_actual   = $body['password_actual']   ?? '';
$password_nuevo    = $body['password_nuevo']    ?? '';
$password_confirmar = $body['password_confirmar'] ?? '';

if (!$password_actual || !$password_nuevo || !$password_confirmar) {
    responder(422, ['error' => 'Todos los campos son requeridos.']);
}

if ($password_nuevo !== $password_confirmar) {
    responder(422, ['error' => 'La nueva contraseña y su confirmación no coinciden.']);
}

if (strlen($password_nuevo) < 8) {
    responder(422, ['error' => 'La nueva contraseña debe tener al menos 8 caracteres.']);
}

try {
    $db = conectarDB();

    $stmt = $db->prepare('SELECT password_hash FROM usuarios WHERE id_usuario = ? LIMIT 1');
    $stmt->execute([$_SESSION['id_usuario']]);
    $usuario = $stmt->fetch();

    if (!$usuario || !password_verify($password_actual, $usuario['password_hash'])) {
        responder(401, ['error' => 'La contraseña actual es incorrecta.']);
    }

    $nuevo_hash = password_hash($password_nuevo, PASSWORD_BCRYPT);

    $db->prepare('UPDATE usuarios SET password_hash = ? WHERE id_usuario = ?')
       ->execute([$nuevo_hash, $_SESSION['id_usuario']]);

    responder(200, ['ok' => true]);

} catch (PDOException $e) {
    responder(500, ['error' => 'Error al cambiar la contraseña.']);
}
