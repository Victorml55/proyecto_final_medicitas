<?php
/**
 * PUT /api/actualizar-perfil.php  → actualiza datos personales y médicos del usuario en sesión
 */

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/config.php';

if (!isset($_SESSION['id_usuario'])) {
    responder(401, ['error' => 'No autenticado']);
}

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    responder(405, ['error' => 'Método no permitido']);
}

$body = leerBody();

$nombre             = trim($body['nombre']             ?? '');
$apellido_paterno   = trim($body['apellido_paterno']   ?? '');
$apellido_materno   = trim($body['apellido_materno']   ?? '');
$telefono           = trim($body['telefono']           ?? '');
$fecha_nacimiento   = trim($body['fecha_nacimiento']   ?? '');
$genero             = trim($body['genero']             ?? '');
$tipo_sangre        = trim($body['tipo_sangre']        ?? '');
$alergias           = trim($body['alergias']           ?? '');
$enfermedades       = trim($body['enfermedades_cronicas'] ?? '');
$emergencia_nombre  = trim($body['contacto_emergencia_nombre']    ?? '');
$emergencia_tel     = trim($body['contacto_emergencia_telefono']  ?? '');

if (!$nombre || !$apellido_paterno) {
    responder(422, ['error' => 'El nombre y apellido paterno son requeridos.']);
}

$generos_validos = ['M', 'F', 'Otro'];
if ($genero && !in_array($genero, $generos_validos)) {
    responder(422, ['error' => 'Género no válido.']);
}

$sangres_validas = ['A+','A-','B+','B-','AB+','AB-','O+','O-',''];
if ($tipo_sangre && !in_array($tipo_sangre, $sangres_validas)) {
    responder(422, ['error' => 'Tipo de sangre no válido.']);
}

try {
    $db = conectarDB();

    $db->prepare(
        "UPDATE usuarios SET
            nombre           = ?,
            apellido_paterno = ?,
            apellido_materno = ?,
            telefono         = ?,
            fecha_nacimiento = ?,
            genero           = ?
         WHERE id_usuario = ?"
    )->execute([
        $nombre,
        $apellido_paterno,
        $apellido_materno ?: null,
        $telefono         ?: null,
        $fecha_nacimiento ?: null,
        $genero           ?: null,
        $_SESSION['id_usuario'],
    ]);

    $stmt = $db->prepare('SELECT id_paciente FROM pacientes WHERE id_usuario = ? LIMIT 1');
    $stmt->execute([$_SESSION['id_usuario']]);
    $paciente = $stmt->fetch();

    if ($paciente) {
        $db->prepare(
            "UPDATE pacientes SET
                tipo_sangre                  = ?,
                alergias                     = ?,
                enfermedades_cronicas        = ?,
                contacto_emergencia_nombre   = ?,
                contacto_emergencia_telefono = ?
             WHERE id_paciente = ?"
        )->execute([
            $tipo_sangre       ?: null,
            $alergias          ?: null,
            $enfermedades      ?: null,
            $emergencia_nombre ?: null,
            $emergencia_tel    ?: null,
            $paciente['id_paciente'],
        ]);
    }

    $_SESSION['nombre'] = $nombre;

    responder(200, ['ok' => true]);

} catch (PDOException $e) {
    responder(500, ['error' => 'Error al actualizar el perfil.']);
}
