<?php
/**
 * API – Valoraciones de médicos
 *
 * GET /api/valoraciones.php?medico=X  → promedio, total y lista de reseñas del médico
 */

require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder(405, ['error' => 'Método no permitido']);
}

$idMedico = isset($_GET['medico']) ? (int)$_GET['medico'] : null;
if (!$idMedico) {
    responder(400, ['error' => 'Se requiere el parámetro ?medico=']);
}

$db = conectarDB();

$stmtStats = $db->prepare(
    'SELECT ROUND(AVG(calificacion)::numeric, 1) AS promedio,
            COUNT(*) AS total
     FROM valoraciones
     WHERE id_medico = ?'
);
$stmtStats->execute([$idMedico]);
$stats = $stmtStats->fetch();

$stmtReviews = $db->prepare(
    "SELECT v.id_valoracion,
            v.calificacion,
            v.comentario,
            v.anonimo,
            v.fecha_valoracion,
            CASE WHEN v.anonimo THEN 'Anónimo'
                 ELSE u.nombre || ' ' || LEFT(u.apellido_paterno, 1) || '.'
            END AS nombre_paciente
     FROM valoraciones v
     JOIN pacientes p ON p.id_paciente = v.id_paciente
     JOIN usuarios  u ON u.id_usuario  = p.id_usuario
     WHERE v.id_medico = ?
     ORDER BY v.fecha_valoracion DESC"
);
$stmtReviews->execute([$idMedico]);
$reviews = $stmtReviews->fetchAll();

responder(200, [
    'promedio'     => $stats['promedio'] !== null ? (float)$stats['promedio'] : null,
    'total'        => (int)$stats['total'],
    'valoraciones' => $reviews,
]);
