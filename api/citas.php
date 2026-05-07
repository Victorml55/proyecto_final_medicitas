<?php
/**
 * API RESTful – Citas
 *
 * GET    /api/citas.php        → lista todas (con nombres de paciente, médico y estado)
 * GET    /api/citas.php?id=1   → obtiene una
 * POST   /api/citas.php        → crea nueva
 * PUT    /api/citas.php?id=1   → actualiza
 * DELETE /api/citas.php?id=1   → elimina
 *
 * Campos POST/PUT (JSON):
 *   id_paciente*, id_medico*, id_estado*, fecha_cita*, hora_inicio*, hora_fin*,
 *   id_consultorio, motivo_consulta, notas_paciente, costo, codigo_confirmacion
 */

require_once __DIR__ . '/config.php';

$db     = conectarDB();
$metodo = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($metodo) {

    case 'GET':
        if ($id) {
            $stmt = $db->prepare('SELECT * FROM citas WHERE id_cita = ?');
            $stmt->execute([$id]);
            $fila = $stmt->fetch();
            $fila
                ? responder(200, $fila)
                : responder(404, ['error' => 'Cita no encontrada']);
        } else {
            $stmt = $db->query(
                "SELECT c.id_cita, c.fecha_cita, c.hora_inicio, c.hora_fin,
                        c.motivo_consulta, c.costo, c.codigo_confirmacion,
                        up.nombre || ' ' || up.apellido_paterno AS nombre_paciente,
                        um.nombre || ' ' || um.apellido_paterno AS nombre_medico,
                        co.numero_consultorio,
                        e.nombre_estado
                 FROM citas c
                 JOIN pacientes    p   ON p.id_paciente  = c.id_paciente
                 JOIN usuarios     up  ON up.id_usuario  = p.id_usuario
                 JOIN medicos      m   ON m.id_medico    = c.id_medico
                 JOIN usuarios     um  ON um.id_usuario  = m.id_usuario
                 JOIN estados_cita e   ON e.id_estado    = c.id_estado
                 LEFT JOIN consultorios co ON co.id_consultorio = c.id_consultorio
                 ORDER BY c.fecha_cita DESC, c.hora_inicio DESC"
            );
            responder(200, $stmt->fetchAll());
        }
        break;

    case 'POST':
        $d = leerBody();
        $faltantes = [];
        if (empty($d['id_paciente']))  $faltantes[] = 'id_paciente';
        if (empty($d['id_medico']))    $faltantes[] = 'id_medico';
        if (empty($d['id_estado']))    $faltantes[] = 'id_estado';
        if (empty($d['fecha_cita']))   $faltantes[] = 'fecha_cita';
        if (empty($d['hora_inicio']))  $faltantes[] = 'hora_inicio';
        if (empty($d['hora_fin']))     $faltantes[] = 'hora_fin';
        if ($faltantes) {
            responder(422, ['error' => 'Campos requeridos faltantes', 'campos' => $faltantes]);
        }
        $stmt = $db->prepare(
            "INSERT INTO citas
                (id_paciente, id_medico, id_consultorio, id_estado,
                 fecha_cita, hora_inicio, hora_fin, motivo_consulta,
                 notas_paciente, costo, codigo_confirmacion)
             VALUES (?,?,?,?,?,?,?,?,?,?,?) RETURNING *"
        );
        $stmt->execute([
            (int)$d['id_paciente'],
            (int)$d['id_medico'],
            !empty($d['id_consultorio']) ? (int)$d['id_consultorio'] : null,
            (int)$d['id_estado'],
            $d['fecha_cita'],
            $d['hora_inicio'],
            $d['hora_fin'],
            trim($d['motivo_consulta']     ?? '') ?: null,
            trim($d['notas_paciente']      ?? '') ?: null,
            $d['costo'] !== '' ? (float)($d['costo'] ?? 0) : null,
            trim($d['codigo_confirmacion'] ?? '') ?: null,
        ]);
        responder(201, $stmt->fetch());
        break;

    case 'PUT':
        if (!$id) responder(400, ['error' => 'Se requiere el parámetro ?id=']);
        $d = leerBody();
        $faltantes = [];
        if (empty($d['id_paciente']))  $faltantes[] = 'id_paciente';
        if (empty($d['id_medico']))    $faltantes[] = 'id_medico';
        if (empty($d['id_estado']))    $faltantes[] = 'id_estado';
        if (empty($d['fecha_cita']))   $faltantes[] = 'fecha_cita';
        if (empty($d['hora_inicio']))  $faltantes[] = 'hora_inicio';
        if (empty($d['hora_fin']))     $faltantes[] = 'hora_fin';
        if ($faltantes) {
            responder(422, ['error' => 'Campos requeridos faltantes', 'campos' => $faltantes]);
        }
        $stmt = $db->prepare(
            "UPDATE citas SET
                id_paciente=?, id_medico=?, id_consultorio=?, id_estado=?,
                fecha_cita=?, hora_inicio=?, hora_fin=?, motivo_consulta=?,
                notas_paciente=?, costo=?, codigo_confirmacion=?
             WHERE id_cita=? RETURNING *"
        );
        $stmt->execute([
            (int)$d['id_paciente'],
            (int)$d['id_medico'],
            !empty($d['id_consultorio']) ? (int)$d['id_consultorio'] : null,
            (int)$d['id_estado'],
            $d['fecha_cita'],
            $d['hora_inicio'],
            $d['hora_fin'],
            trim($d['motivo_consulta']     ?? '') ?: null,
            trim($d['notas_paciente']      ?? '') ?: null,
            $d['costo'] !== '' ? (float)($d['costo'] ?? 0) : null,
            trim($d['codigo_confirmacion'] ?? '') ?: null,
            $id,
        ]);
        $fila = $stmt->fetch();
        $fila
            ? responder(200, $fila)
            : responder(404, ['error' => 'Cita no encontrada']);
        break;

    case 'DELETE':
        if (!$id) responder(400, ['error' => 'Se requiere el parámetro ?id=']);
        $stmt = $db->prepare('DELETE FROM citas WHERE id_cita = ? RETURNING id_cita');
        $stmt->execute([$id]);
        $stmt->fetch()
            ? responder(200, ['mensaje' => 'Cita eliminada correctamente'])
            : responder(404, ['error' => 'Cita no encontrada']);
        break;

    default:
        responder(405, ['error' => 'Método no permitido']);
}
