<div class="d-flex align-items-center mb-3">
    <a href="archivo_adjunto.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Subir archivo adjunto</h2>
</div>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="card shadow-sm" style="max-width:660px;">
    <div class="card-body">
        <form method="POST" action="archivo_adjunto.php?accion=crear"
              enctype="multipart/form-data" novalidate id="frm">

            <div class="mb-3">
                <label class="form-label fw-semibold">Paciente <span class="text-danger">*</span></label>
                <select name="id_paciente" class="form-select" required id="selPaciente">
                    <option value="">— Selecciona paciente —</option>
                    <?php foreach ($pacientes as $p): ?>
                    <option value="<?= $p['id_paciente'] ?>"
                        <?= (int)($_POST['id_paciente'] ?? 0) === (int)$p['id_paciente'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nombre_completo']) ?> (<?= htmlspecialchars($p['email']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Selecciona un paciente.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Archivo <span class="text-danger">*</span></label>
                <input type="file" name="archivo" class="form-control" required id="inputArchivo"
                       accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx,.xls,.xlsx,.txt">
                <div class="form-text">Tipos permitidos: PDF, imágenes, Word, Excel, TXT. Máx. 5 MB.</div>
                <div class="invalid-feedback">Selecciona un archivo.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">N° de cita relacionada</label>
                <input type="number" name="id_cita" class="form-control" min="1"
                       placeholder="Opcional"
                       value="<?= htmlspecialchars($_POST['id_cita'] ?? '') ?>">
                <div class="form-text">Puedes dejarlo vacío si el archivo no corresponde a una cita específica.</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Descripción</label>
                <input type="text" name="descripcion" class="form-control" maxlength="255"
                       placeholder="ej. Resultado de laboratorio, radiografía de tórax…"
                       value="<?= htmlspecialchars($_POST['descripcion'] ?? '') ?>">
            </div>

            <button type="submit" class="btn btn-primary">Subir archivo</button>
        </form>
    </div>
</div>
<script>
document.getElementById('frm').addEventListener('submit', function(e) {
    let ok = true;
    const sel = document.getElementById('selPaciente');
    if (!sel.value) { sel.classList.add('is-invalid'); ok = false; }
    else sel.classList.remove('is-invalid');
    const arc = document.getElementById('inputArchivo');
    if (!arc.files.length) { arc.classList.add('is-invalid'); ok = false; }
    else arc.classList.remove('is-invalid');
    if (!ok) e.preventDefault();
});
</script>
