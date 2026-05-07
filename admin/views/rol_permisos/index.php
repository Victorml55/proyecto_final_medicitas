<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="page-heading mb-0">Rol – Permiso</h2>
    <a href="rol_permiso.php?accion=crear" class="btn btn-primary">+ Asignar permiso</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Rol</th>
                    <th>Módulo</th>
                    <th>Permiso</th>
                    <th>Fecha asignación</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($asignaciones)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Sin asignaciones registradas.</td></tr>
                <?php else: foreach ($asignaciones as $a): ?>
                <tr>
                    <td><?= $a['id_rol_permiso'] ?></td>
                    <td><span class="badge bg-primary"><?= htmlspecialchars($a['nombre_rol']) ?></span></td>
                    <td>
                        <?php if ($a['modulo']): ?>
                        <span class="badge bg-info text-dark"><?= htmlspecialchars($a['modulo']) ?></span>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td><code><?= htmlspecialchars($a['nombre_permiso']) ?></code></td>
                    <td><?= $a['fecha_asignacion'] ? date('d/m/Y H:i', strtotime($a['fecha_asignacion'])) : '—' ?></td>
                    <td class="text-end">
                        <a href="rol_permiso.php?accion=actualizar&id=<?= $a['id_rol_permiso'] ?>"
                           class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        <a href="rol_permiso.php?accion=borrar&id=<?= $a['id_rol_permiso'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Quitar el permiso «<?= htmlspecialchars(addslashes($a['nombre_permiso'])) ?>» del rol <?= htmlspecialchars(addslashes($a['nombre_rol'])) ?>?')">Quitar</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
