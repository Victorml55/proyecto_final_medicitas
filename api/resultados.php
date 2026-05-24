<?php
/**
 * GET /api/resultados.php  → archivos adjuntos del paciente en sesión
 */

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/config.php';

if (!isset($_SESSION['id_usuario'])) {
    responder(401, ['error' => 'No autenticado']);
}

try {
    $db = conectarDB();

    $stmtP = $db->prepare('SELECT id_paciente FROM pacientes WHERE id_usuario = ? LIMIT 1');
    $stmtP->execute([$_SESSION['id_usuario']]);
    $paciente = $stmtP->fetch();

    if (!$paciente) {
        responder(200, []);
    }

    $stmt = $db->prepare(
        "SELECT a.id_archivo, a.nombre_archivo, a.tipo_archivo, a.tamaño_kb,
                a.descripcion, a.fecha_subida, a.id_cita,
                c.fecha_cita,
                um.nombre || ' ' || um.apellido_paterno AS nombre_medico
         FROM archivos_adjuntos a
         LEFT JOIN citas    c  ON c.id_cita   = a.id_cita
         LEFT JOIN medicos  m  ON m.id_medico  = c.id_medico
         LEFT JOIN usuarios um ON um.id_usuario = m.id_usuario
         WHERE a.id_paciente = ?
         ORDER BY a.fecha_subida DESC"
    );
    $stmt->execute([$paciente['id_paciente']]);

    responder(200, $stmt->fetchAll());

} catch (PDOException $e) {
    responder(500, ['error' => 'Error al obtener los resultados.']);
}
