<?php
require_once(__DIR__ . '/sistema.class.php');
require_once(__DIR__ . '/auth.php');
requerirLogin();

$db    = (new class extends Sistema { function __construct() { $this->conectar(); } })->db;
$error = null;
$ok    = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['accion'] ?? '') === 'subir') {
    $id_cita_lab = (int)($_POST['id_cita_lab'] ?? 0);
    $descripcion = trim($_POST['descripcion'] ?? '') ?: null;

    do {
        if (!$id_cita_lab) { $error = 'ID de cita no válido.'; break; }

        $file = $_FILES['archivo'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $error = 'No se recibió un archivo válido.'; break;
        }
        if ($file['size'] > 10 * 1024 * 1024) {
            $error = 'El archivo supera el límite de 10 MB.'; break;
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if ($mime !== 'application/pdf') {
            $error = 'Solo se permiten archivos PDF.'; break;
        }

        $stmtC = $db->prepare('SELECT id_paciente FROM citas_laboratorio WHERE id_cita_lab = ? LIMIT 1');
        $stmtC->execute([$id_cita_lab]);
        $cita = $stmtC->fetch();
        if (!$cita) { $error = 'Cita no encontrada.'; break; }

        $dir = __DIR__ . '/uploads/resultados/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $base          = pathinfo($file['name'], PATHINFO_FILENAME);
        $nombreSeguro  = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base);
        $nombreArchivo = 'lab' . $id_cita_lab . '_' . time() . '_' . $nombreSeguro . '.pdf';

        if (!move_uploaded_file($file['tmp_name'], $dir . $nombreArchivo)) {
            $error = 'No se pudo guardar el archivo.'; break;
        }

        $stmtI = $db->prepare(
            "INSERT INTO archivos_adjuntos
                 (id_cita_lab, id_paciente, nombre_archivo, ruta_archivo,
                  tipo_archivo, tamaño_kb, descripcion, categoria)
             VALUES (?, ?, ?, ?, 'pdf', ?, ?, 'resultado_lab')"
        );
        $stmtI->execute([
            $id_cita_lab,
            $cita['id_paciente'],
            $file['name'],
            'uploads/resultados/' . $nombreArchivo,
            (int)ceil($file['size'] / 1024),
            $descripcion,
        ]);

        $ok = 'Resultado subido correctamente.';
    } while (false);
}

// Cargar citas con info de paciente y si ya tienen resultado
$citas = $db->query(
    "SELECT cl.id_cita_lab,
            cl.fecha_cita,
            cl.hora_inicio,
            cl.tipo_analisis,
            cl.costo,
            u.nombre || ' ' || u.apellido_paterno AS nombre_paciente,
            e.nombre_estado,
            (SELECT COUNT(*) FROM archivos_adjuntos a
             WHERE a.id_cita_lab = cl.id_cita_lab AND a.categoria = 'resultado_lab') AS tiene_resultado
     FROM citas_laboratorio cl
     JOIN pacientes p ON p.id_paciente = cl.id_paciente
     JOIN usuarios  u ON u.id_usuario  = p.id_usuario
     JOIN estados_cita e ON e.id_estado = cl.id_estado
     ORDER BY cl.fecha_cita DESC, cl.hora_inicio DESC"
)->fetchAll();

require(__DIR__ . '/views/header.php');
require(__DIR__ . '/views/resultado_lab/index.php');
require(__DIR__ . '/views/footer.php');
