<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../admin/services/MailService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, ['error' => 'Método no permitido.']);
}

$data = leerBody();

$nombre           = trim($data['nombre']           ?? '');
$apellido_paterno = trim($data['apellido_paterno'] ?? '');
$apellido_materno = trim($data['apellido_materno'] ?? '') ?: null;
$email            = trim($data['email']            ?? '');
$password         = $data['password']              ?? '';
$telefono         = trim($data['telefono']         ?? '') ?: null;
$fecha_nacimiento = trim($data['fecha_nacimiento'] ?? '') ?: null;
$genero           = trim($data['genero']           ?? '') ?: null;

if (!$nombre || !$apellido_paterno || !$email || !$password) {
    responder(400, ['error' => 'Nombre, apellido paterno, correo y contraseña son obligatorios.']);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    responder(400, ['error' => 'El correo no tiene un formato válido.']);
}

if (strlen($password) < 8) {
    responder(400, ['error' => 'La contraseña debe tener al menos 8 caracteres.']);
}

if ($fecha_nacimiento && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_nacimiento)) {
    responder(400, ['error' => 'Formato de fecha inválido.']);
}

try {
    $db = conectarDB();

    $check = $db->prepare('SELECT id_usuario FROM usuarios WHERE email = ? LIMIT 1');
    $check->execute([$email]);
    if ($check->fetch()) {
        responder(409, ['error' => 'Ya existe una cuenta registrada con ese correo.']);
    }

    // Limpiar verificaciones previas del mismo email o expiradas
    $db->prepare('DELETE FROM verificaciones_pendientes WHERE email = ? OR expira_en < NOW()')
       ->execute([$email]);

    $token = bin2hex(random_bytes(32));
    $hash  = password_hash($password, PASSWORD_BCRYPT);

    $db->prepare(
        'INSERT INTO verificaciones_pendientes
            (token, nombre, apellido_paterno, apellido_materno, email, password_hash,
             telefono, fecha_nacimiento, genero)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    )->execute([$token, $nombre, $apellido_paterno, $apellido_materno, $email, $hash,
                $telefono, $fecha_nacimiento, $genero]);

    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $link      = "$protocolo://$host/api/verificar.php?token=$token";

    MailService::confirmacionRegistro($email, "$nombre $apellido_paterno", $link);

    responder(200, [
        'success' => true,
        'mensaje' => 'Te enviamos un correo de confirmación. Revisa tu bandeja y haz clic en el enlace para activar tu cuenta.',
    ]);

} catch (PDOException $e) {
    responder(500, ['error' => 'Error al registrar la cuenta. Intenta de nuevo.']);
}
