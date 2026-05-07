<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="page-heading mb-0">Permisos</h2>
    <a href="permiso.php?accion=crear" class="btn btn-primary">+ Nuevo permiso</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Módulo</th>
                    <th>Descripción</th>
                    <th>Activo</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($permisos)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Sin registros.</td></tr>
                <?php else: foreach ($permisos as $p): ?>
                <tr>
                    <td><?= $p['id_permiso'] ?></td>
                    <td><code><?= htmlspecialchars($p['nombre_permiso']) ?></code></td>
                    <td>
                        <?php if ($p['modulo']): ?>
                        <span class="badge bg-info text-dark"><?= htmlspecialchars($p['modulo']) ?></span>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($p['descripcion'] ?? '—') ?></td>
                    <td>
                        <?= $p['activo']
                            ? '<span class="badge bg-success">Activo</span>'
                            : '<span class="badge bg-secondary">Inactivo</span>' ?>
                    </td>
                    <td class="text-end">
                        <a href="permiso.php?accion=actualizar&id=<?= $p['id_permiso'] ?>"
                           class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        <a href="permiso.php?accion=borrar&id=<?= $p['id_permiso'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Eliminar el permiso «<?= htmlspecialchars(addslashes($p['nombre_permiso'])) ?>»?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
