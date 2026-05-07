<?php

class Permiso extends Sistema {

    function __construct() { $this->conectar(); }

    function leer() {
        $stmt = $this->db->prepare(
            'SELECT id_permiso, nombre_permiso, descripcion, modulo, activo
             FROM permisos ORDER BY modulo, nombre_permiso'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function leerUno(int $id) {
        $stmt = $this->db->prepare(
            'SELECT id_permiso, nombre_permiso, descripcion, modulo, activo
             FROM permisos WHERE id_permiso = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function crear(array $d) {
        $stmt = $this->db->prepare(
            'INSERT INTO permisos (nombre_permiso, descripcion, modulo, activo)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            trim($d['nombre_permiso']),
            trim($d['descripcion'] ?? '') ?: null,
            trim($d['modulo']      ?? '') ?: null,
            isset($d['activo']) ? 'true' : 'false',
        ]);
    }

    function actualizar(array $d) {
        $stmt = $this->db->prepare(
            'UPDATE permisos SET nombre_permiso=?, descripcion=?, modulo=?, activo=?
             WHERE id_permiso=?'
        );
        $stmt->execute([
            trim($d['nombre_permiso']),
            trim($d['descripcion'] ?? '') ?: null,
            trim($d['modulo']      ?? '') ?: null,
            isset($d['activo']) ? 'true' : 'false',
            (int)$d['id_permiso'],
        ]);
    }

    function borrar(int $id) {
        $stmt = $this->db->prepare('DELETE FROM permisos WHERE id_permiso = ?');
        $stmt->execute([$id]);
    }

    function nombreExiste(string $nombre, int $excludeId = 0): bool {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM permisos WHERE nombre_permiso = ? AND id_permiso != ?'
        );
        $stmt->execute([$nombre, $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    function modulosUnicos(): array {
        $stmt = $this->db->prepare(
            'SELECT DISTINCT modulo FROM permisos WHERE modulo IS NOT NULL ORDER BY modulo'
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
