<?php

/**
 * Procesa el campo $_FILES['foto_perfil'] y actualiza usuarios.foto_perfil.
 * Retorna la URL guardada o null si no hubo archivo / falló la validación.
 */
function procesarFotoPerfil(int $idUsuario, PDO $db): ?string {
    if (!isset($_FILES['foto_perfil']) || $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $file     = $_FILES['foto_perfil'];
    $maxBytes = 5 * 1024 * 1024;

    if ($file['size'] > $maxBytes) return null;

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    $tipos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];

    if (!array_key_exists($mime, $tipos)) return null;

    $dir = __DIR__ . '/../../img/perfiles/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    // Eliminar foto anterior
    $stmtV = $db->prepare('SELECT foto_perfil FROM usuarios WHERE id_usuario = ?');
    $stmtV->execute([$idUsuario]);
    $vieja = $stmtV->fetchColumn();
    if ($vieja) {
        $rutaVieja = __DIR__ . '/../../' . ltrim($vieja, '/');
        if (file_exists($rutaVieja)) @unlink($rutaVieja);
    }

    $nombre  = 'u' . $idUsuario . '_' . time() . '.' . $tipos[$mime];
    $destino = $dir . $nombre;

    if (!move_uploaded_file($file['tmp_name'], $destino)) return null;

    $url = '/img/perfiles/' . $nombre;
    $db->prepare('UPDATE usuarios SET foto_perfil = ? WHERE id_usuario = ?')->execute([$url, $idUsuario]);
    return $url;
}
