<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../admin/services/MailService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, ['error' => 'Método no permitido.']);
}

$data  = leerBody();
$email = trim($data['email'] ?? '');

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    responder(400, ['error' => 'Ingresa un correo válido.']);
}

try {
    $db = conectarDB();

    $stmt = $db->prepare('SELECT id_usuario, nombre, apellido_paterno FROM usuarios WHERE email = ? AND activo = true LIMIT 1');
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    // Respuesta genérica para no revelar si el correo existe
    if (!$usuario) {
        responder(200, ['success' => true, 'mensaje' => 'Si ese correo está registrado, recibirás un enlace para restablecer tu contraseña.']);
    }

    // Invalidar tokens anteriores del usuario
    $db->prepare('DELETE FROM tokens_recuperacion WHERE id_usuario = ?')->execute([$usuario['id_usuario']]);

    $token = bin2hex(random_bytes(32));

    $db->prepare('INSERT INTO tokens_recuperacion (id_usuario, token) VALUES (?, ?)')->execute([$usuario['id_usuario'], $token]);

    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $link      = "$protocolo://$host/recuperar-contrasena.html?token=$token";

    $nombre = "{$usuario['nombre']} {$usuario['apellido_paterno']}";
    MailService::recuperarContrasena($email, $nombre, $link);

    responder(200, ['success' => true, 'mensaje' => 'Si ese correo está registrado, recibirás un enlace para restablecer tu contraseña.']);

} catch (PDOException $e) {
    responder(500, ['error' => 'Error del servidor. Intenta de nuevo.']);
}
