<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="page-heading mb-0">Especialidades</h2>
    <a href="especialidad.php?accion=crear" class="btn btn-primary">+ Nueva especialidad</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr><th>#</th><th>Nombre</th><th>Descripción</th><th>Activo</th><th class="text-end">Acciones</th></tr>
            </thead>
            <tbody>
                <?php if (empty($especialidades)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">Sin registros.</td></tr>
                <?php else: foreach ($especialidades as $e): ?>
                <tr>
                    <td><?= $e['id_especialidad'] ?></td>
                    <td><?= htmlspecialchars($e['nombre_especialidad']) ?></td>
                    <td><?= htmlspecialchars($e['descripcion'] ?? '—') ?></td>
                    <td><?= $e['activo'] ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                    <td class="text-end">
                        <a href="especialidad.php?accion=actualizar&id=<?= $e['id_especialidad'] ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        <a href="especialidad.php?accion=borrar&id=<?= $e['id_especialidad'] ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Eliminar «<?= htmlspecialchars(addslashes($e['nombre_especialidad'])) ?>»?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
