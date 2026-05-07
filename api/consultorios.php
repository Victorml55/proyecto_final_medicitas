<?php
/**
 * API RESTful – Consultorios
 *
 * GET    /api/consultorios.php        → lista todos
 * GET    /api/consultorios.php?id=1   → obtiene uno
 * POST   /api/consultorios.php        → crea nuevo
 * PUT    /api/consultorios.php?id=1   → actualiza
 * DELETE /api/consultorios.php?id=1   → elimina
 *
 * Campos POST/PUT (JSON):
 *   numero_consultorio*, piso, descripcion, activo
 */

require_once __DIR__ . '/config.php';

$db     = conectarDB();
$metodo = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($metodo) {

    case 'GET':
        if ($id) {
            $stmt = $db->prepare('SELECT * FROM consultorios WHERE id_consultorio = ?');
            $stmt->execute([$id]);
            $fila = $stmt->fetch();
            $fila
                ? responder(200, $fila)
                : responder(404, ['error' => 'Consultorio no encontrado']);
        } else {
            $stmt = $db->query('SELECT * FROM consultorios ORDER BY numero_consultorio');
            responder(200, $stmt->fetchAll());
        }
        break;

    case 'POST':
        $d = leerBody();
        if (empty($d['numero_consultorio'])) {
            responder(422, ['error' => 'El campo numero_consultorio es requerido']);
        }
        $stmt = $db->prepare(
            'INSERT INTO consultorios (numero_consultorio, piso, descripcion, activo)
             VALUES (?, ?, ?, ?) RETURNING *'
        );
        $stmt->execute([
            trim($d['numero_consultorio']),
            isset($d['piso']) && $d['piso'] !== '' ? (int)$d['piso'] : null,
            trim($d['descripcion'] ?? '') ?: null,
            isset($d['activo']) ? (bool)$d['activo'] : true,
        ]);
        responder(201, $stmt->fetch());
        break;

    case 'PUT':
        if (!$id) responder(400, ['error' => 'Se requiere el parámetro ?id=']);
        $d = leerBody();
        if (empty($d['numero_consultorio'])) {
            responder(422, ['error' => 'El campo numero_consultorio es requerido']);
        }
        $stmt = $db->prepare(
            'UPDATE consultorios SET numero_consultorio = ?, piso = ?, descripcion = ?, activo = ?
             WHERE id_consultorio = ? RETURNING *'
        );
        $stmt->execute([
            trim($d['numero_consultorio']),
            isset($d['piso']) && $d['piso'] !== '' ? (int)$d['piso'] : null,
            trim($d['descripcion'] ?? '') ?: null,
            isset($d['activo']) ? (bool)$d['activo'] : true,
            $id,
        ]);
        $fila = $stmt->fetch();
        $fila
            ? responder(200, $fila)
            : responder(404, ['error' => 'Consultorio no encontrado']);
        break;

    case 'DELETE':
        if (!$id) responder(400, ['error' => 'Se requiere el parámetro ?id=']);
        $stmt = $db->prepare('DELETE FROM consultorios WHERE id_consultorio = ? RETURNING id_consultorio');
        $stmt->execute([$id]);
        $stmt->fetch()
            ? responder(200, ['mensaje' => 'Consultorio eliminado correctamente'])
            : responder(404, ['error' => 'Consultorio no encontrado']);
        break;

    default:
        responder(405, ['error' => 'Método no permitido']);
}
