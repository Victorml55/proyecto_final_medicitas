<?php

class DiaNoLaborable extends Sistema {

    function __construct() { $this->conectar(); }

    function leer() {
        $stmt = $this->db->prepare(
            "SELECT d.id_dia_no_laborable, d.fecha, d.motivo, d.es_recurrente, d.id_medico,
                    u.nombre || ' ' || u.apellido_paterno AS nombre_medico
             FROM dias_no_laborables d
             LEFT JOIN medicos  m ON m.id_medico  = d.id_medico
             LEFT JOIN usuarios u ON u.id_usuario = m.id_usuario
             ORDER BY d.fecha DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function leerUno(int $id) {
        $stmt = $this->db->prepare(
            'SELECT * FROM dias_no_laborables WHERE id_dia_no_laborable = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function crear(array $d) {
        $stmt = $this->db->prepare(
            'INSERT INTO dias_no_laborables (id_medico, fecha, motivo, es_recurrente)
             VALUES (?,?,?,?)'
        );
        $stmt->execute([
            $d['id_medico'] ? (int)$d['id_medico'] : null,
            $d['fecha'],
            trim($d['motivo'] ?? '') ?: null,
            isset($d['es_recurrente']) ? 'true' : 'false',
        ]);
    }

    function actualizar(array $d) {
        $stmt = $this->db->prepare(
            'UPDATE dias_no_laborables SET id_medico=?, fecha=?, motivo=?, es_recurrente=?
             WHERE id_dia_no_laborable=?'
        );
        $stmt->execute([
            $d['id_medico'] ? (int)$d['id_medico'] : null,
            $d['fecha'],
            trim($d['motivo'] ?? '') ?: null,
            isset($d['es_recurrente']) ? 'true' : 'false',
            (int)$d['id_dia_no_laborable'],
        ]);
    }

    function borrar(int $id) {
        $stmt = $this->db->prepare('DELETE FROM dias_no_laborables WHERE id_dia_no_laborable = ?');
        $stmt->execute([$id]);
    }

    function todosMedicos(): array {
        $stmt = $this->db->prepare(
            "SELECT m.id_medico,
                    u.nombre || ' ' || u.apellido_paterno AS nombre_medico,
                    e.nombre_especialidad
             FROM medicos m
             JOIN usuarios      u ON u.id_usuario      = m.id_usuario
             JOIN especialidades e ON e.id_especialidad = m.id_especialidad
             WHERE m.activo = true
             ORDER BY u.apellido_paterno, u.nombre"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
