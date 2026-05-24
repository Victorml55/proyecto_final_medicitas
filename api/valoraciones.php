<?php
/**
 * API – Valoraciones de médicos
 *
 * GET  /api/valoraciones.php?medico=X   → promedio, total y lista de reseñas
 * GET  /api/valoraciones.php?cita=X     → verifica si ya existe reseña para esa cita
 * POST /api/valoraciones.php            → guarda una nueva reseña (requiere sesión)
 */

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config.php';

$metodo = $_SERVER['REQUEST_METHOD'];
$db     = conectarDB();

// ── POST: guardar nueva valoración ───────────────────────────────────────
if ($metodo === 'POST') {
    if (!isset($_SESSION['id_usuario'])) {
        responder(401, ['error' => 'Debes iniciar sesión para calificar']);
    }

    $d            = leerBody();
    $idCita       = isset($d['id_cita'])      ? (int)$d['id_cita']      : 0;
    $calificacion = isset($d['calificacion']) ? (int)$d['calificacion'] : 0;
    $comentario   = trim($d['comentario'] ?? '');
    $anonimo      = !empty($d['anonimo']) ? true : false;

    if (!$idCita || $calificacion < 1 || $calificacion > 5) {
        responder(422, ['error' => 'Se requiere id_cita y calificacion (1-5)']);
    }

    try {
        // Verificar que el usuario logueado es el paciente de esa cita
        $stmtPac = $db->prepare(
            "SELECT c.id_cita, c.id_paciente, c.id_medico, est.nombre_estado
             FROM citas c
             JOIN estados_cita est ON est.id_estado = c.id_estado
             JOIN pacientes    p   ON p.id_paciente = c.id_paciente
             WHERE c.id_cita = ? AND p.id_usuario = ?
             LIMIT 1"
        );
        $stmtPac->execute([$idCita, $_SESSION['id_usuario']]);
        $cita = $stmtPac->fetch();

        if (!$cita) {
            responder(404, ['error' => 'Cita no encontrada o no pertenece a este usuario']);
        }
        if ($cita['nombre_estado'] !== 'Concluida') {
            responder(409, ['error' => 'Solo puedes calificar citas concluidas']);
        }

        // Verificar que no haya valoración previa para esta cita
        $stmtDup = $db->prepare('SELECT id_valoracion FROM valoraciones WHERE id_cita = ? LIMIT 1');
        $stmtDup->execute([$idCita]);
        if ($stmtDup->fetch()) {
            responder(409, ['error' => 'Ya existe una calificación para esta cita']);
        }

        $stmtIns = $db->prepare(
            "INSERT INTO valoraciones (id_cita, id_paciente, id_medico, calificacion, comentario, anonimo)
             VALUES (?, ?, ?, ?, ?, ?)
             RETURNING id_valoracion"
        );
        $stmtIns->execute([
            $idCita,
            $cita['id_paciente'],
            $cita['id_medico'],
            $calificacion,
            $comentario ?: null,
            $anonimo ? 'true' : 'false',
        ]);
        $nueva = $stmtIns->fetch();
        $nueva
            ? responder(201, ['mensaje' => 'Calificación guardada', 'id_valoracion' => $nueva['id_valoracion']])
            : responder(500, ['error' => 'No se pudo guardar la calificación']);

    } catch (PDOException $e) {
        responder(500, ['error' => 'Error de base de datos: ' . $e->getMessage()]);
    }
}

if ($metodo !== 'GET') {
    responder(405, ['error' => 'Método no permitido']);
}

// ── GET: verificar si ya existe reseña para una cita ────────────────────
if (isset($_GET['cita'])) {
    $idCita = (int)$_GET['cita'];
    $stmt   = $db->prepare('SELECT id_valoracion, calificacion, comentario FROM valoraciones WHERE id_cita = ? LIMIT 1');
    $stmt->execute([$idCita]);
    $row = $stmt->fetch();
    responder(200, ['existe' => (bool)$row, 'valoracion' => $row ?: null]);
}

// ── GET: lista de reseñas de un médico ──────────────────────────────────
$idMedico = isset($_GET['medico']) ? (int)$_GET['medico'] : null;
if (!$idMedico) {
    responder(400, ['error' => 'Se requiere ?medico= o ?cita=']);
}


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
