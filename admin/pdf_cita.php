<?php

// ============================================================
// pdf_cita.php – Genera y descarga el comprobante PDF de una cita
// Sprint 06: TCPDF
// ============================================================

require_once __DIR__ . '/sistema.class.php';
require_once __DIR__ . '/auth.php';
requerirLogin();
require_once __DIR__ . '/services/PdfService.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: cita.php?accion=leer');
    exit;
}

$app  = new Sistema();
$app->conectar();

// Obtener todos los datos de la cita con joins completos
$stmt = $app->db->prepare(
    "SELECT
        c.id_cita,
        c.fecha_cita,
        c.hora_inicio,
        c.hora_fin,
        c.motivo_consulta,
        c.notas_paciente,
        c.costo,
        c.codigo_confirmacion,
        up.nombre || ' ' || up.apellido_paterno  AS nombre_paciente,
        up.telefono                               AS telefono_paciente,
        up.email                                  AS email_paciente,
        um.nombre || ' ' || um.apellido_paterno  AS nombre_medico,
        e2.nombre_especialidad,
        co.numero_consultorio,
        e.nombre_estado
    FROM citas c
    JOIN pacientes    p   ON p.id_paciente    = c.id_paciente
    JOIN usuarios     up  ON up.id_usuario    = p.id_usuario
    JOIN medicos      m   ON m.id_medico      = c.id_medico
    JOIN usuarios     um  ON um.id_usuario    = m.id_usuario
    JOIN especialidades e2 ON e2.id_especialidad = m.id_especialidad
    JOIN estados_cita  e  ON e.id_estado      = c.id_estado
    LEFT JOIN consultorios co ON co.id_consultorio = c.id_consultorio
    WHERE c.id_cita = ?"
);
$stmt->execute([$id]);
$cita = $stmt->fetch();

if (!$cita) {
    header('Location: cita.php?accion=leer');
    exit;
}

// Generar y descargar el PDF
PdfService::comprobanteCita($cita);
