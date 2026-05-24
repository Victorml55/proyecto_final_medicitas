<?php
/**
 * GET /api/horarios-laboratorio.php?dias=1
 *     → array de nombres de días con horario activo
 *
 * GET /api/horarios-laboratorio.php?fecha=YYYY-MM-DD
 *     → { slots: [...], dia: "Lunes" }
 *       cada slot: { hora, hora_fin, label, ocupado }
 */

require_once __DIR__ . '/config.php';

$fecha = $_GET['fecha'] ?? '';

// Modo: devolver solo los días de semana disponibles
if (isset($_GET['dias'])) {
    $db   = conectarDB();
    $stmt = $db->query(
        'SELECT DISTINCT dia_semana FROM horarios_laboratorio WHERE activo = true'
    );
    responder(200, array_column($stmt->fetchAll(), 'dia_semana'));
}

if (!$fecha || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    responder(400, ['error' => 'Se requiere el parámetro fecha (YYYY-MM-DD)']);
}

$diasMap   = [0=>'Domingo',1=>'Lunes',2=>'Martes',3=>'Miércoles',4=>'Jueves',5=>'Viernes',6=>'Sábado'];
$diaSemana = $diasMap[(int)date('w', strtotime($fecha))];

$db = conectarDB();

$stmt = $db->prepare(
    'SELECT hora_inicio, hora_fin, duracion_slot
     FROM horarios_laboratorio
     WHERE dia_semana = ? AND activo = true
     ORDER BY hora_inicio'
);
$stmt->execute([$diaSemana]);
$horarios = $stmt->fetchAll();

if (!$horarios) {
    responder(200, ['slots' => [], 'mensaje' => 'El laboratorio no atiende este día']);
}

// Slots ya ocupados en esa fecha (Pendiente=1, Confirmada=2)
$stmt2 = $db->prepare(
    "SELECT hora_inicio FROM citas_laboratorio
     WHERE fecha_cita = ? AND id_estado IN (1, 2)"
);
$stmt2->execute([$fecha]);
$ocupados = array_column($stmt2->fetchAll(), 'hora_inicio');

$esHoy   = ($fecha === date('Y-m-d'));
$ahoraTs = $esHoy ? time() : 0;
$slots   = [];

foreach ($horarios as $h) {
    $inicio = strtotime($h['hora_inicio']);
    $fin    = strtotime($h['hora_fin']);
    $durMin = (int)($h['duracion_slot'] ?? 30);

    while ($inicio < $fin) {
        $horaStr  = date('H:i:s', $inicio);
        $horaFin  = date('H:i:s', $inicio + $durMin * 60);
        $ocupado  = in_array($horaStr, $ocupados) || ($esHoy && $inicio <= $ahoraTs);

        $slots[] = [
            'hora'     => $horaStr,
            'hora_fin' => $horaFin,
            'label'    => date('h:i A', $inicio),
            'ocupado'  => $ocupado,
        ];
        $inicio += $durMin * 60;
    }
}

responder(200, ['slots' => $slots, 'dia' => $diaSemana]);
