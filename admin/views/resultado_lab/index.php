<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-heading mb-0">Resultados de Laboratorio</h1>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($ok): ?>
    <div class="alert alert-success"><?= htmlspecialchars($ok) ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">#</th>
                    <th>Paciente</th>
                    <th>Fecha</th>
                    <th>Tipo de análisis</th>
                    <th>Estado</th>
                    <th>Resultado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($citas)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No hay citas de laboratorio.</td></tr>
            <?php else: foreach ($citas as $c): ?>
                <tr>
                    <td class="ps-3 text-muted small"><?= $c['id_cita_lab'] ?></td>
                    <td><?= htmlspecialchars($c['nombre_paciente']) ?></td>
                    <td><?= date('d/m/Y', strtotime($c['fecha_cita'])) ?>
                        <div class="text-muted small"><?= substr($c['hora_inicio'], 0, 5) ?></div>
                    </td>
                    <td><?= htmlspecialchars($c['tipo_analisis'] ?? '—') ?></td>
                    <td><span class="badge bg-secondary"><?= htmlspecialchars($c['nombre_estado']) ?></span></td>
                    <td>
                        <?php if ($c['tiene_resultado'] > 0): ?>
                            <span class="badge bg-success">
                                <span class="material-symbols-outlined align-middle" style="font-size:14px">check_circle</span>
                                Subido
                            </span>
                        <?php else: ?>
                            <span class="badge bg-light text-secondary border">Pendiente</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end pe-3">
                        <button class="btn btn-sm btn-outline-primary"
                                onclick="abrirModal(<?= $c['id_cita_lab'] ?>, '<?= htmlspecialchars(addslashes($c['nombre_paciente'])) ?>', '<?= htmlspecialchars(addslashes($c['tipo_analisis'] ?? '')) ?>')">
                            <span class="material-symbols-outlined align-middle" style="font-size:15px">upload_file</span>
                            Subir PDF
                        </button>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal subir resultado -->
<div class="modal fade" id="modalSubir" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="resultado_lab.php?accion=subir" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Subir resultado de laboratorio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_cita_lab" id="modal-id">
                    <p class="text-muted small mb-3" id="modal-info"></p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Archivo PDF <span class="text-danger">*</span></label>
                        <input type="file" name="archivo" accept="application/pdf" class="form-control" required>
                        <div class="form-text">Solo PDF, máximo 10 MB.</div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold">Descripción <span class="text-muted fw-normal">(opcional)</span></label>
                        <input type="text" name="descripcion" class="form-control" placeholder="Ej: Resultados biometría hemática">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="material-symbols-outlined align-middle" style="font-size:16px">upload</span>
                        Subir
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModal(id, paciente, analisis) {
    document.getElementById('modal-id').value   = id;
    document.getElementById('modal-info').textContent =
        'Paciente: ' + paciente + (analisis ? ' · ' + analisis : '');
    new bootstrap.Modal(document.getElementById('modalSubir')).show();
}
</script>
