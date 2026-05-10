<?php

require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder(405, ['error' => 'Método no permitido.']);
}

try {
    $db = conectarDB();

    $stmt = $db->query(
        'SELECT id_catalogo, nombre, categoria, presentacion, precio, disponible
         FROM catalogo_farmacia
         ORDER BY categoria, nombre'
    );

    responder(200, ['success' => true, 'data' => $stmt->fetchAll()]);

} catch (PDOException $e) {
    responder(500, ['error' => 'Error al obtener el catálogo.']);
}
