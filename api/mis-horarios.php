<?php
/**
 * GET  /api/mis-horarios.php          → horarios del médico autenticado
 * POST /api/mis-horarios.php          → reemplaza todos sus horarios
 */

require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['id_usuario'])) {
    responder(401, ['error' => 'No autenticado.']);
}

$idMedico = $_SESSION['id_medico'] ?? null;

if (!$idMedico) {
    $db   = conectarDB();
    $stmt = $db->prepare('SELECT id_medico FROM medicos WHERE id_usuario = ? LIMIT 1');
    $stmt->execute([$_SESSION['id_usuario']]);
    $row  = $stmt->fetch();
    if (!$row) responder(403, ['error' => 'No eres médico.']);
    $idMedico = (int)$row['id_medico'];
}

$db     = conectarDB();
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    $stmt = $db->prepare(
        'SELECT dia_semana, hora_inicio, hora_fin, activo
         FROM horarios_medicos WHERE id_medico = ? ORDER BY
         CASE dia_semana
           WHEN \'Lunes\' THEN 1 WHEN \'Martes\' THEN 2 WHEN \'Miércoles\' THEN 3
           WHEN \'Jueves\' THEN 4 WHEN \'Viernes\' THEN 5 WHEN \'Sábado\' THEN 6
           WHEN \'Domingo\' THEN 7 END'
    );
    $stmt->execute([$idMedico]);
    responder(200, $stmt->fetchAll());
}

if ($metodo === 'POST') {
    $dias  = leerBody();
    $validos = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];

    // Eliminar horarios actuales y reemplazar
    $db->prepare('DELETE FROM horarios_medicos WHERE id_medico = ?')->execute([$idMedico]);

    $stmt = $db->prepare(
        'INSERT INTO horarios_medicos (id_medico, dia_semana, hora_inicio, hora_fin, activo)
         VALUES (?, ?, ?, ?, true)'
    );

    foreach ($dias as $entrada) {
        $dia   = $entrada['dia_semana']  ?? '';
        $ini   = $entrada['hora_inicio'] ?? '';
        $fin   = $entrada['hora_fin']    ?? '';
        if (!in_array($dia, $validos, true) || !$ini || !$fin || $fin <= $ini) continue;
        $stmt->execute([$idMedico, $dia, $ini, $fin]);
    }

    responder(200, ['success' => true, 'mensaje' => 'Horarios guardados correctamente.']);
}

responder(405, ['error' => 'Método no permitido.']);
