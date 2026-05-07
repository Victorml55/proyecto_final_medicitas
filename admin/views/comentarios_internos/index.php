<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="page-heading mb-0">Comentarios internos</h2>
    <a href="comentario_interno.php?accion=crear" class="btn btn-primary">+ Nuevo comentario</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Comentario</th>
                    <th>Autor</th>
                    <th>Paciente</th>
                    <th>Cita</th>
                    <th>Importancia</th>
                    <th>Fecha</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($comentarios)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Sin comentarios registrados.</td></tr>
                <?php else: foreach ($comentarios as $c): ?>
                <tr <?= $c['es_importante'] ? 'class="table-warning"' : '' ?>>
                    <td><?= $c['id_comentario'] ?></td>
                    <td style="max-width:280px;">
                        <?= htmlspecialchars(mb_strimwidth($c['comentario'], 0, 80, '…')) ?>
                    </td>
                    <td><?= htmlspecialchars($c['nombre_autor']) ?></td>
                    <td><?= $c['nombre_paciente'] ? htmlspecialchars($c['nombre_paciente']) : '<span class="text-muted">—</span>' ?></td>
                    <td>
                        <?= $c['id_cita']
                            ? '<span class="badge bg-light text-dark border">#' . $c['id_cita'] . '</span>'
                            : '<span class="text-muted">—</span>' ?>
                    </td>
                    <td>
                        <?= $c['es_importante']
                            ? '<span class="badge bg-danger">⚑ Importante</span>'
                            : '<span class="text-muted">Normal</span>' ?>
                    </td>
                    <td><?= $c['fecha_comentario'] ? date('d/m/Y H:i', strtotime($c['fecha_comentario'])) : '—' ?></td>
                    <td class="text-end">
                        <a href="comentario_interno.php?accion=actualizar&id=<?= $c['id_comentario'] ?>"
                           class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        <a href="comentario_interno.php?accion=borrar&id=<?= $c['id_comentario'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Eliminar este comentario?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
