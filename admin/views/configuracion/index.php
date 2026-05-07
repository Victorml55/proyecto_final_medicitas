<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="page-heading mb-0">Configuración del sistema</h2>
    <a href="configuracion.php?accion=crear" class="btn btn-primary">+ Nueva clave</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr><th>#</th><th>Clave</th><th>Valor</th><th>Tipo</th><th>Descripción</th><th class="text-end">Acciones</th></tr>
            </thead>
            <tbody>
                <?php if (empty($configs)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Sin registros.</td></tr>
                <?php else: foreach ($configs as $c): ?>
                <tr>
                    <td><?= $c['id_config'] ?></td>
                    <td><code><?= htmlspecialchars($c['clave']) ?></code></td>
                    <td><?= htmlspecialchars($c['valor']) ?></td>
                    <td><span class="badge bg-secondary"><?= htmlspecialchars($c['tipo_dato']) ?></span></td>
                    <td><?= htmlspecialchars($c['descripcion'] ?? '—') ?></td>
                    <td class="text-end">
                        <a href="configuracion.php?accion=actualizar&id=<?= $c['id_config'] ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        <a href="configuracion.php?accion=borrar&id=<?= $c['id_config'] ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Eliminar la clave «<?= htmlspecialchars(addslashes($c['clave'])) ?>»?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
