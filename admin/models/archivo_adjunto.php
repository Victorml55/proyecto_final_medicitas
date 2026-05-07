<?php

class ArchivoAdjunto extends Sistema {

    function __construct() { $this->conectar(); }

    function leer() {
        $stmt = $this->db->prepare(
            "SELECT a.id_archivo, a.nombre_archivo, a.tipo_archivo, a.tamaño_kb,
                    a.descripcion, a.fecha_subida, a.id_cita,
                    u.nombre || ' ' || u.apellido_paterno AS nombre_paciente
             FROM archivos_adjuntos a
             JOIN pacientes p ON p.id_paciente = a.id_paciente
             JOIN usuarios  u ON u.id_usuario  = p.id_usuario
             ORDER BY a.fecha_subida DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function leerUno(int $id) {
        $stmt = $this->db->prepare('SELECT * FROM archivos_adjuntos WHERE id_archivo = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function crear(array $d, array $file) {
        // Mover archivo subido
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('adj_') . '.' . strtolower($ext);
        $ruta     = 'uploads/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            throw new RuntimeException('No se pudo guardar el archivo.');
        }

        $stmt = $this->db->prepare(
            "INSERT INTO archivos_adjuntos
                (id_cita, id_paciente, nombre_archivo, ruta_archivo, tipo_archivo, tamaño_kb, descripcion)
             VALUES (?,?,?,?,?,?,?)"
        );
        $stmt->execute([
            $d['id_cita'] ? (int)$d['id_cita'] : null,
            (int)$d['id_paciente'],
            htmlspecialchars($file['name']),
            $ruta,
            strtolower($ext),
            (int)ceil($file['size'] / 1024),
            trim($d['descripcion'] ?? '') ?: null,
        ]);
    }

    function actualizar(array $d) {
        $stmt = $this->db->prepare(
            'UPDATE archivos_adjuntos SET id_paciente=?, id_cita=?, descripcion=? WHERE id_archivo=?'
        );
        $stmt->execute([
            (int)$d['id_paciente'],
            $d['id_cita'] ? (int)$d['id_cita'] : null,
            trim($d['descripcion'] ?? '') ?: null,
            (int)$d['id_archivo'],
        ]);
    }

    function borrar(int $id) {
        // Eliminar el archivo físico también
        $stmt = $this->db->prepare('SELECT ruta_archivo FROM archivos_adjuntos WHERE id_archivo = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            $path = __DIR__ . '/../' . $row['ruta_archivo'];
            if (file_exists($path)) unlink($path);
        }
        $stmt = $this->db->prepare('DELETE FROM archivos_adjuntos WHERE id_archivo = ?');
        $stmt->execute([$id]);
    }

    function todosPacientes(): array {
        $stmt = $this->db->prepare(
            "SELECT p.id_paciente,
                    u.nombre || ' ' || u.apellido_paterno AS nombre_completo,
                    u.email
             FROM pacientes p
             JOIN usuarios u ON u.id_usuario = p.id_usuario
             ORDER BY u.apellido_paterno, u.nombre"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function citasPorPaciente(int $idPaciente): array {
        $stmt = $this->db->prepare(
            "SELECT c.id_cita, c.fecha_cita,
                    um.nombre || ' ' || um.apellido_paterno AS nombre_medico
             FROM citas c
             JOIN medicos  m  ON m.id_medico  = c.id_medico
             JOIN usuarios um ON um.id_usuario = m.id_usuario
             WHERE c.id_paciente = ?
             ORDER BY c.fecha_cita DESC"
        );
        $stmt->execute([$idPaciente]);
        return $stmt->fetchAll();
    }
}
