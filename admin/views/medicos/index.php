<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="page-heading mb-0">Médicos</h2>
    <a href="medico.php?accion=crear" class="btn btn-primary">+ Nuevo médico</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Especialidad</th>
                    <th>Cédula</th>
                    <th>Costo consulta</th>
                    <th>Duración</th>
                    <th>Activo</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($medicos)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Sin registros.</td></tr>
                <?php else: foreach ($medicos as $m): ?>
                <tr>
                    <td><?= $m['id_medico'] ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <?php if (!empty($m['foto_perfil'])): ?>
                                <img src="<?= htmlspecialchars($m['foto_perfil']) ?>" style="width:36px;height:36px;object-fit:cover;border-radius:50%;flex-shrink:0;">
                            <?php else: ?>
                                <span style="width:36px;height:36px;border-radius:50%;background:#e2e8f0;display:inline-flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">&#128100;</span>
                            <?php endif; ?>
                            <div>
                                <div class="fw-semibold"><?= htmlspecialchars(trim($m['nombre_completo'])) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($m['email']) ?></small>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($m['nombre_especialidad']) ?></span></td>
                    <td><code><?= htmlspecialchars($m['cedula_profesional']) ?></code></td>
                    <td>$<?= number_format($m['costo_consulta'], 2) ?></td>
                    <td><?= $m['duracion_consulta'] ?> min</td>
                    <td><?= $m['activo'] ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                    <td class="text-end">
                        <a href="medico.php?accion=actualizar&id=<?= $m['id_medico'] ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        <a href="medico.php?accion=borrar&id=<?= $m['id_medico'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Eliminar al médico <?= htmlspecialchars(addslashes(trim($m['nombre_completo']))) ?>?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
