<?php

class UsuarioRol extends Sistema {

    function __construct() { $this->conectar(); }

    /** Listado con nombre del usuario y del rol */
    function leer() {
        $stmt = $this->db->prepare(
            "SELECT ur.id_usuario_rol, ur.id_usuario, ur.id_rol,
                    u.nombre || ' ' || u.apellido_paterno AS nombre_usuario,
                    u.email,
                    r.nombre_rol,
                    ur.fecha_asignacion
             FROM usuario_roles ur
             JOIN usuarios u ON u.id_usuario = ur.id_usuario
             JOIN roles    r ON r.id_rol     = ur.id_rol
             ORDER BY u.apellido_paterno, u.nombre, r.nombre_rol"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function leerUno(int $id) {
        $stmt = $this->db->prepare(
            'SELECT id_usuario_rol, id_usuario, id_rol
             FROM usuario_roles WHERE id_usuario_rol = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function crear(array $d) {
        $stmt = $this->db->prepare(
            'INSERT INTO usuario_roles (id_usuario, id_rol) VALUES (?, ?)'
        );
        $stmt->execute([(int)$d['id_usuario'], (int)$d['id_rol']]);
    }

    function actualizar(array $d) {
        $stmt = $this->db->prepare(
            'UPDATE usuario_roles SET id_usuario=?, id_rol=? WHERE id_usuario_rol=?'
        );
        $stmt->execute([(int)$d['id_usuario'], (int)$d['id_rol'], (int)$d['id_usuario_rol']]);
    }

    function borrar(int $id) {
        $stmt = $this->db->prepare('DELETE FROM usuario_roles WHERE id_usuario_rol = ?');
        $stmt->execute([$id]);
    }

    function asignacionExiste(int $idUsuario, int $idRol, int $excludeId = 0): bool {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM usuario_roles
             WHERE id_usuario = ? AND id_rol = ? AND id_usuario_rol != ?'
        );
        $stmt->execute([$idUsuario, $idRol, $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /** Para los selects del formulario */
    function todosUsuarios(): array {
        $stmt = $this->db->prepare(
            "SELECT id_usuario, nombre || ' ' || apellido_paterno AS nombre_completo, email
             FROM usuarios WHERE activo = true ORDER BY apellido_paterno, nombre"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function todosRoles(): array {
        $stmt = $this->db->prepare(
            'SELECT id_rol, nombre_rol FROM roles ORDER BY nombre_rol'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
