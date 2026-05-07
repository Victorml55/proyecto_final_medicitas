<?php

class Pago extends Sistema {

    function __construct() { $this->conectar(); }

    function leer() {
        $stmt = $this->db->prepare(
            "SELECT pg.id_pago, pg.monto, pg.metodo_pago, pg.referencia_pago,
                    pg.estado_pago, pg.fecha_pago, pg.notas,
                    c.id_cita, c.fecha_cita,
                    u.nombre || ' ' || u.apellido_paterno AS nombre_paciente
             FROM pagos pg
             JOIN citas     c  ON c.id_cita      = pg.id_cita
             JOIN pacientes p  ON p.id_paciente  = c.id_paciente
             JOIN usuarios  u  ON u.id_usuario   = p.id_usuario
             ORDER BY pg.fecha_pago DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function leerUno(int $id) {
        $stmt = $this->db->prepare('SELECT * FROM pagos WHERE id_pago = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    function crear(array $d) {
        $stmt = $this->db->prepare(
            "INSERT INTO pagos (id_cita, monto, metodo_pago, referencia_pago, estado_pago, notas)
             VALUES (?,?,?,?,?,?)"
        );
        $stmt->execute([
            (int)$d['id_cita'],
            (float)$d['monto'],
            $d['metodo_pago'],
            trim($d['referencia_pago'] ?? '') ?: null,
            $d['estado_pago'] ?: 'Pendiente',
            trim($d['notas'] ?? '') ?: null,
        ]);
    }

    function actualizar(array $d) {
        $stmt = $this->db->prepare(
            "UPDATE pagos SET id_cita=?, monto=?, metodo_pago=?, referencia_pago=?, estado_pago=?, notas=?
             WHERE id_pago=?"
        );
        $stmt->execute([
            (int)$d['id_cita'],
            (float)$d['monto'],
            $d['metodo_pago'],
            trim($d['referencia_pago'] ?? '') ?: null,
            $d['estado_pago'] ?: 'Pendiente',
            trim($d['notas'] ?? '') ?: null,
            (int)$d['id_pago'],
        ]);
    }

    function borrar(int $id) {
        $stmt = $this->db->prepare('DELETE FROM pagos WHERE id_pago = ?');
        $stmt->execute([$id]);
    }

    function todasCitas(): array {
        $stmt = $this->db->prepare(
            "SELECT c.id_cita,
                    c.fecha_cita,
                    u.nombre || ' ' || u.apellido_paterno AS nombre_paciente,
                    um.nombre || ' ' || um.apellido_paterno AS nombre_medico
             FROM citas c
             JOIN pacientes p  ON p.id_paciente = c.id_paciente
             JOIN usuarios  u  ON u.id_usuario  = p.id_usuario
             JOIN medicos   m  ON m.id_medico   = c.id_medico
             JOIN usuarios  um ON um.id_usuario  = m.id_usuario
             ORDER BY c.fecha_cita DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
