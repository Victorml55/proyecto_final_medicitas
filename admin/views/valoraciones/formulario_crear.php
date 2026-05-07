<div class="d-flex align-items-center mb-3">
    <a href="valoracion.php?accion=leer" class="btn btn-sm btn-outline-secondary me-3">← Volver</a>
    <h2 class="page-heading mb-0">Nueva valoración</h2>
</div>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="card shadow-sm" style="max-width:660px;">
    <div class="card-body">
        <form method="POST" action="valoracion.php?accion=crear" novalidate id="frm">

            <div class="mb-3">
                <label class="form-label fw-semibold">Cita <span class="text-danger">*</span></label>
                <select name="id_cita" class="form-select" required id="selCita"
                        onchange="autorellenar(this)">
                    <option value="">— Selecciona una cita —</option>
                    <?php foreach ($citas as $c): ?>
                    <option value="<?= $c['id_cita'] ?>"
                            data-paciente="<?= $c['id_paciente'] ?>"
                            data-medico="<?= $c['id_medico'] ?>"
                        <?= (int)($_POST['id_cita'] ?? 0) === (int)$c['id_cita'] ? 'selected' : '' ?>>
                        #<?= $c['id_cita'] ?> — <?= date('d/m/Y', strtotime($c['fecha_cita'])) ?>
                        | <?= htmlspecialchars($c['nombre_paciente']) ?>
                        → Dr. <?= htmlspecialchars($c['nombre_medico']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Selecciona una cita.</div>
            </div>

            <input type="hidden" name="id_paciente" id="hidPaciente" value="<?= (int)($_POST['id_paciente'] ?? 0) ?>">
            <input type="hidden" name="id_medico"   id="hidMedico"   value="<?= (int)($_POST['id_medico']   ?? 0) ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold">Calificación <span class="text-danger">*</span></label>
                <div class="d-flex gap-3 align-items-center" id="estrellas">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="calificacion"
                               id="cal<?= $i ?>" value="<?= $i ?>"
                               <?= (int)($_POST['calificacion'] ?? 0) === $i ? 'checked' : '' ?>>
                        <label class="form-check-label fs-5" for="cal<?= $i ?>"><?= $i ?> ★</label>
                    </div>
                    <?php endfor; ?>
                </div>
                <div class="invalid-feedback d-block" id="calErr" style="display:none!important"></div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Comentario</label>
                <textarea name="comentario" class="form-control" rows="3"><?= htmlspecialchars($_POST['comentario'] ?? '') ?></textarea>
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input type="checkbox" name="anonimo" id="anonimo" class="form-check-input" value="1"
                           <?= isset($_POST['anonimo']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="anonimo">Publicar como anónimo</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Guardar valoración</button>
        </form>
    </div>
</div>
<script>
function autorellenar(sel) {
    const opt = sel.options[sel.selectedIndex];
    document.getElementById('hidPaciente').value = opt.dataset.paciente || '';
    document.getElementById('hidMedico').value   = opt.dataset.medico   || '';
}
document.getElementById('frm').addEventListener('submit', function(e) {
    let ok = true;
    const sel = document.getElementById('selCita');
    if (!sel.value) { sel.classList.add('is-invalid'); ok = false; }
    else sel.classList.remove('is-invalid');
    const cals = this.querySelectorAll('[name=calificacion]');
    const marcado = [...cals].some(r => r.checked);
    const err = document.getElementById('calErr');
    if (!marcado) {
        err.textContent = 'Selecciona una calificación.';
        err.style.display = 'block';
        ok = false;
    } else {
        err.style.display = 'none';
    }
    if (!ok) e.preventDefault();
});
</script>
