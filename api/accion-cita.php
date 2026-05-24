<?php
// Endpoint público: el médico actúa sobre una cita desde el enlace del correo.
// No requiere sesión – el token es la autenticación.

// ── Helpers ──────────────────────────────────────────────────────────────────

function accionDB(): PDO {
    static $pdo;
    if ($pdo) return $pdo;
    $envPath = dirname(__DIR__) . '/.env';
    $env = [];
    if (file_exists($envPath)) {
        foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
            [$k, $v] = explode('=', $line, 2);
            $env[trim($k)] = trim($v);
        }
    }
    $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s',
        $env['DB_HOST'] ?? 'postgres',
        $env['DB_PORT'] ?? '5432',
        $env['DB_NAME'] ?? 'medicitas'
    );
    $pdo = new PDO($dsn, $env['DB_USER'] ?? 'admin', $env['DB_PASSWORD'] ?? 'admin123', [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    return $pdo;
}

function mesES(int $m): string {
    return ['','enero','febrero','marzo','abril','mayo','junio',
            'julio','agosto','septiembre','octubre','noviembre','diciembre'][$m] ?? '';
}

function fechaLegible(string $fecha): string {
    [$y, $m, $d] = explode('-', $fecha);
    return (int)$d . ' de ' . mesES((int)$m) . ' de ' . $y;
}

function horaLegible(string $hora): string {
    [$hh, $mm] = explode(':', $hora);
    $h12 = (int)$hh % 12 ?: 12;
    return sprintf('%d:%s %s', $h12, $mm, (int)$hh >= 12 ? 'PM' : 'AM');
}

function resultPage(string $tipo, string $titulo, string $msg): never {
    $iconos  = ['exito' => '✅', 'error' => '❌', 'info' => 'ℹ️'];
    $colores = ['exito' => '#16a34a', 'error' => '#dc2626', 'info' => '#005f99'];
    $icono = $iconos[$tipo]  ?? 'ℹ️';
    $color = $colores[$tipo] ?? '#005f99';
    echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'>
<meta name='viewport' content='width=device-width,initial-scale=1'>
<title>MediCitas</title>
<style>*{box-sizing:border-box}body{margin:0;font-family:Arial,sans-serif;background:#f0f4f8;min-height:100vh;display:flex;align-items:center;justify-content:center}.wrap{background:#fff;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.1);padding:48px 40px;max-width:480px;width:90%;text-align:center}.brand{display:block;color:#005f99;font-size:20px;font-weight:bold;margin-bottom:28px}.icon{font-size:52px;margin-bottom:12px}h1{color:{$color};margin:0 0 12px;font-size:21px}p{color:#555;line-height:1.6;margin:0}</style>
</head><body><div class='wrap'>
<span class='brand'>🏥 MediCitas</span>
<div class='icon'>{$icono}</div>
<h1>{$titulo}</h1>
<p>{$msg}</p>
</div></body></html>";
    exit;
}

// ── Validación de parámetros ─────────────────────────────────────────────────

$token  = trim($_REQUEST['token']  ?? '');
$accion = trim($_REQUEST['accion'] ?? '');

if (!$token || !in_array($accion, ['confirmar', 'rechazar', 'reprogramar'])) {
    resultPage('error', 'Enlace inválido', 'El enlace que recibiste no es válido o ha expirado.');
}

// ── Consulta de la cita por token ────────────────────────────────────────────

$db   = accionDB();
$stmt = $db->prepare("
    SELECT c.id_cita, c.id_estado, c.fecha_cita, c.hora_inicio, c.hora_fin,
           c.motivo_consulta, c.id_paciente, c.id_medico,
           up.nombre || ' ' || up.apellido_paterno AS nombre_paciente,
           up.email AS email_paciente,
           um.nombre || ' ' || um.apellido_paterno AS nombre_medico,
           um.genero AS genero_medico,
           m.duracion_consulta,
           esp.nombre_especialidad,
           est.nombre_estado
    FROM citas c
    JOIN pacientes      p   ON p.id_paciente    = c.id_paciente
    JOIN usuarios       up  ON up.id_usuario    = p.id_usuario
    JOIN medicos        m   ON m.id_medico      = c.id_medico
    JOIN usuarios       um  ON um.id_usuario    = m.id_usuario
    JOIN especialidades esp ON esp.id_especialidad = m.id_especialidad
    JOIN estados_cita   est ON est.id_estado    = c.id_estado
    WHERE c.token_accion = ?
");
$stmt->execute([$token]);
$cita = $stmt->fetch();

if (!$cita) {
    resultPage('error', 'Enlace inválido', 'No se encontró ninguna cita asociada a este enlace. Es posible que ya haya sido procesada o eliminada.');
}

if ($cita['id_estado'] != 1) {
    $estadoActual = htmlspecialchars($cita['nombre_estado']);
    resultPage('info', 'Acción ya procesada', "Esta cita ya fue procesada. Estado actual: <strong>{$estadoActual}</strong>.<br><br>Si necesitas realizar cambios, contáctanos directamente.");
}

// ── POST: procesar acción ────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pacienteNombre = $cita['nombre_paciente'];
    $pacienteEmail  = $cita['email_paciente'];

    switch ($accion) {

        case 'confirmar':
            $db->prepare("UPDATE citas SET id_estado = 2, fecha_actualizacion = NOW() WHERE id_cita = ?")
               ->execute([$cita['id_cita']]);

            try {
                require_once __DIR__ . '/../admin/services/MailService.php';
                $datos = [
                    'fecha'         => fechaLegible($cita['fecha_cita']),
                    'hora'          => horaLegible($cita['hora_inicio']),
                    'medico'        => $cita['nombre_medico'],
                    'genero_medico' => $cita['genero_medico'],
                    'especialidad'  => $cita['nombre_especialidad'],
                ];
                MailService::citaConfirmada($pacienteEmail, $pacienteNombre, $datos);
            } catch (Throwable $e) {
                error_log('[accion-cita] Email error confirmar: ' . $e->getMessage());
            }

            resultPage('exito', 'Cita confirmada', 'La cita de <strong>' . htmlspecialchars($pacienteNombre) . '</strong> ha sido confirmada. El paciente fue notificado por correo electrónico.');

        case 'rechazar':
            $motivo = trim($_POST['motivo'] ?? '');

            $stmtR = $db->query("SELECT id_estado FROM estados_cita WHERE nombre_estado = 'Rechazada' LIMIT 1");
            $rec   = $stmtR->fetch();
            if (!$rec) {
                $db->exec("INSERT INTO estados_cita (nombre_estado, descripcion, color) VALUES ('Rechazada', 'Cita rechazada por el médico', '#dc2626')");
                $stmtR = $db->query("SELECT id_estado FROM estados_cita WHERE nombre_estado = 'Rechazada' LIMIT 1");
                $rec   = $stmtR->fetch();
            }
            $idRechazada = $rec['id_estado'];

            $db->prepare("UPDATE citas SET id_estado = ?, fecha_actualizacion = NOW() WHERE id_cita = ?")
               ->execute([$idRechazada, $cita['id_cita']]);

            try {
                require_once __DIR__ . '/../admin/services/MailService.php';
                $datos = [
                    'fecha'         => fechaLegible($cita['fecha_cita']),
                    'hora'          => horaLegible($cita['hora_inicio']),
                    'medico'        => $cita['nombre_medico'],
                    'genero_medico' => $cita['genero_medico'],
                    'especialidad'  => $cita['nombre_especialidad'],
                ];
                MailService::citaRechazada($pacienteEmail, $pacienteNombre, $datos, $motivo);
            } catch (Throwable $e) {
                error_log('[accion-cita] Email error rechazar: ' . $e->getMessage());
            }

            resultPage('exito', 'Cita rechazada', 'La solicitud fue rechazada. El paciente fue notificado por correo electrónico.');

        case 'reprogramar':
            $nuevaFecha = trim($_POST['nueva_fecha'] ?? '');
            $nuevaHora  = trim($_POST['nueva_hora']  ?? '');

            if (!$nuevaFecha || !$nuevaHora) {
                resultPage('error', 'Datos incompletos', 'Por favor ingresa la nueva fecha y hora antes de continuar.');
            }
            if ($nuevaFecha <= date('Y-m-d')) {
                resultPage('error', 'Fecha inválida', 'La nueva fecha debe ser un día futuro.');
            }

            $nuevaHoraDB = $nuevaHora . ':00';
            $duracion    = (int)($cita['duracion_consulta'] ?? 30);
            $tsInicio    = strtotime($nuevaFecha . ' ' . $nuevaHoraDB);
            $horaFinDB   = date('H:i:s', $tsInicio + $duracion * 60);

            $db->prepare("UPDATE citas SET fecha_cita = ?, hora_inicio = ?, hora_fin = ?, id_estado = 2, fecha_actualizacion = NOW() WHERE id_cita = ?")
               ->execute([$nuevaFecha, $nuevaHoraDB, $horaFinDB, $cita['id_cita']]);

            try {
                require_once __DIR__ . '/../admin/services/MailService.php';
                $datos = [
                    'fecha'         => fechaLegible($nuevaFecha),
                    'hora'          => horaLegible($nuevaHoraDB),
                    'medico'        => $cita['nombre_medico'],
                    'genero_medico' => $cita['genero_medico'],
                    'especialidad'  => $cita['nombre_especialidad'],
                ];
                MailService::citaReprogramada($pacienteEmail, $pacienteNombre, $datos);
            } catch (Throwable $e) {
                error_log('[accion-cita] Email error reprogramar: ' . $e->getMessage());
            }

            $nuevaFechaLeg = htmlspecialchars(fechaLegible($nuevaFecha));
            $nuevaHoraLeg  = htmlspecialchars(horaLegible($nuevaHoraDB));
            resultPage('exito', 'Cita reprogramada', "La cita fue reprogramada para el <strong>{$nuevaFechaLeg}</strong> a las <strong>{$nuevaHoraLeg}</strong> y confirmada. El paciente fue notificado.");
    }
}

// ── GET: mostrar formulario de acción ────────────────────────────────────────

$gm        = $cita['genero_medico'] ?? null;
$titMed    = $gm === 'M' ? 'Dr.' : ($gm === 'F' ? 'Dra.' : 'Dr(a).');
$paciente  = htmlspecialchars($cita['nombre_paciente'] ?? '');
$medico    = htmlspecialchars($titMed . ' ' . ($cita['nombre_medico'] ?? ''));
$fecha     = htmlspecialchars(fechaLegible($cita['fecha_cita']));
$hora      = htmlspecialchars(horaLegible($cita['hora_inicio']));
$motivo    = htmlspecialchars($cita['motivo_consulta'] ?? 'No especificado');
$especial  = htmlspecialchars($cita['nombre_especialidad'] ?? '');
$tokenHtml = htmlspecialchars($token);
$minFecha  = date('Y-m-d', strtotime('+1 day'));

$titulos = [
    'confirmar'   => 'Confirmar cita',
    'rechazar'    => 'Rechazar cita',
    'reprogramar' => 'Reprogramar cita',
];
$colores = [
    'confirmar'   => '#16a34a',
    'rechazar'    => '#dc2626',
    'reprogramar' => '#f59e0b',
];
$btnTextos = [
    'confirmar'   => '✅ Confirmar cita',
    'rechazar'    => '❌ Rechazar cita',
    'reprogramar' => '📅 Reprogramar y confirmar',
];

$tituloAccion = $titulos[$accion];
$colorBtn     = $colores[$accion];
$textoBtn     = $btnTextos[$accion];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= $tituloAccion ?> | MediCitas</title>
  <style>
    * { box-sizing: border-box; }
    body { margin: 0; font-family: Arial, sans-serif; background: #f0f4f8; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,.1); max-width: 520px; width: 100%; overflow: hidden; }
    .card-header { background: #005f99; padding: 24px 32px; }
    .card-header .brand { color: #fff; font-size: 20px; font-weight: bold; margin: 0 0 4px; }
    .card-header p { color: #b3d9f2; margin: 0; font-size: 14px; }
    .card-body { padding: 32px; }
    h2 { color: #1e2d3b; margin: 0 0 20px; font-size: 20px; }
    .info-box { background: #f0f8ff; border-radius: 8px; padding: 16px 20px; margin-bottom: 24px; }
    .info-box p { margin: 6px 0; color: #333; font-size: 14px; }
    label { display: block; color: #444; font-size: 14px; font-weight: bold; margin-bottom: 6px; margin-top: 16px; }
    input[type=date], input[type=time] {
      width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px;
      font-size: 15px; color: #333; outline: none;
    }
    input[type=date]:focus, input[type=time]:focus { border-color: #005f99; box-shadow: 0 0 0 3px rgba(0,95,153,.15); }
    textarea {
      width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px;
      font-size: 14px; color: #333; resize: vertical; min-height: 90px; outline: none; font-family: inherit;
    }
    textarea:focus { border-color: #005f99; box-shadow: 0 0 0 3px rgba(0,95,153,.15); }
    .btn-submit {
      display: block; width: 100%; margin-top: 24px; padding: 13px;
      background: <?= $colorBtn ?>; color: #fff; border: none; border-radius: 8px;
      font-size: 16px; font-weight: bold; cursor: pointer; transition: opacity .15s;
    }
    .btn-submit:hover { opacity: .88; }
    .hint { color: #6b7280; font-size: 12px; margin-top: 20px; text-align: center; }
  </style>
</head>
<body>
<div class="card">
  <div class="card-header">
    <p class="brand">🏥 MediCitas</p>
    <p><?= htmlspecialchars($tituloAccion) ?></p>
  </div>
  <div class="card-body">
    <h2><?= htmlspecialchars($tituloAccion) ?></h2>

    <div class="info-box">
      <p><strong>👤 Paciente:</strong> <?= $paciente ?></p>
      <p><strong>📅 Fecha:</strong> <?= $fecha ?></p>
      <p><strong>🕐 Hora:</strong> <?= $hora ?></p>
      <p><strong>🩺 Especialidad:</strong> <?= $especial ?></p>
      <?php if ($motivo !== 'No especificado'): ?>
      <p><strong>📋 Motivo:</strong> <?= $motivo ?></p>
      <?php endif; ?>
    </div>

    <form method="POST" action="/api/accion-cita.php">
      <input type="hidden" name="token"  value="<?= $tokenHtml ?>">
      <input type="hidden" name="accion" value="<?= htmlspecialchars($accion) ?>">

      <?php if ($accion === 'rechazar'): ?>
        <label for="motivo">Motivo del rechazo <span style="color:#6b7280;font-weight:normal;">(opcional)</span></label>
        <textarea id="motivo" name="motivo" placeholder="Ej. No tengo disponibilidad en ese horario..."></textarea>

      <?php elseif ($accion === 'reprogramar'): ?>
        <label for="nueva_fecha">Nueva fecha</label>
        <input type="date" id="nueva_fecha" name="nueva_fecha" min="<?= $minFecha ?>" required>

        <label for="nueva_hora">Nueva hora de inicio</label>
        <input type="time" id="nueva_hora" name="nueva_hora" required>

      <?php elseif ($accion === 'confirmar'): ?>
        <p style="color:#444;font-size:14px;margin:0 0 4px;">Al confirmar, se notificará al paciente por correo electrónico.</p>
      <?php endif; ?>

      <button type="submit" class="btn-submit"><?= htmlspecialchars($textoBtn) ?></button>
    </form>

    <p class="hint">Este enlace es de uso único para el médico asignado.</p>
  </div>
</div>
</body>
</html>
