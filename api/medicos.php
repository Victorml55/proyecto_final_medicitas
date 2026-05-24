<?php
/**
 * API RESTful – Médicos
 *
 * GET    /api/medicos.php        → lista todos (con nombre y especialidad)
 * GET    /api/medicos.php?id=1   → obtiene uno
 * POST   /api/medicos.php        → crea nuevo
 * PUT    /api/medicos.php?id=1   → actualiza
 * DELETE /api/medicos.php?id=1   → elimina
 *
 * Campos POST/PUT (JSON):
 *   id_usuario*, id_especialidad*, cedula_profesional*,
 *   universidad, fecha_inicio, biografia,
 *   costo_consulta*, duracion_consulta, activo
 */

require_once __DIR__ . '/config.php';

$db     = conectarDB();
$metodo = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($metodo) {

    case 'GET':
        if ($id) {
            $stmt = $db->prepare(
                "SELECT m.id_medico, m.cedula_profesional, m.universidad,
                        m.fecha_inicio, m.biografia, m.costo_consulta,
                        m.duracion_consulta, m.activo,
                        u.nombre, u.apellido_paterno, u.apellido_materno,
                        u.email, u.telefono, u.foto_perfil, u.genero,
                        e.nombre_especialidad
                 FROM medicos m
                 JOIN usuarios      u ON u.id_usuario      = m.id_usuario
                 JOIN especialidades e ON e.id_especialidad = m.id_especialidad
                 WHERE m.id_medico = ?"
            );
            $stmt->execute([$id]);
            $fila = $stmt->fetch();
            $fila
                ? responder(200, $fila)
                : responder(404, ['error' => 'Médico no encontrado']);
        } else {
            $stmt = $db->query(
                "SELECT m.id_medico, m.cedula_profesional, m.costo_consulta,
                        m.duracion_consulta, m.activo,
                        u.nombre || ' ' || u.apellido_paterno AS nombre_completo,
                        u.email, u.genero, u.foto_perfil,
                        e.nombre_especialidad
                 FROM medicos m
                 JOIN usuarios      u ON u.id_usuario      = m.id_usuario
                 JOIN especialidades e ON e.id_especialidad = m.id_especialidad
                 ORDER BY u.apellido_paterno, u.nombre"
            );
            responder(200, $stmt->fetchAll());
        }
        break;

    case 'POST':
        $d = leerBody();
        $faltantes = [];
        if (empty($d['id_usuario']))         $faltantes[] = 'id_usuario';
        if (empty($d['id_especialidad']))     $faltantes[] = 'id_especialidad';
        if (empty($d['cedula_profesional']))  $faltantes[] = 'cedula_profesional';
        if (!isset($d['costo_consulta']))     $faltantes[] = 'costo_consulta';
        if ($faltantes) {
            responder(422, ['error' => 'Campos requeridos faltantes', 'campos' => $faltantes]);
        }
        try {
            $stmt = $db->prepare(
                "INSERT INTO medicos
                    (id_usuario, id_especialidad, cedula_profesional, universidad,
                     fecha_inicio, biografia, costo_consulta, duracion_consulta, activo)
                 VALUES (?,?,?,?,?,?,?,?,?) RETURNING *"
            );
            $stmt->execute([
                (int)$d['id_usuario'],
                (int)$d['id_especialidad'],
                trim($d['cedula_profesional']),
                trim($d['universidad']       ?? '') ?: null,
                trim($d['fecha_inicio']      ?? '') ?: null,
                trim($d['biografia']         ?? '') ?: null,
                (float)$d['costo_consulta'],
                (int)($d['duracion_consulta'] ?? 30),
                isset($d['activo']) ? (bool)$d['activo'] : true,
            ]);
            responder(201, $stmt->fetch());
        } catch (PDOException $e) {
            responder(409, ['error' => $e->getMessage()]);
        }
        break;

    case 'PUT':
        if (!$id) responder(400, ['error' => 'Se requiere el parámetro ?id=']);
        $d = leerBody();
        $faltantes = [];
        if (empty($d['id_usuario']))         $faltantes[] = 'id_usuario';
        if (empty($d['id_especialidad']))     $faltantes[] = 'id_especialidad';
        if (empty($d['cedula_profesional']))  $faltantes[] = 'cedula_profesional';
        if (!isset($d['costo_consulta']))     $faltantes[] = 'costo_consulta';
        if ($faltantes) {
            responder(422, ['error' => 'Campos requeridos faltantes', 'campos' => $faltantes]);
        }
        try {
            $stmt = $db->prepare(
                "UPDATE medicos SET
                    id_usuario=?, id_especialidad=?, cedula_profesional=?, universidad=?,
                    fecha_inicio=?, biografia=?, costo_consulta=?, duracion_consulta=?, activo=?
                 WHERE id_medico=? RETURNING *"
            );
            $stmt->execute([
                (int)$d['id_usuario'],
                (int)$d['id_especialidad'],
                trim($d['cedula_profesional']),
                trim($d['universidad']       ?? '') ?: null,
                trim($d['fecha_inicio']      ?? '') ?: null,
                trim($d['biografia']         ?? '') ?: null,
                (float)$d['costo_consulta'],
                (int)($d['duracion_consulta'] ?? 30),
                isset($d['activo']) ? (bool)$d['activo'] : true,
                $id,
            ]);
            $fila = $stmt->fetch();
            $fila
                ? responder(200, $fila)
                : responder(404, ['error' => 'Médico no encontrado']);
        } catch (PDOException $e) {
            responder(409, ['error' => $e->getMessage()]);
        }
        break;

    case 'DELETE':
        if (!$id) responder(400, ['error' => 'Se requiere el parámetro ?id=']);
        $stmt = $db->prepare('DELETE FROM medicos WHERE id_medico = ? RETURNING id_medico');
        $stmt->execute([$id]);
        $stmt->fetch()
            ? responder(200, ['mensaje' => 'Médico eliminado correctamente'])
            : responder(404, ['error' => 'Médico no encontrado']);
        break;

    default:
        responder(405, ['error' => 'Método no permitido']);
}
