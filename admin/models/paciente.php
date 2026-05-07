<?php

class Paciente extends Sistema {

    function __construct() { $this->conectar(); }

    function leer() {
        $stmt = $this->db->prepare(
            "SELECT p.id_paciente, p.numero_expediente, p.tipo_sangre, p.seguro_medico,
                    u.nombre || ' ' || u.apellido_paterno || ' ' || COALESCE(u.apellido_materno,'') AS nombre_completo,
                    u.email, u.telefono, u.activo
             FROM pacientes p
             JOIN usuarios u ON u.id_usuario = p.id_usuario
             ORDER BY u.apellido_paterno, u.nombre"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function leerUno(int $id) {
        $stmt = $this->db->prepare(
            "SELECT p.*, u.nombre, u.apellido_paterno, u.apellido_materno, u.email, u.telefono
             FROM pacientes p
             JOIN usuarios u ON u.id_usuario = p.id_usuario
             WHERE p.id_paciente = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function crear(array $d) {
        $stmt = $this->db->prepare(
            "INSERT INTO pacientes
                (id_usuario, numero_expediente, tipo_sangre, alergias, enfermedades_cronicas,
                 contacto_emergencia_nombre, contacto_emergencia_telefono, seguro_medico, numero_poliza)
             VALUES (?,?,?,?,?,?,?,?,?)"
        );
        $stmt->execute([
            (int)$d['id_usuario'],
            trim($d['numero_expediente'] ?? '') ?: null,
            trim($d['tipo_sangre']       ?? '') ?: null,
            trim($d['alergias']          ?? '') ?: null,
            trim($d['enfermedades_cronicas'] ?? '') ?: null,
            trim($d['contacto_emergencia_nombre']    ?? '') ?: null,
            trim($d['contacto_emergencia_telefono']  ?? '') ?: null,
            trim($d['seguro_medico']     ?? '') ?: null,
            trim($d['numero_poliza']     ?? '') ?: null,
        ]);
    }

    function actualizar(array $d) {
        $stmt = $this->db->prepare(
            "UPDATE pacientes SET
                id_usuario=?, numero_expediente=?, tipo_sangre=?, alergias=?,
                enfermedades_cronicas=?, contacto_emergencia_nombre=?,
                contacto_emergencia_telefono=?, seguro_medico=?, numero_poliza=?
             WHERE id_paciente=?"
        );
        $stmt->execute([
            (int)$d['id_usuario'],
            trim($d['numero_expediente'] ?? '') ?: null,
            trim($d['tipo_sangre']       ?? '') ?: null,
            trim($d['alergias']          ?? '') ?: null,
            trim($d['enfermedades_cronicas'] ?? '') ?: null,
            trim($d['contacto_emergencia_nombre']    ?? '') ?: null,
            trim($d['contacto_emergencia_telefono']  ?? '') ?: null,
            trim($d['seguro_medico']     ?? '') ?: null,
            trim($d['numero_poliza']     ?? '') ?: null,
            (int)$d['id_paciente'],
        ]);
    }

    function borrar(int $id) {
        $stmt = $this->db->prepare('DELETE FROM pacientes WHERE id_paciente = ?');
        $stmt->execute([$id]);
    }

    function expedienteExiste(string $exp, int $excludeId = 0): bool {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM pacientes WHERE numero_expediente = ? AND id_paciente != ?'
        );
        $stmt->execute([$exp, $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    function usuarioYaEsPaciente(int $idUsuario, int $excludeId = 0): bool {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM pacientes WHERE id_usuario = ? AND id_paciente != ?'
        );
        $stmt->execute([$idUsuario, $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    function todosUsuarios(): array {
        $stmt = $this->db->prepare(
            "SELECT id_usuario, nombre || ' ' || apellido_paterno || ' ' || COALESCE(apellido_materno,'') AS nombre_completo, email
             FROM usuarios WHERE activo = true ORDER BY apellido_paterno, nombre"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
