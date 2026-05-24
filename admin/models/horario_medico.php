<?php

class HorarioMedico extends Sistema {

    function __construct() { $this->conectar(); }

    function leer() {
        $stmt = $this->db->prepare(
            "SELECT h.id_horario, h.dia_semana, h.hora_inicio, h.hora_fin, h.activo,
                    u.nombre || ' ' || u.apellido_paterno AS nombre_medico,
                    c.numero_consultorio
             FROM horarios_medicos h
             JOIN medicos    m ON m.id_medico      = h.id_medico
             JOIN usuarios   u ON u.id_usuario     = m.id_usuario
             LEFT JOIN consultorios c ON c.id_consultorio = h.id_consultorio
             ORDER BY u.apellido_paterno, u.nombre,
                      CASE h.dia_semana
                        WHEN 'Lunes'     THEN 1 WHEN 'Martes'    THEN 2
                        WHEN 'Miércoles' THEN 3 WHEN 'Jueves'    THEN 4
                        WHEN 'Viernes'   THEN 5 WHEN 'Sábado'    THEN 6
                        WHEN 'Domingo'   THEN 7 END,
                      h.hora_inicio"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function leerUno(int $id) {
        $stmt = $this->db->prepare(
            'SELECT * FROM horarios_medicos WHERE id_horario = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function crear(array $d) {
        $stmt = $this->db->prepare(
            'INSERT INTO horarios_medicos (id_medico, id_consultorio, dia_semana, hora_inicio, hora_fin, activo)
             VALUES (?,?,?,?,?,?)'
        );
        $stmt->execute([
            (int)$d['id_medico'],
            $d['id_consultorio'] ? (int)$d['id_consultorio'] : null,
            $d['dia_semana'],
            $d['hora_inicio'],
            $d['hora_fin'],
            isset($d['activo']) ? 'true' : 'false',
        ]);
    }

    function actualizar(array $d) {
        $stmt = $this->db->prepare(
            'UPDATE horarios_medicos SET id_medico=?, id_consultorio=?, dia_semana=?, hora_inicio=?, hora_fin=?, activo=?
             WHERE id_horario=?'
        );
        $stmt->execute([
            (int)$d['id_medico'],
            $d['id_consultorio'] ? (int)$d['id_consultorio'] : null,
            $d['dia_semana'],
            $d['hora_inicio'],
            $d['hora_fin'],
            isset($d['activo']) ? 'true' : 'false',
            (int)$d['id_horario'],
        ]);
    }

    function borrar(int $id) {
        $stmt = $this->db->prepare('DELETE FROM horarios_medicos WHERE id_horario = ?');
        $stmt->execute([$id]);
    }

    function todosMedicos(): array {
        $stmt = $this->db->prepare(
            "SELECT m.id_medico, m.id_consultorio,
                    u.nombre || ' ' || u.apellido_paterno AS nombre_medico, e.nombre_especialidad
             FROM medicos m
             JOIN usuarios     u ON u.id_usuario      = m.id_usuario
             JOIN especialidades e ON e.id_especialidad = m.id_especialidad
             WHERE m.activo = true ORDER BY u.apellido_paterno, u.nombre"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function todosConsultorios(): array {
        $stmt = $this->db->prepare(
            'SELECT id_consultorio, numero_consultorio, piso FROM consultorios WHERE activo = true ORDER BY numero_consultorio'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
