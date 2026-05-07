<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="page-heading mb-0">Valoraciones</h2>
    <a href="valoracion.php?accion=crear" class="btn btn-primary">+ Nueva valoración</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Cita</th>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Calificación</th>
                    <th>Comentario</th>
                    <th>Anónimo</th>
                    <th>Fecha</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($valoraciones)): ?>
                <tr><td colspan="9" class="text-center text-muted py-4">Sin valoraciones registradas.</td></tr>
                <?php else: foreach ($valoraciones as $v): ?>
                <tr>
                    <td><?= $v['id_valoracion'] ?></td>
                    <td><span class="badge bg-light text-dark border">#<?= $v['id_cita'] ?></span></td>
                    <td><?= htmlspecialchars($v['nombre_paciente']) ?></td>
                    <td><?= htmlspecialchars($v['nombre_medico']) ?></td>
                    <td>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span style="color:<?= $i <= $v['calificacion'] ? '#f59e0b' : '#d1d5db' ?>">★</span>
                        <?php endfor; ?>
                        <small class="text-muted">(<?= $v['calificacion'] ?>)</small>
                    </td>
                    <td>
                        <?php if ($v['comentario']): ?>
                        <span title="<?= htmlspecialchars($v['comentario']) ?>">
                            <?= htmlspecialchars(mb_strimwidth($v['comentario'], 0, 50, '…')) ?>
                        </span>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $v['anonimo'] ? '<span class="badge bg-secondary">Sí</span>' : 'No' ?></td>
                    <td><?= $v['fecha_valoracion'] ? date('d/m/Y', strtotime($v['fecha_valoracion'])) : '—' ?></td>
                    <td class="text-end">
                        <a href="valoracion.php?accion=actualizar&id=<?= $v['id_valoracion'] ?>"
                           class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        <a href="valoracion.php?accion=borrar&id=<?= $v['id_valoracion'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Eliminar esta valoración?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
