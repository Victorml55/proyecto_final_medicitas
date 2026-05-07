<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="page-heading mb-0">Archivos adjuntos</h2>
    <a href="archivo_adjunto.php?accion=crear" class="btn btn-primary">+ Subir archivo</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Archivo</th>
                    <th>Paciente</th>
                    <th>Tipo</th>
                    <th>Tamaño</th>
                    <th>Cita</th>
                    <th>Fecha subida</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($archivos)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Sin archivos registrados.</td></tr>
                <?php else:
                $iconos = [
                    'pdf'  => '📄', 'jpg' => '🖼️', 'jpeg' => '🖼️', 'png' => '🖼️',
                    'gif'  => '🖼️', 'doc' => '📝', 'docx' => '📝',
                    'xls'  => '📊', 'xlsx'=> '📊', 'txt'  => '📃',
                ];
                foreach ($archivos as $a): ?>
                <tr>
                    <td><?= $a['id_archivo'] ?></td>
                    <td>
                        <?= $iconos[$a['tipo_archivo']] ?? '📎' ?>
                        <a href="../<?= htmlspecialchars($a['ruta_archivo'] ?? '#') ?>" target="_blank"
                           class="text-decoration-none ms-1">
                            <?= htmlspecialchars($a['nombre_archivo']) ?>
                        </a>
                        <?php if ($a['descripcion']): ?>
                        <div class="text-muted small"><?= htmlspecialchars($a['descripcion']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($a['nombre_paciente']) ?></td>
                    <td><span class="badge bg-light text-dark border"><?= strtoupper(htmlspecialchars($a['tipo_archivo'] ?? '—')) ?></span></td>
                    <td><?= $a['tamaño_kb'] ? number_format($a['tamaño_kb']) . ' KB' : '—' ?></td>
                    <td>
                        <?= $a['id_cita']
                            ? '<span class="badge bg-light text-dark border">#' . $a['id_cita'] . '</span>'
                            : '<span class="text-muted">—</span>' ?>
                    </td>
                    <td><?= $a['fecha_subida'] ? date('d/m/Y H:i', strtotime($a['fecha_subida'])) : '—' ?></td>
                    <td class="text-end">
                        <a href="archivo_adjunto.php?accion=actualizar&id=<?= $a['id_archivo'] ?>"
                           class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        <a href="archivo_adjunto.php?accion=borrar&id=<?= $a['id_archivo'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Eliminar el archivo «<?= htmlspecialchars(addslashes($a['nombre_archivo'])) ?>»? Esta acción no se puede deshacer.')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
