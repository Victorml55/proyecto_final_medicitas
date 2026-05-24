<?php
/**
 * GET  /api/resultados.php           → archivos del paciente en sesión (todos los categoria)
 * GET  /api/resultados.php?id_cita=X → archivos de una cita (paciente o médico)
 * POST /api/resultados.php           → sube archivo (médico, multipart)
 *                                      campo 'categoria': 'resultado' (default) | 'receta'
 */

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/config.php';

if (!isset($_SESSION['id_usuario'])) {
    responder(401, ['error' => 'No autenticado']);
}

$db  = conectarDB();
$uid = (int)$_SESSION['id_usuario'];

// ── POST: subir archivo ──────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmtM = $db->prepare('SELECT id_medico FROM medicos WHERE id_usuario = ? LIMIT 1');
    $stmtM->execute([$uid]);
    $medico = $stmtM->fetch();
    if (!$medico) {
        responder(403, ['error' => 'Solo médicos pueden subir archivos']);
    }

    $id_cita    = isset($_POST['id_cita']) ? (int)$_POST['id_cita'] : 0;
    $descripcion = trim($_POST['descripcion'] ?? '') ?: null;
    $categoria   = in_array(trim($_POST['categoria'] ?? ''), ['receta', 'resultado'])
                   ? trim($_POST['categoria']) : 'resultado';

    if (!$id_cita) {
        responder(422, ['error' => 'Se requiere id_cita']);
    }

    // Verificar que la cita pertenece a este médico y obtener id_paciente
    $stmtV = $db->prepare('SELECT id_paciente FROM citas WHERE id_cita = ? AND id_medico = ?');
    $stmtV->execute([$id_cita, $medico['id_medico']]);
    $citaRow = $stmtV->fetch();
    if (!$citaRow) {
        responder(403, ['error' => 'No tiene acceso a esta cita']);
    }
    $id_paciente = $citaRow['id_paciente'];

    if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
        $codigo = $_FILES['archivo']['error'] ?? -1;
        if ($codigo === UPLOAD_ERR_SIZE || $codigo === UPLOAD_ERR_FORM_SIZE) {
            responder(422, ['error' => 'El archivo es demasiado grande. Máximo 10 MB.']);
        }
        responder(422, ['error' => 'No se recibió un archivo válido.']);
    }

    $file     = $_FILES['archivo'];
    $maxBytes = 10 * 1024 * 1024;
    if ($file['size'] > $maxBytes) {
        responder(422, ['error' => 'El archivo no debe superar 10 MB.']);
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);

    // Receta: solo PDF
    if ($categoria === 'receta') {
        if ($mime !== 'application/pdf') {
            responder(422, ['error' => 'La receta debe ser un archivo PDF.']);
        }
        $tiposPermitidos = ['application/pdf' => 'pdf'];
    } else {
        $tiposPermitidos = [
            'application/pdf'                                                          => 'pdf',
            'image/jpeg'                                                               => 'jpg',
            'image/png'                                                                => 'png',
            'image/gif'                                                                => 'gif',
            'application/msword'                                                       => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel'                                                 => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'       => 'xlsx',
        ];
    }

    if (!array_key_exists($mime, $tiposPermitidos)) {
        responder(422, ['error' => 'Tipo de archivo no permitido. Use PDF, imágenes, Word o Excel.']);
    }

    $ext  = $tiposPermitidos[$mime];
    $dir  = __DIR__ . '/../admin/uploads/resultados/';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $base          = pathinfo($file['name'], PATHINFO_FILENAME);
    $nombreSeguro  = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base);
    $prefix        = $categoria === 'receta' ? 'rec' : 'r';
    $nombreArchivo = $prefix . $id_cita . '_' . time() . '_' . $nombreSeguro . '.' . $ext;
    $destino       = $dir . $nombreArchivo;

    if (!move_uploaded_file($file['tmp_name'], $destino)) {
        responder(500, ['error' => 'No se pudo guardar el archivo en el servidor.']);
    }

    $rutaDB   = 'uploads/resultados/' . $nombreArchivo;
    $tamañoKB = (int)ceil($file['size'] / 1024);

    $stmtI = $db->prepare(
        "INSERT INTO archivos_adjuntos
             (id_cita, id_paciente, nombre_archivo, ruta_archivo, tipo_archivo, tamaño_kb, descripcion, categoria)
         VALUES (?,?,?,?,?,?,?,?) RETURNING id_archivo"
    );
    $stmtI->execute([
        $id_cita, $id_paciente,
        $file['name'], $rutaDB, $ext, $tamañoKB, $descripcion, $categoria,
    ]);
    $id_archivo = $stmtI->fetchColumn();

    responder(201, ['ok' => true, 'id_archivo' => $id_archivo]);
}

// ── GET ──────────────────────────────────────────────────────────────────────

try {
    // GET ?id_cita=X → archivos de una cita (accesible por paciente o médico)
    if (isset($_GET['id_cita'])) {
        $id_cita = (int)$_GET['id_cita'];

        $stmtCheck = $db->prepare("
            SELECT c.id_cita
            FROM citas c
            JOIN pacientes p ON p.id_paciente = c.id_paciente
            JOIN medicos   m ON m.id_medico   = c.id_medico
            WHERE c.id_cita = ? AND (p.id_usuario = ? OR m.id_usuario = ?)
            LIMIT 1
        ");
        $stmtCheck->execute([$id_cita, $uid, $uid]);
        if (!$stmtCheck->fetch()) {
            responder(403, ['error' => 'Acceso denegado']);
        }

        $stmt = $db->prepare(
            "SELECT id_archivo, nombre_archivo, tipo_archivo, tamaño_kb, descripcion, fecha_subida, categoria
             FROM archivos_adjuntos WHERE id_cita = ? ORDER BY categoria DESC, fecha_subida DESC"
        );
        $stmt->execute([$id_cita]);
        responder(200, $stmt->fetchAll());
    }

    // GET sin parámetros → todos los archivos del paciente en sesión
    $stmtP = $db->prepare('SELECT id_paciente FROM pacientes WHERE id_usuario = ? LIMIT 1');
    $stmtP->execute([$uid]);
    $paciente = $stmtP->fetch();

    if (!$paciente) {
        responder(200, []);
    }

    $stmt = $db->prepare(
        "SELECT a.id_archivo, a.nombre_archivo, a.tipo_archivo, a.tamaño_kb,
                a.descripcion, a.fecha_subida, a.id_cita, a.categoria,
                c.fecha_cita,
                um.nombre || ' ' || um.apellido_paterno AS nombre_medico,
                um.genero AS genero_medico
         FROM archivos_adjuntos a
         LEFT JOIN citas    c  ON c.id_cita    = a.id_cita
         LEFT JOIN medicos  m  ON m.id_medico  = c.id_medico
         LEFT JOIN usuarios um ON um.id_usuario = m.id_usuario
         WHERE a.id_paciente = ?
         ORDER BY a.fecha_subida DESC"
    );
    $stmt->execute([$paciente['id_paciente']]);

    responder(200, $stmt->fetchAll());

} catch (PDOException $e) {
    responder(500, ['error' => 'Error al obtener los archivos.']);
}
