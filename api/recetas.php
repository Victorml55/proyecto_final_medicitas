<?php
/**
 * GET /api/recetas.php  → recetas del paciente en sesión, con sus medicamentos
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
        "SELECT r.id_receta, r.diagnostico, r.indicaciones_generales, r.fecha_emision,
                c.fecha_cita,
                um.nombre || ' ' || um.apellido_paterno AS nombre_medico,
                um.genero AS genero_medico,
                e.nombre_especialidad
         FROM recetas r
         JOIN citas       c  ON c.id_cita      = r.id_cita
         JOIN pacientes   p  ON p.id_paciente  = c.id_paciente
         JOIN medicos     m  ON m.id_medico    = c.id_medico
         JOIN usuarios    um ON um.id_usuario  = m.id_usuario
         LEFT JOIN especialidades e ON e.id_especialidad = m.id_especialidad
         WHERE p.id_paciente = ?
         ORDER BY r.fecha_emision DESC"
    );
    $stmt->execute([$paciente['id_paciente']]);
    $recetas = $stmt->fetchAll();

    $stmtMed = $db->prepare(
        'SELECT * FROM medicamentos_receta WHERE id_receta = ? ORDER BY id_medicamento_receta'
    );
    foreach ($recetas as &$r) {
        $stmtMed->execute([$r['id_receta']]);
        $r['medicamentos'] = $stmtMed->fetchAll();
    }

    responder(200, $recetas);

} catch (PDOException $e) {
    responder(500, ['error' => 'Error al obtener las recetas.']);
}
