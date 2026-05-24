<?php

class Medico extends Sistema {

    function __construct() { $this->conectar(); }

    function leer() {
        $stmt = $this->db->prepare(
            "SELECT m.id_medico, m.cedula_profesional, m.costo_consulta, m.duracion_consulta,
                    m.fecha_inicio, m.activo,
                    u.nombre || ' ' || u.apellido_paterno || ' ' || COALESCE(u.apellido_materno,'') AS nombre_completo,
                    u.email, u.genero, u.foto_perfil,
                    e.nombre_especialidad
             FROM medicos m
             JOIN usuarios     u ON u.id_usuario      = m.id_usuario
             JOIN especialidades e ON e.id_especialidad = m.id_especialidad
             ORDER BY u.apellido_paterno, u.nombre"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function leerUno(int $id) {
        $stmt = $this->db->prepare(
            "SELECT m.*, u.nombre, u.apellido_paterno, u.apellido_materno, u.email, u.telefono
             FROM medicos m
             JOIN usuarios u ON u.id_usuario = m.id_usuario
             WHERE m.id_medico = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function crear(array $d): int {
        $stmt = $this->db->prepare(
            "INSERT INTO medicos
                (id_usuario, id_especialidad, cedula_profesional, universidad,
                 fecha_inicio, biografia, costo_consulta, duracion_consulta, activo)
             VALUES (?,?,?,?,?,?,?,?,?) RETURNING id_medico"
        );
        $stmt->execute([
            (int)$d['id_usuario'],
            (int)$d['id_especialidad'],
            trim($d['cedula_profesional']),
            trim($d['universidad']  ?? '') ?: null,
            trim($d['fecha_inicio'] ?? '') ?: null,
            trim($d['biografia']      ?? '') ?: null,
            (float)$d['costo_consulta'],
            (int)($d['duracion_consulta'] ?: 30),
            isset($d['activo']) ? 'true' : 'false',
        ]);
        return (int)$stmt->fetchColumn();
    }

    function actualizar(array $d) {
        $stmt = $this->db->prepare(
            "UPDATE medicos SET
                id_usuario=?, id_especialidad=?, cedula_profesional=?, universidad=?,
                fecha_inicio=?, biografia=?, costo_consulta=?, duracion_consulta=?, activo=?
             WHERE id_medico=?"
        );
        $stmt->execute([
            (int)$d['id_usuario'],
            (int)$d['id_especialidad'],
            trim($d['cedula_profesional']),
            trim($d['universidad']  ?? '') ?: null,
            trim($d['fecha_inicio'] ?? '') ?: null,
            trim($d['biografia']      ?? '') ?: null,
            (float)$d['costo_consulta'],
            (int)($d['duracion_consulta'] ?: 30),
            isset($d['activo']) ? 'true' : 'false',
            (int)$d['id_medico'],
        ]);
    }

    function borrar(int $id) {
        $stmt = $this->db->prepare('DELETE FROM medicos WHERE id_medico = ?');
        $stmt->execute([$id]);
    }

    function cedulaExiste(string $cedula, int $excludeId = 0): bool {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM medicos WHERE cedula_profesional = ? AND id_medico != ?'
        );
        $stmt->execute([$cedula, $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    function usuarioYaEsMedico(int $idUsuario, int $excludeId = 0): bool {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM medicos WHERE id_usuario = ? AND id_medico != ?'
        );
        $stmt->execute([$idUsuario, $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    function todosUsuarios(): array {
        $stmt = $this->db->prepare(
            "SELECT id_usuario, nombre || ' ' || apellido_paterno || ' ' || COALESCE(apellido_materno,'') AS nombre_completo, email
             FROM usuarios WHERE activo = true ORDER BY apellido_paterno, nombre"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function todasEspecialidades(): array {
        $stmt = $this->db->prepare(
            'SELECT id_especialidad, nombre_especialidad FROM especialidades WHERE activo = true ORDER BY nombre_especialidad'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
