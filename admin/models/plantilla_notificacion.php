<?php

class PlantillaNotificacion extends Sistema {

    function __construct() { $this->conectar(); }

    function leer() {
        $stmt = $this->db->prepare(
            "SELECT id_plantilla, codigo_plantilla, nombre_plantilla, asunto,
                    tipo_canal, activa, fecha_creacion
             FROM plantillas_notificaciones
             ORDER BY tipo_canal, nombre_plantilla"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function leerUno(int $id) {
        $stmt = $this->db->prepare(
            'SELECT * FROM plantillas_notificaciones WHERE id_plantilla = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function crear(array $d) {
        $stmt = $this->db->prepare(
            "INSERT INTO plantillas_notificaciones
                (codigo_plantilla, nombre_plantilla, asunto, cuerpo_mensaje, tipo_canal, activa)
             VALUES (?,?,?,?,?,?)"
        );
        $stmt->execute([
            trim($d['codigo_plantilla']),
            trim($d['nombre_plantilla']),
            trim($d['asunto']),
            trim($d['cuerpo_mensaje']),
            $d['tipo_canal'],
            isset($d['activa']) ? 'true' : 'false',
        ]);
    }

    function actualizar(array $d) {
        $stmt = $this->db->prepare(
            "UPDATE plantillas_notificaciones
             SET codigo_plantilla=?, nombre_plantilla=?, asunto=?,
                 cuerpo_mensaje=?, tipo_canal=?, activa=?, fecha_modificacion=NOW()
             WHERE id_plantilla=?"
        );
        $stmt->execute([
            trim($d['codigo_plantilla']),
            trim($d['nombre_plantilla']),
            trim($d['asunto']),
            trim($d['cuerpo_mensaje']),
            $d['tipo_canal'],
            isset($d['activa']) ? 'true' : 'false',
            (int)$d['id_plantilla'],
        ]);
    }

    function borrar(int $id) {
        $stmt = $this->db->prepare('DELETE FROM plantillas_notificaciones WHERE id_plantilla = ?');
        $stmt->execute([$id]);
    }

    function codigoExiste(string $codigo, int $excludeId = 0): bool {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM plantillas_notificaciones WHERE codigo_plantilla = ? AND id_plantilla != ?'
        );
        $stmt->execute([$codigo, $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
