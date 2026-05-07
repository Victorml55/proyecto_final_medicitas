<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="page-heading mb-0">Días no laborables</h2>
    <a href="dia_no_laborable.php?accion=crear" class="btn btn-primary">+ Nuevo día</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Médico</th>
                    <th>Motivo</th>
                    <th>Recurrente</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($dias)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Sin registros.</td></tr>
                <?php else: foreach ($dias as $d): ?>
                <tr>
                    <td><?= $d['id_dia_no_laborable'] ?></td>
                    <td>
                        <strong><?= date('d/m/Y', strtotime($d['fecha'])) ?></strong>
                        <small class="text-muted ms-1"><?= date('l', strtotime($d['fecha'])) ?></small>
                    </td>
                    <td>
                        <?= $d['nombre_medico']
                            ? htmlspecialchars($d['nombre_medico'])
                            : '<span class="badge bg-secondary">Global (todos)</span>' ?>
                    </td>
                    <td><?= htmlspecialchars($d['motivo'] ?? '—') ?></td>
                    <td>
                        <?= $d['es_recurrente']
                            ? '<span class="badge bg-warning text-dark">Sí (anual)</span>'
                            : '<span class="badge bg-light text-dark border">No</span>' ?>
                    </td>
                    <td class="text-end">
                        <a href="dia_no_laborable.php?accion=actualizar&id=<?= $d['id_dia_no_laborable'] ?>"
                           class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        <a href="dia_no_laborable.php?accion=borrar&id=<?= $d['id_dia_no_laborable'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Eliminar el día <?= date('d/m/Y', strtotime($d['fecha'])) ?>?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
