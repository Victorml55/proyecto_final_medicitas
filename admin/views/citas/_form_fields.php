<div class="row g-3">

    <div class="col-md-6">
        <label class="form-label fw-semibold">Paciente <span class="text-danger">*</span></label>
        <select name="id_paciente" class="form-select" required id="selPaciente">
            <option value="">— Selecciona un paciente —</option>
            <?php foreach ($pacientes as $p): ?>
            <option value="<?= $p['id_paciente'] ?>"
                <?= (int)($vals['id_paciente'] ?? 0) === (int)$p['id_paciente'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($p['nombre_completo']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Médico <span class="text-danger">*</span></label>
        <select name="id_medico" class="form-select" required id="selMedico">
            <option value="">— Selecciona un médico —</option>
            <?php foreach ($medicos as $m): ?>
            <option value="<?= $m['id_medico'] ?>"
                <?= (int)($vals['id_medico'] ?? 0) === (int)$m['id_medico'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($m['nombre_completo']) ?> — <?= htmlspecialchars($m['nombre_especialidad']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Consultorio</label>
        <select name="id_consultorio" class="form-select">
            <option value="">— Sin asignar —</option>
            <?php foreach ($consultorios as $co): ?>
            <option value="<?= $co['id_consultorio'] ?>"
                <?= (int)($vals['id_consultorio'] ?? 0) === (int)$co['id_consultorio'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($co['numero_consultorio']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
        <select name="id_estado" class="form-select" required id="selEstado">
            <option value="">— Selecciona un estado —</option>
            <?php foreach ($estados as $e): ?>
            <option value="<?= $e['id_estado'] ?>"
                <?= (int)($vals['id_estado'] ?? 0) === (int)$e['id_estado'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($e['nombre_estado']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Fecha <span class="text-danger">*</span></label>
        <input type="date" name="fecha_cita" class="form-control" required id="inpFecha"
               value="<?= htmlspecialchars($vals['fecha_cita'] ?? '') ?>">
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Hora inicio <span class="text-danger">*</span></label>
        <input type="time" name="hora_inicio" class="form-control" required id="inpHoraIni"
               value="<?= htmlspecialchars($vals['hora_inicio'] ?? '') ?>">
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Hora fin <span class="text-danger">*</span></label>
        <input type="time" name="hora_fin" class="form-control" required id="inpHoraFin"
               value="<?= htmlspecialchars($vals['hora_fin'] ?? '') ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Costo ($)</label>
        <input type="number" name="costo" class="form-control" min="0" step="0.01"
               placeholder="0.00"
               value="<?= htmlspecialchars($vals['costo'] ?? '') ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Código de confirmación</label>
        <input type="text" name="codigo_confirmacion" class="form-control" maxlength="10"
               placeholder="Ej. CONF-001"
               value="<?= htmlspecialchars($vals['codigo_confirmacion'] ?? '') ?>">
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Motivo de consulta</label>
        <textarea name="motivo_consulta" class="form-control" rows="2"
                  placeholder="Describe el motivo de la consulta..."><?= htmlspecialchars($vals['motivo_consulta'] ?? '') ?></textarea>
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Notas del paciente</label>
        <textarea name="notas_paciente" class="form-control" rows="2"
                  placeholder="Observaciones adicionales del paciente..."><?= htmlspecialchars($vals['notas_paciente'] ?? '') ?></textarea>
    </div>

</div>

<script>
(function () {
    const frm = document.getElementById('frmCita');
    if (!frm) return;
    frm.addEventListener('submit', function (e) {
        let ok = true;
        ['selPaciente', 'selMedico', 'selEstado'].forEach(function (id) {
            const el = document.getElementById(id);
            if (!el.value) { el.classList.add('is-invalid'); ok = false; }
            else el.classList.remove('is-invalid');
        });
        ['inpFecha', 'inpHoraIni', 'inpHoraFin'].forEach(function (id) {
            const el = document.getElementById(id);
            if (!el.value) { el.classList.add('is-invalid'); ok = false; }
            else el.classList.remove('is-invalid');
        });
        const ini = document.getElementById('inpHoraIni').value;
        const fin = document.getElementById('inpHoraFin').value;
        if (ini && fin && fin <= ini) {
            document.getElementById('inpHoraFin').classList.add('is-invalid');
            ok = false;
        }
        if (!ok) e.preventDefault();
    });
})();
</script>
