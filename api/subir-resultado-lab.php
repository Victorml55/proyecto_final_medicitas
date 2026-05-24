<?php
/**
 * POST /api/subir-resultado-lab.php
 * Solo administradores. Sube el PDF de resultados para una cita de laboratorio.
 * Campos: id_cita_lab (int), archivo (PDF), descripcion (opcional)
 */

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['id_usuario'])) {
    responder(401, ['error' => 'No autenticado']);
}

if (!in_array('Administrador', $_SESSION['roles'] ?? [], true)) {
    responder(403, ['error' => 'Acceso restringido a administradores']);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, ['error' => 'Método no permitido']);
}

$id_cita_lab = isset($_POST['id_cita_lab']) ? (int)$_POST['id_cita_lab'] : 0;
$descripcion = trim($_POST['descripcion'] ?? '') ?: null;

if (!$id_cita_lab) {
    responder(422, ['error' => 'Se requiere id_cita_lab']);
}

$file = $_FILES['archivo'] ?? null;
if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
    $codigo = $file['error'] ?? -1;
    if ($codigo === UPLOAD_ERR_SIZE || $codigo === UPLOAD_ERR_FORM_SIZE) {
        responder(422, ['error' => 'El archivo es demasiado grande. Máximo 10 MB.']);
    }
    responder(422, ['error' => 'No se recibió un archivo válido.']);
}

if ($file['size'] > 10 * 1024 * 1024) {
    responder(422, ['error' => 'El archivo no debe superar 10 MB.']);
}

$mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
if ($mime !== 'application/pdf') {
    responder(422, ['error' => 'Solo se permiten archivos PDF.']);
}

$db = conectarDB();

$stmtC = $db->prepare('SELECT id_cita_lab, id_paciente FROM citas_laboratorio WHERE id_cita_lab = ? LIMIT 1');
$stmtC->execute([$id_cita_lab]);
$cita = $stmtC->fetch();
if (!$cita) {
    responder(404, ['error' => 'Cita de laboratorio no encontrada']);
}

$dir = __DIR__ . '/../admin/uploads/resultados/';
if (!is_dir($dir)) mkdir($dir, 0755, true);

$base          = pathinfo($file['name'], PATHINFO_FILENAME);
$nombreSeguro  = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base);
$nombreArchivo = 'lab' . $id_cita_lab . '_' . time() . '_' . $nombreSeguro . '.pdf';
$destino       = $dir . $nombreArchivo;

if (!move_uploaded_file($file['tmp_name'], $destino)) {
    responder(500, ['error' => 'No se pudo guardar el archivo en el servidor.']);
}

$stmtI = $db->prepare(
    "INSERT INTO archivos_adjuntos
         (id_cita_lab, id_paciente, nombre_archivo, ruta_archivo,
          tipo_archivo, tamaño_kb, descripcion, categoria)
     VALUES (?, ?, ?, ?, 'pdf', ?, ?, 'resultado_lab')
     RETURNING id_archivo"
);
$stmtI->execute([
    $id_cita_lab,
    $cita['id_paciente'],
    $file['name'],
    'uploads/resultados/' . $nombreArchivo,
    (int)ceil($file['size'] / 1024),
    $descripcion,
]);

responder(201, ['ok' => true, 'id_archivo' => $stmtI->fetchColumn()]);
