<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="page-heading mb-0">Plantillas de notificaciones</h2>
    <a href="plantilla_notificacion.php?accion=crear" class="btn btn-primary">+ Nueva plantilla</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Canal</th>
                    <th>Asunto</th>
                    <th>Activa</th>
                    <th>Creación</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($plantillas)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Sin registros.</td></tr>
                <?php else:
                $canalColor = ['Email'=>'primary','SMS'=>'success','Sistema'=>'secondary'];
                foreach ($plantillas as $p): ?>
                <tr>
                    <td><?= $p['id_plantilla'] ?></td>
                    <td><code><?= htmlspecialchars($p['codigo_plantilla']) ?></code></td>
                    <td><?= htmlspecialchars($p['nombre_plantilla']) ?></td>
                    <td><span class="badge bg-<?= $canalColor[$p['tipo_canal']] ?? 'secondary' ?>"><?= htmlspecialchars($p['tipo_canal']) ?></span></td>
                    <td>
                        <span title="<?= htmlspecialchars($p['asunto']) ?>">
                            <?= htmlspecialchars(mb_substr($p['asunto'], 0, 45)) ?><?= mb_strlen($p['asunto']) > 45 ? '…' : '' ?>
                        </span>
                    </td>
                    <td><?= $p['activa'] ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                    <td><?= date('d/m/Y', strtotime($p['fecha_creacion'])) ?></td>
                    <td class="text-end">
                        <a href="plantilla_notificacion.php?accion=actualizar&id=<?= $p['id_plantilla'] ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        <a href="plantilla_notificacion.php?accion=borrar&id=<?= $p['id_plantilla'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Eliminar la plantilla «<?= htmlspecialchars(addslashes($p['nombre_plantilla'])) ?>»?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
