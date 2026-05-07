<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="page-heading mb-0">Usuarios</h2>
    <a href="usuario.php?accion=crear" class="btn btn-primary">+ Nuevo usuario</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr><th>#</th><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Género</th><th>Activo</th><th>Registro</th><th class="text-end">Acciones</th></tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Sin registros.</td></tr>
                <?php else: foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= $u['id_usuario'] ?></td>
                    <td><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido_paterno'] . ' ' . ($u['apellido_materno'] ?? '')) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['telefono'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($u['genero'] ?? '—') ?></td>
                    <td><?= $u['activo'] ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                    <td><?= $u['fecha_registro'] ? date('d/m/Y', strtotime($u['fecha_registro'])) : '—' ?></td>
                    <td class="text-end">
                        <a href="usuario.php?accion=actualizar&id=<?= $u['id_usuario'] ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        <a href="usuario.php?accion=borrar&id=<?= $u['id_usuario'] ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Eliminar al usuario <?= htmlspecialchars(addslashes($u['nombre'] . ' ' . $u['apellido_paterno'])) ?>?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
