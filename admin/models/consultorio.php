<?php

class Consultorio extends Sistema {

    function __construct() { $this->conectar(); }

    function leer() {
        $stmt = $this->db->prepare('SELECT id_consultorio, numero_consultorio, piso, descripcion, activo FROM consultorios ORDER BY numero_consultorio');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function leerUno(int $id) {
        $stmt = $this->db->prepare('SELECT id_consultorio, numero_consultorio, piso, descripcion, activo FROM consultorios WHERE id_consultorio = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function crear(array $d) {
        $stmt = $this->db->prepare('INSERT INTO consultorios (numero_consultorio, piso, descripcion, activo) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            trim($d['numero_consultorio']),
            ($d['piso'] !== '' ? (int)$d['piso'] : null),
            trim($d['descripcion'] ?? ''),
            isset($d['activo']) ? 'true' : 'false',
        ]);
    }

    function actualizar(array $d) {
        $stmt = $this->db->prepare('UPDATE consultorios SET numero_consultorio = ?, piso = ?, descripcion = ?, activo = ? WHERE id_consultorio = ?');
        $stmt->execute([
            trim($d['numero_consultorio']),
            ($d['piso'] !== '' ? (int)$d['piso'] : null),
            trim($d['descripcion'] ?? ''),
            isset($d['activo']) ? 'true' : 'false',
            (int)$d['id_consultorio'],
        ]);
    }

    function borrar(int $id) {
        $stmt = $this->db->prepare('DELETE FROM consultorios WHERE id_consultorio = ?');
        $stmt->execute([$id]);
    }
}
