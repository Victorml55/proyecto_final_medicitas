<?php
/**
 * API RESTful – Especialidades
 *
 * GET    /api/especialidades.php        → lista todas
 * GET    /api/especialidades.php?id=1   → obtiene una
 * POST   /api/especialidades.php        → crea nueva
 * PUT    /api/especialidades.php?id=1   → actualiza
 * DELETE /api/especialidades.php?id=1   → elimina
 */

require_once __DIR__ . '/config.php';

$db     = conectarDB();
$metodo = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($metodo) {

    case 'GET':
        if ($id) {
            $stmt = $db->prepare('SELECT * FROM especialidades WHERE id_especialidad = ?');
            $stmt->execute([$id]);
            $fila = $stmt->fetch();
            $fila
                ? responder(200, $fila)
                : responder(404, ['error' => 'Especialidad no encontrada']);
        } else {
            $stmt = $db->query('SELECT * FROM especialidades ORDER BY nombre_especialidad');
            responder(200, $stmt->fetchAll());
        }
        break;

    case 'POST':
        $d = leerBody();
        if (empty($d['nombre_especialidad'])) {
            responder(422, ['error' => 'El campo nombre_especialidad es requerido']);
        }
        $stmt = $db->prepare(
            'INSERT INTO especialidades (nombre_especialidad, descripcion, activo)
             VALUES (?, ?, ?) RETURNING *'
        );
        $stmt->execute([
            trim($d['nombre_especialidad']),
            trim($d['descripcion'] ?? '') ?: null,
            isset($d['activo']) ? (bool)$d['activo'] : true,
        ]);
        responder(201, $stmt->fetch());
        break;

    case 'PUT':
        if (!$id) responder(400, ['error' => 'Se requiere el parámetro ?id=']);
        $d = leerBody();
        if (empty($d['nombre_especialidad'])) {
            responder(422, ['error' => 'El campo nombre_especialidad es requerido']);
        }
        $stmt = $db->prepare(
            'UPDATE especialidades
             SET nombre_especialidad = ?, descripcion = ?, activo = ?
             WHERE id_especialidad = ? RETURNING *'
        );
        $stmt->execute([
            trim($d['nombre_especialidad']),
            trim($d['descripcion'] ?? '') ?: null,
            isset($d['activo']) ? (bool)$d['activo'] : true,
            $id,
        ]);
        $fila = $stmt->fetch();
        $fila
            ? responder(200, $fila)
            : responder(404, ['error' => 'Especialidad no encontrada']);
        break;

    case 'DELETE':
        if (!$id) responder(400, ['error' => 'Se requiere el parámetro ?id=']);
        $stmt = $db->prepare(
            'DELETE FROM especialidades WHERE id_especialidad = ? RETURNING id_especialidad'
        );
        $stmt->execute([$id]);
        $stmt->fetch()
            ? responder(200, ['mensaje' => 'Especialidad eliminada correctamente'])
            : responder(404, ['error' => 'Especialidad no encontrada']);
        break;

    default:
        responder(405, ['error' => 'Método no permitido']);
}
