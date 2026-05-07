<?php

class Especialidad extends Sistema {

    function __construct() { $this->conectar(); }

    function leer() {
        $stmt = $this->db->prepare('SELECT id_especialidad, nombre_especialidad, descripcion, activo FROM especialidades ORDER BY nombre_especialidad');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function leerUno(int $id) {
        $stmt = $this->db->prepare('SELECT id_especialidad, nombre_especialidad, descripcion, activo FROM especialidades WHERE id_especialidad = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function crear(array $d) {
        $stmt = $this->db->prepare('INSERT INTO especialidades (nombre_especialidad, descripcion, activo) VALUES (?, ?, ?)');
        $stmt->execute([trim($d['nombre_especialidad']), trim($d['descripcion'] ?? ''), isset($d['activo']) ? 'true' : 'false']);
    }

    function actualizar(array $d) {
        $stmt = $this->db->prepare('UPDATE especialidades SET nombre_especialidad = ?, descripcion = ?, activo = ? WHERE id_especialidad = ?');
        $stmt->execute([trim($d['nombre_especialidad']), trim($d['descripcion'] ?? ''), isset($d['activo']) ? 'true' : 'false', (int)$d['id_especialidad']]);
    }

    function borrar(int $id) {
        $stmt = $this->db->prepare('DELETE FROM especialidades WHERE id_especialidad = ?');
        $stmt->execute([$id]);
    }
}
