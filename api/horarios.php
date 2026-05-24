<?php
/**
 * GET /api/horarios.php?medico=X&fecha=YYYY-MM-DD
 * Retorna los slots disponibles de un médico en una fecha dada,
 * excluyendo los horarios ya ocupados por citas confirmadas/pendientes.
 */

require_once __DIR__ . '/config.php';

$id_medico = isset($_GET['medico']) ? (int)$_GET['medico'] : 0;
$fecha     = $_GET['fecha'] ?? '';

if (!$id_medico) {
    responder(400, ['error' => 'Se requiere el parámetro medico']);
}

// Modo: solo días de semana disponibles del médico (sin fecha)
if (isset($_GET['dias'])) {
    try {
        $db   = conectarDB();
        $stmt = $db->prepare(
            'SELECT DISTINCT dia_semana FROM horarios_medicos WHERE id_medico = ? AND activo = true'
        );
        $stmt->execute([$id_medico]);
        responder(200, array_column($stmt->fetchAll(), 'dia_semana'));
    } catch (PDOException $e) {
        responder(500, ['error' => 'Error al obtener días disponibles.']);
    }
}

if (!$fecha) {
    responder(400, ['error' => 'Se requiere el parámetro fecha']);
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    responder(400, ['error' => 'Formato de fecha inválido']);
}

$diasMap = [
    0 => 'Domingo', 1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
    4 => 'Jueves',  5 => 'Viernes', 6 => 'Sábado'
];
$diaSemana = $diasMap[(int)date('w', strtotime($fecha))];

try {
    $db = conectarDB();

    // Horarios definidos para ese médico ese día
    $stmt = $db->prepare(
        "SELECT h.hora_inicio, h.hora_fin, h.id_consultorio,
                c.numero_consultorio
         FROM horarios_medicos h
         LEFT JOIN consultorios c ON c.id_consultorio = h.id_consultorio
         WHERE h.id_medico = ? AND h.dia_semana = ? AND h.activo = true
         ORDER BY h.hora_inicio"
    );
    $stmt->execute([$id_medico, $diaSemana]);
    $horarios = $stmt->fetchAll();

    if (!$horarios) {
        responder(200, ['slots' => [], 'mensaje' => 'El médico no tiene horario disponible este día']);
    }

    // Citas ya agendadas para ese médico en esa fecha
    $stmt2 = $db->prepare(
        "SELECT hora_inicio FROM citas
         WHERE id_medico = ? AND fecha_cita = ?
           AND id_estado IN (1, 2)"
    );
    $stmt2->execute([$id_medico, $fecha]);
    $ocupados = array_column($stmt2->fetchAll(), 'hora_inicio');

    // Generar slots de 30 minutos dentro de cada bloque horario
    $esHoy      = ($fecha === date('Y-m-d'));
    $ahoraTs    = $esHoy ? time() : 0;

    $slots = [];
    foreach ($horarios as $h) {
        $inicio = strtotime($h['hora_inicio']);
        $fin    = strtotime($h['hora_fin']);
        $consultorio = $h['numero_consultorio'] ?? null;

        while ($inicio < $fin) {
            $horaStr    = date('H:i:s', $inicio);
            $horaLabel  = date('h:i A', $inicio);
            $ocupado    = in_array($horaStr, $ocupados) || ($esHoy && $inicio <= $ahoraTs);

            $slots[] = [
                'hora'         => $horaStr,
                'label'        => $horaLabel,
                'ocupado'      => $ocupado,
                'consultorio'  => $consultorio,
            ];
            $inicio += 30 * 60;
        }
    }

    responder(200, ['slots' => $slots, 'dia' => $diaSemana]);

} catch (PDOException $e) {
    responder(500, ['error' => 'Error al obtener horarios.']);
}
