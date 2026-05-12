<?php
/**
 * GET /api/perfil.php  → datos completos del usuario + paciente de la sesión activa
 */

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/config.php';

if (!isset($_SESSION['id_usuario'])) {
    responder(401, ['error' => 'No autenticado']);
}

try {
    $db = conectarDB();

    $stmt = $db->prepare(
        "SELECT u.id_usuario, u.nombre, u.apellido_paterno, u.apellido_materno,
                u.email, u.telefono, u.fecha_nacimiento, u.genero, u.fecha_registro,
                p.id_paciente, p.numero_expediente, p.tipo_sangre,
                p.alergias, p.enfermedades_cronicas,
                p.contacto_emergencia_nombre, p.contacto_emergencia_telefono,
                p.seguro_medico, p.numero_poliza
         FROM usuarios u
         LEFT JOIN pacientes p ON p.id_usuario = u.id_usuario
         WHERE u.id_usuario = ?
         LIMIT 1"
    );
    $stmt->execute([$_SESSION['id_usuario']]);
    $datos = $stmt->fetch();

    if (!$datos) responder(404, ['error' => 'Usuario no encontrado']);

    responder(200, $datos);

} catch (PDOException $e) {
    responder(500, ['error' => 'Error al obtener el perfil.']);
}
