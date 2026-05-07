<?php

class ComentarioInterno extends Sistema {

    function __construct() { $this->conectar(); }

    function leer() {
        $stmt = $this->db->prepare(
            "SELECT ci.id_comentario, ci.comentario, ci.es_importante, ci.fecha_comentario,
                    ci.id_cita, ci.id_paciente,
                    ua.nombre || ' ' || ua.apellido_paterno AS nombre_autor,
                    up.nombre || ' ' || up.apellido_paterno AS nombre_paciente
             FROM comentarios_internos ci
             JOIN usuarios  ua ON ua.id_usuario  = ci.id_usuario_autor
             LEFT JOIN pacientes p  ON p.id_paciente = ci.id_paciente
             LEFT JOIN usuarios  up ON up.id_usuario  = p.id_usuario
             ORDER BY ci.es_importante DESC, ci.fecha_comentario DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function leerUno(int $id) {
        $stmt = $this->db->prepare('SELECT * FROM comentarios_internos WHERE id_comentario = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function crear(array $d) {
        $stmt = $this->db->prepare(
            'INSERT INTO comentarios_internos (id_cita, id_paciente, id_usuario_autor, comentario, es_importante)
             VALUES (?,?,?,?,?)'
        );
        $stmt->execute([
            $d['id_cita']     ? (int)$d['id_cita']     : null,
            $d['id_paciente'] ? (int)$d['id_paciente'] : null,
            (int)$d['id_usuario_autor'],
            trim($d['comentario']),
            isset($d['es_importante']) ? 'true' : 'false',
        ]);
    }

    function actualizar(array $d) {
        $stmt = $this->db->prepare(
            'UPDATE comentarios_internos SET id_cita=?, id_paciente=?, id_usuario_autor=?, comentario=?, es_importante=?
             WHERE id_comentario=?'
        );
        $stmt->execute([
            $d['id_cita']     ? (int)$d['id_cita']     : null,
            $d['id_paciente'] ? (int)$d['id_paciente'] : null,
            (int)$d['id_usuario_autor'],
            trim($d['comentario']),
            isset($d['es_importante']) ? 'true' : 'false',
            (int)$d['id_comentario'],
        ]);
    }

    function borrar(int $id) {
        $stmt = $this->db->prepare('DELETE FROM comentarios_internos WHERE id_comentario = ?');
        $stmt->execute([$id]);
    }

    function todosUsuarios(): array {
        $stmt = $this->db->prepare(
            "SELECT id_usuario, nombre || ' ' || apellido_paterno AS nombre_completo
             FROM usuarios WHERE activo = true ORDER BY apellido_paterno, nombre"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function todosPacientes(): array {
        $stmt = $this->db->prepare(
            "SELECT p.id_paciente, u.nombre || ' ' || u.apellido_paterno AS nombre_completo
             FROM pacientes p
             JOIN usuarios u ON u.id_usuario = p.id_usuario
             ORDER BY u.apellido_paterno, u.nombre"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
