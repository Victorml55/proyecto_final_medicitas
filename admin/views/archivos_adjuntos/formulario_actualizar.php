<div class="d-flex align-items-center mb-3">
    <a href="archivo_adjunto.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Editar archivo adjunto</h2>
</div>
<?php if (!$archivo): ?>
<div class="alert alert-warning">Archivo no encontrado.</div>
<?php else: ?>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="card shadow-sm" style="max-width:660px;">
    <div class="card-body">

        <div class="alert alert-info d-flex align-items-center mb-4">
            <span class="material-symbols-outlined me-2">attach_file</span>
            <div>
                <strong><?= htmlspecialchars($archivo['nombre_archivo']) ?></strong>
                <span class="text-muted ms-2"><?= strtoupper($archivo['tipo_archivo']) ?> —
                <?= $archivo['tamaño_kb'] ? number_format($archivo['tamaño_kb']) . ' KB' : '' ?></span><br>
                <small class="text-muted">El archivo físico no se puede cambiar, solo sus metadatos.</small>
            </div>
        </div>

        <form method="POST" action="archivo_adjunto.php?accion=actualizar&id=<?= $archivo['id_archivo'] ?>" novalidate id="frm">

            <div class="mb-3">
                <label class="form-label fw-semibold">Paciente <span class="text-danger">*</span></label>
                <select name="id_paciente" class="form-select" required id="selPaciente">
                    <option value="">— Selecciona paciente —</option>
                    <?php foreach ($pacientes as $p): ?>
                    <option value="<?= $p['id_paciente'] ?>"
                        <?= (int)$archivo['id_paciente'] === (int)$p['id_paciente'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nombre_completo']) ?> (<?= htmlspecialchars($p['email']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Selecciona un paciente.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">N° de cita relacionada</label>
                <input type="number" name="id_cita" class="form-control" min="1"
                       value="<?= htmlspecialchars($archivo['id_cita'] ?? '') ?>">
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Descripción</label>
                <input type="text" name="descripcion" class="form-control" maxlength="255"
                       value="<?= htmlspecialchars($archivo['descripcion'] ?? '') ?>">
            </div>

            <button type="submit" class="btn btn-primary">Actualizar</button>
        </form>
    </div>
</div>
<script>
document.getElementById('frm').addEventListener('submit', function(e) {
    const sel = document.getElementById('selPaciente');
    if (!sel.value) { sel.classList.add('is-invalid'); e.preventDefault(); }
    else sel.classList.remove('is-invalid');
});
</script>
<?php endif; ?>
