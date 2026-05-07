<?php

class Receta extends Sistema {

    function __construct() { $this->conectar(); }

    function leer() {
        $stmt = $this->db->prepare(
            "SELECT r.id_receta, r.diagnostico, r.fecha_emision,
                    c.id_cita, c.fecha_cita,
                    up.nombre || ' ' || up.apellido_paterno AS nombre_paciente,
                    um.nombre || ' ' || um.apellido_paterno AS nombre_medico,
                    COUNT(mr.id_medicamento_receta) AS num_medicamentos
             FROM recetas r
             JOIN citas      c   ON c.id_cita      = r.id_cita
             JOIN pacientes  p   ON p.id_paciente  = c.id_paciente
             JOIN usuarios   up  ON up.id_usuario  = p.id_usuario
             JOIN medicos    m   ON m.id_medico    = c.id_medico
             JOIN usuarios   um  ON um.id_usuario  = m.id_usuario
             LEFT JOIN medicamentos_receta mr ON mr.id_receta = r.id_receta
             GROUP BY r.id_receta, c.id_cita, c.fecha_cita, up.nombre, up.apellido_paterno, um.nombre, um.apellido_paterno
             ORDER BY r.fecha_emision DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function leerUno(int $id) {
        $stmt = $this->db->prepare('SELECT * FROM recetas WHERE id_receta = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function leerMedicamentos(int $idReceta): array {
        $stmt = $this->db->prepare(
            'SELECT * FROM medicamentos_receta WHERE id_receta = ? ORDER BY id_medicamento_receta'
        );
        $stmt->execute([$idReceta]);
        return $stmt->fetchAll();
    }

    function crear(array $d): int {
        $stmt = $this->db->prepare(
            'INSERT INTO recetas (id_cita, diagnostico, indicaciones_generales) VALUES (?,?,?)'
        );
        $stmt->execute([
            (int)$d['id_cita'],
            trim($d['diagnostico']            ?? '') ?: null,
            trim($d['indicaciones_generales'] ?? '') ?: null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    function crearMedicamento(int $idReceta, array $med) {
        $stmt = $this->db->prepare(
            'INSERT INTO medicamentos_receta
                (id_receta, nombre_medicamento, presentacion, dosis, frecuencia, duracion, indicaciones)
             VALUES (?,?,?,?,?,?,?)'
        );
        $stmt->execute([
            $idReceta,
            trim($med['nombre_medicamento']),
            trim($med['presentacion'] ?? '') ?: null,
            trim($med['dosis']),
            trim($med['frecuencia']),
            trim($med['duracion']      ?? '') ?: null,
            trim($med['indicaciones']  ?? '') ?: null,
        ]);
    }

    function actualizar(array $d) {
        $stmt = $this->db->prepare(
            'UPDATE recetas SET id_cita=?, diagnostico=?, indicaciones_generales=? WHERE id_receta=?'
        );
        $stmt->execute([
            (int)$d['id_cita'],
            trim($d['diagnostico']            ?? '') ?: null,
            trim($d['indicaciones_generales'] ?? '') ?: null,
            (int)$d['id_receta'],
        ]);
    }

    function borrarMedicamentos(int $idReceta) {
        $stmt = $this->db->prepare('DELETE FROM medicamentos_receta WHERE id_receta = ?');
        $stmt->execute([$idReceta]);
    }

    function borrar(int $id) {
        $stmt = $this->db->prepare('DELETE FROM recetas WHERE id_receta = ?');
        $stmt->execute([$id]);
    }

    function todasCitas(): array {
        $stmt = $this->db->prepare(
            "SELECT c.id_cita, c.fecha_cita,
                    up.nombre || ' ' || up.apellido_paterno AS nombre_paciente,
                    um.nombre || ' ' || um.apellido_paterno AS nombre_medico
             FROM citas c
             JOIN pacientes p  ON p.id_paciente = c.id_paciente
             JOIN usuarios  up ON up.id_usuario = p.id_usuario
             JOIN medicos   m  ON m.id_medico   = c.id_medico
             JOIN usuarios  um ON um.id_usuario = m.id_usuario
             ORDER BY c.fecha_cita DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
