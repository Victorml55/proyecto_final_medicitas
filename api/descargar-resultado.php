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

    // Verificar acceso: paciente propietario, médico de la cita, o resultado de lab del paciente
    $stmtAccess = $db->prepare("
        SELECT a.nombre_archivo, a.ruta_archivo, a.tipo_archivo
        FROM archivos_adjuntos a
        LEFT JOIN citas     c  ON c.id_cita     = a.id_cita
        LEFT JOIN pacientes p  ON p.id_paciente = a.id_paciente
        LEFT JOIN medicos   m  ON m.id_medico   = c.id_medico
        WHERE a.id_archivo = ?
          AND (p.id_usuario = ? OR m.id_usuario = ?)
        LIMIT 1
    ");
    $stmtAccess->execute([$id, $_SESSION['id_usuario'], $_SESSION['id_usuario']]);
    $archivo = $stmtAccess->fetch();

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
