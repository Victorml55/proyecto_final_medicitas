<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="page-heading mb-0">Estados de cita</h2>
    <a href="estado_cita.php?accion=crear" class="btn btn-primary">+ Nuevo estado</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr><th>#</th><th>Nombre</th><th>Color</th><th>Descripción</th><th class="text-end">Acciones</th></tr>
            </thead>
            <tbody>
                <?php if (empty($estados)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">Sin registros.</td></tr>
                <?php else: foreach ($estados as $e): ?>
                <tr>
                    <td><?= $e['id_estado'] ?></td>
                    <td><?= htmlspecialchars($e['nombre_estado']) ?></td>
                    <td>
                        <?php if (!empty($e['color'])): ?>
                        <span style="display:inline-block;width:20px;height:20px;background:<?= htmlspecialchars($e['color']) ?>;border-radius:4px;border:1px solid #ccc;vertical-align:middle;"></span>
                        <small class="ms-1"><?= htmlspecialchars($e['color']) ?></small>
                        <?php else: ?>—<?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($e['descripcion'] ?? '—') ?></td>
                    <td class="text-end">
                        <a href="estado_cita.php?accion=actualizar&id=<?= $e['id_estado'] ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        <a href="estado_cita.php?accion=borrar&id=<?= $e['id_estado'] ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Eliminar «<?= htmlspecialchars(addslashes($e['nombre_estado'])) ?>»?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
