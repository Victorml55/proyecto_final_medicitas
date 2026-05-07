<div class="row g-3">

    <div class="col-md-6">
        <label class="form-label fw-semibold">Médico</label>
        <select name="id_medico" class="form-select">
            <option value="">— Global (aplica a todos) —</option>
            <?php foreach ($medicos as $m): ?>
            <option value="<?= $m['id_medico'] ?>"
                <?= (int)($vals['id_medico'] ?? 0) === (int)$m['id_medico'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($m['nombre_medico']) ?> — <?= htmlspecialchars($m['nombre_especialidad']) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <div class="form-text">Déjalo en "Global" para bloquear la fecha para todos los médicos.</div>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Fecha <span class="text-danger">*</span></label>
        <input type="date" name="fecha" class="form-control" required id="inputFecha"
               value="<?= htmlspecialchars($vals['fecha'] ?? '') ?>">
        <div class="invalid-feedback">La fecha es obligatoria.</div>
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Motivo</label>
        <input type="text" name="motivo" class="form-control" maxlength="200"
               placeholder="ej. Día festivo nacional, vacaciones, congreso médico…"
               value="<?= htmlspecialchars($vals['motivo'] ?? '') ?>">
    </div>

    <div class="col-12">
        <div class="form-check">
            <input type="checkbox" name="es_recurrente" id="es_recurrente"
                   class="form-check-input" value="1"
                   <?= ($vals['es_recurrente'] ?? false) ? 'checked' : '' ?>>
            <label class="form-check-label" for="es_recurrente">
                Recurrente anualmente
                <small class="text-muted">(se repite cada año en esta misma fecha)</small>
            </label>
        </div>
    </div>

</div>
<script>
(function(){
    const frm = document.getElementById('frm');
    if (!frm) return;
    frm.addEventListener('submit', function(e) {
        const f = document.getElementById('inputFecha');
        if (!f.value) { f.classList.add('is-invalid'); e.preventDefault(); }
        else f.classList.remove('is-invalid');
    });
})();
</script>
