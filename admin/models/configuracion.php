<?php

class Configuracion extends Sistema {

    const TIPOS = ['string', 'int', 'boolean', 'json'];

    function __construct() { $this->conectar(); }

    function leer() {
        $stmt = $this->db->prepare('SELECT id_config, clave, valor, descripcion, tipo_dato FROM configuracion_sistema ORDER BY clave');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function leerUno(int $id) {
        $stmt = $this->db->prepare('SELECT id_config, clave, valor, descripcion, tipo_dato FROM configuracion_sistema WHERE id_config = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function crear(array $d) {
        $stmt = $this->db->prepare('INSERT INTO configuracion_sistema (clave, valor, descripcion, tipo_dato) VALUES (?, ?, ?, ?)');
        $stmt->execute([trim($d['clave']), trim($d['valor']), trim($d['descripcion'] ?? ''), $d['tipo_dato']]);
    }

    function actualizar(array $d) {
        $stmt = $this->db->prepare('UPDATE configuracion_sistema SET clave = ?, valor = ?, descripcion = ?, tipo_dato = ? WHERE id_config = ?');
        $stmt->execute([trim($d['clave']), trim($d['valor']), trim($d['descripcion'] ?? ''), $d['tipo_dato'], (int)$d['id_config']]);
    }

    function borrar(int $id) {
        $stmt = $this->db->prepare('DELETE FROM configuracion_sistema WHERE id_config = ?');
        $stmt->execute([$id]);
    }
}
