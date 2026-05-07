<div class="d-flex align-items-center mb-3">
    <a href="paciente.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Nuevo paciente</h2>
</div>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php $tiposSangre = ['A+','A-','B+','B-','AB+','AB-','O+','O-']; ?>
<div class="card shadow-sm" style="max-width:720px;">
    <div class="card-body">
        <form method="POST" action="paciente.php?accion=crear" novalidate id="frm">
            <div class="row g-3">

                <div class="col-md-8">
                    <label class="form-label fw-semibold">Usuario <span class="text-danger">*</span></label>
                    <select name="id_usuario" class="form-select" required id="selUsuario">
                        <option value="">— Selecciona usuario —</option>
                        <?php foreach ($usuarios as $u): ?>
                        <option value="<?= $u['id_usuario'] ?>" <?= (int)($_POST['id_usuario'] ?? 0) === (int)$u['id_usuario'] ? 'selected' : '' ?>>
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
                        <option value="<?= $ts ?>" <?= ($_POST['tipo_sangre'] ?? '') === $ts ? 'selected' : '' ?>><?= $ts ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">N° de expediente</label>
                    <input type="text" name="numero_expediente" class="form-control" maxlength="20"
                           value="<?= htmlspecialchars($_POST['numero_expediente'] ?? '') ?>">
                    <div class="form-text">Déjalo vacío para asignarlo automáticamente.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Seguro médico</label>
                    <input type="text" name="seguro_medico" class="form-control" maxlength="100"
                           value="<?= htmlspecialchars($_POST['seguro_medico'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">N° de póliza</label>
                    <input type="text" name="numero_poliza" class="form-control" maxlength="50"
                           value="<?= htmlspecialchars($_POST['numero_poliza'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Contacto de emergencia</label>
                    <input type="text" name="contacto_emergencia_nombre" class="form-control" maxlength="150"
                           placeholder="Nombre completo"
                           value="<?= htmlspecialchars($_POST['contacto_emergencia_nombre'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Teléfono emergencia</label>
                    <input type="tel" name="contacto_emergencia_telefono" class="form-control" maxlength="15"
                           value="<?= htmlspecialchars($_POST['contacto_emergencia_telefono'] ?? '') ?>">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Alergias</label>
                    <textarea name="alergias" class="form-control" rows="2"
                              placeholder="Ej: Penicilina, látex, nueces…"><?= htmlspecialchars($_POST['alergias'] ?? '') ?></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Enfermedades crónicas</label>
                    <textarea name="enfermedades_cronicas" class="form-control" rows="2"
                              placeholder="Ej: Diabetes tipo 2, hipertensión…"><?= htmlspecialchars($_POST['enfermedades_cronicas'] ?? '') ?></textarea>
                </div>

            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Guardar paciente</button>
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
