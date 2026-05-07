<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="page-heading mb-0">Consultorios</h2>
    <a href="consultorio.php?accion=crear" class="btn btn-primary">+ Nuevo consultorio</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr><th>#</th><th>Número</th><th>Piso</th><th>Descripción</th><th>Activo</th><th class="text-end">Acciones</th></tr>
            </thead>
            <tbody>
                <?php if (empty($consultorios)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Sin registros.</td></tr>
                <?php else: foreach ($consultorios as $c): ?>
                <tr>
                    <td><?= $c['id_consultorio'] ?></td>
                    <td><?= htmlspecialchars($c['numero_consultorio']) ?></td>
                    <td><?= $c['piso'] ?? '—' ?></td>
                    <td><?= htmlspecialchars($c['descripcion'] ?? '—') ?></td>
                    <td><?= $c['activo'] ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                    <td class="text-end">
                        <a href="consultorio.php?accion=actualizar&id=<?= $c['id_consultorio'] ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        <a href="consultorio.php?accion=borrar&id=<?= $c['id_consultorio'] ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Eliminar el consultorio «<?= htmlspecialchars(addslashes($c['numero_consultorio'])) ?>»?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
