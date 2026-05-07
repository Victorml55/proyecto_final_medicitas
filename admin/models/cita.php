<?php

class Cita extends Sistema {

    function __construct() { $this->conectar(); }

    function leer(): array {
        $stmt = $this->db->prepare(
            "SELECT c.id_cita, c.fecha_cita, c.hora_inicio, c.hora_fin,
                    c.motivo_consulta, c.costo, c.codigo_confirmacion,
                    up.nombre || ' ' || up.apellido_paterno AS nombre_paciente,
                    um.nombre || ' ' || um.apellido_paterno AS nombre_medico,
                    co.numero_consultorio,
                    e.nombre_estado
             FROM citas c
             JOIN pacientes  p   ON p.id_paciente  = c.id_paciente
             JOIN usuarios   up  ON up.id_usuario  = p.id_usuario
             JOIN medicos    m   ON m.id_medico    = c.id_medico
             JOIN usuarios   um  ON um.id_usuario  = m.id_usuario
             JOIN estados_cita e ON e.id_estado    = c.id_estado
             LEFT JOIN consultorios co ON co.id_consultorio = c.id_consultorio
             ORDER BY c.fecha_cita DESC, c.hora_inicio DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function leerUno(int $id): array|false {
        $stmt = $this->db->prepare('SELECT * FROM citas WHERE id_cita = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function crear(array $d): void {
        $stmt = $this->db->prepare(
            "INSERT INTO citas
                (id_paciente, id_medico, id_consultorio, id_estado,
                 fecha_cita, hora_inicio, hora_fin, motivo_consulta,
                 notas_paciente, costo, codigo_confirmacion)
             VALUES (?,?,?,?,?,?,?,?,?,?,?)"
        );
        $stmt->execute([
            (int)$d['id_paciente'],
            (int)$d['id_medico'],
            !empty($d['id_consultorio']) ? (int)$d['id_consultorio'] : null,
            (int)$d['id_estado'],
            $d['fecha_cita'],
            $d['hora_inicio'],
            $d['hora_fin'],
            trim($d['motivo_consulta']  ?? '') ?: null,
            trim($d['notas_paciente']   ?? '') ?: null,
            $d['costo'] !== '' ? (float)$d['costo'] : null,
            trim($d['codigo_confirmacion'] ?? '') ?: null,
        ]);
    }

    function actualizar(array $d): void {
        $stmt = $this->db->prepare(
            "UPDATE citas SET
                id_paciente=?, id_medico=?, id_consultorio=?, id_estado=?,
                fecha_cita=?, hora_inicio=?, hora_fin=?, motivo_consulta=?,
                notas_paciente=?, costo=?, codigo_confirmacion=?
             WHERE id_cita=?"
        );
        $stmt->execute([
            (int)$d['id_paciente'],
            (int)$d['id_medico'],
            !empty($d['id_consultorio']) ? (int)$d['id_consultorio'] : null,
            (int)$d['id_estado'],
            $d['fecha_cita'],
            $d['hora_inicio'],
            $d['hora_fin'],
            trim($d['motivo_consulta']  ?? '') ?: null,
            trim($d['notas_paciente']   ?? '') ?: null,
            $d['costo'] !== '' ? (float)$d['costo'] : null,
            trim($d['codigo_confirmacion'] ?? '') ?: null,
            (int)$d['id_cita'],
        ]);
    }

    function borrar(int $id): void {
        $stmt = $this->db->prepare('DELETE FROM citas WHERE id_cita = ?');
        $stmt->execute([$id]);
    }

    /* ── Listas para selects ────────────────────────────────────────── */

    function todosPacientes(): array {
        $stmt = $this->db->prepare(
            "SELECT p.id_paciente,
                    u.nombre || ' ' || u.apellido_paterno AS nombre_completo
             FROM pacientes p
             JOIN usuarios u ON u.id_usuario = p.id_usuario
             WHERE u.activo = true
             ORDER BY u.apellido_paterno, u.nombre"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function todosMedicos(): array {
        $stmt = $this->db->prepare(
            "SELECT m.id_medico,
                    u.nombre || ' ' || u.apellido_paterno AS nombre_completo,
                    e.nombre_especialidad
             FROM medicos m
             JOIN usuarios      u ON u.id_usuario      = m.id_usuario
             JOIN especialidades e ON e.id_especialidad = m.id_especialidad
             WHERE u.activo = true
             ORDER BY u.apellido_paterno, u.nombre"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function todosConsultorios(): array {
        $stmt = $this->db->prepare(
            "SELECT id_consultorio, numero_consultorio FROM consultorios ORDER BY numero_consultorio"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function todosEstados(): array {
        $stmt = $this->db->prepare(
            "SELECT id_estado, nombre_estado FROM estados_cita ORDER BY nombre_estado"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ── Sprint 06: Métodos para obtener datos de correo ──────────────

    function datosPacienteParaCorreo(int $idPaciente): array|false {
        $stmt = $this->db->prepare(
            "SELECT u.email, u.nombre || ' ' || u.apellido_paterno AS nombre
             FROM pacientes p
             JOIN usuarios u ON u.id_usuario = p.id_usuario
             WHERE p.id_paciente = ?"
        );
        $stmt->execute([$idPaciente]);
        return $stmt->fetch();
    }

    function datosMedicoParaCorreo(int $idMedico): array|false {
        $stmt = $this->db->prepare(
            "SELECT u.nombre || ' ' || u.apellido_paterno AS nombre
             FROM medicos m
             JOIN usuarios u ON u.id_usuario = m.id_usuario
             WHERE m.id_medico = ?"
        );
        $stmt->execute([$idMedico]);
        return $stmt->fetch();
    }
}
