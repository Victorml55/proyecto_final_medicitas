<?php

class Rol extends Sistema {

    function __construct() { $this->conectar(); }

    function leer() {
        $stmt = $this->db->prepare('SELECT id_rol, nombre_rol, descripcion FROM roles ORDER BY nombre_rol');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function leerUno(int $id) {
        $stmt = $this->db->prepare('SELECT id_rol, nombre_rol, descripcion FROM roles WHERE id_rol = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function crear(array $d) {
        $stmt = $this->db->prepare('INSERT INTO roles (nombre_rol, descripcion) VALUES (?, ?)');
        $stmt->execute([trim($d['nombre_rol']), trim($d['descripcion'] ?? '')]);
    }

    function actualizar(array $d) {
        $stmt = $this->db->prepare('UPDATE roles SET nombre_rol = ?, descripcion = ? WHERE id_rol = ?');
        $stmt->execute([trim($d['nombre_rol']), trim($d['descripcion'] ?? ''), (int)$d['id_rol']]);
    }

    function borrar(int $id) {
        $stmt = $this->db->prepare('DELETE FROM roles WHERE id_rol = ?');
        $stmt->execute([$id]);
    }
}
