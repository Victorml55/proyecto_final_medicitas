<?php
/**
 * API – Citas de Laboratorio Clínico
 *
 * GET  /api/citas-laboratorio.php          → lista las citas del paciente en sesión
 * POST /api/citas-laboratorio.php          → crea nueva cita de laboratorio
 * PUT  /api/citas-laboratorio.php?id=X&accion=cancelar → cancela la cita
 */

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config.php';

$db     = conectarDB();
$metodo = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

// ── Helpers ────────────────────────────────────────────────────────────────

function getPacienteId(PDO $db): ?int {
    if (!isset($_SESSION['id_usuario'])) return null;
    $s = $db->prepare('SELECT id_paciente FROM pacientes WHERE id_usuario = ? LIMIT 1');
    $s->execute([$_SESSION['id_usuario']]);
    $r = $s->fetch();
    return $r ? (int)$r['id_paciente'] : null;
}

// ── Router ─────────────────────────────────────────────────────────────────

switch ($metodo) {

    // ── GET: listar citas del paciente ─────────────────────────────────────
    case 'GET':
        if (!isset($_SESSION['id_usuario'])) {
            responder(401, ['error' => 'No autenticado']);
        }
        $idPac = getPacienteId($db);
        if (!$idPac) {
            responder(200, []);
        }
        $stmt = $db->prepare(
            "SELECT cl.id_cita_lab, cl.fecha_cita, cl.hora_inicio, cl.hora_fin,
                    cl.tipo_analisis, cl.notas_paciente, cl.costo,
                    cl.codigo_confirmacion, cl.fecha_creacion,
                    e.nombre_estado, e.color
             FROM citas_laboratorio cl
             JOIN estados_cita e ON e.id_estado = cl.id_estado
             WHERE cl.id_paciente = ?
             ORDER BY cl.fecha_cita DESC, cl.hora_inicio DESC"
        );
        $stmt->execute([$idPac]);
        responder(200, $stmt->fetchAll());

    // ── POST: crear cita de laboratorio ───────────────────────────────────
    case 'POST':
        if (!isset($_SESSION['id_usuario'])) {
            responder(401, ['error' => 'No autenticado']);
        }
        $idPac = getPacienteId($db);
        if (!$idPac) {
            responder(403, ['error' => 'No tienes un perfil de paciente']);
        }

        $d = json_decode(file_get_contents('php://input'), true) ?? [];

        foreach (['fecha_cita', 'hora_inicio', 'hora_fin'] as $f) {
            if (empty($d[$f])) {
                responder(400, ['error' => "El campo $f es requerido"]);
            }
        }

        // Verificar que el slot no esté ocupado
        $stmtOk = $db->prepare(
            "SELECT 1 FROM citas_laboratorio
             WHERE fecha_cita = ? AND hora_inicio = ? AND id_estado IN (1, 2)
             LIMIT 1"
        );
        $stmtOk->execute([$d['fecha_cita'], $d['hora_inicio']]);
        if ($stmtOk->fetch()) {
            responder(409, ['error' => 'Este horario ya está ocupado. Selecciona otro.']);
        }

        $codigo = strtoupper(substr(md5(uniqid('lab', true)), 0, 8));

        $stmt = $db->prepare(
            "INSERT INTO citas_laboratorio
                (id_paciente, id_estado, fecha_cita, hora_inicio, hora_fin,
                 tipo_analisis, notas_paciente, costo, codigo_confirmacion)
             VALUES (?, 1, ?, ?, ?, ?, ?, ?, ?)
             RETURNING id_cita_lab"
        );
        $stmt->execute([
            $idPac,
            $d['fecha_cita'],
            $d['hora_inicio'],
            $d['hora_fin'],
            $d['tipo_analisis']  ?? null,
            $d['notas_paciente'] ?? null,
            isset($d['costo']) && $d['costo'] !== '' ? (float)$d['costo'] : null,
            $codigo,
        ]);
        $row = $stmt->fetch();
        responder(201, [
            'id_cita_lab'         => (int)$row['id_cita_lab'],
            'codigo_confirmacion' => $codigo,
        ]);

    // ── PUT: cancelar cita ─────────────────────────────────────────────────
    case 'PUT':
        if (!isset($_SESSION['id_usuario'])) {
            responder(401, ['error' => 'No autenticado']);
        }
        if (!$id) {
            responder(400, ['error' => 'Se requiere id de cita']);
        }
        $accion = $_GET['accion'] ?? '';
        $idPac  = getPacienteId($db);
        if (!$idPac) {
            responder(403, ['error' => 'No autorizado']);
        }

        if ($accion === 'cancelar') {
            $stmt = $db->prepare(
                "UPDATE citas_laboratorio SET id_estado = 3
                 WHERE id_cita_lab = ? AND id_paciente = ?
                 RETURNING id_cita_lab"
            );
            $stmt->execute([$id, $idPac]);
            if (!$stmt->fetch()) {
                responder(409, ['error' => 'No se pudo cancelar la cita']);
            }
            responder(200, ['mensaje' => 'Cita cancelada']);
        }

        responder(400, ['error' => 'Acción no válida']);

    default:
        responder(405, ['error' => 'Método no permitido']);
}
