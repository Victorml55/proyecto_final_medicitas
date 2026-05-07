<?php

require_once __DIR__ . '/config.php';

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

    $hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $db->prepare(
        'INSERT INTO usuarios
            (nombre, apellido_paterno, apellido_materno, email, password_hash,
             telefono, fecha_nacimiento, genero, activo)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, true)'
    );
    $stmt->execute([
        $nombre,
        $apellido_paterno,
        $apellido_materno,
        $email,
        $hash,
        $telefono,
        $fecha_nacimiento,
        $genero,
    ]);

    responder(201, ['success' => true, 'mensaje' => 'Cuenta creada exitosamente.']);

} catch (PDOException $e) {
    responder(500, ['error' => 'Error al registrar la cuenta. Intenta de nuevo.']);
}
