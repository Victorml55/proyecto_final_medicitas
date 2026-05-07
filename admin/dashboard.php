<?php

// ============================================================
// dashboard.php – Controlador del Dashboard de KPIs
// Sprint 06: Indicadores Clave de Desempeño
// ============================================================

require_once __DIR__ . '/sistema.class.php';
require_once __DIR__ . '/auth.php';
requerirLogin();
require_once __DIR__ . '/models/dashboard.php';

$dash = new Dashboard();

// ── Tarjetas ────────────────────────────────────────────────
$kpi = [
    'total_citas'      => $dash->totalCitas(),
    'total_pacientes'  => $dash->totalPacientes(),
    'total_medicos'    => $dash->totalMedicos(),
    'ingreso_total'    => $dash->ingresoTotal(),
    'citas_hoy'        => $dash->citasHoy(),
    'citas_mes'        => $dash->citasMesActual(),
];

// ── Datos para gráficas (JSON para Chart.js) ────────────────
$citasEstado  = $dash->citasPorEstado();
$citasMes     = $dash->citasPorMes();
$topMedicos   = $dash->topMedicos();
$ingresosMes  = $dash->ingresosPorMes();
$ultimasCitas = $dash->ultimasCitas();

require __DIR__ . '/views/header.php';
require __DIR__ . '/views/dashboard/index.php';
require __DIR__ . '/views/footer.php';
