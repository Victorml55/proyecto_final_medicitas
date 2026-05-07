<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="page-heading mb-0">Roles</h2>
    <a href="rol.php?accion=crear" class="btn btn-primary">+ Nuevo rol</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr><th>#</th><th>Nombre</th><th>Descripción</th><th class="text-end">Acciones</th></tr>
            </thead>
            <tbody>
                <?php if (empty($roles)): ?>
                <tr><td colspan="4" class="text-center text-muted py-4">Sin registros.</td></tr>
                <?php else: foreach ($roles as $r): ?>
                <tr>
                    <td><?= $r['id_rol'] ?></td>
                    <td><?= htmlspecialchars($r['nombre_rol']) ?></td>
                    <td><?= htmlspecialchars($r['descripcion'] ?? '—') ?></td>
                    <td class="text-end">
                        <a href="rol.php?accion=actualizar&id=<?= $r['id_rol'] ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        <a href="rol.php?accion=borrar&id=<?= $r['id_rol'] ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Eliminar el rol «<?= htmlspecialchars(addslashes($r['nombre_rol'])) ?>»?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
