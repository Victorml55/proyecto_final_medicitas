<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="page-heading mb-0">Recetas</h2>
    <a href="receta.php?accion=crear" class="btn btn-primary">+ Nueva receta</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Cita</th>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Diagnóstico</th>
                    <th>Medicamentos</th>
                    <th>Fecha emisión</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recetas)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Sin registros.</td></tr>
                <?php else: foreach ($recetas as $r): ?>
                <tr>
                    <td><?= $r['id_receta'] ?></td>
                    <td>
                        <span class="badge bg-secondary">Cita #<?= $r['id_cita'] ?></span>
                        <div class="small text-muted"><?= date('d/m/Y', strtotime($r['fecha_cita'])) ?></div>
                    </td>
                    <td><?= htmlspecialchars($r['nombre_paciente']) ?></td>
                    <td><?= htmlspecialchars($r['nombre_medico']) ?></td>
                    <td>
                        <?php if ($r['diagnostico']): ?>
                            <span title="<?= htmlspecialchars($r['diagnostico']) ?>">
                                <?= htmlspecialchars(mb_substr($r['diagnostico'], 0, 40)) ?><?= mb_strlen($r['diagnostico']) > 40 ? '…' : '' ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge bg-info text-dark"><?= $r['num_medicamentos'] ?> med.</span>
                    </td>
                    <td><?= date('d/m/Y H:i', strtotime($r['fecha_emision'])) ?></td>
                    <td class="text-end">
                        <a href="receta.php?accion=actualizar&id=<?= $r['id_receta'] ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        <a href="receta.php?accion=borrar&id=<?= $r['id_receta'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Eliminar la receta #<?= $r['id_receta'] ?>? Se eliminarán también sus medicamentos.')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
