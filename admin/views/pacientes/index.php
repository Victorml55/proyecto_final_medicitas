<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="page-heading mb-0">Pacientes</h2>
    <a href="paciente.php?accion=crear" class="btn btn-primary">+ Nuevo paciente</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Expediente</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Tipo sangre</th>
                    <th>Seguro</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pacientes)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Sin registros.</td></tr>
                <?php else: foreach ($pacientes as $p): ?>
                <tr>
                    <td><?= $p['id_paciente'] ?></td>
                    <td><code><?= htmlspecialchars($p['numero_expediente'] ?? '—') ?></code></td>
                    <td><?= htmlspecialchars(trim($p['nombre_completo'])) ?></td>
                    <td><?= htmlspecialchars($p['email']) ?></td>
                    <td><?= htmlspecialchars($p['telefono'] ?? '—') ?></td>
                    <td>
                        <?php if ($p['tipo_sangre']): ?>
                        <span class="badge bg-danger"><?= htmlspecialchars($p['tipo_sangre']) ?></span>
                        <?php else: ?>—<?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($p['seguro_medico'] ?? '—') ?></td>
                    <td class="text-end">
                        <a href="paciente.php?accion=actualizar&id=<?= $p['id_paciente'] ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        <a href="paciente.php?accion=borrar&id=<?= $p['id_paciente'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Eliminar al paciente <?= htmlspecialchars(addslashes(trim($p['nombre_completo']))) ?>?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
