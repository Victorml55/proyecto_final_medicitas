<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="page-heading mb-0">Horarios de médicos</h2>
    <a href="horario_medico.php?accion=crear" class="btn btn-primary">+ Nuevo horario</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Médico</th>
                    <th>Día</th>
                    <th>Hora inicio</th>
                    <th>Hora fin</th>
                    <th>Consultorio</th>
                    <th>Activo</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($horarios)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Sin registros.</td></tr>
                <?php else: foreach ($horarios as $h): ?>
                <tr>
                    <td><?= $h['id_horario'] ?></td>
                    <td><?= htmlspecialchars($h['nombre_medico']) ?></td>
                    <td><span class="badge bg-primary"><?= htmlspecialchars($h['dia_semana']) ?></span></td>
                    <td><?= substr($h['hora_inicio'], 0, 5) ?></td>
                    <td><?= substr($h['hora_fin'], 0, 5) ?></td>
                    <td><?= $h['numero_consultorio'] ? htmlspecialchars($h['numero_consultorio']) : '<span class="text-muted">—</span>' ?></td>
                    <td><?= $h['activo'] ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                    <td class="text-end">
                        <a href="horario_medico.php?accion=actualizar&id=<?= $h['id_horario'] ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        <a href="horario_medico.php?accion=borrar&id=<?= $h['id_horario'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Eliminar este horario?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
