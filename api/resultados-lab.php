<?php
/**
 * GET /api/resultados-lab.php
 * Devuelve los resultados de laboratorio del paciente en sesión.
 */

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['id_usuario'])) {
    responder(401, ['error' => 'No autenticado']);
}

$db  = conectarDB();
$uid = (int)$_SESSION['id_usuario'];

$stmtP = $db->prepare('SELECT id_paciente FROM pacientes WHERE id_usuario = ? LIMIT 1');
$stmtP->execute([$uid]);
$paciente = $stmtP->fetch();

if (!$paciente) {
    responder(200, []);
}

$stmt = $db->prepare(
    "SELECT a.id_archivo,
            a.nombre_archivo,
            a.tamaño_kb,
            a.descripcion,
            a.fecha_subida,
            a.id_cita_lab,
            cl.tipo_analisis,
            cl.fecha_cita
     FROM archivos_adjuntos a
     JOIN citas_laboratorio cl ON cl.id_cita_lab = a.id_cita_lab
     WHERE a.id_paciente = ? AND a.categoria = 'resultado_lab'
     ORDER BY a.fecha_subida DESC"
);
$stmt->execute([$paciente['id_paciente']]);

responder(200, $stmt->fetchAll());
