<?php

class Valoracion extends Sistema {

    function __construct() { $this->conectar(); }

    function leer() {
        $stmt = $this->db->prepare(
            "SELECT v.id_valoracion, v.calificacion, v.comentario, v.anonimo, v.fecha_valoracion,
                    up.nombre || ' ' || up.apellido_paterno AS nombre_paciente,
                    um.nombre || ' ' || um.apellido_paterno AS nombre_medico,
                    v.id_cita
             FROM valoraciones v
             JOIN pacientes p  ON p.id_paciente = v.id_paciente
             JOIN usuarios  up ON up.id_usuario  = p.id_usuario
             JOIN medicos   m  ON m.id_medico    = v.id_medico
             JOIN usuarios  um ON um.id_usuario  = m.id_usuario
             ORDER BY v.fecha_valoracion DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function leerUno(int $id) {
        $stmt = $this->db->prepare('SELECT * FROM valoraciones WHERE id_valoracion = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function crear(array $d) {
        $stmt = $this->db->prepare(
            'INSERT INTO valoraciones (id_cita, id_paciente, id_medico, calificacion, comentario, anonimo)
             VALUES (?,?,?,?,?,?)'
        );
        $stmt->execute([
            (int)$d['id_cita'],
            (int)$d['id_paciente'],
            (int)$d['id_medico'],
            (int)$d['calificacion'],
            trim($d['comentario'] ?? '') ?: null,
            isset($d['anonimo']) ? 'true' : 'false',
        ]);
    }

    function actualizar(array $d) {
        $stmt = $this->db->prepare(
            'UPDATE valoraciones SET calificacion=?, comentario=?, anonimo=? WHERE id_valoracion=?'
        );
        $stmt->execute([
            (int)$d['calificacion'],
            trim($d['comentario'] ?? '') ?: null,
            isset($d['anonimo']) ? 'true' : 'false',
            (int)$d['id_valoracion'],
        ]);
    }

    function borrar(int $id) {
        $stmt = $this->db->prepare('DELETE FROM valoraciones WHERE id_valoracion = ?');
        $stmt->execute([$id]);
    }

    function citaExisteValoracion(int $idCita, int $excludeId = 0): bool {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM valoraciones WHERE id_cita = ? AND id_valoracion != ?'
        );
        $stmt->execute([$idCita, $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    function todasCitas(): array {
        $stmt = $this->db->prepare(
            "SELECT c.id_cita, c.fecha_cita,
                    up.nombre || ' ' || up.apellido_paterno AS nombre_paciente,
                    um.nombre || ' ' || um.apellido_paterno AS nombre_medico,
                    p.id_paciente, m.id_medico
             FROM citas c
             JOIN pacientes p  ON p.id_paciente = c.id_paciente
             JOIN usuarios  up ON up.id_usuario  = p.id_usuario
             JOIN medicos   m  ON m.id_medico    = c.id_medico
             JOIN usuarios  um ON um.id_usuario  = m.id_usuario
             ORDER BY c.fecha_cita DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
