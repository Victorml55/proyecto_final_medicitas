<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="page-heading mb-0">Citas</h2>
    <a href="cita.php?accion=crear" class="btn btn-primary">+ Nueva cita</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Fecha</th>
                    <th>Horario</th>
                    <th>Consultorio</th>
                    <th>Estado</th>
                    <th>Costo</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($citas)): ?>
                <tr><td colspan="9" class="text-center text-muted py-4">Sin registros.</td></tr>
                <?php else: foreach ($citas as $c):
                    $badge = match(strtolower($c['nombre_estado'])) {
                        'confirmada'  => 'bg-success',
                        'pendiente'   => 'bg-warning text-dark',
                        'cancelada'   => 'bg-danger',
                        'completada'  => 'bg-primary',
                        'en consulta' => 'bg-info text-dark',
                        default       => 'bg-secondary',
                    };
                ?>
                <tr>
                    <td><?= $c['id_cita'] ?></td>
                    <td><?= htmlspecialchars($c['nombre_paciente']) ?></td>
                    <td>
                        <div><?= htmlspecialchars($c['nombre_medico']) ?></div>
                    </td>
                    <td><?= date('d/m/Y', strtotime($c['fecha_cita'])) ?></td>
                    <td class="small text-muted">
                        <?= substr($c['hora_inicio'], 0, 5) ?> – <?= substr($c['hora_fin'], 0, 5) ?>
                    </td>
                    <td><?= $c['numero_consultorio'] ? htmlspecialchars($c['numero_consultorio']) : '<span class="text-muted">—</span>' ?></td>
                    <td><span class="badge <?= $badge ?>"><?= htmlspecialchars($c['nombre_estado']) ?></span></td>
                    <td><?= $c['costo'] !== null ? '$' . number_format($c['costo'], 2) : '<span class="text-muted">—</span>' ?></td>
                    <td class="text-end">
                        <a href="pdf_cita.php?id=<?= $c['id_cita'] ?>"
                           class="btn btn-sm btn-outline-danger me-1" title="Descargar PDF" target="_blank">
                            <span class="material-symbols-outlined align-middle" style="font-size:15px">picture_as_pdf</span>
                            PDF
                        </a>
                        <a href="cita.php?accion=actualizar&id=<?= $c['id_cita'] ?>"
                           class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        <a href="cita.php?accion=borrar&id=<?= $c['id_cita'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Eliminar la cita #<?= $c['id_cita'] ?>?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
