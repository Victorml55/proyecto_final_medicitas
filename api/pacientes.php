<?php
/**
 * API RESTful – Pacientes
 *
 * GET    /api/pacientes.php        → lista todos (con nombre del usuario)
 * GET    /api/pacientes.php?id=1   → obtiene uno
 * POST   /api/pacientes.php        → crea nuevo
 * PUT    /api/pacientes.php?id=1   → actualiza
 * DELETE /api/pacientes.php?id=1   → elimina
 *
 * Campos POST/PUT (JSON):
 *   id_usuario*, numero_expediente, tipo_sangre, alergias,
 *   enfermedades_cronicas, contacto_emergencia_nombre,
 *   contacto_emergencia_telefono, seguro_medico, numero_poliza
 */

require_once __DIR__ . '/config.php';

$db     = conectarDB();
$metodo = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($metodo) {

    case 'GET':
        if ($id) {
            $stmt = $db->prepare(
                "SELECT p.*, u.nombre, u.apellido_paterno, u.apellido_materno,
                        u.email, u.telefono
                 FROM pacientes p
                 JOIN usuarios u ON u.id_usuario = p.id_usuario
                 WHERE p.id_paciente = ?"
            );
            $stmt->execute([$id]);
            $fila = $stmt->fetch();
            $fila
                ? responder(200, $fila)
                : responder(404, ['error' => 'Paciente no encontrado']);
        } else {
            $stmt = $db->query(
                "SELECT p.id_paciente, p.numero_expediente, p.tipo_sangre, p.seguro_medico,
                        u.nombre || ' ' || u.apellido_paterno AS nombre_completo,
                        u.email, u.telefono, u.activo
                 FROM pacientes p
                 JOIN usuarios u ON u.id_usuario = p.id_usuario
                 ORDER BY u.apellido_paterno, u.nombre"
            );
            responder(200, $stmt->fetchAll());
        }
        break;

    case 'POST':
        $d = leerBody();
        if (empty($d['id_usuario'])) {
            responder(422, ['error' => 'El campo id_usuario es requerido']);
        }
        $stmt = $db->prepare(
            "INSERT INTO pacientes
                (id_usuario, numero_expediente, tipo_sangre, alergias,
                 enfermedades_cronicas, contacto_emergencia_nombre,
                 contacto_emergencia_telefono, seguro_medico, numero_poliza)
             VALUES (?,?,?,?,?,?,?,?,?) RETURNING *"
        );
        $stmt->execute([
            (int)$d['id_usuario'],
            trim($d['numero_expediente']             ?? '') ?: null,
            trim($d['tipo_sangre']                   ?? '') ?: null,
            trim($d['alergias']                      ?? '') ?: null,
            trim($d['enfermedades_cronicas']         ?? '') ?: null,
            trim($d['contacto_emergencia_nombre']    ?? '') ?: null,
            trim($d['contacto_emergencia_telefono']  ?? '') ?: null,
            trim($d['seguro_medico']                 ?? '') ?: null,
            trim($d['numero_poliza']                 ?? '') ?: null,
        ]);
        responder(201, $stmt->fetch());
        break;

    case 'PUT':
        if (!$id) responder(400, ['error' => 'Se requiere el parámetro ?id=']);
        $d = leerBody();
        if (empty($d['id_usuario'])) {
            responder(422, ['error' => 'El campo id_usuario es requerido']);
        }
        $stmt = $db->prepare(
            "UPDATE pacientes SET
                id_usuario=?, numero_expediente=?, tipo_sangre=?, alergias=?,
                enfermedades_cronicas=?, contacto_emergencia_nombre=?,
                contacto_emergencia_telefono=?, seguro_medico=?, numero_poliza=?
             WHERE id_paciente=? RETURNING *"
        );
        $stmt->execute([
            (int)$d['id_usuario'],
            trim($d['numero_expediente']             ?? '') ?: null,
            trim($d['tipo_sangre']                   ?? '') ?: null,
            trim($d['alergias']                      ?? '') ?: null,
            trim($d['enfermedades_cronicas']         ?? '') ?: null,
            trim($d['contacto_emergencia_nombre']    ?? '') ?: null,
            trim($d['contacto_emergencia_telefono']  ?? '') ?: null,
            trim($d['seguro_medico']                 ?? '') ?: null,
            trim($d['numero_poliza']                 ?? '') ?: null,
            $id,
        ]);
        $fila = $stmt->fetch();
        $fila
            ? responder(200, $fila)
            : responder(404, ['error' => 'Paciente no encontrado']);
        break;

    case 'DELETE':
        if (!$id) responder(400, ['error' => 'Se requiere el parámetro ?id=']);
        $stmt = $db->prepare('DELETE FROM pacientes WHERE id_paciente = ? RETURNING id_paciente');
        $stmt->execute([$id]);
        $stmt->fetch()
            ? responder(200, ['mensaje' => 'Paciente eliminado correctamente'])
            : responder(404, ['error' => 'Paciente no encontrado']);
        break;

    default:
        responder(405, ['error' => 'Método no permitido']);
}
