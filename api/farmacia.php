<?php
/**
 * API RESTful – Catálogo Farmacia
 *
 * GET    /api/farmacia.php        → lista todos
 * GET    /api/farmacia.php?id=1   → obtiene uno
 * POST   /api/farmacia.php        → crea nuevo
 * PUT    /api/farmacia.php?id=1   → actualiza
 * DELETE /api/farmacia.php?id=1   → elimina
 *
 * Campos POST/PUT (JSON):
 *   nombre*, categoria*, presentacion, precio, disponible
 */

require_once __DIR__ . '/config.php';

$db     = conectarDB();
$metodo = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($metodo) {

    case 'GET':
        if ($id) {
            $stmt = $db->prepare('SELECT * FROM catalogo_farmacia WHERE id_catalogo = ?');
            $stmt->execute([$id]);
            $fila = $stmt->fetch();
            $fila
                ? responder(200, $fila)
                : responder(404, ['error' => 'Producto no encontrado']);
        } else {
            $stmt = $db->query('SELECT * FROM catalogo_farmacia ORDER BY categoria, nombre');
            responder(200, ['success' => true, 'data' => $stmt->fetchAll()]);
        }
        break;

    case 'POST':
        $d = leerBody();
        if (empty($d['nombre']) || empty($d['categoria'])) {
            responder(422, ['error' => 'Los campos nombre y categoria son requeridos']);
        }
        $stmt = $db->prepare(
            'INSERT INTO catalogo_farmacia (nombre, categoria, presentacion, precio, disponible)
             VALUES (?, ?, ?, ?, ?) RETURNING *'
        );
        $stmt->execute([
            trim($d['nombre']),
            trim($d['categoria']),
            trim($d['presentacion'] ?? '') ?: null,
            isset($d['precio']) && $d['precio'] !== '' ? (float)$d['precio'] : null,
            isset($d['disponible']) ? (bool)$d['disponible'] : true,
        ]);
        responder(201, $stmt->fetch());
        break;

    case 'PUT':
        if (!$id) responder(400, ['error' => 'Se requiere el parámetro ?id=']);
        $d = leerBody();
        if (empty($d['nombre']) || empty($d['categoria'])) {
            responder(422, ['error' => 'Los campos nombre y categoria son requeridos']);
        }
        $stmt = $db->prepare(
            'UPDATE catalogo_farmacia
             SET nombre = ?, categoria = ?, presentacion = ?, precio = ?, disponible = ?
             WHERE id_catalogo = ? RETURNING *'
        );
        $stmt->execute([
            trim($d['nombre']),
            trim($d['categoria']),
            trim($d['presentacion'] ?? '') ?: null,
            isset($d['precio']) && $d['precio'] !== '' ? (float)$d['precio'] : null,
            isset($d['disponible']) ? (bool)$d['disponible'] : true,
            $id,
        ]);
        $fila = $stmt->fetch();
        $fila
            ? responder(200, $fila)
            : responder(404, ['error' => 'Producto no encontrado']);
        break;

    case 'DELETE':
        if (!$id) responder(400, ['error' => 'Se requiere el parámetro ?id=']);
        $stmt = $db->prepare(
            'DELETE FROM catalogo_farmacia WHERE id_catalogo = ? RETURNING id_catalogo'
        );
        $stmt->execute([$id]);
        $stmt->fetch()
            ? responder(200, ['mensaje' => 'Producto eliminado correctamente'])
            : responder(404, ['error' => 'Producto no encontrado']);
        break;

    default:
        responder(405, ['error' => 'Método no permitido']);
}
