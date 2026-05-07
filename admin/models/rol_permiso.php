<?php

class RolPermiso extends Sistema {

    function __construct() { $this->conectar(); }

    function leer() {
        $stmt = $this->db->prepare(
            "SELECT rp.id_rol_permiso, rp.id_rol, rp.id_permiso,
                    r.nombre_rol,
                    p.nombre_permiso, p.modulo,
                    rp.fecha_asignacion
             FROM rol_permisos rp
             JOIN roles    r ON r.id_rol      = rp.id_rol
             JOIN permisos p ON p.id_permiso  = rp.id_permiso
             ORDER BY r.nombre_rol, p.modulo, p.nombre_permiso"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function leerUno(int $id) {
        $stmt = $this->db->prepare(
            'SELECT id_rol_permiso, id_rol, id_permiso FROM rol_permisos WHERE id_rol_permiso = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function crear(array $d) {
        $stmt = $this->db->prepare(
            'INSERT INTO rol_permisos (id_rol, id_permiso) VALUES (?, ?)'
        );
        $stmt->execute([(int)$d['id_rol'], (int)$d['id_permiso']]);
    }

    function actualizar(array $d) {
        $stmt = $this->db->prepare(
            'UPDATE rol_permisos SET id_rol=?, id_permiso=? WHERE id_rol_permiso=?'
        );
        $stmt->execute([(int)$d['id_rol'], (int)$d['id_permiso'], (int)$d['id_rol_permiso']]);
    }

    function borrar(int $id) {
        $stmt = $this->db->prepare('DELETE FROM rol_permisos WHERE id_rol_permiso = ?');
        $stmt->execute([$id]);
    }

    function asignacionExiste(int $idRol, int $idPermiso, int $excludeId = 0): bool {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM rol_permisos
             WHERE id_rol = ? AND id_permiso = ? AND id_rol_permiso != ?'
        );
        $stmt->execute([$idRol, $idPermiso, $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    function todosRoles(): array {
        $stmt = $this->db->prepare('SELECT id_rol, nombre_rol FROM roles ORDER BY nombre_rol');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function todosPermisos(): array {
        $stmt = $this->db->prepare(
            'SELECT id_permiso, nombre_permiso, modulo
             FROM permisos WHERE activo = true ORDER BY modulo, nombre_permiso'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
