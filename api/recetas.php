<?php
/**
 * GET  /api/recetas.php           → recetas del paciente en sesión, con medicamentos
 * GET  /api/recetas.php?id_cita=X → receta de una cita específica (paciente o médico)
 * POST /api/recetas.php           → crea / actualiza receta de una cita (médico)
 */

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/config.php';

if (!isset($_SESSION['id_usuario'])) {
    responder(401, ['error' => 'No autenticado']);
}

$db  = conectarDB();
$uid = (int)$_SESSION['id_usuario'];

// ── POST: crear / actualizar receta ─────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmtM = $db->prepare('SELECT id_medico FROM medicos WHERE id_usuario = ? LIMIT 1');
    $stmtM->execute([$uid]);
    $medico = $stmtM->fetch();
    if (!$medico) {
        responder(403, ['error' => 'Solo médicos pueden crear recetas']);
    }

    $d = json_decode(file_get_contents('php://input'), true) ?? [];
    if (empty($d['id_cita'])) {
        responder(422, ['error' => 'Se requiere id_cita']);
    }
    $id_cita = (int)$d['id_cita'];

    // Verificar que la cita pertenece a este médico
    $stmtV = $db->prepare('SELECT id_cita FROM citas WHERE id_cita = ? AND id_medico = ?');
    $stmtV->execute([$id_cita, $medico['id_medico']]);
    if (!$stmtV->fetch()) {
        responder(403, ['error' => 'No tiene acceso a esta cita']);
    }

    $stmtEx = $db->prepare('SELECT id_receta FROM recetas WHERE id_cita = ? LIMIT 1');
    $stmtEx->execute([$id_cita]);
    $exReceta = $stmtEx->fetch();

    $db->beginTransaction();
    try {
        if ($exReceta) {
            $db->prepare('UPDATE recetas SET diagnostico=?, indicaciones_generales=? WHERE id_receta=?')
               ->execute([
                   trim($d['diagnostico'] ?? '') ?: null,
                   trim($d['indicaciones_generales'] ?? '') ?: null,
                   $exReceta['id_receta'],
               ]);
            $idReceta = $exReceta['id_receta'];
            $db->prepare('DELETE FROM medicamentos_receta WHERE id_receta = ?')->execute([$idReceta]);
        } else {
            $stmtI = $db->prepare(
                'INSERT INTO recetas (id_cita, diagnostico, indicaciones_generales) VALUES (?,?,?) RETURNING id_receta'
            );
            $stmtI->execute([
                $id_cita,
                trim($d['diagnostico'] ?? '') ?: null,
                trim($d['indicaciones_generales'] ?? '') ?: null,
            ]);
            $idReceta = $stmtI->fetchColumn();
        }

        $meds = $d['medicamentos'] ?? [];
        if (is_array($meds)) {
            $stmtMed = $db->prepare(
                'INSERT INTO medicamentos_receta
                     (id_receta, nombre_medicamento, presentacion, dosis, frecuencia, duracion, indicaciones)
                 VALUES (?,?,?,?,?,?,?)'
            );
            foreach ($meds as $med) {
                if (empty(trim($med['nombre_medicamento'] ?? ''))) continue;
                $stmtMed->execute([
                    $idReceta,
                    trim($med['nombre_medicamento']),
                    trim($med['presentacion'] ?? '') ?: null,
                    trim($med['dosis']        ?? '') ?: '—',
                    trim($med['frecuencia']   ?? '') ?: '—',
                    trim($med['duracion']     ?? '') ?: null,
                    trim($med['indicaciones'] ?? '') ?: null,
                ]);
            }
        }

        $db->commit();
    } catch (Throwable $e) {
        $db->rollBack();
        responder(500, ['error' => 'Error al guardar la receta.']);
    }

    responder(201, ['ok' => true, 'id_receta' => $idReceta]);
}

// ── GET ──────────────────────────────────────────────────────────────────────

try {
    // GET ?id_cita=X → receta de una cita (accesible por paciente o médico)
    if (isset($_GET['id_cita'])) {
        $id_cita = (int)$_GET['id_cita'];

        // Verificar que el usuario es el paciente o el médico de esta cita
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

        $stmt = $db->prepare('SELECT * FROM recetas WHERE id_cita = ? LIMIT 1');
        $stmt->execute([$id_cita]);
        $receta = $stmt->fetch();

        if (!$receta) {
            responder(200, null);
        }

        $stmtMed = $db->prepare(
            'SELECT * FROM medicamentos_receta WHERE id_receta = ? ORDER BY id_medicamento_receta'
        );
        $stmtMed->execute([$receta['id_receta']]);
        $receta['medicamentos'] = $stmtMed->fetchAll();

        responder(200, $receta);
    }

    // GET sin parámetros → todas las recetas del paciente en sesión
    $stmtP = $db->prepare('SELECT id_paciente FROM pacientes WHERE id_usuario = ? LIMIT 1');
    $stmtP->execute([$uid]);
    $paciente = $stmtP->fetch();

    if (!$paciente) {
        responder(200, []);
    }

    $stmt = $db->prepare(
        "SELECT r.id_receta, r.diagnostico, r.indicaciones_generales, r.fecha_emision,
                c.fecha_cita,
                um.nombre || ' ' || um.apellido_paterno AS nombre_medico,
                um.genero AS genero_medico,
                e.nombre_especialidad
         FROM recetas r
         JOIN citas       c  ON c.id_cita      = r.id_cita
         JOIN pacientes   p  ON p.id_paciente  = c.id_paciente
         JOIN medicos     m  ON m.id_medico    = c.id_medico
         JOIN usuarios    um ON um.id_usuario  = m.id_usuario
         LEFT JOIN especialidades e ON e.id_especialidad = m.id_especialidad
         WHERE p.id_paciente = ?
         ORDER BY r.fecha_emision DESC"
    );
    $stmt->execute([$paciente['id_paciente']]);
    $recetas = $stmt->fetchAll();

    $stmtMed = $db->prepare(
        'SELECT * FROM medicamentos_receta WHERE id_receta = ? ORDER BY id_medicamento_receta'
    );
    foreach ($recetas as &$r) {
        $stmtMed->execute([$r['id_receta']]);
        $r['medicamentos'] = $stmtMed->fetchAll();
    }

    responder(200, $recetas);

} catch (PDOException $e) {
    responder(500, ['error' => 'Error al obtener las recetas.']);
}
