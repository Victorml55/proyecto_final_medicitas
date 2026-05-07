<div class="d-flex align-items-center mb-3">
    <a href="paciente.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Editar paciente</h2>
</div>
<?php if (!$paciente): ?>
<div class="alert alert-warning">Paciente no encontrado.</div>
<?php else: ?>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php $tiposSangre = ['A+','A-','B+','B-','AB+','AB-','O+','O-']; ?>
<div class="card shadow-sm" style="max-width:720px;">
    <div class="card-body">
        <form method="POST" action="paciente.php?accion=actualizar&id=<?= $paciente['id_paciente'] ?>" novalidate id="frm">
            <div class="row g-3">

                <div class="col-md-8">
                    <label class="form-label fw-semibold">Usuario <span class="text-danger">*</span></label>
                    <select name="id_usuario" class="form-select" required id="selUsuario">
                        <option value="">— Selecciona usuario —</option>
                        <?php foreach ($usuarios as $u): ?>
                        <option value="<?= $u['id_usuario'] ?>" <?= (int)$paciente['id_usuario'] === (int)$u['id_usuario'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars(trim($u['nombre_completo'])) ?> (<?= htmlspecialchars($u['email']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Selecciona un usuario.</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tipo de sangre</label>
                    <select name="tipo_sangre" class="form-select">
                        <option value="">— Desconocido —</option>
                        <?php foreach ($tiposSangre as $ts): ?>
                        <option value="<?= $ts ?>" <?= ($paciente['tipo_sangre'] ?? '') === $ts ? 'selected' : '' ?>><?= $ts ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">N° de expediente</label>
                    <input type="text" name="numero_expediente" class="form-control" maxlength="20"
                           value="<?= htmlspecialchars($paciente['numero_expediente'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Seguro médico</label>
                    <input type="text" name="seguro_medico" class="form-control" maxlength="100"
                           value="<?= htmlspecialchars($paciente['seguro_medico'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">N° de póliza</label>
                    <input type="text" name="numero_poliza" class="form-control" maxlength="50"
                           value="<?= htmlspecialchars($paciente['numero_poliza'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Contacto de emergencia</label>
                    <input type="text" name="contacto_emergencia_nombre" class="form-control" maxlength="150"
                           value="<?= htmlspecialchars($paciente['contacto_emergencia_nombre'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Teléfono emergencia</label>
                    <input type="tel" name="contacto_emergencia_telefono" class="form-control" maxlength="15"
                           value="<?= htmlspecialchars($paciente['contacto_emergencia_telefono'] ?? '') ?>">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Alergias</label>
                    <textarea name="alergias" class="form-control" rows="2"><?= htmlspecialchars($paciente['alergias'] ?? '') ?></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Enfermedades crónicas</label>
                    <textarea name="enfermedades_cronicas" class="form-control" rows="2"><?= htmlspecialchars($paciente['enfermedades_cronicas'] ?? '') ?></textarea>
                </div>

            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Actualizar paciente</button>
            </div>
        </form>
    </div>
</div>
<script>
document.getElementById('frm').addEventListener('submit', function(e) {
    const sel = document.getElementById('selUsuario');
    if (!sel.value) { sel.classList.add('is-invalid'); e.preventDefault(); }
    else sel.classList.remove('is-invalid');
});
</script>
<?php endif; ?>
