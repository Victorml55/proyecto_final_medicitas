<?php

// ============================================================
// models/dashboard.php – Consultas de KPIs y métricas
// Sprint 06: Dashboard con indicadores clave
// ============================================================

class Dashboard extends Sistema {

    function __construct() { $this->conectar(); }

    // ── TARJETAS RESUMEN ────────────────────────────────────────────

    function totalCitas(): int {
        return (int) $this->db->query('SELECT COUNT(*) FROM citas')->fetchColumn();
    }

    function totalPacientes(): int {
        return (int) $this->db->query('SELECT COUNT(*) FROM pacientes')->fetchColumn();
    }

    function totalMedicos(): int {
        return (int) $this->db->query('SELECT COUNT(*) FROM medicos')->fetchColumn();
    }

    function ingresoTotal(): float {
        return (float) $this->db->query(
            "SELECT COALESCE(SUM(c.costo), 0)
             FROM citas c
             JOIN estados_cita e ON e.id_estado = c.id_estado
             WHERE e.nombre_estado = 'Concluida'"
        )->fetchColumn();
    }

    function citasHoy(): int {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM citas WHERE fecha_cita = CURRENT_DATE"
        )->fetchColumn();
    }

    function citasMesActual(): int {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM citas
             WHERE EXTRACT(MONTH FROM fecha_cita) = EXTRACT(MONTH FROM CURRENT_DATE)
               AND EXTRACT(YEAR  FROM fecha_cita) = EXTRACT(YEAR  FROM CURRENT_DATE)"
        )->fetchColumn();
    }

    // ── GRÁFICA 1: Citas por estado ────────────────────────────────

    function citasPorEstado(): array {
        $stmt = $this->db->query(
            "SELECT e.nombre_estado, COUNT(c.id_cita) AS total
             FROM estados_cita e
             LEFT JOIN citas c ON c.id_estado = e.id_estado
             GROUP BY e.nombre_estado
             ORDER BY total DESC"
        );
        return $stmt->fetchAll();
    }

    // ── GRÁFICA 2: Citas por mes (últimos 6 meses) ────────────────

    function citasPorMes(): array {
        $stmt = $this->db->query(
            "SELECT TO_CHAR(fecha_cita, 'Mon YYYY') AS mes,
                    EXTRACT(YEAR  FROM fecha_cita)  AS anio,
                    EXTRACT(MONTH FROM fecha_cita)  AS num_mes,
                    COUNT(*) AS total
             FROM citas
             WHERE fecha_cita >= DATE_TRUNC('month', CURRENT_DATE) - INTERVAL '5 months'
             GROUP BY mes, anio, num_mes
             ORDER BY anio, num_mes"
        );
        return $stmt->fetchAll();
    }

    // ── GRÁFICA 3: Top médicos con más citas ──────────────────────

    function topMedicos(): array {
        $stmt = $this->db->query(
            "SELECT u.nombre || ' ' || u.apellido_paterno AS nombre_medico,
                    COUNT(c.id_cita) AS total_citas
             FROM medicos m
             JOIN usuarios u ON u.id_usuario = m.id_usuario
             LEFT JOIN citas c ON c.id_medico = m.id_medico
             GROUP BY nombre_medico
             ORDER BY total_citas DESC
             LIMIT 7"
        );
        return $stmt->fetchAll();
    }

    // ── GRÁFICA 4: Ingresos por mes (últimos 6 meses) ────────────

    function ingresosPorMes(): array {
        $stmt = $this->db->query(
            "SELECT TO_CHAR(c.fecha_cita, 'Mon YYYY') AS mes,
                    EXTRACT(YEAR  FROM c.fecha_cita)  AS anio,
                    EXTRACT(MONTH FROM c.fecha_cita)  AS num_mes,
                    COALESCE(SUM(c.costo), 0)         AS total
             FROM citas c
             JOIN estados_cita e ON e.id_estado = c.id_estado
             WHERE e.nombre_estado = 'Concluida'
               AND c.fecha_cita >= DATE_TRUNC('month', CURRENT_DATE) - INTERVAL '5 months'
             GROUP BY mes, anio, num_mes
             ORDER BY anio, num_mes"
        );
        return $stmt->fetchAll();
    }

    // ── TABLA: Últimas 5 citas ─────────────────────────────────────

    function ultimasCitas(): array {
        $stmt = $this->db->query(
            "SELECT c.id_cita,
                    c.fecha_cita,
                    c.hora_inicio,
                    up.nombre || ' ' || up.apellido_paterno AS nombre_paciente,
                    um.nombre || ' ' || um.apellido_paterno AS nombre_medico,
                    e.nombre_estado
             FROM citas c
             JOIN pacientes   p  ON p.id_paciente = c.id_paciente
             JOIN usuarios    up ON up.id_usuario  = p.id_usuario
             JOIN medicos     m  ON m.id_medico    = c.id_medico
             JOIN usuarios    um ON um.id_usuario  = m.id_usuario
             JOIN estados_cita e ON e.id_estado    = c.id_estado
             ORDER BY c.fecha_cita DESC, c.hora_inicio DESC
             LIMIT 5"
        );
        return $stmt->fetchAll();
    }
}
