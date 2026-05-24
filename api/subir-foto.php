<?php
/**
 * POST /api/subir-foto.php  → sube foto de perfil del usuario en sesión
 * Acepta multipart/form-data con campo "foto"
 */

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/config.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id_usuario'])) {
    responder(401, ['error' => 'No autenticado']);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, ['error' => 'Método no permitido']);
}

if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    $codigoError = $_FILES['foto']['error'] ?? -1;
    if ($codigoError === UPLOAD_ERR_SIZE || $codigoError === UPLOAD_ERR_FORM_SIZE) {
        responder(422, ['error' => 'El archivo es demasiado grande. Máximo 5 MB.']);
    }
    responder(422, ['error' => 'No se recibió ningún archivo o ocurrió un error al subir.']);
}

$file     = $_FILES['foto'];
$maxBytes = 5 * 1024 * 1024;

if ($file['size'] > $maxBytes) {
    responder(422, ['error' => 'El archivo no debe superar 5 MB.']);
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($file['tmp_name']);

$tiposPermitidos = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
    'image/webp' => 'webp',
];

if (!array_key_exists($mime, $tiposPermitidos)) {
    responder(422, ['error' => 'Solo se permiten imágenes JPG, PNG, GIF o WebP.']);
}

$ext  = $tiposPermitidos[$mime];
$dir  = __DIR__ . '/../img/perfiles/';

if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

$nombreArchivo = 'u' . (int)$_SESSION['id_usuario'] . '_' . time() . '.' . $ext;
$destino       = $dir . $nombreArchivo;

try {
    $db = conectarDB();

    // Eliminar foto anterior si existe
    $stmtVieja = $db->prepare('SELECT foto_perfil FROM usuarios WHERE id_usuario = ?');
    $stmtVieja->execute([$_SESSION['id_usuario']]);
    $rutaVieja = $stmtVieja->fetchColumn();

    if ($rutaVieja) {
        $archivoViejo = __DIR__ . '/../' . ltrim($rutaVieja, '/');
        if (file_exists($archivoViejo)) {
            @unlink($archivoViejo);
        }
    }

    if (!move_uploaded_file($file['tmp_name'], $destino)) {
        responder(500, ['error' => 'No se pudo guardar el archivo en el servidor.']);
    }

    $url = '/img/perfiles/' . $nombreArchivo;

    $db->prepare('UPDATE usuarios SET foto_perfil = ? WHERE id_usuario = ?')
       ->execute([$url, $_SESSION['id_usuario']]);

    responder(200, ['ok' => true, 'url' => $url]);

} catch (PDOException $e) {
    responder(500, ['error' => 'Error de base de datos al guardar la foto.']);
}
