<?php

require_once __DIR__ . '/config.php';

$token = trim($_GET['token'] ?? '');

if (!$token) {
    mostrarPagina('error', 'Enlace inválido', 'El enlace de verificación no es válido.');
}

try {
    $db = conectarDB();

    $stmt = $db->prepare(
        'SELECT * FROM verificaciones_pendientes WHERE token = ? AND expira_en > NOW() LIMIT 1'
    );
    $stmt->execute([$token]);
    $pendiente = $stmt->fetch();

    if (!$pendiente) {
        mostrarPagina('error', 'Enlace expirado', 'El enlace de verificación ha expirado o ya fue usado. Vuelve a registrarte.');
    }

    // Verificar que el email no fue registrado mientras esperaba
    $check = $db->prepare('SELECT id_usuario FROM usuarios WHERE email = ? LIMIT 1');
    $check->execute([$pendiente['email']]);
    if ($check->fetch()) {
        $db->prepare('DELETE FROM verificaciones_pendientes WHERE token = ?')->execute([$token]);
        mostrarPagina('error', 'Correo ya registrado', 'Ya existe una cuenta con ese correo. Puedes iniciar sesión.');
    }

    $db->beginTransaction();
    try {
        $stmtUser = $db->prepare(
            'INSERT INTO usuarios
                (nombre, apellido_paterno, apellido_materno, email, password_hash,
                 telefono, fecha_nacimiento, genero, activo)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, true) RETURNING id_usuario'
        );
        $stmtUser->execute([
            $pendiente['nombre'],
            $pendiente['apellido_paterno'],
            $pendiente['apellido_materno'],
            $pendiente['email'],
            $pendiente['password_hash'],
            $pendiente['telefono'],
            $pendiente['fecha_nacimiento'],
            $pendiente['genero'],
        ]);
        $idUsuario = (int)$stmtUser->fetchColumn();

        // Crear expediente de paciente automáticamente
        $expediente = 'EXP-' . date('Y') . '-' . str_pad($idUsuario, 5, '0', STR_PAD_LEFT);
        $db->prepare(
            'INSERT INTO pacientes (id_usuario, numero_expediente) VALUES (?, ?)'
        )->execute([$idUsuario, $expediente]);

        $db->prepare('DELETE FROM verificaciones_pendientes WHERE token = ?')->execute([$token]);

        $db->commit();
    } catch (PDOException $e) {
        $db->rollBack();
        mostrarPagina('error', 'Error del servidor', 'No se pudo activar la cuenta. Intenta de nuevo más tarde.');
    }

    mostrarPagina('ok', '¡Cuenta activada!', 'Tu cuenta ha sido verificada. Ya puedes iniciar sesión.');

} catch (PDOException $e) {
    mostrarPagina('error', 'Error del servidor', 'No se pudo activar la cuenta. Intenta de nuevo más tarde.');
}

function mostrarPagina(string $tipo, string $titulo, string $mensaje): void {
    header('Content-Type: text/html; charset=utf-8');
    $color  = $tipo === 'ok' ? '#22c55e' : '#ef4444';
    $icono  = $tipo === 'ok' ? '✅' : '❌';
    $login  = '../index.html';
    echo <<<HTML
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{$titulo} | MediCitas</title>
        <style>
            body { margin:0; font-family:Arial,sans-serif; background:#f0f4f8;
                   display:flex; align-items:center; justify-content:center; min-height:100vh; }
            .card { background:#fff; border-radius:12px; padding:48px 40px; text-align:center;
                    box-shadow:0 4px 16px rgba(0,0,0,.1); max-width:420px; width:100%; }
            .icono { font-size:56px; }
            h1 { color:{$color}; margin:16px 0 8px; font-size:22px; }
            p  { color:#555; line-height:1.6; }
            a  { display:inline-block; margin-top:24px; background:#005f99; color:#fff;
                 padding:11px 28px; border-radius:6px; text-decoration:none; font-weight:bold; }
        </style>
    </head>
    <body>
        <div class="card">
            <div class="icono">{$icono}</div>
            <h1>{$titulo}</h1>
            <p>{$mensaje}</p>
            <a href="{$login}">Ir al inicio</a>
        </div>
    </body>
    </html>
    HTML;
    exit;
}
