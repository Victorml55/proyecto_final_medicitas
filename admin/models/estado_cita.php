<?php

class EstadoCita extends Sistema {

    function __construct() { $this->conectar(); }

    function leer() {
        $stmt = $this->db->prepare('SELECT id_estado, nombre_estado, descripcion, color FROM estados_cita ORDER BY nombre_estado');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function leerUno(int $id) {
        $stmt = $this->db->prepare('SELECT id_estado, nombre_estado, descripcion, color FROM estados_cita WHERE id_estado = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function crear(array $d) {
        $color = trim($d['color'] ?? '');
        $stmt = $this->db->prepare('INSERT INTO estados_cita (nombre_estado, descripcion, color) VALUES (?, ?, ?)');
        $stmt->execute([trim($d['nombre_estado']), trim($d['descripcion'] ?? ''), ($color !== '' ? $color : null)]);
    }

    function actualizar(array $d) {
        $color = trim($d['color'] ?? '');
        $stmt = $this->db->prepare('UPDATE estados_cita SET nombre_estado = ?, descripcion = ?, color = ? WHERE id_estado = ?');
        $stmt->execute([trim($d['nombre_estado']), trim($d['descripcion'] ?? ''), ($color !== '' ? $color : null), (int)$d['id_estado']]);
    }

    function borrar(int $id) {
        $stmt = $this->db->prepare('DELETE FROM estados_cita WHERE id_estado = ?');
        $stmt->execute([$id]);
    }
}
