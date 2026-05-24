<?php
/**
 * GET /api/descargar-resultado.php?id=X
 * Sirve el archivo solo si pertenece al paciente en sesión.
 */

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/config.php';

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    exit('No autenticado');
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    http_response_code(400);
    exit('ID no válido');
}

try {
    $db = conectarDB();

    $stmtP = $db->prepare('SELECT id_paciente FROM pacientes WHERE id_usuario = ? LIMIT 1');
    $stmtP->execute([$_SESSION['id_usuario']]);
    $paciente = $stmtP->fetch();

    if (!$paciente) {
        http_response_code(403);
        exit('Acceso denegado');
    }

    $stmt = $db->prepare(
        'SELECT nombre_archivo, ruta_archivo, tipo_archivo FROM archivos_adjuntos
          WHERE id_archivo = ? AND id_paciente = ?'
    );
    $stmt->execute([$id, $paciente['id_paciente']]);
    $archivo = $stmt->fetch();

    if (!$archivo) {
        http_response_code(404);
        exit('Archivo no encontrado');
    }

    $ruta = __DIR__ . '/../admin/' . $archivo['ruta_archivo'];

    if (!file_exists($ruta)) {
        http_response_code(404);
        exit('Archivo no encontrado en el servidor');
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($ruta) ?: 'application/octet-stream';

    header('Content-Type: '        . $mime);
    header('Content-Length: '      . filesize($ruta));
    header('Content-Disposition: inline; filename="' . rawurlencode($archivo['nombre_archivo']) . '"');
    header('Cache-Control: private, no-cache');

    readfile($ruta);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    exit('Error del servidor');
}
