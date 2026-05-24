<?php
/**
 * API RESTful – Citas
 *
 * GET    /api/citas.php        → lista todas (con nombres de paciente, médico y estado)
 * GET    /api/citas.php?id=1   → obtiene una
 * POST   /api/citas.php        → crea nueva
 * PUT    /api/citas.php?id=1   → actualiza
 * DELETE /api/citas.php?id=1   → elimina
 *
 * Campos POST/PUT (JSON):
 *   id_paciente*, id_medico*, id_estado*, fecha_cita*, hora_inicio*, hora_fin*,
 *   id_consultorio, motivo_consulta, notas_paciente, costo, codigo_confirmacion
 */

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config.php';

$db     = conectarDB();
$metodo = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($metodo) {

    case 'GET':
        if ($id) {
            $stmt = $db->prepare(
                "SELECT c.*, e.nombre_estado, e.color,
                        um.nombre || ' ' || um.apellido_paterno AS nombre_medico,
                        um.genero AS genero_medico,
                        um.foto_perfil AS foto_medico,
                        esp.nombre_especialidad,
                        COALESCE(cc.numero_consultorio, cm.numero_consultorio) AS numero_consultorio,
                        m.duracion_consulta
                 FROM citas c
                 JOIN estados_cita e   ON e.id_estado    = c.id_estado
                 JOIN medicos      m   ON m.id_medico    = c.id_medico
                 JOIN usuarios     um  ON um.id_usuario  = m.id_usuario
                 JOIN especialidades esp ON esp.id_especialidad = m.id_especialidad
                 LEFT JOIN consultorios cc ON cc.id_consultorio = c.id_consultorio
                 LEFT JOIN consultorios cm ON cm.id_consultorio = m.id_consultorio
                 WHERE c.id_cita = ?"
            );
            $stmt->execute([$id]);
            $fila = $stmt->fetch();
            $fila
                ? responder(200, $fila)
                : responder(404, ['error' => 'Cita no encontrada']);
        } else {
            $idPaciente = isset($_GET['paciente']) ? (int)$_GET['paciente'] : null;
            $idMedico   = isset($_GET['medico'])   ? (int)$_GET['medico']   : null;
            if ($idPaciente) {
                $where = 'WHERE c.id_paciente = ' . $idPaciente;
            } elseif ($idMedico) {
                $where = 'WHERE c.id_medico = ' . $idMedico;
            } else {
                $where = '';
            }
            $stmt = $db->query(
                "SELECT c.id_cita, c.id_paciente, c.fecha_cita, c.hora_inicio, c.hora_fin,
                        c.motivo_consulta, c.costo, c.codigo_confirmacion, c.id_estado,
                        up.nombre || ' ' || up.apellido_paterno AS nombre_paciente,
                        um.nombre || ' ' || um.apellido_paterno AS nombre_medico,
                        um.genero AS genero_medico,
                        um.foto_perfil AS foto_medico,
                        esp.nombre_especialidad,
                        COALESCE(cc.numero_consultorio, cm.numero_consultorio) AS numero_consultorio,
                        e.nombre_estado, e.color,
                        m.duracion_consulta
                 FROM citas c
                 JOIN pacientes    p   ON p.id_paciente  = c.id_paciente
                 JOIN usuarios     up  ON up.id_usuario  = p.id_usuario
                 JOIN medicos      m   ON m.id_medico    = c.id_medico
                 JOIN usuarios     um  ON um.id_usuario  = m.id_usuario
                 JOIN especialidades esp ON esp.id_especialidad = m.id_especialidad
                 JOIN estados_cita e   ON e.id_estado    = c.id_estado
                 LEFT JOIN consultorios cc ON cc.id_consultorio = c.id_consultorio
                 LEFT JOIN consultorios cm ON cm.id_consultorio = m.id_consultorio
                 $where
                 ORDER BY c.fecha_cita DESC, c.hora_inicio DESC"
            );
            responder(200, $stmt->fetchAll());
        }
        break;

    case 'POST':
        $d = leerBody();
        $faltantes = [];
        if (empty($d['id_paciente']))  $faltantes[] = 'id_paciente';
        if (empty($d['id_medico']))    $faltantes[] = 'id_medico';
        if (empty($d['id_estado']))    $faltantes[] = 'id_estado';
        if (empty($d['fecha_cita']))   $faltantes[] = 'fecha_cita';
        if (empty($d['hora_inicio']))  $faltantes[] = 'hora_inicio';
        if (empty($d['hora_fin']))     $faltantes[] = 'hora_fin';
        if ($faltantes) {
            responder(422, ['error' => 'Campos requeridos faltantes', 'campos' => $faltantes]);
        }
        $token = bin2hex(random_bytes(32));

        $stmt = $db->prepare(
            "INSERT INTO citas
                (id_paciente, id_medico, id_consultorio, id_estado,
                 fecha_cita, hora_inicio, hora_fin, motivo_consulta,
                 notas_paciente, costo, codigo_confirmacion, token_accion)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?) RETURNING *"
        );
        $stmt->execute([
            (int)$d['id_paciente'],
            (int)$d['id_medico'],
            !empty($d['id_consultorio']) ? (int)$d['id_consultorio'] : null,
            1,
            $d['fecha_cita'],
            $d['hora_inicio'],
            $d['hora_fin'],
            trim($d['motivo_consulta']     ?? '') ?: null,
            trim($d['notas_paciente']      ?? '') ?: null,
            isset($d['costo']) && $d['costo'] !== '' ? (float)$d['costo'] : null,
            trim($d['codigo_confirmacion'] ?? '') ?: null,
            $token,
        ]);
        $nueva = $stmt->fetch();

        // Notificaciones por correo (silenciosas — no bloquean la respuesta)
        try {
            require_once __DIR__ . '/../admin/services/MailService.php';

            $qPac = $db->prepare(
                "SELECT up.email, up.nombre || ' ' || up.apellido_paterno AS nombre
                 FROM pacientes p JOIN usuarios up ON up.id_usuario = p.id_usuario
                 WHERE p.id_paciente = ?"
            );
            $qPac->execute([$nueva['id_paciente']]);
            $infoPac = $qPac->fetch();

            $qMed = $db->prepare(
                "SELECT um.email, um.nombre || ' ' || um.apellido_paterno AS nombre,
                        um.genero,
                        esp.nombre_especialidad
                 FROM medicos m
                 JOIN usuarios um ON um.id_usuario = m.id_usuario
                 JOIN especialidades esp ON esp.id_especialidad = m.id_especialidad
                 WHERE m.id_medico = ?"
            );
            $qMed->execute([$nueva['id_medico']]);
            $infoMed = $qMed->fetch();

            $consultorio = null;
            if ($nueva['id_consultorio']) {
                $qCon = $db->prepare('SELECT numero_consultorio FROM consultorios WHERE id_consultorio = ?');
                $qCon->execute([$nueva['id_consultorio']]);
                $con = $qCon->fetch();
                $consultorio = $con ? $con['numero_consultorio'] : null;
            }

            $meses = ['enero','febrero','marzo','abril','mayo','junio',
                      'julio','agosto','septiembre','octubre','noviembre','diciembre'];
            [$y, $m, $dia] = explode('-', $nueva['fecha_cita']);
            $fechaLegible = (int)$dia . ' de ' . $meses[(int)$m - 1] . ' de ' . $y;

            [$hh, $mm] = explode(':', $nueva['hora_inicio']);
            $h12 = (int)$hh % 12 ?: 12;
            $ampm = (int)$hh >= 12 ? 'PM' : 'AM';
            $horaLegible = sprintf('%d:%s %s', $h12, $mm, $ampm);

            $scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $baseUrl = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');

            $datosCita = [
                'fecha'          => $fechaLegible,
                'hora'           => $horaLegible,
                'medico'         => $infoMed['nombre']              ?? '',
                'genero_medico'  => $infoMed['genero']              ?? null,
                'paciente'       => $infoPac['nombre']              ?? '',
                'especialidad'   => $infoMed['nombre_especialidad'] ?? '',
                'consultorio'    => $consultorio,
                'motivo'         => $nueva['motivo_consulta']       ?? 'No especificado',
                'codigo'         => $nueva['codigo_confirmacion']   ?? '',
                'token'          => $nueva['token_accion']          ?? '',
                'base_url'       => $baseUrl,
            ];

            if ($infoPac) {
                MailService::confirmacionCita($infoPac['email'], $infoPac['nombre'], $datosCita);
            }
            if ($infoMed) {
                MailService::nuevaCitaMedico($infoMed['email'], $infoMed['nombre'], $datosCita);
            }
        } catch (Throwable $e) {
            error_log('[citas.php] Error al enviar correos: ' . $e->getMessage());
        }

        responder(201, $nueva);
        break;

    case 'PUT':
        if (!$id) responder(400, ['error' => 'Se requiere el parámetro ?id=']);
        $accionPut = isset($_GET['accion']) ? trim($_GET['accion']) : null;

        // ── Confirmar cita Pendiente (médico) ────────────────────────────────
        if ($accionPut === 'confirmar') {
            if (!isset($_SESSION['id_usuario'])) {
                responder(401, ['error' => 'No autenticado']);
            }
            $stmtM = $db->prepare('SELECT id_medico FROM medicos WHERE id_usuario = ? LIMIT 1');
            $stmtM->execute([$_SESSION['id_usuario']]);
            $medico = $stmtM->fetch();
            if (!$medico) {
                responder(403, ['error' => 'Solo médicos pueden confirmar citas']);
            }

            $stmt = $db->prepare(
                "UPDATE citas SET id_estado = 2
                 WHERE id_cita = ? AND id_medico = ? AND id_estado = 1
                 RETURNING id_cita"
            );
            $stmt->execute([$id, $medico['id_medico']]);
            if (!$stmt->fetch()) {
                responder(409, ['error' => 'No se puede confirmar (debe estar Pendiente y pertenecer a este médico)']);
            }

            responder(200, ['mensaje' => 'Cita confirmada']);
        }

        // ── Cancelar cita Pendiente directamente ─────────────────────────────
        if ($accionPut === 'cancelar') {
            $stmt = $db->prepare(
                "UPDATE citas SET id_estado = 3
                 WHERE id_cita = ? AND id_estado = 1 RETURNING id_cita"
            );
            $stmt->execute([$id]);
            $stmt->fetch()
                ? responder(200, ['mensaje' => 'Cita cancelada'])
                : responder(409, ['error' => 'La cita no puede cancelarse en su estado actual']);
        }

        // ── Cambiar fecha/hora de una cita Pendiente ─────────────────────────
        if ($accionPut === 'cambiar-fecha') {
            $d = leerBody();
            if (empty($d['fecha_cita']) || empty($d['hora_inicio'])) {
                responder(422, ['error' => 'Se requieren fecha_cita y hora_inicio']);
            }
            $qDur = $db->prepare(
                "SELECT m.duracion_consulta FROM citas c
                 JOIN medicos m ON m.id_medico = c.id_medico WHERE c.id_cita = ?"
            );
            $qDur->execute([$id]);
            $durRow   = $qDur->fetch();
            $duracion = (int)($durRow['duracion_consulta'] ?? 30);
            $tsInicio = strtotime($d['fecha_cita'] . ' ' . $d['hora_inicio']);
            $horaFin  = date('H:i:s', $tsInicio + $duracion * 60);

            $stmt = $db->prepare(
                "UPDATE citas SET fecha_cita = ?, hora_inicio = ?, hora_fin = ?
                 WHERE id_cita = ? AND id_estado = 1 RETURNING id_cita"
            );
            $stmt->execute([$d['fecha_cita'], $d['hora_inicio'], $horaFin, $id]);
            $stmt->fetch()
                ? responder(200, ['mensaje' => 'Fecha actualizada'])
                : responder(409, ['error' => 'No se pudo actualizar. La cita debe estar en estado Pendiente.']);
        }

        // ── Solicitar cancelación de una cita Confirmada ─────────────────────
        if ($accionPut === 'solicitar-cancelacion') {
            $stmtSol = $db->query(
                "SELECT id_estado FROM estados_cita WHERE nombre_estado = 'Solicitud de cancelación' LIMIT 1"
            );
            $recSol = $stmtSol->fetch();
            if (!$recSol) {
                $db->exec("INSERT INTO estados_cita (nombre_estado, descripcion, color)
                           VALUES ('Solicitud de cancelación', 'El paciente solicitó cancelar la cita confirmada', '#f59e0b')");
                $stmtSol = $db->query(
                    "SELECT id_estado FROM estados_cita WHERE nombre_estado = 'Solicitud de cancelación' LIMIT 1"
                );
                $recSol = $stmtSol->fetch();
            }
            $idSolicitud = $recSol['id_estado'];

            $stmt = $db->prepare(
                "UPDATE citas SET id_estado = ?
                 WHERE id_cita = ? AND id_estado = 2
                 RETURNING id_cita, id_paciente, id_medico, fecha_cita, hora_inicio, motivo_consulta, token_accion"
            );
            $stmt->execute([$idSolicitud, $id]);
            $cita = $stmt->fetch();
            if (!$cita) {
                responder(409, ['error' => 'La cita no está en estado Confirmada']);
            }

            try {
                require_once __DIR__ . '/../admin/services/MailService.php';

                $qPac = $db->prepare(
                    "SELECT up.email, up.nombre || ' ' || up.apellido_paterno AS nombre
                     FROM pacientes p JOIN usuarios up ON up.id_usuario = p.id_usuario
                     WHERE p.id_paciente = ?"
                );
                $qPac->execute([$cita['id_paciente']]);
                $infoPac = $qPac->fetch();

                $qMed = $db->prepare(
                    "SELECT um.email, um.nombre || ' ' || um.apellido_paterno AS nombre,
                            um.genero, esp.nombre_especialidad
                     FROM medicos m
                     JOIN usuarios um ON um.id_usuario = m.id_usuario
                     JOIN especialidades esp ON esp.id_especialidad = m.id_especialidad
                     WHERE m.id_medico = ?"
                );
                $qMed->execute([$cita['id_medico']]);
                $infoMed = $qMed->fetch();

                $meses = ['enero','febrero','marzo','abril','mayo','junio',
                          'julio','agosto','septiembre','octubre','noviembre','diciembre'];
                [$y, $m, $dia] = explode('-', $cita['fecha_cita']);
                $fechaLeg = (int)$dia . ' de ' . $meses[(int)$m - 1] . ' de ' . $y;

                [$hh, $mm] = explode(':', $cita['hora_inicio']);
                $h12  = (int)$hh % 12 ?: 12;
                $ampm = (int)$hh >= 12 ? 'PM' : 'AM';
                $horaLeg = sprintf('%d:%s %s', $h12, $mm, $ampm);

                $scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $baseUrl = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');

                $datosCita = [
                    'fecha'         => $fechaLeg,
                    'hora'          => $horaLeg,
                    'medico'        => $infoMed['nombre']              ?? '',
                    'genero_medico' => $infoMed['genero']              ?? null,
                    'paciente'      => $infoPac['nombre']              ?? '',
                    'especialidad'  => $infoMed['nombre_especialidad'] ?? '',
                    'motivo'        => $cita['motivo_consulta']        ?? 'No especificado',
                    'token'         => $cita['token_accion']           ?? '',
                    'base_url'      => $baseUrl,
                ];

                if ($infoMed) {
                    MailService::solicitudCancelacionMedico($infoMed['email'], $infoMed['nombre'], $datosCita);
                }
            } catch (Throwable $e) {
                error_log('[citas.php] Error email solicitar-cancelacion: ' . $e->getMessage());
            }

            responder(200, ['mensaje' => 'Solicitud de cancelación enviada al médico']);
        }

        // ── Concluir cita (médico) ────────────────────────────────────────────
        if ($accionPut === 'concluir') {
            if (!isset($_SESSION['id_usuario'])) {
                responder(401, ['error' => 'No autenticado']);
            }
            $stmtM = $db->prepare('SELECT id_medico FROM medicos WHERE id_usuario = ? LIMIT 1');
            $stmtM->execute([$_SESSION['id_usuario']]);
            $medico = $stmtM->fetch();
            if (!$medico) {
                responder(403, ['error' => 'Solo médicos pueden concluir citas']);
            }

            // Obtener o crear estado "Concluida"
            $stmtC = $db->query("SELECT id_estado FROM estados_cita WHERE nombre_estado = 'Concluida' LIMIT 1");
            $rec   = $stmtC->fetch();
            if (!$rec) {
                $db->exec("INSERT INTO estados_cita (nombre_estado, descripcion, color) VALUES ('Concluida', 'Consulta finalizada por el médico', '#10b981')");
                $stmtC = $db->query("SELECT id_estado FROM estados_cita WHERE nombre_estado = 'Concluida' LIMIT 1");
                $rec   = $stmtC->fetch();
            }
            $idConcluida = $rec['id_estado'];

            $stmt = $db->prepare(
                "UPDATE citas
                 SET id_estado = ?,
                     costo = COALESCE(NULLIF(costo, 0), (SELECT costo_consulta FROM medicos WHERE id_medico = citas.id_medico))
                 WHERE id_cita = ? AND id_medico = ? AND id_estado = 2
                 RETURNING id_cita"
            );
            $stmt->execute([$idConcluida, $id, $medico['id_medico']]);
            if (!$stmt->fetch()) {
                responder(409, ['error' => 'No se puede concluir (debe estar Confirmada y pertenecer a este médico)']);
            }

            // Enviar correo al paciente con resumen y enlace a la encuesta
            try {
                require_once __DIR__ . '/../admin/services/MailService.php';

                $qInfo = $db->prepare(
                    "SELECT c.fecha_cita, c.hora_inicio,
                            up.email AS email_paciente,
                            up.nombre || ' ' || up.apellido_paterno AS nombre_paciente,
                            um.nombre || ' ' || um.apellido_paterno AS nombre_medico,
                            um.genero AS genero_medico,
                            esp.nombre_especialidad
                     FROM citas c
                     JOIN pacientes    p   ON p.id_paciente   = c.id_paciente
                     JOIN usuarios     up  ON up.id_usuario   = p.id_usuario
                     JOIN medicos      m   ON m.id_medico     = c.id_medico
                     JOIN usuarios     um  ON um.id_usuario   = m.id_usuario
                     JOIN especialidades esp ON esp.id_especialidad = m.id_especialidad
                     WHERE c.id_cita = ?"
                );
                $qInfo->execute([$id]);
                $info = $qInfo->fetch();

                if ($info) {
                    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
                               . '://' . $_SERVER['HTTP_HOST'];

                    // Formatear fecha y hora legibles
                    $meses = ['','enero','febrero','marzo','abril','mayo','junio',
                              'julio','agosto','septiembre','octubre','noviembre','diciembre'];
                    [$y, $mon, $d] = explode('-', $info['fecha_cita']);
                    $fechaLeg = (int)$d . ' de ' . $meses[(int)$mon] . ' de ' . $y;

                    [$hh, $mm] = explode(':', $info['hora_inicio']);
                    $h12      = (int)$hh % 12 ?: 12;
                    $horaLeg  = sprintf('%d:%s %s', $h12, $mm, (int)$hh >= 12 ? 'PM' : 'AM');

                    MailService::citaConcluida(
                        $info['email_paciente'],
                        $info['nombre_paciente'],
                        [
                            'fecha'         => $fechaLeg,
                            'hora'          => $horaLeg,
                            'medico'        => $info['nombre_medico'],
                            'genero_medico' => $info['genero_medico'],
                            'especialidad'  => $info['nombre_especialidad'],
                            'id_cita'       => $id,
                            'base_url'      => $baseUrl,
                        ]
                    );
                }
            } catch (Throwable $e) {
                error_log('[citas.php] Error email concluir: ' . $e->getMessage());
            }

            responder(200, ['mensaje' => 'Cita concluida']);
        }

        // ── Cancelar cita Confirmada (médico, >30 min de anticipación) ──────────
        if ($accionPut === 'cancelar-medico') {
            if (!isset($_SESSION['id_usuario'])) {
                responder(401, ['error' => 'No autenticado']);
            }
            $stmtM = $db->prepare('SELECT id_medico FROM medicos WHERE id_usuario = ? LIMIT 1');
            $stmtM->execute([$_SESSION['id_usuario']]);
            $medico = $stmtM->fetch();
            if (!$medico) {
                responder(403, ['error' => 'Solo médicos pueden usar esta acción']);
            }

            $stmtC = $db->prepare(
                'SELECT id_estado, fecha_cita, hora_inicio FROM citas WHERE id_cita = ? AND id_medico = ? LIMIT 1'
            );
            $stmtC->execute([$id, $medico['id_medico']]);
            $cita = $stmtC->fetch();

            if (!$cita) {
                responder(404, ['error' => 'Cita no encontrada o no pertenece a este médico']);
            }
            if ((int)$cita['id_estado'] !== 2) {
                responder(409, ['error' => 'Solo se pueden cancelar citas en estado Confirmada']);
            }

            $tsCita = strtotime($cita['fecha_cita'] . ' ' . $cita['hora_inicio']);
            if (($tsCita - time()) < 30 * 60) {
                responder(409, ['error' => 'Solo puedes cancelar con más de 30 minutos de anticipación']);
            }

            $stmt = $db->prepare('UPDATE citas SET id_estado = 3 WHERE id_cita = ? RETURNING id_cita');
            $stmt->execute([$id]);
            $stmt->fetch()
                ? responder(200, ['mensaje' => 'Cita cancelada'])
                : responder(409, ['error' => 'No se pudo cancelar la cita']);
        }

        // ── Actualización completa (comportamiento original) ──────────────────
        $d = leerBody();
        $faltantes = [];
        if (empty($d['id_paciente']))  $faltantes[] = 'id_paciente';
        if (empty($d['id_medico']))    $faltantes[] = 'id_medico';
        if (empty($d['id_estado']))    $faltantes[] = 'id_estado';
        if (empty($d['fecha_cita']))   $faltantes[] = 'fecha_cita';
        if (empty($d['hora_inicio']))  $faltantes[] = 'hora_inicio';
        if (empty($d['hora_fin']))     $faltantes[] = 'hora_fin';
        if ($faltantes) {
            responder(422, ['error' => 'Campos requeridos faltantes', 'campos' => $faltantes]);
        }
        $stmt = $db->prepare(
            "UPDATE citas SET
                id_paciente=?, id_medico=?, id_consultorio=?, id_estado=?,
                fecha_cita=?, hora_inicio=?, hora_fin=?, motivo_consulta=?,
                notas_paciente=?, costo=?, codigo_confirmacion=?
             WHERE id_cita=? RETURNING *"
        );
        $stmt->execute([
            (int)$d['id_paciente'],
            (int)$d['id_medico'],
            !empty($d['id_consultorio']) ? (int)$d['id_consultorio'] : null,
            (int)$d['id_estado'],
            $d['fecha_cita'],
            $d['hora_inicio'],
            $d['hora_fin'],
            trim($d['motivo_consulta']     ?? '') ?: null,
            trim($d['notas_paciente']      ?? '') ?: null,
            $d['costo'] !== '' ? (float)($d['costo'] ?? 0) : null,
            trim($d['codigo_confirmacion'] ?? '') ?: null,
            $id,
        ]);
        $fila = $stmt->fetch();
        $fila
            ? responder(200, $fila)
            : responder(404, ['error' => 'Cita no encontrada']);
        break;

    case 'DELETE':
        if (!$id) responder(400, ['error' => 'Se requiere el parámetro ?id=']);
        $stmt = $db->prepare('DELETE FROM citas WHERE id_cita = ? RETURNING id_cita');
        $stmt->execute([$id]);
        $stmt->fetch()
            ? responder(200, ['mensaje' => 'Cita eliminada correctamente'])
            : responder(404, ['error' => 'Cita no encontrada']);
        break;

    default:
        responder(405, ['error' => 'Método no permitido']);
}
