<?php
// Preparar datos JSON para Chart.js
$estadoLabels  = json_encode(array_column($citasEstado, 'nombre_estado'));
$estadoData    = json_encode(array_column($citasEstado, 'total'));

$mesLabels     = json_encode(array_column($citasMes, 'mes'));
$mesData       = json_encode(array_column($citasMes, 'total'));

$medicoLabels  = json_encode(array_column($topMedicos, 'nombre_medico'));
$medicoData    = json_encode(array_column($topMedicos, 'total_citas'));

$ingLabels     = json_encode(array_column($ingresosMes, 'mes'));
$ingData       = json_encode(array_map('floatval', array_column($ingresosMes, 'total')));
?>

<!-- ── Estilos adicionales del dashboard ── -->
<style>
    .kpi-card {
        border: none;
        border-radius: 14px;
        transition: transform .15s;
        overflow: hidden;
    }
    .kpi-card:hover { transform: translateY(-3px); }
    .kpi-icon {
        width: 52px; height: 52px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 26px;
    }
    .kpi-value { font-size: 2rem; font-weight: 800; line-height: 1; }
    .kpi-label { font-size: 0.78rem; text-transform: uppercase;
                 letter-spacing: .05em; color: #94a3b8; font-weight: 600; }
    .chart-card { border: none; border-radius: 14px; box-shadow: 0 2px 10px rgba(0,0,0,.07); }
    .section-title { font-size: 1rem; font-weight: 700;
                     color: #005f99; margin-bottom: 1rem; }
    .badge-estado {
        font-size: .72rem; padding: 4px 10px; border-radius: 20px;
        font-weight: 600;
    }
</style>

<h2 class="page-heading">
    <span class="material-symbols-outlined align-middle me-1">dashboard</span>
    Dashboard · KPIs
</h2>

<!-- ═══════════════════════════════════════════════
     FILA 1 · Tarjetas de resumen
════════════════════════════════════════════════ -->
<div class="row g-3 mb-4">

    <!-- Total citas -->
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card card shadow-sm h-100 p-3">
            <div class="kpi-icon mb-2" style="background:#e6f0f9;">
                <span class="material-symbols-outlined" style="color:#005f99;">calendar_month</span>
            </div>
            <div class="kpi-value text-primary"><?= number_format($kpi['total_citas']) ?></div>
            <div class="kpi-label mt-1">Total citas</div>
        </div>
    </div>

    <!-- Citas hoy -->
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card card shadow-sm h-100 p-3">
            <div class="kpi-icon mb-2" style="background:#fef9c3;">
                <span class="material-symbols-outlined" style="color:#ca8a04;">today</span>
            </div>
            <div class="kpi-value" style="color:#ca8a04;"><?= number_format($kpi['citas_hoy']) ?></div>
            <div class="kpi-label mt-1">Citas hoy</div>
        </div>
    </div>

    <!-- Citas este mes -->
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card card shadow-sm h-100 p-3">
            <div class="kpi-icon mb-2" style="background:#e0f2fe;">
                <span class="material-symbols-outlined" style="color:#0284c7;">date_range</span>
            </div>
            <div class="kpi-value" style="color:#0284c7;"><?= number_format($kpi['citas_mes']) ?></div>
            <div class="kpi-label mt-1">Citas este mes</div>
        </div>
    </div>

    <!-- Pacientes -->
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card card shadow-sm h-100 p-3">
            <div class="kpi-icon mb-2" style="background:#fce7f3;">
                <span class="material-symbols-outlined" style="color:#be185d;">personal_injury</span>
            </div>
            <div class="kpi-value" style="color:#be185d;"><?= number_format($kpi['total_pacientes']) ?></div>
            <div class="kpi-label mt-1">Pacientes</div>
        </div>
    </div>

    <!-- Médicos -->
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card card shadow-sm h-100 p-3">
            <div class="kpi-icon mb-2" style="background:#dcfce7;">
                <span class="material-symbols-outlined" style="color:#16a34a;">stethoscope</span>
            </div>
            <div class="kpi-value" style="color:#16a34a;"><?= number_format($kpi['total_medicos']) ?></div>
            <div class="kpi-label mt-1">Médicos</div>
        </div>
    </div>

    <!-- Ingresos totales -->
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card card shadow-sm h-100 p-3">
            <div class="kpi-icon mb-2" style="background:#f0fdf4;">
                <span class="material-symbols-outlined" style="color:#15803d;">payments</span>
            </div>
            <div class="kpi-value" style="color:#15803d; font-size:1.4rem;">
                $<?= number_format($kpi['ingreso_total'], 0) ?>
            </div>
            <div class="kpi-label mt-1">Ingresos MXN</div>
        </div>
    </div>

</div>

<!-- ═══════════════════════════════════════════════
     FILA 2 · Gráficas principales
════════════════════════════════════════════════ -->
<div class="row g-3 mb-4">

    <!-- Citas por mes (barras) -->
    <div class="col-lg-7">
        <div class="chart-card card p-4 h-100">
            <div class="section-title">
                <span class="material-symbols-outlined align-middle me-1" style="font-size:18px;">bar_chart</span>
                Citas por mes (últimos 6 meses)
            </div>
            <canvas id="chartCitasMes" height="110"></canvas>
        </div>
    </div>

    <!-- Distribución por estado (dona) -->
    <div class="col-lg-5">
        <div class="chart-card card p-4 h-100">
            <div class="section-title">
                <span class="material-symbols-outlined align-middle me-1" style="font-size:18px;">donut_large</span>
                Citas por estado
            </div>
            <canvas id="chartEstados" height="160"></canvas>
        </div>
    </div>

</div>

<!-- ═══════════════════════════════════════════════
     FILA 3 · Top médicos + Ingresos
════════════════════════════════════════════════ -->
<div class="row g-3 mb-4">

    <!-- Top médicos (barras horizontales) -->
    <div class="col-lg-6">
        <div class="chart-card card p-4 h-100">
            <div class="section-title">
                <span class="material-symbols-outlined align-middle me-1" style="font-size:18px;">leaderboard</span>
                Top médicos por citas
            </div>
            <canvas id="chartMedicos" height="180"></canvas>
        </div>
    </div>

    <!-- Ingresos por mes (línea) -->
    <div class="col-lg-6">
        <div class="chart-card card p-4 h-100">
            <div class="section-title">
                <span class="material-symbols-outlined align-middle me-1" style="font-size:18px;">trending_up</span>
                Ingresos por mes (últimos 6 meses)
            </div>
            <canvas id="chartIngresos" height="180"></canvas>
        </div>
    </div>

</div>

<!-- ═══════════════════════════════════════════════
     FILA 4 · Últimas citas
════════════════════════════════════════════════ -->
<div class="row g-3">
    <div class="col-12">
        <div class="chart-card card p-4">
            <div class="section-title">
                <span class="material-symbols-outlined align-middle me-1" style="font-size:18px;">history</span>
                Últimas 5 citas registradas
            </div>
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ultimasCitas)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-3">Sin registros.</td></tr>
                    <?php else: foreach ($ultimasCitas as $uc):
                        $badge = match(strtolower($uc['nombre_estado'])) {
                            'confirmada'  => 'bg-success',
                            'pendiente'   => 'bg-warning text-dark',
                            'cancelada'   => 'bg-danger',
                            'completada'  => 'bg-primary',
                            'en consulta' => 'bg-info text-dark',
                            default       => 'bg-secondary',
                        };
                    ?>
                    <tr>
                        <td class="text-muted">#<?= $uc['id_cita'] ?></td>
                        <td><?= htmlspecialchars($uc['nombre_paciente']) ?></td>
                        <td><?= htmlspecialchars($uc['nombre_medico']) ?></td>
                        <td><?= date('d/m/Y', strtotime($uc['fecha_cita'])) ?></td>
                        <td class="text-muted small"><?= substr($uc['hora_inicio'], 0, 5) ?></td>
                        <td>
                            <span class="badge-estado badge <?= $badge ?>">
                                <?= htmlspecialchars($uc['nombre_estado']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="pdf_cita.php?id=<?= $uc['id_cita'] ?>"
                               class="btn btn-sm btn-outline-danger" target="_blank" title="Descargar PDF">
                                <span class="material-symbols-outlined align-middle" style="font-size:14px;">picture_as_pdf</span>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
            <div class="mt-2 text-end">
                <a href="cita.php?accion=leer" class="btn btn-sm btn-outline-primary">Ver todas las citas →</a>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════
     Chart.js – Scripts
════════════════════════════════════════════════ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const AZUL    = '#005f99';
const AZUL_L  = 'rgba(0,95,153,0.15)';
const COLORES = ['#005f99','#0284c7','#16a34a','#ca8a04','#be185d','#7c3aed','#ea580c'];

// ── 1. Citas por mes (barras) ──────────────────
new Chart(document.getElementById('chartCitasMes'), {
    type: 'bar',
    data: {
        labels: <?= $mesLabels ?>,
        datasets: [{
            label: 'Citas',
            data: <?= $mesData ?>,
            backgroundColor: AZUL,
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 },
                 grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
});

// ── 2. Estados (dona) ─────────────────────────
new Chart(document.getElementById('chartEstados'), {
    type: 'doughnut',
    data: {
        labels: <?= $estadoLabels ?>,
        datasets: [{
            data: <?= $estadoData ?>,
            backgroundColor: COLORES,
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { padding: 12, font: { size: 11 } } }
        }
    }
});

// ── 3. Top médicos (barras horizontales) ──────
new Chart(document.getElementById('chartMedicos'), {
    type: 'bar',
    data: {
        labels: <?= $medicoLabels ?>,
        datasets: [{
            label: 'Citas',
            data: <?= $medicoData ?>,
            backgroundColor: COLORES,
            borderRadius: 5,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, ticks: { stepSize: 1 },
                 grid: { color: '#f1f5f9' } },
            y: { grid: { display: false } }
        }
    }
});

// ── 4. Ingresos por mes (línea) ───────────────
new Chart(document.getElementById('chartIngresos'), {
    type: 'line',
    data: {
        labels: <?= $ingLabels ?>,
        datasets: [{
            label: 'Ingresos ($)',
            data: <?= $ingData ?>,
            borderColor: '#16a34a',
            backgroundColor: 'rgba(22,163,74,0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#16a34a',
            pointRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' },
                 ticks: { callback: v => '$' + v.toLocaleString('es-MX') } },
            x: { grid: { display: false } }
        }
    }
});
</script>
