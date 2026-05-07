<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="page-heading mb-0">Usuario – Rol</h2>
    <a href="usuario_rol.php?accion=crear" class="btn btn-primary">+ Asignar rol</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Rol asignado</th>
                    <th>Fecha asignación</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($asignaciones)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Sin asignaciones registradas.</td></tr>
                <?php else: foreach ($asignaciones as $a): ?>
                <tr>
                    <td><?= $a['id_usuario_rol'] ?></td>
                    <td><?= htmlspecialchars($a['nombre_usuario']) ?></td>
                    <td><?= htmlspecialchars($a['email']) ?></td>
                    <td><span class="badge bg-primary"><?= htmlspecialchars($a['nombre_rol']) ?></span></td>
                    <td><?= $a['fecha_asignacion'] ? date('d/m/Y H:i', strtotime($a['fecha_asignacion'])) : '—' ?></td>
                    <td class="text-end">
                        <a href="usuario_rol.php?accion=actualizar&id=<?= $a['id_usuario_rol'] ?>"
                           class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        <a href="usuario_rol.php?accion=borrar&id=<?= $a['id_usuario_rol'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Quitar el rol «<?= htmlspecialchars(addslashes($a['nombre_rol'])) ?>» del usuario <?= htmlspecialchars(addslashes($a['nombre_usuario'])) ?>?')">Quitar</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
