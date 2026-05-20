<?php

require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, ['error' => 'Método no permitido.']);
}

$data     = leerBody();
$token    = trim($data['token']    ?? '');
$password = trim($data['password'] ?? '');

if (!$token || !$password) {
    responder(400, ['error' => 'Token y contraseña son requeridos.']);
}

if (strlen($password) < 8) {
    responder(400, ['error' => 'La contraseña debe tener al menos 8 caracteres.']);
}

try {
    $db = conectarDB();

    $stmt = $db->prepare(
        'SELECT id_usuario FROM tokens_recuperacion
         WHERE token = ? AND usado = FALSE AND expira_en > NOW() LIMIT 1'
    );
    $stmt->execute([$token]);
    $registro = $stmt->fetch();

    if (!$registro) {
        responder(400, ['error' => 'El enlace no es válido o ha expirado.']);
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);

    $db->prepare('UPDATE usuarios SET password_hash = ? WHERE id_usuario = ?')
       ->execute([$hash, $registro['id_usuario']]);

    $db->prepare('UPDATE tokens_recuperacion SET usado = TRUE WHERE token = ?')
       ->execute([$token]);

    responder(200, ['success' => true, 'mensaje' => 'Contraseña actualizada correctamente.']);

} catch (PDOException $e) {
    responder(500, ['error' => 'Error del servidor. Intenta de nuevo.']);
}
